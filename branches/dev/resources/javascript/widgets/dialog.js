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
 * Contains the classes of the PHP2Go dialogs API
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'widgets/dialog.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'ajax.js');

/**
 * Base class of the dialogs API. Builds simple dialogs
 * that can be placed absolutely (centered) or relative
 * to an existent element. These dialogs can be populated
 * with string contents, an existent element reference or
 * an AJAX call
 * @constructor
 * @param {Object} opts Configuration options
 */
Dialog = function(opts) {
	/**
	 * Dialog ID
	 * @type String
	 */
	this.id = opts.id || PHP2Go.uid('dialog');
	/**
	 * Dialog's root element
	 * @type Object
	 */
	this.el = null;
	/**
	 * Dialog's content container
	 * @type Object
	 */
	this.contentEl = null;
	/**
	 * Dialog's parent element
	 * @type Object
	 */
	this.parent = $(opts.parent) || document.body;
	/**
	 * Whether the dialog must use relative or absolute position
	 * @type Boolean
	 */
	this.relative = !!opts.relative;
	/**
	 * Left offset, when using relative positioning
	 * @type Number
	 */
	this.left = opts.left || null;
	/**
	 * Top offset, when using relative positioning
	 * @type Number
	 */
	this.top = opts.top || null;
	/**
	 * Dialog z-index
	 * @type Number
	 */
	this.zIndex = opts.zIndex || 9999;
	/**
	 * Whether the dialog must follow window scroll (absolute positioning only)
	 * @type Boolean
	 */
	this.followScroll = (!this.relative ? !!opts.followScroll : false);
	/**
	 * Dialog contents (string or element reference)
	 * @type Object
	 */
	this.contents = opts.contents;
	/**
	 * CSS class for the dialog's content
	 * @type String
	 */
	this.contentsClass = opts.contentsClass || null;
	/**
	 * URI to load dialog contents from, using AJAX
	 * @type String
	 */
	this.loadUri = opts.loadUri || null;
	/**
	 * Load method (get or post)
	 * @type String
	 */
	this.loadMethod = opts.loadMethod || 'get';
	/**
	 * Request parameters to be used while loading contents with AJAX
	 * @type Object
	 */
	this.loadParams = opts.loadParams || null;
	/**
	 * Element that must open the dialog
	 * @type Object
	 */
	this.trigger = $(opts.trigger) || null;
	if (this.trigger) {
		this.trigger.setStyle('cursor', 'pointer');
		Event.addListener(this.trigger, 'click', this.open.bind(this));
	}
	/**
	 * Element ID that must receive focus when dialog opens
	 * @type Object
	 */
	this.focusId = opts.focusId || null;
	/**
	 * Dialog buttons
	 * @type Array
	 */
	this.buttons = [];
	/**
	 * CSS class for the dialog's buttons
	 * @type String
	 */
	this.buttonsClass = opts.buttonsClass || '';
	/**
	 * Called before opening the dialog, can be used to cancel this event
	 * @type Function
	 */
	this.onBeforeOpen = opts.onBeforeOpen || null;
	/**
	 * Called after the dialog is opened
	 * @type Function
	 */
	this.onOpen = opts.onOpen || null;
	/**
	 * Called before closing the dialog, can be used to cancel this event
	 * @type Function
	 */
	this.onBeforeClose = opts.onBeforeClose || null;
	/**
	 * Called after the dialog is closed
	 * @type Function
	 */
	this.onClose = opts.onClose || null;
	/**
	 * @ignore
	 */
	this.defaultButton = null;
	/**
	 * @ignore
	 */
	this.tabDelim = {};
	/**
	 * @ignore
	 */
	this.tabForward = false;
	if (opts.buttons && opts.buttons.length) {
		for (var i=0; i<opts.buttons.length; i++)
			this.addButton(opts.buttons[i][0], opts.buttons[i][1] || null, !!opts.buttons[i][2]);
	}
};

/**
 * Performs initialization routines on the dialog
 * @type void
 */
Dialog.prototype.setup = function() {
	if (!this.el) {
		Event.addListener(window, 'unload', this.close.bind(this));
		this.el = $N('div', this.parent, {
			position: 'absolute',
			display: 'none',
			zIndex: this.zIndex
		}, '', { id: this.id });
		this.tabDelim.start = $N('span', this.el, null, '', { tabIndex: 0 });
		var contentRoot = $N('div', this.el, (this.contentsClass ? {} : {
			backgroundColor: '#fff',
			color: '#000',
			border: '1px solid #000',
			padding: '5px'
		}), '', {
			id: this.id + '_content',
			className: this.contentsClass
		});
		this.contentEl = $N('div', contentRoot);
		this.setupContents();
		this.tabDelim.end = $N('span', this.el, null, '', { tabIndex: 0 });
	}
};

/**
 * Initializes the contents of the dialog
 * @type void
 */
Dialog.prototype.setupContents = function() {
	if (!this.loadUri) {
		this.setContents();
	} else {
		// AJAX content
		var req = new AjaxUpdater(this.loadUri, {
			method: this.loadMethod,
			params: this.loadParams,
			async: false,
			container: this.contentEl
		});
		req.send();
	}
	// buttons
	if (this.buttons.length > 0) {
		var self = this, parent = $N('div', this.contentEl.parentNode, {
			textAlign: 'center',
			marginTop: '4px',
			marginBottom: '2px'
		});
		for (var i=0; i<this.buttons.length; i++) {
			var btn = this.buttons[i].el = $N('button', parent, {marginLeft: '5px', marginRight: '5px'}, this.buttons[i].text, {
				id: this.id + '_btn' + i,
				className: this.buttonsClass				
			});
			btn.setAttribute('type', 'button');
			btn.index = i;
			Event.addListener(btn, 'click', function(e) {
				var idx = $EV(e).target.index;
				if (Object.isFunc(self.buttons[idx].fn))
					self.buttons[idx].fn.apply(self);
			});
			if (this.buttons[i].def)
				this.defaultButton = btn;
		}
	} else {
		Event.addListener(this.el, 'click', this.close.bind(this));
	}
};

/**
 * Adds a button on the dialog
 * @param {String} Caption
 * @param {Function} Handler function
 * @param {Boolean} Is this the default button?
 * @type void
 */
Dialog.prototype.addButton = function(text, fn, def) {
	this.buttons.push({
		text: text,
		fn: fn || null,
		def: !!def
	});
};

/**
 * Changes the handler function of a dialog's button
 * @param {Number} idx Button index (zero based)
 * @param {Function} fn Handler function
 * @type void
 */
Dialog.prototype.setButtonAction = function(idx, fn) {
	if (this.buttons[idx])
		this.buttons[idx].fn = fn;
};

/**
 * Set dialog's contents
 * @param {Object} contents String or element reference
 * @type void
 */
Dialog.prototype.setContents = function(contents) {
	this.contents = contents || this.contents;
	if (this.contents) {
		if (Object.isString(this.contents))
			this.contentEl.update(this.contents);
		else if (this.contents.tagName)
			this.contents.setParentNode(this.contentEl);
		else if (this.contents.toString)
			this.contentEl.update(this.contents.toString());
	}
};

/**
 * Opens the dialog
 * @type void
 */
Dialog.prototype.open = function() {
	if (this.onBeforeOpen && this.onBeforeOpen.apply(this) == false)
		return;
	this.setup();
	this.show();
	(this.onOpen) && (this.onOpen.apply(this));
};

/**
 * Closes the dialog
 * @type void
 */
Dialog.prototype.close = function() {
	if (this.onBeforeClose && this.onBeforeClose.apply(this) == false)
		return;
	this.hide();
	if (this.onClose)
		this.onClose.apply(this);
};

/**
 * Shows the dialog
 * @type void
 */
Dialog.prototype.show = function() {
	// add events
	if (this.followScroll && !this.relative)
		Event.addListener(window, 'scroll', PHP2Go.method(this, 'scrollHandler'), true);
	Event.addListener(window, 'resize', PHP2Go.method(this, 'resizeHandler'), true);
	// show dialog
	this.el.show();
	// place dialog
	this.place();
	// focus element
	var focusEl = (this.focusId ? $(this.focusId) : null);
	if (Element.isChildOf(focusEl, this.contentEl))
		focusEl.focus();
	else if (this.defaultButton)
		this.defaultButton.focus();
	else if (this.buttons.length > 0)
		this.buttons[0].el.focus();
	else
		this.tabDelim.start.focus();
};

/**
 * Places the dialog according to the provided settings
 * @type void
 */
Dialog.prototype.place = function() {
	var elDim, parDim, offset;
	if (this.relative) {
		if (this.left !== null || this.top !== null) {
			offset = (this.parent == document.body ? {x: 0, y: 0} : this.parent.getPosition());
			this.el.moveTo(offset.x + (this.left||0), offset.y + (this.top||0));
		} else {
			elDim = this.el.getDimensions();
			parDim = this.parent.getDimensions();
			offset = this.parent.getPosition();
			this.el.moveTo((((parDim.width-elDim.width)/2)+offset.x), (((parDim.height-elDim.height)/2)+offset.y));
		}
	} else {
		elDim = this.el.getDimensions();
		parDim = Window.size();
		offset = Window.scroll();
		this.el.moveTo((((parDim.width-elDim.width)/2)+offset.x), (((parDim.height-elDim.height)/2)+offset.y));
	}
};

/**
 * Hides the dialog
 * @type void
 */
Dialog.prototype.hide = function() {
	// remove events
	if (this.followScroll && !this.relative)
		Event.removeListener(window, 'scroll', PHP2Go.method(this, 'scrollHandler'), true);
	Event.removeListener(window, 'resize', PHP2Go.method(this, 'resizeHandler'), true);
	// hide dialog
	this.el.hide();
};

/**
 * Called upon window.onresize
 * @type void
 */
Dialog.prototype.resizeHandler = function(e) {
	this.place();
};

/**
 * Called upon window.onscroll
 * @type void
 */
Dialog.prototype.scrollHandler = function(e) {
	this.place();
};

/**
 * Extends base dialog by adding modal behavior:
 * blocks the user from accessing areas outside
 * the dialog and grays out the dialog background
 * @constructor
 * @base Dialog
 * @param {Object} opts Configuration options
 */
ModalDialog = function(opts) {
	this.Dialog(Object.extend(opts, {
		followScroll: true
	}));
	/**
	 * Overlay element used as dialog background
	 * @type Object
	 */
	this.overlay = null;
	/**
	 * Overlay color
	 * @type String
	 */
	this.overlayColor = opts.overlayColor || '#ccc';
	/**
	 * CSS class for the overlay element
	 * @type String
	 */
	this.overlayClass = opts.overlayClass || '';
	/**
	 * Background opacity
	 * @type Number
	 */
	this.opacity = opts.opacity || 0.4;
};
ModalDialog.extend(Dialog, 'Dialog');

/**
 * Performs initialization routines on the dialog
 * @type void
 */
ModalDialog.prototype.setup = function() {
	ModalDialog.superclass.setup.apply(this);
	if (!this.overlay) {
		var styles = Object.extend({
			position: 'absolute',
			left: '0px',
			top: '0px',
			zIndex: this.zIndex - 2,
			backgroundColor: this.overlayColor,
			opacity: this.opacity,
			display: 'none'
		}, (PHP2Go.browser.ie ? {} : {
			width: '100%',
			height: '100%'
		}));
		this.overlay = $N('div', document.body, styles, '', {
			id: this.id + '_overlay',
			className: this.overlayClass
		});
	}
};

/**
 * Shows the modal dialog
 * @type void
 */
ModalDialog.prototype.show = function() {
	ModalDialog.superclass.show.apply(this);
	// add events
	var tf = PHP2Go.method(this, 'tabDelimFocusHandler');
	var tb = PHP2Go.method(this, 'tabDelimBlurHandler');
	var k = PHP2Go.method(this, 'keyHandler');
	Event.addListener(this.tabDelim.start, 'focus', tf);
	Event.addListener(this.tabDelim.start, 'blur', tb);
	Event.addListener(this.tabDelim.end, 'focus', tf);
	Event.addListener(this.tabDelim.end, 'blur', tb);
	Event.addListener(document, 'keypress', k);
	// show overlay
	this.overlay.style.display = '';
};

/**
 * Places the modal dialog according to the provided settings
 * @type void
 */
ModalDialog.prototype.place = function() {
	ModalDialog.superclass.place.apply(this);
	var sc = Window.scroll();
	this.overlay.style.left = sc.x + 'px';
	this.overlay.style.top = sc.y + 'px';
	if (PHP2Go.browser.ie) {
		var size = Window.size();
		this.overlay.style.width = size.width + 'px';
		this.overlay.style.height = size.height + 'px';
	}
};

/**
 * Hides the modal dialog
 * @type void
 */
ModalDialog.prototype.hide = function() {
	ModalDialog.superclass.hide.apply(this);
	// remove events
	var tf = PHP2Go.method(this, 'tabDelimFocusHandler');
	var tb = PHP2Go.method(this, 'tabDelimBlurHandler');
	var k = PHP2Go.method(this, 'keyHandler');
	Event.removeListener(this.tabDelim.start, 'focus', tf);
	Event.removeListener(this.tabDelim.start, 'blur', tb);
	Event.removeListener(this.tabDelim.end, 'focus', tf);
	Event.removeListener(this.tabDelim.end, 'blur', tb);
	Event.removeListener(document, 'keypress', k);
	// hide overlay
	this.overlay.style.display = 'none';
};

/**
 * Event listener that helps to prevent user from
 * tabbing outside the dialog area
 * @type void
 */
ModalDialog.prototype.tabDelimFocusHandler = function(e) {
	e = e || window.event;
	var trg = e.target || e.srcElement;
	if (trg == this.tabDelim.start) {
		if (this.tabForward) {
			this.tabForward = false;
		} else {
			this.tabForward = true;
			this.tabDelim.end.focus();
		}
	} else if (trg == this.tabDelim.end) {
		if (this.tabForward) {
			this.tabForward = false;
		} else {
			this.tabForward = true;
			this.tabDelim.start.focus();
		}
	}
};

/**
 * Event listener that helps to prevent user from
 * tabbing outside the dialog area
 * @type void
 */
ModalDialog.prototype.tabDelimBlurHandler = function() {
	var self = this;
	setTimeout(function() {
		self.tabForward = false;
	}, 100);
};

/**
 * Event listener that helps to prevent user from
 * tabbing outside the dialog area
 * @type void
 */
ModalDialog.prototype.keyHandler = function(e) {
	var e = $EV(e);
	if (e.target.isChildOf(this.el))
		return;
	if (e.key() != 9) {
		e.stop();
	} else {
		try {
			this.tabDelim.start.focus();
		} catch (e) { }
	}
};

/**
 * Specialized version of a modal dialog used to display images
 * @constructor
 * @base ModalDialog
 * @param {Object} opts Configuration options
 */
ImageDialog = function(opts) {
	this.ModalDialog(Object.extend(opts, {
		contents: null,
		loadUri: null,
		relative: false
	}));
	/**
	 * Used to display the image inside the dialog box
	 * @type Object
	 */
	this.img = null;
	/**
	 * Image URI
	 * @type String
	 */
	this.imgUri = (opts.img || (this.trigger || {}).src || null);
};
ImageDialog.extend(ModalDialog, 'ModalDialog');

/**
 * Finds all images whose class attribute matches the given
 * class name and configures them to show in a centered modal
 * dialog box
 * @param {String} cls Class name
 * @type void
 */
ImageDialog.setup = function(cls) {
	var imgs = document.getElementsByClassName(cls, 'img');
	for (var i=0; i<imgs.length; i++) {
		new ImageDialog({
			trigger: imgs[i]
		});
	}
};

/**
 * Performs initialization routines on the dialog
 * @type void
 */
ImageDialog.prototype.setup = function() {
	ImageDialog.superclass.setup.apply(this);
	if (!this.img) {
		this.img = $N('img', this.contentEl, {
			cursor: 'pointer',
			display: 'none'
		});
		this.img.onload = function() {
			this.img.style.display = '';
			this.place();
		}.bind(this);
		this.img.src = this.imgUri;
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/dialog.js'] = true;

}