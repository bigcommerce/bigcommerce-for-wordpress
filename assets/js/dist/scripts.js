webpackJsonp([0],[
/* 0 */,
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

var _Object$getOwnPropertyDescriptor = __webpack_require__(131);

var _Object$defineProperty = __webpack_require__(134);

function _interopRequireWildcard(obj) {
  if (obj && obj.__esModule) {
    return obj;
  } else {
    var newObj = {};

    if (obj != null) {
      for (var key in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, key)) {
          var desc = _Object$defineProperty && _Object$getOwnPropertyDescriptor ? _Object$getOwnPropertyDescriptor(obj, key) : {};

          if (desc.get || desc.set) {
            _Object$defineProperty(newObj, key, desc);
          } else {
            newObj[key] = obj[key];
          }
        }
      }
    }

    newObj.default = obj;
    return newObj;
  }
}

module.exports = _interopRequireWildcard;

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.insertBefore = exports.insertAfter = exports.closest = exports.getNodes = exports.convertElements = exports.hasClassFromArray = exports.removeClassThatContains = exports.removeClass = exports.hasClass = exports.getChildren = exports.addClass = void 0;

/**
 * @module
 * @description Some vanilla js cross browser utils
 */

/**
 * Add a class to a dom element or exit safely if not set
 *
 * @param el Node
 * @param className Class string
 * @returns {*} Node or false
 */
var addClass = function addClass(el) {
  var className = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var element = el;

  if (!element) {
    return false;
  }

  element.classList.add(className);
  return element;
};
/**
 *
 * Get immediate child nodes and return an array of them
 *
 * @param el
 * @returns {Array} Iterable array of dom nodes
 */


exports.addClass = addClass;

var getChildren = function getChildren(el) {
  var children = [];
  var i = el.children.length;

  for (i; i--;) {
    // eslint-disable-line
    if (el.children[i].nodeType !== 8) {
      children.unshift(el.children[i]);
    }
  }

  return children;
};
/**
 *
 * Test if a dom node has a class or returns false if el not defined
 *
 * @param el
 * @param className
 * @returns {boolean}
 */


exports.getChildren = getChildren;

var hasClass = function hasClass(el) {
  var className = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (!el) {
    return false;
  }

  return el.classList.contains(className);
};
/**
 * Removes a class from the dom node
 *
 * @param el
 * @param className
 * @returns {*} returns false or el if passed
 */


exports.hasClass = hasClass;

var removeClass = function removeClass(el, className) {
  var element = el;

  if (!element) {
    return false;
  }

  element.classList.remove(className);
  return element;
};
/**
 * Remove a class from an element that contains a string
 *
 * @param el
 * @param string
 */


exports.removeClass = removeClass;

var removeClassThatContains = function removeClassThatContains(el) {
  var string = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  for (var i = 0; i < el.classList.length; i++) {
    if (el.classList.item(i).indexOf(string) !== -1) {
      el.classList.remove(el.classList.item(i));
    }
  }
};
/**
 * Compares an els classList against an array of strings to see if any match
 * @param el the element to check against
 * @param arr The array of classes as strings to test against
 * @param prefix optional prefix string applied to all test strings
 * @param suffix optional suffix string
 */


exports.removeClassThatContains = removeClassThatContains;

var hasClassFromArray = function hasClassFromArray(el) {
  var arr = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  var prefix = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
  var suffix = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';
  return arr.some(function (c) {
    return el.classList.contains("".concat(prefix).concat(c).concat(suffix));
  });
};
/**
 * Highly efficient function to convert a nodelist into a standard array. Allows you to run Array.forEach
 *
 * @param {Element|NodeList} elements to convert
 * @returns {Array} Of converted elements
 */


exports.hasClassFromArray = hasClassFromArray;

var convertElements = function convertElements() {
  var elements = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var converted = [];
  var i = elements.length;

  for (i; i--; converted.unshift(elements[i])) {
    ;
  } // eslint-disable-line


  return converted;
};
/**
 * Should be used at all times for getting nodes throughout our app. Please use the data-js attribute whenever possible
 *
 * @param selector The selector string to search for. If arg 4 is false (default) then we search for [data-js="selector"]
 * @param convert Convert the NodeList to an array? Then we can Array.forEach directly. Uses convertElements from above
 * @param node Parent node to search from. Defaults to document
 * @param custom Is this a custom selector where we don't want to use the data-js attribute?
 * @returns {NodeList}
 */


exports.convertElements = convertElements;

var getNodes = function getNodes() {
  var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var convert = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var node = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : document;
  var custom = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
  var selectorString = custom ? selector : "[data-js=\"".concat(selector, "\"]");
  var nodes = node.querySelectorAll(selectorString);

  if (convert) {
    nodes = convertElements(nodes);
  }

  return nodes;
};
/**
 * Gets the closest ancestor that matches a selector string
 *
 * @param el
 * @param selector
 * @returns {*}
 */


exports.getNodes = getNodes;

var closest = function closest(el, selector) {
  var matchesFn;
  var parent;
  ['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'].some(function (fn) {
    if (typeof document.body[fn] === 'function') {
      matchesFn = fn;
      return true;
    }
    /* istanbul ignore next */


    return false;
  });

  while (el) {
    parent = el.parentElement;

    if (parent && parent[matchesFn](selector)) {
      return parent;
    }

    el = parent; // eslint-disable-line
  }

  return null;
};
/**
 * Insert a node after another node
 *
 * @param newNode {Element|NodeList}
 * @param referenceNode {Element|NodeList}
 */


exports.closest = closest;

var insertAfter = function insertAfter(newNode, referenceNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextElementSibling);
};
/**
 * Insert a node before another node
 *
 * @param newNode {Element|NodeList}
 * @param referenceNode {Element|NodeList}
 */


exports.insertAfter = insertAfter;

var insertBefore = function insertBefore(newNode, referenceNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode);
};

exports.insertBefore = insertBefore;

/***/ }),
/* 3 */,
/* 4 */,
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.trigger = exports.ready = exports.on = void 0;

var _assign2 = _interopRequireDefault(__webpack_require__(198));

var on = function on(el, name, handler) {
  if (el.addEventListener) {
    el.addEventListener(name, handler);
  } else {
    el.attachEvent("on".concat(name), function () {
      handler.call(el);
    });
  }
};

exports.on = on;

var ready = function ready(fn) {
  if (document.readyState !== 'loading') {
    fn();
  } else if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', fn);
  } else {
    document.attachEvent('onreadystatechange', function () {
      if (document.readyState !== 'loading') {
        fn();
      }
    });
  }
};

exports.ready = ready;

var trigger = function trigger(opts) {
  var event;
  var options = (0, _assign2.default)({
    data: {},
    el: document,
    event: '',
    native: true
  }, opts);

  if (options.native) {
    event = document.createEvent('HTMLEvents');
    event.initEvent(options.event, true, false);
  } else {
    try {
      event = new CustomEvent(options.event, {
        detail: options.data
      });
    } catch (e) {
      event = document.createEvent('CustomEvent');
      event.initCustomEvent(options.event, true, true, options.data);
    }
  }

  options.el.dispatchEvent(event);
};

exports.trigger = trigger;

/***/ }),
/* 6 */,
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.NLS = void 0;
var NLS = window.bigcommerce_i18n || {};
exports.NLS = NLS;

/***/ }),
/* 8 */,
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

var baseDelay = __webpack_require__(311),
    baseRest = __webpack_require__(75),
    toNumber = __webpack_require__(74);

/**
 * Invokes `func` after `wait` milliseconds. Any additional arguments are
 * provided to `func` when it's invoked.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Function
 * @param {Function} func The function to delay.
 * @param {number} wait The number of milliseconds to delay invocation.
 * @param {...*} [args] The arguments to invoke `func` with.
 * @returns {number} Returns the timer id.
 * @example
 *
 * _.delay(function(text) {
 *   console.log(text);
 * }, 1000, 'later');
 * // => Logs 'later' after one second.
 */
var delay = baseRest(function(func, wait, args) {
  return baseDelay(func, toNumber(wait) || 0, args);
});

module.exports = delay;


/***/ }),
/* 10 */,
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.BANNERS = exports.COUPON_CODE_REMOVE = exports.COUPON_CODE_ADD = exports.CURRENCY_CODE = exports.SHIPPING_API_METHODS = exports.SHIPPING_API_ZONES = exports.STORE_DOMAIN = exports.MINI_CART = exports.PRICING_API_NONCE = exports.PRICING_API_URL = exports.PRODUCT_MESSAGES = exports.COUNTRIES_OBJ = exports.AJAX_CART_NONCE = exports.AJAX_CART_ENABLED = exports.CART_API_BASE = exports.TEMPLATE_URL = exports.IMAGES_URL = exports.CART = exports.CONFIG = void 0;
var CONFIG = window.bigcommerce_config || {};
exports.CONFIG = CONFIG;
var CART = CONFIG.cart || {};
exports.CART = CART;
var IMAGES_URL = CONFIG.images_url || {};
exports.IMAGES_URL = IMAGES_URL;
var TEMPLATE_URL = CONFIG.template_url || {};
exports.TEMPLATE_URL = TEMPLATE_URL;
var CART_API_BASE = CONFIG.cart.api_url || '';
exports.CART_API_BASE = CART_API_BASE;
var AJAX_CART_ENABLED = CONFIG.cart.ajax_enabled || '';
exports.AJAX_CART_ENABLED = AJAX_CART_ENABLED;
var AJAX_CART_NONCE = CONFIG.cart.ajax_cart_nonce || '';
exports.AJAX_CART_NONCE = AJAX_CART_NONCE;
var COUNTRIES_OBJ = CONFIG.countries || {};
exports.COUNTRIES_OBJ = COUNTRIES_OBJ;
var PRODUCT_MESSAGES = CONFIG.product.messages || '';
exports.PRODUCT_MESSAGES = PRODUCT_MESSAGES;
var PRICING_API_URL = CONFIG.pricing.api_url || '';
exports.PRICING_API_URL = PRICING_API_URL;
var PRICING_API_NONCE = CONFIG.pricing.ajax_pricing_nonce || '';
exports.PRICING_API_NONCE = PRICING_API_NONCE;
var MINI_CART = CONFIG.cart.mini_cart.enabled || false;
exports.MINI_CART = MINI_CART;
var STORE_DOMAIN = CONFIG.store_domain || '';
exports.STORE_DOMAIN = STORE_DOMAIN;
var SHIPPING_API_ZONES = CONFIG.cart.zones_api_url || '';
exports.SHIPPING_API_ZONES = SHIPPING_API_ZONES;
var SHIPPING_API_METHODS = CONFIG.cart.methods_api_url || '';
exports.SHIPPING_API_METHODS = SHIPPING_API_METHODS;
var CURRENCY_CODE = CONFIG.currency_code || '';
exports.CURRENCY_CODE = CURRENCY_CODE;
var COUPON_CODE_ADD = CONFIG.cart.coupon_code_add_api_url || '';
exports.COUPON_CODE_ADD = COUPON_CODE_ADD;
var COUPON_CODE_REMOVE = CONFIG.cart.coupon_code_delete_api_url || '';
exports.COUPON_CODE_REMOVE = COUPON_CODE_REMOVE;
var BANNERS = CONFIG.banners || [];
exports.BANNERS = BANNERS;

/***/ }),
/* 12 */
/***/ (function(module, exports) {

/**
 * Checks if `value` is classified as an `Array` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an array, else `false`.
 * @example
 *
 * _.isArray([1, 2, 3]);
 * // => true
 *
 * _.isArray(document.body.children);
 * // => false
 *
 * _.isArray('abc');
 * // => false
 *
 * _.isArray(_.noop);
 * // => false
 */
var isArray = Array.isArray;

module.exports = isArray;


/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

var freeGlobal = __webpack_require__(112);

/** Detect free variable `self`. */
var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

/** Used as a reference to the global object. */
var root = freeGlobal || freeSelf || Function('return this')();

module.exports = root;


/***/ }),
/* 14 */,
/* 15 */,
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

var baseKeys = __webpack_require__(122),
    getTag = __webpack_require__(174),
    isArguments = __webpack_require__(51),
    isArray = __webpack_require__(12),
    isArrayLike = __webpack_require__(29),
    isBuffer = __webpack_require__(80),
    isPrototype = __webpack_require__(79),
    isTypedArray = __webpack_require__(81);

/** `Object#toString` result references. */
var mapTag = '[object Map]',
    setTag = '[object Set]';

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Checks if `value` is an empty object, collection, map, or set.
 *
 * Objects are considered empty if they have no own enumerable string keyed
 * properties.
 *
 * Array-like values such as `arguments` objects, arrays, buffers, strings, or
 * jQuery-like collections are considered empty if they have a `length` of `0`.
 * Similarly, maps and sets are considered empty if they have a `size` of `0`.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is empty, else `false`.
 * @example
 *
 * _.isEmpty(null);
 * // => true
 *
 * _.isEmpty(true);
 * // => true
 *
 * _.isEmpty(1);
 * // => true
 *
 * _.isEmpty([1, 2, 3]);
 * // => false
 *
 * _.isEmpty({ 'a': 1 });
 * // => false
 */
function isEmpty(value) {
  if (value == null) {
    return true;
  }
  if (isArrayLike(value) &&
      (isArray(value) || typeof value == 'string' || typeof value.splice == 'function' ||
        isBuffer(value) || isTypedArray(value) || isArguments(value))) {
    return !value.length;
  }
  var tag = getTag(value);
  if (tag == mapTag || tag == setTag) {
    return !value.size;
  }
  if (isPrototype(value)) {
    return !baseKeys(value).length;
  }
  for (var key in value) {
    if (hasOwnProperty.call(value, key)) {
      return false;
    }
  }
  return true;
}

module.exports = isEmpty;


/***/ }),
/* 20 */
/***/ (function(module, exports, __webpack_require__) {

var baseIsNative = __webpack_require__(199),
    getValue = __webpack_require__(202);

/**
 * Gets the native function at `key` of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {string} key The key of the method to get.
 * @returns {*} Returns the function if it's native, else `undefined`.
 */
function getNative(object, key) {
  var value = getValue(object, key);
  return baseIsNative(value) ? value : undefined;
}

module.exports = getNative;


/***/ }),
/* 21 */,
/* 22 */,
/* 23 */
/***/ (function(module, exports, __webpack_require__) {

var toString = __webpack_require__(135);

/** Used to generate unique IDs. */
var idCounter = 0;

/**
 * Generates a unique ID. If `prefix` is given, the ID is appended to it.
 *
 * @static
 * @since 0.1.0
 * @memberOf _
 * @category Util
 * @param {string} [prefix=''] The value to prefix the ID with.
 * @returns {string} Returns the unique ID.
 * @example
 *
 * _.uniqueId('contact_');
 * // => 'contact_104'
 *
 * _.uniqueId();
 * // => '105'
 */
function uniqueId(prefix) {
  var id = ++idCounter;
  return toString(prefix) + id;
}

module.exports = uniqueId;


/***/ }),
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * JavaScript Cookie v2.2.0
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
	var registeredInModuleLoader = false;
	if (true) {
		!(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
				__WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		registeredInModuleLoader = true;
	}
	if (true) {
		module.exports = factory();
		registeredInModuleLoader = true;
	}
	if (!registeredInModuleLoader) {
		var OldCookies = window.Cookies;
		var api = window.Cookies = factory();
		api.noConflict = function () {
			window.Cookies = OldCookies;
			return api;
		};
	}
}(function () {
	function extend () {
		var i = 0;
		var result = {};
		for (; i < arguments.length; i++) {
			var attributes = arguments[ i ];
			for (var key in attributes) {
				result[key] = attributes[key];
			}
		}
		return result;
	}

	function init (converter) {
		function api (key, value, attributes) {
			var result;
			if (typeof document === 'undefined') {
				return;
			}

			// Write

			if (arguments.length > 1) {
				attributes = extend({
					path: '/'
				}, api.defaults, attributes);

				if (typeof attributes.expires === 'number') {
					var expires = new Date();
					expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
					attributes.expires = expires;
				}

				// We're using "expires" because "max-age" is not supported by IE
				attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

				try {
					result = JSON.stringify(value);
					if (/^[\{\[]/.test(result)) {
						value = result;
					}
				} catch (e) {}

				if (!converter.write) {
					value = encodeURIComponent(String(value))
						.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
				} else {
					value = converter.write(value, key);
				}

				key = encodeURIComponent(String(key));
				key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
				key = key.replace(/[\(\)]/g, escape);

				var stringifiedAttributes = '';

				for (var attributeName in attributes) {
					if (!attributes[attributeName]) {
						continue;
					}
					stringifiedAttributes += '; ' + attributeName;
					if (attributes[attributeName] === true) {
						continue;
					}
					stringifiedAttributes += '=' + attributes[attributeName];
				}
				return (document.cookie = key + '=' + value + stringifiedAttributes);
			}

			// Read

			if (!key) {
				result = {};
			}

			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all. Also prevents odd result when
			// calling "get()"
			var cookies = document.cookie ? document.cookie.split('; ') : [];
			var rdecode = /(%[0-9A-Z]{2})+/g;
			var i = 0;

			for (; i < cookies.length; i++) {
				var parts = cookies[i].split('=');
				var cookie = parts.slice(1).join('=');

				if (!this.json && cookie.charAt(0) === '"') {
					cookie = cookie.slice(1, -1);
				}

				try {
					var name = parts[0].replace(rdecode, decodeURIComponent);
					cookie = converter.read ?
						converter.read(cookie, name) : converter(cookie, name) ||
						cookie.replace(rdecode, decodeURIComponent);

					if (this.json) {
						try {
							cookie = JSON.parse(cookie);
						} catch (e) {}
					}

					if (key === name) {
						result = cookie;
						break;
					}

					if (!key) {
						result[name] = cookie;
					}
				} catch (e) {}
			}

			return result;
		}

		api.set = api;
		api.get = function (key) {
			return api.call(api, key);
		};
		api.getJSON = function () {
			return api.apply({
				json: true
			}, [].slice.call(arguments));
		};
		api.defaults = {};

		api.remove = function (key, attributes) {
			api(key, '', extend(attributes, {
				expires: -1
			}));
		};

		api.withConverter = init;

		return api;
	}

	return init(function () {});
}));


/***/ }),
/* 25 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.DEPRECATED_CART_ITEM_COUNT_COOKIE = exports.DEPRECATED_CART_ID_COOKIE_NAME = exports.CART_ITEM_COUNT_COOKIE = exports.CART_ID_COOKIE_NAME = void 0;
var CART_ID_COOKIE_NAME = 'wp-bigcommerce_cart_id';
exports.CART_ID_COOKIE_NAME = CART_ID_COOKIE_NAME;
var CART_ITEM_COUNT_COOKIE = 'wp-bigcommerce_cart_item_count';
exports.CART_ITEM_COUNT_COOKIE = CART_ITEM_COUNT_COOKIE;
var DEPRECATED_CART_ID_COOKIE_NAME = 'bigcommerce_cart_id';
exports.DEPRECATED_CART_ID_COOKIE_NAME = DEPRECATED_CART_ID_COOKIE_NAME;
var DEPRECATED_CART_ITEM_COUNT_COOKIE = 'bigcommerce_cart_item_count';
exports.DEPRECATED_CART_ITEM_COUNT_COOKIE = DEPRECATED_CART_ITEM_COUNT_COOKIE;

/***/ }),
/* 26 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.wpAPICouponCodes = exports.wpAPIGetShippingMethods = exports.wpAPIGetShippingZones = exports.wpAPIProductPricing = exports.wpAdminAjax = exports.wpAPIProductComponentPreview = exports.wpAPIProductsPreview = exports.wpAPIMiniCartGet = exports.wpAPICartDelete = exports.wpAPIAddToCartAjax = exports.wpAPICartUpdate = exports.wpAPIShortcodeBuilder = exports.wpAPIPagedProductLookup = exports.wpAPIProductLookup = void 0;

var _superagent = _interopRequireDefault(__webpack_require__(107));

var _wpSettings = __webpack_require__(399);

var _gutenbergSettings = __webpack_require__(402);

/**
 * @module Ajax request functions.
 * @description Setup ajax requests via Super Agent and export for modular usage.
 */
var wpAPIProductLookup = function wpAPIProductLookup() {
  var queryString = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  return _superagent.default.get(_wpSettings.PRODUCTS_ENDPOINT).query(queryString);
};

exports.wpAPIProductLookup = wpAPIProductLookup;

var wpAPIPagedProductLookup = function wpAPIPagedProductLookup(URL) {
  return _superagent.default.get(URL);
};

exports.wpAPIPagedProductLookup = wpAPIPagedProductLookup;

var wpAPIShortcodeBuilder = function wpAPIShortcodeBuilder() {
  var queryString = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  return _superagent.default.get(_wpSettings.SHORTCODE_ENDPOINT).query(queryString);
};

exports.wpAPIShortcodeBuilder = wpAPIShortcodeBuilder;

var wpAPICartUpdate = function wpAPICartUpdate(cartURL) {
  var querySrting = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  return _superagent.default.put(cartURL).query(querySrting);
};

exports.wpAPICartUpdate = wpAPICartUpdate;

var wpAPIAddToCartAjax = function wpAPIAddToCartAjax(cartURL) {
  var querySrting = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  return _superagent.default.post(cartURL).query(querySrting).timeout({
    response: 15000,
    // Wait 15 seconds for the server to start sending,
    deadline: 60000 // but allow 1 minute for the file to finish loading.

  });
};

exports.wpAPIAddToCartAjax = wpAPIAddToCartAjax;

var wpAPICartDelete = function wpAPICartDelete(cartURL) {
  return _superagent.default.post(cartURL);
};

exports.wpAPICartDelete = wpAPICartDelete;

var wpAPIMiniCartGet = function wpAPIMiniCartGet(cartURL) {
  return _superagent.default.get(cartURL);
};

exports.wpAPIMiniCartGet = wpAPIMiniCartGet;

var wpAPIProductsPreview = function wpAPIProductsPreview() {
  var queryObj = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return _superagent.default.get(_gutenbergSettings.GUTENBERG_PRODUCTS.preview_url).query(queryObj);
};

exports.wpAPIProductsPreview = wpAPIProductsPreview;

var wpAPIProductComponentPreview = function wpAPIProductComponentPreview() {
  var queryObj = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return _superagent.default.get(_gutenbergSettings.GUTENBERG_PRODUCT_COMPONENTS.preview_url).query(queryObj);
};

exports.wpAPIProductComponentPreview = wpAPIProductComponentPreview;

var wpAdminAjax = function wpAdminAjax() {
  var queryObj = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return _superagent.default.get(_wpSettings.ADMIN_AJAX).query(queryObj).timeout({
    response: 20000,
    // Wait 20 seconds for the server to start sending,
    deadline: 60000 // but allow 1 minute for the file to finish loading.

  });
};

exports.wpAdminAjax = wpAdminAjax;

var wpAPIProductPricing = function wpAPIProductPricing() {
  var pricingURL = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var pricingNonce = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var productsObj = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  return _superagent.default.post(pricingURL).set('Content-Type', 'application/json').set('X-WP-Nonce', pricingNonce).send(productsObj).timeout({
    response: 15000,
    // Wait 15 seconds for the server to start sending,
    deadline: 60000 // but allow 1 minute for the file to finish loading.

  });
};

exports.wpAPIProductPricing = wpAPIProductPricing;

var wpAPIGetShippingZones = function wpAPIGetShippingZones(URL) {
  return _superagent.default.get(URL);
};

exports.wpAPIGetShippingZones = wpAPIGetShippingZones;

var wpAPIGetShippingMethods = function wpAPIGetShippingMethods(url) {
  var zoneID = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  return _superagent.default.get("".concat(url, "/").concat(zoneID, "/methods/html"));
};

exports.wpAPIGetShippingMethods = wpAPIGetShippingMethods;

var wpAPICouponCodes = function wpAPICouponCodes() {
  var couponCodeURL = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var queryObj = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var couponsNonce = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
  return _superagent.default.post(couponCodeURL).set('Content-Type', 'application/json').set('X-WP-Nonce', couponsNonce).query(queryObj).timeout({
    response: 15000,
    // Wait 15 seconds for the server to start sending,
    deadline: 30000 // but allow 30 seconds for the request to finish processing.

  });
};

exports.wpAPICouponCodes = wpAPICouponCodes;

/***/ }),
/* 27 */
/***/ (function(module, exports) {

/**
 * Checks if `value` is the
 * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
 * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
 * @example
 *
 * _.isObject({});
 * // => true
 *
 * _.isObject([1, 2, 3]);
 * // => true
 *
 * _.isObject(_.noop);
 * // => true
 *
 * _.isObject(null);
 * // => false
 */
function isObject(value) {
  var type = typeof value;
  return value != null && (type == 'object' || type == 'function');
}

module.exports = isObject;


/***/ }),
/* 28 */
/***/ (function(module, exports) {

/**
 * Checks if `value` is object-like. A value is object-like if it's not `null`
 * and has a `typeof` result of "object".
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
 * @example
 *
 * _.isObjectLike({});
 * // => true
 *
 * _.isObjectLike([1, 2, 3]);
 * // => true
 *
 * _.isObjectLike(_.noop);
 * // => false
 *
 * _.isObjectLike(null);
 * // => false
 */
function isObjectLike(value) {
  return value != null && typeof value == 'object';
}

module.exports = isObjectLike;


/***/ }),
/* 29 */
/***/ (function(module, exports, __webpack_require__) {

var isFunction = __webpack_require__(117),
    isLength = __webpack_require__(77);

/**
 * Checks if `value` is array-like. A value is considered array-like if it's
 * not a function and has a `value.length` that's an integer greater than or
 * equal to `0` and less than or equal to `Number.MAX_SAFE_INTEGER`.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
 * @example
 *
 * _.isArrayLike([1, 2, 3]);
 * // => true
 *
 * _.isArrayLike(document.body.children);
 * // => true
 *
 * _.isArrayLike('abc');
 * // => true
 *
 * _.isArrayLike(_.noop);
 * // => false
 */
function isArrayLike(value) {
  return value != null && isLength(value.length) && !isFunction(value);
}

module.exports = isArrayLike;


/***/ }),
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(289);

/***/ }),
/* 34 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.HANDLE_COUPON_CODE = exports.HANDLE_CART_STATE = exports.AJAX_CART_UPDATE = void 0;
// custom events
var AJAX_CART_UPDATE = 'bigcommerce/ajax_cart_update';
exports.AJAX_CART_UPDATE = AJAX_CART_UPDATE;
var HANDLE_CART_STATE = 'bigcommerce/handle_cart_state';
exports.HANDLE_CART_STATE = HANDLE_CART_STATE;
var HANDLE_COUPON_CODE = 'bigcommerce/handle_coupon_code';
exports.HANDLE_COUPON_CODE = HANDLE_COUPON_CODE;

/***/ }),
/* 35 */
/***/ (function(module, exports, __webpack_require__) {

var Symbol = __webpack_require__(36),
    getRawTag = __webpack_require__(196),
    objectToString = __webpack_require__(197);

/** `Object#toString` result references. */
var nullTag = '[object Null]',
    undefinedTag = '[object Undefined]';

/** Built-in value references. */
var symToStringTag = Symbol ? Symbol.toStringTag : undefined;

/**
 * The base implementation of `getTag` without fallbacks for buggy environments.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the `toStringTag`.
 */
function baseGetTag(value) {
  if (value == null) {
    return value === undefined ? undefinedTag : nullTag;
  }
  return (symToStringTag && symToStringTag in Object(value))
    ? getRawTag(value)
    : objectToString(value);
}

module.exports = baseGetTag;


/***/ }),
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

var root = __webpack_require__(13);

/** Built-in value references. */
var Symbol = root.Symbol;

module.exports = Symbol;


/***/ }),
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */,
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _default = {
  isFetching: false,
  instances: {
    carts: {}
  }
};
exports.default = _default;

/***/ }),
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

var baseGetTag = __webpack_require__(35),
    isObjectLike = __webpack_require__(28);

/** `Object#toString` result references. */
var symbolTag = '[object Symbol]';

/**
 * Checks if `value` is classified as a `Symbol` primitive or object.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
 * @example
 *
 * _.isSymbol(Symbol.iterator);
 * // => true
 *
 * _.isSymbol('abc');
 * // => false
 */
function isSymbol(value) {
  return typeof value == 'symbol' ||
    (isObjectLike(value) && baseGetTag(value) == symbolTag);
}

module.exports = isSymbol;


/***/ }),
/* 49 */
/***/ (function(module, exports) {

/**
 * Performs a
 * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
 * comparison between two values to determine if they are equivalent.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to compare.
 * @param {*} other The other value to compare.
 * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
 * @example
 *
 * var object = { 'a': 1 };
 * var other = { 'a': 1 };
 *
 * _.eq(object, object);
 * // => true
 *
 * _.eq(object, other);
 * // => false
 *
 * _.eq('a', 'a');
 * // => true
 *
 * _.eq('a', Object('a'));
 * // => false
 *
 * _.eq(NaN, NaN);
 * // => true
 */
function eq(value, other) {
  return value === other || (value !== value && other !== other);
}

module.exports = eq;


/***/ }),
/* 50 */
/***/ (function(module, exports, __webpack_require__) {

var arrayLikeKeys = __webpack_require__(211),
    baseKeys = __webpack_require__(122),
    isArrayLike = __webpack_require__(29);

/**
 * Creates an array of the own enumerable property names of `object`.
 *
 * **Note:** Non-object values are coerced to objects. See the
 * [ES spec](http://ecma-international.org/ecma-262/7.0/#sec-object.keys)
 * for more details.
 *
 * @static
 * @since 0.1.0
 * @memberOf _
 * @category Object
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 *   this.b = 2;
 * }
 *
 * Foo.prototype.c = 3;
 *
 * _.keys(new Foo);
 * // => ['a', 'b'] (iteration order is not guaranteed)
 *
 * _.keys('hi');
 * // => ['0', '1']
 */
function keys(object) {
  return isArrayLike(object) ? arrayLikeKeys(object) : baseKeys(object);
}

module.exports = keys;


/***/ }),
/* 51 */
/***/ (function(module, exports, __webpack_require__) {

var baseIsArguments = __webpack_require__(213),
    isObjectLike = __webpack_require__(28);

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/** Built-in value references. */
var propertyIsEnumerable = objectProto.propertyIsEnumerable;

/**
 * Checks if `value` is likely an `arguments` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an `arguments` object,
 *  else `false`.
 * @example
 *
 * _.isArguments(function() { return arguments; }());
 * // => true
 *
 * _.isArguments([1, 2, 3]);
 * // => false
 */
var isArguments = baseIsArguments(function() { return arguments; }()) ? baseIsArguments : function(value) {
  return isObjectLike(value) && hasOwnProperty.call(value, 'callee') &&
    !propertyIsEnumerable.call(value, 'callee');
};

module.exports = isArguments;


/***/ }),
/* 52 */,
/* 53 */,
/* 54 */,
/* 55 */,
/* 56 */,
/* 57 */,
/* 58 */,
/* 59 */,
/* 60 */,
/* 61 */,
/* 62 */,
/* 63 */,
/* 64 */,
/* 65 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(318);

/***/ }),
/* 66 */
/***/ (function(module, exports, __webpack_require__) {

var getNative = __webpack_require__(20);

/* Built-in method references that are verified to be native. */
var nativeCreate = getNative(Object, 'create');

module.exports = nativeCreate;


/***/ }),
/* 67 */
/***/ (function(module, exports, __webpack_require__) {

var listCacheClear = __webpack_require__(329),
    listCacheDelete = __webpack_require__(330),
    listCacheGet = __webpack_require__(331),
    listCacheHas = __webpack_require__(332),
    listCacheSet = __webpack_require__(333);

/**
 * Creates an list cache object.
 *
 * @private
 * @constructor
 * @param {Array} [entries] The key-value pairs to cache.
 */
function ListCache(entries) {
  var index = -1,
      length = entries == null ? 0 : entries.length;

  this.clear();
  while (++index < length) {
    var entry = entries[index];
    this.set(entry[0], entry[1]);
  }
}

// Add methods to `ListCache`.
ListCache.prototype.clear = listCacheClear;
ListCache.prototype['delete'] = listCacheDelete;
ListCache.prototype.get = listCacheGet;
ListCache.prototype.has = listCacheHas;
ListCache.prototype.set = listCacheSet;

module.exports = ListCache;


/***/ }),
/* 68 */
/***/ (function(module, exports, __webpack_require__) {

var eq = __webpack_require__(49);

/**
 * Gets the index at which the `key` is found in `array` of key-value pairs.
 *
 * @private
 * @param {Array} array The array to inspect.
 * @param {*} key The key to search for.
 * @returns {number} Returns the index of the matched value, else `-1`.
 */
function assocIndexOf(array, key) {
  var length = array.length;
  while (length--) {
    if (eq(array[length][0], key)) {
      return length;
    }
  }
  return -1;
}

module.exports = assocIndexOf;


/***/ }),
/* 69 */
/***/ (function(module, exports, __webpack_require__) {

var isKeyable = __webpack_require__(335);

/**
 * Gets the data for `map`.
 *
 * @private
 * @param {Object} map The map to query.
 * @param {string} key The reference key.
 * @returns {*} Returns the map data.
 */
function getMapData(map, key) {
  var data = map.__data__;
  return isKeyable(key)
    ? data[typeof key == 'string' ? 'string' : 'hash']
    : data.map;
}

module.exports = getMapData;


/***/ }),
/* 70 */
/***/ (function(module, exports, __webpack_require__) {

var isSymbol = __webpack_require__(48);

/** Used as references for various `Number` constants. */
var INFINITY = 1 / 0;

/**
 * Converts `value` to a string key if it's not a string or symbol.
 *
 * @private
 * @param {*} value The value to inspect.
 * @returns {string|symbol} Returns the key.
 */
function toKey(value) {
  if (typeof value == 'string' || isSymbol(value)) {
    return value;
  }
  var result = (value + '');
  return (result == '0' && (1 / value) == -INFINITY) ? '-0' : result;
}

module.exports = toKey;


/***/ }),
/* 71 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _stringify = _interopRequireDefault(__webpack_require__(179));

__webpack_require__(180);

__webpack_require__(46);

var queryToJson = function queryToJson() {
  var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var pairs = params.length ? params.split('&') : location.search.slice(1).split('&');
  var result = {};
  var pairArray = [];
  pairs.forEach(function (pair) {
    pairArray = pair.split('=');
    result[pairArray[0]] = decodeURIComponent(pairArray[1] || '');
  });
  return JSON.parse((0, _stringify.default)(result));
};

var _default = queryToJson;
exports.default = _default;

/***/ }),
/* 72 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.cartEmpty = void 0;

var _i18n = __webpack_require__(7);

/**
 * @module Cart Templates
 */
var cartEmpty = "\n\t\t<div class=\"bc-cart__empty\">\n\t\t\t<h2 class=\"bc-cart__title--empty\">".concat(_i18n.NLS.cart.message_empty, "</h2>\n\t\t\t<a href=\"").concat(_i18n.NLS.cart.continue_shopping_url, "\" class=\"bc-cart__continue-shopping\">").concat(_i18n.NLS.cart.continue_shopping_label, "</a>\n\t\t</div>\n\t");
exports.cartEmpty = cartEmpty;

/***/ }),
/* 73 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.updateFlatsomeCartMenuQty = exports.updateFlatsomeCartMenuPrice = exports.updateCartMenuItem = exports.updateMenuQtyOnPageLoad = exports.updateMenuQtyTotal = exports.cartMenuSet = void 0;

var _values = _interopRequireDefault(__webpack_require__(33));

var _isEmpty2 = _interopRequireDefault(__webpack_require__(19));

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _jsCookie = _interopRequireDefault(__webpack_require__(24));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _cookies = __webpack_require__(25);

/**
 * @module Cart Menu Item
 * @description Handle cart item count for WordPress menu item.
 */
var cartMenuSet = function cartMenuSet(itemCount) {
  return _jsCookie.default.set(_cookies.CART_ITEM_COUNT_COOKIE, itemCount);
};

exports.cartMenuSet = cartMenuSet;

var updateCartMenuItem = function updateCartMenuItem() {
  var currentCount = _jsCookie.default.get(_cookies.CART_ITEM_COUNT_COOKIE);

  tools.getNodes('bc-cart-item-count', true).forEach(function (item) {
    item.classList.remove('full');

    if (!currentCount || currentCount <= 0) {
      item.innerHTML = '';
      return;
    }

    (0, _delay2.default)(function () {
      item.classList.add('full');
    }, 150);
    item.innerHTML = currentCount;
  });
};

exports.updateCartMenuItem = updateCartMenuItem;

var updateMenuQtyTotal = function updateMenuQtyTotal() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var totalQty = [];
  (0, _values.default)(data.items).forEach(function (value) {
    var itemQty = value.quantity;
    totalQty.push(itemQty);
  });
  cartMenuSet(totalQty.reduce(function (prev, next) {
    return prev + next;
  }, 0));
  updateCartMenuItem();
};

exports.updateMenuQtyTotal = updateMenuQtyTotal;

var updateFlatsomeCartMenuPrice = function updateFlatsomeCartMenuPrice() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var price = !(0, _isEmpty2.default)(data) ? data.subtotal.formatted : '';
  var menuItemPrice = tools.getNodes('.header-cart-link .cart-price', false, document, true)[0];

  if (!menuItemPrice) {
    return;
  }

  menuItemPrice.innerHTML = price;
};

exports.updateFlatsomeCartMenuPrice = updateFlatsomeCartMenuPrice;

var updateFlatsomeCartMenuQty = function updateFlatsomeCartMenuQty() {
  var menuItemQty = tools.getNodes('.header-cart-link .cart-icon strong', true, document, true);

  if (!menuItemQty) {
    return;
  }

  var currentCount = _jsCookie.default.get(_cookies.CART_ITEM_COUNT_COOKIE);

  if (!currentCount) {
    currentCount = '0';
  }

  menuItemQty.forEach(function (cartIcon) {
    cartIcon.innerHTML = currentCount;
  });
};

exports.updateFlatsomeCartMenuQty = updateFlatsomeCartMenuQty;

var updateMenuQtyOnPageLoad = function updateMenuQtyOnPageLoad() {
  var cookie = _jsCookie.default.get(_cookies.CART_ITEM_COUNT_COOKIE);

  if (!cookie) {
    return;
  }

  updateCartMenuItem();

  if (cookie !== _jsCookie.default.get(_cookies.CART_ITEM_COUNT_COOKIE)) {
    cartMenuSet(cookie);
    updateCartMenuItem();
    (0, _delay2.default)(function () {
      _jsCookie.default.remove(_cookies.CART_ITEM_COUNT_COOKIE);
    }, 100);
  }
};

exports.updateMenuQtyOnPageLoad = updateMenuQtyOnPageLoad;

/***/ }),
/* 74 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(27),
    isSymbol = __webpack_require__(48);

/** Used as references for various `Number` constants. */
var NAN = 0 / 0;

/** Used to match leading and trailing whitespace. */
var reTrim = /^\s+|\s+$/g;

/** Used to detect bad signed hexadecimal string values. */
var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;

/** Used to detect binary string values. */
var reIsBinary = /^0b[01]+$/i;

/** Used to detect octal string values. */
var reIsOctal = /^0o[0-7]+$/i;

/** Built-in method references without a dependency on `root`. */
var freeParseInt = parseInt;

/**
 * Converts `value` to a number.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to process.
 * @returns {number} Returns the number.
 * @example
 *
 * _.toNumber(3.2);
 * // => 3.2
 *
 * _.toNumber(Number.MIN_VALUE);
 * // => 5e-324
 *
 * _.toNumber(Infinity);
 * // => Infinity
 *
 * _.toNumber('3.2');
 * // => 3.2
 */
function toNumber(value) {
  if (typeof value == 'number') {
    return value;
  }
  if (isSymbol(value)) {
    return NAN;
  }
  if (isObject(value)) {
    var other = typeof value.valueOf == 'function' ? value.valueOf() : value;
    value = isObject(other) ? (other + '') : other;
  }
  if (typeof value != 'string') {
    return value === 0 ? value : +value;
  }
  value = value.replace(reTrim, '');
  var isBinary = reIsBinary.test(value);
  return (isBinary || reIsOctal.test(value))
    ? freeParseInt(value.slice(2), isBinary ? 2 : 8)
    : (reIsBadHex.test(value) ? NAN : +value);
}

module.exports = toNumber;


/***/ }),
/* 75 */
/***/ (function(module, exports, __webpack_require__) {

var identity = __webpack_require__(76),
    overRest = __webpack_require__(205),
    setToString = __webpack_require__(207);

/**
 * The base implementation of `_.rest` which doesn't validate or coerce arguments.
 *
 * @private
 * @param {Function} func The function to apply a rest parameter to.
 * @param {number} [start=func.length-1] The start position of the rest parameter.
 * @returns {Function} Returns the new function.
 */
function baseRest(func, start) {
  return setToString(overRest(func, start, identity), func + '');
}

module.exports = baseRest;


/***/ }),
/* 76 */
/***/ (function(module, exports) {

/**
 * This method returns the first argument it receives.
 *
 * @static
 * @since 0.1.0
 * @memberOf _
 * @category Util
 * @param {*} value Any value.
 * @returns {*} Returns `value`.
 * @example
 *
 * var object = { 'a': 1 };
 *
 * console.log(_.identity(object) === object);
 * // => true
 */
function identity(value) {
  return value;
}

module.exports = identity;


/***/ }),
/* 77 */
/***/ (function(module, exports) {

/** Used as references for various `Number` constants. */
var MAX_SAFE_INTEGER = 9007199254740991;

/**
 * Checks if `value` is a valid array-like length.
 *
 * **Note:** This method is loosely based on
 * [`ToLength`](http://ecma-international.org/ecma-262/7.0/#sec-tolength).
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
 * @example
 *
 * _.isLength(3);
 * // => true
 *
 * _.isLength(Number.MIN_VALUE);
 * // => false
 *
 * _.isLength(Infinity);
 * // => false
 *
 * _.isLength('3');
 * // => false
 */
function isLength(value) {
  return typeof value == 'number' &&
    value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
}

module.exports = isLength;


/***/ }),
/* 78 */
/***/ (function(module, exports) {

/** Used as references for various `Number` constants. */
var MAX_SAFE_INTEGER = 9007199254740991;

/** Used to detect unsigned integer values. */
var reIsUint = /^(?:0|[1-9]\d*)$/;

/**
 * Checks if `value` is a valid array-like index.
 *
 * @private
 * @param {*} value The value to check.
 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
 */
function isIndex(value, length) {
  var type = typeof value;
  length = length == null ? MAX_SAFE_INTEGER : length;

  return !!length &&
    (type == 'number' ||
      (type != 'symbol' && reIsUint.test(value))) &&
        (value > -1 && value % 1 == 0 && value < length);
}

module.exports = isIndex;


/***/ }),
/* 79 */
/***/ (function(module, exports) {

/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Checks if `value` is likely a prototype object.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a prototype, else `false`.
 */
function isPrototype(value) {
  var Ctor = value && value.constructor,
      proto = (typeof Ctor == 'function' && Ctor.prototype) || objectProto;

  return value === proto;
}

module.exports = isPrototype;


/***/ }),
/* 80 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module) {var root = __webpack_require__(13),
    stubFalse = __webpack_require__(214);

/** Detect free variable `exports`. */
var freeExports = typeof exports == 'object' && exports && !exports.nodeType && exports;

/** Detect free variable `module`. */
var freeModule = freeExports && typeof module == 'object' && module && !module.nodeType && module;

/** Detect the popular CommonJS extension `module.exports`. */
var moduleExports = freeModule && freeModule.exports === freeExports;

/** Built-in value references. */
var Buffer = moduleExports ? root.Buffer : undefined;

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeIsBuffer = Buffer ? Buffer.isBuffer : undefined;

/**
 * Checks if `value` is a buffer.
 *
 * @static
 * @memberOf _
 * @since 4.3.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a buffer, else `false`.
 * @example
 *
 * _.isBuffer(new Buffer(2));
 * // => true
 *
 * _.isBuffer(new Uint8Array(2));
 * // => false
 */
var isBuffer = nativeIsBuffer || stubFalse;

module.exports = isBuffer;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(120)(module)))

/***/ }),
/* 81 */
/***/ (function(module, exports, __webpack_require__) {

var baseIsTypedArray = __webpack_require__(215),
    baseUnary = __webpack_require__(121),
    nodeUtil = __webpack_require__(216);

/* Node.js helper references. */
var nodeIsTypedArray = nodeUtil && nodeUtil.isTypedArray;

/**
 * Checks if `value` is classified as a typed array.
 *
 * @static
 * @memberOf _
 * @since 3.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
 * @example
 *
 * _.isTypedArray(new Uint8Array);
 * // => true
 *
 * _.isTypedArray([]);
 * // => false
 */
var isTypedArray = nodeIsTypedArray ? baseUnary(nodeIsTypedArray) : baseIsTypedArray;

module.exports = isTypedArray;


/***/ }),
/* 82 */,
/* 83 */,
/* 84 */,
/* 85 */,
/* 86 */,
/* 87 */,
/* 88 */,
/* 89 */,
/* 90 */,
/* 91 */,
/* 92 */,
/* 93 */,
/* 94 */,
/* 95 */,
/* 96 */,
/* 97 */,
/* 98 */,
/* 99 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["b"] = isDOMElement;
/* harmony export (immutable) */ __webpack_exports__["a"] = addClasses;
/* harmony export (immutable) */ __webpack_exports__["c"] = removeClasses;
function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

// This is not really a perfect check, but works fine.
// From http://stackoverflow.com/questions/384286
var HAS_DOM_2 = (typeof HTMLElement === "undefined" ? "undefined" : _typeof(HTMLElement)) === "object";
function isDOMElement(obj) {
  return HAS_DOM_2 ? obj instanceof HTMLElement : obj && _typeof(obj) === "object" && obj !== null && obj.nodeType === 1 && typeof obj.nodeName === "string";
}
function addClasses(el, classNames) {
  classNames.forEach(function (className) {
    el.classList.add(className);
  });
}
function removeClasses(el, classNames) {
  classNames.forEach(function (className) {
    el.classList.remove(className);
  });
}
//# sourceMappingURL=dom.js.map

/***/ }),
/* 100 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = throwIfMissing;
function throwIfMissing() {
  throw new Error("Missing parameter");
}
//# sourceMappingURL=throwIfMissing.js.map

/***/ }),
/* 101 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/* global NodeList, Element, define */

(function (global) {
	'use strict';

	var FOCUSABLE_ELEMENTS = ['a[href]', 'area[href]', 'input:not([disabled])', 'select:not([disabled])', 'textarea:not([disabled])', 'button:not([disabled])', 'iframe', 'object', 'embed', '[contenteditable]', '[tabindex]:not([tabindex^="-"])'];
	var TAB_KEY = 9;
	var ESCAPE_KEY = 27;
	var focusedBeforeDialog;
	var browser = browserTests();
	var scroll = 0;
	var scroller = browser.ie || browser.firefox || (browser.chrome && !browser.edge) ? document.documentElement : document.body;

	/**
	 * Define the constructor to instantiate a dialog
	 *
	 * @constructor
	 * @param {Object} options
	 */
	function A11yDialog(options) {
		this.options = extend({
			appendTarget: '',
			bodyLock: true,
			closeButtonAriaLabel: 'Close this dialog window',
			closeButtonClasses: 'a11y-dialog__close-button',
			contentClasses: 'a11y-dialog__content',
			effect: 'none',
			effectSpeed: 300,
			effectEasing: 'ease-in-out',
			overlayClasses: 'a11y-dialog__overlay',
			overlayClickCloses: true,
			trigger: null,
			wrapperClasses: 'a11y-dialog',
		}, options);
		// Prebind the functions that will be bound in addEventListener and
		// removeEventListener to avoid losing references
		this._rendered = false;
		this._show = this.show.bind(this);
		this._hide = this.hide.bind(this);
		this._maintainFocus = this._maintainFocus.bind(this);
		this._bindKeypress = this._bindKeypress.bind(this);

		this.trigger = isString(this.options.trigger) ? getNodes(this.options.trigger, false, document, true)[0] : this.options.trigger;
		this.node = null;

		if (!this.trigger) {
			console.warn('Lookup for a11y target node failed.');
			return;
		}

		// Keep an object of listener types mapped to callback functions
		this._listeners = {};

		// Initialise everything needed for the dialog to work properly
		this.create();
	}

	/**
	 * Set up everything necessary for the dialog to be functioning
	 *
	 * @return {this}
	 */
	A11yDialog.prototype.create = function () {
		this.shown = false;
		this.trigger.addEventListener('click', this._show);

		// Execute all callbacks registered for the `create` event
		this._fire('create');

		return this;
	};

	/**
	 * Render the dialog
	 *
	 * @return {this}
	 */
	A11yDialog.prototype.render = function (event) {
		var contentNode = getNodes(this.trigger.dataset.content)[0];
		if (!contentNode) {
			return this;
		}
		var node = document.createElement('div');
		node.setAttribute('aria-hidden', 'true');
		node.classList.add(this.options.wrapperClasses);
		node.innerHTML = '<div data-js="a11y-overlay" tabindex="-1" class="' + this.options.overlayClasses + '"></div>\n' +
			'  <div class="' + this.options.contentClasses + '" role="dialog">\n' +
			'    <div role="document">\n' +
			'      <button ' +
			'           data-js="a11y-close-button"' +
			'           class="' + this.options.closeButtonClasses + '" ' +
			'           type="button" ' +
			'           aria-label="' + this.options.closeButtonAriaLabel + '"' +
			'       ></button>\n' +
			'      ' + contentNode.innerHTML +
			'    </div>\n' +
			'  </div>';

		var appendTarget = this.trigger;
		if (this.options.appendTarget.length) {
			appendTarget = document.querySelectorAll(this.options.appendTarget)[0] || this.trigger;
		}
		insertAfter(node, appendTarget);
		this.node = node;
		this.overlay = getNodes('a11y-overlay', false, this.node)[0];
		this.closeButton = getNodes('a11y-close-button', false, this.node)[0];
		if (this.options.overlayClickCloses) {
			this.overlay.addEventListener('click', this._hide);
		}
		this.closeButton.addEventListener('click', this._hide);
		this._rendered = true;
		this._fire('render', event);
		return this;
	};

	/**
	 * Show the dialog element, disable all the targets (siblings), trap the
	 * current focus within it, listen for some specific key presses and fire all
	 * registered callbacks for `show` event
	 *
	 * @param {Event} event
	 * @return {this}
	 */
	A11yDialog.prototype.show = function (event) {
		// If the dialog is already open, abort
		if (this.shown) {
			return this;
		}

		if (!this._rendered) {
			this.render(event);
		}

		if (!this._rendered) {
			return this;
		}

		this.shown = true;
		this._applyOpenEffect();
		this.node.setAttribute('aria-hidden', 'false');
		if (this.options.bodyLock) {
			lock();
		}

		// Keep a reference to the currently focused element to be able to restore
		// it later, then set the focus to the first focusable child of the dialog
		// element
		focusedBeforeDialog = document.activeElement;
		setFocusToFirstItem(this.node);

		// Bind a focus event listener to the body element to make sure the focus
		// stays trapped inside the dialog while open, and start listening for some
		// specific key presses (TAB and ESC)
		document.body.addEventListener('focus', this._maintainFocus, true);
		document.addEventListener('keydown', this._bindKeypress);

		// Execute all callbacks registered for the `show` event
		this._fire('show', event);

		return this;
	};

	/**
	 * Hide the dialog element, enable all the targets (siblings), restore the
	 * focus to the previously active element, stop listening for some specific
	 * key presses and fire all registered callbacks for `hide` event
	 *
	 * @param {Event} event
	 * @return {this}
	 */
	A11yDialog.prototype.hide = function (event) {
		// If the dialog is already closed, abort
		if (!this.shown) {
			return this;
		}

		this.shown = false;
		this.node.setAttribute('aria-hidden', 'true');
		this._applyCloseEffect();

		if (this.options.bodyLock) {
			unlock();
		}

		// If their was a focused element before the dialog was opened, restore the
		// focus back to it
		if (focusedBeforeDialog) {
			focusedBeforeDialog.focus();
		}

		// Remove the focus event listener to the body element and stop listening
		// for specific key presses
		document.body.removeEventListener('focus', this._maintainFocus, true);
		document.removeEventListener('keydown', this._bindKeypress);

		// Execute all callbacks registered for the `hide` event
		this._fire('hide', event);

		return this;
	};

	/**
	 * Destroy the current instance (after making sure the dialog has been hidden)
	 * and remove all associated listeners from dialog openers and closers
	 *
	 * @return {this}
	 */
	A11yDialog.prototype.destroy = function () {
		// Hide the dialog to avoid destroying an open instance
		this.hide();

		this.trigger.removeEventListener('click', this._show);
		if (this.options.overlayClickCloses) {
			this.overlay.removeEventListener('click', this._hide);
		}
		this.closeButton.removeEventListener('click', this._hide);

		// Execute all callbacks registered for the `destroy` event
		this._fire('destroy');

		// Keep an object of listener types mapped to callback functions
		this._listeners = {};

		return this;
	};

	/**
	 * Register a new callback for the given event type
	 *
	 * @param {string} type
	 * @param {Function} handler
	 */
	A11yDialog.prototype.on = function (type, handler) {
		if (typeof this._listeners[type] === 'undefined') {
			this._listeners[type] = [];
		}

		this._listeners[type].push(handler);

		return this;
	};

	/**
	 * Unregister an existing callback for the given event type
	 *
	 * @param {string} type
	 * @param {Function} handler
	 */
	A11yDialog.prototype.off = function (type, handler) {
		var index = this._listeners[type].indexOf(handler);

		if (index > -1) {
			this._listeners[type].splice(index, 1);
		}

		return this;
	};

	/**
	 * Iterate over all registered handlers for given type and call them all with
	 * the dialog element as first argument, event as second argument (if any).
	 *
	 * @access private
	 * @param {string} type
	 * @param {Event} event
	 */
	A11yDialog.prototype._fire = function (type, event) {
		var listeners = this._listeners[type] || [];

		listeners.forEach(function (listener) {
			listener(this.node, event);
		}.bind(this));
	};

	/**
	 * Private event handler used when listening to some specific key presses
	 * (namely ESCAPE and TAB)
	 *
	 * @access private
	 * @param {Event} event
	 */
	A11yDialog.prototype._bindKeypress = function (event) {
		// If the dialog is shown and the ESCAPE key is being pressed, prevent any
		// further effects from the ESCAPE key and hide the dialog
		if (this.shown && event.which === ESCAPE_KEY) {
			event.preventDefault();
			this.hide();
		}

		// If the dialog is shown and the TAB key is being pressed, make sure the
		// focus stays trapped within the dialog element
		if (this.shown && event.which === TAB_KEY) {
			trapTabKey(this.node, event);
		}
	};

	/**
	 * Private event handler used when making sure the focus stays within the
	 * currently open dialog
	 *
	 * @access private
	 * @param {Event} event
	 */
	A11yDialog.prototype._maintainFocus = function (event) {
		// If the dialog is shown and the focus is not within the dialog element,
		// move it back to its first focusable child
		if (this.shown && !this.node.contains(event.target)) {
			setFocusToFirstItem(this.node);
		}
	};

	/**
	 * Applies effects to the opening of the dialog.
	 *
	 * @access private
	 */

	A11yDialog.prototype._applyOpenEffect = function () {
		var _this = this;
		if (this.options.effect === 'fade') {
			this.node.style.opacity = '0';
			this.node.style.transition = 'opacity ' + this.options.effectSpeed + 'ms ' + this.options.effectEasing;
			setTimeout(function(){
				_this.node.style.opacity = '1';
			}, 50);
		}
	};

	/**
	 * Applies effects to the closing of the dialog.
	 *
	 * @access private
	 */

	A11yDialog.prototype._applyCloseEffect = function () {
		var _this = this;
		if (this.options.effect === 'fade') {
			this.node.setAttribute('aria-hidden', 'false');
			this.node.style.opacity = '0';
			setTimeout(function(){
				_this.node.style.transition = '';
				_this.node.setAttribute('aria-hidden', 'true');
			}, this.options.effectSpeed);
		}
	};

	/**
	 * Highly efficient function to convert a nodelist into a standard array. Allows you to run Array.forEach
	 *
	 * @param {Element|NodeList} elements to convert
	 * @returns {Array} Of converted elements
	 */

	function convertElements(elements) {
		var converted = [];
		var i = elements.length;
		for (i; i--; converted.unshift(elements[i])); // eslint-disable-line

		return converted;
	}

	/**
	 * Should be used at all times for getting nodes throughout our app. Please use the data-js attribute whenever possible
	 *
	 * @param selector The selector string to search for. If arg 4 is false (default) then we search for [data-js="selector"]
	 * @param convert Convert the NodeList to an array? Then we can Array.forEach directly. Uses convertElements from above
	 * @param node Parent node to search from. Defaults to document
	 * @param custom Is this a custom selector where we don't want to use the data-js attribute?
	 * @returns {NodeList}
	 */

	function getNodes(selector, convert, node, custom) {
		if (!node) {
			node = document;
		}
		var selectorString = custom ? selector : '[data-js="' + selector + '"]';
		var nodes = node.querySelectorAll(selectorString);
		if (convert) {
			nodes = convertElements(nodes);
		}
		return nodes;
	}

	/**
	 * Query the DOM for nodes matching the given selector, scoped to context (or
	 * the whole document)
	 *
	 * @param {String} selector
	 * @param {Element} [context = document]
	 * @return {Array<Element>}
	 */
	function $$(selector, context) {
		return convertElements((context || document).querySelectorAll(selector));
	}

	/**
	 * Set the focus to the first focusable child of the given element
	 *
	 * @param {Element} node
	 */
	function setFocusToFirstItem(node) {
		var focusableChildren = getFocusableChildren(node);

		if (focusableChildren.length) {
			focusableChildren[0].focus();
		}
	}

	/**
	 * Insert a node after another node
	 *
	 * @param newNode {Element|NodeList}
	 * @param referenceNode {Element|NodeList}
	 */
	function insertAfter(newNode, referenceNode) {
		referenceNode.parentNode.insertBefore(newNode, referenceNode.nextElementSibling);
	}

	/**
	 * Get the focusable children of the given element
	 *
	 * @param {Element} node
	 * @return {Array<Element>}
	 */
	function getFocusableChildren(node) {
		return $$(FOCUSABLE_ELEMENTS.join(','), node).filter(function (child) {
			return !!(child.offsetWidth || child.offsetHeight || child.getClientRects().length);
		});
	}

	function isString(x) {
		return Object.prototype.toString.call(x) === "[object String]"
	}

	function extend(obj, src) {
		Object.keys(src).forEach(function(key) { obj[key] = src[key]; });
		return obj;
	}

	/**
	 * Trap the focus inside the given element
	 *
	 * @param {Element} node
	 * @param {Event} event
	 */
	function trapTabKey(node, event) {
		var focusableChildren = getFocusableChildren(node);
		var focusedItemIndex = focusableChildren.indexOf(document.activeElement);

		// If the SHIFT key is being pressed while tabbing (moving backwards) and
		// the currently focused item is the first one, move the focus to the last
		// focusable item from the dialog element
		if (event.shiftKey && focusedItemIndex === 0) {
			focusableChildren[focusableChildren.length - 1].focus();
			event.preventDefault();
			// If the SHIFT key is not being pressed (moving forwards) and the currently
			// focused item is the last one, move the focus to the first focusable item
			// from the dialog element
		} else if (!event.shiftKey && focusedItemIndex === focusableChildren.length - 1) {
			focusableChildren[0].focus();
			event.preventDefault();
		}
	}

	/**
	 * @function lock
	 * @description Lock the body at a particular position and prevent scroll,
	 * use margin to simulate original scroll position.
	 */

	function lock() {
		scroll = scroller.scrollTop;
		document.body.classList.add('a11y-dialog__body-locked');
		document.body.style.position = 'fixed';
		document.body.style.width = '100%';
		document.body.style.marginTop = '-' + scroll + 'px';
	}

	/**
	 * @function unlock
	 * @description Unlock the body and return it to its actual scroll position.
	 */

	function unlock() {
		document.body.style.marginTop = '';
		document.body.style.position = '';
		document.body.style.width = '';
		scroller.scrollTop = scroll;
		document.body.classList.remove('a11y-dialog__body-locked');
	}

	function browserTests() {
		var android = /(android)/i.test(navigator.userAgent);
		var chrome = !!window.chrome;
		var firefox = typeof InstallTrigger !== 'undefined';
		var ie = document.documentMode;
		var edge = !ie && !!window.StyleMedia;
		var ios = !!navigator.userAgent.match(/(iPod|iPhone|iPad)/i);
		var iosMobile = !!navigator.userAgent.match(/(iPod|iPhone)/i);
		var opera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
		var safari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0 || !chrome && !opera && window.webkitAudioContext !== 'undefined'; // eslint-disable-line
		var os = navigator.platform;

		return {
			android: android,
			chrome: chrome,
			edge: edge,
			firefox: firefox,
			ie: ie,
			ios: ios,
			iosMobile: iosMobile,
			opera: opera,
			safari: safari,
			os: os,
		}
	}

	if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
		module.exports = A11yDialog;
	} else if (true) {
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return A11yDialog;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else if (typeof global === 'object') {
		global.A11yDialog = A11yDialog;
	}
}(typeof global !== 'undefined' ? global : window));

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(113)))

/***/ }),
/* 102 */
/***/ (function(module, exports, __webpack_require__) {

var _isIterable = __webpack_require__(312);

var _getIterator = __webpack_require__(315);

function _sliceIterator(arr, i) {
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = _getIterator(arr), _s; !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}

function _slicedToArray(arr, i) {
  if (Array.isArray(arr)) {
    return arr;
  } else if (_isIterable(Object(arr))) {
    return _sliceIterator(arr, i);
  } else {
    throw new TypeError("Invalid attempt to destructure non-iterable instance");
  }
}

module.exports = _slicedToArray;

/***/ }),
/* 103 */
/***/ (function(module, exports, __webpack_require__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __webpack_require__(42);
var TAG = __webpack_require__(6)('toStringTag');
// ES3 wrong here
var ARG = cof(function () { return arguments; }()) == 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (e) { /* empty */ }
};

module.exports = function (it) {
  var O, T, B;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T
    // builtinTag case
    : ARG ? cof(O)
    // ES3 arguments fallback
    : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
};


/***/ }),
/* 104 */
/***/ (function(module, exports, __webpack_require__) {

var mapCacheClear = __webpack_require__(322),
    mapCacheDelete = __webpack_require__(334),
    mapCacheGet = __webpack_require__(336),
    mapCacheHas = __webpack_require__(337),
    mapCacheSet = __webpack_require__(338);

/**
 * Creates a map cache object to store key-value pairs.
 *
 * @private
 * @constructor
 * @param {Array} [entries] The key-value pairs to cache.
 */
function MapCache(entries) {
  var index = -1,
      length = entries == null ? 0 : entries.length;

  this.clear();
  while (++index < length) {
    var entry = entries[index];
    this.set(entry[0], entry[1]);
  }
}

// Add methods to `MapCache`.
MapCache.prototype.clear = mapCacheClear;
MapCache.prototype['delete'] = mapCacheDelete;
MapCache.prototype.get = mapCacheGet;
MapCache.prototype.has = mapCacheHas;
MapCache.prototype.set = mapCacheSet;

module.exports = MapCache;


/***/ }),
/* 105 */
/***/ (function(module, exports, __webpack_require__) {

var getNative = __webpack_require__(20),
    root = __webpack_require__(13);

/* Built-in method references that are verified to be native. */
var Map = getNative(root, 'Map');

module.exports = Map;


/***/ }),
/* 106 */
/***/ (function(module, exports, __webpack_require__) {

var isArray = __webpack_require__(12),
    isSymbol = __webpack_require__(48);

/** Used to match property names within property paths. */
var reIsDeepProp = /\.|\[(?:[^[\]]*|(["'])(?:(?!\1)[^\\]|\\.)*?\1)\]/,
    reIsPlainProp = /^\w*$/;

/**
 * Checks if `value` is a property name and not a property path.
 *
 * @private
 * @param {*} value The value to check.
 * @param {Object} [object] The object to query keys on.
 * @returns {boolean} Returns `true` if `value` is a property name, else `false`.
 */
function isKey(value, object) {
  if (isArray(value)) {
    return false;
  }
  var type = typeof value;
  if (type == 'number' || type == 'symbol' || type == 'boolean' ||
      value == null || isSymbol(value)) {
    return true;
  }
  return reIsPlainProp.test(value) || !reIsDeepProp.test(value) ||
    (object != null && value in Object(object));
}

module.exports = isKey;


/***/ }),
/* 107 */
/***/ (function(module, exports, __webpack_require__) {

/**
 * Root reference for iframes.
 */

var root;
if (typeof window !== 'undefined') { // Browser window
  root = window;
} else if (typeof self !== 'undefined') { // Web Worker
  root = self;
} else { // Other environments
  console.warn("Using browser-only version of superagent in non-browser environment");
  root = this;
}

var Emitter = __webpack_require__(391);
var RequestBase = __webpack_require__(392);
var isObject = __webpack_require__(181);
var ResponseBase = __webpack_require__(393);
var Agent = __webpack_require__(395);

/**
 * Noop.
 */

function noop(){};

/**
 * Expose `request`.
 */

var request = exports = module.exports = function(method, url) {
  // callback
  if ('function' == typeof url) {
    return new exports.Request('GET', method).end(url);
  }

  // url first
  if (1 == arguments.length) {
    return new exports.Request('GET', method);
  }

  return new exports.Request(method, url);
}

exports.Request = Request;

/**
 * Determine XHR.
 */

request.getXHR = function () {
  if (root.XMLHttpRequest
      && (!root.location || 'file:' != root.location.protocol
          || !root.ActiveXObject)) {
    return new XMLHttpRequest;
  } else {
    try { return new ActiveXObject('Microsoft.XMLHTTP'); } catch(e) {}
    try { return new ActiveXObject('Msxml2.XMLHTTP.6.0'); } catch(e) {}
    try { return new ActiveXObject('Msxml2.XMLHTTP.3.0'); } catch(e) {}
    try { return new ActiveXObject('Msxml2.XMLHTTP'); } catch(e) {}
  }
  throw Error("Browser-only version of superagent could not find XHR");
};

/**
 * Removes leading and trailing whitespace, added to support IE.
 *
 * @param {String} s
 * @return {String}
 * @api private
 */

var trim = ''.trim
  ? function(s) { return s.trim(); }
  : function(s) { return s.replace(/(^\s*|\s*$)/g, ''); };

/**
 * Serialize the given `obj`.
 *
 * @param {Object} obj
 * @return {String}
 * @api private
 */

function serialize(obj) {
  if (!isObject(obj)) return obj;
  var pairs = [];
  for (var key in obj) {
    pushEncodedKeyValuePair(pairs, key, obj[key]);
  }
  return pairs.join('&');
}

/**
 * Helps 'serialize' with serializing arrays.
 * Mutates the pairs array.
 *
 * @param {Array} pairs
 * @param {String} key
 * @param {Mixed} val
 */

function pushEncodedKeyValuePair(pairs, key, val) {
  if (val != null) {
    if (Array.isArray(val)) {
      val.forEach(function(v) {
        pushEncodedKeyValuePair(pairs, key, v);
      });
    } else if (isObject(val)) {
      for(var subkey in val) {
        pushEncodedKeyValuePair(pairs, key + '[' + subkey + ']', val[subkey]);
      }
    } else {
      pairs.push(encodeURIComponent(key)
        + '=' + encodeURIComponent(val));
    }
  } else if (val === null) {
    pairs.push(encodeURIComponent(key));
  }
}

/**
 * Expose serialization method.
 */

request.serializeObject = serialize;

/**
  * Parse the given x-www-form-urlencoded `str`.
  *
  * @param {String} str
  * @return {Object}
  * @api private
  */

function parseString(str) {
  var obj = {};
  var pairs = str.split('&');
  var pair;
  var pos;

  for (var i = 0, len = pairs.length; i < len; ++i) {
    pair = pairs[i];
    pos = pair.indexOf('=');
    if (pos == -1) {
      obj[decodeURIComponent(pair)] = '';
    } else {
      obj[decodeURIComponent(pair.slice(0, pos))] =
        decodeURIComponent(pair.slice(pos + 1));
    }
  }

  return obj;
}

/**
 * Expose parser.
 */

request.parseString = parseString;

/**
 * Default MIME type map.
 *
 *     superagent.types.xml = 'application/xml';
 *
 */

request.types = {
  html: 'text/html',
  json: 'application/json',
  xml: 'text/xml',
  urlencoded: 'application/x-www-form-urlencoded',
  'form': 'application/x-www-form-urlencoded',
  'form-data': 'application/x-www-form-urlencoded'
};

/**
 * Default serialization map.
 *
 *     superagent.serialize['application/xml'] = function(obj){
 *       return 'generated xml here';
 *     };
 *
 */

request.serialize = {
  'application/x-www-form-urlencoded': serialize,
  'application/json': JSON.stringify
};

/**
  * Default parsers.
  *
  *     superagent.parse['application/xml'] = function(str){
  *       return { object parsed from str };
  *     };
  *
  */

request.parse = {
  'application/x-www-form-urlencoded': parseString,
  'application/json': JSON.parse
};

/**
 * Parse the given header `str` into
 * an object containing the mapped fields.
 *
 * @param {String} str
 * @return {Object}
 * @api private
 */

function parseHeader(str) {
  var lines = str.split(/\r?\n/);
  var fields = {};
  var index;
  var line;
  var field;
  var val;

  for (var i = 0, len = lines.length; i < len; ++i) {
    line = lines[i];
    index = line.indexOf(':');
    if (index === -1) { // could be empty line, just skip it
      continue;
    }
    field = line.slice(0, index).toLowerCase();
    val = trim(line.slice(index + 1));
    fields[field] = val;
  }

  return fields;
}

/**
 * Check if `mime` is json or has +json structured syntax suffix.
 *
 * @param {String} mime
 * @return {Boolean}
 * @api private
 */

function isJSON(mime) {
  // should match /json or +json
  // but not /json-seq
  return /[\/+]json($|[^-\w])/.test(mime);
}

/**
 * Initialize a new `Response` with the given `xhr`.
 *
 *  - set flags (.ok, .error, etc)
 *  - parse header
 *
 * Examples:
 *
 *  Aliasing `superagent` as `request` is nice:
 *
 *      request = superagent;
 *
 *  We can use the promise-like API, or pass callbacks:
 *
 *      request.get('/').end(function(res){});
 *      request.get('/', function(res){});
 *
 *  Sending data can be chained:
 *
 *      request
 *        .post('/user')
 *        .send({ name: 'tj' })
 *        .end(function(res){});
 *
 *  Or passed to `.send()`:
 *
 *      request
 *        .post('/user')
 *        .send({ name: 'tj' }, function(res){});
 *
 *  Or passed to `.post()`:
 *
 *      request
 *        .post('/user', { name: 'tj' })
 *        .end(function(res){});
 *
 * Or further reduced to a single call for simple cases:
 *
 *      request
 *        .post('/user', { name: 'tj' }, function(res){});
 *
 * @param {XMLHTTPRequest} xhr
 * @param {Object} options
 * @api private
 */

function Response(req) {
  this.req = req;
  this.xhr = this.req.xhr;
  // responseText is accessible only if responseType is '' or 'text' and on older browsers
  this.text = ((this.req.method !='HEAD' && (this.xhr.responseType === '' || this.xhr.responseType === 'text')) || typeof this.xhr.responseType === 'undefined')
     ? this.xhr.responseText
     : null;
  this.statusText = this.req.xhr.statusText;
  var status = this.xhr.status;
  // handle IE9 bug: http://stackoverflow.com/questions/10046972/msie-returns-status-code-of-1223-for-ajax-request
  if (status === 1223) {
    status = 204;
  }
  this._setStatusProperties(status);
  this.header = this.headers = parseHeader(this.xhr.getAllResponseHeaders());
  // getAllResponseHeaders sometimes falsely returns "" for CORS requests, but
  // getResponseHeader still works. so we get content-type even if getting
  // other headers fails.
  this.header['content-type'] = this.xhr.getResponseHeader('content-type');
  this._setHeaderProperties(this.header);

  if (null === this.text && req._responseType) {
    this.body = this.xhr.response;
  } else {
    this.body = this.req.method != 'HEAD'
      ? this._parseBody(this.text ? this.text : this.xhr.response)
      : null;
  }
}

ResponseBase(Response.prototype);

/**
 * Parse the given body `str`.
 *
 * Used for auto-parsing of bodies. Parsers
 * are defined on the `superagent.parse` object.
 *
 * @param {String} str
 * @return {Mixed}
 * @api private
 */

Response.prototype._parseBody = function(str) {
  var parse = request.parse[this.type];
  if (this.req._parser) {
    return this.req._parser(this, str);
  }
  if (!parse && isJSON(this.type)) {
    parse = request.parse['application/json'];
  }
  return parse && str && (str.length || str instanceof Object)
    ? parse(str)
    : null;
};

/**
 * Return an `Error` representative of this response.
 *
 * @return {Error}
 * @api public
 */

Response.prototype.toError = function(){
  var req = this.req;
  var method = req.method;
  var url = req.url;

  var msg = 'cannot ' + method + ' ' + url + ' (' + this.status + ')';
  var err = new Error(msg);
  err.status = this.status;
  err.method = method;
  err.url = url;

  return err;
};

/**
 * Expose `Response`.
 */

request.Response = Response;

/**
 * Initialize a new `Request` with the given `method` and `url`.
 *
 * @param {String} method
 * @param {String} url
 * @api public
 */

function Request(method, url) {
  var self = this;
  this._query = this._query || [];
  this.method = method;
  this.url = url;
  this.header = {}; // preserves header name case
  this._header = {}; // coerces header names to lowercase
  this.on('end', function(){
    var err = null;
    var res = null;

    try {
      res = new Response(self);
    } catch(e) {
      err = new Error('Parser is unable to parse the response');
      err.parse = true;
      err.original = e;
      // issue #675: return the raw response if the response parsing fails
      if (self.xhr) {
        // ie9 doesn't have 'response' property
        err.rawResponse = typeof self.xhr.responseType == 'undefined' ? self.xhr.responseText : self.xhr.response;
        // issue #876: return the http status code if the response parsing fails
        err.status = self.xhr.status ? self.xhr.status : null;
        err.statusCode = err.status; // backwards-compat only
      } else {
        err.rawResponse = null;
        err.status = null;
      }

      return self.callback(err);
    }

    self.emit('response', res);

    var new_err;
    try {
      if (!self._isResponseOK(res)) {
        new_err = new Error(res.statusText || 'Unsuccessful HTTP response');
      }
    } catch(custom_err) {
      new_err = custom_err; // ok() callback can throw
    }

    // #1000 don't catch errors from the callback to avoid double calling it
    if (new_err) {
      new_err.original = err;
      new_err.response = res;
      new_err.status = res.status;
      self.callback(new_err, res);
    } else {
      self.callback(null, res);
    }
  });
}

/**
 * Mixin `Emitter` and `RequestBase`.
 */

Emitter(Request.prototype);
RequestBase(Request.prototype);

/**
 * Set Content-Type to `type`, mapping values from `request.types`.
 *
 * Examples:
 *
 *      superagent.types.xml = 'application/xml';
 *
 *      request.post('/')
 *        .type('xml')
 *        .send(xmlstring)
 *        .end(callback);
 *
 *      request.post('/')
 *        .type('application/xml')
 *        .send(xmlstring)
 *        .end(callback);
 *
 * @param {String} type
 * @return {Request} for chaining
 * @api public
 */

Request.prototype.type = function(type){
  this.set('Content-Type', request.types[type] || type);
  return this;
};

/**
 * Set Accept to `type`, mapping values from `request.types`.
 *
 * Examples:
 *
 *      superagent.types.json = 'application/json';
 *
 *      request.get('/agent')
 *        .accept('json')
 *        .end(callback);
 *
 *      request.get('/agent')
 *        .accept('application/json')
 *        .end(callback);
 *
 * @param {String} accept
 * @return {Request} for chaining
 * @api public
 */

Request.prototype.accept = function(type){
  this.set('Accept', request.types[type] || type);
  return this;
};

/**
 * Set Authorization field value with `user` and `pass`.
 *
 * @param {String} user
 * @param {String} [pass] optional in case of using 'bearer' as type
 * @param {Object} options with 'type' property 'auto', 'basic' or 'bearer' (default 'basic')
 * @return {Request} for chaining
 * @api public
 */

Request.prototype.auth = function(user, pass, options){
  if (1 === arguments.length) pass = '';
  if (typeof pass === 'object' && pass !== null) { // pass is optional and can be replaced with options
    options = pass;
    pass = '';
  }
  if (!options) {
    options = {
      type: 'function' === typeof btoa ? 'basic' : 'auto',
    };
  }

  var encoder = function(string) {
    if ('function' === typeof btoa) {
      return btoa(string);
    }
    throw new Error('Cannot use basic auth, btoa is not a function');
  };

  return this._auth(user, pass, options, encoder);
};

/**
 * Add query-string `val`.
 *
 * Examples:
 *
 *   request.get('/shoes')
 *     .query('size=10')
 *     .query({ color: 'blue' })
 *
 * @param {Object|String} val
 * @return {Request} for chaining
 * @api public
 */

Request.prototype.query = function(val){
  if ('string' != typeof val) val = serialize(val);
  if (val) this._query.push(val);
  return this;
};

/**
 * Queue the given `file` as an attachment to the specified `field`,
 * with optional `options` (or filename).
 *
 * ``` js
 * request.post('/upload')
 *   .attach('content', new Blob(['<a id="a"><b id="b">hey!</b></a>'], { type: "text/html"}))
 *   .end(callback);
 * ```
 *
 * @param {String} field
 * @param {Blob|File} file
 * @param {String|Object} options
 * @return {Request} for chaining
 * @api public
 */

Request.prototype.attach = function(field, file, options){
  if (file) {
    if (this._data) {
      throw Error("superagent can't mix .send() and .attach()");
    }

    this._getFormData().append(field, file, options || file.name);
  }
  return this;
};

Request.prototype._getFormData = function(){
  if (!this._formData) {
    this._formData = new root.FormData();
  }
  return this._formData;
};

/**
 * Invoke the callback with `err` and `res`
 * and handle arity check.
 *
 * @param {Error} err
 * @param {Response} res
 * @api private
 */

Request.prototype.callback = function(err, res){
  if (this._shouldRetry(err, res)) {
    return this._retry();
  }

  var fn = this._callback;
  this.clearTimeout();

  if (err) {
    if (this._maxRetries) err.retries = this._retries - 1;
    this.emit('error', err);
  }

  fn(err, res);
};

/**
 * Invoke callback with x-domain error.
 *
 * @api private
 */

Request.prototype.crossDomainError = function(){
  var err = new Error('Request has been terminated\nPossible causes: the network is offline, Origin is not allowed by Access-Control-Allow-Origin, the page is being unloaded, etc.');
  err.crossDomain = true;

  err.status = this.status;
  err.method = this.method;
  err.url = this.url;

  this.callback(err);
};

// This only warns, because the request is still likely to work
Request.prototype.buffer = Request.prototype.ca = Request.prototype.agent = function(){
  console.warn("This is not supported in browser version of superagent");
  return this;
};

// This throws, because it can't send/receive data as expected
Request.prototype.pipe = Request.prototype.write = function(){
  throw Error("Streaming is not supported in browser version of superagent");
};

/**
 * Check if `obj` is a host object,
 * we don't want to serialize these :)
 *
 * @param {Object} obj
 * @return {Boolean}
 * @api private
 */
Request.prototype._isHost = function _isHost(obj) {
  // Native objects stringify to [object File], [object Blob], [object FormData], etc.
  return obj && 'object' === typeof obj && !Array.isArray(obj) && Object.prototype.toString.call(obj) !== '[object Object]';
}

/**
 * Initiate request, invoking callback `fn(res)`
 * with an instanceof `Response`.
 *
 * @param {Function} fn
 * @return {Request} for chaining
 * @api public
 */

Request.prototype.end = function(fn){
  if (this._endCalled) {
    console.warn("Warning: .end() was called twice. This is not supported in superagent");
  }
  this._endCalled = true;

  // store callback
  this._callback = fn || noop;

  // querystring
  this._finalizeQueryString();

  return this._end();
};

Request.prototype._end = function() {
  var self = this;
  var xhr = (this.xhr = request.getXHR());
  var data = this._formData || this._data;

  this._setTimeouts();

  // state change
  xhr.onreadystatechange = function(){
    var readyState = xhr.readyState;
    if (readyState >= 2 && self._responseTimeoutTimer) {
      clearTimeout(self._responseTimeoutTimer);
    }
    if (4 != readyState) {
      return;
    }

    // In IE9, reads to any property (e.g. status) off of an aborted XHR will
    // result in the error "Could not complete the operation due to error c00c023f"
    var status;
    try { status = xhr.status } catch(e) { status = 0; }

    if (!status) {
      if (self.timedout || self._aborted) return;
      return self.crossDomainError();
    }
    self.emit('end');
  };

  // progress
  var handleProgress = function(direction, e) {
    if (e.total > 0) {
      e.percent = e.loaded / e.total * 100;
    }
    e.direction = direction;
    self.emit('progress', e);
  };
  if (this.hasListeners('progress')) {
    try {
      xhr.onprogress = handleProgress.bind(null, 'download');
      if (xhr.upload) {
        xhr.upload.onprogress = handleProgress.bind(null, 'upload');
      }
    } catch(e) {
      // Accessing xhr.upload fails in IE from a web worker, so just pretend it doesn't exist.
      // Reported here:
      // https://connect.microsoft.com/IE/feedback/details/837245/xmlhttprequest-upload-throws-invalid-argument-when-used-from-web-worker-context
    }
  }

  // initiate request
  try {
    if (this.username && this.password) {
      xhr.open(this.method, this.url, true, this.username, this.password);
    } else {
      xhr.open(this.method, this.url, true);
    }
  } catch (err) {
    // see #1149
    return this.callback(err);
  }

  // CORS
  if (this._withCredentials) xhr.withCredentials = true;

  // body
  if (!this._formData && 'GET' != this.method && 'HEAD' != this.method && 'string' != typeof data && !this._isHost(data)) {
    // serialize stuff
    var contentType = this._header['content-type'];
    var serialize = this._serializer || request.serialize[contentType ? contentType.split(';')[0] : ''];
    if (!serialize && isJSON(contentType)) {
      serialize = request.serialize['application/json'];
    }
    if (serialize) data = serialize(data);
  }

  // set header fields
  for (var field in this.header) {
    if (null == this.header[field]) continue;

    if (this.header.hasOwnProperty(field))
      xhr.setRequestHeader(field, this.header[field]);
  }

  if (this._responseType) {
    xhr.responseType = this._responseType;
  }

  // send stuff
  this.emit('request', this);

  // IE11 xhr.send(undefined) sends 'undefined' string as POST payload (instead of nothing)
  // We need null here if data is undefined
  xhr.send(typeof data !== 'undefined' ? data : null);
  return this;
};

request.agent = function() {
  return new Agent();
};

["GET", "POST", "OPTIONS", "PATCH", "PUT", "DELETE"].forEach(function(method) {
  Agent.prototype[method.toLowerCase()] = function(url, fn) {
    var req = new request.Request(method, url);
    this._setDefaults(req);
    if (fn) {
      req.end(fn);
    }
    return req;
  };
});

Agent.prototype.del = Agent.prototype['delete'];

/**
 * GET `url` with optional callback `fn(res)`.
 *
 * @param {String} url
 * @param {Mixed|Function} [data] or fn
 * @param {Function} [fn]
 * @return {Request}
 * @api public
 */

request.get = function(url, data, fn) {
  var req = request('GET', url);
  if ('function' == typeof data) (fn = data), (data = null);
  if (data) req.query(data);
  if (fn) req.end(fn);
  return req;
};

/**
 * HEAD `url` with optional callback `fn(res)`.
 *
 * @param {String} url
 * @param {Mixed|Function} [data] or fn
 * @param {Function} [fn]
 * @return {Request}
 * @api public
 */

request.head = function(url, data, fn) {
  var req = request('HEAD', url);
  if ('function' == typeof data) (fn = data), (data = null);
  if (data) req.query(data);
  if (fn) req.end(fn);
  return req;
};

/**
 * OPTIONS query to `url` with optional callback `fn(res)`.
 *
 * @param {String} url
 * @param {Mixed|Function} [data] or fn
 * @param {Function} [fn]
 * @return {Request}
 * @api public
 */

request.options = function(url, data, fn) {
  var req = request('OPTIONS', url);
  if ('function' == typeof data) (fn = data), (data = null);
  if (data) req.send(data);
  if (fn) req.end(fn);
  return req;
};

/**
 * DELETE `url` with optional `data` and callback `fn(res)`.
 *
 * @param {String} url
 * @param {Mixed} [data]
 * @param {Function} [fn]
 * @return {Request}
 * @api public
 */

function del(url, data, fn) {
  var req = request('DELETE', url);
  if ('function' == typeof data) (fn = data), (data = null);
  if (data) req.send(data);
  if (fn) req.end(fn);
  return req;
}

request['del'] = del;
request['delete'] = del;

/**
 * PATCH `url` with optional `data` and callback `fn(res)`.
 *
 * @param {String} url
 * @param {Mixed} [data]
 * @param {Function} [fn]
 * @return {Request}
 * @api public
 */

request.patch = function(url, data, fn) {
  var req = request('PATCH', url);
  if ('function' == typeof data) (fn = data), (data = null);
  if (data) req.send(data);
  if (fn) req.end(fn);
  return req;
};

/**
 * POST `url` with optional `data` and callback `fn(res)`.
 *
 * @param {String} url
 * @param {Mixed} [data]
 * @param {Function} [fn]
 * @return {Request}
 * @api public
 */

request.post = function(url, data, fn) {
  var req = request('POST', url);
  if ('function' == typeof data) (fn = data), (data = null);
  if (data) req.send(data);
  if (fn) req.end(fn);
  return req;
};

/**
 * PUT `url` with optional `data` and callback `fn(res)`.
 *
 * @param {String} url
 * @param {Mixed|Function} [data] or fn
 * @param {Function} [fn]
 * @return {Request}
 * @api public
 */

request.put = function(url, data, fn) {
  var req = request('PUT', url);
  if ('function' == typeof data) (fn = data), (data = null);
  if (data) req.send(data);
  if (fn) req.end(fn);
  return req;
};


/***/ }),
/* 108 */,
/* 109 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _slicedToArray2 = _interopRequireDefault(__webpack_require__(102));

var _entries = _interopRequireDefault(__webpack_require__(65));

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _isEmpty2 = _interopRequireDefault(__webpack_require__(19));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _jsCookie = _interopRequireDefault(__webpack_require__(24));

var _ajax = __webpack_require__(26);

var tools = _interopRequireWildcard(__webpack_require__(2));

var _events = __webpack_require__(5);

var _cartState = _interopRequireDefault(__webpack_require__(47));

var _wpSettings = __webpack_require__(11);

var _cookies = __webpack_require__(25);

var _events2 = __webpack_require__(34);

var _i18n = __webpack_require__(7);

var _cartTemplates = __webpack_require__(72);

var _cartMenuItem = __webpack_require__(73);

/**
 * @module Cart Items Ajax
 * @description Ajax handling for cart items.
 */
var timeoutOptions = {
  delay: 500
};
var timeout = null;
/**
 * @function getCartAPIURL
 * @description build the cart API endpoint url.
 * @param e
 * @returns {string}
 */

var getCartAPIURL = function getCartAPIURL(e) {
  var cartID = _jsCookie.default.get(_cookies.CART_ID_COOKIE_NAME);

  var cartItem = e.delegateTarget.dataset.cart_item_id;
  return (0, _isEmpty2.default)(cartID && cartItem) ? '' : "".concat(_wpSettings.CART_API_BASE, "/").concat(cartID).concat(_i18n.NLS.cart.items_url_param).concat(cartItem);
};
/**
 * @function getItemUpdateQueryString
 * @description build the query string for the current item being updated.
 * @param input
 * @returns {string}
 */


var getItemUpdateQueryString = function getItemUpdateQueryString() {
  var input = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var quantity = input.delegateTarget.value;
  var k = encodeURIComponent(_i18n.NLS.cart.quantity_param);
  var v = encodeURIComponent(quantity);
  return (0, _isEmpty2.default)(quantity) ? '' : "".concat(k, "=").concat(v);
};
/**
 * @function handleCartState
 * @description check the cart(s) state and disable/enable actions on current status.
 */


var handleCartState = function handleCartState(e) {
  var carts = tools.getNodes('bc-cart', true);
  var eventMiniCart = e.detail ? e.detail.miniCartID : '';

  if (!carts) {
    return;
  }

  carts.forEach(function (cart) {
    var itemInputs = tools.getNodes('bc-cart-item__quantity', true, cart);
    var itemRemoveButtons = tools.getNodes('.bc-cart-item__remove-button', true, cart, true);
    var checkoutButton = tools.getNodes('proceed-to-checkout', false, cart)[0];
    var isMiniCart = tools.closest(cart, '[data-js="bc-mini-cart"]');
    var shippingMethods = tools.getNodes('[data-shipping-field]', true, cart, true);

    if (isMiniCart && isMiniCart.dataset.miniCartId === eventMiniCart) {
      return;
    }

    if (_cartState.default.isFetching) {
      itemInputs.forEach(function (item) {
        item.setAttribute('disabled', 'disabled');
      });
      itemRemoveButtons.forEach(function (item) {
        item.setAttribute('disabled', 'disabled');
      });

      if (checkoutButton) {
        checkoutButton.setAttribute('disabled', 'disabled');
      }

      if (shippingMethods) {
        shippingMethods.forEach(function (field) {
          return field.setAttribute('disabled', 'disabled');
        });
      }

      cart.classList.add('bc-updating-cart');
      return;
    }

    itemInputs.forEach(function (item) {
      item.removeAttribute('disabled');
    });
    itemRemoveButtons.forEach(function (item) {
      item.removeAttribute('disabled');
    });

    if (checkoutButton) {
      checkoutButton.removeAttribute('disabled');
    }

    if (shippingMethods) {
      shippingMethods.forEach(function (field) {
        return field.removeAttribute('disabled');
      });
    }

    cart.classList.remove('bc-updating-cart');
  });
};
/**
 * @function updateCartItems
 * @description update the cart item total value.
 * @param data {object}
 */


var updateCartItems = function updateCartItems() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  tools.getNodes('bc-cart', true).forEach(function (cart) {
    (0, _entries.default)(data.items).forEach(function (_ref) {
      var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];

      var id = key;
      var totalSalePrice = value.total_sale_price.formatted;
      var itemRow = tools.getNodes(id, false, cart, false)[0];
      var totalPrice = tools.getNodes('.bc-cart-item-total-price', false, itemRow, true)[0];
      totalPrice.innerHTML = totalSalePrice;
    });
  });
};
/**
 * @function updatedCartTotals
 * @description update the cart subtotal amount.
 * @param data
 */


var updatedCartTotals = function updatedCartTotals() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var cartData = data.detail ? data.detail.data : data;
  tools.getNodes('bc-cart', true).forEach(function (cart) {
    var baseAmount = cartData.subtotal.formatted;
    var subTotal = tools.getNodes('.bc-cart-subtotal__amount', false, cart, true)[0];
    var taxTotal = tools.getNodes('.bc-cart-tax__amount', false, cart, true)[0];
    var cartTotal = tools.getNodes('.bc-cart-total__amount', false, cart, true)[0];
    subTotal.textContent = baseAmount;

    if (taxTotal) {
      taxTotal.textContent = cartData.tax_amount.formatted;
    }

    if (cartTotal) {
      cartTotal.textContent = cartData.cart_amount.formatted;
    }
  });
};

var handleFlatsomeTheme = function handleFlatsomeTheme() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var flatsome = tools.getNodes('.bc-wp-flatsome-theme', false, document, true)[0];

  if (!flatsome) {
    return;
  }

  (0, _cartMenuItem.updateFlatsomeCartMenuQty)();
  (0, _cartMenuItem.updateFlatsomeCartMenuPrice)(data);
};
/**
 * @function cartItemQtyUpdated
 * @description handle the API response when a cart item is updated.
 * @param data
 */


var cartItemQtyUpdated = function cartItemQtyUpdated() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  if (!data) {
    return;
  }

  updateCartItems(data);
  updatedCartTotals(data);
  (0, _cartMenuItem.updateMenuQtyTotal)(data);
  handleFlatsomeTheme(data);
};
/**
 * @function bcAPICodeResponseHandler
 * @description Handle error message output for API errors.
 * @param eventTrigger
 * @param data
 */


var bcAPICodeResponseHandler = function bcAPICodeResponseHandler() {
  var eventTrigger = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var APIErrorNotification = tools.getNodes('bc-cart-error-message');

  if (!APIErrorNotification) {
    return;
  }

  APIErrorNotification.forEach(function (container) {
    if (data.statusCode === 502) {
      container.innerHTML = _i18n.NLS.cart.cart_error_502;
      tools.closest(container, '.bc-cart-error').classList.add('message-active');
      return;
    }

    container.innerHTML = '';
    tools.closest(container, '.bc-cart-error').classList.remove('message-active');
  });
};
/**
 * @function handleQtyUpdate
 * @description after an item qty has been updated, run ajax to update the cart.
 * @param inputEvent
 */


var handleQtyUpdate = function handleQtyUpdate(inputEvent) {
  if (inputEvent.delegateTarget.value.length <= 0) {
    return;
  }

  var cartURL = getCartAPIURL(inputEvent);
  var queryString = getItemUpdateQueryString(inputEvent);
  var isMiniCart = tools.closest(inputEvent.delegateTarget, '[data-js="bc-mini-cart"]');
  var miniCartID = isMiniCart ? isMiniCart.dataset.miniCartId : '';
  window.clearTimeout(timeout);
  timeout = (0, _delay2.default)(function () {
    _cartState.default.isFetching = true;
    handleCartState(inputEvent.delegateTarget);
    (0, _ajax.wpAPICartUpdate)(cartURL, queryString).end(function (err, res) {
      _cartState.default.isFetching = false;
      handleCartState(inputEvent.delegateTarget);
      bcAPICodeResponseHandler(res);

      if (err) {
        console.error(err); // case: If we get a 502 from the cart API here reset the value of the field to its original value.

        if (res.statusCode === 502) {
          inputEvent.delegateTarget.value = inputEvent.delegateTarget.dataset.currentvalue ? inputEvent.delegateTarget.dataset.currentvalue : inputEvent.delegateTarget.getAttribute('value');
        }

        return;
      }

      inputEvent.delegateTarget.setAttribute('data-currentvalue', inputEvent.delegateTarget.value);
      cartItemQtyUpdated(res.body);
      (0, _events.trigger)({
        event: _events2.AJAX_CART_UPDATE,
        data: {
          miniCartID: miniCartID,
          cartData: res.body
        },
        native: false
      });
    });
  }, timeoutOptions.delay);
};
/**
 * @function removeCartItem
 * @description remove a cart item row from the cart view DOM.
 * @param itemRow string
 * @param data {object}
 */


var removeCartItem = function removeCartItem() {
  var itemRow = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

  if (!itemRow.parentNode) {
    return;
  }

  itemRow.parentNode.removeChild(itemRow);

  if (data.statusCode === 204) {
    var cart = tools.getNodes('bc-cart', false, itemRow)[0];
    var cartFooter = tools.getNodes('.bc-cart-footer', false, cart, true)[0];
    var cartBody = tools.getNodes('.bc-cart-body', false, cart, true)[0];
    cartBody.insertAdjacentHTML('afterbegin', _cartTemplates.cartEmpty);
    cartFooter.parentNode.removeChild(cartFooter);

    _jsCookie.default.remove(_cookies.CART_ID_COOKIE_NAME);

    _jsCookie.default.remove(_cookies.CART_ITEM_COUNT_COOKIE);

    (0, _cartMenuItem.updateCartMenuItem)();
    handleFlatsomeTheme(data.body);
    return;
  }

  updatedCartTotals(data.body);
  (0, _cartMenuItem.updateMenuQtyTotal)(data.body);
  handleFlatsomeTheme(data.body);
};
/**
 * @function handleCartItemRemoval
 * @description send and handle the API response for removal of a cart item.
 * @param e
 */


var handleCartItemRemoval = function handleCartItemRemoval(e) {
  var cartItemURL = getCartAPIURL(e);
  var deleteItemURL = "".concat(cartItemURL, "/delete");
  var removeButton = e.delegateTarget;
  var isMiniCart = tools.closest(removeButton, '[data-js="bc-mini-cart"]');
  var miniCartID = isMiniCart ? isMiniCart.dataset.miniCartId : '';

  if (_cartState.default.isFetching || (0, _isEmpty2.default)(cartItemURL)) {
    return;
  }

  _cartState.default.isFetching = true;
  handleCartState(removeButton);
  (0, _ajax.wpAPICartDelete)(deleteItemURL).end(function (err, res) {
    var itemRow = tools.closest(removeButton, "[data-js=\"".concat(removeButton.dataset.cart_item_id, "\"]"));
    _cartState.default.isFetching = false;
    handleCartState(removeButton);
    bcAPICodeResponseHandler(removeButton, res);

    if (err) {
      console.error(err);
      return;
    }

    removeCartItem(itemRow, res);
    (0, _events.trigger)({
      event: _events2.AJAX_CART_UPDATE,
      data: {
        miniCartID: miniCartID,
        cartData: res.body
      },
      native: false
    });
  });
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)(document, '[data-js="bc-cart-item__quantity"]', 'input', handleQtyUpdate);
  (0, _delegate.default)(document, '[data-js="remove-cart-item"]', 'click', handleCartItemRemoval);
  (0, _events.on)(document, _events2.HANDLE_CART_STATE, handleCartState);
  (0, _events.on)(document, _events2.HANDLE_COUPON_CODE, handleCartState);
  (0, _events.on)(document, _events2.HANDLE_COUPON_CODE, updatedCartTotals);
};

var init = function init() {
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 110 */
/***/ (function(module, exports) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

!(function(global) {
  "use strict";

  var Op = Object.prototype;
  var hasOwn = Op.hasOwnProperty;
  var undefined; // More compressible than void 0.
  var $Symbol = typeof Symbol === "function" ? Symbol : {};
  var iteratorSymbol = $Symbol.iterator || "@@iterator";
  var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
  var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  var inModule = typeof module === "object";
  var runtime = global.regeneratorRuntime;
  if (runtime) {
    if (inModule) {
      // If regeneratorRuntime is defined globally and we're in a module,
      // make the exports object identical to regeneratorRuntime.
      module.exports = runtime;
    }
    // Don't bother evaluating the rest of this file if the runtime was
    // already defined globally.
    return;
  }

  // Define the runtime globally (as expected by generated code) as either
  // module.exports (if we're in a module) or a new, empty object.
  runtime = global.regeneratorRuntime = inModule ? module.exports : {};

  function wrap(innerFn, outerFn, self, tryLocsList) {
    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
    var generator = Object.create(protoGenerator.prototype);
    var context = new Context(tryLocsList || []);

    // The ._invoke method unifies the implementations of the .next,
    // .throw, and .return methods.
    generator._invoke = makeInvokeMethod(innerFn, self, context);

    return generator;
  }
  runtime.wrap = wrap;

  // Try/catch helper to minimize deoptimizations. Returns a completion
  // record like context.tryEntries[i].completion. This interface could
  // have been (and was previously) designed to take a closure to be
  // invoked without arguments, but in all the cases we care about we
  // already have an existing method we want to call, so there's no need
  // to create a new function object. We can even get away with assuming
  // the method takes exactly one argument, since that happens to be true
  // in every case, so we don't have to touch the arguments object. The
  // only additional allocation required is the completion record, which
  // has a stable shape and so hopefully should be cheap to allocate.
  function tryCatch(fn, obj, arg) {
    try {
      return { type: "normal", arg: fn.call(obj, arg) };
    } catch (err) {
      return { type: "throw", arg: err };
    }
  }

  var GenStateSuspendedStart = "suspendedStart";
  var GenStateSuspendedYield = "suspendedYield";
  var GenStateExecuting = "executing";
  var GenStateCompleted = "completed";

  // Returning this object from the innerFn has the same effect as
  // breaking out of the dispatch switch statement.
  var ContinueSentinel = {};

  // Dummy constructor functions that we use as the .constructor and
  // .constructor.prototype properties for functions that return Generator
  // objects. For full spec compliance, you may wish to configure your
  // minifier not to mangle the names of these two functions.
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}

  // This is a polyfill for %IteratorPrototype% for environments that
  // don't natively support it.
  var IteratorPrototype = {};
  IteratorPrototype[iteratorSymbol] = function () {
    return this;
  };

  var getProto = Object.getPrototypeOf;
  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  if (NativeIteratorPrototype &&
      NativeIteratorPrototype !== Op &&
      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
    // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
  }

  var Gp = GeneratorFunctionPrototype.prototype =
    Generator.prototype = Object.create(IteratorPrototype);
  GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;
  GeneratorFunctionPrototype.constructor = GeneratorFunction;
  GeneratorFunctionPrototype[toStringTagSymbol] =
    GeneratorFunction.displayName = "GeneratorFunction";

  // Helper for defining the .next, .throw, and .return methods of the
  // Iterator interface in terms of a single ._invoke method.
  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function(method) {
      prototype[method] = function(arg) {
        return this._invoke(method, arg);
      };
    });
  }

  runtime.isGeneratorFunction = function(genFun) {
    var ctor = typeof genFun === "function" && genFun.constructor;
    return ctor
      ? ctor === GeneratorFunction ||
        // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction"
      : false;
  };

  runtime.mark = function(genFun) {
    if (Object.setPrototypeOf) {
      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
    } else {
      genFun.__proto__ = GeneratorFunctionPrototype;
      if (!(toStringTagSymbol in genFun)) {
        genFun[toStringTagSymbol] = "GeneratorFunction";
      }
    }
    genFun.prototype = Object.create(Gp);
    return genFun;
  };

  // Within the body of any async function, `await x` is transformed to
  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
  // `hasOwn.call(value, "__await")` to determine if the yielded value is
  // meant to be awaited.
  runtime.awrap = function(arg) {
    return { __await: arg };
  };

  function AsyncIterator(generator) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);
      if (record.type === "throw") {
        reject(record.arg);
      } else {
        var result = record.arg;
        var value = result.value;
        if (value &&
            typeof value === "object" &&
            hasOwn.call(value, "__await")) {
          return Promise.resolve(value.__await).then(function(value) {
            invoke("next", value, resolve, reject);
          }, function(err) {
            invoke("throw", err, resolve, reject);
          });
        }

        return Promise.resolve(value).then(function(unwrapped) {
          // When a yielded Promise is resolved, its final value becomes
          // the .value of the Promise<{value,done}> result for the
          // current iteration. If the Promise is rejected, however, the
          // result for this iteration will be rejected with the same
          // reason. Note that rejections of yielded Promises are not
          // thrown back into the generator function, as is the case
          // when an awaited Promise is rejected. This difference in
          // behavior between yield and await is important, because it
          // allows the consumer to decide what to do with the yielded
          // rejection (swallow it and continue, manually .throw it back
          // into the generator, abandon iteration, whatever). With
          // await, by contrast, there is no opportunity to examine the
          // rejection reason outside the generator function, so the
          // only option is to throw it from the await expression, and
          // let the generator function handle the exception.
          result.value = unwrapped;
          resolve(result);
        }, reject);
      }
    }

    var previousPromise;

    function enqueue(method, arg) {
      function callInvokeWithMethodAndArg() {
        return new Promise(function(resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise =
        // If enqueue has been called before, then we want to wait until
        // all previous Promises have been resolved before calling invoke,
        // so that results are always delivered in the correct order. If
        // enqueue has not been called before, then it is important to
        // call invoke immediately, without waiting on a callback to fire,
        // so that the async generator function has the opportunity to do
        // any necessary setup in a predictable way. This predictability
        // is why the Promise constructor synchronously invokes its
        // executor callback, and why async functions synchronously
        // execute code before the first await. Since we implement simple
        // async functions in terms of async generators, it is especially
        // important to get this right, even though it requires care.
        previousPromise ? previousPromise.then(
          callInvokeWithMethodAndArg,
          // Avoid propagating failures to Promises returned by later
          // invocations of the iterator.
          callInvokeWithMethodAndArg
        ) : callInvokeWithMethodAndArg();
    }

    // Define the unified helper method that is used to implement .next,
    // .throw, and .return (see defineIteratorMethods).
    this._invoke = enqueue;
  }

  defineIteratorMethods(AsyncIterator.prototype);
  AsyncIterator.prototype[asyncIteratorSymbol] = function () {
    return this;
  };
  runtime.AsyncIterator = AsyncIterator;

  // Note that simple async functions are implemented on top of
  // AsyncIterator objects; they just return a Promise for the value of
  // the final result produced by the iterator.
  runtime.async = function(innerFn, outerFn, self, tryLocsList) {
    var iter = new AsyncIterator(
      wrap(innerFn, outerFn, self, tryLocsList)
    );

    return runtime.isGeneratorFunction(outerFn)
      ? iter // If outerFn is a generator, return the full iterator.
      : iter.next().then(function(result) {
          return result.done ? result.value : iter.next();
        });
  };

  function makeInvokeMethod(innerFn, self, context) {
    var state = GenStateSuspendedStart;

    return function invoke(method, arg) {
      if (state === GenStateExecuting) {
        throw new Error("Generator is already running");
      }

      if (state === GenStateCompleted) {
        if (method === "throw") {
          throw arg;
        }

        // Be forgiving, per 25.3.3.3.3 of the spec:
        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
        return doneResult();
      }

      context.method = method;
      context.arg = arg;

      while (true) {
        var delegate = context.delegate;
        if (delegate) {
          var delegateResult = maybeInvokeDelegate(delegate, context);
          if (delegateResult) {
            if (delegateResult === ContinueSentinel) continue;
            return delegateResult;
          }
        }

        if (context.method === "next") {
          // Setting context._sent for legacy support of Babel's
          // function.sent implementation.
          context.sent = context._sent = context.arg;

        } else if (context.method === "throw") {
          if (state === GenStateSuspendedStart) {
            state = GenStateCompleted;
            throw context.arg;
          }

          context.dispatchException(context.arg);

        } else if (context.method === "return") {
          context.abrupt("return", context.arg);
        }

        state = GenStateExecuting;

        var record = tryCatch(innerFn, self, context);
        if (record.type === "normal") {
          // If an exception is thrown from innerFn, we leave state ===
          // GenStateExecuting and loop back for another invocation.
          state = context.done
            ? GenStateCompleted
            : GenStateSuspendedYield;

          if (record.arg === ContinueSentinel) {
            continue;
          }

          return {
            value: record.arg,
            done: context.done
          };

        } else if (record.type === "throw") {
          state = GenStateCompleted;
          // Dispatch the exception by looping back around to the
          // context.dispatchException(context.arg) call above.
          context.method = "throw";
          context.arg = record.arg;
        }
      }
    };
  }

  // Call delegate.iterator[context.method](context.arg) and handle the
  // result, either by returning a { value, done } result from the
  // delegate iterator, or by modifying context.method and context.arg,
  // setting context.delegate to null, and returning the ContinueSentinel.
  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];
    if (method === undefined) {
      // A .throw or .return when the delegate iterator has no .throw
      // method always terminates the yield* loop.
      context.delegate = null;

      if (context.method === "throw") {
        if (delegate.iterator.return) {
          // If the delegate iterator has a return method, give it a
          // chance to clean up.
          context.method = "return";
          context.arg = undefined;
          maybeInvokeDelegate(delegate, context);

          if (context.method === "throw") {
            // If maybeInvokeDelegate(context) changed context.method from
            // "return" to "throw", let that override the TypeError below.
            return ContinueSentinel;
          }
        }

        context.method = "throw";
        context.arg = new TypeError(
          "The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);

    if (record.type === "throw") {
      context.method = "throw";
      context.arg = record.arg;
      context.delegate = null;
      return ContinueSentinel;
    }

    var info = record.arg;

    if (! info) {
      context.method = "throw";
      context.arg = new TypeError("iterator result is not an object");
      context.delegate = null;
      return ContinueSentinel;
    }

    if (info.done) {
      // Assign the result of the finished delegate to the temporary
      // variable specified by delegate.resultName (see delegateYield).
      context[delegate.resultName] = info.value;

      // Resume execution at the desired location (see delegateYield).
      context.next = delegate.nextLoc;

      // If context.method was "throw" but the delegate handled the
      // exception, let the outer generator proceed normally. If
      // context.method was "next", forget context.arg since it has been
      // "consumed" by the delegate iterator. If context.method was
      // "return", allow the original .return call to continue in the
      // outer generator.
      if (context.method !== "return") {
        context.method = "next";
        context.arg = undefined;
      }

    } else {
      // Re-yield the result returned by the delegate method.
      return info;
    }

    // The delegate iterator is finished, so forget it and continue with
    // the outer generator.
    context.delegate = null;
    return ContinueSentinel;
  }

  // Define Generator.prototype.{next,throw,return} in terms of the
  // unified ._invoke helper method.
  defineIteratorMethods(Gp);

  Gp[toStringTagSymbol] = "Generator";

  // A Generator should always return itself as the iterator object when the
  // @@iterator function is called on it. Some browsers' implementations of the
  // iterator prototype chain incorrectly implement this, causing the Generator
  // object to not be returned from this call. This ensures that doesn't happen.
  // See https://github.com/facebook/regenerator/issues/274 for more details.
  Gp[iteratorSymbol] = function() {
    return this;
  };

  Gp.toString = function() {
    return "[object Generator]";
  };

  function pushTryEntry(locs) {
    var entry = { tryLoc: locs[0] };

    if (1 in locs) {
      entry.catchLoc = locs[1];
    }

    if (2 in locs) {
      entry.finallyLoc = locs[2];
      entry.afterLoc = locs[3];
    }

    this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal";
    delete record.arg;
    entry.completion = record;
  }

  function Context(tryLocsList) {
    // The root entry object (effectively a try statement without a catch
    // or a finally block) gives us a place to store values thrown from
    // locations where there is no enclosing try statement.
    this.tryEntries = [{ tryLoc: "root" }];
    tryLocsList.forEach(pushTryEntry, this);
    this.reset(true);
  }

  runtime.keys = function(object) {
    var keys = [];
    for (var key in object) {
      keys.push(key);
    }
    keys.reverse();

    // Rather than returning an object with a next method, we keep
    // things simple and return the next function itself.
    return function next() {
      while (keys.length) {
        var key = keys.pop();
        if (key in object) {
          next.value = key;
          next.done = false;
          return next;
        }
      }

      // To avoid creating an additional object, we just hang the .value
      // and .done properties off the next function object itself. This
      // also ensures that the minifier will not anonymize the function.
      next.done = true;
      return next;
    };
  };

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) {
        return iteratorMethod.call(iterable);
      }

      if (typeof iterable.next === "function") {
        return iterable;
      }

      if (!isNaN(iterable.length)) {
        var i = -1, next = function next() {
          while (++i < iterable.length) {
            if (hasOwn.call(iterable, i)) {
              next.value = iterable[i];
              next.done = false;
              return next;
            }
          }

          next.value = undefined;
          next.done = true;

          return next;
        };

        return next.next = next;
      }
    }

    // Return an iterator with no values.
    return { next: doneResult };
  }
  runtime.values = values;

  function doneResult() {
    return { value: undefined, done: true };
  }

  Context.prototype = {
    constructor: Context,

    reset: function(skipTempReset) {
      this.prev = 0;
      this.next = 0;
      // Resetting context._sent for legacy support of Babel's
      // function.sent implementation.
      this.sent = this._sent = undefined;
      this.done = false;
      this.delegate = null;

      this.method = "next";
      this.arg = undefined;

      this.tryEntries.forEach(resetTryEntry);

      if (!skipTempReset) {
        for (var name in this) {
          // Not sure about the optimal order of these conditions:
          if (name.charAt(0) === "t" &&
              hasOwn.call(this, name) &&
              !isNaN(+name.slice(1))) {
            this[name] = undefined;
          }
        }
      }
    },

    stop: function() {
      this.done = true;

      var rootEntry = this.tryEntries[0];
      var rootRecord = rootEntry.completion;
      if (rootRecord.type === "throw") {
        throw rootRecord.arg;
      }

      return this.rval;
    },

    dispatchException: function(exception) {
      if (this.done) {
        throw exception;
      }

      var context = this;
      function handle(loc, caught) {
        record.type = "throw";
        record.arg = exception;
        context.next = loc;

        if (caught) {
          // If the dispatched exception was caught by a catch block,
          // then let that catch block handle the exception normally.
          context.method = "next";
          context.arg = undefined;
        }

        return !! caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        var record = entry.completion;

        if (entry.tryLoc === "root") {
          // Exception thrown outside of any try block that could handle
          // it, so set the completion value of the entire function to
          // throw the exception.
          return handle("end");
        }

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc");
          var hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            } else if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) {
              return handle(entry.catchLoc, true);
            }

          } else if (hasFinally) {
            if (this.prev < entry.finallyLoc) {
              return handle(entry.finallyLoc);
            }

          } else {
            throw new Error("try statement without catch or finally");
          }
        }
      }
    },

    abrupt: function(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc <= this.prev &&
            hasOwn.call(entry, "finallyLoc") &&
            this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      if (finallyEntry &&
          (type === "break" ||
           type === "continue") &&
          finallyEntry.tryLoc <= arg &&
          arg <= finallyEntry.finallyLoc) {
        // Ignore the finally entry if control is not jumping to a
        // location outside the try/catch block.
        finallyEntry = null;
      }

      var record = finallyEntry ? finallyEntry.completion : {};
      record.type = type;
      record.arg = arg;

      if (finallyEntry) {
        this.method = "next";
        this.next = finallyEntry.finallyLoc;
        return ContinueSentinel;
      }

      return this.complete(record);
    },

    complete: function(record, afterLoc) {
      if (record.type === "throw") {
        throw record.arg;
      }

      if (record.type === "break" ||
          record.type === "continue") {
        this.next = record.arg;
      } else if (record.type === "return") {
        this.rval = this.arg = record.arg;
        this.method = "return";
        this.next = "end";
      } else if (record.type === "normal" && afterLoc) {
        this.next = afterLoc;
      }

      return ContinueSentinel;
    },

    finish: function(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) {
          this.complete(entry.completion, entry.afterLoc);
          resetTryEntry(entry);
          return ContinueSentinel;
        }
      }
    },

    "catch": function(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;
          if (record.type === "throw") {
            var thrown = record.arg;
            resetTryEntry(entry);
          }
          return thrown;
        }
      }

      // The context.catch method must only be called with a location
      // argument that corresponds to a known catch block.
      throw new Error("illegal catch attempt");
    },

    delegateYield: function(iterable, resultName, nextLoc) {
      this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      };

      if (this.method === "next") {
        // Deliberately forget the last sent value so that we don't
        // accidentally pass it on to the delegate.
        this.arg = undefined;
      }

      return ContinueSentinel;
    }
  };
})(
  // In sloppy mode, unbound `this` refers to the global object, fallback to
  // Function constructor if we're in global strict mode. That is sadly a form
  // of indirect eval which violates Content Security Policy.
  (function() { return this })() || Function("return this")()
);


/***/ }),
/* 111 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 25.4.1.5 NewPromiseCapability(C)
var aFunction = __webpack_require__(56);

function PromiseCapability(C) {
  var resolve, reject;
  this.promise = new C(function ($$resolve, $$reject) {
    if (resolve !== undefined || reject !== undefined) throw TypeError('Bad Promise constructor');
    resolve = $$resolve;
    reject = $$reject;
  });
  this.resolve = aFunction(resolve);
  this.reject = aFunction(reject);
}

module.exports.f = function (C) {
  return new PromiseCapability(C);
};


/***/ }),
/* 112 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {/** Detect free variable `global` from Node.js. */
var freeGlobal = typeof global == 'object' && global && global.Object === Object && global;

module.exports = freeGlobal;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(113)))

/***/ }),
/* 113 */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || Function("return this")() || (1,eval)("this");
} catch(e) {
	// This works if the window reference is available
	if(typeof window === "object")
		g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ }),
/* 114 */
/***/ (function(module, exports, __webpack_require__) {

var baseAssignValue = __webpack_require__(115),
    eq = __webpack_require__(49);

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Assigns `value` to `key` of `object` if the existing value is not equivalent
 * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
 * for equality comparisons.
 *
 * @private
 * @param {Object} object The object to modify.
 * @param {string} key The key of the property to assign.
 * @param {*} value The value to assign.
 */
function assignValue(object, key, value) {
  var objValue = object[key];
  if (!(hasOwnProperty.call(object, key) && eq(objValue, value)) ||
      (value === undefined && !(key in object))) {
    baseAssignValue(object, key, value);
  }
}

module.exports = assignValue;


/***/ }),
/* 115 */
/***/ (function(module, exports, __webpack_require__) {

var defineProperty = __webpack_require__(116);

/**
 * The base implementation of `assignValue` and `assignMergeValue` without
 * value checks.
 *
 * @private
 * @param {Object} object The object to modify.
 * @param {string} key The key of the property to assign.
 * @param {*} value The value to assign.
 */
function baseAssignValue(object, key, value) {
  if (key == '__proto__' && defineProperty) {
    defineProperty(object, key, {
      'configurable': true,
      'enumerable': true,
      'value': value,
      'writable': true
    });
  } else {
    object[key] = value;
  }
}

module.exports = baseAssignValue;


/***/ }),
/* 116 */
/***/ (function(module, exports, __webpack_require__) {

var getNative = __webpack_require__(20);

var defineProperty = (function() {
  try {
    var func = getNative(Object, 'defineProperty');
    func({}, '', {});
    return func;
  } catch (e) {}
}());

module.exports = defineProperty;


/***/ }),
/* 117 */
/***/ (function(module, exports, __webpack_require__) {

var baseGetTag = __webpack_require__(35),
    isObject = __webpack_require__(27);

/** `Object#toString` result references. */
var asyncTag = '[object AsyncFunction]',
    funcTag = '[object Function]',
    genTag = '[object GeneratorFunction]',
    proxyTag = '[object Proxy]';

/**
 * Checks if `value` is classified as a `Function` object.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a function, else `false`.
 * @example
 *
 * _.isFunction(_);
 * // => true
 *
 * _.isFunction(/abc/);
 * // => false
 */
function isFunction(value) {
  if (!isObject(value)) {
    return false;
  }
  // The use of `Object#toString` avoids issues with the `typeof` operator
  // in Safari 9 which returns 'object' for typed arrays and other constructors.
  var tag = baseGetTag(value);
  return tag == funcTag || tag == genTag || tag == asyncTag || tag == proxyTag;
}

module.exports = isFunction;


/***/ }),
/* 118 */
/***/ (function(module, exports) {

/** Used for built-in method references. */
var funcProto = Function.prototype;

/** Used to resolve the decompiled source of functions. */
var funcToString = funcProto.toString;

/**
 * Converts `func` to its source code.
 *
 * @private
 * @param {Function} func The function to convert.
 * @returns {string} Returns the source code.
 */
function toSource(func) {
  if (func != null) {
    try {
      return funcToString.call(func);
    } catch (e) {}
    try {
      return (func + '');
    } catch (e) {}
  }
  return '';
}

module.exports = toSource;


/***/ }),
/* 119 */
/***/ (function(module, exports, __webpack_require__) {

var eq = __webpack_require__(49),
    isArrayLike = __webpack_require__(29),
    isIndex = __webpack_require__(78),
    isObject = __webpack_require__(27);

/**
 * Checks if the given arguments are from an iteratee call.
 *
 * @private
 * @param {*} value The potential iteratee value argument.
 * @param {*} index The potential iteratee index or key argument.
 * @param {*} object The potential iteratee object argument.
 * @returns {boolean} Returns `true` if the arguments are from an iteratee call,
 *  else `false`.
 */
function isIterateeCall(value, index, object) {
  if (!isObject(object)) {
    return false;
  }
  var type = typeof index;
  if (type == 'number'
        ? (isArrayLike(object) && isIndex(index, object.length))
        : (type == 'string' && index in object)
      ) {
    return eq(object[index], value);
  }
  return false;
}

module.exports = isIterateeCall;


/***/ }),
/* 120 */
/***/ (function(module, exports) {

module.exports = function(module) {
	if(!module.webpackPolyfill) {
		module.deprecate = function() {};
		module.paths = [];
		// module.parent = undefined by default
		if(!module.children) module.children = [];
		Object.defineProperty(module, "loaded", {
			enumerable: true,
			get: function() {
				return module.l;
			}
		});
		Object.defineProperty(module, "id", {
			enumerable: true,
			get: function() {
				return module.i;
			}
		});
		module.webpackPolyfill = 1;
	}
	return module;
};


/***/ }),
/* 121 */
/***/ (function(module, exports) {

/**
 * The base implementation of `_.unary` without support for storing metadata.
 *
 * @private
 * @param {Function} func The function to cap arguments for.
 * @returns {Function} Returns the new capped function.
 */
function baseUnary(func) {
  return function(value) {
    return func(value);
  };
}

module.exports = baseUnary;


/***/ }),
/* 122 */
/***/ (function(module, exports, __webpack_require__) {

var isPrototype = __webpack_require__(79),
    nativeKeys = __webpack_require__(217);

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * The base implementation of `_.keys` which doesn't treat sparse arrays as dense.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names.
 */
function baseKeys(object) {
  if (!isPrototype(object)) {
    return nativeKeys(object);
  }
  var result = [];
  for (var key in Object(object)) {
    if (hasOwnProperty.call(object, key) && key != 'constructor') {
      result.push(key);
    }
  }
  return result;
}

module.exports = baseKeys;


/***/ }),
/* 123 */,
/* 124 */,
/* 125 */,
/* 126 */,
/* 127 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _verge = _interopRequireDefault(__webpack_require__(128));

var _state = _interopRequireDefault(__webpack_require__(129));

var _options = __webpack_require__(228);

/**
 * @module
 * @exports viewportDims
 * @description Sets viewport dimensions using verge on shared state
 * and detects mobile or desktop state.
 */
var viewportDims = function viewportDims() {
  _state.default.v_height = _verge.default.viewportH();
  _state.default.v_width = _verge.default.viewportW();

  if (_state.default.v_width >= _options.MOBILE_BREAKPOINT) {
    _state.default.is_desktop = true;
    _state.default.is_mobile = false;
  } else {
    _state.default.is_desktop = false;
    _state.default.is_mobile = true;
  }
};

var _default = viewportDims;
exports.default = _default;

/***/ }),
/* 128 */,
/* 129 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _default = {
  desktop_initialized: false,
  is_desktop: false,
  is_mobile: false,
  mobile_initialized: false,
  v_height: 0,
  v_width: 0
};
exports.default = _default;

/***/ }),
/* 130 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _uniqueId2 = _interopRequireDefault(__webpack_require__(23));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _swiper = _interopRequireDefault(__webpack_require__(137));

var _events = __webpack_require__(5);

var tools = _interopRequireWildcard(__webpack_require__(2));

var el = {
  galleries: tools.getNodes('bc-product-gallery')
};
var instances = {
  swipers: {}
};
var galleryOptions = {
  galleryMain: function galleryMain() {
    return {
      a11y: true,
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      threshold: 170
    };
  },
  galleryThumbs: function galleryThumbs() {
    return {
      a11y: true,
      slidesPerView: 'auto',
      touchRatio: 0.2,
      centeredSlides: true,
      slideToClickedSlide: true,
      threshold: 30
    };
  }
};
/**
 * @function syncMainSlider
 * @description Sync the main slider to the carousel.
 * Too bad swiper has a bug with this and we have to resort this this stuff
 * https://github.com/nolimits4web/Swiper/issues/1658
 */

var syncMainSlider = function syncMainSlider(e) {
  var carousel = tools.closest(e.delegateTarget, '.swiper-container');
  instances.swipers[carousel.dataset.controls].slideTo(e.delegateTarget.dataset.index);
};
/**
 * @module bindCarouselEvents
 * @description Bind Carousel Events.
 */


var bindCarouselEvents = function bindCarouselEvents(swiperThumbId, swiperMainId) {
  instances.swipers[swiperMainId].on('slideChange', function () {
    instances.swipers[swiperThumbId].slideTo(instances.swipers[swiperMainId].activeIndex);
  });
  (0, _delegate.default)(instances.swipers[swiperThumbId].wrapperEl, '[data-js="bc-gallery-thumb-trigger"]', 'click', syncMainSlider);
};
/**
 * @function initCarousel
 * @description Init the carousel
 */


var initCarousel = function initCarousel(slider, swiperMainId) {
  var carousel = slider.nextElementSibling;
  var swiperThumbId = (0, _uniqueId2.default)('swiper-carousel-');
  carousel.classList.add('initialized');
  var opts = galleryOptions.galleryThumbs();
  instances.swipers[swiperThumbId] = new _swiper.default(carousel, opts);
  slider.setAttribute('data-controls', swiperThumbId);
  carousel.setAttribute('data-id', swiperThumbId);
  carousel.setAttribute('data-controls', swiperMainId);
  bindCarouselEvents(swiperThumbId, swiperMainId);
};
/**
 * @module
 * @description Swiper init.
 */


var initGalleries = function initGalleries() {
  tools.getNodes('[data-js="bc-gallery-container"]:not(.initialized)', true, document, true).forEach(function (slider) {
    var swiperMainId = (0, _uniqueId2.default)('swiper-');
    slider.classList.add('initialized');
    instances.swipers[swiperMainId] = new _swiper.default(slider, galleryOptions.galleryMain());
    slider.setAttribute('data-id', swiperMainId);
    instances.swipers[swiperMainId].on('slideChange', function () {
      return (0, _events.trigger)({
        event: 'bigcommerce/gallery_slide_changed',
        data: {
          slider: instances.swipers[swiperMainId],
          previousSlide: instances.swipers[swiperMainId].previousIndex
        },
        native: false
      });
    });

    if (!slider.classList.contains('bc-product-gallery--has-carousel')) {
      return;
    }

    initCarousel(slider, swiperMainId);
  });
};

var init = function init() {
  if (!el.galleries) {
    return;
  }

  initGalleries();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 131 */,
/* 132 */,
/* 133 */,
/* 134 */,
/* 135 */
/***/ (function(module, exports, __webpack_require__) {

var baseToString = __webpack_require__(235);

/**
 * Converts `value` to a string. An empty string is returned for `null`
 * and `undefined` values. The sign of `-0` is preserved.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to convert.
 * @returns {string} Returns the converted string.
 * @example
 *
 * _.toString(null);
 * // => ''
 *
 * _.toString(-0);
 * // => '-0'
 *
 * _.toString([1, 2, 3]);
 * // => '1,2,3'
 */
function toString(value) {
  return value == null ? '' : baseToString(value);
}

module.exports = toString;


/***/ }),
/* 136 */
/***/ (function(module, exports) {

/**
 * A specialized version of `_.map` for arrays without support for iteratee
 * shorthands.
 *
 * @private
 * @param {Array} [array] The array to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Array} Returns the new mapped array.
 */
function arrayMap(array, iteratee) {
  var index = -1,
      length = array == null ? 0 : array.length,
      result = Array(length);

  while (++index < length) {
    result[index] = iteratee(array[index], index, array);
  }
  return result;
}

module.exports = arrayMap;


/***/ }),
/* 137 */,
/* 138 */,
/* 139 */,
/* 140 */,
/* 141 */,
/* 142 */,
/* 143 */,
/* 144 */,
/* 145 */,
/* 146 */,
/* 147 */,
/* 148 */,
/* 149 */,
/* 150 */,
/* 151 */,
/* 152 */,
/* 153 */,
/* 154 */,
/* 155 */,
/* 156 */,
/* 157 */,
/* 158 */,
/* 159 */,
/* 160 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _values = _interopRequireDefault(__webpack_require__(33));

var _keys = _interopRequireDefault(__webpack_require__(64));

var _youtubePlayer = _interopRequireDefault(__webpack_require__(291));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _events = __webpack_require__(5);

var el = {
  videos: tools.getNodes('bc-product-video-player', true)
};
var instances = {
  players: {}
};
/**
 * @function stopPreviousVideo
 * @param e
 */

var stopPreviousVideo = function stopPreviousVideo(e) {
  if ((0, _keys.default)(instances.players).length > 0) {
    return;
  }

  var currentVideo = '';

  if (e.detail.quickView) {
    // If this is triggered by a quickview dialog being closed.
    var quickView = e.detail.quickView.node;
    currentVideo = tools.getNodes('.swiper-slide-active > iframe', false, quickView, true)[0];
  } else {
    // This was triggered by an actual slide change event from a click or swipe.
    currentVideo = tools.getNodes("[data-index=\"".concat(e.detail.previousSlide, "\"] > iframe"), false, e.detail.slider.wrapperEl, true)[0];
  }

  if (!currentVideo) {
    return;
  }

  var videoWrap = currentVideo.parentNode; // Remove the precious video iframe to stop it. (avoids issues with tracking current state and clicks)

  videoWrap.removeChild(currentVideo); // Immediately add the video iframe back to the slide so it can be restarted.

  videoWrap.appendChild(currentVideo);
};
/**
 * @function stopVideos
 * @description stop all videos that are currently playing.
 * TODO: This function should be deprecated/disabled in version 4.0.
 */


var stopVideos = function stopVideos() {
  if ((0, _keys.default)(instances.players).length === 0) {
    return;
  }

  (0, _values.default)(instances.players).forEach(function (player) {
    player.stopVideo();
  });
};
/**
 * @function playBCVideo
 * @description play the currently selected video.
 * @param e
 * TODO: This function should be deprecated/disabled in version 4.0.
 */


var playBCVideo = function playBCVideo(e) {
  if ((0, _keys.default)(instances.players).length === 0) {
    return;
  }

  var playerID = e.delegateTarget.dataset.playerId;

  if (!playerID) {
    return;
  }

  instances.players[playerID].playVideo();
};
/**
 * @function initPlayers
 * @description setup all available videos in an instanced object for easier control.
 */


var initPlayers = function initPlayers() {
  tools.getNodes('[data-js="bc-product-video-player"]:not(.initialized)', true, document, true).forEach(function (player) {
    var playerID = player.dataset.youtubeId;
    tools.addClass(player, 'initialized');
    instances.players[playerID] = (0, _youtubePlayer.default)(player, {
      videoId: playerID
    });
  });
};

var bindEvents = function bindEvents() {
  (0, _events.on)(document, 'bigcommerce/gallery_slide_changed', stopPreviousVideo); // TODO: These events should be deprecated/disabled in version 4.0.
  // TODO: Remove the youtube-player NPM package dependency in version 4.0.

  (0, _delegate.default)(document, '[data-js="bc-gallery-thumb-trigger"]', 'click', playBCVideo);
  (0, _events.on)(document, 'bigcommerce/gallery_slide_changed', stopVideos);
};

var init = function init() {
  if (!el.videos) {
    return;
  }

  initPlayers();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 161 */
/***/ (function(module, exports, __webpack_require__) {

var getKeys = __webpack_require__(32);
var toIObject = __webpack_require__(15);
var isEnum = __webpack_require__(43).f;
module.exports = function (isEntries) {
  return function (it) {
    var O = toIObject(it);
    var keys = getKeys(O);
    var length = keys.length;
    var i = 0;
    var result = [];
    var key;
    while (length > i) if (isEnum.call(O, key = keys[i++])) {
      result.push(isEntries ? [key, O[key]] : O[key]);
    } return result;
  };
};


/***/ }),
/* 162 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _uniqueId2 = _interopRequireDefault(__webpack_require__(23));

var _mtA11yDialog = _interopRequireDefault(__webpack_require__(101));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _events = __webpack_require__(5);

var tools = _interopRequireWildcard(__webpack_require__(2));

var _productGallery = _interopRequireDefault(__webpack_require__(130));

var _productVideos = _interopRequireDefault(__webpack_require__(160));

var _variants = _interopRequireDefault(__webpack_require__(163));

/**
 * @module Quick View Dialog Modal
 * @description Create a dialog modal for products in grids that have options.
 */
var hasCards = tools.getNodes('bc-product-loop-card').length;
var instances = {
  dialogs: {}
};
var state = {
  delay: 150
};

var getOptions = function getOptions(dialogID) {
  return {
    appendTarget: 'body',
    trigger: "[data-trigger=\"".concat(dialogID, "\"]"),
    bodyLock: true,
    effect: 'fade',
    effectSpeed: 200,
    effectEasing: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
    overlayClasses: 'bc-product-quick-view__overlay',
    contentClasses: 'bc-product-quick-view__content',
    wrapperClasses: 'bc-product-quick-view__wrapper',
    closeButtonClasses: 'bc-product-quick-view__close-button bc-icon icon-bc-cross'
  };
};

var initSingleDialog = function initSingleDialog(e) {
  var dialogTrigger = e.delegateTarget;
  var dialog = tools.closest(e.delegateTarget, '[data-js="bc-product-loop-card"]');
  var dialogID = (0, _uniqueId2.default)('bc-product-quick-view-dialog-');
  var target = tools.getNodes('[data-quick-view-script]', false, dialog, true)[0];

  if (!dialogTrigger || !target) {
    return;
  }

  dialog.classList.add('initialized');
  dialogTrigger.setAttribute('data-content', dialogID);
  dialogTrigger.setAttribute('data-trigger', dialogID);
  target.setAttribute('data-js', dialogID);
  instances.dialogs[dialogID] = new _mtA11yDialog.default(getOptions(dialogID));
  instances.dialogs[dialogID].on('render', function () {
    (0, _delay2.default)(function () {
      return (0, _productGallery.default)();
    }, state.delay);
    (0, _delay2.default)(function () {
      return (0, _productVideos.default)();
    }, state.delay);
    (0, _delay2.default)(function () {
      return (0, _variants.default)(dialog);
    }, state.delay);
    (0, _delay2.default)(function () {
      return (0, _events.trigger)({
        event: 'bigcommerce/get_pricing',
        data: {
          quickView: true
        },
        native: false
      });
    }, state.delay);
  });
  instances.dialogs[dialogID].on('hide', function () {
    return (0, _events.trigger)({
      event: 'bigcommerce/gallery_slide_changed',
      data: {
        quickView: instances.dialogs[dialogID]
      },
      native: false
    });
  });

  if (tools.closest(e.target, '[data-js="bc-product-quick-view-dialog-trigger"]')) {
    instances.dialogs[dialogID].show();
  }
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)(document.body, '[data-js="bc-product-loop-card"]:not(.initialized) [data-js="bc-product-quick-view-dialog-trigger"]', 'click', initSingleDialog);
};

var init = function init() {
  if (!hasCards) {
    return;
  }

  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 163 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

__webpack_require__(46);

var _slicedToArray2 = _interopRequireDefault(__webpack_require__(102));

var _entries = _interopRequireDefault(__webpack_require__(65));

var _uniqueId2 = _interopRequireDefault(__webpack_require__(23));

var _difference2 = _interopRequireDefault(__webpack_require__(320));

var _findIndex2 = _interopRequireDefault(__webpack_require__(349));

var _isEmpty2 = _interopRequireDefault(__webpack_require__(19));

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _flatpickr = _interopRequireDefault(__webpack_require__(386));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _queryToJson = _interopRequireDefault(__webpack_require__(71));

var _updateQueryVar = _interopRequireDefault(__webpack_require__(388));

var _events = __webpack_require__(5);

var _i18n = __webpack_require__(7);

var _wpSettings = __webpack_require__(11);

var _productMessage = __webpack_require__(389);

var instances = {
  product: [],
  selections: []
};
var state = {
  isValidOption: false,
  singleVariant: false,
  variantID: '',
  variantMessage: '',
  variantPrice: '',
  sku: '',
  variantImage: {
    url: '',
    template: '',
    zoom: '',
    srcset: ''
  }
};
var el = {
  singleWrapper: tools.getNodes('.bc-product-single', false, document, true)[0]
};
/**
 * @function disableActionButton
 * @description Disable the product form submit button.
 * @param button - Button node of the current form.
 */

var disableActionButton = function disableActionButton(button) {
  button.setAttribute('disabled', 'disabled');
};
/**
 * @function enableActionButton
 * @description Enable the product form submit button.
 * @param button - Button node of the current form.
 */


var enableActionButton = function enableActionButton(button) {
  button.removeAttribute('disabled');
};
/**
 * @function setButtonState
 * @description Toggle the state of the current form button based on chosen options.
 * @param button - Button node of the current form.
 */


var setButtonState = function setButtonState(button) {
  if (state.isValidOption) {
    enableActionButton(button);
    return;
  }

  disableActionButton(button);
};
/**
 * @function setInventory
 * @description Updates inventory/out of stock message and inputs
 */


var setInventory = function setInventory() {
  // update inventory message and quantity only if it's set
  if (state.maxInventory === -1) {
    return;
  }

  var productTitle = tools.getNodes('.bc-product__title', false, document, true)[0];
  var qtyInput = tools.getNodes('.bc-product-form__quantity-input', false, document, true)[0];
  var inventoryContainer = tools.getNodes('.bc-product__inventory', false, document, true)[0];

  if (!inventoryContainer) {
    inventoryContainer = document.createElement('div');
    tools.addClass(inventoryContainer, 'bc-product__inventory');
    productTitle.appendChild(inventoryContainer);
  } // update input max


  if (qtyInput && state.maxInventory !== -1) {
    qtyInput.max = state.maxInventory;
  }
};
/**
 * @function setVariantIDHiddenField
 * @description If this is a valid variant, set its ID to the value of the form's hidden variant_id field.
 * @param formWrapper - Parent form node.
 */


var setVariantIDHiddenField = function setVariantIDHiddenField(formWrapper) {
  var variantField = tools.getNodes('.variant_id', false, formWrapper, true)[0];

  if (!state.variantID) {
    variantField.value = '';
    return;
  }

  variantField.value = state.variantID;
};
/**
 * @function handleAlertMessage
 * @description Add or remove a variant message from the current product form.
 * @param formWrapper - Parent form node.
 */


var handleAlertMessage = function handleAlertMessage() {
  var formWrapper = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var container = tools.getNodes('bc-product-message', false, formWrapper)[0];
  var message = tools.getNodes('.bc-alert', false, container, true)[0];

  if (message) {
    message.parentNode.removeChild(message);
  }

  if (state.variantMessage.length <= 0) {
    return;
  }

  container.insertAdjacentHTML('beforeend', (0, _productMessage.productMessage)(state.variantMessage));
};
/**
 * @function setSelectedVariantPrice
 * @description get the price of the current selected variant ID and replace the price element with it's
 *     formatted_price value.
 * @param wrapper
 */


var setSelectedVariantPrice = function setSelectedVariantPrice() {
  var wrapper = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

  if (!wrapper || !state.variantPrice) {
    return;
  }

  var pricingWrapper = tools.getNodes('bc-cached-product-pricing', false, wrapper)[0];

  if (!pricingWrapper) {
    return;
  }

  var salePriceElement = pricingWrapper.querySelector('.bc-product__original-price');
  var priceElement = pricingWrapper.querySelector('.bc-product__price');

  if (salePriceElement) {
    salePriceElement.parentNode.removeChild(salePriceElement);
  }

  priceElement.textContent = state.variantPrice;
};
/**
 * @function setVariantSKU
 * @description Update the variant SKU after successful variant selection;
 * @param wrapper
 */


var setVariantSKU = function setVariantSKU() {
  var wrapper = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

  if (!wrapper) {
    return;
  }

  var skuWrapper = tools.getNodes('bc-product-sku', false, wrapper)[0];

  if (!skuWrapper) {
    // No SKU template wrapper. No SKU
    return;
  }

  skuWrapper.textContent = state.sku.length > 0 ? state.sku : '';
};
/**
 * @function showVariantImage
 * @description Shows the variant image if one is available from state.variantImage.url.
 * @param swiperInstance
 * @param swiperNavInstance
 */


var showVariantImage = function showVariantImage(swiperInstance, swiperNavInstance) {
  var slide = document.createElement('div');
  tools.addClass(slide, 'swiper-slide');
  tools.addClass(slide, 'bc-product-gallery__image-slide');
  tools.addClass(slide, 'bc-product-gallery__image-variant');
  slide.insertAdjacentHTML('beforeend', state.variantImage.template);
  var image = tools.getNodes('bc-variant-image', false, slide)[0];
  image.setAttribute('src', state.variantImage.url);
  image.setAttribute('alt', state.sku);
  image.setAttribute('data-zoom', state.variantImage.zoom);
  image.setAttribute('srcset', state.variantImage.srcset);
  swiperInstance.appendSlide(slide);
  swiperInstance.update();
  swiperInstance.slideTo(swiperInstance.slides.length);

  if (swiperNavInstance) {
    swiperNavInstance.slideTo(0);
  }

  (0, _delay2.default)(function () {
    (0, _events.trigger)({
      event: 'bigcommerce/init_slide_zoom',
      data: {
        container: slide.querySelector('img')
      },
      native: false
    });
  }, 100);
};
/**
 * @function removeVariantImage
 * @description Hide the active variant image.
 * @param swiperInstance
 * @param swiperNavInstance
 */


var removeVariantImage = function removeVariantImage(swiperInstance, swiperNavInstance) {
  var slideIndex = '';
  (0, _entries.default)(swiperInstance.slides).forEach(function (_ref) {
    var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
        key = _ref2[0],
        slide = _ref2[1];

    if (key === 'length') {
      return;
    }

    if (tools.hasClass(slide, 'bc-product-gallery__image-variant')) {
      slideIndex = key;
    }
  });
  swiperInstance.slideTo(0);

  if (swiperNavInstance) {
    swiperNavInstance.slideTo(0);
  }

  if (!slideIndex) {
    return;
  }

  swiperInstance.removeSlide(slideIndex);
};
/**
 * @function showHideVariantImage
 * @description Hides any active variant image and then displays a new one if it is available.
 * @param e
 * @param wrapper
 */


var showHideVariantImage = function showHideVariantImage(e) {
  var wrapper = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (!e && !wrapper) {
    return;
  }

  var currentWrapper = e ? tools.closest(e.detail.currentGallery.el, '[data-js="bc-product-data-wrapper"]') : wrapper;
  var variantContainer = tools.getNodes('bc-product-variant-image', false, currentWrapper)[0]; // Check that the proper variant image container is present in the DOM.

  if (!variantContainer) {
    return;
  }

  state.variantImage.template = variantContainer.innerHTML;
  var swiperWrapper = tools.getNodes('bc-gallery-container', false, currentWrapper)[0];
  var swiperInstance = swiperWrapper.swiper;
  var swiperNavWrapper = tools.getNodes("[data-id=\"".concat(swiperWrapper.dataset.controls, "\"]"), false, document, true)[0];
  var swiperNavInstance = swiperNavWrapper ? swiperNavWrapper.swiper : null; // hide the image after each variant request.

  removeVariantImage(swiperInstance, swiperNavInstance); // If there is a variant image, show it with a short delay for animation purposes.

  if (state.variantImage.url) {
    showVariantImage(swiperInstance, swiperNavInstance);
  }
};
/**
 * @function handleSelectedVariant
 * @description Takes the current variant and handles status and messaging.
 * @param product
 */


var handleSelectedVariant = function handleSelectedVariant() {
  var product = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  if (!product) {
    return;
  } // Set the price and ID regardless of state.


  state.variantPrice = product.formatted_price;
  state.variantID = product.variant_id;
  state.sku = product.sku;
  state.maxInventory = product.inventory; // Case: product variant has a variant image.

  if (product.image.url.length > 0) {
    state.variantImage.url = product.image.url;
    state.variantImage.zoom = !(0, _isEmpty2.default)(product.zoom.url) ? product.zoom.url : '';
    state.variantImage.srcset = !(0, _isEmpty2.default)(product.image.srcset) ? product.image.srcset : '';
  } // Case: Current variant choice has inventory and is not disabled.


  if ((product.inventory > 0 || product.inventory === -1) && !product.disabled) {
    state.isValidOption = true;
    state.variantMessage = '';
    state.inventoryMessage = "(".concat(product.inventory, " ").concat(_i18n.NLS.inventory.in_stock, ")");
    return;
  } // Case: Current variant is disabled.


  if (product.disabled) {
    state.isValidOption = false;
    state.variantMessage = product.disabled_message;
    return;
  } // Case: Current variant is out of stock.


  if (product.inventory === 0) {
    state.isValidOption = false;
    state.variantMessage = _wpSettings.PRODUCT_MESSAGES.not_available;
    state.inventoryMessage = _i18n.NLS.inventory.out_of_stock;
    return;
  } // Case: We're assuming there are no issues with the current selections and the form action can be used.


  state.isValidOption = true;
  state.variantMessage = '';
};
/**
 * @function parseVariants
 * @description Check to see if the current selections match a variant and handle the variant status within the form.
 * @param variants The current products' full variants object
 * @param choices The current products' variant choices array
 */


var parseVariants = function parseVariants(variants, choices) {
  // Case: This is a product without variants.
  if (variants.length === 1) {
    state.isValidOption = variants[0].inventory !== 0;
    state.singleVariant = true;
    state.variantID = variants[0].variant_id;
    state.sku = variants[0].sku;
    return;
  } // Try to match the selections to the option_ids in a variant.


  var variantIndex = (0, _findIndex2.default)(variants, function (variant) {
    return (0, _isEmpty2.default)((0, _difference2.default)(variant.option_ids, choices));
  }); // Case: The current selection(s) do not match any product variants.

  if (variantIndex === -1) {
    state.isValidOption = false;
    return;
  }

  handleSelectedVariant(variants[variantIndex]);
};
/**
 * @function buildSelectionArray
 * @description On load or on selection change, build an array of variant IDs used to check for product matches.
 * @param selectionArray
 * @param optionsContainer
 */


var buildSelectionArray = function buildSelectionArray(selectionArray, optionsContainer) {
  // Reset the current array.
  selectionArray.length = 0;
  var selection = '';
  tools.getNodes('product-form-option', true, optionsContainer).forEach(function (field) {
    var fieldType = field.dataset.field;

    if (fieldType === 'product-form-option-radio') {
      selection = tools.getNodes('input:checked', false, field, true)[0];
    }

    if (fieldType === 'product-form-option-select') {
      selection = tools.getNodes('select', false, field, true)[0];
    }

    if (!selection) {
      return;
    }

    selectionArray.push(parseInt(selection.value, 10));
  });
};
/**
 * @function setProductURLParameter
 * @description Set and/or updates the variant_id query param in the url.
 */


var setProductURLParameter = function setProductURLParameter() {
  if (!state.variantID || !state.sku || !el.singleWrapper || state.singleVariant) {
    return;
  }

  window.history.replaceState(null, null, (0, _updateQueryVar.default)('variant_id'));
  window.history.replaceState(null, null, (0, _updateQueryVar.default)('sku', state.sku));
};
/**
 * @function validateTextArea
 * @description Listen for key presses and validate that the text meets the textarea's restrictions.
 * @param e
 */


var validateTextArea = function validateTextArea(e) {
  var maxRows = e.delegateTarget.dataset.maxrows;
  var currentValue = e.delegateTarget.value;
  var currentLineCount = currentValue.split(/\r*\n/).length;

  if (e.which === 13 && currentLineCount >= maxRows) {
    e.preventDefault();
    return false;
  }

  return true;
};
/**
 * @function handleModifierFields
 * @description If there are fields present which allow manual user input (i.e. text, date, textarea A.K.A. Modifiers),
 *     allow for additional validation beyond HTML5 and overrides.
 * @param options
 */


var handleModifierFields = function handleModifierFields(options) {
  (0, _delegate.default)(options, '.bc-product-form__control.bc-product-form__control--textarea textarea', 'keydown', validateTextArea);
  tools.getNodes('.bc-product-form__control.bc-product-form__control--date', true, options, true).forEach(function (field) {
    var dateField = tools.getNodes('input[type="date"]', false, field, true)[0];
    var defaultDate = dateField.value;
    var minDate = dateField.getAttribute('min');
    var maxDate = dateField.getAttribute('max');
    var fpOptions = {
      allowInput: false,
      altInput: true,
      altFormat: 'F j, Y',
      defaultDate: defaultDate,
      minDate: minDate,
      maxDate: maxDate,
      static: true
    };
    (0, _flatpickr.default)(dateField, fpOptions);
  });
};
/**
 * @function handleSelections
 * @description On load or on selection change, determine which product form we are in and run all main functions.
 * @param e - a delegate event from a click.
 * @param node - a specific DOM node to use for options.
 */


var handleSelections = function handleSelections(e) {
  var node = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var optionsContainer = e ? tools.closest(e.delegateTarget, '[data-js="product-options"]') : node;

  if (!optionsContainer) {
    return;
  }

  state.variantMessage = '';
  state.inventoryMessage = '';
  state.maxInventory = '';
  state.variantID = '';
  state.sku = '';
  state.variantPrice = '';
  state.singleVariant = false;
  state.variantImage.url = '';
  state.variantImage.zoom = '';
  var formWrapper = tools.closest(optionsContainer, '.bc-product-form');
  var productID = optionsContainer.dataset.productId;
  var submitButton = tools.getNodes('.bc-btn--form-submit', false, formWrapper, true)[0];
  var metaWrapper = tools.closest(optionsContainer, '[data-js="bc-product-data-wrapper"]');

  if (!metaWrapper) {
    metaWrapper = tools.closest(optionsContainer, '[data-wrapper="bc-product-data-wrapper"]');
  }

  buildSelectionArray(instances.selections[productID], optionsContainer);
  parseVariants(instances.product[productID], instances.selections[productID]);
  setProductURLParameter();
  setVariantIDHiddenField(formWrapper);
  setSelectedVariantPrice(metaWrapper);
  setVariantSKU(metaWrapper);
  setInventory();
  showHideVariantImage(null, metaWrapper);
  setButtonState(submitButton);
  handleAlertMessage(formWrapper);
};
/**
 * @function handleOptionClicks
 * @description Click/change event listener for form fields on each product. Runs our main handleSelections function on
 *     the event.
 * @param options - the current .initialized form options node.
 */


var handleOptionClicks = function handleOptionClicks() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  tools.getNodes('product-form-option', true, options).forEach(function (option) {
    var fieldType = option.dataset.field;

    if (fieldType === 'product-form-option-radio') {
      (0, _delegate.default)(option, 'input[type=radio]', 'click', handleSelections);
    }

    if (fieldType === 'product-form-option-select') {
      (0, _delegate.default)(option, 'select', 'change', handleSelections);
    }
  });
};
/**
 * @function handleProductQueryParam
 * @description Creates an added layer of variant checking to ensure that a URL with a variant_id param is set properly.
 *
 * @param options
 */


var handleProductQueryParam = function handleProductQueryParam() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  // Assumes this is the PDP single page.
  var variantID = (0, _queryToJson.default)().variant_id;
  var sku = (0, _queryToJson.default)().sku;

  if (!variantID && !sku || !el.singleWrapper) {
    handleSelections(null, options);
    return;
  }

  var productOptions = tools.getNodes('product-options', false, el.singleWrapper)[0];
  var formWrapper = el.singleWrapper.querySelector('.bc-product-form');
  tools.addClass(formWrapper, 'bc-product__is-setting-options');
  handleSelections(null, productOptions);
  (0, _delay2.default)(function () {
    return tools.removeClass(formWrapper, 'bc-product__is-setting-options');
  }, 500);
};
/**
 * @function initializeUniqueFieldIDs
 * @description Add a UID to each field and label set for option in order to avoid collisions with form control.
 * @param options
 * @param productVariantsID
 */


var initializeUniqueFieldIDs = function initializeUniqueFieldIDs() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var productVariantsID = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  tools.getNodes('product-form-option', true, options).forEach(function (option) {
    var fieldType = option.dataset.field; // Set a UID for all labels

    tools.getNodes('label', true, option, true).forEach(function (label) {
      var labelFor = "".concat(label.getAttribute('for'), "[").concat(productVariantsID, "]");
      label.setAttribute('for', labelFor);
    }); // Set the same UID for each radio input.

    if (fieldType === 'product-form-option-radio') {
      tools.getNodes('input[type=radio]', true, option, true).forEach(function (radio) {
        var fieldID = "".concat(radio.getAttribute('id'), "[").concat(productVariantsID, "]");
        radio.setAttribute('id', fieldID);
      });
    } // Set the same UID for each select field.


    if (fieldType === 'product-form-option-select') {
      tools.getNodes('select', true, option, true).forEach(function (select) {
        var fieldID = "".concat(select.getAttribute('id'), "[").concat(productVariantsID, "]");
        select.setAttribute('id', fieldID);
      });
    }
  });
};
/**
 * @function initOptionsPickers
 * @description Traverse the dom and find forms that have not been initialized. Add a unique ID and setup instanced
 *     containers for handling product form data.
 */


var initOptionsPickers = function initOptionsPickers() {
  var variantsObj;
  tools.getNodes('.bc-product-form__options:not(.initialized)', true, document, true).forEach(function (options) {
    var productVariantsID = (0, _uniqueId2.default)('product-');
    var variants = tools.getNodes('product-variants-object', false, options)[0]; // Setup this products' variants obj.

    variantsObj = JSON.parse(variants.dataset.variants); // Assign the obj to its local instanced product object.

    instances.product[productVariantsID] = variantsObj; // Setup a blank instanced selections array for the current product.

    instances.selections[productVariantsID] = []; // "Initialize" the current form options.

    initializeUniqueFieldIDs(options, productVariantsID);
    tools.addClass(options, 'initialized'); // Add the unique ID to the current options node for easily selecting the form parent associated with this product.

    options.setAttribute('data-product-id', productVariantsID); // On initialization, setup our form.

    handleOptionClicks(options);
    handleModifierFields(options);
    handleProductQueryParam(options);
  });
};

var init = function init(container) {
  if (!container) {
    return;
  }

  initOptionsPickers();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 164 */
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__(103);
var ITERATOR = __webpack_require__(6)('iterator');
var Iterators = __webpack_require__(31);
module.exports = __webpack_require__(3).getIteratorMethod = function (it) {
  if (it != undefined) return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};


/***/ }),
/* 165 */
/***/ (function(module, exports, __webpack_require__) {

var MapCache = __webpack_require__(104),
    setCacheAdd = __webpack_require__(339),
    setCacheHas = __webpack_require__(340);

/**
 *
 * Creates an array cache object to store unique values.
 *
 * @private
 * @constructor
 * @param {Array} [values] The values to cache.
 */
function SetCache(values) {
  var index = -1,
      length = values == null ? 0 : values.length;

  this.__data__ = new MapCache;
  while (++index < length) {
    this.add(values[index]);
  }
}

// Add methods to `SetCache`.
SetCache.prototype.add = SetCache.prototype.push = setCacheAdd;
SetCache.prototype.has = setCacheHas;

module.exports = SetCache;


/***/ }),
/* 166 */
/***/ (function(module, exports) {

/**
 * The base implementation of `_.findIndex` and `_.findLastIndex` without
 * support for iteratee shorthands.
 *
 * @private
 * @param {Array} array The array to inspect.
 * @param {Function} predicate The function invoked per iteration.
 * @param {number} fromIndex The index to search from.
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {number} Returns the index of the matched value, else `-1`.
 */
function baseFindIndex(array, predicate, fromIndex, fromRight) {
  var length = array.length,
      index = fromIndex + (fromRight ? 1 : -1);

  while ((fromRight ? index-- : ++index < length)) {
    if (predicate(array[index], index, array)) {
      return index;
    }
  }
  return -1;
}

module.exports = baseFindIndex;


/***/ }),
/* 167 */
/***/ (function(module, exports) {

/**
 * Checks if a `cache` value for `key` exists.
 *
 * @private
 * @param {Object} cache The cache to query.
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */
function cacheHas(cache, key) {
  return cache.has(key);
}

module.exports = cacheHas;


/***/ }),
/* 168 */
/***/ (function(module, exports) {

/**
 * Appends the elements of `values` to `array`.
 *
 * @private
 * @param {Array} array The array to modify.
 * @param {Array} values The values to append.
 * @returns {Array} Returns `array`.
 */
function arrayPush(array, values) {
  var index = -1,
      length = values.length,
      offset = array.length;

  while (++index < length) {
    array[offset + index] = values[index];
  }
  return array;
}

module.exports = arrayPush;


/***/ }),
/* 169 */
/***/ (function(module, exports, __webpack_require__) {

var baseMatches = __webpack_require__(350),
    baseMatchesProperty = __webpack_require__(373),
    identity = __webpack_require__(76),
    isArray = __webpack_require__(12),
    property = __webpack_require__(381);

/**
 * The base implementation of `_.iteratee`.
 *
 * @private
 * @param {*} [value=_.identity] The value to convert to an iteratee.
 * @returns {Function} Returns the iteratee.
 */
function baseIteratee(value) {
  // Don't store the `typeof` result in a variable to avoid a JIT bug in Safari 9.
  // See https://bugs.webkit.org/show_bug.cgi?id=156034 for more details.
  if (typeof value == 'function') {
    return value;
  }
  if (value == null) {
    return identity;
  }
  if (typeof value == 'object') {
    return isArray(value)
      ? baseMatchesProperty(value[0], value[1])
      : baseMatches(value);
  }
  return property(value);
}

module.exports = baseIteratee;


/***/ }),
/* 170 */
/***/ (function(module, exports, __webpack_require__) {

var ListCache = __webpack_require__(67),
    stackClear = __webpack_require__(352),
    stackDelete = __webpack_require__(353),
    stackGet = __webpack_require__(354),
    stackHas = __webpack_require__(355),
    stackSet = __webpack_require__(356);

/**
 * Creates a stack cache object to store key-value pairs.
 *
 * @private
 * @constructor
 * @param {Array} [entries] The key-value pairs to cache.
 */
function Stack(entries) {
  var data = this.__data__ = new ListCache(entries);
  this.size = data.size;
}

// Add methods to `Stack`.
Stack.prototype.clear = stackClear;
Stack.prototype['delete'] = stackDelete;
Stack.prototype.get = stackGet;
Stack.prototype.has = stackHas;
Stack.prototype.set = stackSet;

module.exports = Stack;


/***/ }),
/* 171 */
/***/ (function(module, exports, __webpack_require__) {

var baseIsEqualDeep = __webpack_require__(357),
    isObjectLike = __webpack_require__(28);

/**
 * The base implementation of `_.isEqual` which supports partial comparisons
 * and tracks traversed objects.
 *
 * @private
 * @param {*} value The value to compare.
 * @param {*} other The other value to compare.
 * @param {boolean} bitmask The bitmask flags.
 *  1 - Unordered comparison
 *  2 - Partial comparison
 * @param {Function} [customizer] The function to customize comparisons.
 * @param {Object} [stack] Tracks traversed `value` and `other` objects.
 * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
 */
function baseIsEqual(value, other, bitmask, customizer, stack) {
  if (value === other) {
    return true;
  }
  if (value == null || other == null || (!isObjectLike(value) && !isObjectLike(other))) {
    return value !== value && other !== other;
  }
  return baseIsEqualDeep(value, other, bitmask, customizer, baseIsEqual, stack);
}

module.exports = baseIsEqual;


/***/ }),
/* 172 */
/***/ (function(module, exports, __webpack_require__) {

var SetCache = __webpack_require__(165),
    arraySome = __webpack_require__(173),
    cacheHas = __webpack_require__(167);

/** Used to compose bitmasks for value comparisons. */
var COMPARE_PARTIAL_FLAG = 1,
    COMPARE_UNORDERED_FLAG = 2;

/**
 * A specialized version of `baseIsEqualDeep` for arrays with support for
 * partial deep comparisons.
 *
 * @private
 * @param {Array} array The array to compare.
 * @param {Array} other The other array to compare.
 * @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
 * @param {Function} customizer The function to customize comparisons.
 * @param {Function} equalFunc The function to determine equivalents of values.
 * @param {Object} stack Tracks traversed `array` and `other` objects.
 * @returns {boolean} Returns `true` if the arrays are equivalent, else `false`.
 */
function equalArrays(array, other, bitmask, customizer, equalFunc, stack) {
  var isPartial = bitmask & COMPARE_PARTIAL_FLAG,
      arrLength = array.length,
      othLength = other.length;

  if (arrLength != othLength && !(isPartial && othLength > arrLength)) {
    return false;
  }
  // Assume cyclic values are equal.
  var stacked = stack.get(array);
  if (stacked && stack.get(other)) {
    return stacked == other;
  }
  var index = -1,
      result = true,
      seen = (bitmask & COMPARE_UNORDERED_FLAG) ? new SetCache : undefined;

  stack.set(array, other);
  stack.set(other, array);

  // Ignore non-index properties.
  while (++index < arrLength) {
    var arrValue = array[index],
        othValue = other[index];

    if (customizer) {
      var compared = isPartial
        ? customizer(othValue, arrValue, index, other, array, stack)
        : customizer(arrValue, othValue, index, array, other, stack);
    }
    if (compared !== undefined) {
      if (compared) {
        continue;
      }
      result = false;
      break;
    }
    // Recursively compare arrays (susceptible to call stack limits).
    if (seen) {
      if (!arraySome(other, function(othValue, othIndex) {
            if (!cacheHas(seen, othIndex) &&
                (arrValue === othValue || equalFunc(arrValue, othValue, bitmask, customizer, stack))) {
              return seen.push(othIndex);
            }
          })) {
        result = false;
        break;
      }
    } else if (!(
          arrValue === othValue ||
            equalFunc(arrValue, othValue, bitmask, customizer, stack)
        )) {
      result = false;
      break;
    }
  }
  stack['delete'](array);
  stack['delete'](other);
  return result;
}

module.exports = equalArrays;


/***/ }),
/* 173 */
/***/ (function(module, exports) {

/**
 * A specialized version of `_.some` for arrays without support for iteratee
 * shorthands.
 *
 * @private
 * @param {Array} [array] The array to iterate over.
 * @param {Function} predicate The function invoked per iteration.
 * @returns {boolean} Returns `true` if any element passes the predicate check,
 *  else `false`.
 */
function arraySome(array, predicate) {
  var index = -1,
      length = array == null ? 0 : array.length;

  while (++index < length) {
    if (predicate(array[index], index, array)) {
      return true;
    }
  }
  return false;
}

module.exports = arraySome;


/***/ }),
/* 174 */
/***/ (function(module, exports, __webpack_require__) {

var DataView = __webpack_require__(368),
    Map = __webpack_require__(105),
    Promise = __webpack_require__(369),
    Set = __webpack_require__(370),
    WeakMap = __webpack_require__(371),
    baseGetTag = __webpack_require__(35),
    toSource = __webpack_require__(118);

/** `Object#toString` result references. */
var mapTag = '[object Map]',
    objectTag = '[object Object]',
    promiseTag = '[object Promise]',
    setTag = '[object Set]',
    weakMapTag = '[object WeakMap]';

var dataViewTag = '[object DataView]';

/** Used to detect maps, sets, and weakmaps. */
var dataViewCtorString = toSource(DataView),
    mapCtorString = toSource(Map),
    promiseCtorString = toSource(Promise),
    setCtorString = toSource(Set),
    weakMapCtorString = toSource(WeakMap);

/**
 * Gets the `toStringTag` of `value`.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the `toStringTag`.
 */
var getTag = baseGetTag;

// Fallback for data views, maps, sets, and weak maps in IE 11 and promises in Node.js < 6.
if ((DataView && getTag(new DataView(new ArrayBuffer(1))) != dataViewTag) ||
    (Map && getTag(new Map) != mapTag) ||
    (Promise && getTag(Promise.resolve()) != promiseTag) ||
    (Set && getTag(new Set) != setTag) ||
    (WeakMap && getTag(new WeakMap) != weakMapTag)) {
  getTag = function(value) {
    var result = baseGetTag(value),
        Ctor = result == objectTag ? value.constructor : undefined,
        ctorString = Ctor ? toSource(Ctor) : '';

    if (ctorString) {
      switch (ctorString) {
        case dataViewCtorString: return dataViewTag;
        case mapCtorString: return mapTag;
        case promiseCtorString: return promiseTag;
        case setCtorString: return setTag;
        case weakMapCtorString: return weakMapTag;
      }
    }
    return result;
  };
}

module.exports = getTag;


/***/ }),
/* 175 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(27);

/**
 * Checks if `value` is suitable for strict equality comparisons, i.e. `===`.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` if suitable for strict
 *  equality comparisons, else `false`.
 */
function isStrictComparable(value) {
  return value === value && !isObject(value);
}

module.exports = isStrictComparable;


/***/ }),
/* 176 */
/***/ (function(module, exports) {

/**
 * A specialized version of `matchesProperty` for source values suitable
 * for strict equality comparisons, i.e. `===`.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @param {*} srcValue The value to match.
 * @returns {Function} Returns the new spec function.
 */
function matchesStrictComparable(key, srcValue) {
  return function(object) {
    if (object == null) {
      return false;
    }
    return object[key] === srcValue &&
      (srcValue !== undefined || (key in Object(object)));
  };
}

module.exports = matchesStrictComparable;


/***/ }),
/* 177 */
/***/ (function(module, exports, __webpack_require__) {

var castPath = __webpack_require__(178),
    toKey = __webpack_require__(70);

/**
 * The base implementation of `_.get` without support for default values.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {Array|string} path The path of the property to get.
 * @returns {*} Returns the resolved value.
 */
function baseGet(object, path) {
  path = castPath(path, object);

  var index = 0,
      length = path.length;

  while (object != null && index < length) {
    object = object[toKey(path[index++])];
  }
  return (index && index == length) ? object : undefined;
}

module.exports = baseGet;


/***/ }),
/* 178 */
/***/ (function(module, exports, __webpack_require__) {

var isArray = __webpack_require__(12),
    isKey = __webpack_require__(106),
    stringToPath = __webpack_require__(375),
    toString = __webpack_require__(135);

/**
 * Casts `value` to a path array if it's not one.
 *
 * @private
 * @param {*} value The value to inspect.
 * @param {Object} [object] The object to query keys on.
 * @returns {Array} Returns the cast property path array.
 */
function castPath(value, object) {
  if (isArray(value)) {
    return value;
  }
  return isKey(value, object) ? [value] : stringToPath(toString(value));
}

module.exports = castPath;


/***/ }),
/* 179 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(387);

/***/ }),
/* 180 */
/***/ (function(module, exports, __webpack_require__) {

// @@search logic
__webpack_require__(52)('search', 1, function (defined, SEARCH, $search) {
  // 21.1.3.15 String.prototype.search(regexp)
  return [function search(regexp) {
    'use strict';
    var O = defined(this);
    var fn = regexp == undefined ? undefined : regexp[SEARCH];
    return fn !== undefined ? fn.call(regexp, O) : new RegExp(regexp)[SEARCH](String(O));
  }, $search];
});


/***/ }),
/* 181 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Check if `obj` is an object.
 *
 * @param {Object} obj
 * @return {Boolean}
 * @api private
 */

function isObject(obj) {
  return null !== obj && 'object' === typeof obj;
}

module.exports = isObject;


/***/ }),
/* 182 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;
var _default = {
  isFetching: false,
  isGutenberg: false,
  currentEditor: '',
  productHTML: '',
  wpAPIDisplaySettings: {
    order: '',
    orderby: '',
    per_page: ''
  },
  wpAPIQueryObj: {
    bigcommerce_flag: [],
    bigcommerce_brand: [],
    bigcommerce_category: [],
    bigcommerce_channel: '',
    recent: [],
    search: []
  },
  selectedProducts: {
    bc_id: []
  },
  insertCallback: false
};
exports.default = _default;

/***/ }),
/* 183 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.paginationError = void 0;

/**
 * @template Inline error message templates for plugin errors.
 * @param message
 * @returns {string}
 */
var paginationError = function paginationError(message) {
  return "\n\t\t<span class=\"bc-alert bc-alert--error bc-pagination__error-message\">".concat(message, "</span>\n\t");
};

exports.paginationError = paginationError;

/***/ }),
/* 184 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(418);


/***/ }),
/* 185 */
/***/ (function(module, exports, __webpack_require__) {

var _Promise = __webpack_require__(419);

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new _Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function step(key, arg) {
        try {
          var info = gen[key](arg);
          var value = info.value;
        } catch (error) {
          reject(error);
          return;
        }

        if (info.done) {
          resolve(value);
        } else {
          _Promise.resolve(value).then(_next, _throw);
        }
      }

      function _next(value) {
        step("next", value);
      }

      function _throw(err) {
        step("throw", err);
      }

      _next();
    });
  };
}

module.exports = _asyncToGenerator;

/***/ }),
/* 186 */
/***/ (function(module, exports, __webpack_require__) {

// 7.3.20 SpeciesConstructor(O, defaultConstructor)
var anObject = __webpack_require__(14);
var aFunction = __webpack_require__(56);
var SPECIES = __webpack_require__(6)('species');
module.exports = function (O, D) {
  var C = anObject(O).constructor;
  var S;
  return C === undefined || (S = anObject(C)[SPECIES]) == undefined ? D : aFunction(S);
};


/***/ }),
/* 187 */
/***/ (function(module, exports, __webpack_require__) {

var ctx = __webpack_require__(44);
var invoke = __webpack_require__(426);
var html = __webpack_require__(153);
var cel = __webpack_require__(87);
var global = __webpack_require__(8);
var process = global.process;
var setTask = global.setImmediate;
var clearTask = global.clearImmediate;
var MessageChannel = global.MessageChannel;
var Dispatch = global.Dispatch;
var counter = 0;
var queue = {};
var ONREADYSTATECHANGE = 'onreadystatechange';
var defer, channel, port;
var run = function () {
  var id = +this;
  // eslint-disable-next-line no-prototype-builtins
  if (queue.hasOwnProperty(id)) {
    var fn = queue[id];
    delete queue[id];
    fn();
  }
};
var listener = function (event) {
  run.call(event.data);
};
// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
if (!setTask || !clearTask) {
  setTask = function setImmediate(fn) {
    var args = [];
    var i = 1;
    while (arguments.length > i) args.push(arguments[i++]);
    queue[++counter] = function () {
      // eslint-disable-next-line no-new-func
      invoke(typeof fn == 'function' ? fn : Function(fn), args);
    };
    defer(counter);
    return counter;
  };
  clearTask = function clearImmediate(id) {
    delete queue[id];
  };
  // Node.js 0.8-
  if (__webpack_require__(42)(process) == 'process') {
    defer = function (id) {
      process.nextTick(ctx(run, id, 1));
    };
  // Sphere (JS game engine) Dispatch API
  } else if (Dispatch && Dispatch.now) {
    defer = function (id) {
      Dispatch.now(ctx(run, id, 1));
    };
  // Browsers with MessageChannel, includes WebWorkers
  } else if (MessageChannel) {
    channel = new MessageChannel();
    port = channel.port2;
    channel.port1.onmessage = listener;
    defer = ctx(port.postMessage, port, 1);
  // Browsers with postMessage, skip WebWorkers
  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
  } else if (global.addEventListener && typeof postMessage == 'function' && !global.importScripts) {
    defer = function (id) {
      global.postMessage(id + '', '*');
    };
    global.addEventListener('message', listener, false);
  // IE8-
  } else if (ONREADYSTATECHANGE in cel('script')) {
    defer = function (id) {
      html.appendChild(cel('script'))[ONREADYSTATECHANGE] = function () {
        html.removeChild(this);
        run.call(id);
      };
    };
  // Rest old browsers
  } else {
    defer = function (id) {
      setTimeout(ctx(run, id, 1), 0);
    };
  }
}
module.exports = {
  set: setTask,
  clear: clearTask
};


/***/ }),
/* 188 */
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return { e: false, v: exec() };
  } catch (e) {
    return { e: true, v: e };
  }
};


/***/ }),
/* 189 */
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__(14);
var isObject = __webpack_require__(16);
var newPromiseCapability = __webpack_require__(111);

module.exports = function (C, x) {
  anObject(C);
  if (isObject(x) && x.constructor === C) return x;
  var promiseCapability = newPromiseCapability.f(C);
  var resolve = promiseCapability.resolve;
  resolve(x);
  return promiseCapability.promise;
};


/***/ }),
/* 190 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _extends2 = _interopRequireDefault(__webpack_require__(158));

/**
 * @function scrollTo
 * @since 1.0
 * @desc scrollTo allows equalized or duration based scrolling of the body to a supplied $target with options.
 */
var scrollTo = function scrollTo(opts) {
  var options = (0, _extends2.default)({
    auto: false,
    auto_coefficent: 2.5,
    afterScroll: function afterScroll() {},
    duration: 1000,
    easing: 'linear',
    offset: 0,
    $target: jQuery()
  }, opts);
  var position;
  var htmlPosition;

  if (options.$target.length) {
    position = options.$target.offset().top + options.offset;

    if (options.auto) {
      htmlPosition = jQuery('html').scrollTop();

      if (position > htmlPosition) {
        options.duration = (position - htmlPosition) / options.auto_coefficent;
      } else {
        options.duration = (htmlPosition - position) / options.auto_coefficent;
      }
    }

    jQuery('html, body').animate({
      scrollTop: position
    }, options.duration, options.easing, options.after_scroll);
  }
};

var _default = scrollTo;
exports.default = _default;

/***/ }),
/* 191 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

__webpack_require__(45);

var _delegate = _interopRequireDefault(__webpack_require__(4));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _wpSettings = __webpack_require__(11);

var _dynamicStateSelect = __webpack_require__(438);

var _dynamicStateInput = __webpack_require__(439);

/**
 * @module Country/state component for dynamic field values
 */
var el = {
  container: tools.getNodes('bc-dynamic-fields')[0]
};
var countryState = {
  countryWithStates: false
};
/**
 * @function swapStateProvinceSelectTextField
 * @description If a country that has states is chosen, hide the text field and show a new select field.
 * @param stateControlContainer
 * @param countryHasStates
 * @param countryStates
 */

var swapStateProvinceSelectTextField = function swapStateProvinceSelectTextField() {
  var stateControlContainer = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var countryHasStates = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var countryStates = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
  var stateControl = tools.getNodes('bc-dynamic-state-control', false, stateControlContainer)[0];
  var newStateField;
  var fieldValue;

  if (countryState.currentCountry === countryState.initialCountryValue) {
    fieldValue = countryState.initialStateValue;
  }

  if (countryHasStates) {
    newStateField = (0, _dynamicStateSelect.stateSelectField)(countryStates, countryState.stateFieldId, countryState.stateFieldName, countryState.initialStateValue);
  } else {
    newStateField = (0, _dynamicStateInput.stateInputField)(countryState.stateFieldId, countryState.stateFieldName, fieldValue);
  }

  stateControlContainer.removeChild(stateControl);
  stateControlContainer.insertAdjacentHTML('beforeend', newStateField);
};
/**
 * @function parseCountryObject
 * @description traverse the selected country object and determine if states are available. Process handlers if this is true.
 * @param selectedCountry
 * @param stateControlContainer
 */


var parseCountryObject = function parseCountryObject() {
  var selectedCountry = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var stateControlContainer = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (!selectedCountry && !stateControlContainer) {
    return;
  }

  var countryObj = _wpSettings.COUNTRIES_OBJ.filter(function (item) {
    return item.country === selectedCountry;
  });

  var countryStates = countryObj[0].states;

  if (!countryStates) {
    countryState.countryWithStates = false;
  } else {
    countryState.countryWithStates = true;
  }

  swapStateProvinceSelectTextField(stateControlContainer, countryState.countryWithStates, countryStates);
};
/**
 * @function storeInitialFieldStates
 * @description stores current state field type and value in countryState object
 */


var storeInitialFieldStates = function storeInitialFieldStates() {
  var countryControl = tools.getNodes('bc-dynamic-country-select')[0];
  var stateControl = tools.getNodes('bc-dynamic-state-control')[0];
  var stateFieldType = stateControl.tagName.toLowerCase();

  if (stateFieldType === 'select') {
    countryState.initialStateValue = stateControl.options[stateControl.selectedIndex].value;
  } else {
    countryState.initialStateValue = stateControl.value;
  }

  countryState.stateFieldId = stateControl.id;
  countryState.stateFieldName = stateControl.name;
  countryState.initialCountryValue = countryControl.options[countryControl.selectedIndex].value;
};
/**
 * @function handleCountriesSelection
 * @description When a country is selected, setup the process for determining the form changes.
 * @param e
 */


var handleCountriesSelection = function handleCountriesSelection(e) {
  var selectedCountry = e.delegateTarget.value;
  var form = tools.closest(e.delegateTarget, '.bc-form');
  var stateControlContainer = tools.getNodes('bc-dynamic-state', false, form, false)[0];
  countryState.currentCountry = e.target.options[e.target.selectedIndex].value;
  parseCountryObject(selectedCountry, stateControlContainer);
};
/**
 * @function cacheElements
 * @description check for el.container rendered after page load
 */


var cacheElements = function cacheElements() {
  el.container = tools.getNodes('bc-dynamic-fields')[0];
};
/**
 * @function bindEvents
 * @description bind all event handlers and listeners for addresses.
 */


var bindEvents = function bindEvents() {
  (0, _delegate.default)(el.container, '[data-js="bc-dynamic-country-select"]', 'change', handleCountriesSelection);
};

var init = function init() {
  cacheElements();

  if (!el.container) {
    return;
  }

  bindEvents();
  storeInitialFieldStates();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 192 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

var _ready = _interopRequireDefault(__webpack_require__(193));

(0, _ready.default)();

/***/ }),
/* 193 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _debounce2 = _interopRequireDefault(__webpack_require__(194));

var _events = __webpack_require__(5);

var _applyBrowserClasses = _interopRequireDefault(__webpack_require__(219));

var _resize = _interopRequireDefault(__webpack_require__(227));

var _plugins = _interopRequireDefault(__webpack_require__(229));

var _viewportDims = _interopRequireDefault(__webpack_require__(127));

var _index = _interopRequireDefault(__webpack_require__(230));

var _index2 = _interopRequireDefault(__webpack_require__(310));

var _index3 = _interopRequireDefault(__webpack_require__(396));

var _index4 = _interopRequireDefault(__webpack_require__(398));

var _index5 = _interopRequireDefault(__webpack_require__(416));

var _index6 = _interopRequireDefault(__webpack_require__(434));

var _index7 = _interopRequireDefault(__webpack_require__(436));

var _index8 = _interopRequireDefault(__webpack_require__(444));

var _index9 = _interopRequireDefault(__webpack_require__(447));

var _index10 = _interopRequireDefault(__webpack_require__(453));

var _index11 = _interopRequireDefault(__webpack_require__(456));

// you MUST do this in every module you use lodash in.
// A custom bundle of only the lodash you use will be built by babel.

/**
 * @function bindEvents
 * @description Bind global event listeners here,
 */
var bindEvents = function bindEvents() {
  (0, _events.on)(window, 'resize', (0, _debounce2.default)(_resize.default, 200, false));
};
/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */


var init = function init() {
  // apply browser classes
  (0, _applyBrowserClasses.default)(); // init external plugins

  (0, _plugins.default)(); // set initial states

  (0, _viewportDims.default)(); // initialize global events

  bindEvents();
  (0, _index3.default)(); // initialize the main scripts

  (0, _index2.default)();
  (0, _index.default)();
  (0, _index4.default)();
  (0, _index5.default)();
  (0, _index6.default)();
  (0, _index7.default)();
  (0, _index8.default)();
  (0, _index9.default)();
  (0, _index11.default)();
  (0, _index10.default)();
  console.info('BigCommerce FE: Initialized all javascript that targeted document ready.');
};
/**
 * @function domReady
 * @description Export our dom ready enabled init.
 */


var domReady = function domReady() {
  (0, _events.ready)(init);
};

var _default = domReady;
exports.default = _default;

/***/ }),
/* 194 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(27),
    now = __webpack_require__(195),
    toNumber = __webpack_require__(74);

/** Error message constants. */
var FUNC_ERROR_TEXT = 'Expected a function';

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeMax = Math.max,
    nativeMin = Math.min;

/**
 * Creates a debounced function that delays invoking `func` until after `wait`
 * milliseconds have elapsed since the last time the debounced function was
 * invoked. The debounced function comes with a `cancel` method to cancel
 * delayed `func` invocations and a `flush` method to immediately invoke them.
 * Provide `options` to indicate whether `func` should be invoked on the
 * leading and/or trailing edge of the `wait` timeout. The `func` is invoked
 * with the last arguments provided to the debounced function. Subsequent
 * calls to the debounced function return the result of the last `func`
 * invocation.
 *
 * **Note:** If `leading` and `trailing` options are `true`, `func` is
 * invoked on the trailing edge of the timeout only if the debounced function
 * is invoked more than once during the `wait` timeout.
 *
 * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
 * until to the next tick, similar to `setTimeout` with a timeout of `0`.
 *
 * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
 * for details over the differences between `_.debounce` and `_.throttle`.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Function
 * @param {Function} func The function to debounce.
 * @param {number} [wait=0] The number of milliseconds to delay.
 * @param {Object} [options={}] The options object.
 * @param {boolean} [options.leading=false]
 *  Specify invoking on the leading edge of the timeout.
 * @param {number} [options.maxWait]
 *  The maximum time `func` is allowed to be delayed before it's invoked.
 * @param {boolean} [options.trailing=true]
 *  Specify invoking on the trailing edge of the timeout.
 * @returns {Function} Returns the new debounced function.
 * @example
 *
 * // Avoid costly calculations while the window size is in flux.
 * jQuery(window).on('resize', _.debounce(calculateLayout, 150));
 *
 * // Invoke `sendMail` when clicked, debouncing subsequent calls.
 * jQuery(element).on('click', _.debounce(sendMail, 300, {
 *   'leading': true,
 *   'trailing': false
 * }));
 *
 * // Ensure `batchLog` is invoked once after 1 second of debounced calls.
 * var debounced = _.debounce(batchLog, 250, { 'maxWait': 1000 });
 * var source = new EventSource('/stream');
 * jQuery(source).on('message', debounced);
 *
 * // Cancel the trailing debounced invocation.
 * jQuery(window).on('popstate', debounced.cancel);
 */
function debounce(func, wait, options) {
  var lastArgs,
      lastThis,
      maxWait,
      result,
      timerId,
      lastCallTime,
      lastInvokeTime = 0,
      leading = false,
      maxing = false,
      trailing = true;

  if (typeof func != 'function') {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  wait = toNumber(wait) || 0;
  if (isObject(options)) {
    leading = !!options.leading;
    maxing = 'maxWait' in options;
    maxWait = maxing ? nativeMax(toNumber(options.maxWait) || 0, wait) : maxWait;
    trailing = 'trailing' in options ? !!options.trailing : trailing;
  }

  function invokeFunc(time) {
    var args = lastArgs,
        thisArg = lastThis;

    lastArgs = lastThis = undefined;
    lastInvokeTime = time;
    result = func.apply(thisArg, args);
    return result;
  }

  function leadingEdge(time) {
    // Reset any `maxWait` timer.
    lastInvokeTime = time;
    // Start the timer for the trailing edge.
    timerId = setTimeout(timerExpired, wait);
    // Invoke the leading edge.
    return leading ? invokeFunc(time) : result;
  }

  function remainingWait(time) {
    var timeSinceLastCall = time - lastCallTime,
        timeSinceLastInvoke = time - lastInvokeTime,
        timeWaiting = wait - timeSinceLastCall;

    return maxing
      ? nativeMin(timeWaiting, maxWait - timeSinceLastInvoke)
      : timeWaiting;
  }

  function shouldInvoke(time) {
    var timeSinceLastCall = time - lastCallTime,
        timeSinceLastInvoke = time - lastInvokeTime;

    // Either this is the first call, activity has stopped and we're at the
    // trailing edge, the system time has gone backwards and we're treating
    // it as the trailing edge, or we've hit the `maxWait` limit.
    return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
      (timeSinceLastCall < 0) || (maxing && timeSinceLastInvoke >= maxWait));
  }

  function timerExpired() {
    var time = now();
    if (shouldInvoke(time)) {
      return trailingEdge(time);
    }
    // Restart the timer.
    timerId = setTimeout(timerExpired, remainingWait(time));
  }

  function trailingEdge(time) {
    timerId = undefined;

    // Only invoke if we have `lastArgs` which means `func` has been
    // debounced at least once.
    if (trailing && lastArgs) {
      return invokeFunc(time);
    }
    lastArgs = lastThis = undefined;
    return result;
  }

  function cancel() {
    if (timerId !== undefined) {
      clearTimeout(timerId);
    }
    lastInvokeTime = 0;
    lastArgs = lastCallTime = lastThis = timerId = undefined;
  }

  function flush() {
    return timerId === undefined ? result : trailingEdge(now());
  }

  function debounced() {
    var time = now(),
        isInvoking = shouldInvoke(time);

    lastArgs = arguments;
    lastThis = this;
    lastCallTime = time;

    if (isInvoking) {
      if (timerId === undefined) {
        return leadingEdge(lastCallTime);
      }
      if (maxing) {
        // Handle invocations in a tight loop.
        clearTimeout(timerId);
        timerId = setTimeout(timerExpired, wait);
        return invokeFunc(lastCallTime);
      }
    }
    if (timerId === undefined) {
      timerId = setTimeout(timerExpired, wait);
    }
    return result;
  }
  debounced.cancel = cancel;
  debounced.flush = flush;
  return debounced;
}

module.exports = debounce;


/***/ }),
/* 195 */
/***/ (function(module, exports, __webpack_require__) {

var root = __webpack_require__(13);

/**
 * Gets the timestamp of the number of milliseconds that have elapsed since
 * the Unix epoch (1 January 1970 00:00:00 UTC).
 *
 * @static
 * @memberOf _
 * @since 2.4.0
 * @category Date
 * @returns {number} Returns the timestamp.
 * @example
 *
 * _.defer(function(stamp) {
 *   console.log(_.now() - stamp);
 * }, _.now());
 * // => Logs the number of milliseconds it took for the deferred invocation.
 */
var now = function() {
  return root.Date.now();
};

module.exports = now;


/***/ }),
/* 196 */
/***/ (function(module, exports, __webpack_require__) {

var Symbol = __webpack_require__(36);

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var nativeObjectToString = objectProto.toString;

/** Built-in value references. */
var symToStringTag = Symbol ? Symbol.toStringTag : undefined;

/**
 * A specialized version of `baseGetTag` which ignores `Symbol.toStringTag` values.
 *
 * @private
 * @param {*} value The value to query.
 * @returns {string} Returns the raw `toStringTag`.
 */
function getRawTag(value) {
  var isOwn = hasOwnProperty.call(value, symToStringTag),
      tag = value[symToStringTag];

  try {
    value[symToStringTag] = undefined;
    var unmasked = true;
  } catch (e) {}

  var result = nativeObjectToString.call(value);
  if (unmasked) {
    if (isOwn) {
      value[symToStringTag] = tag;
    } else {
      delete value[symToStringTag];
    }
  }
  return result;
}

module.exports = getRawTag;


/***/ }),
/* 197 */
/***/ (function(module, exports) {

/** Used for built-in method references. */
var objectProto = Object.prototype;

/**
 * Used to resolve the
 * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
 * of values.
 */
var nativeObjectToString = objectProto.toString;

/**
 * Converts `value` to a string using `Object.prototype.toString`.
 *
 * @private
 * @param {*} value The value to convert.
 * @returns {string} Returns the converted string.
 */
function objectToString(value) {
  return nativeObjectToString.call(value);
}

module.exports = objectToString;


/***/ }),
/* 198 */
/***/ (function(module, exports, __webpack_require__) {

var assignValue = __webpack_require__(114),
    copyObject = __webpack_require__(203),
    createAssigner = __webpack_require__(204),
    isArrayLike = __webpack_require__(29),
    isPrototype = __webpack_require__(79),
    keys = __webpack_require__(50);

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Assigns own enumerable string keyed properties of source objects to the
 * destination object. Source objects are applied from left to right.
 * Subsequent sources overwrite property assignments of previous sources.
 *
 * **Note:** This method mutates `object` and is loosely based on
 * [`Object.assign`](https://mdn.io/Object/assign).
 *
 * @static
 * @memberOf _
 * @since 0.10.0
 * @category Object
 * @param {Object} object The destination object.
 * @param {...Object} [sources] The source objects.
 * @returns {Object} Returns `object`.
 * @see _.assignIn
 * @example
 *
 * function Foo() {
 *   this.a = 1;
 * }
 *
 * function Bar() {
 *   this.c = 3;
 * }
 *
 * Foo.prototype.b = 2;
 * Bar.prototype.d = 4;
 *
 * _.assign({ 'a': 0 }, new Foo, new Bar);
 * // => { 'a': 1, 'c': 3 }
 */
var assign = createAssigner(function(object, source) {
  if (isPrototype(source) || isArrayLike(source)) {
    copyObject(source, keys(source), object);
    return;
  }
  for (var key in source) {
    if (hasOwnProperty.call(source, key)) {
      assignValue(object, key, source[key]);
    }
  }
});

module.exports = assign;


/***/ }),
/* 199 */
/***/ (function(module, exports, __webpack_require__) {

var isFunction = __webpack_require__(117),
    isMasked = __webpack_require__(200),
    isObject = __webpack_require__(27),
    toSource = __webpack_require__(118);

/**
 * Used to match `RegExp`
 * [syntax characters](http://ecma-international.org/ecma-262/7.0/#sec-patterns).
 */
var reRegExpChar = /[\\^$.*+?()[\]{}|]/g;

/** Used to detect host constructors (Safari). */
var reIsHostCtor = /^\[object .+?Constructor\]$/;

/** Used for built-in method references. */
var funcProto = Function.prototype,
    objectProto = Object.prototype;

/** Used to resolve the decompiled source of functions. */
var funcToString = funcProto.toString;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/** Used to detect if a method is native. */
var reIsNative = RegExp('^' +
  funcToString.call(hasOwnProperty).replace(reRegExpChar, '\\$&')
  .replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
);

/**
 * The base implementation of `_.isNative` without bad shim checks.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a native function,
 *  else `false`.
 */
function baseIsNative(value) {
  if (!isObject(value) || isMasked(value)) {
    return false;
  }
  var pattern = isFunction(value) ? reIsNative : reIsHostCtor;
  return pattern.test(toSource(value));
}

module.exports = baseIsNative;


/***/ }),
/* 200 */
/***/ (function(module, exports, __webpack_require__) {

var coreJsData = __webpack_require__(201);

/** Used to detect methods masquerading as native. */
var maskSrcKey = (function() {
  var uid = /[^.]+$/.exec(coreJsData && coreJsData.keys && coreJsData.keys.IE_PROTO || '');
  return uid ? ('Symbol(src)_1.' + uid) : '';
}());

/**
 * Checks if `func` has its source masked.
 *
 * @private
 * @param {Function} func The function to check.
 * @returns {boolean} Returns `true` if `func` is masked, else `false`.
 */
function isMasked(func) {
  return !!maskSrcKey && (maskSrcKey in func);
}

module.exports = isMasked;


/***/ }),
/* 201 */
/***/ (function(module, exports, __webpack_require__) {

var root = __webpack_require__(13);

/** Used to detect overreaching core-js shims. */
var coreJsData = root['__core-js_shared__'];

module.exports = coreJsData;


/***/ }),
/* 202 */
/***/ (function(module, exports) {

/**
 * Gets the value at `key` of `object`.
 *
 * @private
 * @param {Object} [object] The object to query.
 * @param {string} key The key of the property to get.
 * @returns {*} Returns the property value.
 */
function getValue(object, key) {
  return object == null ? undefined : object[key];
}

module.exports = getValue;


/***/ }),
/* 203 */
/***/ (function(module, exports, __webpack_require__) {

var assignValue = __webpack_require__(114),
    baseAssignValue = __webpack_require__(115);

/**
 * Copies properties of `source` to `object`.
 *
 * @private
 * @param {Object} source The object to copy properties from.
 * @param {Array} props The property identifiers to copy.
 * @param {Object} [object={}] The object to copy properties to.
 * @param {Function} [customizer] The function to customize copied values.
 * @returns {Object} Returns `object`.
 */
function copyObject(source, props, object, customizer) {
  var isNew = !object;
  object || (object = {});

  var index = -1,
      length = props.length;

  while (++index < length) {
    var key = props[index];

    var newValue = customizer
      ? customizer(object[key], source[key], key, object, source)
      : undefined;

    if (newValue === undefined) {
      newValue = source[key];
    }
    if (isNew) {
      baseAssignValue(object, key, newValue);
    } else {
      assignValue(object, key, newValue);
    }
  }
  return object;
}

module.exports = copyObject;


/***/ }),
/* 204 */
/***/ (function(module, exports, __webpack_require__) {

var baseRest = __webpack_require__(75),
    isIterateeCall = __webpack_require__(119);

/**
 * Creates a function like `_.assign`.
 *
 * @private
 * @param {Function} assigner The function to assign values.
 * @returns {Function} Returns the new assigner function.
 */
function createAssigner(assigner) {
  return baseRest(function(object, sources) {
    var index = -1,
        length = sources.length,
        customizer = length > 1 ? sources[length - 1] : undefined,
        guard = length > 2 ? sources[2] : undefined;

    customizer = (assigner.length > 3 && typeof customizer == 'function')
      ? (length--, customizer)
      : undefined;

    if (guard && isIterateeCall(sources[0], sources[1], guard)) {
      customizer = length < 3 ? undefined : customizer;
      length = 1;
    }
    object = Object(object);
    while (++index < length) {
      var source = sources[index];
      if (source) {
        assigner(object, source, index, customizer);
      }
    }
    return object;
  });
}

module.exports = createAssigner;


/***/ }),
/* 205 */
/***/ (function(module, exports, __webpack_require__) {

var apply = __webpack_require__(206);

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeMax = Math.max;

/**
 * A specialized version of `baseRest` which transforms the rest array.
 *
 * @private
 * @param {Function} func The function to apply a rest parameter to.
 * @param {number} [start=func.length-1] The start position of the rest parameter.
 * @param {Function} transform The rest array transform.
 * @returns {Function} Returns the new function.
 */
function overRest(func, start, transform) {
  start = nativeMax(start === undefined ? (func.length - 1) : start, 0);
  return function() {
    var args = arguments,
        index = -1,
        length = nativeMax(args.length - start, 0),
        array = Array(length);

    while (++index < length) {
      array[index] = args[start + index];
    }
    index = -1;
    var otherArgs = Array(start + 1);
    while (++index < start) {
      otherArgs[index] = args[index];
    }
    otherArgs[start] = transform(array);
    return apply(func, this, otherArgs);
  };
}

module.exports = overRest;


/***/ }),
/* 206 */
/***/ (function(module, exports) {

/**
 * A faster alternative to `Function#apply`, this function invokes `func`
 * with the `this` binding of `thisArg` and the arguments of `args`.
 *
 * @private
 * @param {Function} func The function to invoke.
 * @param {*} thisArg The `this` binding of `func`.
 * @param {Array} args The arguments to invoke `func` with.
 * @returns {*} Returns the result of `func`.
 */
function apply(func, thisArg, args) {
  switch (args.length) {
    case 0: return func.call(thisArg);
    case 1: return func.call(thisArg, args[0]);
    case 2: return func.call(thisArg, args[0], args[1]);
    case 3: return func.call(thisArg, args[0], args[1], args[2]);
  }
  return func.apply(thisArg, args);
}

module.exports = apply;


/***/ }),
/* 207 */
/***/ (function(module, exports, __webpack_require__) {

var baseSetToString = __webpack_require__(208),
    shortOut = __webpack_require__(210);

/**
 * Sets the `toString` method of `func` to return `string`.
 *
 * @private
 * @param {Function} func The function to modify.
 * @param {Function} string The `toString` result.
 * @returns {Function} Returns `func`.
 */
var setToString = shortOut(baseSetToString);

module.exports = setToString;


/***/ }),
/* 208 */
/***/ (function(module, exports, __webpack_require__) {

var constant = __webpack_require__(209),
    defineProperty = __webpack_require__(116),
    identity = __webpack_require__(76);

/**
 * The base implementation of `setToString` without support for hot loop shorting.
 *
 * @private
 * @param {Function} func The function to modify.
 * @param {Function} string The `toString` result.
 * @returns {Function} Returns `func`.
 */
var baseSetToString = !defineProperty ? identity : function(func, string) {
  return defineProperty(func, 'toString', {
    'configurable': true,
    'enumerable': false,
    'value': constant(string),
    'writable': true
  });
};

module.exports = baseSetToString;


/***/ }),
/* 209 */
/***/ (function(module, exports) {

/**
 * Creates a function that returns `value`.
 *
 * @static
 * @memberOf _
 * @since 2.4.0
 * @category Util
 * @param {*} value The value to return from the new function.
 * @returns {Function} Returns the new constant function.
 * @example
 *
 * var objects = _.times(2, _.constant({ 'a': 1 }));
 *
 * console.log(objects);
 * // => [{ 'a': 1 }, { 'a': 1 }]
 *
 * console.log(objects[0] === objects[1]);
 * // => true
 */
function constant(value) {
  return function() {
    return value;
  };
}

module.exports = constant;


/***/ }),
/* 210 */
/***/ (function(module, exports) {

/** Used to detect hot functions by number of calls within a span of milliseconds. */
var HOT_COUNT = 800,
    HOT_SPAN = 16;

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeNow = Date.now;

/**
 * Creates a function that'll short out and invoke `identity` instead
 * of `func` when it's called `HOT_COUNT` or more times in `HOT_SPAN`
 * milliseconds.
 *
 * @private
 * @param {Function} func The function to restrict.
 * @returns {Function} Returns the new shortable function.
 */
function shortOut(func) {
  var count = 0,
      lastCalled = 0;

  return function() {
    var stamp = nativeNow(),
        remaining = HOT_SPAN - (stamp - lastCalled);

    lastCalled = stamp;
    if (remaining > 0) {
      if (++count >= HOT_COUNT) {
        return arguments[0];
      }
    } else {
      count = 0;
    }
    return func.apply(undefined, arguments);
  };
}

module.exports = shortOut;


/***/ }),
/* 211 */
/***/ (function(module, exports, __webpack_require__) {

var baseTimes = __webpack_require__(212),
    isArguments = __webpack_require__(51),
    isArray = __webpack_require__(12),
    isBuffer = __webpack_require__(80),
    isIndex = __webpack_require__(78),
    isTypedArray = __webpack_require__(81);

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Creates an array of the enumerable property names of the array-like `value`.
 *
 * @private
 * @param {*} value The value to query.
 * @param {boolean} inherited Specify returning inherited property names.
 * @returns {Array} Returns the array of property names.
 */
function arrayLikeKeys(value, inherited) {
  var isArr = isArray(value),
      isArg = !isArr && isArguments(value),
      isBuff = !isArr && !isArg && isBuffer(value),
      isType = !isArr && !isArg && !isBuff && isTypedArray(value),
      skipIndexes = isArr || isArg || isBuff || isType,
      result = skipIndexes ? baseTimes(value.length, String) : [],
      length = result.length;

  for (var key in value) {
    if ((inherited || hasOwnProperty.call(value, key)) &&
        !(skipIndexes && (
           // Safari 9 has enumerable `arguments.length` in strict mode.
           key == 'length' ||
           // Node.js 0.10 has enumerable non-index properties on buffers.
           (isBuff && (key == 'offset' || key == 'parent')) ||
           // PhantomJS 2 has enumerable non-index properties on typed arrays.
           (isType && (key == 'buffer' || key == 'byteLength' || key == 'byteOffset')) ||
           // Skip index properties.
           isIndex(key, length)
        ))) {
      result.push(key);
    }
  }
  return result;
}

module.exports = arrayLikeKeys;


/***/ }),
/* 212 */
/***/ (function(module, exports) {

/**
 * The base implementation of `_.times` without support for iteratee shorthands
 * or max array length checks.
 *
 * @private
 * @param {number} n The number of times to invoke `iteratee`.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Array} Returns the array of results.
 */
function baseTimes(n, iteratee) {
  var index = -1,
      result = Array(n);

  while (++index < n) {
    result[index] = iteratee(index);
  }
  return result;
}

module.exports = baseTimes;


/***/ }),
/* 213 */
/***/ (function(module, exports, __webpack_require__) {

var baseGetTag = __webpack_require__(35),
    isObjectLike = __webpack_require__(28);

/** `Object#toString` result references. */
var argsTag = '[object Arguments]';

/**
 * The base implementation of `_.isArguments`.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an `arguments` object,
 */
function baseIsArguments(value) {
  return isObjectLike(value) && baseGetTag(value) == argsTag;
}

module.exports = baseIsArguments;


/***/ }),
/* 214 */
/***/ (function(module, exports) {

/**
 * This method returns `false`.
 *
 * @static
 * @memberOf _
 * @since 4.13.0
 * @category Util
 * @returns {boolean} Returns `false`.
 * @example
 *
 * _.times(2, _.stubFalse);
 * // => [false, false]
 */
function stubFalse() {
  return false;
}

module.exports = stubFalse;


/***/ }),
/* 215 */
/***/ (function(module, exports, __webpack_require__) {

var baseGetTag = __webpack_require__(35),
    isLength = __webpack_require__(77),
    isObjectLike = __webpack_require__(28);

/** `Object#toString` result references. */
var argsTag = '[object Arguments]',
    arrayTag = '[object Array]',
    boolTag = '[object Boolean]',
    dateTag = '[object Date]',
    errorTag = '[object Error]',
    funcTag = '[object Function]',
    mapTag = '[object Map]',
    numberTag = '[object Number]',
    objectTag = '[object Object]',
    regexpTag = '[object RegExp]',
    setTag = '[object Set]',
    stringTag = '[object String]',
    weakMapTag = '[object WeakMap]';

var arrayBufferTag = '[object ArrayBuffer]',
    dataViewTag = '[object DataView]',
    float32Tag = '[object Float32Array]',
    float64Tag = '[object Float64Array]',
    int8Tag = '[object Int8Array]',
    int16Tag = '[object Int16Array]',
    int32Tag = '[object Int32Array]',
    uint8Tag = '[object Uint8Array]',
    uint8ClampedTag = '[object Uint8ClampedArray]',
    uint16Tag = '[object Uint16Array]',
    uint32Tag = '[object Uint32Array]';

/** Used to identify `toStringTag` values of typed arrays. */
var typedArrayTags = {};
typedArrayTags[float32Tag] = typedArrayTags[float64Tag] =
typedArrayTags[int8Tag] = typedArrayTags[int16Tag] =
typedArrayTags[int32Tag] = typedArrayTags[uint8Tag] =
typedArrayTags[uint8ClampedTag] = typedArrayTags[uint16Tag] =
typedArrayTags[uint32Tag] = true;
typedArrayTags[argsTag] = typedArrayTags[arrayTag] =
typedArrayTags[arrayBufferTag] = typedArrayTags[boolTag] =
typedArrayTags[dataViewTag] = typedArrayTags[dateTag] =
typedArrayTags[errorTag] = typedArrayTags[funcTag] =
typedArrayTags[mapTag] = typedArrayTags[numberTag] =
typedArrayTags[objectTag] = typedArrayTags[regexpTag] =
typedArrayTags[setTag] = typedArrayTags[stringTag] =
typedArrayTags[weakMapTag] = false;

/**
 * The base implementation of `_.isTypedArray` without Node.js optimizations.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
 */
function baseIsTypedArray(value) {
  return isObjectLike(value) &&
    isLength(value.length) && !!typedArrayTags[baseGetTag(value)];
}

module.exports = baseIsTypedArray;


/***/ }),
/* 216 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(module) {var freeGlobal = __webpack_require__(112);

/** Detect free variable `exports`. */
var freeExports = typeof exports == 'object' && exports && !exports.nodeType && exports;

/** Detect free variable `module`. */
var freeModule = freeExports && typeof module == 'object' && module && !module.nodeType && module;

/** Detect the popular CommonJS extension `module.exports`. */
var moduleExports = freeModule && freeModule.exports === freeExports;

/** Detect free variable `process` from Node.js. */
var freeProcess = moduleExports && freeGlobal.process;

/** Used to access faster Node.js helpers. */
var nodeUtil = (function() {
  try {
    // Use `util.types` for Node.js 10+.
    var types = freeModule && freeModule.require && freeModule.require('util').types;

    if (types) {
      return types;
    }

    // Legacy `process.binding('util')` for Node.js < 10.
    return freeProcess && freeProcess.binding && freeProcess.binding('util');
  } catch (e) {}
}());

module.exports = nodeUtil;

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(120)(module)))

/***/ }),
/* 217 */
/***/ (function(module, exports, __webpack_require__) {

var overArg = __webpack_require__(218);

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeKeys = overArg(Object.keys, Object);

module.exports = nativeKeys;


/***/ }),
/* 218 */
/***/ (function(module, exports) {

/**
 * Creates a unary function that invokes `func` with its argument transformed.
 *
 * @private
 * @param {Function} func The function to wrap.
 * @param {Function} transform The argument transform.
 * @returns {Function} Returns the new function.
 */
function overArg(func, transform) {
  return function(arg) {
    return func(transform(arg));
  };
}

module.exports = overArg;


/***/ }),
/* 219 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _tests = __webpack_require__(220);

/**
 * @function browserClasses
 * @description sets up browser classes on body without using user agent strings were possible.
 */
var applyBrowserClasses = function applyBrowserClasses() {
  var browser = (0, _tests.browserTests)();
  var classes = document.body.classList;

  if (browser.android) {
    classes.add('device-android');
  } else if (browser.ios) {
    classes.add('device-ios');
  }

  if (browser.edge) {
    classes.add('browser-edge');
  } else if (browser.chrome) {
    classes.add('browser-chrome');
  } else if (browser.firefox) {
    classes.add('browser-firefox');
  } else if (browser.ie) {
    classes.add('browser-ie');
  } else if (browser.opera) {
    classes.add('browser-opera');
  } else if (browser.safari) {
    classes.add('browser-safari');
  }
};

var _default = applyBrowserClasses;
exports.default = _default;

/***/ }),
/* 220 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.browserTests = exports.canLocalStore = exports.isJson = void 0;

__webpack_require__(82);

/**
 * @module
 * @description Some handy test for common issues.
 */
var isJson = function isJson(str) {
  try {
    JSON.parse(str);
  } catch (e) {
    return false;
  }

  return true;
};

exports.isJson = isJson;

var canLocalStore = function canLocalStore() {
  var mod;
  var result = false;

  try {
    mod = new Date();
    localStorage.setItem(mod, mod.toString());
    result = localStorage.getItem(mod) === mod.toString();
    localStorage.removeItem(mod);
    return result;
  } catch (_error) {
    console.error('This browser doesn\'t support local storage or is not allowing writing to it.');
    return result;
  }
};

exports.canLocalStore = canLocalStore;
var android = /(android)/i.test(navigator.userAgent);
var chrome = !!window.chrome;
var firefox = typeof InstallTrigger !== 'undefined';
var ie =
/* @cc_on!@ */
false || document.documentMode;
var edge = !ie && !!window.StyleMedia;
var ios = !!navigator.userAgent.match(/(iPod|iPhone|iPad)/i);
var iosMobile = !!navigator.userAgent.match(/(iPod|iPhone)/i);
var opera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
var safari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0 || !chrome && !opera && window.webkitAudioContext !== 'undefined'; // eslint-disable-line

var os = navigator.platform;

var browserTests = function browserTests() {
  return {
    android: android,
    chrome: chrome,
    edge: edge,
    firefox: firefox,
    ie: ie,
    ios: ios,
    iosMobile: iosMobile,
    opera: opera,
    safari: safari,
    os: os
  };
};

exports.browserTests = browserTests;

/***/ }),
/* 221 */,
/* 222 */,
/* 223 */,
/* 224 */,
/* 225 */,
/* 226 */,
/* 227 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _events = __webpack_require__(5);

var _viewportDims = _interopRequireDefault(__webpack_require__(127));

/**
 * @module
 * @exports resize
 * @description Kicks in any third party plugins that operate on a sitewide basis.
 */
var resize = function resize() {
  // code for resize events can go here
  (0, _viewportDims.default)();
  (0, _events.trigger)({
    event: 'modern_tribe/resize_executed',
    native: false
  });
};

var _default = resize;
exports.default = _default;

/***/ }),
/* 228 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.FULL_BREAKPOINT = exports.MOBILE_BREAKPOINT = void 0;
// breakpoint settings
var MOBILE_BREAKPOINT = 768;
exports.MOBILE_BREAKPOINT = MOBILE_BREAKPOINT;
var FULL_BREAKPOINT = 960;
exports.FULL_BREAKPOINT = FULL_BREAKPOINT;

/***/ }),
/* 229 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

// @flow

/**
 * @module
 * @exports plugins
 * @description Kicks in any third party plugins that operate on
 * a sitewide basis.
 */
// import gsap from 'gsap'; // uncomment to import gsap globally
var plugins = function plugins() {// initialize global external plugins here
};

var _default = plugins;
exports.default = _default;

/***/ }),
/* 230 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _productGallery = _interopRequireDefault(__webpack_require__(130));

var _productVideos = _interopRequireDefault(__webpack_require__(160));

var _productGalleryZoom = _interopRequireDefault(__webpack_require__(304));

/**
 * @module BigCommerce Product Gallery Sliders
 * @description Clearinghouse for loading all gallery JS.
 */
var init = function init() {
  (0, _productGallery.default)();
  (0, _productVideos.default)();
  (0, _productGalleryZoom.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 231 */,
/* 232 */,
/* 233 */,
/* 234 */,
/* 235 */
/***/ (function(module, exports, __webpack_require__) {

var Symbol = __webpack_require__(36),
    arrayMap = __webpack_require__(136),
    isArray = __webpack_require__(12),
    isSymbol = __webpack_require__(48);

/** Used as references for various `Number` constants. */
var INFINITY = 1 / 0;

/** Used to convert symbols to primitives and strings. */
var symbolProto = Symbol ? Symbol.prototype : undefined,
    symbolToString = symbolProto ? symbolProto.toString : undefined;

/**
 * The base implementation of `_.toString` which doesn't convert nullish
 * values to empty strings.
 *
 * @private
 * @param {*} value The value to process.
 * @returns {string} Returns the string.
 */
function baseToString(value) {
  // Exit early for strings to avoid a performance hit in some environments.
  if (typeof value == 'string') {
    return value;
  }
  if (isArray(value)) {
    // Recursively convert values (susceptible to call stack limits).
    return arrayMap(value, baseToString) + '';
  }
  if (isSymbol(value)) {
    return symbolToString ? symbolToString.call(value) : '';
  }
  var result = (value + '');
  return (result == '0' && (1 / value) == -INFINITY) ? '-0' : result;
}

module.exports = baseToString;


/***/ }),
/* 236 */,
/* 237 */,
/* 238 */,
/* 239 */,
/* 240 */,
/* 241 */,
/* 242 */,
/* 243 */,
/* 244 */,
/* 245 */,
/* 246 */,
/* 247 */,
/* 248 */,
/* 249 */,
/* 250 */,
/* 251 */,
/* 252 */,
/* 253 */,
/* 254 */,
/* 255 */,
/* 256 */,
/* 257 */,
/* 258 */,
/* 259 */,
/* 260 */,
/* 261 */,
/* 262 */,
/* 263 */,
/* 264 */,
/* 265 */,
/* 266 */,
/* 267 */,
/* 268 */,
/* 269 */,
/* 270 */,
/* 271 */,
/* 272 */,
/* 273 */,
/* 274 */,
/* 275 */,
/* 276 */,
/* 277 */,
/* 278 */,
/* 279 */,
/* 280 */,
/* 281 */,
/* 282 */,
/* 283 */,
/* 284 */,
/* 285 */,
/* 286 */,
/* 287 */,
/* 288 */,
/* 289 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(290);
module.exports = __webpack_require__(3).Object.values;


/***/ }),
/* 290 */
/***/ (function(module, exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-values-entries
var $export = __webpack_require__(10);
var $values = __webpack_require__(161)(false);

$export($export.S, 'Object', {
  values: function values(it) {
    return $values(it);
  }
});


/***/ }),
/* 291 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _sister = __webpack_require__(292);

var _sister2 = _interopRequireDefault(_sister);

var _loadYouTubeIframeApi = __webpack_require__(293);

var _loadYouTubeIframeApi2 = _interopRequireDefault(_loadYouTubeIframeApi);

var _YouTubePlayer = __webpack_require__(295);

var _YouTubePlayer2 = _interopRequireDefault(_YouTubePlayer);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * @typedef YT.Player
 * @see https://developers.google.com/youtube/iframe_api_reference
 * */

/**
 * @see https://developers.google.com/youtube/iframe_api_reference#Loading_a_Video_Player
 */
var youtubeIframeAPI = void 0;

/**
 * A factory function used to produce an instance of YT.Player and queue function calls and proxy events of the resulting object.
 *
 * @param maybeElementId Either An existing YT.Player instance,
 * the DOM element or the id of the HTML element where the API will insert an <iframe>.
 * @param options See `options` (Ignored when using an existing YT.Player instance).
 * @param strictState A flag designating whether or not to wait for
 * an acceptable state when calling supported functions. Default: `false`.
 * See `FunctionStateMap.js` for supported functions and acceptable states.
 */

exports.default = function (maybeElementId) {
  var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  var strictState = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

  var emitter = (0, _sister2.default)();

  if (!youtubeIframeAPI) {
    youtubeIframeAPI = (0, _loadYouTubeIframeApi2.default)(emitter);
  }

  if (options.events) {
    throw new Error('Event handlers cannot be overwritten.');
  }

  if (typeof maybeElementId === 'string' && !document.getElementById(maybeElementId)) {
    throw new Error('Element "' + maybeElementId + '" does not exist.');
  }

  options.events = _YouTubePlayer2.default.proxyEvents(emitter);

  var playerAPIReady = new Promise(function (resolve) {
    if ((typeof maybeElementId === 'undefined' ? 'undefined' : _typeof(maybeElementId)) === 'object' && maybeElementId.playVideo instanceof Function) {
      var player = maybeElementId;

      resolve(player);
    } else {
      // asume maybeElementId can be rendered inside
      // eslint-disable-next-line promise/catch-or-return
      youtubeIframeAPI.then(function (YT) {
        // eslint-disable-line promise/prefer-await-to-then
        var player = new YT.Player(maybeElementId, options);

        emitter.on('ready', function () {
          resolve(player);
        });

        return null;
      });
    }
  });

  var playerApi = _YouTubePlayer2.default.promisifyPlayer(playerAPIReady, strictState);

  playerApi.on = emitter.on;
  playerApi.off = emitter.off;

  return playerApi;
};

module.exports = exports['default'];

/***/ }),
/* 292 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Sister;

/**
* @link https://github.com/gajus/sister for the canonical source repository
* @license https://github.com/gajus/sister/blob/master/LICENSE BSD 3-Clause
*/
Sister = function () {
    var sister = {},
        events = {};

    /**
     * @name handler
     * @function
     * @param {Object} data Event data.
     */

    /**
     * @param {String} name Event name.
     * @param {handler} handler
     * @return {listener}
     */
    sister.on = function (name, handler) {
        var listener = {name: name, handler: handler};
        events[name] = events[name] || [];
        events[name].unshift(listener);
        return listener;
    };

    /**
     * @param {listener}
     */
    sister.off = function (listener) {
        var index = events[listener.name].indexOf(listener);

        if (index !== -1) {
            events[listener.name].splice(index, 1);
        }
    };

    /**
     * @param {String} name Event name.
     * @param {Object} data Event data.
     */
    sister.trigger = function (name, data) {
        var listeners = events[name],
            i;

        if (listeners) {
            i = listeners.length;
            while (i--) {
                listeners[i].handler(data);
            }
        }
    };

    return sister;
};

module.exports = Sister;


/***/ }),
/* 293 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _loadScript = __webpack_require__(294);

var _loadScript2 = _interopRequireDefault(_loadScript);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function (emitter) {
  /**
   * A promise that is resolved when window.onYouTubeIframeAPIReady is called.
   * The promise is resolved with a reference to window.YT object.
   */
  var iframeAPIReady = new Promise(function (resolve) {
    if (window.YT && window.YT.Player && window.YT.Player instanceof Function) {
      resolve(window.YT);

      return;
    } else {
      var protocol = window.location.protocol === 'http:' ? 'http:' : 'https:';

      (0, _loadScript2.default)(protocol + '//www.youtube.com/iframe_api', function (error) {
        if (error) {
          emitter.trigger('error', error);
        }
      });
    }

    var previous = window.onYouTubeIframeAPIReady;

    // The API will call this function when page has finished downloading
    // the JavaScript for the player API.
    window.onYouTubeIframeAPIReady = function () {
      if (previous) {
        previous();
      }

      resolve(window.YT);
    };
  });

  return iframeAPIReady;
};

module.exports = exports['default'];

/***/ }),
/* 294 */
/***/ (function(module, exports) {


module.exports = function load (src, opts, cb) {
  var head = document.head || document.getElementsByTagName('head')[0]
  var script = document.createElement('script')

  if (typeof opts === 'function') {
    cb = opts
    opts = {}
  }

  opts = opts || {}
  cb = cb || function() {}

  script.type = opts.type || 'text/javascript'
  script.charset = opts.charset || 'utf8';
  script.async = 'async' in opts ? !!opts.async : true
  script.src = src

  if (opts.attrs) {
    setAttributes(script, opts.attrs)
  }

  if (opts.text) {
    script.text = '' + opts.text
  }

  var onend = 'onload' in script ? stdOnEnd : ieOnEnd
  onend(script, cb)

  // some good legacy browsers (firefox) fail the 'in' detection above
  // so as a fallback we always set onload
  // old IE will ignore this and new IE will set onload
  if (!script.onload) {
    stdOnEnd(script, cb);
  }

  head.appendChild(script)
}

function setAttributes(script, attrs) {
  for (var attr in attrs) {
    script.setAttribute(attr, attrs[attr]);
  }
}

function stdOnEnd (script, cb) {
  script.onload = function () {
    this.onerror = this.onload = null
    cb(null, script)
  }
  script.onerror = function () {
    // this.onload = null here is necessary
    // because even IE9 works not like others
    this.onerror = this.onload = null
    cb(new Error('Failed to load ' + this.src), script)
  }
}

function ieOnEnd (script, cb) {
  script.onreadystatechange = function () {
    if (this.readyState != 'complete' && this.readyState != 'loaded') return
    this.onreadystatechange = null
    cb(null, script) // there is no way to catch loading errors in IE8
  }
}


/***/ }),
/* 295 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _debug = __webpack_require__(296);

var _debug2 = _interopRequireDefault(_debug);

var _functionNames = __webpack_require__(300);

var _functionNames2 = _interopRequireDefault(_functionNames);

var _eventNames = __webpack_require__(301);

var _eventNames2 = _interopRequireDefault(_eventNames);

var _FunctionStateMap = __webpack_require__(302);

var _FunctionStateMap2 = _interopRequireDefault(_FunctionStateMap);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/* eslint-disable promise/prefer-await-to-then */

var debug = (0, _debug2.default)('youtube-player');

var YouTubePlayer = {};

/**
 * Construct an object that defines an event handler for all of the YouTube
 * player events. Proxy captured events through an event emitter.
 *
 * @todo Capture event parameters.
 * @see https://developers.google.com/youtube/iframe_api_reference#Events
 */
YouTubePlayer.proxyEvents = function (emitter) {
  var events = {};

  var _loop = function _loop(eventName) {
    var onEventName = 'on' + eventName.slice(0, 1).toUpperCase() + eventName.slice(1);

    events[onEventName] = function (event) {
      debug('event "%s"', onEventName, event);

      emitter.trigger(eventName, event);
    };
  };

  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = _eventNames2.default[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var eventName = _step.value;

      _loop(eventName);
    }
  } catch (err) {
    _didIteratorError = true;
    _iteratorError = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion && _iterator.return) {
        _iterator.return();
      }
    } finally {
      if (_didIteratorError) {
        throw _iteratorError;
      }
    }
  }

  return events;
};

/**
 * Delays player API method execution until player state is ready.
 *
 * @todo Proxy all of the methods using Object.keys.
 * @todo See TRICKY below.
 * @param playerAPIReady Promise that resolves when player is ready.
 * @param strictState A flag designating whether or not to wait for
 * an acceptable state when calling supported functions.
 * @returns {Object}
 */
YouTubePlayer.promisifyPlayer = function (playerAPIReady) {
  var strictState = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var functions = {};

  var _loop2 = function _loop2(functionName) {
    if (strictState && _FunctionStateMap2.default[functionName]) {
      functions[functionName] = function () {
        for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
          args[_key] = arguments[_key];
        }

        return playerAPIReady.then(function (player) {
          var stateInfo = _FunctionStateMap2.default[functionName];
          var playerState = player.getPlayerState();

          // eslint-disable-next-line no-warning-comments
          // TODO: Just spread the args into the function once Babel is fixed:
          // https://github.com/babel/babel/issues/4270
          //
          // eslint-disable-next-line prefer-spread
          var value = player[functionName].apply(player, args);

          // TRICKY: For functions like `seekTo`, a change in state must be
          // triggered given that the resulting state could match the initial
          // state.
          if (stateInfo.stateChangeRequired ||

          // eslint-disable-next-line no-extra-parens
          Array.isArray(stateInfo.acceptableStates) && stateInfo.acceptableStates.indexOf(playerState) === -1) {
            return new Promise(function (resolve) {
              var onPlayerStateChange = function onPlayerStateChange() {
                var playerStateAfterChange = player.getPlayerState();

                var timeout = void 0;

                if (typeof stateInfo.timeout === 'number') {
                  timeout = setTimeout(function () {
                    player.removeEventListener('onStateChange', onPlayerStateChange);

                    resolve();
                  }, stateInfo.timeout);
                }

                if (Array.isArray(stateInfo.acceptableStates) && stateInfo.acceptableStates.indexOf(playerStateAfterChange) !== -1) {
                  player.removeEventListener('onStateChange', onPlayerStateChange);

                  clearTimeout(timeout);

                  resolve();
                }
              };

              player.addEventListener('onStateChange', onPlayerStateChange);
            }).then(function () {
              return value;
            });
          }

          return value;
        });
      };
    } else {
      functions[functionName] = function () {
        for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
          args[_key2] = arguments[_key2];
        }

        return playerAPIReady.then(function (player) {
          // eslint-disable-next-line no-warning-comments
          // TODO: Just spread the args into the function once Babel is fixed:
          // https://github.com/babel/babel/issues/4270
          //
          // eslint-disable-next-line prefer-spread
          return player[functionName].apply(player, args);
        });
      };
    }
  };

  var _iteratorNormalCompletion2 = true;
  var _didIteratorError2 = false;
  var _iteratorError2 = undefined;

  try {
    for (var _iterator2 = _functionNames2.default[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
      var functionName = _step2.value;

      _loop2(functionName);
    }
  } catch (err) {
    _didIteratorError2 = true;
    _iteratorError2 = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion2 && _iterator2.return) {
        _iterator2.return();
      }
    } finally {
      if (_didIteratorError2) {
        throw _iteratorError2;
      }
    }
  }

  return functions;
};

exports.default = YouTubePlayer;
module.exports = exports['default'];

/***/ }),
/* 296 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(process) {/**
 * This is the web browser implementation of `debug()`.
 *
 * Expose `debug()` as the module.
 */

exports = module.exports = __webpack_require__(298);
exports.log = log;
exports.formatArgs = formatArgs;
exports.save = save;
exports.load = load;
exports.useColors = useColors;
exports.storage = 'undefined' != typeof chrome
               && 'undefined' != typeof chrome.storage
                  ? chrome.storage.local
                  : localstorage();

/**
 * Colors.
 */

exports.colors = [
  'lightseagreen',
  'forestgreen',
  'goldenrod',
  'dodgerblue',
  'darkorchid',
  'crimson'
];

/**
 * Currently only WebKit-based Web Inspectors, Firefox >= v31,
 * and the Firebug extension (any Firefox version) are known
 * to support "%c" CSS customizations.
 *
 * TODO: add a `localStorage` variable to explicitly enable/disable colors
 */

function useColors() {
  // NB: In an Electron preload script, document will be defined but not fully
  // initialized. Since we know we're in Chrome, we'll just detect this case
  // explicitly
  if (typeof window !== 'undefined' && window.process && window.process.type === 'renderer') {
    return true;
  }

  // is webkit? http://stackoverflow.com/a/16459606/376773
  // document is undefined in react-native: https://github.com/facebook/react-native/pull/1632
  return (typeof document !== 'undefined' && document.documentElement && document.documentElement.style && document.documentElement.style.WebkitAppearance) ||
    // is firebug? http://stackoverflow.com/a/398120/376773
    (typeof window !== 'undefined' && window.console && (window.console.firebug || (window.console.exception && window.console.table))) ||
    // is firefox >= v31?
    // https://developer.mozilla.org/en-US/docs/Tools/Web_Console#Styling_messages
    (typeof navigator !== 'undefined' && navigator.userAgent && navigator.userAgent.toLowerCase().match(/firefox\/(\d+)/) && parseInt(RegExp.$1, 10) >= 31) ||
    // double check webkit in userAgent just in case we are in a worker
    (typeof navigator !== 'undefined' && navigator.userAgent && navigator.userAgent.toLowerCase().match(/applewebkit\/(\d+)/));
}

/**
 * Map %j to `JSON.stringify()`, since no Web Inspectors do that by default.
 */

exports.formatters.j = function(v) {
  try {
    return JSON.stringify(v);
  } catch (err) {
    return '[UnexpectedJSONParseError]: ' + err.message;
  }
};


/**
 * Colorize log arguments if enabled.
 *
 * @api public
 */

function formatArgs(args) {
  var useColors = this.useColors;

  args[0] = (useColors ? '%c' : '')
    + this.namespace
    + (useColors ? ' %c' : ' ')
    + args[0]
    + (useColors ? '%c ' : ' ')
    + '+' + exports.humanize(this.diff);

  if (!useColors) return;

  var c = 'color: ' + this.color;
  args.splice(1, 0, c, 'color: inherit')

  // the final "%c" is somewhat tricky, because there could be other
  // arguments passed either before or after the %c, so we need to
  // figure out the correct index to insert the CSS into
  var index = 0;
  var lastC = 0;
  args[0].replace(/%[a-zA-Z%]/g, function(match) {
    if ('%%' === match) return;
    index++;
    if ('%c' === match) {
      // we only are interested in the *last* %c
      // (the user may have provided their own)
      lastC = index;
    }
  });

  args.splice(lastC, 0, c);
}

/**
 * Invokes `console.log()` when available.
 * No-op when `console.log` is not a "function".
 *
 * @api public
 */

function log() {
  // this hackery is required for IE8/9, where
  // the `console.log` function doesn't have 'apply'
  return 'object' === typeof console
    && console.log
    && Function.prototype.apply.call(console.log, console, arguments);
}

/**
 * Save `namespaces`.
 *
 * @param {String} namespaces
 * @api private
 */

function save(namespaces) {
  try {
    if (null == namespaces) {
      exports.storage.removeItem('debug');
    } else {
      exports.storage.debug = namespaces;
    }
  } catch(e) {}
}

/**
 * Load `namespaces`.
 *
 * @return {String} returns the previously persisted debug modes
 * @api private
 */

function load() {
  var r;
  try {
    r = exports.storage.debug;
  } catch(e) {}

  // If debug isn't set in LS, and we're in Electron, try to load $DEBUG
  if (!r && typeof process !== 'undefined' && 'env' in process) {
    r = process.env.DEBUG;
  }

  return r;
}

/**
 * Enable namespaces listed in `localStorage.debug` initially.
 */

exports.enable(load());

/**
 * Localstorage attempts to return the localstorage.
 *
 * This is necessary because safari throws
 * when a user disables cookies/localstorage
 * and you attempt to access it.
 *
 * @return {LocalStorage}
 * @api private
 */

function localstorage() {
  try {
    return window.localStorage;
  } catch (e) {}
}

/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(297)))

/***/ }),
/* 297 */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),
/* 298 */
/***/ (function(module, exports, __webpack_require__) {


/**
 * This is the common logic for both the Node.js and web browser
 * implementations of `debug()`.
 *
 * Expose `debug()` as the module.
 */

exports = module.exports = createDebug.debug = createDebug['default'] = createDebug;
exports.coerce = coerce;
exports.disable = disable;
exports.enable = enable;
exports.enabled = enabled;
exports.humanize = __webpack_require__(299);

/**
 * The currently active debug mode names, and names to skip.
 */

exports.names = [];
exports.skips = [];

/**
 * Map of special "%n" handling functions, for the debug "format" argument.
 *
 * Valid key names are a single, lower or upper-case letter, i.e. "n" and "N".
 */

exports.formatters = {};

/**
 * Previous log timestamp.
 */

var prevTime;

/**
 * Select a color.
 * @param {String} namespace
 * @return {Number}
 * @api private
 */

function selectColor(namespace) {
  var hash = 0, i;

  for (i in namespace) {
    hash  = ((hash << 5) - hash) + namespace.charCodeAt(i);
    hash |= 0; // Convert to 32bit integer
  }

  return exports.colors[Math.abs(hash) % exports.colors.length];
}

/**
 * Create a debugger with the given `namespace`.
 *
 * @param {String} namespace
 * @return {Function}
 * @api public
 */

function createDebug(namespace) {

  function debug() {
    // disabled?
    if (!debug.enabled) return;

    var self = debug;

    // set `diff` timestamp
    var curr = +new Date();
    var ms = curr - (prevTime || curr);
    self.diff = ms;
    self.prev = prevTime;
    self.curr = curr;
    prevTime = curr;

    // turn the `arguments` into a proper Array
    var args = new Array(arguments.length);
    for (var i = 0; i < args.length; i++) {
      args[i] = arguments[i];
    }

    args[0] = exports.coerce(args[0]);

    if ('string' !== typeof args[0]) {
      // anything else let's inspect with %O
      args.unshift('%O');
    }

    // apply any `formatters` transformations
    var index = 0;
    args[0] = args[0].replace(/%([a-zA-Z%])/g, function(match, format) {
      // if we encounter an escaped % then don't increase the array index
      if (match === '%%') return match;
      index++;
      var formatter = exports.formatters[format];
      if ('function' === typeof formatter) {
        var val = args[index];
        match = formatter.call(self, val);

        // now we need to remove `args[index]` since it's inlined in the `format`
        args.splice(index, 1);
        index--;
      }
      return match;
    });

    // apply env-specific formatting (colors, etc.)
    exports.formatArgs.call(self, args);

    var logFn = debug.log || exports.log || console.log.bind(console);
    logFn.apply(self, args);
  }

  debug.namespace = namespace;
  debug.enabled = exports.enabled(namespace);
  debug.useColors = exports.useColors();
  debug.color = selectColor(namespace);

  // env-specific initialization logic for debug instances
  if ('function' === typeof exports.init) {
    exports.init(debug);
  }

  return debug;
}

/**
 * Enables a debug mode by namespaces. This can include modes
 * separated by a colon and wildcards.
 *
 * @param {String} namespaces
 * @api public
 */

function enable(namespaces) {
  exports.save(namespaces);

  exports.names = [];
  exports.skips = [];

  var split = (typeof namespaces === 'string' ? namespaces : '').split(/[\s,]+/);
  var len = split.length;

  for (var i = 0; i < len; i++) {
    if (!split[i]) continue; // ignore empty strings
    namespaces = split[i].replace(/\*/g, '.*?');
    if (namespaces[0] === '-') {
      exports.skips.push(new RegExp('^' + namespaces.substr(1) + '$'));
    } else {
      exports.names.push(new RegExp('^' + namespaces + '$'));
    }
  }
}

/**
 * Disable debug output.
 *
 * @api public
 */

function disable() {
  exports.enable('');
}

/**
 * Returns true if the given mode name is enabled, false otherwise.
 *
 * @param {String} name
 * @return {Boolean}
 * @api public
 */

function enabled(name) {
  var i, len;
  for (i = 0, len = exports.skips.length; i < len; i++) {
    if (exports.skips[i].test(name)) {
      return false;
    }
  }
  for (i = 0, len = exports.names.length; i < len; i++) {
    if (exports.names[i].test(name)) {
      return true;
    }
  }
  return false;
}

/**
 * Coerce `val`.
 *
 * @param {Mixed} val
 * @return {Mixed}
 * @api private
 */

function coerce(val) {
  if (val instanceof Error) return val.stack || val.message;
  return val;
}


/***/ }),
/* 299 */
/***/ (function(module, exports) {

/**
 * Helpers.
 */

var s = 1000;
var m = s * 60;
var h = m * 60;
var d = h * 24;
var y = d * 365.25;

/**
 * Parse or format the given `val`.
 *
 * Options:
 *
 *  - `long` verbose formatting [false]
 *
 * @param {String|Number} val
 * @param {Object} [options]
 * @throws {Error} throw an error if val is not a non-empty string or a number
 * @return {String|Number}
 * @api public
 */

module.exports = function(val, options) {
  options = options || {};
  var type = typeof val;
  if (type === 'string' && val.length > 0) {
    return parse(val);
  } else if (type === 'number' && isNaN(val) === false) {
    return options.long ? fmtLong(val) : fmtShort(val);
  }
  throw new Error(
    'val is not a non-empty string or a valid number. val=' +
      JSON.stringify(val)
  );
};

/**
 * Parse the given `str` and return milliseconds.
 *
 * @param {String} str
 * @return {Number}
 * @api private
 */

function parse(str) {
  str = String(str);
  if (str.length > 100) {
    return;
  }
  var match = /^((?:\d+)?\.?\d+) *(milliseconds?|msecs?|ms|seconds?|secs?|s|minutes?|mins?|m|hours?|hrs?|h|days?|d|years?|yrs?|y)?$/i.exec(
    str
  );
  if (!match) {
    return;
  }
  var n = parseFloat(match[1]);
  var type = (match[2] || 'ms').toLowerCase();
  switch (type) {
    case 'years':
    case 'year':
    case 'yrs':
    case 'yr':
    case 'y':
      return n * y;
    case 'days':
    case 'day':
    case 'd':
      return n * d;
    case 'hours':
    case 'hour':
    case 'hrs':
    case 'hr':
    case 'h':
      return n * h;
    case 'minutes':
    case 'minute':
    case 'mins':
    case 'min':
    case 'm':
      return n * m;
    case 'seconds':
    case 'second':
    case 'secs':
    case 'sec':
    case 's':
      return n * s;
    case 'milliseconds':
    case 'millisecond':
    case 'msecs':
    case 'msec':
    case 'ms':
      return n;
    default:
      return undefined;
  }
}

/**
 * Short format for `ms`.
 *
 * @param {Number} ms
 * @return {String}
 * @api private
 */

function fmtShort(ms) {
  if (ms >= d) {
    return Math.round(ms / d) + 'd';
  }
  if (ms >= h) {
    return Math.round(ms / h) + 'h';
  }
  if (ms >= m) {
    return Math.round(ms / m) + 'm';
  }
  if (ms >= s) {
    return Math.round(ms / s) + 's';
  }
  return ms + 'ms';
}

/**
 * Long format for `ms`.
 *
 * @param {Number} ms
 * @return {String}
 * @api private
 */

function fmtLong(ms) {
  return plural(ms, d, 'day') ||
    plural(ms, h, 'hour') ||
    plural(ms, m, 'minute') ||
    plural(ms, s, 'second') ||
    ms + ' ms';
}

/**
 * Pluralization helper.
 */

function plural(ms, n, name) {
  if (ms < n) {
    return;
  }
  if (ms < n * 1.5) {
    return Math.floor(ms / n) + ' ' + name;
  }
  return Math.ceil(ms / n) + ' ' + name + 's';
}


/***/ }),
/* 300 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});


/**
 * @see https://developers.google.com/youtube/iframe_api_reference#Functions
 */
exports.default = ['cueVideoById', 'loadVideoById', 'cueVideoByUrl', 'loadVideoByUrl', 'playVideo', 'pauseVideo', 'stopVideo', 'getVideoLoadedFraction', 'cuePlaylist', 'loadPlaylist', 'nextVideo', 'previousVideo', 'playVideoAt', 'setShuffle', 'setLoop', 'getPlaylist', 'getPlaylistIndex', 'setOption', 'mute', 'unMute', 'isMuted', 'setVolume', 'getVolume', 'seekTo', 'getPlayerState', 'getPlaybackRate', 'setPlaybackRate', 'getAvailablePlaybackRates', 'getPlaybackQuality', 'setPlaybackQuality', 'getAvailableQualityLevels', 'getCurrentTime', 'getDuration', 'removeEventListener', 'getVideoUrl', 'getVideoEmbedCode', 'getOptions', 'getOption', 'addEventListener', 'destroy', 'setSize', 'getIframe'];
module.exports = exports['default'];

/***/ }),
/* 301 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});


/**
 * @see https://developers.google.com/youtube/iframe_api_reference#Events
 * `volumeChange` is not officially supported but seems to work
 * it emits an object: `{volume: 82.6923076923077, muted: false}`
 */
exports.default = ['ready', 'stateChange', 'playbackQualityChange', 'playbackRateChange', 'error', 'apiChange', 'volumeChange'];
module.exports = exports['default'];

/***/ }),
/* 302 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

var _PlayerStates = __webpack_require__(303);

var _PlayerStates2 = _interopRequireDefault(_PlayerStates);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = {
  pauseVideo: {
    acceptableStates: [_PlayerStates2.default.ENDED, _PlayerStates2.default.PAUSED],
    stateChangeRequired: false
  },
  playVideo: {
    acceptableStates: [_PlayerStates2.default.ENDED, _PlayerStates2.default.PLAYING],
    stateChangeRequired: false
  },
  seekTo: {
    acceptableStates: [_PlayerStates2.default.ENDED, _PlayerStates2.default.PLAYING, _PlayerStates2.default.PAUSED],
    stateChangeRequired: true,

    // TRICKY: `seekTo` may not cause a state change if no buffering is
    // required.
    timeout: 3000
  }
};
module.exports = exports['default'];

/***/ }),
/* 303 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = {
  BUFFERING: 3,
  ENDED: 0,
  PAUSED: 2,
  PLAYING: 1,
  UNSTARTED: -1,
  VIDEO_CUED: 5
};
module.exports = exports["default"];

/***/ }),
/* 304 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _uniqueId2 = _interopRequireDefault(__webpack_require__(23));

var _driftZoom = _interopRequireDefault(__webpack_require__(305));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _events = __webpack_require__(5);

/**
 * @module Product Gallery Zoom
 * @description If zoom is enabled in the customizer, setup the image zoom functionality.
 */
var el = {
  container: tools.getNodes('bc-product-image-zoom')[0]
};
var instances = {
  zoomers: {}
};
var imageZoomOptions = {
  containInline: true,
  paneContainer: '',
  zoomFactor: 2,
  inlinePane: 320
};

var initImageZoomers = function initImageZoomers() {
  var image = tools.getNodes('[data-js="bc-gallery-container"] .bc-product-gallery__image-slide:not(.initialized)', true, document, true);
  image.forEach(function (img) {
    var zoomMainId = (0, _uniqueId2.default)('zoom-');
    tools.addClass(img, 'initialized');
    img.dataset.zoomid = zoomMainId;
    imageZoomOptions.paneContainer = img;
    instances.zoomers[zoomMainId] = new _driftZoom.default(img.querySelector('img'), imageZoomOptions);
  });
};

var init = function init() {
  if (!el.container) {
    return;
  }

  initImageZoomers();
  (0, _events.on)(document, 'bigcommerce/init_slide_zoom', initImageZoomers);
};

var _default = init;
exports.default = _default;

/***/ }),
/* 305 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Drift; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__util_dom__ = __webpack_require__(99);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__injectBaseStylesheet__ = __webpack_require__(306);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__Trigger__ = __webpack_require__(307);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__ZoomPane__ = __webpack_require__(309);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }






var Drift =
/*#__PURE__*/
function () {
  function Drift(triggerEl) {
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

    _classCallCheck(this, Drift);

    this.VERSION = "1.4.0";
    this.triggerEl = triggerEl;
    this.destroy = this.destroy.bind(this);

    if (!Object(__WEBPACK_IMPORTED_MODULE_0__util_dom__["b" /* isDOMElement */])(this.triggerEl)) {
      throw new TypeError("`new Drift` requires a DOM element as its first argument.");
    } // Prefix for generated element class names (e.g. `my-ns` will
    // result in classes such as `my-ns-pane`. Default `drift-`
    // prefixed classes will always be added as well.


    var namespace = options["namespace"] || null; // Whether the ZoomPane should show whitespace when near the edges.

    var showWhitespaceAtEdges = options["showWhitespaceAtEdges"] || false; // Whether the inline ZoomPane should stay inside
    // the bounds of its image.

    var containInline = options["containInline"] || false; // How much to offset the ZoomPane from the
    // interaction point when inline.

    var inlineOffsetX = options["inlineOffsetX"] || 0;
    var inlineOffsetY = options["inlineOffsetY"] || 0; // A DOM element to append the inline ZoomPane to

    var inlineContainer = options["inlineContainer"] || document.body; // Which trigger attribute to pull the ZoomPane image source from.

    var sourceAttribute = options["sourceAttribute"] || "data-zoom"; // How much to magnify the trigger by in the ZoomPane.
    // (e.g., `zoomFactor: 3` will result in a 900 px wide ZoomPane imag
    // if the trigger is displayed at 300 px wide)

    var zoomFactor = options["zoomFactor"] || 3; // A DOM element to append the non-inline ZoomPane to.
    // Required if `inlinePane !== true`.

    var paneContainer = options["paneContainer"] === undefined ? document.body : options["paneContainer"]; // When to switch to an inline ZoomPane. This can be a boolean or
    // an integer. If `true`, the ZoomPane will always be inline,
    // if `false`, it will switch to inline when `windowWidth <= inlinePane`

    var inlinePane = options["inlinePane"] || 375; // If `true`, touch events will trigger the zoom, like mouse events.

    var handleTouch = "handleTouch" in options ? !!options["handleTouch"] : true; // If present (and a function), this will be called
    // whenever the ZoomPane is shown.

    var onShow = options["onShow"] || null; // If present (and a function), this will be called
    // whenever the ZoomPane is hidden.

    var onHide = options["onHide"] || null; // Add base styles to the page. See the "Theming"
    // section of README.md for more information.

    var injectBaseStyles = "injectBaseStyles" in options ? !!options["injectBaseStyles"] : true; // An optional number that determines how long to wait before
    // showing the ZoomPane because of a `mouseenter` event.

    var hoverDelay = options["hoverDelay"] || 0; // An optional number that determines how long to wait before
    // showing the ZoomPane because of a `touchstart` event.
    // It's unlikely that you would want to use this option, since
    // "tap and hold" is much more intentional than a hover event.

    var touchDelay = options["touchDelay"] || 0; // If true, a bounding box will show the area currently being previewed
    // during mouse hover

    var hoverBoundingBox = options["hoverBoundingBox"] || false; // If true, a bounding box will show the area currently being previewed
    // during touch events

    var touchBoundingBox = options["touchBoundingBox"] || false; // A DOM element to append the bounding box to.

    var boundingBoxContainer = options["boundingBoxContainer"] || document.body;

    if (inlinePane !== true && !Object(__WEBPACK_IMPORTED_MODULE_0__util_dom__["b" /* isDOMElement */])(paneContainer)) {
      throw new TypeError("`paneContainer` must be a DOM element when `inlinePane !== true`");
    }

    if (!Object(__WEBPACK_IMPORTED_MODULE_0__util_dom__["b" /* isDOMElement */])(inlineContainer)) {
      throw new TypeError("`inlineContainer` must be a DOM element");
    }

    this.settings = {
      namespace: namespace,
      showWhitespaceAtEdges: showWhitespaceAtEdges,
      containInline: containInline,
      inlineOffsetX: inlineOffsetX,
      inlineOffsetY: inlineOffsetY,
      inlineContainer: inlineContainer,
      sourceAttribute: sourceAttribute,
      zoomFactor: zoomFactor,
      paneContainer: paneContainer,
      inlinePane: inlinePane,
      handleTouch: handleTouch,
      onShow: onShow,
      onHide: onHide,
      injectBaseStyles: injectBaseStyles,
      hoverDelay: hoverDelay,
      touchDelay: touchDelay,
      hoverBoundingBox: hoverBoundingBox,
      touchBoundingBox: touchBoundingBox,
      boundingBoxContainer: boundingBoxContainer
    };

    if (this.settings.injectBaseStyles) {
      Object(__WEBPACK_IMPORTED_MODULE_1__injectBaseStylesheet__["a" /* default */])();
    }

    this._buildZoomPane();

    this._buildTrigger();
  }

  _createClass(Drift, [{
    key: "_buildZoomPane",
    value: function _buildZoomPane() {
      this.zoomPane = new __WEBPACK_IMPORTED_MODULE_3__ZoomPane__["a" /* default */]({
        container: this.settings.paneContainer,
        zoomFactor: this.settings.zoomFactor,
        showWhitespaceAtEdges: this.settings.showWhitespaceAtEdges,
        containInline: this.settings.containInline,
        inline: this.settings.inlinePane,
        namespace: this.settings.namespace,
        inlineOffsetX: this.settings.inlineOffsetX,
        inlineOffsetY: this.settings.inlineOffsetY,
        inlineContainer: this.settings.inlineContainer
      });
    }
  }, {
    key: "_buildTrigger",
    value: function _buildTrigger() {
      this.trigger = new __WEBPACK_IMPORTED_MODULE_2__Trigger__["a" /* default */]({
        el: this.triggerEl,
        zoomPane: this.zoomPane,
        handleTouch: this.settings.handleTouch,
        onShow: this.settings.onShow,
        onHide: this.settings.onHide,
        sourceAttribute: this.settings.sourceAttribute,
        hoverDelay: this.settings.hoverDelay,
        touchDelay: this.settings.touchDelay,
        hoverBoundingBox: this.settings.hoverBoundingBox,
        touchBoundingBox: this.settings.touchBoundingBox,
        namespace: this.settings.namespace,
        zoomFactor: this.settings.zoomFactor,
        boundingBoxContainer: this.settings.boundingBoxContainer
      });
    }
  }, {
    key: "setZoomImageURL",
    value: function setZoomImageURL(imageURL) {
      this.zoomPane._setImageURL(imageURL);
    }
  }, {
    key: "disable",
    value: function disable() {
      this.trigger.enabled = false;
    }
  }, {
    key: "enable",
    value: function enable() {
      this.trigger.enabled = true;
    }
  }, {
    key: "destroy",
    value: function destroy() {
      this.trigger._hide();

      this.trigger._unbindEvents();
    }
  }, {
    key: "isShowing",
    get: function get() {
      return this.zoomPane.isShowing;
    }
  }, {
    key: "zoomFactor",
    get: function get() {
      return this.settings.zoomFactor;
    },
    set: function set(zf) {
      this.settings.zoomFactor = zf;
      this.zoomPane.settings.zoomFactor = zf;
      this.trigger.settings.zoomFactor = zf;
      this.boundingBox.settings.zoomFactor = zf;
    }
  }]);

  return Drift;
}(); // Public API

/* eslint-disable no-self-assign */



Object.defineProperty(Drift.prototype, "isShowing", {
  get: function get() {
    return this.isShowing;
  }
});
Object.defineProperty(Drift.prototype, "zoomFactor", {
  get: function get() {
    return this.zoomFactor;
  },
  set: function set(value) {
    this.zoomFactor = value;
  }
});
Drift.prototype["setZoomImageURL"] = Drift.prototype.setZoomImageURL;
Drift.prototype["disable"] = Drift.prototype.disable;
Drift.prototype["enable"] = Drift.prototype.enable;
Drift.prototype["destroy"] = Drift.prototype.destroy;
/* eslint-enable no-self-assign */
//# sourceMappingURL=Drift.js.map

/***/ }),
/* 306 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = injectBaseStylesheet;
/* UNMINIFIED RULES

const RULES = `
@keyframes noop {
  0% { zoom: 1; }
}

@-webkit-keyframes noop {
  0% { zoom: 1; }
}

.drift-zoom-pane.drift-open {
  display: block;
}

.drift-zoom-pane.drift-opening, .drift-zoom-pane.drift-closing {
  animation: noop 1ms;
  -webkit-animation: noop 1ms;
}

.drift-zoom-pane {
  position: absolute;
  overflow: hidden;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none;
}

.drift-zoom-pane-loader {
  display: none;
}

.drift-zoom-pane img {
  position: absolute;
  display: block;
  max-width: none;
  max-height: none;
}

.drift-bounding-box {
  position: absolute;
  pointer-events: none;
}
`;

*/
var RULES = ".drift-bounding-box,.drift-zoom-pane{position:absolute;pointer-events:none}@keyframes noop{0%{zoom:1}}@-webkit-keyframes noop{0%{zoom:1}}.drift-zoom-pane.drift-open{display:block}.drift-zoom-pane.drift-closing,.drift-zoom-pane.drift-opening{animation:noop 1ms;-webkit-animation:noop 1ms}.drift-zoom-pane{overflow:hidden;width:100%;height:100%;top:0;left:0}.drift-zoom-pane-loader{display:none}.drift-zoom-pane img{position:absolute;display:block;max-width:none;max-height:none}";
function injectBaseStylesheet() {
  if (document.querySelector(".drift-base-styles")) {
    return;
  }

  var styleEl = document.createElement("style");
  styleEl.type = "text/css";
  styleEl.classList.add("drift-base-styles");
  styleEl.appendChild(document.createTextNode(RULES));
  var head = document.head;
  head.insertBefore(styleEl, head.firstChild);
}
//# sourceMappingURL=injectBaseStylesheet.js.map

/***/ }),
/* 307 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return Trigger; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__ = __webpack_require__(100);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__BoundingBox__ = __webpack_require__(308);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }




var Trigger =
/*#__PURE__*/
function () {
  function Trigger() {
    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    _classCallCheck(this, Trigger);

    this._show = this._show.bind(this);
    this._hide = this._hide.bind(this);
    this._handleEntry = this._handleEntry.bind(this);
    this._handleMovement = this._handleMovement.bind(this);
    var _options$el = options.el,
        el = _options$el === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$el,
        _options$zoomPane = options.zoomPane,
        zoomPane = _options$zoomPane === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$zoomPane,
        _options$sourceAttrib = options.sourceAttribute,
        sourceAttribute = _options$sourceAttrib === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$sourceAttrib,
        _options$handleTouch = options.handleTouch,
        handleTouch = _options$handleTouch === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$handleTouch,
        _options$onShow = options.onShow,
        onShow = _options$onShow === void 0 ? null : _options$onShow,
        _options$onHide = options.onHide,
        onHide = _options$onHide === void 0 ? null : _options$onHide,
        _options$hoverDelay = options.hoverDelay,
        hoverDelay = _options$hoverDelay === void 0 ? 0 : _options$hoverDelay,
        _options$touchDelay = options.touchDelay,
        touchDelay = _options$touchDelay === void 0 ? 0 : _options$touchDelay,
        _options$hoverBoundin = options.hoverBoundingBox,
        hoverBoundingBox = _options$hoverBoundin === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$hoverBoundin,
        _options$touchBoundin = options.touchBoundingBox,
        touchBoundingBox = _options$touchBoundin === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$touchBoundin,
        _options$namespace = options.namespace,
        namespace = _options$namespace === void 0 ? null : _options$namespace,
        _options$zoomFactor = options.zoomFactor,
        zoomFactor = _options$zoomFactor === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$zoomFactor,
        _options$boundingBoxC = options.boundingBoxContainer,
        boundingBoxContainer = _options$boundingBoxC === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$boundingBoxC;
    this.settings = {
      el: el,
      zoomPane: zoomPane,
      sourceAttribute: sourceAttribute,
      handleTouch: handleTouch,
      onShow: onShow,
      onHide: onHide,
      hoverDelay: hoverDelay,
      touchDelay: touchDelay,
      hoverBoundingBox: hoverBoundingBox,
      touchBoundingBox: touchBoundingBox,
      namespace: namespace,
      zoomFactor: zoomFactor,
      boundingBoxContainer: boundingBoxContainer
    };

    if (this.settings.hoverBoundingBox || this.settings.touchBoundingBox) {
      this.boundingBox = new __WEBPACK_IMPORTED_MODULE_1__BoundingBox__["a" /* default */]({
        namespace: this.settings.namespace,
        zoomFactor: this.settings.zoomFactor,
        containerEl: this.settings.boundingBoxContainer
      });
    }

    this.enabled = true;

    this._bindEvents();
  }

  _createClass(Trigger, [{
    key: "_preventDefault",
    value: function _preventDefault(event) {
      event.preventDefault();
    }
  }, {
    key: "_preventDefaultAllowTouchScroll",
    value: function _preventDefaultAllowTouchScroll(event) {
      if (!this.settings.touchDelay || !this._isTouchEvent(event) || this.isShowing) {
        event.preventDefault();
      }
    }
  }, {
    key: "_isTouchEvent",
    value: function _isTouchEvent(event) {
      return !!event.touches;
    }
  }, {
    key: "_bindEvents",
    value: function _bindEvents() {
      this.settings.el.addEventListener("mouseenter", this._handleEntry, false);
      this.settings.el.addEventListener("mouseleave", this._hide, false);
      this.settings.el.addEventListener("mousemove", this._handleMovement, false);

      if (this.settings.handleTouch) {
        this.settings.el.addEventListener("touchstart", this._handleEntry, false);
        this.settings.el.addEventListener("touchend", this._hide, false);
        this.settings.el.addEventListener("touchmove", this._handleMovement, false);
      } else {
        this.settings.el.addEventListener("touchstart", this._preventDefault, false);
        this.settings.el.addEventListener("touchend", this._preventDefault, false);
        this.settings.el.addEventListener("touchmove", this._preventDefault, false);
      }
    }
  }, {
    key: "_unbindEvents",
    value: function _unbindEvents() {
      this.settings.el.removeEventListener("mouseenter", this._handleEntry, false);
      this.settings.el.removeEventListener("mouseleave", this._hide, false);
      this.settings.el.removeEventListener("mousemove", this._handleMovement, false);

      if (this.settings.handleTouch) {
        this.settings.el.removeEventListener("touchstart", this._handleEntry, false);
        this.settings.el.removeEventListener("touchend", this._hide, false);
        this.settings.el.removeEventListener("touchmove", this._handleMovement, false);
      } else {
        this.settings.el.removeEventListener("touchstart", this._preventDefault, false);
        this.settings.el.removeEventListener("touchend", this._preventDefault, false);
        this.settings.el.removeEventListener("touchmove", this._preventDefault, false);
      }
    }
  }, {
    key: "_handleEntry",
    value: function _handleEntry(e) {
      this._preventDefaultAllowTouchScroll(e);

      this._lastMovement = e;

      if (e.type == "mouseenter" && this.settings.hoverDelay) {
        this.entryTimeout = setTimeout(this._show, this.settings.hoverDelay);
      } else if (this.settings.touchDelay) {
        this.entryTimeout = setTimeout(this._show, this.settings.touchDelay);
      } else {
        this._show();
      }
    }
  }, {
    key: "_show",
    value: function _show() {
      if (!this.enabled) {
        return;
      }

      var onShow = this.settings.onShow;

      if (onShow && typeof onShow === "function") {
        onShow();
      }

      this.settings.zoomPane.show(this.settings.el.getAttribute(this.settings.sourceAttribute), this.settings.el.clientWidth, this.settings.el.clientHeight);

      if (this._lastMovement) {
        var touchActivated = this._lastMovement.touches;

        if (touchActivated && this.settings.touchBoundingBox || !touchActivated && this.settings.hoverBoundingBox) {
          this.boundingBox.show(this.settings.zoomPane.el.clientWidth, this.settings.zoomPane.el.clientHeight);
        }
      }

      this._handleMovement();
    }
  }, {
    key: "_hide",
    value: function _hide(e) {
      if (e) {
        this._preventDefaultAllowTouchScroll(e);
      }

      this._lastMovement = null;

      if (this.entryTimeout) {
        clearTimeout(this.entryTimeout);
      }

      if (this.boundingBox) {
        this.boundingBox.hide();
      }

      var onHide = this.settings.onHide;

      if (onHide && typeof onHide === "function") {
        onHide();
      }

      this.settings.zoomPane.hide();
    }
  }, {
    key: "_handleMovement",
    value: function _handleMovement(e) {
      if (e) {
        this._preventDefaultAllowTouchScroll(e);

        this._lastMovement = e;
      } else if (this._lastMovement) {
        e = this._lastMovement;
      } else {
        return;
      }

      var movementX;
      var movementY;

      if (e.touches) {
        var firstTouch = e.touches[0];
        movementX = firstTouch.clientX;
        movementY = firstTouch.clientY;
      } else {
        movementX = e.clientX;
        movementY = e.clientY;
      }

      var el = this.settings.el;
      var rect = el.getBoundingClientRect();
      var offsetX = movementX - rect.left;
      var offsetY = movementY - rect.top;
      var percentageOffsetX = offsetX / this.settings.el.clientWidth;
      var percentageOffsetY = offsetY / this.settings.el.clientHeight;

      if (this.boundingBox) {
        this.boundingBox.setPosition(percentageOffsetX, percentageOffsetY, rect);
      }

      this.settings.zoomPane.setPosition(percentageOffsetX, percentageOffsetY, rect);
    }
  }, {
    key: "isShowing",
    get: function get() {
      return this.settings.zoomPane.isShowing;
    }
  }]);

  return Trigger;
}();


//# sourceMappingURL=Trigger.js.map

/***/ }),
/* 308 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return BoundingBox; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__ = __webpack_require__(100);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__util_dom__ = __webpack_require__(99);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }




var BoundingBox =
/*#__PURE__*/
function () {
  function BoundingBox(options) {
    _classCallCheck(this, BoundingBox);

    this.isShowing = false;
    var _options$namespace = options.namespace,
        namespace = _options$namespace === void 0 ? null : _options$namespace,
        _options$zoomFactor = options.zoomFactor,
        zoomFactor = _options$zoomFactor === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$zoomFactor,
        _options$containerEl = options.containerEl,
        containerEl = _options$containerEl === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$containerEl;
    this.settings = {
      namespace: namespace,
      zoomFactor: zoomFactor,
      containerEl: containerEl
    };
    this.openClasses = this._buildClasses("open");

    this._buildElement();
  }

  _createClass(BoundingBox, [{
    key: "_buildClasses",
    value: function _buildClasses(suffix) {
      var classes = ["drift-".concat(suffix)];
      var ns = this.settings.namespace;

      if (ns) {
        classes.push("".concat(ns, "-").concat(suffix));
      }

      return classes;
    }
  }, {
    key: "_buildElement",
    value: function _buildElement() {
      this.el = document.createElement("div");
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(this.el, this._buildClasses("bounding-box"));
    }
  }, {
    key: "show",
    value: function show(zoomPaneWidth, zoomPaneHeight) {
      this.isShowing = true;
      this.settings.containerEl.appendChild(this.el);
      var style = this.el.style;
      style.width = "".concat(Math.round(zoomPaneWidth / this.settings.zoomFactor), "px");
      style.height = "".concat(Math.round(zoomPaneHeight / this.settings.zoomFactor), "px");
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(this.el, this.openClasses);
    }
  }, {
    key: "hide",
    value: function hide() {
      if (this.isShowing) {
        this.settings.containerEl.removeChild(this.el);
      }

      this.isShowing = false;
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.openClasses);
    }
  }, {
    key: "setPosition",
    value: function setPosition(percentageOffsetX, percentageOffsetY, triggerRect) {
      var pageXOffset = window.pageXOffset;
      var pageYOffset = window.pageYOffset;
      var inlineLeft = triggerRect.left + percentageOffsetX * triggerRect.width - this.el.clientWidth / 2 + pageXOffset;
      var inlineTop = triggerRect.top + percentageOffsetY * triggerRect.height - this.el.clientHeight / 2 + pageYOffset;

      if (inlineLeft < triggerRect.left + pageXOffset) {
        inlineLeft = triggerRect.left + pageXOffset;
      } else if (inlineLeft + this.el.clientWidth > triggerRect.left + triggerRect.width + pageXOffset) {
        inlineLeft = triggerRect.left + triggerRect.width - this.el.clientWidth + pageXOffset;
      }

      if (inlineTop < triggerRect.top + pageYOffset) {
        inlineTop = triggerRect.top + pageYOffset;
      } else if (inlineTop + this.el.clientHeight > triggerRect.top + triggerRect.height + pageYOffset) {
        inlineTop = triggerRect.top + triggerRect.height - this.el.clientHeight + pageYOffset;
      }

      this.el.style.left = "".concat(inlineLeft, "px");
      this.el.style.top = "".concat(inlineTop, "px");
    }
  }]);

  return BoundingBox;
}();


//# sourceMappingURL=BoundingBox.js.map

/***/ }),
/* 309 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ZoomPane; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__ = __webpack_require__(100);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__util_dom__ = __webpack_require__(99);
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }


 // All officially-supported browsers have this, but it's easy to
// account for, just in case.

var divStyle = document.createElement("div").style;
var HAS_ANIMATION = typeof document === "undefined" ? false : "animation" in divStyle || "webkitAnimation" in divStyle;

var ZoomPane =
/*#__PURE__*/
function () {
  function ZoomPane() {
    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

    _classCallCheck(this, ZoomPane);

    this._completeShow = this._completeShow.bind(this);
    this._completeHide = this._completeHide.bind(this);
    this._handleLoad = this._handleLoad.bind(this);
    this.isShowing = false;
    var _options$container = options.container,
        container = _options$container === void 0 ? null : _options$container,
        _options$zoomFactor = options.zoomFactor,
        zoomFactor = _options$zoomFactor === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$zoomFactor,
        _options$inline = options.inline,
        inline = _options$inline === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$inline,
        _options$namespace = options.namespace,
        namespace = _options$namespace === void 0 ? null : _options$namespace,
        _options$showWhitespa = options.showWhitespaceAtEdges,
        showWhitespaceAtEdges = _options$showWhitespa === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$showWhitespa,
        _options$containInlin = options.containInline,
        containInline = _options$containInlin === void 0 ? Object(__WEBPACK_IMPORTED_MODULE_0__util_throwIfMissing__["a" /* default */])() : _options$containInlin,
        _options$inlineOffset = options.inlineOffsetX,
        inlineOffsetX = _options$inlineOffset === void 0 ? 0 : _options$inlineOffset,
        _options$inlineOffset2 = options.inlineOffsetY,
        inlineOffsetY = _options$inlineOffset2 === void 0 ? 0 : _options$inlineOffset2,
        _options$inlineContai = options.inlineContainer,
        inlineContainer = _options$inlineContai === void 0 ? document.body : _options$inlineContai;
    this.settings = {
      container: container,
      zoomFactor: zoomFactor,
      inline: inline,
      namespace: namespace,
      showWhitespaceAtEdges: showWhitespaceAtEdges,
      containInline: containInline,
      inlineOffsetX: inlineOffsetX,
      inlineOffsetY: inlineOffsetY,
      inlineContainer: inlineContainer
    };
    this.openClasses = this._buildClasses("open");
    this.openingClasses = this._buildClasses("opening");
    this.closingClasses = this._buildClasses("closing");
    this.inlineClasses = this._buildClasses("inline");
    this.loadingClasses = this._buildClasses("loading");

    this._buildElement();
  }

  _createClass(ZoomPane, [{
    key: "_buildClasses",
    value: function _buildClasses(suffix) {
      var classes = ["drift-".concat(suffix)];
      var ns = this.settings.namespace;

      if (ns) {
        classes.push("".concat(ns, "-").concat(suffix));
      }

      return classes;
    }
  }, {
    key: "_buildElement",
    value: function _buildElement() {
      this.el = document.createElement("div");
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(this.el, this._buildClasses("zoom-pane"));
      var loaderEl = document.createElement("div");
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(loaderEl, this._buildClasses("zoom-pane-loader"));
      this.el.appendChild(loaderEl);
      this.imgEl = document.createElement("img");
      this.el.appendChild(this.imgEl);
    }
  }, {
    key: "_setImageURL",
    value: function _setImageURL(imageURL) {
      this.imgEl.setAttribute("src", imageURL);
    }
  }, {
    key: "_setImageSize",
    value: function _setImageSize(triggerWidth, triggerHeight) {
      this.imgEl.style.width = "".concat(triggerWidth * this.settings.zoomFactor, "px");
      this.imgEl.style.height = "".concat(triggerHeight * this.settings.zoomFactor, "px");
    } // `percentageOffsetX` and `percentageOffsetY` must be percentages
    // expressed as floats between `0' and `1`.

  }, {
    key: "setPosition",
    value: function setPosition(percentageOffsetX, percentageOffsetY, triggerRect) {
      var imgElWidth = this.imgEl.offsetWidth;
      var imgElHeight = this.imgEl.offsetHeight;
      var elWidth = this.el.offsetWidth;
      var elHeight = this.el.offsetHeight;
      var centreOfContainerX = elWidth / 2;
      var centreOfContainerY = elHeight / 2;
      var targetImgXToBeCentre = imgElWidth * percentageOffsetX;
      var targetImgYToBeCentre = imgElHeight * percentageOffsetY;
      var left = centreOfContainerX - targetImgXToBeCentre;
      var top = centreOfContainerY - targetImgYToBeCentre;
      var differenceBetweenContainerWidthAndImgWidth = elWidth - imgElWidth;
      var differenceBetweenContainerHeightAndImgHeight = elHeight - imgElHeight;
      var isContainerLargerThanImgX = differenceBetweenContainerWidthAndImgWidth > 0;
      var isContainerLargerThanImgY = differenceBetweenContainerHeightAndImgHeight > 0;
      var minLeft = isContainerLargerThanImgX ? differenceBetweenContainerWidthAndImgWidth / 2 : 0;
      var minTop = isContainerLargerThanImgY ? differenceBetweenContainerHeightAndImgHeight / 2 : 0;
      var maxLeft = isContainerLargerThanImgX ? differenceBetweenContainerWidthAndImgWidth / 2 : differenceBetweenContainerWidthAndImgWidth;
      var maxTop = isContainerLargerThanImgY ? differenceBetweenContainerHeightAndImgHeight / 2 : differenceBetweenContainerHeightAndImgHeight;

      if (this.el.parentElement === this.settings.inlineContainer) {
        // This may be needed in the future to deal with browser event
        // inconsistencies, but it's difficult to tell for sure.
        // let scrollX = isTouch ? 0 : window.scrollX;
        // let scrollY = isTouch ? 0 : window.scrollY;
        var scrollX = window.pageXOffset;
        var scrollY = window.pageYOffset;
        var inlineLeft = triggerRect.left + percentageOffsetX * triggerRect.width - elWidth / 2 + this.settings.inlineOffsetX + scrollX;
        var inlineTop = triggerRect.top + percentageOffsetY * triggerRect.height - elHeight / 2 + this.settings.inlineOffsetY + scrollY;

        if (this.settings.containInline) {
          if (inlineLeft < triggerRect.left + scrollX) {
            inlineLeft = triggerRect.left + scrollX;
          } else if (inlineLeft + elWidth > triggerRect.left + triggerRect.width + scrollX) {
            inlineLeft = triggerRect.left + triggerRect.width - elWidth + scrollX;
          }

          if (inlineTop < triggerRect.top + scrollY) {
            inlineTop = triggerRect.top + scrollY;
          } else if (inlineTop + elHeight > triggerRect.top + triggerRect.height + scrollY) {
            inlineTop = triggerRect.top + triggerRect.height - elHeight + scrollY;
          }
        }

        this.el.style.left = "".concat(inlineLeft, "px");
        this.el.style.top = "".concat(inlineTop, "px");
      }

      if (!this.settings.showWhitespaceAtEdges) {
        if (left > minLeft) {
          left = minLeft;
        } else if (left < maxLeft) {
          left = maxLeft;
        }

        if (top > minTop) {
          top = minTop;
        } else if (top < maxTop) {
          top = maxTop;
        }
      }

      this.imgEl.style.transform = "translate(".concat(left, "px, ").concat(top, "px)");
      this.imgEl.style.webkitTransform = "translate(".concat(left, "px, ").concat(top, "px)");
    }
  }, {
    key: "_removeListenersAndResetClasses",
    value: function _removeListenersAndResetClasses() {
      this.el.removeEventListener("animationend", this._completeShow, false);
      this.el.removeEventListener("animationend", this._completeHide, false);
      this.el.removeEventListener("webkitAnimationEnd", this._completeShow, false);
      this.el.removeEventListener("webkitAnimationEnd", this._completeHide, false);
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.openClasses);
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.closingClasses);
    }
  }, {
    key: "show",
    value: function show(imageURL, triggerWidth, triggerHeight) {
      this._removeListenersAndResetClasses();

      this.isShowing = true;
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(this.el, this.openClasses);

      if (this.imgEl.getAttribute("src") != imageURL) {
        Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(this.el, this.loadingClasses);
        this.imgEl.addEventListener("load", this._handleLoad, false);

        this._setImageURL(imageURL);
      }

      this._setImageSize(triggerWidth, triggerHeight);

      if (this._isInline) {
        this._showInline();
      } else {
        this._showInContainer();
      }

      if (HAS_ANIMATION) {
        this.el.addEventListener("animationend", this._completeShow, false);
        this.el.addEventListener("webkitAnimationEnd", this._completeShow, false);
        Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(this.el, this.openingClasses);
      }
    }
  }, {
    key: "_showInline",
    value: function _showInline() {
      this.settings.inlineContainer.appendChild(this.el);
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(this.el, this.inlineClasses);
    }
  }, {
    key: "_showInContainer",
    value: function _showInContainer() {
      this.settings.container.appendChild(this.el);
    }
  }, {
    key: "hide",
    value: function hide() {
      this._removeListenersAndResetClasses();

      this.isShowing = false;

      if (HAS_ANIMATION) {
        this.el.addEventListener("animationend", this._completeHide, false);
        this.el.addEventListener("webkitAnimationEnd", this._completeHide, false);
        Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["a" /* addClasses */])(this.el, this.closingClasses);
      } else {
        Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.openClasses);
        Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.inlineClasses);
      }
    }
  }, {
    key: "_completeShow",
    value: function _completeShow() {
      this.el.removeEventListener("animationend", this._completeShow, false);
      this.el.removeEventListener("webkitAnimationEnd", this._completeShow, false);
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.openingClasses);
    }
  }, {
    key: "_completeHide",
    value: function _completeHide() {
      this.el.removeEventListener("animationend", this._completeHide, false);
      this.el.removeEventListener("webkitAnimationEnd", this._completeHide, false);
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.openClasses);
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.closingClasses);
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.inlineClasses);
      this.el.setAttribute("style", ""); // The window could have been resized above or below `inline`
      // limits since the ZoomPane was shown. Because of this, we
      // can't rely on `this._isInline` here.

      if (this.el.parentElement === this.settings.container) {
        this.settings.container.removeChild(this.el);
      } else if (this.el.parentElement === this.settings.inlineContainer) {
        this.settings.inlineContainer.removeChild(this.el);
      }
    }
  }, {
    key: "_handleLoad",
    value: function _handleLoad() {
      this.imgEl.removeEventListener("load", this._handleLoad, false);
      Object(__WEBPACK_IMPORTED_MODULE_1__util_dom__["c" /* removeClasses */])(this.el, this.loadingClasses);
    }
  }, {
    key: "_isInline",
    get: function get() {
      var inline = this.settings.inline;
      return inline === true || typeof inline === "number" && window.innerWidth <= inline;
    }
  }]);

  return ZoomPane;
}();


//# sourceMappingURL=ZoomPane.js.map

/***/ }),
/* 310 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _quickViewDialog = _interopRequireDefault(__webpack_require__(162));

var _pagination = _interopRequireDefault(__webpack_require__(390));

/**
 * @module Buttons Buttons Buttons
 * @description Clearing house to load all public button functionality.
 */
var init = function init() {
  (0, _quickViewDialog.default)();
  (0, _pagination.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 311 */
/***/ (function(module, exports) {

/** Error message constants. */
var FUNC_ERROR_TEXT = 'Expected a function';

/**
 * The base implementation of `_.delay` and `_.defer` which accepts `args`
 * to provide to `func`.
 *
 * @private
 * @param {Function} func The function to delay.
 * @param {number} wait The number of milliseconds to delay invocation.
 * @param {Array} args The arguments to provide to `func`.
 * @returns {number|Object} Returns the timer id or timeout object.
 */
function baseDelay(func, wait, args) {
  if (typeof func != 'function') {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  return setTimeout(function() { func.apply(undefined, args); }, wait);
}

module.exports = baseDelay;


/***/ }),
/* 312 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(313);

/***/ }),
/* 313 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(63);
__webpack_require__(60);
module.exports = __webpack_require__(314);


/***/ }),
/* 314 */
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__(103);
var ITERATOR = __webpack_require__(6)('iterator');
var Iterators = __webpack_require__(31);
module.exports = __webpack_require__(3).isIterable = function (it) {
  var O = Object(it);
  return O[ITERATOR] !== undefined
    || '@@iterator' in O
    // eslint-disable-next-line no-prototype-builtins
    || Iterators.hasOwnProperty(classof(O));
};


/***/ }),
/* 315 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(316);

/***/ }),
/* 316 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(63);
__webpack_require__(60);
module.exports = __webpack_require__(317);


/***/ }),
/* 317 */
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__(14);
var get = __webpack_require__(164);
module.exports = __webpack_require__(3).getIterator = function (it) {
  var iterFn = get(it);
  if (typeof iterFn != 'function') throw TypeError(it + ' is not iterable!');
  return anObject(iterFn.call(it));
};


/***/ }),
/* 318 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(319);
module.exports = __webpack_require__(3).Object.entries;


/***/ }),
/* 319 */
/***/ (function(module, exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-values-entries
var $export = __webpack_require__(10);
var $entries = __webpack_require__(161)(true);

$export($export.S, 'Object', {
  entries: function entries(it) {
    return $entries(it);
  }
});


/***/ }),
/* 320 */
/***/ (function(module, exports, __webpack_require__) {

var baseDifference = __webpack_require__(321),
    baseFlatten = __webpack_require__(346),
    baseRest = __webpack_require__(75),
    isArrayLikeObject = __webpack_require__(348);

/**
 * Creates an array of `array` values not included in the other given arrays
 * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
 * for equality comparisons. The order and references of result values are
 * determined by the first array.
 *
 * **Note:** Unlike `_.pullAll`, this method returns a new array.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Array
 * @param {Array} array The array to inspect.
 * @param {...Array} [values] The values to exclude.
 * @returns {Array} Returns the new array of filtered values.
 * @see _.without, _.xor
 * @example
 *
 * _.difference([2, 1], [2, 3]);
 * // => [1]
 */
var difference = baseRest(function(array, values) {
  return isArrayLikeObject(array)
    ? baseDifference(array, baseFlatten(values, 1, isArrayLikeObject, true))
    : [];
});

module.exports = difference;


/***/ }),
/* 321 */
/***/ (function(module, exports, __webpack_require__) {

var SetCache = __webpack_require__(165),
    arrayIncludes = __webpack_require__(341),
    arrayIncludesWith = __webpack_require__(345),
    arrayMap = __webpack_require__(136),
    baseUnary = __webpack_require__(121),
    cacheHas = __webpack_require__(167);

/** Used as the size to enable large array optimizations. */
var LARGE_ARRAY_SIZE = 200;

/**
 * The base implementation of methods like `_.difference` without support
 * for excluding multiple arrays or iteratee shorthands.
 *
 * @private
 * @param {Array} array The array to inspect.
 * @param {Array} values The values to exclude.
 * @param {Function} [iteratee] The iteratee invoked per element.
 * @param {Function} [comparator] The comparator invoked per element.
 * @returns {Array} Returns the new array of filtered values.
 */
function baseDifference(array, values, iteratee, comparator) {
  var index = -1,
      includes = arrayIncludes,
      isCommon = true,
      length = array.length,
      result = [],
      valuesLength = values.length;

  if (!length) {
    return result;
  }
  if (iteratee) {
    values = arrayMap(values, baseUnary(iteratee));
  }
  if (comparator) {
    includes = arrayIncludesWith;
    isCommon = false;
  }
  else if (values.length >= LARGE_ARRAY_SIZE) {
    includes = cacheHas;
    isCommon = false;
    values = new SetCache(values);
  }
  outer:
  while (++index < length) {
    var value = array[index],
        computed = iteratee == null ? value : iteratee(value);

    value = (comparator || value !== 0) ? value : 0;
    if (isCommon && computed === computed) {
      var valuesIndex = valuesLength;
      while (valuesIndex--) {
        if (values[valuesIndex] === computed) {
          continue outer;
        }
      }
      result.push(value);
    }
    else if (!includes(values, computed, comparator)) {
      result.push(value);
    }
  }
  return result;
}

module.exports = baseDifference;


/***/ }),
/* 322 */
/***/ (function(module, exports, __webpack_require__) {

var Hash = __webpack_require__(323),
    ListCache = __webpack_require__(67),
    Map = __webpack_require__(105);

/**
 * Removes all key-value entries from the map.
 *
 * @private
 * @name clear
 * @memberOf MapCache
 */
function mapCacheClear() {
  this.size = 0;
  this.__data__ = {
    'hash': new Hash,
    'map': new (Map || ListCache),
    'string': new Hash
  };
}

module.exports = mapCacheClear;


/***/ }),
/* 323 */
/***/ (function(module, exports, __webpack_require__) {

var hashClear = __webpack_require__(324),
    hashDelete = __webpack_require__(325),
    hashGet = __webpack_require__(326),
    hashHas = __webpack_require__(327),
    hashSet = __webpack_require__(328);

/**
 * Creates a hash object.
 *
 * @private
 * @constructor
 * @param {Array} [entries] The key-value pairs to cache.
 */
function Hash(entries) {
  var index = -1,
      length = entries == null ? 0 : entries.length;

  this.clear();
  while (++index < length) {
    var entry = entries[index];
    this.set(entry[0], entry[1]);
  }
}

// Add methods to `Hash`.
Hash.prototype.clear = hashClear;
Hash.prototype['delete'] = hashDelete;
Hash.prototype.get = hashGet;
Hash.prototype.has = hashHas;
Hash.prototype.set = hashSet;

module.exports = Hash;


/***/ }),
/* 324 */
/***/ (function(module, exports, __webpack_require__) {

var nativeCreate = __webpack_require__(66);

/**
 * Removes all key-value entries from the hash.
 *
 * @private
 * @name clear
 * @memberOf Hash
 */
function hashClear() {
  this.__data__ = nativeCreate ? nativeCreate(null) : {};
  this.size = 0;
}

module.exports = hashClear;


/***/ }),
/* 325 */
/***/ (function(module, exports) {

/**
 * Removes `key` and its value from the hash.
 *
 * @private
 * @name delete
 * @memberOf Hash
 * @param {Object} hash The hash to modify.
 * @param {string} key The key of the value to remove.
 * @returns {boolean} Returns `true` if the entry was removed, else `false`.
 */
function hashDelete(key) {
  var result = this.has(key) && delete this.__data__[key];
  this.size -= result ? 1 : 0;
  return result;
}

module.exports = hashDelete;


/***/ }),
/* 326 */
/***/ (function(module, exports, __webpack_require__) {

var nativeCreate = __webpack_require__(66);

/** Used to stand-in for `undefined` hash values. */
var HASH_UNDEFINED = '__lodash_hash_undefined__';

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Gets the hash value for `key`.
 *
 * @private
 * @name get
 * @memberOf Hash
 * @param {string} key The key of the value to get.
 * @returns {*} Returns the entry value.
 */
function hashGet(key) {
  var data = this.__data__;
  if (nativeCreate) {
    var result = data[key];
    return result === HASH_UNDEFINED ? undefined : result;
  }
  return hasOwnProperty.call(data, key) ? data[key] : undefined;
}

module.exports = hashGet;


/***/ }),
/* 327 */
/***/ (function(module, exports, __webpack_require__) {

var nativeCreate = __webpack_require__(66);

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * Checks if a hash value for `key` exists.
 *
 * @private
 * @name has
 * @memberOf Hash
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */
function hashHas(key) {
  var data = this.__data__;
  return nativeCreate ? (data[key] !== undefined) : hasOwnProperty.call(data, key);
}

module.exports = hashHas;


/***/ }),
/* 328 */
/***/ (function(module, exports, __webpack_require__) {

var nativeCreate = __webpack_require__(66);

/** Used to stand-in for `undefined` hash values. */
var HASH_UNDEFINED = '__lodash_hash_undefined__';

/**
 * Sets the hash `key` to `value`.
 *
 * @private
 * @name set
 * @memberOf Hash
 * @param {string} key The key of the value to set.
 * @param {*} value The value to set.
 * @returns {Object} Returns the hash instance.
 */
function hashSet(key, value) {
  var data = this.__data__;
  this.size += this.has(key) ? 0 : 1;
  data[key] = (nativeCreate && value === undefined) ? HASH_UNDEFINED : value;
  return this;
}

module.exports = hashSet;


/***/ }),
/* 329 */
/***/ (function(module, exports) {

/**
 * Removes all key-value entries from the list cache.
 *
 * @private
 * @name clear
 * @memberOf ListCache
 */
function listCacheClear() {
  this.__data__ = [];
  this.size = 0;
}

module.exports = listCacheClear;


/***/ }),
/* 330 */
/***/ (function(module, exports, __webpack_require__) {

var assocIndexOf = __webpack_require__(68);

/** Used for built-in method references. */
var arrayProto = Array.prototype;

/** Built-in value references. */
var splice = arrayProto.splice;

/**
 * Removes `key` and its value from the list cache.
 *
 * @private
 * @name delete
 * @memberOf ListCache
 * @param {string} key The key of the value to remove.
 * @returns {boolean} Returns `true` if the entry was removed, else `false`.
 */
function listCacheDelete(key) {
  var data = this.__data__,
      index = assocIndexOf(data, key);

  if (index < 0) {
    return false;
  }
  var lastIndex = data.length - 1;
  if (index == lastIndex) {
    data.pop();
  } else {
    splice.call(data, index, 1);
  }
  --this.size;
  return true;
}

module.exports = listCacheDelete;


/***/ }),
/* 331 */
/***/ (function(module, exports, __webpack_require__) {

var assocIndexOf = __webpack_require__(68);

/**
 * Gets the list cache value for `key`.
 *
 * @private
 * @name get
 * @memberOf ListCache
 * @param {string} key The key of the value to get.
 * @returns {*} Returns the entry value.
 */
function listCacheGet(key) {
  var data = this.__data__,
      index = assocIndexOf(data, key);

  return index < 0 ? undefined : data[index][1];
}

module.exports = listCacheGet;


/***/ }),
/* 332 */
/***/ (function(module, exports, __webpack_require__) {

var assocIndexOf = __webpack_require__(68);

/**
 * Checks if a list cache value for `key` exists.
 *
 * @private
 * @name has
 * @memberOf ListCache
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */
function listCacheHas(key) {
  return assocIndexOf(this.__data__, key) > -1;
}

module.exports = listCacheHas;


/***/ }),
/* 333 */
/***/ (function(module, exports, __webpack_require__) {

var assocIndexOf = __webpack_require__(68);

/**
 * Sets the list cache `key` to `value`.
 *
 * @private
 * @name set
 * @memberOf ListCache
 * @param {string} key The key of the value to set.
 * @param {*} value The value to set.
 * @returns {Object} Returns the list cache instance.
 */
function listCacheSet(key, value) {
  var data = this.__data__,
      index = assocIndexOf(data, key);

  if (index < 0) {
    ++this.size;
    data.push([key, value]);
  } else {
    data[index][1] = value;
  }
  return this;
}

module.exports = listCacheSet;


/***/ }),
/* 334 */
/***/ (function(module, exports, __webpack_require__) {

var getMapData = __webpack_require__(69);

/**
 * Removes `key` and its value from the map.
 *
 * @private
 * @name delete
 * @memberOf MapCache
 * @param {string} key The key of the value to remove.
 * @returns {boolean} Returns `true` if the entry was removed, else `false`.
 */
function mapCacheDelete(key) {
  var result = getMapData(this, key)['delete'](key);
  this.size -= result ? 1 : 0;
  return result;
}

module.exports = mapCacheDelete;


/***/ }),
/* 335 */
/***/ (function(module, exports) {

/**
 * Checks if `value` is suitable for use as unique object key.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is suitable, else `false`.
 */
function isKeyable(value) {
  var type = typeof value;
  return (type == 'string' || type == 'number' || type == 'symbol' || type == 'boolean')
    ? (value !== '__proto__')
    : (value === null);
}

module.exports = isKeyable;


/***/ }),
/* 336 */
/***/ (function(module, exports, __webpack_require__) {

var getMapData = __webpack_require__(69);

/**
 * Gets the map value for `key`.
 *
 * @private
 * @name get
 * @memberOf MapCache
 * @param {string} key The key of the value to get.
 * @returns {*} Returns the entry value.
 */
function mapCacheGet(key) {
  return getMapData(this, key).get(key);
}

module.exports = mapCacheGet;


/***/ }),
/* 337 */
/***/ (function(module, exports, __webpack_require__) {

var getMapData = __webpack_require__(69);

/**
 * Checks if a map value for `key` exists.
 *
 * @private
 * @name has
 * @memberOf MapCache
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */
function mapCacheHas(key) {
  return getMapData(this, key).has(key);
}

module.exports = mapCacheHas;


/***/ }),
/* 338 */
/***/ (function(module, exports, __webpack_require__) {

var getMapData = __webpack_require__(69);

/**
 * Sets the map `key` to `value`.
 *
 * @private
 * @name set
 * @memberOf MapCache
 * @param {string} key The key of the value to set.
 * @param {*} value The value to set.
 * @returns {Object} Returns the map cache instance.
 */
function mapCacheSet(key, value) {
  var data = getMapData(this, key),
      size = data.size;

  data.set(key, value);
  this.size += data.size == size ? 0 : 1;
  return this;
}

module.exports = mapCacheSet;


/***/ }),
/* 339 */
/***/ (function(module, exports) {

/** Used to stand-in for `undefined` hash values. */
var HASH_UNDEFINED = '__lodash_hash_undefined__';

/**
 * Adds `value` to the array cache.
 *
 * @private
 * @name add
 * @memberOf SetCache
 * @alias push
 * @param {*} value The value to cache.
 * @returns {Object} Returns the cache instance.
 */
function setCacheAdd(value) {
  this.__data__.set(value, HASH_UNDEFINED);
  return this;
}

module.exports = setCacheAdd;


/***/ }),
/* 340 */
/***/ (function(module, exports) {

/**
 * Checks if `value` is in the array cache.
 *
 * @private
 * @name has
 * @memberOf SetCache
 * @param {*} value The value to search for.
 * @returns {number} Returns `true` if `value` is found, else `false`.
 */
function setCacheHas(value) {
  return this.__data__.has(value);
}

module.exports = setCacheHas;


/***/ }),
/* 341 */
/***/ (function(module, exports, __webpack_require__) {

var baseIndexOf = __webpack_require__(342);

/**
 * A specialized version of `_.includes` for arrays without support for
 * specifying an index to search from.
 *
 * @private
 * @param {Array} [array] The array to inspect.
 * @param {*} target The value to search for.
 * @returns {boolean} Returns `true` if `target` is found, else `false`.
 */
function arrayIncludes(array, value) {
  var length = array == null ? 0 : array.length;
  return !!length && baseIndexOf(array, value, 0) > -1;
}

module.exports = arrayIncludes;


/***/ }),
/* 342 */
/***/ (function(module, exports, __webpack_require__) {

var baseFindIndex = __webpack_require__(166),
    baseIsNaN = __webpack_require__(343),
    strictIndexOf = __webpack_require__(344);

/**
 * The base implementation of `_.indexOf` without `fromIndex` bounds checks.
 *
 * @private
 * @param {Array} array The array to inspect.
 * @param {*} value The value to search for.
 * @param {number} fromIndex The index to search from.
 * @returns {number} Returns the index of the matched value, else `-1`.
 */
function baseIndexOf(array, value, fromIndex) {
  return value === value
    ? strictIndexOf(array, value, fromIndex)
    : baseFindIndex(array, baseIsNaN, fromIndex);
}

module.exports = baseIndexOf;


/***/ }),
/* 343 */
/***/ (function(module, exports) {

/**
 * The base implementation of `_.isNaN` without support for number objects.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is `NaN`, else `false`.
 */
function baseIsNaN(value) {
  return value !== value;
}

module.exports = baseIsNaN;


/***/ }),
/* 344 */
/***/ (function(module, exports) {

/**
 * A specialized version of `_.indexOf` which performs strict equality
 * comparisons of values, i.e. `===`.
 *
 * @private
 * @param {Array} array The array to inspect.
 * @param {*} value The value to search for.
 * @param {number} fromIndex The index to search from.
 * @returns {number} Returns the index of the matched value, else `-1`.
 */
function strictIndexOf(array, value, fromIndex) {
  var index = fromIndex - 1,
      length = array.length;

  while (++index < length) {
    if (array[index] === value) {
      return index;
    }
  }
  return -1;
}

module.exports = strictIndexOf;


/***/ }),
/* 345 */
/***/ (function(module, exports) {

/**
 * This function is like `arrayIncludes` except that it accepts a comparator.
 *
 * @private
 * @param {Array} [array] The array to inspect.
 * @param {*} target The value to search for.
 * @param {Function} comparator The comparator invoked per element.
 * @returns {boolean} Returns `true` if `target` is found, else `false`.
 */
function arrayIncludesWith(array, value, comparator) {
  var index = -1,
      length = array == null ? 0 : array.length;

  while (++index < length) {
    if (comparator(value, array[index])) {
      return true;
    }
  }
  return false;
}

module.exports = arrayIncludesWith;


/***/ }),
/* 346 */
/***/ (function(module, exports, __webpack_require__) {

var arrayPush = __webpack_require__(168),
    isFlattenable = __webpack_require__(347);

/**
 * The base implementation of `_.flatten` with support for restricting flattening.
 *
 * @private
 * @param {Array} array The array to flatten.
 * @param {number} depth The maximum recursion depth.
 * @param {boolean} [predicate=isFlattenable] The function invoked per iteration.
 * @param {boolean} [isStrict] Restrict to values that pass `predicate` checks.
 * @param {Array} [result=[]] The initial result value.
 * @returns {Array} Returns the new flattened array.
 */
function baseFlatten(array, depth, predicate, isStrict, result) {
  var index = -1,
      length = array.length;

  predicate || (predicate = isFlattenable);
  result || (result = []);

  while (++index < length) {
    var value = array[index];
    if (depth > 0 && predicate(value)) {
      if (depth > 1) {
        // Recursively flatten arrays (susceptible to call stack limits).
        baseFlatten(value, depth - 1, predicate, isStrict, result);
      } else {
        arrayPush(result, value);
      }
    } else if (!isStrict) {
      result[result.length] = value;
    }
  }
  return result;
}

module.exports = baseFlatten;


/***/ }),
/* 347 */
/***/ (function(module, exports, __webpack_require__) {

var Symbol = __webpack_require__(36),
    isArguments = __webpack_require__(51),
    isArray = __webpack_require__(12);

/** Built-in value references. */
var spreadableSymbol = Symbol ? Symbol.isConcatSpreadable : undefined;

/**
 * Checks if `value` is a flattenable `arguments` object or array.
 *
 * @private
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is flattenable, else `false`.
 */
function isFlattenable(value) {
  return isArray(value) || isArguments(value) ||
    !!(spreadableSymbol && value && value[spreadableSymbol]);
}

module.exports = isFlattenable;


/***/ }),
/* 348 */
/***/ (function(module, exports, __webpack_require__) {

var isArrayLike = __webpack_require__(29),
    isObjectLike = __webpack_require__(28);

/**
 * This method is like `_.isArrayLike` except that it also checks if `value`
 * is an object.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to check.
 * @returns {boolean} Returns `true` if `value` is an array-like object,
 *  else `false`.
 * @example
 *
 * _.isArrayLikeObject([1, 2, 3]);
 * // => true
 *
 * _.isArrayLikeObject(document.body.children);
 * // => true
 *
 * _.isArrayLikeObject('abc');
 * // => false
 *
 * _.isArrayLikeObject(_.noop);
 * // => false
 */
function isArrayLikeObject(value) {
  return isObjectLike(value) && isArrayLike(value);
}

module.exports = isArrayLikeObject;


/***/ }),
/* 349 */
/***/ (function(module, exports, __webpack_require__) {

var baseFindIndex = __webpack_require__(166),
    baseIteratee = __webpack_require__(169),
    toInteger = __webpack_require__(384);

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeMax = Math.max;

/**
 * This method is like `_.find` except that it returns the index of the first
 * element `predicate` returns truthy for instead of the element itself.
 *
 * @static
 * @memberOf _
 * @since 1.1.0
 * @category Array
 * @param {Array} array The array to inspect.
 * @param {Function} [predicate=_.identity] The function invoked per iteration.
 * @param {number} [fromIndex=0] The index to search from.
 * @returns {number} Returns the index of the found element, else `-1`.
 * @example
 *
 * var users = [
 *   { 'user': 'barney',  'active': false },
 *   { 'user': 'fred',    'active': false },
 *   { 'user': 'pebbles', 'active': true }
 * ];
 *
 * _.findIndex(users, function(o) { return o.user == 'barney'; });
 * // => 0
 *
 * // The `_.matches` iteratee shorthand.
 * _.findIndex(users, { 'user': 'fred', 'active': false });
 * // => 1
 *
 * // The `_.matchesProperty` iteratee shorthand.
 * _.findIndex(users, ['active', false]);
 * // => 0
 *
 * // The `_.property` iteratee shorthand.
 * _.findIndex(users, 'active');
 * // => 2
 */
function findIndex(array, predicate, fromIndex) {
  var length = array == null ? 0 : array.length;
  if (!length) {
    return -1;
  }
  var index = fromIndex == null ? 0 : toInteger(fromIndex);
  if (index < 0) {
    index = nativeMax(length + index, 0);
  }
  return baseFindIndex(array, baseIteratee(predicate, 3), index);
}

module.exports = findIndex;


/***/ }),
/* 350 */
/***/ (function(module, exports, __webpack_require__) {

var baseIsMatch = __webpack_require__(351),
    getMatchData = __webpack_require__(372),
    matchesStrictComparable = __webpack_require__(176);

/**
 * The base implementation of `_.matches` which doesn't clone `source`.
 *
 * @private
 * @param {Object} source The object of property values to match.
 * @returns {Function} Returns the new spec function.
 */
function baseMatches(source) {
  var matchData = getMatchData(source);
  if (matchData.length == 1 && matchData[0][2]) {
    return matchesStrictComparable(matchData[0][0], matchData[0][1]);
  }
  return function(object) {
    return object === source || baseIsMatch(object, source, matchData);
  };
}

module.exports = baseMatches;


/***/ }),
/* 351 */
/***/ (function(module, exports, __webpack_require__) {

var Stack = __webpack_require__(170),
    baseIsEqual = __webpack_require__(171);

/** Used to compose bitmasks for value comparisons. */
var COMPARE_PARTIAL_FLAG = 1,
    COMPARE_UNORDERED_FLAG = 2;

/**
 * The base implementation of `_.isMatch` without support for iteratee shorthands.
 *
 * @private
 * @param {Object} object The object to inspect.
 * @param {Object} source The object of property values to match.
 * @param {Array} matchData The property names, values, and compare flags to match.
 * @param {Function} [customizer] The function to customize comparisons.
 * @returns {boolean} Returns `true` if `object` is a match, else `false`.
 */
function baseIsMatch(object, source, matchData, customizer) {
  var index = matchData.length,
      length = index,
      noCustomizer = !customizer;

  if (object == null) {
    return !length;
  }
  object = Object(object);
  while (index--) {
    var data = matchData[index];
    if ((noCustomizer && data[2])
          ? data[1] !== object[data[0]]
          : !(data[0] in object)
        ) {
      return false;
    }
  }
  while (++index < length) {
    data = matchData[index];
    var key = data[0],
        objValue = object[key],
        srcValue = data[1];

    if (noCustomizer && data[2]) {
      if (objValue === undefined && !(key in object)) {
        return false;
      }
    } else {
      var stack = new Stack;
      if (customizer) {
        var result = customizer(objValue, srcValue, key, object, source, stack);
      }
      if (!(result === undefined
            ? baseIsEqual(srcValue, objValue, COMPARE_PARTIAL_FLAG | COMPARE_UNORDERED_FLAG, customizer, stack)
            : result
          )) {
        return false;
      }
    }
  }
  return true;
}

module.exports = baseIsMatch;


/***/ }),
/* 352 */
/***/ (function(module, exports, __webpack_require__) {

var ListCache = __webpack_require__(67);

/**
 * Removes all key-value entries from the stack.
 *
 * @private
 * @name clear
 * @memberOf Stack
 */
function stackClear() {
  this.__data__ = new ListCache;
  this.size = 0;
}

module.exports = stackClear;


/***/ }),
/* 353 */
/***/ (function(module, exports) {

/**
 * Removes `key` and its value from the stack.
 *
 * @private
 * @name delete
 * @memberOf Stack
 * @param {string} key The key of the value to remove.
 * @returns {boolean} Returns `true` if the entry was removed, else `false`.
 */
function stackDelete(key) {
  var data = this.__data__,
      result = data['delete'](key);

  this.size = data.size;
  return result;
}

module.exports = stackDelete;


/***/ }),
/* 354 */
/***/ (function(module, exports) {

/**
 * Gets the stack value for `key`.
 *
 * @private
 * @name get
 * @memberOf Stack
 * @param {string} key The key of the value to get.
 * @returns {*} Returns the entry value.
 */
function stackGet(key) {
  return this.__data__.get(key);
}

module.exports = stackGet;


/***/ }),
/* 355 */
/***/ (function(module, exports) {

/**
 * Checks if a stack value for `key` exists.
 *
 * @private
 * @name has
 * @memberOf Stack
 * @param {string} key The key of the entry to check.
 * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
 */
function stackHas(key) {
  return this.__data__.has(key);
}

module.exports = stackHas;


/***/ }),
/* 356 */
/***/ (function(module, exports, __webpack_require__) {

var ListCache = __webpack_require__(67),
    Map = __webpack_require__(105),
    MapCache = __webpack_require__(104);

/** Used as the size to enable large array optimizations. */
var LARGE_ARRAY_SIZE = 200;

/**
 * Sets the stack `key` to `value`.
 *
 * @private
 * @name set
 * @memberOf Stack
 * @param {string} key The key of the value to set.
 * @param {*} value The value to set.
 * @returns {Object} Returns the stack cache instance.
 */
function stackSet(key, value) {
  var data = this.__data__;
  if (data instanceof ListCache) {
    var pairs = data.__data__;
    if (!Map || (pairs.length < LARGE_ARRAY_SIZE - 1)) {
      pairs.push([key, value]);
      this.size = ++data.size;
      return this;
    }
    data = this.__data__ = new MapCache(pairs);
  }
  data.set(key, value);
  this.size = data.size;
  return this;
}

module.exports = stackSet;


/***/ }),
/* 357 */
/***/ (function(module, exports, __webpack_require__) {

var Stack = __webpack_require__(170),
    equalArrays = __webpack_require__(172),
    equalByTag = __webpack_require__(358),
    equalObjects = __webpack_require__(362),
    getTag = __webpack_require__(174),
    isArray = __webpack_require__(12),
    isBuffer = __webpack_require__(80),
    isTypedArray = __webpack_require__(81);

/** Used to compose bitmasks for value comparisons. */
var COMPARE_PARTIAL_FLAG = 1;

/** `Object#toString` result references. */
var argsTag = '[object Arguments]',
    arrayTag = '[object Array]',
    objectTag = '[object Object]';

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * A specialized version of `baseIsEqual` for arrays and objects which performs
 * deep comparisons and tracks traversed objects enabling objects with circular
 * references to be compared.
 *
 * @private
 * @param {Object} object The object to compare.
 * @param {Object} other The other object to compare.
 * @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
 * @param {Function} customizer The function to customize comparisons.
 * @param {Function} equalFunc The function to determine equivalents of values.
 * @param {Object} [stack] Tracks traversed `object` and `other` objects.
 * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
 */
function baseIsEqualDeep(object, other, bitmask, customizer, equalFunc, stack) {
  var objIsArr = isArray(object),
      othIsArr = isArray(other),
      objTag = objIsArr ? arrayTag : getTag(object),
      othTag = othIsArr ? arrayTag : getTag(other);

  objTag = objTag == argsTag ? objectTag : objTag;
  othTag = othTag == argsTag ? objectTag : othTag;

  var objIsObj = objTag == objectTag,
      othIsObj = othTag == objectTag,
      isSameTag = objTag == othTag;

  if (isSameTag && isBuffer(object)) {
    if (!isBuffer(other)) {
      return false;
    }
    objIsArr = true;
    objIsObj = false;
  }
  if (isSameTag && !objIsObj) {
    stack || (stack = new Stack);
    return (objIsArr || isTypedArray(object))
      ? equalArrays(object, other, bitmask, customizer, equalFunc, stack)
      : equalByTag(object, other, objTag, bitmask, customizer, equalFunc, stack);
  }
  if (!(bitmask & COMPARE_PARTIAL_FLAG)) {
    var objIsWrapped = objIsObj && hasOwnProperty.call(object, '__wrapped__'),
        othIsWrapped = othIsObj && hasOwnProperty.call(other, '__wrapped__');

    if (objIsWrapped || othIsWrapped) {
      var objUnwrapped = objIsWrapped ? object.value() : object,
          othUnwrapped = othIsWrapped ? other.value() : other;

      stack || (stack = new Stack);
      return equalFunc(objUnwrapped, othUnwrapped, bitmask, customizer, stack);
    }
  }
  if (!isSameTag) {
    return false;
  }
  stack || (stack = new Stack);
  return equalObjects(object, other, bitmask, customizer, equalFunc, stack);
}

module.exports = baseIsEqualDeep;


/***/ }),
/* 358 */
/***/ (function(module, exports, __webpack_require__) {

var Symbol = __webpack_require__(36),
    Uint8Array = __webpack_require__(359),
    eq = __webpack_require__(49),
    equalArrays = __webpack_require__(172),
    mapToArray = __webpack_require__(360),
    setToArray = __webpack_require__(361);

/** Used to compose bitmasks for value comparisons. */
var COMPARE_PARTIAL_FLAG = 1,
    COMPARE_UNORDERED_FLAG = 2;

/** `Object#toString` result references. */
var boolTag = '[object Boolean]',
    dateTag = '[object Date]',
    errorTag = '[object Error]',
    mapTag = '[object Map]',
    numberTag = '[object Number]',
    regexpTag = '[object RegExp]',
    setTag = '[object Set]',
    stringTag = '[object String]',
    symbolTag = '[object Symbol]';

var arrayBufferTag = '[object ArrayBuffer]',
    dataViewTag = '[object DataView]';

/** Used to convert symbols to primitives and strings. */
var symbolProto = Symbol ? Symbol.prototype : undefined,
    symbolValueOf = symbolProto ? symbolProto.valueOf : undefined;

/**
 * A specialized version of `baseIsEqualDeep` for comparing objects of
 * the same `toStringTag`.
 *
 * **Note:** This function only supports comparing values with tags of
 * `Boolean`, `Date`, `Error`, `Number`, `RegExp`, or `String`.
 *
 * @private
 * @param {Object} object The object to compare.
 * @param {Object} other The other object to compare.
 * @param {string} tag The `toStringTag` of the objects to compare.
 * @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
 * @param {Function} customizer The function to customize comparisons.
 * @param {Function} equalFunc The function to determine equivalents of values.
 * @param {Object} stack Tracks traversed `object` and `other` objects.
 * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
 */
function equalByTag(object, other, tag, bitmask, customizer, equalFunc, stack) {
  switch (tag) {
    case dataViewTag:
      if ((object.byteLength != other.byteLength) ||
          (object.byteOffset != other.byteOffset)) {
        return false;
      }
      object = object.buffer;
      other = other.buffer;

    case arrayBufferTag:
      if ((object.byteLength != other.byteLength) ||
          !equalFunc(new Uint8Array(object), new Uint8Array(other))) {
        return false;
      }
      return true;

    case boolTag:
    case dateTag:
    case numberTag:
      // Coerce booleans to `1` or `0` and dates to milliseconds.
      // Invalid dates are coerced to `NaN`.
      return eq(+object, +other);

    case errorTag:
      return object.name == other.name && object.message == other.message;

    case regexpTag:
    case stringTag:
      // Coerce regexes to strings and treat strings, primitives and objects,
      // as equal. See http://www.ecma-international.org/ecma-262/7.0/#sec-regexp.prototype.tostring
      // for more details.
      return object == (other + '');

    case mapTag:
      var convert = mapToArray;

    case setTag:
      var isPartial = bitmask & COMPARE_PARTIAL_FLAG;
      convert || (convert = setToArray);

      if (object.size != other.size && !isPartial) {
        return false;
      }
      // Assume cyclic values are equal.
      var stacked = stack.get(object);
      if (stacked) {
        return stacked == other;
      }
      bitmask |= COMPARE_UNORDERED_FLAG;

      // Recursively compare objects (susceptible to call stack limits).
      stack.set(object, other);
      var result = equalArrays(convert(object), convert(other), bitmask, customizer, equalFunc, stack);
      stack['delete'](object);
      return result;

    case symbolTag:
      if (symbolValueOf) {
        return symbolValueOf.call(object) == symbolValueOf.call(other);
      }
  }
  return false;
}

module.exports = equalByTag;


/***/ }),
/* 359 */
/***/ (function(module, exports, __webpack_require__) {

var root = __webpack_require__(13);

/** Built-in value references. */
var Uint8Array = root.Uint8Array;

module.exports = Uint8Array;


/***/ }),
/* 360 */
/***/ (function(module, exports) {

/**
 * Converts `map` to its key-value pairs.
 *
 * @private
 * @param {Object} map The map to convert.
 * @returns {Array} Returns the key-value pairs.
 */
function mapToArray(map) {
  var index = -1,
      result = Array(map.size);

  map.forEach(function(value, key) {
    result[++index] = [key, value];
  });
  return result;
}

module.exports = mapToArray;


/***/ }),
/* 361 */
/***/ (function(module, exports) {

/**
 * Converts `set` to an array of its values.
 *
 * @private
 * @param {Object} set The set to convert.
 * @returns {Array} Returns the values.
 */
function setToArray(set) {
  var index = -1,
      result = Array(set.size);

  set.forEach(function(value) {
    result[++index] = value;
  });
  return result;
}

module.exports = setToArray;


/***/ }),
/* 362 */
/***/ (function(module, exports, __webpack_require__) {

var getAllKeys = __webpack_require__(363);

/** Used to compose bitmasks for value comparisons. */
var COMPARE_PARTIAL_FLAG = 1;

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Used to check objects for own properties. */
var hasOwnProperty = objectProto.hasOwnProperty;

/**
 * A specialized version of `baseIsEqualDeep` for objects with support for
 * partial deep comparisons.
 *
 * @private
 * @param {Object} object The object to compare.
 * @param {Object} other The other object to compare.
 * @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
 * @param {Function} customizer The function to customize comparisons.
 * @param {Function} equalFunc The function to determine equivalents of values.
 * @param {Object} stack Tracks traversed `object` and `other` objects.
 * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
 */
function equalObjects(object, other, bitmask, customizer, equalFunc, stack) {
  var isPartial = bitmask & COMPARE_PARTIAL_FLAG,
      objProps = getAllKeys(object),
      objLength = objProps.length,
      othProps = getAllKeys(other),
      othLength = othProps.length;

  if (objLength != othLength && !isPartial) {
    return false;
  }
  var index = objLength;
  while (index--) {
    var key = objProps[index];
    if (!(isPartial ? key in other : hasOwnProperty.call(other, key))) {
      return false;
    }
  }
  // Assume cyclic values are equal.
  var stacked = stack.get(object);
  if (stacked && stack.get(other)) {
    return stacked == other;
  }
  var result = true;
  stack.set(object, other);
  stack.set(other, object);

  var skipCtor = isPartial;
  while (++index < objLength) {
    key = objProps[index];
    var objValue = object[key],
        othValue = other[key];

    if (customizer) {
      var compared = isPartial
        ? customizer(othValue, objValue, key, other, object, stack)
        : customizer(objValue, othValue, key, object, other, stack);
    }
    // Recursively compare objects (susceptible to call stack limits).
    if (!(compared === undefined
          ? (objValue === othValue || equalFunc(objValue, othValue, bitmask, customizer, stack))
          : compared
        )) {
      result = false;
      break;
    }
    skipCtor || (skipCtor = key == 'constructor');
  }
  if (result && !skipCtor) {
    var objCtor = object.constructor,
        othCtor = other.constructor;

    // Non `Object` object instances with different constructors are not equal.
    if (objCtor != othCtor &&
        ('constructor' in object && 'constructor' in other) &&
        !(typeof objCtor == 'function' && objCtor instanceof objCtor &&
          typeof othCtor == 'function' && othCtor instanceof othCtor)) {
      result = false;
    }
  }
  stack['delete'](object);
  stack['delete'](other);
  return result;
}

module.exports = equalObjects;


/***/ }),
/* 363 */
/***/ (function(module, exports, __webpack_require__) {

var baseGetAllKeys = __webpack_require__(364),
    getSymbols = __webpack_require__(365),
    keys = __webpack_require__(50);

/**
 * Creates an array of own enumerable property names and symbols of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of property names and symbols.
 */
function getAllKeys(object) {
  return baseGetAllKeys(object, keys, getSymbols);
}

module.exports = getAllKeys;


/***/ }),
/* 364 */
/***/ (function(module, exports, __webpack_require__) {

var arrayPush = __webpack_require__(168),
    isArray = __webpack_require__(12);

/**
 * The base implementation of `getAllKeys` and `getAllKeysIn` which uses
 * `keysFunc` and `symbolsFunc` to get the enumerable property names and
 * symbols of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {Function} keysFunc The function to get the keys of `object`.
 * @param {Function} symbolsFunc The function to get the symbols of `object`.
 * @returns {Array} Returns the array of property names and symbols.
 */
function baseGetAllKeys(object, keysFunc, symbolsFunc) {
  var result = keysFunc(object);
  return isArray(object) ? result : arrayPush(result, symbolsFunc(object));
}

module.exports = baseGetAllKeys;


/***/ }),
/* 365 */
/***/ (function(module, exports, __webpack_require__) {

var arrayFilter = __webpack_require__(366),
    stubArray = __webpack_require__(367);

/** Used for built-in method references. */
var objectProto = Object.prototype;

/** Built-in value references. */
var propertyIsEnumerable = objectProto.propertyIsEnumerable;

/* Built-in method references for those with the same name as other `lodash` methods. */
var nativeGetSymbols = Object.getOwnPropertySymbols;

/**
 * Creates an array of the own enumerable symbols of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the array of symbols.
 */
var getSymbols = !nativeGetSymbols ? stubArray : function(object) {
  if (object == null) {
    return [];
  }
  object = Object(object);
  return arrayFilter(nativeGetSymbols(object), function(symbol) {
    return propertyIsEnumerable.call(object, symbol);
  });
};

module.exports = getSymbols;


/***/ }),
/* 366 */
/***/ (function(module, exports) {

/**
 * A specialized version of `_.filter` for arrays without support for
 * iteratee shorthands.
 *
 * @private
 * @param {Array} [array] The array to iterate over.
 * @param {Function} predicate The function invoked per iteration.
 * @returns {Array} Returns the new filtered array.
 */
function arrayFilter(array, predicate) {
  var index = -1,
      length = array == null ? 0 : array.length,
      resIndex = 0,
      result = [];

  while (++index < length) {
    var value = array[index];
    if (predicate(value, index, array)) {
      result[resIndex++] = value;
    }
  }
  return result;
}

module.exports = arrayFilter;


/***/ }),
/* 367 */
/***/ (function(module, exports) {

/**
 * This method returns a new empty array.
 *
 * @static
 * @memberOf _
 * @since 4.13.0
 * @category Util
 * @returns {Array} Returns the new empty array.
 * @example
 *
 * var arrays = _.times(2, _.stubArray);
 *
 * console.log(arrays);
 * // => [[], []]
 *
 * console.log(arrays[0] === arrays[1]);
 * // => false
 */
function stubArray() {
  return [];
}

module.exports = stubArray;


/***/ }),
/* 368 */
/***/ (function(module, exports, __webpack_require__) {

var getNative = __webpack_require__(20),
    root = __webpack_require__(13);

/* Built-in method references that are verified to be native. */
var DataView = getNative(root, 'DataView');

module.exports = DataView;


/***/ }),
/* 369 */
/***/ (function(module, exports, __webpack_require__) {

var getNative = __webpack_require__(20),
    root = __webpack_require__(13);

/* Built-in method references that are verified to be native. */
var Promise = getNative(root, 'Promise');

module.exports = Promise;


/***/ }),
/* 370 */
/***/ (function(module, exports, __webpack_require__) {

var getNative = __webpack_require__(20),
    root = __webpack_require__(13);

/* Built-in method references that are verified to be native. */
var Set = getNative(root, 'Set');

module.exports = Set;


/***/ }),
/* 371 */
/***/ (function(module, exports, __webpack_require__) {

var getNative = __webpack_require__(20),
    root = __webpack_require__(13);

/* Built-in method references that are verified to be native. */
var WeakMap = getNative(root, 'WeakMap');

module.exports = WeakMap;


/***/ }),
/* 372 */
/***/ (function(module, exports, __webpack_require__) {

var isStrictComparable = __webpack_require__(175),
    keys = __webpack_require__(50);

/**
 * Gets the property names, values, and compare flags of `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @returns {Array} Returns the match data of `object`.
 */
function getMatchData(object) {
  var result = keys(object),
      length = result.length;

  while (length--) {
    var key = result[length],
        value = object[key];

    result[length] = [key, value, isStrictComparable(value)];
  }
  return result;
}

module.exports = getMatchData;


/***/ }),
/* 373 */
/***/ (function(module, exports, __webpack_require__) {

var baseIsEqual = __webpack_require__(171),
    get = __webpack_require__(374),
    hasIn = __webpack_require__(378),
    isKey = __webpack_require__(106),
    isStrictComparable = __webpack_require__(175),
    matchesStrictComparable = __webpack_require__(176),
    toKey = __webpack_require__(70);

/** Used to compose bitmasks for value comparisons. */
var COMPARE_PARTIAL_FLAG = 1,
    COMPARE_UNORDERED_FLAG = 2;

/**
 * The base implementation of `_.matchesProperty` which doesn't clone `srcValue`.
 *
 * @private
 * @param {string} path The path of the property to get.
 * @param {*} srcValue The value to match.
 * @returns {Function} Returns the new spec function.
 */
function baseMatchesProperty(path, srcValue) {
  if (isKey(path) && isStrictComparable(srcValue)) {
    return matchesStrictComparable(toKey(path), srcValue);
  }
  return function(object) {
    var objValue = get(object, path);
    return (objValue === undefined && objValue === srcValue)
      ? hasIn(object, path)
      : baseIsEqual(srcValue, objValue, COMPARE_PARTIAL_FLAG | COMPARE_UNORDERED_FLAG);
  };
}

module.exports = baseMatchesProperty;


/***/ }),
/* 374 */
/***/ (function(module, exports, __webpack_require__) {

var baseGet = __webpack_require__(177);

/**
 * Gets the value at `path` of `object`. If the resolved value is
 * `undefined`, the `defaultValue` is returned in its place.
 *
 * @static
 * @memberOf _
 * @since 3.7.0
 * @category Object
 * @param {Object} object The object to query.
 * @param {Array|string} path The path of the property to get.
 * @param {*} [defaultValue] The value returned for `undefined` resolved values.
 * @returns {*} Returns the resolved value.
 * @example
 *
 * var object = { 'a': [{ 'b': { 'c': 3 } }] };
 *
 * _.get(object, 'a[0].b.c');
 * // => 3
 *
 * _.get(object, ['a', '0', 'b', 'c']);
 * // => 3
 *
 * _.get(object, 'a.b.c', 'default');
 * // => 'default'
 */
function get(object, path, defaultValue) {
  var result = object == null ? undefined : baseGet(object, path);
  return result === undefined ? defaultValue : result;
}

module.exports = get;


/***/ }),
/* 375 */
/***/ (function(module, exports, __webpack_require__) {

var memoizeCapped = __webpack_require__(376);

/** Used to match property names within property paths. */
var rePropName = /[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|$))/g;

/** Used to match backslashes in property paths. */
var reEscapeChar = /\\(\\)?/g;

/**
 * Converts `string` to a property path array.
 *
 * @private
 * @param {string} string The string to convert.
 * @returns {Array} Returns the property path array.
 */
var stringToPath = memoizeCapped(function(string) {
  var result = [];
  if (string.charCodeAt(0) === 46 /* . */) {
    result.push('');
  }
  string.replace(rePropName, function(match, number, quote, subString) {
    result.push(quote ? subString.replace(reEscapeChar, '$1') : (number || match));
  });
  return result;
});

module.exports = stringToPath;


/***/ }),
/* 376 */
/***/ (function(module, exports, __webpack_require__) {

var memoize = __webpack_require__(377);

/** Used as the maximum memoize cache size. */
var MAX_MEMOIZE_SIZE = 500;

/**
 * A specialized version of `_.memoize` which clears the memoized function's
 * cache when it exceeds `MAX_MEMOIZE_SIZE`.
 *
 * @private
 * @param {Function} func The function to have its output memoized.
 * @returns {Function} Returns the new memoized function.
 */
function memoizeCapped(func) {
  var result = memoize(func, function(key) {
    if (cache.size === MAX_MEMOIZE_SIZE) {
      cache.clear();
    }
    return key;
  });

  var cache = result.cache;
  return result;
}

module.exports = memoizeCapped;


/***/ }),
/* 377 */
/***/ (function(module, exports, __webpack_require__) {

var MapCache = __webpack_require__(104);

/** Error message constants. */
var FUNC_ERROR_TEXT = 'Expected a function';

/**
 * Creates a function that memoizes the result of `func`. If `resolver` is
 * provided, it determines the cache key for storing the result based on the
 * arguments provided to the memoized function. By default, the first argument
 * provided to the memoized function is used as the map cache key. The `func`
 * is invoked with the `this` binding of the memoized function.
 *
 * **Note:** The cache is exposed as the `cache` property on the memoized
 * function. Its creation may be customized by replacing the `_.memoize.Cache`
 * constructor with one whose instances implement the
 * [`Map`](http://ecma-international.org/ecma-262/7.0/#sec-properties-of-the-map-prototype-object)
 * method interface of `clear`, `delete`, `get`, `has`, and `set`.
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Function
 * @param {Function} func The function to have its output memoized.
 * @param {Function} [resolver] The function to resolve the cache key.
 * @returns {Function} Returns the new memoized function.
 * @example
 *
 * var object = { 'a': 1, 'b': 2 };
 * var other = { 'c': 3, 'd': 4 };
 *
 * var values = _.memoize(_.values);
 * values(object);
 * // => [1, 2]
 *
 * values(other);
 * // => [3, 4]
 *
 * object.a = 2;
 * values(object);
 * // => [1, 2]
 *
 * // Modify the result cache.
 * values.cache.set(object, ['a', 'b']);
 * values(object);
 * // => ['a', 'b']
 *
 * // Replace `_.memoize.Cache`.
 * _.memoize.Cache = WeakMap;
 */
function memoize(func, resolver) {
  if (typeof func != 'function' || (resolver != null && typeof resolver != 'function')) {
    throw new TypeError(FUNC_ERROR_TEXT);
  }
  var memoized = function() {
    var args = arguments,
        key = resolver ? resolver.apply(this, args) : args[0],
        cache = memoized.cache;

    if (cache.has(key)) {
      return cache.get(key);
    }
    var result = func.apply(this, args);
    memoized.cache = cache.set(key, result) || cache;
    return result;
  };
  memoized.cache = new (memoize.Cache || MapCache);
  return memoized;
}

// Expose `MapCache`.
memoize.Cache = MapCache;

module.exports = memoize;


/***/ }),
/* 378 */
/***/ (function(module, exports, __webpack_require__) {

var baseHasIn = __webpack_require__(379),
    hasPath = __webpack_require__(380);

/**
 * Checks if `path` is a direct or inherited property of `object`.
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Object
 * @param {Object} object The object to query.
 * @param {Array|string} path The path to check.
 * @returns {boolean} Returns `true` if `path` exists, else `false`.
 * @example
 *
 * var object = _.create({ 'a': _.create({ 'b': 2 }) });
 *
 * _.hasIn(object, 'a');
 * // => true
 *
 * _.hasIn(object, 'a.b');
 * // => true
 *
 * _.hasIn(object, ['a', 'b']);
 * // => true
 *
 * _.hasIn(object, 'b');
 * // => false
 */
function hasIn(object, path) {
  return object != null && hasPath(object, path, baseHasIn);
}

module.exports = hasIn;


/***/ }),
/* 379 */
/***/ (function(module, exports) {

/**
 * The base implementation of `_.hasIn` without support for deep paths.
 *
 * @private
 * @param {Object} [object] The object to query.
 * @param {Array|string} key The key to check.
 * @returns {boolean} Returns `true` if `key` exists, else `false`.
 */
function baseHasIn(object, key) {
  return object != null && key in Object(object);
}

module.exports = baseHasIn;


/***/ }),
/* 380 */
/***/ (function(module, exports, __webpack_require__) {

var castPath = __webpack_require__(178),
    isArguments = __webpack_require__(51),
    isArray = __webpack_require__(12),
    isIndex = __webpack_require__(78),
    isLength = __webpack_require__(77),
    toKey = __webpack_require__(70);

/**
 * Checks if `path` exists on `object`.
 *
 * @private
 * @param {Object} object The object to query.
 * @param {Array|string} path The path to check.
 * @param {Function} hasFunc The function to check properties.
 * @returns {boolean} Returns `true` if `path` exists, else `false`.
 */
function hasPath(object, path, hasFunc) {
  path = castPath(path, object);

  var index = -1,
      length = path.length,
      result = false;

  while (++index < length) {
    var key = toKey(path[index]);
    if (!(result = object != null && hasFunc(object, key))) {
      break;
    }
    object = object[key];
  }
  if (result || ++index != length) {
    return result;
  }
  length = object == null ? 0 : object.length;
  return !!length && isLength(length) && isIndex(key, length) &&
    (isArray(object) || isArguments(object));
}

module.exports = hasPath;


/***/ }),
/* 381 */
/***/ (function(module, exports, __webpack_require__) {

var baseProperty = __webpack_require__(382),
    basePropertyDeep = __webpack_require__(383),
    isKey = __webpack_require__(106),
    toKey = __webpack_require__(70);

/**
 * Creates a function that returns the value at `path` of a given object.
 *
 * @static
 * @memberOf _
 * @since 2.4.0
 * @category Util
 * @param {Array|string} path The path of the property to get.
 * @returns {Function} Returns the new accessor function.
 * @example
 *
 * var objects = [
 *   { 'a': { 'b': 2 } },
 *   { 'a': { 'b': 1 } }
 * ];
 *
 * _.map(objects, _.property('a.b'));
 * // => [2, 1]
 *
 * _.map(_.sortBy(objects, _.property(['a', 'b'])), 'a.b');
 * // => [1, 2]
 */
function property(path) {
  return isKey(path) ? baseProperty(toKey(path)) : basePropertyDeep(path);
}

module.exports = property;


/***/ }),
/* 382 */
/***/ (function(module, exports) {

/**
 * The base implementation of `_.property` without support for deep paths.
 *
 * @private
 * @param {string} key The key of the property to get.
 * @returns {Function} Returns the new accessor function.
 */
function baseProperty(key) {
  return function(object) {
    return object == null ? undefined : object[key];
  };
}

module.exports = baseProperty;


/***/ }),
/* 383 */
/***/ (function(module, exports, __webpack_require__) {

var baseGet = __webpack_require__(177);

/**
 * A specialized version of `baseProperty` which supports deep paths.
 *
 * @private
 * @param {Array|string} path The path of the property to get.
 * @returns {Function} Returns the new accessor function.
 */
function basePropertyDeep(path) {
  return function(object) {
    return baseGet(object, path);
  };
}

module.exports = basePropertyDeep;


/***/ }),
/* 384 */
/***/ (function(module, exports, __webpack_require__) {

var toFinite = __webpack_require__(385);

/**
 * Converts `value` to an integer.
 *
 * **Note:** This method is loosely based on
 * [`ToInteger`](http://www.ecma-international.org/ecma-262/7.0/#sec-tointeger).
 *
 * @static
 * @memberOf _
 * @since 4.0.0
 * @category Lang
 * @param {*} value The value to convert.
 * @returns {number} Returns the converted integer.
 * @example
 *
 * _.toInteger(3.2);
 * // => 3
 *
 * _.toInteger(Number.MIN_VALUE);
 * // => 0
 *
 * _.toInteger(Infinity);
 * // => 1.7976931348623157e+308
 *
 * _.toInteger('3.2');
 * // => 3
 */
function toInteger(value) {
  var result = toFinite(value),
      remainder = result % 1;

  return result === result ? (remainder ? result - remainder : result) : 0;
}

module.exports = toInteger;


/***/ }),
/* 385 */
/***/ (function(module, exports, __webpack_require__) {

var toNumber = __webpack_require__(74);

/** Used as references for various `Number` constants. */
var INFINITY = 1 / 0,
    MAX_INTEGER = 1.7976931348623157e+308;

/**
 * Converts `value` to a finite number.
 *
 * @static
 * @memberOf _
 * @since 4.12.0
 * @category Lang
 * @param {*} value The value to convert.
 * @returns {number} Returns the converted number.
 * @example
 *
 * _.toFinite(3.2);
 * // => 3.2
 *
 * _.toFinite(Number.MIN_VALUE);
 * // => 5e-324
 *
 * _.toFinite(Infinity);
 * // => 1.7976931348623157e+308
 *
 * _.toFinite('3.2');
 * // => 3.2
 */
function toFinite(value) {
  if (!value) {
    return value === 0 ? value : 0;
  }
  value = toNumber(value);
  if (value === INFINITY || value === -INFINITY) {
    var sign = (value < 0 ? -1 : 1);
    return sign * MAX_INTEGER;
  }
  return value === value ? value : 0;
}

module.exports = toFinite;


/***/ }),
/* 386 */
/***/ (function(module, exports, __webpack_require__) {

/* flatpickr v4.5.1, @license MIT */
(function (global, factory) {
     true ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global.flatpickr = factory());
}(this, (function () { 'use strict';

    var pad = function pad(number) {
      return ("0" + number).slice(-2);
    };
    var int = function int(bool) {
      return bool === true ? 1 : 0;
    };
    function debounce(func, wait, immediate) {
      if (immediate === void 0) {
        immediate = false;
      }

      var timeout;
      return function () {
        var context = this,
            args = arguments;
        timeout !== null && clearTimeout(timeout);
        timeout = window.setTimeout(function () {
          timeout = null;
          if (!immediate) func.apply(context, args);
        }, wait);
        if (immediate && !timeout) func.apply(context, args);
      };
    }
    var arrayify = function arrayify(obj) {
      return obj instanceof Array ? obj : [obj];
    };

    var do_nothing = function do_nothing() {
      return undefined;
    };

    var monthToStr = function monthToStr(monthNumber, shorthand, locale) {
      return locale.months[shorthand ? "shorthand" : "longhand"][monthNumber];
    };
    var revFormat = {
      D: do_nothing,
      F: function F(dateObj, monthName, locale) {
        dateObj.setMonth(locale.months.longhand.indexOf(monthName));
      },
      G: function G(dateObj, hour) {
        dateObj.setHours(parseFloat(hour));
      },
      H: function H(dateObj, hour) {
        dateObj.setHours(parseFloat(hour));
      },
      J: function J(dateObj, day) {
        dateObj.setDate(parseFloat(day));
      },
      K: function K(dateObj, amPM, locale) {
        dateObj.setHours(dateObj.getHours() % 12 + 12 * int(new RegExp(locale.amPM[1], "i").test(amPM)));
      },
      M: function M(dateObj, shortMonth, locale) {
        dateObj.setMonth(locale.months.shorthand.indexOf(shortMonth));
      },
      S: function S(dateObj, seconds) {
        dateObj.setSeconds(parseFloat(seconds));
      },
      U: function U(_, unixSeconds) {
        return new Date(parseFloat(unixSeconds) * 1000);
      },
      W: function W(dateObj, weekNum) {
        var weekNumber = parseInt(weekNum);
        return new Date(dateObj.getFullYear(), 0, 2 + (weekNumber - 1) * 7, 0, 0, 0, 0);
      },
      Y: function Y(dateObj, year) {
        dateObj.setFullYear(parseFloat(year));
      },
      Z: function Z(_, ISODate) {
        return new Date(ISODate);
      },
      d: function d(dateObj, day) {
        dateObj.setDate(parseFloat(day));
      },
      h: function h(dateObj, hour) {
        dateObj.setHours(parseFloat(hour));
      },
      i: function i(dateObj, minutes) {
        dateObj.setMinutes(parseFloat(minutes));
      },
      j: function j(dateObj, day) {
        dateObj.setDate(parseFloat(day));
      },
      l: do_nothing,
      m: function m(dateObj, month) {
        dateObj.setMonth(parseFloat(month) - 1);
      },
      n: function n(dateObj, month) {
        dateObj.setMonth(parseFloat(month) - 1);
      },
      s: function s(dateObj, seconds) {
        dateObj.setSeconds(parseFloat(seconds));
      },
      w: do_nothing,
      y: function y(dateObj, year) {
        dateObj.setFullYear(2000 + parseFloat(year));
      }
    };
    var tokenRegex = {
      D: "(\\w+)",
      F: "(\\w+)",
      G: "(\\d\\d|\\d)",
      H: "(\\d\\d|\\d)",
      J: "(\\d\\d|\\d)\\w+",
      K: "",
      M: "(\\w+)",
      S: "(\\d\\d|\\d)",
      U: "(.+)",
      W: "(\\d\\d|\\d)",
      Y: "(\\d{4})",
      Z: "(.+)",
      d: "(\\d\\d|\\d)",
      h: "(\\d\\d|\\d)",
      i: "(\\d\\d|\\d)",
      j: "(\\d\\d|\\d)",
      l: "(\\w+)",
      m: "(\\d\\d|\\d)",
      n: "(\\d\\d|\\d)",
      s: "(\\d\\d|\\d)",
      w: "(\\d\\d|\\d)",
      y: "(\\d{2})"
    };
    var formats = {
      Z: function Z(date) {
        return date.toISOString();
      },
      D: function D(date, locale, options) {
        return locale.weekdays.shorthand[formats.w(date, locale, options)];
      },
      F: function F(date, locale, options) {
        return monthToStr(formats.n(date, locale, options) - 1, false, locale);
      },
      G: function G(date, locale, options) {
        return pad(formats.h(date, locale, options));
      },
      H: function H(date) {
        return pad(date.getHours());
      },
      J: function J(date, locale) {
        return locale.ordinal !== undefined ? date.getDate() + locale.ordinal(date.getDate()) : date.getDate();
      },
      K: function K(date, locale) {
        return locale.amPM[int(date.getHours() > 11)];
      },
      M: function M(date, locale) {
        return monthToStr(date.getMonth(), true, locale);
      },
      S: function S(date) {
        return pad(date.getSeconds());
      },
      U: function U(date) {
        return date.getTime() / 1000;
      },
      W: function W(date, _, options) {
        return options.getWeek(date);
      },
      Y: function Y(date) {
        return date.getFullYear();
      },
      d: function d(date) {
        return pad(date.getDate());
      },
      h: function h(date) {
        return date.getHours() % 12 ? date.getHours() % 12 : 12;
      },
      i: function i(date) {
        return pad(date.getMinutes());
      },
      j: function j(date) {
        return date.getDate();
      },
      l: function l(date, locale) {
        return locale.weekdays.longhand[date.getDay()];
      },
      m: function m(date) {
        return pad(date.getMonth() + 1);
      },
      n: function n(date) {
        return date.getMonth() + 1;
      },
      s: function s(date) {
        return date.getSeconds();
      },
      w: function w(date) {
        return date.getDay();
      },
      y: function y(date) {
        return String(date.getFullYear()).substring(2);
      }
    };

    var english = {
      weekdays: {
        shorthand: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        longhand: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
      },
      months: {
        shorthand: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        longhand: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
      },
      daysInMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
      firstDayOfWeek: 0,
      ordinal: function ordinal(nth) {
        var s = nth % 100;
        if (s > 3 && s < 21) return "th";

        switch (s % 10) {
          case 1:
            return "st";

          case 2:
            return "nd";

          case 3:
            return "rd";

          default:
            return "th";
        }
      },
      rangeSeparator: " to ",
      weekAbbreviation: "Wk",
      scrollTitle: "Scroll to increment",
      toggleTitle: "Click to toggle",
      amPM: ["AM", "PM"],
      yearAriaLabel: "Year"
    };

    var createDateFormatter = function createDateFormatter(_ref) {
      var _ref$config = _ref.config,
          config = _ref$config === void 0 ? defaults : _ref$config,
          _ref$l10n = _ref.l10n,
          l10n = _ref$l10n === void 0 ? english : _ref$l10n;
      return function (dateObj, frmt, overrideLocale) {
        if (config.formatDate !== undefined) return config.formatDate(dateObj, frmt);
        var locale = overrideLocale || l10n;
        return frmt.split("").map(function (c, i, arr) {
          return formats[c] && arr[i - 1] !== "\\" ? formats[c](dateObj, locale, config) : c !== "\\" ? c : "";
        }).join("");
      };
    };
    var createDateParser = function createDateParser(_ref2) {
      var _ref2$config = _ref2.config,
          config = _ref2$config === void 0 ? defaults : _ref2$config,
          _ref2$l10n = _ref2.l10n,
          l10n = _ref2$l10n === void 0 ? english : _ref2$l10n;
      return function (date, givenFormat, timeless, customLocale) {
        if (date !== 0 && !date) return undefined;
        var locale = customLocale || l10n;
        var parsedDate;
        var date_orig = date;
        if (date instanceof Date) parsedDate = new Date(date.getTime());else if (typeof date !== "string" && date.toFixed !== undefined) parsedDate = new Date(date);else if (typeof date === "string") {
          var format = givenFormat || (config || defaults).dateFormat;
          var datestr = String(date).trim();

          if (datestr === "today") {
            parsedDate = new Date();
            timeless = true;
          } else if (/Z$/.test(datestr) || /GMT$/.test(datestr)) parsedDate = new Date(date);else if (config && config.parseDate) parsedDate = config.parseDate(date, format);else {
            parsedDate = !config || !config.noCalendar ? new Date(new Date().getFullYear(), 0, 1, 0, 0, 0, 0) : new Date(new Date().setHours(0, 0, 0, 0));
            var matched,
                ops = [];

            for (var i = 0, matchIndex = 0, regexStr = ""; i < format.length; i++) {
              var token = format[i];
              var isBackSlash = token === "\\";
              var escaped = format[i - 1] === "\\" || isBackSlash;

              if (tokenRegex[token] && !escaped) {
                regexStr += tokenRegex[token];
                var match = new RegExp(regexStr).exec(date);

                if (match && (matched = true)) {
                  ops[token !== "Y" ? "push" : "unshift"]({
                    fn: revFormat[token],
                    val: match[++matchIndex]
                  });
                }
              } else if (!isBackSlash) regexStr += ".";

              ops.forEach(function (_ref3) {
                var fn = _ref3.fn,
                    val = _ref3.val;
                return parsedDate = fn(parsedDate, val, locale) || parsedDate;
              });
            }

            parsedDate = matched ? parsedDate : undefined;
          }
        }

        if (!(parsedDate instanceof Date && !isNaN(parsedDate.getTime()))) {
          config.errorHandler(new Error("Invalid date provided: " + date_orig));
          return undefined;
        }

        if (timeless === true) parsedDate.setHours(0, 0, 0, 0);
        return parsedDate;
      };
    };
    function compareDates(date1, date2, timeless) {
      if (timeless === void 0) {
        timeless = true;
      }

      if (timeless !== false) {
        return new Date(date1.getTime()).setHours(0, 0, 0, 0) - new Date(date2.getTime()).setHours(0, 0, 0, 0);
      }

      return date1.getTime() - date2.getTime();
    }
    var getWeek = function getWeek(givenDate) {
      var date = new Date(givenDate.getTime());
      date.setHours(0, 0, 0, 0);
      date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
      var week1 = new Date(date.getFullYear(), 0, 4);
      return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
    };
    var isBetween = function isBetween(ts, ts1, ts2) {
      return ts > Math.min(ts1, ts2) && ts < Math.max(ts1, ts2);
    };
    var duration = {
      DAY: 86400000
    };

    var defaults = {
      _disable: [],
      _enable: [],
      allowInput: false,
      altFormat: "F j, Y",
      altInput: false,
      altInputClass: "form-control input",
      animate: typeof window === "object" && window.navigator.userAgent.indexOf("MSIE") === -1,
      ariaDateFormat: "F j, Y",
      clickOpens: true,
      closeOnSelect: true,
      conjunction: ", ",
      dateFormat: "Y-m-d",
      defaultHour: 12,
      defaultMinute: 0,
      defaultSeconds: 0,
      disable: [],
      disableMobile: false,
      enable: [],
      enableSeconds: false,
      enableTime: false,
      errorHandler: function errorHandler(err) {
        return typeof console !== "undefined" && console.warn(err);
      },
      getWeek: getWeek,
      hourIncrement: 1,
      ignoredFocusElements: [],
      inline: false,
      locale: "default",
      minuteIncrement: 5,
      mode: "single",
      nextArrow: "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z' /></svg>",
      noCalendar: false,
      now: new Date(),
      onChange: [],
      onClose: [],
      onDayCreate: [],
      onDestroy: [],
      onKeyDown: [],
      onMonthChange: [],
      onOpen: [],
      onParseConfig: [],
      onReady: [],
      onValueUpdate: [],
      onYearChange: [],
      onPreCalendarPosition: [],
      plugins: [],
      position: "auto",
      positionElement: undefined,
      prevArrow: "<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' viewBox='0 0 17 17'><g></g><path d='M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z' /></svg>",
      shorthandCurrentMonth: false,
      showMonths: 1,
      static: false,
      time_24hr: false,
      weekNumbers: false,
      wrap: false
    };

    function toggleClass(elem, className, bool) {
      if (bool === true) return elem.classList.add(className);
      elem.classList.remove(className);
    }
    function createElement(tag, className, content) {
      var e = window.document.createElement(tag);
      className = className || "";
      content = content || "";
      e.className = className;
      if (content !== undefined) e.textContent = content;
      return e;
    }
    function clearNode(node) {
      while (node.firstChild) {
        node.removeChild(node.firstChild);
      }
    }
    function findParent(node, condition) {
      if (condition(node)) return node;else if (node.parentNode) return findParent(node.parentNode, condition);
      return undefined;
    }
    function createNumberInput(inputClassName, opts) {
      var wrapper = createElement("div", "numInputWrapper"),
          numInput = createElement("input", "numInput " + inputClassName),
          arrowUp = createElement("span", "arrowUp"),
          arrowDown = createElement("span", "arrowDown");
      numInput.type = "text";
      numInput.pattern = "\\d*";
      if (opts !== undefined) for (var key in opts) {
        numInput.setAttribute(key, opts[key]);
      }
      wrapper.appendChild(numInput);
      wrapper.appendChild(arrowUp);
      wrapper.appendChild(arrowDown);
      return wrapper;
    }

    if (typeof Object.assign !== "function") {
      Object.assign = function (target) {
        if (!target) {
          throw TypeError("Cannot convert undefined or null to object");
        }

        for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
          args[_key - 1] = arguments[_key];
        }

        var _loop = function _loop() {
          var source = args[_i];

          if (source) {
            Object.keys(source).forEach(function (key) {
              return target[key] = source[key];
            });
          }
        };

        for (var _i = 0; _i < args.length; _i++) {
          _loop();
        }

        return target;
      };
    }

    var DEBOUNCED_CHANGE_MS = 300;

    function FlatpickrInstance(element, instanceConfig) {
      var self = {
        config: Object.assign({}, flatpickr.defaultConfig),
        l10n: english
      };
      self.parseDate = createDateParser({
        config: self.config,
        l10n: self.l10n
      });
      self._handlers = [];
      self._bind = bind;
      self._setHoursFromDate = setHoursFromDate;
      self._positionCalendar = positionCalendar;
      self.changeMonth = changeMonth;
      self.changeYear = changeYear;
      self.clear = clear;
      self.close = close;
      self._createElement = createElement;
      self.destroy = destroy;
      self.isEnabled = isEnabled;
      self.jumpToDate = jumpToDate;
      self.open = open;
      self.redraw = redraw;
      self.set = set;
      self.setDate = setDate;
      self.toggle = toggle;

      function setupHelperFunctions() {
        self.utils = {
          getDaysInMonth: function getDaysInMonth(month, yr) {
            if (month === void 0) {
              month = self.currentMonth;
            }

            if (yr === void 0) {
              yr = self.currentYear;
            }

            if (month === 1 && (yr % 4 === 0 && yr % 100 !== 0 || yr % 400 === 0)) return 29;
            return self.l10n.daysInMonth[month];
          }
        };
      }

      function init() {
        self.element = self.input = element;
        self.isOpen = false;
        parseConfig();
        setupLocale();
        setupInputs();
        setupDates();
        setupHelperFunctions();
        if (!self.isMobile) build();
        bindEvents();

        if (self.selectedDates.length || self.config.noCalendar) {
          if (self.config.enableTime) {
            setHoursFromDate(self.config.noCalendar ? self.latestSelectedDateObj || self.config.minDate : undefined);
          }

          updateValue(false);
        }

        setCalendarWidth();
        self.showTimeInput = self.selectedDates.length > 0 || self.config.noCalendar;
        var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

        if (!self.isMobile && isSafari) {
          positionCalendar();
        }

        triggerEvent("onReady");
      }

      function bindToInstance(fn) {
        return fn.bind(self);
      }

      function setCalendarWidth() {
        var config = self.config;
        if (config.weekNumbers === false && config.showMonths === 1) return;else if (config.noCalendar !== true) {
          window.requestAnimationFrame(function () {
            self.calendarContainer.style.visibility = "hidden";
            self.calendarContainer.style.display = "block";

            if (self.daysContainer !== undefined) {
              var daysWidth = (self.days.offsetWidth + 1) * config.showMonths;
              self.daysContainer.style.width = daysWidth + "px";
              self.calendarContainer.style.width = daysWidth + (self.weekWrapper !== undefined ? self.weekWrapper.offsetWidth : 0) + "px";
              self.calendarContainer.style.removeProperty("visibility");
              self.calendarContainer.style.removeProperty("display");
            }
          });
        }
      }

      function updateTime(e) {
        if (self.selectedDates.length === 0) return;

        if (e !== undefined && e.type !== "blur") {
          timeWrapper(e);
        }

        var prevValue = self._input.value;
        setHoursFromInputs();
        updateValue();

        if (self._input.value !== prevValue) {
          self._debouncedChange();
        }
      }

      function ampm2military(hour, amPM) {
        return hour % 12 + 12 * int(amPM === self.l10n.amPM[1]);
      }

      function military2ampm(hour) {
        switch (hour % 24) {
          case 0:
          case 12:
            return 12;

          default:
            return hour % 12;
        }
      }

      function setHoursFromInputs() {
        if (self.hourElement === undefined || self.minuteElement === undefined) return;
        var hours = (parseInt(self.hourElement.value.slice(-2), 10) || 0) % 24,
            minutes = (parseInt(self.minuteElement.value, 10) || 0) % 60,
            seconds = self.secondElement !== undefined ? (parseInt(self.secondElement.value, 10) || 0) % 60 : 0;

        if (self.amPM !== undefined) {
          hours = ampm2military(hours, self.amPM.textContent);
        }

        var limitMinHours = self.config.minTime !== undefined || self.config.minDate && self.minDateHasTime && self.latestSelectedDateObj && compareDates(self.latestSelectedDateObj, self.config.minDate, true) === 0;
        var limitMaxHours = self.config.maxTime !== undefined || self.config.maxDate && self.maxDateHasTime && self.latestSelectedDateObj && compareDates(self.latestSelectedDateObj, self.config.maxDate, true) === 0;

        if (limitMaxHours) {
          var maxTime = self.config.maxTime !== undefined ? self.config.maxTime : self.config.maxDate;
          hours = Math.min(hours, maxTime.getHours());
          if (hours === maxTime.getHours()) minutes = Math.min(minutes, maxTime.getMinutes());
          if (minutes === maxTime.getMinutes()) seconds = Math.min(seconds, maxTime.getSeconds());
        }

        if (limitMinHours) {
          var minTime = self.config.minTime !== undefined ? self.config.minTime : self.config.minDate;
          hours = Math.max(hours, minTime.getHours());
          if (hours === minTime.getHours()) minutes = Math.max(minutes, minTime.getMinutes());
          if (minutes === minTime.getMinutes()) seconds = Math.max(seconds, minTime.getSeconds());
        }

        setHours(hours, minutes, seconds);
      }

      function setHoursFromDate(dateObj) {
        var date = dateObj || self.latestSelectedDateObj;
        if (date) setHours(date.getHours(), date.getMinutes(), date.getSeconds());
      }

      function setDefaultHours() {
        var hours = self.config.defaultHour;
        var minutes = self.config.defaultMinute;
        var seconds = self.config.defaultSeconds;

        if (self.config.minDate !== undefined) {
          var min_hr = self.config.minDate.getHours();
          var min_minutes = self.config.minDate.getMinutes();
          hours = Math.max(hours, min_hr);
          if (hours === min_hr) minutes = Math.max(min_minutes, minutes);
          if (hours === min_hr && minutes === min_minutes) seconds = self.config.minDate.getSeconds();
        }

        if (self.config.maxDate !== undefined) {
          var max_hr = self.config.maxDate.getHours();
          var max_minutes = self.config.maxDate.getMinutes();
          hours = Math.min(hours, max_hr);
          if (hours === max_hr) minutes = Math.min(max_minutes, minutes);
          if (hours === max_hr && minutes === max_minutes) seconds = self.config.maxDate.getSeconds();
        }

        setHours(hours, minutes, seconds);
      }

      function setHours(hours, minutes, seconds) {
        if (self.latestSelectedDateObj !== undefined) {
          self.latestSelectedDateObj.setHours(hours % 24, minutes, seconds || 0, 0);
        }

        if (!self.hourElement || !self.minuteElement || self.isMobile) return;
        self.hourElement.value = pad(!self.config.time_24hr ? (12 + hours) % 12 + 12 * int(hours % 12 === 0) : hours);
        self.minuteElement.value = pad(minutes);
        if (self.amPM !== undefined) self.amPM.textContent = self.l10n.amPM[int(hours >= 12)];
        if (self.secondElement !== undefined) self.secondElement.value = pad(seconds);
      }

      function onYearInput(event) {
        var year = parseInt(event.target.value) + (event.delta || 0);

        if (year / 1000 > 1 || event.key === "Enter" && !/[^\d]/.test(year.toString())) {
          changeYear(year);
        }
      }

      function bind(element, event, handler, options) {
        if (event instanceof Array) return event.forEach(function (ev) {
          return bind(element, ev, handler, options);
        });
        if (element instanceof Array) return element.forEach(function (el) {
          return bind(el, event, handler, options);
        });
        element.addEventListener(event, handler, options);

        self._handlers.push({
          element: element,
          event: event,
          handler: handler,
          options: options
        });
      }

      function onClick(handler) {
        return function (evt) {
          evt.which === 1 && handler(evt);
        };
      }

      function triggerChange() {
        triggerEvent("onChange");
      }

      function bindEvents() {
        if (self.config.wrap) {
          ["open", "close", "toggle", "clear"].forEach(function (evt) {
            Array.prototype.forEach.call(self.element.querySelectorAll("[data-" + evt + "]"), function (el) {
              return bind(el, "click", self[evt]);
            });
          });
        }

        if (self.isMobile) {
          setupMobile();
          return;
        }

        var debouncedResize = debounce(onResize, 50);
        self._debouncedChange = debounce(triggerChange, DEBOUNCED_CHANGE_MS);
        if (self.daysContainer && !/iPhone|iPad|iPod/i.test(navigator.userAgent)) bind(self.daysContainer, "mouseover", function (e) {
          if (self.config.mode === "range") onMouseOver(e.target);
        });
        bind(window.document.body, "keydown", onKeyDown);
        if (!self.config.static) bind(self._input, "keydown", onKeyDown);
        if (!self.config.inline && !self.config.static) bind(window, "resize", debouncedResize);
        if (window.ontouchstart !== undefined) bind(window.document, "click", documentClick);else bind(window.document, "mousedown", onClick(documentClick));
        bind(window.document, "focus", documentClick, {
          capture: true
        });

        if (self.config.clickOpens === true) {
          bind(self._input, "focus", self.open);
          bind(self._input, "mousedown", onClick(self.open));
        }

        if (self.daysContainer !== undefined) {
          bind(self.monthNav, "mousedown", onClick(onMonthNavClick));
          bind(self.monthNav, ["keyup", "increment"], onYearInput);
          bind(self.daysContainer, "mousedown", onClick(selectDate));
        }

        if (self.timeContainer !== undefined && self.minuteElement !== undefined && self.hourElement !== undefined) {
          var selText = function selText(e) {
            return e.target.select();
          };

          bind(self.timeContainer, ["increment"], updateTime);
          bind(self.timeContainer, "blur", updateTime, {
            capture: true
          });
          bind(self.timeContainer, "mousedown", onClick(timeIncrement));
          bind([self.hourElement, self.minuteElement], ["focus", "click"], selText);
          if (self.secondElement !== undefined) bind(self.secondElement, "focus", function () {
            return self.secondElement && self.secondElement.select();
          });

          if (self.amPM !== undefined) {
            bind(self.amPM, "mousedown", onClick(function (e) {
              updateTime(e);
              triggerChange();
            }));
          }
        }
      }

      function jumpToDate(jumpDate) {
        var jumpTo = jumpDate !== undefined ? self.parseDate(jumpDate) : self.latestSelectedDateObj || (self.config.minDate && self.config.minDate > self.now ? self.config.minDate : self.config.maxDate && self.config.maxDate < self.now ? self.config.maxDate : self.now);

        try {
          if (jumpTo !== undefined) {
            self.currentYear = jumpTo.getFullYear();
            self.currentMonth = jumpTo.getMonth();
          }
        } catch (e) {
          e.message = "Invalid date supplied: " + jumpTo;
          self.config.errorHandler(e);
        }

        self.redraw();
      }

      function timeIncrement(e) {
        if (~e.target.className.indexOf("arrow")) incrementNumInput(e, e.target.classList.contains("arrowUp") ? 1 : -1);
      }

      function incrementNumInput(e, delta, inputElem) {
        var target = e && e.target;
        var input = inputElem || target && target.parentNode && target.parentNode.firstChild;
        var event = createEvent("increment");
        event.delta = delta;
        input && input.dispatchEvent(event);
      }

      function build() {
        var fragment = window.document.createDocumentFragment();
        self.calendarContainer = createElement("div", "flatpickr-calendar");
        self.calendarContainer.tabIndex = -1;

        if (!self.config.noCalendar) {
          fragment.appendChild(buildMonthNav());
          self.innerContainer = createElement("div", "flatpickr-innerContainer");

          if (self.config.weekNumbers) {
            var _buildWeeks = buildWeeks(),
                weekWrapper = _buildWeeks.weekWrapper,
                weekNumbers = _buildWeeks.weekNumbers;

            self.innerContainer.appendChild(weekWrapper);
            self.weekNumbers = weekNumbers;
            self.weekWrapper = weekWrapper;
          }

          self.rContainer = createElement("div", "flatpickr-rContainer");
          self.rContainer.appendChild(buildWeekdays());

          if (!self.daysContainer) {
            self.daysContainer = createElement("div", "flatpickr-days");
            self.daysContainer.tabIndex = -1;
          }

          buildDays();
          self.rContainer.appendChild(self.daysContainer);
          self.innerContainer.appendChild(self.rContainer);
          fragment.appendChild(self.innerContainer);
        }

        if (self.config.enableTime) {
          fragment.appendChild(buildTime());
        }

        toggleClass(self.calendarContainer, "rangeMode", self.config.mode === "range");
        toggleClass(self.calendarContainer, "animate", self.config.animate === true);
        toggleClass(self.calendarContainer, "multiMonth", self.config.showMonths > 1);
        self.calendarContainer.appendChild(fragment);
        var customAppend = self.config.appendTo !== undefined && self.config.appendTo.nodeType !== undefined;

        if (self.config.inline || self.config.static) {
          self.calendarContainer.classList.add(self.config.inline ? "inline" : "static");

          if (self.config.inline) {
            if (!customAppend && self.element.parentNode) self.element.parentNode.insertBefore(self.calendarContainer, self._input.nextSibling);else if (self.config.appendTo !== undefined) self.config.appendTo.appendChild(self.calendarContainer);
          }

          if (self.config.static) {
            var wrapper = createElement("div", "flatpickr-wrapper");
            if (self.element.parentNode) self.element.parentNode.insertBefore(wrapper, self.element);
            wrapper.appendChild(self.element);
            if (self.altInput) wrapper.appendChild(self.altInput);
            wrapper.appendChild(self.calendarContainer);
          }
        }

        if (!self.config.static && !self.config.inline) (self.config.appendTo !== undefined ? self.config.appendTo : window.document.body).appendChild(self.calendarContainer);
      }

      function createDay(className, date, dayNumber, i) {
        var dateIsEnabled = isEnabled(date, true),
            dayElement = createElement("span", "flatpickr-day " + className, date.getDate().toString());
        dayElement.dateObj = date;
        dayElement.$i = i;
        dayElement.setAttribute("aria-label", self.formatDate(date, self.config.ariaDateFormat));

        if (className.indexOf("hidden") === -1 && compareDates(date, self.now) === 0) {
          self.todayDateElem = dayElement;
          dayElement.classList.add("today");
          dayElement.setAttribute("aria-current", "date");
        }

        if (dateIsEnabled) {
          dayElement.tabIndex = -1;

          if (isDateSelected(date)) {
            dayElement.classList.add("selected");
            self.selectedDateElem = dayElement;

            if (self.config.mode === "range") {
              toggleClass(dayElement, "startRange", self.selectedDates[0] && compareDates(date, self.selectedDates[0], true) === 0);
              toggleClass(dayElement, "endRange", self.selectedDates[1] && compareDates(date, self.selectedDates[1], true) === 0);
              if (className === "nextMonthDay") dayElement.classList.add("inRange");
            }
          }
        } else {
          dayElement.classList.add("disabled");
        }

        if (self.config.mode === "range") {
          if (isDateInRange(date) && !isDateSelected(date)) dayElement.classList.add("inRange");
        }

        if (self.weekNumbers && self.config.showMonths === 1 && className !== "prevMonthDay" && dayNumber % 7 === 1) {
          self.weekNumbers.insertAdjacentHTML("beforeend", "<span class='flatpickr-day'>" + self.config.getWeek(date) + "</span>");
        }

        triggerEvent("onDayCreate", dayElement);
        return dayElement;
      }

      function focusOnDayElem(targetNode) {
        targetNode.focus();
        if (self.config.mode === "range") onMouseOver(targetNode);
      }

      function getFirstAvailableDay(delta) {
        var startMonth = delta > 0 ? 0 : self.config.showMonths - 1;
        var endMonth = delta > 0 ? self.config.showMonths : -1;

        for (var m = startMonth; m != endMonth; m += delta) {
          var month = self.daysContainer.children[m];
          var startIndex = delta > 0 ? 0 : month.children.length - 1;
          var endIndex = delta > 0 ? month.children.length : -1;

          for (var i = startIndex; i != endIndex; i += delta) {
            var c = month.children[i];
            if (c.className.indexOf("hidden") === -1 && isEnabled(c.dateObj)) return c;
          }
        }

        return undefined;
      }

      function getNextAvailableDay(current, delta) {
        var givenMonth = current.className.indexOf("Month") === -1 ? current.dateObj.getMonth() : self.currentMonth;
        var endMonth = delta > 0 ? self.config.showMonths : -1;
        var loopDelta = delta > 0 ? 1 : -1;

        for (var m = givenMonth - self.currentMonth; m != endMonth; m += loopDelta) {
          var month = self.daysContainer.children[m];
          var startIndex = givenMonth - self.currentMonth === m ? current.$i + delta : delta < 0 ? month.children.length - 1 : 0;
          var numMonthDays = month.children.length;

          for (var i = startIndex; i >= 0 && i < numMonthDays && i != (delta > 0 ? numMonthDays : -1); i += loopDelta) {
            var c = month.children[i];
            if (c.className.indexOf("hidden") === -1 && isEnabled(c.dateObj) && Math.abs(current.$i - i) >= Math.abs(delta)) return focusOnDayElem(c);
          }
        }

        self.changeMonth(loopDelta);
        focusOnDay(getFirstAvailableDay(loopDelta), 0);
        return undefined;
      }

      function focusOnDay(current, offset) {
        var dayFocused = isInView(document.activeElement);
        var startElem = current !== undefined ? current : dayFocused ? document.activeElement : self.selectedDateElem !== undefined && isInView(self.selectedDateElem) ? self.selectedDateElem : self.todayDateElem !== undefined && isInView(self.todayDateElem) ? self.todayDateElem : getFirstAvailableDay(offset > 0 ? 1 : -1);
        if (startElem === undefined) return self._input.focus();
        if (!dayFocused) return focusOnDayElem(startElem);
        getNextAvailableDay(startElem, offset);
      }

      function buildMonthDays(year, month) {
        var firstOfMonth = (new Date(year, month, 1).getDay() - self.l10n.firstDayOfWeek + 7) % 7;
        var prevMonthDays = self.utils.getDaysInMonth((month - 1 + 12) % 12);
        var daysInMonth = self.utils.getDaysInMonth(month),
            days = window.document.createDocumentFragment(),
            isMultiMonth = self.config.showMonths > 1,
            prevMonthDayClass = isMultiMonth ? "prevMonthDay hidden" : "prevMonthDay",
            nextMonthDayClass = isMultiMonth ? "nextMonthDay hidden" : "nextMonthDay";
        var dayNumber = prevMonthDays + 1 - firstOfMonth,
            dayIndex = 0;

        for (; dayNumber <= prevMonthDays; dayNumber++, dayIndex++) {
          days.appendChild(createDay(prevMonthDayClass, new Date(year, month - 1, dayNumber), dayNumber, dayIndex));
        }

        for (dayNumber = 1; dayNumber <= daysInMonth; dayNumber++, dayIndex++) {
          days.appendChild(createDay("", new Date(year, month, dayNumber), dayNumber, dayIndex));
        }

        for (var dayNum = daysInMonth + 1; dayNum <= 42 - firstOfMonth && (self.config.showMonths === 1 || dayIndex % 7 !== 0); dayNum++, dayIndex++) {
          days.appendChild(createDay(nextMonthDayClass, new Date(year, month + 1, dayNum % daysInMonth), dayNum, dayIndex));
        }

        var dayContainer = createElement("div", "dayContainer");
        dayContainer.appendChild(days);
        return dayContainer;
      }

      function buildDays() {
        if (self.daysContainer === undefined) {
          return;
        }

        clearNode(self.daysContainer);
        if (self.weekNumbers) clearNode(self.weekNumbers);
        var frag = document.createDocumentFragment();

        for (var i = 0; i < self.config.showMonths; i++) {
          var d = new Date(self.currentYear, self.currentMonth, 1);
          d.setMonth(self.currentMonth + i);
          frag.appendChild(buildMonthDays(d.getFullYear(), d.getMonth()));
        }

        self.daysContainer.appendChild(frag);
        self.days = self.daysContainer.firstChild;

        if (self.config.mode === "range" && self.selectedDates.length === 1) {
          onMouseOver();
        }
      }

      function buildMonth() {
        var container = createElement("div", "flatpickr-month");
        var monthNavFragment = window.document.createDocumentFragment();
        var monthElement = createElement("span", "cur-month");
        var yearInput = createNumberInput("cur-year", {
          tabindex: "-1"
        });
        var yearElement = yearInput.childNodes[0];
        yearElement.setAttribute("aria-label", self.l10n.yearAriaLabel);
        if (self.config.minDate) yearElement.setAttribute("data-min", self.config.minDate.getFullYear().toString());

        if (self.config.maxDate) {
          yearElement.setAttribute("data-max", self.config.maxDate.getFullYear().toString());
          yearElement.disabled = !!self.config.minDate && self.config.minDate.getFullYear() === self.config.maxDate.getFullYear();
        }

        var currentMonth = createElement("div", "flatpickr-current-month");
        currentMonth.appendChild(monthElement);
        currentMonth.appendChild(yearInput);
        monthNavFragment.appendChild(currentMonth);
        container.appendChild(monthNavFragment);
        return {
          container: container,
          yearElement: yearElement,
          monthElement: monthElement
        };
      }

      function buildMonths() {
        clearNode(self.monthNav);
        self.monthNav.appendChild(self.prevMonthNav);

        for (var m = self.config.showMonths; m--;) {
          var month = buildMonth();
          self.yearElements.push(month.yearElement);
          self.monthElements.push(month.monthElement);
          self.monthNav.appendChild(month.container);
        }

        self.monthNav.appendChild(self.nextMonthNav);
      }

      function buildMonthNav() {
        self.monthNav = createElement("div", "flatpickr-months");
        self.yearElements = [];
        self.monthElements = [];
        self.prevMonthNav = createElement("span", "flatpickr-prev-month");
        self.prevMonthNav.innerHTML = self.config.prevArrow;
        self.nextMonthNav = createElement("span", "flatpickr-next-month");
        self.nextMonthNav.innerHTML = self.config.nextArrow;
        buildMonths();
        Object.defineProperty(self, "_hidePrevMonthArrow", {
          get: function get() {
            return self.__hidePrevMonthArrow;
          },
          set: function set(bool) {
            if (self.__hidePrevMonthArrow !== bool) {
              toggleClass(self.prevMonthNav, "disabled", bool);
              self.__hidePrevMonthArrow = bool;
            }
          }
        });
        Object.defineProperty(self, "_hideNextMonthArrow", {
          get: function get() {
            return self.__hideNextMonthArrow;
          },
          set: function set(bool) {
            if (self.__hideNextMonthArrow !== bool) {
              toggleClass(self.nextMonthNav, "disabled", bool);
              self.__hideNextMonthArrow = bool;
            }
          }
        });
        self.currentYearElement = self.yearElements[0];
        updateNavigationCurrentMonth();
        return self.monthNav;
      }

      function buildTime() {
        self.calendarContainer.classList.add("hasTime");
        if (self.config.noCalendar) self.calendarContainer.classList.add("noCalendar");
        self.timeContainer = createElement("div", "flatpickr-time");
        self.timeContainer.tabIndex = -1;
        var separator = createElement("span", "flatpickr-time-separator", ":");
        var hourInput = createNumberInput("flatpickr-hour");
        self.hourElement = hourInput.childNodes[0];
        var minuteInput = createNumberInput("flatpickr-minute");
        self.minuteElement = minuteInput.childNodes[0];
        self.hourElement.tabIndex = self.minuteElement.tabIndex = -1;
        self.hourElement.value = pad(self.latestSelectedDateObj ? self.latestSelectedDateObj.getHours() : self.config.time_24hr ? self.config.defaultHour : military2ampm(self.config.defaultHour));
        self.minuteElement.value = pad(self.latestSelectedDateObj ? self.latestSelectedDateObj.getMinutes() : self.config.defaultMinute);
        self.hourElement.setAttribute("data-step", self.config.hourIncrement.toString());
        self.minuteElement.setAttribute("data-step", self.config.minuteIncrement.toString());
        self.hourElement.setAttribute("data-min", self.config.time_24hr ? "0" : "1");
        self.hourElement.setAttribute("data-max", self.config.time_24hr ? "23" : "12");
        self.minuteElement.setAttribute("data-min", "0");
        self.minuteElement.setAttribute("data-max", "59");
        self.timeContainer.appendChild(hourInput);
        self.timeContainer.appendChild(separator);
        self.timeContainer.appendChild(minuteInput);
        if (self.config.time_24hr) self.timeContainer.classList.add("time24hr");

        if (self.config.enableSeconds) {
          self.timeContainer.classList.add("hasSeconds");
          var secondInput = createNumberInput("flatpickr-second");
          self.secondElement = secondInput.childNodes[0];
          self.secondElement.value = pad(self.latestSelectedDateObj ? self.latestSelectedDateObj.getSeconds() : self.config.defaultSeconds);
          self.secondElement.setAttribute("data-step", self.minuteElement.getAttribute("data-step"));
          self.secondElement.setAttribute("data-min", self.minuteElement.getAttribute("data-min"));
          self.secondElement.setAttribute("data-max", self.minuteElement.getAttribute("data-max"));
          self.timeContainer.appendChild(createElement("span", "flatpickr-time-separator", ":"));
          self.timeContainer.appendChild(secondInput);
        }

        if (!self.config.time_24hr) {
          self.amPM = createElement("span", "flatpickr-am-pm", self.l10n.amPM[int((self.latestSelectedDateObj ? self.hourElement.value : self.config.defaultHour) > 11)]);
          self.amPM.title = self.l10n.toggleTitle;
          self.amPM.tabIndex = -1;
          self.timeContainer.appendChild(self.amPM);
        }

        return self.timeContainer;
      }

      function buildWeekdays() {
        if (!self.weekdayContainer) self.weekdayContainer = createElement("div", "flatpickr-weekdays");else clearNode(self.weekdayContainer);

        for (var i = self.config.showMonths; i--;) {
          var container = createElement("div", "flatpickr-weekdaycontainer");
          self.weekdayContainer.appendChild(container);
        }

        updateWeekdays();
        return self.weekdayContainer;
      }

      function updateWeekdays() {
        var firstDayOfWeek = self.l10n.firstDayOfWeek;
        var weekdays = self.l10n.weekdays.shorthand.concat();

        if (firstDayOfWeek > 0 && firstDayOfWeek < weekdays.length) {
          weekdays = weekdays.splice(firstDayOfWeek, weekdays.length).concat(weekdays.splice(0, firstDayOfWeek));
        }

        for (var i = self.config.showMonths; i--;) {
          self.weekdayContainer.children[i].innerHTML = "\n      <span class=flatpickr-weekday>\n        " + weekdays.join("</span><span class=flatpickr-weekday>") + "\n      </span>\n      ";
        }
      }

      function buildWeeks() {
        self.calendarContainer.classList.add("hasWeeks");
        var weekWrapper = createElement("div", "flatpickr-weekwrapper");
        weekWrapper.appendChild(createElement("span", "flatpickr-weekday", self.l10n.weekAbbreviation));
        var weekNumbers = createElement("div", "flatpickr-weeks");
        weekWrapper.appendChild(weekNumbers);
        return {
          weekWrapper: weekWrapper,
          weekNumbers: weekNumbers
        };
      }

      function changeMonth(value, is_offset) {
        if (is_offset === void 0) {
          is_offset = true;
        }

        var delta = is_offset ? value : value - self.currentMonth;
        if (delta < 0 && self._hidePrevMonthArrow === true || delta > 0 && self._hideNextMonthArrow === true) return;
        self.currentMonth += delta;

        if (self.currentMonth < 0 || self.currentMonth > 11) {
          self.currentYear += self.currentMonth > 11 ? 1 : -1;
          self.currentMonth = (self.currentMonth + 12) % 12;
          triggerEvent("onYearChange");
        }

        buildDays();
        triggerEvent("onMonthChange");
        updateNavigationCurrentMonth();
      }

      function clear(triggerChangeEvent) {
        if (triggerChangeEvent === void 0) {
          triggerChangeEvent = true;
        }

        self.input.value = "";
        if (self.altInput !== undefined) self.altInput.value = "";
        if (self.mobileInput !== undefined) self.mobileInput.value = "";
        self.selectedDates = [];
        self.latestSelectedDateObj = undefined;
        self.showTimeInput = false;

        if (self.config.enableTime === true) {
          setDefaultHours();
        }

        self.redraw();
        if (triggerChangeEvent) triggerEvent("onChange");
      }

      function close() {
        self.isOpen = false;

        if (!self.isMobile) {
          self.calendarContainer.classList.remove("open");

          self._input.classList.remove("active");
        }

        triggerEvent("onClose");
      }

      function destroy() {
        if (self.config !== undefined) triggerEvent("onDestroy");

        for (var i = self._handlers.length; i--;) {
          var h = self._handlers[i];
          h.element.removeEventListener(h.event, h.handler, h.options);
        }

        self._handlers = [];

        if (self.mobileInput) {
          if (self.mobileInput.parentNode) self.mobileInput.parentNode.removeChild(self.mobileInput);
          self.mobileInput = undefined;
        } else if (self.calendarContainer && self.calendarContainer.parentNode) {
          if (self.config.static && self.calendarContainer.parentNode) {
            var wrapper = self.calendarContainer.parentNode;
            wrapper.lastChild && wrapper.removeChild(wrapper.lastChild);

            while (wrapper.firstChild) {
              wrapper.parentNode.insertBefore(wrapper.firstChild, wrapper);
            }

            wrapper.parentNode.removeChild(wrapper);
          } else self.calendarContainer.parentNode.removeChild(self.calendarContainer);
        }

        if (self.altInput) {
          self.input.type = "text";
          if (self.altInput.parentNode) self.altInput.parentNode.removeChild(self.altInput);
          delete self.altInput;
        }

        if (self.input) {
          self.input.type = self.input._type;
          self.input.classList.remove("flatpickr-input");
          self.input.removeAttribute("readonly");
          self.input.value = "";
        }

        ["_showTimeInput", "latestSelectedDateObj", "_hideNextMonthArrow", "_hidePrevMonthArrow", "__hideNextMonthArrow", "__hidePrevMonthArrow", "isMobile", "isOpen", "selectedDateElem", "minDateHasTime", "maxDateHasTime", "days", "daysContainer", "_input", "_positionElement", "innerContainer", "rContainer", "monthNav", "todayDateElem", "calendarContainer", "weekdayContainer", "prevMonthNav", "nextMonthNav", "currentMonthElement", "currentYearElement", "navigationCurrentMonth", "selectedDateElem", "config"].forEach(function (k) {
          try {
            delete self[k];
          } catch (_) {}
        });
      }

      function isCalendarElem(elem) {
        if (self.config.appendTo && self.config.appendTo.contains(elem)) return true;
        return self.calendarContainer.contains(elem);
      }

      function documentClick(e) {
        if (self.isOpen && !self.config.inline) {
          var isCalendarElement = isCalendarElem(e.target);
          var isInput = e.target === self.input || e.target === self.altInput || self.element.contains(e.target) || e.path && e.path.indexOf && (~e.path.indexOf(self.input) || ~e.path.indexOf(self.altInput));
          var lostFocus = e.type === "blur" ? isInput && e.relatedTarget && !isCalendarElem(e.relatedTarget) : !isInput && !isCalendarElement;
          var isIgnored = !self.config.ignoredFocusElements.some(function (elem) {
            return elem.contains(e.target);
          });

          if (lostFocus && isIgnored) {
            self.close();

            if (self.config.mode === "range" && self.selectedDates.length === 1) {
              self.clear(false);
              self.redraw();
            }
          }
        }
      }

      function changeYear(newYear) {
        if (!newYear || self.config.minDate && newYear < self.config.minDate.getFullYear() || self.config.maxDate && newYear > self.config.maxDate.getFullYear()) return;
        var newYearNum = newYear,
            isNewYear = self.currentYear !== newYearNum;
        self.currentYear = newYearNum || self.currentYear;

        if (self.config.maxDate && self.currentYear === self.config.maxDate.getFullYear()) {
          self.currentMonth = Math.min(self.config.maxDate.getMonth(), self.currentMonth);
        } else if (self.config.minDate && self.currentYear === self.config.minDate.getFullYear()) {
          self.currentMonth = Math.max(self.config.minDate.getMonth(), self.currentMonth);
        }

        if (isNewYear) {
          self.redraw();
          triggerEvent("onYearChange");
        }
      }

      function isEnabled(date, timeless) {
        if (timeless === void 0) {
          timeless = true;
        }

        var dateToCheck = self.parseDate(date, undefined, timeless);
        if (self.config.minDate && dateToCheck && compareDates(dateToCheck, self.config.minDate, timeless !== undefined ? timeless : !self.minDateHasTime) < 0 || self.config.maxDate && dateToCheck && compareDates(dateToCheck, self.config.maxDate, timeless !== undefined ? timeless : !self.maxDateHasTime) > 0) return false;
        if (self.config.enable.length === 0 && self.config.disable.length === 0) return true;
        if (dateToCheck === undefined) return false;
        var bool = self.config.enable.length > 0,
            array = bool ? self.config.enable : self.config.disable;

        for (var i = 0, d; i < array.length; i++) {
          d = array[i];
          if (typeof d === "function" && d(dateToCheck)) return bool;else if (d instanceof Date && dateToCheck !== undefined && d.getTime() === dateToCheck.getTime()) return bool;else if (typeof d === "string" && dateToCheck !== undefined) {
            var parsed = self.parseDate(d, undefined, true);
            return parsed && parsed.getTime() === dateToCheck.getTime() ? bool : !bool;
          } else if (typeof d === "object" && dateToCheck !== undefined && d.from && d.to && dateToCheck.getTime() >= d.from.getTime() && dateToCheck.getTime() <= d.to.getTime()) return bool;
        }

        return !bool;
      }

      function isInView(elem) {
        if (self.daysContainer !== undefined) return elem.className.indexOf("hidden") === -1 && self.daysContainer.contains(elem);
        return false;
      }

      function onKeyDown(e) {
        var isInput = e.target === self._input;
        var allowInput = self.config.allowInput;
        var allowKeydown = self.isOpen && (!allowInput || !isInput);
        var allowInlineKeydown = self.config.inline && isInput && !allowInput;

        if (e.keyCode === 13 && isInput) {
          if (allowInput) {
            self.setDate(self._input.value, true, e.target === self.altInput ? self.config.altFormat : self.config.dateFormat);
            return e.target.blur();
          } else self.open();
        } else if (isCalendarElem(e.target) || allowKeydown || allowInlineKeydown) {
          var isTimeObj = !!self.timeContainer && self.timeContainer.contains(e.target);

          switch (e.keyCode) {
            case 13:
              if (isTimeObj) updateTime();else selectDate(e);
              break;

            case 27:
              e.preventDefault();
              focusAndClose();
              break;

            case 8:
            case 46:
              if (isInput && !self.config.allowInput) {
                e.preventDefault();
                self.clear();
              }

              break;

            case 37:
            case 39:
              if (!isTimeObj) {
                e.preventDefault();

                if (self.daysContainer !== undefined && (allowInput === false || isInView(document.activeElement))) {
                  var _delta = e.keyCode === 39 ? 1 : -1;

                  if (!e.ctrlKey) focusOnDay(undefined, _delta);else {
                    changeMonth(_delta);
                    focusOnDay(getFirstAvailableDay(1), 0);
                  }
                }
              } else if (self.hourElement) self.hourElement.focus();

              break;

            case 38:
            case 40:
              e.preventDefault();
              var delta = e.keyCode === 40 ? 1 : -1;

              if (self.daysContainer) {
                if (e.ctrlKey) {
                  changeYear(self.currentYear - delta);
                  focusOnDay(getFirstAvailableDay(1), 0);
                } else if (!isTimeObj) focusOnDay(undefined, delta * 7);
              } else if (self.config.enableTime) {
                if (!isTimeObj && self.hourElement) self.hourElement.focus();
                updateTime(e);

                self._debouncedChange();
              }

              break;

            case 9:
              if (!isTimeObj) break;
              var elems = [self.hourElement, self.minuteElement, self.secondElement, self.amPM].filter(function (x) {
                return x;
              });
              var i = elems.indexOf(e.target);

              if (i !== -1) {
                var target = elems[i + (e.shiftKey ? -1 : 1)];

                if (target !== undefined) {
                  e.preventDefault();
                  target.focus();
                }
              }

              break;

            default:
              break;
          }
        }

        if (self.amPM !== undefined && e.target === self.amPM) {
          switch (e.key) {
            case self.l10n.amPM[0].charAt(0):
            case self.l10n.amPM[0].charAt(0).toLowerCase():
              self.amPM.textContent = self.l10n.amPM[0];
              setHoursFromInputs();
              updateValue();
              break;

            case self.l10n.amPM[1].charAt(0):
            case self.l10n.amPM[1].charAt(0).toLowerCase():
              self.amPM.textContent = self.l10n.amPM[1];
              setHoursFromInputs();
              updateValue();
              break;
          }
        }

        triggerEvent("onKeyDown", e);
      }

      function onMouseOver(elem) {
        if (self.selectedDates.length !== 1 || elem && (!elem.classList.contains("flatpickr-day") || elem.classList.contains("disabled"))) return;
        var hoverDate = elem ? elem.dateObj.getTime() : self.days.firstElementChild.dateObj.getTime(),
            initialDate = self.parseDate(self.selectedDates[0], undefined, true).getTime(),
            rangeStartDate = Math.min(hoverDate, self.selectedDates[0].getTime()),
            rangeEndDate = Math.max(hoverDate, self.selectedDates[0].getTime()),
            lastDate = self.daysContainer.lastChild.lastChild.dateObj.getTime();
        var containsDisabled = false;
        var minRange = 0,
            maxRange = 0;

        for (var t = rangeStartDate; t < lastDate; t += duration.DAY) {
          if (!isEnabled(new Date(t), true)) {
            containsDisabled = containsDisabled || t > rangeStartDate && t < rangeEndDate;
            if (t < initialDate && (!minRange || t > minRange)) minRange = t;else if (t > initialDate && (!maxRange || t < maxRange)) maxRange = t;
          }
        }

        for (var m = 0; m < self.config.showMonths; m++) {
          var month = self.daysContainer.children[m];
          var prevMonth = self.daysContainer.children[m - 1];

          var _loop = function _loop(i, l) {
            var dayElem = month.children[i],
                date = dayElem.dateObj;
            var timestamp = date.getTime();
            var outOfRange = minRange > 0 && timestamp < minRange || maxRange > 0 && timestamp > maxRange;

            if (outOfRange) {
              dayElem.classList.add("notAllowed");
              ["inRange", "startRange", "endRange"].forEach(function (c) {
                dayElem.classList.remove(c);
              });
              return "continue";
            } else if (containsDisabled && !outOfRange) return "continue";

            ["startRange", "inRange", "endRange", "notAllowed"].forEach(function (c) {
              dayElem.classList.remove(c);
            });

            if (elem !== undefined) {
              elem.classList.add(hoverDate < self.selectedDates[0].getTime() ? "startRange" : "endRange");

              if (month.contains(elem) || !(m > 0 && prevMonth && prevMonth.lastChild.dateObj.getTime() >= timestamp)) {
                if (initialDate < hoverDate && timestamp === initialDate) dayElem.classList.add("startRange");else if (initialDate > hoverDate && timestamp === initialDate) dayElem.classList.add("endRange");
                if (timestamp >= minRange && (maxRange === 0 || timestamp <= maxRange) && isBetween(timestamp, initialDate, hoverDate)) dayElem.classList.add("inRange");
              }
            }
          };

          for (var i = 0, l = month.children.length; i < l; i++) {
            var _ret = _loop(i, l);

            if (_ret === "continue") continue;
          }
        }
      }

      function onResize() {
        if (self.isOpen && !self.config.static && !self.config.inline) positionCalendar();
      }

      function open(e, positionElement) {
        if (positionElement === void 0) {
          positionElement = self._positionElement;
        }

        if (self.isMobile === true) {
          if (e) {
            e.preventDefault();
            e.target && e.target.blur();
          }

          setTimeout(function () {
            self.mobileInput !== undefined && self.mobileInput.focus();
          }, 0);
          triggerEvent("onOpen");
          return;
        }

        if (self._input.disabled || self.config.inline) return;
        var wasOpen = self.isOpen;
        self.isOpen = true;

        if (!wasOpen) {
          self.calendarContainer.classList.add("open");

          self._input.classList.add("active");

          triggerEvent("onOpen");
          positionCalendar(positionElement);
        }

        if (self.config.enableTime === true && self.config.noCalendar === true) {
          if (self.selectedDates.length === 0) {
            self.setDate(self.config.minDate !== undefined ? new Date(self.config.minDate.getTime()) : new Date(), false);
            setDefaultHours();
            updateValue();
          }

          if (self.config.allowInput === false && (e === undefined || !self.timeContainer.contains(e.relatedTarget))) {
            setTimeout(function () {
              return self.hourElement.select();
            }, 50);
          }
        }
      }

      function minMaxDateSetter(type) {
        return function (date) {
          var dateObj = self.config["_" + type + "Date"] = self.parseDate(date, self.config.dateFormat);
          var inverseDateObj = self.config["_" + (type === "min" ? "max" : "min") + "Date"];

          if (dateObj !== undefined) {
            self[type === "min" ? "minDateHasTime" : "maxDateHasTime"] = dateObj.getHours() > 0 || dateObj.getMinutes() > 0 || dateObj.getSeconds() > 0;
          }

          if (self.selectedDates) {
            self.selectedDates = self.selectedDates.filter(function (d) {
              return isEnabled(d);
            });
            if (!self.selectedDates.length && type === "min") setHoursFromDate(dateObj);
            updateValue();
          }

          if (self.daysContainer) {
            redraw();
            if (dateObj !== undefined) self.currentYearElement[type] = dateObj.getFullYear().toString();else self.currentYearElement.removeAttribute(type);
            self.currentYearElement.disabled = !!inverseDateObj && dateObj !== undefined && inverseDateObj.getFullYear() === dateObj.getFullYear();
          }
        };
      }

      function parseConfig() {
        var boolOpts = ["wrap", "weekNumbers", "allowInput", "clickOpens", "time_24hr", "enableTime", "noCalendar", "altInput", "shorthandCurrentMonth", "inline", "static", "enableSeconds", "disableMobile"];
        var hooks = ["onChange", "onClose", "onDayCreate", "onDestroy", "onKeyDown", "onMonthChange", "onOpen", "onParseConfig", "onReady", "onValueUpdate", "onYearChange", "onPreCalendarPosition"];
        var userConfig = Object.assign({}, instanceConfig, JSON.parse(JSON.stringify(element.dataset || {})));
        var formats$$1 = {};
        self.config.parseDate = userConfig.parseDate;
        self.config.formatDate = userConfig.formatDate;
        Object.defineProperty(self.config, "enable", {
          get: function get() {
            return self.config._enable;
          },
          set: function set(dates) {
            self.config._enable = parseDateRules(dates);
          }
        });
        Object.defineProperty(self.config, "disable", {
          get: function get() {
            return self.config._disable;
          },
          set: function set(dates) {
            self.config._disable = parseDateRules(dates);
          }
        });
        var timeMode = userConfig.mode === "time";

        if (!userConfig.dateFormat && (userConfig.enableTime || timeMode)) {
          formats$$1.dateFormat = userConfig.noCalendar || timeMode ? "H:i" + (userConfig.enableSeconds ? ":S" : "") : flatpickr.defaultConfig.dateFormat + " H:i" + (userConfig.enableSeconds ? ":S" : "");
        }

        if (userConfig.altInput && (userConfig.enableTime || timeMode) && !userConfig.altFormat) {
          formats$$1.altFormat = userConfig.noCalendar || timeMode ? "h:i" + (userConfig.enableSeconds ? ":S K" : " K") : flatpickr.defaultConfig.altFormat + (" h:i" + (userConfig.enableSeconds ? ":S" : "") + " K");
        }

        Object.defineProperty(self.config, "minDate", {
          get: function get() {
            return self.config._minDate;
          },
          set: minMaxDateSetter("min")
        });
        Object.defineProperty(self.config, "maxDate", {
          get: function get() {
            return self.config._maxDate;
          },
          set: minMaxDateSetter("max")
        });

        var minMaxTimeSetter = function minMaxTimeSetter(type) {
          return function (val) {
            self.config[type === "min" ? "_minTime" : "_maxTime"] = self.parseDate(val, "H:i");
          };
        };

        Object.defineProperty(self.config, "minTime", {
          get: function get() {
            return self.config._minTime;
          },
          set: minMaxTimeSetter("min")
        });
        Object.defineProperty(self.config, "maxTime", {
          get: function get() {
            return self.config._maxTime;
          },
          set: minMaxTimeSetter("max")
        });

        if (userConfig.mode === "time") {
          self.config.noCalendar = true;
          self.config.enableTime = true;
        }

        Object.assign(self.config, formats$$1, userConfig);

        for (var i = 0; i < boolOpts.length; i++) {
          self.config[boolOpts[i]] = self.config[boolOpts[i]] === true || self.config[boolOpts[i]] === "true";
        }

        for (var _i = hooks.length; _i--;) {
          if (self.config[hooks[_i]] !== undefined) {
            self.config[hooks[_i]] = arrayify(self.config[hooks[_i]] || []).map(bindToInstance);
          }
        }

        self.isMobile = !self.config.disableMobile && !self.config.inline && self.config.mode === "single" && !self.config.disable.length && !self.config.enable.length && !self.config.weekNumbers && /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        for (var _i2 = 0; _i2 < self.config.plugins.length; _i2++) {
          var pluginConf = self.config.plugins[_i2](self) || {};

          for (var key in pluginConf) {
            if (~hooks.indexOf(key)) {
              self.config[key] = arrayify(pluginConf[key]).map(bindToInstance).concat(self.config[key]);
            } else if (typeof userConfig[key] === "undefined") self.config[key] = pluginConf[key];
          }
        }

        triggerEvent("onParseConfig");
      }

      function setupLocale() {
        if (typeof self.config.locale !== "object" && typeof flatpickr.l10ns[self.config.locale] === "undefined") self.config.errorHandler(new Error("flatpickr: invalid locale " + self.config.locale));
        self.l10n = Object.assign({}, flatpickr.l10ns.default, typeof self.config.locale === "object" ? self.config.locale : self.config.locale !== "default" ? flatpickr.l10ns[self.config.locale] : undefined);
        tokenRegex.K = "(" + self.l10n.amPM[0] + "|" + self.l10n.amPM[1] + "|" + self.l10n.amPM[0].toLowerCase() + "|" + self.l10n.amPM[1].toLowerCase() + ")";
        self.formatDate = createDateFormatter(self);
        self.parseDate = createDateParser({
          config: self.config,
          l10n: self.l10n
        });
      }

      function positionCalendar(customPositionElement) {
        if (self.calendarContainer === undefined) return;
        triggerEvent("onPreCalendarPosition");
        var positionElement = customPositionElement || self._positionElement;
        var calendarHeight = Array.prototype.reduce.call(self.calendarContainer.children, function (acc, child) {
          return acc + child.offsetHeight;
        }, 0),
            calendarWidth = self.calendarContainer.offsetWidth,
            configPos = self.config.position.split(" "),
            configPosVertical = configPos[0],
            configPosHorizontal = configPos.length > 1 ? configPos[1] : null,
            inputBounds = positionElement.getBoundingClientRect(),
            distanceFromBottom = window.innerHeight - inputBounds.bottom,
            showOnTop = configPosVertical === "above" || configPosVertical !== "below" && distanceFromBottom < calendarHeight && inputBounds.top > calendarHeight;
        var top = window.pageYOffset + inputBounds.top + (!showOnTop ? positionElement.offsetHeight + 2 : -calendarHeight - 2);
        toggleClass(self.calendarContainer, "arrowTop", !showOnTop);
        toggleClass(self.calendarContainer, "arrowBottom", showOnTop);
        if (self.config.inline) return;
        var left = window.pageXOffset + inputBounds.left - (configPosHorizontal != null && configPosHorizontal === "center" ? (calendarWidth - inputBounds.width) / 2 : 0);
        var right = window.document.body.offsetWidth - inputBounds.right;
        var rightMost = left + calendarWidth > window.document.body.offsetWidth;
        toggleClass(self.calendarContainer, "rightMost", rightMost);
        if (self.config.static) return;
        self.calendarContainer.style.top = top + "px";

        if (!rightMost) {
          self.calendarContainer.style.left = left + "px";
          self.calendarContainer.style.right = "auto";
        } else {
          self.calendarContainer.style.left = "auto";
          self.calendarContainer.style.right = right + "px";
        }
      }

      function redraw() {
        if (self.config.noCalendar || self.isMobile) return;
        updateNavigationCurrentMonth();
        buildDays();
      }

      function focusAndClose() {
        self._input.focus();

        if (window.navigator.userAgent.indexOf("MSIE") !== -1 || navigator.msMaxTouchPoints !== undefined) {
          setTimeout(self.close, 0);
        } else {
          self.close();
        }
      }

      function selectDate(e) {
        e.preventDefault();
        e.stopPropagation();

        var isSelectable = function isSelectable(day) {
          return day.classList && day.classList.contains("flatpickr-day") && !day.classList.contains("disabled") && !day.classList.contains("notAllowed");
        };

        var t = findParent(e.target, isSelectable);
        if (t === undefined) return;
        var target = t;
        var selectedDate = self.latestSelectedDateObj = new Date(target.dateObj.getTime());
        var shouldChangeMonth = (selectedDate.getMonth() < self.currentMonth || selectedDate.getMonth() > self.currentMonth + self.config.showMonths - 1) && self.config.mode !== "range";
        self.selectedDateElem = target;
        if (self.config.mode === "single") self.selectedDates = [selectedDate];else if (self.config.mode === "multiple") {
          var selectedIndex = isDateSelected(selectedDate);
          if (selectedIndex) self.selectedDates.splice(parseInt(selectedIndex), 1);else self.selectedDates.push(selectedDate);
        } else if (self.config.mode === "range") {
          if (self.selectedDates.length === 2) self.clear(false);
          self.selectedDates.push(selectedDate);
          if (compareDates(selectedDate, self.selectedDates[0], true) !== 0) self.selectedDates.sort(function (a, b) {
            return a.getTime() - b.getTime();
          });
        }
        setHoursFromInputs();

        if (shouldChangeMonth) {
          var isNewYear = self.currentYear !== selectedDate.getFullYear();
          self.currentYear = selectedDate.getFullYear();
          self.currentMonth = selectedDate.getMonth();
          if (isNewYear) triggerEvent("onYearChange");
          triggerEvent("onMonthChange");
        }

        updateNavigationCurrentMonth();
        buildDays();
        updateValue();
        if (self.config.enableTime) setTimeout(function () {
          return self.showTimeInput = true;
        }, 50);
        if (!shouldChangeMonth && self.config.mode !== "range" && self.config.showMonths === 1) focusOnDayElem(target);else self.selectedDateElem && self.selectedDateElem.focus();
        if (self.hourElement !== undefined) setTimeout(function () {
          return self.hourElement !== undefined && self.hourElement.select();
        }, 451);

        if (self.config.closeOnSelect) {
          var single = self.config.mode === "single" && !self.config.enableTime;
          var range = self.config.mode === "range" && self.selectedDates.length === 2 && !self.config.enableTime;

          if (single || range) {
            focusAndClose();
          }
        }

        triggerChange();
      }

      var CALLBACKS = {
        locale: [setupLocale, updateWeekdays],
        showMonths: [buildMonths, setCalendarWidth, buildWeekdays]
      };

      function set(option, value) {
        if (option !== null && typeof option === "object") Object.assign(self.config, option);else {
          self.config[option] = value;
          if (CALLBACKS[option] !== undefined) CALLBACKS[option].forEach(function (x) {
            return x();
          });
        }
        self.redraw();
        jumpToDate();
      }

      function setSelectedDate(inputDate, format) {
        var dates = [];
        if (inputDate instanceof Array) dates = inputDate.map(function (d) {
          return self.parseDate(d, format);
        });else if (inputDate instanceof Date || typeof inputDate === "number") dates = [self.parseDate(inputDate, format)];else if (typeof inputDate === "string") {
          switch (self.config.mode) {
            case "single":
            case "time":
              dates = [self.parseDate(inputDate, format)];
              break;

            case "multiple":
              dates = inputDate.split(self.config.conjunction).map(function (date) {
                return self.parseDate(date, format);
              });
              break;

            case "range":
              dates = inputDate.split(self.l10n.rangeSeparator).map(function (date) {
                return self.parseDate(date, format);
              });
              break;

            default:
              break;
          }
        } else self.config.errorHandler(new Error("Invalid date supplied: " + JSON.stringify(inputDate)));
        self.selectedDates = dates.filter(function (d) {
          return d instanceof Date && isEnabled(d, false);
        });
        if (self.config.mode === "range") self.selectedDates.sort(function (a, b) {
          return a.getTime() - b.getTime();
        });
      }

      function setDate(date, triggerChange, format) {
        if (triggerChange === void 0) {
          triggerChange = false;
        }

        if (format === void 0) {
          format = self.config.dateFormat;
        }

        if (date !== 0 && !date || date instanceof Array && date.length === 0) return self.clear(triggerChange);
        setSelectedDate(date, format);
        self.showTimeInput = self.selectedDates.length > 0;
        self.latestSelectedDateObj = self.selectedDates[0];
        self.redraw();
        jumpToDate();
        setHoursFromDate();
        updateValue(triggerChange);
        if (triggerChange) triggerEvent("onChange");
      }

      function parseDateRules(arr) {
        return arr.slice().map(function (rule) {
          if (typeof rule === "string" || typeof rule === "number" || rule instanceof Date) {
            return self.parseDate(rule, undefined, true);
          } else if (rule && typeof rule === "object" && rule.from && rule.to) return {
            from: self.parseDate(rule.from, undefined),
            to: self.parseDate(rule.to, undefined)
          };

          return rule;
        }).filter(function (x) {
          return x;
        });
      }

      function setupDates() {
        self.selectedDates = [];
        self.now = self.parseDate(self.config.now) || new Date();
        var preloadedDate = self.config.defaultDate || ((self.input.nodeName === "INPUT" || self.input.nodeName === "TEXTAREA") && self.input.placeholder && self.input.value === self.input.placeholder ? null : self.input.value);
        if (preloadedDate) setSelectedDate(preloadedDate, self.config.dateFormat);
        var initialDate = self.selectedDates.length > 0 ? self.selectedDates[0] : self.config.minDate && self.config.minDate.getTime() > self.now.getTime() ? self.config.minDate : self.config.maxDate && self.config.maxDate.getTime() < self.now.getTime() ? self.config.maxDate : self.now;
        self.currentYear = initialDate.getFullYear();
        self.currentMonth = initialDate.getMonth();
        if (self.selectedDates.length > 0) self.latestSelectedDateObj = self.selectedDates[0];
        if (self.config.minTime !== undefined) self.config.minTime = self.parseDate(self.config.minTime, "H:i");
        if (self.config.maxTime !== undefined) self.config.maxTime = self.parseDate(self.config.maxTime, "H:i");
        self.minDateHasTime = !!self.config.minDate && (self.config.minDate.getHours() > 0 || self.config.minDate.getMinutes() > 0 || self.config.minDate.getSeconds() > 0);
        self.maxDateHasTime = !!self.config.maxDate && (self.config.maxDate.getHours() > 0 || self.config.maxDate.getMinutes() > 0 || self.config.maxDate.getSeconds() > 0);
        Object.defineProperty(self, "showTimeInput", {
          get: function get() {
            return self._showTimeInput;
          },
          set: function set(bool) {
            self._showTimeInput = bool;
            if (self.calendarContainer) toggleClass(self.calendarContainer, "showTimeInput", bool);
            self.isOpen && positionCalendar();
          }
        });
      }

      function setupInputs() {
        self.input = self.config.wrap ? element.querySelector("[data-input]") : element;

        if (!self.input) {
          self.config.errorHandler(new Error("Invalid input element specified"));
          return;
        }

        self.input._type = self.input.type;
        self.input.type = "text";
        self.input.classList.add("flatpickr-input");
        self._input = self.input;

        if (self.config.altInput) {
          self.altInput = createElement(self.input.nodeName, self.input.className + " " + self.config.altInputClass);
          self._input = self.altInput;
          self.altInput.placeholder = self.input.placeholder;
          self.altInput.disabled = self.input.disabled;
          self.altInput.required = self.input.required;
          self.altInput.tabIndex = self.input.tabIndex;
          self.altInput.type = "text";
          self.input.setAttribute("type", "hidden");
          if (!self.config.static && self.input.parentNode) self.input.parentNode.insertBefore(self.altInput, self.input.nextSibling);
        }

        if (!self.config.allowInput) self._input.setAttribute("readonly", "readonly");
        self._positionElement = self.config.positionElement || self._input;
      }

      function setupMobile() {
        var inputType = self.config.enableTime ? self.config.noCalendar ? "time" : "datetime-local" : "date";
        self.mobileInput = createElement("input", self.input.className + " flatpickr-mobile");
        self.mobileInput.step = self.input.getAttribute("step") || "any";
        self.mobileInput.tabIndex = 1;
        self.mobileInput.type = inputType;
        self.mobileInput.disabled = self.input.disabled;
        self.mobileInput.required = self.input.required;
        self.mobileInput.placeholder = self.input.placeholder;
        self.mobileFormatStr = inputType === "datetime-local" ? "Y-m-d\\TH:i:S" : inputType === "date" ? "Y-m-d" : "H:i:S";

        if (self.selectedDates.length > 0) {
          self.mobileInput.defaultValue = self.mobileInput.value = self.formatDate(self.selectedDates[0], self.mobileFormatStr);
        }

        if (self.config.minDate) self.mobileInput.min = self.formatDate(self.config.minDate, "Y-m-d");
        if (self.config.maxDate) self.mobileInput.max = self.formatDate(self.config.maxDate, "Y-m-d");
        self.input.type = "hidden";
        if (self.altInput !== undefined) self.altInput.type = "hidden";

        try {
          if (self.input.parentNode) self.input.parentNode.insertBefore(self.mobileInput, self.input.nextSibling);
        } catch (_a) {}

        bind(self.mobileInput, "change", function (e) {
          self.setDate(e.target.value, false, self.mobileFormatStr);
          triggerEvent("onChange");
          triggerEvent("onClose");
        });
      }

      function toggle(e) {
        if (self.isOpen === true) return self.close();
        self.open(e);
      }

      function triggerEvent(event, data) {
        var hooks = self.config[event];

        if (hooks !== undefined && hooks.length > 0) {
          for (var i = 0; hooks[i] && i < hooks.length; i++) {
            hooks[i](self.selectedDates, self.input.value, self, data);
          }
        }

        if (event === "onChange") {
          self.input.dispatchEvent(createEvent("change"));
          self.input.dispatchEvent(createEvent("input"));
        }
      }

      function createEvent(name) {
        var e = document.createEvent("Event");
        e.initEvent(name, true, true);
        return e;
      }

      function isDateSelected(date) {
        for (var i = 0; i < self.selectedDates.length; i++) {
          if (compareDates(self.selectedDates[i], date) === 0) return "" + i;
        }

        return false;
      }

      function isDateInRange(date) {
        if (self.config.mode !== "range" || self.selectedDates.length < 2) return false;
        return compareDates(date, self.selectedDates[0]) >= 0 && compareDates(date, self.selectedDates[1]) <= 0;
      }

      function updateNavigationCurrentMonth() {
        if (self.config.noCalendar || self.isMobile || !self.monthNav) return;
        self.yearElements.forEach(function (yearElement, i) {
          var d = new Date(self.currentYear, self.currentMonth, 1);
          d.setMonth(self.currentMonth + i);
          self.monthElements[i].textContent = monthToStr(d.getMonth(), self.config.shorthandCurrentMonth, self.l10n) + " ";
          yearElement.value = d.getFullYear().toString();
        });
        self._hidePrevMonthArrow = self.config.minDate !== undefined && (self.currentYear === self.config.minDate.getFullYear() ? self.currentMonth <= self.config.minDate.getMonth() : self.currentYear < self.config.minDate.getFullYear());
        self._hideNextMonthArrow = self.config.maxDate !== undefined && (self.currentYear === self.config.maxDate.getFullYear() ? self.currentMonth + 1 > self.config.maxDate.getMonth() : self.currentYear > self.config.maxDate.getFullYear());
      }

      function updateValue(triggerChange) {
        if (triggerChange === void 0) {
          triggerChange = true;
        }

        if (self.selectedDates.length === 0) return self.clear(triggerChange);

        if (self.mobileInput !== undefined && self.mobileFormatStr) {
          self.mobileInput.value = self.latestSelectedDateObj !== undefined ? self.formatDate(self.latestSelectedDateObj, self.mobileFormatStr) : "";
        }

        var joinChar = self.config.mode !== "range" ? self.config.conjunction : self.l10n.rangeSeparator;
        self.input.value = self.selectedDates.map(function (dObj) {
          return self.formatDate(dObj, self.config.dateFormat);
        }).join(joinChar);

        if (self.altInput !== undefined) {
          self.altInput.value = self.selectedDates.map(function (dObj) {
            return self.formatDate(dObj, self.config.altFormat);
          }).join(joinChar);
        }

        if (triggerChange !== false) triggerEvent("onValueUpdate");
      }

      function onMonthNavClick(e) {
        e.preventDefault();
        var isPrevMonth = self.prevMonthNav.contains(e.target);
        var isNextMonth = self.nextMonthNav.contains(e.target);

        if (isPrevMonth || isNextMonth) {
          changeMonth(isPrevMonth ? -1 : 1);
        } else if (self.yearElements.indexOf(e.target) >= 0) {
          e.target.select();
        } else if (e.target.classList.contains("arrowUp")) {
          self.changeYear(self.currentYear + 1);
        } else if (e.target.classList.contains("arrowDown")) {
          self.changeYear(self.currentYear - 1);
        }
      }

      function timeWrapper(e) {
        e.preventDefault();
        var isKeyDown = e.type === "keydown",
            input = e.target;

        if (self.amPM !== undefined && e.target === self.amPM) {
          self.amPM.textContent = self.l10n.amPM[int(self.amPM.textContent === self.l10n.amPM[0])];
        }

        var min = parseFloat(input.getAttribute("data-min")),
            max = parseFloat(input.getAttribute("data-max")),
            step = parseFloat(input.getAttribute("data-step")),
            curValue = parseInt(input.value, 10),
            delta = e.delta || (isKeyDown ? e.which === 38 ? 1 : -1 : 0);
        var newValue = curValue + step * delta;

        if (typeof input.value !== "undefined" && input.value.length === 2) {
          var isHourElem = input === self.hourElement,
              isMinuteElem = input === self.minuteElement;

          if (newValue < min) {
            newValue = max + newValue + int(!isHourElem) + (int(isHourElem) && int(!self.amPM));
            if (isMinuteElem) incrementNumInput(undefined, -1, self.hourElement);
          } else if (newValue > max) {
            newValue = input === self.hourElement ? newValue - max - int(!self.amPM) : min;
            if (isMinuteElem) incrementNumInput(undefined, 1, self.hourElement);
          }

          if (self.amPM && isHourElem && (step === 1 ? newValue + curValue === 23 : Math.abs(newValue - curValue) > step)) {
            self.amPM.textContent = self.l10n.amPM[int(self.amPM.textContent === self.l10n.amPM[0])];
          }

          input.value = pad(newValue);
        }
      }

      init();
      return self;
    }

    function _flatpickr(nodeList, config) {
      var nodes = Array.prototype.slice.call(nodeList);
      var instances = [];

      for (var i = 0; i < nodes.length; i++) {
        var node = nodes[i];

        try {
          if (node.getAttribute("data-fp-omit") !== null) continue;

          if (node._flatpickr !== undefined) {
            node._flatpickr.destroy();

            node._flatpickr = undefined;
          }

          node._flatpickr = FlatpickrInstance(node, config || {});
          instances.push(node._flatpickr);
        } catch (e) {
          console.error(e);
        }
      }

      return instances.length === 1 ? instances[0] : instances;
    }

    if (typeof HTMLElement !== "undefined") {
      HTMLCollection.prototype.flatpickr = NodeList.prototype.flatpickr = function (config) {
        return _flatpickr(this, config);
      };

      HTMLElement.prototype.flatpickr = function (config) {
        return _flatpickr([this], config);
      };
    }

    var flatpickr = function flatpickr(selector, config) {
      if (selector instanceof NodeList) return _flatpickr(selector, config);else if (typeof selector === "string") return _flatpickr(window.document.querySelectorAll(selector), config);
      return _flatpickr([selector], config);
    };

    flatpickr.defaultConfig = defaults;
    flatpickr.l10ns = {
      en: Object.assign({}, english),
      default: Object.assign({}, english)
    };

    flatpickr.localize = function (l10n) {
      flatpickr.l10ns.default = Object.assign({}, flatpickr.l10ns.default, l10n);
    };

    flatpickr.setDefaults = function (config) {
      flatpickr.defaultConfig = Object.assign({}, flatpickr.defaultConfig, config);
    };

    flatpickr.parseDate = createDateParser({});
    flatpickr.formatDate = createDateFormatter({});
    flatpickr.compareDates = compareDates;

    if (typeof jQuery !== "undefined") {
      jQuery.fn.flatpickr = function (config) {
        return _flatpickr(this, config);
      };
    }

    Date.prototype.fp_incr = function (days) {
      return new Date(this.getFullYear(), this.getMonth(), this.getDate() + (typeof days === "string" ? parseInt(days, 10) : days));
    };

    if (typeof window !== "undefined") {
      window.flatpickr = flatpickr;
    }

    return flatpickr;

})));


/***/ }),
/* 387 */
/***/ (function(module, exports, __webpack_require__) {

var core = __webpack_require__(3);
var $JSON = core.JSON || (core.JSON = { stringify: JSON.stringify });
module.exports = function stringify(it) { // eslint-disable-line no-unused-vars
  return $JSON.stringify.apply($JSON, arguments);
};


/***/ }),
/* 388 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

__webpack_require__(46);

__webpack_require__(98);

var updateQueryVar = function updateQueryVar(key, value) {
  var url = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : window.location.href;
  var re = new RegExp("([?&])".concat(key, "=.*?(&|#|$)(.*)"), 'gi');
  var hash;
  var separator;
  var parsedUrl = url;

  if (re.test(url)) {
    if (typeof value !== 'undefined' && value !== null) {
      parsedUrl = url.replace(re, "$1".concat(key, "=").concat(value, "$2$3"));
    } else {
      hash = url.split('#');
      parsedUrl = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');

      if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
        parsedUrl += "#".concat(hash[1]);
      }
    }
  } else if (typeof value !== 'undefined' && value !== null) {
    separator = url.indexOf('?') !== -1 ? '&' : '?';
    hash = url.split('#');
    parsedUrl = "".concat(hash[0]).concat(separator).concat(key, "=").concat(value);

    if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
      parsedUrl += "#".concat(hash[1]);
    }
  }

  return parsedUrl;
};

var _default = updateQueryVar;
exports.default = _default;

/***/ }),
/* 389 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.productMessage = void 0;

/**
 * @template Product Message
 */
var productMessage = function productMessage(message) {
  return "<span class=\"bc-product-form__message bc-alert bc-alert--info\">".concat(message, "</span>");
};

exports.productMessage = productMessage;

/***/ }),
/* 390 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _superagent = _interopRequireDefault(__webpack_require__(107));

var _spin = __webpack_require__(108);

var _events = __webpack_require__(5);

var tools = _interopRequireWildcard(__webpack_require__(2));

var _shortcodeState = _interopRequireDefault(__webpack_require__(182));

var _quickViewDialog = _interopRequireDefault(__webpack_require__(162));

var _i18n = __webpack_require__(7);

var _errors = __webpack_require__(183);

var el = {
  container: tools.getNodes('load-items-trigger', true, document, false)
};
var options = {
  delay: 150,
  afterLoadDelay: 250
};
/**
 * @function createSpinner
 * @description create a new spinner element.
 * @returns {*}
 */

var createSpinLoader = function createSpinLoader() {
  var itemContainer = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

  if (!itemContainer) {
    return;
  }

  var container = tools.closest(itemContainer, '.bc-load-items');
  var spinner = tools.getNodes('.bc-load-items__loader', false, container, true)[0];
  var spinnerOptions = {
    opacity: 0.5,
    scale: 0.5,
    lines: 12
  };
  new _spin.Spinner(spinnerOptions).spin(spinner);
};
/**
 * @function initializeItems
 * @description add a class to signify that item has been rendered in the shortcode container.
 * @param itemContainer
 */


var initializeItems = function initializeItems() {
  var itemContainer = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

  if (!itemContainer) {
    return;
  }

  tools.getChildren(itemContainer).forEach(function (item) {
    if (!tools.hasClass(item, 'item-initialized') && !tools.hasClass(item, 'bc-load-items__trigger')) {
      tools.addClass(item, 'item-initialized');
    }
  });
};
/**
 * @function loadNextPageItems
 * @description Get and inject the rendered HTML from the WP API response to load the next page of items.
 * @param items
 * @param itemContainer
 */


var loadNextPageItems = function loadNextPageItems() {
  var items = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var itemContainer = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  (0, _delay2.default)(function () {
    itemContainer.insertAdjacentHTML('beforeend', items.rendered);
  }, options.delay);
  (0, _delay2.default)(function () {
    if (tools.hasClass(itemContainer, 'bc-product-grid')) {
      (0, _quickViewDialog.default)();
    }

    initializeItems(itemContainer);
    (0, _events.trigger)({
      event: 'bigcommerce/get_pricing',
      native: false
    });
  }, options.afterLoadDelay);
};
/**
 * @function removePagedButton
 * @description Remove the paged button that triggered the current successful API request.
 * @param target
 * @param container
 */


var removePagedButton = function removePagedButton() {
  var target = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var container = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (!target && !container) {
    return;
  }

  var loadMoreWrapper = tools.closest(target, '.bc-load-items__trigger');
  container.removeChild(loadMoreWrapper);
};
/**
 * @function handleItemsLoading
 * @description Handler for gracefully loading the next set of paged items into the current shortcode container.
 * @param target
 * @param items
 */


var handleItemsLoading = function handleItemsLoading() {
  var target = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var items = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

  if (!target && !items || _shortcodeState.default.isFetching) {
    return;
  }

  var itemContainer = tools.closest(target, '.bc-load-items-container');
  removePagedButton(target, itemContainer);
  loadNextPageItems(items, itemContainer);
};
/**
 * @function handleSpinnerState
 * @description Show or hid the display of the spinner when fetching data.
 * @param target
 */


var handleSpinnerState = function handleSpinnerState() {
  var target = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var gridWrapper = tools.closest(target, '.bc-load-items');
  var loader = tools.getNodes('.bc-load-items__loader', false, gridWrapper, true)[0];

  if (_shortcodeState.default.isFetching) {
    tools.addClass(loader, 'active');
    return;
  }

  tools.removeClass(loader, 'active');
};
/**
 * @function handleRequestError
 * @description if there is a pagination request error, display the message inline.
 * @param err
 * @param target
 */


var handleRequestError = function handleRequestError() {
  var err = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var target = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (!target && !err) {
    return;
  }

  var message = err.timeout ? _i18n.NLS.errors.pagination_timeout_error : _i18n.NLS.errors.pagination_error;
  var loadMoreWrapper = tools.closest(target, '.bc-load-items__trigger');
  var currentErrorMessage = tools.getNodes('.bc-pagination__error-message', false, loadMoreWrapper, true)[0];

  if (currentErrorMessage) {
    currentErrorMessage.parentNode.removeChild(currentErrorMessage);
  }

  target.removeAttribute('disabled');
  loadMoreWrapper.insertAdjacentHTML('beforeend', (0, _errors.paginationError)(message));
  initializeItems(loadMoreWrapper);
};
/**
 * @function getNextPageItems
 * @description Ajax query to get the next set of items in a paged shortcode container.
 * @param e
 */


var getNextPageItems = function getNextPageItems(e) {
  e.preventDefault();
  e.delegateTarget.setAttribute('disabled', 'disabled');
  var button = e.delegateTarget;
  var itemsURL = e.delegateTarget.dataset.href;

  if (!itemsURL) {
    return;
  }

  _shortcodeState.default.isFetching = true;
  handleSpinnerState(button);

  _superagent.default.get(itemsURL).timeout({
    response: 5000,
    // 5 seconds to hear back from the server.
    deadline: 30000 // 30 seconds to finish the request process.

  }).end(function (err, res) {
    _shortcodeState.default.isFetching = false;
    handleSpinnerState(button);

    if (err) {
      handleRequestError(err, button);
      return;
    }

    handleItemsLoading(button, res.body);
  });
};
/**
 * @function cacheElements
 * @description Load some additional elements into the scope if el.container exists.
 */


var cacheElements = function cacheElements() {
  el.itemContainer = tools.getNodes('.bc-load-items-container--has-pages', true, document, true);
};
/**
 * @function bindEvents
 * @description Handle events triggered by paged shortcode triggers.
 */


var bindEvents = function bindEvents() {
  el.itemContainer.forEach(function (itemContainer) {
    createSpinLoader(itemContainer);
    initializeItems(itemContainer);
  });
  (0, _delegate.default)(document, '[data-js="load-items-trigger-btn"]', 'click', getNextPageItems);
};

var init = function init() {
  if (!el.container) {
    return;
  }

  cacheElements();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 391 */
/***/ (function(module, exports, __webpack_require__) {


/**
 * Expose `Emitter`.
 */

if (true) {
  module.exports = Emitter;
}

/**
 * Initialize a new `Emitter`.
 *
 * @api public
 */

function Emitter(obj) {
  if (obj) return mixin(obj);
};

/**
 * Mixin the emitter properties.
 *
 * @param {Object} obj
 * @return {Object}
 * @api private
 */

function mixin(obj) {
  for (var key in Emitter.prototype) {
    obj[key] = Emitter.prototype[key];
  }
  return obj;
}

/**
 * Listen on the given `event` with `fn`.
 *
 * @param {String} event
 * @param {Function} fn
 * @return {Emitter}
 * @api public
 */

Emitter.prototype.on =
Emitter.prototype.addEventListener = function(event, fn){
  this._callbacks = this._callbacks || {};
  (this._callbacks['$' + event] = this._callbacks['$' + event] || [])
    .push(fn);
  return this;
};

/**
 * Adds an `event` listener that will be invoked a single
 * time then automatically removed.
 *
 * @param {String} event
 * @param {Function} fn
 * @return {Emitter}
 * @api public
 */

Emitter.prototype.once = function(event, fn){
  function on() {
    this.off(event, on);
    fn.apply(this, arguments);
  }

  on.fn = fn;
  this.on(event, on);
  return this;
};

/**
 * Remove the given callback for `event` or all
 * registered callbacks.
 *
 * @param {String} event
 * @param {Function} fn
 * @return {Emitter}
 * @api public
 */

Emitter.prototype.off =
Emitter.prototype.removeListener =
Emitter.prototype.removeAllListeners =
Emitter.prototype.removeEventListener = function(event, fn){
  this._callbacks = this._callbacks || {};

  // all
  if (0 == arguments.length) {
    this._callbacks = {};
    return this;
  }

  // specific event
  var callbacks = this._callbacks['$' + event];
  if (!callbacks) return this;

  // remove all handlers
  if (1 == arguments.length) {
    delete this._callbacks['$' + event];
    return this;
  }

  // remove specific handler
  var cb;
  for (var i = 0; i < callbacks.length; i++) {
    cb = callbacks[i];
    if (cb === fn || cb.fn === fn) {
      callbacks.splice(i, 1);
      break;
    }
  }

  // Remove event specific arrays for event types that no
  // one is subscribed for to avoid memory leak.
  if (callbacks.length === 0) {
    delete this._callbacks['$' + event];
  }

  return this;
};

/**
 * Emit `event` with the given args.
 *
 * @param {String} event
 * @param {Mixed} ...
 * @return {Emitter}
 */

Emitter.prototype.emit = function(event){
  this._callbacks = this._callbacks || {};

  var args = new Array(arguments.length - 1)
    , callbacks = this._callbacks['$' + event];

  for (var i = 1; i < arguments.length; i++) {
    args[i - 1] = arguments[i];
  }

  if (callbacks) {
    callbacks = callbacks.slice(0);
    for (var i = 0, len = callbacks.length; i < len; ++i) {
      callbacks[i].apply(this, args);
    }
  }

  return this;
};

/**
 * Return array of callbacks for `event`.
 *
 * @param {String} event
 * @return {Array}
 * @api public
 */

Emitter.prototype.listeners = function(event){
  this._callbacks = this._callbacks || {};
  return this._callbacks['$' + event] || [];
};

/**
 * Check if this emitter has `event` handlers.
 *
 * @param {String} event
 * @return {Boolean}
 * @api public
 */

Emitter.prototype.hasListeners = function(event){
  return !! this.listeners(event).length;
};


/***/ }),
/* 392 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Module of mixed-in functions shared between node and client code
 */
var isObject = __webpack_require__(181);

/**
 * Expose `RequestBase`.
 */

module.exports = RequestBase;

/**
 * Initialize a new `RequestBase`.
 *
 * @api public
 */

function RequestBase(obj) {
  if (obj) return mixin(obj);
}

/**
 * Mixin the prototype properties.
 *
 * @param {Object} obj
 * @return {Object}
 * @api private
 */

function mixin(obj) {
  for (var key in RequestBase.prototype) {
    obj[key] = RequestBase.prototype[key];
  }
  return obj;
}

/**
 * Clear previous timeout.
 *
 * @return {Request} for chaining
 * @api public
 */

RequestBase.prototype.clearTimeout = function _clearTimeout(){
  clearTimeout(this._timer);
  clearTimeout(this._responseTimeoutTimer);
  delete this._timer;
  delete this._responseTimeoutTimer;
  return this;
};

/**
 * Override default response body parser
 *
 * This function will be called to convert incoming data into request.body
 *
 * @param {Function}
 * @api public
 */

RequestBase.prototype.parse = function parse(fn){
  this._parser = fn;
  return this;
};

/**
 * Set format of binary response body.
 * In browser valid formats are 'blob' and 'arraybuffer',
 * which return Blob and ArrayBuffer, respectively.
 *
 * In Node all values result in Buffer.
 *
 * Examples:
 *
 *      req.get('/')
 *        .responseType('blob')
 *        .end(callback);
 *
 * @param {String} val
 * @return {Request} for chaining
 * @api public
 */

RequestBase.prototype.responseType = function(val){
  this._responseType = val;
  return this;
};

/**
 * Override default request body serializer
 *
 * This function will be called to convert data set via .send or .attach into payload to send
 *
 * @param {Function}
 * @api public
 */

RequestBase.prototype.serialize = function serialize(fn){
  this._serializer = fn;
  return this;
};

/**
 * Set timeouts.
 *
 * - response timeout is time between sending request and receiving the first byte of the response. Includes DNS and connection time.
 * - deadline is the time from start of the request to receiving response body in full. If the deadline is too short large files may not load at all on slow connections.
 *
 * Value of 0 or false means no timeout.
 *
 * @param {Number|Object} ms or {response, deadline}
 * @return {Request} for chaining
 * @api public
 */

RequestBase.prototype.timeout = function timeout(options){
  if (!options || 'object' !== typeof options) {
    this._timeout = options;
    this._responseTimeout = 0;
    return this;
  }

  for(var option in options) {
    switch(option) {
      case 'deadline':
        this._timeout = options.deadline;
        break;
      case 'response':
        this._responseTimeout = options.response;
        break;
      default:
        console.warn("Unknown timeout option", option);
    }
  }
  return this;
};

/**
 * Set number of retry attempts on error.
 *
 * Failed requests will be retried 'count' times if timeout or err.code >= 500.
 *
 * @param {Number} count
 * @param {Function} [fn]
 * @return {Request} for chaining
 * @api public
 */

RequestBase.prototype.retry = function retry(count, fn){
  // Default to 1 if no count passed or true
  if (arguments.length === 0 || count === true) count = 1;
  if (count <= 0) count = 0;
  this._maxRetries = count;
  this._retries = 0;
  this._retryCallback = fn;
  return this;
};

var ERROR_CODES = [
  'ECONNRESET',
  'ETIMEDOUT',
  'EADDRINFO',
  'ESOCKETTIMEDOUT'
];

/**
 * Determine if a request should be retried.
 * (Borrowed from segmentio/superagent-retry)
 *
 * @param {Error} err
 * @param {Response} [res]
 * @returns {Boolean}
 */
RequestBase.prototype._shouldRetry = function(err, res) {
  if (!this._maxRetries || this._retries++ >= this._maxRetries) {
    return false;
  }
  if (this._retryCallback) {
    try {
      var override = this._retryCallback(err, res);
      if (override === true) return true;
      if (override === false) return false;
      // undefined falls back to defaults
    } catch(e) {
      console.error(e);
    }
  }
  if (res && res.status && res.status >= 500 && res.status != 501) return true;
  if (err) {
    if (err.code && ~ERROR_CODES.indexOf(err.code)) return true;
    // Superagent timeout
    if (err.timeout && err.code == 'ECONNABORTED') return true;
    if (err.crossDomain) return true;
  }
  return false;
};

/**
 * Retry request
 *
 * @return {Request} for chaining
 * @api private
 */

RequestBase.prototype._retry = function() {

  this.clearTimeout();

  // node
  if (this.req) {
    this.req = null;
    this.req = this.request();
  }

  this._aborted = false;
  this.timedout = false;

  return this._end();
};

/**
 * Promise support
 *
 * @param {Function} resolve
 * @param {Function} [reject]
 * @return {Request}
 */

RequestBase.prototype.then = function then(resolve, reject) {
  if (!this._fullfilledPromise) {
    var self = this;
    if (this._endCalled) {
      console.warn("Warning: superagent request was sent twice, because both .end() and .then() were called. Never call .end() if you use promises");
    }
    this._fullfilledPromise = new Promise(function(innerResolve, innerReject) {
      self.end(function(err, res) {
        if (err) innerReject(err);
        else innerResolve(res);
      });
    });
  }
  return this._fullfilledPromise.then(resolve, reject);
};

RequestBase.prototype['catch'] = function(cb) {
  return this.then(undefined, cb);
};

/**
 * Allow for extension
 */

RequestBase.prototype.use = function use(fn) {
  fn(this);
  return this;
};

RequestBase.prototype.ok = function(cb) {
  if ('function' !== typeof cb) throw Error("Callback required");
  this._okCallback = cb;
  return this;
};

RequestBase.prototype._isResponseOK = function(res) {
  if (!res) {
    return false;
  }

  if (this._okCallback) {
    return this._okCallback(res);
  }

  return res.status >= 200 && res.status < 300;
};

/**
 * Get request header `field`.
 * Case-insensitive.
 *
 * @param {String} field
 * @return {String}
 * @api public
 */

RequestBase.prototype.get = function(field){
  return this._header[field.toLowerCase()];
};

/**
 * Get case-insensitive header `field` value.
 * This is a deprecated internal API. Use `.get(field)` instead.
 *
 * (getHeader is no longer used internally by the superagent code base)
 *
 * @param {String} field
 * @return {String}
 * @api private
 * @deprecated
 */

RequestBase.prototype.getHeader = RequestBase.prototype.get;

/**
 * Set header `field` to `val`, or multiple fields with one object.
 * Case-insensitive.
 *
 * Examples:
 *
 *      req.get('/')
 *        .set('Accept', 'application/json')
 *        .set('X-API-Key', 'foobar')
 *        .end(callback);
 *
 *      req.get('/')
 *        .set({ Accept: 'application/json', 'X-API-Key': 'foobar' })
 *        .end(callback);
 *
 * @param {String|Object} field
 * @param {String} val
 * @return {Request} for chaining
 * @api public
 */

RequestBase.prototype.set = function(field, val){
  if (isObject(field)) {
    for (var key in field) {
      this.set(key, field[key]);
    }
    return this;
  }
  this._header[field.toLowerCase()] = val;
  this.header[field] = val;
  return this;
};

/**
 * Remove header `field`.
 * Case-insensitive.
 *
 * Example:
 *
 *      req.get('/')
 *        .unset('User-Agent')
 *        .end(callback);
 *
 * @param {String} field
 */
RequestBase.prototype.unset = function(field){
  delete this._header[field.toLowerCase()];
  delete this.header[field];
  return this;
};

/**
 * Write the field `name` and `val`, or multiple fields with one object
 * for "multipart/form-data" request bodies.
 *
 * ``` js
 * request.post('/upload')
 *   .field('foo', 'bar')
 *   .end(callback);
 *
 * request.post('/upload')
 *   .field({ foo: 'bar', baz: 'qux' })
 *   .end(callback);
 * ```
 *
 * @param {String|Object} name
 * @param {String|Blob|File|Buffer|fs.ReadStream} val
 * @return {Request} for chaining
 * @api public
 */
RequestBase.prototype.field = function(name, val) {
  // name should be either a string or an object.
  if (null === name || undefined === name) {
    throw new Error('.field(name, val) name can not be empty');
  }

  if (this._data) {
    console.error(".field() can't be used if .send() is used. Please use only .send() or only .field() & .attach()");
  }

  if (isObject(name)) {
    for (var key in name) {
      this.field(key, name[key]);
    }
    return this;
  }

  if (Array.isArray(val)) {
    for (var i in val) {
      this.field(name, val[i]);
    }
    return this;
  }

  // val should be defined now
  if (null === val || undefined === val) {
    throw new Error('.field(name, val) val can not be empty');
  }
  if ('boolean' === typeof val) {
    val = '' + val;
  }
  this._getFormData().append(name, val);
  return this;
};

/**
 * Abort the request, and clear potential timeout.
 *
 * @return {Request}
 * @api public
 */
RequestBase.prototype.abort = function(){
  if (this._aborted) {
    return this;
  }
  this._aborted = true;
  this.xhr && this.xhr.abort(); // browser
  this.req && this.req.abort(); // node
  this.clearTimeout();
  this.emit('abort');
  return this;
};

RequestBase.prototype._auth = function(user, pass, options, base64Encoder) {
  switch (options.type) {
    case 'basic':
      this.set('Authorization', 'Basic ' + base64Encoder(user + ':' + pass));
      break;

    case 'auto':
      this.username = user;
      this.password = pass;
      break;

    case 'bearer': // usage would be .auth(accessToken, { type: 'bearer' })
      this.set('Authorization', 'Bearer ' + user);
      break;
  }
  return this;
};

/**
 * Enable transmission of cookies with x-domain requests.
 *
 * Note that for this to work the origin must not be
 * using "Access-Control-Allow-Origin" with a wildcard,
 * and also must set "Access-Control-Allow-Credentials"
 * to "true".
 *
 * @api public
 */

RequestBase.prototype.withCredentials = function(on) {
  // This is browser-only functionality. Node side is no-op.
  if (on == undefined) on = true;
  this._withCredentials = on;
  return this;
};

/**
 * Set the max redirects to `n`. Does noting in browser XHR implementation.
 *
 * @param {Number} n
 * @return {Request} for chaining
 * @api public
 */

RequestBase.prototype.redirects = function(n){
  this._maxRedirects = n;
  return this;
};

/**
 * Maximum size of buffered response body, in bytes. Counts uncompressed size.
 * Default 200MB.
 *
 * @param {Number} n
 * @return {Request} for chaining
 */
RequestBase.prototype.maxResponseSize = function(n){
  if ('number' !== typeof n) {
    throw TypeError("Invalid argument");
  }
  this._maxResponseSize = n;
  return this;
};

/**
 * Convert to a plain javascript object (not JSON string) of scalar properties.
 * Note as this method is designed to return a useful non-this value,
 * it cannot be chained.
 *
 * @return {Object} describing method, url, and data of this request
 * @api public
 */

RequestBase.prototype.toJSON = function() {
  return {
    method: this.method,
    url: this.url,
    data: this._data,
    headers: this._header,
  };
};

/**
 * Send `data` as the request body, defaulting the `.type()` to "json" when
 * an object is given.
 *
 * Examples:
 *
 *       // manual json
 *       request.post('/user')
 *         .type('json')
 *         .send('{"name":"tj"}')
 *         .end(callback)
 *
 *       // auto json
 *       request.post('/user')
 *         .send({ name: 'tj' })
 *         .end(callback)
 *
 *       // manual x-www-form-urlencoded
 *       request.post('/user')
 *         .type('form')
 *         .send('name=tj')
 *         .end(callback)
 *
 *       // auto x-www-form-urlencoded
 *       request.post('/user')
 *         .type('form')
 *         .send({ name: 'tj' })
 *         .end(callback)
 *
 *       // defaults to x-www-form-urlencoded
 *      request.post('/user')
 *        .send('name=tobi')
 *        .send('species=ferret')
 *        .end(callback)
 *
 * @param {String|Object} data
 * @return {Request} for chaining
 * @api public
 */

RequestBase.prototype.send = function(data){
  var isObj = isObject(data);
  var type = this._header['content-type'];

  if (this._formData) {
    console.error(".send() can't be used if .attach() or .field() is used. Please use only .send() or only .field() & .attach()");
  }

  if (isObj && !this._data) {
    if (Array.isArray(data)) {
      this._data = [];
    } else if (!this._isHost(data)) {
      this._data = {};
    }
  } else if (data && this._data && this._isHost(this._data)) {
    throw Error("Can't merge these send calls");
  }

  // merge
  if (isObj && isObject(this._data)) {
    for (var key in data) {
      this._data[key] = data[key];
    }
  } else if ('string' == typeof data) {
    // default to x-www-form-urlencoded
    if (!type) this.type('form');
    type = this._header['content-type'];
    if ('application/x-www-form-urlencoded' == type) {
      this._data = this._data
        ? this._data + '&' + data
        : data;
    } else {
      this._data = (this._data || '') + data;
    }
  } else {
    this._data = data;
  }

  if (!isObj || this._isHost(data)) {
    return this;
  }

  // default to json
  if (!type) this.type('json');
  return this;
};

/**
 * Sort `querystring` by the sort function
 *
 *
 * Examples:
 *
 *       // default order
 *       request.get('/user')
 *         .query('name=Nick')
 *         .query('search=Manny')
 *         .sortQuery()
 *         .end(callback)
 *
 *       // customized sort function
 *       request.get('/user')
 *         .query('name=Nick')
 *         .query('search=Manny')
 *         .sortQuery(function(a, b){
 *           return a.length - b.length;
 *         })
 *         .end(callback)
 *
 *
 * @param {Function} sort
 * @return {Request} for chaining
 * @api public
 */

RequestBase.prototype.sortQuery = function(sort) {
  // _sort default to true but otherwise can be a function or boolean
  this._sort = typeof sort === 'undefined' ? true : sort;
  return this;
};

/**
 * Compose querystring to append to req.url
 *
 * @api private
 */
RequestBase.prototype._finalizeQueryString = function(){
  var query = this._query.join('&');
  if (query) {
    this.url += (this.url.indexOf('?') >= 0 ? '&' : '?') + query;
  }
  this._query.length = 0; // Makes the call idempotent

  if (this._sort) {
    var index = this.url.indexOf('?');
    if (index >= 0) {
      var queryArr = this.url.substring(index + 1).split('&');
      if ('function' === typeof this._sort) {
        queryArr.sort(this._sort);
      } else {
        queryArr.sort();
      }
      this.url = this.url.substring(0, index) + '?' + queryArr.join('&');
    }
  }
};

// For backwards compat only
RequestBase.prototype._appendQueryString = function() {console.trace("Unsupported");}

/**
 * Invoke callback with timeout error.
 *
 * @api private
 */

RequestBase.prototype._timeoutError = function(reason, timeout, errno){
  if (this._aborted) {
    return;
  }
  var err = new Error(reason + timeout + 'ms exceeded');
  err.timeout = timeout;
  err.code = 'ECONNABORTED';
  err.errno = errno;
  this.timedout = true;
  this.abort();
  this.callback(err);
};

RequestBase.prototype._setTimeouts = function() {
  var self = this;

  // deadline
  if (this._timeout && !this._timer) {
    this._timer = setTimeout(function(){
      self._timeoutError('Timeout of ', self._timeout, 'ETIME');
    }, this._timeout);
  }
  // response timeout
  if (this._responseTimeout && !this._responseTimeoutTimer) {
    this._responseTimeoutTimer = setTimeout(function(){
      self._timeoutError('Response timeout of ', self._responseTimeout, 'ETIMEDOUT');
    }, this._responseTimeout);
  }
};


/***/ }),
/* 393 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Module dependencies.
 */

var utils = __webpack_require__(394);

/**
 * Expose `ResponseBase`.
 */

module.exports = ResponseBase;

/**
 * Initialize a new `ResponseBase`.
 *
 * @api public
 */

function ResponseBase(obj) {
  if (obj) return mixin(obj);
}

/**
 * Mixin the prototype properties.
 *
 * @param {Object} obj
 * @return {Object}
 * @api private
 */

function mixin(obj) {
  for (var key in ResponseBase.prototype) {
    obj[key] = ResponseBase.prototype[key];
  }
  return obj;
}

/**
 * Get case-insensitive `field` value.
 *
 * @param {String} field
 * @return {String}
 * @api public
 */

ResponseBase.prototype.get = function(field) {
  return this.header[field.toLowerCase()];
};

/**
 * Set header related properties:
 *
 *   - `.type` the content type without params
 *
 * A response of "Content-Type: text/plain; charset=utf-8"
 * will provide you with a `.type` of "text/plain".
 *
 * @param {Object} header
 * @api private
 */

ResponseBase.prototype._setHeaderProperties = function(header){
    // TODO: moar!
    // TODO: make this a util

    // content-type
    var ct = header['content-type'] || '';
    this.type = utils.type(ct);

    // params
    var params = utils.params(ct);
    for (var key in params) this[key] = params[key];

    this.links = {};

    // links
    try {
        if (header.link) {
            this.links = utils.parseLinks(header.link);
        }
    } catch (err) {
        // ignore
    }
};

/**
 * Set flags such as `.ok` based on `status`.
 *
 * For example a 2xx response will give you a `.ok` of __true__
 * whereas 5xx will be __false__ and `.error` will be __true__. The
 * `.clientError` and `.serverError` are also available to be more
 * specific, and `.statusType` is the class of error ranging from 1..5
 * sometimes useful for mapping respond colors etc.
 *
 * "sugar" properties are also defined for common cases. Currently providing:
 *
 *   - .noContent
 *   - .badRequest
 *   - .unauthorized
 *   - .notAcceptable
 *   - .notFound
 *
 * @param {Number} status
 * @api private
 */

ResponseBase.prototype._setStatusProperties = function(status){
    var type = status / 100 | 0;

    // status / class
    this.status = this.statusCode = status;
    this.statusType = type;

    // basics
    this.info = 1 == type;
    this.ok = 2 == type;
    this.redirect = 3 == type;
    this.clientError = 4 == type;
    this.serverError = 5 == type;
    this.error = (4 == type || 5 == type)
        ? this.toError()
        : false;

    // sugar
    this.created = 201 == status;
    this.accepted = 202 == status;
    this.noContent = 204 == status;
    this.badRequest = 400 == status;
    this.unauthorized = 401 == status;
    this.notAcceptable = 406 == status;
    this.forbidden = 403 == status;
    this.notFound = 404 == status;
    this.unprocessableEntity = 422 == status;
};


/***/ }),
/* 394 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Return the mime type for the given `str`.
 *
 * @param {String} str
 * @return {String}
 * @api private
 */

exports.type = function(str){
  return str.split(/ *; */).shift();
};

/**
 * Return header field parameters.
 *
 * @param {String} str
 * @return {Object}
 * @api private
 */

exports.params = function(str){
  return str.split(/ *; */).reduce(function(obj, str){
    var parts = str.split(/ *= */);
    var key = parts.shift();
    var val = parts.shift();

    if (key && val) obj[key] = val;
    return obj;
  }, {});
};

/**
 * Parse Link header fields.
 *
 * @param {String} str
 * @return {Object}
 * @api private
 */

exports.parseLinks = function(str){
  return str.split(/ *, */).reduce(function(obj, str){
    var parts = str.split(/ *; */);
    var url = parts[0].slice(1, -1);
    var rel = parts[1].split(/ *= */)[1].slice(1, -1);
    obj[rel] = url;
    return obj;
  }, {});
};

/**
 * Strip content related fields from `header`.
 *
 * @param {Object} header
 * @return {Object} header
 * @api private
 */

exports.cleanHeader = function(header, changesOrigin){
  delete header['content-type'];
  delete header['content-length'];
  delete header['transfer-encoding'];
  delete header['host'];
  // secuirty
  if (changesOrigin) {
    delete header['authorization'];
    delete header['cookie'];
  }
  return header;
};


/***/ }),
/* 395 */
/***/ (function(module, exports) {

function Agent() {
  this._defaults = [];
}

["use", "on", "once", "set", "query", "type", "accept", "auth", "withCredentials", "sortQuery", "retry", "ok", "redirects",
 "timeout", "buffer", "serialize", "parse", "ca", "key", "pfx", "cert"].forEach(function(fn) {
  /** Default setting for all requests from this agent */
  Agent.prototype[fn] = function(/*varargs*/) {
    this._defaults.push({fn:fn, arguments:arguments});
    return this;
  }
});

Agent.prototype._setDefaults = function(req) {
    this._defaults.forEach(function(def) {
      req[def.fn].apply(req, def.arguments);
    });
};

module.exports = Agent;


/***/ }),
/* 396 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _cart = _interopRequireDefault(__webpack_require__(397));

/**
 * @module Global BigCommerce Javascript
 * @description Clearinghouse for global BigCommerce JS API methods.
 */
var init = function init() {
  (0, _cart.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 397 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _isEmpty2 = _interopRequireDefault(__webpack_require__(19));

var _jsCookie = _interopRequireDefault(__webpack_require__(24));

var COOKIES = _interopRequireWildcard(__webpack_require__(25));

var GLOBALS = _interopRequireWildcard(__webpack_require__(11));

/**
 * @module Global BigCommerce Cart API Methods
 * @description A set of global cart functions that can be called in and script or plugin to get BC data.
 */

/**
 * @function setCartCookieNames
 * @default Due to an issue on some servers, cookies for WordPress need to be set with the `wp-` prefix.
 * TODO: This should be deprecated entirely in the next major release.
 */
var resetCartCookieNames = function resetCartCookieNames() {
  // Get deprecated cart cookies.
  var deprecatedCartID = _jsCookie.default.get(COOKIES.DEPRECATED_CART_ID_COOKIE_NAME);

  var deprecatedCartCount = _jsCookie.default.get(COOKIES.DEPRECATED_CART_ITEM_COUNT_COOKIE);

  var currentCartID = _jsCookie.default.get(COOKIES.CART_ID_COOKIE_NAME);

  var currentCartCount = _jsCookie.default.get(COOKIES.CART_ITEM_COUNT_COOKIE); // If there are no old or new cookies set, stop here.


  if (!deprecatedCartCount && !deprecatedCartID || currentCartID && currentCartCount) {
    return;
  } // Reset the cart ID cookie name


  if (!(0, _isEmpty2.default)(deprecatedCartID) && (0, _isEmpty2.default)(currentCartID)) {
    _jsCookie.default.set(COOKIES.CART_ID_COOKIE_NAME, deprecatedCartID);

    _jsCookie.default.remove(COOKIES.DEPRECATED_CART_ID_COOKIE_NAME);
  } // Reset the cart count cookie name


  if (!(0, _isEmpty2.default)(deprecatedCartCount) && (0, _isEmpty2.default)(currentCartCount)) {
    _jsCookie.default.set(COOKIES.CART_ITEM_COUNT_COOKIE, deprecatedCartCount);

    _jsCookie.default.remove(COOKIES.DEPRECATED_CART_ITEM_COUNT_COOKIE);
  }
};
/**
 * @function addGlobalCartMethods
 * @default clearinghouse for global functions for cart related data.
 */


var addGlobalCartMethods = function addGlobalCartMethods() {
  /**
   * @function getCartID
   * @default checks for a valid BC cart cookie and returns it's ID value.
   * @returns {string}
   */
  GLOBALS.CART.getCartID = function () {
    return _jsCookie.default.get(COOKIES.CART_ID_COOKIE_NAME);
  };
};

var init = function init() {
  resetCartCookieNames();
  addGlobalCartMethods();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 398 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _ajaxItems = _interopRequireDefault(__webpack_require__(109));

var _cartMenuItem = __webpack_require__(73);

var _cartPage = _interopRequireDefault(__webpack_require__(403));

var _addToCart = _interopRequireDefault(__webpack_require__(404));

var _miniCartWidget = _interopRequireDefault(__webpack_require__(405));

var _miniCartNav = _interopRequireDefault(__webpack_require__(413));

var _shippingCalculator = _interopRequireDefault(__webpack_require__(414));

var _couponCode = _interopRequireDefault(__webpack_require__(415));

/**
 * @module Cart
 * @description Clearinghouse for all cart scripts.
 */
var init = function init() {
  (0, _ajaxItems.default)();
  (0, _cartMenuItem.updateMenuQtyOnPageLoad)();
  (0, _cartPage.default)();
  (0, _addToCart.default)();
  (0, _miniCartWidget.default)();
  (0, _miniCartNav.default)();
  (0, _shippingCalculator.default)();
  (0, _couponCode.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 399 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.IMPORT_PROGRESS_NONCE = exports.IMPORT_PROGRESS_ACTION = exports.DIAGNOSTICS_SECTION = exports.DIAGNOSTICS_ACTION = exports.DIAGNOSTICS_NONCE = exports.COUNTRIES_OBJ = exports.ACCOUNT_ACTION = exports.ACCOUNT_NONCE = exports.ADMIN_AJAX = exports.PRODUCTS_ORDER = exports.PRODUCTS_RECENT = exports.PRODUCTS_SEARCH = exports.PRODUCTS_BRAND = exports.PRODUCTS_FLAG = exports.PRODUCTS_CATEGORY = exports.ADMIN_ICONS = exports.ADMIN_IMAGES = exports.SHORTCODE_ENDPOINT = exports.PRODUCTS_ENDPOINT = void 0;

__webpack_require__(180);

__webpack_require__(400);

var wpadmin = window.bigcommerce_admin_config || {};
var editorDialog = wpadmin.editor_dialog || {};
var PRODUCTS_ENDPOINT = editorDialog.product_api_url || '';
exports.PRODUCTS_ENDPOINT = PRODUCTS_ENDPOINT;
var SHORTCODE_ENDPOINT = editorDialog.shortcode_api_url || '';
exports.SHORTCODE_ENDPOINT = SHORTCODE_ENDPOINT;
var ADMIN_IMAGES = wpadmin.images_url || '';
exports.ADMIN_IMAGES = ADMIN_IMAGES;
var ADMIN_ICONS = wpadmin.icons_url || '';
exports.ADMIN_ICONS = ADMIN_ICONS;
var PRODUCTS_CATEGORY = wpadmin.categories;
exports.PRODUCTS_CATEGORY = PRODUCTS_CATEGORY;
var PRODUCTS_FLAG = wpadmin.flags;
exports.PRODUCTS_FLAG = PRODUCTS_FLAG;
var PRODUCTS_BRAND = wpadmin.brands;
exports.PRODUCTS_BRAND = PRODUCTS_BRAND;
var PRODUCTS_SEARCH = wpadmin.search;
exports.PRODUCTS_SEARCH = PRODUCTS_SEARCH;
var PRODUCTS_RECENT = wpadmin.recent;
exports.PRODUCTS_RECENT = PRODUCTS_RECENT;
var PRODUCTS_ORDER = wpadmin.sort_order;
exports.PRODUCTS_ORDER = PRODUCTS_ORDER;
var ADMIN_AJAX = wpadmin.admin_ajax;
exports.ADMIN_AJAX = ADMIN_AJAX;
var ACCOUNT_NONCE = wpadmin.account_rest_nonce;
exports.ACCOUNT_NONCE = ACCOUNT_NONCE;
var ACCOUNT_ACTION = wpadmin.account_rest_action;
exports.ACCOUNT_ACTION = ACCOUNT_ACTION;
var COUNTRIES_OBJ = wpadmin.countries;
exports.COUNTRIES_OBJ = COUNTRIES_OBJ;
var DIAGNOSTICS_NONCE = wpadmin.diagnostics_ajax_nonce;
exports.DIAGNOSTICS_NONCE = DIAGNOSTICS_NONCE;
var DIAGNOSTICS_ACTION = wpadmin.diagnostics_ajax_action;
exports.DIAGNOSTICS_ACTION = DIAGNOSTICS_ACTION;
var DIAGNOSTICS_SECTION = wpadmin.diagnostics_section;
exports.DIAGNOSTICS_SECTION = DIAGNOSTICS_SECTION;
var IMPORT_PROGRESS_ACTION = wpadmin.product_import_ajax_action;
exports.IMPORT_PROGRESS_ACTION = IMPORT_PROGRESS_ACTION;
var IMPORT_PROGRESS_NONCE = wpadmin.product_import_ajax_nonce;
exports.IMPORT_PROGRESS_NONCE = IMPORT_PROGRESS_NONCE;

/***/ }),
/* 400 */
/***/ (function(module, exports, __webpack_require__) {

// 21.2.5.3 get RegExp.prototype.flags()
if (__webpack_require__(38) && /./g.flags != 'g') __webpack_require__(83).f(RegExp.prototype, 'flags', {
  configurable: true,
  get: __webpack_require__(401)
});


/***/ }),
/* 401 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 21.2.5.3 get RegExp.prototype.flags
var anObject = __webpack_require__(123);
module.exports = function () {
  var that = anObject(this);
  var result = '';
  if (that.global) result += 'g';
  if (that.ignoreCase) result += 'i';
  if (that.multiline) result += 'm';
  if (that.unicode) result += 'u';
  if (that.sticky) result += 'y';
  return result;
};


/***/ }),
/* 402 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.GUTENBERG_CHANNEL_INDICATOR = exports.GUTENBERG_STORE_LINK = exports.GUTENBERG_WISHLIST = exports.GUTENBERG_PRODUCT_COMPONENTS = exports.GUTENBERG_PRODUCT_REVIEWS = exports.GUTENBERG_GIFT_CERTIFICATE_BALANCE = exports.GUTENBERG_GIFT_CERTIFICATE_FORM = exports.GUTENBERG_REGISTER = exports.GUTENBERG_LOGIN = exports.GUTENBERG_ORDERS = exports.GUTENBERG_ADDRESS = exports.GUTENBERG_ACCOUNT = exports.GUTENBERG_CHECKOUT = exports.GUTENBERG_CART = exports.GUTENBERG_PRODUCTS = exports.GUTENBERG_BLOCKS = exports.gutenbergconfig = void 0;
var gutenbergconfig = window.bigcommerce_gutenberg_config || {};
exports.gutenbergconfig = gutenbergconfig;
var GUTENBERG_BLOCKS = gutenbergconfig.blocks || {};
exports.GUTENBERG_BLOCKS = GUTENBERG_BLOCKS;
var GUTENBERG_PRODUCTS = GUTENBERG_BLOCKS['bigcommerce/products'] || {};
exports.GUTENBERG_PRODUCTS = GUTENBERG_PRODUCTS;
var GUTENBERG_CART = GUTENBERG_BLOCKS['bigcommerce/cart'] || {};
exports.GUTENBERG_CART = GUTENBERG_CART;
var GUTENBERG_CHECKOUT = GUTENBERG_BLOCKS['bigcommerce/checkout'] || {};
exports.GUTENBERG_CHECKOUT = GUTENBERG_CHECKOUT;
var GUTENBERG_ACCOUNT = GUTENBERG_BLOCKS['bigcommerce/account-profile'] || {};
exports.GUTENBERG_ACCOUNT = GUTENBERG_ACCOUNT;
var GUTENBERG_ADDRESS = GUTENBERG_BLOCKS['bigcommerce/address-list'] || {};
exports.GUTENBERG_ADDRESS = GUTENBERG_ADDRESS;
var GUTENBERG_ORDERS = GUTENBERG_BLOCKS['bigcommerce/order-history'] || {};
exports.GUTENBERG_ORDERS = GUTENBERG_ORDERS;
var GUTENBERG_LOGIN = GUTENBERG_BLOCKS['bigcommerce/login-form'] || {};
exports.GUTENBERG_LOGIN = GUTENBERG_LOGIN;
var GUTENBERG_REGISTER = GUTENBERG_BLOCKS['bigcommerce/registration-form'] || {};
exports.GUTENBERG_REGISTER = GUTENBERG_REGISTER;
var GUTENBERG_GIFT_CERTIFICATE_FORM = GUTENBERG_BLOCKS['bigcommerce/gift-certificate-form'] || {};
exports.GUTENBERG_GIFT_CERTIFICATE_FORM = GUTENBERG_GIFT_CERTIFICATE_FORM;
var GUTENBERG_GIFT_CERTIFICATE_BALANCE = GUTENBERG_BLOCKS['bigcommerce/gift-certificate-balance'] || {};
exports.GUTENBERG_GIFT_CERTIFICATE_BALANCE = GUTENBERG_GIFT_CERTIFICATE_BALANCE;
var GUTENBERG_PRODUCT_REVIEWS = GUTENBERG_BLOCKS['bigcommerce/product-reviews'] || {};
exports.GUTENBERG_PRODUCT_REVIEWS = GUTENBERG_PRODUCT_REVIEWS;
var GUTENBERG_PRODUCT_COMPONENTS = GUTENBERG_BLOCKS['bigcommerce/product-components'] || {};
exports.GUTENBERG_PRODUCT_COMPONENTS = GUTENBERG_PRODUCT_COMPONENTS;
var GUTENBERG_WISHLIST = GUTENBERG_BLOCKS['bigcommerce/wishlist'] || {};
exports.GUTENBERG_WISHLIST = GUTENBERG_WISHLIST;
var GUTENBERG_STORE_LINK = gutenbergconfig.store_link || '';
exports.GUTENBERG_STORE_LINK = GUTENBERG_STORE_LINK;
var GUTENBERG_CHANNEL_INDICATOR = gutenbergconfig.channel_indicator || '';
exports.GUTENBERG_CHANNEL_INDICATOR = GUTENBERG_CHANNEL_INDICATOR;

/***/ }),
/* 403 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

var _interopRequireWildcard = __webpack_require__(1);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var tools = _interopRequireWildcard(__webpack_require__(2));

var _i18n = __webpack_require__(7);

var _queryToJson = _interopRequireDefault(__webpack_require__(71));

/**
 * @module Cart Page
 * @description JS scripts for the cart page that do not require ajax.
 */
var el = {
  container: tools.getNodes('bc-cart')[0]
};

var handleCartErrorOnPageLoad = function handleCartErrorOnPageLoad() {
  var queryObj = (0, _queryToJson.default)();
  var APIError = queryObj['api-error'];

  if (!APIError) {
    return;
  }

  if (APIError === '502') {
    el.APIErrorNotification.innerHTML = _i18n.NLS.cart.add_to_cart_error_502;
    tools.closest(el.APIErrorNotification, '.bc-cart-error').classList.add('message-active');
  }
};

var cacheElements = function cacheElements() {
  el.APIErrorNotification = tools.getNodes('bc-cart-error-message', false, el.container, false)[0];
};

var bindEvents = function bindEvents() {
  handleCartErrorOnPageLoad();
};

var init = function init() {
  if (!el.container) {
    return;
  }

  cacheElements();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 404 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _values = _interopRequireDefault(__webpack_require__(33));

var _slicedToArray2 = _interopRequireDefault(__webpack_require__(102));

var _entries = _interopRequireDefault(__webpack_require__(65));

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _jsCookie = _interopRequireDefault(__webpack_require__(24));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _events = __webpack_require__(5);

var _ajax = __webpack_require__(26);

var _wpSettings = __webpack_require__(11);

var _cookies = __webpack_require__(25);

var _events2 = __webpack_require__(34);

var _i18n = __webpack_require__(7);

var _cartMenuItem = __webpack_require__(73);

var state = {
  isFetching: false,
  ajax_enabled: _wpSettings.AJAX_CART_ENABLED,
  cartItem: {
    product_id: '',
    variant_id: '',
    options: {},
    quantity: 1
  },
  cartMessage: ''
};
/**
 * @function buildAjaxQueryString
 * @description Build a query string of all options and modifiers for a selected variant.
 * @returns {string}
 */

var buildAjaxQueryString = function buildAjaxQueryString() {
  var str = [];
  (0, _entries.default)(state.cartItem).forEach(function (_ref) {
    var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
        key = _ref2[0],
        value = _ref2[1];

    if (!value || value.length === 0) {
      return;
    }

    if (key === 'options') {
      (0, _entries.default)(value).forEach(function (_ref3, index) {
        var _ref4 = (0, _slicedToArray2.default)(_ref3, 2),
            objectKey = _ref4[0],
            objectValue = _ref4[1];

        (0, _entries.default)(objectValue).forEach(function (_ref5) {
          var _ref6 = (0, _slicedToArray2.default)(_ref5, 2),
              objKey = _ref6[0],
              objValue = _ref6[1];

          var k = encodeURIComponent(objKey);
          var v = encodeURIComponent(objValue);
          str.push("".concat(key, "[").concat(index, "][").concat(k, "]=").concat(v));
        });
      });
    } else {
      var k = encodeURIComponent(key);
      var v = encodeURIComponent(value);
      str.push("".concat(k, "=").concat(v));
    }
  });
  return str ? str.join('&') : '';
};
/**
 * @function handleProductModifiers
 * @description Parse the modifiers object for a selected variant and set it to the cartItem state.
 * @param modifiers
 */


var handleProductModifiers = function handleProductModifiers() {
  var modifiers = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  modifiers.forEach(function (field, index) {
    // If a checkbox field is not checked, or a text/textarea field is blank, do not submit that data.
    if (!field.value || (field.type === 'checkbox' || field.type === 'radio') && !field.checked) {
      return;
    }

    state.cartItem.options[index] = {
      id: parseFloat(field.dataset.optionId),
      value: field.value
    };
  });
};
/**
 * @function getAjaxQueryString
 * @description Reset the current cartItem object. Repopulate it with the current selected variant. Return the
 * 				query string to be submitted with the ajax call.
 * @param button
 * @returns {string}
 */


var getAjaxQueryString = function getAjaxQueryString(button) {
  state.cartItem.product_id = '';
  state.cartItem.variant = '';
  state.cartItem.options = {};
  state.cartItem.quantity = 1;
  var form = tools.closest(button, '.bc-product-form');
  var hasOptions = tools.getNodes('bc-product-option-field', true, form);
  var qty = tools.getNodes('.bc-product-form__quantity-input', false, form, true)[0]; // Always need a product_id

  state.cartItem.product_id = button.dataset.js; // Set the quantity to be added to the cart

  state.cartItem.quantity = qty ? qty.value : 1; // Product Card or product without options.

  if (!hasOptions || !hasOptions.length) {
    return buildAjaxQueryString();
  } // Handle Options


  handleProductModifiers(hasOptions);
  return buildAjaxQueryString();
};
/**
 * @function updateCartItemCount
 * @description Upon successfully adding a item to the cart, get the new cart count from the response and update the
 *     			cart menu item.
 * @param data
 */


var updateCartItemCount = function updateCartItemCount() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var menuCartCount = tools.getNodes('bc-cart-item-count')[0];

  if (!menuCartCount) {
    return;
  }

  var cartCount = 0;
  tools.removeClass(menuCartCount, 'full');
  (0, _values.default)(data.items).forEach(function (item) {
    if (!item.quantity) {
      return;
    }

    cartCount += parseFloat(item.quantity);
  });
  (0, _delay2.default)(function () {
    return tools.addClass(menuCartCount, 'full');
  }, 150);
  menuCartCount.textContent = cartCount.toString();
  (0, _cartMenuItem.cartMenuSet)(cartCount);
};
/**
 * @function createAjaxResponseMessage
 * @description Construct a response message to be displayed on the page with the product submitted to the cart API.
 * @param wrapper {string} container for the message to be attached to.
 * @param message {string} Global error and success messages from the plugin's global i18n JS object.
 * @param error {boolean} Whether or not this response is an error.
 */


var createAjaxResponseMessage = function createAjaxResponseMessage() {
  var wrapper = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var message = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var error = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  var messageWrapper = tools.getNodes('bc-ajax-add-to-cart-message', false, wrapper)[0];

  if (!messageWrapper) {
    return;
  }

  var statusClass = error ? 'bc-alert--error' : 'bc-alert--success';
  var messageElement = document.createElement('p');
  tools.addClass(messageElement, 'bc-ajax-add-to-cart__message');
  tools.addClass(messageElement, 'bc-alert');
  tools.addClass(messageElement, statusClass);
  messageElement.innerHTML = message;
  messageWrapper.innerHTML = '';
  messageWrapper.appendChild(messageElement);
};
/**
 * @function handleFetchingState
 * @description While the ajax request is being performed, handle the cart button state and animations.
 * @param button
 */


var handleFetchingState = function handleFetchingState() {
  var button = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

  if (!button) {
    var allCartButtons = tools.getNodes('.bc-btn--add_to_cart', true, document, true);
    allCartButtons.forEach(handleFetchingState);
    return;
  }

  if (state.isFetching) {
    button.setAttribute('disabled', 'disabled');
    tools.addClass(button, 'bc-ajax-cart-processing');
    return;
  }

  button.removeAttribute('disabled');
  tools.removeClass(button, 'bc-ajax-cart-processing');
};
/**
 * @function handleCartErrors
 * @description Using the error response codes from the API response, set the corresponding message to print on the page.
 * @param response
 */


var handleCartErrors = function handleCartErrors(response) {
  state.cartMessage = '';
  var status = response.data.status.toString(); // If we're missing a status code, just use our default string.

  if (!status) {
    state.cartMessage = _i18n.NLS.cart.ajax_add_to_cart_error;
  } // Map the error code to API's response message.


  switch (true) {
    case status.charAt(0) === '4':
      state.cartMessage = response.message;
      break;

    case status.charAt(0) === '5':
    default:
      state.cartMessage = _i18n.NLS.cart.ajax_add_to_cart_error;
  }
};
/**
 * @function handleAjaxAddToCartRequest
 * @description Payload function that handles submitting the product to the cart API.
 * @param e
 */


var handleAjaxAddToCartRequest = function handleAjaxAddToCartRequest(e) {
  e.preventDefault();
  state.isFetching = true;
  var cartButton = e.delegateTarget;
  var form = tools.closest(cartButton, '.bc-product-form');

  if (!form.checkValidity()) {
    form.reportValidity(); // Check HTML5 form field validity and report on errors.

    return;
  }

  var cartID = _jsCookie.default.get(_cookies.CART_ID_COOKIE_NAME);

  var url = cartID ? "".concat(_wpSettings.CART_API_BASE, "/").concat(cartID) : _wpSettings.CART_API_BASE;
  var query = getAjaxQueryString(cartButton);
  handleFetchingState(cartID ? cartButton : null);
  (0, _ajax.wpAPIAddToCartAjax)(url, query).set('X-WP-Nonce', _wpSettings.AJAX_CART_NONCE).end(function (err, res) {
    state.isFetching = false;
    handleFetchingState(cartID ? cartButton : null);

    if (err) {
      console.error(err);
      handleCartErrors(res.body);
      createAjaxResponseMessage(form, state.cartMessage, true);
      return;
    }

    createAjaxResponseMessage(form, _i18n.NLS.cart.ajax_add_to_cart_success, false);
    updateCartItemCount(res.body);
    (0, _cartMenuItem.updateFlatsomeCartMenuQty)();
    (0, _cartMenuItem.updateFlatsomeCartMenuPrice)(res.body);
    (0, _events.trigger)({
      event: _events2.AJAX_CART_UPDATE,
      native: false
    });
    (0, _events.trigger)({
      event: 'bigcommerce/analytics_trigger',
      data: {
        cartButton: cartButton,
        cartID: res.body.cart_id
      },
      native: false
    });
  });
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)('.bc-btn--add_to_cart', 'click', handleAjaxAddToCartRequest);
};

var init = function init() {
  if (!state.ajax_enabled) {
    return;
  }

  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 405 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _entries = _interopRequireDefault(__webpack_require__(65));

var _values = _interopRequireDefault(__webpack_require__(33));

var _uniqueId2 = _interopRequireDefault(__webpack_require__(23));

var _some2 = _interopRequireDefault(__webpack_require__(406));

var _isEmpty2 = _interopRequireDefault(__webpack_require__(19));

var _jsCookie = _interopRequireDefault(__webpack_require__(24));

var _events = __webpack_require__(5);

var tools = _interopRequireWildcard(__webpack_require__(2));

var _cookies = __webpack_require__(25);

var _ajax = __webpack_require__(26);

var _i18n = __webpack_require__(7);

var _cartState = _interopRequireDefault(__webpack_require__(47));

var _wpSettings = __webpack_require__(11);

var _events2 = __webpack_require__(34);

var _cartTemplates = __webpack_require__(72);

var _cartMenuItem = __webpack_require__(73);

var _ajaxItems = _interopRequireDefault(__webpack_require__(109));

/**
 * @function setEmptyCart
 * @description If the cart is empty, fetch and set the empty cart template.
 * @param miniCartID
 */
var setEmptyCart = function setEmptyCart() {
  var miniCartID = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  (0, _values.default)(_cartState.default.instances.carts).forEach(function (widget) {
    widget.innerHTML = _cartTemplates.cartEmpty;
  });
  _cartState.default.isFetching = false;
  (0, _events.trigger)({
    event: _events2.HANDLE_CART_STATE,
    data: {
      miniCartID: miniCartID
    },
    native: false
  });
};
/**
 * @function updateCartMenuCount
 * @description if we have a response from the mini cart endpoint and the cart count does not match the cookie, run this.
 * @param count
 */


var updateCartMenuCount = function updateCartMenuCount() {
  var count = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var cookie = Number(_jsCookie.default.get(_cookies.CART_ITEM_COUNT_COOKIE));

  if (count === cookie) {
    return;
  }

  _jsCookie.default.set(_cookies.CART_ITEM_COUNT_COOKIE, count);

  (0, _cartMenuItem.updateCartMenuItem)();
};
/**
 * @function loadMiniCarts
 * @description loads the template to display mini-cart widgets
 * @param e
 */


var loadMiniCarts = function loadMiniCarts(e) {
  if ((0, _entries.default)(_cartState.default.instances.carts).length <= 0) {
    return;
  } // Check for event detail data from outside events.


  var eventMiniCartID = e ? e.detail.miniCartID : ''; // Get the current Cart ID

  var cartID = _jsCookie.default.get(_cookies.CART_ID_COOKIE_NAME);

  var cartURL = (0, _isEmpty2.default)(cartID) ? '' : "".concat(_wpSettings.CART_API_BASE, "/").concat(cartID).concat(_i18n.NLS.cart.mini_url_param); // Start the handle_cart_state event.

  _cartState.default.isFetching = true;
  (0, _events.trigger)({
    event: _events2.HANDLE_CART_STATE,
    data: {
      miniCartID: eventMiniCartID
    },
    native: false
  }); // If we don't have a cartID and URL, stop here.

  if ((0, _isEmpty2.default)(cartURL)) {
    setEmptyCart();
    return;
  }

  (0, _ajax.wpAPIMiniCartGet)(cartURL).set('X-WP-Nonce', _wpSettings.AJAX_CART_NONCE).end(function (err, res) {
    if (err) {
      console.error(err);
      setEmptyCart();
      return;
    } // Loop through available mini carts and update cart HTML


    (0, _values.default)(_cartState.default.instances.carts).forEach(function (widget) {
      // Skip this cart if it is the one that triggered the original event.
      if (widget.dataset.miniCartId === eventMiniCartID) {
        return;
      }

      widget.innerHTML = res.body.rendered;
      (0, _ajaxItems.default)(); // If the count key exists in the response object, proceed with updating the menu item count.

      if (!(0, _some2.default)(res.body.count, _isEmpty2.default)) {
        updateCartMenuCount(res.body.count);
      }
    }); // End the handle_cart_state event.

    _cartState.default.isFetching = false;
    (0, _events.trigger)({
      event: _events2.HANDLE_CART_STATE,
      data: {
        miniCartID: eventMiniCartID
      },
      native: false
    });
  });
};

var cacheElements = function cacheElements() {
  tools.getNodes('bc-mini-cart', true).forEach(function (cart) {
    var miniCartID = (0, _uniqueId2.default)('bc-mini-cart-');
    tools.addClass(cart, 'initialized');
    cart.setAttribute('data-mini-cart-id', miniCartID);
    _cartState.default.instances.carts[miniCartID] = cart;
  });
};

var bindEvents = function bindEvents() {
  (0, _events.on)(document, _events2.AJAX_CART_UPDATE, loadMiniCarts);
};

var init = function init() {
  cacheElements();
  bindEvents();
  loadMiniCarts();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 406 */
/***/ (function(module, exports, __webpack_require__) {

var arraySome = __webpack_require__(173),
    baseIteratee = __webpack_require__(169),
    baseSome = __webpack_require__(407),
    isArray = __webpack_require__(12),
    isIterateeCall = __webpack_require__(119);

/**
 * Checks if `predicate` returns truthy for **any** element of `collection`.
 * Iteration is stopped once `predicate` returns truthy. The predicate is
 * invoked with three arguments: (value, index|key, collection).
 *
 * @static
 * @memberOf _
 * @since 0.1.0
 * @category Collection
 * @param {Array|Object} collection The collection to iterate over.
 * @param {Function} [predicate=_.identity] The function invoked per iteration.
 * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
 * @returns {boolean} Returns `true` if any element passes the predicate check,
 *  else `false`.
 * @example
 *
 * _.some([null, 0, 'yes', false], Boolean);
 * // => true
 *
 * var users = [
 *   { 'user': 'barney', 'active': true },
 *   { 'user': 'fred',   'active': false }
 * ];
 *
 * // The `_.matches` iteratee shorthand.
 * _.some(users, { 'user': 'barney', 'active': false });
 * // => false
 *
 * // The `_.matchesProperty` iteratee shorthand.
 * _.some(users, ['active', false]);
 * // => true
 *
 * // The `_.property` iteratee shorthand.
 * _.some(users, 'active');
 * // => true
 */
function some(collection, predicate, guard) {
  var func = isArray(collection) ? arraySome : baseSome;
  if (guard && isIterateeCall(collection, predicate, guard)) {
    predicate = undefined;
  }
  return func(collection, baseIteratee(predicate, 3));
}

module.exports = some;


/***/ }),
/* 407 */
/***/ (function(module, exports, __webpack_require__) {

var baseEach = __webpack_require__(408);

/**
 * The base implementation of `_.some` without support for iteratee shorthands.
 *
 * @private
 * @param {Array|Object} collection The collection to iterate over.
 * @param {Function} predicate The function invoked per iteration.
 * @returns {boolean} Returns `true` if any element passes the predicate check,
 *  else `false`.
 */
function baseSome(collection, predicate) {
  var result;

  baseEach(collection, function(value, index, collection) {
    result = predicate(value, index, collection);
    return !result;
  });
  return !!result;
}

module.exports = baseSome;


/***/ }),
/* 408 */
/***/ (function(module, exports, __webpack_require__) {

var baseForOwn = __webpack_require__(409),
    createBaseEach = __webpack_require__(412);

/**
 * The base implementation of `_.forEach` without support for iteratee shorthands.
 *
 * @private
 * @param {Array|Object} collection The collection to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Array|Object} Returns `collection`.
 */
var baseEach = createBaseEach(baseForOwn);

module.exports = baseEach;


/***/ }),
/* 409 */
/***/ (function(module, exports, __webpack_require__) {

var baseFor = __webpack_require__(410),
    keys = __webpack_require__(50);

/**
 * The base implementation of `_.forOwn` without support for iteratee shorthands.
 *
 * @private
 * @param {Object} object The object to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @returns {Object} Returns `object`.
 */
function baseForOwn(object, iteratee) {
  return object && baseFor(object, iteratee, keys);
}

module.exports = baseForOwn;


/***/ }),
/* 410 */
/***/ (function(module, exports, __webpack_require__) {

var createBaseFor = __webpack_require__(411);

/**
 * The base implementation of `baseForOwn` which iterates over `object`
 * properties returned by `keysFunc` and invokes `iteratee` for each property.
 * Iteratee functions may exit iteration early by explicitly returning `false`.
 *
 * @private
 * @param {Object} object The object to iterate over.
 * @param {Function} iteratee The function invoked per iteration.
 * @param {Function} keysFunc The function to get the keys of `object`.
 * @returns {Object} Returns `object`.
 */
var baseFor = createBaseFor();

module.exports = baseFor;


/***/ }),
/* 411 */
/***/ (function(module, exports) {

/**
 * Creates a base function for methods like `_.forIn` and `_.forOwn`.
 *
 * @private
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {Function} Returns the new base function.
 */
function createBaseFor(fromRight) {
  return function(object, iteratee, keysFunc) {
    var index = -1,
        iterable = Object(object),
        props = keysFunc(object),
        length = props.length;

    while (length--) {
      var key = props[fromRight ? length : ++index];
      if (iteratee(iterable[key], key, iterable) === false) {
        break;
      }
    }
    return object;
  };
}

module.exports = createBaseFor;


/***/ }),
/* 412 */
/***/ (function(module, exports, __webpack_require__) {

var isArrayLike = __webpack_require__(29);

/**
 * Creates a `baseEach` or `baseEachRight` function.
 *
 * @private
 * @param {Function} eachFunc The function to iterate over a collection.
 * @param {boolean} [fromRight] Specify iterating from right to left.
 * @returns {Function} Returns the new base function.
 */
function createBaseEach(eachFunc, fromRight) {
  return function(collection, iteratee) {
    if (collection == null) {
      return collection;
    }
    if (!isArrayLike(collection)) {
      return eachFunc(collection, iteratee);
    }
    var length = collection.length,
        index = fromRight ? length : -1,
        iterable = Object(collection);

    while ((fromRight ? index-- : ++index < length)) {
      if (iteratee(iterable[index], index, iterable) === false) {
        break;
      }
    }
    return collection;
  };
}

module.exports = createBaseEach;


/***/ }),
/* 413 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _uniqueId2 = _interopRequireDefault(__webpack_require__(23));

var _isEmpty2 = _interopRequireDefault(__webpack_require__(19));

var _jsCookie = _interopRequireDefault(__webpack_require__(24));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _wpSettings = __webpack_require__(11);

var _ajax = __webpack_require__(26);

var _cartState = _interopRequireDefault(__webpack_require__(47));

var _events = __webpack_require__(5);

var _cookies = __webpack_require__(25);

var _events2 = __webpack_require__(34);

var _i18n = __webpack_require__(7);

var _state = _interopRequireDefault(__webpack_require__(129));

var _ajaxItems = _interopRequireDefault(__webpack_require__(109));

var _cartTemplates = __webpack_require__(72);

var el = {
  cartMenuItems: tools.getNodes('.menu-item-bigcommerce-cart', true, document, true)
};
var miniCartNavState = {
  show: false,
  clickHandler: null
};
/**
 * @function showHideMiniCart
 * @description Show or hide the mini cart in the menu item.
 * @param miniCartWrapper
 */

var showHideMiniCart = function showHideMiniCart(miniCartWrapper) {
  if (miniCartNavState.show) {
    tools.addClass(miniCartWrapper, 'bc-show-mini-cart-nav');
    return;
  }

  tools.removeClass(miniCartWrapper, 'bc-show-mini-cart-nav');
};
/**
 * @function setEmptyCart
 * @description If the cart is empty, fetch and set the empty cart template.
 */


var setEmptyCart = function setEmptyCart(miniCartWrapper) {
  miniCartWrapper.innerHTML = _cartTemplates.cartEmpty;
};
/**
 * @function handleClicks
 * @description handle cart menu item clicks.
 * @param e
 */


var handleClicks = function handleClicks(e) {
  if (!miniCartNavState.clickHandler) {
    return;
  }

  e.preventDefault();
  e.stopPropagation();
  var miniCartWrapper = tools.getNodes('bc-mini-cart', false, e.delegateTarget.parentNode)[0]; // Toggle visibility of the mini cart nav menu item.

  if (tools.hasClass(miniCartWrapper, 'bc-show-mini-cart-nav')) {
    miniCartNavState.show = false;
    showHideMiniCart(miniCartWrapper);
  } else {
    miniCartNavState.show = true;
    showHideMiniCart(miniCartWrapper);
  } // If the mini cart had been created already. We do not need to reload it. Changes will occur when events are fired.


  if (tools.hasClass(miniCartWrapper, 'initialized')) {
    return;
  }

  var cartID = _jsCookie.default.get(_cookies.CART_ID_COOKIE_NAME);

  var cartURL = (0, _isEmpty2.default)(cartID) ? '' : "".concat(_wpSettings.CART_API_BASE, "/").concat(cartID).concat(_i18n.NLS.cart.mini_url_param);

  if ((0, _isEmpty2.default)(cartURL)) {
    setEmptyCart();
    return;
  }

  (0, _ajax.wpAPIMiniCartGet)(cartURL).end(function (err, res) {
    if (err) {
      console.error(err);
      setEmptyCart(miniCartWrapper);
      return;
    }

    miniCartWrapper.innerHTML = res.body.rendered;
    (0, _ajaxItems.default)();
  });
};
/**
 * @function handleOffMenuClicks
 * @description Handle clicks outside of the current live mini cart from a menu item and close it.
 * @param e
 */


var handleOffMenuClicks = function handleOffMenuClicks(e) {
  var isMenuLink = tools.hasClass(e.target.parentNode, 'menu-item-bigcommerce-cart');

  if (isMenuLink) {
    return;
  }

  tools.getNodes('.bc-mini-cart--nav-menu', true, document, true).forEach(function (cart) {
    if (!cart.contains(e.target)) {
      miniCartNavState.show = false;
      showHideMiniCart(cart);
    }
  });
};
/**
 * @function initCartMenuItems
 * @description Kick off the cart menu items and inject mini cart wrappers.
 */


var initCartMenuItems = function initCartMenuItems() {
  el.cartMenuItems.forEach(function (menuItem) {
    var miniCartID = (0, _uniqueId2.default)('bc-mini-cart-');
    var fragment = document.createElement('div');
    tools.addClass(fragment, 'bc-mini-cart');
    tools.addClass(fragment, 'bc-mini-cart--nav-menu');
    fragment.setAttribute('data-js', 'bc-mini-cart');
    fragment.setAttribute('data-mini-cart-id', miniCartID);
    fragment.textContent = _i18n.NLS.cart.mini_cart_loading;
    tools.addClass(fragment, 'initialized');
    _cartState.default.instances.carts[miniCartID] = fragment;
    menuItem.appendChild(fragment);
  });
  (0, _events.trigger)({
    event: _events2.AJAX_CART_UPDATE,
    native: false
  });
};
/**
 * @function handleViewport
 * @description Enable or destroy the event listener for handling cart menu clicks on mobile and desktop.
 */


var handleViewport = function handleViewport() {
  if (_state.default.is_mobile) {
    // destroy the event handler if it exists.
    if (miniCartNavState.clickHandler) {
      miniCartNavState.clickHandler.destroy();
    }

    miniCartNavState.clickHandler = null;
    return;
  }

  if (_state.default.is_desktop && !miniCartNavState.clickHandler) {
    miniCartNavState.clickHandler = (0, _delegate.default)(document, '.menu-item-bigcommerce-cart > a', 'click', handleClicks);
  }
};

var bindEvents = function bindEvents() {
  window.addEventListener('click', handleOffMenuClicks);
  (0, _events.on)(document, 'modern_tribe/resize_executed', handleViewport);
};

var init = function init() {
  if (!_wpSettings.MINI_CART || !el.cartMenuItems) {
    return;
  }

  bindEvents();
  handleViewport();
  initCartMenuItems();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 414 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _values = _interopRequireDefault(__webpack_require__(33));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _cartState = _interopRequireDefault(__webpack_require__(47));

var _spin = __webpack_require__(108);

var _events = __webpack_require__(34);

var _wpSettings = __webpack_require__(11);

var _i18n = __webpack_require__(7);

var _ajax = __webpack_require__(26);

var _events2 = __webpack_require__(5);

/**
 * @module Shipping Calculator
 * @description Shipping estimate calculator
 */
var el = {
  calculator: tools.getNodes('bc-shipping-calculator', false, document)[0]
};
/**
 * @description Module State object.
 * @type {{shippingItemCount: number, fetching: boolean, spinner: string, isActive: boolean, hasError: boolean, subtotal: string}}
 */

var state = {
  fetching: false,
  isActive: false,
  hasError: false,
  spinner: '',
  shippingItemCount: 0,
  subtotal: ''
};
/**
 * @function handleShippingError
 * @description Handle errors and error messaging for shipping calculator requests.
 */

var handleShippingError = function handleShippingError() {
  var errorMessage = tools.getNodes('.bc-shipping-error', false, el.calculator, true)[0];

  if (state.hasError && !errorMessage) {
    var errorWrapper = document.createElement('div');
    tools.addClass(errorWrapper, 'bc-shipping-error');
    errorWrapper.innerText = _i18n.NLS.cart.shipping_calc_error;
    el.calculator.appendChild(errorWrapper);
    return;
  }

  if (!state.hasError && errorMessage) {
    errorMessage.parentNode.removeChild(errorMessage);
  }
};
/**
 * @function setValidCartCount
 * @description if we have a 'peritem' shipping option, count and store the number of valid items to ship.
 * @param data
 */


var setValidCartCount = function setValidCartCount(data) {
  // Count Items
  var count = 0;
  (0, _values.default)(data.items).forEach(function (item) {
    if (item.bigcommerce_product_type[0].slug === 'digital') {
      return;
    }

    count += item.quantity;
  });
  state.shippingItemCount = count;
};
/**
 * @function resetShippingCalculator
 * @description reset the shipping calculator if the cart is updated via ajax.
 */


var resetShippingCalculator = function resetShippingCalculator(e) {
  state.isActive = false;
  state.hasError = false;
  handleShippingError(); // On a cart ajax refresh event

  var cartData = e.detail.cartData ? e.detail.cartData : e.detail.data;

  if (cartData) {
    // First set the subtotal to the cart state.
    state.subtotal = cartData.subtotal.formatted; // If the only remaining items in the card are digital goods, remove the shipping calculator all together.

    setValidCartCount(cartData);

    if (state.shippingItemCount === 0) {
      el.calculator.parentNode.removeChild(el.calculator);
      return;
    }
  } // Update the subtotal field with the current subtotal in state.


  el.currentSubtotal.innerText = state.subtotal; // If we have any shipping fields, remove them!

  var shippingFields = tools.getNodes('.bc-shipping-calculator-fields', false, el.calculator, true)[0];

  if (!shippingFields) {
    return;
  }

  shippingFields.parentNode.removeChild(shippingFields);
};
/**
 * @function createSpinner
 * @description create a spinner instance to be used if the shipping calculator is requested.
 */


var createSpinner = function createSpinner() {
  var spinnerOptions = {
    opacity: 0.5,
    scale: 0.5,
    lines: 12
  };
  new _spin.Spinner(spinnerOptions).spin(el.spinner);
};
/**
 * @function handleSpinnerState
 * @description handle the state of the cart, the spinner and the fields during a fetch request.
 */


var handleSpinnerState = function handleSpinnerState() {
  if (_cartState.default.isFetching) {
    (0, _events2.trigger)({
      event: _events.HANDLE_CART_STATE,
      native: false
    });
    tools.addClass(el.spinner, 'show-spinner');
    return;
  }

  tools.removeClass(el.spinner, 'show-spinner');
  (0, _events2.trigger)({
    event: _events.HANDLE_CART_STATE,
    native: false
  });
};
/**
 * @function getZones
 * @description Get the shipping zones associated with this store.
 */


var getZones = function getZones() {
  if (state.isActive) {
    return;
  }

  state.isActive = true;
  _cartState.default.isFetching = true;
  handleSpinnerState();
  (0, _ajax.wpAPIGetShippingZones)(_wpSettings.SHIPPING_API_ZONES).end(function (err, res) {
    _cartState.default.isFetching = false;
    handleSpinnerState();

    if (err) {
      state.hasError = true;
      state.isActive = false;
      handleShippingError();
      console.error(err);
      return;
    }

    var html = res.body.rendered;
    var fieldsWrapper = document.createElement('div'); // Remove any error messages on success.

    state.hasError = false;
    handleShippingError();
    tools.addClass(fieldsWrapper, 'bc-shipping-calculator-fields');
    fieldsWrapper.innerHTML = html;
    el.calculator.appendChild(fieldsWrapper);
    el.currentSubtotal.innerText = state.subtotal;
  });
};
/**
 * @function getMethods
 * @description get the shipping methods associated with the selected shipping zone.
 * @param e
 */


var getMethods = function getMethods(e) {
  _cartState.default.isFetching = true;
  handleSpinnerState();
  (0, _ajax.wpAPIGetShippingMethods)(_wpSettings.SHIPPING_API_METHODS, e.delegateTarget.value).end(function (err, res) {
    _cartState.default.isFetching = false;
    handleSpinnerState();

    if (err) {
      state.hasError = true;
      handleShippingError();
      console.error(err);
      return;
    }

    var html = res.body.rendered;
    var fieldsWrapper = tools.getNodes('.bc-shipping-calculator-fields', false, el.calculator, true)[0];
    var methods = tools.getNodes('bc-shipping-methods', false, el.calculator)[0];

    if (!fieldsWrapper) {
      return;
    } // Remove any error messages on success.


    state.hasError = false;
    handleShippingError();

    if (methods) {
      methods.parentNode.removeChild(methods);
    }

    fieldsWrapper.insertAdjacentHTML('beforeend', html);
    el.currentSubtotal.innerText = state.subtotal;
  });
};
/**
 * @function updateShippingCosts
 * @description find the cart subtotal node and update it with the new price.
 * @param shippingOption
 */


var updateShippingCosts = function updateShippingCosts(shippingOption) {
  var subtotal = shippingOption.dataset.cartSubtotal;
  var subtotalContainer = tools.getNodes('.bc-cart-subtotal__amount', false, document, true)[0];
  subtotalContainer.innerText = subtotal;
  var total = shippingOption.dataset.cartTotal;
  var totalContainer = tools.getNodes('.bc-cart-total__amount', false, document, true)[0];
  totalContainer.innerText = total;
};
/**
 * @function updateCartPrice
 * @description payload function. checking to see we have valid state and elements first, then update the subtotal.
 */


var updateCartPrice = function updateCartPrice() {
  var shippingOption = tools.getNodes('input[name="shipping-method"]:checked', false, el.calculator, true)[0];

  if (!shippingOption) {
    state.hasError = true;
    handleShippingError();
    return;
  } // Clear and reset the module state


  state.shippingItemCount = 0; // Remove old error message on new request.

  state.hasError = false;
  handleShippingError();
  updateShippingCosts(shippingOption);
};

var cacheElements = function cacheElements() {
  el.spinner = tools.getNodes('bc-loader', false, el.calculator)[0];
  el.currentSubtotal = tools.getNodes('[data-subtotal]', false, document, true)[0];

  if (el.currentSubtotal) {
    state.subtotal = el.currentSubtotal.dataset.subtotal;
  }
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)(el.calculator, '[data-js="shipping-calculator-toggle"]', 'click', getZones);
  (0, _delegate.default)(el.calculator, '[data-js="bc-shipping-zones"]', 'change', getMethods);
  (0, _delegate.default)(el.calculator, '[data-js="shipping-calculator-update"]', 'click', updateCartPrice);
  (0, _events2.on)(document, _events.AJAX_CART_UPDATE, resetShippingCalculator);
  (0, _events2.on)(document, _events.HANDLE_COUPON_CODE, resetShippingCalculator);
};

var init = function init() {
  if (!el.calculator) {
    return;
  }

  bindEvents();
  cacheElements();
  createSpinner();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 415 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

var _interopRequireWildcard = __webpack_require__(1);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var tools = _interopRequireWildcard(__webpack_require__(2));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _ajax = __webpack_require__(26);

var _events = __webpack_require__(5);

var _wpSettings = __webpack_require__(11);

var _events2 = __webpack_require__(34);

var _i18n = __webpack_require__(7);

var _cartState = _interopRequireDefault(__webpack_require__(47));

/**
 * @module Coupon Codes
 * @description Scripts to handle cart submission of coupon codes.
 */
var el = {
  container: tools.getNodes('bc-coupon-code')[0]
};
/**
 * @function updateCouponDiscount
 * @description Update discount amount on qty update or removal. Remove coupon section on empty cart.
 * @param e
 */

var updateCouponDiscount = function updateCouponDiscount(e) {
  if (!e.detail.cartData) {
    el.container.parentNode.removeChild(el.container);
    return;
  }

  el.couponDetails.innerText = "".concat(_i18n.NLS.cart.coupon_discount, ": -").concat(e.detail.cartData.coupons[0].discounted_amount.formatted);
};
/**
 * @function handleCouponSuccess
 * @description Update cart data when a coupon has been applied.
 * @param cartObject
 */


var handleCouponSuccess = function handleCouponSuccess() {
  var cartObject = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  if (!cartObject) {
    return;
  }

  var couponCode = cartObject.coupons[0].code;
  el.container.classList.add('bc-hide-add-form');
  el.container.classList.remove('bc-hide-remove-form');
  el.addCouponForm.setAttribute('aria-hidden', true);
  el.removeCouponForm.setAttribute('aria-hidden', false);
  el.cartErrorWrapper.classList.remove('message-active');
  el.cartError.innerText = _i18n.NLS.cart.coupon_success;
  el.removeCouponButton.dataset.couponCode = couponCode;
  el.removeCouponTitle.innerText = couponCode;
  el.couponField.value = '';
  el.couponDetails.innerText = "".concat(_i18n.NLS.cart.coupon_discount, ": -").concat(cartObject.coupons[0].discounted_amount.formatted);
};
/**
 * @function handleCouponRemoval
 * @description Update cart data when a coupon has been removed.
 */


var handleCouponRemoval = function handleCouponRemoval() {
  el.container.classList.add('bc-hide-remove-form');
  el.container.classList.remove('bc-hide-add-form');
  el.addCouponForm.setAttribute('aria-hidden', false);
  el.removeCouponForm.setAttribute('aria-hidden', true);
  el.cartErrorWrapper.classList.remove('message-active');
  el.cartError.innerText = _i18n.NLS.cart.coupon_success;
  el.removeCouponButton.dataset.couponCode = '';
  el.removeCouponTitle.innerText = '';
  el.couponDetails.innerText = '';
};
/**
 * @function handleCouponAddError
 * @description Handle coupon errors when adding.
 */


var handleCouponAddError = function handleCouponAddError() {
  el.couponField.focus();
  el.container.classList.add('bc-hide-remove-form');
  el.container.classList.remove('bc-hide-add-form');
  el.cartErrorWrapper.classList.add('message-active');
  el.cartError.innerText = _i18n.NLS.cart.coupon_error;
};
/**
 * @function handleCouponRemoveError
 * @description Handle coupon errors when removing.
 */


var handleCouponRemoveError = function handleCouponRemoveError() {
  el.cartErrorWrapper.classList.remove('message-active');
  el.cartError.innerText = _i18n.NLS.cart.coupon_removal_error;
  el.container.classList.add('bc-hide-remove-form');
  el.container.classList.remove('bc-hide-add-form');
};
/**
 * @function handleCouponCodeAdd
 * @description Main coupon function to apply a coupon to the cart.
 */


var handleCouponCodeAdd = function handleCouponCodeAdd() {
  if (!_wpSettings.COUPON_CODE_ADD) {
    return;
  }

  var queryObject = {
    coupon_code: el.couponField.value
  };
  _cartState.default.isFetching = true;
  (0, _events.trigger)({
    event: _events2.HANDLE_CART_STATE,
    native: false
  });
  (0, _ajax.wpAPICouponCodes)(_wpSettings.COUPON_CODE_ADD, queryObject, _wpSettings.AJAX_CART_NONCE).end(function (err, res) {
    _cartState.default.isFetching = false;
    (0, _events.trigger)({
      event: _events2.HANDLE_CART_STATE,
      native: false
    });

    if (err || res.body.error) {
      console.error(err, res.body ? res.body.error : '');
      handleCouponAddError();
      return;
    }

    (0, _events.trigger)({
      event: _events2.HANDLE_COUPON_CODE,
      data: {
        data: res.body
      },
      native: false
    });
    handleCouponSuccess(res.body);
  });
};
/**
 * @function handleCouponCodeRemove
 * @description Main coupon function to remove a coupon from the cart.
 * @param e
 */


var handleCouponCodeRemove = function handleCouponCodeRemove(e) {
  if (!_wpSettings.COUPON_CODE_REMOVE) {
    return;
  }

  var queryObject = {
    coupon_code: e.delegateTarget.dataset.couponCode
  };
  _cartState.default.isFetching = true;
  (0, _events.trigger)({
    event: _events2.HANDLE_CART_STATE,
    native: false
  });
  (0, _ajax.wpAPICouponCodes)(_wpSettings.COUPON_CODE_REMOVE, queryObject, _wpSettings.AJAX_CART_NONCE).end(function (err, res) {
    _cartState.default.isFetching = false;
    (0, _events.trigger)({
      event: _events2.HANDLE_CART_STATE,
      native: false
    });

    if (err || res.body.error) {
      console.error(err, res.body ? res.body.error : '');
      handleCouponRemoveError();
      return;
    }

    handleCouponRemoval();
    (0, _events.trigger)({
      event: _events2.HANDLE_COUPON_CODE,
      data: {
        data: res.body
      },
      native: false
    });
  });
};

var cacheElements = function cacheElements() {
  el.addCouponForm = tools.getNodes('bc-add-coupon-form', false, el.container)[0];
  el.couponField = tools.getNodes('bc-coupon-code-field', false, el.container)[0];
  el.removeCouponForm = tools.getNodes('bc-remove-coupon-form', false, el.container)[0];
  el.removeCouponButton = tools.getNodes('bc-coupon-code-remove', false, el.container)[0];
  el.removeCouponTitle = tools.getNodes('.bc-coupon-name', false, el.removeCouponButton, true)[0];
  el.couponDetails = tools.getNodes('bc-coupon-details', false, el.container)[0];
  el.cartErrorWrapper = tools.getNodes('.bc-cart-error', false, document, true)[0];
  el.cartError = tools.getNodes('bc-cart-error-message', false, el.cartErrorWrapper)[0];
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)(el.container, '[data-js="bc-coupon-code-submit"]', 'click', handleCouponCodeAdd);
  (0, _delegate.default)(el.container, '[data-js="bc-coupon-code-remove"]', 'click', handleCouponCodeRemove);
  (0, _events.on)(document, _events2.AJAX_CART_UPDATE, updateCouponDiscount);
};

var init = function init() {
  if (!el.container) {
    return;
  }

  cacheElements();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 416 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _embeddedCheckout = _interopRequireDefault(__webpack_require__(417));

/**
 * @module Checkout
 * @description Clearinghouse for all checkout scripts.
 */
var init = function init() {
  (0, _embeddedCheckout.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 417 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _regenerator = _interopRequireDefault(__webpack_require__(184));

__webpack_require__(110);

var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(185));

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _jsCookie = _interopRequireDefault(__webpack_require__(24));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _scrollTo = _interopRequireDefault(__webpack_require__(190));

var _events = __webpack_require__(5);

var _cookies = __webpack_require__(25);

var _bigcommerce_config = _interopRequireDefault(__webpack_require__(433));

var _cartTemplates = __webpack_require__(72);

/**
 * @module Cart Page
 * @description JS scripts for the cart page that do not require ajax.
 */
var el = {
  container: tools.getNodes('bc-embedded-checkout')[0]
};
/**
 * @function clearCartData
 * @description Clears out the localstorage and cart count in the nav menu item.
 */

var clearCartData = function clearCartData() {
  var cartCount = _jsCookie.default.get(_cookies.CART_ITEM_COUNT_COOKIE);

  if (!cartCount) {
    return;
  }

  var cartMenuCount = tools.getNodes('bc-cart-item-count', true);
  (0, _delay2.default)(function () {
    _jsCookie.default.remove(_cookies.CART_ITEM_COUNT_COOKIE);

    _jsCookie.default.remove(_cookies.CART_ID_COOKIE_NAME);

    cartMenuCount.forEach(function (menuItem) {
      tools.removeClass(menuItem, 'full');
      menuItem.textContent = '';
    });
  }, 250);
};
/**
 * @function scrollIframe
 * @description After completing the checkout process, ensure the top of the embedded checkout is visible using scrollTo.
 */


var scrollIframe = function scrollIframe() {
  var options = {
    offset: -80,
    duration: 750,
    $target: jQuery(el.container)
  };
  (0, _delay2.default)(function () {
    return (0, _scrollTo.default)(options);
  }, 1000);
};
/**
 * @function handleOrderCompleteEvents
 * @description Clear the cart data and scroll the order into view on the page if the order is successfully completed.
 */


var handleOrderCompleteEvents = function handleOrderCompleteEvents() {
  (0, _events.trigger)({
    event: 'bigcommerce/order_complete',
    data: {
      cart_id: _jsCookie.default.get(_cookies.CART_ID_COOKIE_NAME)
    },
    native: false
  });
  clearCartData();
  scrollIframe();
};
/**
 * @function handleLogoutEvents
 * @description Log the user out of wordpress if they have successfully logged out of BC via the Embedded Checkout SDK.
 */


var handleLogoutEvents = function handleLogoutEvents() {
  if (!_bigcommerce_config.default.logout_url) {
    return;
  }

  window.location = _bigcommerce_config.default.logout_url;
};
/**
 * @function loadEmbeddedCheckout
 * @description Create an instance of the BC embedded checkout.
 */


var loadEmbeddedCheckout =
/*#__PURE__*/
function () {
  var _ref = (0, _asyncToGenerator2.default)(
  /*#__PURE__*/
  _regenerator.default.mark(function _callee() {
    var checkoutCDN, config;
    return _regenerator.default.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            _context.next = 2;
            return checkoutKitLoader.load('embedded-checkout');

          case 2:
            checkoutCDN = _context.sent;
            // Load the config from the data attribute of the checkout container.
            config = JSON.parse(el.container.dataset.config); // Return an empty cart message if there's no checkout URL.

            if (!(!config.url || config.url < 0)) {
              _context.next = 7;
              break;
            }

            el.container.innerHTML = _cartTemplates.cartEmpty;
            return _context.abrupt("return");

          case 7:
            // Set the onComplete callback to use the clearCartData function.
            config.onComplete = handleOrderCompleteEvents; // Set the onComplete callback to use the clearCartData function.

            config.onSignOut = handleLogoutEvents; // Embed the checkout.

            checkoutCDN.embedCheckout(config);

          case 10:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, this);
  }));

  return function loadEmbeddedCheckout() {
    return _ref.apply(this, arguments);
  };
}();

var init = function init() {
  if (!el.container) {
    return;
  }

  loadEmbeddedCheckout();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 418 */
/***/ (function(module, exports, __webpack_require__) {

/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

// This method of obtaining a reference to the global object needs to be
// kept identical to the way it is obtained in runtime.js
var g = (function() { return this })() || Function("return this")();

// Use `getOwnPropertyNames` because not all browsers support calling
// `hasOwnProperty` on the global `self` object in a worker. See #183.
var hadRuntime = g.regeneratorRuntime &&
  Object.getOwnPropertyNames(g).indexOf("regeneratorRuntime") >= 0;

// Save the old regeneratorRuntime in case it needs to be restored later.
var oldRuntime = hadRuntime && g.regeneratorRuntime;

// Force reevalutation of runtime.js.
g.regeneratorRuntime = undefined;

module.exports = __webpack_require__(110);

if (hadRuntime) {
  // Restore the original runtime.
  g.regeneratorRuntime = oldRuntime;
} else {
  // Remove the global property added by runtime.js.
  try {
    delete g.regeneratorRuntime;
  } catch(e) {
    g.regeneratorRuntime = undefined;
  }
}


/***/ }),
/* 419 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(420);

/***/ }),
/* 420 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(155);
__webpack_require__(60);
__webpack_require__(63);
__webpack_require__(421);
__webpack_require__(431);
__webpack_require__(432);
module.exports = __webpack_require__(3).Promise;


/***/ }),
/* 421 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var LIBRARY = __webpack_require__(61);
var global = __webpack_require__(8);
var ctx = __webpack_require__(44);
var classof = __webpack_require__(103);
var $export = __webpack_require__(10);
var isObject = __webpack_require__(16);
var aFunction = __webpack_require__(56);
var anInstance = __webpack_require__(422);
var forOf = __webpack_require__(423);
var speciesConstructor = __webpack_require__(186);
var task = __webpack_require__(187).set;
var microtask = __webpack_require__(427)();
var newPromiseCapabilityModule = __webpack_require__(111);
var perform = __webpack_require__(188);
var promiseResolve = __webpack_require__(189);
var PROMISE = 'Promise';
var TypeError = global.TypeError;
var process = global.process;
var $Promise = global[PROMISE];
var isNode = classof(process) == 'process';
var empty = function () { /* empty */ };
var Internal, newGenericPromiseCapability, OwnPromiseCapability, Wrapper;
var newPromiseCapability = newGenericPromiseCapability = newPromiseCapabilityModule.f;

var USE_NATIVE = !!function () {
  try {
    // correct subclassing with @@species support
    var promise = $Promise.resolve(1);
    var FakePromise = (promise.constructor = {})[__webpack_require__(6)('species')] = function (exec) {
      exec(empty, empty);
    };
    // unhandled rejections tracking support, NodeJS Promise without it fails @@species test
    return (isNode || typeof PromiseRejectionEvent == 'function') && promise.then(empty) instanceof FakePromise;
  } catch (e) { /* empty */ }
}();

// helpers
var isThenable = function (it) {
  var then;
  return isObject(it) && typeof (then = it.then) == 'function' ? then : false;
};
var notify = function (promise, isReject) {
  if (promise._n) return;
  promise._n = true;
  var chain = promise._c;
  microtask(function () {
    var value = promise._v;
    var ok = promise._s == 1;
    var i = 0;
    var run = function (reaction) {
      var handler = ok ? reaction.ok : reaction.fail;
      var resolve = reaction.resolve;
      var reject = reaction.reject;
      var domain = reaction.domain;
      var result, then;
      try {
        if (handler) {
          if (!ok) {
            if (promise._h == 2) onHandleUnhandled(promise);
            promise._h = 1;
          }
          if (handler === true) result = value;
          else {
            if (domain) domain.enter();
            result = handler(value);
            if (domain) domain.exit();
          }
          if (result === reaction.promise) {
            reject(TypeError('Promise-chain cycle'));
          } else if (then = isThenable(result)) {
            then.call(result, resolve, reject);
          } else resolve(result);
        } else reject(value);
      } catch (e) {
        reject(e);
      }
    };
    while (chain.length > i) run(chain[i++]); // variable length - can't use forEach
    promise._c = [];
    promise._n = false;
    if (isReject && !promise._h) onUnhandled(promise);
  });
};
var onUnhandled = function (promise) {
  task.call(global, function () {
    var value = promise._v;
    var unhandled = isUnhandled(promise);
    var result, handler, console;
    if (unhandled) {
      result = perform(function () {
        if (isNode) {
          process.emit('unhandledRejection', value, promise);
        } else if (handler = global.onunhandledrejection) {
          handler({ promise: promise, reason: value });
        } else if ((console = global.console) && console.error) {
          console.error('Unhandled promise rejection', value);
        }
      });
      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
      promise._h = isNode || isUnhandled(promise) ? 2 : 1;
    } promise._a = undefined;
    if (unhandled && result.e) throw result.v;
  });
};
var isUnhandled = function (promise) {
  return promise._h !== 1 && (promise._a || promise._c).length === 0;
};
var onHandleUnhandled = function (promise) {
  task.call(global, function () {
    var handler;
    if (isNode) {
      process.emit('rejectionHandled', promise);
    } else if (handler = global.onrejectionhandled) {
      handler({ promise: promise, reason: promise._v });
    }
  });
};
var $reject = function (value) {
  var promise = this;
  if (promise._d) return;
  promise._d = true;
  promise = promise._w || promise; // unwrap
  promise._v = value;
  promise._s = 2;
  if (!promise._a) promise._a = promise._c.slice();
  notify(promise, true);
};
var $resolve = function (value) {
  var promise = this;
  var then;
  if (promise._d) return;
  promise._d = true;
  promise = promise._w || promise; // unwrap
  try {
    if (promise === value) throw TypeError("Promise can't be resolved itself");
    if (then = isThenable(value)) {
      microtask(function () {
        var wrapper = { _w: promise, _d: false }; // wrap
        try {
          then.call(value, ctx($resolve, wrapper, 1), ctx($reject, wrapper, 1));
        } catch (e) {
          $reject.call(wrapper, e);
        }
      });
    } else {
      promise._v = value;
      promise._s = 1;
      notify(promise, false);
    }
  } catch (e) {
    $reject.call({ _w: promise, _d: false }, e); // wrap
  }
};

// constructor polyfill
if (!USE_NATIVE) {
  // 25.4.3.1 Promise(executor)
  $Promise = function Promise(executor) {
    anInstance(this, $Promise, PROMISE, '_h');
    aFunction(executor);
    Internal.call(this);
    try {
      executor(ctx($resolve, this, 1), ctx($reject, this, 1));
    } catch (err) {
      $reject.call(this, err);
    }
  };
  // eslint-disable-next-line no-unused-vars
  Internal = function Promise(executor) {
    this._c = [];             // <- awaiting reactions
    this._a = undefined;      // <- checked in isUnhandled reactions
    this._s = 0;              // <- state
    this._d = false;          // <- done
    this._v = undefined;      // <- value
    this._h = 0;              // <- rejection state, 0 - default, 1 - handled, 2 - unhandled
    this._n = false;          // <- notify
  };
  Internal.prototype = __webpack_require__(428)($Promise.prototype, {
    // 25.4.5.3 Promise.prototype.then(onFulfilled, onRejected)
    then: function then(onFulfilled, onRejected) {
      var reaction = newPromiseCapability(speciesConstructor(this, $Promise));
      reaction.ok = typeof onFulfilled == 'function' ? onFulfilled : true;
      reaction.fail = typeof onRejected == 'function' && onRejected;
      reaction.domain = isNode ? process.domain : undefined;
      this._c.push(reaction);
      if (this._a) this._a.push(reaction);
      if (this._s) notify(this, false);
      return reaction.promise;
    },
    // 25.4.5.1 Promise.prototype.catch(onRejected)
    'catch': function (onRejected) {
      return this.then(undefined, onRejected);
    }
  });
  OwnPromiseCapability = function () {
    var promise = new Internal();
    this.promise = promise;
    this.resolve = ctx($resolve, promise, 1);
    this.reject = ctx($reject, promise, 1);
  };
  newPromiseCapabilityModule.f = newPromiseCapability = function (C) {
    return C === $Promise || C === Wrapper
      ? new OwnPromiseCapability(C)
      : newGenericPromiseCapability(C);
  };
}

$export($export.G + $export.W + $export.F * !USE_NATIVE, { Promise: $Promise });
__webpack_require__(62)($Promise, PROMISE);
__webpack_require__(429)(PROMISE);
Wrapper = __webpack_require__(3)[PROMISE];

// statics
$export($export.S + $export.F * !USE_NATIVE, PROMISE, {
  // 25.4.4.5 Promise.reject(r)
  reject: function reject(r) {
    var capability = newPromiseCapability(this);
    var $$reject = capability.reject;
    $$reject(r);
    return capability.promise;
  }
});
$export($export.S + $export.F * (LIBRARY || !USE_NATIVE), PROMISE, {
  // 25.4.4.6 Promise.resolve(x)
  resolve: function resolve(x) {
    return promiseResolve(LIBRARY && this === Wrapper ? $Promise : this, x);
  }
});
$export($export.S + $export.F * !(USE_NATIVE && __webpack_require__(430)(function (iter) {
  $Promise.all(iter)['catch'](empty);
})), PROMISE, {
  // 25.4.4.1 Promise.all(iterable)
  all: function all(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var resolve = capability.resolve;
    var reject = capability.reject;
    var result = perform(function () {
      var values = [];
      var index = 0;
      var remaining = 1;
      forOf(iterable, false, function (promise) {
        var $index = index++;
        var alreadyCalled = false;
        values.push(undefined);
        remaining++;
        C.resolve(promise).then(function (value) {
          if (alreadyCalled) return;
          alreadyCalled = true;
          values[$index] = value;
          --remaining || resolve(values);
        }, reject);
      });
      --remaining || resolve(values);
    });
    if (result.e) reject(result.v);
    return capability.promise;
  },
  // 25.4.4.4 Promise.race(iterable)
  race: function race(iterable) {
    var C = this;
    var capability = newPromiseCapability(C);
    var reject = capability.reject;
    var result = perform(function () {
      forOf(iterable, false, function (promise) {
        C.resolve(promise).then(capability.resolve, reject);
      });
    });
    if (result.e) reject(result.v);
    return capability.promise;
  }
});


/***/ }),
/* 422 */
/***/ (function(module, exports) {

module.exports = function (it, Constructor, name, forbiddenField) {
  if (!(it instanceof Constructor) || (forbiddenField !== undefined && forbiddenField in it)) {
    throw TypeError(name + ': incorrect invocation!');
  } return it;
};


/***/ }),
/* 423 */
/***/ (function(module, exports, __webpack_require__) {

var ctx = __webpack_require__(44);
var call = __webpack_require__(424);
var isArrayIter = __webpack_require__(425);
var anObject = __webpack_require__(14);
var toLength = __webpack_require__(152);
var getIterFn = __webpack_require__(164);
var BREAK = {};
var RETURN = {};
var exports = module.exports = function (iterable, entries, fn, that, ITERATOR) {
  var iterFn = ITERATOR ? function () { return iterable; } : getIterFn(iterable);
  var f = ctx(fn, that, entries ? 2 : 1);
  var index = 0;
  var length, step, iterator, result;
  if (typeof iterFn != 'function') throw TypeError(iterable + ' is not iterable!');
  // fast case for arrays with default iterator
  if (isArrayIter(iterFn)) for (length = toLength(iterable.length); length > index; index++) {
    result = entries ? f(anObject(step = iterable[index])[0], step[1]) : f(iterable[index]);
    if (result === BREAK || result === RETURN) return result;
  } else for (iterator = iterFn.call(iterable); !(step = iterator.next()).done;) {
    result = call(iterator, f, step.value, entries);
    if (result === BREAK || result === RETURN) return result;
  }
};
exports.BREAK = BREAK;
exports.RETURN = RETURN;


/***/ }),
/* 424 */
/***/ (function(module, exports, __webpack_require__) {

// call something on iterator step with safe closing on error
var anObject = __webpack_require__(14);
module.exports = function (iterator, fn, value, entries) {
  try {
    return entries ? fn(anObject(value)[0], value[1]) : fn(value);
  // 7.4.6 IteratorClose(iterator, completion)
  } catch (e) {
    var ret = iterator['return'];
    if (ret !== undefined) anObject(ret.call(iterator));
    throw e;
  }
};


/***/ }),
/* 425 */
/***/ (function(module, exports, __webpack_require__) {

// check on default Array iterator
var Iterators = __webpack_require__(31);
var ITERATOR = __webpack_require__(6)('iterator');
var ArrayProto = Array.prototype;

module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
};


/***/ }),
/* 426 */
/***/ (function(module, exports) {

// fast apply, http://jsperf.lnkit.com/fast-apply/5
module.exports = function (fn, args, that) {
  var un = that === undefined;
  switch (args.length) {
    case 0: return un ? fn()
                      : fn.call(that);
    case 1: return un ? fn(args[0])
                      : fn.call(that, args[0]);
    case 2: return un ? fn(args[0], args[1])
                      : fn.call(that, args[0], args[1]);
    case 3: return un ? fn(args[0], args[1], args[2])
                      : fn.call(that, args[0], args[1], args[2]);
    case 4: return un ? fn(args[0], args[1], args[2], args[3])
                      : fn.call(that, args[0], args[1], args[2], args[3]);
  } return fn.apply(that, args);
};


/***/ }),
/* 427 */
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(8);
var macrotask = __webpack_require__(187).set;
var Observer = global.MutationObserver || global.WebKitMutationObserver;
var process = global.process;
var Promise = global.Promise;
var isNode = __webpack_require__(42)(process) == 'process';

module.exports = function () {
  var head, last, notify;

  var flush = function () {
    var parent, fn;
    if (isNode && (parent = process.domain)) parent.exit();
    while (head) {
      fn = head.fn;
      head = head.next;
      try {
        fn();
      } catch (e) {
        if (head) notify();
        else last = undefined;
        throw e;
      }
    } last = undefined;
    if (parent) parent.enter();
  };

  // Node.js
  if (isNode) {
    notify = function () {
      process.nextTick(flush);
    };
  // browsers with MutationObserver, except iOS Safari - https://github.com/zloirock/core-js/issues/339
  } else if (Observer && !(global.navigator && global.navigator.standalone)) {
    var toggle = true;
    var node = document.createTextNode('');
    new Observer(flush).observe(node, { characterData: true }); // eslint-disable-line no-new
    notify = function () {
      node.data = toggle = !toggle;
    };
  // environments with maybe non-completely correct, but existent Promise
  } else if (Promise && Promise.resolve) {
    var promise = Promise.resolve();
    notify = function () {
      promise.then(flush);
    };
  // for other environments - macrotask based on:
  // - setImmediate
  // - MessageChannel
  // - window.postMessag
  // - onreadystatechange
  // - setTimeout
  } else {
    notify = function () {
      // strange IE + webpack dev server bug - use .call(global)
      macrotask.call(global, flush);
    };
  }

  return function (fn) {
    var task = { fn: fn, next: undefined };
    if (last) last.next = task;
    if (!head) {
      head = task;
      notify();
    } last = task;
  };
};


/***/ }),
/* 428 */
/***/ (function(module, exports, __webpack_require__) {

var hide = __webpack_require__(22);
module.exports = function (target, src, safe) {
  for (var key in src) {
    if (safe && target[key]) target[key] = src[key];
    else hide(target, key, src[key]);
  } return target;
};


/***/ }),
/* 429 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var global = __webpack_require__(8);
var core = __webpack_require__(3);
var dP = __webpack_require__(18);
var DESCRIPTORS = __webpack_require__(17);
var SPECIES = __webpack_require__(6)('species');

module.exports = function (KEY) {
  var C = typeof core[KEY] == 'function' ? core[KEY] : global[KEY];
  if (DESCRIPTORS && C && !C[SPECIES]) dP.f(C, SPECIES, {
    configurable: true,
    get: function () { return this; }
  });
};


/***/ }),
/* 430 */
/***/ (function(module, exports, __webpack_require__) {

var ITERATOR = __webpack_require__(6)('iterator');
var SAFE_CLOSING = false;

try {
  var riter = [7][ITERATOR]();
  riter['return'] = function () { SAFE_CLOSING = true; };
  // eslint-disable-next-line no-throw-literal
  Array.from(riter, function () { throw 2; });
} catch (e) { /* empty */ }

module.exports = function (exec, skipClosing) {
  if (!skipClosing && !SAFE_CLOSING) return false;
  var safe = false;
  try {
    var arr = [7];
    var iter = arr[ITERATOR]();
    iter.next = function () { return { done: safe = true }; };
    arr[ITERATOR] = function () { return iter; };
    exec(arr);
  } catch (e) { /* empty */ }
  return safe;
};


/***/ }),
/* 431 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
// https://github.com/tc39/proposal-promise-finally

var $export = __webpack_require__(10);
var core = __webpack_require__(3);
var global = __webpack_require__(8);
var speciesConstructor = __webpack_require__(186);
var promiseResolve = __webpack_require__(189);

$export($export.P + $export.R, 'Promise', { 'finally': function (onFinally) {
  var C = speciesConstructor(this, core.Promise || global.Promise);
  var isFunction = typeof onFinally == 'function';
  return this.then(
    isFunction ? function (x) {
      return promiseResolve(C, onFinally()).then(function () { return x; });
    } : onFinally,
    isFunction ? function (e) {
      return promiseResolve(C, onFinally()).then(function () { throw e; });
    } : onFinally
  );
} });


/***/ }),
/* 432 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// https://github.com/tc39/proposal-promise-try
var $export = __webpack_require__(10);
var newPromiseCapability = __webpack_require__(111);
var perform = __webpack_require__(188);

$export($export.S, 'Promise', { 'try': function (callbackfn) {
  var promiseCapability = newPromiseCapability.f(this);
  var result = perform(callbackfn);
  (result.e ? promiseCapability.reject : promiseCapability.resolve)(result.v);
  return promiseCapability.promise;
} });


/***/ }),
/* 433 */
/***/ (function(module, exports) {

module.exports = bigcommerce_config;

/***/ }),
/* 434 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

var _interopRequireWildcard = __webpack_require__(1);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var tools = _interopRequireWildcard(__webpack_require__(2));

var _filters = _interopRequireDefault(__webpack_require__(435));

/**
 * @module Loop
 *
 * @description Clearinghouse for Loop/Archive scripts.
 */
var el = {
  container: tools.getNodes('.post-type-archive-bigcommerce_product', false, document, true)[0]
};

var init = function init() {
  if (!el.container) {
    return;
  }

  (0, _filters.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 435 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

var _interopRequireWildcard = __webpack_require__(1);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var tools = _interopRequireWildcard(__webpack_require__(2));

var _queryToJson = _interopRequireDefault(__webpack_require__(71));

/**
 * @module Product Loop Filter Refinery
 *
 * @description Handle the filter refinery state for any FE JS needs.
 */
var el = {
  refinery: tools.getNodes('bc-product-archive-refinery', false, document, false)[0]
};
var state = {
  currentSort: {
    key: 'bc-sort',
    value: ''
  }
};
/**
 * @function getCurrentSort
 * @description Since all refinements happen on page load, here we'll set `state.currentSort` to the current `bc-sort` selection.
 */

var getCurrentSort = function getCurrentSort() {
  var query = (0, _queryToJson.default)();

  if (!query[state.currentSort.key]) {
    return;
  }

  state.currentSort.value = query[state.currentSort.key];
};
/**
 * @function updateFilterReset
 * @description If `state.currentSort` is set, update the filter reset button `href` attribute to keep the current `bc-sort` query parameter.
 */


var updateFilterReset = function updateFilterReset() {
  if (!state.currentSort.value || !el.resetButton) {
    return;
  }

  var href = el.resetButton.getAttribute('href');
  el.resetButton.href = "".concat(href, "?").concat(state.currentSort.key, "=").concat(state.currentSort.value);
};

var cacheElements = function cacheElements() {
  el.resetButton = tools.getNodes('bc-reset-filters', false, document, false)[0];
};

var init = function init() {
  if (!el.refinery) {
    return;
  }

  cacheElements();
  getCurrentSort();
  updateFilterReset();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 436 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _address = _interopRequireDefault(__webpack_require__(437));

var _dynamicStateField = _interopRequireDefault(__webpack_require__(191));

var _formQueryParam = _interopRequireDefault(__webpack_require__(441));

var _formErrors = _interopRequireDefault(__webpack_require__(442));

/**
 * @module Page scripts clearinghouse.
 */
var init = function init() {
  (0, _address.default)();
  (0, _dynamicStateField.default)();
  (0, _formQueryParam.default)();
  (0, _formErrors.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 437 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

__webpack_require__(138);

__webpack_require__(145);

var _uniqueId2 = _interopRequireDefault(__webpack_require__(23));

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _mtA11yDialog = _interopRequireDefault(__webpack_require__(101));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _dynamicStateField = _interopRequireDefault(__webpack_require__(191));

var _addressDelete = __webpack_require__(440);

var el = {
  container: tools.getNodes('bc-account-addresses')[0]
};
var instances = {
  dialogs: {}
};
/**
 * @function getOptions
 * @description Set standard A11yDialog options
 * @param dialogID
 * @returns {{appendTarget: string, trigger: string, bodyLock: boolean, effect: string, effectSpeed: number, effectEasing: string, overlayClasses: string, contentClasses: string, wrapperClasses: string, closeButtonClasses: string}}
 */

var getOptions = function getOptions(dialogID) {
  return {
    appendTarget: '.bc-account-addresses__list',
    trigger: "[data-trigger=\"".concat(dialogID, "\"]"),
    bodyLock: false,
    effect: 'fade',
    effectSpeed: 200,
    effectEasing: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
    overlayClasses: 'bc-account-address-form__overlay',
    contentClasses: 'bc-account-address-form__content',
    wrapperClasses: 'bc-account-address-form__wrapper',
    closeButtonClasses: 'bc-account-address-form__close-button bc-icon icon-bc-cross u-bc-visual-hide'
  };
};
/**
 * @function initHideDialog
 * @description Setup the currently active and rendered dialog box with a new .hide() trigger.
 * @param dialogEl
 * @param dialogID
 */


var initHideDialog = function initHideDialog(dialogEl, dialogID) {
  var cancelEdit = tools.getNodes('bc-account-address-form-cancel', false, dialogEl, false)[0];
  cancelEdit.setAttribute('data-dialogid', dialogID);
};
/**
 * @function hideDialog
 * @description Hide the current dialog when triggered.
 * @param e
 */


var hideDialog = function hideDialog(e) {
  var dialogID = e.delegateTarget.dataset.dialogid;
  instances.dialogs[dialogID].hide();
};
/**
 * @function handleDeleteConfirmation
 * @description Handle the delete confirmation process on an address.
 * @param card
 * @param form
 */


var handleDeleteConfirmation = function handleDeleteConfirmation(card, form) {
  var template = tools.getNodes('.bc-account-address__delete-confirmation', false, card, true)[0];
  tools.addClass(template, 'bc-confirmation-active');
  (0, _delegate.default)(card, '[data-js="bc-confirm-address-deletion"]', 'click', function () {
    form.submit();
  });
  (0, _delegate.default)(card, '[data-js="bc-confirm-address-cancel"]', 'click', function () {
    tools.removeClass(template, 'bc-confirmation-active');
  });
};
/**
 * @function displayDeleteConfirmation
 * @description Display the delete confirmation screen.
 * @param card
 */


var displayDeleteConfirmation = function displayDeleteConfirmation(card) {
  card.insertAdjacentHTML('beforeend', _addressDelete.deleteConfirmation);
};
/**
 * @function handleDeleteAddress
 * @description Present the user with confirm and cancel buttons when attempting to delete an address.
 * @param e
 */


var handleDeleteAddress = function handleDeleteAddress(e) {
  e.preventDefault();
  var form = tools.closest(e.delegateTarget, '.bc-account-address__delete-form');
  var card = tools.closest(e.delegateTarget, 'li.bc-account-addresses__item');
  displayDeleteConfirmation(card);
  handleDeleteConfirmation(card, form);
};
/**
 * @function handleDialogWithErrors
 * @description display dialog with form errors and only allow save action
 * @param dialogID
 */


var handleDialogWithErrors = function handleDialogWithErrors(dialogID) {
  //remove transitions so display is instant
  var dialog = instances.dialogs[dialogID];
  dialog.options.effectSpeed = 0;
  el.addressList.style.transitionDuration = '0'; //  Show the dialog

  dialog.show();
  tools.addClass(el.addressList, 'bc-account-address--form-active'); // Delay the activation of the cancel button so we can ensure the dialog is rendered.

  (0, _delay2.default)(function () {
    initHideDialog(dialog.node, dialogID);
  }, 50);
};
/**
 * @function initDialogs
 * @description setup all the dialog boxes and triggers for each available address on the page.
 */


var initDialogs = function initDialogs() {
  tools.getNodes('[data-js="bc-account-address-actions"]:not(.initialized)', true, el.container, true).forEach(function (dialog) {
    var dialogID = (0, _uniqueId2.default)('bc-account-address-form-dialog-');
    var trigger = tools.getChildren(dialog)[0];
    var target = tools.getChildren(dialog)[1];
    dialog.classList.add('initialized');
    trigger.setAttribute('data-content', dialogID);
    trigger.setAttribute('data-trigger', dialogID);
    target.setAttribute('data-js', dialogID);
    instances.dialogs[dialogID] = new _mtA11yDialog.default(getOptions(dialogID)); // open dialog if any forms contain error class

    if (target.textContent.includes('bc-form__control--error')) {
      handleDialogWithErrors(dialogID);
    }

    instances.dialogs[dialogID].on('render', function () {
      // On the first time this dialog is rendered, perform these actions.
      var alertSuccess = tools.getNodes('.bc-alert-group--success', false, el.container.parentElement, true)[0]; // On successful update, remove the success message when the next dialog is rendered.
      // Note: Since this is a page refresh, no dialogs have been rendered yet if success occurs.

      if (alertSuccess) {
        alertSuccess.parentNode.removeChild(alertSuccess);
      }
    });
    instances.dialogs[dialogID].on('show', function (dialogEl) {
      // Every time a dialog is shown, perform these actions.
      tools.addClass(el.addressList, 'bc-account-address--form-active');
      (0, _delay2.default)(function () {
        initHideDialog(dialogEl, dialogID);
        (0, _dynamicStateField.default)();
      }, 50);
    });
    instances.dialogs[dialogID].on('hide', function () {
      // Every time a dialog is closed, perform these actions.
      tools.removeClass(el.addressList, 'bc-account-address--form-active');
    });
  });
};
/**
 * @function cacheElements
 * @description elements to store if el.container exists.
 */


var cacheElements = function cacheElements() {
  el.addressList = tools.getNodes('.bc-account-addresses__list', false, el.container, true)[0];
};
/**
 * @function bindEvents
 * @description bind all event handlers and listeners for addresses.
 */


var bindEvents = function bindEvents() {
  (0, _delegate.default)(el.container, '[data-js="bc-account-address-form-cancel"]', 'click', hideDialog);
  (0, _delegate.default)(el.container, '[data-js="bc-account-address-delete"]', 'click', handleDeleteAddress);
};

var init = function init() {
  if (!el.container) {
    return;
  }

  cacheElements();
  initDialogs();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 438 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.stateSelectField = void 0;

/**
 * @template Select Field of States/Provinces.
 * @param states
 * @param initValue
 * @returns {string}
 */
var stateSelectField = function stateSelectField(states, fieldId, fieldName, initValue) {
  return "\n\t<select id=\"".concat(fieldId, "\" class=\"bc-account-address-state\" name=\"").concat(fieldName, "\" data-js=\"bc-dynamic-state-control\">\n\t\t").concat(states.map(function (state) {
    return "\n\t\t\t<option value=\"".concat(state.state, "\" data-state-abbr=\"\" ").concat(initValue === state.state ? 'selected' : '', ">").concat(state.state, "</option>\n\t\t");
  }).join(''), "\n\t</select>\n\t");
};

exports.stateSelectField = stateSelectField;

/***/ }),
/* 439 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.stateInputField = void 0;

/**
 * @template Input Field of States/Provinces.
 * @param fieldId
 * @param fieldName
 * @param initValue
 * @returns {string}
 */
var stateInputField = function stateInputField(fieldId, fieldName, initValue) {
  return "<input type=\"text\" id=\"".concat(fieldId, "\" name=\"").concat(fieldName, "\" data-js=\"bc-dynamic-state-control\" value=\"").concat(!initValue ? '' : initValue, "\" />");
};

exports.stateInputField = stateInputField;

/***/ }),
/* 440 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.deleteConfirmation = void 0;

var _i18n = __webpack_require__(7);

var deleteConfirmation = "\n\t<div class=\"bc-account-address__delete-confirmation\">\n\t\t<p>".concat(_i18n.NLS.account.confirm_delete_message, "</p>\n\t\t<button class=\"bc-btn bc-account-address__delete-confirm\" data-js=\"bc-confirm-address-deletion\">").concat(_i18n.NLS.account.confirm_delete_address, "</button>\n\t\t<button class=\"bc-btn bc-btn--inverse bc-account-address__delete-cancel\" data-js=\"bc-confirm-address-cancel\">").concat(_i18n.NLS.account.cancel_delete_address, "</button>\n\t</div>\n\t");
exports.deleteConfirmation = deleteConfirmation;

/***/ }),
/* 441 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _keys = _interopRequireDefault(__webpack_require__(64));

var _isEmpty2 = _interopRequireDefault(__webpack_require__(19));

var _queryToJson = _interopRequireDefault(__webpack_require__(71));

var el = {
  query: (0, _queryToJson.default)(),
  queryVars: ['bc-error', 'bc-message']
};
/**
 * @function removeQueryVars
 * @description search query for keys matching queryVars array. If found, set URL to current path without page refresh.
 */

var removeQueryVars = function removeQueryVars() {
  (0, _keys.default)(el.query).forEach(function (key, i) {
    if (key === el.queryVars[i]) {
      //remove the parameters without a change in the page
      window.history.replaceState(null, null, window.location.pathname);
    }
  });
};

var init = function init() {
  if ((0, _isEmpty2.default)(el.query)) {
    return;
  }

  removeQueryVars();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 442 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _formErrorMessage = __webpack_require__(443);

var el = {
  container: tools.getNodes('.bc-alert-group--error', false, document, true)[0]
};

var cacheElements = function cacheElements() {
  el.formWrapper = tools.getNodes('.bc-form--has-errors', false, document, true)[0];
};
/**
 * @function initFormErrorMessageHandler
 * @description If we have an error message alert, loop through the messages and find ones with related form fields. If
 *     we have a match, move that message inline to the form and hide the error in the alert node.
 */


var initFormErrorMessageHandler = function initFormErrorMessageHandler() {
  tools.getNodes('.bc-alert--error', true, el.container, true).forEach(function (error) {
    var key = error.dataset.messageKey;
    var input = tools.getNodes("[data-form-field=\"bc-form-field-".concat(key, "\"]"), false, el.formWrapper, true)[0];

    if (!input) {
      return;
    }

    error.style.display = 'none';
    input.parentNode.insertAdjacentHTML('beforeend', (0, _formErrorMessage.formErrorMessage)(error.innerHTML));
  });
  (0, _delay2.default)(function () {
    return tools.addClass(el.container, 'bc-fade-in-alert-group');
  }, 50);
};

var init = function init() {
  if (!el.container) {
    return;
  }

  cacheElements();
  initFormErrorMessageHandler();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 443 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.formErrorMessage = void 0;

/**
 * @template Inline error message template for form fields.
 * @param message
 * @returns {string}
 */
var formErrorMessage = function formErrorMessage(message) {
  return "\n\t\t<span class=\"bc-form__error-message\">".concat(message, "</span>\n\t");
};

exports.formErrorMessage = formErrorMessage;

/***/ }),
/* 444 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

var _interopRequireWildcard = __webpack_require__(1);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var tools = _interopRequireWildcard(__webpack_require__(2));

var _variants = _interopRequireDefault(__webpack_require__(163));

var _reviews = _interopRequireDefault(__webpack_require__(445));

var _pricing = _interopRequireDefault(__webpack_require__(446));

/**
 * @module Product Variants.
 */
var el = {
  container: tools.getNodes('bc-product-single', false, document)[0],
  formWrapper: tools.getNodes('.bc-product-form', false, document, true)[0]
};

var init = function init() {
  (0, _variants.default)(el.formWrapper);
  (0, _reviews.default)(el.container);
  (0, _pricing.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 445 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _isEmpty2 = _interopRequireDefault(__webpack_require__(19));

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _scrollTo = _interopRequireDefault(__webpack_require__(190));

var _shortcodeState = _interopRequireDefault(__webpack_require__(182));

var _superagent = _interopRequireDefault(__webpack_require__(107));

var _i18n = __webpack_require__(7);

var _errors = __webpack_require__(183);

/**
 * @module Product Reviews
 * @description Product reviews scripts.
 */
var el = {
  container: tools.getNodes('#bc-single-product__reviews', false, document, true)[0],
  firstPageURL: tools.getNodes('bc-reviews-ajax-url')[0]
};
var state = {
  formActive: false
};
var options = {
  delay: 150,
  afterLoadDelay: 250
};
var scrollToOptions = {
  duration: 250,
  easing: 'linear',
  offset: 0,
  $target: ''
};
/**
 * @function scrollToReviews
 * @description animate the page scroll position to the reviews section.
 */

var scrollToReviews = function scrollToReviews(e) {
  if (e) {
    e.preventDefault();
  }

  scrollToOptions.$target = jQuery('.bc-single-product__reviews');
  scrollToOptions.offset = -30;
  (0, _scrollTo.default)(scrollToOptions);
};
/**
 * @function scrollToReviewForm
 * @description animate the page scroll position to the review form.
 */


var scrollToReviewForm = function scrollToReviewForm() {
  scrollToOptions.$target = jQuery('.bc-product-review-form');
  scrollToOptions.offset = 40;
  (0, _scrollTo.default)(scrollToOptions);
};
/**
 * @function enableProductReviewForm
 * @description show the review form.
 * @param e
 */


var enableProductReviewForm = function enableProductReviewForm(e) {
  var target = e.delegateTarget;
  var formWrapper = tools.closest(target, '.bc-product-review-form-wrapper');
  state.formActive = true;
  tools.addClass(formWrapper, 'bc-product-review-form--active');
  scrollToReviewForm();
};
/**
 * @function disableProductReviewForm
 * @description hide the product review form.
 * @param e
 */


var disableProductReviewForm = function disableProductReviewForm(e) {
  var target = e.delegateTarget;
  var formWrapper = tools.closest(target, '.bc-product-review-form-wrapper');
  state.formActive = false;
  tools.removeClass(formWrapper, 'bc-product-review-form--active');
  scrollToReviews();
};
/**
 * @function handleFormAlert
 * @description on page load, if we have an alert from the form submission, determine its type and scroll to the message.
 */


var handleFormAlert = function handleFormAlert() {
  var alert = tools.getNodes('.bc-alert-group', false, el.container, true)[0];

  if (!alert) {
    return;
  }

  if (tools.hasClass(alert, 'bc-alert-group--error')) {
    tools.addClass(el.formWrapper, 'bc-product-review-form--active');
    scrollToReviewForm();
    return;
  }

  scrollToReviews();
};
/**
 * @function initializeItems
 * @description add a class to signify that item has been rendered in the shortcode container.
 * @param itemContainer
 */


var initializeItems = function initializeItems() {
  var itemContainer = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

  if (!itemContainer) {
    return;
  }

  tools.getChildren(itemContainer).forEach(function (item) {
    if (!tools.hasClass(item, 'item-initialized') && !tools.hasClass(item, 'bc-load-items__trigger')) {
      tools.addClass(item, 'item-initialized');
    }
  });
};
/**
 * @function handleRequestError
 * @description if there is a pagination request error, display the message inline.
 * @param err
 * @param target
 */


var handleRequestError = function handleRequestError() {
  var err = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var target = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (!target && !err) {
    return;
  }

  var message = err.timeout ? _i18n.NLS.errors.pagination_timeout_error : _i18n.NLS.errors.pagination_error;
  var loadMoreWrapper = tools.closest(target, '.bc-product-review-list');
  target.removeAttribute('disabled');
  loadMoreWrapper.insertAdjacentHTML('beforeend', (0, _errors.paginationError)(message));
  initializeItems(loadMoreWrapper);
};
/**
 * @function loadNextPageItems
 * @description Get and inject the rendered HTML from the WP API response to load the next page of items.
 * @param items
 * @param itemContainer
 */


var loadNextPageItems = function loadNextPageItems() {
  var items = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var itemContainer = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  (0, _delay2.default)(function () {
    itemContainer.insertAdjacentHTML('beforeend', items.rendered);
    initializeItems(itemContainer);
  }, options.delay);
};
/**
 * @function handleItemsLoading
 * @description Handler for gracefully loading the next set of paged items into the current shortcode container.
 * @param target
 * @param items
 */


var handleItemsLoading = function handleItemsLoading() {
  var target = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var items = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};

  if (!target && !items || _shortcodeState.default.isFetching) {
    return;
  }

  var itemContainer = tools.closest(target, '.bc-load-items-container');
  loadNextPageItems(items, itemContainer);
};
/**
 * @function handleSpinnerState
 * @description Show or hid the display of the spinner when fetching data.
 * @param target
 */


var handleSpinnerState = function handleSpinnerState() {
  var target = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var gridWrapper = tools.closest(target, '.bc-load-items');
  var loader = tools.getNodes('.bc-load-items__loader', false, gridWrapper, true)[0];

  if (_shortcodeState.default.isFetching) {
    tools.addClass(loader, 'active');
    return;
  }

  tools.removeClass(loader, 'active');
};
/**
 * @function getNextPageItems
 * @description Ajax query to get the next set of items in a paged shortcode container.
 */


var getFirstPageItems = function getFirstPageItems() {
  var firstRequestURL = el.firstPageURL.value;

  if ((0, _isEmpty2.default)(firstRequestURL)) {
    return;
  }

  _shortcodeState.default.isFetching = true;
  handleSpinnerState(el.firstPageURL);

  _superagent.default.get(firstRequestURL).timeout({
    response: 5000,
    // 5 seconds to hear back from the server.
    deadline: 30000 // 30 seconds to finish the request process.

  }).end(function (err, res) {
    _shortcodeState.default.isFetching = false;
    handleSpinnerState(el.firstPageURL);

    if (err) {
      handleRequestError(err, el.firstPageURL);
      return;
    }

    handleItemsLoading(el.firstPageURL, res.body);
  });
};

var cacheElements = function cacheElements() {
  el.productSingle = tools.getNodes('.bc-product-single', false, document, true)[0];
  el.formWrapper = tools.getNodes('bc-product-review-form-wrapper')[0];
};

var bindEvents = function bindEvents() {
  handleFormAlert();

  if (el.productSingle) {
    (0, _delegate.default)(el.productSingle, '[data-js="bc-single-product-reviews-anchor"]', 'click', scrollToReviews);
  }

  if (el.formWrapper) {
    (0, _delegate.default)(el.formWrapper, '[data-js="bc-product-review-write"]', 'click', enableProductReviewForm);
    (0, _delegate.default)(el.formWrapper, '[data-js="bc-product-review-cancel-write"]', 'click', disableProductReviewForm);
  }

  if (el.firstPageURL) {
    getFirstPageItems();
  }
};

var init = function init() {
  if (!el.container) {
    return;
  }

  cacheElements();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 446 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _stringify = _interopRequireDefault(__webpack_require__(179));

var _values = _interopRequireDefault(__webpack_require__(33));

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _events = __webpack_require__(5);

var _ajax = __webpack_require__(26);

var _wpSettings = __webpack_require__(11);

var el = {
  container: tools.getNodes('bc-api-product-pricing', true),
  priceWrapper: tools.getNodes('bc-product-pricing', true)
};
var state = {
  isFetching: false,
  optionTrigger: '',
  isQuickView: false,
  delay: 250,
  req: null,
  products: {
    items: []
  }
};
/**
 * @function maybePriceIsLoading
 * @description based on state.isFetching, show of hide the spinner element.
 * @param pricingContainer
 */

var maybePriceIsLoading = function maybePriceIsLoading() {
  var pricingContainer = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

  if (state.isFetching) {
    tools.addClass(pricingContainer, 'bc-price-is-loading');
    return;
  }

  tools.removeClass(pricingContainer, 'bc-price-is-loading');
};
/**
 * @function getSelectedOptions
 * @description On load or on selection change, build an options array to submit to the pricing API.
 * @param optionsContainer
 */


var getSelectedOptions = function getSelectedOptions(optionsContainer) {
  var selection = '';
  var options = [];
  tools.getNodes('product-form-option', true, optionsContainer).forEach(function (field) {
    var fieldType = field.dataset.field;

    switch (fieldType) {
      case 'product-form-option-radio':
      case 'product-form-option-checkbox':
        selection = tools.getNodes('input:checked', false, field, true)[0];
        break;

      case 'product-form-option-select':
        selection = tools.getNodes('select', false, field, true)[0];
        break;

      default:
        selection = '';
    }

    if (!selection) {
      return;
    }

    var option = {
      option_id: parseInt(selection.dataset.optionId, 10),
      value_id: parseInt(selection.value, 10)
    };
    options.push(option);
  });
  return options;
};
/**
 * @function getInitialPricing
 * @description There are no options for this item and it is OK to just submit the product ID.
 * @param productID
 */


var getInitialPricing = function getInitialPricing(productID) {
  var item = {
    product_id: productID
  };
  state.products.items.push(item);
};
/**
 * @function getOptionsPricing
 * @description If the product has visible options on the page, get those and add them to this item's options array.
 * @param e
 * @param productID
 * @param priceContainer
 * @param productOptions
 */


var getOptionsPricing = function getOptionsPricing(e, productID, priceContainer, productOptions) {
  var item = {
    product_id: productID,
    options: getSelectedOptions(productOptions)
  };
  state.products.items.push(item);
};
/**
 * @function buildPricingObject
 * @description Determine what type of data we're submitting to the API and build the state.products.items array.
 * @param priceContainer
 */


var buildPricingObject = function buildPricingObject(priceContainer) {
  var bcid = parseInt(priceContainer.dataset.pricingApiProductId, 10);
  var dataWrapper = tools.closest(priceContainer, '[data-js="bc-product-data-wrapper"]'); // CASE: This is not a card and we should get all possible selected options to submit.

  if (dataWrapper) {
    var optionsContainer = tools.getNodes('product-options', false, dataWrapper)[0];
    getOptionsPricing(null, bcid, priceContainer, optionsContainer);
  } else {
    // CASE: This is a card or single product with no options and it is safe to just get the basic pricing.
    getInitialPricing(bcid, priceContainer);
  }
};
/**
 * @function showCachedPricing
 * @description Show the cached pricing nodes if the API is down, errors out, or the API pricing nodes are overwritten
 *     or missing.
 * @param products
 */


var showCachedPricing = function showCachedPricing() {
  var products = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];

  // The API is down or responded with an error but and we still have API Pricing Nodes on the page.
  if (products.length && products.items) {
    products.items.forEach(function (product) {
      // Only update the requested products to show the cached pricing data.
      var priceWrapper = tools.getNodes("[data-product-price-id=\"".concat(product.product_id, "\"]"), false, document, true)[0];
      var cachedPricingNode = tools.getNodes('bc-cached-product-pricing', true, priceWrapper)[0];
      tools.addClass(cachedPricingNode, 'bc-product__pricing--visible');
      maybePriceIsLoading(priceWrapper);
    });
    return;
  } // CASE: There are no Pricing API nodes to update, just show all the cached pricing ASAP.


  var cachedPricingNodes = tools.getNodes('bc-cached-product-pricing', true, document);

  if (!cachedPricingNodes || !cachedPricingNodes.length) {
    return;
  }

  cachedPricingNodes.forEach(function (element) {
    return tools.addClass(element, 'bc-product__pricing--visible');
  });
};
/**
 * @function filterAPIPricingData
 * @description Depending on the display_type of the item data, update the applicable price nodes with the new pricing
 *     data.
 * @param type
 * @param APIPricingNode
 * @param data
 */


var filterAPIPricingData = function filterAPIPricingData() {
  var type = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var APIPricingNode = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var data = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var pricingNodes = [];
  var pricingContainer = tools.closest(APIPricingNode, '[data-js="bc-product-pricing"]');

  if (!pricingContainer) {
    return;
  } // This will hide the spinner because state.isFetching is false.


  maybePriceIsLoading(pricingContainer); // If the cached pricing is visible, hide it now that we have data.

  var cachedPricingNode = tools.getNodes('bc-cached-product-pricing', false, pricingContainer)[0];

  if (cachedPricingNode) {
    tools.removeClass(cachedPricingNode, 'bc-product__pricing--visible');
  } // Create an array of potential nodes to update relative to the container they belong to.


  tools.addClass(APIPricingNode, 'bc-product__pricing--visible');
  pricingNodes['price-node'] = APIPricingNode.querySelector('.bc-product__price--base');
  pricingNodes['sale-node'] = APIPricingNode.querySelector('.bc-product__price--sale');
  pricingNodes['original-price-node'] = APIPricingNode.querySelector('.bc-product__original-price');
  pricingNodes['retail-price-node'] = APIPricingNode.querySelector('.bc-product__retail-price');
  pricingNodes['retail-value-node'] = APIPricingNode.querySelector('.bc-product__retail-price-value');
  pricingNodes.forEach(function (node) {
    return tools.removeClass(node, 'bc-show-current-price');
  });

  if (data.retail_price.formatted.length === 0) {
    tools.addClass(pricingNodes['retail-price-node'], 'bc-no-retail-price');
  } else {
    pricingNodes['retail-value-node'].textContent = data.retail_price.formatted;
  } // CASE: The display_type is 'sale'.


  if (type === 'sale') {
    pricingNodes['original-price-node'].textContent = data.original_price.formatted;
    pricingNodes['sale-node'].textContent = data.calculated_price.formatted;
    pricingNodes['price-node'].textContent = '';
    tools.addClass(pricingNodes['original-price-node'], 'bc-show-current-price');
    tools.addClass(pricingNodes['sale-node'], 'bc-show-current-price');
    return;
  } // CASE: The display_type is either 'price_range' or 'simple' and we can update the same node with  either data.


  var basePrice = type === 'price_range' ? "".concat(data.price_range.min.formatted, " - ").concat(data.price_range.max.formatted) : data.calculated_price.formatted;
  pricingNodes['original-price-node'].textContent = '';
  pricingNodes['sale-node'].textContent = '';
  pricingNodes['price-node'].textContent = basePrice;
  tools.addClass(pricingNodes['price-node'], 'bc-show-current-price');
};
/**
 * @function handleAPIItemData
 * @description For each item in the array, determine if the response belongs to a single product or a set of products.
 * @param type
 * @param productID
 * @param data
 */


var handleAPIItemData = function handleAPIItemData() {
  var type = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var productID = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var data = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var parentNode = document; // CASE: This call was triggered by an option field change and belongs to a single product. Only update this instance.

  if (state.optionTrigger || state.isQuickView) {
    if (state.optionTrigger) {
      parentNode = tools.closest(state.optionTrigger, '[data-js="bc-product-data-wrapper"]');
    } else if (state.isQuickView) {
      var wrapper = tools.getNodes('.bc-product-quick-view__wrapper[aria-hidden=false]', false, document, true)[0];
      parentNode = tools.getNodes('bc-product-data-wrapper', false, wrapper)[0];
    }

    var priceNode = tools.getNodes("[data-pricing-api-product-id=\"".concat(productID, "\"]"), false, parentNode, true)[0];
    filterAPIPricingData(type, priceNode, data); // When the fields are re-enabled, it is safe to reset this value to blank to prepare it for the next call.
    // Prevents memory leak.

    state.optionTrigger = ''; //reset

    state.isQuickView = false;
    return;
  } // CASE: This data belongs to a group of items on the page and in case there are duplicates of this product, update all of them.


  tools.getNodes("[data-pricing-api-product-id=\"".concat(productID, "\"]"), true, parentNode, true).forEach(function (APIPricingNode) {
    filterAPIPricingData(type, APIPricingNode, data);
  });
};
/**
 * @function handleAPIPricingData
 * @description Using the response from the API payload, parse through the items array and handle the display_type.
 * @param data
 */


var handleAPIPricingData = function handleAPIPricingData() {
  var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var APIPricingNodes = tools.getNodes('bc-api-product-pricing', true, document);

  if (!APIPricingNodes || !APIPricingNodes.length) {
    return;
  }

  (0, _values.default)(data.items).forEach(function (item) {
    var productID = parseInt(item.product_id, 10);
    handleAPIItemData(item.display_type, productID, item);
  });
};
/**
 * @function submitAPIRequest
 * @description Submit the items saved in the state object to the API endpoint for current pricing data.
 */


var submitAPIRequest = function submitAPIRequest() {
  var instance = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  if (state.products.items.length < 1) {
    return;
  }

  if (instance.req) {
    instance.req.abort();
    instance.req = null;
  }

  state.isFetching = true;
  instance.req = (0, _ajax.wpAPIProductPricing)(_wpSettings.PRICING_API_URL, _wpSettings.PRICING_API_NONCE, (0, _stringify.default)(state.products)).end(function (err, res) {
    state.isFetching = false;
    state.req = null;

    if (err) {
      console.error(err);
      showCachedPricing(state.products);
      return;
    }

    (0, _delay2.default)(function () {
      return handleAPIPricingData(res.body);
    }, state.delay);
  });
};
/**
 * @function handleOptionChanges
 * @description When an option change is triggered, create a new API request.
 * @param e
 */


var handleOptionChanges = function handleOptionChanges(e) {
  // We have to use e.target here due to lodash debounce losing the delegateTarget property.
  var wrapper = tools.closest(e.target, '[data-js="bc-product-data-wrapper"]');
  var priceWrapper = tools.getNodes('bc-product-pricing', false, wrapper)[0];
  var priceContainer = tools.getNodes('bc-api-product-pricing', false, wrapper)[0]; // We're resetting the array here because option changes should only trigger a single item call to the API.

  state.products.items = [];
  state.isFetching = true;
  state.optionTrigger = e.target;
  buildPricingObject(priceContainer);
  maybePriceIsLoading(priceWrapper);
  submitAPIRequest(state);
};
/**
 * @function initOptionClicks
 * @description Click/change event listener for form fields on each product. Runs the handleOptionChanges function on
 *     the event.
 * @param pricingContainer - the current .initialized pricing node.
 */


var initOptionClicks = function initOptionClicks() {
  var pricingContainer = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var wrapper = tools.closest(pricingContainer, '[data-js="bc-product-data-wrapper"]');

  if (!wrapper) {
    return;
  }

  var options = tools.getNodes('.bc-product-form__options.initialized', false, wrapper, true)[0];
  var radios = tools.getNodes('[data-js="product-form-option"] input[type=radio]', true, options, true);
  var selects = tools.getNodes('[data-js="product-form-option"] select', true, options, true);
  var checkboxes = tools.getNodes('[data-js="product-form-option"] input[type=checkbox]', true, options, true);

  if (radios.length > 0) {
    (0, _delegate.default)(options, '[data-js="product-form-option"] input[type=radio]', 'click', handleOptionChanges);
  }

  if (selects.length > 0) {
    (0, _delegate.default)(options, '[data-js="product-form-option"] select', 'change', handleOptionChanges);
  }

  if (checkboxes.length > 0) {
    (0, _delegate.default)(options, '[data-js="product-form-option"] input[type=checkbox]', 'click', handleOptionChanges);
  }
};
/**
 * @function isPreinitialized
 * @description determines if a pricing node is eligible for preinitialization
 * @param pricingContainer
 */


var isPreinitialized = function isPreinitialized(pricingContainer) {
  if (!tools.hasClass(pricingContainer, 'preinitialized')) {
    return false; // not preinitialized
  }

  var dataWrapper = tools.closest(pricingContainer, '[data-js="bc-product-data-wrapper"]');

  if (!dataWrapper) {
    return true; // no product options that might affect preinitialized pricing
  }

  var optionsContainer = tools.getNodes('product-options', false, dataWrapper)[0];
  var options = getSelectedOptions(optionsContainer);
  return options.length < 1; // no options selected = OK to use preinitialized value
};
/**
 * @function initPricing
 * @description prepare all dynamic pricing elements for receiving Pricing API data.
 * @param e
 */


var initPricing = function initPricing(e) {
  state.products.items = []; // Reset the items array to be submitted to the API endpoint.
  // Get all nodes that are not initialized and prepare them for the type of data they need to receive.

  state.isFetching = true;
  tools.getNodes('[data-js="bc-product-pricing"]:not(.initialized)', true, document, true).forEach(function (pricingContainer) {
    var pricingAPINode = tools.getNodes('bc-api-product-pricing', false, pricingContainer)[0];
    tools.addClass(pricingContainer, 'initialized'); // If this node is not preinitialized, it is safe to push to the state.items array and apply the price loading class.

    if (!isPreinitialized(pricingContainer)) {
      buildPricingObject(pricingAPINode);
      maybePriceIsLoading(pricingContainer);
    }

    initOptionClicks(pricingContainer);
  });
  state.isQuickView = e ? e.detail.quickView : false; // After looping through all the available nodes, run an API request.

  submitAPIRequest();
};

var bindEvents = function bindEvents() {
  (0, _events.on)(document, 'bigcommerce/get_pricing', initPricing);
};

var init = function init() {
  if (!el.priceWrapper || !el.priceWrapper.length) {
    // There are no pricing wrappers on the page at all. i.e Cart page, checkout page, etc.
    return;
  }

  if (!el.container || !el.container.length) {
    // If there are no API Pricing nodes present, show cached pricing.
    showCachedPricing();
    return;
  } // Setup and submit a Pricing API request on page load.


  initPricing();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 447 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _manageDialogs = _interopRequireDefault(__webpack_require__(448));

var _share = _interopRequireDefault(__webpack_require__(449));

var _product = _interopRequireDefault(__webpack_require__(451));

var _list = _interopRequireDefault(__webpack_require__(452));

/**
 * @module Wish Lists
 *
 * @description Clearinghouse for Wish Lists Scripts
 *
 */
var init = function init() {
  (0, _manageDialogs.default)();
  (0, _share.default)();
  (0, _product.default)();
  (0, _list.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 448 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _mtA11yDialog = _interopRequireDefault(__webpack_require__(101));

var _delegate = _interopRequireDefault(__webpack_require__(4));

/**
 *
 */
var el = {
  container: tools.getNodes('bc-manage-wish-list')[0]
};
var instances = {
  dialogs: {}
};

var getOptions = function getOptions(dialogID) {
  return {
    appendTarget: 'body',
    trigger: "[data-trigger=\"".concat(dialogID, "\"]"),
    bodyLock: true,
    effect: 'fade',
    effectSpeed: 200,
    effectEasing: 'cubic-bezier(0.445, 0.050, 0.550, 0.950)',
    overlayClasses: 'bc-wish-list-dialog__overlay',
    contentClasses: 'bc-wish-list-dialog-content-wrapper',
    wrapperClasses: 'bc-wish-list-dialog__wrapper',
    closeButtonClasses: 'bc-product-quick-view__close-button bc-icon icon-bc-cross'
  };
};
/**
 * @function initHideDialog
 * @description Setup the currently active and rendered dialog box with a new .hide() trigger.
 * @param dialogEl
 * @param dialogID
 */


var initHideDialog = function initHideDialog(dialogEl, dialogID) {
  var cancelEdit = tools.getNodes('bc-wish-list-dialog-close', false, dialogEl, false)[0];

  if (!cancelEdit) {
    return;
  }

  cancelEdit.setAttribute('data-dialogid', dialogID);
};
/**
 * @function hideDialog
 * @description Hide the current dialog when triggered.bc-create-wish-list-form--new
 * @param e
 */


var hideDialog = function hideDialog(e) {
  var dialogID = e.delegateTarget.dataset.dialogid;
  instances.dialogs[dialogID].hide();
};

var initDialogs = function initDialogs() {
  tools.getNodes('[data-js="bc-manage-wish-list"]:not(.initialized)', true, document, true).forEach(function (dialog) {
    tools.getNodes('bc-wish-list-dialog-trigger', false, dialog).forEach(function (dialogTrigger) {
      var dialogID = dialogTrigger.dataset.content;
      var target = tools.getNodes(dialogID, false, dialog)[0];

      if (!dialogTrigger || !target) {
        return;
      }

      dialog.classList.add('initialized');
      dialog.setAttribute('dialogid', dialogID);
      instances.dialogs[dialogID] = new _mtA11yDialog.default(getOptions(dialogID));
      instances.dialogs[dialogID].on('hide', function (elem) {
        var editNameField = tools.getNodes('.bc-wish-list-name-field', false, elem, true)[0];

        if (!editNameField) {
          return;
        }

        editNameField.value = editNameField.dataset.defaultValue;
      });
      instances.dialogs[dialogID].on('render', function (elem) {
        (0, _delay2.default)(function () {
          return initHideDialog(elem, dialogID);
        }, 50);
      });
    });
  });
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)('[data-js="bc-wish-list-dialog-close"]', 'click', hideDialog);
};

var init = function init() {
  if (!el.container) {
    return;
  }

  initDialogs();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 449 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _delay2 = _interopRequireDefault(__webpack_require__(9));

var _delegate = _interopRequireDefault(__webpack_require__(4));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _wishLists = __webpack_require__(450);

/**
 * @module Share Wish List
 *
 * @description scripts used to share wishlists.
 */
var el = {
  container: tools.getNodes('.bc-manage-wish-list-header', false, document, true)[0]
};
/**
 * @function animateToolTip
 * @description Animate and then remove the tooltip for the copied wishlist URL.
 * @param tooltip
 */

var animateToolTip = function animateToolTip(tooltip) {
  if (!tooltip) {
    return;
  }

  el.copyButton.setAttribute('disabled', 'disabled');
  (0, _delay2.default)(function () {
    return tools.addClass(tooltip, 'active');
  }, 150);
  (0, _delay2.default)(function () {
    return tools.removeClass(tooltip, 'active');
  }, 2000);
  (0, _delay2.default)(function () {
    tooltip.parentNode.removeChild(tooltip);
    el.copyButton.removeAttribute('disabled');
  }, 2150);
};
/**
 * @function handleShareCopy
 * @description Handle the click event for the copy button and copy the URL to the clipboard.
 * @param e
 */


var handleShareCopy = function handleShareCopy(e) {
  if (!e) {
    return;
  }

  var shareWrapper = tools.closest(e.delegateTarget, '[data-js="bc-manage-wish-list-share"]');
  var shareField = tools.getNodes('#bc-wish-list-share', false, shareWrapper, true)[0];
  var tooltip = document.createElement('div');
  tools.addClass(tooltip, 'bc-copied-wish-list-wrapper');
  tooltip.innerHTML = _wishLists.copyToolTip;
  shareField.select();
  document.execCommand('copy');

  if (tools.hasClass(tooltip, 'active')) {
    return;
  }

  shareWrapper.appendChild(tooltip);
  animateToolTip(tooltip);
};

var cacheElements = function cacheElements() {
  el.copyButton = tools.getNodes('bc-copy-wishlist-url', false, el.container)[0];
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)(el.container, '[data-js="bc-copy-wishlist-url"]', 'click', handleShareCopy);
};

var init = function init() {
  if (!el.container) {
    return;
  }

  cacheElements();
  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 450 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.copyToolTip = void 0;

var _i18n = __webpack_require__(7);

var copyToolTip = "\n\t<div class=\"bc-wish-list-copied\">\n\t\t<span class=\"bc-wish-list-copied-success\">".concat(_i18n.NLS.wish_lists.copy_success, "</span>\n\t</div>\n\t");
exports.copyToolTip = copyToolTip;

/***/ }),
/* 451 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _delegate = _interopRequireDefault(__webpack_require__(4));

var tools = _interopRequireWildcard(__webpack_require__(2));

/**
 * @module Product Detail Page Wish List
 *
 * @description Scripts used for adding an item to a Wish List on the PDP.
 */
var el = {
  container: tools.getNodes('bc-pdp-add-to-wish-list')[0]
};

var hideWishLists = function hideWishLists() {
  var wrapper = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var button = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var list = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
  tools.removeClass(wrapper, 'bc-show-lists');
  tools.removeClass(button, 'bc-show-lists');
  tools.removeClass(list, 'bc-show-lists');
  button.setAttribute('aria-expanded', false);
};

var showWishLists = function showWishLists() {
  var wrapper = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
  var button = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  var list = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
  tools.addClass(wrapper, 'bc-show-lists');
  tools.addClass(button, 'bc-show-lists');
  tools.addClass(list, 'bc-show-lists');
  button.setAttribute('aria-expanded', true);
};

var handleClickOutsideList = function handleClickOutsideList(e) {
  var openLists = tools.getNodes('[data-js="bc-pdp-add-to-wish-list"].bc-show-lists', true, e.currentTarget, true);

  if (!openLists) {
    return;
  }

  openLists.forEach(function (wrapper) {
    if (wrapper.contains(e.target)) {
      return;
    }

    var button = tools.getNodes('bc-pdp-wish-list-toggle', false, wrapper)[0];
    var list = tools.getNodes('bc-pdp-wish-lists', false, wrapper)[0];
    hideWishLists(wrapper, button, list);
  });
};

var toggleWishListsList = function toggleWishListsList(e) {
  var button = e.delegateTarget;
  var wrapper = tools.closest(button, '[data-js="bc-pdp-add-to-wish-list"]');
  var list = tools.getNodes('bc-pdp-wish-lists', false, wrapper)[0];

  if (tools.hasClass(button, 'bc-show-lists')) {
    hideWishLists(wrapper, button, list);
    return;
  }

  showWishLists(wrapper, button, list);
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)(el.container, '[data-js="bc-pdp-wish-list-toggle"]', 'click', toggleWishListsList);
  document.addEventListener('click', handleClickOutsideList);
};

var init = function init() {
  if (!el.container) {
    return;
  }

  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 452 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _i18n = __webpack_require__(7);

var tools = _interopRequireWildcard(__webpack_require__(2));

/**
 * @module Wish List List Module
 *
 * @description scripts used to share wishlists in wishlist lists
 */
var el = {
  container: tools.getNodes('.bc-wish-list-body', false, document, true)[0]
};
/**
 * @function handleShareCopy
 * @description Handle the click event for the copy button and copy the URL to the clipboard.
 * @param e
 */

var handleShareCopy = function handleShareCopy(e) {
  if (!e) {
    return;
  }

  var button = e.delegateTarget;
  var shareWrapper = tools.closest(e.delegateTarget, '[data-js="bc-wish-list-actions"]');
  var shareField = tools.getNodes('.bc-wishlist-link-input', false, shareWrapper, true)[0];
  var tooltip = document.createElement('div');
  shareField.type = 'text';
  tools.addClass(tooltip, 'bc-copied-wish-list-wrapper');
  shareField.select();
  document.execCommand('copy');
  shareField.type = 'hidden';
  button.innerHTML = _i18n.NLS.wish_lists.copied;
  button.disabled = true;
  setTimeout(function () {
    button.innerHTML = _i18n.NLS.wish_lists.copy_link;
    button.disabled = false;
  }, 1000);
};

var bindEvents = function bindEvents() {
  (0, _delegate.default)(el.container, '[data-js="bc-copy-wishlist-url"]', 'click', handleShareCopy);
};

var init = function init() {
  if (!el.container) {
    return;
  }

  bindEvents();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 453 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _banners = _interopRequireDefault(__webpack_require__(454));

/**
 * @module Banners
 * @description Clearinghouse for all banner scripts.
 */
var init = function init() {
  (0, _banners.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 454 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var GLOBALS = _interopRequireWildcard(__webpack_require__(11));

var _banners = __webpack_require__(455);

/**
 * @module Banners
 * @description Adds banners to your site.
 */
var banners = {
  top: '',
  bottom: ''
};
/**
 * Determines if the given banner should be displayed.
 *
 * @param {*} banner BigCommerce Banner object.
 * @return {boolean} True if banner should be displayed false otherwise.
 */

var showBanner = function showBanner(banner) {
  var now = Math.round(new Date().getTime() / 1000); // check if banner is visible and not expired

  if (!banner.visible || banner.date_type === 'custom' && now >= banner.date_to) {
    return false;
  }

  return true;
};

var init = function init() {
  if (!GLOBALS.BANNERS) {
    return;
  } // Loop through banners array


  GLOBALS.BANNERS.items.forEach(function (banner) {
    if (!showBanner(banner)) {
      return;
    } // append banner to location


    banners[banner.location] += (0, _banners.bannerContent)(banner.content);
  });
  var styles = "background-color: ".concat(GLOBALS.BANNERS.bg_color, "; color: ").concat(GLOBALS.BANNERS.text_color, ";"); // insert "top" banners

  if (banners.top) {
    document.body.insertAdjacentHTML('afterbegin', (0, _banners.bannerWrapper)(styles, banners.top));
  } // insert "bottom" banners


  if (banners.bottom) {
    document.body.insertAdjacentHTML('beforeend', (0, _banners.bannerWrapper)(styles, banners.bottom));
  }
};

var _default = init;
exports.default = _default;

/***/ }),
/* 455 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.bannerWrapper = exports.bannerContent = void 0;

var bannerContent = function bannerContent(content) {
  return "<div class=\"bc-banner\">".concat(content, "</div>");
};

exports.bannerContent = bannerContent;

var bannerWrapper = function bannerWrapper() {
  var styles = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var banners = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  return "<div class=\"bc-banners\" style=\"".concat(styles, "\">").concat(banners, "</div>");
};

exports.bannerWrapper = bannerWrapper;

/***/ }),
/* 456 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _segment = _interopRequireDefault(__webpack_require__(457));

var _matomo = _interopRequireDefault(__webpack_require__(458));

/**
 * @module Analytics Tracking Events
 * @description Clearing house to load all public analytics functionality.
 */
var init = function init() {
  (0, _segment.default)();
  (0, _matomo.default)();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 457 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

var _regenerator = _interopRequireDefault(__webpack_require__(184));

__webpack_require__(110);

var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(185));

__webpack_require__(45);

var _delegate = _interopRequireDefault(__webpack_require__(4));

var _wpSettings = __webpack_require__(11);

var tools = _interopRequireWildcard(__webpack_require__(2));

var _events = __webpack_require__(5);

/**
 * @module Segment Analytics Tracking
 * @description Allow Facebook Pixel and Google Analytics tracking on specified Segment events.
 */
var el = {
  segment: tools.getNodes('bc-segment-tracker')[0]
};
/**
 * @function handleAddToCartTracker
 * @description Event handler for tracking products added to the cart.
 * @param e
 */

var handleAddToCartTracker = function handleAddToCartTracker(e) {
  var cartTrigger = e ? e.detail.cartButton : tools.getNodes('[data-tracking-event="add_to_cart_message"]', false, document, true)[0];

  if (!cartTrigger) {
    return;
  }

  var analyticsData = cartTrigger.dataset.trackingData;
  var jsonData = JSON.parse(analyticsData);
  analytics.track('Product Added', {
    cart_id: e ? e.detail.cart_id : jsonData.cart_id,
    product_id: jsonData.product_id,
    variant: jsonData.variant_id
  });
  console.info("Segment has sent the following cart tracking data to your analytics account(s): ".concat(analyticsData));
};
/**
 * @function handleClickTracker
 * @description Event handler for clicking on products to view PDP or Quick View.
 * @param e
 */


var handleClickTracker = function handleClickTracker(e) {
  var target = e.delegateTarget;
  var analyticsData = target.dataset.trackingData;

  if (!analyticsData) {
    return;
  }

  var jsonData = JSON.parse(analyticsData);
  analytics.track('Product Viewed', {
    product_id: jsonData.product_id,
    name: jsonData.name
  });
  console.info("Segment has sent the following tracking data to your analytics account(s): ".concat(analyticsData));
};
/**
 * @function handleOrderCompleteTracker
 * @description Event handler for embedded checkout order completion.
 * @param e
 * TODO: This needs to be overhauled once BC can provide proper order data in the ECO response.
 */


var handleOrderCompleteTracker = function handleOrderCompleteTracker(e) {
  if (!e.detail) {
    return;
  }

  var cartID = e.detail.cart_id;
  analytics.track('BigCommerce Order Completed', {
    cart_id: cartID
  });
  console.info("Segment has sent the following tracking data to your analytics account(s): Order Completed. Cart ID: ".concat(cartID));
};
/**
 * @function gaCrossDomainInit
 * @description Enable GA x-domain tracking by default.
 * @return {Promise<void>}
 */


var gaCrossDomainInit =
/*#__PURE__*/
function () {
  var _ref = (0, _asyncToGenerator2.default)(
  /*#__PURE__*/
  _regenerator.default.mark(function _callee() {
    return _regenerator.default.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            if (!(typeof ga === 'undefined')) {
              _context.next = 2;
              break;
            }

            return _context.abrupt("return");

          case 2:
            _context.next = 4;
            return analytics.ready(function () {
              ga('require', 'linker');
              ga('linker:autoLink', [_wpSettings.STORE_DOMAIN]);
            });

          case 4:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, this);
  }));

  return function gaCrossDomainInit() {
    return _ref.apply(this, arguments);
  };
}();

var bindEvents = function bindEvents() {
  tools.getNodes('bc-product-loop-card', true, document).forEach(function (product) {
    (0, _delegate.default)(product, '[data-js="bc-product-quick-view-dialog-trigger"]', 'click', handleClickTracker);
    (0, _delegate.default)(product, '.bc-product__title-link', 'click', handleClickTracker);
  });
  tools.getNodes('bc-product-quick-view-content', true, document).forEach(function (dialog) {
    (0, _delegate.default)(dialog, '.bc-product__title-link', 'click', handleClickTracker);
  });
  (0, _events.on)(document, 'bigcommerce/analytics_trigger', handleAddToCartTracker);
  (0, _events.on)(document, 'bigcommerce/order_complete', handleOrderCompleteTracker);
};

var init = function init() {
  if (!el.segment) {
    return;
  }

  gaCrossDomainInit();
  bindEvents();
  handleAddToCartTracker();
};

var _default = init;
exports.default = _default;

/***/ }),
/* 458 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _interopRequireWildcard = __webpack_require__(1);

var _interopRequireDefault = __webpack_require__(0);

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = void 0;

__webpack_require__(45);

var _delegate = _interopRequireDefault(__webpack_require__(4));

var tools = _interopRequireWildcard(__webpack_require__(2));

var _events = __webpack_require__(5);

/**
 * @module Matomo Analytics Tracking
 * @description Handle Matomo analytics tracking events.
 */
var matomoConfig = {
  matomoAPI: typeof _paq !== 'undefined' // Check for the global _paq function and confirm it's an object.

};

var setChannelVisitTracking = function setChannelVisitTracking() {
  if (!window.bigcommerce_config) {
    return;
  }

  _paq.push(['setCustomVariable', window.bigcommerce_config.matomo.custom_variables.var_1.id, // Index, the number from 1 to 5 where this custom variable name is stored
  window.bigcommerce_config.matomo.custom_variables.var_1.name, // Name, the name of the variable, for example: BC_Channel
  "".concat(window.bigcommerce_config.channel.id, " - ").concat(window.bigcommerce_config.channel.name), // Value, for example: "Channel Name", "Channel ID", "3324567"
  'visit'] // Scope of the custom variable, "visit" means the custom variable applies to the current visit
  );
};

var handleAddToCartTracker = function handleAddToCartTracker(e) {
  var cartTrigger = e ? e.detail.cartButton : tools.getNodes('[data-tracking-event="add_to_cart_message"]', false, document, true)[0];

  if (!cartTrigger) {
    return;
  }

  var analyticsData = cartTrigger.dataset.trackingData;
  var jsonData = JSON.parse(analyticsData);
  setChannelVisitTracking(); // add the first product to the order

  _paq.push(['addEcommerceItem', jsonData.product_id, // (required) SKU: Product unique identifier
  jsonData.name] // (optional) Product name
  );

  console.info("Matomo has recorded the following cart tracking data: ".concat(analyticsData));
};

var handleProductView = function handleProductView(e) {
  var target = e.delegateTarget;
  var analyticsData = target.dataset.trackingData;

  if (!analyticsData) {
    return;
  }

  var jsonData = JSON.parse(analyticsData);
  setChannelVisitTracking();

  _paq.push(['setEcommerceView', jsonData.product_id, // (required) SKU: Product unique identifier
  jsonData.name] // (optional) Product name
  ); // Calling trackPageView is required when tracking a product view


  _paq.push(['trackPageView']);

  console.info("Matomo has recorded the following tracking data: ".concat(analyticsData));
};

var bindEvents = function bindEvents() {
  tools.getNodes('bc-product-loop-card', true, document).forEach(function (product) {
    (0, _delegate.default)(product, '[data-js="bc-product-quick-view-dialog-trigger"]', 'click', handleProductView);
    (0, _delegate.default)(product, '.bc-product__title-link', 'click', handleProductView);
  });
  tools.getNodes('bc-product-quick-view-content', true, document).forEach(function (dialog) {
    (0, _delegate.default)(dialog, '.bc-product__title-link', 'click', handleProductView);
  });
  (0, _events.on)(document, 'bigcommerce/analytics_trigger', handleAddToCartTracker);
};

var init = function init() {
  if (!matomoConfig.matomoAPI) {
    return;
  }

  bindEvents();
  handleAddToCartTracker();
};

var _default = init;
exports.default = _default;

/***/ })
],[192]);
//# sourceMappingURL=scripts.js.map