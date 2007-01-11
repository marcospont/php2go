/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * @fileoverview
 * This file contains the StringList class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'util/stringlist.js']) {

/**
 * A StringList handles strings that are lists of elements splitted
 * by a separator substring. Ex: "2#3#4#5". With this class, it's
 * possible to query, add or remove list elements, besides converting
 * the list into an array and getting the most updated version of the
 * list using the toString method
 * @param {String} str Base string. Defaults to an empty string
 * @param {String} sep String separator. Defaults to "#"
 * @constructor
 */
StringList = function(str, sep) {
	/**
	 * Holds the base string
	 * @type String
	 * @private
	 */
	this.str = String(str || '');
	/**
	 * Holds the separator used to split the list members
	 * @type String
	 * @private
	 */
	this.sep = sep || '#';
	/**
	 * Set this flag to false to disable onUpdate
	 * calls when the string list changes
	 * @type Boolean
	 */
	this.observing = true;
	/**
	 * Searches for a given element in the list
	 * @param {String} obj Search substring
	 * @type Boolean
	 */
	this.contains = function(obj) {
		var tmp = this.sep + this.str + this.sep;
		return tmp.find(this.sep + obj + this.sep);
	};
	/**
	 * Check if the list is empty
	 * @type Boolean
	 */
	this.empty  = function() {
		return (this.str == '');
	};
	/**
	 * Clear the list
	 * @type void
	 */
	this.clear = function() {
		this.update('');
	};
	/**
	 * Adds a new element onto the list
	 * @param {String} obj New element
	 * @return Returns true if the element could be added, false otherwise
	 * @type Boolean
	 */
	this.add = function(obj) {
		if (!this.contains(obj)) {
			this.update(this.str == "" ? obj : this.str + this.sep + obj);
			return true;
		}
		return false;
	};
	/**
	 * Populate the string list using
	 * the options of a SELECT element
	 * @param {Object} sel SELECT element
	 * @type void
	 */
	this.importOptions = function(sel, idx) {
		idx = PHP2Go.ifUndef(idx, 0);
		if (sel.options) {
			for (i=idx; i<sel.options.length; i++) {
				this.add(sel.options[i].value);
			}
		}
	};
	/**
	 * Removes an element from the list
	 * @param {String} obj Element to be removed
	 * @return Returns true if the element could be removed, false otherwise
	 * @type Boolean
	 */
	this.remove = function(obj) {
		var tmp = this.sep + this.str + this.sep;
		var pos = tmp.indexOf(this.sep + obj + this.sep);
		if (pos != -1) {
			if (pos == 0)
				this.update(this.str.substr(obj.length+this.sep.length));
			else
				this.update(this.str.substr(0, pos-this.sep.length) + this.str.substr(pos+obj.length));
			return true;
		}
		return false;
	};
	/**
	 * Private function used to change the value of the string list
	 * @param {String} str New value
	 * @type void
	 * @private
	 */
	this.update = function(str) {
		this.str = str;
		this.observing && this.onUpdate(str);
	};
	/**
	 * Handle modifications in the string list. This method
	 * is abstract and must be implemented.
	 * @param {String} str String list contents
	 * @type void
	 */
	this.onUpdate = function(str) {};
	/**
	 * Converts the list into an Array instance
	 * @type Array
	 */
	this.toArray = function() {
		return (this.str != '' ? this.str.split(this.sep) : []);
	};
	/**
	 * Gets the most updated version of the list
	 * @type String
	 */
	this.toString = function() {
		return this.str;
	};
	/**
	 * Serialize the list
	 * @type String
	 */
	this.serialize = function() {
		return this.str.serialize();
	};
};

PHP2Go.included[PHP2Go.baseUrl + 'util/stringlist.js'] = true;

}