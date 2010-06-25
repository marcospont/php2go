<?php

Php2Go::import('php2go.db.statement.DbStatementColumn');

abstract class DbStatement
{
	protected $adapter;
	protected $stmt;
	protected $fetchMode = Db::FETCH_ASSOC;

	public function __construct(DbAdapter $adapter, $query) {
		$this->adapter = $adapter;
		$this->stmt = $this->prepare($query);
	}

	public function getAdapter() {
		return $this->adapter;
	}

	public function getDriver() {
		return $this->adapter->getDriver();
	}

	abstract public function getFetchMode();

	abstract public function setFetchMode($fetchMode);

	abstract public function getMetaData($col=null);

	abstract public function execute(array $bind=array());

	abstract public function fetch();

	public function fetchCol($col) {
		$row = $this->fetch();
		if (is_array($row))
			return @$row[$col];
		return false;
	}

	public function fetchInto(&$result) {
		$row = $this->fetch();
		if (is_array($row)) {
			$result = $row;
			return true;
		}
		return false;
	}

	public function fetchObject($class='stdClass', array $args=array()) {
		$row = $this->fetch();
		if (is_array($row)) {
			array_unshift($args, $class);
			$obj = Php2Go::newInstanceArgs($args);
			foreach ($row as $col => $value)
				$obj->{$col} = $value;
			return $obj;
		}
		return false;
	}

	public function fetchAll() {
		$result = array();
		while ($row = $this->fetch())
			$result[] = $row;
		return $result;
	}

	abstract public function close();

	abstract public function affectedRows();

	abstract public function columnCount();

	abstract public function rowCount();

	abstract public function nextRowset();

	abstract protected function prepare($query);

}