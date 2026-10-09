import { canHover, prefersReducedMotion, prefersSavingData } from './lib/prefs';
import { createPlayer, PlayerState } from './lib/youtube';

const HOVER_DELAY = 250;
const YOUTUBE_START_TIMEOUT = 3500;

/**
 * Silent hover previews for portfolio cards.
 *
 * - Prefers a studio-supplied MP4/WebM teaser (data-teaser).
 * - Falls back to a muted YouTube player (data-youtube) created only after
 *   an intentional hover; if autoplay is blocked, the poster stays.
 * - One preview at a time; stops on leave, scroll-out, hidden tab, filter
 *   change or when the screening room opens.
 * - Disabled for touch, reduced motion and data-saving visitors: cards
 *   then rely on their explicit "Watch film" buttons.
 */
export function initPreviews() {
    const cards = [...document.querySelectorAll('[data-project-card]')].filter(
        (card) => card.dataset.teaser || card.dataset.youtube,
    );

    if (!cards.length || !canHover() || prefersReducedMotion() || prefersSavingData()) {
        return;
    }

    let pending = null;
    let active = null;
    let generation = 0;
    const visible = new WeakSet();

    const cancelPending = () => {
        if (pending) {
            window.clearTimeout(pending.timer);
            pending = null;
        }
    };

    const stopActive = () => {
        generation += 1;
        if (active) {
            active.stop();
            active = null;
        }
    };

    const stopAll = () => {
        cancelPending();
        stopActive();
    };

    const markPlaying = (card, slot) => {
        slot.classList.add('is-playing');
        card.querySelector('[data-card-media]')?.classList.add('is-previewing');
    };

    const clearPlaying = (card, slot) => {
        slot.classList.remove('is-playing');
        card.querySelector('[data-card-media]')?.classList.remove('is-previewing');
    };

    const startTeaser = (card, slot) => {
        let video = slot.querySelector('video');

        if (!video) {
            video = document.createElement('video');
            video.muted = true;
            video.defaultMuted = true;
            video.loop = true;
            video.playsInline = true;
            video.preload = 'none';
            video.setAttribute('muted', '');
            video.setAttribute('playsinline', '');
            video.setAttribute('disablepictureinpicture', '');
            video.setAttribute('tabindex', '-1');
            video.src = card.dataset.teaser;
            slot.append(video);
        }

        const onPlaying = () => markPlaying(card, slot);
        video.addEventListener('playing', onPlaying);
        video.currentTime = 0;
        video.play().catch(() => clearPlaying(card, slot));

        return {
            card,
            stop() {
                video.removeEventListener('playing', onPlaying);
                video.pause();
                clearPlaying(card, slot);
                try {
                    video.currentTime = 0;
                } catch {
                    // Not seekable yet; nothing to reset.
                }
            },
        };
    };

    const startYouTube = (card, slot) => {
        const token = generation;
        let player = null;
        let startTimer = null;
        const host = document.createElement('div');
        host.className = 'yt-preview';
        slot.append(host);

        const stop = () => {
            window.clearTimeout(startTimer);
            clearPlaying(card, slot);
            try {
                player?.destroy();
            } catch {
                // Player already gone.
            }
            player = null;
            slot.replaceChildren();
        };

        const blocked = () => {
            card.dataset.previewBlocked = 'true';
            if (active?.card === card) {
                stopActive();
            } else {
                stop();
            }
        };

        createPlayer(host, {
            videoId: card.dataset.youtube,
            title: card.querySelector('h3')?.textContent?.trim(),
            playerVars: {
                autoplay: 1,
                mute: 1,
                controls: 0,
                disablekb: 1,
                fs: 0,
                iv_load_policy: 3,
                loop: 1,
                playlist: card.dataset.youtube,
            },
            events: {
                onReady: (event) => {
                    if (token !== generation) {
                        return;
                    }
                    event.target.mute();
                    event.target.playVideo();
                    startTimer = window.setTimeout(() => {
                        if (event.target.getPlayerState?.() !== PlayerState.PLAYING) {
                            blocked();
                        }
                    }, YOUTUBE_START_TIMEOUT);
                },
                onStateChange: (event) => {
                    if (event.data === PlayerState.PLAYING && token === generation) {
                        window.clearTimeout(startTimer);
                        markPlaying(card, slot);
                    }
                },
                onAutoplayBlocked: () => token === generation && blocked(),
                onError: () => token === generation && blocked(),
            },
        })
            .then((created) => {
                player = created;
                if (token !== generation) {
                    stop();
                }
            })
            .catch(() => token === generation && blocked());

        return { card, stop };
    };

    const start = (card) => {
        stopActive();

        if (!visible.has(card) || document.hidden || card.hidden || card.dataset.previewBlocked) {
            return;
        }

        const slot = card.querySelector('[data-preview-slot]');
        if (!slot) {
            return;
        }

        active = card.dataset.teaser ? startTeaser(card, slot) : startYouTube(card, slot);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    visible.add(entry.target);
                    return;
                }
                visible.delete(entry.target);
                if (pending?.card === entry.target) {
                    cancelPending();
                }
                if (active?.card === entry.target) {
                    stopActive();
                }
            });
        },
        { threshold: 0.35 },
    );

    cards.forEach((card) => {
        observer.observe(card);
        const media = card.querySelector('[data-card-media]');

        media.addEventListener('pointerenter', (event) => {
            if (event.pointerType !== 'mouse') {
                return;
            }
            cancelPending();
            if (active?.card === card) {
                return;
            }
            pending = { card, timer: window.setTimeout(() => {
                pending = null;
                start(card);
            }, HOVER_DELAY) };
        });

        media.addEventListener('pointerleave', () => {
            if (pending?.card === card) {
                cancelPending();
            }
            if (active?.card === card) {
                stopActive();
            }
        });
    });

    document.addEventListener('visibilitychange', () => document.hidden && stopAll());
    window.addEventListener('screening:open', stopAll);
    window.addEventListener('work-filtered', stopAll);
    window.addEventListener('pagehide', stopAll);
}
