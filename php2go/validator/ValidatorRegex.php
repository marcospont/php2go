<?php

class ValidatorRegex extends Validator
{
	protected $pattern = null;

	public function __construct() {
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} contains an invalid value.');
	}

	protected function validateOptions() {
		if ($this->pattern === null)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}

	public function validate($value) {
		if (!$this->matchPattern($value)) {
			$this->setError($this->resolveMessage());
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if (!$this->matchPattern($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}

	protected function matchPattern($value) {
		return (preg_match($this->pattern, $value));
	}
}