const path = require('path');
const webpack = require('webpack');

module.exports = {
	cache: true,
	externals: {
		jquery: 'jQuery',
		bigcommerce_config: 'bigcommerce_config',
	},
	resolve: {
		alias: {
			utils: path.resolve(__dirname, 'assets/js/src/utils'),
			adminConfig: path.resolve(__dirname, 'assets/js/src/admin/config'),
			publicConfig: path.resolve(__dirname, 'assets/js/src/public/config'),
			bcConstants: path.resolve(__dirname, 'assets/js/src/constants'),
		},
		extensions: ['.js'],
	},
	resolveLoader: {
		modules: [
			path.resolve(__dirname, 'node_modules'),
		],
	},
	module: {
		noParse: /node_modules\/vex-js\/dist\/js\/vex.js/,
		rules: [
			{
				test: /\.js$/,
				exclude: [/(node_modules)(?![/|\\](dom7|swiper))/],
				use: [
					{
						loader: 'babel-loader',
					},
				],
			},
		],
	},
	plugins: [
		new webpack.ProvidePlugin({}),
	],
};
