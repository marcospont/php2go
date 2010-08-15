<?php

class ActiveRecordRelationHasOne extends ActiveRecordRelationBelongsTo
{
	protected $requiredOptions = array('class');

	public function find(ActiveRecord $base) {
		$model = ActiveRecord::model($this->options['class']);
		return DAO::instance()->find($model->getTableName(), sprintf('%s = ?', $model->getMetaData()->primaryKey), array($base->getPrimaryKey()));
	}

	public function save(ActiveRecord $base, $model) {
		if ($model instanceof ActiveRecord) {
			if (@$this->options['mustDelete']) {
				DAO::instance()->delete($model->getTableName(), sprintf('%s = ?', $model->getMetaData()->primaryKey), array($base->getPrimaryKey()));
				$this->options['mustDelete'] = false;
			}
			if ($model->isNew())
				$model->setPrimaryKey($base->getPrimaryKey());
			if (!$model->save())
				return false;
		}
		return true;
	}

	public function delete(ActiveRecord $base) {
		$model = $base->getRelation($this->name);
		if ($model instanceof ActiveRecord) {
			if ($this->options['deleteRestrict']) {
				$base->addError($this->name, __(PHP2GO_LANG_DOMAIN, 'This record can not be removed because it is referenced elsewhere in the system.'));
				return false;
			}
			return $model->delete();
		}
		return true;
	}

	protected function buildJoin(ActiveRecord $base, ActiveRecord $model) {
		return sprintf(
			"left outer join %s on %s.%s = %s.%s",
			($this->name != $model->getTableName() ? "{$model->getTableName()} as {$this->name}" : $this->name),
			$base->getTableName(), $base->getMetaData()->primaryKey,
			$this->name, $model->getMetaData()->primaryKey
		);
	}
}