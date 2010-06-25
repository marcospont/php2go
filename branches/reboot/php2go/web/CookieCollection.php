<?php

class CookieCollection extends HashMap
{
	public function __construct() {
		parent::__construct($this->getCookies(), true);
	}

	private function getCookies() {
		$cookies = array();
		foreach ($_COOKIE as $name => $value)
			$cookies[$name] = new Cookie($name, $value);
		return $cookies;
	}
}