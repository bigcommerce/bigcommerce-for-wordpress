/**
 * @template Inline error message template for form fields.
 * @param message
 * @returns {string}
 */
export const formErrorMessage = message => (
	`
		<span class="bc-form__error-message">${message}</span>
	`
);
