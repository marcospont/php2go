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
 * This file contains the AutoCompleteField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/autocompletefield.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'ajax.js');

/**
 * AutoCompleteField adds autocomplete funcionality to a
 * normal text input. The choices can be filtered locally
 * or through an Ajax request. It supports multiple selection,
 * and it's totally integrated with PHP2Go forms API
 * @constructor
 * @base ComponentField
 * @param {String} id Field ID
 * @param {Object} options Settings
 */
AutoCompleteField = function(id, options) {
	this.ComponentField($(id), 'AutoCompleteField');
	/**
	 * Holds the div element used to show autocomplete choices
	 * @type Object
	 */
	this.autoComplete = $(id + '_choices');
	/**
	 * Component settings
	 * @type Object
	 */
	this.options = options || {};
	/**
	 * Last count of choices
	 * @type Number
	 */
	this.choiceCount = 0;
	/**
	 * Last list of available choices
	 * @type Array
	 */
	this.choiceItems = [];
	/**
	 * Indicates a choice is being made
	 * @type Boolean
	 */
	this.choosing = false;
	/**
	 * Flag that indicates mouse is over the choices container
	 * @type Boolean
	 */
	this.overChoices = false;
	/**
	 * Indicates text field changed
	 * @type Boolean
	 */
	this.fldChanged = false;
	/**
	 * Index of the current selected choice
	 * @type Boolean
	 */
	this.selectedIndex = -1;
	/**
	 * @ignore
	 */
	this.lastValue = null;
	/**
	 * @ignore
	 */
	this.timer = null;
	this.setup();
};
AutoCompleteField.extend(ComponentField, 'ComponentField');

/**
 * Initializes all options and event handlers
 * @type void
 */
AutoCompleteField.prototype.setup = function() {
	var self = this, op = this.options;
	this.fld.component = this;
	this.autoComplete.style.zIndex = 1000;
	op.ajax = !!op.ajax;
	op.ajaxOptions = (typeof(op.ajaxOptions) != 'object' ? {} : op.ajaxOptions);
	op.ajaxOptions.params = op.ajaxOptions.params || {};
	op.url = op.url || document.location.pathname;
	op.choices = (op.ajax || !op.choices || !op.choices.length ? [] : op.choices);
	op.delay = op.delay || 0.3;
	op.maxChoices = op.maxChoices || 10;
	op.minChars = op.minChars || 2;
	op.ignoreCase = PHP2Go.ifUndef(op.ignoreCase, true);
	op.fullSearch = !!op.fullSearch;
	op.autoSelect = !!op.autoSelect;
	op.incremental = !!op.incremental;
	op.separator = op.separator || ',';
	op.style = {
		normal: (op.style || {}).normal || 'autoCompleteNormal',
		selected: (op.style || {}).selected || 'autoCompleteSelected',
		hover: (op.style || {}).hover || 'autoCompleteHover'
	};
	if (op.throbber) {
		if ((op.throbber.constructor || $EF) == Throbber)
			op.throbber = args.throbber;
		else
			op.throbber = new Throbber({element: op.throbber, centralize: false});
	}
	this.fld.setAttribute('autocomplete', 'off');
	Event.addListener(this.fld, 'keydown', function(e) { self.keyDownHandler(e); });
	Event.addListener(this.fld, 'keyup', function(e) { self.keyUpHandler(e); });
	Event.addListener(this.fld, 'blur', function(e) { self.blurHandler(e); });
	Event.addListener(this.autoComplete, 'mouseover', function(e) { self.mouseHandler(e); });
	Event.addListener(this.autoComplete, 'mouseout', function(e) { self.mouseHandler(e); });
	Event.addListener(this.autoComplete, 'scroll', function(e) { self.mouseHandler(e); });
};

/**
 * Retrieve the component's value. Returns a string
 * (incremental=false) or an array (incremental=true)
 * @type Object
 */
AutoCompleteField.prototype.getValue = function() {
	if (this.options.incremental) {
		if (this.fld.value.trim() == '')
			return null;
		var res = this.fld.value.split(this.options.separator).filter(function(item, idx) {
			var val = item.trim();
			return (val != '' ? val : null);
		});
		return (res.empty() ? null : res);
	}
	return (this.fld.value.trim());
};

/**
 * Define a new value for the component. Expects an
 * array when incremental mode is on
 * @param {Object} val New value
 * @type void
 */
AutoCompleteField.prototype.setValue = function(val) {
	(this.incremental && typeof(val) == 'array') && (val = val.split(this.separator + ' '));
	this.fld.value = val;
};

/**
 * Clear the component's value
 * @type void
 */
AutoCompleteField.prototype.clear = function() {
	this.fld.value = '';
	this.hideChoices();
};

/**
 * Check if the component's value is empty
 * @type Boolean
 */
AutoCompleteField.prototype.isEmpty = function() {
	var val = this.getValue();
	return (val == null || val == '');
};

/**
 * Disable the component
 * @type void
 */
AutoCompleteField.prototype.disable = function() {
	this.fld.disabled = true;
	this.hideChoices();
};

/**
 * Display a box containing the available choices
 * @type void
 */
AutoCompleteField.prototype.showChoices = function() {
	var elm = this.autoComplete;
	if (!elm.isVisible()) {
		var pos = this.fld.getPosition();
		var dim = this.fld.getDimensions();
		elm.show();
		if (isFinite(this.options.height))
			elm.resizeTo(dim.width, this.options.height);
		else
			elm.setStyle('width', dim.width);
		elm.moveTo(pos.x, pos.y+dim.height);
	}
};

/**
 * Hide the auto complete choices box
 * @type void
 */
AutoCompleteField.prototype.hideChoices = function() {
	this.autoComplete.hide();
	this.choosing = false;
};

/**
 * Retrieve the choices for the current search token.
 * When ajax mode is on, a request will be performed.
 * Otherwise, local choices will be filtered
 * @type void
 */
AutoCompleteField.prototype.getChoices = function() {
	var self = this, tok = this.getLastToken();
	var op = this.options, ign = op.ignoreCase;
	if (op.ajax) {
		var ajax = new AjaxRequest(op.url, op.ajaxOptions);
		ajax.async = true;
		ajax.addParam(this.fld.name, tok);
		ajax.addParam('ignorecase', (ign?1:0));
		ajax.addParam('fullsearch', (op.fullSearch?1:0));
		ajax.bind('onSuccess', function(resp) {
			if (resp.responseText.match(/^<ul/i))
				self.updateChoices(resp.responseText);
			else
				self.updateChoices(null);
		});
		ajax.bind('onError', function(resp) { self.updateChoices(null); });
		ajax.bind('onException', function(resp) { self.updateChoices(null); });
		(op.throbber) && (op.throbber.show());
		ajax.send();
	} else {
		var id, item, pos, cnt = 0;
		var tok = this.getLastToken(), res = '';
		for (var i=0; i<op.choices.length; i++) {
			item = op.choices[i];
			pos = (ign?item.toLowerCase():item).indexOf(ign?tok.toLowerCase():tok);
			if (pos != -1) {
				if (pos == 0) {
					cnt++; res += "<li><b><u>" + item.substring(0, tok.length) + "</u></b>" + item.substring(tok.length) + "</li>";
				} else if (op.fullSearch) {
					cnt++; res += "<li>" + item.substring(0, pos) + "<b><u>" + item.substring(pos, pos+tok.length) + "</u></b>" + item.substring(pos+tok.length) + "</li>";
				}
			}
			if (cnt == op.maxChoices)
				break;
		}
		(res != '') && (res = "<ul>" + res + "</ul>");
		this.updateChoices(res);
	}
};

/**
 * Update the choices box contents
 * @param {String} html New contents
 * @access private
 * @type void
 */
AutoCompleteField.prototype.updateChoices = function(html) {
	(this.options.ajax && this.options.throbber) && (this.options.throbber.hide());
	if (!this.fldChanged) {
		this.autoComplete.update(html);
		if (html != '') {
			var i, item, self = this;
			this.choiceItems = this.autoComplete.getElementsByTagName('li');
			this.choiceCount = this.choiceItems.length;
			if (this.options.autoSelect && this.choiceCount == 1) {
				this.chooseByIndex(0);
			} else {
				for (i=0; i<this.choiceItems.length; i++) {
					item = this.choiceItems[i];
					item.className = this.options.style.normal;
					item.index = i;
					Event.addListener(item, 'mouseover', function(e) { self.choiceHoverHandler($EV(e)); });
					Event.addListener(item, 'mouseout', function(e) { self.choiceHoverHandler($EV(e)); });
					Event.addListener(item, 'click', function(e) {
						self.choiceClickHandler($EV(e));
					});
				}
				this.choosing = true;
				this.selectedIndex = -1;
				this.navigate(1);
			}
		} else {
			this.choosing = false;
			this.hideChoices();
		}
	}

};

/**
 * Handles keydown event. Capture action keys when choices
 * are being displayed. Store last field value and cancel
 * last keyup timer
 * @param {Event} e Event
 * @type void
 */
AutoCompleteField.prototype.keyDownHandler = function(e) {
	e = $EV(e);
	if (this.choosing) {
		switch (e.key()) {
			// tab, enter
			case 9 :
			case 13 :
				this.chooseByIndex(this.selectedIndex); e.stop(); return;
			// esc
			case 27 :
				this.hideChoices(); e.stop(); return;
			// arrowdown
			case 40 : this.navigate(1); e.stop(); return;
			// arrowup
			case 38 : this.navigate(-1); e.stop(); return;
		}
	}
	this.lastValue = this.fld.value;
	if (this.timer)
		clearTimeout(this.timer);
};

/**
 * Handles keyup event. Triggers timer function when the
 * value of the field has changed
 * @param {Event} e Event
 * @type void
 */
AutoCompleteField.prototype.keyUpHandler = function(e) {
	var k = $K(e);
	this.fldChanged = (this.fld.value != this.lastValue);
	if ('#9#13#16#17#33#34#35#36#37#38#39#40#45#127#4098#'.indexOf('#'+k+'#') != -1 || !this.fldChanged)
		return;
	this.timer = setTimeout(this.timerHandler.bind(this), this.options.delay*1000);
};

/**
 * Gets called when the delay after the last typed key is reached.
 * Calls {@link AutoCompleteField#getChoices} when current token
 * exceeds the minimum required length
 * @type void
 */
AutoCompleteField.prototype.timerHandler = function() {
	this.fldChanged = false;
	if (this.getLastToken().length >= this.options.minChars) {
		this.getChoices();
	} else {
		this.choosing = false;
		this.hideChoices();
	}
};

/**
 * Gets called when text input looses focus. Must hide choices
 * if typed key is 9 (TAB) or mouse is not over the choices container
 * @param {Event} e Event
 * @type void
 */
AutoCompleteField.prototype.blurHandler = function(e) {
	var self = this;
	var k = $K(e);
	setTimeout(function() {
		if (!self.overChoices || k == 9)
			self.hideChoices();
	}, 150);
};

/**
 * Mouse events handler for the choices container. Controls the
 * state of the overChoices flag (mouseover and mouseout) and
 * prevents text input blur upon scroll
 * @param {Event} e Event
 * @type void
 */
AutoCompleteField.prototype.mouseHandler = function(e) {
	e = (e||window.event);
	var elm = (e.target||e.srcElement);
	switch (e.type) {
		case 'mouseover' :
			if (elm == this.autoComplete)
				this.overChoices = true;
			break;
		case 'mouseout' :
			if (elm == this.autoComplete)
				this.overChoices = false;
			break;
		case 'scroll' :
			this.fld.focus();
			break;
	}
};

/**
 * Handles mouseover/mouseout events on choice items
 * @param {Event} e Event
 * @type void
 */
AutoCompleteField.prototype.choiceHoverHandler = function(e) {
	var elm = e.findElement('li');
	if (elm && elm.index) {
		if (e.type == 'mouseover')
			(elm.index != this.selectedIndex) && (elm.className = this.options.style.hover);
		else
			elm.className = (elm.index == this.selectedIndex ? this.options.style.selected : this.options.style.normal);
	}
};

/**
 * Handles click on choice items
 * @param {Event} e Event
 * @type void
 */
AutoCompleteField.prototype.choiceClickHandler = function(e) {
	var elm = e.findElement('li');
	if (elm && typeof(elm.index) != 'undefined') {
		this.chooseByIndex(elm.index);
		e.stop();
	}
};

/**
 * Navigate by a given offset in the available choices
 * @param {Number} fw Offset
 * @access private
 * @type void
 */
AutoCompleteField.prototype.navigate = function(fw) {
	if (this.choiceCount > 0) {
		this.showChoices();
		var items = this.choiceItems;
		if (this.selectedIndex > -1)
			items[this.selectedIndex].className = this.options.style.normal;
		this.selectedIndex = (
			fw > 0
			? (this.selectedIndex+fw < this.choiceCount ? this.selectedIndex+fw : 0)
			: (this.selectedIndex+fw >= 0 ? this.selectedIndex+fw : this.choiceCount-1)
		);
		items[this.selectedIndex].className = this.options.style.selected;
		items[this.selectedIndex].scrollIntoView(false);
	} else {
		this.choosing = false;
		this.hideChoices();
	}
};

/**
 * Choose an available choice from its index
 * @param {Number} idx Choice index
 * @access private
 * @type void
 */
AutoCompleteField.prototype.chooseByIndex = function(idx) {
	var res, itemText, item = this.choiceItems[idx];
	if (this.options.choiceValueNode && (res = item.getElementsByTagName(this.options.choiceValueNode))) {
		itemText = res[0].innerHTML.stripTags();
	} else {
		itemText = item.innerHTML.stripTags();
	}
	if (this.options.incremental) {
		var tokPos = this.findLastSeparator();
		if (tokPos != -1) {
			var blank = this.fld.value.substr(tokPos+1).match(/^\s+/);
			this.fld.value = this.lastValue = this.fld.value.substr(0, tokPos+1) + (blank?blank[0]:'') + itemText + this.options.separator + ' ';
		} else {
			this.fld.value = this.lastValue = itemText + this.options.separator + ' ';
		}
	} else {
		this.fld.value = itemText;
	}
	this.hideChoices();
	this.raiseEvent('change');
	this.fld.focus();
};

/**
 * Retrieve the search token. When incremental mode is
 * on, the rightmost token will be returned.
 * @type String
 */
AutoCompleteField.prototype.getLastToken = function() {
	if (this.options.incremental) {
		var tokPos = this.findLastSeparator();
		if (tokPos != -1)
			return this.fld.value.substr(tokPos+1).trim();
	}
	return this.fld.value;
};

/**
 * Finds the last separator position
 * in the field contents
 * @type Number
 */
AutoCompleteField.prototype.findLastSeparator = function() {
	return this.fld.value.lastIndexOf(this.options.separator);
};

PHP2Go.included[PHP2Go.baseUrl + 'form/autocompletefield.js'] = true;

}
