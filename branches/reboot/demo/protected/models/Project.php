<?php

class Project extends ActiveRecord
{
	public $tableName = 'projects';
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
	
	public function relations() {
		return array(
			'manager' => array(ActiveRecord::BELONGS_TO, 'class' => 'Person', 'foreignKey' => 'id_manager'),
			'people' => array(ActiveRecord::HAS_AND_BELONGS_TO_MANY, 'class' => 'Person', 'join' => 'projects_people(id_project,id_person)'),
			'tasks' => array(ActiveRecord::HAS_MANY, 'class' => 'Task', 'foreignKey' => 'id_project')
		);
	}
}