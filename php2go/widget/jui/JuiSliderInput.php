<?php

class JuiSliderInput extends JuiInput
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
		parent::setValue($value);
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
		parent::init();
		$this->addJsListener('start', "$(\"#{$this->getId()}-slider\").slider('value', $('#{$this->getId()}').val());");
		$this->addJsListener('slide', "$(\"#{$this->getId()}\").val(ui.value);");
	}

	public function run() {
		echo '<div' . $this->view->html()->renderAttrs(array_merge($this->attrs, array('id' => $this->getId() . '-slider'))) . '></div>';
		if ($this->hasModel())
			echo $this->view->model()->hidden($this->model, $this->modelAttr, array('id' => $this->getId()));
		else
			echo $this->view->form()->hidden($this->name, $this->value, array('id' => $this->getId()));
		$this->view->jQuery()->addCallById($this->getId() . '-slider',
			'slider', array($this->getSetupParams())
		);
	}
}