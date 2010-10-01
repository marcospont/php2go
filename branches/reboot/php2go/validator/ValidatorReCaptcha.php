<?php

require_once PHP2GO_PATH . '/vendor/recaptcha/recaptchalib.php';

class ValidatorReCaptcha extends Validator
{
	protected $privateKey;

	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, 'The verification code is incorrect.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, 'The verification code is incorrect.');
	}

	public function loadOptions(array $options) {
		if (($privateKey = Php2Go::app()->getOption('reCaptcha.privateKey')) !== null)
			$options['privateKey'] = $privateKey;
		parent::loadOptions($options);
	}

	protected function validateOptions() {
		if (empty($this->privateKey) || !is_string($this->privateKey))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));
	}

	public function validate($value) {
		return false;
	}

	public function validateModelAttribute(Model $model, $attr) {
		$request = Php2Go::app()->getRequest();
		$response = recaptcha_check_answer($this->privateKey, $request->getUserAddress(), $request->getPost('recaptcha_challenge_field'), $request->getPost('recaptcha_response_field'));
		if (!$response->is_valid)
			$this->addModelError($model, $attr, $this->resolveMessage());
	}
}