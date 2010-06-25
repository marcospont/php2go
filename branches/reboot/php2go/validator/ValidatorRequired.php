<?php

class ValidatorRequired extends Validator
{
	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, 'Value can not be empty.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} is required.');
	}

	public function validate($value) {
		if (Util::isEmpty($value)) {
			$this->setError($this->resolveMessage());
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = $model->{$attr};
		if (Util::isEmpty($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}
}