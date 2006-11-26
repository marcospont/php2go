//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/resources/javascript/structures.js,v 1.6 2006/10/11 22:28:38 mpont Exp $
// $Date: 2006/10/11 22:28:38 $
// $Revision: 1.6 $

/**
 * @fileoverview
 * This file contain the data structure classes
 * Collection, Hash and the native Array prototype
 * extensions
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'structures.js']) {

var $break = new Object();
var $continue = new Object();

/**
 * @class Collection
 */
var Collection = {
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
			if (filter(item))
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
			if (!filter(item))
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
	filter : function(filter) {
		var res = [], v = null;
		this.walk(function(item, idx) {
			v = filter(item);
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
		var found = false;
		this.walk(function(item, idx) {
			if (item == obj) {
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
			if (typeof(item[property]) != 'undefined')
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
	 * @type Array
	 */
	map : function(iterator) {
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
	 * iteration index. In order to emulate break and
	 * continue funcionalities, you must throw
	 * the exceptions named $break and $continue.
	 * @param {Function} iterator Iterator function
	 * @type void
	 */
	walk : function(iterator) {
		if (typeof(iterator) == 'function') {
			var idx = 0;
			try {
				this.each(function(item) {
					try {
						iterator(item, idx++);
					} catch(e) {
						if (e != $continue)
							throw e;
					}
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
		return (this.data[key]);
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
	 * Searches for a given key and returns
	 * back its value
	 * @param {String} key Search key
	 * @type Object
	 */
	findValue : function(key) {
		var value = null;
		this.walk(function(item) {
			if (item.key == key) {
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
Object.implement(Hash, Collection);

/**
 * Builds a Hash object based on an iterable value
 * @param {Object} iterable Iterable object
 * @type Hash
 */
Hash.valueOf = function(iterable) {
	var h = new Object();
	h.data = iterable || {};
	Object.implement(h, Hash);
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
	if (typeof(iterable.length) == 'undefined')
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

/**
 * Private method that iterates over the
 * elements of the array. The walk method
 * of the Collection class should be used
 * instead of this
 * @param {Function} Iterator function
 * @private
 */
Array.prototype.each = function(iterator) {
	for (var i=0; i<this.length; i++)
		iterator(this[i]);
};

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
 * Verifies if the array is empty
 * @type Boolean
 */
Array.prototype.empty = function() {
	return (this.length == 0);
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
 * Removes all elements in the array
 * and return the new empty array
 * @type Array
 */
Array.prototype.clear = function() {
	this.length = 0;
	return this;
};

/**
 * Searches for a given object inside the array.
 * Returns the index of the first occurrence of
 * the object inside array, or -1 if the object
 * was not found
 * @param {Object} obj Object to be searched
 * @type Number
 */
Array.prototype.indexOf = function(obj) {
	var self = this;
	for (var i=0; i<self.length; i++)
		if (self[i] == obj) return i;
	return -1;
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
Object.implement(Array.prototype, Collection);

PHP2Go.included[PHP2Go.baseUrl + 'structures.js'] = true;

}