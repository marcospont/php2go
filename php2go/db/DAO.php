<?php

final class DAO
{
	private static $inst = null;
	private $adapter = null;

	public static function instance() {
		if (!self::$inst)
			self::$inst = new DAO();
		return self::$inst;
	}

	protected function __construct() {
		$this->adapter = Db::instance();
	}

 	public function getAdapter() {
 		return $this->adapter;
 	}

 	public function getCommandBuilder() {
 		return $this->adapter->getCommandBuilder();
 	}

 	public function findAll($table, $criteria=null, array $bind=array()) {
 		$criteria = $this->normalizeCriteria($criteria);
		$indexBy = Util::consumeArray($criteria, 'indexBy', null);
		$query = $this->getCommandBuilder()->buildFind($table, $criteria);
		if (isset($criteria['limit'])) {
			if ($indexBy)
				return $this->adapter->limitAssoc($query, $criteria['limit'], @$criteria['offset'], $bind, $indexBy);
			else
				return $this->adapter->limit($query, $criteria['limit'], @$criteria['offset'], $bind);
		} else {
			if ($indexBy)
				return $this->adapter->fetchAssoc($query, $bind, $indexBy);
			else
				return $this->adapter->fetchAll($query, $bind);
		}
 	}

	public function find($table, $criteria=null, array $bind=array(), $all=false) {
		$criteria = $this->normalizeCriteria($criteria);
		$query = $this->getCommandBuilder()->buildFind($table, $criteria);
		return $this->adapter->fetchRow($query, $bind);
	}

	public function findPairs($table, $display, $criteria=null, array $bind=array()) {
		$adapter = $this->getAdapter();
		$key = $adapter->getMetaData($table)->primaryKey;
		if (is_array($key)) {
			$keys = $key;
			$key = array();
			for ($i=0,$l=sizeof($pk); $i<$l; $i++) {
				$key[] = $pk[$i];
				if ($i < ($l-1))
					$key[] = $adapter->quote('-');
			}
			$key = $adapter->concat($key);
		}
		if (is_array($display)) {
			$cols = $display;
			$display = array();
			for ($i=0,$l=sizeof($cols); $i<$l; $i++) {
				$display[] = $cols[$i];
				if ($i < ($l-1))
					$display[] = $adapter->quote(' - ');
			}
			$display = $adapter->concat($display);
		}
		$criteria = $this->normalizeCriteria($criteria);
		$criteria['fields'] = array($key, $display);
		$query = $this->getCommandBuilder()->buildFind($table, $criteria);
		return $adapter->fetchPairs($query, $bind);
	}

	public function count($table, $criteria=null, array $bind=array()) {
		$query = $this->getCommandBuilder()->buildCount($table, $criteria);
		return intval($this->adapter->fetchCell($query, $bind));
	}

	public function exists($table, $criteria=null, array $bind=array()) {
		return ($this->count($table, $criteria, $bind) > 0);
	}

	public function insert($table, array $values) {
		if (!empty($values)) {
			$query = $this->getCommandBuilder()->buildInsert($table, $values);
			return $this->adapter->execute($query);
		}
		return false;
	}

	public function update($table, array $values, $condition, array $bind=array()) {
		if (!empty($values) && !empty($condition)) {
			$query = $this->getCommandBuilder()->buildUpdate($table, $values, $condition, $bind);
			if (!empty($query)) {
				$this->adapter->execute($query);
				return $this->adapter->affectedRows();
			} else {
				return true;
			}
		}
		return false;
	}

	public function updateAll($table, array $values) {
		if (!empty($values)) {
			$query = $this->getCommandBuilder()->buildUpdate($table, $values);
			$this->adapter->execute($query);
			return $this->adapter->affectedRows();
		}
		return false;
	}

	public function delete($table, $condition, array $bind=array()) {
		if (!empty($condition)) {
			$query = $this->getCommandBuilder()->buildDelete($table, $condition);
			$this->adapter->execute($query, $bind);
			return $this->adapter->affectedRows();
		}
		return false;
	}

	public function deleteAll($table) {
		$query = $this->getCommandBuilder()->buildDelete($table);
		$this->adapter->execute($query);
		return $this->adapter->affectedRows();
	}

	private function normalizeCriteria($criteria=null) {
		if (is_string($criteria))
			$criteria = array('condition' => $criteria);
		elseif (!is_array($criteria))
			$criteria = array();
		return $criteria;
	}
}