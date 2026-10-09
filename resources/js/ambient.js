import { prefersReducedMotion } from './lib/prefs';

/**
 * Small page behaviours: sticky header state, the ticker pause control
 * and the enquiry form's submit state.
 */
export function initAmbient() {
    headerState();
    ticker();
    enquiryForm();
    focusOnLoad();
}

function headerState() {
    const header = document.querySelector('[data-site-header]');
    if (!header) {
        return;
    }
    const update = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    update();
    window.addEventListener('scroll', update, { passive: true });
}

function ticker() {
    document.querySelectorAll('[data-ticker]').forEach((ticker) => {
        const button = ticker.querySelector('[data-ticker-toggle]');
        const label = ticker.querySelector('[data-ticker-label]');
        button?.addEventListener('click', () => {
            const paused = ticker.classList.toggle('is-paused');
            button.setAttribute('aria-pressed', String(paused));
            label.textContent = paused ? 'Play scrolling text' : 'Pause scrolling text';
            button.innerHTML = paused
                ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M8 5.5v13a.5.5 0 0 0 .77.42l10.2-6.5a.5.5 0 0 0 0-.84L8.77 5.08A.5.5 0 0 0 8 5.5Z" fill="currentColor" stroke="none"/></svg>'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M8 5v14M16 5v14"/></svg>';
            button.append(label);
        });
    });
}

function enquiryForm() {
    const form = document.querySelector('[data-enquiry-form]');
    form?.addEventListener('submit', () => {
        const button = form.querySelector('[data-submit]');
        if (button) {
            button.disabled = true;
            button.firstChild.textContent = 'Sending… ';
        }
    });
}

function focusOnLoad() {
    const target = document.querySelector('[data-focus-on-load]');
    if (!target) {
        return;
    }

    // Wait for the browser's own #fragment scroll so it can't steal focus back.
    const focusTarget = () => requestAnimationFrame(() => {
        target.setAttribute('tabindex', target.getAttribute('tabindex') ?? '-1');
        target.focus({ preventScroll: true });
        target.scrollIntoView({ block: 'center', behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
    });

    if (document.readyState === 'complete') {
        focusTarget();
    } else {
        window.addEventListener('load', focusTarget, { once: true });
    }
}
