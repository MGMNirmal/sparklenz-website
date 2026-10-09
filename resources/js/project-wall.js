import { gsap } from 'gsap';
import { prefersReducedMotion } from './lib/prefs';

/** Pixels per second for columns 1–5; odd columns rise, even columns fall. */
const SPEEDS = [18, 22, 16, 24, 20];
const MAX_FRAME_SECONDS = 0.1;

/**
 * The home page project wall. Each column contains its tiles twice; the
 * track moves by exactly one copy's height and wraps, so the loop is
 * seamless. Motion pauses on hover, keyboard focus, the pause control,
 * when offscreen, when the tab is hidden and while the screening room is
 * open. Reduced-motion visitors get a static layout.
 */
export function initProjectWall() {
    const wall = document.querySelector('[data-project-wall]');
    if (!wall) {
        return;
    }

    if (prefersReducedMotion()) {
        wall.classList.add('is-static');
        return;
    }

    const viewport = wall.querySelector('.wall__viewport');
    const toggle = wall.querySelector('[data-wall-toggle]');
    const columns = [...wall.querySelectorAll('[data-wall-column]')].map((column, index) => ({
        element: column,
        track: column.querySelector('[data-wall-track]'),
        group: column.querySelector('[data-wall-group]'),
        direction: column.dataset.direction === 'down' ? 1 : -1,
        speed: SPEEDS[index % SPEEDS.length],
        offset: 0,
        loopHeight: 0,
        startRatio: (index * 0.17) % 1,
    }));

    const paused = { user: false, hover: false, focus: false, offscreen: true, hidden: document.hidden, dialog: false };
    let ticking = false;

    const measure = () => {
        columns.forEach((column) => {
            const visible = column.element.offsetParent !== null;
            const previous = column.loopHeight;
            column.loopHeight = visible ? column.group.offsetHeight : 0;
            if (column.loopHeight && !previous) {
                column.offset = column.loopHeight * column.startRatio;
            } else if (column.loopHeight) {
                column.offset %= column.loopHeight;
            }
        });
        render();
    };

    const render = () => {
        columns.forEach((column) => {
            if (!column.loopHeight) {
                return;
            }
            // Rising columns move 0 → -H; falling columns move -H → 0.
            const y = column.direction < 0 ? -column.offset : column.offset - column.loopHeight;
            column.track.style.transform = `translate3d(0, ${y.toFixed(2)}px, 0)`;
        });
    };

    const tick = (time, deltaMs) => {
        const seconds = Math.min(deltaMs / 1000, MAX_FRAME_SECONDS);
        columns.forEach((column) => {
            if (column.loopHeight) {
                column.offset = (column.offset + column.speed * seconds) % column.loopHeight;
            }
        });
        render();
    };

    const sync = () => {
        const shouldRun = !Object.values(paused).some(Boolean);
        if (shouldRun && !ticking) {
            gsap.ticker.add(tick);
            ticking = true;
        } else if (!shouldRun && ticking) {
            gsap.ticker.remove(tick);
            ticking = false;
        }
    };

    if (toggle) {
        toggle.hidden = false;
        const label = toggle.querySelector('[data-wall-label]');
        const pauseIcon = toggle.querySelector('[data-wall-icon="pause"]');
        const playIcon = toggle.querySelector('[data-wall-icon="play"]');
        toggle.addEventListener('click', () => {
            paused.user = !paused.user;
            toggle.setAttribute('aria-pressed', String(paused.user));
            label.textContent = paused.user ? 'Resume motion' : 'Pause motion';
            pauseIcon.toggleAttribute('hidden', paused.user);
            playIcon.toggleAttribute('hidden', !paused.user);
            sync();
        });
    }

    viewport.addEventListener('pointerenter', (event) => {
        if (event.pointerType === 'mouse') {
            paused.hover = true;
            sync();
        }
    });
    viewport.addEventListener('pointerleave', () => {
        paused.hover = false;
        sync();
    });
    viewport.addEventListener('focusin', (event) => {
        // Only keyboard focus pauses; focus restored after a mouse click does not.
        paused.focus = event.target.matches(':focus-visible');
        sync();
    });
    viewport.addEventListener('focusout', (event) => {
        if (!viewport.contains(event.relatedTarget)) {
            paused.focus = false;
            sync();
        }
    });

    new IntersectionObserver(([entry]) => {
        paused.offscreen = !entry.isIntersecting;
        sync();
    }).observe(viewport);

    document.addEventListener('visibilitychange', () => {
        paused.hidden = document.hidden;
        sync();
    });
    window.addEventListener('screening:open', () => {
        paused.dialog = true;
        sync();
    });
    window.addEventListener('screening:close', () => {
        paused.dialog = false;
        sync();
    });

    // Re-measure when the layout changes (breakpoints, fonts, lazy images).
    new ResizeObserver(measure).observe(viewport);
    columns.forEach((column) => new ResizeObserver(measure).observe(column.group));
    measure();
}
