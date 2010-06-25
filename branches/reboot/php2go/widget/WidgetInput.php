<?php

abstract class WidgetInput extends WidgetElement
{
	protected $name;
	protected $value;
	protected $model;
	protected $modelAttr;

	public function setName($name) {
		$this->name = $name;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function hasModel() {
		return (isset($this->model) && !empty($this->modelAttr));
	}

	public function setModel(Model $model) {
		$this->model = $model;
	}

	public function setModelAttr($attr) {
		$this->modelAttr = $attr;
	}

	public function init() {
		$this->defineId();
	}

	protected function defineId() {
		if (!isset($this->attrs['id'])) {
			if ($this->name !== null) {
				$this->attrs['id'] = $this->getIdByName($this->name);
			} elseif ($this->hasModel()) {
				$attr = $this->modelAttr;
				$this->attrs['id'] = $this->getIdByModelAttr($this->model, $attr);
			} else {
				throw new Exception(__(PHP2GO_LANG_DOMAIN, '"%s" widget requires "name" or "model" and "modelAttr" options.', array(get_class($this))));
			}
		}
	}
}