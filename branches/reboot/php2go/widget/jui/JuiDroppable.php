<?php

class JuiDroppable extends JuiElement
{
	public function setAccept($accept) {
		$this->params['accept'] = $accept;
	}

	public function setActiveClass($activeClass) {
		$this->params['activeClass'] = $activeClass;
	}

	public function setGreedy($greedy) {
		$this->params['greedy'] = (bool)$greedy;
	}

	public function setHoverClass($hoverClass) {
		$this->params['hoverClass'] = $hoverClass;
	}

	public function setScope($scope) {
		$this->params['scope'] = $scope;
	}

	public function setTolerance($tolerance) {
		$this->params['tolerance'] = $tolerance;
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'activate' => array('event', 'ui'),
			'deactivate' => array('event', 'ui'),
			'over' => array('event', 'ui'),
			'out' => array('event', 'ui'),
			'drop' => array('event', 'ui')
		));
	}

	public function init() {
		echo '<div' . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</div>' . PHP_EOL;
		$this->view->jQuery()->addCallById($this->getId(),
			'droppable', array($this->getSetupParams())
		);
	}
}