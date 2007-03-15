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
 * This file contain the LookupChoiceField component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/lookupchoice.js']) {

/**
 * The LookupChoiceField class is a component composed by
 * a select input and a text input, which can be used to
 * filter the select options
 * @constructor
 * @base ComponentField
 * @param {String} id Select input ID
 */
LookupChoiceField = function(id) {
	this.ComponentField($(id), 'LookupChoiceField');
	/**
	 * Filter input field
	 * @type Object
	 */
	this.filter = $(id + '_filter');
	/**
	 * Holds the original options list
	 * @type String
	 */
	this.stringOptions = '';
	/**
	 * Used to update and rebuild the options list
	 * @type Array
	 */
	this.options = [];
	/**
	 * @ignore
	 */
	this.lastFilter = null;
	this.setup();
};
LookupChoiceField.extend(ComponentField, 'ComponentField');

/**
 * Initializes the component's properties and event listeners
 * @type void
 */
LookupChoiceField.prototype.setup = function() {
	var self = this;
	this.fld.component = this;
	this.filter.auxiliary = true;
	// equalize widths
	var wid = this.fld.getStyle('width');
	this.filter.style.width = wid;
	this.filter.originalValue = this.filter.value;
	// filter field event listeners
	Event.addListener(this.filter, 'focus', function() {
		if (self.filter.value == self.filter.originalValue)
			self.filter.value = '';
	});
	Event.addListener(this.filter, 'keyup', function() {
		if (self.filter.value != self.lastFilter) {
			self.updateList();
			self.lastFilter = self.filter.value;
		}
	});
	Event.addListener(this.filter, 'paste', function() {
		self.updateList();
	});
	Event.addListener(this.filter, 'blur', function() {
		if (self.filter.value.trim() == '')
			self.filter.value = self.filter.originalValue;
	});
	// initialize the internal options list
	this.initializeList();
};

/**
 * Returns the selected option's value
 * @type Object
 */
LookupChoiceField.prototype.getValue = function() {
	var idx = this.fld.selectedIndex;
	if (idx >= 0 && this.fld.options[idx].value != "")
		return this.fld.options[idx].value;
	return null;
};

/**
 * Define the list selected option
 * @param {String} val Option to be selected
 * @type void
 */
LookupChoiceField.prototype.setValue = function(val) {
	var self = this, opt = $C(this.fld.options);
	opt.walk(function(item, idx) {
		if (item.value == val) {
			self.fld.value = val;
			item.selected = true;
			throw $break;
		}
	});
};

/**
 * Unselect all selected options in the list
 * @type void
 */
LookupChoiceField.prototype.clear = function() {
	this.fld.value = '';
	$C(this.fld.options).walk(function(el, idx) {
		el.selected = false;
	});
};

/**
 * Enables the component
 * @type void
 */
LookupChoiceField.prototype.enable = function() {
	this.filter.disabled = false;
	this.fld.disabled = false;
};

/**
 * Disables the component
 * @type void
 */
LookupChoiceField.prototype.disable = function() {
	this.filter.disabled = true;
	this.fld.disabled = true;
};

/**
 * Read all list options to an internal class member
 * @type void
 */
LookupChoiceField.prototype.initializeList = function() {
	var self = this;
	if (this.stringOptions == '') {
		self.options = [];
		$C(this.fld.options).walk(function(item, idx) {
			self.options.push(item);
			if (self.stringOptions != '')
				self.stringOptions += '|';
			self.stringOptions += item.value + '~' + item.text;
		});
	} else {
		self.options = [];
		this.stringOptions.split('|').walk(function(item, idx) {
			var option = item.split('~');
			self.options.push(new Option(option[1], option[0]));
		});
	}
};

/**
 * Rebuilds the list using its original options
 * @type void
 */
LookupChoiceField.prototype.rebuildList = function() {
	this.initializeList();
	for (var i=0; i<this.options.length; i++) {
		this.fld.options[i] = this.options[i];
		this.fld.options[i].selected = false;
	}
	this.fld.options.length = this.options.length;
};

/**
 * Updates the list contents based on the current filter value.
 * If the filter is empty, the original list will be restored
 * @type void
 */
LookupChoiceField.prototype.updateList = function() {
	var filter = this.filter.value.replace(/^\s*/, '');
	if (filter == '') {
		this.rebuildList();
	} else {
		var j = 0, opt = this.fld.options;
		var pattern = new RegExp("^" + filter, "i");
		this.initializeList();
		this.options.walk(function(item, idx) {
			if (item.value != "" && pattern.test(item.text)) {
				opt[j] = item;
				opt[j].selected = false;
				opt.length = ++j;
			}
		});
		if (j == 1) {
			opt[0].selected = true;
			this.fld.selectedIndex = 0;
		}
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'form/lookupchoice.js'] = true;

}
