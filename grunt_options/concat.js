/**
 *
 * Module: grunt-contrib-concat
 * Documentation: https://npmjs.org/package/grunt-contrib-concat
 *
 */

module.exports = {
	themeMinVendors: {
		src: [
			'<%= pkg._bc_public_js_dist_path %>vendorWebpack.min.js',
		],
		dest: '<%= pkg._bc_public_js_dist_path %>vendor.min.js',
	},
};
