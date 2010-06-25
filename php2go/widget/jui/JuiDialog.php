<?php

class JuiDialog extends JuiElement
{
	protected $trigger;

	public function setAutoOpen($autoOpen) {
		$this->params['autoOpen'] = (bool)$autoOpen;
	}

	public function setButtons(array $buttons) {
		foreach ($buttons as $label => $handler)
			$this->addButton($label, $handler);
	}

	public function addButton($label, $handler) {
		if (!isset($this->params['buttons']))
			$this->params['buttons'] = array();
		$this->params['buttons'][$label] = Js::callback($handler);
	}

	public function setCloseOnEscape($closeOnEscape) {
		$this->params['closeOnEscape'] = (bool)$closeOnEscape;
	}

	public function setCloseText($closeText) {
		$this->params['closeText'] = $closeText;
	}

	public function setDialogClass($dialogClass) {
		$this->params['dialogClass'] = $dialogClass;
	}

	public function setDraggable($draggable) {
		$this->params['draggable'] = (bool)$draggable;
	}

	public function setHeight($height) {
		$this->params['height'] = $height;
	}

	public function setHideEffect($hideEffect) {
		$this->params['hide'] = $hideEffect;
	}

	public function setMaxHeight($maxHeight) {
		$this->params['maxHeight'] = $maxHeight;
	}

	public function setMaxWidth($maxWidth) {
		$this->params['maxWidth'] = $maxWidth;
	}

	public function setMinHeight($minHeight) {
		$this->params['minHeight'] = $minHeight;
	}

	public function setMinWidth($minWidth) {
		$this->params['minWidth'] = $minWidth;
	}

	public function setModal($modal) {
		$this->params['modal'] = (bool)$modal;
	}

	public function setPosition($position) {
		$this->params['position'] = $position;
	}

	public function setResizable($resizable) {
		$this->params['resizable'] = (bool)$resizable;
	}

	public function setShowEffect($showEffect) {
		$this->params['show'] = $showEffect;
	}

	public function setStack($stack) {
		$this->params['stack'] = (bool)$stack;
	}

	public function setTitle($title) {
		$this->params['title'] = $title;
	}

	public function setTrigger($trigger) {
		$this->trigger = $trigger;
	}

	public function setWidth($width) {
		$this->params['width'] = $width;
	}

	public function setZIndex($zIndex) {
		$this->params['zIndex'] = $zIndex;
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'beforeClose' => array('event', 'ui'),
			'open' => array('event', 'ui'),
			'focus' => array('event', 'ui'),
			'dragStart' => array('event', 'ui'),
			'drag' => array('event', 'ui'),
			'dragStop' => array('event', 'ui'),
			'resizeStart' => array('event', 'ui'),
			'resize' => array('event', 'ui'),
			'resizeStop' => array('event', 'ui'),
			'close' => array('event', 'ui')
		));
	}

	public function init() {
		echo '<div' . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</div>' . PHP_EOL;
		if (isset($this->trigger)) {
			$this->view->jQuery()->addCallById($this->trigger, 'click', array(Js::callback(
				"$(\"#{$this->getId()}\").dialog(\"open\");"
			)));
		}
		$this->view->jQuery()->addCallById($this->getId(),
			'dialog', array($this->getSetupParams())
		);
	}

	protected function getDefaultParams() {
		return array(
			'autoOpen' => false
		);
	}
}