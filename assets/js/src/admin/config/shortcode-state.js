
export default {
	isFetching: false,
	isGutenberg: false,
	currentEditor: '',
	productHTML: '',
	wpAPIDisplaySettings: {
		order: '',
		orderby: '',
		per_page: '',
	},
	wpAPIQueryObj: {
		bigcommerce_flag: [],
		bigcommerce_brand: [],
		bigcommerce_category: [],
		bigcommerce_channel: '',
		recent: [],
		search: [],
	},
	selectedProducts: {
		bc_id: [],
	},
	insertCallback: false,
};
