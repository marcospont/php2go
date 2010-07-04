<?php

class PaginatorAdapterActiveRecord extends PaginatorAdapter
{
	private $model;
	private $criteria = null;
	private $bind = array();
	private $count;

	public function __construct(ActiveRecord $model, array $options=array()) {
		$this->model = $model;
		$this->criteria = Util::consumeArray($options, 'criteria', null);
		if (is_string($this->criteria)) {
			$this->criteria = array(
				'condition' => $this->criteria
			);
		}
		$this->bind = Util::consumeArray($options, 'bind', array());
	}

	public function count() {
		if ($this->count === null)
			$this->count = $this->model->count($this->criteria, $this->bind);
		return $this->count;
	}

	public function getItems($offset, $pageSize) {
		$this->criteria['limit'] = $pageSize;
		$this->criteria['offset'] = $offset;
		return $this->model->findAll($this->criteria, $this->bind);
	}
}