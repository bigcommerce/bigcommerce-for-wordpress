
import _ from 'lodash';

import { trigger } from '../events';

/**
 * @function deepscroll
 * @desc A plugin that updates the url as targets are scrolled by using the data attribute
 * data-url-key. It depends on lodash and waypoints. This code is ie9 and up.
 *
 * @param opts Object The options object. Check below for available and defaults.
 */

/* global Waypoint */

const deepScroll = (opts) => {
	const options = _.assign({
		attr: 'data-url-key',
		targets: null,
		offset: 0,
	}, opts);
	const url = `${document.location.protocol}//${document.location.hostname}${document.location.pathname}`;
	const items = [];
	let nodes;

	const updateHash = (el) => {
		if (history.pushState) {
			if (el) {
				const hash = el.getAttribute('data-url-key') ? `#${el.getAttribute('data-url-key')}` : window.location.pathname;
				history.replaceState('', '', hash);
			} else {
				history.replaceState('', '', url);
			}
		}
	};

	const triggerScrollby = (el) => {
		trigger({ event: 'modern_tribe/scrolledto', native: false, data: { el } });
	};

	const handleWaypointDown = (dir, el) => {
		if (dir === 'down') {
			updateHash(el);
			triggerScrollby(el);
		}

		if (dir === 'up' && parseInt(el.getAttribute('data-index'), 10) === 0) {
			updateHash(null);
			triggerScrollby(null);
		}
	};

	const handleWaypointUp = (dir, el) => {
		if (dir === 'up') {
			updateHash(el);
			triggerScrollby(el);
		}
	};

	const applyWaypoint = (el) => {
		const data = {};
		const urlKey = el.getAttribute(options.attr);
		const objectId = urlKey || _.uniqueId('way-');
		const title = el.getAttribute('data-nav-title');

		data[`${objectId}-down`] = new Waypoint({
			element: el,
			handler: dir => handleWaypointDown(dir, el),
			offset: `${options.offset}px`,
		});

		data[`${objectId}-up`] = new Waypoint({
			element: el,
			handler: dir => handleWaypointUp(dir, el),
			offset: () => -(el.clientHeight - options.offset),
		});

		items.push({
			has_data: el.innerHTML.trim() !== '',
			urlKey,
			title,
			waypoint: data,
		});
	};

	const executeResize = () => {
		_.delay(() => Waypoint.refreshAll(), 200);
	};

	const refresh = () => {
		_.delay(() => Waypoint.refreshAll(), 1000);
	};

	const bindEvents = () => {
		document.addEventListener('modern_tribe/refresh_waypoints', executeResize);
		document.addEventListener('modern_tribe/resize_executed', executeResize);
		document.addEventListener('modern_tribe/accordion_animated', executeResize);
		window.addEventListener('load', refresh);
	};

	if (options.targets) {
		nodes = [].slice.call(options.targets);
		nodes.forEach(el => applyWaypoint(el));

		bindEvents();
	}
};

export default deepScroll;
