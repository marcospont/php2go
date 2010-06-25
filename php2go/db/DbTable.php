<?php

class DbTable extends Component
{
	private $name;
	private $primaryKey;
	private $columns;	
	private $indexes;
	private $foreignKeys;
	
	public function __construct($name) {
		if (!in_array($name, Db::instance()->getTables()))
			throw new DbException(__(PHP2GO_LANG_DOMAIN, 'The table "%s" was not found in the database.', array($name)));
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getColumns() {
		if ($this->columns === null)
			$this->columns = Db::instance()->getColumns($this->name);
		return $this->columns;
	}
	
	public function getColumnNames() {
		return array_keys($this->getColumns());
	}
	
	public function hasColumn($column) {
		return (array_key_exists($column, $this->getColumns()));
	}
	
	public function getPrimaryKey() {
		if ($this->primaryKey === null)
			$this->primaryKey = $this->findPrimaryKey();
		return $this->primaryKey;
	}
	
	public function isPrimaryKey($column) {
		return ($this->hasColumn($column) && $this->columns[$column]->primary);
	}
	
	public function getIndexes() {
		if ($this->indexes === null)
			$this->indexes = Db::instance()->getIndexes($this->name);
		return $this->indexes;
	}
	
	public function getForeignKeys() {
		if ($this->foreignKeys === null)
			$this->foreignKeys = Db::instance()->getForeignKeys($this->name);
		return $this->foreignKeys;
	}
	
	private function findPrimaryKey() {
		$pk = array();
		foreach ($this->getColumns() as $column) {
			if ($column->primary)
				$pk[] = $column->name;
		}
		return (sizeof($pk) == 1 ? $pk[0] : $pk);
	}
}