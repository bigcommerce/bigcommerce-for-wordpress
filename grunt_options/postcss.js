/**
 *
 * Module: grunt-preprocess
 * Documentation: https://npmjs.org/package/grunt-preprocess
 *
 */

var postcssFunctions = require('../dev_components/theme/pcss/functions');

var compileOptions = {
	map: true,
	processors: [
		require('postcss-partial-import')({
			extension: ".pcss",
		}),
		require('postcss-mixins'),
		require('postcss-custom-properties'),
		require('postcss-simple-vars'),
		require('postcss-custom-media'),
		require('postcss-functions')({ functions: postcssFunctions }),
		require('postcss-quantity-queries'),
		require('postcss-aspect-ratio'),
		require('postcss-nested'),
		require('lost'),
		require('postcss-inline-svg'),
		require('postcss-cssnext'),
	],
};

var cssnanoOptions = {
	map: false,
	processors: [
		require('cssnano')({ zindex: false }),
	],
};

var lintOptions = {
	processors: [
		require('stylelint'),
		require('postcss-reporter')({ clearMessages: true, throwError: true, plugins: ['stylelint'] }),
	],
};

module.exports = {
	theme: {
		options: compileOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>master.css': '<%= pkg._bc_public_pcss_path %>master.pcss',
		},
	},

	themeAMP: {
		options: compileOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>master-amp.css': '<%= pkg._bc_public_pcss_path %>master-amp.pcss',
		},
	},

	themeCartAMP: {
		options: compileOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>cart-amp.css': '<%= pkg._bc_public_pcss_path %>cart-amp.pcss',
		},
	},

	themeWPAdmin: {
		options: compileOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>bc-admin.css': '<%= pkg._bc_admin_pcss_path %>bc-admin.pcss',
		},
	},

	themeGutenbergBlocks: {
		options: compileOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>bc-gutenberg.css': '<%= pkg._bc_admin_pcss_path %>bc-gutenberg.pcss',
		},
	},

	// Task: Minification

	themeMin: {
		options: cssnanoOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>master.min.css': '<%= pkg._bc_css_dist_path %>master.css',
		},
	},

	themeAMPMin: {
		options: cssnanoOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>master-amp.min.css': '<%= pkg._bc_css_dist_path %>master-amp.css',
		},
	},

	themeCartAMPMin: {
		options: cssnanoOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>cart-amp.min.css': '<%= pkg._bc_css_dist_path %>cart-amp.css',
		},
	},

	themeWPAdminMin: {
		options: cssnanoOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>bc-admin.min.css': '<%= pkg._bc_css_dist_path %>bc-admin.css',
		},
	},

	themeGutenbergBlocksMin: {
		options: cssnanoOptions,
		files: {
			'<%= pkg._bc_css_dist_path %>bc-gutenberg.min.css': '<%= pkg._bc_css_dist_path %>bc-gutenberg.css',
		},
	},

	// Task: Linting

	themeLint: {
		options: lintOptions,
		src: [
			'<%= pkg._bc_public_pcss_path %>**/*.pcss',
			'!<%= pkg._bc_public_pcss_path %>content/page/_legacy.pcss',
		],
	},
};
