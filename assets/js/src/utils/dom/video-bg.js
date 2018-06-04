/**
 * @desc Embed a youtube or vimeo video and make it fill its parent container on init and resize.
 * You can use the default export to automatically apply it to all found on the page. The required dom is:
 *
 * <div class="tribe-video-wall" data-js="tribe-video-wall" data-url="youtube or vimeo page url"></div>
 *
 * OR
 *
 * <div class="tribe-video-wall" data-js="tribe-video-wall" data-id="youtube or vimeo video id" data-type="youtube or vimeo"></div>
 *
 * To kick it on simply import tribeVideoWall from '../utils/dom/video-bg'; in plugins.js
 *
 * and
 *
 * tribeVideoWall();
 *
 * in init.
 *
 * Or you can use the individual init that is exported below (or other helpers) do have more control on a per instance basis.
 */

import _ from 'lodash';
import VimeoPlayer from '@vimeo/player';

import { browserTests } from '../tests';
import * as tools from '../tools';

const browser = browserTests();

/**
 * Takes a url and returns an object containing video id and type for youtube and vimeo videos, or null if not found.
 *
 * @param url
 * @returns {*}
 */

export const getVideoConfig = (url = '') => {
	let videoId = null;
	if (url.indexOf('vimeo') !== -1) {
		const regex = /https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/;
		const match = url.match(regex);
		videoId = match ? { type: 'vimeo', id: match[3] } : null;
	} else {
		const regex = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/; // eslint-disable-line
		const match = url.match(regex);
		videoId = (match && match[2].length === 11) ? { type: 'youtube', id: match[2] } : null;
	}
	return videoId;
};

/**
 * Takes a string and checks if it contains vimeo/youtube identifiers associated with their video page urls
 *
 * @param url
 */

export const isVideoUrl = (url = '') => url.indexOf('vimeo') !== -1 || url.indexOf('youtube') !== -1 || url.indexOf('youtu.be') !== -1;

/**
 * Injects the minimum required css for the walls to function correctly. Additional styles can be done in theme.
 */

const injectCSS = () => {
	const css = document.createElement('style');
	css.id = 'tribe-video-wall-css';
	css.type = 'text/css';
	css.innerHTML = `
	.tribe-video-wall {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 2;
		overflow: hidden;
		opacity: 0;
		transition: opacity 500ms ease-in;
	}
	
	.tribe-video-wall.loaded {
		opacity: 1;
	}
	
	.tribe-video-wall:after {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 3;
	}
	
	.tribe-video-wall__iframe {
		width: 100%;
		height: 100%;
		position: absolute;
		top: 0;
		left: 0;
		max-width: none;
		max-height: none;
		min-width: 100%;
	}
	`;
	document.getElementsByTagName('head')[0].appendChild(css);
};

/**
 * Inits an instance of a video wall
 *
 * @param opts
 */

export const init = (opts = {}) => {
	const options = {
		container: null,
		id: '',
		url: '',
		resize_event: 'modern_tribe/resize_executed',
		type: 'youtube',
	};

	// merge options
	Object.assign(options, opts);

	// dont run on mobile, they wont autoplay and this hurts them
	if (browser.android || browser.ios) {
		return;
	}

	// check if we should parse url or already have video id
	if (!options.id.length && options.url.length) {
		if (!isVideoUrl(options.url)) {
			return;
		}
		const videoConfig = getVideoConfig(options.url);
		if (!videoConfig) {
			return;
		}
		options.id = videoConfig.id;
		options.type = videoConfig.type;
	}

	// check for container and id
	if (!options.container || !options.id.length) {
		// need these, leaving town
		return;
	}

	if (!document.getElementById('tribe-video-wall-css')) {
		injectCSS();
	}

	// setup shared elements and values
	let h;
	let w;
	let iframeVideo;
	let player;
	const iframe = document.createElement('iframe');
	const ytId = _.uniqueId('yt-container-');
	const iframeId = _.uniqueId('video-bg-');

	// setup shared defaults on the iframe and container
	iframe.id = iframeId;
	iframe.classList.add('tribe-video-wall__iframe');
	iframe.setAttribute('webkitallowfullscreen', '');
	iframe.setAttribute('mozallowfullscreen', '');
	iframe.setAttribute('allowfullscreen', '');

	// on init and resize detect the container height and width and work out how to fill the area with video and no
	// crop lines. Basically simulating background size cover for video.
	const fitVideoToContainer = () => {
		if (w === 0 || h === 0 || !iframeVideo) {
			return;
		}

		const dH = options.container.offsetHeight;
		const dW = options.container.offsetWidth;
		let sH = 0;
		let sW = 0;

		sW = (dH / h) * w;
		sW = (sW >= dW) ? sW : dW;

		sH = dH;
		if (sW === dW) {
			sH = (dW / w) * h;
		}

		const left = sW === dW ? 0 : -(Math.abs(sW - dW) / 2);

		iframeVideo.style.position = 'absolute';
		iframeVideo.style.width = `${sW}px`;
		iframeVideo.style.height = `${sH}px`;
		iframeVideo.style.top = 0;
		iframeVideo.style.left = `${left}px`;
	};

	const embedYoutube = () => {
		const ytContainer = document.createElement('div');
		ytContainer.id = ytId;
		options.container.appendChild(ytContainer);
		if (!window.YT) {
			const tag = document.createElement('script');
			tag.src = 'https://www.youtube.com/iframe_api';
			const firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		}

		window.onYouTubeIframeAPIReady = () => {
			player = new window.YT.Player(ytId, {
				videoId: options.id,
				controls: 0,
				showinfo: 0,
				modestbranding: 1,
				iv_load_policy: 3,
				loop: 1,
				playlist: options.id,
				events: {
					onReady: (event) => {
						iframeVideo = document.getElementById(ytId);
						iframeVideo.classList.add('tribe-video-wall__iframe');
						h = iframeVideo.getAttribute('height');
						w = iframeVideo.getAttribute('width');
						iframeVideo.style.transform = 'scale(1.1)';
						fitVideoToContainer();
						event.target.loadPlaylist([options.id]);
						event.target.setLoop(true);
						event.target.mute();
						event.target.playVideo();
					},
					onStateChange: (event) => {
						if (event.data === window.YT.PlayerState.PLAYING) {
							options.container.classList.add('loaded');
						}
					},
				},
			});
		};
	};

	const embedVimeo = () => {
		iframe.src = `//player.vimeo.com/video/${options.id}?background=1&autoplay=0&mute=0&loop=0`;
		options.container.appendChild(iframe);
		iframeVideo = document.getElementById(iframeId);
		iframeVideo.setAttribute('data-vimeo-loop', 'true');
		iframeVideo.setAttribute('data-vimeo-byline', 'false');
		iframeVideo.setAttribute('data-vimeo-portrait', 'false');
		iframeVideo.setAttribute('data-vimeo-title', 'false');

		player = new VimeoPlayer(iframeVideo);
		player.getVideoHeight().then((value) => {
			h = value;
		});
		player.getVideoWidth().then((value) => {
			w = value;
			fitVideoToContainer();
		});
		player.getEnded().then(() => player.play());
		player.ready().then(() => {
			player.play();
			player.setVolume(0);
			player.setLoop(true);
			_.delay(() => options.container.classList.add('loaded'), 400);
		});
	};

	const bindEvents = () => {
		document.addEventListener(options.resize_event, fitVideoToContainer);
	};

	bindEvents();

	switch (options.type) {
	case 'vimeo':
		embedVimeo();
		break;
	case 'youtube':
		embedYoutube();
		break;
	default:
		break;
	}
};

/**
 * Sets up init for a single instance from the initAll loop.
 *
 * @param data
 */

const setupInit = (data = {}) => {
	const options = {
		container: data.container,
		resize_event: data.options.resize_event,
	};

	// if we have a url, use that and exit
	if (data.container.dataset.url) {
		options.url = data.container.dataset.url;
		init(options);
		return;
	}

	// we didnt have a url but got both and id and a type, lets use that
	if (data.container.dataset.id && data.container.dataset.type) {
		options.id = data.container.dataset.id;
		options.type = data.container.dataset.type;
		init(options);
	}
};

/**
 * Default export to init all on page
 *
 * @param opts
 */

const initAll = (opts = {}) => {
	// dont run on mobile, they wont autoplay and this hurts them
	if (browser.android || browser.ios) {
		return;
	}

	const options = {
		selector: 'tribe-video-wall',
		resize_event: 'modern_tribe/resize_executed',
	};

	// merge options
	Object.assign(options, opts);

	const videoWalls = tools.getNodes(options.selector, true);
	videoWalls.forEach(container => setupInit({ container, options }));

	console.info(`Initialized Tribe Video Wall on ${videoWalls.length} element${videoWalls.length > 1 ? 's' : ''}.`);
};

export default initAll;
