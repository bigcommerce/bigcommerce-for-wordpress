import { I18N } from '../config/i18n';

export const connectionError = data => (
	`
	<h3 class="bc-account-connection__error-title">${I18N.messages.account_connection_error}</h3>
	<span class="bc-account-connection__error-code">${I18N.messages.account_connection_code} <code>${data.code}</code></span>
	<br>
	<span class="bc-account-connection__error-message">${data.message}</span>
	`
);

export const channelConfirmation = channel => (
	`
	<p class="bc-channel-selection-confirmation" data-js="bc-channel-selection-confirmation">
	${I18N.messages.channel_confirmation.replace('%s', channel)}
	</p>
	`
);

export const bcAdminSpinner = (
	`
	<span class="spinner is-active"></span>	
	`
);
