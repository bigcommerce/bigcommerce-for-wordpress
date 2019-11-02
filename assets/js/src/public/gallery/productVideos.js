import YouTubePlayer from 'youtube-player';
import * as tools from 'utils/tools';
import delegate from 'delegate';
import { on } from 'utils/events';

const el = {
	videos: tools.getNodes('bc-product-video-player', true),
};

const instances = {
	players: {},
};

/**
 * @function stopPreviousVideo
 * @param e
 */
const stopPreviousVideo = (e) => {
	if (Object.keys(instances.players).length > 0) {
		return;
	}

	let currentVideo = '';

	if (e.detail.quickView) {
		// If this is triggered by a quickview dialog being closed.
		const quickView = e.detail.quickView.node;
		currentVideo = tools.getNodes('.swiper-slide-active > iframe', false, quickView, true)[0];
	} else {
		// This was triggered by an actual slide change event from a click or swipe.
		currentVideo = tools.getNodes(`[data-index="${e.detail.previousSlide}"] > iframe`, false, e.detail.slider.wrapperEl, true)[0];
	}

	if (!currentVideo) {
		return;
	}

	const videoWrap = currentVideo.parentNode;
	// Remove the precious video iframe to stop it. (avoids issues with tracking current state and clicks)
	videoWrap.removeChild(currentVideo);
	// Immediately add the video iframe back to the slide so it can be restarted.
	videoWrap.appendChild(currentVideo);
};

/**
 * @function stopVideos
 * @description stop all videos that are currently playing.
 * TODO: This function should be deprecated/disabled in version 4.0.
 */
const stopVideos = () => {
	if (Object.keys(instances.players).length === 0) {
		return;
	}

	Object.values(instances.players).forEach((player) => {
		player.stopVideo();
	});
};

/**
 * @function playBCVideo
 * @description play the currently selected video.
 * @param e
 * TODO: This function should be deprecated/disabled in version 4.0.
 */
const playBCVideo = (e) => {
	if (Object.keys(instances.players).length === 0) {
		return;
	}

	const playerID = e.delegateTarget.dataset.playerId;

	if (!playerID) {
		return;
	}

	instances.players[playerID].playVideo();
};

/**
 * @function initPlayers
 * @description setup all available videos in an instanced object for easier control.
 */
const initPlayers = () => {
	tools.getNodes('[data-js="bc-product-video-player"]:not(.initialized)', true, document, true).forEach((player) => {
		const playerID = player.dataset.youtubeId;
		tools.addClass(player, 'initialized');
		instances.players[playerID] = YouTubePlayer(player, {
			videoId: playerID,
		});
	});
};

const bindEvents = () => {
	on(document, 'bigcommerce/gallery_slide_changed', stopPreviousVideo);

	// TODO: These events should be deprecated/disabled in version 4.0.
	// TODO: Remove the youtube-player NPM package dependency in version 4.0.
	delegate(document, '[data-js="bc-gallery-thumb-trigger"]', 'click', playBCVideo);
	on(document, 'bigcommerce/gallery_slide_changed', stopVideos);
};

const init = () => {
	if (!el.videos) {
		return;
	}

	initPlayers();
	bindEvents();
};

export default init;
