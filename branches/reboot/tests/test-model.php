<?php

include 'bootstrap.php';

class MyModel extends FormModel
{
	public $name;
	public $age;
	public $sex;
	public $email;
	public $birth_date;
	public $website;

	public function rules() {
		return array(
			array('name,age,sex,email,birth_date', 'required'),
			array('name', 'length', 'max' => 50),
			array('name', 'regex', 'pattern' => '/^\w[\w\s]+$/'),
			array('age', 'number', 'integer' => true, 'unsigned' => true),
			array('age', 'compare', 'operator' => 'goet', 'peer' => 18, 'dataType' => 'integer'),
			array('sex', 'in', 'choices' => array('M', 'F')),
			array('email', 'email'),
			array('birth_date', 'date'),
			array('website', 'url')
		);
	}
}

$model = new MyModel();
$model->name = 'Marcos';
$model->age = '30';
$model->sex = 'M';
$model->email = 'marcos.pont@gmail.com';
$model->birth_date = '03/12/1980';
$model->website = 'http://oscozinheiros.blogspot.com';
$model->validate();
echo '<pre>';
var_dump($model->getErrors());
echo '</pre>';