/**
 *
 * Module: gruntphpsetconstant
 * Documentation: https://www.npmjs.org/package/grunt-php-set-constant
 *
 */

module.exports = {
	config: {
		constant: 'BIGCOMMERCE_ASSETS_BUILD_TIMESTAMP',
		value: '<%= grunt.template.today("h.MM.mm.dd.yyyy") %>',
		file: 'build-timestamp.php',
	},
};
