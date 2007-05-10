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
 * This file contains the classes used to validate input coming from form fields.
 * There's a set of validator classes and a validation aggregator (FormValidator),
 * which is attached to every form generated by the PHP2Go forms API
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'validator.js']) {

/**
 * Base validator class
 * @constructor
 * @param {Object} args Validator args
 */
Validator = function(args) {
	/**
	 * Validating field
	 * @type Field
	 */
	this.fld = $FF(args.form, args.field);
	/**
	 * Field label
	 * @type String
	 */
	try {
		this.fldLabel = (this.fld.label || this.fld.name);
	} catch(e) {
	}
	/**
	 * Error message
	 * @type String
	 */
	this.msg = null;
};

/**
 * Runs proper validation based on a mask name
 * @param {String} v Value to validate
 * @param {String} mask Mask name (including arguments and limits)
 * @type Boolean
 */
Validator.isMask = function(v, mask) {
	switch (mask) {
		case 'DIGIT' : return Validator.isDigit(v);
		case 'INTEGER' : return Validator.isInteger(v);
		case 'FLOAT' : return Validator.isFloat(v);
		case 'CURRENCY' : return Validator.isCurrency(v);
		case 'CPFCNPJ' : return Validator.isCPFCNPJ(v);
		case 'WORD' : return Validator.isWord(v);
		case 'EMAIL' : return Validator.isEmail(v);
		case 'URL' : return Validator.isURL(v);
		case 'DATE-EURO' : return Validator.isDate(v, 'EURO');
		case 'DATE-US' : return Validator.isDate(v, 'US');
		case 'TIME' : return Validator.isTime(v, false);
		case 'TIME-AMPM' : return Validator.isTime(v, true);
		default :
			var m = [];
			if (m = /FLOAT(\-([1-9][0-9]*)\:([1-9][0-9]*))?/.exec(mask))
				return (m[1] ? Validator.isFloat(m[2], m[3]) : Validator.isFloat());
			else if (m = /ZIP\-?([1-9])\:?([1-9])/.exec(mask))
				return Validator.isZIP(v, m[1], m[2]);
			else
				return true;
	}
	return true;
};

/**
 * Digit mask validation routine
 * @param {String} val Value to validate
 * @type Boolean
 */
Validator.isDigit = function(val) {
	if (val == "")
		return true;
	if (!(/^[0-9]+$/.test(val)))
		return false;
	var intval = parseInt(val, 10);
	return (intval == 0 || intval);
};

/**
 * Integer numbers validation routine
 * @param {String} val Value to validate
 * @type Boolean
 */
Validator.isInteger = function(val) {
	if (val == "")
		return true;
	if (!(/^(\+|\-)?\d+$/.test(val)))
		return false;
	var intval = parseInt(val, 10);
	return (intval == 0 || intval);
};

/**
 * Floating point numbers validation routine
 * @param {String} val Value to validate
 * @param {Number} intPart Integer part size
 * @param {Number} decPart Decimal part size
 * @type Boolean
 */
Validator.isFloat = function(val, intPart, decPart) {
	if (val == "")
		return true;
	var floatval = parseFloat(val, 10);
	if (floatval == 0 || floatval) {
		intPart = intPart || "";
		decPart = decPart || "";
		return val.match(new RegExp("^\-?\\d{1,"+intPart+"}(\\.\\d{1,"+decPart+"})?$"));
	}
	return false;
};

/**
 * Currency validation routine
 * @param {String} val Value to validate
 * @type Boolean
 */
Validator.isCurrency = function(val) {
	if (val == "")
		return true;
	var size = (val.charAt(0) == '-' ? val.length-1 : val.length);
	var mod = (size - 3) % 4;
	var groups = (size - mod - 3) / 4;
	var re = new RegExp("^\-?\\d{"+mod+"}(\\.\\d{3}){"+groups+"},\\d{2}$");
	return re.test(val);
};

/**
 * Validates CPF/CNPJ document numbers
 * @param {String} val Value to validate
 * @type Boolean
 */
Validator.isCPFCNPJ = function(val) {
	if (val == "")
		return true;
	var len = val.length;
	var sum1, sum2, rest, d1, d2;
	if (val.length == 14) {
		sum1 = (val.charAt(0) * 5) + (val.charAt(1) * 4) + (val.charAt(2) * 3) + (val.charAt(3) * 2) + (val.charAt(4) * 9) + (val.charAt(5) * 8) + (val.charAt(6) * 7) + (val.charAt(7) * 6) + (val.charAt(8) * 5) + (val.charAt(9) * 4) + (val.charAt(10) * 3) + (val.charAt(11) * 2);
		rest = sum1 % 11, d1 = rest < 2 ? 0 : 11 - rest;
		sum2 = (val.charAt(0) * 6) + (val.charAt(1) * 5) + (val.charAt(2) * 4) + (val.charAt(3) * 3) + (val.charAt(4) * 2) + (val.charAt(5) * 9) + (val.charAt(6) * 8) + (val.charAt(7) * 7) + (val.charAt(8) * 6) + (val.charAt(9) * 5) + (val.charAt(10) * 4) + (val.charAt(11) * 3) + (val.charAt(12) * 2);
		rest = sum2 % 11, d2 = rest < 2 ? 0 : 11 - rest;
		return ((val.charAt(12) == d1) && (val.charAt(13) == d2));
	} else if (val.length == 11) {
		sum1 = (val.charAt(0) * 10) + (val.charAt(1) * 9) + (val.charAt(2) * 8) + (val.charAt(3) * 7) + (val.charAt(4) * 6) + (val.charAt(5) * 5) + (val.charAt(6) * 4) + (val.charAt(7) * 3) + (val.charAt(8) * 2);
		rest = sum1 % 11, d1 = rest < 2 ? 0 : 11 - rest;
		sum2 = (val.charAt(0) * 11) + (val.charAt(1) * 10) + (val.charAt(2) * 9) + (val.charAt(3) * 8) + (val.charAt(4) * 7) + (val.charAt(5) * 6) + (val.charAt(6) * 5) + (val.charAt(7) * 4) + (val.charAt(8) * 3) + (val.charAt(9) * 2);
		rest = sum2 % 11, d2 = rest < 2 ? 0 : 11 - rest;
		return ((val.charAt(9) == d1) && (val.charAt(10) == d2));
	} else {
		return false;
	}
};

/**
 * Validates if a given value is word boundary
 * @param {String} val Value to validate
 * @type Boolean
 */
Validator.isWord = function(val) {
	return (val == "" || (/^\w+((-\w+)|(\.\w+))*$/.test(val)));
};

/**
 * E-mail validation routine
 * @param {String} val Value to validate
 * @type Boolean
 */
Validator.isEmail = function(val) {
	var rep = val.replace(/^[^0-9a-zA-Z_\[\]\.\-@]+$/, "");
	return (val == "" || (val == rep && (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(val))));
};

/**
 * URL validation routine
 * @param {String} val Value to validate
 * @type Boolean
 */
Validator.isURL = function(val) {
	return (val == "" || (/^(ht|f)tps?\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z0-9\-\._]{2,3}(:[a-zA-Z0-9]*)?\/?([a-zA-Z0-9\-\._\?\,\'\/\\\+&%\$#\=~])*$/i.test(val)));
};

/**
 * Date validation routine
 * @param {String} val Value to validate
 * @param {String} fmt Date format (EURO, US)
 * @type Boolean
 */
Validator.isDate = function(val, fmt) {
	if (val == "")
		return true;
	fmt = fmt || 'EURO';
	var re, d, m, y, bm, leap;
	if (fmt == 'US')
		re = /^(\d{1,4})(\/|\.|\-)(\d{1,2})(\/|\.|\-)(\d{1,2})$/;
	else
		re = /^(\d{1,2})(\/|\.|\-)(\d{1,2})(\/|\.|\-)(\d{1,4})$/;
	if (mt = re.exec(val)) {
		d = parseInt((fmt == 'US' ? mt[5] : mt[1]), 10);
		y = parseInt((fmt == 'US' ? mt[1] : mt[5]), 10);
		leap = (y % 4 == 0) && (y % 100 != 0 || y % 400 == 0);
		m = parseInt(mt[3], 10), m31 = 0xAD5, bm = (1 << (m-1));
        if (y < 1000 || m < 1 || m > 12 || d < 1 || d > 31 || (d == 31 && (bm & m31) == 0) || (d == 30 && m == 2) || (d == 29 && m == 2 && !leap)) {
            return false;
        } else {
			return true;
		}
	}
	return false;
};

/**
 * Time validation routine
 * @param {String} val Value to validate
 * @param {Boolean} ampm Require AM/PM flag
 * @type Boolean
 */
Validator.isTime = function(val, ampm) {
	if (val == "")
		return true;
	ampm = !!ampm;
	var mt = val.match(new RegExp("^(\\d{1,2})\:(\\d{1,2})(\:(\d{1,2}))?"+(ampm?"(?:a|p)":"")));
	if (mt) {
		var h = parseInt(mt[1], 10);
		var m = parseInt(mt[2], 10);
		var s = (mt[4] ? parseInt(mt[4], 10) : 0);
		return (h >= 0 && h <= 23 && m >= 0 && m <= 59 && s >= 0 && s <= 59);
	}
	return false;
};

/**
 * ZIP code validation routine
 * @param {String} val Value to validate
 * @param {Number} left Left size
 * @param {Number} right Right size
 * @type Boolean
 */
Validator.isZIP = function(val, left, right) {
	if (val == "")
		return true;
	left = left || 5;
	right = right || 3;
	return val.match(new RegExp("^\\d{"+left+"}\-\\d{"+right+"}$"));
};

/**
 * Check if this validator validates a mandatory
 * field, based on a condition or not
 * @type Boolean
 */
Validator.prototype.isMandatory = function() {
	return (this instanceof RequiredValidator || (this instanceof RuleValidator && !this.msg && (/^REQIF/.test(this.ruleType))));
};

/**
 * Retrieve validator error message
 * @type String
 */
Validator.prototype.getErrorMessage = function() {
	if (!this.msg)
		return Lang.invalidValue;
	return this.msg.assignAll(this.fldLabel);
};

/**
 * Abstract validation method
 * @type Boolean
 */
Validator.prototype.validate = function() {
	return true;
};

/**
 * Validator class used to validate mandatory fields
 * @constructor
 * @base Validator
 * @param {Object} args Validator params
 */
RequiredValidator = function(args) {
	this.Validator(args);
	/**
	 * @ignore
	 */
	this.msg = Lang.validator.requiredField;
};
RequiredValidator.extend(Validator, 'Validator');

/**
 * Executes the validation routine
 * @type Boolean
 */
RequiredValidator.prototype.validate = function() {
	return (!this.fld.isEmpty());
};

/**
 * Builds validator's error message using the language table
 * @type String
 */
RequiredValidator.prototype.getErrorMessage = function() {
	return Lang.validator.requiredField.assignAll(this.fldLabel);
};

/**
 * This validator is used to perform data type testing
 * on fields that must respect an input mask
 * @constructor
 * @base Validator
 * @param {Object} args Validator params
 */
DataTypeValidator = function(args) {
	this.Validator(args);
	/**
	 * Field's mask name
	 * @type String
	 */
	this.mask = args.mask;
	/**
	 * @ignore
	 */
	this.msg = args.msg;
};
DataTypeValidator.extend(Validator, 'Validator');

/**
 * Executes the validation routine, using the
 * {@link Validator#isMask} method
 * @type Boolean
 */
DataTypeValidator.prototype.validate = function() {
	if (this.fld.isEmpty())
		return true;
	// force mask blur routine
	var mask = this.fld.getMask();
	if (mask)
		mask.onBlur(this.fld.fld);
	// validate against the mask
	return Validator.isMask(this.fld.getValue(), this.mask);
};

/**
 * Get data type error message. If a custom message is not
 * present, the default message from the language table will be used
 * @type String
 */
DataTypeValidator.prototype.getErrorMessage = function() {
	if (this.msg)
		return this.msg;
	var maskType = /^[A-Z]+/.exec(this.mask)[0];
	return Lang.validator.invalidDataType.assignAll(this.fldLabel, Lang.masks[maskType]);
};

/**
 * This validator can be used to test if a given field
 * contains "at least" or "at most" N chars
 * @constructor
 * @base Validator
 * @param {Object} args Validator args
 */
LengthValidator = function(args) {
	this.Validator(args);
	/**
	 * Rule name (maxlength or minlength)
	 * @type String
	 */
	this.rule = (args.rule || "").toUpperCase();
	/**
	 * Rule limit
	 * @type Number
	 */
	this.limit = parseInt(args.limit, 10);
	/**
	 * @ignore
	 */
	this.msg = args.msg;
};
LengthValidator.extend(Validator, 'Validator');

/**
 * Executes the validation routine
 * @type Boolean
 */
LengthValidator.prototype.validate = function() {
	var val = this.fld.getValue();
	return (!val || PHP2Go.compare(val.length, this.limit, (this.rule == 'MAXLENGTH' ? 'LOET' : 'GOET'), 'INTEGER'));
};

/**
 * Build and return the length validator default message,
 * if a custom message is not present
 * @type String
 */
LengthValidator.prototype.getErrorMessage = function() {
	if (this.msg)
		return this.msg;
	var msg = (this.rule == 'MAXLENGTH' ? Lang.validator.maxLengthField : Lang.validator.minLengthField);
	return msg.assignAll(this.fldLabel, this.limit);
};

/**
 * The RuleValidator can be used to test validation rules. Simple comparison,
 * regexp, JS function and conditional obligatoriness are some kinds of rules
 * that can be processed by this class
 * @constructor
 * @base Validator
 * @type {Object} args Rule arguments
 */
RuleValidator = function(args) {
	this.Validator(args);
	/**
	 * Rule type
	 * @type String
	 */
	this.ruleType = (args.ruleType || "").toUpperCase();
	/**
	 * Comparison data type (for comparison rules)
	 * @type String
	 */
	this.dataType = args.dataType || 'STRING';
	/**
	 * Holds the target comparison value, in REQIFXX rules
	 * @type Field
	 */
	this.peerValue = args.peerValue || '';
	/**
	 * Comparison peer
	 * @type Object
	 */
	this.peer = (args.peerField ? $FF(args.form, args.peerField) : args.peerValue);
	/**
	 * Peer type (PEER_FIELD or PEER_VALUE)
	 * @type Number
	 */
	this.peerType = (args.peerField ? RuleValidator.PEER_FIELD : RuleValidator.PEER_VALUE);
	/**
	 * Peer label
	 * @type String
	 */
	this.peerLabel = (args.peerField && this.peer ? this.peer.label : null);
	/**
	 * Validation function
	 * @type Function
	 */
	this.func = args.func || $EF;
	/**
	 * @ignore
	 */
	this.msg = args.msg;
};
RuleValidator.extend(Validator, 'Validator');

/**
 * Comparison rule between 2 form fields
 * @type Number
 */
RuleValidator.PEER_FIELD = 1;
/**
 * Comparison rule between a form field and a literal
 * @type Number
 */
RuleValidator.PEER_VALUE = 2;

/**
 * Retrieve the error message based on the instance params
 * @type String
 */
RuleValidator.prototype.getErrorMessage = function() {
	if (this.msg)
		return this.msg;
	var m = Lang.validator, lbl = this.fldLabel;
	var v = (this.peerType == RuleValidator.PEER_VALUE);
	switch (this.ruleType) {
		case 'REGEX' : return m.invalidField.assignAll(lbl);
		case 'JSFUNC' : return m.invalidField.assignAll(lbl);
		case 'EQ' : return (v ? m.eqValue.assignAll(lbl, this.peer) : m.eqField.assignAll(lbl, this.peerLabel));
		case 'NEQ' : return (v ? m.neqValue.assignAll(lbl, this.peer) : m.neqField.assignAll(lbl, this.peerLabel));
		case 'GT' : return (v ? m.gtValue.assignAll(lbl, this.peer) : m.gtField.assignAll(lbl, this.peerLabel));
		case 'GOET' : return (v ? m.goetValue.assignAll(lbl, this.peer) : m.goetField.assignAll(lbl, this.peerLabel));
		case 'LT' : return (v ? m.ltValue.assignAll(lbl, this.peer) : m.ltField.assignAll(lbl, this.peerLabel));
		case 'LOET' : return (v ? m.loetValue.assignAll(lbl, this.peer) : m.loetField.assignAll(lbl, this.peerLabel));
		default : return m.requiredField.assignAll(this.fldLabel);
	}
};

/**
 * Internal comparison method
 * @param {String} op Comparison operator
 * @access private
 * @type Boolean
 */
RuleValidator.prototype.compare = function(op) {
	var trg, src = (this.fld.getValue() || '').toString();
	if (this.peerType == RuleValidator.PEER_FIELD) {
		trg = (this.peer.getValue() || '').toString();
		if (src.trim() == "" && trg.trim() == "")
			return true;
	} else {
		trg = this.peer;
		if (src.trim() == "")
			return true;
	}
	// transform currency values
	try {
		if (this.fld.getMask() == CurrencyMask)
			src = src.replace(".", "").replace(",", ".");
		if (this.peerType == RuleValidator.PEER_FIELD && this.peer.getMask() == CurrencyMask)
			trg = trg.replace(".", "").replace(",", ".");
	} catch(e) {}
	return PHP2Go.compare(src, trg, op || this.ruleType, this.dataType);
};

/**
 * Executes the validation routine
 * @type Boolean
 */
RuleValidator.prototype.validate = function() {
	var f = this.fld, p = this.peer;
	switch (this.ruleType) {
		case 'REGEX' :
			return (f.isEmpty() || p.test(f.getValue()));
		case 'JSFUNC' :
			return this.func(f.fld || f.grp);
		case 'EQ' :
		case 'NEQ' :
		case 'GT' :
		case 'GOET' :
		case 'LT' :
		case 'LOET' :
			return this.compare();
		case 'REQIF' :
			return (!f.isEmpty() || p.isEmpty());
		case 'REQIFEQ' :
		case 'REQIFNEQ' :
		case 'REQIFGT' :
		case 'REQIFGOET' :
		case 'REQIFLT' :
		case 'REQIFLOET' :
			return (!f.isEmpty() || p.isEmpty() || !PHP2Go.compare(p.getValue(), this.peerValue, this.ruleType.substring(5), this.dataType));
	}
};

/**
 * The FormValidator class executes validation routines on fields of a given form.
 * It's able to execute a chain of validators, collecting their results and error messages.
 * The errors summary can be displayed using alert boxes or DHTML
 * @constructor
 * @param {Object} frm Form reference or id
 */
FormValidator = function(frm) {
	/**
	 * Form reference
	 * @type Object
	 */
	this.frm = $(frm) || document.forms[frm];
	/**
	 * Set of validators
	 * @type Array
	 */
	this.validators = [];
	/**
	 * Set of error messages
	 * @type Array
	 */
	this.messages = [];
	/**
	 * Set of empty mandatory fields
	 * @type Array
	 */
	this.emptyLabels = [];
	/**
	 * Error summary properties
	 * @type Object
	 */
	this.errorDisplayOptions = {
		header: Lang.validator.invalidFields,
		mode: FormValidator.MODE_ALERT,
		list: FormValidator.LIST_FLOW,
		showAll: true,
		target: null,
		nl: "\n",
		ls: "---------------------------------------------------------------------------------\n"
	};
};

/**
 * Errors summary must be reported using an alert box
 * @type Number
 */
FormValidator.MODE_ALERT = 1;
/**
 * Errors summary must be displayed inside some page's node (inline)
 * @type Number
 */
FormValidator.MODE_DHTML = 2;
/**
 * Summary items must be displayed inside an HTML table
 * @type Number
 */
FormValidator.LIST_FLOW = 1;
/**
 * Unordered lists must be used to display the summary items
 * @type Number
 */
FormValidator.LIST_BULLET = 2;

/**
 * Configures how validation error(s) must be displayed
 * @param {Number} mode Display mode (MODE_ALERT or MODE_DHTML)
 * @param {Object} target Container node (id or reference)
 * @param {Boolean} showAll Show all errors or just the first one
 * @param {Number} list List mode (LIST_FLOW or LIST_BULLET)
 * @param {String} header Summary header
 * @type void
 */
FormValidator.prototype.setErrorDisplayOptions = function(mode, target, showAll, list, header) {
	// using a container node (DHTML)
	var opt = this.errorDisplayOptions;
	opt.showAll = !!showAll;
	if (mode == FormValidator.MODE_DHTML) {
		var trg = $(target);
		if (trg) {
			opt.mode = mode;
			opt.ls = "";
			opt.nl = "<br>";
			opt.target = trg;
			if (list == FormValidator.LIST_FLOW || list == FormValidator.LIST_BULLET)
				opt.list = list;
		}
	}
	// using an alert box
	else if (mode == FormValidator.MODE_ALERT) {
		opt.mode = mode;
		opt.ls = "----------------------------------------------------------------------------\n";
		opt.nl = "\n";
	}
	if (header != null)
		opt.header = header;
};

/**
 * Adds a new validator in the validation chain. For more details
 * about the validator arguments, please consult project docs
 * @param {String} field Field name
 * @param {Function} validator Validator
 * @param {Object} args Hash of validator arguments
 * @type void
 */
FormValidator.prototype.add = function(field, validator, args) {
	if (this.frm && typeof(validator) == 'function') {
		// setup arguments
		args = args || {};
		args.form = this.frm;
		args.field = field;
		// add validator
		this.validators.push(new validator(args));
	}
};

/**
 * Register an onsubmit event listener in the form. This listener
 * is bound with the {@link FormValidator#run} method
 * @type void
 */
FormValidator.prototype.setup = function() {
	// tie the validator with its form
	var frm = this.frm;
	frm.validator = this;
	if (!frm.ajax) {
		// register onsubmit event listener
		Event.addListener(frm, 'submit', function(e) {
			frm.validator.run(e);
		});
	}
	// register onreset event listener
	Event.addListener(frm, 'reset', function(e) {
		frm.validator.clearErrors();
	});

};

/**
 * Used by the forms API to apply transformations in some fields
 * values (upper, lower, trim, capitalize) before submission
 * @param {FormValidator} self FormValidator instance
 * @type void
 */
FormValidator.prototype.onBeforeValidate = function(self) {
};

/**
 * This abstract method can be overriden to
 * define extra validation routines. It's also
 * used by PHP2Go forms API to evalute the VALIDATEFUNC
 * attribute from the XML specification
 * @param {FormValidator} self FormValidator instance
 * @type Boolean
 */
FormValidator.prototype.onAfterValidate = function(self) {
	return true;
};

/**
 * This method runs all the registered validators, collecting
 * the produced error messages. For validators that validate
 * field's obligatoriness, the field label is also pushed onto
 * a stack. The first empty and the first invalid fields
 * are detected, so that we can determine which field must
 * focused when displaying the errors summary
 * @param {Event} e Event reference
 * @type void
 */
FormValidator.prototype.run = function(e) {
	e = $EV(e);
	this.messages = [];
	this.emptyLabels = [];
	var res = true, valid = true;
	var ept, inv, items = this.validators;
	// execute onBeforeValidate trigger
	this.onBeforeValidate(this, this.frm);
	// execute validation chain
	for (var i=0; i<items.length; i++) {
		valid = items[i].validate();
		res = res && valid;
		if (!valid) {
			this.messages.push(items[i].getErrorMessage());
			// register first invalid field and cancel event
			if (!inv) {
				inv = items[i].fld;
				(e && e.preventDefault());
			}
			// if only the first error must be displayed
			if (!this.errorDisplayOptions.showAll)
				break;
			// register empty field, and first empty field
			if (items[i].isMandatory()) {
				this.emptyLabels.push(items[i].fldLabel);
				(!ept) && (ept = items[i].fld);
			}
		}
	}
	// execute onAfterValidate event trigger function
	if (!this.onAfterValidate(this)) {
		res = false;
		(e && e.preventDefault());
	}
	if (!res) {
		(!this.emptyLabels.empty() || !this.messages.empty()) && (this.showErrors());
		(ept || inv) && ((ept || inv).focus());
		return false;
	} else {
		this.clearErrors();
	}
	return true;
};

/**
 * Builds a string buffer containing the error message(s)
 * @type String
 */
FormValidator.prototype.buildErrors = function() {
	var buf = "", m = Lang.validator, opt = this.errorDisplayOptions;
	var bullets = (opt.list == FormValidator.LIST_BULLET);
	var dhtml = (opt.mode == FormValidator.MODE_DHTML);
	// if we must display only the first error message
	if (!opt.showAll)
		return this.messages[0];
	// if there are any empty required fields, display them first
	if (this.emptyLabels.length > 0) {
		if (dhtml) {
			if (opt.header != "") {
				buf += opt.header;
				if (!bullets)
					buf += opt.nl;
			}
			if (bullets)
				buf += "<ul>";
			this.emptyLabels.walk(function(item, idx) {
				if (bullets)
					buf += "<li>" + m.requiredField.assignAll(item) + "</li>";
				else
					buf += m.requiredField.assignAll(item) + opt.nl;
			});
			if (bullets)
				buf += "</ul>";
			buf += opt.ls;
		} else {
			buf += m.requiredFields + opt.nl + opt.ls;
			this.emptyLabels.walk(function(item, idx) {
				buf += item + opt.nl;
			});
			buf += opt.ls + m.completeFields;
		}
	} else {
		if (dhtml) {
			if (opt.header != "") {
				buf += opt.header;
				if (!bullets)
					buf += opt.nl;
			}
			if (bullets)
				buf += "<ul>";
			this.messages.walk(function(item, idx) {
				if (bullets)
					buf += "<li>" + item + "</li>";
				else
					buf += item + opt.nl;
			});
			if (bullets)
				buf += "</ul>";
			buf += opt.ls;
		} else {
			if (opt.header != "")
				buf += opt.header.stripTags() + opt.nl + opt.ls;
			this.messages.walk(function(item, idx) {
				buf += item + opt.nl;
			});
			buf += opt.ls + m.fixFields;
		}
	}
	return buf;
};

/**
 * Shows the error message(s) using an alert box (MODE_ALERT)
 * or a given target node (MODE_DHTML)
 * @type void
 */
FormValidator.prototype.showErrors = function() {
	var opt = this.errorDisplayOptions;
	if (opt.mode == FormValidator.MODE_ALERT) {
		alert(this.buildErrors());
	} else {
		var trg = $(opt.target);
		if (trg) {
			trg.update(this.buildErrors());
			trg.show();
			window.scrollTo(0, trg.getPosition().y);
		}
	}
};

/**
 * Clears the target node used to display the error message(s)
 * @type void
 */
FormValidator.prototype.clearErrors = function() {
	var opt = this.errorDisplayOptions;
	if (opt.mode == FormValidator.MODE_DHTML) {
		var trg = $(opt.target);
		if (trg) {
			trg.clear();
			trg.hide();
		}
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'validator.js'] = true;

}