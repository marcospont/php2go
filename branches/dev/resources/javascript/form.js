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
 * This file contains the basic form API of this framework.
 * The form singleton contains utility functions to deal with
 * forms and form fields. The Field class hierarchy encapsulates
 * tasks of getting/setting/clearing value, disabling/enabling,
 * requiring focus or serializing based on the field type
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form.js']) {

/**
 * @class Form
 */
var Form = {
	/**
	 * Retrieve a form object from its name
	 * @param {String} formName Form name
	 * @type Object
	 */
	get : function(formName) {
		return document.forms[formName];
	},
	/**
	 * Returns the array of elements of this form, indexed by name
	 * @param {Object} form Form instance or id
	 * @type Array
	 */
	getElements : function(form) {
		form = $(form);
		if (form)
			return form.elements;
		return [];
	},
	/**
	 * Returns a hash table of fields of a form, indexed by name.
	 * Here, buttons and auxiliary inputs used by form components
	 * are not listed. Only the top elements declared in the XML
	 * specification might be returned
	 * @param {Object} form Form instance or id
	 * @type Hash
	 */
	getFields : function(form) {
		var res = $H();
		form = $(form);
		if (form) {
			$C(form.elements).walk(function(el, idx) {
				if (el.auxiliary == true || ((!el.type || !el.name) && !el.component) || (el.type && (/^submit|reset|button$/.test(el.type))))
					return;
				var key = (el.name || el.id);
				if (!res.containsKey(key))
					res.data[key] = Form.getField(form, key);
			});
		}
		return res;
	},
	/**
	 * Returns a single field of a form by name. The value returned
	 * is an instance of one of the Field classes, depending on the
	 * type and nature of the field. Instances for form fields are
	 * created only once
	 * @param {Object} form Form instance or id
	 * @param {String} name Field name
	 * @type Object
	 */
	getField : function(form, name) {
		if (form = $(form)) {
			var fld = form.elements[name];
			if (fld && !fld.type && fld.length)
				return Field.fromFormElement(fld[0]);
			return Field.fromFormElement(fld);
		}
		return null;
	},
	/**
	 * Clears the value of all form fields
	 * @param {Object} form Form instance or id
	 * @param {Array} prsv Field names to be preserved
	 * @type void
	 */
	clear : function(form, prsv) {
		if (form = $(form)) {
			var flds = this.getFields(form);
			var prsv = $A(prsv);
			flds.walk(function(el, idx) {
				if (el.key != '__form_signature' && !prsv.contains(el.key))
					el.value.clear();
			});
			if (form.validator)
				form.validator.clearErrors();
		}
	},
	/**
	 * Reset all the fields in the form to their default values
	 * @param {Object} Form instance or id
	 * @type void
	 */
	reset : function(form) {
		if (form = $(form))
			form.reset();
	},
	/**
	 * Enable all fields in a form
	 * @param {Object} form Form instance or id
	 * @param {Array} prsv Field names to be skipped
	 * @type void
	 */
	enableAll : function(form, prsv) {
		form = $(form), prsv = $A(prsv);
		var flds = this.getFields(form);
		flds.walk(function(el, idx) {
			if (!prsv.contains(el.name))
				el.value.enable();
		});
	},
	/**
	 * Enable one or more fields in a given form.
	 * This method excepts field names starting from
	 * the second parameter
	 * @param {Object} form Form instance or id
	 * @type void
	 */
	enable : function(form) {
		var list = $A(arguments).slice(1);
		list.walk(function(el, idx) {
			if (f = Form.getField(form, el))
				f.enable();
		});
	},
	/**
	 * Disable all fields in a form
	 * @param {Object} form Form instance or id
	 * @param {Array} prsv Field names to be preserved
	 * @type void
	 */
	disableAll : function(form, prsv) {
		form = $(form), prsv = $A(prsv);
		var flds = this.getFields(form);
		flds.walk(function(el, idx) {
			if (!prsv.contains(el.name))
				el.value.disable();
		});
	},
	/**
	 * Disable one or more fields in a given form.
	 * This method excepts field names starting from
	 * the second parameter
	 * @param {Object} form Form reference or id
	 * @type void
	 */
	disable : function(form) {
		var list = $A(arguments).slice(1);
		list.walk(function(el, idx) {
			if (f = Form.getField(form, el))
				f.disable();
		});
	},
	/**
	 * Serialize all the field's contents. The result is
	 * a string separated by & that can be used in query strings
	 * and HTTP requests
	 * @param {Object} form Form reference or id
	 * @param {Array} flds Optional list of fields to be serialized
	 */
	serialize : function(form, fld) {
		fld = fld || [];
		this.updateCheckboxes(form);
		return this.getFields(form).filter(function(el) {
			if (fld.empty() || fld.contains(el.key))
				return el.value.serialize();
		}).join('&');
	},
	/**
	 * Setup a given form to be submitted using AJAX. The
	 * second argument must be a function that returns
	 * a valid instance of an AJAX request class.
	 * @param {Object} form Form reference or id
	 * @param {Function} Function that builds the AJAX handler
	 */
	ajaxify : function(form, ajax) {
		form = $(form);
		if (form && Object.isFunc(ajax)) {
			form.ajax = ajax;
			Event.addListener(form, 'submit', function(e) {
				var evt = $EV(e), frm = null;
				evt.stop();
				frm = Element.getParentByTagName(evt.element(), 'form');
				if (frm) {
					var ajax = frm.ajax();
					ajax.form = frm.id;
					ajax.formValidate = true;
					ajax.send();
				}
			}, true);
		}
	},
	/**
	 * Move focus to the first enable field of a form
	 * @param {Object} form Form reference or id
	 * @type void
	 */
	focusFirstField : function(form) {
		var flds = this.getFields(form).getValues();
		flds.walk(function(el, idx) {
			if (el.type != 'hidden' && el.focus())
				throw $break;
		});
	},
	/**
	 * Update value and enable state of hidden fields
	 * associated with checkboxes before submission
	 * @param {Object} form Form reference or id
	 * @type void
	 */
	updateCheckboxes : function(form) {
		form = $(form);
		if (form) {
			function updateHidden(chk, idx) {
				var rel = $('V_'+chk.name);
				if (rel) {
					rel.value = (chk.checked?'T':'F');
					rel.disabled = chk.disabled;
				}
			}
			$C(form.elements).accept(function(item) {
				return (item.type == 'checkbox');
			}).walk(updateHidden);
		}
	}
};
Event.addListener(window, 'load', function() {
	var forms = $C(document.getElementsByTagName('form'));
	forms.walk(function(item, idx) {
		Event.addListener(item, 'submit', function() {
			Form.updateCheckboxes(item);
		});
	});
});

/**
 * This is the base class of the Field hierarchy.
 * Contains methods to set/get/clear the field value,
 * disable/enable, request focus and check if it's
 * empty
 * @constructor
 * @param {Object} fld Field object
 */
Field = function(fld) {
	/**
	 * Holds the form element instance
	 * @type Object
	 */
	this.fld = fld;
	/**
	 * Element id
	 * @type String
	 */
	this.id = (fld.id || null);
	/**
	 * Element name
	 * @type String
	 */
	this.name = (fld.name || fld.id);
	/**
	 * Element label
	 */
	this.label = (fld.title || null);
	/**
	 * Element type
	 * @type String
	 */
	this.type = (fld.type || null);
};

/**
 * Create a Field object from a given form element
 * @param {Object} elm Form input element
 * @return Field
 */
Field.fromFormElement = function(elm) {
	elm = $(elm);
	if (elm) {
		if (elm.component) {
			return elm.component;
		} else if (elm.tagName.equalsIgnoreCase('select')) {
			elm.component = new SelectField(elm);
			return elm.component;
		} else if (elm.type == 'radio' || elm.type == 'checkbox') {
			var grp = elm.form.elements[elm.name];
			if (grp.length || /\[\]$/.test(elm.name))  {
				var comp = new GroupField(grp);
				return comp;
			} else {
				elm.component = new InputSelectorField(elm);
				return elm.component;
			}
		} else if (elm.name && elm.type) {
			elm.component = new InputField(elm);
			return elm.component;
		}
	}
	return null;
};

/**
 * Retorna o elemento HTML associado ao objeto
 * @type {Object}
 */
Field.prototype.getFormElement = function() {
	return this.fld;
};

/**
 * Retrieve the mask object associated with the field
 * @type Mask
 */
Field.prototype.getMask = function() {
	if (this.fld && this.fld.inputMask)
		return this.fld.inputMask.mask;
	return null;
};

/**
 * Returns the current value of the field
 * @type Object
 */
Field.prototype.getValue = function() {
	return (this.fld.value || null);
};

/**
 * Sets the value of the field
 * @param {Object} val New value
 * @type void
 */
Field.prototype.setValue = function(val) {
	this.fld.value = val;
	if (this.fld.onchange)
		this.fld.onchange();
};

/**
 * Clears the value of the field
 * @type void
 */
Field.prototype.clear = function() {
	if (!this.fld.readOnly)
		this.fld.value = '';
};

/**
 * Check whether the field contains an empty value
 * @type Boolean
 */
Field.prototype.isEmpty = function() {
	return (this.getValue() == null);
};

/**
 * Enables the field
 * @type void
 */
Field.prototype.enable = function() {
	this.setDisabled(false);
};

/**
 * Disables the field
 */
Field.prototype.disable = function() {
	this.setDisabled(true);
};

/**
 * Internal method to disable/enable the field
 * @param {Boolean} b Flag value
 * @type void
 * @private
 */
Field.prototype.setDisabled = function(b) {
	this.fld.disabled = b;
};

/**
 * Move focus to the field. The method
 * won't execute on disabled inputs
 */
Field.prototype.focus = function() {
	if (this.beforeFocus() && !this.fld.disabled) {
		this.fld.focus();
		return true;
	}
	return false;
};

/**
 * Perform tasks and run validations
 * before activating the field
 * @param {Object} Field element
 * @type Bool
 */
Field.prototype.beforeFocus = function() {
	var elm = (this instanceof GroupField ? this.grp[0] : this.fld);
	while (elm = elm.parentNode) {
		if (elm.tabPanel && !elm.tabPanel.isActive()) {
			if (!elm.tabPanel.isEnabled())
				return false;
			elm.tabPanel.activate();
		}
	}
	return true;
};

/**
 * Serialize field's name and value so that
 * the returning value can be used to build
 * query strings or HTTP requests
 * @type String
 */
Field.prototype.serialize = function() {
	if (this.fld.disabled == true)
		return null;
	var nm = this.name.replace(/\[\]$/, '');
	var self = this, v = this.getValue();
	if (v != null && v != '') {
		if (v.constructor == Array) {
			return v.map(function(el) {
				return nm + '[]=' + el.urlEncode();
			}).join('&');
		} else {
			return this.name + '=' + v.urlEncode();
		}
	}
	return null;
};

/**
 * This class extends the basic Field class
 * and it's used in text form inputs: text,
 * password and textarea
 * @constructor
 * @base Field
 * @param {Object} fld Field object
 */
InputField = function(fld) {
	this.Field(fld);
	this.fld.component = this;
};
InputField.extend(Field, 'Field');

/**
 * Verify if the trimmed value of the field is empty
 * @type void
 */
InputField.prototype.isEmpty = function() {
	return (this.fld.value.trim() == '');
};

/**
 * Retrieve information about the field's selection
 * @type Object
 */
InputField.prototype.getSelection = function() {
	return FieldSelection.get(this.fld);
};

/**
 * This Field child class is used with selector
 * fields: radio buttons and checkboxes
 * @constructor
 * @base Field
 * @param {Object} fld Field object
 */
InputSelectorField = function(fld) {
	this.Field(fld);
	this.fld.component = this;
};
InputSelectorField.extend(Field, 'Field');

/**
 * Returns the value of the input. If the current
 * status of the control is "not checked", the value
 * returned is null
 * @type String
 */
InputSelectorField.prototype.getValue = function() {
	if (this.fld.type == 'checkbox') {
		var peer = $('V_'+this.fld.name);
		if (peer)
			return peer.value;
	}
	return (this.fld.checked ? this.fld.value : null);
};

/**
 * Changes the checked status of the input
 * @param {Boolean} b New checked status
 * @type void
 */
InputSelectorField.prototype.setChecked = function(b) {
	this.fld.checked = !!b;
};

/**
 * Unchecks the input
 * @type void
 */
InputSelectorField.prototype.clear = function() {
	this.fld.checked = false;
};

/**
 * Verifies if the input is unchecked
 * @type Boolean
 */
InputSelectorField.prototype.isEmpty = function() {
	return (this.fld.checked == false);
};

/**
 * Class used to encapsulate operations over select
 * inputs, accepting single or multiple choice
 * @constructor
 * @base Field
 * @param {Object} fld Field object
 */
SelectField = function(fld) {
	this.Field(fld);
	this.fld.component = this;
	/**
	 * Indicates if the field accepts multiple choice
	 * @type Boolean
	 */
	this.multiple = (this.type != 'select-one');
};
SelectField.extend(Field, 'Field');

/**
 * Gets the current selected option(s). In multiple
 * choice select inputs, this method returns an array
 * of values
 * @type Object
 */
SelectField.prototype.getValue = function() {
	if (this.multiple) {
		var res = $C(this.fld.options).filter(function(el) {
			if (el.selected == true)
				return el.value;
			return null;
		});
		return (res.empty() ? null : res);
	} else {
		var idx = this.fld.selectedIndex;
		if (idx >= 0 && this.fld.options[idx].value != "")
			return this.fld.options[idx].value;
		return null;
	}
};

/**
 * Changes the selected option(s) in the field. For multiple
 * choice inputs, expects an array parameter
 * @param {Object} val New value
 * @type void
 */
SelectField.prototype.setValue = function(val) {
	var self = this, val = (this.multiple ? $A(val) : val);
	$C(this.fld.options).walk(function(el, idx) {
		if (self.multiple) {
			el.selected = (val.contains(el.value) ? true : false);
		} else if (el.value == val) {
			el.selected = true;
			self.fld.value = el.value;
			self.fld.selectedIndex = idx;
			throw $break;
		}
	});
	if (this.fld.onchange)
		this.fld.onchange();
};

/**
 * Select an option by its text
 * @param {String} text Option text
 * @type void
 */
SelectField.prototype.selectByText = function(text) {
	$C(this.fld.options).walk(function(el, idx) {
		(el.text == text) && (el.selected = true);
	});
};

/**
 * Unselect all selected options in the list
 * @type void
 */
SelectField.prototype.clear = function() {
	if (!this.multiple)
		this.fld.value = '';
	$C(this.fld.options).walk(function(el, idx) {
		el.selected = false;
	});
};

/**
 * Remove all options off the select field
 * @type void
 */
SelectField.prototype.clearOptions = function() {
	this.fld.options.length = 0;
};

/**
 * Change the first option of the select field
 * @param {String} value Option value
 * @param {String} text Option text
 * @type void
 */
SelectField.prototype.setFirstOption = function(value, text) {
	var o = this.fld.options;
	o[0] = new Option(text, value);
	(o.length == 0) && (o.length = 1);
};

/**
 * Add a new option in the select field
 * @param {String} value Option value
 * @param {String} text Option text
 * @param {Number} pos Option position (defaults to last position)
 * @type void
 */
SelectField.prototype.addOption = function(value, text, pos) {
	var i, f = this.fld;
	pos = Math.abs(Object.ifUndef(pos, f.options.length));
	if (f.add) {
		if (pos < f.options.length) {
			try { // insert before (W3C standard)
				f.add(new Option(text, value), f.options[pos]);
			} catch(e) { // insert at position (IE)
				f.add(new Option(text, value), pos);
			}
		} else {
			f.options[pos] = new Option(text, value);
		}
	} else {
		for (i=f.options.length; i>pos; i--)
			f.options[i] = f.options[i-1];
		f.options[pos] = new Option(text, value);
	}
};

/**
 * Import options to the select field from a string splitted by
 * line and column separators. The default separators are "|" for
 * a line and "~" for value and text. Ex: "1~One|2~Two|3~Three".
 * The index on where the options must be added can be specified
 * on the 4th parameter
 * @param {String} str Options string
 * @param {String} lsep Line separator
 * @param {String} csep Column separator
 * @param {Number} pos Insertion position
 * @param {String} val Value to set
 * @type void
 */
SelectField.prototype.importOptions = function(str, lsep, csep, pos, val) {
	lsep = (lsep || '|'), csep = (csep || '~'), val = (val || this.fld.value);
	pos = Math.abs(pos || 0);
	if (pos <= this.fld.options.length) {
		var self = this;
		self.fld.options.length = pos;
		str.split(lsep).walk(function(el, idx) {
			var opt = el.split(csep);
			if (opt.length >= 2) {
				self.fld.options[pos] = new Option(opt[1], opt[0]);
				pos++;
			}
			if (val && val == opt[0])
				self.fld.value = val;
		});
	}
};

/**
 * Remove an option from the list
 * @param {Number} idx Option index
 * @type void
 */
SelectField.prototype.removeOption = function(idx) {
	var i, f = this.fld, idx = Math.abs(idx);
	var r = Object.isFunc(f.remove);
	if (idx >= f.options.length)
		return;
	// DOM browsers
	if (r) {
		f.remove(idx);
	// older browsers
	} else {
		for (i=idx; i<(f.options.length-1); i++) {
			f.options[i].value = f.options[i+1].value;
			f.options[i].text = f.options[i+1].text;
			f.options[i].selected = f.options[i+1].selected;
		}
		f.options.length--;
	}
};

/**
 * Remove the selected options from the list. This method is
 * used by EditSelection and LookupSelection form components
 * @param {Function} func Callback for each removed option
 * @param {Number} idx Start index. Defaults to 0
 * @type void
 */
SelectField.prototype.removeSelectedOptions = function(func, idx) {
	var i, c, j, k, f = this.fld;
	var r = Object.isFunc(f.remove);
	func = func || $EF, idx = Object.ifUndef(idx, 0);
	for (i=idx; i<f.options.length; i++) {
		if (f.options[i].selected == true) {
			// DOM browsers
			if (r) {
				try {
					func(f.options[i]);
				} catch(e) {
					if (e == $break) break;
					if (e == $continue) continue;
				}
				f.remove(i);
				i--;
			// older browsers
			} else {
				c = 0, j = i;
				while ((j < f.options.length) && (f.options[j].selected == true)) {
					try {
						func(f.options[j]);
					} catch(e) {
						if (e == $break) break;
						if (e == $continue) continue;
					}
					c++, j++;
				}
				if (f.options.length > (i+c)) {
					for (k=i; k<(f.options.length-c); k++) {
						f.options[k].value = f.options[k+c].value;
						f.options[k].text = f.options[k+c].text;
						f.options[k].selected = f.options[k+c].selected;
					}
				}
				f.options.length = f.options.length - c;
			}
		}
	}
};

/**
 * The GroupField class encapsulate operations over
 * grouped elements, which are sets of form inputs
 * with the same name, accepting single choice (radio
 * button groups) or multiple choice (checkbox groups)
 * @constructor
 * @param {Object} grp Group of fields
 */
GroupField = function(grp) {
	/**
	 * Members of the group
	 * @type Array
	 */
	this.grp = $A(grp);
	var self  = this;
	this.grp.walk(function(item, idx) {
		item.component = self;
	});
	/**
	 * Name of the group
	 * @type String
	 */
	this.name = this.grp[0].name;
	/**
	 * Label of the group
	 * @type String
	 */
	this.label = (this.grp[0].title || null);
	/**
	 * Type of the group members
	 * @type String
	 */
	this.type = this.grp[0].type;
	/**
	 * The group accepts multiple choice or not
	 * @type Boolean
	 */
	this.multiple = (this.type == 'checkbox');
};
GroupField.extend(Field, 'Field');

/**
 * Return the current value of the group. For multiple
 * choice groups, the value returned is an array
 * @type Object
 */
GroupField.prototype.getValue = function() {
	var v = this.grp.filter(function(el) {
		if (el.checked)
			return el.value;
		return null;
	});
	if (v.empty())
		return null;
	return (this.multiple ? v : v[0]);
};

/**
 * Return the array of group members
 * @type Array
 */
GroupField.prototype.getFormElement = function() {
	return this.grp;
};

/**
 * Changes the selected option(s) of the group.
 * For multiple choice groups, the expected parameter
 * is an array of values
 * @param {Object} val New value
 * @type void
 */
GroupField.prototype.setValue = function(val) {
	var self = this, val = (this.multiple ? $A(val) : val);
	this.grp.walk(function(el, idx) {
		if (self.multiple) {
			el.checked = val.contains(el.value);
			if (el.onchange)
				el.onchange();
		} else if (el.value == val) {
			el.checked = true;
			if (el.onchange)
				el.onchange();
			throw $break;
		}
	});
};

/**
 * Unselect the selected option(s) in the group
 * @type void
 */
GroupField.prototype.clear = function() {
	this.setAll(false);
};

/**
 * Invert group members state. Returns the
 * number of checked inputs after the inversion
 * @type Number
 */
GroupField.prototype.invert = function() {
	var res = null;
	if (this.multiple) {
		res = 0;
		this.grp.walk(function(el, idx) {
			el.checked = !el.checked;
			res += (el.checked?1:0);
		});
	}
	return res;
};

/**
 * Change all group members to a given state
 * @param {Boolean} b State
 * @type void
 */
GroupField.prototype.setAll = function(b) {
	b = !!b;
	this.grp.walk(function(el, idx) {
		el.checked = b;
	});
};

/**
 * Verifies if the group has a selected value (or values)
 * @type Boolean
 */
GroupField.prototype.isEmpty = function() {
	var empty = true;
	this.grp.walk(function(el, idx) {
		if (el.checked) {
			empty = false;
			throw $break;
		}
	});
	return empty;
};

/**
 * Enables/disables all elements in the group
 * @type void
 */
GroupField.prototype.setDisabled = function(b) {
	this.grp.walk(function(el, idx) {
		el.disabled = b;
	});
};

/**
 * Move the focus to the first enabled member of the group
 * @type void
 */
GroupField.prototype.focus = function() {
	if (!this.beforeFocus())
		return false;
	var found = false;
	this.grp.walk(function(el, idx) {
		if (!el.disabled) {
			el.focus();
			found = true;
			throw $break;
		}
	});
	return found;
};

/**
 * Serialize the name of the group and the
 * selected value(s). The returning value could
 * be used to build query strings
 * or HTTP requests
 * @type String
 */
GroupField.prototype.serialize = function() {
	var self = this, nm = this.name.replace(/\[\]$/, '');
	var v = this.grp.filter(function(el) {
		if (el.checked && !el.disabled) {
			if (self.multiple)
				return nm + '[]=' + el.value.urlEncode();
			return nm + '=' + el.value.urlEncode();
		}
		return null;
	});
	return (v.empty() ? null : v.join('&'));
};

/**
 * Base class for all specialized form components.
 * This class methods should be implemented by each
 * form component
 * @constructor
 * @base Field
 * @param {Object} Component top element's name or reference
 * @param {String} Component class name
 */
ComponentField = function(fld, clsName) {
	this.Field(fld);
	/**
	 * Component class name
	 * @type String
	 */
	this.componentClass = clsName;
	/**
	 * Component's event listeners
	 * @type Object
	 */
	this.listeners = {};
};
ComponentField.extend(Field, 'Field');

/**
 * Register a new event listener in the component
 * @param {String} name Event name
 * @param {Function} func Handler function
 * @type void
 */
ComponentField.prototype.addEventListener = function(name, func) {
	this.listeners[name] = this.listeners[name] || [];
	this.listeners[name].push(func.bind(this));
};

/**
 * Used to raise events inside the component. Searches
 * for a handler function and call it if it exists
 * @param {String} name Event name
 * @param {Array} args Event arguments
 * @type void
 */
ComponentField.prototype.raiseEvent = function(name, args) {
	var funcs = this.listeners[name] || [];
	for (var i=0; i<funcs.length; i++) {
		funcs[i](args);
	}
	if (this.fld && Object.isFunc(this.fld['on'+name]))
		this.fld['on'+name]();
};

/**
 * Hooks events to a group of checkboxes and controls that
 * perform actions on these checkboxes
 * @param {String} frm Form name
 * @param {String} name Group name (including [] chars)
 * @param {Object} opts Options
 * @constructor
 */
CheckboxController = function(frm, name, opts) {
	/**
	 * Group of checkboxes
	 * @type GroupField
	 */
	this.group = $FF(frm, name);
	/**
	 * ID or reference to the "Check All" element
	 * @type Object
	 */
	this.all = (opts.all ? $(opts.all) : null);
	/**
	 * ID or reference to the "Uncheck All" element
	 * @type Object
	 */
	this.none = (opts.none ? $(opts.none) : null);
	/**
	 * ID or reference to the "Toggle All" element
	 * This element, in particular, must be a checkbox input
	 * @type Object
	 */
	this.toggle = (opts.toggle && (opts.toggle.type || '') == 'checkbox' ? $(opts.toggle) : null);
	/**
	 * ID or reference to the "Invert Selection" element
	 * @type Object
	 */
	this.invert = (opts.invert ? $(opts.invert) : null);
	/**
	 * Enabler function. Receives "false" when all boxes are unchecked, and true otherwise
	 * @type Function
	 */
	this.enabler = (Object.isFunc(opts.enabler) ? opts.enabler : null);
	if (this.group && (this.all||this.none||this.toggle||this.invert||this.enabler))
		this.setupEvents();
};

/**
 * Setup all necessary event handlers
 * @type void
 */
CheckboxController.prototype.setupEvents = function() {
	var self = this;
	var inputs = self.group.grp;
	var boxChanged = function(e) {
		e = (e||window.event);
		var box = (e.target||e.srcElement);
		var val = self.group.getValue();
		if (box.checked) {
			(self.enabler) && (self.enabler(true));
			if (val.length == inputs.length && self.toggle)
				self.toggle.checked = true;
		} else {
			(self.toggle) && (self.toggle.checked = false);
			if (val == null && self.enabler)
				self.enabler(false);
		}
	};
	for (var i=0; i<inputs.length; i++)
		Event.addListener(inputs[i], 'click', boxChanged);
	if (self.invert) {
		Event.addListener(self.invert, 'click', function(e) {
			var res = self.group.invert();
			(self.toggle) && (self.toggle.checked = (res == inputs.length));
			(self.enabler) && (self.enabler(res > 0));
		});
	}
	var setAll = function(b) {
		self.group.setAll(b);
		(self.toggle) && (self.toggle.checked = b);
		(self.enabler) && (self.enabler(b));
	};
	(self.all) && (Event.addListener(self.all, 'click', function(e) { setAll(true); }));
	(self.none) && (Event.addListener(self.none, 'click', function(e) { setAll(false); }));
	if (self.toggle) {
		self.toggle.auxiliary = true;
		Event.addListener(self.toggle, 'click', function(e) { setAll(self.toggle.checked); });
	}
};

/**
 * The FieldSelection class can be used to read
 * and change the selection of a given field.
 * It also can be used to change the cursor position
 * in a text or password field
 * @class FieldSelection
 */
var FieldSelection = {
	/**
	 * Add event listeners in the field so that we
	 * can have up-to-dated information about its
	 * selection when applying masks. This is only
	 * needed when running MS Internet Explorer
	 * @param {Object} elm Field
	 * @type void
	 */
	prepare : function(elm) {
		elm = $(elm);
		if (elm && elm.createTextRange) {
			var setRange = function(e) {
				elm.range = document.selection.createRange().duplicate();
			};
			Event.addListener(elm, 'click', setRange);
			Event.addListener(elm, 'dblclick', setRange);
			Event.addListener(elm, 'select', setRange);
			Event.addListener(elm, 'keyup', setRange);
			Event.addListener(elm, 'paste', setRange);
			setRange();
		}
	},
	/**
	 * Return the current selected text in a given field
	 * @param {Object} elm Field
	 * @type String
	 */
	get : function(elm) {
		elm = $(elm);
		if (elm && !elm.disabled) {
			if (elm.createTextRange) {
				if (!elm.range)
					this.prepare(elm);
				return elm.range.text;
			} else if (elm.setSelectionRange) {
				return elm.value.substring(elm.selectionStart, elm.selectionEnd);
			}
		}
		return "";
	},
	/**
	 * Retrieves information about the selection range
	 * of a given field. The value returned is an
	 * object containing 3 properties: start, end and size
	 * @param {Object} elm Field
	 * @type Object
	 */
	getRange : function(elm) {
		elm = $(elm);
		if (elm && !elm.disabled) {
			if (elm.setSelectionRange) {
				return { start : elm.selectionStart, end : elm.selectionEnd, size : (elm.selectionEnd - elm.selectionStart) };
			} else if (elm.createTextRange) {
				var range, start, end;
				if (!elm.range)
					this.prepare(elm);
				if (elm.range.parentElement() == elm) {
					range = elm.range.duplicate();
					range.moveStart('textedit', -1);
					end = range.text.length;
					start = end - elm.range.text.length;
					return { start: start, end: end, size: (end-start) };
				}
			}
		}
		return { start: 0, end: 0, size: 0 };
	},
	/**
	 * Change the selection range of a field
	 * @param {Object} elm Field
	 * @param {Number} start Selection start
	 * @param {Number} end Selection end
	 * @type void
	 */
	setRange : function(elm, start, end) {
		elm = $(elm);
		if (elm && !elm.disabled) {
			start = Math.max(start, 0);
			end = Math.min(end, elm.value.length);
			if (elm.createTextRange) {
				elm.focus();
				if (!elm.range) {
					this.prepare(elm);
				} else {
					elm.range.moveStart('textedit', -1);
					elm.range.moveEnd('textedit', -1);
				}
				elm.range.moveEnd('character', end);
				elm.range.moveStart('character', start);
				elm.range.select();
			} else if (elm.setSelectionRange) {
				elm.focus();
				elm.setSelectionRange(start, end);
			}
		}
	},
	/**
	 * Return the current caret position in a given field.
	 * Note that even when the selection size of the field
	 * is greater than 1, the caret position represents
	 * the selection start position
	 * @param {Object} elm Field
	 * @type Number
	 */
	getCaret: function(elm) {
		elm = $(elm);
		if (elm && !elm.disabled) {
			if (elm.setSelectionRange) {
				return elm.selectionStart;
			} else if (elm.createTextRange) {
				var range, end;
				if (!elm.range)
					this.prepare(elm);
				if (elm.range.parentElement() == elm) {
					range = elm.range.duplicate();
					range.moveStart('textedit', -1);
					if (range.text.length != elm.range.text.length) {
						end = range.text.length;
						return (end - elm.range.text.length);
					} else {
						return range.text.length;
					}
				}
			}
		}
		return 0;
	},
	/**
	 * Set the caret position of a field
	 * @param {Object} elm Field
	 * @param {Number} pos Caret position
	 * @type void
	 */
	setCaret : function(elm, pos) {
		this.setRange(elm, pos, pos);
	},
	/**
	 * Collapse the field's selection
	 * @param {Object} elm Field
	 * @param {Boolean} toStart Collapse to start (true) or to end (false). Defaults to true
	 * @type void
	 */
	collapse : function(elm, toStart) {
		elm = $(elm);
		if (elm && !elm.disabled) {
			toStart = Object.ifUndef(toStart, true);
			if (elm.createTextRange) {
				var range = elm.createTextRange();
				range.collapse(toStart);
				range.select();
			} else if (elm.setSelectionRange) {
				var pos = (toStart ? 0 : elm.value.length);
				elm.setSelectionRange(pos, pos);
				elm.focus();
			}
		}
	}
};

/**
 * Utility function to build a Field object
 * from a given form input element
 * @param {Object} elm Form element
 * @return Field
 */
$F = function(elm) {
	return Field.fromFormElement(elm);
};

/**
 * Utility function to retrieve a Field object
 * from a form name or reference and a field name
 * @param {Object} form Form instance or id
 * @param {String} field Field name
 * @type Field
 */
$FF = function(form, field) {
	return Form.getField(form, field);
};

/**
 * Shortcut function to read the value of a field
 * @param {Object} form Form instance or id
 * @param {String} field Field name
 * @type Object
 */
$V = function(form, field) {
	var f = Form.getField(form, field);
	return (f ? f.getValue() : null);
};

PHP2Go.included[PHP2Go.baseUrl + 'form.js'] = true;

}