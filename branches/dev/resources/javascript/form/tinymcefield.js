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
 * This file contains the TinyMCEField wrapper class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/tinymcefield.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'vendor/tinymce/jscripts/tiny_mce/tiny_mce.js');

/**
 * The TinyMCEField class is a wrapper that performs integration with the
 * third party library tinyMCE. Provides all necessary properties and methods
 * of a component field, such as getValue, setValue, disable, enable, ...
 * @constructor
 * @base ComponentField
 * @param {Object} fld Textares that must be transformed by tinyMCE
 * @param {Object} params tinyMCE parameters
 */
TinyMCEField = function(fld, params) {
	this.ComponentField(fld, 'TinyMCEField');
	/**
	 * @ignore
	 */
	this.mce = null;
	/**
	 * @ignore
	 */
	this.mustFocus = false;
	/**
	 * @ignore
	 */
	this.readOnly = false;
	/**
	 * @ignore
	 */
	this.type = 'iframe';
	this.setup(params);
};
TinyMCEField.extend(ComponentField, 'ComponentField');

/**
 * Performs all the setup routines of the HTML editor. This method
 * is called after the creation of the instance
 * @param {Object} params tinyMCE parameters
 * @type void
 */
TinyMCEField.prototype.setup = function(params) {
	this.fld.component = this;
	var self = this;
	if (params.readonly) {
		this.readOnly = true;
		params.readonly = 0;
	}
	params.oninit = function() {
		self.mce = tinyMCE.get(self.fld.id);
		self.mce.iframe = self.mce.contentAreaContainer.firstChild;
		self.mce.toolbars = $(self.mce.editorContainer).getElementsByClassName('mceToolbar');
		if (self.fld.disabled || self.readOnly) {
			self.setReadOnly(true);
		} else {
			if (self.mustFocus) {
				self.mce.focus();
				self.mustFocus = false;
			}
		}
	};
	tinyMCE.init(params);
};

/**
 * Return the contents of the editor as HTML
 * @type String
 */
TinyMCEField.prototype.getValue = function() {
	return this.mce.getContent();
};

/**
 * Return the contents of the editor as text
 * @type String
 */
TinyMCEField.prototype.getValueAsText = function() {
	var ml = RegExp.multiline;
	var text = this.getValue().stripTags().replace('&nbsp;', ' ');
	RegExp.multiline = ml;
	return text;
};

/**
 * Change the value of the component
 * @param {String} html New value
 * @type void
 */
TinyMCEField.prototype.setValue = function(html) {
	this.mce.setContent(html);
};

/**
 * Inserts HTML code in the current selection of the component
 * @param {String} html Code to be inserted
 * @type void
 */
TinyMCEField.prototype.insertHTML = function(html) {
	tinyMCE.execCommand('mceInsertContent', false, html);
};

/**
 * Clean up the component value
 * @type void
 */
TinyMCEField.prototype.clear = function() {
	this.mce.setContent('');
};

/**
 * Checks if the component value is empty
 * @type Boolean
 */
TinyMCEField.prototype.isEmpty = function() {
	var re = /<(img|input|hr)/i;
	var html = this.getValue();
	var text = this.getValueAsText().trim();
	return (text == "" && !re.test(html));
};

/**
 * Disables/enables read-only mode
 * @param {Boolean} ro Flag value
 * @param {Boolean} tb Show/hide toolbars
 * @type void
 */
TinyMCEField.prototype.setReadOnly = function(ro, tb) {
	var mce = this.mce, dom = tinymce.DOM, d = mce.getDoc();
	var tb = (typeof(tb) == 'undefined' ? true : !!tb);
	var tbl = mce.getContainer().firstChild;
	if (ro) {
		if (!tinymce.isIE) {
			try {
				d.designMode = 'Off';
			} catch(e) {
			}
		} else {
			var b = mce.getBody();
			DOM.hide(b);
			b.contentEditable = false;
			DOM.show(b);
		}
		if (tb) {
			var h = tbl.offsetHeight;
			for (var i=0; i<mce.toolbars.length; i++)
				mce.toolbars[i].style.display = 'none';
			mce.iframe.style.height = h + 'px';
		}
	} else {
		if (!tinyMCE.isIE) {
			try {
				d.designMode = 'On';
			} catch(e) {
			}
		} else {
			var b = mce.getBody();
			DOM.hide(b);
			b.contentEditable = true;
			DOM.show(b);
		}
		if (tb) {
			var h = mce.iframe.offsetHeight;
			for (var i=0; i<mce.toolbars.length; i++)
				mce.toolbars[i].style.display = '';
			mce.iframe.style.height = (h - (tbl.offsetHeight - h)) + 'px';
		}
	}
};

/**
 * Disables/enables the component
 * @param {Boolean} b Flag value
 * @type void
 */
TinyMCEField.prototype.setDisabled = function(b) {
	if (b) {
		this.setReadOnly(true);
	} else {
		if (!this.readOnly) {
			this.setReadOnly(false);
		} else {
			var mce = this.mce, tbl = mce.getContainer().firstChild;
			var h = mce.iframe.offsetHeight;
			for (var i=0; i<mce.toolbars.length; i++)
				mce.toolbars[i].style.display = '';
			mce.iframe.style.height = (h - (tbl.offsetHeight - h)) + 'px';
		}
	}
	this.fld.disabled = b;
};

/**
 * Move focus to the editor component
 * @type Boolean
 */
TinyMCEField.prototype.focus = function() {
	if (this.mce)
		this.mce.focus(false);
	else
		this.mustFocus = true;
};

/**
 * Serialize the component so that the value
 * can be used to perform HTTP requests or
 * to build query param strings
 * @type String
 */
TinyMCEField.prototype.serialize = function() {
	this.mce.save();
	if (this.fld.value != '')
		return this.name + '=' + this.fld.value.urlEncode();
	return null;
};

PHP2Go.included[PHP2Go.baseUrl + 'form/tinymcefield.js'] = true;

}
