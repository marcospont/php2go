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
 * This file contains the Throbber class, which is
 * used internally by the Ajax libraries
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'util/throbber.js']) {

/**
 * A throbber is a graphic element used to indicate that an action
 * is being performed. This class exposes a simple API to show/hide
 * these graphic elements
 * @constructor
 * @param {Object} opts Options
 */
Throbber = function(opts) {
	/**
	 * The throbber
	 * @type Object
	 */
	this.element = ($(opts.element) || null);
	/**
	 * Parent element (by position)
	 * @type Object
	 */
	this.parent = ($(opts.parent) || document.body);
	/**
	 * Whether to centralize the throbber in its parent
	 * @type Boolean
	 */
	this.centralize = PHP2Go.ifUndef(opts.centralize, true);
	/**
	 * onShow handler function
	 * @type Function
	 */
	this.onShow = opts.onShow || null;
	/**
	 * onHide handler function
	 * @type Function
	 */
	this.onHide = opts.onHide || null;
	this.setup();
};

/**
 * Initializes throbber element and handler functions
 * @type void
 */
Throbber.prototype.setup = function() {
	if (this.element)
		this.element.hide();
	this.onShow = (typeof(this.onShow) == 'function' ? this.onShow.bind(this) : null);
	this.onHide = (typeof(this.onHide) == 'function' ? this.onHide.bind(this) : null);
	var self = this;
	Event.addListener(window, 'unload', self.hide);
};

/**
 * Turn on throbber's visibility
 * @type void
 */
Throbber.prototype.show = function() {
	var elm, elmDim, parDim, offset;
	var body = (this.parent == document.body);
	if (elm = this.element) {
		if (this.onShow)
			this.onShow();
		if (this.centralize && elm.style.position != 'absolute')
			elm.style.position = 'absolute';
		elm.show();
		if (this.centralize) {
			elmDim = elm.getDimensions();
			if (body) {
				parDim = Window.size();
				offset = Window.scroll();
			} else {
				parDim = this.parent.getDimensions();
				offset = this.parent.getPosition();
			}
			elm.moveTo(((parDim.width-elmDim.width)/2)+offset.x, ((parDim.height-elmDim.height)/2)+offset.y);
		}
	}
};

/**
 * Hide the trobber
 * @type void
 */
Throbber.prototype.hide = function() {
	if (elm = this.element) {
		if (this.onHide)
			this.onHide();
		elm.hide();
	}
};

/**
 * Verify if throbber is currently active (visible)
 * @type Boolean
 */
Throbber.prototype.isActive = function() {
	return (this.element.isVisible());
};

PHP2Go.included[PHP2Go.baseUrl + 'util/throbber.js'] = true;

}
