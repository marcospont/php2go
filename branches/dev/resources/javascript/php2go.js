/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * @fileoverview
 * This file contains the base of the PHP2Go Javascript Framework. The UserAgent
 * class, that provides information about the user browser; The PHP2Go base class;
 * The Object and Function class and prototype extensions
 */

/**
 * Main class of the framework. Contains utility methods
 * that might be used by all other classes and libraries
 * @class PHP2Go
 */
var PHP2Go = {
	/**
	 * Base URL of the PHP2Go Javascript Framework
	 * @type String
	 */
	baseUrl : null,
	/**
	 * Active locale code
	 * @type String
	 */
	locale : null,
	/**
	 * @ignore
	 */
	included : {},
	/**
	 * @ignore
	 */
	loaded : false,
	/**
	 * @ignore
	 */
	uidCache : [0,{}],
	/**
	 * @ignore
	 */
	scriptRegExpAll : new RegExp('(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)', 'img'),
	/**
	 * @ignore
	 */
	scriptRegExpOne : new RegExp('(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)', 'im'),
	/**
	 * Indicates if it was possible to add
	 * methods in the HTMLElement class prototype
	 * @type Boolean
	 */
	nativeElementExtension : !!window.HTMLElement,
	/**
	 * Includes a given JS libray
	 * @param {String} lib Library path
	 * @param {String} charset Library charset
	 * @type void
	 */
	include : function(lib, charset) {
		if (!this.included[lib])
			document.write("<script type=\"text/javascript\"" + (charset ? " charset=\"" + charset + "\"" : "") + " src=\"" + lib + "\"></script>");
	},
	/**
	 * Framework's initialization routine. Automatically
	 * called inside php2go.js. Don't call it by yourself
	 * @type void
	 */
	load : function() {
		if (this.loaded)
			return;
		var mt, scripts = document.getElementsByTagName('script');
		for (var i=0; i<scripts.length; i++) {
			if (scripts[i].src) {
				this.included[scripts[i].src] = true;
				mt = scripts[i].src.match(/(.*)php2go\.js(\?locale=([^&]+)(&date=(.*))?(&charset=(.*))?)?/);
				if (mt) {
					this.baseUrl = mt[1];
					break;
				}
			} else {
				mt = [];
			}
		}
		if (!mt[3])
			PHP2Go.raiseException("PHP2Go Javascript Framework needs a locale parameter: php2go.js?locale=locale_code");
		this.locale = mt[3];
		this.include(this.baseUrl + 'structures.js');
		this.include(this.baseUrl + 'dom.js');
		this.include(this.baseUrl + 'compat.js');
		this.include(this.baseUrl + 'locale.php?locale=' + mt[3] + (mt[4] ? mt[4] : ''), (mt[7] ? mt[7] : null));
		this.loaded = true;
	},
	/**
	 * Raise a custom exception
	 * @param {String} Exception message
	 * @param {String} Exception name (optional)
	 * @return void
	 */
	raiseException : function(msg, name) {
		if (Object.isFunc(window.Error)) {
			var e = new Error(msg);
			if (!e.message)
				e.message = msg;
			if (name)
				e.name = name;
			throw e;
		} else if (Object.isString(msg)) {
			throw msg;
		}
	},
	/**
	 * UID generator function. The "pfx" parameter
	 * can be used to specify a prefix for the generated ID
	 * @param {String} pfx UID prefix
	 * @type String
	 */
	uid : function(pfx) {
		var c = PHP2Go.uidCache;
		if (pfx) {
			c[1][pfx] = (c[1][pfx] || 0) + 1;
			return pfx + String(c[1][pfx]);
		} else {
			return String(++c[0]);
		}
	},
	/**
	 * Contains information about the user's navigator and OS
	 *
	 * <br>Properties:
	 * <ul><li>ie (Boolean)</li><li>ie7 (Boolean)</li><li>ie6 (Boolean)</li><li>
	 * ie5 (Boolean)</li><li>opera (Boolean)</li><li>khtml (Boolean)</li><li>safari
	 * (Boolean)</li><li>mozilla (Boolean)</li><li>gecko (Boolean)</li><li>windows
	 * (Boolean)</li><li>linux (Boolean)</li><li>mac (Boolean)</li><li>unix
	 * (Boolean)</li><li>os (String)</li></ul>
	 *
	 * @type Object
	 */
	browser : function() {
		var bw = {}, ua = navigator.userAgent.toLowerCase();
		bw.ie = /msie/i.test(ua) && !/opera/i.test(ua);
		bw.ie7 = bw.ie && /msie 7/i.test(ua);
		bw.ie6 = bw.ie && /msie 6/i.test(ua);
		bw.ie5 = bw.ie && /msie 5\.0/i.test(ua);
		bw.opera = /opera/i.test(ua);
		bw.khtml = /konqueror|safari|webkit|khtml/i.test(ua);
		bw.safari = /safari|webkit/i.test(ua);
		bw.mozilla = !bw.ie && !bw.opera && !bw.khtml && /mozilla/i.test(ua);
		bw.gecko = /gecko/i.test(ua);
		bw.windows = bw.linux = bw.mac = bw.unix = false;
		bw.os = (/windows/i.test(ua) ? 'windows' : (/linux/i.test(ua) ? 'linux' : (/mac/i.test(ua) ? 'mac' : (/unix/i.test(ua) ? 'unix' : 'unknown'))));
		(bw.os != 'unknown') && (bw[bw.os] = true);
		return bw;
	}(),
	/**
	 * Utility method to compare values using a given
	 * operator and a given datatype. Both values must
	 * be of the provided type
	 * @param {Object} a Left operand
	 * @param {Object} b Right operand
	 * @param {String} op Operator (EQ, NEQ, LT, LOET, GT, GOET)
	 * @param {String} type Data type
	 * @type Boolean
	 */
	compare : function(a, b, op, type) {
		type = type || 'STRING';
		switch (type) {
			case 'INTEGER' : a = parseInt(a, 10), b = parseInt(b, 10); break;
			case 'FLOAT' : a = parseFloat(a, 10), b = parseFloat(b, 10); break;
			case 'CURRENCY' : a = parseFloat(a, 10), b = parseFloat(b, 10); break;
			case 'DATE' : a = Date.toDays(a), b = Date.toDays(b); break;
		}
		switch (op) {
			case 'EQ' : return (a == b);
			case 'NEQ' : return (a != b);
			case 'LT' : return (a < b);
			case 'LOET' : return (a <= b);
			case 'GT' : return (a > b);
			case 'GOET' : return (a >= b);
			default : return false;
		}
	},
	/**
	 * Evaluates a Javascript string in global context
	 * @param {String} Javascript string
	 */
	eval : function(str) {
		if (window.execScript)
			window.execScript(str);
		else if (this.browser.safari)
			window.setTimeout(str, 0);
		else
			eval.call(window, str);
	},
	/**
	 * Builds a reference to an object's method.
	 * Can be used to bind events to methods.
	 * @param {Object} obj Object
	 * @param {String} method Method name
	 * @type Function
	 */
	method : function(obj, method) {
		if (!obj._methods)
			obj._methods = {};
		if (!obj._methods[method])
			obj._methods[method] = function() { obj[method].apply(obj, arguments); };
		return obj._methods[method];
	}
};

/**
 * Indicates if it was possible to add
 * methods in the Event native prototype
 * @type Boolean
 */
try {
	PHP2Go.nativeEventExtension = (typeof(Event.prototype) != 'undefined');
} catch(e) {
	PHP2Go.nativeEventExtension = false;
}

/**
 * Makes all properties and methods of src available to dst
 * @param {Object} dst Target
 * @param {Object} src Source
 * @param {Boolean} ov Override existent properties
 * @type Object
 * @addon
 */
Object.extend = function(dst, src, ov) {
	ov = Object.ifUndef(ov, true);
	for (p in src) {
		if (!ov && dst[p])
			continue;
		dst[p] = src[p];
	}
	return dst;
};

/**
 * Dumps information about a given object.
 * Uses the serialize method, if available, or
 * the toString method. Handles special
 * cases such as undefined and null
 * @param {Object} obj Object to be serialized out
 * @type String
 */
Object.serialize = function(obj) {
	if (typeof(obj) == 'undefined') return;
	if (typeof(obj) == 'function') return;
	if (typeof(obj) == 'boolean') return obj.toString();
	if (obj === null) return 'null';
	if (obj.serialize) return obj.serialize();
	if (obj === window || obj === document) return;
	var buf = [];
	for (var p in obj) {
		try {
			if (obj[p] == '') continue;
			if (obj[p] && obj[p].ownerDocument === document) continue;
			var v = Object.serialize(obj[p]);
			if (typeof(v) != 'undefined')
				buf.push(p + ":" + v);
		} catch (e) { }
	}
	return '{' + buf.join(', ') + '}';
};

/**
 * Checks if a given object is undefined
 * @param {Object} obj Object
 * @type Boolean
 */
Object.isUndef = function(obj) {
	return (typeof obj == 'undefined');
};

/**
 * Tests a given object and returns a default value when it's undefined
 * @param {Object} obj Object
 * @param {Object} def Fallback value
 * @type Object
 */
Object.ifUndef = function(obj, def) {
	return (typeof obj == 'undefined' ? def : obj);
};

/**
 * Checks if the given object is a DOM node
 * @param {Object} obj Object
 * @type Boolean
 */
Object.isElement = function(obj) {
	return (obj && obj.nodeType == 1);
};

/**
 * Checks if a given object is an array
 * @param {Object} obj Object
 * @type Boolean
 */
Object.isArray = function(obj) {
	return (obj && obj.constructor === Array);
};

/**
 * Checks if a given object is a function
 * @param {Object} obj Object
 * @type Boolean
 */
Object.isFunc = function(obj) {
	return (typeof obj == 'function');
};

/**
 * Checks if a given object is a string
 * @param {Object} obj Object
 * @type Boolean
 */
Object.isString = function(obj) {
	return (typeof obj == 'string');	
};

/**
 * Checks if a given object is a number
 * @param {Object} obj Object
 * @type Boolean
 */
Object.isNumber = function(obj) {
	return (typeof obj == 'number');
};

/**
 * Parses the value of a style property
 * @param {String} val Property value
 * @type Number
 */
Object.toPixels = function(val) {
	if (val && val.slice(-2) == 'px')
		return parseFloat(val, 10);
	return 0;
};


if (!Function.prototype.apply) {
	/**
	 * Applies the function on a given object, using
	 * the given set of arguments
	 * @param {Object} obj Object to apply the function
	 * @param {Array} args Arguments set
	 * @return The function return
	 */
	Function.prototype.apply = function(obj, args) {
		var argStr = [], res = null;
		(!obj) && (obj = window);
		(!args) && (args = []);
		for (var i=0; i<args.length; i++)
			argStr[i] = 'args[' + i + ']';
		obj.__f__ = this;
		res = eval('obj.__f__(' + argStr.join(',') + ')');
		obj.__f__ = null;
		return res;
	};
}

/**
 * Returns an instance of the function pre-bound
 * to the method owner object
 * @param {Object} obj The object that owns the method
 * @type Function
 */
Function.prototype.bind = function(obj) {
	var self = this;
	return function() {
		return self.apply(obj, arguments);
	}
};

/**
 * Encapsulates the pattern of converting the
 * first argument of the function into its 'this' value
 * @type Function
 */
Function.prototype.methodize = function() {
	if (this._methodized)
		return this._methodized;
	var self = this;
	return this._methodized = function() {
		return self.apply(null, [this].concat($A(arguments)));
	};
};

/**
 * Creates an inheritance relationship with another function,
 * if both are class constructors. If propName is provided,
 * a direct link to the parent's constructor will also be created.
 * @param {Function} parent Parent class constructor
 * @param {String} propName Prop name that should hold the reference to the parent ctor
 * @type void
 */
Function.prototype.extend = function(parent, propName) {
	if (Object.isFunc(parent)) {
		// inheritance
		var f = function() {};
		f.prototype = parent.prototype;
		this.prototype = new f();
		this.prototype.constructor = this;
		// parent class reference
		this.superclass = parent.prototype;
		// parent constructor reference
		if (propName)
			this.prototype[propName] = parent.prototype.constructor;
		if (parent.prototype.constructor == Object.prototype.constructor)
			parent.prototype.constructor = parent;
	}
};

/**
 * Calls the function after a given number of miliseconds.
 * The first argument must be the delay. The others will be
 * used as arguments to the function call. Returns the
 * timeout handle.
 * @type Number
 */
Function.prototype.delay = function() {
	var self = this, args = $A(arguments), ms = args.shift();
	return window.setTimeout(function() {
		return self.apply(self, args);
	}, ms);
};

/**
 * Removes blank chars from the start and
 * from the end of the string
 * @type String
 */
String.prototype.trim = function() {
	return this.replace(/^\s*/, "").replace(/\s*$/, "");
};

/**
 * Checks if the string is empty
 * @type Boolean
 */
String.prototype.empty = function() {
	return /^\s*$/.test(this);
};

/**
 * Verifies if a given value is present on the string.
 * The test is made using the indexOf method
 * @param {String} str Substring to be searched
 * @type Boolean
 */
String.prototype.find = function(str) {
	return (this.indexOf(str) != -1);
};

/**
 * Wraps the string in left and right sides. If right
 * wrap string is not provided, left is used
 * @param {String} l Wrap for left side
 * @param {String} r Wrap for right side
 * @type String
 */
String.prototype.wrap = function(l, r) {
	return (l || '') + this + (r || l || '');
};

/**
 * Remove a slice from the string, starting at p1 and ending at p2
 * @param {Number} p1 Cut start position
 * @param {Number} p2 Cut end position
 * @type String
 */
String.prototype.cut = function(p1, p2) {
	return this.substr(0, p1) + this.substr(p2 || this.length);
};

/**
 * Insert a string inside another string in a given position
 * @param {String} val Value to insert
 * @param {Number} at Insert position
 * @type String
 */
String.prototype.insert = function(val, at) {
	at = at || 0;
	return this.substr(0, at) + val + this.substr(at);
};

/**
 * Remove a portion of the string and replace it with something else
 * @param {Number} offset Cut start position
 * @param {Number} len Cut length
 * @param {String} replace Replacement value
 * @type String
 */
String.prototype.splice = function(offset, len, replace) {
	return this.cut(offset, offset+len).insert(replace, offset);
};

/**
 * Repeat the string 'n' times and
 * return the resultant string
 * @param {Number} n Times to repeat the string
 * @type String
 */
String.prototype.repeat = function(n) {
	var res = '', n = (n || 0);
	while (n--)
		res += this;
	return res;
};

/**
 * Fills the string until a given length using
 * another substring. The 'type' attribute defines
 * if the padding must be inserted at the left side,
 * at the right side or at both sides of the string
 * @param {String} pad Pad substring
 * @param {Number} len Final length
 * @param {String} type Pad type
 * @type String
 */
String.prototype.pad = function(pad, len, type) {
	if (len < 0 || len <= this.length)
		return this;
	pad = (pad ? pad.charAt(0) : ' ');
	type = type || 'left';
	if (type == 'left')
		return pad.repeat(len-this.length) + this;
	else if (type == 'right')
		return this + pad.repeat(len-this.length);
	else
		return pad.repeat(Math.ceil((len - this.length)/2)) + this + pad.repeat(Math.floor((len - this.length)/2));
};

/**
 * Verifies if the string is equal to another string
 * using case-insensitive comparison
 * @param {String} str String to compare
 * @type Boolean
 */
String.prototype.equalsIgnoreCase = function(str) {
	return this.toLowerCase() == String(str).toLowerCase();
};

/**
 * Cross-browser encoding function
 * @type String
 */
String.prototype.urlEncode = function() {
	if (window.encodeURIComponent)
		return encodeURIComponent(this);
	return escape(this);
};

/**
 * Convert the string to its camelized version.
 * Ex: convert background-color into backgroundColor.
 * @type String
 */
String.prototype.camelize = function() {
	var res, tmp = this.split('-');
	if (tmp.length == 1)
		return tmp[0];
	res = (this.indexOf('-') == 0 ? tmp[0].charAt(0).toUpperCase() + tmp[0].substring(1) : tmp[0]);
	for (var i=1; i<tmp.length; i++)
		res += tmp[i].charAt(0).toUpperCase() + tmp[i].substring(1);
	return res;
};

/**
 * Capitalize the string
 * @type String
 */
String.prototype.capitalize = function() {
	var wl = this.split(/\s+/g);
	return $C(wl).map(function(w, idx) {
		return w.charAt(0).toUpperCase() + w.substr(1).toLowerCase();
	}).join(' ');
};

/**
 * Escapes all HTML code inside the string
 * and return a new string instance
 * @type String
 */
String.prototype.escapeHTML = function() {
	var self = arguments.callee;
	self.text.data = this;
	return self.div.innerHTML;
};
Object.extend(String.prototype.escapeHTML, {
	div: document.createElement('div'),
	text: document.createTextNode('')
});
with (String.prototype.escapeHTML)
	div.appendChild(text);


/**
 * Remove extra spaces from the string
 * @type String
 */
String.prototype.stripSpaces = function() {
	return this.replace(/\s+/g, ' ');
};

/**
 * Remove HTML/XHTML tags from the string
 * @type String
 */
String.prototype.stripTags = function() {
	return this.replace(/<\/?[^>]+>/gi, '');
};

/**
 * Remove &lt;script&gt; tags from the string
 * @type String
 */
String.prototype.stripScripts = function() {
	try {
		return this.replace(PHP2Go.scriptRegExpAll, '');
	} catch(e) {
		var self = this, sp = this.indexOf("<script"), ep = 0;
		while (sp != -1) {
			ep = self.substr(sp).indexOf("</script>");
			if (ep != -1) {
				self = (sp ? self.substr(0, sp-1) : '') + self.substr(sp).substr(ep+9);
				sp = self.indexOf("<script");
			} else {
				sp = -1;
			}
		}
		return self;
	}
};

/**
 * Extract and evaluate all script
 * blocks included in the string
 * @type void
 */
String.prototype.evalScripts = function() {
	try {
		var ra = PHP2Go.scriptRegExpAll;
		var ro = PHP2Go.scriptRegExpOne;
		var matches = this.match(ra) || [];
		return $C(matches).map(function(item) {
			var match = item.match(ro);
			if (match)
				PHP2Go.eval(match[1]);
		});
	} catch(e) {
		var tmp, self = this, sp = this.indexOf("<script"), ep1 = 0, ep2 = 0;
		while (sp != -1) {
			tmp = self.substr(sp);
			ep1 = tmp.indexOf(">");
			ep2 = tmp.indexOf("</script>");
			if (ep1 != -1 && ep2 != -1) {
				PHP2Go.eval(tmp.substr(ep1+1, ep2-ep1-1));
				self = (sp ? self.substr(0, sp-1) : '') + tmp.substr(ep2+9);
				sp = self.indexOf("<script");
			} else {
				sp = -1;
			}
		}
		return self;
	}
};

/**
 * Evaluate script blocks on the string after
 * a delay of "t" miliseconds
 * @param {Integer} t Delay in miliseconds
 * @type void
 */
String.prototype.evalScriptsDelayed = function(t) {
	var self = this, t = (t || 10);
	if (PHP2Go.browser.ie5) {
		window.timeoutArg = self;
		setTimeout("window.timeoutArg.evalScripts();window.timeoutArg=null;", t);
	} else {
		this.evalScripts.bind(this).delay(t);
	}
};

/**
 * Replace all %N directives found in the string
 * using the function arguments as replacements.
 * Ex: "%1, %2!".assignAll("Hello", "World");
 * @type String
 */
String.prototype.assignAll = function() {
	var a = $A(arguments), self = this;
	a.walk(function(item, idx) {
		self = self.replace('%' + (idx+1), item);
	});
	return self;
};

/**
 * Dumps out the string, including
 * starting and ending single quotes
 * @type String
 */
String.prototype.serialize = function() {
	return "'" + this.replace('\\', '\\\\').replace("'", '\\\'') + "'";
};

/**
 * Converts the string into an array
 * @type Array
 */
String.prototype.toArray = function() {
	return this.split('');
};

/**
 * Convert a date written in one of the
 * framework's formats (d/m/Y or Y/m/d) into
 * a Date object
 * @param {String} str Date string
 * @type Date
 * @addon
 */
Date.fromString = function(str) {
	var mt, dt = new Date();
	var loc = Locale.date, re = loc.regexp;
	if (mt = str.match(re)) {
		dt.setDate(parseInt(mt[loc.matches[0]], 10));
		dt.setMonth(parseInt(mt[loc.matches[1]], 10)-1);
		dt.setYear(mt[loc.matches[2]]);
		(mt[7]) && (dt.setHours(parseInt(mt[7], 10)));
		(mt[8]) && (dt.setMinutes(parseInt(mt[8], 10)));
		(mt[9]) && (dt.setSeconds(parseInt(mt[9], 10)));
	}
	return dt;
};

/**
 * Convert a date string in number of days. Used by
 * {@link PHP2Go#compare} to compare date values. This
 * method only understands dates in the acceptable date
 * formats (d/m/Y or Y/m/d)
 * @param {String} date Date string
 * @type Number
 */
Date.toDays = function(date) {
	var d, m, y, c;
	var loc = Locale.date;
	var re = loc.regexp;
	if (mt = date.match(re)) {
		d = parseInt(mt[loc.matches[0]], 10);
		m = parseInt(mt[loc.matches[1]], 10);
		y = mt[loc.matches[2]];
		c = parseInt(y.substring(0, 2), 10);
		y = y.substring(2);
		if (m > 2) {
			m -= 3;
		} else {
			m += 9;
			if (y) {
				y--;
			} else {
				y = 99;
				c--;
			}
		}
		return (Math.floor((146097*c)/4)+Math.floor((1461*y)/4)+Math.floor((153*m+2)/5)+d+1721119);
	} else {
		return 0;
	}
};

/**
 * Truncates a given number to a given precision (number of decimals)
 * @param {Number} num Number that must be truncated
 * @param {Number} prec Precision
 * @type Number
 * @addon
 */
Math.truncate = function(num, prec) {
	(isNaN(prec)) && (prec = 0);
	return (Math.round(num * Math.pow(10, prec)) / Math.pow(10, prec));
};

if (!Object.isFunc(window.isFinite)) {
	/**
	 * @ignore
	 */
	window.isFinite = function(number) {
		return (!isNaN(number) && (number <= Math.POSITIVE_INFINITY || number >= Math.NEGATIVE_INFINITY));
	}
}

/**
 * Converts the number to a string with length 'len'
 * @param {Number} len String length
 * @param {Number} base Target base (defaults to 10)
 * @type String
 * @addon
 */
Number.prototype.toPaddedString = function(len, base) {
    return this.toString(base||10).pad('0', len);
};
/**
 * Builds the string representation of the number
 * @type String
 */
Number.prototype.serialize = function() {
	return (isFinite(this) ? this.toString() : 'null');
};

/**
 * The Cookie singleton contains function to deal with browser cookies.
 * Contains the basic get, set and remove operations and 2 other utility
 * methods
 * @class Cookie
 */
var Cookie = {
	/**
	 * Searches for a cookie's value using its name
	 * @param {String} name Cookie name
	 * @type String
	 */
	get : function(name) {
		var re = new RegExp("(^|;)\\s*" + escape(name) + "=([^;]+)");
		var res = document.cookie.match(re);
		if (res && res[2])
			return unescape(res[2]);
		return null;
	},
	/**
	 * Retrieves a hash of all registered cookies
	 * @type Hash
	 */
	getAll : function() {
		var res = $H(), ck = document.cookie.split(';');
		var rn = new RegExp("^\s*([^=]+)"), rv = new RegExp("=(.*$)");
		for (var i=0; i<ck.length; i++) {
			// ignore errors here
			try { res.set(unescape(ck[i].match(rn)[1]), unescape(ck[i].match(rv)[1])); } catch(e) { }
		}
		return res;
	},
	/**
	 * Creates or sets a cookie. The expiry parameter must be expressed
	 * in seconds. Path, domain and secure arguments will be ignored if
	 * not provided
	 * @param {String} name Cookie name
	 * @param {String} value Cookie value
	 * @param {Number} exp Cookie expiration, in seconds
	 * @param {String} path Cookie path on this server
	 * @param {String} domain Cookie domain
	 * @param {Boolean} sec Whether to create a secure cookie
	 * @type void
	 */
	set : function(name, value, exp, path, domain, sec) {
		// sanitize parameters
		name = escape(name), value = escape(value);
		exp = (exp && !isNaN(exp) ? parseInt(exp, 10) : 0);
		var ck = name + "=" + value;
		if (exp) {
			var date = new Date();
			date.setTime(date.getTime() + (1000*parseInt(exp, 10)));
			ck += ';expires=' + date.toGMTString();
		}
		if (path)
			ck += ';path=' + path;
		if (domain)
			ck += ';domain=' + domain;
		if (!!sec)
			ck += ';secure';
		document.cookie = ck;
	},
	/**
	 * Removes a registered cookie
	 * @param {String} name Cookie name
	 * @param {String} path Cookie path
	 * @param {String} domain Cookie domain
	 * @type void
	 */
	remove : function(name, path, domain) {
		this.set(name, "", -3600, path, domain);
	},
	/**
	 * Utility method to transform a number of days, hours,
	 * minutes or weeks in number of seconds
	 * @param {Number} d Number of days
	 * @param {Number} h Number of hours
	 * @param {Number} m Number of minutes
	 * @param {Number} w Number of weeks
	 * @type Number
	 */
	buildLifeTime : function(d, h, m, w) {
		return (
			(!isNaN(d) ? (d * 24 * 60 * 60) : 0) +
			(!isNaN(h) ? (h * 60 * 60) : 0) +
			(!isNaN(m) ? (m * 60) : 0) +
			(!isNaN(w) ? (w * 7 * 24 * 60 * 60) : 0)
		);
	}
};

/**
 * Utility class to display messages in a log console.
 * It supports 5 types of messages: log, info, debug, warn
 * and error. The messages are displayed in a div, in the
 * top of the page
 * @class Logger
 */
var Logger = {
	/**
	 * Initialize the log console
	 * @type void
	 * @private
	 */
	initialize : function() {
		/**
		 * @ignore
		 */
		this.expanded = false;
		/**
		 * Log console container div
		 * @type Object
		 * @private
		 */
		this.container = $N('div', document.body, {
			position : 'absolute',
			left : '0px',
			top : '0px',
			width : '100px',
			height : '20px',
			textAlign : 'left',
			fontFamily : 'Courier',
			fontSize : '10px',
			color : 'white',
			zIndex : 2000
		});
		var self = this;
		// create and configure top div
		this.top = $N('div', this.container, {height : '20px'}, "<button type='button' style='width:50px;height:20px;background-color:ButtonFace;border:1px solid black;padding:0;margin:1px'>toggle</button><button type='button' style='width:40px;height:20px;background-color:ButtonFace;border:1px solid black;padding:0;margin:1px'>clear</button>");
		this.top.firstChild.onclick = function() {
			var c = self.container, o = self.output;
			var e = self.expanded = !self.expanded;
			c.style.width = (e ? '100%' : '100px');
			c.style.height = (e ? '200px' : '20px');
			o.toggleDisplay();
			if (e && o.lastChild)
				o.lastChild.scrollIntoView(false);
		};
		this.top.firstChild.nextSibling.onclick = function() {
			self.output.clear();
		};
		// create and configure output div
		this.output = $N('div', this.container, {
			width : '98%',
			height : '160px',
			display : 'none',
			position : 'absolute',
			overflow : 'auto',
			backgroundColor : 'white',
			margin : '5px',
			padding : '4px',
			border : '1px solid black'
		});
	},
	/**
	 * Logs a message into the console
	 * @param {String} text Log message
	 * @param {String} color Message color
	 * @type void
	 */
	log : function(text, color) {
		(!this.container) && (this.initialize());
		(!Object.isString(text)) && (text = Object.serialize(text));
		this.output.insertHTML("<pre style='padding:0;margin:0;color:" + (color || 'white') + "'>" + String(text).escapeHTML() + "</pre>", "bottom");
	},
	/**
	 * Logs an info message
	 * @param {String} text Message
	 * @type void
	 */
	info : function(text) {
		this.log(text, 'blue');
	},
	/**
	 * Logs a debug message
	 * @param {String} text Message
	 * @type void
	 */
	debug : function(text) {
		this.log(text, 'green');
	},
	/**
	 * Logs a warning message
	 * @param {String} text Message
	 * @type void
	 */
	warn : function(text) {
		this.log(text, 'orange');
	},
	/**
	 * Logs an error message
	 * @param {String} text Message
	 * @type void
	 */
	error : function(text) {
		this.log(text, 'red');
	},
	/**
	 * Logs info about an exception
	 * @param {Exception} e Exception
	 * @type void
	 */
	exception : function(e) {
		var info = '[' + e.name + '] - ' + e.message;
		if (e.stack) {
			info += "\n" + e.stack.split("\n").filter(function(item, idx) {
				var where = item.split('@');
				if (where[1] && where[1] != ':0')
					return 'at ' + where[1];
			}).join("\n");
		}
		this.log(info, 'red');
	}
};

/**
 * Contains methods to retrieve information about the browser
 * main window and also create new windows or popups
 * @class Window
 */
var Window = {
	/**
	 * Opens a new browser window. The "type" parameter is a sum
	 * where each member represents a chrome property: 1-toolbar,
	 * 2-location, 4-directories, 8-status, 16-menubar, 32-scrollbars,
	 * 64-resizable, 128-copyhistory. Ex: 48 = menubar+scrollbars
	 * @param {String} url Window URL
	 * @param {Number} wid Window width, defaults to screen width
	 * @param {Number} hei Window height, defaults to screen available height
	 * @param {Number} x Window X, defaults to 0
	 * @param {Number} y Window Y, defaults to 0
	 * @param {Number} type Bitmap chrome properties, defaults to 255
	 * @param {String} tit Window title
	 * @param {Boolean} ret Whether to return the created window or not
	 * @type void
	 */
	open : function(url, wid, hei, x, y, type, tit, ret) {
		// sanitize parameters
		wid = wid || screen.width, hei = hei || screen.availHeight;
		x = Math.abs(x), y = Math.abs(y);
		type = Object.ifUndef(type, 255), ret = !!ret;
		// build props string
		var props =
			(type & 1 ? 'toolbar,' : '') + (type & 2 ? 'location,' : '') +
			(type & 4 ? 'directories,' : '') + (type & 8 ? 'status,' : '') +
			(type & 16 ? 'menubar,' : '') + (type & 32 ? 'scrollbars,' : '') +
			(type & 64 ? 'resizable,' : '') + (type & 128 ? 'copyhistory,' : '') +
			'width='+wid+',height='+hei+',left='+x+',top='+y;
		var wnd = window.open(url, tit, props);
		wnd.focus();
		if (ret)
			return wnd;
	},
	/**
	 * Opens a new browser window in the center of the main window.
	 * Uses {@link Window#open} internally
	 * @param {String} url Window URL
	 * @param {Number} wid Window width, defaults to 800
	 * @param {Number} hei Window height, defaults to 600
	 * @param {Number} type Bitmap properties, defaults to 255
	 * @param {String} tit Window title
	 * @param {Boolean} ret Whether to return the created window or not
	 * @type void
	 */
	openCentered : function(url, wid, hei, type, tit, ret) {
		wid = wid || 800, hei = hei || 600;
		var x = Math.floor((screen.availWidth-wid)/2);
		var y = Math.floor((screen.availHeight-hei)/2);
		return Window.open(url, wid, hei, x, y, type, tit, ret);
	},
	/**
	 * Opens a new window usen the event's source element
	 * to define the X and Y coordinates. In browsers that
	 * don't support window.screenLeft and window.screenTop,
	 * the Y coordinate won't be accurate
	 * @param {Object} e Event
	 * @param {String} url Window URL
	 * @param {Number} wid Window width, defaults to 800
	 * @param {Number} hei Window height, defaults to 600
	 * @param {Number} type Bitmap properties, defaults to 255
	 * @param {String} tit Window title
	 * @param {Boolean} ret Whether to return the created window or not
	 */
	openFromEvent : function(e, url, wid, hei, type, tit, ret) {
		e = $EV(e), wid = wid || 800, hei = hei || 600;
		var w = window, b = document.body, x, y;
		var el = $E(e.element());
		var ep = el.getPosition();
		var ed = el.getDimensions();
		var ws = Window.scroll();
		if (!Object.isUndef(w.screenLeft)) {
			// window left offset + element X position + element width - window scroll X
			x = (w.screenLeft + ep.x + ed.width) - ws.x;
			// window top offset + element Y position - window scroll Y
			y = (w.screenTop + ep.y) - ws.y;
		} else {
			// window left offset + element X position + element width - window scroll X
			var sx = (w.screenX >= 0 ? w.screenX : 0);
			x = (sx + ep.x + ed.width) - ws.x;
			// event screen Y
			y = e.screenY - 10;
		}
		return Window.open(url, wid, hei, x+2, y, type, tit, ret);
	},
	/**
	 * Opens a blank window. Uses {@link Window#open} internally
	 * @param {Number} wid Window width, defaults to 640
	 * @param {Number} hei Window height, defaults to 480
	 * @param {Number} x Window X, defaults to 0
	 * @param {Number} y Window Y, defaults to 0
	 * @param {Number} type Bitmap properties, defaults to 255
	 */
	blank : function(wid, hei, x, y, type) {
		return Window.open('about:blank', wid, hei, x, y, type, PHP2Go.uid('blank'), true);
	},
	/**
	 * Writes HTML into the given window's document
	 * @param {Window} wnd Window object
	 * @param {String} html HTML contents
	 * @param {Boolean} close Whether to close the window's document
	 * @type void
	 */
	write : function(wnd, html, close) {
		close = Object.ifUndef(close, true);
		if (wnd.document) {
			if (!wnd.writing) {
				wnd.document.open();
				wnd.writing = true;
			}
			wnd.document.write(html);
			if (close) {
				wnd.writing = false;
				wnd.document.close();
			}
		}
	},
	/**
	 * Gets the window size. The returned value
	 * is an object containing x and y coordinates.
	 * The dimensions are related to the inner browser
	 * window
	 * @type Object
	 */
	size : function() {
		var w = window, b = document.body, e = document.documentElement;
		return {
			width : (w.innerWidth || e.clientWidth || b.offsetWidth),
			height : (w.innerHeight || e.clientHeight || b.offsetHeight)
		};
	},
	/**
	 * Retrieves the left and top offset of the browser window
	 * from the 0,0 coordinate of the desktop window. In
	 * browsers that support screenLeft and screenTop properties,
	 * the values returned include the browser's address bar and
	 * toolbars
	 * @type Object
	 */
	position : function() {
		var w = window;
		return {
			x : (w.screenX || w.screenLeft),
			y : (w.screenY || w.screenTop)
		};
	},
	/**
	 * Gets the window's scroll positions
	 * @type Number
	 */
	scroll : function() {
		var w = window, e = (document.documentElement || document.body);
		return {
			x : (w.pageXOffset || e.scrollLeft || 0),
			y : (w.pageYOffset || e.scrollTop || 0)
		};
	}
};

/**
 * This singleton contains methods to deal with IFRAME elements:
 * retrieve dimensions, change scrolling position and target URL
 * @class IFrame
 */
var IFrame = {
	/**
	 * Read the internal dimensions of an IFRAME's document.
	 * The value returned is an object containing 2 properties:
	 * width and height
	 * @param {Object} elm IFrame reference or id
	 * @type Object
	 */
	size : function(elm) {
		try {
			if (elm = $(elm)) {
				var b = (elm.contentDocument ? elm.contentDocument.body : window.frames[elm.id].document.body);
				return { width: b.offsetWidth, height: b.offsetHeight };
			}
		} catch(e) { }
		return { width: -1, height: -1 };
	},
	/**
	 * Scroll an iframe horizontally to a given position
	 * @param {Object} elm IFrame reference or id
	 * @param {Number} x New scrollX position
	 * @type void
	 */
	scrollXTo : function(elm, x) {
		elm = $(elm);
		var win = (elm ? (elm.contentWindow || window.frames(elm.id)) : null);
		if (window) {
			var y = (win.scrollY || win.document.body.scrollTop);
			win.scrollTo(x, y);
		}
	},
	/**
	 * Scroll an iframe vertically to a given position
	 * @param {Object} elm IFrame reference or id
	 * @param {Number} y New scrollY position
	 * @type void
	 */
	scrollYTo : function(elm, y) {
		elm = $(elm);
		var win = (elm ? (elm.contentWindow || window.frames(elm.id)) : null);
		if (win) {
			var x = (win.scrollX || win.document.body.scrollLeft);
			win.scrollTo(x, y);
		}
	},
	/**
	 * Change an IFrame's URL
	 * @param {Object} elm IFrame reference or id
	 * @param {String} url New URL
	 */
	setUrl : function(elm, url) {
		elm = $(elm);
		if (elm) {
			if (elm.contentDocument)
				elm.setAttribute('src', url);
			else if (window.frames(elm.id)) {
				window.frames(elm.id).location.replace(url);
			}
		}
	}
};

/**
 * The Report singleton is used by php2go.data.Report, responsible
 * for building data sets splitted in multiple pages with navigation
 * @class Report
 */
var Report = {
	/**
	 * Validates the page number entered in the "go to page"
	 * form, which is one of the navigation features of the Report class
	 * @param {Object} frm Go to page form
	 * @param {Number} curr Current page
	 * @param {Number} total Total of pages
	 * @param {Function} handler onChangePage handler
	 * @type Boolean
	 */
	goToPage : function(frm, curr, total, handler) {
		var pg, fld = frm.elements['page'];
		if (fld.value != '') {
			pg = parseInt(fld.value, 10);
			if (pg > 0 && pg <= total) {
				if (Object.isFunc(handler))
					handler({from: curr, to: pg});
				if (frm.action.indexOf('?') == -1)
					frm.action = frm.action+'?page='+pg;
				else if (frm.action.indexOf('?') == frm.action.length-1)
					frm.action = frm.action+'page='+pg;
				else
					frm.action = frm.action+'&page='+pg;
				return true;
			} else {
				alert(Lang.report.invalidPage);
				fld.value = '';
				fld.focus();
			}
		}
		return false;
	}
};

/**
 * The Widget class is the base class of all widgets.
 * @param {Object} attrs Widget's attributes
 * @param {Function} func Setup function
 * @constructor
 */
Widget = function(attrs, func) {
	/**
	 * Widget's attributes
	 * @type Object
	 */
	this.attributes = {};
	/**
	 * Widget's event listeners
	 * @type Object
	 */
	this.listeners = {};
	this.loadAttributes(attrs);
	if (Object.isFunc(func))
		func(this);
};

/**
 * @ignore
 */
Widget.widgets = [];

/**
 * Initializes a widget, given its name and attributes
 * @param {String} name Widget name
 * @param {Object} attrs Widget attributes
 */
Widget.init = function(name, attrs, setupFunc) {
	PHP2Go.include(PHP2Go.baseUrl + 'widgets/' + name.toLowerCase() + '.js');
	this.widgets.push([name, attrs, setupFunc]);
};

/**
 * Check if the widget has a given list of attributes
 * @type Boolean
 */
Widget.prototype.hasAttributes = function() {
	for (var i=0; i<arguments.length; i++) {
		if (Object.isUndef(this.attributes[arguments[i]]))
			return false;
	}
	return true;
};

/**
 * Loads the widget's attributes
 * @param {Object} attrs Widget attributes
 */
Widget.prototype.loadAttributes = function(attrs) {
	for (var prop in attrs) {
		this.attributes[prop] = attrs[prop];
	}
};

/**
 * Register a new event listener in the widget
 * @param {String} name Event name
 * @param {Function} func Handler function
 * @type void
 */
Widget.prototype.addEventListener = function(name, func) {
	this.listeners[name] = this.listeners[name] || [];
	this.listeners[name].push(func.bind(this));
};

/**
 * Raises an event in the widget. Call all
 * handlers bound to the event
 * @param {String} name Event name
 * @param {Array} args Event arguments
 * @type void
 */
Widget.prototype.raiseEvent = function(name, args) {
	var funcs = this.listeners[name] || [];
	for (var i=0; i<funcs.length; i++) {
		if (funcs[i](args) === false)
			return false;
	}
	return true;
};

/**
 * Finds one or more objects by their ids.
 * If one argument is passed, the function will return
 * an object or null if not found. If more than one argument
 * is passed, the function will return an array
 * @return Object or array of objects
 * @type Object
 */
$ = function() {
	var elm, d = document, a = arguments;
	// single argument
	if (a.length == 1) {
		elm = a[0];
		if (typeof(elm) == 'string')
			elm = (d.getElementById ? $E(d.getElementById(elm)) : (d.all ? $E(d.all[elm]) : null));
		else
			elm = $E(elm);
		return elm;
	}
	// multiple arguments
	for (var i=0, res=[], len=a.length; i<len; i++) {
		elm = a[i];
		if (typeof(elm) == 'string')
			elm = (d.getElementById ? $E(d.getElementById(elm)) : (d.all ? $E(d.all[elm]) : null));
		else
			elm = $E(elm);
		res.push(elm);
	}
	return res;
};

/**
 * Empty function
 * @type void
 */
$EF = function() {
};

/**
 * Identity function
 * @type Object
 */
$IF = function(x) {
	return x;
};

/**
 * This function returns an array object from a given
 * iterable element, or object that implements the
 * toArray method
 * @param {Object} o Iterable object
 * @type Hash
 */
$A = function(o) {
	if (o && o.constructor == Array)
		return o;
	return Array.valueOf(o);
};

/**
 * Convert an iterable object into an object
 * that can be iterated as a collection. The second
 * parameter indicates if the iterable must be handled
 * as an associative collection (Hash) or an array
 * @param {Object} o Iterable object
 * @param {Boolean} assoc Indicates if the iterable object is associative or not
 * @type Object
 */
$C = function(o, assoc) {
	assoc = !!assoc;
	if (o.walk && o.each)
		return o;
	else if (!o)
		o = (assoc ? {} : []);
	if (assoc) {
		o = {data:o};
		o.each = Hash.each;
	} else {
		o.each = Array.prototype.each;
	}
	Object.extend(o, Collection, false);
	return o;
};

/**
 * This function returns a hash object
 * from a given iterable element
 * @param {Object} obj Iterable object
 * @type Hash
 */
$H = function(obj) {
	return Hash.valueOf(obj);
};

/**
 * Convert an HTML element to access all methods
 * of Element class as its native methods. The
 * objects are converted only once. In browsers with
 * native HTMLElement support, this function will
 * directly return the passed object
 * @param {Object} elm Object to be converted
 * @type Object
 */
$E = function(elm) {
	if (!elm || !elm.tagName || elm.nodeType == 3 || elm._extended || elm == window || PHP2Go.nativeElementExtensions)
		return elm;
	Element.extend(elm);
	elm._extended = $EF;
	return elm;
};

/**
 * Utility function to create DOM element nodes. The
 * element can be initialized with a set of style
 * properties and initial HTML contents
 * @param {String} name Tag name
 * @param {Object} parent Parent node
 * @param {Object} style Style properties
 * @param {String} innerHTML Inner HTML
 * @param {Object} attrs Attributes
 * @type Object
 */
$N = function(name, parent, style, html, attrs) {
	var elm = $E(document.createElement(name.toLowerCase()));
	elm.setStyle(style);
	(parent) && (parent.appendChild(elm));
	(html) && (elm.innerHTML = html);
	(attrs) && (Object.extend(elm, attrs));
	return elm;
};

PHP2Go.load();