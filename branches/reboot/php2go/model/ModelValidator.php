<?php

abstract class ModelValidator
{
	private static $defaultValidatorClasses = array(
		'compare' => 'ValidatorComparison',
		'date' => array('ValidatorDataType', 'dataType'),
		'datetime' => array('ValidatorDataType', 'dataType'),
		'decimal' => array('ValidatorDataType', 'dataType'),
		'email' => 'ValidatorEmail',
		'in' => 'ValidatorChoice',
		'integer' => array('ValidatorDataType', 'dataType'),
		'length' => 'ValidatorLength',
		'number' => 'ValidatorNumber',
		'regex' => 'ValidatorRegex',
		'required' => 'ValidatorRequired',
		'time' => array('ValidatorDataType', 'dataType'),
		'type' => 'ValidatorDataType',
		'unique' => 'ValidatorUnique',
		'upload' => 'ValidatorUpload',
		'url' => 'ValidatorUrl'
	);

	public static function factory($name, Model $model, $attrs, array $options=array()) {
		if (is_string($attrs))
			$options['modelAttributes'] = preg_split('/[\s,]+/', $attrs, -1, PREG_SPLIT_NO_EMPTY);
		if (is_string(@$options['on']))
			$options['modelScenarios'] = preg_split('/[\s,]+/', $options['on'], -1, PREG_SPLIT_NO_EMPTY);
		if (!isset($options['messageDomain']))
			$options['messageDomain'] = Php2Go::app()->getTranslator()->getValidatorDomain();
		if (method_exists($model, $name)) {
			$options['method'] = $name;
			$validator = new ValidatorInline();
			$validator->loadOptions($options);
		} else {
			if (isset(self::$defaultValidatorClasses[$name])) {
				if (is_array(self::$defaultValidatorClasses[$name])) {
					$class = self::$defaultValidatorClasses[$name][0];
					$options[self::$defaultValidatorClasses[$name][1]] = $name;
				} else {
					$class = self::$defaultValidatorClasses[$name];
				}
				$parent = null;
			} else {
				$class = $name;
				$parent = 'Validator';
			}
			$validator = Php2Go::newInstance(array(
				'class' => $class,
				'parent' => $parent,
				'options' => $options
			));
		}
		return $validator;
	}
}