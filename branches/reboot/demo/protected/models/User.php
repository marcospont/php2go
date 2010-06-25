<?php

class User extends ActiveRecord
{
	public $tableName = 'users';
	
	public static function model($class=__CLASS__) {
		return parent::model($class);
	}
	
	public function relations() {
		return array(
			'person' => array(ActiveRecord::BELONGS_TO, 'class' => 'Person', 'foreignKey' => 'id_user')
		);
	}	
}