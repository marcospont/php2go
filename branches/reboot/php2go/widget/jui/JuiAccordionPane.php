<?php

class JuiAccordionPane extends JuiElement
{
	protected $title;

	public function setTitle($title) {
		$this->title = $title;
	}

	public function init() {
		echo '<h3><a href="#' . $this->getId() . '">' . $this->view->escape($this->title) . '</a></h3>';
		echo '<div' . $this->renderAttrs() . '>' . PHP_EOL;
	}

	public function run() {
		echo '</div>' . PHP_EOL;
	}
}