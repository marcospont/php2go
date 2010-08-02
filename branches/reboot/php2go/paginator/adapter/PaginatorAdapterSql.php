<?php

class PaginatorAdapterSql extends PaginatorAdapter
{
	private $adapter;
	private $query;
	private $bind = array();
	private $count;

	public function __construct($query, array $options=array()) {
		$this->adapter = Db::instance();
		$this->query = $query;
		$this->bind = Util::consumeArray($options, 'bind', array());
	}

	public function count() {
		if ($this->count === null)
			$this->count = $this->adapter->count($this->query, $this->bind);
		return $this->count;
	}

	public function getItems($offset, $pageSize) {
		return $this->adapter->limit($this->query, $pageSize, $offset, $this->bind);
	}
}