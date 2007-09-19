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
	 * @param {Object} scope Scope object
	 * @type Boolean
	 */
	some : function(filter, scope) {
		var res = false;
		this.walk(function(item, idx) {
			if (res = !!filter.apply(scope || null, [item, idx]))
				throw $break;
		});
		return res;
	},
	/**
	 * Applies a filter function on all elements
	 * of the collection. Returns true if all
	 * member satisfy the filter.
	 * @param {Function} filter Filter function
	 * @param {Object} scope Scope object
	 * @type Boolean
	 */
	every : function(filter, scope) {
		var res = true;
		this.walk(function(item, idx) {
			if (!filter.apply(scope || null, [item, idx])) {
				res = false;
				throw $break;
			}
		});
		return res;
	},
	/**
	 * Applies a filter function on all the
	 * elements of the collection. Returns back
	 * an array of elements that satisfy the filter
	 * @param {Function} filter Filter function
	 * @param {Object} scope Scope object
	 * @return Array of members that satisfy the filter
	 * @type Array
	 */
	accept : function(filter, scope) {
		var res = [];
		this.walk(function(item, idx) {
			if (filter.apply(scope || null, [item, idx]))
				res.push(item);
		});
		return res;
	},
	/**
	 * Applies a filter function on the collection's
	 * elements, returning back an array of elements
	 * that DON'T satisfy the filter
	 * @param {Function} filter Filter function
	 * @param {Object} scope Scope object
	 * @return Array of members that don't satisfy the filter
	 * @type Array
	 */
	reject : function(filter, scope) {
		var res = [];
		this.walk(function(item, idx) {
			if (!filter.apply(scope || null, [item, idx]))
				res.push(item);
		});
		return res;
	},
	/**
	 * Applies a filter function on the collection's
	 * element, pushing the not null return values to
	 * a resulting array
	 * @param {Function} filter Filter function
	 * @param {Object} scope Scope object
	 * @type Array
	 */
	valid : function(filter, scope) {
		var res = [], v = null;
		this.walk(function(item, idx) {
			v = filter.apply(scope || null, [item, idx]);
			if (v != null)
				res.push(v);
		});
		return res;
	},
	/**
	 * Collect the collection members that satisfy a
	 * given regexp pattern. The return value of this
	 * method is an array. Works better on string
	 * collections
	 * @param {RegExp} pattern Regexp pattern
	 * @type Array
	 */
	grep : function(pattern) {
		var str, res = [];
		var re = (Object.isString(pattern) ? new RegExp(pattern) : pattern);
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
	 * @param {Object} scope Scope object
	 * @type Object
	 */
	inject : function(memo, iterator, scope) {
		this.walk(function(item, idx) {
			memo = iterator.apply(scope || null, [memo, item, idx]);
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
		var args = Array.prototype.slice.call(arguments, 1);
		return this.map(function(item, idx) {
			(item[method]) && (item[method].apply(item, args));
		});
	},
	/**
	 * Returns a new copy of the collection applying
	 * a given iterator to every element
	 * @param {Function} iterator Iterator function
	 * @param {Object} scope Scope object
	 * @type Array
	 */
	map : function(iterator, scope) {
		var res = [];
		this.walk(function(item, idx) {
			res.push(iterator.apply(scope || null, [item, idx]));
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
	},
	/**
	 * Builds a Hash object based on an iterable value
	 * @param {Object} iterable Iterable object
	 * @type Hash
	 */
	valueOf : function(iterable) {
		var h = new Object();
		h.data = iterable || {};
		Object.extend(h, Hash);
		return h;
	}
};
Object.extend(Hash, Collection);

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
	 * @param {Number} idx From index
	 * @type Number
	 */
	Array.prototype.indexOf = function(obj, idx) {
		var len = this.length;
		var idx = (idx < 0 ? idx + len : idx || 0);
		for (; idx < len; idx++) {
			if (this[idx] === obj)
				return idx;
		}
		return -1;
	};
}

if (!Array.prototype.lastIndexOf) {
	/**
	 * Returns the index of the last occurrence of
	 * an object inside the array, or -1 if not found
	 * @param {Object} obj Object to be searched
	 * @param {Number} idx From index
	 * @type Number
	 */
	Array.prototype.lastIndexOf = function(obj, idx) {
		var len = this.length;
		var idx = (isNaN(idx) ? len : (idx < 0 ? idx + len : (idx >= len ? len - 1 : idx)));
		for (; idx > -1; idx--)
			if (this[idx] === obj)
				return idx;
		return -1;
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
 * @type Object
 */
Array.prototype.first = function() {
	return this[0];
};

/**
 * Returns the first element of the array
 * @type Object
 */
Array.prototype.last = function() {
	return this[this.length-1];
};

/**
 * Includes the passed element in the array, only if not already present
 * @param {Object} item Item to add
 * @type Array
 */
Array.prototype.include = function(item) {
	if (this.indexOf(item) == -1)
		this.push(item);
	return this;
};

/**
 * Removes all occurrences of an item from the array
 * @param {Object} item Array item
 * @type Array
 */
Array.prototype.remove = function(item) {
	var i = 0, len = this.length;
	while (i < len) {
		if (this[i] === item) {
			this.splice(i, 1);
			len--;
		} else {
			i++;
		}
	}
	return this;
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

Array.implement(Collection);
if (Array.prototype.filter)
	Array.prototype.accept = Array.prototype.filter;

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

PHP2Go.included[PHP2Go.baseUrl + 'structures.js'] = true;

}