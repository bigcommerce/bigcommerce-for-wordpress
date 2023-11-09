/**
 * Temporary workaround for ssl issues
 * https://github.com/mzabriskie/axios/issues/535#issuecomment-262299969
 */
process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0';
module.exports = function (grunt) {
	/**
	 *
	 * Function to return object from grunt task options stored as files in the 'grunt_options' folder.
	 *
	 */

	function loadConfig(path) {

		var glob = require('glob');
		var object = {};
		var key;

		glob.sync('*', { cwd: path }).forEach(function (option) {
			key = option.replace(/\.js$/, '');
			object[key] = require(path + option);
		});

		return object;
	}

	/**
	 *
	 * Start up config by reading from package.json.
	 *
	 */

	var dev = grunt.file.exists('local-config.json') ? grunt.file.readJSON('local-config.json') : { proxy: 'square1.tribe', certs_path: '' };

	var config = {
		pkg: grunt.file.readJSON('package.json'),
		dev: dev,
	};

	/**
	 *
	 * Extend config with all the task options in /options based on the name, eg:
	 * watch.js => watch{}
	 *
	 */

	grunt.util._.extend(config, loadConfig('./grunt_options/'));

	/**
	 *
	 *  Apply config to Grunt.
	 *
	 */

	grunt.initConfig(config);

	/**
	 *
	 * Usually you would have to load each task one by one.
	 * The load grunt tasks module installed here will read the dependencies/devDependencies/peerDependencies in your package.json
	 * and load grunt tasks that match the provided patterns, eg 'grunt' below.
	 *
	 */

	require('load-grunt-tasks')(grunt);

	/**
	 *
	 * Tasks are registered here. Starts with default, which is run by simply running 'grunt' in your cli.
	 * All other use grunt + taskname.
	 *
	 */

	grunt.registerTask(
		'default', [
			'dist',
		]);

	grunt.registerTask(
		'amp', [
			'postcss:themeAMP',
			'postcss:themeAMPMin',
			'postcss:themeCartAMP',
			'postcss:themeCartAMPMin',
		]);

	grunt.registerTask(
		'wp-admin', [
			'postcss:themeWPAdmin',
			'postcss:themeWPAdminMin',
		]);

	grunt.registerTask(
		'wp-gutenblocks', [
			'postcss:themeGutenbergBlocks',
			'postcss:themeGutenbergBlocksMin',
		]);

	var le = grunt.option('le') || 'mac';

	grunt.registerTask(
		'build', [
			'clean:themeMinCSS',
			'postcss:theme',
			'postcss:themeMin',
			'header:theme',
			'postcss:themeAMP',
			'postcss:themeAMPMin',
			'header:themeAMP',
			'postcss:themeWPAdmin',
			'postcss:themeWPAdminMin',
			'header:themeWPAdmin',
			'postcss:themeGutenbergBlocks',
			'postcss:themeGutenbergBlocksMin',
			'header:themeGutenberg',
			'clean:themeMinJS',
			'webpack',
			'concat:themeMinVendors',
			'clean:themeMinVendorJS',
			'lineending:' + le,
			'buildTimestamp',
			'setPHPConstant',
		]);

	grunt.registerTask(
		'test', [
			// 'accessibility',
			'shell:test',
		]);

	grunt.registerTask(
		'lint', [
			'eslint',
			'postcss:themeLint',
		]);

	grunt.registerTask(
		'cheat', [
			'shell:install',
			'concurrent:dist',
			'lineending:' + le,
		]);

	grunt.registerTask(
		'dist', [
			'shell:install',
			'shell:test',
			'concurrent:preflight',
			'concurrent:dist',
			'lineending:' + le,
		]);

	grunt.registerTask(
		'dev', [
			'browserSync:dev',
			'watch',
		]);

	grunt.registerTask(
		'devDocker', [
			'browserSync:devDocker',
			'watch',
		]);

	grunt.registerTask(
		'icons', [
			'clean:coreIconsStart',
			'unzip:coreIcons',
			'copy:coreIconsFonts',
			'copy:coreIconsStyles',
			'copy:coreIconsVariables',
			'replace:coreIconsStyle',
			'replace:coreIconsVariables',
			'header:coreIconsStyle',
			'header:coreIconsVariables',
			'footer:coreIconsVariables',
			'concurrent:dist',
			'clean:coreIconsEnd',
			'lineending:' + le,
		]);

	grunt.registerTask(
		'buildTimestamp', 'set a PHP constant with the build timestamp', function() {
			grunt.file.write( 'build-timestamp.php', "<?php\ndefine('BIGCOMMERCE_ASSETS_BUILD_TIMESTAMP', '');\n")
		});

};
