/**
 *
 * Module: grunt-contrib-clean
 * Documentation: https://npmjs.org/package/grunt-contrib-clean
 *
 */

module.exports = {
	coreIconsStart: [
		'<%= pkg._component_path %>/theme/icons/bigcommerce',
		'<%= pkg._bc_public_fonts_path %>icons-bigcommerce',
		'<%= pkg._bc_public_pcss_path %>base/_icons.pcss',
		'<%= pkg._bc_public_pcss_path %>utilities/variables/_icons.pcss',
	],

	coreIconsEnd: [
		'<%= pkg._component_path %>/bigcommerce-icons.zip',
	],

	themeMinCSS: [
		'<%= pkg._bc_css_dist_path %>*.css',
	],

	themeMinJS: [
		'<%= pkg._bc_public_js_dist_path %>*.min.js',
		'<%= pkg._bc_admin_js_dist_path %>*.min.js',
	],

	themeMinVendorJS: [
		'<%= pkg._bc_public_js_dist_path %>vendorWebpack.min.js',
	],
};
