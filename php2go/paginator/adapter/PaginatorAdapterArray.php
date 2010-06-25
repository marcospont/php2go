<?php

class PaginatorAdapterArray extends PaginatorAdapter 
{
	private $array;
	private $count;
	
	public function __construct(array $array, array $options=array()) {
		$this->array = $array;
		$this->count = sizeof($array);
	}
	
	public function count() {
		return $this->count;
	}
	
	public function getItems($offset, $pageSize) {
		return array_slice($this->array, $offset, $pageSize);
	}
}