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
// $Header: /www/cvsroot/php2go/resources/javascript/form/datepickerfield.js,v 1.3 2006/11/19 17:59:43 mpont Exp $
// $Date: 2006/11/19 17:59:43 $
// $Revision: 1.3 $

/**
 * @fileoverview
 * This file contains the DatePickerField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/datepickerfield.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'vendor/jscalendar/calendar_stripped.js');
PHP2Go.include(PHP2Go.baseUrl + 'vendor/jscalendar/calendar-setup_stripped.js');

/**
 * The DatePickerField component is a date selection tool based
 * on the JSCalendar calendar widget. It accepts single or multiple
 * date selection, and it's fully integrated with listeners and
 * validation API
 * @constructor
 * @base ComponentField
 * @param {String} id Component ID
 * @param {Object} options Calendar options
 */
DatePickerField = function(id, options) {
	this.ComponentField($(id), 'DatePickerField');
	/**
	 * Holds the instance of Calendar class
	 * @type Object
	 */
	this.calendar = null;
	/**
	 * The calendar container DIV
	 * @type Object
	 */
	this.container = $(id + '_calendar');
	/**
	 * Calendar options
	 * @type Object
	 */
	this.options = options;
	this.setup();
};
DatePickerField.extend(ComponentField, 'ComponentField');

/**
 * Performs initialization routines for this component
 * @type void
 */
DatePickerField.prototype.setup = function() {
	var self = this;
	this.fld.component = this;
	// define missing options
	this.options.flat = this.container.id;
	var func = this.options.statusFunc;
	this.options.dateStatusFunc = function(date) {
		if (self.fld.disabled)
			return true;
		return (window[func] ? window[func](date) : false);
	};
	if (this.options.multiple) {
		var ds, min = "99999999";
		if (this.options.multiple.length > 0) {
			this.options.multiple = this.options.multiple.map(function(item, idx) {
				var ret = Date.fromString(item);
				var ds = ret.print("%Y%m%d");
				if (ds < min) {
					min = ds;
					self.options.date = ret.print("%Y/%m/%d %H:%M:%S");
				}
				return ret;
			});
		}
		this.options.onMultiple = this.multiSelectHandler.bind(this);
	} else {
		this.options.onSelect = this.selectHandler.bind(this);
	}
	// reset to default values
	Event.addListener(this.fld.form, 'reset', function(evt) {
		var dt = null, vals = [];
		if (self.options.multiple) {
			self.calendar.multiple = {};
			for (var i=0; i<self.options.multiple.length; i++) {
				dt = self.options.multiple[i];
				self.calendar.multiple[dt.print("%Y%m%d")] = dt;
				vals.push(dt.print(self.calendar.dateFormat));
			}
			self.calendar.setDate(self.options.date ? new Date(self.options.date) : new Date());
			self.fld.value = vals.join(self.options.dateSep);
		} else {
			self.calendar.setDate(self.options.date ? new Date(self.options.date) : new Date());
			self.fld.value = (self.options.date ? (new Date(self.options.date)).print(self.calendar.dateFormat) : "");
		}
		self.raiseEvent('change');
	});
	// setup Calendar when DOM is available
	Event.onDOMReady(function() {
		self.calendar = Calendar.setup(self.options);
	});
};

/**
 * Retrieve the value of the component
 * @type Object
 */
DatePickerField.prototype.getValue = function() {
	if (this.options.multiple)
		return (this.fld.value != "" ? this.fld.value.split(this.options.dateSep) : null);
	return this.fld.value;
};

/**
 * Define a new value for the component
 * @param {Object} val Value (string or array)
 * @type void
 */
DatePickerField.prototype.setValue = function(val) {
	if (this.options.multiple) {
		this.calendar.multiple = {};
		var ds, min = "99999999", dates = $A(val), dateVals = [];
		for (var i=0; i<dates.length; i++) {
			dates[i] = Date.fromString(dates[i]);
			dateVals.push(dates[i].print(this.calendar.dateFormat));
			ds = dates[i].print("%Y%m%d");
			if (ds < min) {
				min = ds;
				this.calendar.date = dates[i];
			}
			this.calendar.multiple[ds] = dates[i];
		}
		this.fld.value = dateVals.join(this.options.dateSep);
	} else {
		this.calendar.date = Date.fromString(val);
		this.fld.value = this.calendar.date.print(this.calendar.dateFormat);
	}
	this.calendar.refresh();
	this.raiseEvent('change');
};

/**
 * Clear the component's value
 * @type void
 */
DatePickerField.prototype.clear = function() {
	this.fld.value = '';
	if (this.options.multiple)
		this.calendar.multiple = {};
	this.calendar.dateStr = null;
	this.calendar.refresh();
};

/**
 * Enable the component
 * @type void
 */
DatePickerField.prototype.enable = function() {
	this.setDisabled(false);
};

/**
 * Disable the component
 * @type void
 */
DatePickerField.prototype.disable = function() {
	this.setDisabled(true);
};

/**
 * Internal method to disable/enable the component.
 * Called from {@link DatePickerField#enable}
 * and {@link DatePickerField#disable}
 * @param {Boolean} b Flag value
 * @type void
 * @private
 */
DatePickerField.prototype.setDisabled = function(b) {
	this.fld.disabled = b;
	this.calendar.refresh();
};

/**
 * Override default focus implementation. Maps to
 * framework's empty function, as this component
 * can't receive focus
 * @type void
 */
DatePickerField.prototype.focus = $EF;

/**
 * Serialize the component's value
 * @type String
 */
DatePickerField.prototype.serialize = function() {
	if (this.fld.value.trim() == '')
		return null;
	if (this.options.multiple) {
		var self = this;
		return this.fld.value.split(this.options.dateSep).map(function(item, idx) {
			return self.name + '[]=' + item.urlEncode();
		}).join('&');
	} else {
		return this.name + '=' + this.fld.value;
	}
};

/**
 * Handler method called when a date is selected
 * in single selection mode
 * @param {Object} cal Calendar
 * @param {String} date Selected date
 * @type void
 */
DatePickerField.prototype.selectHandler = function(cal, date) {
	this.fld.value = date;
	this.raiseEvent('click');
	this.raiseEvent('change');
};

/**
 * Handler method called when a date is selected or
 * unselected in multiple selection mode
 * @param {Object} cal Calendar
 * @param {Date} date Selected/unselected date
 * @param {Boolean} selected Operation flag
 * @type void
 */
DatePickerField.prototype.multiSelectHandler = function(cal, date, selected) {
	var dts = [];
	for (var ds in cal.multiple)
		dts.push(cal.multiple[ds].print(cal.dateFormat));
	this.fld.value = dts.join(this.options.dateSep);
	this.raiseEvent('click');
	this.raiseEvent('change');
};

PHP2Go.included[PHP2Go.baseUrl + 'form/datepickerfield.js'] = true;

}