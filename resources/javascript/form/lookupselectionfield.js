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
 * This file contains the LookupSelectionField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/lookupselectionfield.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'util/stringlist.js');

/**
 * The LookupSelectionField component is composed by two select elements
 * that can exchange options. The source list options can be copied
 * to the target list, which represents the list of selected items.
 * The action buttons allow to add options, add all options from left
 * to right, remove already inserted options and remove all inserted options
 * @constructor
 * @base ComponentField
 * @requires StringList
 * @param {String} id Component ID
 * @param {String} available Source list ID
 * @param {String} selected Selected list ID
 * @param {String} added Added values field ID
 * @param {String} removed Removed values field ID
 * @param {String} sep Separator to the control lists
 */
LookupSelectionField = function(id, available, selected, added, removed, sep) {
	this.ComponentField($(id), 'LookupSelectionField');
	/**
	 * Top element in the component output structure
	 * @type Object
	 */
	this.top = $(this.id + '_top');
	/**
	 * Select element that holds the available items
	 * @type Object
	 */
	this.available = $(available);
	/**
	 * Select element that holds the selected items
	 * @type Object
	 */
	this.selected = $(selected);
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
	 * Label that shows the current count of selected items
	 * @type Object
	 */
	this.count = $(this.id + '_cnt');
	/**
	 * Action buttons
	 * @type Array
	 */
	this.buttons = $(this.id + '_add', this.id + '_addall', this.id + '_rem', this.id + '_remall');
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
LookupSelectionField.extend(ComponentField, 'ComponentField');

/**
 * Execute all the initialization steps
 * of this form component
 * @type void
 */
LookupSelectionField.prototype.setup = function() {
	var self = this;
	this.fld.component = this;
	this.available.auxiliary = true;
	this.selected.auxiliary = true;
	this.added.auxiliary = true;
	this.added.value = '';
	this.removed.auxiliary = true;
	this.removed.value = '';
	this.count.innerHTML = this.selected.options.length-1;
	this.addedList = new StringList('', this.listSep);
	this.addedList.onUpdate = function(str) {
		self.added.value = str;
	};
	this.removedList = new StringList('', this.listSep);
	this.removedList.onUpdate = function(str) {
		self.removed.value = str;
	};
	this.preList = new StringList('', this.listSep);
	this.preList.importOptions(this.selected, 1);
	this.originalList = [];
	var o = this.selected.options;
	for (i=1; i<o.length; i++)
		this.originalList.push([o[i].value, o[i].text]);
	Event.addListener(this.available.form, 'reset', function(evt) {
		self.clear();
	});
};

/**
 * Checks if the list of selected items contains at least one option
 * @type Boolean
 */
LookupSelectionField.prototype.isEmpty = function() {
	return (this.selected.options.length < 2);
};

/**
 * Reset the component to its default state
 * @type void
 */
LookupSelectionField.prototype.clear = function() {
	// rebuild original list if the original list was changed
	if (this.changed) {
		var o = this.selected.options;
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
 * Disables/enables the component
 * @param {Boolean} b Flag value
 * @type void
 */
LookupSelectionField.prototype.setDisabled = function(b) {
	this.available.disabled = b;
	this.selected.disabled = b;
	this.buttons.walk(function(item, idx) {
		item.disabled = b;
	});
};

/**
 * Move focus to the component's first member
 * @type void
 */
LookupSelectionField.prototype.focus = function() {
	if (this.beforeFocus() && !this.available.disabled) {
		this.available.focus();
		return true;
	}
	return false;
};

/**
 * Serialize the component's value. The
 * LookupSelectionField is serialized in the form
 * of a 2-dimension array: the added values list
 * and the removed values list
 * @type String
 */
LookupSelectionField.prototype.serialize = function() {
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
 * Private method that adds a single options in the selected items list
 * @param {Object} opt Option to be added
 * @type void
 * @private
 */
LookupSelectionField.prototype.addSingle = function(opt) {
	var o = this.selected.options;
	// add the item if it was removed or if it's not in the original list and was not added
	var a = (!this.preList.contains(opt.value) && !this.addedList.contains(opt.value));
	var r = this.removedList.contains(opt.value);
	if (a || r) {
		o[o.length] = new Option(opt.text, opt.value);
		(r ? this.removedList.remove(opt.value) : this.addedList.add(opt.value));
		this.count.innerHTML = o.length-1;
		this.changed = true;
		opt.selected = false;
	}
};

/**
 * Fetch all selected options from the source list and
 * add them in the selected items list. Options successfully
 * added will be unselected; Duplicated items will remain
 * selected
 * @type void
 */
LookupSelectionField.prototype.add = function() {
	var self = this, s = this.available.options, t = this.selected.options, a, r;
	$C(s).walk(function(o, idx) {
		if (o.selected) {
			try {
				self.raiseEvent('add', [o]);
			} catch(e) {
				alert(e.message);
				throw $break;
			}
			self.addSingle(o);
		}
	});
	this.updateButtons();
};

/**
 * Copy all options from the source list to the target list.
 * Duplicated items will be ignored
 * @type void
 */
LookupSelectionField.prototype.addAll = function() {
	var self = this, s = this.available.options, t = this.selected.options, a, r;
	$C(s).walk(function(o, idx) {
		try {
			self.raiseEvent('add', [o]);
		} catch(e) {
			alert(e.message);
			throw $break;
		}
		self.addSingle(o);
	});
	this.updateButtons();
};

/**
 * Remove all selected options from the selected items list
 * @type void
 */
LookupSelectionField.prototype.remove = function() {
	var self = this;
	var selectedField = $F(this.selected);
	selectedField.removeSelectedOptions(function(o) {
		try {
			self.raiseEvent('remove', [o]);
		} catch(e) {
			alert(e.message);
			throw $break;
		}
		(!self.changed) && (self.changed = true);
		if (self.addedList.contains(o.value))
			self.addedList.remove(o.value);
		else
			self.removedList.add(o.value);
	}, 1);
	this.count.innerHTML = this.selected.options.length - 1;
	this.updateButtons();
};

/**
 * Remove all options of the selected items list
 * @type void
 */
LookupSelectionField.prototype.removeAll = function() {
	var self = this, cnt = 0;
	this.addedList.observing = this.removedList.observing = false;
	$C(this.selected.options).walk(function(o, idx) {
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
		this.selected.options.length = (this.selected.options.length-cnt);
		this.count.innerHTML = (this.selected.options.length-1);
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
LookupSelectionField.prototype.updateButtons = function() {
	var o = this.selected.options;
	this.buttons[2].disabled = (o.length < 2);
	this.buttons[3].disabled = (o.length < 2);
};

PHP2Go.included[PHP2Go.baseUrl + 'form/lookupselectionfield.js'] = true;

}
