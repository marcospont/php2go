<?php

class PasswordStrength extends Widget
{
	protected $password;

	public function setBadClass($badClass) {
		$this->params['css']['bad'] = $badClass;
	}

	public function setBadLimit($badLimit) {
		$this->params['badLimit'] = (int)$badLimit;
	}

	public function setBadText($badText) {
		$this->params['badPass'] = $badText;
	}

	public function setCallback($callback) {
		$this->params['strengthCallback'] = Js::callback($callback, array('score', 'strength'));
	}

	public function setClass($class) {
		$this->params['baseStyle'] = $class;
	}

	public function setGoodClass($goodClass) {
		$this->params['css']['good'] = $goodClass;
	}

	public function setGoodLimit($goodLimit) {
		$this->params['goodLimit'] = (int)$goodLimit;
	}

	public function setGoodText($goodText) {
		$this->params['goodPass'] = $goodText;
	}

	public function setShortLimit($shortLimit) {
		$this->params['shortLimit'] = (int)$shortLimit;
	}

	public function setShortText($shortText) {
		$this->params['shortPass'] = $shortText;
	}

	public function setStrongClass($strongClass) {
		$this->params['css']['strong'] = $strongClass;
	}

	public function setStrongText($strongText) {
		$this->params['strongPass'] = $strongText;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function setUserId($userId) {
		$this->params['userid'] = '#' . ltrim($userId, '#');
	}

	public function preInit() {
		parent::preInit();
		$this->view->head()->addLibrary('jquery-passStrengthener');
	}

	public function init() {
		if (!isset($this->password))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, '"%s" widget requires the "password" option.', array(get_class($this))));
	}

	public function run() {
		$this->view->jQuery()->addCallById($this->password,
			'passStrengthener', array((!empty($this->params) ? $this->params : Js::emptyObject()))
		);
	}

	protected function getDefaultParams() {
		return array(
			'shortPass' => __(PHP2GO_LANG_DOMAIN, 'Too short'),
			'badPass' => __(PHP2GO_LANG_DOMAIN, 'Weak'),
			'goodPass' => __(PHP2GO_LANG_DOMAIN, 'Good'),
			'strongPass' => __(PHP2GO_LANG_DOMAIN, 'Strong'),
			'samePass' => __(PHP2GO_LANG_DOMAIN, 'Username and password are identical')
		);
	}
}