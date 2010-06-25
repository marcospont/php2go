<?php

abstract class Validator
{
	protected $error;
	protected $breakOnError = false;
	protected $allowEmpty = true;
	protected $modelAttributes = array();
	protected $modelScenarios = array();
	protected $message;
	protected $messageDomain;
	protected $defaultMessage;
	protected $defaultMessages = array();
	protected $defaultModelMessage;
	protected $defaultModelMessages = array();

	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, 'Invalid value.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} is not valid.');
	}

	public function loadOptions(array $options) {
		$this->resetOptions();
		foreach ($options as $k => $v)
			$this->{$k} = $v;
		$this->validateOptions();
	}

	protected function resetOptions() {
		$reflection = new ReflectionClass($this);
		$defaults = $reflection->getDefaultProperties();
		$properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
		foreach ($properties as $prop) {
			if (!preg_match('/^default(Model)?Messages?$/', $prop->getName()))
				$this->{$prop->getName()} = $defaults[$prop->getName()];
		}
		foreach ($this->defaultMessages as $k => $v)
			unset($this->{$k});
		foreach ($this->defaultModelMessages as $k => $v)
			unset($this->{$k});
	}

	protected function validateOptions() {
	}

	public function hasModelAttribute($attr) {
		return (in_array($attr, $this->modelAttributes, true));
	}

	public function hasModelScenario($scenario) {
		return (empty($this->modelScenarios) || in_array($scenario, $this->modelScenarios, true));
	}

	abstract protected function validate($value);

	public function validateModel(Model $model, $attrs=null) {
		if (is_array($attrs))
			$attrs = array_intersect($this->modelAttributes, $attrs);
		else
			$attrs = $this->modelAttributes;
		foreach ($attrs as $attr) {
			if (!$this->breakOnError || !$model->hasErrors($attr))
				$this->validateModelAttribute($model, $attr);
		}
	}

	protected function validateModelAttribute(Model $model, $attr) {
	}

	public function getError() {
		return $this->error;
	}

	public function setError($message, array $params=array()) {
		$this->error = Util::buildMessage($message, $params);
	}

	protected function addModelError(Model $model, $attr, $message, $params=array()) {
		$params['attribute'] = $model->getAttributeLabel($attr);
		$model->addError($attr, Util::buildMessage($message, $params));
	}

	protected function resolveModelMessage($key=null, $tryMainMessage=true) {
		return $this->resolveMessage($key, $tryMainMessage, true);
	}

	protected function resolveMessage($key=null, $tryMainMessage=true, $isModel=false) {
		$prop = ($key !== null ? $key : 'message');
		if (isset($this->{$prop})) {
			if ($this->messageDomain !== null)
				return __($this->messageDomain, $this->{$prop});
			return __($this->{$prop});
		} elseif ($key !== null && $tryMainMessage && isset($this->message)) {
			if (isset($this->messageDomain))
				return __($this->messageDomain, $this->message);
			return __($this->message);
		} else {
			$defaultMessages = ($isModel ? 'defaultModelMessages' : 'defaultMessages');
			$defaultMessage = ($isModel ? 'defaultModelMessage' : 'defaultMessage');
			if ($key !== null && isset($this->{$defaultMessages}[$key]))
				return $this->{$defaultMessages}[$key];
			return $this->{$defaultMessage};
		}
	}
}