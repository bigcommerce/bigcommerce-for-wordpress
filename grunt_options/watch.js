/**
 *
 * Module: grunt-contrib-watch
 * Documentation: https://npmjs.org/package/grunt-contrib-watch
 *
 */

var defaultOpts = {
	spawn: false,
	livereload: true,
};

module.exports = {
	themeCSS: {
		files: [
			'<%= pkg._bc_public_pcss_path %>**/*.pcss',
			'!<%= pkg._bc_public_pcss_path %>**/*-amp.pcss',
			'!<%= pkg._bc_public_pcss_path %>amp/**/*',
			'!<%= pkg._bc_admin_pcss_path %>**/*.pcss',
		],
		tasks: [
			'postcss:theme',
		],
		options: defaultOpts,
	},

	themeAMP: {
		files: [
			'<%= pkg._bc_public_pcss_path %>**/*.pcss',
			'!<%= pkg._bc_admin_pcss_path %>**/*.pcss',
		],
		tasks: [
			'postcss:themeAMP',
			'postcss:themeAMPMin',
		],
		options: defaultOpts,
	},

	themeAdmin: {
		files: [
			'<%= pkg._bc_admin_pcss_path %>**/*.pcss',
			'!<%= pkg._bc_admin_pcss_path %>bc-gutenberg.pcss',
			'!<%= pkg._bc_admin_pcss_path %>gutenberg/**/*.pcss',
		],
		tasks: [
			'postcss:themeWPAdmin',
		],
		options: defaultOpts,
	},

	themeGutenberg: {
		files: [
			'<%= pkg._bc_admin_pcss_path %>gutenberg/**/*.pcss',
			'<%= pkg._bc_public_pcss_path %>utilities/**/*.pcss',
			'<%= pkg._bc_public_pcss_path %>content/**/*.pcss',
			'<%= pkg._bc_admin_pcss_path %>bc-gutenberg.pcss',
			'!<%= pkg._bc_public_pcss_path %>content/page/*.pcss',
			'!<%= pkg._bc_public_pcss_path %>content/cart/*.pcss',
		],
		tasks: [
			'postcss:themeGutenbergBlocks',
		],
		options: defaultOpts,
	},

	themeScripts: {
		files: [
			'<%= pkg._bc_public_js_src_path %>**/*.js',
		],
		tasks: [
			'webpack:themeDev',
		],
		options: defaultOpts,
	},

	adminScripts: {
		files: [
			'<%= pkg._bc_admin_js_src_path %>**/*.js',
			'!<%= pkg._bc_gutenberg_js_src_path %>**/*.js',
		],
		tasks: [
			'webpack:adminDev',
		],
		options: defaultOpts,
	},

	gutenbergAdminScripts: {
		files: [
			'<%= pkg._bc_gutenberg_js_src_path %>**/*.js',
		],
		tasks: [
			'webpack:gutenbergDev',
		],
		options: defaultOpts,
	},

	utilScripts: {
		files: [
			'<%= pkg._bc_public_js_util_path %>**/*.js',
		],
		tasks: [
			'webpack:themeDev',
			'webpack:adminDev',
		],
		options: defaultOpts,
	},

	themeTemplates: {
		files: [
			'<%= pkg._bc_assets_path %>/**/*.php',
			'<%= pkg._bc_assets_path %>/**/*.twig',
		],
		options: defaultOpts,
	},
};
