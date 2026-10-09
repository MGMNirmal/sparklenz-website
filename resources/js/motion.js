import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { canHover, prefersReducedMotion } from './lib/prefs';

/**
 * Entrance and scroll motion. Content is fully visible by default; the
 * hidden starting states only apply once `motion-ready` is set here, and
 * any failure removes it again so nothing stays hidden.
 */
export function initMotion() {
    const root = document.documentElement;

    if (prefersReducedMotion() || !('IntersectionObserver' in window)) {
        return;
    }

    root.classList.add('motion-ready');

    try {
        gsap.registerPlugin(ScrollTrigger);
        heroEntrance();
        sectionReveals();
        if (canHover()) {
            parallax();
        }
    } catch (error) {
        root.classList.remove('motion-ready');
        gsap.set('[data-reveal], [data-hero-fade], [data-hero-art], .hero__title .line__inner', { clearProps: 'all' });
        console.error(error);
    }
}

function heroEntrance() {
    const lines = gsap.utils.toArray('.hero__title .line__inner');
    const fades = gsap.utils.toArray('[data-hero-fade]');
    const art = gsap.utils.toArray('[data-hero-art]');

    const timeline = gsap.timeline({ defaults: { ease: 'power4.out' } });

    if (lines.length) {
        timeline.to(lines, { y: 0, duration: 0.8, stagger: 0.1 }, 0.05);
    }

    if (fades.length) {
        timeline.to(fades, { opacity: 1, y: 0, duration: 0.6, stagger: 0.08, ease: 'power3.out' }, lines.length ? 0.3 : 0.1);
    }

    if (art.length) {
        timeline.fromTo(art, { y: 24, scale: 0.94 }, { opacity: 1, y: 0, scale: 1, duration: 0.8, stagger: 0.12, ease: 'power3.out' }, 0.25);
    }
}

function sectionReveals() {
    const queue = [];
    let flushScheduled = false;

    const flush = () => {
        flushScheduled = false;
        gsap.to(queue.splice(0), { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out', stagger: 0.08, overwrite: true });
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }
                observer.unobserve(entry.target);
                queue.push(entry.target);
            });
            if (queue.length && !flushScheduled) {
                flushScheduled = true;
                requestAnimationFrame(flush);
            }
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
    );

    document.querySelectorAll('[data-reveal]').forEach((element) => observer.observe(element));
}

function parallax() {
    gsap.utils.toArray('[data-parallax]').forEach((image) => {
        gsap.fromTo(
            image,
            { yPercent: -3 },
            {
                yPercent: 3,
                ease: 'none',
                scrollTrigger: { trigger: image.parentElement, start: 'top bottom', end: 'bottom top', scrub: 0.6 },
            },
        );
    });
}
