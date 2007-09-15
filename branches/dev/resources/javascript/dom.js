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
	for (var p in Element) {
		var v = Element[p];
		if (Object.isFunc(v) && p != 'extend' && !(p in obj))
			obj[p] = v.methodize();
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
 * Recursively sums the values of a given property
 * on all ancestors of a given node
 * @param {Object} elm Element
 * @param {String} prop Property name
 * @type Number
 */
Element.recursivelySum = function(elm, prop) {
	var res = 0;
	if (elm = $(elm)) {
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
	}
	return res;
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

if (document.evaluate) {
	/**
	 * Search for all elements that match the given set of class names
	 * @param {Object} elm Base element
	 * @param {String} clsName CSS class names (one or more, space separated)
	 * @type Array
	 */
	Element.getElementsByClassName = function(elm, clsNames) {
		clsNames = (clsNames + '').trim();
		if ((elm = $(elm)) && !clsNames.empty()) {
			clsNames = clsNames.split(/\s+/);
			var cond = clsNames.valid(function(item, idx) {
				return (item.empty() ? null : "[contains(concat(' ', @class, ' '), ' %1 ')]".assignAll(item));
			}).join('');
			return (cond ? document.getElementsByXPath('.//*' + cond, elm) : []);
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
	Element.getElementsByClassName = function(elm, clsNames) {
		clsNames = (clsNames + '').trim();
		if ((elm = $(elm)) && !clsNames.empty()) {
			var clsStr = ' ' + clsNames + ' ', clsList = clsNames.split(/\s+/);
			var res = [], elms = elm.getElementsByTagName('*');
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
 * Define document.getElementsByClassName only when undefined
 */
if (!document.getElementsByClassName) {
	document.getElementsByClassName = function(clsNames) {
		return Element.getElementsByClassName(document, clsNames);
	};
}

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
 * Collects element's child nodes, skipping
 * all text nodes. Extends and returns all
 * child nodes in an array
 * @param {Object} elm Base element
 * @type Array
 */
Element.getChildNodes = function(elm) {
	var res = [], fc = null;
	if (elm = $(elm) && (fc = elm.firstChild)) {
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
		return rc('previousSibling').reverse().concat(rc('nextSibling'));
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
 * Retrieve an object containing left and top
 * offsets for a given element. This method
 * returns an object containing 2 properties:
 * x (left offset) and y (top offset)
 * @param {Object} elm Element
 * @param {Number} tbt Box type (0, 1, 2 or 3)
 * @type Object
 */
Element.getPosition = function(elm, tbt) {
	var elm = $(elm), p = {x: 0, y: 0};
	if (elm) {
		var b = PHP2Go.browser, db = document.body || document.documentElement;
		// native and target box type
		var nbt = 2, tbt = Object.ifUndef(tbt, 0);
		if (elm.getBoundingClientRect) {
			var bcr = elm.getBoundingClientRect();
			p.x = bcr.left - 2;
			p.y = bcr.top - 2;
		} else if (document.getBoxObjectFor) {
			nbt = 1;
			var box = document.getBoxObjectFor(elm);
			p.x = box.x - elm.recursivelySum('scrollLeft');
			p.y = box.y - elm.recursivelySum('scrollTop');
		} else if (elm.offsetParent) {
			if (elm.parentNode != db) {
				p.x -= Element.recursivelySum((b.opera ? db : elm), 'scrollLeft');
				p.y -= Element.recursivelySum((b.opera ? db : elm), 'scrollTop');
			}
			var cur = elm, end = (b.safari && elm.style.getPropertyValue('position') == 'absolute' && elm.parentNode == db ? db : db.parentNode);
			do {
				var l = elm.offsetLeft;
				if (!b.opera || l > 0)
					p.x += (isNaN(l) ? 0 : l);
				var t = elm.offsetTop;
				p.y += (isNaN(t) ? 0 : t);
				cur = cur.offsetParent;
			} while (cur != end && cur != null);
		} else if (elm.x && elm.y) {
			p.x += (isNaN(elm.x) ? 0 : elm.x);
			p.y += (isNan(elm.y) ? 0 : elm.y);
		}
		var tp = Object.toPixels;
		var extents = ['padding', 'border', 'margin'];
		if (nbt > tbt) {
			for (var i=tbt; i<nbt; i++) {
				p.x -= tp(elm.getComputedStyle(extents[i] + '-left'));
				p.y -= tp(elm.getComputedStyle(extents[i] + '-top'));
			}
		} else if (tbt > nbt) {
			for (var i=tbt; i>nbt; --i) {
				p.x -= tp(elm.getComputedStyle(extents[i-1] + '-left'));
				p.y -= tp(elm.getComputedStyle(extents[i-1] + '-top'));
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
		if (elm.getComputedStyle('display') != 'none') {
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
 * Get an element's border box
 * @param {Object} elm Element
 * @type Object
 */
Element.getBorderBox = function(elm) {	
	if (elm = $(elm)) {
		var self = Element.getBorderBox;
		self.getSide = self.getSide || function(node, side) {
			return (node.getComputedStyle('border-' + side + '-style') == 'none' ? 0 : Object.toPixels(node.getComputedStyle('border-' + side + '-width')));			
		};
		return {
			width: self.getSide(elm, 'left') + self.getSide(elm, 'right'),
			height: self.getSide(elm, 'top') + self.getSide(elm, 'bottom')
		};
	}
	return {width: 0, height: 0};
};

/**
 * Get an element's padding box
 * @param {Object} elm Element
 * @type Object
 */
Element.getPaddingBox = function(elm) {
	if (elm = $(elm)) {
		var tp = Object.toPixels;
		return {
			width: tp(elm.getComputedStyle('padding-left')) + tp(elm.getComputedStyle('padding-right')),
			height: tp(elm.getComputedStyle('padding-top')) + tp(elm.getComputedStyle('padding-bottom'))
		};
	}
	return {width: 0, height: 0};
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

if (PHP2Go.browser.ie) {
	/**
	 * Retrieves the computed value of a given style property
	 * @param {Object} elm Element
	 * @param {String} prop Property name
	 * @type String
	 */
	Element.getComputedStyle = function(elm, prop) {
		elm = $(elm);
		if (elm && elm.currentStyle)
			return elm.currentStyle[prop.camelize()];
		return null;
	};
	/**
	 * Gets all computed styles of a given element
	 * @param {Object} elm Element
	 * @type Object
	 */
	Element.getComputedStyles = function(elm) {
		if (elm = $(elm))
			return elm.currentStyle;
		return null;
	};
} else {
	/**
	 * @ignore
	 */
	Element.getComputedStyle = function(elm, prop) {
		var d = document, cs, elm = $(elm);
		if (elm && d.defaultView && d.defaultView.getComputedStyle) {
			if (cs = d.defaultView.getComputedStyle(elm, null))
				return cs[prop.camelize()];
		}
		return null;
	};
	/**
	 * @ignore
	 */
	Element.getComputedStyles = function(elm) {
		var d = document, elm = $(elm);
		if (elm && d.defaultView && d.defaultView.getComputedStyle)
			return d.defaultView.getComputedStyle(elm, null);
		return null;
	};
}

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
	var val = null, elm = $(elm);
	if (elm && elm.style) {
		var d = document, camel = prop.camelize();
		if (PHP2Go.browser.ie && (prop == 'float' || prop == 'cssFloat'))
			camel = 'styleFloat';
		else if (prop == 'float')
			camel = 'cssFloat';
		val = elm.style[camel];
		if (!val)
			val = elm.getComputedStyle(prop);
		if (PHP2Go.browser.opera && ['left', 'top', 'right', 'bottom'].contains(prop) && elm.getComputedStyle('position') == 'static')
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
		if (Object.isString(prop)) {
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
					if (PHP2Go.browser.ie && !PHP2Go.browser.ie7 && elm.getComputedStyle('position') == 'absolute' && elm.getComputedStyle('display') != 'none')
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
		var op = 1;
		if (PHP2Go.browser.ie) {
			var mt = [];
			if (mt = Element.getOpacity.re.exec(elm.getStyle('filter') || ''))
				op = (parseFloat(mt[1], 10) / 100);
		} else {
			op = parseFloat(elm.style.opacity || elm.style.MozOpacity || elm.style.KhtmlOpacity || 1, 10);
		}
		return (op >= 0.999999 ? 1 : op);
	}
	return null;
};
/**
 * @ignore
 */
Element.getOpacity.re = /alpha\(opacity=(.*)\)/;

/**
 * Set an element's opacity level. The opacity level
 * must be a decimal number between 0 and 1
 * @param {Object} elm Element
 * @param {Number} op Opacity level
 * @type Object
 */
Element.setOpacity = function(elm, op) {
	if (elm = $(elm)) {
		op = (isNaN(op) || op >= 1 ? 1 : (op < 0.00001 ? 0 : op));
		var s = elm.style, b = PHP2Go.browser;
		if (b.ie) {
			s.zoom = 1;
			s.filter = (elm.getStyle('filter') || '').replace(Element.setOpacity.re, '');
			s.filter += (op ? 'alpha(opacity=' + Math.round(op*100) + ')' : '');
		} else if (b.mozilla) {
			s.opacity = s.MozOpacity = op;
		} else if (b.khtml) {
			s.opacity = s.KhtmlOpacity = op;
		} else {
			s.opacity = op;
		}
	}
	return elm;
};
/**
 * @ignore
 */
Element.setOpacity.re = /alpha\([^\)]*\)/gi;

/**
 * Checks if the element contains a given CSS class
 * @param {Object} elm Element
 * @param {String} cl CSS class
 * @type Boolean
 */
Element.hasClass = function(elm, cl) {
	if (elm = $(elm)) {
		var ec = elm.className, cl = cl + '';
		return (
			(ec.length > 0 && ec == cl) ||
			(ec.match(new RegExp("(^|\\s)" + cl + "(\\s|$)")))
		);
	}
};

/**
 * Adds a CSS class on an element
 * @param {Object} elm Element
 * @param {String} cl CSS class
 * @type void
 */
Element.addClass = function(elm, cl) {
	if (elm = $(elm)) {
		cl += '';
		if (!elm.hasClass(cl)) {
			elm.className += (elm.className ? ' ' : '') + cl;
		}
	}
};

/**
 * Removes a CSS class from an element
 * @param {Object} elm Element
 * @param {String} cl CSS class
 * @type void
 */
Element.removeClass = function(elm, cl) {
	if (elm = $(elm)) {
		var re = new RegExp("^"+cl+"\\b\\s*|\\s*\\b"+cl+"\\b", 'g'), cl = cl + '';
		elm.className = elm.className.replace(re, '');
	}
};

/**
 * Adds/removes a CSS class on an element
 * @param {Object} elm Element
 * @param {String} cl CSS class
 * @type void
 */
Element.toggleClass = function(elm, cl) {
	if (elm = $(elm)) {
		cl += '';
		elm[elm.hasClass(cl) ? 'removeClass' : 'addClass'](cl);
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
 * Shows an element
 * @param {Object} elm Element
 * @type void
 */
Element.show = function(elm) {
	if (elm = $(elm)) {
		elm.style.display = (elm.tagName.equalsIgnoreCase('div') ? 'block' : '');
		if (PHP2Go.browser.wch && elm.getComputedStyle('position') == 'absolute')
			WCH.attach(elm);
	}
};

/**
 * Hides an element
 * @param {Object} elm Element
 * @type void
 */
Element.hide = function(elm) {
	if (elm = $(elm)) {
		elm.style.display = 'none';
		if (PHP2Go.browser.wch && elm.getComputedStyle('position') == 'absolute')
			WCH.detach(elm);
	}
};

/**
 * Toggles the display property of an element
 * @param {Object} elm Element
 * @type void
 */
Element.toggleDisplay = function(elm) {
	if (elm = $(elm)) {
		if (elm.style.display == 'none')
			elm.show();
		else
			elm.hide();
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
		if (PHP2Go.browser.wch && elm.getComputedStyle('position') == 'absolute')
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
		if (PHP2Go.browser.wch && elm.getComputedStyle('position') == 'absolute')
			WCH.update(elm, {dimensions: {width: w, height: h}});
	}
	return elm;
};

/**
 * Scrolls the window to the element's position
 * @param {Object} elm Element
 * @type Object
 */
Element.scrollTo = function(elm) {
	if (elm = $(elm)) {
		var pos = elm.getPosition();
		window.scrollTo(pos.x, pos.y);
	}
	return elm;
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
 * Inserts HTML code inside an element. The 'position' argument
 * allows to define where the HTML contents must be inserted :
 * "before" the element, on the "top" or on the "bottom" of the
 * element or "after" the element
 * @param {Object} elm Element
 * @param {String} ins HTML or element to insert
 * @param {String} pos Insertion position. Defaults to "bottom"
 * @param {Boolean} eval Whether to eval scripts. Defaults to false
 * @type Object
 */
Element.insert = function(elm, ins, position, evalScripts) {
	elm = $(elm), evalScripts = !!evalScripts, position = position || 'bottom';
	if (elm) {
		var pos = Element.insertion[position];
		if (pos) {
			if (Object.isElement(ins)) {
				pos.insert(elm, ins);
				return elm;
			}
			ins = String(ins);
			if (!document.createRange || PHP2Go.browser.opera) {
				var tag = elm.tagName.toUpperCase();
				var tran = Element.translation;
				if (tag in tran.tags) {
					var frag = tran.createFromHTML(tag, ins.stripScripts());
					(position == 'top' || position == 'bottom') && (frag.reverse());
					frag.walk(function(item, idx) {
						pos.insert(elm, item);
					});
				} else {
					elm.insertAdjacentHTML(pos.adjacency, ins.stripScripts());
				}
			} else {
				var rng = elm.ownerDocument.createRange();
				pos.initializeRange(elm, rng);
				pos.insert(elm, rng.createContextualFragment(ins.stripScripts()));
			}
			(evalScripts) && (ins.evalScriptsDelayed());
		}
	}
	return elm;
};

/**
 * Set the HTML contents of a given element.
 * By setting 'useDom' to true, a temporary div
 * element will be created and its child nodes will
 * be copied to the target element.
 * @param {Object} elm Element
 * @param {Object} upd HTML or element to update with
 * @param {Boolean} evalScripts Whether to eval scripts. Defaults to false
 * @param {Boolean} useDom Whether to use DOM. Defaults to false
 * @type Object
 */
Element.update = function(elm, upd, evalScripts, useDom) {
	elm = $(elm), evalScripts = !!evalScripts, useDom = !!useDom;
	if (elm) {
		if (Object.isElement(upd))
			return elm.clear().insert(upd);
		upd = String(upd);
		if (upd.empty())
			return elm.clear(useDom);
		var tag = elm.tagName.toUpperCase();
		var tran = Element.translation;
		if (PHP2Go.browser.ie && tag in tran.tags) {
			while (elm.firstChild)
				elm.removeChild(elm.firstChild);
			var frag = tran.createFromHTML(tag, upd.stripScripts());
			frag.walk(function(item, idx) {
				elm.appendChild(item);
			});
		} else if (useDom) {
			var div = document.createElement('div');
			div.innerHTML = upd.stripScripts();
			while (elm.firstChild)
				elm.removeChild(elm.firstChild);
			while (div.firstChild)
				elm.appendChild(div.removeChild(div.firstChild));
			delete div;
		} else {
			elm.innerHTML = upd.stripScripts();
		}
		(evalScripts) && (upd.evalScriptsDelayed());
	}
	return elm;
};

/**
 * Replace an element with the given HTML code
 * @param {Object} elm Element
 * @param {Object} rep Replacement code or element
 * @param {Boolean} eval Whether to eval scripts. Defaults to false
 * @type void
 */
Element.replace = function(elm, rep, evalScripts) {
	elm = $(elm), evalScripts = !!evalScripts;
	if (elm) {
		if (Object.isElement(rep)) {
			elm.parentNode.replaceChild(rep, elm);
			return elm;
		}
		rep = String(rep);
		if (elm.outerHTML) {
			var tag = elm.tagName.toUpperCase();
			var tran = Element.translation;
			if (tag in tran.tags) {
				var parent = elm.parentNode;
				var next = elm.nextSibling;
				while (next && !Object.isElement(next))
					next = next.nextSibling;
				var frag = tran.createFromHTML(tag, rep.stripScripts());
				parent.removeChild(elm);
				if (next)
					frag.walk(function(item, idx) { parent.insertBefore(item, next); });
				else
					frag.walk(function(item, idx) { parent.appendChild(item); });
			} else {
				elm.outerHTML = rep.stripScripts();
			}
		} else {
			var rng = elm.ownerDocument.createRange();
			rng.selectNode(elm);
			elm.parentNode.replaceChild(rng.createContextualFragment(rep.stripScripts()), elm);
		}
		(evalScripts) && (rep.evalScriptsDelayed());
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
			var zi = elm.getComputedStyle('z-index');
			if (!elm.wchIframe) {
				wch = elm.wchIframe = $N('iframe', elm.parentNode, {position: 'absolute'});
				if (PHP2Go.browser.ie6)
					wch.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0);';
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
				for (var i=0; i<self.queue.length; i++)
					self.queue[i]();
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
		// initialize queue
		this.queue = [];
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

if (window.attachEvent) {
	/**
	 * @ignore
	 */
	function flushCache() {
		Event.cache.walk(function(item, idx) {
			Event.removeListener.apply(this, item);
		});
		delete Event.cache;
		try {
			window.onload = $EF;
			window.onunload = $EF;
		} catch(e) { }
	}
	window.attachEvent('onunload', flushCache);
}

PHP2Go.included[PHP2Go.baseUrl + 'dom.js'] = true;

}