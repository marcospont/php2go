<?php

class PaginatorAdapterActiveRecord extends PaginatorAdapter
{
	private $model;
	private $criteria = null;
	private $bind = array();	
	
	public function __construct($model, array $options=array()) {
		$this->model = $model;
		$this->criteria = Util::consumeArray($options, 'criteria', null);
		$this->bind = Util::consumeArray($options, 'bind', array());
	}
	
	public function count() {
		return $this->model->count($this->criteria, $this->bind);		
	}
	
	public function getItems($offset, $pageSize) {
		$this->criteria['limit'] = $pageSize;
		$this->criteria['offset'] = $offset;
		return $this->model->findAll($this->criteria, $this->bind);
	}
}