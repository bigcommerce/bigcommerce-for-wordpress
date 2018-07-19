export const termTemplate = termData => (
	`
		<li class="bc-shortcode-ui__terms-list-item">
			<span class="bc-shortcode-ui__terms-list-term">
				${termData.label}
				<button type="button" data-value="${termData.value}" class="bc-shortcode-ui__remove-term" data-js="bc-shortcode-ui-remove-term">
					<i class="dashicons dashicons-no-alt"></i>
				</button>
			</span>
		</li>
	`
);
