import { gsap } from 'gsap';
import { prefersReducedMotion } from './lib/prefs';
import { createPlayer, describePlayerError, loadYouTubeApi, PlayerState } from './lib/youtube';

const AUTOPLAY_CHECK_DELAY = 2500;
const SLOW_LOAD_NOTICE_DELAY = 10000;

/**
 * The screening room: a modal <dialog> that plays a project's YouTube film
 * (or shows its stills) with all titles, captions and actions outside the
 * player. Text is always inserted with textContent.
 */
export function initScreeningRoom() {
    const dialog = document.getElementById('screening-room');
    const dataElement = document.getElementById('project-data');

    if (!dialog || !dataElement || typeof dialog.showModal !== 'function') {
        return null;
    }

    let projects = [];
    try {
        projects = JSON.parse(dataElement.textContent || '[]');
    } catch {
        return null;
    }

    const el = (name) => dialog.querySelector(`[data-screening-${name}]`);
    const stage = el('stage');
    const status = el('status');
    const thumbs = el('thumbs');
    const enquireBase = el('enquire').getAttribute('href');
    const isWorkPage = document.body.dataset.page === 'work';

    let current = null;
    let player = null;
    let playerToken = 0;
    let autoplayTimer = null;
    let slowLoadTimer = null;
    let galleryIndex = 0;
    let returnFocusTo = null;

    const setStatus = (message = '', tone = 'info', link = null) => {
        status.replaceChildren();
        status.dataset.tone = tone;
        if (message) {
            status.append(document.createTextNode(message));
        }
        if (link) {
            const anchor = document.createElement('a');
            anchor.href = link.href;
            anchor.textContent = link.label;
            anchor.target = '_blank';
            anchor.rel = 'noopener';
            status.append(anchor);
        }
    };

    const destroyPlayer = () => {
        playerToken += 1;
        window.clearTimeout(autoplayTimer);
        window.clearTimeout(slowLoadTimer);
        try {
            player?.destroy();
        } catch {
            // Already destroyed.
        }
        player = null;
    };

    const posterImage = (project) => {
        if (!project.cover) {
            return null;
        }
        const img = document.createElement('img');
        img.className = 'screening__poster';
        img.src = project.cover;
        img.alt = '';
        img.decoding = 'async';
        return img;
    };

    const designArtwork = (project) => {
        const art = document.createElement('div');
        art.className = 'design-art';
        art.setAttribute('role', 'img');
        art.setAttribute('aria-label', project.coverAlt || project.title);
        const ring = document.createElement('div');
        ring.className = 'design-art__ring';
        const title = document.createElement('div');
        title.className = 'design-art__title';
        title.textContent = project.title;
        const foot = document.createElement('div');
        foot.className = 'design-art__foot';
        foot.append(Object.assign(document.createElement('span'), { textContent: project.client || '' }));
        foot.append(Object.assign(document.createElement('span'), { textContent: 'Sparklenz' }));
        art.append(ring, title, foot);
        return art;
    };

    const renderFilm = (project, autoplay) => {
        stage.className = `screening__stage${project.portrait ? ' screening__stage--portrait' : ''}`;
        const poster = posterImage(project);
        if (poster) {
            stage.append(poster);
        }

        const host = document.createElement('div');
        host.className = 'yt-host';
        stage.append(host);
        setStatus('Loading the film…');

        const token = ++playerToken;
        const youtubeUrl = `https://www.youtube.com/watch?v=${encodeURIComponent(project.youtubeId)}`;
        slowLoadTimer = window.setTimeout(() => {
            if (token === playerToken) {
                setStatus('The player is taking longer than usual to load — it will start here when ready. ', 'info', { href: youtubeUrl, label: 'Or watch on YouTube ↗' });
            }
        }, SLOW_LOAD_NOTICE_DELAY);

        createPlayer(host, {
            videoId: project.youtubeId,
            title: project.title,
            playerVars: { autoplay: autoplay ? 1 : 0 },
            events: {
                onReady: (event) => {
                    if (token !== playerToken) {
                        return;
                    }
                    window.clearTimeout(slowLoadTimer);
                    poster?.remove();
                    setStatus('');
                    if (!autoplay) {
                        setStatus('Press play to start the film.');
                        return;
                    }
                    event.target.playVideo();
                    autoplayTimer = window.setTimeout(() => {
                        const state = event.target.getPlayerState?.();
                        if (token === playerToken && state !== PlayerState.PLAYING && state !== PlayerState.BUFFERING) {
                            setStatus('Your browser paused autoplay — press play on the player to start the film.');
                        }
                    }, AUTOPLAY_CHECK_DELAY);
                },
                onStateChange: (event) => {
                    if (token === playerToken && event.data === PlayerState.PLAYING) {
                        window.clearTimeout(autoplayTimer);
                        setStatus('');
                    }
                },
                onAutoplayBlocked: () => {
                    if (token === playerToken) {
                        setStatus('Your browser blocked autoplay — press play on the player to start the film.');
                    }
                },
                onError: (event) => {
                    if (token !== playerToken) {
                        return;
                    }
                    destroyPlayer();
                    stage.replaceChildren();
                    const fallback = posterImage(project);
                    if (fallback) {
                        stage.append(fallback);
                    }
                    setStatus(`${describePlayerError(event.data)} `, 'error', { href: youtubeUrl, label: 'Try watching on YouTube ↗' });
                },
            },
        })
            .then((created) => {
                if (token !== playerToken) {
                    created.destroy();
                    return;
                }
                player = created;
            })
            .catch(() => {
                if (token !== playerToken) {
                    return;
                }
                window.clearTimeout(slowLoadTimer);
                setStatus('The video player couldn’t load. Check your connection or content blocker. ', 'error', {
                    href: youtubeUrl,
                    label: 'Watch on YouTube ↗',
                });
            });
    };

    const renderGallery = (project) => {
        const images = project.images.length
            ? project.images
            : project.cover
              ? [{ src: project.cover, alt: project.coverAlt || project.title, caption: null }]
              : [];

        stage.className = 'screening__stage screening__stage--image';
        thumbs.replaceChildren();
        thumbs.hidden = images.length < 2;

        if (!images.length) {
            stage.className = 'screening__stage';
            const empty = document.createElement('div');
            empty.className = 'screening__empty';
            empty.append(designArtwork(project));
            stage.append(empty);
            return;
        }

        galleryIndex = Math.min(galleryIndex, images.length - 1);
        const show = (index) => {
            galleryIndex = (index + images.length) % images.length;
            const image = images[galleryIndex];
            const img = document.createElement('img');
            img.src = image.src;
            img.alt = image.alt || '';
            img.decoding = 'async';
            stage.replaceChildren(img);
            setStatus(images.length > 1 ? `Image ${galleryIndex + 1} of ${images.length}${image.caption ? ` — ${image.caption}` : ''}` : image.caption || '');
            thumbs.querySelectorAll('button').forEach((button, i) => button.setAttribute('aria-current', String(i === galleryIndex)));
        };

        images.forEach((image, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.setAttribute('aria-label', `Show image ${index + 1}${image.alt ? `: ${image.alt}` : ''}`);
            const thumb = document.createElement('img');
            thumb.src = image.src;
            thumb.alt = '';
            thumb.loading = 'lazy';
            button.append(thumb);
            button.addEventListener('click', () => show(index));
            thumbs.append(button);
        });

        stage.showImage = show;
        show(galleryIndex);
    };

    const render = (project, { autoplay = false } = {}) => {
        destroyPlayer();
        current = project;
        galleryIndex = 0;
        stage.replaceChildren();
        stage.showImage = null;
        thumbs.hidden = true;
        thumbs.replaceChildren();
        setStatus('');

        const index = projects.indexOf(project);
        el('counter').textContent = `SPK—${String(index + 1).padStart(2, '0')} / ${String(projects.length).padStart(2, '0')}`;
        el('title').textContent = project.title;
        el('summary').textContent = project.summary || '';
        el('description').textContent = project.description || '';
        el('description').hidden = !project.description;
        el('client').textContent = project.client || '';
        el('category').textContent = project.category || '';
        el('demo').hidden = !project.isDemo;
        el('prev').disabled = projects.length < 2;
        el('next').disabled = projects.length < 2;

        const enquire = new URL(enquireBase, window.location.origin);
        enquire.searchParams.set('project', project.slug);
        enquire.hash = 'enquiry';
        el('enquire').href = enquire.toString();

        if (project.youtubeId) {
            renderFilm(project, autoplay);
        } else if (project.type === 'video') {
            stage.className = `screening__stage${project.portrait ? ' screening__stage--portrait' : ''}`;
            const poster = posterImage(project);
            if (poster) {
                stage.append(poster);
            }
            const empty = document.createElement('div');
            empty.className = 'screening__empty';
            empty.append(Object.assign(document.createElement('p'), { className: 'label', textContent: 'Film coming soon' }));
            stage.append(empty);
            setStatus('The full film will appear here once it has been published.');
        } else {
            renderGallery(project);
        }

        if (isWorkPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('project', project.slug);
            history.replaceState(history.state, '', url);
        }
    };

    const open = (slug, { trigger = null, autoplay = false } = {}) => {
        const project = projects.find((item) => item.slug === slug);
        if (!project) {
            return;
        }

        if (!dialog.open) {
            returnFocusTo = trigger || document.activeElement;
            window.dispatchEvent(new CustomEvent('screening:open'));
            document.body.classList.add('is-locked');
            dialog.showModal();
            if (!prefersReducedMotion()) {
                gsap.fromTo(dialog.querySelector('.screening__panel'), { autoAlpha: 0, y: 24 }, { autoAlpha: 1, y: 0, duration: 0.45, ease: 'power3.out' });
            }
        }

        // Start loading the player immediately — the entrance animation never delays playback.
        render(project, { autoplay });
        el('close').focus();
    };

    const cleanup = () => {
        destroyPlayer();
        stage.replaceChildren();
        thumbs.replaceChildren();
        current = null;
        document.body.classList.remove('is-locked');
        window.dispatchEvent(new CustomEvent('screening:close'));

        if (isWorkPage) {
            const url = new URL(window.location.href);
            url.searchParams.delete('project');
            history.replaceState(history.state, '', url);
        }

        if (returnFocusTo?.isConnected) {
            returnFocusTo.focus({ preventScroll: true });
        }
        returnFocusTo = null;
    };

    const close = () => {
        if (dialog.open) {
            dialog.close();
        }
    };

    const step = (direction) => {
        if (!current || projects.length < 2) {
            return;
        }
        const index = projects.indexOf(current);
        render(projects[(index + direction + projects.length) % projects.length], { autoplay: false });
    };

    dialog.addEventListener('close', cleanup);
    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        close();
    });
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            close();
        }
    });
    dialog.addEventListener('keydown', (event) => {
        if (stage.showImage && (event.key === 'ArrowRight' || event.key === 'ArrowLeft') && !event.target.closest('input, textarea')) {
            event.preventDefault();
            stage.showImage(galleryIndex + (event.key === 'ArrowRight' ? 1 : -1));
        }
    });
    el('close').addEventListener('click', close);
    el('prev').addEventListener('click', () => step(-1));
    el('next').addEventListener('click', () => step(1));
    window.addEventListener('pagehide', destroyPlayer);

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-open-project]');
        if (!opener || event.defaultPrevented || event.button !== 0) {
            return;
        }
        event.preventDefault();
        const card = opener.closest('[data-project-card]');
        const trigger = opener.matches('button, a') ? opener : card?.querySelector('button[data-open-project]');
        const autoplaySource = trigger?.dataset.autoplay ?? opener.dataset.autoplay;
        open(opener.dataset.openProject, { trigger, autoplay: autoplaySource === 'true' });
    });

    // Warm the API when someone shows intent to watch, so playback starts faster.
    document.addEventListener(
        'pointerover',
        (event) => {
            if (event.target.closest?.('[data-open-project][data-autoplay="true"]')) {
                loadYouTubeApi().catch(() => {});
            }
        },
        { passive: true },
    );

    const deepLink = document.querySelector('[data-open-on-load]')?.dataset.openOnLoad;
    if (deepLink) {
        open(deepLink, { autoplay: false });
    }

    return { open, close };
}
