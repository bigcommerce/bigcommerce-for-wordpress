/**
 *
 * Module: grunt-contrib-uglify
 * Documentation: https://npmjs.org/package/grunt-contrib-uglify
 *
 */

module.exports = {
	themeMin: {
		options: {
			banner: '/* Core: JS Master */\n',
			sourceMap: false,
			compress: {
				drop_console: true,
			},
		},
		files: {
			'<%= pkg._bc_public_js_dist_path %>vendorGlobal.min.js': [],
		},
	},
};
