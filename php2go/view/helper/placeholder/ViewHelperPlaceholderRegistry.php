<?php

class ViewHelperPlaceholderRegistry
{
	private static $inst;
	protected $containers = array();

	public static function instance() {
		if (self::$inst === null)
			self::$inst = new ViewHelperPlaceholderRegistry();
		return self::$inst;
	}

	public function create($id) {
		$this->containers[$id] = new ViewHelperPlaceholderContainer();
		return $this->containers[$id];
	}

	public function get($id) {
		if (isset($this->containers[$id]))
			return $this->containers[$id];
		return $this->create($id);
	}
}