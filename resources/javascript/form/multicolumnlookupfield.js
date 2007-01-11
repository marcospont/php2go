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
 * This file contains the MultiColumnLookupField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/multicolumnlookupfield.js']) {

/**
 * Javascript component used by the MultiColumnLookupField form component
 * @class MultiColumnLookupField
 * @base ComponentField
 * @param {String} id Component's ID
 * @param {Integer} height Table height
 * @param {Object} style Style properties hash
 */
MultiColumnLookupField = function(id, height, style) {
	this.ComponentField($(id), 'MultiColumnLookupField');
	this.container = $(id + '_container');
	this.text = $(id + '_text');
	this.btn = $(id + '_button');
	this.tblContainer = $(id + '_tableContainer');
	this.tbl = this.tblContainer.getElementsByTagName('table')[0];
	this.height = height || 150;
	this.style = {
		normal: (style || {}).normal || 'mclookupNormal',
		selected: (style || {}).selected || 'mclookupSelected',
		hover: (style || {}).hover || 'mclookupHover'
	};
	this.size = this.fld.options.length;
	this.selectedIndex = (this.isEmpty() ? -1 : this.fld.selectedIndex);
	this.setup();
};
MultiColumnLookupField.extend(ComponentField, 'ComponentField');

/**
 * Initializes the component's properties and event handlers
 * @type void
 */
MultiColumnLookupField.prototype.setup = function() {
	var self = this;
	var f = this.fld, t = this.text;
	var b = this.btn, c = this.container;
	// setup stylesheet properties
	f.component = b.component = t.component = this;
	f.hide();
	t.auxiliary = true;
	t.style.border = 'none';
	t.style.paddingLeft = '5px';
	b.style.width = '18px';
	b.style.height = '19px';
	b.style.verticalAlign = 'top';
	b.firstChild.style.verticalAlign = 'middle';
	c.style.border = '1px solid ThreeDShadow';
	c.style.display = 'block';
	Event.addLoadListener(function() {
		c.style.width = (t.offsetWidth + b.offsetWidth + (PHP2Go.browser.ie?2:0)) + 'px';
	});
	// setup event listeners
	Event.addListener(self.btn, 'focus', function(e) { self.raiseEvent('focus'); });
	Event.addListener(self.btn, 'keydown', function(e) { self.keyHandler(e); });
	Event.addListener(self.btn, 'click', function(e) {
		self.raiseEvent('click');
		self.toggleDisplay();
	});
	Event.addListener(self.text, 'keydown', function(e) { self.keyHandler(e); });
	Event.addListener(self.text, 'click', function(e) {
		self.raiseEvent('click');
		self.toggleDisplay();
	});
	Event.addListener(self.tblContaier, 'keydown', function(e) { self.keyHandler(e); });
	$C(self.tbl.getElementsByTagName('tr')).walk(function(item, idx) {
		item.index = idx;
		Event.addListener(item, 'mouseover', function(e) { self.cellHoverHandler($EV(e)); });
		Event.addListener(item, 'mouseout', function(e) { self.cellHoverHandler($EV(e)); });
		Event.addListener(item, 'click', function(e) {
			self.cellClickHandler($EV(e));
		});
	});
	// set initial value
	if (this.selectedIndex >= 1) {
		this.text.value = this.fld.options[this.selectedIndex].text;
		this.tbl.rows[this.selectedIndex].className = this.style.selected;
	}
};

/**
 * Retrieve the component's value
 * @type String
 */
MultiColumnLookupField.prototype.getValue = function() {
	var idx = this.fld.selectedIndex;
	if (idx >= 0 && this.fld.options[idx].value != "")
		return this.fld.options[idx].value;
	return null;
};

/**
 * Define the component's value
 * @param {String} val New value
 * @type void
 */
MultiColumnLookupField.prototype.setValue = function(val) {
	var self = this;
	$C(this.fld.options).walk(function(item, idx) {
		if (item.value == val) {
			self.setValueByIndex(idx);
			throw $break;
		}
	});
};

/**
 * Select an option from its index
 * @param {Integer} idx Option index
 * @type void
 */
MultiColumnLookupField.prototype.setValueByIndex = function(idx) {
	var rows = this.tbl.rows;
	if (this.selectedIndex >= 1)
		rows[this.selectedIndex].className = this.style.normal;
	this.fld.options[idx].selected = true;
	this.selectedIndex = this.fld.selectedIndex = idx;
	this.text.value = this.fld.options[idx].text;
	rows[idx].className = this.style.selected;
	this.raiseEvent('change');
};

/**
 * Clear the component's value
 * @type void
 */
MultiColumnLookupField.prototype.clear = function() {
	$C(this.fld.options).walk(function(item, idx) { item.selected = false; });
	this.fld.selectedIndex = -1;
	this.selectedIndex = null;
	this.text.value = '';
};

/**
 * Clear all options in the component
 * @type void
 */
MultiColumnLookupField.prototype.clearOptions = function() {
	var fst = this.tbl.firstChild.firstChild;
	this.tbl.update("<tr class='" + this.style.normal + "'>" + fst.innerHTML + "</tr>");
	this.txt.value = '';
	this.fld.options.length = 1;
	this.fld.selectedIndex = -1;
	this.selectedIndex = null;
};

/**
 * Import options to the component from a string splitted by line and column
 * separators. The default separators are "|" for lines and "~" for columns.
 * Each 'line' in the string must contain the same number of columns specified
 * in the component's datasource. The add position can be specified at the
 * 4th parameter (pos)
 * @param String str Options string
 * @param String lsep Line separator
 * @param String csep Column separator
 * @param Integer pos Add position
 * @type void
 */
MultiColumnLookupField.prototype.importOptions = function(str, lsep, csep, pos) {
	lsep = (lsep || '|'), csep = (csep || '~');
	pos = Math.abs(pos || 0) + 1;
	if (pos <= this.fld.options.length) {
		var self = this;
		var unselect = false;
		str.split(lsep).walk(function(el, idx) {
			var opt = el.split(csep);
			if (opt.length >= 2) {
				// add a new combo option and shifts the option data
				self.fld.options[pos] = new Option(opt[1], opt[0]);
				opt.shift();
				// was selectedIndex replaced?
				if (pos == self.selectedIndex)
					unselect = true;
				// replace or add a new table row
				if (pos < self.tbl.rows.length) {
					var row = self.tbl.rows[pos];
					opt.walk(function(item, idx) {
						row.cells[idx].innerHTML = item;
					});
				} else {
					var row = self.tbl.insertRow(pos);
					row.className = self.style.normal;
					row.index = pos;
					opt.walk(function(item, idx) {
						var cell = row.insertCell(idx);
						cell.noWrap = true;
						cell.innerHTML = item;
					});
					//Element.update(row, "<td nowrap>" + opt.join("</td><td nowrap>") + "</td>");
					Event.addListener(row, 'mouseover', function(e) { self.cellHoverHandler($EV(e)); });
					Event.addListener(row, 'mouseout', function(e) { self.cellHoverHandler($EV(e)); });
					Event.addListener(row, 'click', function(e) {
						self.cellClickHandler($EV(e));
					});
				}
				pos++;
			}
		});
		// remove remaining rows
		if (pos < this.tbl.rows.length) {
			for (i=(this.tbl.rows.length-1); i>=pos; i--) {
				if (pos == this.selectedIndex)
					unselect = false;
				this.tbl.deleteRow(i);
			}
		}
		// unselect if necessary
		if (unselect) {
			this.text.value = '';
			this.selectedIndex = null;
		}
	}
};

/**
 * Enable the component
 * @type void
 */
MultiColumnLookupField.prototype.enable = function() {
	this.setDisabled(false);
};

/**
 * Disable the component
 * @type void
 */
MultiColumnLookupField.prototype.disable = function() {
	this.setDisabled(true);
};

/**
 * Internal method to disable/enable the component.
 * Is called from {@link MultiColumnLookupField#enable}
 * and {@link MultiColumnLookupField#disable}
 * @param {Boolean} b Flag value
 * @type void
 * @private
 */
MultiColumnLookupField.prototype.setDisabled = function(b) {
	b = !!b;
	b && this.hide();
	this.fld.disabled = b;
	this.text.disabled = b;
	this.btn.disabled = b;
	this.btn.setOpacity(b?0.6:0);
};

/**
 * Move focus to the component's first member
 * @type void
 */
MultiColumnLookupField.prototype.focus = function() {
	if (!this.btn.disabled) {
		this.btn.focus();
		this.raiseEvent('focus');
		return true;
	}
	return false;
};

/**
 * Toggle display status of the component
 * @type void
 */
MultiColumnLookupField.prototype.toggleDisplay = function() {
	if (!this.tblContainer.isVisible()) {
		var b = document.body, c = this.container;
		var t = this.tbl, tc = this.tblContainer;
		var ss = (PHP2Go.browser.ie ? 18 : 16);
		// adjust table container style
		tc.style.left = c.getPosition().x;
		tc.style.height = this.height + 'px';
		tc.style.zIndex = 2;
		tc.show();
		// minimum width
		if (t.offsetWidth < c.offsetWidth) {
			tc.style.width = c.offsetWidth;
			t.style.width = c.offsetWidth - ss;
		}
		// add horizontal scrollbar height
		if (t.offsetHeight < this.height)
			tc.style.height = t.offsetHeight + (tc.offsetWidth < t.offsetWidth ? ss : 0);
		// position beyond document body limits
		var p = tc.getPosition();
		if ((p.x + tc.offsetWidth) > (b.clientWidth + b.scrollLeft))
			tc.style.left = (b.scrollLeft + b.clientWidth - tc.offsetWidth);
		if ((p.y + tc.offsetHeight) > (b.clientHeight + b.scrollTop))
			tc.style.top = (p.y - tc.offsetHeight - c.offsetHeight);
		// move to selected option
		if (this.selectedIndex >= 1)
			this.tbl.rows[this.selectedIndex].scrollIntoView(false);
		// add document listener
		Event.addListener(document, 'mousedown', this.mouseDownHandler.bind(this), true);
	} else {
		this.tblContainer.hide();
	}
};

/**
 * Hide the table containing component's options
 * @type void
 */
MultiColumnLookupField.prototype.hide = function() {
	if (this.tblContainer.isVisible()) {
		this.raiseEvent('blur');
		this.tblContainer.hide();
		if (this.fld.selectedIndex < 1) {
			if (this.selectedIndex >= 1) {
				this.tbl.rows[this.selectedIndex].className = this.style.normal;
				this.selectedIndex = this.fld.selectedIndex;
			}
		}
		Event.removeListener(document, 'mousedown', this.mouseDownHandler.bind(this), true);
	}
};

/**
 * Handler method for keyboard events
 * @param {Event} e Event
 * @type void
 */
MultiColumnLookupField.prototype.keyHandler = function(e) {
	var k = $K(e);
	switch (k) {
		// pgdown
		case 34 : this.navigate(5); break;
		// pgup
		case 33 : this.navigate(-5); break;
		// arrowdown
		case 40 : this.navigate(1); break;
		// arrowup
		case 38 : this.navigate(-1); break;
		// end
		case 35 : this.navigate(this.selectedIndex > -1 ? this.size-this.selectedIndex-1 : this.size-1); break;
		// home
		case 36 : this.navigate(this.selectedIndex > -1 ? -this.selectedIndex+1 : 2); break;
		// tab, esc
		case 9 :
		case 27 :
			this.hide(); break;
		// enter
		case 13 :
			if (this.selectedIndex >= 1) {
				this.setValueByIndex(this.selectedIndex);
				this.toggleDisplay();
				$EV(e).stop();
			}
			break;
		default :
			if (!e.ctrlKey && !e.altKey && k != 32 && (key < 112 || key > 123))
				$EV(e).stop();
			break;
	}
};

/**
 * Handler method for the mousedown event. Used to listen to mouse
 * events outside the component's table. If an outer click is detected,
 * the table must be hidden
 * @param {Event} e Event
 * @type void
 */
MultiColumnLookupField.prototype.mouseDownHandler = function(e) {
	var e = $EV(e), t = $E(e.element());
	if (!t.isChildOf(this.container)) {
		this.hide();
	}
};

/**
 * Handles mouseover event on the option rows. Used to highlight
 * the row when the cursor pointer is over it
 * @param {Event} e Event
 * @type void
 */
MultiColumnLookupField.prototype.cellHoverHandler = function(e) {
	var elm = e.element();
	if (elm && elm.parentNode.index) {
		var p = elm.parentNode;
		if (e.type == 'mouseover') {
			(this.selectedIndex == -1 || p.index != (this.selectedIndex)) && (p.className = this.style.hover);
		} else {
			p.className = (this.selectedIndex != -1 && p.index == (this.selectedIndex) ? this.style.selected : this.style.normal);
		}
	}
};

/**
 * Handles a mouse click event on the table rows. The target option become selected
 * @param {Event} e Event
 * @type void
 */
MultiColumnLookupField.prototype.cellClickHandler = function(e) {
	var elm = e.element();
	if (elm && elm.parentNode.index) {
		this.setValueByIndex(elm.parentNode.index);
		this.hide();
	}
};

/**
 * Navigation method. Called from {@link MultiColumnLookupField#navigate},
 * this method allows to move focus from one row to another, in a given offset
 * @param {Integer} fw Offset (positive or negative)
 * @type void
 */
MultiColumnLookupField.prototype.navigate = function(fw) {
	if (!this.tblContainer.isVisible())
		this.toggleDisplay();
	var rows = this.tbl.rows;
	if (this.selectedIndex == -1) {
		this.selectedIndex = 0;
	} else if (this.selectedIndex >= 1) {
		rows[this.selectedIndex].className = this.style.normal;
	}
	this.selectedIndex = (
		fw > 0
		? (this.selectedIndex+fw <= (this.size-1) ? this.selectedIndex+fw : 1)
		: (this.selectedIndex+fw >= 1 ? this.selectedIndex+fw : this.size-1)
	);
	rows[this.selectedIndex].className = this.style.selected;
	rows[this.selectedIndex].scrollIntoView(false);
};

PHP2Go.included[PHP2Go.baseUrl + 'form/multicolumnlookupfield.js'] = true;

}