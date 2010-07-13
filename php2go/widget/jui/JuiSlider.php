<?php

class JuiSlider extends JuiElement
{
	public function setAnimate($animate) {
		$this->params['animate'] = $animate;
	}

	public function setMax($max) {
		$this->params['max'] = $max;
	}

	public function setMin($min) {
		$this->params['min'] = $min;
	}

	public function setOrientation($orientation) {
		$this->params['orientation'] = $orientation;
	}

	public function setRange($range) {
		$this->params['range'] = $range;
	}

	public function setStep($step) {
		$this->params['step'] = $step;
	}

	public function setValue($value) {
		$this->params['value'] = $value;
	}

	public function setValues(array $values) {
		$this->params['values'] = $values;
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'start' => array('event', 'ui'),
			'slide' => array('event', 'ui'),
			'change' => array('event', 'ui'),
			'stop' => array('event', 'ui')
		));
	}

	public function init() {
		$this->view->jQuery()->addCallById($this->getId(),
			'slider', array($this->getSetupParams())
		);
		echo '<div' . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</div>' . PHP_EOL;
	}
}