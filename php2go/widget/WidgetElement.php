<?php

abstract class WidgetElement extends Widget
{
	private static $idCounter = array();
	protected $attrs = array();

	public function preInit() {
		parent::preInit();
		$this->attrs = $this->getDefaultAttrs();
	}

	public function getId() {
		if (!isset($this->attrs['id']))
			$this->attrs['id'] = Util::id(get_class($this));
		return $this->attrs['id'];
	}

	public function setId($id) {
		$this->attrs['id'] = $id;
	}

	public function setClass($class) {
		$this->attrs['class'] = $class;
	}

	public function setStyle($style) {
		$this->attrs['style'] = $style;
	}

	public function setAttrs(array $attrs) {
		$this->attrs = array_merge($this->attrs, $attrs);
	}

	protected function getDefaultAttrs() {
		return array();
	}

	protected function renderAttrs() {
		if (!isset($this->attrs['id']))
			$this->attrs['id'] = Util::id(get_class($this));
		return parent::renderAttrs($this->attrs);
	}
}