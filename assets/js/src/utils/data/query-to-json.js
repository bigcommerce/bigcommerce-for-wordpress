
const queryToJson = (params = '') => {
	const pairs = params.length ? params.split('&') : location.search.slice(1).split('&');
	const result = {};
	let pairArray = [];
	pairs.forEach((pair) => {
		pairArray = pair.split('=');
		result[pairArray[0]] = decodeURIComponent(pairArray[1] || '');
	});

	return JSON.parse(JSON.stringify(result));
};

export default queryToJson;
