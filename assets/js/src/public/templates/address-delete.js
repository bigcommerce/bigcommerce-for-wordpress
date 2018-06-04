import { NLS } from '../config/i18n';

export const deleteConfirmation = (
	`
	<div class="bc-account-address__delete-confirmation">
		<p>${NLS.account.confirm_delete_message}</p>
		<button class="bc-btn bc-account-address__delete-confirm" data-js="bc-confirm-address-deletion">${NLS.account.confirm_delete_address}</button>
		<button class="bc-btn bc-btn--inverse bc-account-address__delete-cancel" data-js="bc-confirm-address-cancel">${NLS.account.cancel_delete_address}</button>
	</div>
	`
);
