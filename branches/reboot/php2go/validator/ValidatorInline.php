<?php

class ValidatorInline extends Validator
{
	public $object;
	public $method;
	public $params;
	
	protected function validateOptions() {
		if (empty($this->attributes) && !is_object($this->object))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}
	
	public function validate($value) {
		if (!$this->object->{$this->method}($value, $this->params)) {
			$this->setError($this->resolveMessage(), array('value' => $value));
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if (!$model->{$this->method}($value, $this->params))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}
}