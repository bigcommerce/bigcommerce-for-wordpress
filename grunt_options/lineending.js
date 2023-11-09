module.exports = {
	mac: {},
	win: {
		options: {
			eol: 'crlf',
			overwrite: true,
		},
		files: {
			'': [
				'<%= pkg._bc_public_js_vendor_path %>**/*',
				'<%= pkg._bc_public_js_dist_path %>**/*',
				'<%= pkg._bc_css_dist_path %>**/*',
			],
		},
	},
};
