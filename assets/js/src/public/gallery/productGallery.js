/**
 * @module Product Gallery
 * @description Main product gallery swiper JS.
 */

import _ from 'lodash';
import delegate from 'delegate';
import Swiper from 'swiper';
import { trigger } from 'utils/events';

import * as tools from 'utils/tools';

const el = {
	galleries: tools.getNodes('bc-product-gallery'),
};

const instances = {
	swipers: {},
};

const galleryOptions = {
	galleryMain: () => ({
		a11y: true,
		effect: 'fade',
		fadeEffect: {
			crossFade: true,
		},
		threshold: 170,
	}),
	galleryThumbs: () => ({
		a11y: true,
		slidesPerView: 'auto',
		touchRatio: 0.2,
		centeredSlides: true,
		slideToClickedSlide: true,
		threshold: 30,
	}),
};

/**
 * @function syncMainSlider
 * @description Sync the main slider to the carousel.
 * Too bad swiper has a bug with this and we have to resort this this stuff
 * https://github.com/nolimits4web/Swiper/issues/1658
 */

const syncMainSlider = (e) => {
	const carousel = tools.closest(e.delegateTarget, '.swiper-container');
	instances.swipers[carousel.dataset.controls].slideTo(e.delegateTarget.dataset.index);
};

/**
 * @module bindCarouselEvents
 * @description Bind Carousel Events.
 */

const bindCarouselEvents = (swiperThumbId, swiperMainId) => {
	instances.swipers[swiperMainId].on('slideChange', () => {
		instances.swipers[swiperThumbId].slideTo(instances.swipers[swiperMainId].activeIndex);
	});
	delegate(instances.swipers[swiperThumbId].wrapperEl, '[data-js="bc-gallery-thumb-trigger"]', 'click', syncMainSlider);
};

/**
 * @function initCarousel
 * @description Init the carousel
 */

const initCarousel = (slider, swiperMainId) => {
	const carousel = slider.nextElementSibling;
	const swiperThumbId = _.uniqueId('swiper-carousel-');
	carousel.classList.add('initialized');
	const opts = galleryOptions.galleryThumbs();

	instances.swipers[swiperThumbId] = new Swiper(carousel, opts);
	slider.setAttribute('data-controls', swiperThumbId);
	carousel.setAttribute('data-id', swiperThumbId);
	carousel.setAttribute('data-controls', swiperMainId);
	bindCarouselEvents(swiperThumbId, swiperMainId);
};

/**
 * @module
 * @description Swiper init.
 */

const initGalleries = () => {
	tools.getNodes('[data-js="bc-gallery-container"]:not(.initialized)', true, document, true).forEach((slider) => {
		const swiperMainId = _.uniqueId('swiper-');
		slider.classList.add('initialized');
		instances.swipers[swiperMainId] = new Swiper(slider, galleryOptions.galleryMain());
		slider.setAttribute('data-id', swiperMainId);
		instances.swipers[swiperMainId].on('slideChange', () => trigger({ event: 'bigcommerce/gallery_slide_changed', data: { slider: instances.swipers[swiperMainId], previousSlide: instances.swipers[swiperMainId].previousIndex }, native: false }));
		if (!slider.classList.contains('bc-product-gallery--has-carousel')) {
			return;
		}
		initCarousel(slider, swiperMainId);
	});
};

const init = () => {
	if (!el.galleries) {
		return;
	}

	initGalleries();
};

export default init;
