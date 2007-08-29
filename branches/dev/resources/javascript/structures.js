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
 * This file contain the data structure classes
 * Collection, Hash and the native Array prototype
 * extensions
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'structures.js']) {

var $break = {};
var $continue = {};

/**
 * @class Collection
 */
var Collection = {
	/**
	 * Applies a filter function on all elements
	 * of the collection. Returns true if at least
	 * one member satisfy the filter.
	 * @param {Function} filter Filter function
	 * @type Boolean
	 */
	some : function(filter) {
		var res = false;
		this.walk(function(item, idx) {
			if (res = !!filter(item, idx))
				throw $break;
		});
		return res;
	},
	/**
	 * Applies a filter function on all elements
	 * of the collection. Returns true if all
	 * member satisfy the filter.
	 * @param {Function} filter Filter function
	 * @type Boolean
	 */
	every : function(filter) {
		var res = true;
		this.walk(function(item, idx) {
			res = res && !!filter(item, idx);
			if (!res)
				throw $break;
		});
		return res;
	},
	/**
	 * Applies a filter function on all the
	 * elements of the collection. Returns back
	 * an array of elements that satisfy the filter
	 * @param {Function} filter Filter function
	 * @return Array of members that satisfy the filter
	 * @type Array
	 */
	accept : function(filter) {
		var res = [];
		this.walk(function(item, idx) {
			if (filter(item, idx))
				res.push(item);
		});
		return res;
	},
	/**
	 * Applies a filter function on the collection's
	 * elements, returning back an array of elements
	 * that DON'T satisfy the filter
	 * @param {Function} filter Filter function
	 * @return Array of members that don't satisfy the filter
	 * @type Array
	 */
	reject : function(filter) {
		var res = [];
		this.walk(function(item, idx) {
			if (!filter(item, idx))
				res.push(item);
		});
		return res;
	},
	/**
	 * Applies a filter function on the collection's
	 * element, pushing the not null return values to
	 * a resulting array
	 * @param {Function} filter Filter function
	 * @type Array
	 */
	valid : function(filter) {
		var res = [], v = null;
		this.walk(function(item, idx) {
			v = filter(item, idx);
			if (v != null)
				res.push(v);
		});
		return res;
	},
	/**
	 * Collect the collection members that satisfy a
	 * given regexp pattern. The return value of this
	 * method is an array. Works better with string
	 * collections
	 * @param {RegExp} pattern Regexp pattern
	 * @type Array
	 */
	grep : function(pattern) {
		var str, res = [];
		var re = new RegExp(pattern);
		this.walk(function(item, idx) {
			str = (item.toString ? item.toString() : String(item));
			if (str.match(pattern))
				res.push(item);
		});
		return res;
	},
	/**
	 * Verify if the collection contains a given object
	 * @param {Object} obj Object to be searched
	 * @type Boolean
	 */
	contains : function(obj) {
		if (Object.isFunc(this.indexOf))
			return (this.indexOf(obj) != -1);
		var found = false;
		this.walk(function(item, idx) {
			if (item === obj) {
				found = true;
				throw $break;
			}
		});
		return found;
	},
	/**
	 * Reads the value of the property 'property' in
	 * each element of the collection, and return the
	 * results in an Array object
	 * @param {String} property Property name
	 * @type Array
	 */
	extract : function(property) {
		var res = [];
		this.walk(function(item, idx) {
			if (!Object.isUndef(item[property]))
				res.push(item[property]);
		});
		return res;
	},
	/**
	 * Injects the results of an iterator over all
	 * collection elements. The iterator function is
	 * called with the passed memo as first parameter.
	 * Every iteration must return the new value of 'memo'
	 * @param {Object} memo Object to inject collection members
	 * @param {Function} iterator Iterator function
	 * @type Object
	 */
	inject : function(memo, iterator) {
		this.walk(function(item, idx) {
			memo = iterator(memo, item, idx);
		});
		return memo;
	},
	/**
	 * Call a given method of the collection items,
	 * using the provided arguments. This method considers
	 * that all the collection items are objects
	 * @param {String} method Method name
	 * @type void
	 */
	invoke : function(method) {
		var args = (arguments.length == 1 ? [] : $A(arguments).slice(1));
		return this.map(function(item, idx) {
			(item[method]) && (item[method].apply(item, args));
		});
	},
	/**
	 * Returns a new copy of the collection applying
	 * a given iterator to every element
	 * @param {Function} iterator Iterator function
	 * @param {Object} scope Iterator scope
	 * @type Array
	 */
	map : function(iterator, scope) {
		iterator = (iterator ? iterator.bind(scope) : $IF);
		var res = [];
		this.walk(function(item, idx) {
			res.push(iterator(item));
		});
		return res;
	},
	/**
	 * Applies an iterator function to all
	 * the members of the collection. The iterator
	 * receives the member and the current
	 * iteration index. In order to emulate the break
	 * functionality, you must throw an exception named $break.
	 * @param {Function} iterator Iterator function
	 * @type void
	 */
	walk : function(iterator) {
		if (Object.isFunc(iterator)) {
			var idx = 0;
			try {
				this.each(function(item) {
					iterator(item, idx++);
				});
			} catch(e) {
				if (e != $break)
					throw e;
			}
		}
	}
};

/**
 * The Hash class contains methods to handle
 * hash structures, which are collections of
 * key:value pairs
 * @class Hash
 * @base Collection
 */
var Hash = {
	/**
	 * Private method that iterates over
	 * the hash elements. The function elements
	 * are ignored. The iterator function
	 * receives an object with 2 properties:
	 * key and value
	 * @param {Function} iterator Iterator function
	 * @type void
	 * @private
	 */
	each : function(iterator) {
		for (key in this.data) {
			var value = this.data[key];
			iterator({key : key, value : value});
		}
	},
	/**
	 * Return an array containing the hash keys
	 * @type Array
	 */
	getKeys : function() {
		return this.extract('key');
	},
	/**
	 * Return an array containing the hash values
	 * @type Array
	 */
	getValues : function() {
		return this.extract('value');
	},
	/**
	 * Check if a given key exists in the hash
	 * @param {String} key Element key
	 * @type Boolean
	 */
	containsKey : function(key) {
		return (!!this.data[key]);
	},
	/**
	 * Set the value of a given key
	 * @param {String} key Key name
	 * @param {Object} value Key value
	 * @type void
	 */
	set : function(key, value) {
		this.data[key] = value;
	},
	/**
	 * Remove a given key from the hash
	 * @param {String} key Key name
	 * @type void
	 */
	unset : function(key) {
		delete this.data[key];
	},
	/**
	 * Searches for a given value and
	 * returns back its key
	 * @param {Object} value Search value
	 * @type Object
	 */
	findKey : function(value) {
		var key = null;
		this.walk(function(item) {
			if (item.value === value) {
				key = item.key;
				throw $break;
			}
		});
		return key;
	},
	/**
	 * Searches for a given key and
	 * returns back its value
	 * @param {String} key Search key
	 * @type Object
	 */
	findValue : function(key) {
		var value = null;
		this.walk(function(item) {
			if (item.key === key) {
				value = item.value;
				throw $break;
			}
		});
		return value;
	},
	/**
	 * Iterates over the hash elements
	 * assigning the key-value pairs to a
	 * target object
	 * @param {Object} target Target object
	 * @type void
	 */
	assign : function(target) {
		this.each(function(item) {
			target[item.key] = item.value;
		});
	},
	/**
	 * Convert the hash into a query string value. Keys
	 * and values are converted to be URL safe
	 * @type String
	 */
	toQueryString : function() {
		return this.map(function(pair) {
			return pair.key.urlEncode() + "=" + String(pair.value).urlEncode();
		}).join('&');
	},
	/**
	 * Dumps the hash elements as a string
	 * @type String
	 */
	serialize : function() {
		return '{' + this.map(function(pair) {
			return pair.key + " : " + Object.serialize(pair.value);
		}).join(', ') + '}';
	}
};

/**
 * Add Collection class methods
 */
Object.extend(Hash, Collection);

/**
 * Builds a Hash object based on an iterable value
 * @param {Object} iterable Iterable object
 * @type Hash
 */
Hash.valueOf = function(iterable) {
	var h = new Object();
	h.data = iterable || {};
	Object.extend(h, Hash);
	return h;
};

/**
 * Builds an Array object based on an iterable value.
 * All non iterable values will return an empty array.
 * Objects that implement the toArray method will use
 * that to build the function return.
 * @param {Object} iterable Iterable that must be used to build the array
 * @base Collection
 * @addon
 */
Array.valueOf = function(iterable) {
	if (!iterable)
		return [];
	if (iterable.toArray)
		return iterable.toArray();
	if (Object.isUndef(iterable.length))
		iterable = [iterable];
	var res = [];
	for (var i=0; i<iterable.length; i++)
		res.push(iterable[i]);
	return res;
};

if (!Array.prototype.push) {
	/**
	 * @ignore
	 */
	Array.prototype.push = function() {
		var a = arguments;
		for (var i=0; i<a.length; i++)
			this[this.length] = a[i];
		return this.length;
	};
}

if (!Array.prototype.pop) {
	/**
	 * @ignore
	 */
	Array.prototype.pop = function() {
		if (this.length > 0)
			return this[this.length-1];
	};
}

if (!Array.prototype.shift) {
	/**
	 * @ignore
	 */
	Array.prototype.shift = function() {
		var res = this[0];
		for (var i=0; i<this.length-1; i++)
			this[i] = this[i+1];
		this.length--;
		return res;
	};
}

if (!Array.prototype.unshift) {
	/**
	 * @ignore
	 */
	Array.prototype.unshift = function() {
		var a = arguments;
		for (var i=a.length; i<this.length; i++)
			this[i] = this[i-1];
		for (var i=0; i<a.length; i++)
			this[i] = a[i];
		this.length += len;
	};
}

if (PHP2Go.browser.opera) {
	/**
	 * Creates a new array containing the array's elements
	 * followed by all provided arguments. Overriden only
	 * on Opera browsers.
	 * @type Array
	 */
	Array.prototype.concat = function() {
		var res = [];
		for (var i=0, len=this.length; i<len; i++)
			res.push(this[i]);
		for (var i=0, len=arguments.length; i<len; i++) {
			if (Object.isArray(arguments[i])) {
				for (var j=0, alen=arguments[i].length; j<alen; j++)
					res.push(arguments[i][j]);
			} else {
				res.push(arguments[i]);
			}
		}
		return res;
	};
}

if (!Array.prototype.indexOf) {
	/**
	 * Searches for a given object inside the array.
	 * Returns the index of the first occurrence of
	 * the object inside array, or -1 if not found.
	 * @param {Object} obj Object to be searched
	 * @param {Number} i Start index
	 * @type Number
	 */
	Array.prototype.indexOf = function(obj, i) {
		i || (i = 0);
		var self = this;
		if (i < 0)
			i = self.length + i;
		for (; i<self.length; i++)
			if (self[i] === obj) return i;
		return -1;
	};
}

if (!Array.prototype.lastIndexOf) {
	/**
	 * Returns the index of the last occurrence of
	 * an object inside the array, or -1 if not found
	 * @param {Object} obj Object to be searched
	 * @param {Number} i Start index
	 * @type Number
	 */
	Array.prototype.lastIndexOf = function(obj, i) {
		i = (isNaN(i) ? this.length : (i < 0 ? this.length + i : i) + 1);
		var p = this.slice(0, i).reverse().indexOf(obj);
		return (p < 0 ? p : i - p - 1);
	};
}

if (!Array.prototype.forEach) {
	/**
	 * Private method that iterates over the
	 * elements of the array. The walk method
	 * of the Collection class should be used
	 * instead of this
	 * @param {Function} Iterator function
	 * @private
	 */
	Array.prototype.each = function(iterator) {
		for (var i=0, len=this.length; i<len; i++)
			iterator(this[i]);
	};
} else {
	/**
	 * @ignore
	 */
	Array.prototype.each = Array.prototype.forEach;
}

/**
 * Returns the first element of the array
 */
Array.prototype.first = function() {
	return this[0];
};

/**
 * Returns the first element of the array
 */
Array.prototype.last = function() {
	return this[this.length-1];
};

/**
 * Removes an element of the array given its index.
 * Indexes which are invalid or out of bounds will be ignored
 * @param {Number} idx Element index to remove
 * @type void
 */
Array.prototype.remove = function(idx) {
	idx = parseInt(idx, 10);
	if (!isNaN(idx) && idx >= 0 && idx < this.length) {
		for (var i=idx+1; i<this.length; i++)
			this[i-1] = this[i];
		this.length--;
	}
};

/**
 * Verifies if the array is empty
 * @type Boolean
 */
Array.prototype.empty = function() {
	return (this.length == 0);
};

/**
 * Removes all elements in the array
 * and return the new empty array
 * @type Array
 */
Array.prototype.clear = function() {
	this.length = 0;
	return this;
};

/**
 * Creates a clone of the array
 * @type Array
 */
Array.prototype.clone = function() {
	return [].concat(this);
};

/**
 * Dumps the array contents as string
 * @type String
 */
Array.prototype.serialize = function() {
	return '[' + this.map(Object.serialize).join(', ') + ']';
};

/**
 * Add Collection class methods
 */
Object.extend(Array.prototype, Collection, false);
if (Array.prototype.filter)
	Array.prototype.accept = Array.prototype.filter;

PHP2Go.included[PHP2Go.baseUrl + 'structures.js'] = true;

}