<?php

class DbCommandBuilderADO extends DbCommandBuilder
{
	public function buildFind($table, $criteria=null) {
		$sql = 'select ';
		if (!empty($criteria)) {
			if (isset($criteria['distinct']) && $criteria['distinct'])
				$sql .= 'distinct ';
			if (!empty($criteria['fields']))
				$sql .= (is_array($criteria['fields']) ? implode(', ', $criteria['fields']) : trim($criteria['fields']));
			else
				$sql .= '*';
			$sql .= " from {$table}";
			if (!empty($criteria['join']))
				$sql .= ' ' . (is_array($criteria['join']) ? implode(' ', $criteria['join']) : $criteria['join']);
			if (!empty($criteria['condition']))
				$sql .= ' where ' . (is_array($criteria['condition']) ? '(' . implode(' and ', $criteria['condition']) . ')' : $criteria['condition']);
			if (!empty($criteria['group']))
				$sql .= ' group by ' . (is_array($criteria['group']) ? implode(', ', $criteria['group']) : $criteria['group']);
			if (!empty($criteria['having']))
				$sql .= ' having ' . (is_array($criteria['having']) ? '(' . implode(' and ', $criteria['having']) . ')' : $criteria['having']);
			if (!empty($criteria['order']))
				$sql .= ' order by ' . (is_array($criteria['order']) ? implode(', ', $criteria['order']) : $criteria['order']);
		} else {
			$sql .= " * from {$table}";
		}
		return $sql;
	}

	public function buildCount($table, $criteria=null) {
		if (is_string($criteria))
			$criteria = array('condition' => $criteria);
		elseif (!is_array($criteria))
			$criteria = array();
		else
			unset($criteria['order']);
		if (isset($criteria['distinct']) && $criteria['distinct']) {
			$pk = (array)$this->adapter->getMetaData($table)->primaryKey;
			$criteria['fields'] = 'count(distinct ' . implode(',', $pk) . ')';
			unset($criteria['distinct']);
		} else {
			$criteria['fields'] = 'count(*)';
		}
		return $this->buildFind($table, $criteria);
	}

	public function buildInsert($table, array $values) {
		return $this->adapter->getDriver()->getInsertSQL($table, $values);
	}

	public function buildUpdate($table, array $values, $condition=null, array $bind=array()) {
		if (!empty($condition)) {
			$stmt = $this->adapter->prepare("select * from {$table} where {$condition}");
			$rs = $stmt->execute($bind);
			if ($rs)
				return $this->adapter->getDriver()->getUpdateSQL($rs, $values, false);
			throw new DbException(__(PHP2GO_LANG_DOMAIN, 'Command builder could not build the update command.'));
		} else {
			// @todo
		}
	}

	public function buildDelete($table, $condition=null) {
		$sql = "delete from {$table}";
		if ($condition)
			$sql .= " where {$condition}";
		return $sql;
	}
}