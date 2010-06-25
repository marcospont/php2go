<?php

class JuiSelectable extends JuiElement
{
	protected $tagName = 'div';

	public function setTagName($tagName) {
		$this->tagName = $tagName;
	}

	public function setAutoRefresh($autoRefresh) {
		$this->params['autoRefresh'] = (bool)$autoRefresh;
	}

	public function setCancel($cancel) {
		$this->params['cancel'] = $cancel;
	}

	public function setDelay($delay) {
		$this->params['delay'] = (int)$delay;
	}

	public function setDistance($distance) {
		$this->params['distance'] = (int)$distance;
	}

	public function setFilter($filter) {
		$this->params['filter'] = $filter;
	}

	public function setTolerance($tolerance) {
		$this->params['tolerance'] = $tolerance;
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
			'selectable', array($this->getSetupParams())
		);
	}
}