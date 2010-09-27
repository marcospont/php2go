<?php

class ActiveRecordRelationHasMany extends ActiveRecordRelation
{
	protected $collection = true;
	protected $requiredOptions = array('class', 'foreignKey');

	public function find(ActiveRecord $base, $criteria=null, array $bind=array()) {
		$model = ActiveRecord::model($this->options['class']);
		if (is_string($criteria))
			$criteria = array('condition' => $criteria);
		elseif (!is_array($criteria))
			$criteria = array();
		$criteria = $this->buildCriteria($criteria);
		$bind[] = $base->getPrimaryKey();
		foreach (array('condition', 'order', 'limit', 'offset') as $item) {
			if (isset($this->options[$item])) {
				if (isset($criteria[$item]))
					$criteria[$item] = array_merge($criteria[$item], (array)$this->options[$item]);
				else
					$criteria[$item] = (array)$this->options[$item];
			}
		}
		if (isset($this->options['bind']))
			$bind = array_merge($bind, (array)$this->options['bind']);
		return DAO::instance()->findAll($model->getTableName(), $criteria, $bind);
	}

	public function save(ActiveRecord $base, $models) {
		if ($models instanceof ActiveRecordRelationCollection) {
			$dao = DAO::instance();
			$model = ActiveRecord::model($this->options['class']);
			$currKeys = $dao->findPairs($model->getTableName(), $model->getMetaData()->primaryKey, sprintf('%s = ?', $this->options['foreignKey']), array($base->getPrimaryKey()));
			if (count($models) > 0) {
				foreach ($models as $instance) {
					if ($instance->isNew())
						$instance->{$this->options['foreignKey']} = $base->getPrimaryKey();
					if (!$instance->isNew()) {
						$pk = $instance->getPrimaryKey();
						if (is_array($pk))
							$pk = implode('-', $pk);
						if (isset($currKeys[$pk]))
							unset($currKeys[$pk]);
					}
					if (!$instance->save())
						return false;
				}
			}
			if (!empty($currKeys)) {
				foreach ($currKeys as $key) {
					$key = explode('-', $key);
					$toDelete = $model->findByPK((sizeof($key) == 1 ? $key[0] : $key));
					if (!$toDelete->delete())
						return false;
				}
			}
		}
		return true;
	}

	public function delete(ActiveRecord $base) {
		$models = $base->getRelation($this->name);
		if (!empty($models)) {
			if (isset($this->options['deleteRestrict']) && $this->options['deleteRestrict'] === true) {
				$base->addError($this->name, __(PHP2GO_LANG_DOMAIN, 'This record can not be removed because it is referenced elsewhere in the system.'));
				return false;
			}
			foreach ($models as $model) {
				if (!$model->delete())
					return false;
			}
		}
		return true;
	}

	protected function buildCriteria(array $criteria) {
		$condition = sprintf('%s = ?', $this->options['foreignKey']);
		if (!isset($criteria['condition'])) {
			$criteria['condition'] = array($condition);
		} else {
			$criteria['condition'] = (array)$criteria['condition'];
			$criteria['condition'][] = $condition;
		}
		return $criteria;
	}
}