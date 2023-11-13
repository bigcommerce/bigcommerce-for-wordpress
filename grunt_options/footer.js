/**
 *
 * Module: grunt-footer
 * Documentation: https://github.com/sindresorhus/grunt-footer
 *
 */

module.exports = {
	coreIconsVariables: {
		options: {
			text: '}',
		},
		files: {
			'<%= pkg._bc_public_pcss_path %>utilities/variables/_icons.pcss': ['<%= pkg._bc_public_pcss_path %>utilities/variables/_icons.pcss'],
		},
	},
};
