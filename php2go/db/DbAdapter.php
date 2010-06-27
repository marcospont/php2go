<?php

abstract class DbAdapter extends Component
{
	const ID_METHOD_SEQUENCE = 'sequence';
	const ID_METHOD_AUTOINCREMENT = 'autoIncrement';

	protected static $meta = array();
	protected $options = array();
	protected $fetchMode = Db::FETCH_ASSOC;
	protected $builder;
	protected $stmt;

	public function __construct(array $options=array()) {
		$this->options = $options;
		$this->registerEvents(array('onAfterConnect', 'onBeforeClose'));
		register_shutdown_function(array($this, 'close'));
	}

	abstract protected function connect();

	abstract protected function disconnect();

	abstract public function isConnected();

	abstract public function getDriver();

	abstract public function getDriverType();

	public function close() {
		if ($this->isConnected()) {
			if ($this->stmt)
				$this->stmt->close();
			$this->disconnect();
		}
	}

	abstract public function getTables();

	public function getMetaData($table) {
		if (!isset(self::$meta[$table]))
			self::$meta[$table] = new DbTable($table);
		return self::$meta[$table];
	}

	abstract public function getColumns($table);

	abstract public function getIndexes($table);

	abstract public function getPrimaryKeys($table);

	abstract public function getForeignKeys($table);

	abstract public function getIdMethod();

	abstract public function getCommandBuilder();

	public function getFetchMode() {
		return $this->fetchMode;
	}

	public function setFetchMode($fetchMode) {
		$current = $this->fetchMode;
		switch ($fetchMode) {
			case Db::FETCH_NUM :
			case Db::FETCH_ASSOC :
			case Db::FETCH_BOTH :
				$this->fetchMode = $fetchMode;
				break;
			default :
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid fetch mode: "%s".', array($fetchMode)));
		}
		return $current;
	}

	abstract public function beginTransaction();

	abstract public function commit();

	abstract public function rollback();

	abstract public function prepare($query);

	abstract public function execute($query, array $bind=array());

	public function fetchAll($query, array $bind=array()) {
		$this->connect();
		$stmt = $this->execute($query, $bind);
		return $stmt->fetchAll();
	}

	public function fetchAssoc($query, array $bind=array(), $col=null) {
		$this->connect();
		$result = array();
		$stmt = $this->execute($query, $bind);
		while ($row = $stmt->fetch()) {
			if ($col !== null && isset($row[$col]))
				$result[Util::consumeArray($row, $col)] = $row;
			else
				$result[reset($row)] = array_slice($row, 0, 1);
		}
		return $result;
	}

	public function fetchCell($query, array $bind=array(), $col=null) {
		$this->connect();
		$stmt = $this->execute($query, $bind);
		if ($row = $stmt->fetch())
			return ($col !== null && isset($row[$col]) ? $row[$col] : reset($row));
		return false;
	}

	public function fetchCol($query, array $bind=array(), $col=null) {
		$this->connect();
		$result = array();
		$stmt = $this->execute($query, $bind);
		while ($row = $stmt->fetch()) {
			if ($col !== null && isset($row[$col]))
				$result[] = $row[$col];
			else
				$result[] = reset($row);
		}
		return $result;
	}

	public function fetchRow($query, array $bind=array()) {
		$this->connect();
		$stmt = $this->execute($query, $bind);
		return $stmt->fetch();
	}

	public function fetchPairs($query, array $bind=array()) {
		$this->connect();
		$result = array();
		$stmt = $this->execute($query, $bind);
		while ($row = $stmt->fetch()) {
			$row = array_values($row);
			if (!isset($row[1]))
				$row[1] = $row[0];
			$result[$row[0]] = $row[1];
		}
		return $result;
	}

	abstract public function limit($query, $limit=null, $offset=null, array $bind=array());

	abstract public function limitAssoc($query, $limit=null, $offset=null, array $bind=array(), $col=null);

	abstract public function getId($tableOrSeq, $column=null);

	abstract public function quote($value);

	abstract public function quoteIdentifier($identifier);

	abstract public function date($date, $quoted=false);

	abstract public function dateTime($dateTime, $quoted=false);

	abstract public function concat(array $args);
}