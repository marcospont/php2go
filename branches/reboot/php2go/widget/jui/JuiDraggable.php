<?php

class JuiDraggable extends JuiElement
{
	protected $items = '> *';

	public function setItems($items) {
		$this->items = $items;
	}

	public function setAppendTo($appendTo) {
		$this->params['appendTo'] = $appendTo;
	}

	public function setAxis($axis) {
		$this->params['axis'] = $axis;
	}

	public function setCancel($cancel) {
		$this->params['cancel'] = $cancel;
	}

	public function setConnectToSortable($connectToSortable) {
		$this->params['connectToSortable'] = $connectToSortable;
	}

	public function setContainment($containment) {
		$this->params['containment'] = $containment;
	}

	public function setCursor($cursor) {
		$this->params['cursor'] = $cursor;
	}

	public function setCursorAt($cursorAt) {
		$this->params['cursorAt'] = $cursorAt;
	}

	public function setDelay($delay) {
		$this->params['delay'] = (int)$delay;
	}

	public function setDistance($distance) {
		$this->params['distance'] = (int)$distance;
	}

	public function setGrid(array $grid) {
		$this->params['grid'] = $grid;
	}

	public function setHandle($handle) {
		$this->params['handle'] = $handle;
	}

	public function setHelper($helper) {
		$this->params['helper'] = $helper;
	}

	public function setIframeFix($iframeFix) {
		$this->params['iframeFix'] = $iframeFix;
	}

	public function setOpacity($opacity) {
		$this->params['opacity'] = (float)$opacity;
	}

	public function setRefreshPositions($refreshPositions) {
		$this->params['refreshPositions'] = (bool)$refreshPositions;
	}

	public function setRevert($revert) {
		$this->params['revert'] = $revert;
	}

	public function setRevertDuration($revertDuration) {
		$this->params['revertDuration'] = (int)$revertDuration;
	}

	public function setScope($scope) {
		$this->params['scope'] = $scope;
	}

	public function setScroll($scroll) {
		$this->params['scroll'] = (bool)$scroll;
	}

	public function setScrollSensitivity($scrollSensitivity) {
		$this->params['scrollSensitivity'] = (int)$scrollSensitivity;
	}

	public function setScrollSpeed($scrollSpeed) {
		$this->params['scrollSpeed'] = (int)$scrollSpeed;
	}

	public function setSnap($snap) {
		$this->params['snap'] = $snap;
	}

	public function setSnapMode($snapMode) {
		$this->params['snapMode'] = $snapMode;
	}

	public function setSnapTolerance($snapTolerance) {
		$this->params['snapTolerance'] = $snapTolerance;
	}

	public function setStack($stack) {
		$this->params['stack'] = $stack;
	}

	public function setZIndex($zIndex) {
		$this->params['zIndex'] = $zIndex;
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'start' => array('event', 'ui'),
			'drag' => array('event', 'ui'),
			'stop' => array('event', 'ui')
		));
	}

	public function init() {
		echo '<div' . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</div>' . PHP_EOL;
		$this->view->jQuery()->addCall("#{$this->getId()} {$this->items}",
			'draggable', array($this->getSetupParams())
		);
	}

	public function draggable($selector, array $options=array()) {
		$this->view->jQuery()->addCall($selector, 'draggable', array((!empty($options) ? $options : Js::emptyObject())));
	}
}