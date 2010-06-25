<?php

class FormModel extends Model
{
	public static $attributes = array();
	
	public function __construct($scenario='') {
		parent::__construct();
		$this->setScenario($scenario);
		$this->init();
	}
	
	public function init() {		
	}
	
	public function getAttributeNames() {
		$class = get_class($this);
		if (!isset(self::$attributes[$class])) {
			$attributes = array();
			$reflection = new ReflectionClass($class);
			foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
				$attributes[] = $property->getName();
			self::$attributes[$class] = $attributes;
		}
		return self::$attributes[$class];
	}
}