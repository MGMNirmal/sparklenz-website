/**
 * Loads the official YouTube IFrame Player API once, on demand, and
 * creates players with the parameters this site relies on.
 *
 * Reference: https://developers.google.com/youtube/iframe_api_reference
 */
let apiPromise = null;

export const PlayerState = { UNSTARTED: -1, ENDED: 0, PLAYING: 1, PAUSED: 2, BUFFERING: 3, CUED: 5 };

export function loadYouTubeApi() {
    if (window.YT?.Player) {
        return Promise.resolve(window.YT);
    }

    if (apiPromise) {
        return apiPromise;
    }

    // Slow networks keep waiting; only a failed script load rejects, so a
    // late-arriving API still starts any player that is waiting for it.
    apiPromise = new Promise((resolve, reject) => {
        const previousCallback = window.onYouTubeIframeAPIReady;

        window.onYouTubeIframeAPIReady = () => {
            previousCallback?.();
            resolve(window.YT);
        };

        const script = document.createElement('script');
        script.src = 'https://www.youtube.com/iframe_api';
        script.async = true;
        script.onerror = () => {
            apiPromise = null;
            script.remove();
            reject(new Error('The YouTube player could not be loaded.'));
        };
        document.head.append(script);
    });

    return apiPromise;
}

/**
 * Messages for the documented onError codes, plus 153 (the player could
 * not identify the embedding site because no referrer was sent).
 */
export function describePlayerError(code) {
    switch (Number(code)) {
        case 2:
            return 'This film link isn’t valid. The studio has been asked to check it.';
        case 5:
            return 'This film can’t be played in your browser’s built-in player.';
        case 100:
            return 'This film is unavailable — it may have been removed or made private.';
        case 101:
        case 150:
            return 'This film can’t be played here — it may be private, removed or not allowed on other websites.';
        case 153:
            return 'YouTube couldn’t verify this website, so the film can’t be shown here. Disabling strict referrer blocking may help.';
        default:
            return 'This film couldn’t be played right now.';
    }
}

/**
 * Creates a player inside `host` (which YouTube replaces with an iframe).
 *
 * @param {HTMLElement} host
 * @param {{ videoId: string, title?: string, playerVars?: object, events?: object }} options
 */
export async function createPlayer(host, { videoId, title, playerVars = {}, events = {} }) {
    const YT = await loadYouTubeApi();

    const player = new YT.Player(host, {
        videoId,
        width: '100%',
        height: '100%',
        playerVars: {
            enablejsapi: 1,
            origin: window.location.origin,
            playsinline: 1,
            rel: 0,
            ...playerVars,
        },
        events,
    });

    const iframe = player.getIframe?.();
    if (iframe) {
        iframe.setAttribute('title', title ? `${title} — YouTube video player` : 'YouTube video player');
        iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen');
    }

    return player;
}
