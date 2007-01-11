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
 * This file contains the InputMask and Mask classes. Both are used together to
 * apply masks on form fields. This file also contain a set of predefined masks,
 * used by PHP2Go forms API (DigitMask, IntegerMask, FloatMask, ...)
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'inputmask.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'form.js');

/**
 * The InputMask class is used to apply a mask on
 * a form field. The class contains a set of event
 * handlers that interact with an instance of the
 * {@link Mask} class to validate and format the data
 * that is entered in the associated text input
 * @class InputMask
 * @param {Object} fld Field reference
 * @param {Object} mask Mask instance
 */
InputMask = function(fld, mask) {
	/**
	 * Form field associated with the mask
	 * @type Object
	 */
	this.fld = fld;
	/**
	 * Active mask object
	 * @type Mask
	 */
	this.mask = mask;
	/**
	 * Reference to the FieldSelection singleton
	 * @type FieldSelection
	 */
	this.fs = FieldSelection;
	/**
	 * Used to catch the field's caret position
	 * @type Number
	 */
	this.caret = 0;
	/**
	 * Used to hold the field's selection size
	 * @type Number
	 */
	this.selSize = 0;
	/**
	 * Last field value
	 * @type String
	 */
	this.lastValue = this.fld.value;
	/**
	 * Used to indicate chars that must be ignored
	 * @type Boolean
	 */
	this.ignore = false;
	/**
	 * Used to indicate chars that might be accepted
	 * @type Boolean
	 */
	this.valid = true;
	/**
	 * Used to control if a key is being pressed
	 * @type Boolean
	 */
	this.pressing = false;
	/**
	 * Used to indicate a paste keyboard command
	 * @type Boolean
	 */
	this.paste = false;
	/**
	 * Used to catch ctrl key
	 * @type Boolean
	 */
	this.ctrl = false;
	/**
	 * Used to catch shift key
	 * @type Boolean
	 */
	this.shift = false;
	if (this.mask) {
		this.addListeners();
		if (maxLength = this.mask.getMaxLength())
			this.fld.maxLength = maxLength;
	}
};

/**
 * Set of key codes to be ignored: pgup (33), pgdown (34), end (35),
 * home (36), left (37), up (38), right (39), down (40), ins (45),
 * del (127, konqueror), 4098 (shift+tab, konqueror)
 * @type String
 */
InputMask.ignoreCodes = '#33#34#35#36#37#38#39#40#45#127#4098#';

/**
 * Sets up an InputMask instance in a given field
 * @param {Object} fld Field reference or id
 * @param {Object} mask Mask instance or mask expression that must be parsed
 * @type void
 */
InputMask.setup = function(fld, mask) {
	fld = $(fld);
	if (fld) {
		if (mask instanceof Mask)
			fld.inputMask = new InputMask(fld, mask);
		else
			fld.inputMask = new InputMask(fld, Mask.fromExpression(mask));
	}
};

/**
 * Register all event listeners in the field
 * @type void
 */
InputMask.prototype.addListeners = function() {
	var f = this.fld, self = this;
	Event.addListener(f, 'keydown', this.keyDownHandler.bind(this));
	Event.addListener(f, 'keypress', this.keyPressHandler.bind(this));
	Event.addListener(f, 'keyup', this.keyUpHandler.bind(this));
	Event.addListener(f, 'blur', this.blurHandler.bind(this));
	if (f.onpaste === null) {
		/**
		 * call onpaste handler only if not pressing.
		 * keyboard paste commands will be handled by keyUpHandler
		 */
		Event.addListener(f, 'paste', function(e) {
			setTimeout(function() {
				if (!self.pressing)
					self.pasteHandler(e);
			}, 10);
		});
	} else {
		/**
		 * call oninput handler only if not pressing.
		 * keyboard paste commands will be handled by keyUpHandler
		 */
		Event.addListener(f, 'input', function(e) {
			if (!self.pressing)
				self.pasteHandler(e);
		});
	}
};

/**
 * The keydown handler implements the first phase of the
 * masking process: filters the keys that must be ignored,
 * determine if the keystroke is a paste combination and
 * retrieves the current caret position and selection size
 * @param {Event} e Event instance
 * @type void
 */
InputMask.prototype.keyDownHandler = function(e) {
	var k = $K(e);
	// toggle pressing flag on
	this.pressing = true;
	// store caret and selection size
	this.caret = this.fs.getCaret(this.fld);
	this.selSize = this.fs.getRange(this.fld).size;
	// store last value
	this.lastValue = this.fld.value;
	// detect paste (ctrl+v, shift+ins)
	this.paste = ((this.ctrl && k == 86) || (this.shift && k == 45));
	// detect ignore keys
	this.ignore = (!this.paste && (k < 32 || (k >= 112 && k <= 123) || InputMask.ignoreCodes.indexOf('#'+k+'#') != -1 || (k == 46 && !PHP2Go.browser.opera && !PHP2Go.browser.khtml)));
	// call mask's keydown handler
	this.valid = this.mask.onKeyDown(this.fld, this.caret, k);
	// toggle ctrl/shift flags on
	(k == 17) && (this.ctrl = true);
	(k == 16) && (this.shift = true);
};

/**
 * The keypress handler queries the field mask to determine
 * if the pressed char must be accepted. If the mask rejects
 * the char, the event returns false
 * @param {Event} e Event instance
 * @type void
 */
InputMask.prototype.keyPressHandler = function(e) {
	var e = e || window.event;
	var k = $K(e);
	var c = String.fromCharCode(k);
	if (this.valid) {
		// initialize valid flag, if not previously flagged as false
		this.valid = (this.ignore || e.ctrlKey);
		// validate the char against the mask
		if (!this.valid)
			this.valid = this.mask.accept(c, this.caret, this.selSize, this.fld.maxLength);
	}
	// stop event if it's an invalid char and if it's not a paste combo
	if (!this.valid && !this.paste)
		(e.preventDefault ? e.preventDefault() : e.returnValue = false);
};

/**
 * The keyup handler updates the field value by applying
 * the mask format if the value changed, if a valid char was
 * entered or if a paste command was executed
 * @param {Event} e Event instance
 * @type void
 */
InputMask.prototype.keyUpHandler = function(e) {
	var k = $K(e), f = this.fld, c = this.caret;
	// toggle pressing flag off
	this.pressing = false;
	// apply mask format if value changed
	if (f.value != this.lastValue || !this.ignore)
		this.update(e || window.event);
	// toggle ctrl/shift flags off
	(k == 17) && (this.ctrl = false);
	(k == 16) && (this.shift = false);
	// set caret position
	var newCaret = (this.ignore || !this.valid ? false : this.mask.getCaretPosition(this.lastValue, f.value, c, k));
	if (newCaret !== false) {
		this.fs.setCaret(f, newCaret);
	} else if (c < f.value.length) {
		// keep caret position for delete
		if ((k == 46 && !PHP2Go.browser.opera && !PHP2Go.browser.khtml) || (k == 127 && PHP2Go.browser.khtml))
			this.fs.setCaret(f, c);
		// go back 1 position for bksp
		else if (k == 8)
			this.fs.setCaret(f, c-1);
		// opera fails when mask automatically inserts literals
		else if (PHP2Go.browser.opera && c == this.lastValue.length && this.valid)
			this.fs.setCaret(f, f.value.length);
	}
	return true;
};

/**
 * The paste handler is used to handle onpaste (IE) and
 * oninput (Gecko) events, updating the field's value
 * by applying the mask format
 * @param {Event} e Event instance
 * @type void
 */
InputMask.prototype.pasteHandler = function(e) {
	this.update(e || window.event);
};

/**
 * The blur handler also calls the {@link InputMask#update},
 * validating the field's value once more against the mask
 * @param {Event} e Event instance
 * @type void
 */
InputMask.prototype.blurHandler = function(e) {
	this.update(e || window.event, true);
};

/**
 * Runs the format method of the field mask. It's called from inside
 * {@link InputMask#keyUpHandler}, {@link InputMask#pasteHandler} and
 * {@link InputMask#blurHandler}
 * @param {Event} e Event instance, when called from the event handlers
 * @param {Boolean} isBlur Whether the update method was called from the blur handler
 * @type void
 */
InputMask.prototype.update = function(e, isBlur) {
	isBlur = !!isBlur;
	// apply the mask
	this.mask.apply(this.fld);
	// call blur handler if existent
	(isBlur) && (this.mask.onBlur(this.fld));
	// call field's onchange handler
	if (this.fld.value != this.lastValue && this.fld.onchange) {
		var evt = {};
		if (typeof(e) != 'undefined') {
			for (var p in e)
				evt[p] = e[p];
		}
		evt.type = 'change';
		this.fld.onchange(evt);
	}
};

/**
 * A mask is a set of definitions to restrict the contents
 * and format required in a form field. The mask can be
 * composed by fields (input slots with specific rules for
 * length and acceptable chars) and literals (members
 * of the mask with fixed contents and sometimes fixed
 * position)
 * @class Mask
 */
Mask = function() {
	/**
	 * Set of mask fields and literals
	 * @type Array
	 */
	this.fields = [];
};

/**
 * Build a new instance of the Mask class based on a given
 * expression. The expression is parsed by {@link Mask#loadExpression}
 * @param {String} expr Mask expression
 * @type Mask
 */
Mask.fromExpression = function(expr) {
	var mask = new Mask();
	mask.loadExpression(expr);
	return mask;
};

/**
 * @ignore
 */
Mask.cache = {};

/**
 * Return a Mask object based on one of the mask names available to
 * the PHP2Go form API: DIGIT, INTEGER, FLOAT, CURRENCY, DATE, ...
 * @param {String} name Mask name
 * @type Mask
 */
Mask.fromMaskName = function(name) {
	if (Mask.cache[name])
		return Mask.cache[name];
	switch (name) {
		case 'DIGIT' :
			Mask.cache[name] = DigitMask; break;
		case 'INTEGER' :
			Mask.cache[name] = IntegerMask; break;
		case 'CURRENCY' :
			Mask.cache[name] = CurrencyMask; break;
		case 'CPFCNPJ' :
			Mask.cache[name] = CPFCNPJMask; break;
		case 'WORD' :
			Mask.cache[name] = WordMask; break;
		case 'EMAIL' :
			Mask.cache[name] = EmailMask; break;
		case 'URL' :
			Mask.cache[name] = URLMask; break;
		case 'DATE-EURO' :
			Mask.cache[name] = DateMask('EURO'); break;
		case 'DATE-US' :
			Mask.cache[name] = DateMask('US'); break;
		case 'TIME-AMPM' :
			Mask.cache[name] = TimeMask(true); break;
		case 'TIME' :
			Mask.cache[name] = TimeMask(false); break;
		default :
			var m = [];
			if (m = /FLOAT(\-([1-9][0-9]*)\:([1-9][0-9]*))?/.exec(name))
				Mask.cache[name] = (m[1] ? FloatMask(m[2], m[3]) : FloatMask());
			else if (m = /ZIP\-?([1-9])\:?([1-9])/.exec(name))
				Mask.cache[name] = ZIPMask(m[1], m[2]);
			else
				Mask.cache[name] = NullMask;
	}
	return Mask.cache[name];
};

/**
 * Transforms a given expression into fields and literals. The set of
 * information that can be understood by this method is a small subset
 * of the RegExp specification: character classes (with or without limits),
 * mask chars or mask char sequences and literals
 * @param {String} expr Expression to be parsed
 * @type void
 */
Mask.prototype.loadExpression = function(expr) {
	var ex = PHP2Go.raiseException;
	var map = { '#': '0-9', 'A': 'a-zA-Z', 'L': 'a-z', 'P' : '\.\,', 'U': 'A-Z', 'W': '0-9A-Za-z_' };
	var c, lit, cc = "", cm = "", state = 0, m;
	try {
		for (var i=0; i<expr.length; i++) {
			c = expr.charAt(i);
			// optional sign
			if (c == '?') {
				if (i == 0)
					ex(Lang.inputMask.optionalCharFirst);
				if (state == 1) {
					this.addField(cc, 0, 1);
					cc = ""; state = 0;
				}
				continue;
			}
			// escape
			if (c == "\\") {
				if (state == 1) {
					this.addField(cc, 1, 1);
					cc = ""; state = 0;
				} else if (state == 2) {
					this.addField(map[cm.charAt(0)], cm.length, cm.length);
					cm = ""; state = 0;
				}
				if (i < (expr.length-1)) {
					lit = expr.charAt(++i);
					if (cc != "") {
						this.addField(cc, 1, 1);
						cc = ""; state = 0;
					}
					this.addLiteral(lit, (expr.charAt(i+1) == '?'));
					continue;
				} else {
					ex(Lang.inputMask.escapeCharLast);
				}
			}
			// character class
			if (c == '[') {
				if (state == 1) {
					this.addField(cc, 1, 1);
					cc = ""; state = 0;
				} else if (state == 2) {
					this.addField(map[cm.charAt(0)], cm.length, cm.length);
					cm = ""; state = 0;
				}
				while (i < expr.length && (c = expr.charAt(++i)) != ']') {
					if (c == '\\') {
						if (i < (expr.length-1)) {
							cc += '\\' + expr.charAt(++i);
							continue;
						} else {
							ex(Lang.inputMask.escapeCharLast);
						}
					}
					if (c == '[')
						ex(Lang.inputMask.invalidCharClass);
					if (map[c])
						cc += map[c];
					else
						cc += c;
				}
				if (c == ']') {
					state = 1;
					continue;
				}
				ex(Lang.inputMask.invalidCharClass);
			}
			// 0-many or 1-many
			if (c == '*' || c == '+') {
				if (state == 1) {
					this.addField(cc, (c=='*'?0:1), -1);
					cc = ""; state = 0;
				} else {
					this.addLiteral(c, (expr.charAt(i+1) == '?'));
				}
				continue;
			}
			// limits expression
			if (c == '{') {
				if (state == 1) {
					buf = "";
					while (i < expr.length && (c = expr.charAt(++i)) != '}') {
						if (c == '{')
							ex(Lang.inputMask.invalidLimits);
						buf += c;
					}
					if (c == '}' && (m = /^([0-9]+)(,([0-9]+)?)?$/.exec(buf))) {
						var min = parseInt(m[1], 10);
						var max = (m[3] ? parseInt(m[3], 10) : (m[2] ? -1 : min));
						if (min >= 0) {
							this.addField(cc, min, max);
							cc = ""; state = 0;
							continue;
						}
					}
				}
				ex(Lang.inputMask.invalidLimits);
			}
			// mask char
			if (map[c]) {
				if (state == 1) {
					this.addField(cc, (c=='*'?0:1), -1);
					cc = ""; state = 0;
				} else if (state == 2) {
					if (c != cm.charAt(0)) {
						this.addField(map[cm.charAt(0)], cm.length, cm.length);
						cm = c;
					} else {
						cm += c;
					}
				} else {
					cm = c; state = 2;
				}
			// literal
			} else {
				if (state == 1) {
					this.addField(cc, (c=='*'?0:1), -1);
					cc = ""; state = 0;
				} else if (state == 2) {
					this.addField(map[cm.charAt(0)], cm.length, cm.length);
					cm = ""; state = 0;
				}
				this.addLiteral(c, (expr.charAt(i+1) == '?'));
			}
		}
		// pending char class
		if (state == 1)
			this.addField(cc, 1, 1);
		// pending mask char sequence
		if (state == 2)
			this.addField(map[cm.charAt(0)], cm.length, cm.length);
	} catch(e) {
		alert(e.message);
	}
};

/**
 * Adds a new field in the mask. The chars parameter
 * must be a character class (0-9, a-z). Special characters
 * (., [, ], ...) must be escaped
 * @param {String} chars Acceptable chars
 * @param {Number} min Minimum size
 * @param {Number} max Maximum size
 * @type void
 */
Mask.prototype.addField = function(chars, min, max) {
	min = parseInt(min, 10);
	max = parseInt(max, 10);
	var idx = this.fields.length;
	this.fields.push({ idx: idx, literal: false, value: "", filled: false, positive: new RegExp("["+chars+"]"), negative: new RegExp("[^"+chars+"]+", "g"), min: min, max: max, optional: (min == 0) });
};

/**
 * Adds a new literal in the mask. Literals
 * can't have a length greater than 1
 * @param {String} c Literal char
 * @param {Boolean} opt Whether the literal is optional
 * @type void
 */
Mask.prototype.addLiteral = function(c, opt) {
	var idx = this.fields.length;
	this.fields.push({ idx: idx, literal: true, value: String(c).charAt(0), filled: false, optional: !!opt });
};

/**
 * Calculate the maxlength of the mask
 * @type Number
 */
Mask.prototype.getMaxLength = function() {
	var len = 0;
	for (var i=0,f=this.fields; i<f.length; i++) {
		if (f[i].literal)
			len++;
		else if (f[i].max != -1)
			len += f[i].max;
		else
			return false;
	}
	return len;
};

/**
 * Return the highest available position inside a field,
 * based on its index. Optional literals will return 0
 * @param {Number} idx Field index
 * @param {Number} max Form input maxlength
 * @type Number
 */
Mask.prototype.getMaxPosition = function(idx, max) {
	var fld = this.fields[idx];
	if (fld.literal) {
		return (!fld.optional || fld.filled ? 1 : 0);
	} else {
		var fldNext = this.fields[idx+1] || null;
		if (fld.max != -1) {
			if (fld.min < fld.max && fldNext && ((fldNext.literal && fldNext.filled) || (!fldNext.literal && fldNext.value != "")))
				return fld.value.length+1;
			return fld.max;
		} else if (fldNext && ((fldNext.literal && fldNext.filled) || (!fldNext.literal && fldNext.value != ""))) {
			return fld.value.length+1;
		} else {
			return parseInt(max, 10);
		}
	}
};

/**
 * This method can be overriden to add caret position routines.
 * This method will be called inside {@link InputMask#keyUpHandler}
 * @param {String} oldVal Field's old value
 * @param {String} newVal Field's new value
 * @param {Number} caret Field's last caret position
 * @param {Number} key Last typed key
 * @type Number
 */
Mask.prototype.getCaretPosition = function(oldVal, newVal, caret, key) {
	return false;
};

/**
 * Verify if the mask is a simple regexp filter,
 * without multiple fields or literals
 * @type Boolean
 */
Mask.prototype.isOnlyFilter = function() {
	var f = this.fields;
	return (f.length == 1 && !f[0].literal && f[0].max == -1);
};

/**
 * Verifies if the mask is complete
 * @type Boolean
 */
Mask.prototype.isComplete = function() {
	var f = this.fields;
	for (var i=0; i<f.length; i++) {
		if ((f[i].literal && !f[i].optional && !f[i].filled) || (f[i].value.length < f[i].min))
			return false;
	}
	return true;
};

/**
 * Test if a char must be accepted by the mask,
 * given the current caret position and the current
 * field's selection size
 * @param {String} chr Typed char
 * @param {Number} caretPos Current caret position
 * @param {Number} selSize Current field's selection size
 * @param {Number} maxLen Field's maxlength
 * @type Boolean
 */
Mask.prototype.accept = function(chr, caretPos, selSize, maxLen) {
	var fld, flds = this.fields;
	var minPos = maxPos = 0;
	var partial = false;
	// simple filter mask
	if (this.isOnlyFilter())
		return flds[0].positive.test(chr);
	// composite mask
	for (var i=0; i<flds.length; i++) {
		fld = flds[i];
		if (fld.literal) {
			if (selSize > 1)
				return true;
			// check position
			if ((partial && caretPos <= maxPos) || caretPos == maxPos) {
				// check typed char
				if (chr == fld.value && !fld.filled)
					return true;
			}
			// increment max position
			maxPos += this.getMaxPosition(i, maxLen);
			continue;
		} else {
			minPos = maxPos;
			// increment max position
			maxPos += this.getMaxPosition(i, maxLen);
			// check position
			if (caretPos < maxPos) {
				// check free space
				if (selSize > 0 || fld.max == -1 || fld.value.length < fld.max) {
					// char inserted in previous field (auto-add literal)
					if (caretPos < minPos && fld.value != "")
						return false;
					// check typed char
					if (fld.positive.test(chr))
						return true;
					// stops on a full field that rejects a char
					if (fld.min == fld.max)
						return false;
				}
			}
			// set partial and update max position if field maxlength is variable
			partial = false;
			if (fld.max != -1 && fld.min < fld.max) {
				maxPos = minPos+fld.value.length;
				partial = true;
			} else if (fld.max == -1 && fld.value.length >= fld.min) {
				partial = true;
			}
			continue;
		}
	}
	return false;
};

/**
 * Apply the mask by filtering (when the mask is a simple
 * filter) or formatting (when the mask contains multiple
 * fields and literals)
 * @param {Object} fld Field reference
 * @type void
 */
Mask.prototype.apply = function(fld) {
	if (this.isOnlyFilter())
		this.filter(fld);
	else
		this.format(fld);
};

/**
 * Handler function called from inside {@link InputMask#keyDownHandler}.
 * It can be used to override mask's default policy to accept typed chars
 * @param {Object} fld Field reference
 * @param {Integer} caret Caret position
 * @param {Integer} key Last typed key
 * @type Boolean
 */
Mask.prototype.onKeyDown = function(fld, caret, key) {
	return true;
};

/**
 * Handler function called from inside {@link Mask#format}.
 * It can be used to apply filters on the field's value before
 * the formatting routines
 * @param {String} val Current input value
 * @type String
 */
Mask.prototype.onBeforeChange = function(val) {
	return val;
};

/**
 * Handler function called from inside {@link Mask#format}.
 * Should be used to apply extra format on the new field's
 * value after the mask was applied and before updating
 * the form input
 * @param {String} val New input value
 * @type String
 */
Mask.prototype.onAfterChange = function(val) {
	return val;
};

/**
 * This method can be overriden to apply transformations
 * or validations in the field's value when the blur event
 * occurs
 * @param {Object} fld Field reference
 * @type void
 */
Mask.prototype.onBlur = function(fld) {
};

/**
 * Filters the field value removing chars that
 * don't respect the mask regular expression
 * @param {Object} field Field reference
 * @access private
 * @type void
 */
Mask.prototype.filter = function(field) {
	var newVal = field.value, f = this.fields;
	newVal = this.onBeforeChange(newVal);
	newVal = f[0].value = newVal.replace(f[0].negative, "");
	newVal = this.onAfterChange(field.maxLength != -1 ? newVal.substring(0, field.maxLength) : newVal);
	if (newVal != field.value)
		field.value = newVal;
};

/**
 * Iterates through all the field chars, validating them
 * against the mask format. Invalid chars will be removed and
 * missing literals will be included
 * @param {Object} field Field reference
 * @access private
 * @type void
 */
Mask.prototype.format = function(field) {
	var maxPos = chrIdx = fldIdx = 0;
	var chr = newVal = "", shift = false;
	var pendingLiterals = [], val = field.value;
	var f, flds = this.fields;
	// reset state of all fields
	for (var i=0; i<flds.length; i++) {
		flds[i].filled = false;
		if (!flds[i].literal)
			flds[i].value = "";
	}
	val = this.onBeforeChange(val);
	// iterate through the field's value
	for (var i=0; i<val.length; i++) {
		pendingLiterals = [], chr = val.charAt(i);
		// iterate through the mask fields
		for (var j=0; j<flds.length; j++) {
			f = flds[j];
			if (!f.literal) {
				shift = true;
				// increment max position
				maxPos += this.getMaxPosition(j, field.maxLength);
				// check position
				if (chrIdx < maxPos && j >= fldIdx) {
					// check free space
					if (f.max == -1 || f.value.length < f.max) {
						// check for valid char
						if (f.positive.test(chr)) {
							// add missing optional literals
							for (var k=i+1,l=1; k<val.length; k++,l++) {
								if (flds[j-l] && flds[j-l].literal && flds[j-l].optional && !flds[j-l].filled && val.charAt(k) == flds[j-l].value.charAt(0)) {
									newVal += flds[j-l].value;
									flds[j-l].filled = true;
									continue;
								}
								break;
							}
							// add pending literals
							for (k=0; k<pendingLiterals.length; k++) {
								newVal += flds[pendingLiterals[k]].value;
								flds[pendingLiterals[k]].filled = true;
							}
							// add char
							f.value += chr;
							newVal += chr;
							chrIdx++;
							fldIdx = j;
							// check minimum size satisfied
							if (f.value.length >= f.min)
								shift = false;
							// check maximum length reached
							if (f.max != -1 && f.value.length == f.max) {
								f.filled = true;
								fldIdx = j+1;
							}
							break;
						}
					} else {
						shift = false;
						continue;
					}
				}
				// check minlength reached
				if (f.value.length >= f.min) {
					shift = false;
					continue;
				} else {
					break;
				}
			} else {
				// increment max position
				maxPos += this.getMaxPosition(j, field.maxLength);
				// check if not filled
				if (j >= fldIdx && !f.filled) {
					// check position and valid char
					if (chrIdx <= maxPos && chr == f.value.charAt(0) && pendingLiterals.length == 0) {
						if (!shift) {
							newVal += f.value;
							f.filled = true;
							chrIdx++;
							fldIdx = j+1;
						}
						break;
					} else if (!f.optional) {
						pendingLiterals.push(j);
					}
				}
			}
		}
	}
	newVal = this.onAfterChange(newVal.substring(0, field.maxLength));
	if (newVal != field.value)
		field.value = newVal;
};

/**
 * Serialize the mask
 * @type String
 */
Mask.prototype.serialize = function() {
	return this.fields.serialize();
};

/**
 * Digits mask
 * @type Mask
 */
var DigitMask = Mask.fromExpression("[#]+");
/**
 * Integer numbers mask
 * @type Mask
 */
var IntegerMask = Mask.fromExpression("-?[#]+");
/**
 * Float mask. Accepts limits for integer and decimal parts
 * @param {Number} intPart Integer part size
 * @param {Number} decPart Decimal part size
 */
var FloatMask = function(intPart, decPart) {
	var mask = new Mask();
	mask.intSize = Math.max(parseInt(intPart, 10), 1);
	mask.decSize = Math.max(parseInt(decPart, 10), 1);
	mask.loadExpression((mask.intSize > 0 && mask.decSize > 0 ? "-?[#]{1,"+mask.intSize+"}P[#]{0,"+mask.decSize+"}" : "-?[#]+P[#]*"));
	mask.onBeforeChange = function(val) {
		return val.replace(',', '.');
	};
	return mask;
};
/**
 * Currency mask. Implements onAfterChange to
 * apply monetary format on the field contents,
 * onBlur to auto add decimals on integer numbers
 * and getCaretPosition to define new cursor position
 * after the field is changed
 * @type Mask
 */
var CurrencyMask = function() {
	var mask = new Mask();
	mask.timeout = null;
	mask.loadExpression("-?[#\.\,]+");
	mask.onKeyDown = function(f, c, k) {
		if (k == 110 || k == 194 || k == 188 || k == 190)
			return false;
		var self = this;
		clearTimeout(this.timeout);
		this.timeout = setTimeout(function() { self.applyFormat(f, k); }, 40);
		return true;
	};
	mask.applyFormat = function(f, k) {
		var before = f.value, caret = FieldSelection.getCaret(f);
		var tmp = before.replace(/[^0-9]+/g, ''), neg = (before.charAt(0) == '-');
		var isNum = ((k>=48&&k<=57)||(k>=96&&k<=105));
		if (tmp.length > 2) {
			var grp = '', intp = '', decp = '', after = '';
			decp = tmp.substring(tmp.length-2);
			tmp = tmp.substring(0, tmp.length-2);
			while (tmp != "") {
				grp = (tmp.length > 3 ? tmp.substring(tmp.length-3) : tmp);
				tmp = tmp.substring(0, tmp.length-grp.length);
				intp = grp + '.' + intp;
			}
			intp = intp.substring(0, intp.length-1);
			after = (neg?'-':'') + intp + ',' + decp;
			if (after != f.value) {
				f.value = after;
				var left = before.substring(0, caret).replace(/[\.,]+/g, "");
				var c, n = 0, validChars = left.length + (isNum?1:(k==8?-1:0));
				for (var i=0; i<after.length; i++) {
					c = after.charAt(i);
					if (c == '-' || c == '.' || c == ',')
						continue;
					if (++n == validChars)
						FieldSelection.setCaret(f, i+1);
				}
			}
		} else if (tmp != f.value) {
			f.value = (neg?'-':'') + tmp;
		}
	};
	mask.onBlur = function(f) {
		v = f.value;
		if (v != "" && v.length < 4) {
			if (v.length == 1 && v.charAt(0) == '-')
				f.value = "";
			else if (v.length <= 2 || (v.length == 3 && v.charAt(0) == '-'))
				f.value += ',00';
		}
	};
	return mask;
}();
/**
 * CPF/CNPJ mask
 * @type Mask
 */
var CPFCNPJMask = Mask.fromExpression("[#]{11,14}");
/**
 * Word mask. Useful in values that represent
 * usernames, passwords, codes
 * @type Mask
 */
var WordMask = Mask.fromExpression("[W\\.\\-]+");
/**
 * Email mask
 * @type Mask
 */
var EmailMask = Mask.fromExpression("[W\\.\\-\\[\\]@]+");
/**
 * URL mask
 * @type Mask
 */
var URLMask = Mask.fromExpression("[W\\.\\-\\[\\]@\\:=&;\\+\\/\\?%\\$\\#~]+");
/**
 * Date mask. Accepts EURO (d/m/Y) and US (Y/m/d) formats
 * @param {String} format Date format
 * @type Mask
 */
var DateMask = function(format) {
	var mask = new Mask();
	mask.loadExpression(format == 'US' ? '[#]{0,4}/[#]{0,2}/[#]{0,2}' : '[#]{0,2}/[#]{0,2}/[#]{0,4}');
	mask.onBeforeChange = function(val) {
		return val.replace(/[-\.]/, '/');
	};
	mask.onBlur = function(fld) {
		var m = [];
		if (format == 'EURO') {
			if (m = /^([0-9]{2}\/[0-9]{2}\/)([0-9]{2})$/.exec(fld.value))
				fld.value = m[1] + (parseInt(m[2]) > 50 ? '19'+m[2] : '20'+m[2]);
		} else {
			if (m = /^([0-9]{2})(\/[0-9]{2}\/[0-9]{2})$/.exec(fld.value))
				fld.value = (parseInt(m[1]) > 50 ? '19'+m[1] : '20'+m[1]) + m[2];
		}
	};
	return mask;
};
/**
 * Time mask
 * @param {Boolean} ampm Whether to use AM/PM or standard time format. Defaults to false
 * @type Mask
 */
var TimeMask = function(ampm) {
	var mask = new Mask();
	mask.loadExpression(!!ampm ? "##:##[ap]" : "##:##");
	mask.onBeforeChange = function(val) {
		return val.replace("A", "a").replace("P", "p");
	};
	return mask;
};
/**
 * ZIP code mask
 * @param {Number} left ZIP code left size
 * @param {Number} right ZIP code right size
 * @type Mask
 */
var ZIPMask = function(left, right) {
	left = Math.max(parseInt(left, 10), 1);
	right = Math.max(parseInt(right, 10), 1);
	var mask = new Mask();
	mask.loadExpression("[#]{"+left+"}-[#]{"+right+"}");
	return mask;
};

/**
 * Null mask
 * Used to represent a mask where all chars are acceptable
 * @type Mask
 */
var NullMask = function() {
	var mask = new Mask();
	mask.accept = function(c, p, s, m) { return true; };
	mask.apply = $EF;
	return mask;
}();

PHP2Go.included[PHP2Go.baseUrl + 'inputmask.js'] = true;

}
