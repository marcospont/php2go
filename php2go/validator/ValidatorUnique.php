<?php

class ValidatorUnique extends Validator
{
	protected $modelName;
	protected $attrName;
	protected $caseSensitive = true;
	protected $condition = null;
	protected $conditionVars = array();

	public function __construct() {
		$this->defaultMessage = __(PHP2GO_LANG_DOMAIN, '"{value}" has already been taken.');
		$this->defaultModelMessage = __(PHP2GO_LANG_DOMAIN, '{attribute} "{value}" has already been taken.');
	}

	public function validate($value) {
		$finder = ActiveRecord::model($this->modelName);
		$metaData = $finder->getMetaData();
		if (!isset($metaData->columns[$this->attrName]))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Column {column} does not exist in table {table}', array('column' => $attrName, 'table' => $finder->getTableName())));
		$condition = ($this->caseSensitive ? "{$this->attrName} = ?" : "lower({$this->attrName}) = lower(?)") . ($this->condition !== null ? ' and ' . $this->condition : '');
		$conditionVars = $this->conditionVars;
		array_unshift($conditionVars, $value);
		if ($finder->exists($condition, $conditionVars)) {
			$this->setError($this->resolveMessage(), array('value' => $value));
			return false;
		}
		return true;
	}

	protected function validateModelAttribute(Model $model, $attr) {
		$value = (string)$model->{$attr};
		if ($this->allowEmpty && Util::isEmpty($value))
			return;
		$modelName = ($this->modelName !== null ? Php2Go::import($this->modelName) : get_class($model));
		$attrName = ($this->attrName !== null ? $this->attrName : $attr);
		$finder = ActiveRecord::model($modelName);
		$metaData = $finder->getMetaData();
		if (!isset($metaData->columns[$attrName]))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Column {column} does not exist in table {table}', array('column' => $attrName, 'table' => $finder->getTableName())));
		$condition = ($this->caseSensitive ? "{$attrName} = ?" : "lower({$attrName}) = lower(?)") . ($this->condition !== null ? ' and ' . $this->condition : '');
		$conditionVars = $this->conditionVars;
		array_unshift($conditionVars, $value);
		if (!$model instanceof ActiveRecord || $model->isNew() || $model->getTableName() != $finder->getTableName()) {
			$exists = $finder->exists($condition, $conditionVars);
		} else {
			$models = $finder->findAll($condition, $conditionVars);
			if (count($models) > 0) {
				if ($metaData->isPrimaryKey($attrName))
					$exists = ($model->getSavedPrimaryKey() !== $model->getPrimaryKey());
				else
					$exists = ($models[0]->getPrimaryKey() !== $model->getPrimaryKey());
			} else {
				$exists = false;
			}
		}
		if ($exists)
			$this->addModelError($model, $attr, $this->resolveModelMessage(), array('value' => $value));
	}
}