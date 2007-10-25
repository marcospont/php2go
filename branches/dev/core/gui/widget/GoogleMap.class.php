<?php
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
 * @version $Id: TemplateContainer.class.php 177 2007-03-21 21:22:08Z mpont $
 */

/**
 * Google Map widget
 *
 * Based on a set of locations and configurations, this widget renders
 * a map, using the API provided by Google Maps.
 *
 * In order to use this widget, you'll have to define the key
 * WIDGETS.GOOGLE_MAPS_KEY in your global configuration set.
 *
 * Available attributes
 * # id : widget ID
 * # width : map width
 * # height : map height
 * # class : CSS class for the map container
 * # type : map type (normal, satellite or hybrid)
 * # center : center point
 * # zoom : zoom level
 * # controls : comma separated list of map controls; accepted values: maptype, smallmap, largemap, smallzoom, scale, overviewmap
 * # locations : array of locations, each one containing lat, lng and info* (mandatory)
 * # draggable : enable or disable map dragging
 * # draggableMarkers : enable or disable dragging on markers
 *
 * Available client events:
 * # onInit
 * 
 * All client events implemented by Google Maps API are also available:
 * # onAddMapType
 * # onRemoveMapType
 * # onClick
 * # onDblClick
 * # onSingleRightClick
 * # onMoveStart
 * # onMove
 * # onMoveEnd
 * # onZoomEnd
 * # onMapTypeChanged
 * # onInfoWindowOpen
 * # onInfoWindowBeforeClose
 * # onInfoWindowClose
 * # onAddOverlay
 * # onRemoveOverlay
 * # onClearOverlays
 * # onMouseOver
 * # onMouseOut
 * # onMouseMove
 * # onDragStart
 * # onDrag
 * # onDragEnd
 * # onLoad
 *
 * @package gui
 * @subpackage widget
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class GoogleMap extends Widget
{
	/**
	 * Widget constructor
	 *
	 * @param array $attrs Attributes
	 * @return GoogleMap
	 */
	function GoogleMap($attrs) {
		parent::Widget($attrs);
		$this->mandatoryAttributes[] = "locations";
	}

	/**
	 * Loads the resources needed by the
	 * widget onto the active DocumentHead
	 *
	 * @param DocumentHead &$Head Document head
	 * @static
	 */
	function loadResources(&$Head) {
		$apiKey = PHP2Go::getConfigVal('WIDGETS.GOOGLE_MAPS_KEY', FALSE);
		if (!$apiKey)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CONFIG_ENTRY_NOT_FOUND', 'WIDGETS.GOOGLE_MAPS_KEY'), E_USER_ERROR, __FILE__, __LINE__);
		$Head->addScript("http://maps.google.com/maps?file=api&v=2&key=" . $apiKey, '', 'text/javascript', 'utf-8');
	}

	/**
	 * Returns the default values for
	 * the widget's attributes
	 *
	 * @return array Default attributes
	 */
	function getDefaultAttributes() {
		return array(
			'id' => PHP2Go::generateUniqueId(parent::getClassName()),
			'width' => '100%',
			'height' => '100%',
			'class' => '',
			'type' => 'normal',
			'center' => NULL,
			'zoom' => NULL,
			'controls' => array('maptype', 'largemap'),
			'draggable' => TRUE,
			'draggableMarkers' => FALSE
		);
	}

	/**
	 * Applies the necessary transformation on
	 * attributes before loading them
	 *
	 * @param array $attrs Attributes
	 */
	function loadAttributes($attrs) {
		if (is_int($attrs['width']))
			$attrs['width'] .= 'px';
		if (is_int($attrs['height']))
			$attrs['height'] .= 'px';
		if (!in_array($attrs['type'], array('normal', 'satellite', 'hybrid')))
			$attrs['type'] = 'normal';
		if (!empty($attrs['controls']) && !is_array($attrs['controls'])) {
			$attrs['controls'] = preg_replace("/\s/", '', $attrs['controls']);
			$attrs['controls'] = explode(',', $attrs['controls']);
			$attrs['controls'] = array_unique($attrs['controls']);
		}
		parent::loadAttributes($attrs);
	}

	/**
	 * Validates the widget's properties
	 */
	function validate() {
		parent::validate();
		if (empty($this->attributes['locations']))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_WIDGET_MANDATORY_PROPERTY', array('locations', parent::getClassName())), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Renders the GoogleMap widget
	 */
	function render() {
		$attrs =& $this->attributes;
		$code = sprintf("\n<div id=\"%s\"%s style=\"width:%s;height:%s;\"></div>", $attrs['id'], (!empty($attrs['class']) ? " class=\"{$attrs['class']}\"" : ""), $attrs['width'], $attrs['height']);
		$code .= sprintf("\n<div id=\"%s_locations\" style=\"display:none\">", $attrs['id']);
		foreach ($this->attributes['locations'] as $location)
			$code .= sprintf("\n  <div>%s</div>", @$location['info']);
		$code .= "\n</div>";
		$locations = array();
		foreach ($this->attributes['locations'] as $location) {
			$locations[] = array(
				'lat' => $location['lat'],
				'lng' => $location['lng'],
				'title' => @$location['title']
			);
		}
		print $code;
		parent::renderJS(array(
			'id' => $attrs['id'],
			'type' => $attrs['type'],
			'center' => (is_array($attrs['center']) ? $attrs['center'] : $this->_calculateCenter($locations)),			
			'zoom' => $attrs['zoom'],
			'controls' => $attrs['controls'],
			'locations' => $locations,
			'draggable' => $attrs['draggable'],
			'draggableMarkers' => $attrs['draggableMarkers']
		));
	}

	/**
	 * Calculates a center point based on all locations
	 *
	 * @param array $locations
	 * @access private
	 * @return array
	 */
	function _calculateCenter($locations) {
		foreach ($locations as $location) {
			if (!isset($min)) {
				$min = array($location['lat'], $location['lng']);
				$max = array($location['lat'], $location['lng']);
			} else {
				$min = array(min($min[0], $location['lat']), min($min[1], $location['lng']));
				$max = array(max($max[0], $location['lat']), max($max[1], $location['lng']));
			}
		}
		$center = array(
			'lat' => $min[0] + (($max[0]-$min[0])/2),
			'lng' => $min[1] + (($max[1]-$min[1])/2)
		);
		return $center;
	}
}
?>