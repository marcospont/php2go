<?php

class JuiTabContainer extends JuiElement
{
	protected $panes = array();

	public function setAjaxOptions(array $ajaxOptions) {
		$this->params['ajaxOptions'] = $ajaxOptions;
	}

	public function setCache($cache) {
		$this->params['cache'] = (bool)$cache;
	}

	public function setCollapsible($collapsible) {
		$this->params['collapsible'] = (bool)$collapsible;
	}

	public function setCookieOptions(array $cookieOptions) {
		$this->params['cookie'] = $cookieOptions;
	}

	public function setDisabledTabs(array $disabledTabs) {
		$this->params['disabled'] = $disabledTabs;
	}

	public function setEvent($event) {
		$this->params['event'] = $event;
	}

	public function setFxOptions(array $fxOptions) {
		$this->params['fx'] = $fxOptions;
	}

	public function setIdPrefix($idPrefix) {
		$this->params['idPrefix'] = $idPrefix;
	}

	public function setSelected($selected) {
		$this->params['selected'] = $selected;
	}

	public function setSpinner($spinner) {
		$this->params['spinner'] = $spinner;
	}

	public function addPane(JuiTabPane $pane) {
		$this->panes[] = $pane;
	}

	public function preInit() {
		parent::preInit();
		$this->registerJsEvents(array(
			'select' => array('event', 'ui'),
			'load' => array('event', 'ui'),
			'show' => array('event', 'ui'),
			'add' => array('event', 'ui'),
			'remove' => array('event', 'ui'),
			'enable' => array('event', 'ui'),
			'disable' => array('event', 'ui')
		));
	}

	public function init() {
		echo '<div' . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		$this->renderTabs();
		echo '</div>' . PHP_EOL;
		$this->view->jQuery()->addCallById($this->getId(),
			'tabs', array($this->getSetupParams())
		);
	}

	protected function renderTabs() {
		$nav = '<ul>' . PHP_EOL;
		$tabs = '';
		foreach ($this->panes as $pane) {
			if (isset($pane->url)) {
				$nav .= "\t" . '<li><a href="' . $pane->url . '" title="' . $pane->label . '"><span>' . $this->view->escape($pane->label) . '</span></a></li>' . PHP_EOL;
			} else {
				$nav .= "\t" . '<li><a href="#' . $pane->id  . '" title="' . $pane->label . '"><span>' . $this->view->escape($pane->label) . '</span></a></li>' . PHP_EOL;
				$tabs .= '<div' . $pane->renderAttrs() . '>' . PHP_EOL  . $pane->getContent() . PHP_EOL . '</div>' . PHP_EOL;
			}
		}
		$nav .= '</ul>' . PHP_EOL;
		echo $nav . $tabs;
	}
}