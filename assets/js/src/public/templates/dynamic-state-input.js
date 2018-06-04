/**
 * @template Input Field of States/Provinces.
 * @param fieldId
 * @param fieldName
 * @param initValue
 * @returns {string}
 */
export const stateInputField = (fieldId, fieldName, initValue) => (
	`<input type="text" id="${fieldId}" name="${fieldName}" data-js="bc-dynamic-state-control" value="${!initValue ? '' : initValue}" />`
);
