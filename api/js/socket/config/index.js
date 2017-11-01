var lodash = require("lodash");
var defaults = require("./default.js");
var config = require("./" + (process.env.NODE_ENV || "development") + ".js");
console.log(lodash.merge({}, defaults, config));
module.exports = lodash.merge({}, defaults, config);
