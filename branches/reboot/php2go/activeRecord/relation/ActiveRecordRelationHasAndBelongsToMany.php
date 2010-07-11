<?php

class ActiveRecordRelationHasAndBelongsToMany extends ActiveRecordRelationHasMany
{
	protected $requiredOptions = array('class', 'join');

	public function validateOptions() {
		parent::validateOptions();
		$matches = array();
		if (!preg_match('/(\w+)\(\s*(\w+)\s*,\s*(\w+)\s*\)/', $this->options['join'], $matches)) {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid format of "%s" option of the "%s" relation. Correct format is "table(column,column)".', array('joinKeys', get_class($this))));
		} else {
			$this->options['joinTable'] = $matches[1];
			$this->options['joinKey'] = $matches[2];
			$this->options['foreignKey'] = $matches[3];
		}
	}

	public function save(ActiveRecord $base, $keys) {
		if (!empty($keys) && count($keys) > 0) {
			$dao = DAO::instance();
			$dao->delete($this->options['joinTable'], sprintf('%s = ?', $this->options['joinKey']), array($base->getPrimaryKey()));
			foreach ($keys as $key) {
				$foreignKey = ($key instanceof ActiveRecord ? $key->getPrimaryKey() : $key);
				$dao->insert($this->options['joinTable'], array(
					$this->options['joinKey'] => $base->getPrimaryKey(),
					$this->options['foreignKey'] => $foreignKey
				));
			}
			return true;
		}
		return true;
	}

	public function delete(ActiveRecord $base) {
		$dao = DAO::instance();
		try {
			$dao->delete($this->options['joinTable'], sprintf('%s = ?', $this->options['joinKey']), array($base->getPrimaryKey()));
			return true;
		} catch (DbException $e) {
			return false;
		}
	}

	protected function buildCriteria(array $criteria) {
		$model = ActiveRecord::model($this->options['class']);
		$tableName = $model->getTableName();
		$fields = array();
		if (!isset($criteria['fields'])) {
			foreach ($model->getAttributeNames() as $name)
				$fields[] = sprintf("%s.%s", $tableName, $name);
		}
		$join = sprintf(
				'inner join %s on %s.%s = %s.%s',
				$this->options['joinTable'],
				$tableName, $model->getMetaData()->primaryKey,
				$this->options['joinTable'], $this->options['foreignKey']
		);
		$condition = sprintf('%s.%s = ?', $this->options['joinTable'], $this->options['joinKey']);
		if (!isset($criteria['fields']))
			$criteria['fields'] = $fields;
		if (!isset($criteria['join'])) {
			$criteria['join'] = array($join);
		} else {
			$criteria['join'] = (array)$criteria['join'];
			$criteria['join'][] = $join;
		}
		if (!isset($criteria['condition'])) {
			$criteria['condition'] = array($condition);
		} else {
			$criteria['condition'] = (array)$criteria['condition'];
			$criteria['condition'][] = $condition;
		}
		return $criteria;
	}
}