<?php

class ValidatorRequired extends Validator
{
	protected $active;

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
		if (isset($this->active) && !$this->isActive($model))
			return;
		if (Util::isEmpty($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}

	protected function isActive(Model $model) {
		$active = preg_replace_callback('/\{(\w+)\}/', create_function(
			'$matches',
			'return "\$model->__get(\'$matches[1]\')";'
		), $this->active);
		return Util::evaluateExpression($active, array('model' => $model));
	}
}