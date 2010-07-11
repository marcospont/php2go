<?php

class ValidatorNoWhitespace extends Validator
{
	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, 'Value must not contain whitespace characters.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} must not contain whitespace characters.');
	}

	public function validate($value) {
		if ($this->hasWhitespace($value)) {
			$this->setError($this->resolveMessage());
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if ($this->hasWhitespace($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}

	protected function hasWhitespace($value) {
		return (!preg_match('/^\S+$/i', $value));
	}
}