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
 * This file contains the MemoField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/memofield.js']) {

/**
 * The MemoField form component is used in form components
 * built using the "memofield" tag in the XML specification
 * @class MemoField
 * @base ComponentField
 * @param {Object} id Textarea field ID
 * @param {Number} maxlength Max length
 */
MemoField = function(id, maxlength) {
	this.ComponentField($(id), 'MemoField');
	/**
	 * Control field used to display characters left
	 * @type Object
	 */
	this.count = $(this.id + "_count");
	/**
	 * Max chars allowed
	 * @type Number
	 */
	this.maxlength = maxlength;
	this.setup();
};
MemoField.extend(ComponentField, 'ComponentField');

/**
 * Initializes the component's properties and event handlers
 * @type void
 */
MemoField.prototype.setup = function() {
	this.fld.component = this;
	this.count.auxiliary = true;
	Event.addListener(this.fld, 'keydown', this.keyHandler.bind(this));
	Event.addListener(this.fld, 'keyup', this.keyHandler.bind(this));
};

/**
 * Sets the value of the field
 * @param {Object} val New value
 * @type void
 */
MemoField.prototype.setValue = function(val) {
	val = val || '';
	this.fld.value = val.substring(0, this.maxlength);
	this.count.value = this.maxlength - this.fld.value.length;
	if (this.fld.onchange)
		this.fld.onchange();
};

/**
 * Clears the textarea value
 * @type void
 */
MemoField.prototype.clear = function() {
	this.fld.value = '';
	this.count.value = this.maxlength;
};

/**
 * Checks whether the textarea value is an empty string
 * @type Boolean
 */
MemoField.prototype.isEmpty = function() {
	return (this.fld.value.trim() == '');
};

/**
 * Handles onKeyUp and onKeyDown events in the textarea,
 * updating the aux control field with the number of chars left
 * @param {Event} e Event
 * @type void
 */
MemoField.prototype.keyHandler = function(e) {
	var key = $K(e), ign = '#33#34#35#36#37#38#39#40#45#4098#';
	if (ign.indexOf('#'+key+'#') == -1 && (key < 112 || key > 123)) {
		var len = this.fld.value.length;
		if (len >= this.maxlength) {
			this.fld.value = this.fld.value.substring(0, this.maxlength);
			this.count.value = 0;
		} else {
			this.count.value = this.maxlength - len;
		}
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'form/memofield.js'] = true;

}
