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
 * The SlideShow widget implements a control that display a
 * list of images sequentially, based on an interval.
 * @param {Object} attrs Widget's attributes
 * @param {Function} func Setup function
 * @constructor
 * @base Widget
 */
function SlideShow(attrs, func) {
	this.Widget(attrs, func);
	/**
	 * First image
	 * @type Object
	 */
	this.image1 = null;
	/**
	 * Second image
	 * @type Object
	 */
	this.image2 = null;
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
	 * Toggle button
	 * @type Object
	 */
	this.toggleBtn = null;
	/**
	 * Image count/total text
	 * @type Object
	 */
	this.cnt = null;
	/**
	 * Image description
	 * @type Object
	 */
	this.desc = null;
	/**
	 * Input used to change the delay amount
	 * @type Object
	 */
	this.delayInput = null;
	/**
	 * Current slide show index
	 * @type Number
	 */
	this.index = 0;
	/**
	 * Number of images in the slide show
	 * @type Number
	 */
	this.total = 0;
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
	this.stopped = (!this.attributes['play']);
}
SlideShow.extend(Widget, 'Widget');

/**
 * Initializes the widget
 */
SlideShow.prototype.setup = function() {
	this.image1 = $(this.attributes['id'] + '_foreground');
	this.image2 = $(this.attributes['id'] + '_background');
	this.toggleBtn = $(this.attributes['id'] + '_toggle');
	this.cnt = $(this.attributes['id'] + '_count');
	this.desc = $(this.attributes['id'] + '_description');
	this.delayInput = $(this.attributes['id'] + '_delay');
	this.total = this.attributes['images'].length;
	var first = this.attributes['images'][this.index];
	this.desc.update(first.description || first.url);
	this[this.foreground].setOpacity(0.9999);
	this[this.background].setOpacity(0.9999);
	if (this.total > 1) {
		Event.addListener(this[this.foreground], 'load', this.onImageLoaded.bind(this), true);
		Event.addListener(this[this.background], 'load', this.onImageLoaded.bind(this), true);
		Event.addListener(this.toggleBtn, 'click', this.onToggle.bind(this));
		Event.addListener(this.delayInput, 'blur', this.onSetDelay.bind(this));
		this[this.foreground].src = first.url;
		this.cnt.update((++this.index) + "/" + this.total);
		if (!this.stopped)
			this.loadNextImage();
	} else {
		this[this.foreground].src = img.url;
		this.toggleBtn.disabled = true;
	}
};

/**
 * Handles the click event on the toggle status button
 * @param {Event} evt Event
 */
SlideShow.prototype.onToggle = function(evt) {
	if (this.stopped) {
		this.stopped = false;
		this.loadNextImage();
		this.toggleBtn.update(this.attributes['pauseCaption']);
	} else {
		this.stopped = true;
		this.toggleBtn.update(this.attributes['playCaption']);
	}
};

/**
 * Handles the onblur event on the delay input
 * @param {Event} evt Event
 */
SlideShow.prototype.onSetDelay = function(evt) {
	evt = evt || window.event;
	var input = evt.target || evt.srcElement;
	if (/^[0-9]+$/.test(input.value)) {
		this.attributes['delay'] = input.value*1000;
	} else {
		alert(Lang.invalidValue);
		input.value = (this.attributes['delay']/1000);
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
	this[this.background].src = this.attributes['images'][this.index].url;
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
	// will enter here when current image index >= 1
	if (this.loading) {
		if (this.stopped)
			return;
		this.resizeImage(this[this.background]);
		this.loading = false;
		if (!this.waiting) {
			this.switchImage();
		}
	}
	// will enter here upon loading of first image
	else {
		this.resizeImage(this[this.foreground]);
	}
};

/**
 * Called when the slide show timer ends
 */
SlideShow.prototype.onTimerEnd = function() {
	if (this.stopped)
		return;
	if (this.waiting) {
		clearTimeout(this.timer);
		this.waiting = false;
		if (!this.loading) {
			this.switchImage();
		}
	}
};

/**
 * Adjusts the size of an image based
 * on the container dimensions
 * @param {Object} img Image
 */
SlideShow.prototype.resizeImage = function(img) {
	// resize using width as constraint
	var w = img.naturalWidth||img.clientWidth;
	var h = img.naturalHeight||img.clientHeight;
	if (w != this.attributes['width']) {
		h = Math.floor((h*this.attributes['width'])/w);
		w = this.attributes['width'];
		img.resizeTo(w, h);
	}
	img.style.visibility = 'visible';
};

/**
 * Switches the current displayed image
 */
SlideShow.prototype.switchImage = function() {
	// change z-indexes
	this[this.foreground].style.zIndex = parseInt(this[this.foreground].style.zIndex, 10) - 1;
	this[this.foreground].style.visibility = 'hidden';
	this[this.background].style.zIndex = parseInt(this[this.background].style.zIndex, 10) + 1;
	// show description
	var curIdx = (this.index == 0 ? this.total-1 : this.index-1);
	var curImg = this.attributes['images'][curIdx];
	this.cnt.update((curIdx+1) + "/" + this.total);
	this.desc.update(curImg.description || curImg.url);
	// switch images
	var tmp = this.foreground;
	this.foreground = this.background;
	this.background = tmp;
	// load next image
	this.loadNextImage();
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/slideshow.js'] = true;

}