/**
 *
 * Module: grunt-contrib-copy
 * Documentation: https://github.com/gruntjs/grunt-contrib-copy
 * Example:
 *
 */

module.exports = {
	coreIconsFonts: {
		files: [
			{
				expand: true,
				flatten: true,
				src: [
					'<%= pkg._component_path %>/theme/icons/bigcommerce/fonts/*',
				],
				dest: '<%= pkg._bc_public_fonts_path %>icons-bigcommerce/',
			},
		],
	},

	coreIconsStyles: {
		files: [
			{
				expand: true,
				flatten: true,
				src: [
					'<%= pkg._component_path %>/theme/icons/bigcommerce/style.css',
				],
				dest: '<%= pkg._bc_public_pcss_path %>base/',
				rename: function(dest, src) {
					return dest + src.replace('style.css', '_icons.pcss');
				},
			},
		],
	},

	coreIconsVariables: {
		files: [
			{
				expand: true,
				flatten: true,
				src: [
					'<%= pkg._component_path %>/theme/icons/bigcommerce/variables.scss',
				],
				dest: '<%= pkg._bc_public_pcss_path %>utilities/variables/',
				rename: function(dest, src) {
					return dest + src.replace('variables.scss', '_icons.pcss');
				},
			},
		],
	},

	themeJS: {
		files: [
			{
				expand: true,
				flatten: true,
				src: [],
				dest: '<%= pkg._bc_public_js_vendor_path %>',
			},
		],
	},
};
