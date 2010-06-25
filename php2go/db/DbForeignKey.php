<?php

class DbForeignKey
{
	public $column;
	public $foreignTable;
	public $foreignColumn;
	
	public function __construct($column, $foreignTable, $foreignColumn) {
		$this->column = $column;
		$this->foreignTable = $foreignTable;
		$this->foreignColumn = $foreignColumn;
	}
}