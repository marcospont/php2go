<?php

class JuiAccordion extends JuiElement
{
	public function setActive($active) {
		$this->params['active'] = $active;
	}

	public function setAnimation($animation) {
		$this->params['animated'] = $animation;
	}

	public function setAutoHeight($autoHeight) {
		$this->params['autoHeight'] = (bool)$autoHeight;
	}

	public function setClearStyle($clearStyle) {
		$this->params['clearStyle'] = (bool)$clearStyle;
	}

	public function setCollapsible($collapsible) {
		$this->params['collapsible'] = (bool)$collapsible;
	}

	public function setEvent($event) {
		$this->params['event'] = $event;
	}

	public function setFillSpace($fillSpace) {
		$this->params['fillSpace'] = (bool)$fillSpace;
	}

	public function setHeaderIcon($icon) {
		if (!isset($this->params['icons']))
			$this->params['icons'] = array();
		$this->params['icons']['header'] = $icon;
	}

	public function setHeaderSelectedIcon($icon) {
		if (!isset($this->params['icons']))
			$this->params['icons'] = array();
		$this->params['icons']['headerSelected'] = $icon;
	}

	public function setNavigation($navigation) {
		$this->params['navigation'] = (bool)$navigation;
	}

	public function setNavigationFilter($navigationFilter) {
		$this->params['navigationFilter'] = Js::callback($navigationFilter);
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'change' => array('event', 'ui'),
			'changestart' => array('event', 'ui')
		));
	}

	public function init() {
		echo '<div' . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</div>';
		$this->view->jQuery()->addCallById($this->getId(), 'accordion', array($this->getSetupParams()));
	}
}