/**
 *
 * Module: grunt-concurrent
 * Documentation: https://github.com/sindresorhus/grunt-concurrent
 * Example:
 *
 */

module.exports = {
	options: {
		logConcurrentOutput: true,
	},

	preflight: [
		[
			'eslint',
		],
		[
			'postcss:themeLint',
		],
		[
			'clean:themeMinCSS',
		],
	],

	dist: [
		[
			'postcss:theme',
			'postcss:themeMin',
			'header:theme',
		],
		[
			'postcss:themeAMP',
			'postcss:themeAMPMin',
			'postcss:themeCartAMP',
			'postcss:themeCartAMPMin',
			'header:themeAMP',
		],
		[
			'postcss:themeWPAdmin',
			'postcss:themeWPAdminMin',
			'header:themeWPAdmin',
		],
		[
			'postcss:themeGutenbergBlocks',
			'postcss:themeGutenbergBlocksMin',
			'header:themeGutenberg',
		],
		[
			'clean:themeMinJS',
			'webpack',
			'concat:themeMinVendors',
			'clean:themeMinVendorJS',
		],
		[
			'buildTimestamp',
			'setPHPConstant',
		],
	],
};
