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

if (!PHP2Go.included[PHP2Go.baseUrl + 'widgets/googlemap.js']) {

/**
 * @fileoverview
 * Contains the SlideShow widget class
 */

/**
 * The GoogleMap widget renders a map based on the API provided
 * by Google Maps. This class receives a set of locations and
 * configurations and apply them in a google map instance.
 * @constructor
 * @base Widget 
 */
function GoogleMap(attrs) {
	this.Widget(attrs);
	/**
	 * Map container
	 * @type Object
	 */
	this.container = $(this.attributes['id']);
	/**
	 * Center point
	 * @type GLatLng
	 */
	this.center = new GLatLng(this.attributes['center'].lat, this.attributes['center'].lng);
	/**
	 * Bounds object composed by all locations
	 * @type GLatLngBounds
	 */
	this.bounds = new GLatLngBounds();
	/**
	 * Map locations
	 * @type Array
	 */
	this.locations = [];
	for (var i=0; i<this.attributes['locations'].length; i++) {
		var point = new GLatLng(this.attributes['locations'][i].lat, this.attributes['locations'][i].lng);
		this.bounds.extend(point);
		this.locations.push(point);
	}
	/**
	 * Google Map instance
	 * @type GMap2
	 */
	this.map = null;
	this.setup();
}
GoogleMap.extend(Widget, 'Widget');

/**
 * Initializes the widget
 */
GoogleMap.prototype.setup = function() {
	Event.addListener(window, 'unload', GUnload);
	var loc = this.locations;
	var info = $(this.attributes['id'] + '_locations').getElementsByTagName('div');
	if (GBrowserIsCompatible()) {
		var map = this.map = new GMap2(this.container);
		(!this.attributes['draggable']) && (map.disableDragging());
		map.addControl(new GMapTypeControl());
		map.addControl(new GLargeMapControl());
		map.setCenter(this.center);
		loc.walk(function(item, idx) {
			var marker = new GMarker(item);
			if (info[idx].innerHTML != '') {
				GEvent.addListener(marker, 'click', function() {
					marker.openInfoWindowHtml(info[idx].innerHTML);
				});
			}
			map.addOverlay(marker);
		});
		map.setZoom(this.attributes['zoom'] || map.getBoundsZoomLevel(this.bounds));
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/googlemap.js'] = true;

}