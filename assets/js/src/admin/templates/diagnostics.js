import { I18N } from '../config/i18n';

export const bigCommerceDiagnostics = data => (
	`
	<div class="bc-diagnostics-data">
	<h1 class="h1 bc-diagnostics-data--success">
		<i class="bc-icon icon-bc-order_confirmation"></i> ${I18N.messages.diagnostics_success_message}
	</h1>
	${Object.values(data).map(group => (
		`<div class="bc-diagnostics-data__section">
			<h2 class="h2 bc-diagnostics-data__section-header">${group.label}</h2>
			
			${Object.values(group.value).map(values => (
				`
				<div class="bc-diagnostics-data__meta">
					<div class="bc-diagnostics-data__meta-label">${values.label}</div>
					<div class="bc-diagnostics-data__meta-value">
						${
						Array.isArray(values.value) ?
						Object.values(values.value).map(strings => (
							!strings.label ? `${strings}<br>` : `${strings.label} ${strings.value ? `- ${strings.value}` : ''}<br>`
						)).join('') :
						values.value
						}
					</div>
				</div>
				`
			)).join('')}
		</div>`
		)).join('')}
	</div>
	`
);

export const bigCommerceDiagnosticsError = (
	`
	<div class="bc-diagnostics-data--error">
		<h4 class="h4 bc-diagnostics-data__section-header">${I18N.messages.diagnostics_error_header}</h4>
		<p>${I18N.messages.diagnostics_error_message}</p>
	</div>
	`
);
