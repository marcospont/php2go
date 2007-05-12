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

if (!PHP2Go.included[PHP2Go.baseUrl + 'widgets/collapsiblepanel.js']) {

/**
 * @fileoverview
 * Contains the CollapsiblePanel widget class
 */

/**
 * The CollapsiblePanel is a widget that represents a panel
 * whose content can be expanded and collapsed by clicking
 * on its header. This Javascript class contains the code
 * that implements this behaviour
 * @param {Object} attrs Widget's attributes
 * @param {Function} func Setup function
 * @constructor
 * @base Widget
 */
function CollapsiblePanel(attrs, func) {
	this.Widget(attrs, func);
	/**
	 * Reference to the panel's header
	 * @type Object
	 */
	this.header = null;
	/**
	 * Reference to the panel's tip
	 * @type Object
	 */
	this.tip = null;
	/**
	 * Reference to the panel's icon (expand/collapse)
	 * @type Object
	 */
	this.icon = null;
	/**
	 * Reference to the panel's content container
	 * @type Object
	 */
	this.content = null;
}
CollapsiblePanel.extend(Widget, 'Widget');

/**
 * Initializes the widget
 */
CollapsiblePanel.prototype.setup = function() {
	this.header = $(this.attributes['id'] + '_header');
	this.tip = $(this.attributes['id'] + '_tip');
	this.icon = $(this.attributes['id'] + '_icon');
	this.content = $(this.attributes['id'] + '_content');
	var self = this;
	var exp = new Image();
	exp.src = this.attributes['expandIcon'];
	var cps = new Image();
	cps.src = this.attributes['collapseIcon'];
	var toggle = function() {
		if (self.content.isVisible()) {
			self.content.hide();
			self.icon.src = exp.src;
			(self.tip) && (self.tip.update(self.attributes['collapsedTip']));
		} else {
			self.content.show();
			self.icon.src = cps.src;
			(self.tip) && (self.tip.update(self.attributes['expandedTip']));
		}
	};
	Event.addListener(this.header, 'click', toggle);
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/collapsiblepanel.js'] = true;

}