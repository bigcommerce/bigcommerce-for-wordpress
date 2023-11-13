/**
 *
 * Module: grunt-shell
 * Documentation: https://github.com/sindresorhus/grunt-shell
 *
 */

module.exports = {
	install: {
		options: {
			stderr: false,
		},
		command: 'yarn install',
	},

	test: {
		options: {
			stderr: false,
		},
		command: 'npm run test',
	},
};
