<?php

require_once PHP2GO_PATH . '/vendor/adodb/adodb.inc.php';
require_once PHP2GO_PATH . '/vendor/adodb/adodb-exceptions.inc.php';

Php2Go::import('php2go.db.commandBuilder.DbCommandBuilderADO');
Php2Go::import('php2go.db.statement.DbStatementADO');

class DbAdapterADO extends DbAdapter
{
	private static $langMap = array(
		'ar' => 'ar', 'bg' => 'bg', 'ca' => 'ca', 'zh' => 'cn', 'cs' => 'cz',
		'da' => 'da', 'de' => 'de', 'en' => 'en', 'es' => 'es', 'eo' => 'esperanto',
		'fa' => 'fa', 'fr' => 'fr', 'hu' => 'hu', 'it' => 'it', 'nl' => 'nl',
		'pl' => 'pl', 'pt' => 'pt-br', 'ro' => 'ro', 'ru' => 'ru1251', 'sv' => 'sv',
		'uk' => 'uk1251', 'th' => 'th'
	);
	private static $tables;
	protected $driver = null;

	public function __construct(array $options=array()) {
		global $ADODB_LANG;
		parent::__construct($options);
		$language = Php2Go::app()->getLocale()->getLanguage();
		$ADODB_LANG = (isset(self::$langMap[$language]) ? self::$langMap[$language] : 'en');
	}

	public function connect() {
		if (!$this->isConnected()) {
			try {
				if (isset($this->options['dsn'])) {
					$this->driver = ADONewConnection($this->options['dsn']);
				} else {
					$this->driver = ADONewConnection($this->options['type']);
					$connFunc = ($this->options['persistent'] ? 'pconnect' : 'connect');
					$this->driver->{$connFunc}($this->options['host'], $this->options['user'], $this->options['pass'], $this->options['base']);
				}
				if (isset($options['debug']) && $options['debug']) {
					define('ADODB_OUTP', 'adoLog');
					$this->driver->debug = 1;
				}
				$this->raiseEvent('onAfterConnect', new Event($this));
			} catch (ADODB_Exception $e) {
				throw new DbException($e->getMessage(), $e->getCode());
			}
			$this->driver->setFetchMode(ADODB_FETCH_ASSOC);
		}
	}

	protected function disconnect() {
		if ($this->driver->_connectionID !== false) {
			$this->raiseEvent('onBeforeClose', new Event($this));
			$this->driver->close();
		}
	}

	public function isConnected() {
		return ($this->driver && $this->driver->_connectionID !== false);
	}

	public function getDriver() {
		$this->connect();
		return $this->driver;
	}

	public function getDriverType() {
		$this->connect();
		return $this->driver->databaseType;
	}

	public function getTables() {
		$this->connect();
		if (!isset(self::$tables))
			self::$tables = $this->driver->metaTables();
		return self::$tables;
	}

	public function getColumns($table) {
		$i = 0;
		$p = 1;
		$this->connect();
		$result = array();
		$columns = $this->driver->metaColumns($table);
		foreach ($columns as $column) {
			$result[$column->name] = new DbColumn(array(
				'name' => $column->name,
				'position' => $i++,
				'type' => $column->type,
				'default' => (!!$column->has_default ? $column->default_value : null),
				'nullable' => !$column->not_null,
				'binary' => !!$column->binary,
				'unsigned' => !!$column->unsigned,
				'length' => intval($column->max_length),
				'scale' => (isset($column->scale) ? intval($column->scale) : null),
				'enums' => (isset($column->enums) ? $column->enums : null),
				'primary' => !!$column->primary_key,
				'primaryPosition' => (!!$column->primary_key ? $p++ : null),
				'identity' => !!$column->auto_increment
			));
		}
		return $result;
	}

	public function getIndexes($table) {
		$this->connect();
		try {
			$result = array();
			$indexes = $this->driver->metaIndexes($table);
			foreach ($indexes as $index)
				$result[] = new DbIndex($index['columns'], $index['unique']);
			return $result;
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function getPrimaryKeys($table) {
		$this->connect();
		try {
			$pk = $this->driver->metaPrimaryKeys($table);
			if (sizeof($pk) == 1)
				return $pk[0];
			return $pk;
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function getForeignKeys($table) {
		$this->connect();
		try {
			$result = array();
			$foreignKeys = $this->driver->metaForeignKeys($table);
			if (is_array($foreignKeys)) {
				foreach ($foreignKeys as $foreignTable => $columns) {
					foreach ($columns as $foreignColumn => $column) {
						if (!is_string($foreignColumn))
							list($column, $foreignColumn) = explode('=', $column);
						$result[] = new DbForeignKey($column, $foreignTable, $foreignColumn);
					}
				}
			}
			return $result;
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function getIdMethod() {
		return ($this->driver->hasInsertID ? DbAdapter::ID_METHOD_AUTOINCREMENT : DbAdapter::ID_METHOD_SEQUENCE);
	}

	public function getCommandBuilder() {
		if (!$this->builder)
			$this->builder = new DbCommandBuilderADO($this);
		return $this->builder;
	}

	public function beginTransaction() {
		$this->connect();
		try {
			$this->driver->startTrans();
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function commit() {
		$this->connect();
		try {
			$this->driver->completeTrans();
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function rollback() {
		$this->connect();
		try {
			$this->driver->rollbackTrans();
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function prepare($query) {
		$this->connect();
		if ($this->stmt)
			$this->stmt->close();
		$this->stmt = new DbStatementADO($this, $query);
		$this->stmt->setFetchMode($this->fetchMode);
		return $this->stmt;
	}

	public function execute($query, array $bind=array()) {
		$this->connect();
		$this->stmt = $this->prepare($query);
		try {
			$this->stmt->execute($bind);
			$this->stmt->setFetchMode($this->fetchMode);
			return $this->stmt;
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function limit($query, $limit=null, $offset=null, array $bind=array()) {
		if ($limit === null || $limit < 0)
			return $this->fetchAll($query, $bind);
		$this->connect();
		try {
			if ($this->stmt)
				$this->stmt->close();
			if ($offset === null || $offset < 0)
				$offset = 0;
			$result = array();
			$rs = $this->driver->selectLimit($query, $limit, $offset, $bind);
			while (!$rs->EOF) {
				$result[] = $rs->fields;
				$rs->moveNext();
			}
			$rs->close();
			return $result;
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function limitAssoc($query, $limit=null, $offset=null, array $bind=array(), $col=null) {
		if ($limit === null || $limit < 0)
			return $this->fetchAll($query, $bind);
		$this->connect();
		try {
			if ($this->stmt)
				$this->stmt->close();
			if ($offset === null || $offset < 0)
				$offset = 0;
			$result = array();
			$rs = $this->driver->selectLimit($query, $limit, $offset, $bind);
			while (!$rs->EOF) {
				if ($col !== null && isset($rs->fields[$col]))
					$result[Util::consumeArray($rs->fields, $col)] = $rs->fields;
				else
					$result[reset($rs->fields)] = array_slice($rs->fields, 0, 1);
				$rs->moveNext();
			}
			$rs->close();
			return $result;
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function affectedRows() {
		return ($this->stmt ? $this->stmt->affectedRows() : 0);
	}

	public function getId($tableOrSeq, $column=null) {
		$this->connect();
		try {
			if ($this->getIdMethod() == DbAdapter::ID_METHOD_AUTOINCREMENT)
				return $this->driver->insert_ID($tableOrSeq, $column);
			else
				return $this->driver->genID($tableOrSeq);
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function quote($value) {
		$this->connect();
		return $this->driver->qstr($value, false);
	}

	public function quoteIdentifier($identifier) {
		$this->connect();
		return "{$this->driver->nameQuote}{$identifier}{$this->driver->nameQuote}";
	}

	public function date($date, $quoted=false) {
		return ($quoted ? $this->driver->dbDate($date) : $this->driver->bindDate($date));
	}

	public function dateTime($dateTime, $quoted=false) {
		return ($quoted ? $this->driver->dbTimeStamp($dateTime) : $this->driver->bindTimeStamp($dateTime));
	}

	public function concat(array $args) {
		return call_user_func_array(array($this->driver, 'concat'), $args);
	}
}

function adoLog($msg) {
	$msg = trim(preg_replace('/&nbsp;\s*$/', '', strip_tags($msg)));
	Php2Go::app()->getLogger()->debug($msg, 'DbAdapterADO');
}