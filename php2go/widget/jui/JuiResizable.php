<?php

class JuiResizable extends JuiElement
{
	protected $tagName = 'div';

	public function setTagName($tagName) {
		$this->tagName = $tagName;
	}

	public function setAlsoResize($alsoResize) {
		$this->params['alsoResize'] = $alsoResize;
	}

	public function setAnimation($animation) {
		$this->params['animate'] = $animation;
	}

	public function setAnimationDuration($animationDuration) {
		$this->params['animateDuration'] = $animationDuration;
	}

	public function setAnimationEasing($animationEasing) {
		$this->params['animateEasing'] = $animationEasing;
	}

	public function setAspectRatio($aspectRatio) {
		$this->params['aspectRatio'] = $aspectRatio;
	}

	public function setAutoHide($autoHide) {
		$this->params['autoHide'] = (bool)$autoHide;
	}

	public function setCancel($cancel) {
		$this->params['cancel'] = $cancel;
	}

	public function setContainment($containment) {
		$this->params['containment'] = $containment;
	}

	public function setDelay($delay) {
		$this->params['delay'] = (int)$delay;
	}

	public function setDistance($distance) {
		$this->params['distance'] = (int)$distance;
	}

	public function setGhost(array $ghost) {
		$this->params['ghost'] = $ghost;
	}

	public function setGrid(array $grid) {
		$this->params['grid'] = $grid;
	}

	public function setHandles($handles) {
		$this->params['handles'] = $handles;
	}

	public function setHelper($helper) {
		$this->params['helper'] = $helper;
	}

	public function setMaxHeight($maxHeight) {
		$this->params['maxHeight'] = (int)$maxHeight;
	}

	public function setMaxWidth($maxWidth) {
		$this->params['maxWidth'] = (int)$maxWidth;
	}

	public function setMinHeight($minHeight) {
		$this->params['minHeight'] = (int)$minHeight;
	}

	public function setMinWidth($minWidth) {
		$this->params['minWidth'] = (int)$minWidth;
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'start' => array('event', 'ui'),
			'resize' => array('event', 'ui'),
			'stop' => array('event', 'ui')
		));
	}

	public function init() {
		echo '<' . $this->tagName . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</' . $this->tagName . '>' . PHP_EOL;
		$this->view->jQuery()->addCallById($this->getId(),
			'resizable', array($this->getSetupParams())
		);
	}

	public function resizable($selector, array $options=array()) {
		$this->view->jQuery()->addCall($selector, 'resizable', array((!empty($options) ? $options : Js::emptyObject())));
	}
}