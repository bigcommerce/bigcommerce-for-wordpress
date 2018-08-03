/**
 * @template Inline error message templates for plugin errors.
 * @param message
 * @returns {string}
 */
export const paginationError = message => (
	`
		<span class="bc-alert bc-alert--error bc-pagination__error-message">${message}</span>
	`
);
