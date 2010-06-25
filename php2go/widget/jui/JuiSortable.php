<?php

class JuiSortable extends JuiElement
{
	protected $tagName = 'div';

	public function setTagName($tagName) {
		$this->tagName = $tagName;
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

	public function setConnectWith($connectWith) {
		$this->params['connectWith'] = $connectWith;
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

	public function setDropOnEmpty($dropOnEmpty) {
		$this->params['dropOnEmpty'] = (bool)$dropOnEmpty;
	}

	public function setForceHelperSize($forceHelperSize) {
		$this->params['forceHelperSize'] = (bool)$forceHelperSize;
	}

	public function setForcePlaceholderSize($forcePlaceholderSize) {
		$this->params['forcePlaceholderSize'] = (bool)$forcePlaceholderSize;
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

	public function setItems($items) {
		$this->params['items'] = $items;
	}

	public function setOpacity($opacity) {
		$this->params['opacity'] = (float)$opacity;
	}

	public function setPlaceholder($placeholder) {
		$this->params['placeholder'] = $placeholder;
	}

	public function setRevert($revert) {
		$this->params['revert'] = $revert;
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

	public function setTolerance($tolerance) {
		$this->params['tolerance'] = $tolerance;
	}

	public function setZIndex($zIndex) {
		$this->params['zIndex'] = $zIndex;
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'selected' => array('event', 'ui'),
			'selecting' => array('event', 'ui'),
			'start' => array('event', 'ui'),
			'stop' => array('event', 'ui'),
			'unselected' => array('event', 'ui'),
			'unselecting' => array('event', 'ui')
		));
	}

	public function init() {
		echo '<' . $this->tagName . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</' . $this->tagName . '>' . PHP_EOL;
		$this->view->jQuery()->addCallById($this->getId(),
			'sortable', array($this->getSetupParams())
		);
	}

	protected function getDefaultParams() {
		return array(
			'cursor' => 'crosshair'
		);
	}
}