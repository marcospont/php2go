<?php

class JuiTitlePane extends JuiElement
{
	protected $draggable;
	protected $resizable;

	public function setAnimation($animation) {
		$this->params['animation'] = $animation;
	}

	public function setCollapsedIcon($collapsedIcon) {
		$this->params['collapsedIcon'] = $collapsedIcon;
	}

	public function setCollapsible($collapsible) {
		$this->params['collapsible'] = (bool)$collapsible;
	}

	public function setCollapsed($collapsed) {
		$this->params['collapsed'] = (bool)$collapsed;
	}

	public function setDraggable($draggable) {
		$this->draggable = $draggable;
	}

	public function setExpandedIcon($expandedIcon) {
		$this->params['expandedIcon'] = $expandedIcon;
	}

	public function setResizable($resizable) {
		$this->resizable = $resizable;
	}

	public function setTitle($title) {
		$this->params['title'] = $title;
	}

	public function preInit() {
		parent::preInit();
		$this->view->head()->addStyleSheet(
			$this->view->asset($this->view->jQuery()->getUiPath() . DS . 'jquery-ui-pane.css'), array(), 0
		);
	}

	public function init() {
		if ($this->params['collapsible']) {
			if ($this->params['collapsed'])
				$this->view->jQuery()->addCall("#{$this->getId()} .ui-pane-content", 'hide');
			$this->view->jQuery()->addCall("#{$this->getId()} .ui-pane-title .ui-icon",
				'click', array(Js::callback(
					"$(this).toggleClass(\"" . $this->params['expandedIcon'] . "\").toggleClass(\"" . $this->params['collapsedIcon'] . "\");" . PHP_EOL .
					"$(this).parents(\".ui-pane:first\").find(\".ui-pane-content\").toggle(" .
						(isset($this->params['animation']) ? Js::encode($this->params['animation']) : '') .
					");")
				)
			);
		}
		if ($this->draggable) {
			$draggable = new JuiDraggable($this->view, $this->parent);
			$draggable->draggable("#{$this->getId()}", (is_array($this->draggable) ? $this->draggable : array()));
		}
		if ($this->resizable) {
			$resizable = new JuiResizable($this->view, $this->parent);
			$resizable->resizable("#{$this->getId()}", (is_array($this->resizable) ? $this->resizable : array()));
		}
		echo '<div' . $this->renderAttrs() . '>';
		echo '<div class="ui-pane-title ui-widget-header ui-corner-all">';
		if ($this->params['collapsible'])
			echo '<span class="ui-icon ' . ($this->params['collapsed'] ? $this->params['collapsedIcon'] : $this->params['expandedIcon']) . '"></span>';
		echo $this->view->escape($this->params['title']);
		echo '</div><div class="ui-pane-content">' . PHP_EOL;
	}

	public function run() {
		echo '</div></div>' . PHP_EOL;
	}

	protected function renderAttrs() {
		$this->attrs['class'] = 'ui-pane ui-widget ui-widget-content ui-helper-clearfix ui-corner-all' . (isset($this->attrs['class']) ? ' ' . implode(' ', (array)$this->attrs['class']) : '');
		return parent::renderAttrs();
	}

	protected function getDefaultParams() {
		return array(
			'collapsedIcon' => 'ui-icon-plusthick',
			'collapsible' => false,
			'collapsed' => false,
			'expandedIcon' => 'ui-icon-minusthick'
		);
	}
}