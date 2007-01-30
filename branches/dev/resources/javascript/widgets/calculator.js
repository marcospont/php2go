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
 * This file contains the Calculator class. This class builds
 * and handles the calculator widget used by the forms API
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'widgets/calculator.js']) {

/**
 * The Calculator class is used to build a simple calculator
 * widget to be used by numeric fields in the PHP2Go forms
 * @constructor
 */
Calculator = function() {
	/**
	 * Calculator container layer
	 * @type Object
	 */
	this.container = null;
	/**
	 * Calculator text input
	 * @type Object
	 */
	this.input = null;
	/**
	 * Current calculator trigger (button, image or link)
	 * @type Object
	 */
	this.trigger = null;
	/**
	 * Current calculator target (field)
	 * @type Object
	 */
	this.target = null;
	/**
	 * Array of calculator elements (action buttons)
	 * @type Array
	 */
	this.elements = [
		['input', 'input', 'calcInput', '0', 3, false],
		['button', 'clear', 'calcRedButton', 'C', 0, false],
		['button', 'clear_entry', 'calcRedButton', 'CE', 0, true],
		['button', 'number7', 'calcBlueButton', '7', 0, false],
		['button', 'number8', 'calcBlueButton', '8', 0, false],
		['button', 'number9', 'calcBlueButton', '9', 0, false],
		['button', 'negate', 'calcRedButton', '+/-', 0, false],
		['button', 'percent', 'calcBlueButton', '%', 0, true],
		['button', 'number4', 'calcBlueButton', '4', 0, false],
		['button', 'number5', 'calcBlueButton', '5', 0, false],
		['button', 'number6', 'calcBlueButton', '6', 0, false],
		['button', 'plus', 'calcRedButton', '+', 0, false],
		['button', 'minus', 'calcRedButton', '-', 0, true],
		['button', 'number1', 'calcBlueButton', '1', 0, false],
		['button', 'number2', 'calcBlueButton', '2', 0, false],
		['button', 'number3', 'calcBlueButton', '3', 0, false],
		['button', 'multiply', 'calcRedButton', '*', 0, false],
		['button', 'divide', 'calcRedButton', '/', 0, true],
		['button', 'number0', 'calcBlueButton', '0', 0, false],
		['button', 'decimal', 'calcBlueButton', '.', 0, false],
		['button', 'result', 'calcResultButton', 'Resultado', 2, false],
		['button', 'equals', 'calcRedButton', '=', 0, false]
	];
	/**
	 * Used to signal keys to be ignored inside keydown and keypress handlers
	 * @type Boolean
	 */
	this.ignore = false;
	/**
	 * Used to hold left operand value
	 * @type Number
	 */
	this.left = 0;
	/**
	 * Used to hold right operand value
	 * @type Number
	 */
	this.right = 0;
	/**
	 * Indicates if next number pressed will represent a new number
	 * @type Boolean
	 */
	this.newNum = true;
	/**
	 * Pending operator
	 * @type String
	 */
	this.pendingOp = "";
	/**
	 * Last operator
	 * @type String
	 */
	this.lastOp = "";
};

/**
 * Indicates if the calculator singleton was already loaded
 * @type Boolean
 */
Calculator.loaded = false;

/**
 * Holds the single instance of the Calculator
 * class, shared among all fields that use it
 * @type Calculator
 */
Calculator.singleton = null;

/**
 * Sets up a new calculator based on a set of options.
 * The available options are <b>trigger</b> (calculator
 * trigger), <b>target</b> (target field) and
 * <b>align</b> (calculator alignment)
 * @param {Object} opts Setup options
 * @type void
 */
Calculator.setup = function(opts) {
	var tgr = $(opts.trigger);
	var trg = $(opts.target);
	var align = opts.align || "right"; // "right", "bottom"
	if (tgr && trg) {
		if (!Calculator.loaded) {
			Calculator.loaded = true;
			Event.addLoadListener(function() {
				Calculator.singleton = c = new Calculator();
				c.build();
			});
		}
		Event.addListener(tgr, 'click', function() {
			var c = Calculator.singleton;
			if (c.trigger == tgr) {
				c.hide();
			} else {
				if (!trg.disabled && !trg.readOnly)
					c.showAt(tgr, trg, align);
			}
		});
	}
};

/**
 * Builds the calculator widget
 * @type void
 */
Calculator.prototype.build = function() {
	var self = this;
	// main container
	this.container = $N('div', null, {position: 'absolute', display:'none'});
	this.container.setParentNode(document.body);
	// main table
	var table = $N('table', this.container);
	table.className = 'calcTable';
	var tbody = $N('tbody', table);
	var cell, input, row = $N('tr', tbody);
	this.elements.walk(function(item, idx) {
		cell = $N('td', row);
		(item[4] > 0) && (cell.colSpan = item[4]);
		input = $N(item[0], cell);
		input.id = 'calculator_' + item[1];
		input.name = item[1];
		input.className = item[2];
		if (item[0] == 'input') {
			self.input = input;
			input.readOnly = true;
			input.value = item[3];
		} else {
			input.innerHTML = item[3];
		}
		(item[5]) && (row = $N('tr', tbody));
	});
	// event listeners
	Event.addListener(table, 'mouseover', function(e) {
		e = (e||window.event);
		var elm = $E(e.target||e.srcElement);
		elm.classNames().add('calcButtonHilite');
	});
	Event.addListener(table, 'mouseout', function(e) {
		e = (e||window.event);
		var elm = $E(e.target||e.srcElement);
		elm.classNames().remove('calcButtonHilite');
	});
	$C(table.getElementsByTagName('button')).walk(function(item, idx) {
		Event.addListener(item, 'click', function() {
			self.buttonHandler(item);
		});
	});
};

/**
 * Calculator keydown handler. Used to detect backspace
 * and delete chars in browsers that can't detect them
 * inside a keypress event
 * @param {Event} e Event
 * @type void
 */
Calculator.prototype.keyDownHandler = function(e) {
	e = $EV(e);
	var o = PHP2Go.browser.opera;
	var c = Calculator.singleton, k = e.key();
	// special routine to capture backspace and delete
	if (k == 8 || ((k == 46 || k == 110) && !o) || (k == 78 && o)) {
		this.ignore = true;
		c.backspace();
		e.stop();
	} else {
		this.ignore = false;
	}
};

/**
 * Calculator keypress handler
 * @param {Event} e Event
 * @type void
 */
Calculator.prototype.keyPressHandler = function(e) {
	e = $EV(e);
	if (!this.ignore) {
		var c = Calculator.singleton;
		var k = e.key();
		if (k >= 48 && k <= 57) {
			c.number(k-48);
			e.preventDefault();
		} else {
			var ctrlAlt = (e.ctrlKey || e.altKey);
			var ops = {'43': 'plus', '45': 'minus', '42': 'multiply', '47': 'divide', '37': 'percent', '13': 'equals', '61': 'equals'};
			switch (k) {
				case 43 :	// plus
				case 45 :	// minus
				case 42 :	// multiply
				case 47 :	// divide
				case 37 :	// percent
				case 13 :	// enter
				case 61 :	// equals
					c.operation(ops[k]);
					e.preventDefault();
					break;
				case 67 :	// C
				case 99 :	// c
					if (!ctrlAlt) {
						c.clear();
						e.preventDefault();
					}
					break;
				case 46 :	// dot
					c.decimal();
					e.preventDefault();
					break;
				case 82 :	//  R
				case 114 :	// r
					if (!ctrlAlt) {
						c.result();
						e.preventDefault();
					}
					break;
				case 27 :	// esc
					c.hide();
					e.preventDefault();
					break;
			}
		}
	}
	e.stop();
};

/**
 * Calculator button handler. Receives as parameter
 * the event sender name and applies the proper action
 * @param {Object} sender Button clicked
 * @type void
 */
Calculator.prototype.buttonHandler = function(sender) {
	switch (sender.name) {
		case 'number0' :
		case 'number1' :
		case 'number2' :
		case 'number3' :
		case 'number4' :
		case 'number5' :
		case 'number6' :
		case 'number7' :
		case 'number8' :
		case 'number9' :
			this.number(sender.name.substring(6));
			break;
		case 'plus' :
		case 'minus' :
		case 'multiply' :
		case 'divide' :
		case 'percent' :
		case 'equals' :
			this.operation(sender.name);
			break;
		case 'clear' :
			this.clear();
			break;
		case 'clear_entry' :
			this.clearEntry();
			break;
		case 'negate' :
			this.negate();
			break;
		case 'decimal' :
			this.decimal();
			break;
		case 'result' :
			this.result();
			break;
	}
};


/**
 * Listen to mousedown events in the document. If the target
 * element is outside the calculator area and is not its
 * trigger element, the calculator popup is closed
 * @param {Event} e Event
 * @type void
 */
Calculator.prototype.mouseDownHandler = function(e) {
	var c = Calculator.singleton;
	var t = $E($EV(e).element());
	if (!t.isChildOf(c.trigger) && !t.isChildOf(c.container))
		c.hide();
};

/**
 * Method called when a number (0-9) is pressed
 * @param {Number} n Number pressed
 * @type void
 */
Calculator.prototype.number = function(n) {
	var i = this.input;
	if (this.newNum) {
		i.value = n;
		this.newNum = false;
	} else {
		(i.value == "0" ? i.value = n : i.value += n);
	}
};

/**
 * Method called when an operation button is pressed
 * @param {String} op Operation name
 * @type void
 */
Calculator.prototype.operation = function(op) {
	var tmp, i = this.input;
	// detect switch of operations
	if (op != 'equals' && this.pendingOp)
		this.pendingOp = "";
	this.newNum = true;
	switch (this.pendingOp) {
		case 'plus' :
			if (op != 'equals' || this.lastOp != 'equals')
				this.right = parseFloat(i.value, 10);
			this.left += this.right;
			break;
		case 'minus' :
			if (op != 'equals' || this.lastOp != 'equals')
				this.right = parseFloat(i.value, 10);
			this.left -= this.right;
			break;
		case 'multiply' :
			if (op != 'equals' || this.lastOp != 'equals')
				this.right = parseFloat(i.value, 10);
			this.left *= this.right;
			break;
		case 'divide' :
			if (op != 'equals' || this.lastOp != 'equals')
				this.right = parseFloat(i.value, 10);
			this.left /= this.right;
			break;
		case 'percent' :
			if (op != 'equals' || this.lastOp != 'equals')
				this.right = parseFloat(i.value, 10);
			this.left = (this.right/100) * this.left;
			break;
		default :
			this.left = parseFloat(i.value, 10);
			break;
	}
	// ouput result
	if (isNaN(this.left))
		this.left = 0;
	i.value = this.left;
	// save last operation
	this.lastOp = op;
	// save pending operation
	(op != 'equals') && (this.pendingOp = op);
};

/**
 * Resets the calculator status
 * @type void
 */
Calculator.prototype.clear = function() {
	this.acc = 0;
	this.lastOp = "";
	this.pendingOp = "";
	this.clearEntry();
};

/**
 * Clears the current calculator entry
 * @type void
 */
Calculator.prototype.clearEntry = function() {
	this.input.value = "0";
	this.newNum = true;
};

/**
 * Inverts the signal of the current calculator entry
 * @type void
 */
Calculator.prototype.negate = function() {
	this.input.value = parseFloat(this.input.value, 10) * -1;
};

/**
 * Adds a decimal point in the calculator text input
 * @type void
 */
Calculator.prototype.decimal = function() {
	var i = this.input;
	if (this.newNum) {
		i.value = "0.";
		this.newNum = false;
	} else {
		if (i.value.indexOf('.') == -1)
			i.value += '.';
	}
};

/**
 * Removes the rightmost number in the calculator text input
 * @type void
 */
Calculator.prototype.backspace = function() {
	var i = this.input;
	i.value = (i.value.length == 1 ? "0" : i.value.substring(0, i.value.length-1));
};

/**
 * Called when the "Result" button is pressed. The current
 * calculator value is copied to the target field
 * @type void
 */
Calculator.prototype.result = function() {
	var t = this.target;
	var v = this.input.value;
	if (isFinite(parseFloat(v))) {
		v = v.substring(0, t.maxLength);
		if (t.inputMask) {
			var p = 0, m = t.inputMask.mask;
			if (v != "") {
				// currency mask
				if (m == CurrencyMask) {
					v = Math.truncate(parseFloat(v), 2).toString().replace(".", ",");
					if (v.indexOf(',') == -1)
						v += ",00";
					if (v.indexOf(',') == (v.length-2))
						v += "0";
					v = v.replace(/[^\-0-9,]+/, "");
				}
				// integer mask
				else if (m == IntegerMask) {
					v = Math.round(parseFloat(v)).toString();
				}
				// float mask with integer part validation
				else if (m.intSize) {
					p = v.indexOf('.');
					(p == -1) && (p = v.length);
					if (p > m.intSize) {
						alert(Lang.validator.invalidFloat.assignAll('', m.intSize, m.decSize));
						this.input.focus();
						return;
					}
				}
			}
			t.value = v;
			t.inputMask.update();
		} else {
			t.value = v;
		}
	}
	t.focus();
	this.hide();
};

/**
 * Shows the calculator widget in a given trigger
 * and target. Called from within {@link Calculator#setup}
 * @param {Object} tgr Trigger
 * @param {Object} trg Target field
 * @param {String} align Alignment ("right", "bottom")
 * @type void
 */
Calculator.prototype.showAt = function(tgr, trg, align) {
	var c = this.container;
	var pos = tgr.getPosition();
	var dim = tgr.getDimensions();
	tgr.blur();
	// set current trigger/input/mask
	this.trigger = tgr;
	this.target = trg;
	// move and display the calculator container
	if (align == "bottom")
		c.moveTo(pos.x+dim.width-c.getDimensions().width, pos.y+dim.height+2);
	else
		c.moveTo(pos.x+dim.width+2, pos.y);
	c.show();
	// set properties based on the trigger
	var tmp = trg.value;
	var input = $('calculator_input');
	if (trg.inputMask) {
		if (trg.inputMask.mask == CurrencyMask)
			tmp = tmp.replace(".", "").replace(",", ".");
		else
			tmp = tmp.replace(/[^0-9\.]/, "");
	}
	input.value = tmp;
	// reset class properties
	this.left = 0;
	this.right = 0;
	this.newNum = (input.value != "" ? false : true);
	this.pendingOp = "";
	this.lastOp = "";
	// register event listener
	var self = this;
	Event.addListener(document, 'keydown', self.keyDownHandler, true);
	Event.addListener(document, 'keypress', self.keyPressHandler, true);
	Event.addListener(document, 'mousedown', self.mouseDownHandler, true);
};

/**
 * Hides the calculator widget. Called from within
 * {@link Calculator#setup}
 * @type void
 */
Calculator.prototype.hide = function() {
	this.trigger = null;
	this.container.hide();
	// unregister event listener
	var c = this;
	Event.removeListener(document, 'keydown', c.keyDownHandler, true);
	Event.removeListener(document, 'keypress', c.keyPressHandler, true);
	Event.removeListener(document, 'mousedown', c.mouseDownHandler, true);
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/calculator.js'] = true;

}