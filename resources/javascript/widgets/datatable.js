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

if (!PHP2Go.included[PHP2Go.baseUrl + 'widgets/datatable.js']) {

/**
 * @fileoverview
 * Contains the DataTable widget class
 */

/**
 * DataTable is a used that transforms a regular HTML table
 * by adding dynamic behaviors, such as sorting, row highlighting
 * and single/multiple row selection
 * @param {Object} attrs Widget's attributes
 * @param {Function} func Setup function
 * @constructor
 * @base Widget
 */
function DataTable(attrs, func) {
	this.Widget(attrs, func);
	/**
	 * Reference to the widget's root node
	 * @type Object
	 */
	this.root = null;
	/**
	 * Reference to the modified table
	 * @type Object
	 */
	this.table = null;
	/**
	 * Reference to the table head
	 * @type Object
	 */
	this.thead = null;
	/**
	 * Reference to the table body
	 * @type Object
	 */
	this.tbody = null;
	/**
	 * Current order direction (true=descending, false=ascending)
	 * @type Boolean
	 */
	this.desc = null;
	/**
	 * Current sort column index
	 * @type Number
	 */
	this.sortIdx = null;
	/**
	 * Sort types
	 * @type Object
	 */
	this.sortTypes = {};
	/**
	 * Current selected rows
	 * @type Array
	 */
	this.selected = [];
	/**
	 * Current anchor row
	 * @type Object
	 */
	this.anchor = null;
}
DataTable.extend(Widget, 'Widget');

/**
 * Holds existent DataTable instances,
 * indexed by widget ID.
 * @type Object
 */
DataTable.instances = {};

/**
 * Initializes the widget
 */
DataTable.prototype.setup = function() {
	var attrs = this.attributes;
	this.root = $(attrs.id);
	this.table = $((this.root.getElementsByTagName('table') || [null])[0]);
	if (this.table && this.table.tHead && this.table.tBodies.length > 0) {
		// setup main members
		this.thead = $(this.table.tHead);
		this.thead.addClass(attrs.headerClass);
		this.tbody = $(this.table.tBodies[0]);
		// scrollable
		if (attrs.scrollable)
			this._setupScroll();
		// setup table events
		if (PHP2Go.browser.ie) {
			Event.addListener(this.table, 'selectstart', function() {
				var e = window.event;
				(e.ctrlKey || e.shiftKey) && (e.returnValue = false);
			});
		}
		if (attrs.selectable)
			Event.addListener(this.table, 'click', this.selectHandler.bind(this), true);
		// setup headers
		if (this.thead.rows.length > 0) {
			var headers = this.thead.rows[0].cells;
			for (var i=0; i<headers.length; i++) {
				headers[i].sortType = attrs.sortTypes[i] || 'NONE';
				if (attrs.sortable && headers[i].sortType != 'NONE') {
					headers[i].style.cursor = 'pointer';
					Event.addListener(headers[i], 'click', this.sortHandler.bind(this));
					$N('img', headers[i], {visibility:'hidden'}, '', {className: 'dataTableArrow', src:attrs.orderAscIcon});
				}
			}
		}
		// setup rows
		this._setupRows();
		// setup sort types
		var toDate = function(v) {
			var d = Date.fromString(v);
			return d.valueOf();
		};
		var toFloat = function(v) {
			var f = v.replace(/[^0-9]+/g, '').replace(/([0-9]{2})$/, '.$1');
			return parseFloat(f, 10);
		};
		var toUpper = function(v) {
			return String(v).toUpperCase();
		};
		this.addSortType('NUMBER', Number);
		this.addSortType('DATE', toDate);
		this.addSortType('DATETIME', toDate);
		this.addSortType('CURRENCY', toFloat);
		this.addSortType('STRING');
		this.addSortType('ISTRING', toUpper);
		DataTable.instances[attrs.id] = this;
		this.raiseEvent('init');
	}
};

/**
 * Refreshes sorting and scrolling behaviors.
 * This method should be used when the contents
 * of the table are dinamically changed.
 * @type void
 */
DataTable.prototype.reset = function() {
	this._setupRows();
	// reset sorting
	if (this.attributes.sortable) {
		var headers = this.thead.rows[0].cells;
		if (this.sortIdx != null) {
			headers[this.sortIdx].lastChild.style.visibility = 'hidden';
			this.sortIdx = idx;
		}
		this.desc = null;
	}
	// refresh size and scroll bars
	if (this.attributes.scrollable) {
		this._onResize();
		this._setupScroll();
	}
};

/**
 * Register a new sort type
 * @param {String} type Type name (prefer using uppercase)
 * @param {Function} valueFunc Function to traduce cell contents into a comparable value
 * @param {Function} compareFunc Function to compare column values
 * @param {Function} rowFunc Function to extract contents from a row's cell
 * @type void
 */
DataTable.prototype.addSortType = function(type, valueFunc, compareFunc, rowFunc) {
	if (!this.sortTypes[type]) {
		this.sortTypes[type] = {
			type : type,
			rowFunc : (Object.isFunc(rowFunc) ? rowFunc : this._getRowValue),
			valueFunc : (Object.isFunc(valueFunc) ? valueFunc : $IF),
			compareFunc : (Object.isFunc(compareFunc) ? compareFunc : this._compare)
		};
	}
};

/**
 * Get current selected rows
 * @type Array
 */
DataTable.prototype.getSelectedRows = function() {
	var sel = this.selected;
	var res = new Array(sel.length);
	for (var i=0; i<sel.length; i++)
		res.push(sel[i]);
	return res;
};

/**
 * Get table's first row
 * @type Object
 */
DataTable.prototype.getFirstRow = function() {
	if (this.tbody && this.tbody.rows.length > 0)
		return this.tbody.rows[0];
	return null;
};

/**
 * Get the next sibling of a given row
 * @param {Object} row Table row
 * @type Object
 */
DataTable.prototype.getPreviousRow = function(row) {
	if (row && row.previousSibling) {
		var n = row.previousSibling;
		while (n) {
			if (n.nodeType == 1 && n.tagName.equalsIgnoreCase('tr') && n.parentNode == this.tbody)
				return n;
			n = n.previousSibling;
		}
	}
	return null;
};

/**
 * Get the previous sibling of a given row
 * @param {Object} row Table row
 * @type Object
 */
DataTable.prototype.getNextRow = function(row) {
	if (row && row.nextSibling) {
		var n = row.nextSibling;
		while (n) {
			if (n.nodeType == 1 && n.tagName.equalsIgnoreCase('tr') && n.parentNode == this.tbody)
				return n;
			n = n.nextSibling;
		}
	}
	return null;
};

/**
 * Handles click event on table headers
 * @param {Event} e Event
 * @type void
 */
DataTable.prototype.sortHandler = function(e) {
	var ev = $EV(e);
	ev.stop();
	var cell = ev.target;
	if (cell) {
		var idx = null;
		if (PHP2Go.browser.ie) {
			var cells = cell.parentNode.childNodes;
			for (var i=0; i<cells.length; i++) {
				if (cells[i] == cell) {
					idx = i;
					break;
				}
			}
		} else {
			idx = cell.cellIndex;
		}
		this.sort(idx);
	}
};

/**
 * Sorts the table by a given column index
 * @param {Number} idx Column index, zero based
 * @param {Boolean} desc Descending?
 * @type void
 */
DataTable.prototype.sort = function(idx, desc) {
	if (this.tbody) {
		var attrs = this.attributes, tbody = this.tbody;
		var type = attrs.sortTypes[idx] || 'STRING';
		if (type == 'NONE')
			return;
		if (desc == null) {
			if (idx != this.sortIdx)
				this.desc = attrs.descending;
			else
				this.desc = !this.desc;
		} else {
			this.desc = !!desc;
		}
		this.raiseEvent('beforesort', [idx, this.desc]);
		// build cache
		var cache = [], rows = this.tbody.rows;
		for (var i=0; i<rows.length; i++) {
			cache.push({
				value: this.sortTypes[type].valueFunc(this.sortTypes[type].rowFunc(rows[i], idx)),
				row: rows[i]
			});
		}
		// sort cache
		cache.sort(this.sortTypes[type].compareFunc);
		if (this.desc)
			cache.reverse();
		// mozilla is faster handling orphaned nodes
		if (PHP2Go.browser.mozilla) {
			var sib = tbody.nextSibling;
			var par = tbody.parentNode;
			par.removeChild(tbody);
		}
		// insert in new order
		for (var i=0; i<cache.length; i++) {
			if (attrs.rowClass) {
				if (attrs.alternateRowClass && ((i+1)%2) == 0) {
					cache[i].row.removeClass(attrs.rowClass);
					cache[i].row.addClass(attrs.alternateRowClass);
				} else {
					cache[i].row.removeClass(attrs.alternateRowClass);
					cache[i].row.addClass(attrs.rowClass);
				}
			}
			tbody.appendChild(cache[i].row);
		}
		if (PHP2Go.browser.mozilla)
			par.insertBefore(tbody, sib);
		// destroy cache
		for (var i=0; i<cache.length; i++) {
			cache[i].value = null;
			cache[i].row = null;
			cache[i] = null;
		}
		// update arrows
		var headers = this.thead.rows[0].cells;
		if (this.sortIdx != null && idx != this.sortIdx)
			headers[this.sortIdx].lastChild.style.visibility = 'hidden';
		headers[idx].lastChild.src = (this.desc ? attrs.orderDescIcon : attrs.orderAscIcon);
		headers[idx].lastChild.style.visibility = 'visible';
		this.raiseEvent('sort', [idx, this.desc]);
		this.sortIdx = idx;
	}
};

/**
 * Handles click event on table rows
 * @param {Event} e Event
 * @type void
 */
DataTable.prototype.selectHandler = function(e) {
	var ev = $EV(e);
	var row = ev.findElement('tr');
	if (row && row.parentNode == this.tbody) {
		ev.stop();
		row = $(row);
		var offset = (this.attributes.scrollable ? 0 : 1);
		var before = this.getSelectedRows();
		var attrs = this.attributes;
		// set current row as anchor
		if (this.selected.length == 0)
			this.anchor = row;
		// single selection or normal click
		if (!attrs.multiple || (!e.ctrlKey && !e.shiftKey)) {
			for (var i=0; i<this.selected.length; i++) {
				if (this.selected[i].selected && this.selected[i] != row)
					this.selectRowUI(this.selected[i], false);
			}
			this.anchor = row;
			if (!row.selected)
				this.selectRowUI(row, true);
			this.selected = [row];
		}
		// multiple selection and ctrl click
		else if (attrs.multiple && e.ctrlKey && !e.shiftKey) {
			this.selectRow(row.rowIndex-offset, !row.selected, false);
			this.anchor = row;
		}
		// multiple selection and ctrl+shift click
		else if (attrs.multiple && e.ctrlKey && e.shiftKey) {
			var up = (row.rowIndex < this.anchor.rowIndex);
			var item = this.anchor;
			while (item != null && item != row) {
				(!item.selected) && (this.selectRow(item.rowIndex-offset, true, false));
				item = (up ? this.getPreviousRow(item) : this.getNextRow(item));
			}
			(!row.selected) && (this.selectRow(row.rowIndex-offset, true, false));
		}
		// multiple selection and shift click
		else if (attrs.multiple && !e.ctrlKey && e.shiftKey) {
			for (var i=0; i<this.selected.length; i++)
				this.selectRowUI(this.selected[i], false);
			this.selected = [];
			var up = (row.rowIndex < this.anchor.rowIndex);
			var item = this.anchor;
			while (item != null) {
				this.selectRow(item.rowIndex-offset, true, false);
				if (item == row)
					break;
				item = (up ? this.getPreviousRow(item) : this.getNextRow(item));
			}
		}
		// detect changes
		var found, changed = (before.length != this.selected.length);
		if (!changed) {
			for (var i=0; i<before.length; i++) {
				found = false;
				for (var j=0; j<this.selected.length; j++) {
					if (before[i] == this.selected[j]) {
						found = true;
						break;
					}
				}
				if (!found) {
					changed = true;
					break;
				}
			}
		}
		(changed && this.raiseEvent('changeselection'));
	}
};

/**
 * Selects or unselects a given table row
 * @param {Number} idx Row index
 * @param {Boolean} b Select/unselect
 * @param {Boolean} r Whether 'changeselection' event must be triggered
 * @type void
 */
DataTable.prototype.selectRow = function(idx, b, r) {
	if (this.tbody) {
		b = !!b, r = Object.ifUndef(r, true);
		var row = this.tbody.rows[idx];
		if (row) {
			if (this.attributes.multiple) {
				if (row.selected == b)
					return;
				this.selectRowUI(row, b);
				if (b) {
					this.selected.push(row);
				} else {
					for (var i=0; i<this.selected.length; i++) {
						if (this.selected[i] == row) {
							this.selected.splice(i, 1);
							break;
						}
					}
				}
				(r && this.raiseEvent('changeselection'));
			} else {
				var old = this.selected[0];
				if (b) {
					if (old == row)
						return;
					if (old)
						this.selectRowUI(old, false);
					this.selectRowUI(row, true);
					this.selected = [row];
					(r && this.raiseEvent('changeselection'));
				} else {
					if (old == row) {
						this.selectRowUI(old, false);
						this.selected = [];
						(r && this.raiseEvent('changeselection'));
					}
				}
			}
		}
	}
};

/**
 * Applies selection UI changes on a given row
 * @param {Object} row Table row
 * @param {Boolean} b Select/unselect
 * @type void
 */
DataTable.prototype.selectRowUI = function(row, b) {
	var a = this.attributes, b = !!b;
	(b ? row.addClass(a.selectedClass) : row.removeClass(a.selectedClass));
	row.selected = b;
};

/**
 * Configures CSS classes for all table rows
 * @access private
 * @type void
 */
DataTable.prototype._setupRows = function() {
	if (this.tbody && this.tbody.rows.length > 0) {
		var attrs = this.attributes;
		var highlight = function(e) {
			e = (e || window.event);
			var row = Element.getParentByTagName(e.target || e.srcElement, 'tr');
			if (row) {
				row = $(row);
				if (e.type == 'mouseover')
					row.addClass(attrs.highlightClass);
				else
					row.removeClass(attrs.highlightClass);
			}
		};
		for (var i=0; i<this.tbody.rows.length; i++) {
			var row = $(this.tbody.rows[i]);
			if (attrs.rowClass) {
				if (attrs.alternateRowClass && ((i+1)%2) == 0)
					row.addClass(attrs.alternateRowClass);
				else
					row.addClass(attrs.rowClass);
			}
			if (!row.dtHighlight && attrs.highlightClass) {
				Event.addListener(row, 'mouseover', highlight);
				Event.addListener(row, 'mouseout', highlight);
				row.dtHighlight = true;
			}
			if (attrs.selectable)
				row.style.cursor = 'pointer';
		}
	}	
};

/**
 * Adds scrollable behavior on the data table
 * @access private
 * @type void
 */
DataTable.prototype._setupScroll = function() {
	var tbl = this.table, head = this.thead, body = this.tbody;
	var border = parseInt(tbl.border || 0, 10);	
	var spacing = parseInt(tbl.cellSpacing || 0, 10);
	//var offset = (border*2)+(spacing*2);
	if (!tbl.head) {
		// create table, move head
		var ht = $N('table');
		ht.cellPadding = tbl.cellPadding || 0;
		ht.cellSpacing = tbl.cellSpacing || 0;
		ht.border = tbl.border || 0;
		ht.className = tbl.className || '';
		ht.style.width = tbl.getDimensions().width + 'px';
		(tbl.id) && (ht.id += 'head');
		head.setParentNode(ht);
		tbl.parentNode.insertBefore(ht, tbl);
		tbl.head = ht;
	}
	// adjust head cells
	if (head.rows.length > 0 && body.rows.length > 0 && head.rows[0].cells.length == body.rows[0].cells.length) {
		for (var i=0,s=body.rows[0].cells.length; i<s; i++) {
			var th = head.rows[0].cells[i];
			var wid = Element.getDimensions(body.rows[0].cells[i]).width;
			var hb = Element.getBorderBox(th).width;
			var hp = Element.getPaddingBox(th).width;
			th.style.width = (wid-hb-hp) + 'px';
		}
	}
	if (!tbl.cont) {
		// scroll container
		tbl.cont = $N('div', tbl.parentNode);
		tbl.setParentNode(tbl.cont);
		this._onResize();
		if (!this.attributes.maxHeight)
			Event.addListener(window, 'resize', PHP2Go.method(this, '_onResize'));
	}	
};

/**
 * Table resize handler
 * @access private
 * @type void
 */
DataTable.prototype._onResize = function() {
	var cont = this.table.parentNode;
	var sb = (PHP2Go.browser.ie ? 16 : 16);
	var tbl = this.table, tblPos = tbl.getPosition();
	var tblSize = tbl.getDimensions(), maxHeight = (this.attributes.maxHeight || Window.size().height-tblPos.y);
	if (tblSize.height > maxHeight) {
		cont.setStyle({
			width: (tblSize.width + sb) + 'px',
			height: (maxHeight - sb) + 'px',
			overflowY: 'scroll'
		});
	} else {
		cont.setStyle({
			width: tblSize.width + 'px',
			height: tblSize.height + 'px',
			overflowY: 'hidden',
			overflowX: 'hidden'
		});
	}
};

/**
 * Default method to read the text contents of a table cell
 * @param {Object} row Table row
 * @param {Number} idx Column index
 * @access private
 * @type String
 */
DataTable.prototype._getRowValue = function(row, idx) {
	if (row && row.cells) {
		var cell = row.cells[idx] || null;
		if (cell)
			return Element.getInnerText(cell);
	}
	return null;
};

/**
 * Default method to compare 2 cell values.
 * It's used to sort the array of values
 * collected from a table column.
 * @param {Object} a Left operand
 * @param {Object} b Right operand
 * @access private
 * @type Number
 */
DataTable.prototype._compare = function(a, b) {
	if (a.value < b.value)
		return -1;
	if (a.value > b.value)
		return 1;
	return 0;
};

}