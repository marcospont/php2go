<?php

class ValidatorEmail extends Validator
{
	private static $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
	private static $fullPattern = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/';	
	protected $allowName = false;
	protected $checkMX = false;
	protected $checkHost = false;
	
	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, '"{value}" is not a valid email address.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid email address.');		
	}
	
	public function validate($value) {
		if (!$this->isEmail($value)) {
			$this->setError($this->resolveMessage(), array('value' => $value));
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if (!$this->isEmail($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}
	
	protected function isEmail($email) {
		if (is_string($email)) {
			if (preg_match(self::$pattern, $email) || ($this->allowName && preg_match(self::$fullPattern, $email))) {
				$valid = true;
				$domain = rtrim(substr($email, strpos($email,'@')+1), '>');
				if ($this->checkMX && function_exists('checkdnsrr'))
					$valid = checkdnsrr($domain, 'MX');
				if ($valid && $this->checkHost)
					$valid = (@fsockopen($domain, 25) !== false);
				return $valid;
			}
		}
		return false;		
	}
}