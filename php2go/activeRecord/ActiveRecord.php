<?php

abstract class ActiveRecord extends Model
{
	const TIMESTAMP = 'timestamp';
	const UPLOAD = 'upload';
	const BELONGS_TO = 'belongsTo';
	const HAS_ONE = 'hasOne';
	const HAS_MANY = 'hasMany';
	const HAS_AND_BELONGS_TO_MANY = 'hasAndBelongsToMany';

	public static $models = array();
	public $tableName;
	public $sequenceName;
	public $order;
	private $new = false;
	private $modified = array();
	private $deleted = false;
	private $pk = null;
	private $attributes = array();
	private $relations = null;
	private $associations = array();
	private $collections = array();
	private $criteria = array();
	private $formatter;

	public static function model($class=__CLASS__) {
		if (!isset(self::$models[$class]))
			self::$models[$class] = new $class(null);
		return self::$models[$class];
	}

	public function __construct($scenario='insert') {
		parent::__construct();
		$this->registerEvents(array(
			'onLoad',
			'onBeforeSave', 'onAfterSave',
			'onBeforeInsert', 'onAfterInsert',
			'onBeforeUpdate', 'onAfterUpdate',
			'onBeforeDelete', 'onAfterDelete'
		));
		$this->parseRelations();
		if ($scenario !== null) {
			$this->setScenario($scenario);
			$this->formatter = new ActiveRecordFormatter($this);
			$this->new = ($scenario == 'insert');
			$this->init();
		}
	}

	public function init() {
		parent::init();
		if ($this->getScenario() == 'insert') {
			foreach ($this->getMetaData()->columns as $name => $column) {
				if ($column->default !== null)
					$this->setAttribute($name, $column->default);
			}
		}
	}

	public function __get($name) {
		if (isset($this->attributes[$name]))
			return $this->getAttribute($name);
		elseif ($this->getMetaData()->hasColumn($name))
			return null;
		elseif (isset($this->relations[$name]))
			return $this->getRelation($name);
		return parent::__get($name);
	}

	public function __set($name, $value) {
		if (!$this->setAttribute($name, $value)) {
			if (isset($this->relations[$name]))
				$this->setRelation($name, $value);
			else
				parent::__set($name, $value);
		}
	}

	public function __isset($name) {
		if (isset($this->attributes[$name]))
			return true;
		elseif ($this->getMetaData()->hasColumn($name))
			return false;
		elseif (array_key_exists($name, $this->associations))
			return (!empty($this->associations[$name]));
		elseif (array_key_exists($name, $this->collections))
			return (!empty($this->collections[$name]));
		elseif (isset($this->relations[$name])) {
			$relation = $this->getRelation($name);
			return (!empty($relation));
		}
		return parent::__isset($name);
	}

	public function __unset($name) {
		if ($this->getMetaData()->hasColumn($name)) {
			if (isset($this->attributes[$name]) && $this->attributes[$name] !== null)
				$this->modified[$name] = array($this->attributes[$name], null);
			unset($this->attributes[$name]);
		} elseif (isset($this->associations[$name])) {
			unset($this->associations[$name]);
		} elseif (isset($this->collections[$name])) {
			unset($this->collections[$name]);
		} else {
			parent::__unset($name);
		}
	}

	public function __call($name, $params) {
		$scopes = $this->scopes();
		if (isset($scopes[$name])) {
			if (!is_string($scopes[$name]) && !is_array($scopes[$name]))
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid scope definition: "%s"', array($name)));
			if (isset($params[0]) && $params[0] === true)
				$this->resetScopes();
			$this->mergeCriteria($this->criteria, $scopes[$name]);
			return $this;
		}
		return parent::__call($name, $params);
	}

	public function __sleep() {
		return array_keys((array)$this);
	}

	public function getTableName() {
		return (isset($this->tableName) ? $this->tableName : __CLASS__);
	}

	public function getMetaData() {
		return DAO::instance()->getAdapter()->getMetaData($this->tableName);
	}

	public function isNew() {
		return $this->new;
	}

	public function getModified() {
		return $this->modified;
	}

	public function isModified() {
		return (!empty($this->modified));
	}

	public function isDeleted() {
		return $this->deleted;
	}

	public function formats() {
		return array();
	}

	public function scopes() {
		return array();
	}

	public function relations() {
		return array();
	}

	public function getSavedPrimaryKey() {
		return $this->pk;
	}

	public function getPrimaryKey() {
		$pk = $this->getMetaData()->primaryKey;
		if (is_string($pk)) {
			return $this->getAttribute($pk);
		} else {
			$values = array();
			foreach ($pk as $name)
				$values[$name] = $this->getAttribute($name);
			return $values;
		}
	}

	public function setPrimaryKey($value) {
		$pk = $this->getMetaData()->primaryKey;
		if (is_string($pk)) {
			$this->setAttribute($pk, $value);
		} else {
			foreach ($pk as $idx => $name)
				$this->setAttribute($name, $value[$idx]);
		}
	}

	public function getAttributeNames() {
		return $this->getMetaData()->getColumnNames();
	}

	public function getAttributes(array $names=array()) {
		$attrs = $this->attributes;
		foreach ($this->getMetaData()->getColumnNames() as $name)
			$attrs[$name] = $this->getAttribute($name);
		if (!empty($names)) {
			$result = array();
			foreach ($names as $name)
				$result[$name] = (isset($attrs[$name]) ? $attrs[$name] : null);
			return $result;
		}
		return $attrs;
	}

	public function hasAttribute($name) {
		return $this->getMetaData()->hasColumn($name);
	}

	public function getAttribute($name, $format=true) {
		if (isset($this->attributes[$name])) {
			if ($format && isset($this->formatter->formats[$name]))
				return $this->formatter->formatGet($name, $this->attributes[$name]);
			return $this->attributes[$name];
		}
		return null;
	}

	public function getAttributeFormat($name) {
		return $this->formatter->getFormat($name);
	}

	public function setAttribute($name, $value) {
		if ($value === '')
			$value = null;
		if (array_key_exists($name, $this->attributes)) {
			if ($value !== $this->{$name}) {
				$current = $this->attributes[$name];
				if (isset($this->formatter->formats[$name]))
					$this->attributes[$name] = $this->formatter->formatSet($name, $value);
				else
					$this->attributes[$name] = $value;
				$this->modified[$name] = array($current, $this->attributes[$name]);
			}
			return true;
		}
		if ($this->getMetaData()->hasColumn($name)) {
			if ($value !== null) {
				if (isset($this->formatter->formats[$name]))
					$this->attributes[$name] = $this->formatter->formatSet($name, $value);
				else
					$this->attributes[$name] = $value;
				$this->modified[$name] = array(null, $this->attributes[$name]);
			}
			return true;
		}
		return false;
	}

	public function getRelation($name, $criteria=null, array $bind=array()) {
		if ($criteria === null) {
			if (array_key_exists($name, $this->associations))
				return $this->associations[$name];
			if (array_key_exists($name, $this->collections))
				return $this->collections[$name];
		}
		if (!($relation = @$this->relations[$name]))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The class "%s" does not have relation "%s"', array(get_class($this), $name)));
		if ($this->isNew()) {
			if ($relation->isCollection())
				return ($this->collections[$name] = new ArrayObject());
			else
				return ($this->associations[$name] = null);
		}
		$data = $relation->find($this, $criteria, $bind);
		if ($relation->isCollection()) {
			if ($criteria === null) {
				if (!empty($data))
					$this->collections[$name] = $this->createRelationCollection($name, $data, true);
				else
					$this->collections[$name] = new ArrayObject();
				return $this->collections[$name];
			} else {
				return (!empty($data) ? $this->createRelationCollection($name, $data, true) : new ArrayObject());
			}
		} else {
			if ($criteria !== null)
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The relation "%s" can not use criteria when loading.', array($name)));
			if (!empty($data))
				$this->associations[$name] = $this->createRelationInstance($name, $data, true);
			else
				$this->associations[$name] = null;
			return $this->associations[$name];
		}
	}

	public function setRelation($name, $value) {
		if (!($relation = @$this->relations[$name]))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The class "%s" does not have relation "%s"', array(get_class($this), $name)));
		switch ($relation->getType()) {
			case ActiveRecord::BELONGS_TO :
				$class = $relation->getClass();
				if (!$value instanceof $class)
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" relation value must be a "%s" instance', array($name, $class)));
				$value->setNamePrefix($this->getNamePrefix() . '[' . $name . ']');
				$this->associations[$name] = $value;
				if ($value->isNew())
					$this->setAttribute($relation->getOption('foreignKey'), null);
				else
					$this->setAttribute($relation->getOption('foreignKey'), $value->getPrimaryKey());
				break;
			case ActiveRecord::HAS_ONE :
				$class = $relation->getClass();
				if (!$value instanceof $class)
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" relation value must be a "%s" instance', array($name, $class)));
				$relation->setOption('mustDelete', true);
				$value->setNamePrefix($this->getNamePrefix() . '[' . $name . ']');
				$this->associations[$name] = $value;
				break;
			case ActiveRecord::HAS_MANY :
				$class = $relation->getClass();
				if (!is_array($value))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" relation value must be an array', array($name)));
				$this->collections[$name] = new ArrayObject();
				foreach ($value as $idx => $record) {
					if (!$record instanceof $class)
						throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" relation value must be an array of "%s" instances', array($name, $class)));
					$record->setNamePrefix($this->getNamePrefix() . '[' . $name . '][' . $idx . ']');
					$this->collections[$name][] = $record;
				}
				break;
			case ActiveRecord::HAS_AND_BELONGS_TO_MANY :
				$class = $relation->getClass();
				if (!is_array($value))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The "%s" relation value must be an array', array($name)));
				$this->collections[$name] = new ArrayObject($value);
				break;
		}
	}

	public function import(array $attrs=array()) {
		foreach ($attrs as $name => $value) {
			if (!$this->setAttribute($name, $value)) {
				if (isset($this->relations[$name])) {
					switch ($this->relations[$name]->getType()) {
						case ActiveRecord::BELONGS_TO :
							$this->getRelation($name)->import($value);
							break;
						case ActiveRecord::HAS_ONE :
							$relation = $this->getRelation($name);
							if ($relation)
								$relation->import($value);
							else
								$this->associations[$name] = $this->createRelationInstance($name, $value);
							break;
						case ActiveRecord::HAS_MANY :
							$this->collections[$name] = $this->createRelationCollection($name, $value);
							break;
						case ActiveRecord::HAS_AND_BELONGS_TO_MANY :
							$this->collections[$name] = new ArrayObject($value);
							break;
					}
				}
			}
		}
		$this->raiseEvent('onImport', new Event($this));
	}

	public function validate($attrs=null) {
		$this->clearErrors();
		if ($this->raiseEvent('onBeforeValidate', new Event($this))) {
			$this->addErrors($this->formatter->getFormatErrors());
			foreach ($this->getValidators() as $validator) {
				$validator->validateModel($this, $attrs);
			}
			$result = !$this->hasErrors();
			foreach ($this->associations as $relation => $model) {
				if ($model !== null && !$model->validate())
					$result = false;
			}
			foreach ($this->collections as $relation => $models) {
				foreach ($models as $model) {
					if (!$model->validate())
						$result = false;
				}
			}
			$this->raiseEvent('onAfterValidate', new Event($this));
			return $result;
		}
		return false;
	}

	public function hasErrors($attr=null) {
		if ($attr !== null)
			return (!empty($this->errors[$attr]));
		if (!empty($this->errors))
			return true;
		foreach ($this->associations as $name => $model) {
			if ($model !== null && $model->hasErrors())
				return true;
		}
		foreach ($this->collections as $name => $models) {
			foreach ($models as $model) {
				if ($model->hasErrors())
					return true;
			}
		}
		return false;
	}

	public function getAllErrors() {
		$errors = parent::getErrors();
		if (!isset($errors[0]))
			$errors[0] = array();
		foreach ($this->associations as $relation => $model) {
			if ($model !== null) {
				$assocErrors = $model->getErrors();
				foreach ($assocErrors as $name => $data) {
					if ($name === 0)
						$errors[0] = array_merge($errors[0], $data);
					else
						$errors[$relation . '.' . $name] = $data;
				}
			}
		}
		foreach ($this->collections as $relation => $models) {
			foreach ($models as $idx => $model) {
				$collectionErrors = $model->getErrors();
				foreach ($collectionErrors as $name => $data) {
					if ($name === 0)
						$errors[0] = array_merge($errors[0], $data);
					else
						$errors[$relation . '.' . $idx . '.' . $name] = $data;
				}
			}
		}
		return $errors;
	}

	public function resetScopes() {
		$this->criteria = array();
		return $this;
	}

	public function find($criteria=null, array $bind=array()) {
		return $this->query($this->createCriteria($criteria, $bind));
	}

	public function findAll($criteria=null, array $bind=array()) {
		return $this->query($this->createCriteria($criteria, $bind, true), true);
	}

	public function findByAttributes(array $attributes, $criteria=null, array $bind=array()) {
		return $this->query($this->createAttributesCriteria($attributes, $criteria, $bind));
	}

	public function findAllByAttributes(array $attributes, $criteria=null, array $bind=array()) {
		return $this->query($this->createAttributesCriteria($attributes, $criteria, $bind, true), true);
	}

	public function findByPK($value) {
		return $this->query($this->createPKCriteria($value));
	}

	public function findPairs($display=null, $criteria=null, array $bind=array()) {
		$criteria = $this->createCriteria($criteria, $bind);
		if (@$criteria['lazy'] !== true)
			$this->mergeAssociations($criteria);
		$bind = Util::consumeArray($criteria, 'bind', array());
		return DAO::instance()->findPairs($this->getTableName(), $display, $criteria, $bind);
	}

	public function count($criteria=null, array $bind=array()) {
		$criteria = $this->createCriteria($criteria, $bind);
		if (@$criteria['lazy'] !== true)
			$this->mergeAssociations($criteria);
		$bind = Util::consumeArray($criteria, 'bind', array());
		return DAO::instance()->count($this->getTableName(), $criteria, $bind);
	}

	public function exists($criteria=null, array $bind=array()) {
		$criteria = $this->createCriteria($criteria, $bind);
		if (@$criteria['lazy'] !== true)
			$this->mergeAssociations($criteria);
		$bind = Util::consumeArray($criteria, 'bind', array());
		return DAO::instance()->exists($this->getTableName(), $criteria, $bind);
	}

	public function save($validate=true) {
		if (!$validate || $this->validate())
			return ($this->new ? $this->insert() : $this->update());
		return false;
	}

	public function saveAttribute($name) {
		return $this->saveAttributes(array($name));
	}

	public function saveAttributes(array $attrs=array()) {
		if ($this->new)
			throw new DbException(__(PHP2GO_LANG_DOMAIN, 'This active record cannot be updated on the database because it is new.'));
		$values = array();
		if (Util::isMap($attrs)) {
			foreach ($attrs as $name => $value)
				$this->setAttribute($name, $value);
			$values[$name] = $this->attributes[$name];
		} else {
			foreach ($attrs as $name) {
				if (isset($this->attributes[$name]))
					$values[$name] = $this->attributes[$name];
			}
		}
		$db = DAO::instance()->getAdapter();
		$db->beginTransaction();
		try {
			if (!empty($this->modified)) {
				$modified = $this->modified;
				if (!$this->raiseEvent('onBeforeSave', new Event($this))) {
					$db->rollback();
					return false;
				}
				if (!$this->raiseEvent('onBeforeUpdate', new Event($this))) {
					$db->rollback();
					return false;
				}
				if ($this->modified != $modified) {
					foreach (array_keys(array_diff_key($this->modified, $modified)) as $name)
						$values[$name] = $this->{$name};
				}
				if ($this->pk == null)
					$this->pk = $this->getPrimaryKey();
				if (!$this->updateByPK($values)) {
					$db->rollback();
					return false;
				}
				if (!$this->raiseEvent('onAfterUpdate', new Event($this))) {
					$db->rollback();
					return false;
				}
				if (!$this->raiseEvent('onAfterSave', new Event($this))) {
					$db->rollback();
					return false;
				}
				$this->modified = array();
				$db->commit();
			}
			return true;
		} catch (DbException $e) {
			$db->rollback();
			throw $e;
		}
	}

	public function insert() {
		if (!$this->new)
			throw new DbException(__(PHP2GO_LANG_DOMAIN, 'This active record cannot be inserted on the database because it is not new.'));
		$dao = DAO::instance();
		$db = $dao->getAdapter();
		$db->beginTransaction();
		try {
			if (!$this->raiseEvent('onBeforeSave', new Event($this))) {
				$db->rollback();
				return false;
			}
			if (!$this->raiseEvent('onBeforeInsert', new Event($this))) {
				$db->rollback();
				return false;
			}
			if (!$this->saveBelongsTo()) {
				$db->rollback();
				return false;
			}
			$table = $this->getTableName();
			$pk = $this->getMetaData()->primaryKey;
			if (is_string($pk) && $this->getPrimaryKey() === null && $db->getIdMethod() == DbAdapter::ID_METHOD_SEQUENCE)
				$this->setAttribute($pk, $db->getId((isset($this->sequenceName) ? $this->sequenceName : $table . '_seq')));
			if ($dao->insert($table, $this->attributes)) {
				if (is_string($pk) && $this->getPrimaryKey() === null && $db->getIdMethod() == DbAdapter::ID_METHOD_AUTOINCREMENT)
					$this->setAttribute($pk, $db->getId($table));
			} else {
				$db->rollback();
				return false;
			}
			if (!$this->saveRelations()) {
				$db->rollback();
				return false;
			}
			if (!$this->raiseEvent('onAfterInsert', new Event($this))) {
				$db->rollback();
				return false;
			}
			if (!$this->raiseEvent('onAfterSave', new Event($this))) {
				$db->rollback();
				return false;
			}
			foreach ($this->relations as $name => $relation) {
				if ($relation->getType() == ActiveRecord::BELONGS_TO && !isset($this->associations[$name]))
					$this->associations[$name] = $this->getRelation($name);
			}
			$this->pk = $this->getPrimaryKey();
			$this->new = false;
			$this->modified = array();
			$this->setScenario('update');
			$db->commit();
			return true;
		} catch (DbException $e) {
			$db->rollback();
			throw $e;
		}
	}

	public function update() {
		if ($this->new)
			throw new DbException(__(PHP2GO_LANG_DOMAIN, 'This active record cannot be updated on the database because it is new.'));
		$db = DAO::instance()->getAdapter();
		$db->beginTransaction();
		try {
			if (!$this->raiseEvent('onBeforeSave', new Event($this))) {
				$db->rollback();
				return false;
			}
			if (!$this->raiseEvent('onBeforeUpdate', new Event($this))) {
				$db->rollback();
				return false;
			}
			if (!$this->saveBelongsTo()) {
				$db->rollback();
				return false;
			}
			if (!empty($this->modified)) {
				if ($this->pk == null)
					$this->pk = $this->getPrimaryKey();
				if (!$this->updateByPK($this->attributes)) {
					$db->rollback();
					return false;
				}
			}
			if (!$this->saveRelations()) {
				$db->rollback();
				return false;
			}
			if (!$this->raiseEvent('onAfterUpdate', new Event($this))) {
				$db->rollback();
				return false;
			}
			if (!$this->raiseEvent('onAfterSave', new Event($this))) {
				$db->rollback();
				return false;
			}
			$this->modified = array();
			$db->commit();
			return true;
		} catch (DbException $e) {
			$db->rollback();
			throw $e;
		}
 	}

 	public function delete() {
 		if ($this->new)
 			throw new DbException(__(PHP2GO_LANG_DOMAIN, 'This active record cannot be deleted from the database because it is new.'));
 		$dao = DAO::instance();
 		$db = $dao->getAdapter();
 		$db->beginTransaction();
		try {
			if (!$this->raiseEvent('onBeforeDelete', new Event($this))) {
	 			$db->rollback();
	 			return false;
	 		}
			if (!$this->deleteRelations()) {
				$db->rollback();
				return false;
			}
			$table = $this->getTableName();
			$criteria = $this->createPKCriteria();
			if (!$dao->delete($table, implode(' and ', (array)$criteria['condition']), $criteria['bind'])) {
				$db->rollback();
				return false;
			}
			if (!$this->deleteBelongsTo()) {
				$db->rollback();
				return false;
			}
			if (!$this->raiseEvent('onAfterDelete', new Event($this))) {
				$db->rollback();
				return false;
			}
			$db->commit();
			$this->deleted = true;
			return true;
		} catch (DbException $e) {
			$db->rollback();
			throw $e;
		}
  	}

  	public function refresh() {
  		if (!$this->new && ($model = $this->findByPK($this->getPrimaryKey())) !== null) {
  			$this->loadAttributes($model->getAttributes());
  			return true;
  		}
  		return false;
  	}

	protected function resolveBehavior($class) {
		switch ($class) {
			case self::TIMESTAMP :
			case self::UPLOAD :
				Php2Go::import('php2go.activeRecord.behavior.*');
				$class = 'ActiveRecordBehavior' . Inflector::camelize($class);
				break;
		}
		return $class;
	}

	protected function query(array $criteria, $all=false) {
		if (@$criteria['lazy'] !== true)
			$this->mergeAssociations($criteria);
		$find = ($all ? 'findAll' : 'find');
		$bind = Util::consumeArray($criteria, 'bind', array());
		$data = DAO::instance()->{$find}($this->getTableName(), $criteria, $bind);
		if ($all) {
			return $this->createCollection($data);
		} elseif ($data) {
			$instance = $this->createInstance($data);
			return $instance;
		} else {
			return null;
		}
	}

	protected function mergeAssociations(&$criteria) {
		if (!empty($this->relations)) {
			$db = DAO::instance()->getAdapter();
			foreach (array('join', 'condition', 'group', 'having', 'order') as $member) {
				if (isset($criteria[$member]))
					$criteria[$member] = (array)$criteria[$member];
				else
					$criteria[$member] = array();
 			}
			$criteria['fields'] = array();
			foreach ($this->getAttributeNames() as $name)
				$criteria['fields'][] = sprintf("%s.%s as %s", $this->tableName, $name, $db->quote("{$this->tableName}.{$name}"));
			$pattern = '/(^|[^\.]|\s)\b(' . implode('|', $this->getAttributeNames()) . ')\b/i';
			foreach ($criteria as $key => $value) {
				if ($key != 'distinct' && $key != 'limit' && $key != 'offset') {
					foreach ($value as $idx => $item)
						$criteria[$key][$idx] = preg_replace($pattern, "$1{$this->getTableName()}.$2", $item);
				}
			}
			foreach ($this->relations as $name => $relation) {
				if (!$relation->isCollection())
					$relation->merge($this, $criteria);
			}
		}
	}

	protected function mergeCriteria(array &$target, $source) {
		if (is_string($source))
			$source = array('condition' => $source);
		foreach ($source as $k => $v) {
			if ($k == 'distinct' && $v)
				$target['distinct'] = $v;
			elseif (!isset($target[$k]))
				$target[$k] = $v;
			else
				$target[$k] = array_merge((array)$target[$k], (array)$source[$k]);
		}
	}

	protected function createCriteria($criteria=null, array $bind=array(), $all=false) {
		if (is_string($criteria))
			$criteria = array('condition' => $criteria);
		elseif (!is_array($criteria))
			$criteria = array();
		$criteria['bind'] = $bind;
		if ($all && !empty($this->order) && !isset($criteria['order']))
			$criteria['order'] = $this->order;
		if (!empty($this->criteria)) {
			$source = $this->criteria;
			$this->mergeCriteria($source, $criteria);
			return $source;
		} else {
			return $criteria;
		}
	}

	protected function createPKCriteria($value=null) {
		$pkCriteria = array();
		$pk = $this->getMetaData()->primaryKey;
		if ($value === null)
			$value = $this->getPrimaryKey();
		if (is_string($pk)) {
			$pkCriteria['condition'] = "{$this->tableName}.{$pk} = ?";
			$pkCriteria['bind'] = array($value);
		} else {
			for ($i=0; $i<sizeof($pk); $i++)
				$pkCriteria['condition'][] = "{$this->tableName}.{$pk[$i]} = ?";
			$pkCriteria['bind'] = $value;
		}
		return $pkCriteria;
	}

	protected function createAttributesCriteria(array $attributes, $criteria=null, array $bind=array(), $all=false) {
		if (is_string($criteria))
			$criteria = array('condition' => array($criteria));
		elseif (!is_array($criteria))
			$criteria = array();
		$criteria['condition'] = (isset($criteria['condition']) ? (array)$criteria['condition'] : array());
		$criteria['bind'] = $bind;
		foreach ($attributes as $name=>$value) {
			if ($this->hasAttribute($name)) {
				if ($value !== null) {
					$criteria['condition'][] = "{$this->tableName}.{$name} = ?";
					$criteria['bind'][] = $value;
				} else {
					$criteria['condition'][] = "{$this->tableName}.{$name} is null";
				}
			}
		}
		if ($all && !empty($this->order) && !isset($criteria['order']))
			$criteria['order'] = $this->order;
		if (!empty($this->criteria)) {
			$source = $this->criteria;
			$this->mergeCriteria($source, $criteria);
			return $source;
		} else {
			return $criteria;
		}
	}

	protected function loadAttributes(array $attrs) {
		if ($this->getScenario() == 'update') {
			$this->attributes = $attrs;
			$this->pk = $this->getPrimaryKey();
			$this->raiseEvent('onLoad', new Event($this));
			$this->modified = array();
		}
	}

	protected function createCollection(array $data) {
		$collection = new ArrayObject();
		foreach ($data as $attrs)
			$collection[] = $this->createInstance($attrs);
		return $collection;
	}

	protected function createInstance(array $data) {
		if (!empty($data)) {
			$attrs = array();
			$assoc = array();
			$class = get_class($this);
			$instance = new $class('update');
			foreach ($data as $name => $value) {
				if (($pos = strpos($name, '.')) !== false) {
					$alias = substr($name, 0, $pos);
					if ($alias == $this->tableName) {
						$column = substr($name, $pos+1);
						$attrs[$column] = $value;
					} else {
						$assoc[$alias][substr($name, $pos+1)] = $value;
					}
				} else {
					$attrs[$name] = $value;
				}
			}
			$instance->loadAttributes($attrs);
			foreach ($assoc as $name => $attrs) {
				if (count($attrs) != count(array_filter($attrs, 'is_null')))
					$instance->associations[$name] = $instance->createRelationInstance($name, $attrs, true);
				else
					$instance->associations[$name] = null;
			}
			return $instance;
		}
		return null;
	}

	protected function createRelationCollection($name, array $data, $isUpdate=false) {
		$collection = new ArrayObject();
		foreach ($data as $i => $attrs)
			$collection[] = $this->createRelationInstance($name, $attrs, $isUpdate, $i);
		return $collection;
	}

	protected function createRelationInstance($name, array $attrs, $isUpdate=false, $index=null) {
		$class = $this->relations[$name]->getClass();
		$model = ActiveRecord::model($class);
		$instance = new $class(($isUpdate || $this->containsPK($model, $attrs) ? 'update' : 'insert'));
		$instance->loadAttributes($attrs);
		$instance->setNamePrefix($this->getNamePrefix() . '[' . $name . ']' . ($index !== null ? '[' . $index . ']' : ''));
		return $instance;
	}

 	private function updateByPK(array $attrs=array()) {
		$dao = DAO::instance();
		$table = $this->getTableName();
		$criteria = $this->createPKCriteria();
		return $dao->update($table, $attrs, implode(' and ', (array)$criteria['condition']), $criteria['bind']);
 	}

	private function saveBelongsTo() {
		foreach ($this->relations as $name => $relation) {
			if ($relation->getType() == ActiveRecord::BELONGS_TO) {
				if (!$relation->save($this, @$this->associations[$name]))
					return false;
			}
		}
		return true;
	}

	private function saveRelations() {
		foreach ($this->relations as $name => $relation) {
			switch ($relation->getType()) {
				case ActiveRecord::HAS_ONE :
					if (!$relation->save($this, @$this->associations[$name]))
						return false;
				case ActiveRecord::HAS_MANY :
				case ActiveRecord::HAS_AND_BELONGS_TO_MANY :
					if (!$relation->save($this, @$this->collections[$name]))
						return false;
			}
		}
		return true;
	}

	private function deleteBelongsTo() {
		foreach ($this->relations as $name => $relation) {
			if ($relation->getType() == ActiveRecord::BELONGS_TO) {
				if (!$relation->delete($this))
					return false;
			}
		}
		return true;
	}

	private function deleteRelations() {
		foreach ($this->relations as $name => $relation) {
			if ($relation->getType() != ActiveRecord::BELONGS_TO) {
				if (!$relation->delete($this))
					return false;
			}
		}
		return true;
	}

	private function parseRelations() {
		if ($this->relations === null) {
			Php2Go::import('php2go.activeRecord.relation.*');
			$this->relations = array();
			foreach ($this->relations() as $name => $relation) {
				if (is_string($name) && $name != $this->tableName && is_array($relation)) {
					$type = array_shift($relation);
					switch ($type) {
						case ActiveRecord::BELONGS_TO :
						case ActiveRecord::HAS_ONE :
						case ActiveRecord::HAS_MANY :
						case ActiveRecord::HAS_AND_BELONGS_TO_MANY :
							$class = Inflector::camelize('ActiveRecordRelation' . $type);
							$this->relations[$name] = new $class($name, $relation);
							break;
						default :
							throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid relation specification: "%s"', array(serialize($relation))));
					}
				} else {
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid relation specification: "%s"', array(serialize($relation))));
				}
			}
		}
	}

	private function containsPK(ActiveRecord $model, array $attrs) {
		$pk = $model->getMetaData()->primaryKey;
		if (is_string($pk))
			return (isset($attrs[$pk]));
		foreach ($pk as $item) {
			if (!isset($attrs[$item]))
				return false;
		}
		return true;
	}
}