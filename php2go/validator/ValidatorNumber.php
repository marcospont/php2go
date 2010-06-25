<?php

class ValidatorNumber extends Validator
{
	protected $integer = false;
	protected $unsigned = false;
	protected $min;
	protected $max;
	protected $localized = false;
	
	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, 'Value is not a valid number.');
		$this->defaultMessages = array(
			'notInteger' => __(PHP2GO_LANG_DOMAIN, 'Value must be an integer number.'),
			'notUnsigned' => __(PHP2GO_LANG_DOMAIN, 'Value must be a positive number.'),
			'tooSmall' => __(PHP2GO_LANG_DOMAIN, 'Value must be greater or equal than {min}.'),
			'tooBig' => __(PHP2GO_LANG_DOMAIN, 'Value must be less or equal than {max}.')
		);
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} must be a valid number.');
		$this->defaultModelMessages = array(
			'notInteger' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be an integer number.'),
			'notUnsigned' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be a positive number.'),
			'tooSmall' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be greater or equal than {min}.'),
			'tooBig' => __(PHP2GO_LANG_DOMAIN, '{attribute} must be less or equal than {max}.')
		);		
	}
	
	public function validate($value) {
		$value = (string)$value;
		if ($this->integer) {
			if (($this->localized && !LocaleNumber::isInteger($value)) || (!$this->localized && !preg_match('/^-?[0-9]+$/', $value))) {
				$this->setError($this->resolveMessage('notInteger'));
				return false;
			}
		} else {
			if (($this->localized && !LocaleNumber::isNumber($value)) || (!$this->localized && !preg_match('/^-?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?$/', $value))) {
				$this->setError($this->resolveMessage('notNumber'));
				return false;
			}
		}
		$value = ($this->localized ? LocaleNumber::getFloat($value) : floatval($value));
		if ($this->unsigned && $value < 0) {
			$this->setError($this->resolveMessage('notUnsigned'));
			return false;
		}
		if ($this->min !== null && $value < $this->min) {
			$this->setError($this->resolveMessage('tooSmall'), array('min' => $this->min));
			return false;
		}
		if ($this->max !== null && $value > $this->max) {
			$this->setError($this->resolveMessage('tooBig'), array('max' => $this->max));
			return false;
		}
		return true;	
	}
	
	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		$localized = ($this->localized || ($model instanceof ActiveRecord && in_array($model->getAttributeFormat($attr), array('integer', 'float'))));
		if ($this->integer) {
			if (($localized && !LocaleNumber::isInteger($value)) || (!$this->localized && !preg_match('/^-?[0-9]+$/', $value)))
				$this->addModelError($model, $attr, $this->resolveModelMessage('notInteger'));
		} else {
			if (($localized && !LocaleNumber::isNumber($value)) || (!$this->localized && !preg_match('/^-?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?$/', $value)))
				$this->addModelError($model, $attr, $this->resolveModelMessage('notNumber'));			
		}
		$value = ($localized ? LocaleNumber::getFloat($value) : floatval($value));
		if ($this->unsigned && $value < 0)
			$this->addModelError($model, $attr, $this->resolveModelMessage('notUnsigned'));
		if ($this->min !== null && $value < $this->min)
			$this->addModelError($model, $attr, $this->resolveModelMessage('tooSmall'), array('min' => $this->min));
		if ($this->max !== null && $value > $this->max)
			$this->addModelError($model, $attr, $this->resolveModelMessage('tooBig'), array('max' => $this->max));
	}
}