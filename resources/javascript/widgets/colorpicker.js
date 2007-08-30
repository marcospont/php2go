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
 * This file contains the ColorPicker widget class
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'widgets/colorpicker.js']) {

/**
 * The ColorPicker class builds a color picker widget. It can work
 * in 2 different modes: "flat", displayed directly in the page document,
 * using a div as its container; and "popup", using a trigger element
 * to toggle its visibility state
 * @constructor
 * @param {Object} opts Set of configuration options
 */
ColorPicker = function(opts) {
	/**
	 * Component mode - flat or popup
	 * @type String
	 */
	this.mode = opts.mode || 'flat';
	/**
	 * Container element, used by flat mode
	 * @type Object
	 */
	this.container = $(opts.container) || null;
	/**
	 * Trigger element, used by popup mode
	 * @type Object
	 */
	this.trigger = $(opts.trigger) || null;
	/**
	 * Holds the text input used to display the RGB value
	 * @type Object
	 */
	this.text = null;
	/**
	 * Component id
	 * @type String
	 */
	this.id = (this.mode == 'flat' ? (opts.id || PHP2Go.uid('colorpicker')) : 'colorpicker');
	/**
	 * Color to be used as initial/reset value
	 * for highlight area and text input
	 * @type String
	 */
	this.nullColor = (opts.nullColor != null ? opts.nullColor : '#ffffff');
	/**
	 * Current selected color
	 * @type String
	 */
	this.curColor = opts.color || this.nullColor;
	/**
	 * Default color
	 * @type String
	 */
	this.defaultColor = this.curColor;
	/**
	 * Enable state
	 * @type Boolean
	 */
	this.disabled = false;
	/**
	 * If set, will be called when a popup color picker opens
	 * @type Function
	 */
	this.onOpen = opts.onOpen || null;
	/**
	 * If set, will be called when a popup color picker is closed
	 * @type Function
	 */
	this.onClose = opts.onClose || null;
	/**
	 * Called when a color is selected. This function will
	 * receive the selected color as an RGB string
	 * @type Function
	 */
	this.onSelect = opts.onSelect || null;
	/**
	 * @ignore
	 */
	this.colors = new Array(
		'#000000','#000000','#003300','#006600','#009900','#00cc00','#00ff00','#330000','#333300','#336600','#339900','#33cc00',
		'#33ff00','#660000','#663300','#666600','#669900','#66cc00','#66ff00','#333333','#000033','#003333','#006633','#009933',
		'#00cc33','#00ff33','#330033','#333333','#336633','#339933','#33cc33','#33ff33','#660033','#663333','#666633','#669933',
		'#66cc33','#66ff33','#666666','#000066','#003366','#006666','#009966','#00cc66','#00ff66','#330066','#333366','#336666',
		'#339966','#33cc66','#33ff66','#660066','#663366','#666666','#669966','#66cc66','#66ff66','#999999','#000099','#003399',
		'#006699','#009999','#00cc99','#00ff99','#330099','#333399','#336699','#339999','#33cc99','#33ff99','#660099','#663399',
		'#666699','#669999','#66cc99','#66ff99','#cccccc','#0000cc','#0033cc','#0066cc','#0099cc','#00cccc','#00ffcc','#3300cc',
		'#3333cc','#3366cc','#3399cc','#33cccc','#33ffcc','#6600cc','#6633cc','#6666cc','#6699cc','#66cccc','#66ffcc','#ffffff',
		'#0000ff','#0033ff','#0066ff','#0099ff','#00ccff','#00ffff','#3300ff','#3333ff','#3366ff','#3399ff','#33ccff','#33ffff',
		'#6600ff','#6633ff','#6666ff','#6699ff','#66ccff','#66ffff','#ff0000','#990000','#993300','#996600','#999900','#99cc00',
		'#99ff00','#cc0000','#cc3300','#cc6600','#cc9900','#cccc00','#ccff00','#ff0000','#ff3300','#ff6600','#ff9900','#ffcc00',
		'#ffff00','#00ff00','#990033','#993333','#996633','#999933','#99cc33','#99ff33','#cc0033','#cc3333','#cc6633','#cc9933',
		'#cccc33','#CCFF33','#ff0033','#ff3333','#ff6633','#ff9933','#ffcc33','#ffff33','#0000ff','#990066','#993366','#996666',
		'#999966','#99cc66','#99ff66','#cc0066','#cc3366','#cc6666','#cc9966','#cccc66','#ccff66','#ff0066','#ff3366','#ff6666',
		'#ff9966','#ffcc66','#ffff66','#ffff00','#990099','#993399','#996699','#999999','#99cc99','#99ff99','#cc0099','#cc3399',
		'#cc6699','#cc9999','#cccc99','#ccff99','#ff0099','#ff3399','#ff6699','#ff9999','#ffcc99','#ffff99','#00ffff','#9900cc',
		'#9933cc','#9966cc','#9999cc','#99cccc','#99ffcc','#cc00cc','#cc33cc','#cc66cc','#cc99cc','#cccccc','#ccffcc','#ff00cc',
		'#ff33cc','#ff66cc','#ff99cc','#ffcccc','#ffffcc','#ff00ff','#9900ff','#9933ff','#9966ff','#9999ff','#99ccff','#99ffff',
		'#cc00ff','#cc33ff','#cc66ff','#cc99ff','#ccccff','#ccffff','#ff00ff','#ff33ff','#ff66ff','#ff99ff','#ffccff','#ffffff'
	);
	this.setup();
};

/**
 * @ignore
 */
ColorPicker.loaded = false;
/**
 * @ignore
 */
ColorPicker.popup = null;

/**
 * Initialize the component, settin up the necessary
 * events and element attributes. Under flat mode,
 * this method will build the color picker element
 * and attach it to the container. Under popup mode,
 * the widget will be built only in the first instance
 * and will be shared among all popup instances
 * @type void
 */
ColorPicker.prototype.setup = function() {
	var self = this;
	if (this.mode == 'popup') {
		if (!ColorPicker.loaded) {
			ColorPicker.loaded = true;
			Event.addLoadListener(function(e) {
				ColorPicker.popup = self.build();
			});
		}
		var trg = this.trigger;
		Event.addListener(trg, 'click', function(e) {
			if (!self.disabled) {
				var p = ColorPicker.popup;
				if (p.trigger == trg) {
					self.hidePopup();
				} else {
					self.showPopupAt(trg);
				}
			}
		});
	} else {
		this.build();
		var rgb = this.text;
		var sel = $(this.id + '_sel');
		Event.addListener(rgb, 'focus', function(e) {
			rgb.select();
		});
		Event.addListener(rgb, 'blur', function(e) {
			var c = rgb.value;
			if (/\#[a-fA-F0-9]{6}/.test(c)) {
				self.curColor = sel.style.backgroundColor = c;
				if (self.onSelect)
					self.onSelect(c);
			}
		});
	}
};

/**
 * This method is called from inside {@link ColorPicker#setup} and
 * builds the HTML structure of the color picker. It's also responsible
 * for setting up the event handlers in all the color cells
 * @type void
 */
ColorPicker.prototype.build = function() {
	var picker, html, self = this, total = this.colors.length;
	// create base div
	if (this.mode == 'flat')
		picker = $N('div', this.container);
	else
		picker = $N('div', document.body, {display:'none',position:'absolute'});
	// build HTML contents
	html = "<table id=\""+this.id+"_table\" cellpadding=\"0\" cellspacing=\"1\" style=\"background-color:black\">";
	if (this.mode == 'popup')
		html += "<tr><td colspan=\"19\" align=\"center\" class=\"pickerTitle\">"+Lang.colorPicker.popupTitle+"</td></tr>";
	this.colors.walk(function(item, idx) {
		((idx%19) == 0) && (html += "<tr>");
		html += "<td class=\"pickerColor\" title=\""+Lang.colorPicker.colorTitle+"\" style=\"background-color:"+item+"\"></td>";
		((idx+1)>total || ((idx+1)%19) == 0) && (html += "</tr>");
	});
	html += "<tr><td class=\"pickerBottomCell\" colspan=\"19\">";
	if (this.mode == 'flat') {
		html += "<div id=\""+this.id+"_sel\" class=\"pickerSelected\" style=\"background-color:"+this.curColor+"\">&nbsp;</div>";
		html += "<div id=\""+this.id+"_hl\" class=\"pickerHighlight\" style=\"float:right;width:30px\">&nbsp;</div>";
	} else {
		html += "<div id=\""+this.id+"_hl\" class=\"pickerHighlight\" style=\"float:left;width:100px\">&nbsp;</div>";
	}
	html += "<input id=\""+this.id+"_rgb\" class=\"pickerRGB\" type=\"text\""+(this.mode=='popup'?" readonly=\"readonly\"":'')+" value=\""+this.curColor+"\" maxlength=\"7\">";
	html += "</td></tr>";
	html += "</table>";
	picker.id = this.id + '_div';
	picker.innerHTML = html;
	// setup color events
	$C(picker.getElementsByTagName('td')).walk(function(item, idx) {
		if (item.className == 'pickerColor') {
			Event.addListener(item, 'click', function(evt) {
				self.colorClick(item);
			});
			Event.addListener(item, 'mouseover', function(evt) {
				self.colorOver(item);
			});
			Event.addListener(item, 'mouseout', function(evt) {
				self.colorOut();
			});
		}
	});
	// pick up a reference to the text input
	this.text = $(this.id+"_rgb");
	return picker;
};

/**
 * Enable/disable the component
 * @param {Boolean} b Flag value
 * @type void
 */
ColorPicker.prototype.setDisabled = function(b) {
	var tbl = $(this.id + '_table');
	this.disabled = b;
	if (this.mode == 'flat') {
		this.text.disabled = b;
		tbl.style.backgroundColor = (b ? '#aaa' : 'black');
		$C(tbl.getElementsByTagName('td')).walk(function(item, idx) {
			item.style.cursor = (b ? 'default' : 'pointer');
			if (item.className == 'pickerColor')
				Element.setOpacity(item, (b ? 0.6 : 1));
		});
		Element.setOpacity(this.id + '_hl', (b ? 0.6 : 1));
		Element.setOpacity(this.id + '_sel', (b ? 0.6 : 1));
	}
};

/**
 * Change current selected color of the widget
 * @param {String} color Color, in RGB format #[0-9]{3-6}
 * @type void
 */
ColorPicker.prototype.setColor = function(color) {
	color = (color || this.nullColor);
	if (this.mode == 'flat') {
		var hl = $(this.id + '_hl');
		var sel = $(this.id + '_sel');
		hl.style.backgroundColor = this.nullColor;
		sel.style.backgroundColor = this.curColor = color;
		if (this.mode == 'flat')
			this.text.value = color;
		if (this.onSelect)
			this.onSelect(this.curColor);
	} else {
		var popup = ColorPicker.popup;
		if (popup.onClose)
			popup.onClose();
		popup.trigger = null;
		popup.hide();
		if (popup.onSelect)
			popup.onSelect(color);
	}
};

/**
 * Handles the onMouseOver event on a color cell
 * @param Object item Color cell
 * @type void
 */
ColorPicker.prototype.colorOver = function(item) {
	if (!this.disabled) {
		var hl = $(this.id + '_hl'), val = this.text;
		hl.style.backgroundColor = item.style.backgroundColor;
		val.value = this.rgbToHex(item.style.backgroundColor);
	}
};

/**
 * Handles the onMouseOut event on a color cell
 * @param Object item Color cell
 * @type void
 */
ColorPicker.prototype.colorOut = function() {
	if (!this.disabled) {
		var hl = $(this.id + '_hl'), val = this.text;
		hl.style.backgroundColor = this.nullColor;
		val.value = (this.mode == 'flat' ? this.curColor : this.nullColor);
	}
};

/**
 * Handles the onClick event of a color cell. Changes
 * the current color in the instance and calls the
 * onSelect callback
 * @param Object item Color cell
 * @type void
 */
ColorPicker.prototype.colorClick = function(item) {
	if (!this.disabled) {
		if (this.mode == 'flat') {
			var sel = $(this.id + '_sel');
			this.curColor = sel.style.backgroundColor = this.rgbToHex(item.style.backgroundColor);
			if (this.onSelect)
				this.onSelect(this.curColor);
		} else {
			var popup = ColorPicker.popup;
			if (popup.onClose)
				popup.onClose();
			popup.trigger = null;
			popup.hide();
			if (popup.onSelect)
				popup.onSelect(this.rgbToHex(item.style.backgroundColor));
		}
	}
};

/**
 * Utility method to convert a color from
 * rgb(N, N, N) format to #XXXXXX format
 * @param String s Color string to be converted
 * @type String
 */
ColorPicker.prototype.rgbToHex = function(s) {
	if (s.toLowerCase().indexOf('rgb') != -1) {
		var re = new RegExp("rgb\\s*?\\(\\s*?([0-9]+).*?,\\s*?([0-9]+).*?,\\s*?([0-9]+).*?\\)", "gi");
		var rgb = s.replace(re, "$1,$2,$3").split(',');
		if (rgb.length == 3) {
			r = parseInt(rgb[0]).toString(16);
			g = parseInt(rgb[1]).toString(16);
			b = parseInt(rgb[2]).toString(16);
			r = r.length == 1 ? '0' + r : r;
			g = g.length == 1 ? '0' + g : g;
			b = b.length == 1 ? '0' + b : b;
			s = "#" + r + g + b;
		}
	}
	return s;
};

/**
 * Listen to mousedown events in the HTML document. If
 * the clicked element is outside the color picker area
 * and is not its trigger element, the widget is closed
 * @param {Event} e Event
 * @type void
 */
ColorPicker.prototype.mouseDownHandler = function(e) {
	var pop = ColorPicker.popup;
	var t = $EV(e).target;
	if (!t.isChildOf(pop.trigger) && !t.isChildOf(pop))
		this.hidePopup();
};

/**
 * Shows the color picker popup in the right side of a given trigger
 * @param {Object} trg Trigger element
 * @type void
 */
ColorPicker.prototype.showPopupAt = function(trg) {
	var pop = ColorPicker.popup;
	var pos = trg.getPosition();
	var dim = trg.getDimensions();
	if (this.onOpen)
		this.onOpen();
	pop.trigger = trg;
	pop.moveTo(pos.x, pos.y+dim.height);
	pop.onClose = this.onClose;
	pop.onSelect = this.onSelect;
	if (pop.style.display == 'none')
		pop.show();
	Event.addListener(document, 'mousedown', PHP2Go.method(this, 'mouseDownHandler'));
};

/**
 * Hides the color picker popup
 * @type void
 */
ColorPicker.prototype.hidePopup = function() {
	var pop = ColorPicker.popup;
	if (pop.onClose)
		pop.onClose();
	pop.trigger = null;
	pop.hide();
	Event.removeListener(document, 'mousedown', PHP2Go.method(this, 'mouseDownHandler'));
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/colorpicker.js'] = true;

}
