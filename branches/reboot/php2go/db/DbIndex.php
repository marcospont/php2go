<?php

class DbIndex
{
	public $columns;
	public $unique;	
	
	public function __construct(array $columns, $unique) {
		$this->columns = $columns;
		$this->unique = $unique;
	}
}