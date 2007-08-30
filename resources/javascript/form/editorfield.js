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
 * This file contains the EditorField form component class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'form/editorfield.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'util/selection.js');
PHP2Go.include(PHP2Go.baseUrl + 'widgets/colorpicker.js');

/**
 * The EditorField class contain all the logic to enable WYSIWYG HTML editing
 * in an IFRAME element of the page. It controls mouse and keyboard events,
 * execute commands in the HTML contents and also implements all the necessary
 * methods of a field, such as getValue, setValue, disable, enable, ...
 * @constructor
 * @base ComponentField
 * @requires Selection
 * @param {Object} fld Hidden field linked with the HTML editor
 * @param {Boolean} opts Component options
 */
EditorField = function(fld, opts) {
	this.ComponentField(fld, 'EditorField');
	/**
	 * @ignore
	 */
	this.type = 'iframe';
	/**
	 * Holds a reference to the iframe
	 * used by the component
	 * @type Object
	 */
	this.iframe = $(this.name + '_iframe');
	/**
	 * The editable document
	 * @type Object
	 */
	this.document = (PHP2Go.browser.ie ? window.frames[this.name + '_iframe'].document : this.iframe.contentDocument);
	/**
	 * Textarea element used to show
	 * the HTML source code
	 * @type Object
	 */
	this.textarea = $(this.name + '_textarea');
	/**
	 * The checkbox used to switch between
	 * source and WYSIWYG modes
	 * @type Object
	 */
	this.switchMode = $(this.name + '_switch');
	/**
	 * The div used to show available emoticons
	 * @type Object
	 */
	this.divEmoticons = $(this.name + '_divemoticons');
	/**
	 * Special editor options
	 * @type Boolean
	 */
	this.config = opts || {};
	/**
	 * Holds resize control variables
	 * @type Object
	 */
	this.resizer = {
		resizing: false,
		minWidth: 100,
		minHeight: 100
	};
	/**
	 * Indicates if the component is in source mode (true) or WYSIWYG mode (false)
	 * @type Boolean
	 */
	this.textMode = false;
	/**
	 * Holds an instance of the Selection class.
	 * This instance is used to operate over
	 * the document selection
	 * @type Selection
	 */
	this.selection = (PHP2Go.browser.ie ? new Selection(this.document) : new Selection(this.iframe.contentDocument, this.iframe.contentWindow));
};
EditorField.extend(ComponentField, 'ComponentField');

/**
 * Performs all the setup routines of the HTML editor. This method
 * is called after the creation of the instance
 * @type void
 */
EditorField.prototype.setup = function() {
	this.fld.component = this;
	this.textarea.auxiliary = true;
	this.switchMode.auxiliary = true;
	$(this.name + '_formatblock', this.name + '_fontname', this.name + '_fontsize').walk(function(item, idx) {
		item.auxiliary = true;
	});
	this.setupOptions();
	this.setupDocument();
	this.setupEvents();
	// setup color picker widgets
	var self = this;
	new ColorPicker({
		mode: 'popup',
		trigger: this.name + '_pickforecolor',
		onOpen: function() {
			self.selection.save();
			self.divEmoticons.hide();
		},
		onSelect: function(color) {
			self.selection.restore();
			self.execCommand('forecolor', color);
		}
	});
	new ColorPicker({
		mode: 'popup',
		trigger: this.name + '_pickbackcolor',
		onOpen: function() {
			self.selection.save();
			self.divEmoticons.hide();
		},
		onSelect: function(color) {
			self.selection.restore();
			self.execCommand('backcolor', color);
		}
	});
	if (this.config.readOnly)
		this.setDisabled(true);
};

/**
 * Define default values to the editor
 * configuration options
 * @type void
 */
EditorField.prototype.setupOptions = function() {
	var self = this;
	var def = function(v, d) {
		self.config[v] = Object.ifUndef(self.config[v], d);
	};
	def('readOnly', false);
	def('styleSheet', null);
	def('resizeMode', 'none');
};

/**
 * Initialize the document with the linked field's
 * value and make it editable. This method is called
 * inside {@link EditorField#setup} method
 * @type void
 */
EditorField.prototype.setupDocument = function() {
	var doc = this.document;
	var html = "<html><head><title></title></head><body>" + this.fld.value + "</body></html>";
	if (PHP2Go.browser.ie) {
		doc.open('', 'replace');
		doc.write(html);
		doc.close();
		doc.designMode = 'on';
	} else {
		doc.open();
		doc.write(html);
		doc.close();
		doc.designMode = 'on';
	}
	try {doc.execCommand("useCSS", false, false);} catch (ex) {}
	try {doc.execCommand("styleWithCSS", false, true);} catch (ex) {}
	var self = this;
	// add stylesheet
	if (this.config.styleSheet) {
		if (doc.createStyleSheet) {
			setTimeout(function() {
				doc.createStyleSheet(self.config.styleSheet);
			}, 5);
		} else {
			var head, link = doc.createElement("link");
			link.rel = "stylesheet";
			link.type = "text/css";
			link.href = self.config.styleSheet;
			if (head = doc.getElementsByTagName('head'))
				head[0].appendChild(link);
		}
	}
	// read size from the cookies
	Event.addLoadListener(function() {
		var cw = Cookie.get('editor_'+self.name+'_width');
		var ch = Cookie.get('editor_'+self.name+'_height');
		if (cw || ch)
			self.resizeTo(parseInt(cw, 10), parseInt(ch, 10));
	});
};

/**
 * Configure all necessary event listeners. Toolbar
 * select inputs, toolbar buttons, switch mode checkbox
 * and document mouse/keyboard events. This method is
 * called inside {@link EditorField#setup} method
 * @type void
 */
EditorField.prototype.setupEvents = function() {
	var self = this, len = self.name.length + 1;
	var win = (PHP2Go.browser.ie ? window.frames[this.name + '_iframe'] : this.document);
	var handleEvent = function(evt) {
		evt = $EV(evt);
		self.raiseEvent(evt.type);
	};
	var handleKeyEvent = function(evt) {
		evt = $EV(evt);
		(!self.keyHandler(evt) ? evt.stop() : self.raiseEvent(evt.type));
	};
	// window events
	['focus', 'blur'].walk(function(item, idx) {
		Event.addListener(win, item, handleEvent);
	});
	// document events
	['click', 'dblclick', 'mouseup', 'mousedown', 'keypress', 'keydown', 'keyup', 'paste'].walk(function(item, idx) {
		if (item == 'keydown' || item == 'keypress')
			Event.addListener(self.document, item, handleKeyEvent);
		else
			Event.addListener(self.document, item, handleEvent);
	});
	// switch mode
	Event.addListener(self.switchMode, 'click', function(event) {
		self.changeMode(self.switchMode.checked);
	});
	// change block format, font name and font size
	$(self.name + '_formatblock', self.name + '_fontname', self.name + '_fontsize').walk(function(item, idx) {
		Event.addListener(item, 'change', function(event) {
			self.execCommand(item.name.substring(len), item.options[item.selectedIndex].value, $EV(event));
			item.options[0].selected = true;
		});
	});
	// top buttons
	$C($(this.name + '_top').getElementsByTagName('a')).walk(function(item, idx) {
		Event.addListener(item, 'click', function(event) {
			self.execCommand(item.name.substring(len), null, $EV(event));
		});
	});
	// bottom buttons
	$C($(this.name + '_bottom').getElementsByTagName('a')).walk(function(item, idx) {
		Event.addListener(item, 'click', function(event) {
			self.execCommand(item.name.substring(len), $EV(event));
		});
	});
	// emoticons
	$C(this.divEmoticons.getElementsByTagName('img')).walk(function(item, idx) {
		Event.addListener(item, 'click', function(event) {
			self.focus();
			self.insertHTML("<img id='"+PHP2Go.uid('image')+"' src='"+item.src+"' border='0' alt=''/>");
		});
	});
	// form submission
	if (this.fld.form) {
		Event.addListener(this.fld.form, 'submit', function(e) {
			self.fld.value = self.getValue();
		});
	}
	// resizing listener
	if (this.config.resizeMode != 'none') {
		Event.addListener($(this.name + '_resize'), 'mousedown', function(e) {
			self.setResizing($EV(e), true);
		});
	}
	// internal event listeners
	if (!PHP2Go.browser.ie) {
		this.addEventListener('afterpaste', function() {
			var self = this;
			setTimeout(function() {
				var v = self.getValue();
				self.execCommand('selectall');
				self.insertHTML(self.cleanHTML(v));
			}, 10);
		});
	}
};

/**
 * Return the contents of the editor as HTML
 * @type String
 */
EditorField.prototype.getValue = function() {
	return (this.textMode ? this.textarea.value : this.document.body.innerHTML);
};

/**
 * Return the contents of the editor as text
 * @type String
 */
EditorField.prototype.getValueAsText = function() {
	var t = this.textarea, b = this.document.body, text, ml;
	if (this.textMode) {
		ml = RegExp.multiline;
		text = t.value.stripTags().replace('&nbsp;', ' ');
		RegExp.multiline = ml;
		return text;
	} else {
		if (b.innerText) {
			return b.innerText;
		} else {
			ml = RegExp.multiline;
			text = b.innerHTML.stripTags().replace('&nbsp;', ' ');
			RegExp.multiline = ml;
			return text;
		}
	}
};

/**
 * Return an array of broken images
 * included in the editor's document
 * @type Array
 */
EditorField.prototype.getBrokenImages = function() {
	var imgs = this.document.body.getElementsByTagName('img');
	var bi = $C(imgs).valid(function(item, idx) {
		if (!Object.isUndef(item.fileSize))
			return (item.fileSize == -1);
		if (!Object.isUndef(item.naturalWidth))
			return (item.naturalWidth == 0);
		return (!item.complete);
	});
	return bi;
};

/**
 * Change the value of the component
 * @param {String} html New value
 * @type void
 */
EditorField.prototype.setValue = function(html) {
	(this.textMode ? this.textarea.value = html : this.document.body.innerHTML = html);
};

/**
 * Inserts HTML code in the current selection of the component
 * @param {String} html Code to be inserted
 * @type void
 */
EditorField.prototype.insertHTML = function(html) {
	var range, frag, last, sel = this.selection;
	this.focus();
	sel.clear();
	if (PHP2Go.browser.ie) {
		sel.getRange().pasteHTML(html);
	} else {
		range = sel.getRange();
		frag = range.createContextualFragment(html);
		last = frag.lastChild;
		range.insertNode(frag);
		sel.selectElement(last);
		sel.collapse(last && last.nodeName.equalsIgnoreCase('br'));
	}
};

/**
 * Clean up the component value
 * @type void
 */
EditorField.prototype.clear = function() {
	(this.textMode ? this.textarea.value = '' : this.document.body.innerHTML = '');
};

/**
 * Checks if the component value is empty
 * @type Boolean
 */
EditorField.prototype.isEmpty = function() {
	var re = /<(img|input|hr)/i;
	var html = this.getValue();
	var text = this.getValueAsText().trim();
	return (text == "" && !re.test(html));
};

/**
 * Disables/enables the component
 * @param {Boolean} b Flag value
 * @type void
 */
EditorField.prototype.setDisabled = function(b) {
	this.fld.disabled = b;
	this.textarea.disabled = b;
	this.switchMode.disabled = b;
	this.config.readOnly = b;
	$C($(this.name + '_toolbar').getElementsByTagName('select')).walk(function(item, idx) {
		item.disabled = b;
	});
	$C($(this.name + '_toolbar').getElementsByTagName('a')).walk(function(item, idx) {
		item.className = (b ? 'editorBtnDisabled' : 'editorBtn');
		item.firstChild.className = (b ? 'editorBtnDisabled' : 'editorBtn');
	});
};

/**
 * Move focus to the editor component
 * @type Boolean
 */
EditorField.prototype.focus = function() {
	if (this.beforeFocus() && !this.config.readOnly) {
		if (this.textMode) {
			this.textarea.focus();
		} else if (PHP2Go.browser.ie) {
			window.frames[this.name + '_iframe'].focus();
		} else {
			this.iframe.contentWindow.focus();
			this.document.designMode = 'on';
		}
		return true;
	}
	return false;
};

/**
 * Serialize the component so that the value
 * can be used to perform HTTP requests or
 * to build query param strings
 * @type String
 */
EditorField.prototype.serialize = function() {
	this.fld.value = (!this.isEmpty() ? this.getValue() : '');
	if (this.fld.value != '')
		return this.name + '=' + this.fld.value.urlEncode();
	return null;
};

/**
 * Switch between source and WYSIWYG modes
 * @param {Boolean} b Flag value
 * @type void
 */
EditorField.prototype.changeMode = function(b) {
	this.textMode = !!b;
	if (this.textMode) {
		this.textarea.value = this.document.body.innerHTML;
		this.iframe.hide();
		this.textarea.show();
	} else {
		this.document.body.innerHTML = this.textarea.value;
		this.textarea.hide();
		this.iframe.show();
	}
	this.focus.bind(this).delay(5);
};

/**
 * Execute a given command in the editor's document
 * @param {String} cmd Command identifier
 * @param {String} val Command parameter
 * @param {Object} ev Event
 * @type void
 */
EditorField.prototype.execCommand = function(cmd, val, ev) {
	if (this.textMode) {
		alert(Lang.editor.validateMode);
		this.textarea.focus();
		return false;
	} else if (this.config.readOnly) {
		return;
	}
	try {
		this.focus();
		switch (cmd.toLowerCase()) {
			case 'addemoticon' :
				this.showHideEmoticons(ev);
				break;
			case 'backcolor' :
				if (!PHP2Go.browser.ie)
					cmd = 'hilitecolor';
				this.document.execCommand(cmd, false, val);
				break;
			case 'createlink' :
				this.createLink();
				break;
			case 'fontname' :
			case 'fontsize' :
				if (val != '')
					this.document.execCommand(cmd, false, val);
				break;
			case 'formatblock' :
				if (val == 'removeformat') {
					var parent = this.selection.getParentByTagNames('h1,h2,h3,h4,h5,h6,pre,address');
					parent && this.removeNode(parent);
				} else {
					PHP2Go.browser.ie && (val = '<'+val+'>');
					this.document.execCommand(cmd, false, val);
				}
				break;
			case 'insertimage' :
				this.insertImage();
				break;
			case 'paste' :
				if (PHP2Go.browser.ie) {
					this.paste();
					ev.stop();
				} else {
					this.document.execCommand(cmd, false, val);
					this.raiseEvent('afterpaste');
				}
				break;
			default :
				this.document.execCommand(cmd, false, val);
				break;
		}
	} catch (e) { }
};

/**
 * Handles all keyboard events inside the edit area
 * @param {Object} e Event
 * @type Boolean
 */
EditorField.prototype.keyHandler = function(e) {
	var self = this, e = $EV(e), isKey = false, cmd = null;
	// force tab to blur on mozilla
	if (e.key() == 9 && !e.shiftKey && !PHP2Go.browser.ie) {
		this.switchMode.focus();
		return false;
	}
	// read only
	if (this.config.readOnly && !e.ctrlKey) {
		e.stop();
		return false;
	}
	// key handling
	isKey = (PHP2Go.browser.ie && e.type == 'keydown') || e.type == 'keypress';
	if (isKey && e.ctrlKey) {
		switch (e.char().toLowerCase()) {
			case 'a' :
				if (e.shiftKey) {
					this.createLink();
					return false;
				} else {
					cmd = 'selectall';
				}
				break;
			case 'b' : !e.shiftKey && (cmd = 'bold'); break;
			case 'c' : !e.shiftKey && PHP2Go.browser.ie && (cmd = 'copy'); break;
			case 'e' : !e.shiftKey && (cmd = 'justifycenter'); break;
			case 'f' :
				if (e.shiftKey) {
					$(this.name + '_fontname').focus();
					return false;
				}
				break;
			case 'i' :
				if (e.shiftKey) {
					this.insertImage();
					return false;
				} else {
					cmd = 'italic';
				}
				break;
			case 'j' : !e.shiftKey && (cmd = 'justifyfull'); break;
			case 'l' : !e.shiftKey && (cmd = 'justifyleft'); break;
			case 'r' : !e.shiftKey && (cmd = 'justifyright'); break;
			case 's' : !e.shiftKey && (cmd = 'strikethrough'); break;
			case 'u' : !e.shiftKey && (cmd = 'underline'); break;
			case 'v' :
				if (!e.shiftKey) {
					PHP2Go.browser.ie ? (cmd = 'paste') : (this.raiseEvent('afterpaste'));
				}
				break;
			case 'x' : !e.shiftKey && PHP2Go.browser.ie && (cmd = 'cut'); break;
			case 'z' : !e.shiftKey && (cmd = 'undo'); break;
		}
		if (cmd != null) {
			this.execCommand(cmd, null, e);
			return false;
		}
	}
	return true;
};

/**
 * This method is called when resizing of the editor are
 * starts (by clicking in the resize image) and ends (when
 * the mouseup event gets called)
 * @param {Event} e Event
 * @param {Boolean} b Start resizing or end resizing
 * @type void
 */
EditorField.prototype.setResizing = function(e, b) {
	b = !!b;
	var self = this, r = this.resizer;
	var cont = $(this.name + '_container');
	var box = $(this.name + '_resizeBox');
	var rh = PHP2Go.method(this, 'resizeHandler');
	if (b) {
		var dim = cont.getDimensions();
		box.resizeTo(dim.width, dim.height);
		cont.style.display = 'none';
		box.style.display = 'block';
		r.resizing = true;
		r.mode = this.config.resizeMode;
		r.point = {x:e.screenX,y:e.screenY};
		r.size = {w:parseInt(box.style.width, 10),h:parseInt(box.style.height, 10)};
		Event.addListener(document, 'mousemove', rh);
		Event.addListener(document, 'mouseup', rh);
	} else {
		box.style.display = 'none';
		cont.style.display = 'block';
		this.document.designMode = 'on';
		r.resizing = false;
		Event.removeListener(document, 'mousemove', rh);
		Event.removeListener(document, 'mouseup', rh);
	}
};

/**
 * Mouse move handler used by the resize routine
 * @param {Event} e Event
 * @type void
 */
EditorField.prototype.resizeHandler = function(e) {
	var r = this.resizer;
	if (!r.resizing)
		return;
	var dx = (e.screenX - r.point.x);
	var dy = (e.screenY - r.point.y);
	var box = $(this.name + '_resizeBox');
	if (e.type == 'mousemove') {
		if (/horizontal|both/i.test(r.mode))
			box.style.width = Math.max(r.size.w + dx, r.minWidth);
		if (/vertical|both/i.test(r.mode))
			box.style.height = Math.max(r.size.h + dy, r.minHeight);
	} else {
		this.setResizing(e, false);
		this.resizeTo(r.size.w+dx, r.size.h+dy);
		var lt = Cookie.buildLifeTime(30);
		Cookie.set('editor_'+this.name+'_width', (r.size.w+dx), lt, '/');
		Cookie.set('editor_'+this.name+'_height', (r.size.h+dy), lt, '/');
	}
};

/**
 * Resizes the editor area to a given width
 * and a given height. Is called when the resize
 * procedure ends and when a previous saved size
 * is loaded from the cookies
 * @param {Number} w Width
 * @param {Number} h Height
 * @type void
 */
EditorField.prototype.resizeTo = function(w, h) {
	var setw = (w && !isNaN(w) && /horizontal|both/i.test(this.config.resizeMode));
	var seth = (h && !isNaN(h) && /vertical|both/i.test(this.config.resizeMode));
	if (setw || seth) {
		var cont = $(this.name + '_container');
		var cd = cont.getDimensions();
		var ifr = this.iframe;
		var id = ifr.getDimensions();
		var dx = (w - cd.width), dy = (h - cd.height);
		if (PHP2Go.browser.gecko) {
			dx -= 4;
			dy -= 4;
		}
		if (setw) {
			cont.style.width = w + 'px';
			ifr.style.width = (id.width+dx) + 'px';
		}
		if (seth) {
			cont.style.height = h + 'px';
			ifr.style.height = (id.height+dy) + 'px';
		}
	}
};

/**
 * Toggle display status of the emoticons layer
 * @param {Object} ev Event
 * @type void
 */
EditorField.prototype.showHideEmoticons = function(ev) {
	var emo = this.divEmoticons;
	if (emo.getStyle('display') == 'none') {
		// set emoticons div position and display it
		var elm = ev.target;
		var pos = elm.getPosition();
		emo.setStyle('left', (pos.x-(emo.getDimensions().width-elm.parentNode.offsetWidth+3)) + 'px');
		emo.setStyle('top', (pos.y+elm.parentNode.offsetHeight-2) + 'px');
		emo.show();
	} else {
		emo.hide();
	}
};

/**
 * Create or edit a hyperlink in the component.
 * Uses a simple prompt to read the link address.
 * @type void
 */
EditorField.prototype.createLink = function() {
	var doc = this.document, sel = this.selection, a = null, link = null;
	if (a = sel.getParentByTagNames('a')) {
		sel.selectElement(a);
		link = prompt(Lang.editor.createLink, a.href.replace(/\/$/, ''));
		if (link != null && link != '' && link != 'http://') {
			a.href = link;
			a.target = '_blank';
		}
	} else {
		link = prompt(Lang.editor.createLink, "http:\/\/");
		if (link != null && link != '' && link != 'http://') {
			if (sel.getType() == 'None') {
				this.insertHTML("<a href='"+link+"' target='_blank'>"+link+"</a>");
			} else {
				doc.execCommand('unlink', false, null);
				doc.execCommand('createlink', false, link);
			}
		}
	}
};

/**
 * Create or edit an image in the component.
 * Uses a simple prompt to read the image address
 * @type void
 */
EditorField.prototype.insertImage = function() {
	var doc = this.document, sel = this.selection;
	var img = sel.getSelectedElement();
	if (img && img.tagName.equalsIgnoreCase('img')) {
		src = prompt(Lang.editor.insertImage, img.src.replace(/\/$/, ''));
		if (src != null && src != "") {
			img.src = src;
			img.style.border = 'none';
		}
	} else {
		src = prompt(Lang.editor.insertImage, "");
		if (src != null && src != "") {
			if (sel.getType() == 'None') {
				this.insertHTML("<img id='"+PHP2Go.uid('image')+"' src='"+src+"' alt='' style='border:none'>");
			} else {
				doc.execCommand('insertimage', false, src);
			}
		}
	}
};

/**
 * Handles the "paste" event in MSIE browsers
 * @type void
 */
EditorField.prototype.paste = function() {
	var div = $(this.name + '_pasteDiv');
	if (!div) {
		div = $N('div', document.body, {
			visibility : 'hidden',
			overflow : 'hidden',
			position : 'absolute',
			width : 1,
			height : 1
		});
		div.id = this.name + '_pasteDiv';
	}
	div.innerHTML = '';
	range = document.body.createTextRange();
	range.moveToElementText(div);
	range.execCommand('paste');
	this.insertHTML(this.cleanHTML(div.innerHTML));
};

/**
 * Remove a given element from the editor's document
 * @param {Object} elm Element to remove
 * @type void
 */
EditorField.prototype.removeNode = function(elm) {
	elm = elm || this.selection.getSelectedElement();
	if (PHP2Go.browser.ie) {
		elm.outerHTML = elm.innerHTML;
	} else {
		var range = elm.ownerDocument.createRange();
		range.setStartBefore(elm);
		range.setEndAfter(elm);
		range.deleteContents();
		range.insertNode(range.createContextualFragment(elm.innerHTML));
	}
};

/**
 * Remove unwanted properties and style definitions
 * from a given HTML code. This method is called from
 * inside {@link EditorField#paste} and is very useful
 * when using MS IE and pasting from MS Word
 * @param {String} html HTML code
 * @type String
 * @private
 */
EditorField.prototype.cleanHTML = function(html) {
	html = html.replace(/<o:p>\s*<\/o:p>/g, '');
	html = html.replace(/<o:p>.*<\/o:p>/g, "&nbsp;");
	if (!PHP2Go.browser.ie) {
		// word VML
		html = html.replace(new RegExp("<!--\[if gte vml 1\]>(<\/?\w+:[^>]*>\s*)+<!\[endif\]-->", 'gm'), '');
		// word comments and code
		html = html.replace(new RegExp("<!--\[[^\]]+\]>", 'gm'), '');
		html = html.replace(new RegExp("<!--\[[^\]]+\]-->", 'gm'), '');
	} else {
		// convert word images into normal images
		html = html.replace(/>\s*<\/v:imagedata>/, "\>");
		html = html.replace(/<v:shape\s*([^>]*)>\s*<v:imagedata.*src="([^"]+)".*\/?>.*<\/v:shape>/gi, "<img src=\"$2\"$1>");
	}
	// mso styles
	html = html.replace(/<(\w[^>]*)\s*class="?mso\w+"?\s*(\w*)/gi, '<$1$2');
	html = html.replace(/mso-.[^:]*:.[^;"]*/g, "");
	// word TOC anchors
	html = html.replace(/<a name="?_Toc[0-9]+"?>/gi, '');
	// margin styles
	html = html.replace(/\s*margin: 0cm 0cm 0pt\s*;/gi, '');
	html = html.replace(/\s*margin: 0cm 0cm 0pt\s*"/gi, "\"");
	// indentation styles
	html = html.replace(/\s*text-indent: 0cm\s*;/gi, '');
	html = html.replace(/\s*text-indent: 0cm\s*"/gi, "\"");
	// page break
	html = html.replace(/\s*page-break-before: [^\s;]+;?"/gi, "\"");
	// font variant
	html = html.replace(/\s*font-variant: [^\s;]+;?"/gi, "\"");
	// tab stops
	html = html.replace(/\s*tab-stops:[^;"]*;?/gi, '');
	html = html.replace(/\s*tab-stops:[^"]*/gi, '');
	// font face and font family
	html = html.replace(/\s*face="[^"]*"/gi, '');
	html = html.replace(/\s*face=[^ >]*/gi, '');
	html = html.replace(/\s*font-family:[^;"]*;?/gi, '');
	// empty class and style declarations
	html =  html.replace(/\s*style="\s*"/gi, '');
	html =  html.replace(/\s*class="\s*"/gi, '');
	// empty SPAN tags
	html = html.replace(/<span\s*[^>]*>\s*&nbsp;\s*<\/span>/gi, '&nbsp;');
	html = html.replace(/<span\s*[^>]*><\/span>/gi, '');
	// lang attributes
	html = html.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3");
	// XML elements and declarations
	html = html.replace(/<\\?\?xml[^>]*>/gi, '');
	// tags with XML namespaces
	html = html.replace(/<\/?\w+:[^>]*>/gi, '');
	return html;
};

PHP2Go.included[PHP2Go.baseUrl + 'form/editorfield.js'] = true;

}
