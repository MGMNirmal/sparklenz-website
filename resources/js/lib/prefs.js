/**
 * Visitor preferences that decide how much motion and media we load.
 */
const reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
const hoverQuery = window.matchMedia('(hover: hover) and (pointer: fine)');
const reducedDataQuery = window.matchMedia('(prefers-reduced-data: reduce)');

export const prefersReducedMotion = () => reducedMotionQuery.matches;

export const canHover = () => hoverQuery.matches;

export const prefersSavingData = () => Boolean(navigator.connection?.saveData) || reducedDataQuery.matches;

export const onPreferenceChange = (callback) => {
    reducedMotionQuery.addEventListener?.('change', callback);
    hoverQuery.addEventListener?.('change', callback);
};
