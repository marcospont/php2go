<?php

class CacheProxy extends Component
{
	protected $cache;

	public function __construct(array $options=array()) {
		$this->loadOptions($options);
	}

	public function loadOptions(array $options) {
		if (!empty($options)) {
			$this->cache = new Cache();
			foreach ($options as $key => $value)
				$this->cache->{$key} = $value;
			$this->onCreateCache();
		}
	}

	public function __get($name) {
		return $this->getCache()->{$name};
	}

	public function __set($name, $value) {
		return $this->getCache()->{$name} = $value;
	}

	public function __isset($name) {
		return (isset($this->getCache()->{$name}));
	}

	public function __unset($name) {
		unset($this->getCache()->{$name});
	}

	public function __call($name, $args) {
		return call_user_func_array(array($this->getCache(), $name), $args);
	}

	protected function getCache() {
		if (!$this->cache) {
			$this->cache = clone Php2Go::app()->getCache();
			$this->onCreateCache();
		}
		return $this->cache;
	}

	protected function onCreateCache() {
	}
}