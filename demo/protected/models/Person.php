<?php

class Person extends ActiveRecord
{
	public $tableName = 'people';
	
	public static function model($class=__CLASS__) {
		return parent::model($class);
	}
	
	public function behaviors() {
		return array(
			'timestamp' => array(ActiveRecord::TIMESTAMP, 
				'insert' => 'add_date',
				'update' => 'update_date'
			),
			'upload' => array(ActiveRecord::UPLOAD,
				'attrs' => array(
					'picture' => array(
						'savePath' => 'upload',
						'generateFileName' => true,
					)
				)
			)
		);
	}
	
	public function formats() {
		return array(
			'birth_date' => 'date'
		);
	}
	
	public function relations() {
		return array(
			'country' => array(ActiveRecord::BELONGS_TO, 'class' => 'Country', 'foreignKey' => 'id_country'),
			'user' => array(ActiveRecord::HAS_ONE, 'class' => 'User', 'foreignKey' => 'id_person'),
			'projects_manager' => array(ActiveRecord::HAS_MANY, 'class' => 'Project', 'foreignKey' => 'id_manager', 'deleteRestrict' => true),
			'projects_member' => array(ActiveRecord::HAS_AND_BELONGS_TO_MANY, 'class' => 'Project', 'join' => 'projects_people(id_person,id_project)'),
			'tasks' => array(ActiveRecord::HAS_MANY, 'class' => 'Task', 'foreignKey' => 'id_owner')
		);
	}
	
	public function rules() {
		return array(
			array('name,sex,birth_date,address,id_country', 'required'),
			array('name', 'length', 'max'=>50),
			array('sex', 'in', 'choices'=>array('M', 'F')),
			array('birth_date', 'date'),
			array('address', 'length', 'max'=>100),
			array('picture', 'upload', 'rules' => array(
				'mimeType' => array('allow' => 'image/jpeg'),
				'imageSize' => array('maxWidth' => 1024, 'maxHeight' => 768)
			))
		);
	}
	
	public function attributeLabels() {
		return array(
			'name' => 'Nome',
			'sex' => 'Sexo',
			'birth_date' => 'Nascimento',
			'address' => 'Endereço',
			'id_country' => 'País',
			'notes' => 'Notas',
			'active' => 'Ativo',
			'int_val' => 'Int',
			'float_val' => 'Float'
		);
	}
}