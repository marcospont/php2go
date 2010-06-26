<?php

class ValidatorCnpj extends Validator
{
	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, '"{value}" is not a valid CNPJ.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid CNPJ.');
	}

	public function validate($value) {
		if (!$this->isCnpj($value)) {
			$this->setError($this->resolveMessage(), array('value' => $value));
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if (!$this->isCnpj($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}

	protected function isCnpj($value) {
		if (!preg_match('/^([0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}|[0-9]{14})$/', $value))
			return false;
		$value = preg_replace('/[^0-9]+/', '', $value);
		$w1 = array(5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
		$w2 = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);
		$sum1 = $sum2 = array();
		for ($i=0; $i<13; $i++) {
			if ($i < 12)
				$sum1[] = $value[$i] * $w1[$i];
			$sum2[] = $value[$i] * $w2[$i];
		}
		$sum1 = array_sum($sum1) % 11;
		$sum2 = array_sum($sum2) % 11;
		if (($sum1 < 2 ? 0 : 11 - $sum1) != $value[12])
			return false;
		return (($sum2 < 2 ? 0 : 11 - $sum2) == $value[13]);
	}
}