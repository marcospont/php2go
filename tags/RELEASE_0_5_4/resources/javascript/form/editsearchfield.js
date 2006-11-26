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
// $Header: /www/cvsroot/php2go/resources/javascript/form/editsearchfield.js,v 1.7 2006/11/19 17:59:43 mpont Exp $
// $Date: 2006/11/19 17:59:43 $
// $Revision: 1.7 $

/**
 * @fileoverview
 * This file contains the EditSearchField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/editsearchfield.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'jsrsclient.js');
PHP2Go.include(PHP2Go.baseUrl + 'inputmask.js');

/**
 * The EditSearchField component consists in a select input whose
 * options are loaded from a simple search (filter name and search
 * term) executed through a JSRS request
 * @constructor
 * @base ComponentField
 * @param {String} id Component ID
 * @param {String} results Results SELECT name
 * @param {Array} masks Lists of filter masks
 * @param {Number} loadIdx Initial option index for the returned search results
 * @param {String} reqUrl Search URL. Defaults to REQUEST_URI
 * @param {Boolean} autoTrim Auto trim search term before sending the search request
 * @param {Boolean} autoDispatch Auto dispatch search when loading the component
 * @param {Boolean} debug JSRS debug flag
 * @param {String} initOption Option to be selected after performing the search (only when autoDispatch==true)
 */
EditSearchField = function(id, results, masks, loadIdx, reqUrl, autoTrim, autoDispatch, debug, initOption) {
	this.ComponentField($(id), 'EditSearchField');
	/**
	 * Select input that contains the search filters
	 * @type Object
	 */
	this.filters = $(id + "_filters");
	/**
	 * List of filter masks
	 * @type Array
	 */
	this.masks = masks;
	/**
	 * Search term input
	 * @type Object
	 */
	this.search = $(this.id + '_search');
	/**
	 * Results select input
	 * @type Object
	 */
	this.results = $(results);
	/**
	 * Search button
	 * @type Object
	 */
	this.button = $(id + "_button");
	/**
	 * Initial option index to load the returned results
	 * @type Number
	 */
	this.loadIdx = loadIdx || 0;
	/**
	 * Search URL
	 * @type String
	 */
	this.requestUrl = reqUrl || document.location.pathname;
	/**
	 * Auto trim flag
	 * @type Boolean
	 */
	this.autoTrim = !!autoTrim;
	/**
	 * Auto dispatch flag
	 * @type Boolean
	 */
	this.autoDispatch = !!autoDispatch;
	/**
	 * Debug flag
	 * @type Boolean
	 */
	this.debug = !!debug;
	/**
	 * @ignore
	 */
	this.hideAlerts = false;
	this.setup();
	if (this.autoDispatch) {
		this.hideAlerts = true;
		this.submit(initOption);
		this.hideAlerts = false;
	}
};
EditSearchField.extend(ComponentField, 'ComponentField');

/**
 * Execute all the initialization routines
 * for this form component
 * @type void
 */
EditSearchField.prototype.setup = function() {
	var self = this;
	this.fld.component = this;
	this.filters.auxiliary = true;
	this.search.auxiliary = true;
	this.results.auxiliary = true;
	// setup keypress listener to detect search submission
	Event.addListener(this.search, 'keypress', function(e) {
		var e = $EV(e), b = self.button;
		if (e.key() == 13) {
			self.submit();
			e.stop();
		}
	});
	// setup search input mask
	InputMask.setup(this.search, Mask.fromMaskName(this.masks[this.filters.selectedIndex]));
	// setup filters and button event listeners
	Event.addListener(this.filters, 'change', function(e) {
		var newMask = Mask.fromMaskName(self.masks[self.filters.selectedIndex]);
		self.search.inputMask.mask = newMask;
		self.search.value = '';
		self.search.focus();
	});
	Event.addListener(this.button, 'click', function(e) {
		if (!self.button.disabled && !self.button.searching)
			self.submit();
	});
};

/**
 * Returns the selected option's value
 * @type Object
 */
EditSearchField.prototype.getValue = function() {
	var idx = this.results.selectedIndex;
	if (idx >= 0 && this.results.options[idx].value != "")
		return this.results.options[idx].value;
	return null;
};

/**
 * Define the list selected option
 * @param {String} val Option to be selected
 * @type void
 */
EditSearchField.prototype.setValue = function(val) {
	var self = this, opt = $C(this.results.options);
	opt.walk(function(item, idx) {
		if (item.value == val) {
			self.results.value = val;
			item.selected = true;
			throw $break;
		}
	});
};

/**
 * Clear the component's value
 * @type void
 */
EditSearchField.prototype.clear = function() {
	this.search.value = '';
	this.filters.options[0].selected = true;
	this.results.options.length = 0;
};

/**
 * Enable the component
 * @type void
 */
EditSearchField.prototype.enable = function() {
	this.setDisabled(false);
};

/**
 * Disable the component
 * @type void
 */
EditSearchField.prototype.disable = function() {
	this.setDisabled(true);
};

/**
 * Focus the first element of the component
 * @type void
 */
EditSearchField.prototype.focus = function() {
	if (this.results.options.length > this.loadIdx) {
		if (!this.results.disabled) {
			this.results.focus();
			return true;
		}
	} else {
		if (!this.filters.disabled) {
			this.filters.focus();
			return true;
		}
	}
	return false;
};

/**
 * Internal method to disable/enable the component.
 * Is called from {@link EditSearchField#enable}
 * and {@link EditSearchField#disable}
 * @param {Boolean} b Flag value
 * @type void
 * @private
 */
EditSearchField.prototype.setDisabled = function(b) {
	this.filters.disabled = b;
	this.search.disabled = b;
	this.button.disabled = b;
	this.results.disabled = b;
};

/**
 * Validates and submits the search. The request is made
 * using a JSRS call. The returned response (if not
 * empty) is parsed and loaded into the target list
 * @param {String} initOption Option to be selected after performing the search
 * @type void
 */
EditSearchField.prototype.submit = function(initOption) {
	var ffilt = $F(this.filters);
	var initOption = PHP2Go.ifUndef(initOption, null);
	if (this.search.value != '') {
		if (this.autoTrim)
			this.search.value = this.search.value.trim();
		if (this.validate()) {
			var self = this, btn = this.button;
			var btnValue = btn.innerHTML;
			// JSRS response handler
			var processResponse = function(response, context) {
				var select = new SelectField(self.results);
				btn.searching = false;
				if (btn.tagName.equalsIgnoreCase('button')) {
					btn.disabled = false;
					btn.innerHTML = btnValue;
				}
				if (response != "") {
					select.fld.options.length = self.loadIdx;
					select.importOptions(response, '|', '~', self.loadIdx);
					if (initOption)
						select.setValue(initOption);
					select.focus();
				} else if (!self.hideAlerts) {
					alert(Lang.search.emptyResults);
				}
			};
			// disable and change button text
			if (btn.tagName.equalsIgnoreCase('button')) {
				btn.disabled = true;
				btn.innerHTML = Lang.search.searchingBtnValue;
			}
			// save values
			$(this.id + '_lastfilter').value = ffilt.getValue();
			$(this.id + '_lastsearch').value = this.search.value;			
			// send JSRS request
			btn.searching = true;
			jsrsExecute(this.requestUrl, processResponse, this.id.toLowerCase()+'PerformSearch', Array(ffilt.getValue(), this.search.value), this.debug);
		} else {
			// invalid search term
			if (!this.hideAlerts)
				alert(Lang.invalidValue);
			this.search.select();
		}
	} else {
		// incomplete search parameters
		if (!this.hideAlerts)
			alert(Lang.search.emptySearch);
		this.filters.focus();
	}
};

/**
 * Validates the search term's value, based on
 * the mask of the active filter
 * @type Boolean
 */
EditSearchField.prototype.validate = function() {
	try {
		var mask = this.masks[this.filters.selectedIndex];
		return (mask == 'STRING' ? true : Validator.isMask(this.search.value, mask));
	} catch(e) {
		return false;
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'form/editsearchfield.js'] = true;

}