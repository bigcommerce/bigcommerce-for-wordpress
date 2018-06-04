/**
 * @template Select Field of States/Provinces.
 * @param states
 * @param initValue
 * @returns {string}
 */
export const stateSelectField = (states, fieldId, fieldName, initValue) => (
	`
	<select id="${fieldId}" class="bc-account-address-state" name="${fieldName}" data-js="bc-dynamic-state-control">
		${states.map(state => `
			<option value="${state.state}" data-state-abbr="" ${initValue === state.state ? 'selected' : ''}>${state.state}</option>
		`).join('')}
	</select>
	`
);
