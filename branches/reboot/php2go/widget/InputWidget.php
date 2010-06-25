<?php

abstract class InputWidget extends Widget
{
	protected $model;
	protected $modelAttr;
	protected $name;
	protected $value;
	protected $inputOptions = array();
	
	public function __construct(View $view, Widget $parent=null) {
		parent::__construct($view, $parent);
		$this->inputOptions = new HashMap();
	}
	
	public function getModel() {
		return $this->model;
	}
	
	public function hasModel() {
		return ($this->model instanceof Model && !empty($this->attribute));
	}
	
	public function setModel(Model $model) {
		$this->model = $model;
	}
	
	public function getModelAttr() {
		return $this->modelAttr;
	}
	
	public function setModelAttr($attr) {
		$this->modelAttr = $attr;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getInputOptions() {
		return $this->inputOptions;
	}
	
	public function setInputOptions(array $attrs) {
		$this->inputOptions = new HashMap($attrs);
	}
	
	protected function resolveId() {
		if ($this->name !== null)
			$name = $this->name;
		elseif (isset($this->inputOptions['name']))
			$name = $this->inputOptions['name'];
		else
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" property is required for "%s" widget.', array('name', get_class($this))));
		if ($this->id === null) {
			if (!isset($this->inputOptions['id']))
				$this->inputOptions['id'] = $this->view->form()->normalizeId($name);
		} else {
			$this->inputOptions['id'] = $this->id;
		}
	}
}