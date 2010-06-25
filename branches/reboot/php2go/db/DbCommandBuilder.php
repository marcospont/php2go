<?php

abstract class DbCommandBuilder
{
	protected $adapter = null;
	
	public function __construct(DbAdapter $adapter) {
		$this->adapter = $adapter;
	}
	
	abstract public function buildFind($table, $criteria=null);
	
	abstract public function buildCount($table, $criteria=null);
	
	abstract public function buildInsert($table, array $values);
	
	abstract public function buildUpdate($table, array $values, $condition=null, array $bind=array());

	abstract public function buildDelete($table, $condition=null);
}