<?php

class ActiveRecordRelationHasOne extends ActiveRecordRelationBelongsTo
{
	protected $requiredOptions = array('class');

	public function find(ActiveRecord $base) {
		$model = ActiveRecord::model($this->options['class']);
		$foreignKey = (isset($this->options['foreignKey']) ? $this->options['foreignKey'] : $base->getMetaData()->primaryKey);
		return DAO::instance()->find($model->getTableName(), sprintf('%s = ?', $model->getMetaData()->primaryKey), array($base->{$foreignKey}));
	}

	public function save(ActiveRecord $base, $model) {
		if ($model instanceof ActiveRecord) {
			$foreignKey = (isset($this->options['foreignKey']) ? $this->options['foreignKey'] : $base->getMetaData()->primaryKey);
			if (@$this->options['mustDelete']) {
				DAO::instance()->delete($model->getTableName(), sprintf('%s = ?', $model->getMetaData()->primaryKey), array($base->{$foreignKey}));
				$this->options['mustDelete'] = false;
			}
			if ($model->isNew())
				$model->{$this->options['foreignKey']} = $base->getPrimaryKey();
			if (!$model->save())
				return false;
		}
		return true;
	}

	public function delete(ActiveRecord $base) {
		$model = $base->getRelation($this->name);
		if ($model instanceof ActiveRecord) {
			if (isset($this->options['deleteRestrict']) && $this->options['deleteRestrict'] === true) {
				$base->addError($this->name, __(PHP2GO_LANG_DOMAIN, 'This record can not be removed because it is referenced elsewhere in the system.'));
				return false;
			}
			return $model->delete();
		}
		return true;
	}

	protected function buildJoin(ActiveRecord $base, ActiveRecord $model) {
		$foreignKey = (isset($this->options['foreignKey']) ? $this->options['foreignKey'] : $base->getMetaData()->primaryKey);
		return sprintf(
			"left outer join %s on %s.%s = %s.%s",
			($this->name != $model->getTableName() ? "{$model->getTableName()} as {$this->name}" : $this->name),
			$base->getTableName(), $foreignKey,
			$this->name, $model->getMetaData()->primaryKey
		);
	}
}