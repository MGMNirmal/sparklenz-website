import { prefersReducedMotion } from './lib/prefs';

const TYPE_MS = 80;
const DELETE_MS = 40;
const HOLD_MS = 2000;
const NEXT_WORD_MS = 300;
const START_DELAY_MS = 1400;

/**
 * Home hero: types the changing headline word, then holds and deletes it
 * before typing the next. Space for the longest word is reserved in the
 * markup, so nothing around it moves. The visible headline is hidden from
 * screen readers; the <h1> carries one stable sentence. The pause control
 * stops the typing, floating illustrations and background drift together.
 */
export function initHero() {
    const hero = document.querySelector('[data-hero]');
    const typewriter = hero?.querySelector('[data-typewriter]');
    const output = typewriter?.querySelector('[data-typewriter-text]');

    if (!hero || !typewriter || !output || prefersReducedMotion()) {
        return;
    }

    let words = [];
    try {
        words = JSON.parse(typewriter.dataset.words || '[]').filter(Boolean);
    } catch {
        return;
    }

    const paused = { user: false, offscreen: false, hidden: document.hidden };
    const isPaused = () => Object.values(paused).some(Boolean);
    const characters = (word) => Array.from(word);

    let wordIndex = 0;
    let shown = characters(words[0] ?? '');
    let phase = 'hold';
    let timer = null;

    const render = () => {
        output.textContent = shown.join('');
    };

    const schedule = (delay) => {
        window.clearTimeout(timer);
        timer = window.setTimeout(step, delay);
    };

    const step = () => {
        timer = null;
        if (isPaused() || words.length < 2) {
            return;
        }

        if (phase === 'hold') {
            phase = 'deleting';
            typewriter.classList.add('is-typing');
        }

        if (phase === 'deleting') {
            if (shown.length) {
                shown = shown.slice(0, -1);
                render();
                schedule(DELETE_MS);
            } else {
                wordIndex = (wordIndex + 1) % words.length;
                phase = 'typing';
                schedule(NEXT_WORD_MS);
            }
            return;
        }

        const target = characters(words[wordIndex]);
        if (shown.length < target.length) {
            shown = target.slice(0, shown.length + 1);
            render();
            schedule(TYPE_MS);
        } else {
            phase = 'hold';
            typewriter.classList.remove('is-typing');
            schedule(HOLD_MS);
        }
    };

    const sync = () => {
        hero.classList.toggle('is-paused', paused.user);
        hero.classList.toggle('is-offscreen', paused.offscreen || paused.hidden);
        if (isPaused()) {
            window.clearTimeout(timer);
            timer = null;
        } else if (!timer) {
            schedule(phase === 'hold' ? HOLD_MS : TYPE_MS);
        }
    };

    const toggle = hero.querySelector('[data-hero-toggle]');
    if (toggle) {
        const label = toggle.querySelector('[data-hero-toggle-label]');
        const pauseIcon = toggle.querySelector('[data-hero-icon="pause"]');
        const playIcon = toggle.querySelector('[data-hero-icon="play"]');
        toggle.hidden = false;
        toggle.addEventListener('click', () => {
            paused.user = !paused.user;
            toggle.setAttribute('aria-pressed', String(paused.user));
            label.textContent = paused.user ? 'Play hero animation' : 'Pause hero animation';
            pauseIcon.toggleAttribute('hidden', paused.user);
            playIcon.toggleAttribute('hidden', !paused.user);
            sync();
        });
    }

    new IntersectionObserver(([entry]) => {
        paused.offscreen = !entry.isIntersecting;
        sync();
    }).observe(hero);

    document.addEventListener('visibilitychange', () => {
        paused.hidden = document.hidden;
        sync();
    });

    render();
    schedule(START_DELAY_MS + HOLD_MS);
}
