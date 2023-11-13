/**
 *
 * Module: grunt-eslint
 * Documentation: https://github.com/sindresorhus/grunt-eslint
 * Example:
 *
 */

module.exports = {
	dist: [
		'<%= pkg._bc_public_js_src_path %>**/*.js',
		'<%= pkg._bc_admin_js_src_path %>**/*.js',
	],
};
