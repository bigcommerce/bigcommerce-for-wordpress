export const bannerContent = content => (
	`<div class="bc-banner">${content}</div>`
);

export const bannerWrapper = (styles = null, banners = null) => (
	`<div class="bc-banners" style="${styles}">${banners}</div>`
);
