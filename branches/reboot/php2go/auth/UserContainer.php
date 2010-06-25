<?php

class UserContainer extends Component
{
	protected $name;
	protected $properties = array();

	public function __construct($name, $properties=array()) {
		$this->name = $name;
		$this->properties = $properties;
	}

	public function __isset($name) {
		if (array_key_exists($name, $this->properties))
			return true;
		return parent::__isset($name);
	}

	public function __get($name) {
		if (array_key_exists($name, $this->properties))
			return $this->properties[$name];
		return parent::__get($name);
	}

	public function __set($name, $value) {
		$this->properties[$name] = $value;
	}

	public function __unset($name) {
		if (array_key_exists($name, $this->properties))
			unset($this->properties[$name]);
		else
			parent::__unset($name);
	}

	public function getName() {
		return $this->name;
	}

	public function getElapsedTime() {
		$loginTime = $this->loginTime;
		if ($loginTime)
			return (microtime(true) - $loginTime);
		return 0;
	}

	public function toArray() {
		return array(
			'name' => $this->name,
			'role' => $this->role,
			'properties' => $this->properties
		);
	}
}