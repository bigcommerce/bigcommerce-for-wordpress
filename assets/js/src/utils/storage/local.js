const put = (key, value) => {
	window.localStorage.setItem(key, value);
};

const get = key => window.localStorage.getItem(key);

const remove = key => window.localStorage.removeItem(key);

const clear = () => {
	window.localStorage.clear();
};

export { put, get, remove, clear };
