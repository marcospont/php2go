<?php

class GoogleMap extends WidgetElement
{
	protected $apiKey;
	protected $params = array();

	public function setApiKey($apiKey) {
		$this->apiKey = $apiKey;
	}

	public function setAddress($address) {
		$this->params['address'] = $address;
	}

	public function setCenter(array $center) {
		$center = array_values($center);
		if (sizeof($center) == 2) {
			$this->params['latitude'] = $center[0];
			$this->params['longitude'] = $center[1];
		}
	}

	public function setZoom($zoom) {
		$this->params['zoom'] = min(19, max(1, intval($zoom)));
	}

	public function setMarkers(array $markers) {
		foreach ($markers as $marker) {
			if (is_array($marker)) {
				$this->addMarker($marker);
			}
		}
	}

	public function addMarker(array $marker) {
		if (Util::isMap($marker)) {
			if (isset($marker['address']) || (isset($marker['latitude']) && isset($marker['longitude']))) {
				if (!isset($this->params['markers']))
					$this->params['markers'] = array();
				$this->params['markers'][] = $marker;
			}
		}
 	}

 	public function setMarkerPrepend($prepend) {
 		$this->params['html_prepend'] = $prepend;
 	}

 	public function setMarkerAppend($append) {
 		$this->params['html_append'] = $append;
 	}

 	public function setMarkerIcon(array $icon) {
 		if (Util::isMap($icon)) {
 			$this->params['icon'] = $icon;
 		}
 	}

 	public function setControls(array $controls) {
 		$this->params['controls'] = $controls;
 	}

 	public function setMapType($mapType) {
 		if (in_array($mapType, array('normal', 'satellite', 'hybrid'))) {
 			switch ($mapType) {
 				case 'normal' :
 					$this->params['maptype'] = Js::identifier('G_NORMAL_MAP');
 					break;
 				case 'satellite' :
 					$this->params['maptype'] = Js::identifier('G_SATELLITE_MAP');
 					break;
 				default :
 					$this->params['maptype'] = Js::identifier('G_HYBRID_MAP');
 					break;
 			}
 		}
 	}

 	public function setScrollWheel($scrollWheel) {
 		$this->params['scrollwheel'] = $scrollWheel;
 	}

 	public function run() {
 		if (!empty($this->apiKey)) {
 			$this->view->head()->addScriptFile('http://maps.google.com/maps?file=api&v=2&key=' . $this->apiKey . '&sensor=false', array(), 0);
 			$this->view->head()->addLibrary('jquery-gmap');
	 		$this->view->jQuery()->addCallById($this->getId(),
	 			'gMap', array((!empty($this->params) ? $this->params : Js::emptyObject()))
	 		);
	 		echo '<div' . $this->renderAttrs() . '></div>' . PHP_EOL;
 		}
 	}
}