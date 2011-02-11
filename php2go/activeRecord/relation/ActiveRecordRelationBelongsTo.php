<?php

class ActiveRecordRelationBelongsTo extends ActiveRecordRelation
{
	private static $cache = array();
	protected $requiredOptions = array('class', 'foreignKey');

	public function merge(ActiveRecord $base, array &$criteria) {
		$this->loaded = true;
		$db = Db::instance();
		$model = ActiveRecord::model($this->options['class']);
		foreach ($model->getAttributeNames() as $name)
			$criteria['fields'][] = sprintf("%s.%s as %s", $this->name, $name, $db->quote("{$this->name}.{$name}"));
		$criteria['join'][] = $this->buildJoin($base, $model);
	}

	public function find(ActiveRecord $base) {
		$model = ActiveRecord::model($this->options['class']);
		$key = $model->getTableName() . '-' . $base->{$this->options['foreignKey']};
		if (!isset(self::$cache[$key])) {
			$joinKey = (isset($this->options['joinKey']) ? $this->options['joinKey'] : $model->getMetaData()->primaryKey);
			$joinCondition = (isset($this->options['joinCondition']) ? ' and ' . $this->options['joinCondition'] : '');
			self::$cache[$key] = DAO::instance()->find(($this->name != $model->getTableName() ? "{$model->getTableName()} as {$this->name}" : $this->name), sprintf('%s = ?', $joinKey) . $joinCondition, array($base->{$this->options['foreignKey']}));
		}
		return self::$cache[$key];
	}

	public function save(ActiveRecord $base, $model) {
		if ($model instanceof ActiveRecord) {
			$wasNew = $model->isNew();
			if ($model->save()) {
				if ($wasNew)
					$base->{$this->options['foreignKey']} = $model->getPrimaryKey();
				return true;
			} else {
				return false;
			}
		}
		return true;
	}

	public function delete(ActiveRecord $base) {
		if (isset($this->options['deleteCascade']) && $this->options['deleteCascade'] === true) {
			$model = $base->getRelation($this->name);
			if ($model instanceof ActiveRecord)
				return $model->delete();
		}
		return true;
	}

	protected function buildJoin(ActiveRecord $base, ActiveRecord $model) {
		$joinType = (isset($this->options['required']) && $this->options['required'] === false ? 'left join' : 'inner join');
		$joinKey = (isset($this->options['joinKey']) ? $this->options['joinKey'] : $model->getMetaData()->primaryKey);
		$joinCondition = (isset($this->options['joinCondition']) ? ' and ' . $this->options['joinCondition'] : '');
		return sprintf(
			"%s %s on (%s.%s = %s.%s%s)",
			$joinType, ($this->name != $model->getTableName() ? "{$model->getTableName()} as {$this->name}" : $this->name),
			$base->getTableName(), $this->options['foreignKey'],
			$this->name, $joinKey,
			$joinCondition
		);
	}
}