<?php

class Cookie
{
	private $name;
	private $value = '';
	private $duration = 0;
	private $expire = 0;
	private $path = '/';
	private $domain = '';
	private $secure = false;
	private $httpOnly = false;

	public function __construct($name, $value, $duration=0, $path='/', $domain='', $secure=false, $httpOnly=false) {
		$this->name = $name;
		$this->value = $value;
		$this->duration = $duration;
		$this->expire = microtime(true) + $duration;
		$this->path = $path;
		$this->domain = $domain;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
	}

	public function __get($name) {
		if (property_exists($this, $name))
			return $this->{$name};
		return null;
	}

	public function __set($name, $value) {
		if ($name == 'duration') {
			$this->duration = $value;
			$this->expire = microtime(true) + $value;
		} else {
			$this->{$name} = $value;
		}
	}

	public function __toString() {
		return $this->value;
	}

	public function add() {
		Php2Go::app()->getResponse()->addCookie($this);
	}

	public function remove() {
		Php2Go::app()->getResponse()->removeCookie($this);
	}
}