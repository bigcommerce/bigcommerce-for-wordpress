/**
 *
 * Module: grunt-header
 * Documentation: https://github.com/sindresorhus/grunt-header
 *
 */

module.exports = {
	coreIconsStyle: {
		options: {
			text: '' +
			'/* -----------------------------------------------------------------------------\n' +
			' *\n' +
			' * Font Icons: Icons (via IcoMoon)\n' +
			' *\n' +
			' * ----------------------------------------------------------------------------- */\n' +
			'\n' +
			'/* stylelint-disable */\n'
		},
		files: {
			'<%= pkg._bc_public_pcss_path %>base/_icons.pcss': ['<%= pkg._bc_public_pcss_path %>base/_icons.pcss'],
		},
	},

	coreIconsVariables: {
		options: {
			text: '' +
			'/* -----------------------------------------------------------------------------\n' +
			' * Font Icons (via IcoMoon)\n' +
			' * ----------------------------------------------------------------------------- */\n' +
			'\n' +
			'/* stylelint-disable */\n' +
			'\n' +
			':root {'
		},
		files: {
			'<%= pkg._bc_public_pcss_path %>utilities/variables/_icons.pcss': ['<%= pkg._bc_public_pcss_path %>utilities/variables/_icons.pcss'],
		},
	},

	theme: {
		options: {
			text: '/* BigCommerce: Global CSS */',
		},
		files: {
			'<%= pkg._bc_css_dist_path %>master.min.css': ['<%= pkg._bc_css_dist_path %>master.min.css'],
		},
	},

	themeAMP: {
		options: {
			text: '/* BigCommerce: Global AMP CSS */',
		},
		files: {
			'<%= pkg._bc_css_dist_path %>master-amp.min.css': ['<%= pkg._bc_css_dist_path %>master-amp.min.css'],
		},
	},

	themeWPAdmin: {
		options: {
			text: '/* BigCommerce: WordPress Admin CSS */',
		},
		files: {
			'<%= pkg._bc_css_dist_path %>bc-admin.min.css': ['<%= pkg._bc_css_dist_path %>bc-admin.min.css'],
		},
	},

	themeGutenberg: {
		options: {
			text: '/* BigCommerce: Gutenberg Editor CSS */',
		},
		files: {
			'<%= pkg._bc_css_dist_path %>bc-gutenberg.min.css': ['<%= pkg._bc_css_dist_path %>bc-gutenberg.min.css'],
		},
	},
};
