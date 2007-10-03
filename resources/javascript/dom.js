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
 * This file contains classes that work over the Document
 * Object Model, like Element and Event. The Element singleton
 * can be used directly from an instance of the HTMLElement class
 * or indirectly, from an object instance or id. The Event class
 * contains new static members and prototype methods
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'dom.js']) {

/**
 * The Element class contains methods that can be applied
 * to any DOM element through the $ and $E functions
 * @class Element
 */
Element = function(name, parent, style, html, attrs) {
	name = name.toLowerCase(), attrs = attrs || {};
	var elm = null;
	if (PHP2Go.browser.ie && (attrs.name || attrs.type)) {
		name = '<' + name +
				(attrs.name ? ' name="' + attrs.name + '"' : '') +
				(attrs.type ? ' type="' + attrs.type + '"' : '') +
				'>';
		delete attrs.name;
		delete attrs.type;
		elm = $E(document.createElement(name));
	} else {
		if (!Element.cache[name]) Element.cache[name] = $E(document.createElement(name));
		elm = Element.cache[name].cloneNode(false);
	}
	(style) && (elm.setStyle(style));
	(parent) && (parent.appendChild(elm));
	(html) && (elm.innerHTML = html);
	(attrs) && (elm.writeAttribute(attrs));
	return elm;
};

/**
 * Get the element's tag
 * @type String
 */
Element.prototype.getTag = function() {
	return this.tagName.toLowerCase();
};

/**
 * Checks if the element has a given attribute.
 * This method doesn't override native browser
 * implementations.
 * @param {String} attr Attribute name
 * @type Boolean
 */
Element.prototype.hasAttribute = function(attr) {
	attr = Element.translation.attrs.names[attr] || attr;
	if (this.getAttributeNode) {
		var node = this.getAttributeNode(attr);
		return (node ? node.specified : false);
	}
	return false;
};

/**
 * Reads the value of an element's attribute
 * @param {String} attr Attribute name
 * @type Object
 */
Element.prototype.readAttribute = function(attr) {
	var trans = Element.translation.attrs, tn, flag = 0, node;
	if (tn = trans.names[attr])
		return this[tn];
	if (flag = trans.iflag[attr])
		return this.getAttribute(attr, flag);
	return this.getAttribute(attr);
};

/**
 * Writes one or more element's attributes
 * @param {Object} attr Attribute name or attributes hash
 * @param {Object} value Attribute value
 * @type Object
 */
Element.prototype.writeAttribute = function(attr, value) {
	var trans = Element.translation.attrs;
	var attrs = attr;
	if (Object.isString(attr)) {
		attrs = {};
		attrs[attr] = value;
	}
	for (var attr in attrs) {
		var tn = trans.names[attr];
		if (tn) {
			this[tn] = attrs[attr];
		} else if (trans.write[attr]) {
			trans.write[attr](this, attrs[attr]);
		} else if (attrs[attr] === null) {
			this.removeAttribute(attr);
		} else {
			this.setAttribute(attr, attrs[attr]);
		}
	}
	return this;
};

/**
 * Recursively collects elements associated by the 'prop' property
 *
 * The property 'prop' must point to a single DOM element.
 * Returns an array of extended elements.
 * @param {String} prop Property
 * @type Array
 */
Element.prototype.recursivelyCollect = function(prop) {
	var res = [], elm = this;
	while (elm = elm[prop]) {
		if (elm.nodeType == 1)
			res.push($E(elm));
	}
	return res;
};

/**
 * Recursively sums the values of a given property
 * on all ancestors of the element
 * @param {String} prop Property name
 * @type Number
 */
Element.prototype.recursivelySum = function(prop) {
	var res = 0, elm = this;
	while (elm) {
		if (elm.getComputedStyle('position') == 'fixed')
			return 0;
		var val = elm[prop];
		if (val) {
			res += val - 0;
		}
		if (elm == document.body)
			break;
		elm = elm.parentNode;
	}
	return res;
};

if (document.evaluate) {
	/**
	 * Search for all elements that match the given set of class names
	 * @param {String} clsName CSS class names (one or more, space separated)
	 * @type Array
	 */
	Element.prototype.getElementsByClassName = function(clsNames) {
		clsNames = (clsNames + '').trim();
		if (!clsNames.empty()) {
			clsNames = clsNames.split(/\s+/);
			var cond = clsNames.valid(function(item, idx) {
				return (item.empty() ? null : "[contains(concat(' ', @class, ' '), ' %1 ')]".assignAll(item));
			}).join('');
			return (cond ? document.getElementsByXPath('.//*' + cond, this) : []);
		}
		return [];
	};
	/**
	 * @ignore
	 */
	document.getElementsByXPath = function(expr, parent) {
		var res = [], qry = document.evaluate(expr, $(parent) || document, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
		for (var i=0,s=qry.snapshotLength; i<s; i++)
			res.push($E(qry.snapshotItem(i)));
		return res;
	};
} else {
	/**
	 * @ignore
	 */
	Element.prototype.getElementsByClassName = function(clsNames) {
		clsNames = (clsNames + '').trim();
		if (!clsNames.empty()) {
			var clsStr = ' ' + clsNames + ' ', clsList = clsNames.split(/\s+/);
			var res = [], elms = this.getElementsByTagName('*');
			for (var i=0,ch; ch=elms[i]; i++) {
				if (ch.className) {
					var cn = ' ' + ch.className + ' ';
					if (cn.find(clsStr) || clsList.every(function(item, idx) { return (!item.empty() && cn.find(' ' + item + ' ')); }))
						res.push($E(ch));
				}
			}
			return res;
		}
		return [];
	};
}

/**
 * Define document.getElementsByClassName only when not already present
 */
if (!document.getElementsByClassName) {
	document.getElementsByClassName = function(clsNames) {
		return Element.getElementsByClassName(document, clsNames);
	};
}

/**
 * Finds an element's ancestor by tag name
 * @param {String} tag Tag to be searched
 * @type Object
 */
Element.prototype.getParentByTagName = function(tag) {
	var elm = this;
	while (elm) {
		if (elm.nodeName.equalsIgnoreCase(tag))
			return $E(elm);
		elm = elm.parentNode;
	}
	return null;
};

/**
 * Collects the ancestor nodes of the element
 * @type Array
 */
Element.prototype.getParentNodes = function() {
	return this.recursivelyCollect('parentNode');
};

/**
 * Sets the element's parent node. Stores the old parent
 * in a property called oldParent and move the node
 * to the child nodes of the new parent
 * @param {Object} p New parent
 * @type Object
 */
Element.prototype.setParentNode = function(p) {
	if (p = $(p)) {
		var child = this;
		if (child.parentNode) {
			if (child.parentNode == p)
				return child;
			child.oldParent = child.parentNode;
			child = this.parentNode.removeChild(child);
		}
		return p.appendChild(child);
	}
	return null;
};

/**
 * Collects the element's child nodes, skipping
 * all text nodes. Extends and returns all
 * child nodes in an array
 * @type Array
 */
Element.prototype.getChildNodes = function() {
	var res = [], fc = null;
	if (fc = this.firstChild) {
		while (!Object.isElement(fc))
			fc = fc.nextSibling;
		if (fc) {
			fc = $E(fc);
			return [fc].concat(fc.getNextSiblings());
		}
	}
	return res;
};

/**
 * Get the element's previous siblings
 * @type Array
 */
Element.prototype.getPreviousSiblings = function() {
	return this.recursivelyCollect('previousSibling');
};

/**
 * Get the element's next siblings
 * @type Array
 */
Element.prototype.getNextSiblings = function() {
	return this.recursivelyCollect('nextSibling');
};

/**
 * Get all element's siblings
 * @type Array
 */
Element.prototype.getSiblings = function() {
	return this.recursivelyCollect('previousSibling').reverse().concat(this.recursivelyCollect('nextSibling'));
};

/**
 * Checks if the element is child of a given element
 * @param {Object} anc Ancestor element
 * @type Boolean
 */
Element.prototype.isChildOf = function(anc) {
	if (anc = $(anc)) {
		if (anc.contains && !PHP2Go.browser.khtml) {
			return anc.contains(this);
		} else if (anc.compareDocumentPosition) {
			return !!(anc.compareDocumentPosition(this) & 16);
		} else {
			var p = this.parentNode;
			while (p) {
				if (p == anc)
					return true;
				if (p.tagName.equalsIgnoreCase('html'))
					return false;
				p = p.parentNode || null;
			}
		}
	}
	return false;
};

/**
 * Retrieve the absolute position of the element.
 * This method returns an object containing 2
 * properties: x (left offset) and y (top offset)
 * @param {Number} tbt Box type (0, 1, 2 or 3)
 * @type Object
 */
Element.prototype.getPosition = function(tbt) {
	var p = {x: 0, y: 0};
	var b = PHP2Go.browser, db = document.body || document.documentElement;
	// native and target box type
	var nbt = Element.BORDER_BOX, tbt = Object.ifUndef(tbt, Element.BORDER_BOX);
	if (this.getBoundingClientRect) {
		var bcr = this.getBoundingClientRect();
		p.x = bcr.left - 2;
		p.y = bcr.top - 2;
	} else if (document.getBoxObjectFor) {
		nbt = Element.PADDING_BOX;
		var box = document.getBoxObjectFor(this);
		p.x = box.x - this.recursivelySum('scrollLeft');
		p.y = box.y - this.recursivelySum('scrollTop');
	} else if (this.offsetParent) {
		if (this.parentNode != db) {
			p.x -= Element.recursivelySum((b.opera ? db : this), 'scrollLeft');
			p.y -= Element.recursivelySum((b.opera ? db : this), 'scrollTop');
		}
		var cur = this, end = (b.safari && this.style.getPropertyValue('position') == 'absolute' && this.parentNode == db ? db : db.parentNode);
		do {
			var l = this.offsetLeft;
			if (!b.opera || l > 0)
				p.x += (isNaN(l) ? 0 : l);
			var t = this.offsetTop;
			p.y += (isNaN(t) ? 0 : t);
			cur = cur.offsetParent;
		} while (cur != end && cur != null);
	} else if (this.x && this.y) {
		p.x += (isNaN(this.x) ? 0 : this.x);
		p.y += (isNaN(this.y) ? 0 : this.y);
	}
	var extents = ['padding', 'border', 'margin'];
	if (nbt > tbt) {
		for (var i=tbt; i<nbt; i++) {
			if (extents[i] == 'border') {
				p.x += this.getBorder('left');
				p.y += this.getBorder('top');
			} else {
				p.x += Object.toPixels(this.getComputedStyle(extents[i] + '-left'));
				p.y += Object.toPixels(this.getComputedStyle(extents[i] + '-top'));
			}
		}
	} else if (tbt > nbt) {
		for (var i=tbt; i>nbt; --i) {
			if (extents[i-1] == 'border') {
				p.x -= this.getBorder('left');
				p.y -= this.getBorder('top');
			} else {
				p.x -= Object.toPixels(this.getComputedStyle(extents[i-1] + '-left'));
				p.y -= Object.toPixels(this.getComputedStyle(extents[i-1] + '-top'));
			}
		}
	}
	return p;
};

/**
 * Get the dimensions of the element.
 * The results are returned as an object,
 * containing 2 properties: width and height
 * @type Object
 */
Element.prototype.getDimensions = function() {
	var d = {width: 0, height: 0};
	if (this.getComputedStyle('display') != 'none') {
		if (this.offsetWidth && this.offsetHeight) {
        	d.width = this.offsetWidth;
        	d.height = this.offsetHeight;
		} else if (this.style && this.style.pixelWidth && this.style.pixelHeight) {
			d.width = this.style.pixelWidth;
			d.height = this.style.pixelHeight;
    	}
	} else {
		var self = this;
		this.swapStyles({
			position: 'absolute', visibility: 'hidden', display: (this.tagName.equalsIgnoreCase('div') ? 'block' : '')
		}, function() {
			d.width = self.clientWidth;
			d.height = self.clientHeight;
		});
	}
	return d;
};

/**
 * Get the element's border value for a given side
 * @param {String} side Border side (top, right, bottom or left)
 * @type Number
 */
Element.prototype.getBorder = function(side) {
	return (this.getComputedStyle('border-' + side + '-style') == 'none' ? 0 : Object.toPixels(this.getComputedStyle('border-' + side + '-width')));
};

/**
 * Get the element's border box
 * @type Object
 */
Element.prototype.getBorderBox = function() {
	return {
		width: this.getBorder('left') + this.getBorder('right'),
		height: this.getBorder('top') + this.getBorder('bottom')
	};
};

/**
 * Get the element's padding box
 * @type Object
 */
Element.prototype.getPaddingBox = function() {
	var tp = Object.toPixels;
	return {
		width: tp(this.getComputedStyle('padding-left')) + tp(this.getComputedStyle('padding-right')),
		height: tp(this.getComputedStyle('padding-top')) + tp(this.getComputedStyle('padding-bottom'))
	};
};

/**
 * Checks if the element is within a given pair of coordinates.
 * The coordinates must be objects containing x and y members:
 * {x: 10, y : 20}. p1 coordinate is the top-left corner
 * @param {Object} p1 Top-left coordinate
 * @param {Object} p2 Bottom-right coordinate
 * @return Boolean
 */
Element.prototype.isWithin = function(p1, p2) {
	var p = this.getPosition(), d = this.getDimensions();
	var ex1 = p.x, ex2 = (p.x+d.width), ey1 = p.y, ey2 = (p.y+d.height);
	return (ex1<p2.x && ex2>p1.x && ey1<p2.y && ey2>p1.y);
};

/**
 * Collects the contents of all text nodes inside the element
 * @type String
 */
Element.prototype.getInnerText = function() {
	var tag = this.getTag();
	if (tag == 'script' || tag == 'style') {
		if (PHP2Go.browser.ie)
			return (tag == 'style' ? this.styleSheet.cssText : this.readAttribute('text'));
		return this.innerHTML;
	}
	if (this.innerText)
		return this.innerText;
	if (this.textContent)
		return this.textContent;
	var s = '', cs = this.childNodes;
	for (var i=0; i<cs.length; i++) {
		switch (cs[i].nodeType) {
			case 1 :
				s += Element.getInnerText(cs[i]);
				break;
			case 3 :
				s += cs[i].nodeValue;
				break;
		}
	}
	return s;
};

/**
 * Defines the text content of the element
 * @param {String} text Text content
 * @type Object
 */
Element.prototype.setInnerText = function(text) {
	var tag = this.getTag();
	if (tag == 'script' || tag == 'style') {
		if (PHP2Go.browser.ie) {
			if (tag == 'style')
				this.styleSheet.cssText = text;
			else
				this.writeAttribute('text', text);
		} else {
			(this.firstChild) && (this.removeChild(this.firstChild));
			this.appendChild(document.createTextNode(text));
		}
	} else {
		this[!Object.isUndef(this.innerText) ? 'innerText' : 'textContent'] = text;
	}
	return this;
};

if (PHP2Go.browser.ie) {
	/**
	 * Retrieves the computed value of a given style property
	 * @param {String} prop Property name
	 * @type String
	 */
	Element.prototype.getComputedStyle = function(prop) {
		if (this.currentStyle)
			return this.currentStyle[prop.camelize()];
		return null;
	};
	/**
	 * Gets all computed styles of the element
	 * @type Object
	 */
	Element.prototype.getComputedStyles = function() {
		return this.currentStyle || null;
	};
} else {
	/**
	 * @ignore
	 */
	Element.prototype.getComputedStyle = function(prop) {
		var d = document, cs;
		if (d.defaultView && d.defaultView.getComputedStyle) {
			if (cs = d.defaultView.getComputedStyle(this, null))
				return cs[prop.camelize()];
		}
		return null;
	};
	/**
	 * @ignore
	 */
	Element.prototype.getComputedStyles = function() {
		var d = document;
		if (d.defaultView && d.defaultView.getComputedStyle)
			return d.defaultView.getComputedStyle(this, null);
		return null;
	};
}

/**
 * Reads a style property of the element.
 * For better results, always provide property
 * name using CSS property declaration style, e.g.:
 * background-color, border-left-width, font-family
 * @param {String} prop Property name
 * @type Object
 */
Element.prototype.getStyle = function(prop) {
	var val = null;
	if (this.style) {
		var d = document, camel = prop.camelize();
		if (PHP2Go.browser.ie && (prop == 'float' || prop == 'cssFloat'))
			camel = 'styleFloat';
		else if (prop == 'float')
			camel = 'cssFloat';
		val = this.style[camel];
		if (!val)
			val = this.getComputedStyle(prop);
		if (PHP2Go.browser.opera && ['left', 'top', 'right', 'bottom'].contains(prop) && this.getComputedStyle('position') == 'static')
			val = null;
		(val == 'auto') && (val = null);
		(prop == 'opacity' && val) && (val = parseFloat(val, 10));
	}
	return val;
};

/**
 * Set one or more style properties of the element
 * @param {Object} prop Hash of properties or property name
 * @param {Object} value Property value
 * @type Object
 */
Element.prototype.setStyle = function(prop, value) {
	var props = prop;
	if (Object.isString(prop)) {
		props = {};
		props[prop] = value;
	}
	for (var prop in props) {
		switch (prop) {
			case 'opacity' :
				this.setOpacity(props[prop]);
				break;
			case 'width' :
			case 'height' :
				this.style[prop.camelize()] = props[prop];
				if (PHP2Go.browser.wch && this.getComputedStyle('position') == 'absolute' && this.getComputedStyle('display') != 'none')
					WCH.update(this);
				break;
			default :
				this.style[prop.camelize()] = props[prop];
				break;
		}
	}
	return this;
};

/**
 * Swaps in/out style properties in order to call a given function
 * @param {Object} props Style properties
 * @param {Function} func Function to be called
 * @type Object
 */
Element.prototype.swapStyles = function(props, func) {
	for (var p in props) {
		this.style['old'+p] = this.style[p];
		this.style[p.camelize()] = props[p];
	}
	func();
	for (var p in props) {
		this.style[p.camelize()] = this.style['old'+p];
		this.style['old'+p] = null;
	}
	return this;
};

/**
 * Gets the element's opacity level. The level is returned
 * as a decimal number between 0 and 1
 * @type Number
 */
Element.prototype.getOpacity = function() {
	var op = 1;
	if (PHP2Go.browser.ie) {
		var mt = [];
		if (mt = Element.prototype.getOpacity.re.exec(this.getStyle('filter') || ''))
			op = (parseFloat(mt[1], 10) / 100);
	} else {
		op = parseFloat(this.style.opacity || this.style.MozOpacity || this.style.KhtmlOpacity || 1, 10);
	}
	return (op >= 0.999999 ? 1 : op);
};
/**
 * @ignore
 */
Element.prototype.getOpacity.re = /alpha\(opacity=(.*)\)/;

/**
 * Sets the element's opacity level. The opacity level
 * must be a decimal number between 0 and 1
 * @param {Number} op Opacity level
 * @type Object
 */
Element.prototype.setOpacity = function(op) {
	op = (isNaN(op) || op >= 1 ? 1 : (op < 0.00001 ? 0 : op));
	var s = this.style, b = PHP2Go.browser;
	s.opacity = op;
	if (b.ie) {
		s.zoom = 1;
		s.filter = (this.getStyle('filter') || '').replace(Element.prototype.setOpacity.re, '');
		s.filter += 'alpha(opacity=' + Math.round(op*100) + ')';
	} else if (b.mozilla) {
		s.MozOpacity = op;
	} else if (b.khtml) {
		s.KhtmlOpacity = op;
	}
	return this;
};
/**
 * @ignore
 */
Element.prototype.setOpacity.re = /alpha\([^\)]*\)/gi;

/**
 * Checks if the element contains a given CSS class
 * @param {String} cl CSS class
 * @type Boolean
 */
Element.prototype.hasClass = function(cl) {
	var ec = this.className, cl = cl + '';
	return (
		(ec.length > 0 && ec == cl) ||
		(ec.match(new RegExp("(^|\\s)" + cl + "(\\s|$)")))
	);
};

/**
 * Adds a CSS class on the element
 * @param {String} cl CSS class
 * @type Object
 */
Element.prototype.addClass = function(cl) {
	cl += '';
	if (!this.hasClass(cl))
		this.className += (this.className ? ' ' : '') + cl;
	return this;
};

/**
 * Removes a CSS class from the element
 * @param {String} cl CSS class
 * @type Object
 */
Element.prototype.removeClass = function(cl) {
	var cl = cl + '', re = new RegExp("^"+cl+"\\b\\s*|\\s*\\b"+cl+"\\b", 'g');
	this.className = this.className.replace(re, '');
	return this;
};

/**
 * Adds/removes a CSS class on the element
 * @param {String} cl CSS class
 * @type Object
 */
Element.prototype.toggleClass = function(cl) {
	cl += '';
	this[this.hasClass(cl) ? 'removeClass' : 'addClass'](cl);
	return this;
};

/**
 * Verify if the element is visible
 * @type Boolean
 */
Element.prototype.isVisible = function() {
	var elm = this;
	while (elm && elm != document) {
		if (elm.style.display == 'none' || elm.style.visibility == 'hidden')
			return false;
		elm = elm.parentNode;
	}
	return true;
};

/**
 * Shows the element
 * @type Object
 */
Element.prototype.show = function() {
	this.style.display = (this.tagName.equalsIgnoreCase('div') ? 'block' : '');
	if (PHP2Go.browser.wch && this.getComputedStyle('position') == 'absolute')
		WCH.attach(this);
	return this;
};

/**
 * Hides the element
 * @type Object
 */
Element.prototype.hide = function() {
	this.style.display = 'none';
	if (PHP2Go.browser.wch && this.getComputedStyle('position') == 'absolute')
		WCH.detach(this);
	return this;
};

/**
 * Toggles the element's visibility
 * @type Object
 */
Element.prototype.toggleDisplay = function() {
	if (this.style.display == 'none')
		this.show();
	else
		this.hide();
	return this;
};

/**
 * Moves the element to the given x and y coordinates
 * @param {Number} x X coordinate
 * @param {Number} y Y coordinate
 * @type Object
 */
Element.prototype.moveTo = function(x, y) {
	this.setStyle('left', x + 'px');
	this.setStyle('top', y + 'px');
	if (PHP2Go.browser.wch && this.getComputedStyle('position') == 'absolute')
		WCH.update(this, {position: {x: x, y: y}});
	return this;
};

/**
 * Resizes the element to the given width and height values
 * @param {Number} w Width
 * @param {Number} h Height
 * @type Object
 */
Element.prototype.resizeTo = function(w, h) {
	this.setStyle('width', w + 'px');
	this.setStyle('height', h + 'px');
	if (PHP2Go.browser.wch && this.getComputedStyle('position') == 'absolute')
		WCH.update(this, {dimensions: {width: w, height: h}});
	return this;
};

/**
 * Scrolls the window to the element's position
 * @type Object
 */
Element.prototype.scrollTo = function() {
	var pos = this.getPosition();
	window.scrollTo(pos.x, pos.y);
	return this;
};

/**
 * Insert the node after a given reference node. That
 * means the new node is inserted between the reference
 * node and the reference node's next sibling
 * @param {Object] ref Reference node
 * @type Object
 */
Element.prototype.insertAfter = function(ref) {
	if (ref.nextSibling)
		ref.parentNode.insertBefore(this, ref.nextSibling);
	else
		ref.parentNode.appendChild(this);
	return this;
};

/**
 * Inserts HTML code inside the element. The 'position' argument
 * allows to define where the HTML contents must be inserted :
 * "before" the element, on the "top" or on the "bottom" of the
 * element or "after" the element
 * @param {String} ins HTML or element to insert
 * @param {String} pos Insertion position. Defaults to "bottom"
 * @param {Boolean} evalScripts Whether to eval scripts. Defaults to false
 * @type Object
 */
Element.prototype.insert = function(ins, position, evalScripts) {
	evalScripts = !!evalScripts, position = position || 'bottom';
	var pos = Element.insertion[position];
	if (pos) {
		if (Object.isElement(ins)) {
			pos.insert(this, ins);
			return this;
		}
		ins = String(ins);
		if (!document.createRange || PHP2Go.browser.opera) {
			var tag = this.tagName.toUpperCase();
			var tran = Element.translation;
			if (tag in tran.tags) {
				var frag = tran.createFromHTML(tag, ins.stripScripts());
				(position == 'top' || position == 'bottom') && (frag.reverse());
				frag.walk(function(item, idx) {
					pos.insert(this, item);
				});
			} else {
				this.insertAdjacentHTML(pos.adjacency, ins.stripScripts());
			}
		} else {
			var rng = this.ownerDocument.createRange();
			pos.initializeRange(this, rng);
			pos.insert(this, rng.createContextualFragment(ins.stripScripts()));
		}
		(evalScripts) && (ins.evalScriptsDelayed());
	}
	return this;
};

/**
 * Set the HTML contents of the element.
 * By setting 'useDom' to true, a temporary div
 * element will be created and its child nodes will
 * be copied to the target element.
 * @param {Object} upd HTML or element to update with
 * @param {Boolean} evalScripts Whether to eval scripts. Defaults to false
 * @param {Boolean} useDom Whether to use DOM. Defaults to false
 * @type Object
 */
Element.prototype.update = function(upd, evalScripts, useDom) {
	evalScripts = !!evalScripts, useDom = !!useDom;
	if (Object.isElement(upd))
		return this.clear().insert(upd);
	upd = String(upd);
	if (upd.empty())
		return this.clear(useDom);
	var tag = this.tagName.toUpperCase();
	var tran = Element.translation;
	if (PHP2Go.browser.ie && tag in tran.tags) {
		while (this.firstChild)
			this.removeChild(this.firstChild);
		var self = this, frag = tran.createFromHTML(tag, upd.stripScripts());
		frag.walk(function(item, idx) {
			self.appendChild(item);
		});
	} else if (useDom) {
		var div = document.createElement('div');
		div.innerHTML = upd.stripScripts();
		while (this.firstChild)
			this.removeChild(this.firstChild);
		while (div.firstChild)
			this.appendChild(div.removeChild(div.firstChild));
		delete div;
	} else {
		this.innerHTML = upd.stripScripts();
	}
	(evalScripts) && (upd.evalScriptsDelayed());
	return this;
};

/**
 * Replaces the element with the given HTML code
 * @param {Object} rep Replacement code or element
 * @param {Boolean} eval Whether to eval scripts. Defaults to false
 * @type Object
 */
Element.prototype.replace = function(rep, evalScripts) {
	evalScripts = !!evalScripts;
	if (Object.isElement(rep)) {
		this.parentNode.replaceChild(rep, this);
		return this;
	}
	rep = String(rep);
	if (this.outerHTML) {
		var tag = this.tagName.toUpperCase();
		var tran = Element.translation;
		if (tag in tran.tags) {
			var par = this.parentNode;
			var next = this.nextSibling;
			while (next && !Object.isElement(next))
				next = next.nextSibling;
			var frag = tran.createFromHTML(tag, rep.stripScripts());
			par.removeChild(this);
			if (next)
				frag.walk(function(item, idx) { par.insertBefore(item, next); });
			else
				frag.walk(function(item, idx) { par.appendChild(item); });
		} else {
			this.outerHTML = rep.stripScripts();
		}
	} else {
		var rng = this.ownerDocument.createRange();
		rng.selectNode(this);
		this.parentNode.replaceChild(rng.createContextualFragment(rep.stripScripts()), this);
	}
	(evalScripts) && (rep.evalScriptsDelayed());
	return this;
};

/**
 * Remove all HTML contents from the element
 * @param {Boolean} useDom Whether to use DOM or not
 * @type Object
 */
Element.prototype.clear = function(useDom) {
	useDom = !!useDom;
	if (useDom) {
		while (this.firstChild)
			this.removeChild(this.firstChild);
	} else {
		this.innerHTML = '';
	}
	return this;
};

/**
 * Verify if the element is empty
 * @type Boolean
 */
Element.prototype.empty = function() {
	return this.innerHTML.empty();
};

/**
 * Remove the element from its parent node
 * @type Object
 */
Element.prototype.remove = function() {
	if (this.parentNode)
		this.parentNode.removeChild(this);
	return this;
};

/**
 * Adds a list of methods to the element extensions.
 * All passed methods will become available in extended
 * elements and as static methods of the Element class
 * @param {Object} props New methods
 * @type void
 */
Element.extend = function(props) {
	for (var prop in props) {
		if (Object.isFunc(props[prop])) {
			HTMLElement.prototype[prop] = props[prop];
			Element.prototype[prop] = props[prop];
			Element[prop] = props[prop].methodize($E);
		}
	}
};

/**
 * @ignore
 */
Element.cache = {};

/**
 * Content box
 * @type Number
 */
Element.CONTENT_BOX = 0;
/**
 * Padding box
 * @type Number
 */
Element.PADDING_BOX = 1;
/**
 * Border box
 * @type Number
 */
Element.BORDER_BOX = 2;
/**
 * Margin box
 * @type Number
 */
Element.MARGIN_BOX = 3;

/**
 * @ignore
 */
Element.insertion = {
	before : {
		adjacency : 'BeforeBegin',
		initializeRange : function(elm, rng) {
			rng.setStartBefore(elm);
		},
		insert : function(elm, ins) {
			elm.parentNode.insertBefore(ins, elm);
		}
	},
	top : {
		adjacency : 'AfterBegin',
		initializeRange : function(elm, rng) {
			rng.selectNodeContents(elm);
			rng.collapse(true);
		},
		insert : function(elm, ins) {
			elm.insertBefore(ins, elm.firstChild);
		}
	},
	bottom : {
		adjacency : 'BeforeEnd',
		insert : function(elm, ins) {
			elm.appendChild(ins);
		}
	},
	after : {
		adjacency : 'AfterEnd',
		initializeRange : function(elm, rng) {
			rng.setStartAfter(elm);
		},
		insert : function(elm, ins) {
			elm.parentNode.insertBefore(ins, elm.nextSibling);
		}
	}
};

/**
 * @ignore
 */
Element.insertion.bottom.initializeRange = Element.insertion.top.initializeRange;

/**
 * @ignore
 */
Element.translation = {
	attrs : {
		names : {
			'class' : 'className',
			'className' : 'className',
			'for' : 'htmlFor',
			'colspan' : 'colSpan',
			'rowspan' : 'rowSpan',
			'valign' : 'vAlign',
			'cellspacing' : 'cellSpacing',
			'cellpadding' : 'cellPadding',
			'enctype' : 'encType',
			'accesskey' : 'accessKey',
			'tabindex' : 'tabIndex',
			'maxlength' : 'maxLength',
			'readonly' : 'readOnly',
			'frameborder' : 'frameBorder',
			'value' : 'value',
			'disabled' : 'disabled',
			'checked' : 'checked',
			'multiple' : 'multiple',
			'selected' : 'selected',
			'title' : (PHP2Go.browser.ie || PHP2Go.browser.opera ? 'title' : null)
		},
		iflag : (PHP2Go.browser.ie ? {
			src: 2, href: 2, type: 2
		} : { }),
		read : {
			'style' : function(elm) {
				return elm.style.cssText.toLowerCase();
			}
		},
		write : {
			'style' : function(elm, value) {
				elm.style.cssText = (value ? value : '');
			}
		}
	},
	tags : {
	    TABLE : ['<table>', '</table>', 1],
	    TBODY : ['<table><tbody>', '</tbody></table>', 2],
	    THEAD : ['<table><tbody>', '</tbody></table>', 2],
	    TFOOT : ['<table><tbody>', '</tbody></table>', 2],
	    TR : ['<table><tbody><tr>', '</tr></tbody></table>', 3],
	    TH : ['<table><tbody><tr><td>', '</td></tr></tbody></table>', 4],
	    TD : ['<table><tbody><tr><td>', '</td></tr></tbody></table>', 4],
	    SELECT : ['<select>', '</select>', 1]
	},
	div : document.createElement('div'),
	createFromHTML : function(tn, html) {
		var self = Element.translation, t = self.tags[tn];
		self.div.innerHTML = t[0] + html + t[1];
		var div = self.div;
		for (var i=0; i<t[2]; i++)
			div = div.firstChild;
		return $A(div.childNodes);
	}
};

/**
 * Convert an HTML element to access all methods
 * of Element class as its native methods. The
 * objects are converted only once. In browsers with
 * native HTMLElement support, this function will
 * directly return the passed object
 * @param {Object} elm Object to be converted
 * @type Object
 */
$E = function(elm) {
	if (!elm) return null;
	if (!elm.nodeType || elm.nodeType != 1) return elm;
	if (elm === window || elm === document) return elm;
	if (elm._extended || PHP2Go.nativeElementExtensions) return elm;
	Object.extend(elm, Element.prototype, false);
	//Element.extend(elm);
	elm._extended = $EF;
	return elm;
};

/**
 * Utility function to create DOM element nodes. The
 * element can be initialized with a set of style
 * properties and initial HTML contents
 * @param {String} name Tag name
 * @param {Object} parent Parent node
 * @param {Object} style Style properties
 * @param {String} innerHTML Inner HTML
 * @param {Object} attrs Attributes
 * @type Object
 */
$N = function(name, parent, style, html, attrs) {
	return new Element(name, parent, style, html, attrs);
};

/**
 * Define HTMLElement if it's not a valid identifier,
 * and add all Element methods to its prototype
 */
if (!PHP2Go.nativeElementExtension) {
	window.HTMLElement = $EF;
	if (document.createElement('div').__proto__) {
		window.HTMLElement.prototype = document.createElement('div').__proto__;
		PHP2Go.nativeElementExtension = true;
	}
}
Element.extend(Element.prototype);

/**
 * WCH stands for Windowed Controls Hider, which is a tool that
 * creates a workaround for a classic IE bug that doesn't allow
 * absolute positioned layers to appear over windowed controls
 * like select inputs, applets, objects or embeds. For IE5.5+,
 * an iframe is created an positioned below the layer. For IE5,
 * all elements whose position conflict with the layer will be
 * hidden
 * @class WCH
 */
var WCH = {
	/**
	 * Attach a WCH in a given element
	 * @param {Object} elm Element
	 */
	attach : function(elm) {
		elm = $(elm);
		if (PHP2Go.browser.ie5) {
			var elms = [], list = [];
			var tags = ['select', 'iframe', 'applet', 'object', 'embed'];
			var p = elm.getPosition(), d = elm.getDimensions();
			var p1 = {x: p.x, y: p.y};
			var p2 = {x: (p.x+d.width), y: (p.y+d.height)};
			for (var i=0; i<tags.length; i++) {
				elms = document.getElementsByTagName(tags[i]);
				for (var j=0; j<elms.length; j++) {
					if (Element.isWithin(elms[j], p1, p2)) {
						elms[j].style.visibility = "hidden";
						list.push(elms[j]);
					}
				}
			}
			elm.wchList = list;
		} else {
			var wch, pos = elm.getPosition(Element.BORDER_BOX), dim = elm.getDimensions();
			var zi = elm.getComputedStyle('z-index');
			if (!elm.wchIframe) {
				wch = elm.wchIframe = $N('iframe', elm.parentNode, {position: 'absolute', filter: 'progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0);'});
				// fix low z-indexes
				if (zi <= 2)
					zi = elm.style.zIndex = 1000;
			} else {
				wch = elm.wchIframe;
			}
			wch.style.display = '';
			wch.style.width = dim.width + 'px';
			wch.style.height = dim.height + 'px';
			wch.style.top = pos.y + 'px';
			wch.style.left = pos.x + 'px';
			wch.style.zIndex = zi - 1;
		}
	},
	/**
	 * Hide/undo WCH attached to a given element
	 * @param {Object} elm Element
	 * @type void
	 */
	detach : function(elm) {
		if (elm.wchIframe)
			elm.wchIframe.style.display = 'none';
		if (elm.wchList) {
			elm.wchList.walk(function(item, idx) {
				item.style.visibility = 'visible';
			});
			elm.wchList = null;
		}
	},
	/**
	 * Update WCH of a given element. This method is
	 * useful when the layer changes position or dimension
	 * @param {Object} elm Element
	 * @param {Object} opts Cached position and/or dimensions, to avoid calculations
	 * @type void
	 */
	update : function(elm, opts) {
		if (elm.wchIframe || elm.wchList) {
			if (elm.wchIframe) {
				opts = opts || {};
				var pos = opts.position || elm.getPosition(Element.BORDER_BOX);
				var dim = opts.dimensions || elm.getDimensions();
				elm.wchIframe.style.left = pos.x;
				elm.wchIframe.style.top = pos.y;
				elm.wchIframe.style.width = dim.width;
				elm.wchIframe.style.height = dim.height;
			} else {
				this.detach(elm);
				this.attach(elm);
			}
		} else {
			this.attach(elm);
		}
	}
};

if (!window.Event) {
	/**
	 * @class Event
	 */
	var Event = {};
	if (!PHP2Go.nativeEventExtension)
		Event.prototype = {};
}

/**
 * Register a function to be executed when
 * the Document Object Model is available. In
 * the worst case, the function will be called
 * when window.onload is fired
 * @param {Function} fn Function to be executed
 * @type void
 */
Event.onDOMReady = function(fn) {
	if (this.done) {
		fn();
		return;
	}	
	if (!this.queue) {
		var self = this, b = PHP2Go.browser, d = document;
		var ready = function() {
			if (!self.done) {
				for (var i=0; i<self.queue.length; i++)
					self.queue[i]();
				self.queue = null;
				self.done = true;i
			}
		};
		// initialize queue
		this.queue = [];
		if (d.addEventListener) {
			// mozilla, opera9
			d.addEventListener('DOMContentLoaded', ready, false);
		} else if (d.readyState && b.ie) {
			// msie
			var f = function() {
				try {
					d.firstChild.doScroll('left');
					ready();
				} catch(e) {
					f.delay(10);
				}
			}; f.delay(0);
		} else if (d.readyState && (b.khtml || b.opera)) {
			// khtml, opera8
			var f = function() {
				if (/loaded|complete/.test(d.readyState))
					ready();
				else
					f.delay(10);
			}; f.delay(0);
		} else {
			// other browsers
			this.addLoadListener(ready);
		}
	}
	this.queue.push(fn);
};

/**
 * Register a function to be executed when
 * window.onload event is fired
 * @param {Function} fn Function to be executed
 * @type void
 */
Event.addLoadListener = function(fn) {
	/**
	 * avoid conflicts between window.onload
	 * and <body onload=""></body>
	 */
	if (!document.body) {
		Event.addListener(window, 'load', fn);
	} else {
		var oldLoad = window.onload;
		if (typeof oldLoad != 'function') {
			window.onload = fn;
		} else {
			window.onload = function() {
				oldLoad();
				fn();
			}
		}
	}
};

/**
 * @ignore
 */
Event.handlers = {
	cache : {},
	getCacheId : function(elm) {
		if (elm.eventId)
			return elm.eventId;
		arguments.callee.id = arguments.callee.id || 1;
		return elm.eventId = arguments.callee.id++;
	},
	getEventName : function(type) {
		type = type.replace(/^on/i, '').toLowerCase();
		(type == 'keypress' && PHP2Go.browser.khtml) && (type = 'keydown');
		return type;		
	},
	getHandlers : function(elm, type) {
		var id = this.getCacheId(elm);
		var c = this.cache[id] = this.cache[id] || {};
		return c[type] = c[type] || [];
	},
	add : function(elm, type, fn) {
		var h = this.getHandlers(elm, type);
		if (h.indexOf(fn) == -1) {
			h.push(fn);
			return true;
		}
		return false;
	},
	remove : function(elm, type, fn) {
		var h = this.getHandlers(elm, type);
		var p = h.indexOf(fn);
		if (p != -1) {
			h.splice(pos, 1);
			return true;
		}
		return false;
	},
	fire : function(elm, type) {
		var h = this.getHandlers(elm, type);
		h.walk(function(h, i) {
			h.apply(elm);
		});
	},
	destroy : function() {
		for (var id in this.cache)
			for (var type in this.cache[id])
				this.cache[id][type] = null;
		delete this.cache;
	}
};

if (window.attachEvent) {
	window.attachEvent('onunload', function() {
		Event.handlers.destroy();
		try {
			window.onload = $EF;
			window.onunload = $EF;
		} catch(e) { }
	});
}

/**
 * Adds an event handler function to a given element and event type
 * @param {Object} elm Element
 * @param {String} type Event type
 * @param {Function} fn Handler function
 * @param {Boolean} capt Use capture
 * @type void
 */
Event.addListener = function(elm, type, fn, capt) {
	if (elm = $(elm)) {
		var handlers = Event.handlers;
		var type = handlers.getEventName(type);
		if (handlers.add(elm, type, fn)) {
			capt = !!capt;
			if (elm.addEventListener)
				elm.addEventListener(type, fn, capt);
			else if (elm.attachEvent)
				elm.attachEvent('on' + type, fn);
		}
	}
};

/**
 * Removes an event handler from an element
 * @param {Object} elm Element
 * @param {String} type Event type
 * @param {Function} fn Handler function
 * @param {Boolean} capt Use capture
 * @type void
 */
Event.removeListener = function(elm, type, fn, capt) {
	if (elm = $(elm)) {
		var handlers = Event.handlers;
		var type = handlers.getEventName(type);
		if (handlers.remove(elm, type, fn)) {
			capt = !!capt;
			if (elm.removeEventListener)
				elm.removeEventListener(type, fn, capt);
			else if (elm.detachEvent)
				elm.detachEvent('on' + type, fn);
		}
	}
};

/**
 * Runs all handlers of a given type on an element
 * @param {Object} elm Element
 * @param {String} type Event type
 * @type void
 */
Event.fire = function(elm, type) {
	if (elm = $(elm)) {
		var handlers = Event.handlers;
		var type = handlers.getEventName(type);
		handlers.fire(elm, type);
	}
};

/**
 * Extends an event object by adding properties and
 * methods that are not available in the browser's engine
 * @param {Event} ev Event
 * @type Event
 */
Event.extend = function() {
	function relatedTarget(ev) {
		switch (ev.type) {
			case 'mouseover' : return $E(ev.fromElement);
			case 'mouseout' : return $E(ev.toElement);
			default : return null;
		}
	}
	return function(ev) {
		var proto = Event.prototype;
		proto.stopPropagation = function() { this.cancelBubble = true; };
		proto.preventDefault = function() { this.returnValue = false; };
		ev = Object.extend(ev, proto);
		ev.target = $E(ev.target || ev.srcElement);
		ev.relatedTarget = relatedTarget(ev);
		var pos = ev.position();
		ev.pageX = pos.x;
		ev.pageY = pos.y;
		return ev;
	}
}();

/**
 * Returns the element that originated the event
 * @type Object
 */
Event.prototype.element = function() {
	var elm = this.target;
	if (elm.nodeType) {
		while (elm.nodeType != 1)
			elm = elm.parentNode;
	}
	return $E(elm);
};

/**
 * Finds a node with a given tag name
 * starting from the event source element
 * @param {String} tag Tag name
 * @type Object
 */
Event.prototype.findElement = function(tag) {
	var elm = this.element();
	return (elm ? Element.getParentByTagName(elm, tag) : null);
};

/**
 * Determines if an event is related to a given element.
 * Only works with mouse events (mousedown, mouseover, mouseout)
 * @param {Object} elm Element
 * @type Boolean
 */
Event.prototype.isRelated = function(elm) {
	if (elm = $(elm)) {
		var rel = this.relatedTarget;
		return (rel && (rel == elm || rel.isChildOf(elm)));
	}
};

/**
 * Returns the typed key code
 * @type Number
 */
Event.prototype.key = function() {
	return this.keyCode || this.which;
};

/**
 * Returns the typed char
 * @type String
 */
Event.prototype.char = function() {
	return String.fromCharCode(this.keyCode || this.which).toLowerCase();
};

/**
 * Retrieve event position in the screen.
 * The value returned is an object containing
 * 2 properties: x and y
 * @type Object
 */
Event.prototype.position = function() {
	var e = document.documentElement || document.body;
	return {
		x : this.pageX || (this.clientX + e.scrollLeft),
		y : this.pageY || (this.clientY + e.scrollTop)
	};
};

/**
 * Suspends the propagation of the event
 * and cancel its default behaviour
 * @type void
 */
Event.prototype.stop = function() {
	this.preventDefault();
	this.stopPropagation();
};

/**
 * @ignore
 */
Event.addLoadListener(function() {
	for (var i=0; i<Widget.widgets.length; i++) {
		var attrs = Widget.widgets[i];
		var widget = new window[attrs[0]](attrs[1], attrs[2]);
		widget.setup();
	}
});

/**
 * Convert a native Event object to access all Event
 * extensions defined by the framework. That copy
 * will happen only in browsers that don't recognize
 * Event.prototype as a valid class prototype
 * @param {Object} e Event
 */
$EV = function(e) {
	e = e || window.event;
	if (!e || PHP2Go.nativeEventExtension || e._extended)
		return e;
	e = Event.extend(e);
	e._extended = $EF;
	return e;
};

/**
 * Get the typed key code of a keyboard event
 * @param {Event} e Event
 * @type Number
 */
$K = function(e) {
	e = (e || window.event);
	return (e.keyCode || e.which);
};

PHP2Go.included[PHP2Go.baseUrl + 'dom.js'] = true;

}