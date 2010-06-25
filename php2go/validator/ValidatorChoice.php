<?php

class ValidatorChoice extends Validator
{
	protected $choices = array();
	
	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, '"{value}" is not a valid choice. Valid choices are: {choices}.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} must be one of the following values: {choices}.');
	}
	
	protected function validateOptions() {
		if (empty($this->choices) || !is_array($this->choices))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}
	
	public function validate($value) {
		if (!$this->inChoices($value)) {
			$this->setError($this->resolveMessage(), array('value' => $value, 'choices' => implode(',', $this->choices)));
			return false;
		}
		return true;
	}
	
	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if (!$this->validate($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage(), array('choices' => implode(',', $this->choices)));
	}
	
	private function inChoices($value) {
		return (in_array($value, $this->choices));
	}
}