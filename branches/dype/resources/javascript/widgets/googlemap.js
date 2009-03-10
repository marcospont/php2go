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
 * @param {Object} attrs Widget's attributes
 * @param {Function} func Setup function
 * @constructor
 * @base Widget
 */
function GoogleMap(attrs, func) {
	this.Widget(attrs, func);
	var apiOk;
	try { 
		apiOk = GBrowserIsCompatible(); 
	} catch (e) { 
		apiOk = false; 
	}
	/**
	 * Map container
	 * @type Object
	 */
	this.container = $(this.attributes.id);
	/**
	 * Google Map instance
	 * @type GMap2
	 */
	this.map = (apiOk ? new GMap2(this.container) : null);
}
GoogleMap.extend(Widget, 'Widget');

/**
 * Holds existent TabView instances,
 * indexed by widget ID.
 * @type Object
 */
GoogleMap.instances = {};

/**
 * Events implemented by GMaps2 API
 * @type Array
 */
GoogleMap.events = ['addmaptype', 'removemaptype', 'click', 'dblclick', 'singlerightclick', 'movestart', 'move', 'moveend', 'zoomend', 'maptypechanged', 'infowindowopen', 'infowindowbeforeclose', 'infowindowclose', 'addoverlay', 'removeoverlay', 'clearoverlays', 'mouseover', 'mouseout', 'mousemove', 'dragstart', 'drag', 'dragend', 'load'];

/**
 * Initializes the widget
 */
GoogleMap.prototype.setup = function() {
	if (this.map) {
		var self = this, map = this.map, attrs = this.attributes;
		var info = $(attrs.id + '_locations').getElementsByTagName('div');	
		var bounds = new GLatLngBounds(), locations = [];
		for (var i=0; i<attrs.locations.length; i++) {
			var pnt = new GLatLng(attrs.locations[i].lat, attrs.locations[i].lng);
			bounds.extend(pnt);
			locations.push(pnt);
		}
		attrs.controls.walk(function(ctrl, idx) {
			var zoomExists = false;
			switch (ctrl.toLowerCase()) {
				case 'maptype' :
					map.addControl(new GMapTypeControl()); break;
				case 'smallmap' :
					if (!zoomExists) {
						map.addControl(new GSmallMapControl());
						zoomExists = true;
					}
					break;
				case 'largemap' :
					if (!zoomExists) {
						map.addControl(new GLargeMapControl());
						zoomExists = true;
					}
					break;
				case 'smallzoom' :
					if (!zoomExists) {
						map.addControl(new GSmallZoomControl());
						zoomExists = true;
					}
					break;
				case 'scale' :
					map.addControl(new GScaleControl()); break;
				case 'overviewmap' :
					map.addControl(new GOverviewMapControl()); break;
			}
		});
		map.setCenter(new GLatLng(attrs.center.lat, attrs.center.lng));
		map.setZoom(attrs.zoom || map.getBoundsZoomLevel(bounds));
		switch (attrs.type) {
			case 'normal' : map.setMapType(G_NORMAL_MAP); break;
			case 'satellite' : map.setMapType(G_SATELLITE_MAP); break;
			case 'hybrid' : map.setMapType(G_HYBRID_MAP); break;
		}
		if (!attrs.draggable)
			map.disableDragging();
		locations.walk(function(item, idx) {
			var marker = new GMarker(item, {
				title: attrs.locations[idx].title || null,
				draggable: self.attributes.draggableMarkers
			});
			if (!info[idx].innerHTML.empty())
				marker.bindInfoWindow(info[idx]);
			map.addOverlay(marker);
		});		
		this.raiseEvent('init');
		Event.addListener(window, 'unload', GUnload);		
	}
	GoogleMap.instances[this.attributes.id] = this;
};

/**
 * Registers an event listener in the widget. Overrides
 * {@link Observable#addEventListener}.
 * @param {String} name Event name
 * @param {Function} func Handler function
 * @param {Object} scope Handler scope
 * @param {Boolean} unshift Allows to add the handler in the first position of the stack
 * @type void
 */
GoogleMap.prototype.addEventListener = function(name, func, scope, unshift) {
	name = name.toLowerCase().replace(/^on/, '');
	if (GoogleMap.events.indexOf(name) != -1) {
		if (this.map)
			GEvent.bind(this.map, name, scope || this, func);
	} else {
		GoogleMap.superclass.addEventListener.apply(this, arguments);
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/googlemap.js'] = true;

}