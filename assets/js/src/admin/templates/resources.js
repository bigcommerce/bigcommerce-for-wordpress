import { I18N } from '../config/i18n';

const { _n, sprintf } = wp.i18n;

export const noDataAvailable = (
	`
	<div class="bc-resources-no-data">
		<h2 class="bc-resources-no-data-heading">${I18N.messages.no_resources_json_data}</h2>
	</div>
	`
);

export const tabButton = tabName => (
	`
	<li class="bc-resources-tabs__list-item" data-js="bc-resources-tab" data-target="${tabName}" role="tab" aria-label="${tabName}" aria-selected="false">
		<button type="button" class="bc-resources-tab-button" data-js="bc-resources-tab-button" data-target="${tabName}"  aria-expanded="false" tabindex="198">${tabName}</button>
	</li>
	`
);

export const tabContentContainer = tabName => (
	`
	<div class="bc-resources-tabs__tab-content-container bc-resources-container-${tabName.toLowerCase()}" data-js="bc-resource-tab-content" data-tab-name="${tabName}" aria-hidden="true">
		<div class="bc-resources-pagination-wrapper" role="navigation" aria-label="${tabName} Pagination Navigation"></div>
	</div>
	`
);

export const tabCardsContent = (tabName, cards, pageNumber) => (
	`
	<div class="bc-resources-tabs__paged-cards bc-resources-tabs__max-width bc-cards-page-active" data-js="bc-resource-tab-cards" data-resource-page-name="${tabName}" data-resource-page-number="${pageNumber}" aria-hidden="false">
		${cards}
	</div>
	`
);

export const videoPlaylist = (playlistName, playlistNumber, cards, videoCount) => (
	`
	<div class="bc-resource-tab-playlist bc-resource-tab-playlist--${playlistNumber}">
		<h3 class="bc-resources-tabs__max-width bc-resource-tab-playlist-name">
			${playlistNumber !== '00' ? `<strong>${playlistNumber}</strong> ` : ''}${playlistName} 
			<span class="bc-playlist-count">${sprintf(_n('%s Video', '%s Videos', videoCount, 'bigcommerce'), videoCount)}</span>
		</h3>
		<div class="bc-resource-tab-playlist-cards">
			${cards}
		</div>
	</div>
	`
);

export const tabVideoCardsContent = (tabName, playlists, pageNumber) => (
	`
	<div class="bc-resources-tabs__paged-cards bc-resources-content-${tabName.toLowerCase()} bc-cards-page-active" data-js="bc-resource-tab-cards" data-resource-page-name="${tabName}" data-resource-page-number="${pageNumber}" aria-hidden="false">
		${playlists}
	</div>
	`
);

export const resourceCard = (image, image2x, title, description, link) => (
	`
	<div class="bc-resource-card" data-js="bc-resource-card">
		<a href="${link}" class="bc-resource-card__link" target="_blank" rel="noopener" title="${title}" tabindex="199">
			<div class="bc-resource-card__image">
				<div class="bc-resource-card__img-overlay"></div>
				<img
				srcset="${image}${image2x}"
				alt="${title} ${description}"
				>
			</div>
			<div class="bc-resource-card__content">
				<h4 class="bc-resource-card__title">${title}</h4>
				<p class="bc-resource-card__description">${description}</p>
			</div>
		</a>
	</div>
	`
);

export const resourceVideoCard = (image, image2x, title, length, description, link) => (
	`
	<div class="bc-resource-card--video" data-js="bc-resource-card">
		<a href="${link}" class="bc-resource-card__video-link" target="_blank" rel="noopener" title="${title}" tabindex="699">
			<div class="bc-resource-card__image">
				<div class="bc-resource-card__img-overlay"></div>
				<img
				srcset="${image}${image2x}"
				alt="${title} ${description}"
				>
			</div>
		</a>
		<div class="bc-resource-card__content">
			<h4 class="bc-resource-card__title">
				<a href="${link}" class="bc-resource-card__video-link" target="_blank" rel="noopener" title="${title}" tabindex="699">${title}</a>
			</h4>
			<span class="bc-resource-card__video-length">${length}</span>
			${description.length > 0 ?
			`<p class="bc-resource-card__video-description">${description}</p>`
			:
			''}
		</div>
	</div>
	`
);

export const paginationLink = (pageNumber, pageName) => (
	`
	<button
	type="button"
	class="bc-resources-pagination-button ${pageNumber === 1 ? 'bc-resources-page-active' : ''}"
	data-js="bc-resources-pagination-button"
	data-page-number="${pageNumber}"
	data-page-name="${pageName}"
	aria-current="${pageNumber === 1}"
	tabindex="200"
	>
	${[pageNumber]}
	</button>
	`
);
