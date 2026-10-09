import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import { initAmbient } from './ambient';
import { initHero } from './hero';
import { initMotion } from './motion';
import { initPreviews } from './previews';
import { initProjectWall } from './project-wall';
import { initScreeningRoom } from './screening-room';

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.start();

const safely = (label, init) => {
    try {
        init();
    } catch (error) {
        console.error(`[sparklenz] ${label} failed`, error);
    }
};

safely('motion', initMotion);
safely('ambient', initAmbient);
safely('hero', initHero);
safely('screening room', initScreeningRoom);
safely('previews', initPreviews);
safely('project wall', initProjectWall);
