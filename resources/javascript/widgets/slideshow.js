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

if (!PHP2Go.included[PHP2Go.baseUrl + 'widgets/slideshow.js']) {

/**
 * @fileoverview
 * Contains the SlideShow widget class
 */

/**
 * @constructor
 */
function SlideShow(attrs) {
	this.Widget(attrs);
	/**
	 * First image
	 * @type Object
	 */
	this.image1 = $(this.attributes['id'] + '_foreground');
	/**
	 * Second image
	 * @type Object
	 */
	this.image2 = $(this.attributes['id'] + '_background');
	/**
	 * Current foreground image
	 * @type Number
	 */
	this.foreground = 'image1';
	/**
	 * Current background image
	 * @type String
	 */
	this.background = 'image2';
	/**
	 * Current slide show index
	 * @type Number
	 */
	this.index = 0;
	/**
	 * Load timer
	 * @type Object
	 */
	this.timer = null;
	/**
	 * Indicates if an image is being loaded
	 * @type Boolean
	 */
	this.loading = false;
	/**
	 * Indicates if we're waiting for the end of a timer delay
	 * @type Boolean
	 */
	this.waiting = false;
	/**
	 * Indicates if the slide show is stopped
	 * @type Boolean
	 */
	this.stopped = false;
	this.setup();
}
SlideShow.extend(Widget, 'Widget');

/**
 * Initializes the widget
 */
SlideShow.prototype.setup = function() {
	this[this.foreground].src = this.attributes['images'][this.index];
	this[this.foreground].setOpacity(0.9999);
	this[this.background].setOpacity(0.9999);
	if (this.attributes['images'].length > 1) {
		Event.addListener(this[this.foreground], 'load', this.onImageLoaded.bind(this), true);
		Event.addListener(this[this.background], 'load', this.onImageLoaded.bind(this), true);
		this.index++;
		this.loadNextImage();
	}
};

/**
 * Loads the next image in the chain
 */
SlideShow.prototype.loadNextImage = function() {
	this.loading = true;
	this.waiting = true;
	this[this.background].setOpacity(1);
	this.timer = setTimeout(this.onTimerEnd.bind(this), this.attributes['delay']);
	this[this.background].src = this.attributes['images'][this.index];
	if (this.index == (this.attributes['images'].length-1)) {
		this.index = 0;
	} else {
		this.index++;
	}
};

/**
 * Responds to the "onload" event of the current background image
 * @param {Event} evt Event
 */
SlideShow.prototype.onImageLoaded = function(evt) {
	if (this.loading) {
		this.loading = false;
		if (!this.waiting) {
			this.switchImage();
		}
	}
};

/**
 * Called when the slide show timer ends
 */
SlideShow.prototype.onTimerEnd = function() {
	if (this.waiting) {
		clearTimeout(this.timer);
		this.waiting = false;
		if (!this.loading) {
			this.switchImage();
		}
	}
};

/**
 * Switches the current displayed image
 */
SlideShow.prototype.switchImage = function() {
	// change z-indexes
	this[this.background].style.zIndex = parseInt(this[this.background].style.zIndex, 10) + 1;
	this[this.foreground].style.zIndex = parseInt(this[this.foreground].style.zIndex, 10) - 1;
	// switch images
	var tmp = this.foreground;
	this.foreground = this.background;
	this.background = tmp;
	// load next image
	this.loadNextImage();
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/slideshow.js'] = true;

}