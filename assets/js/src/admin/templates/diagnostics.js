import { I18N } from '../config/i18n';

const bigCommerceDiagnosticsValues = values => (
	Array.isArray(values.value) ?
		Object.values(values.value).map(strings => (
			!strings.label ? `${strings}<br>` : `${strings.label} ${strings.value ? `- ${strings.value}` : ''}<br>`
		)).join('')
		:
		values.value
);

const bigCommerceDiagnosticsLog = log => (
	`
	<div class="bc-diagnostics-data__meta-value-container">
		<pre>${log}</pre>
	</div>
	`
);

export const bigCommerceDiagnostics = data => (
	`
	<div class="bc-diagnostics-data">
	<h1 class="h1 bc-diagnostics-data--success">
		<i class="bc-icon icon-bc-order_confirmation"></i> ${I18N.messages.diagnostics_success_message}
	</h1>
	
	${Object.values(data).map(group => (
		group.key === 'bigcommerce' ?
			Object.values(group.value).map(values => (
				values.key === 'templateoverrides' && values.value.length > 0 ?
					`<div class="bc-diagnostics-data__notice">
						<i class="dashicons-before dashicons-warning"></i> 
						<span class="bc-diagnostics-data__notice--overrides">${I18N.messages.diagnostics_template_overrides_message}</span> 
					</div>`
				: ''
			)).join('')
		: ''
	)).join('')}
	
	${Object.values(data).map(group => (
		`<div class="bc-diagnostics-data__section bc-diagnostics-data__section-${group.key}">
			<h2 class="h2 bc-diagnostics-data__section-header">${group.label}</h2>
			
			${Object.values(group.value).map(values => (
				`
				<div class="bc-diagnostics-data__meta bc-diagnostics-data__meta-${values.key}">
					<div class="bc-diagnostics-data__meta-label">${values.label}</div>
					<div class="bc-diagnostics-data__meta-value">${

						values.key === 'importlogs' ?
							`${bigCommerceDiagnosticsLog(values.value)}`
							:
							`${bigCommerceDiagnosticsValues(values)}`
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

export const bigCommerceDiagnosticsRequestError = (
	`
	<div class="bc-diagnostics-data--error">
		<h4 class="h4 bc-diagnostics-data__section-header">${I18N.messages.diagnostics_request_error_header}</h4>
		<p>${I18N.messages.diagnostics_request_error_message}</p>
	</div>
	`
);
