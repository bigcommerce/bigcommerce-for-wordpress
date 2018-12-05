import { I18N } from '../config/i18n';

export const importProgress = (status, position, percent) => (
	`
	<div class="bc-import-progress-count">
		${status}
	</div>
	<div class="bc-import-progress-bar">
		<div class="bc-import-progress-bar__mask" style="left: ${position}%;"></div>
		<div class="bc-import-progress-bar__bg"></div>
		<span class="bc-import-progress-bar__percent">${percent}%</span>
	</div>
	`
);

export const importCloseButton = (
	`
	<button class="button bc-icon icon-bc-cross" data-js="bc-import-close-notification">
		<span class="screen-reader-text">${I18N.messages.dismiss_notification}</span>
	</button>
	`
);
