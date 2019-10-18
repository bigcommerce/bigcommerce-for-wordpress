import YouTubePlayer from 'youtube-player';
import * as tools from 'utils/tools';
import delegate from 'delegate';
import { on } from 'utils/events';

const el = {
	videos: tools.getNodes('bc-product-video-player', true),
};

const instances = {
	players: [],
};

/**
 * @function stopVideos
 * @description stop all videos that are currently playing.
 */
const stopVideos = () => {
	Object.values(instances.players).forEach((player) => {
		player.stopVideo();
	});
};

/**
 * @function playBCVideo
 * @description play the currently selected video.
 * @param e
 */
const playBCVideo = (e) => {
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
