<?php

class ValidatorUrl extends Validator
{
	// http://regexlib.com/REDetails.aspx?regexp_id=146
	private static $pattern = '/^(https?|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(:[a-zA-Z0-9]*)?\/?([a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$/i';
	
	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, '"{value}" is not a valid URL.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} is not a valid URL.');
	}
	
	public function validate($value) {
		if (!$this->isUrl($value)) {
			$this->setError($this->resolveMessage(), array('value' => $value));
			return false;
		}
		return true;
	}	
	
	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		if (!$this->isUrl($value))
			$this->addModelError($model, $attr, $this->resolveModelMessage());
	}
	
	protected function isUrl($url) {
		return (is_string($url) && preg_match(self::$pattern, $url));
	}
}