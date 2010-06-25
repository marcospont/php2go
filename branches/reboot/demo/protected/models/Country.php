<?php

class Country extends ActiveRecord 
{
	public $tableName = 'country';
	
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}