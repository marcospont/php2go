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

if (!window.Element) {
	/**
	 * The Element class contains methods to deal common
	 * properties of page elements: attributes, stylesheet
	 * properties, stylesheet class, ...
	 * @class Element
	 */
	var Element = {};
}

/**
 * Copies all Element methods to the target object.
 * This method is used to publish the Element methods
 * in the HTMLElement class prototype, if available
 * @param {Object} obj Target object
 * @type void
 */
Element.extend = function(obj) {
	var v, cache = function(v) {
    	return this[v] = this[v] || function() {
			var a = [this];
			for (var i=0; i<arguments.length; i++)
				a.push(arguments[i]);
      		return v.apply(null, a);
    	}
	};
	for (var p in Element) {
		v = Element[p];
		if (typeof(v) == 'function' && p != 'extend' && !obj[p]) {
			try {
				obj[p] = cache(v);
			} catch (e) { /*Logger.exception(e);*/ }
		}
	}
};

/**
 * Defines a trigger function that runs when
 * a given property of an element is changed
 * @param {Object} elm Element
 * @param {String} property Property name
 * @param {Function} func Trigger function
 * @type void
 */
Element.watch = function(elm, property, func) {
	if (elm = $(elm)) {
		var setter = '_set_' + property;
		elm[setter] = func;
		if (elm.__defineSetter__) {
			elm.__defineSetter__(property, function(val) {
				elm[setter](val);
			});
		} else {
			elm.attachEvent('onpropertychange', function() {
				if (event.propertyName == property)
					event.srcElement[setter](event.srcElement[property]);
			});
		}
	}
};

/**
 * Cross-browser implementation of getElementsByTagName.
 * Returns an Array object containing the found elements
 * @param {Object} elm Base element
 * @param {String} tag Tag name to search
 * @type Array
 */
Element.getElementsByTagName = function(elm, tag) {
	if (elm = $(elm))
		return $C(elm.getElementsByTagName(tag || '*')).map($E);
	return [];
};

/**
 * Return all elements that has a given class name, using
 * the element 'elm' as search base
 * @param {Object} elm Base element
 * @param {String} clsName CSS class name
 * @param {String} tagName Tag name. Defaults to "*"
 * @type Array
 */
Element.getElementsByClassName = function(elm, clsName, tagName) {
	if (elm = $(elm)) {
		if (document.getElementsByXPath)
			return document.getElementsByXPath(".//*[contains(concat(' ', @class, ' '), ' " + clsName + " ')]", elm);
		var re = new RegExp("(^|\\s)" + clsName + "(\\s|$)");
		var res = [], elms = elm.getElementsByTagName(tagName || '*');
		for (var i=0,s=elms.length; i<s; i++) {
			if (elms[i].className && re.test(elms[i].className))
				res.push($E(elms[i]));
		}
		return res;
	}
	return [];
};

/**
 * Finds an ancestor whose tag name is 'tag',
 * starting from a given element
 * @param {Object} elm Base element
 * @param {String} tag Tag to be searched
 * @type Object
 */
Element.getParentByTagName = function(elm, tag) {
	if (elm = $(elm)) {
		if (elm.nodeName.equalsIgnoreCase(tag))
			return elm;
		do {
			if (elm.nodeName.equalsIgnoreCase(tag))
				return $E(elm);
		} while ((elm = elm.parentNode) != null);
	}
	return null;
};

/**
 * Recursively collects elements associated by the 'prop' property
 *
 * The property 'prop' must point to a single DOM element.
 * Returns an array of extended elements.
 * @param {Object} elm Base element
 * @param {String} prop Property
 * @type Array
 */
Element.recursivelyCollect = function(elm, prop) {
	var res = [];
	if (elm = $(elm)) {
		while (elm = elm[prop]) {
			if (elm.nodeType == 1)
				res.push($E(elm));
		}
	}
	return res;
};

/**
 * Collects element's parent nodes
 * @param {Object} elm Base element
 * @type Array
 */
Element.getParentNodes = function(elm) {
	if (elm = $(elm))
		return elm.recursivelyCollect('parentNode');
	return [];
};

/**
 * Collects element's child nodes, skipping
 * all text nodes. Extends and returns all
 * child nodes in an array
 * @param {Object} elm Base element
 * @type Array
 */
Element.getChildNodes = function(elm) {
	var res = [];
	if (elm = $(elm) && (elm = elm.firstChild)) {
		while (elm && elm.nodeType != 1)
			elm = elm.nextSibling;
		if (elm) {
			elm = $E(elm);
			return [elm].concat(elm.getNextSiblings());
		}
	}
	return res;
};

/**
 * Get element's previous siblings
 * @param {Object} elm Base element
 * @type Array
 */
Element.getPreviousSiblings = function(elm) {
	if (elm = $(elm))
		return elm.recursivelyCollect('previousSibling');
	return [];
};

/**
 * Get element's next siblings
 * @param {Object} elm Base element
 * @type Array
 */
Element.getNextSiblings = function(elm) {
	if (elm = $(elm))
		return elm.recursivelyCollect('nextSibling');
	return [];
};

/**
 * Get all element's siblings
 * @param {Object} elm Base element
 * @type Array
 */
Element.getSiblings = function(elm) {
	if (elm = $(elm)) {
		var rc = elm.recursivelyCollect;
		return rc('previousSibling').reverse.concat(rc('nextSibling'));
	}
	return [];
};

/**
 * Checks if a given element has another as ancestor
 * @param {Object} elm Element
 * @param {Object} anc Ancestor element
 * @type Boolean
 */
Element.isChildOf = function(elm, anc) {
	elm = $(elm), anc = $(anc);
	if (elm && anc) {
		if (anc.contains && !PHP2Go.browser.khtml) {
			return anc.contains(elm);
		} else if (anc.compareDocumentPosition) {
			return !!(anc.compareDocumentPosition(elm) & 16);
		} else {
			var p = elm.parentNode;
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
 * Sets the parent of an element. Stores the old parent
 * in a property called oldParent and move the node
 * to the child nodes of the new parent
 * @param {Object} elm Element
 * @param {Object} par New parent
 * @type Object
 */
Element.setParentNode = function(elm, par) {
	elm = $(elm), par = $(par);
	if (elm && par) {
		if (elm.parentNode) {
			if (elm.parentNode == par)
				return elm;
			elm.oldParent = elm.parentNode;
			elm = elm.parentNode.removeChild(elm);
		}
		elm = par.appendChild(elm);
	}
	return elm;
};

/**
 * Checks if an element has a given attribute
 * @param {Object} elm Element
 * @param {String} attr Attribute name
 * @type Boolean
 */
Element.hasAttribute = function(elm, attr) {
	if (elm = $(elm)) {
		if (elm.hasAttribute)
			return elm.hasAttribute(attr);
		var node = elm.getAttributeNode(attr);
		return (node && node.specified);
	}
	return false;
};

/**
 * Insert a node after a given reference node. That
 * means the new node is inserted between the reference
 * node and the reference node's next sibling
 * @param {Object} elm Element
 * @param {Object] ref Reference node
 * @type Object
 */
Element.insertAfter = function(elm, ref) {
	if (elm = $(elm)) {
		if (ref.nextSibling)
			ref.parentNode.insertBefore(elm, ref.nextSibling);
		else
			ref.parentNode.appendChild(elm);
	}
	return elm;
};

/**
 * Retrieve an object containing left and top
 * offsets for a given element. This method
 * returns an object containing 2 properties:
 * x (left offset) and y (top offset)
 * @param {Object} elm Element
 * @type Object
 */
Element.getPosition = function(elm) {
	var elm = $(elm), p = {x: -1, y: -1};
	if (elm) {
		if (document.getBoxObjectFor) {
			var box = document.getBoxObjectFor(elm);
			var bl = parseInt(elm.getStyle('border-left-width'), 10);
			var bt = parseInt(elm.getStyle('border-top-width'), 10);
			p.x = box.x - (!isNaN(bl) ? bl : 0);
			p.y = box.y - (!isNaN(bt) ? bt : 0);
		} else {
			p.x = elm.offsetLeft || parseInt(elm.style.left.replace('px', '') || '0', 10);
			p.y = elm.offsetTop || parseInt(elm.style.top.replace('px', '') || '0', 10);
			var op = (elm.parentNode ? elm.offsetParent : null);
			while (op) {
				p.x += op.offsetLeft;
				p.y += op.offsetTop;
            	op = op.offsetParent;
			}
			if (PHP2Go.browser.opera || (PHP2Go.browser.khtml && elm.getStyle('position') == 'absolute')) {
				p.x -= document.body.offsetLeft;
				p.y -= document.body.offsetTop;
			}
		}
	}
	return p;
};

/**
 * Get the dimensions of a given element.
 * The results are returned as an object,
 * containing 2 properties: width and height
 * @param {Object} elm Element
 * @type Object
 */
Element.getDimensions = function(elm) {
	var elm = $(elm), d = {width: 0, height: 0};
	if (elm) {
		if (elm.getStyle('display') != 'none') {
			if (elm.offsetWidth && elm.offsetHeight) {
	        	d.width = elm.offsetWidth;
	        	d.height = elm.offsetHeight;
			} else if (elm.style && elm.style.pixelWidth && elm.style.pixelHeight) {
				d.width = elm.style.pixelWidth;
				d.height = elm.style.pixelHeight;
	    	}
		} else {
			elm.swapStyles({
				position: 'absolute', visibility: 'hidden', display: (elm.tagName.equalsIgnoreCase('div') ? 'block' : '')
			}, function() {
				d.width = elm.clientWidth;
				d.height = elm.clientHeight;
			});
		}
	}
	return d;
};

/**
 * Checks if the element is within a given pair of coordinates.
 * The coordinates must be objects containing x and y members:
 * {x: 10, y : 20}. p1 coordinate is the top-left corner
 * @param {Object} elm Element
 * @param {Object} p1 Top-left coordinate
 * @param {Object} p2 Bottom-right coordinate
 * @return Boolean
 */
Element.isWithin = function(elm, p1, p2) {
	if (elm = $(elm)) {
		var p = Element.getPosition(elm), d = Element.getDimensions(elm);
		var ex1 = p.x, ex2 = (p.x+d.width), ey1 = p.y, ey2 = (p.y+d.height);
		return (ex1<p2.x && ex2>p1.x && ey1<p2.y && ey2>p1.y);
	}
	return false;
};

/**
 * Gets a style property of a given element.
 * If the property is not found in the element's
 * style definition, the method tries to read it
 * using document's defaultView or currentStyle.
 * For better results, always provide property
 * name using CSS property declaration style, e.g.:
 * background-color, border-left-width, font-family
 * @param {Object} elm Element
 * @param {String} prop Property name
 * @type Object
 */
Element.getStyle = function(elm, prop) {
	elm = $(elm);
	if (elm && elm.style) {
		var d = document, camel = prop.camelize(), val = null;
		if (PHP2Go.browser.ie && (prop == 'float' || prop == 'cssFloat'))
			camel = 'styleFloat';
		else if (prop == 'float')
			camel = 'cssFloat';
		val = elm.style[camel];
		if (!val) {
			if (d.defaultView && d.defaultView.getComputedStyle) {
				var cs = d.defaultView.getComputedStyle(elm, null);
				(cs) && (val = cs.getPropertyValue(prop));
			} else if (elm.currentStyle) {
				val = elm.currentStyle[camel];
			}
		}
		if (PHP2Go.browser.opera && ['left', 'top', 'right', 'bottom'].contains(prop) && Element.getStyle(elm, 'position') == 'static')
			val = null;
		(val == 'auto') && (val = null);
		(prop == 'opacity' && val) && (val = parseFloat(val, 10));
	}
	return val;
};

/**
 * Set one or more style properties of an element
 * @param {Object} elm Element
 * @param {Object} prop Hash of properties or property name
 * @param {Object} value Property value
 * @type Object
 */
Element.setStyle = function(elm, prop, value) {
	if (elm = $(elm)) {
		var props = prop;
		if (typeof(prop) == 'string') {
			props = {};
			props[prop] = value;
		}
		for (var prop in props) {
			switch (prop) {
				case 'opacity' :
					elm.setOpacity(props[prop]);
					break;
				case 'width' :
				case 'height' :
					elm.style[prop.camelize()] = props[prop];
					if (PHP2Go.browser.ie && elm.getStyle('position') == 'absolute' && elm.getStyle('display') != 'none')
						WCH.update(elm);
					break;
				default :
					elm.style[prop.camelize()] = props[prop];
					break;
			}
		}
	}
	return elm;
};

/**
 * Swaps in/out style properties in order to call a given function
 * @param {Object} elm Element
 * @param {Object} props Style properties
 * @param {Function} func Function to be called
 * @type Object
 */
Element.swapStyles = function(elm, props, func) {
	if (elm = $(elm)) {
		for (var p in props) {
			elm.style['old'+p] = elm.style[p];
			elm.style[p.camelize()] = props[p];
		}
		func();
		for (var p in props) {
			elm.style[p.camelize()] = elm.style['old'+p];
			elm.style['old'+p] = null;
		}
	}
	return elm;
};

/**
 * Get an element's opacity level. The level is returned
 * as a decimal number between 0 and 1
 * @param {Object} elm Element
 * @type Number
 */
Element.getOpacity = function(elm) {
	if (elm = $(elm)) {
		var op = elm.getStyle('opacity');
		if (op)
			return parseFloat(op, 10);
		var re = new RegExp("alpha\(opacity=(.*)\)");
		if (op = re.exec(elm.getStyle('filter') || '') && op[1])
			return (parseFloat(op[1], 10) / 100);
		return 1.0;
	}
	return null;
};

/**
 * Set an element's opacity level. The opacity level
 * must be a decimal number between 0 and 1
 * @param {Object} elm Element
 * @param {Number} op Opacity level
 * @type Object
 */
Element.setOpacity = function(elm, op) {
	if (elm = $(elm)) {
		op = (isNaN(op) || op >= 1 ? null : (op < 0.00001 ? 0 : op));
		var s = elm.style;
		s['opacity'] = s['-moz-opacity'] = s['-khtml-opacity'] = op;
		if (PHP2Go.browser.ie) {
			// force layout on the element
			s['zoom'] = 1;
			// remove current alpha value
			s['filter'] = (elm.getStyle('filter') || '').replace(/alpha\([^\)]*\)/gi, '');
			// add new alpha value if not null
			s['filter'] += (op ? 'alpha(opacity=' + Math.round(op*100) + ')' : '');
		}
	}
	return elm;
};

/**
 * Return the element CSS class names.
 * This method returns an instance of the
 * CSSClasses class; through them it's possible
 * to query, add and remove CSS class names
 * @param {Object} elm Element
 * @type CSSClasses
 */
Element.classNames = function(elm) {
	if (elm = $(elm)) {
		if (!elm._classNames)
			elm._classNames = new CSSClasses(elm);
		return elm._classNames;
	}
	return null;
};

/**
 * Adds a CSS class on an element
 * @param {Object} elm Element
 * @param {String} cl CSS class
 * @type void
 */
Element.addClass = function(elm, cl) {
	if ((elm = $(elm)) && cl) {
		elm.removeClass(cl);
		elm.className = cl + ' ' + elm.className.trim();
	}
};

/**
 * Removes a CSS class from an element
 * @param {Object} elm Element
 * @param {String} cl CSS class
 * @type void
 */
Element.removeClass = function(elm, cl) {
	if ((elm = $(elm)) && cl) {
		var re = new RegExp("^"+cl+"\\b\\s*|\\s*\\b"+cl+"\\b", 'g');
		elm.className = elm.className.replace(re, '');
	}
};

/**
 * Verify if a given element is visible
 * @param {Object} elm Element
 * @type Boolean
 */
Element.isVisible = function(elm) {
	if (elm = $(elm)) {
		while (elm && elm != document) {
			if (elm.style.display == 'none' || elm.style.visibility == 'hidden')
				return false;
			elm = elm.parentNode;
		}
		return true;
	}
	return false;
};

/**
 * Shows all elements passed in the argument list.
 * The display style property is changed to "" or to
 * the old value stored in the element
 * @type void
 */
Element.show = function() {
	var item, arg = arguments;
	for (var i=0; i<arg.length; i++) {
		item = $(arg[i]);
		item.style.display = (item.tagName && item.tagName.equalsIgnoreCase('div') ? 'block' : '');
		if (PHP2Go.browser.ie && item.getStyle('position') == 'absolute')
			WCH.attach(item);
	}
};

/**
 * Hides all elements passed in the argument list.
 * Changes the display style property of the
 * elements to 'none'
 * @type void
 */
Element.hide = function() {
	var item, arg = arguments;
	for (var i=0; i<arg.length; i++) {
		item = $(arg[i]);
		item.style.display = 'none';
		if (PHP2Go.browser.ie && item.getStyle('position') == 'absolute')
			WCH.detach(item);
	}
};

/**
 * Alternates the display style property of one or
 * more elements from 'none' to the default value
 * @type void
 */
Element.toggleDisplay = function() {
	var item, arg = arguments;
	for (var i=0; i<arg.length; i++) {
		item = $(arg[i]);
		if (item.getStyle('display') == 'none')
			item.show();
		else
			item.hide();
	}
};

/**
 * Moves an element to a given x and y coordinates
 * @param {Object} elm Element
 * @param {Number} x X coordinate
 * @param {Number} y Y coordinate
 * @type Object
 */
Element.moveTo = function(elm, x, y) {
	if (elm = $(elm)) {
		elm.setStyle('left', x + 'px');
		elm.setStyle('top', y + 'px');
		if (PHP2Go.browser.ie && elm.getStyle('position') == 'absolute')
			WCH.update(elm, {position: {x: x, y: y}});
	}
	return elm;
};

/**
 * Resizes an element to given width and height values
 * @param {Object} elm Element
 * @param {Number} w Width
 * @param {Number} h Height
 * @type Object
 */
Element.resizeTo = function(elm, w, h) {
	if (elm = $(elm)) {
		elm.setStyle('width', w + 'px');
		elm.setStyle('height', h + 'px');
		if (PHP2Go.browser.ie && elm.getStyle('position') == 'absolute')
			WCH.update(elm, {dimensions: {width: w, height: h}});
	}
	return elm;
};

/**
 * Remove HTML contents of an element
 * @param {Object} elm Element
 * @param {Boolean} useDom Whether to use DOM or not
 * @type Object
 */
Element.clear = function(elm, useDom) {
	elm = $(elm), useDom = !!useDom;
	if (elm) {
		if (useDom) {
			while (elm.firstChild)
				elm.removeChild(elm.firstChild);
		} else {
			elm.innerHTML = '';
		}
	}
	return elm;
};

/**
 * Verify if a given element is empty
 * @param {Object} elm Element
 * @type Boolean
 */
Element.empty = function(elm) {
	if (elm = $(elm))
		return elm.innerHTML.empty();
	return true;
};

/**
 * Collects the contents of all text nodes inside a given element
 * @param {Object} elm Element
 * @type String
 */
Element.getInnerText = function(elm) {
	if (elm = $(elm)) {
		if (elm.innerText)
			return elm.innerText;
		var s = '', cs = elm.childNodes;
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
	}
};

/**
 * Set the HTML contents of a given element.
 * By setting 'useDom' to true, a temporary div
 * element will be created and its child nodes will
 * be copied to the target element.
 * @param {Object} elm Element
 * @param {Boolean} eval Whether to eval scripts. Defaults to false
 * @param {Boolean} useDom Whether to use DOM. Defaults to false
 * @type Object
 */
Element.update = function(elm, code, evalScripts, useDom) {
	elm = $(elm), code = String(code), evalScripts = !!evalScripts, useDom = !!useDom;
	if (elm) {
		if (code.empty()) {
			elm.clear(useDom);
		} else {
			var stripped = code.stripScripts();
			// special handling of table-related elements on IE
			if (PHP2Go.browser.ie && elm.tagName.match(/^(table|tbody|tr|td)$/i)) {
				var depth, div = $N('div');
				switch (elm.tagName.toLowerCase()) {
					case 'table' :
						div.innerHTML = '<table>' + stripped + '</table>';
						depth = 1;
						break;
					case 'tbody' :
						div.innerHTML = '<table><tbody>' + stripped + '</tbody></table>';
						depth = 2;
						break;
					case 'tr' :
						div.innerHTML = '<table><tbody><tr>' + stripped + '</tr></tbody></table>';
						depth = 3;
						break;
					case 'td' :
						div.innerHTML = '<table><tbody><tr><td>' + stripped + '</td></tr></tbody></table>';
						depth = 4;
						break;
				}
				while (elm.firstChild)
					elm.removeChild(elm.firstChild);
				for (var i=0; i<depth; i++)
					div = div.firstChild;
				for (var i=0; i<div.childNodes.length; i++)
					elm.appendChild(div.childNodes[i]);
			}
			// update contents using DOM
			else if (useDom) {
				var div = $N('div', null, {}, stripped);
				while (elm.firstChild)
					elm.removeChild(elm.firstChild);
				while (div.firstChild)
					elm.appendChild(div.removeChild(div.firstChild));
				delete div;
			}
			// default behaviour
			else {
				elm.innerHTML = stripped;
			}
			(evalScripts) && (code.evalScriptsDelayed());
		}
	}
	return elm;
};

/**
 * Inserts HTML code inside an element. The 'position' argument
 * allows to define where the HTML contents must be inserted :
 * "before" the element, on the "top" or on the "bottom" of the
 * element or "after" the element
 * @param {Object} elm Element
 * @param {String} code HTML code
 * @param {String} pos Insertion position. Defaults to "bottom"
 * @param {Boolean} eval Whether to eval scripts. Defaults to false
 * @type Object
 */
Element.insertHTML = function(elm, code, pos, evalScripts) {
	elm = $(elm), evalScripts = !!evalScripts;
	var html = String(code).stripScripts();
	if (elm) {
		if (elm.insertAdjacentHTML) {
			var map = {
				'before' : 'BeforeBegin',
				'top' : 'AfterBegin',
				'bottom' : 'BeforeEnd',
				'after' : 'AfterEnd'
			};
			elm.insertAdjacentHTML(map[pos] || 'BeforeEnd', html);
		} else {
			var fgm, rng = elm.ownerDocument.createRange();
			switch (pos) {
				case 'before' :
					rng.setStartBefore(elm);
					fgm = rng.createContextualFragment(html);
					elm.parentNode.insertBefore(fgm, elm);
					break;
				case 'top' :
					rng.selectNodeContents(elm);
					rng.collapse(true);
					fgm = rng.createContextualFragment(html);
					elm.insertBefore(fgm, elm.firstChild);
					break;
				case 'after' :
					rng.setStartAfter(elm);
					fgm = rng.createContextualFragment(html);
					elm.parentNode.insertBefore(fgm, elm.nextSibling);
					break;
				default :
					rng.selectNodeContents(elm);
					rng.collapse(true);
					fgm = rng.createContextualFragment(html);
					elm.appendChild(fgm);
					break;
			}
		}
		(evalScripts) && (code.evalScriptsDelayed());
	}
	return elm;
};

/**
 * Replace an element with the given HTML code
 * @param {Object} elm Element
 * @param {String} code HTML code to replace the element
 * @param {Boolean} eval Whether to eval scripts. Defaults to false
 * @type void
 */
Element.replace = function(elm, code, evalScripts) {
	elm = $(elm), evalScripts = !!evalScripts;
	var html = code.stripScripts();
	if (elm) {
		if (elm.outerHTML) {
			elm.outerHTML = html;
		} else {
			var rng = document.createRange();
			rng.selectNodeContents(elm);
			elm.parentNode.replaceChild(rng.createContextualFragment(html), elm);
		}
		(evalScripts) && (code.evalScriptsDelayed());
	}
};

/**
 * Remove the element from its parent node
 * @param {Object} elm Element
 * @type Object
 */
Element.remove = function(elm) {
	elm = $(elm);
	if (elm && elm.parentNode)
		return elm.parentNode.removeChild(elm);
};

/**
 * Define HTMLElement if it's not a valid identifier,
 * and add all Element methods to its prototype
 */
if (!PHP2Go.nativeElementExtension && document.createElement('div').__proto__) {
	window.HTMLElement = {};
	window.HTMLElement.prototype = document.createElement('div').__proto__;
	PHP2Go.nativeElementExtension = true;
}
if (PHP2Go.nativeElementExtension)
	Element.extend(HTMLElement.prototype);

/**
 * Document extensions
 */
if (!document.getElementsByClassName) {
	document.getElementsByClassName = function(clsName, tagName) {
		return Element.getElementsByClassName(document, clsName, tagName);
	};
}
if (document.evaluate) {
	document.getElementsByXPath = function(expr, parent) {
		var res = [], qry = document.evaluate(expr, $(parent) || document, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null);
		for (var i=0,s=qry.snapshotLength; i<s; i++)
			res.push($E(qry.snapshotItem(i)));
		return res;
	};
}


/**
 * This class provide methods to query, add or remove
 * CSS class names from the className property of an element
 * @constructor
 * @base Collection
 */
CSSClasses = function(elm) {
	/**
	 * Holds the HTML element used by the class
	 * @type Object
	 */
	this.elm = $(elm);
	/**
	 * Implements the collection interface by
	 * applying an iterator function on the
	 * element's class names
	 * @type void
	 * @private
	 */
	this.each = function(iterator) {
		var list = this.elm.className.trim().split(/\s+/);
		for (var i=0; i<list.length; i++)
			iterator(list[i]);
	};
	/**
	 * Verifies if the element contains a given class name
	 * @param {String} cl Class name
	 * @type Boolean
	 */
	this.has = function(cl) {
		var re = new RegExp("\s?"+cl+"\s?", 'i');
		return re.test(this.elm.className);
	};
	/**
	 * Set the entire value of the className property
	 * @param {String} clsNames Class names
	 * @type void
	 */
	this.set = function(clsNames) {
		this.elm.className = clsNames;
	};
	/**
	 * Add one or more class names
	 * to the element classes
	 * @type void
	 */
	this.add = function() {
		var a = arguments, c = this.elm.className;
		for (var i=0; i<a.length; i++) {
			(a[i]) && (c = a[i] + ' ' + c.trim());
		}
		this.set(c.trim());
	};
	/**
	 * Remove one or more class names
	 * from the element classes
	 * @type void
	 */
	this.remove = function() {
		var re, a = arguments, c = this.elm.className;
		for (var i=0; i<a.length; i++) {
			if (a[i]) {
				re = new RegExp("^"+a[i]+"\\b\\s*|\\s*\\b"+a[i]+"\\b", 'g');
				c = c.replace(re, '');
			}
		}
		this.set(c.trim());
	};
	/**
	 * Toggle the CSS class of the element.
	 * The provided parameter is the alternative
	 * value. The primary value is the original
	 * value of "class" attribute in the element
	 * @param {String} Alternative CSS class
	 * @type void
	 */
	this.toggle = function(alt) {
		(this.has(alt)) ? (this.remove(alt)) : (this.add(alt));
	};
	/**
	 * Builds a string representation of the object
	 * @type String
	 */
	this.toString = function() {
		return this.elm.className;
	};
};
Object.extend(CSSClasses.prototype, Collection);

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
			var wch, pos = elm.getPosition(), dim = elm.getDimensions();
			if (!elm.wchIframe) {
				wch = elm.wchIframe = $N('iframe', elm.parentNode, {position: 'absolute'});
				if (PHP2Go.browser.ie6)
					wch.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0);';
				// fix low z-indexes
				if (elm.getStyle('z-index') <= 2)
					elm.setStyle('z-index', 1000);
			} else {
				wch = elm.wchIframe;
			}
			wch.style.display = 'block';
			wch.style.width = dim.width;
			wch.style.height = dim.height;
			wch.style.top = pos.y;
			wch.style.left = pos.x;
			wch.style.zIndex = elm.getStyle('z-index') - 1;
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
				var pos = opts.position || elm.getPosition();
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
 * @ignore
 */
Event.cache = [];

/**
 * Register a function to be executed when
 * the Document Object Model is available. In
 * the worst case, the function will be called
 * when window.onload is fired
 * @param {Function} fn Function to be executed
 * @type void
 */
Event.onDOMReady = function(fn) {
	if (!this.queue) {
		var b = PHP2Go.browser;
		var self = this, d = document;
		var run = function() {
			if (!arguments.callee.done) {
				arguments.callee.done = true;
				self.queue.walk(function(item, idx) {
					item();
				});
				self.queue = null;
				// mozilla, opera9
				if (d.removeEventListener)
					d.removeEventListener('DOMContentLoaded', run, false);
				// msie
				var defer = $('defer_script');
				if (defer)
					defer.remove();
				// safari, opera8
				if (self.timer) {
					clearInterval(self.timer);
					self.timer = null;
				}
			}
		};
		// mozilla, opera9
		if (d.addEventListener)
			d.addEventListener('DOMContentLoaded', run, false);
		// msie
		d.write("<scr"+"ipt id=defer_script defer src=javascript:void(0)><\/scr"+"ipt>");
		var defer = $('defer_script');
		if (defer) {
			defer.onreadystatechange = function() {
				if (this.readyState == "complete")
					run();
			};
			defer.onreadystatechange();
			defer = null;
		}
		// safari, opera8
		if (b.khtml || b.opera) {
			this.timer = setInterval(function() {
				if (/loaded|complete/.test(document.readyState)) {
					run();
				}
			}, 10);
		}
		// other browsers
		this.addLoadListener(run);
		this.queue = [];
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
 * Adds an event handler function to a given element and event type
 * @param {Object} elm Element
 * @param {String} type Event type
 * @param {Function} fn Handler function
 * @param {Boolean} capt Use capture
 * @type void
 */
Event.addListener = function(elm, type, fn, capt) {
	if (elm = $(elm)) {
		type = type.replace('/^on/i', '').toLowerCase();
		if (type == 'keypress' && PHP2Go.browser.khtml)
			type = 'keydown';
		capt = !!capt;
		if (elm.addEventListener)
			elm.addEventListener(type, fn, capt);
		else if (elm.attachEvent)
			elm.attachEvent('on' + type, fn);
		else
			elm['on' + type] = fn;
		Event.cache.push([elm, type, fn, capt]);
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
		type = type.replace('/^on/i', '').toLowerCase();
		if (type == 'keypress' && PHP2Go.browser.khtml)
			type = 'keydown';
		capt = !!capt;
		if (elm.removeEventListener)
			elm.removeEventListener(type, fn, capt);
		else if (elm.detachEvent)
			elm.detachEvent('on' + type, fn);
		else
			elm['on' + type] = null;
	}
};

/**
 * The event listeners are cached, so that they can be
 * detached when the window is unloaded. This is necessary
 * to avoid memory leaks in MS Internet Explorer
 * @ignore
 */
Event.flushCache = function() {
	Event.cache.walk(function(item, idx) {
		Event.removeListener.apply(this, item);
	});
	delete Event.cache;
	try {
		window.onload = $EF;
		window.onunload = $EF;
	} catch(e) {}
};
if (PHP2Go.browser.ie)
	Event.addListener(window, 'unload', Event.flushCache);

/**
 * Returns the element that originated the event
 * @type Object
 */
Event.prototype.element = function() {
	var elm = (this.target || this.srcElement);
	if (elm.nodeType) {
		while (elm.nodeType != 1) elm = elm.parentNode;
	}
	return elm;
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
	elm = $(elm);
	var related = this.relatedTarget;
	if (!related) {
		if (this.type == 'mouseout')
			related = this.toElement;
		else if (this.type == 'mouseover')
			related = this.fromElement;
	}
	return (related && (related == elm || $(related).isChildOf(elm)));
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

if (!Event.prototype.preventDefault) {
	/**
	 * Implementation of the preventDefault method
	 * for browsers that doesn't support it
	 * @type void
	 */
	Event.prototype.preventDefault = function() {
		this.returnValue = false;
	};
}

if (!Event.prototype.stopPropagation) {
	/**
	 * Implementation of the stopPropagation method
	 * for browsers that doesn't support it
	 * @type void
	 */
	Event.prototype.stopPropagation = function() {
		this.cancelBubble = true;
	};
}

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
	if (!e || PHP2Go.nativeEventExtension)
		return e;
	Object.extend(e, Event.prototype);
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