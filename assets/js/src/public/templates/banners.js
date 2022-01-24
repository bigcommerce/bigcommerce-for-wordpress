export const bannerContent = (content, label) => (
	`<aside class="bc-banner" aria-label="${label}">${content}</aside>`
);

export const bannerWrapper = (styles = null, banners = null) => (
	`<div class="bc-banners" style="${styles}">${banners}</div>`
);
