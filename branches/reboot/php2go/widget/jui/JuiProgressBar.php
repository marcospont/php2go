<?php

class JuiProgressBar extends JuiElement
{
	public function setValue($value) {
		$this->params['value'] = $value;
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'change' => array('event', 'ui')
		));
	}

	public function init() {
		echo '<div' . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</div>' . PHP_EOL;
		$this->view->jQuery()->addCallById($this->getId(),
			'progressbar', array($this->getSetupParams())
		);
	}
}