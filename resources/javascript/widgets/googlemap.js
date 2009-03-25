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
 * GMap2 class events
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
		if (attrs.zoom || attrs.locations.length > 0)
			map.setZoom(attrs.zoom || map.getBoundsZoomLevel(bounds));
		else
			map.setZoom(8);
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
 * Registers an event listener on the widget. Overrides
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

/**
 * Registers an event listener on a map object, using the GEvent class
 * @param {Object} obj Map, marker, overlay, polyline, ...
 * @param {String} name Event name
 * @param {Function} func Handler function
 * @param {Object} scope Handler scope
 * @type void
 */
GoogleMap.prototype.bindListener = function(obj, name, func, scope) {
	GEvent.bind(obj, name, scope || this, func);
};

/**
 * Adds a marker on the map
 * @param {GPoint} point Marker point
 * @param {Object} opts Marker options
 * @param {Boolean} center Set the new marker as the center of the map?
 * @param {Number} zoom New zoom level
 * @type GMarker
 */
GoogleMap.prototype.addMarker = function(point, opts, center, zoom) {
	opts = Object.extend({draggable: true}, (opts || {}));
	var marker = new GMarker(point, opts);
	if (opts.info)
		marker.bindInfoWindowHtml(opts.info);
	if (!!center)
		this.map.setCenter(point);
	if (zoom > 0)
		this.map.setZoom(zoom);
	this.map.addOverlay(marker);
	return marker;
};

/**
 * Removes all overlays from the map
 * @type void
 */
GoogleMap.prototype.clear = function() {
	this.map.clearOverlays();
};

/**
 * Searches for a given address using the Google Geocode Client
 * @param {String} address Address
 * @param {Function} callback Callback function
 * @type void
 */
GoogleMap.prototype.findAddress = function(address, callback) {
	var geocoder = new GClientGeocoder();
	if (geocoder)
		geocoder.getLatLng(address, callback);
};

PHP2Go.included[PHP2Go.baseUrl + 'widgets/googlemap.js'] = true;

}