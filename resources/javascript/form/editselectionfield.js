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
// $Header: /www/cvsroot/php2go/resources/javascript/form/editselectionfield.js,v 1.6 2006/11/19 17:59:14 mpont Exp $
// $Date: 2006/11/19 17:59:14 $
// $Revision: 1.6 $

/**
 * @fileoverview
 * This file contains the EditSelectionField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/editselectionfield.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'util/stringlist.js');

/**
 * This class contains the client code of the EditSelectionField
 * component. The component consists in a text input, a select
 * input and 3 action buttons. The select is populated by the
 * values entered in the text input
 * @constructor
 * @base ComponentField
 * @requires StringList
 * @param {String} id Component ID
 * @param {String} edit Text input ID
 * @param {String} lookup Select input ID
 * @param {String} added Added values field ID
 * @param {String} removed Removed values field ID
 * @param {String} sep Separator to the control lists
 */
EditSelectionField = function(id, edit, lookup, added, removed, sep) {
	this.ComponentField($(id), 'EditSelectionField');
	/**
	 * Top element in the component output structure
	 * @type Object
	 */
	this.top = $(this.id + '_top');
	/**
	 * Text iput used to add values in the list
	 * @type Object
	 */
	this.edit = $(edit);
	/**
	 * Select element that holds the inserted items
	 * @type Object
	 */
	this.lookup = $(lookup);
	/**
	 * Control field that holds the added list items
	 * @type Object
	 */
	this.added = $(added);
	/**
	 * Control field that holds the removed list items
	 * @type Object
	 */
	this.removed = $(removed);
	/**
	 * Label that shows the current list size
	 * @type Object
	 */
	this.count = $(this.id + '_cnt');
	/**
	 * Action buttons
	 * @type Array
	 */
	this.buttons = $(this.id + '_add', this.id + '_rem', this.id + '_remall');
	/**
	 * Separator to the control lists
	 * @type String
	 */
	this.listSep = sep;
	/**
	 * Indicates if the list contents
	 * is different from its original state
	 * @type Boolean
	 */
	this.changed = false;
	this.setup();
	this.updateButtons();
};
EditSelectionField.extend(ComponentField, 'ComponentField');

/**
 * Execute all the initialization steps
 * of this form component
 * @type void
 */
EditSelectionField.prototype.setup = function() {
	var self = this;
	this.fld.component = this;
	this.edit.auxiliary = true;
	this.lookup.auxiliary = true;
	this.added.auxiliary = true;
	this.added.value = '';
	this.removed.auxiliary = true;
	this.removed.value = '';
	this.count.innerHTML = this.lookup.options.length-1;
	this.addedList = new StringList('', this.listSep);
	this.addedList.onUpdate = function(str) {
		self.added.value = str;
	};
	this.removedList = new StringList('', this.listSep);
	this.removedList.onUpdate = function(str) {
		self.removed.value = str;
	};
	this.preList = new StringList('', this.listSep);
	this.preList.importOptions(this.lookup, 1);
	this.originalList = [];
	var o = this.lookup.options;
	for (var i=1; i<o.length; i++)
		this.originalList.push([o[i].value, o[i].text]);
	Event.addListener(this.edit.form, 'reset', function(evt) {
		self.clear();
	});	
};

/**
 * Checks if at least one option was added
 * in the component's select element
 * @type Boolean
 */
EditSelectionField.prototype.isEmpty = function() {
	return (this.lookup.options.length < 2);
};

/**
 * Reset the component to its default state
 * @type void
 */
EditSelectionField.prototype.clear = function() {
	this.edit.value = '';
	// rebuild original list if the original list was changed
	if (this.changed) {
		var o = this.lookup.options;
		this.originalList.walk(function(item, idx) {
			o[idx+1] = new Option(item[1], item[0]);
		});
		o.length = this.originalList.length + 1;
		this.count.innerHTML = o.length-1;
	}
	this.addedList.clear();
	this.removedList.clear();
	this.updateButtons();
};

/**
 * Enable the component
 * @type void
 */
EditSelectionField.prototype.enable = function() {
	this.setDisabled(false);
	this.updateButtons();
};

/**
 * Disable the component
 * @type void
 */
EditSelectionField.prototype.disable = function() {
	this.setDisabled(true);
};

/**
 * Internal method to disable/enable the component.
 * Is called from {@link EditSelectionField#enable}
 * and {@link EditSelectionField#disable}
 * @param {Boolean} b Flag value
 * @type void
 * @private
 */
EditSelectionField.prototype.setDisabled = function(b) {
	this.edit.disabled = b;
	this.lookup.disabled = b;
	this.buttons.walk(function(item, idx) {
		item.disabled = b;
	});
};

/**
 * Focus the first component's element
 * @type void
 */
EditSelectionField.prototype.focus = function() {
	if (!this.edit.disabled) {
		this.edit.focus();
		return true;
	}
	return false;
};

/**
 * Serialize the component's value. The
 * EditSelectionField is serialized in the form
 * of a 2-dimension array: the added values list
 * and the removed values list
 * @type String
 */
EditSelectionField.prototype.serialize = function() {
	var self = this, name = this.name, val = [];
	if (!this.addedList.empty())
		val.push(this.addedList.toArray().map(function(item) {
			return name + '[' + self.added.name + '][]=' + item.urlEncode();
		}).join('&'));
	if (!this.removedList.empty())
		val.push(this.removedList.toArray().map(function(item) {
			return name + '[' + self.removed.name + '][]=' + item.urlEncode();
		}).join('&'));
	return (val.empty() ? null : val.join('&'));
};

/**
 * Create a new list item based on the text input value.
 * If the value was already inserted, an alert message
 * is displayed
 * @param {Object} opts Add options
 * @type void
 */
EditSelectionField.prototype.add = function(opts) {
	if (this.edit.value.trim() != '') {
		var idx, pos, v = this.edit.value, o = this.lookup.options, a, r;
		(opts.upper) && (v = v.toUpperCase());
		(opts.lower) && (v = v.toLowerCase());
		(opts.trim) && (v = v.trim());
		(opts.capitalize) && (v = v.capitalize());
		// add the item if it was removed or if it's not in the original list and was not added
		a = (!this.preList.contains(v) && !this.addedList.contains(v));
		r = this.removedList.contains(v);
		if (a || r) {
			try {
				this.raiseEvent('add', [v]);
				o[o.length] = new Option(v, v);
				(r ? this.removedList.remove(v) : this.addedList.add(v));
				this.count.innerHTML = o.length-1;
				this.changed = true;
				this.updateButtons();
			} catch(e) {
				alert(e.message);
				this.edit.select();
			}
		} else {
			alert(Lang.duplicatedValue);
			this.edit.select();
		}
	} else {
		this.edit.focus();
	}
};

/**
 * Remove all selected options in the list
 * @type void
 */
EditSelectionField.prototype.remove = function() {
	var self = this;
	var lookupField = $F(this.lookup);
	lookupField.removeSelectedOptions(function(o) {
		try {
			self.raiseEvent('remove', [o]);
			(!self.changed) && (self.changed = true);
			if (self.addedList.contains(o.value))
				self.addedList.remove(o.value);
			else
				self.removedList.add(o.value);
		} catch(e) {
			alert(e.message);
			throw $break;
		}
	}, 1);
	this.count.innerHTML = this.lookup.options.length - 1;
	this.updateButtons();
};

/**
 * Remove all options of the list
 * @type void
 */
EditSelectionField.prototype.removeAll = function() {
	var self = this, cnt = 0;
	this.addedList.observing = this.removedList.observing = false;
	$C(this.lookup.options).walk(function(o, idx) {
		try {
			if (idx > 0) {
				self.raiseEvent('remove', [o]);
				if (self.addedList.contains(o.value))
					self.addedList.remove(o.value);
				else
					self.removedList.add(o.value);
				cnt++;
			}
		} catch(e) {
			alert(e.message);
			throw $break;
		}
	});
	this.addedList.observing = this.removedList.observing = true;
	if (cnt) {	
		this.lookup.options.length = (this.lookup.options.length-cnt);
		this.count.innerHTML = (this.lookup.options.length-1);
		this.changed = true;
		this.added.value = this.addedList.toString();
		this.removed.value = this.removedList.toString();
		this.updateButtons();
	}
};

/**
 * Update remove and removeAll buttons status based
 * on the existance of elements in the target list
 * @type void
 */
EditSelectionField.prototype.updateButtons = function() {
	var o = this.lookup.options;
	this.buttons[1].disabled = (o.length < 2);
	this.buttons[2].disabled = (o.length < 2);
};

PHP2Go.included[PHP2Go.baseUrl + 'form/editselectionfield.js'] = true;

}