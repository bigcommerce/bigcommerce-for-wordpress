const getCookie = (name) => {
	const match = document.cookie.match(new RegExp(`(^| )${name}=([^;]+)`));
	if (!match) {
		return '';
	}

	return match[2];
};

export { getCookie };
