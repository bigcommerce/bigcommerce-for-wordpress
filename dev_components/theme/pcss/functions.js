var color = require('css-color-converter');

module.exports = {
	darken: function (value, frac) {
		var darken = 1 - parseFloat(frac);
		var rgba = color(value).toRgbaArray();
		var r = rgba[0] * darken;
		var g = rgba[1] * darken;
		var b = rgba[2] * darken;
		return color([r, g, b]).toHexString();
	},

	lighten: function (value, frac) {
		var lighten = 1 + parseFloat(frac);
		var rgba = color(value).toRgbaArray();
		var r = rgba[0] * lighten;
		var g = rgba[1] * lighten;
		var b = rgba[2] * lighten;
		return color([r, g, b]).toHexString();
	},
};
