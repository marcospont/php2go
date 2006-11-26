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
// $Header: /www/cvsroot/php2go/resources/javascript/util/selection.js,v 1.3 2006/11/19 17:59:15 mpont Exp $
// $Date: 2006/11/19 17:59:15 $
// $Revision: 1.3 $

/**
 * @fileoverview
 * This file contains the Selection class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'util/selection.js']) {

/**
 * The Selection class deals with selection and
 * selection ranges inside an HTML document
 * @constructor
 * @param {Object} HTML Document
 * @param {Object} HTML parent window (optional)
 */
Selection = function(doc, win) {
	/**
	 * HTML document
	 * @type Object
	 */
	this.doc = doc;
	/**
	 * Document's parent window
	 * @type Object
	 */
	this.win = win || window;
	/**
	 * @ignore
	 */
	this.cache = null;
};

/**
 * Return the native selection object
 * associated with the document
 * @type Object
 */
Selection.prototype.getSelection = function() {
	return (this.win.getSelection ? this.win.getSelection() : (this.doc.getSelection ? this.doc.getSelection() : this.doc.selection));
};

/**
 * Return the first range of the current
 * selection, or create a new one
 * @type Object
 */
Selection.prototype.getRange = function() {
	var sel = this.getSelection();
	if (sel.getRangeAt) {
		return (sel ? sel.getRangeAt(0) : this.doc.createRange());
	} else {
		return sel.createRange();
	}
};

/**
 * Get the type of the current selection.
 * The possible values are "Control", "Text" or "None"
 * @type String
 */
Selection.prototype.getType = function() {
	var sel = this.getSelection();
	if (sel.getRangeAt) {
		if (sel.rangeCount == 1) {
			var range = sel.getRangeAt(0);
			if (!range.collapsed && (range.startContainer.nodeType == 3 || range.endContainer.nodeType == 3))
				return 'Text';
			if (range.startContainer == range.endContainer && (range.endOffset - range.startOffset) == 1)
				return 'Control';
		}
	} else {
		return sel.type;
	}
	return 'None';
};

/**
 * Return a bookmark to the
 * current selection range
 * @type Object
 */
Selection.prototype.getBookmark = function() {
	var sel = this.getSelection();
	if (sel.getRangeAt) {
		if (sel.rangeCount > 0)
			return sel.getRangeAt(0).cloneRange();
	} else {
		var range = sel.createRange();
		return range.getBookmark();
	}
	return null;
};

/**
 * Retrieve the HTML contents of the current selection
 * @type String
 */
Selection.prototype.getSelectedHTML = function() {
	var elm = document.createElement('body');
	var range = this.getRange();
	if (range.cloneContents) {
		elm.appendChild(range.cloneContents());
	} else {
		elm.innerHTML = range.htmlText;
	}
	return elm.innerHTML;
};

/**
 * Return the current selected element
 * @type Object
 */
Selection.prototype.getSelectedElement = function() {
	var sel = this.getSelection(), range = this.getRange();
	if (range.item || range.parentElement) {
		return (range.item ? range.item(0) : range.parentElement());
	} else if (sel && range) {
		var elm = range.commonAncestorContainer;
		if (!range.collapsed) {
			if (range.startContainer == range.endContainer && (range.endOffset - range.startOffset) == 1) {
				if (range.startContainer.hasChildNodes())
					elm = range.startContainer.childNodes[range.startOffset];
			}
		}
		if (elm.nodeType == 1)
			return elm;
		// traverse up if we're on a Text node
		while ((elm = elm.parentNode) !== null && elm.nodeType != 1);
		return elm;
	}
	return null;
};

/**
 * Traverses the document tree up searching
 * for an element which tag is one of the
 * listed tag names. Ex: "a,img,div,p"
 * @param {String} tags Comma-separated list of tag names
 * @type Object
 */
Selection.prototype.getParentByTagNames = function(tags) {
	var re = new RegExp("^("+tags.replace(/,/g, '|')+")$", 'i');
	var sel = this.getSelection();
	var elm = this.getSelectedElement();
	if (elm) {
		do {
			if (re.test(elm.nodeName))
				return elm;
		} while ((elm = elm.parentNode) != null);
	}
	return null;
};

/**
 * Set a new selection in the document, so that
 * the passed element is between the start and end
 * of the created selection range
 * @param {Object} elm Element
 * @type void
 */
Selection.prototype.selectElement = function(elm, collapse, start) {
	var range, sel, b = this.doc.body;
	if (elm) {
		collapse = PHP2Go.ifUndef(collapse, false);
		start = PHP2Go.ifUndef(start, true);
		if (b.createTextRange) {
			range = b.createTextRange();
			try {
				range.moveToElementText(elm);
				collapse && (range.collapse(start));
				range.select();
			} catch(e) { }
		} else {
			sel = this.getSelection();
			if (sel) {
				range = this.doc.createRange();
				range.selectNode(elm);
				sel.removeAllRanges();
				sel.addRange(range);
			}
		}
	}
};

/**
 * Collapse the document selection
 * @param {Boolean} start Collapse to start (true,null) or to end (false)
 * @type void
 */
Selection.prototype.collapse = function(start) {
	var sel = this.getSelection();
	if (sel.createRange) {
		var range = sel.createRange();
		range.collapse(start !== false);
		range.select();
	} else {
		if (start !== false)
			sel.collapseToStart();
		else
			sel.collapseToEnd();
	}
};

/**
 * Clean up the selection
 * @type void
 */
Selection.prototype.clear = function() {
	var sel = this.getSelection();
	if (sel.rangeCount) {
		for (var i=0; i<sel.rangeCount; i++)
			sel.getRangeAt(i).deleteContents();
	} else {
		(sel.type != 'None') && (sel.clear());
	}
};

/**
 * Save the selection in an internal cache
 * @type void
 */
Selection.prototype.save = function() {
	this.cache = this.getBookmark();
};

/**
 * Restore the selection from the cache
 * @type void
 */
Selection.prototype.restore = function() {
	var sel, range, b = this.doc.body;
	if (this.cache != null) {
		if (b.createTextRange) {
			range = b.createTextRange();
			range.moveToBookmark(this.cache);
			range.select();
		} else if (sel = this.getSelection()) {
			sel.removeAllRanges();
			sel.addRange(this.cache);
		}
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'util/selection.js'] = true;

}