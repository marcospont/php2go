<?php

class HashMap implements ArrayAccess, IteratorAggregate, Countable
{
	private $data = array();
	private $readOnly = false;

	public function __construct(array $data=array(), $readOnly=false) {
		$this->copyFrom($data);
		$this->readOnly = (bool)$readOnly;
	}

	public function __isset($name) {
		return $this->contains($name);
	}

	public function __get($name) {
		return $this->get($name);
	}

	public function __set($name, $value) {
		$this->set($name, $value);
	}

	public function __unset($name) {
		$this->remove($name);
	}

	public function __clone() {
		$data = array();
		foreach ($this->data as $key => $value) {
			if ($value instanceof self)
				$data[$key] = clone $this->data[$key];
			else
				$data[$key] = $this->data[$key];
		}
		$this->data = $data;
	}

	public function getReadOnly() {
		return $this->readOnly;
	}

	public function setReadOnly($readOnly) {
		$this->readOnly = (bool)$readOnly;
		return $this;
	}

	public function keys() {
		return array_keys($this->data);
	}

	public function contains($name) {
		return (array_key_exists($name, $this->data));
	}

	public function get($name, $fallback=null) {
		return (array_key_exists($name, $this->data) ? $this->data[$name] : $fallback);
	}

	public function set($name, $value) {
		if ($this->readOnly)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'This %s instance is read-only.', array(get_class($this))));
		if (is_array($value))
			$value = new self($value);
		$this->data[$name] = $value;
		return $this;
	}

	public function remove($name) {
		if ($this->readOnly)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'This %s instance is read-only.', array(get_class($this))));
		unset($this->data[$name]);
		return $this;
	}

	public function getIterator() {
		return new ArrayIterator($this->data);
	}

	public function count() {
		return count($this->data);
	}

	public function clear() {
		foreach (array_keys($this->data) as $name)
			$this->remove($name);
		return $this;
	}

	public function mergeWith($data) {
		if (!is_array($data) && !$data instanceof self)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Merge data must be a HashMap or an array.'));
		foreach ($data as $key => $value) {
			if (array_key_exists($key, $this->data)) {
				if ($value instanceof self && $this->data[$key] instanceof self)
					$this->data[$key] = $this->data[$key]->merge(new self($value->toArray()));
				else
					$this->data[$key] = $value;
			} else {
				if ($value instanceof self)
					$this->data[$key] = new self($value->toArray());
				else
					$this->data[$key] = $value;
			}
		}
		return $this;
	}

	public function copyFrom(array $data) {
		foreach ($data as $key => $value)
			$this->set($key, $value);
		return $this;
	}

	public function toArray() {
		$data = array();
		foreach ($this->data as $key => $value) {
			if ($value instanceof self)
				$data[$key] = $value->toArray();
			else
				$data[$key] = $value;
		}
		return $data;
	}

	public function offsetExists($name) {
		return $this->contains($name);
	}

	public function offsetGet($name) {
		return $this->get($name);
	}

	public function offsetSet($name, $value) {
		$this->set($name, $value);
	}

	public function offsetUnset($name) {
		$this->remove($name);
	}
}