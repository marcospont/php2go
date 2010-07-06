<?php

class ActiveRecordRelationCollection extends ArrayObject
{
	protected $model;
	protected $relation;

	public function __construct(array $data, ActiveRecord $model, ActiveRecordRelation $relation) {
		parent::__construct($data);
		$this->model = $model;
		$this->relation = $relation;
	}

	public function append($item) {
		if ($item instanceof ActiveRecord)
			$item->setNamePrefix($this->model->getNamePrefix() . '[' . $this->relation->getName() . '][' . $this->count() . ']');
		parent::append($item);
	}

	public function offsetSet($index, $item) {
		if ($item instanceof ActiveRecord)
			$item->setNamePrefix($this->model->getNamePrefix() . '[' . $this->relation->getName() . '][' . ($index === null ? $this->count() : $index) . ']');
		parent::offsetSet($index, $item);
	}
}