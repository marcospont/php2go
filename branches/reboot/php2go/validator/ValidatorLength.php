<?php

class ValidatorLength extends Validator
{
	protected $length;
	protected $min;
	protected $max;
	protected $encoding = false;
	
	public function __construct() {
		$this->defaultMessages = array(
			'wrongLength' => __(PHP2GO_LANG_DOMAIN, 'Value must have {length} characters.'),
			'tooShort' => __(PHP2GO_LANG_DOMAIN, 'Value must have at least {min} characters.'),
			'tooLong' => __(PHP2GO_LANG_DOMAIN, 'Value must have until {max} characters.')
		);
		$this->defaultModelMessages = array(
			'wrongLength' => __(PHP2GO_LANG_DOMAIN, '{attribute} must have {length} characters.'),
			'tooShort' => __(PHP2GO_LANG_DOMAIN, '{attribute} must have at least {min} characters.'),
			'tooLong' => __(PHP2GO_LANG_DOMAIN, '{attribute} must have until {max} characters.')
		);		
	}
	
	protected function validateOptions() {
		if ($this->length === null && $this->min === null && $this->max === null)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}
	
	public function validate($value) {
		if ($this->encoding !== false && function_exists('mb_strlen'))
			$length = mb_strlen($value);
		else
			$length = strlen($value);
		if ($this->min !== null && $length < $this->min) {
			$this->setError($this->resolveMessage('tooShort'), array('min' => $this->min));
			return false;
		}
		if ($this->max !== null && $length > $this->max) {
			$this->setError($this->resolveMessage('tooLong'), array('max' => $this->max));
			return false;
		}
		if ($this->length !== null && $length !== $this->length) {
			$this->setError($this->resolveMessage('wrongLength'), array('length' => $this->length));
			return false;
		}
		return true;
	}
	
	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if ($this->encoding !== false && function_exists('mb_strlen'))
			$length = mb_strlen($value);
		else
			$length = strlen($value);
		if ($this->min !== null && $length < $this->min)
			$this->addModelError($model, $attr, $this->resolveModelMessage('tooShort'), array('min' => $this->min));
		elseif ($this->max !== null && $length > $this->max)
			$this->addModelError($model, $attr, $this->resolveModelMessage('tooLong'), array('max' => $this->max));
		elseif ($this->length !== null && $length !== $this->length)
			$this->addModelError($model, $attr, $this->resolveModelMessage('wrongLength'), array('length' => $this->length));
	}	
}