<?php

class Task extends ActiveRecord
{
	public $tableName = 'tasks';
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function relations() {
		return array(
			'project' => array(ActiveRecord::BELONGS_TO, 'class' => 'Project', 'foreignKey' => 'id_project'),
			'owner' => array(ActiveRecord::BELONGS_TO, 'class' => 'Person', 'foreignKey' => 'id_owner')
		);
	}
}