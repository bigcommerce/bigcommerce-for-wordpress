
const updateQueryVar = (key, value, url = window.location.href) => {
	const re = new RegExp(`([?&])${key}=.*?(&|#|$)(.*)`, 'gi');

	let hash;
	let separator;
	let parsedUrl = url;

	if (re.test(url)) {
		if (typeof value !== 'undefined' && value !== null) {
			parsedUrl = url.replace(re, `$1${key}=${value}$2$3`);
		} else {
			hash = url.split('#');
			parsedUrl = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
			if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
				parsedUrl += `#${hash[1]}`;
			}
		}
	} else if (typeof value !== 'undefined' && value !== null) {
		separator = url.indexOf('?') !== -1 ? '&' : '?';
		hash = url.split('#');
		parsedUrl = `${hash[0]}${separator}${key}=${value}`;
		if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
			parsedUrl += `#${hash[1]}`;
		}
	}

	return parsedUrl;
};

export default updateQueryVar;
