<?php

class ViewHelperCycle extends ViewHelper
{
	const DEFAULT_KEY = 'default';

	protected $key = self::DEFAULT_KEY;
	protected $data = array(self::DEFAULT_KEY => array());
	protected $pointers = array(self::DEFAULT_KEY => -1);

	public function cycle(array $data=array(), $key=self::DEFAULT_KEY) {
		if (!empty($data))
			$this->data[$key] = array_values($data);
		$this->setKey($key);
		return $this;
	}

	public function getKey() {
		return $this->key;
	}

	public function setKey($key) {
		$this->key = $key;
		if (!isset($this->data[$key]))
			$this->data[$key] = array();
		if (!isset($this->pointers[$key]))
			$this->pointers[$key] = -1;
		return $this;
	}

	public function getData() {
		return $this->data[$this->key];
	}

	public function setData(array $data, $key=self::DEFAULT_KEY) {
		$this->setKey($key);
		$this->data[$key] = array_values($data);
		$this->pointers[$key] = -1;
		return $this;
	}

	public function toString() {
		return (string)$this->data[$this->key][$this->key()];
	}

	public function next() {
		$count = count($this->data[$this->key]);
		if ($this->pointers[$this->key] == ($count - 1))
			$this->pointers[$this->key] = 0;
		else
			++$this->pointers[$this->key];
		return $this;
	}

	public function prev() {
		$count = count($this->data[$this->key]);
		if ($this->pointers[$this->key] <= 0)
			$this->pointers[$this->key] = ($count - 1);
		else
			--$this->pointers[$this->key];
		return $this;
	}

	protected function key() {
		if ($this->pointers[$this->key] < 0)
			return 0;
		return $this->pointers[$this->key];
	}
}