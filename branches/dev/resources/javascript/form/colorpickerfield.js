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
// $Header: /www/cvsroot/php2go/resources/javascript/form/colorpickerfield.js,v 1.4 2006/11/19 17:59:43 mpont Exp $
// $Date: 2006/11/19 17:59:43 $
// $Revision: 1.4 $

/**
 * @fileoverview
 * This file contains the ColorPickerField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/colorpickerfield.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'widgets/colorpicker.js');

/**
 * The ColorPickerField component is a color selection tool based
 * on a color palette of 228 options. The color picker widget includes
 * an area to display current selected color, a text input that allows
 * typing a new color (if it fits expected color format) and a display
 * area whose color represents the last hovered color
 * @constructor
 * @base ComponentField
 * @param {String} id Component ID
 * @param {Object} options Component options
 */
ColorPickerField = function(id, options) {
	this.ComponentField($(id), 'ColorPickerField');
	/**
	 * Working mode
	 * @type String
	 */
	this.mode = options.mode || 'flat';
	/**
	 * Container id (flat mode)
	 * @type Object
	 */
	this.container = (options.container ? $(options.container) : null);
	/**
	 * Trigger id
	 * @type Object
	 */
	this.trigger = (options.trigger ? $(options.trigger) : null);
	/**
	 * Holds the instance of the ColorPicker widget
	 * @type ColorPicker
	 */
	this.picker = null;
	this.setup();
};
ColorPickerField.extend(ComponentField, 'ComponentField');

/**
 * Performs initialization routines for this component
 * @type void
 */
ColorPickerField.prototype.setup = function() {
	var self = this;
	if (this.mode == 'popup') {
		this.picker = new ColorPicker({
			mode: 'popup',
			trigger: this.trigger,
			color: this.fld.value,
			nullColor: '',
			onSelect: function(color) {
				self.fld.value = color;
				self.raiseEvent('change');
			}
		});
	} else {
		this.picker = new ColorPicker({
			mode: 'flat',
			container: this.container,
			color: this.fld.value,
			nullColor: '',
			onSelect: function(color) {
				self.fld.value = color;
				self.raiseEvent('change');
			}
		});
	}
	this.fld.component = this;
	this.picker.setDisabled(this.fld.disabled);
	if (this.mode == 'flat') {
		this.picker.text.auxiliary = true;
		this.picker.text.accessKey = this.fld.accessKey;
		this.picker.text.tabIndex = this.fld.tabIndex;
	}
	// setup onReset listener
	Event.addListener(this.fld.form, 'reset', function(evt) { self.picker.setColor(self.picker.defaultColor); });
	if (this.mode == 'flat') {
		// hook text input events
		Event.addListener(this.picker.text, 'focus', function(evt) { self.raiseEvent('focus'); });
		Event.addListener(this.picker.text, 'blur', function(evt) { self.raiseEvent('blur'); });
	}
};

/**
 * Defines a new value for the component
 * @param {String} color New color
 * @type void
 */
ColorPickerField.prototype.setValue = function(color) {
	this.fld.value = color;
	if (this.mode == 'flat') {
		this.picker.setColor(color);
		this.raiseEvent('change');
	}
};

/**
 * Clear the component's value
 * @type void
 */
ColorPickerField.prototype.clear = function() {
	this.picker.setColor(null);
};

/**
 * Enable the component
 * @type void
 */
ColorPickerField.prototype.enable = function() {
	this.fld.disabled = false;
	this.picker.setDisabled(false);
};

/**
 * Disable the component
 * @type void
 */
ColorPickerField.prototype.disable = function() {
	this.fld.disabled = true;
	this.picker.setDisabled(true);
};

/**
 * Request focus to the component
 * @type void
 */
ColorPickerField.prototype.focus = function() {
	if (!this.fld.disabled) {
		if (this.mode == 'flat')
			this.picker.text.select();
		else
			this.fld.focus();
		return true;
	}
	return false;
};

PHP2Go.included[PHP2Go.baseUrl + 'form/colorpickerfield.js'] = true;

}