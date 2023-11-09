module.exports = {
	options: {
		accessibilityLevel: 'WCAG2AA',
		browser: true,
		force: true,
		maxBuffer: '1024*1024',
		reportLocation: 'reports',
		reportType: 'txt',
		verbose: false,
		reportLevels: {
			notice: false,
			warning: false,
			error: true,
		},
	},
	test: {
		options: {
			urls: [
				'https://<%= dev.proxy %>',
			],
		},
	},
};
