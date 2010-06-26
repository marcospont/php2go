<?php

class ValidatorCpf extends Validator
{
	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, '"{value}" is not a valid CPF.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid CPF.');
	}

	public function validate($value) {
		if (!$this->isCpf($value)) {
			$this->setError($this->resolveMessage(), array('value' => $value));
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if (!$this->isCpf($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}

	protected function isCpf($value) {
		if (!preg_match('/^([0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}|[0-9]{11})$/', $value))
			return false;
		$value = preg_replace('/[^0-9]+/', '', $value);
		if ($value == str_repeat($value[0], 11))
			return false;
		$sum1 = $sum2 = array();
		for ($i=0; $i<10; $i++) {
			if ($i < 9)
				$sum1[] = $value[$i] * (10 - $i);
			$sum2[] = $value[$i] * (11 - $i);
		}
		$sum1 = array_sum($sum1) % 11;
		$sum2 = array_sum($sum2) % 11;
		if (($sum1 < 2 ? 0 : 11 - $sum1) != $value[9])
			return false;
		return (($sum2 < 2 ? 0 : 11 - $sum2) == $value[10]);
	}
}