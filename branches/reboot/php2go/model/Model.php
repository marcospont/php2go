<?php

abstract class Model extends Component implements ArrayAccess, IteratorAggregate
{
	private $namePrefix;
	protected $scenario;
	protected $validators;
	protected $errors = array();

	public function __construct() {
		$this->registerEvents(array('onImport', 'onBeforeValidate', 'onAfterValidate'));
	}

	public function init() {
		$this->attachBehaviors($this->behaviors());
	}

	public function getNamePrefix() {
		if (!isset($this->namePrefix))
			$this->namePrefix = Inflector::variablize(get_class($this));
		return $this->namePrefix;
	}

	public function setNamePrefix($namePrefix) {
		$this->namePrefix = $namePrefix;
	}

	public function getScenario() {
		return $this->scenario;
	}

	public function setScenario($scenario) {
		$this->scenario = $scenario;
	}

	public function attributeLabels() {
		return array();
	}

	public function behaviors() {
		return array();
	}

	public function rules() {
		return array();
	}

	abstract function getAttributeNames();

	public function getAttributes(array $names=array()) {
		$values = array();
		$attributes = $this->getAttributeNames();
		if (!empty($names)) {
			foreach ($names as $name) {
				if (isset($attributes[$name]))
					$values[$name] = $this->{$name};
			}
		} else {
			foreach ($attributes as $name)
				$values[$name] = $this->{$name};
		}
		return $values;
	}

	public function hasAttribute($name) {
		return (in_array($name, $this->getAttributeNames()));
	}

	public function getAttribute($name) {
		return $this->{$name};
	}

	public function getAttributeLabel($attr, $fallback=null) {
		$labels = $this->attributeLabels();
		if (isset($labels[$attr]))
			return $labels[$attr];
		return Inflector::humanize($attr);
	}

	public function setAttribute($name, $value) {
		$this->{$name} = $value;
	}

	public function setAttributes(array $values) {
		$attributes = array_flip($this->getAttributeNames());
		foreach ($values as $name => $value) {
			if (isset($attributes[$name]))
				$this->{$name} = $value;
		}
	}

	public function isAttributeRequired($attr) {
		foreach ($this->getValidators($attr) as $validator) {
			if ($validator instanceof ValidatorRequired)
				return true;
		}
		return false;
	}

	public function import(array $attrs=array()) {
		$this->setAttributes($attrs);
		$this->raiseEvent('onImport', new Event($this));
	}

	public function validate($attrs=null) {
		$this->clearErrors();
		if ($this->raiseEvent('onBeforeValidate', new Event($this))) {
			foreach ($this->getValidators() as $validator) {
				$validator->validateModel($this, $attrs);
			}
			$this->raiseEvent('onAfterValidate', new Event($this));
			return (!$this->hasErrors());
		}
		return false;
	}

	public function getValidators($attr=null) {
		if ($this->validators === null)
			$this->validators = $this->createValidators();
		$validators = array();
		$scenario = $this->getScenario();
		foreach ($this->validators as $validator) {
			if ($validator->hasModelScenario($scenario)) {
				if ($attr === null || $validator->hasModelAttribute($attr))
					$validators[] = $validator;
			}
		}
		return $validators;
	}

	public function hasErrors($attr=null) {
		if ($attr !== null)
			return (isset($this->errors[$attr]));
		return (!empty($this->errors));
	}

	public function getGlobalErrors() {
		return (isset($this->errors[0]) ? $this->errors[0] : null);
	}

	public function getErrors($attr=null) {
		if ($attr !== null)
			return (isset($this->errors[$attr]) ? $this->errors[$attr] : null);
		return $this->errors;
	}

	public function addGlobalError($error) {
		if (!isset($this->errors[0]))
			$this->errors[0] = array();
		if (!in_array($error, $this->errors[0]))
			$this->errors[0][] = $error;
	}

	public function addGlobalErrors(array $errors) {
		if (!isset($this->errors[0]))
			$this->errors[0] = array();
		foreach ($errors as $error) {
			if (!in_array($error, $this->errors[0]))
				$this->errors[0][] = $error;
		}
	}

	public function addError($attr, $error) {
		if (!isset($this->errors[$attr]))
			$this->errors[$attr] = array();
		if (!in_array($error, $this->errors[$attr]))
			$this->errors[$attr][] = $error;
	}

	public function addErrors(array $errors) {
		foreach ($errors as $attr => $error) {
			if (!isset($this->errors[$attr]))
				$this->errors[$attr] = array();
			if (is_array($error)) {
				foreach ($error as $i => $e) {
					if (!in_array($error, $this->errors[$attr]))
						$this->errors[$attr][$i] = $e;
				}
			} else {
				if (!in_array($error, $this->errors[$attr]))
					$this->errors[$attr][] = $error;
			}
		}
	}

	public function clearErrors($attr=null) {
		if ($attr !== null)
			unset($this->errors[$attr]);
		else
			$this->errors = array();
	}

	public function getIterator() {
		return new ArrayIterator($this->getAttributes());
	}

	public function offsetExists($offset) {
		return property_exists($this, $offset);
	}

	public function offsetGet($offset) {
		return $this->{$offset};
	}

	public function offsetSet($offset, $item) {
		$this->{$offset} = $item;
	}

	public function offsetUnset($offset) {
		unset($this->{$offset});
	}

	private function createValidators() {
		$validators = array();
		foreach ($this->rules() as $rule) {
			if (is_array($rule) && isset($rule[0], $rule[1])) {
				$attrs = array_shift($rule);
				$name = array_shift($rule);
				$validators[] = ModelValidator::factory($name, $this, $attrs, $rule);
			} else {
				throw new Exception(__(PHP2GO_LANG_DOMAIN, '"%s" has an invalid validation rule. The rule must specify attributes and validator.', array(__CLASS__)));
			}
		}
		return $validators;
	}
}