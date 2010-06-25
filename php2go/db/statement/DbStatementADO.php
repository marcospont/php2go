<?php

class DbStatementADO extends DbStatement
{
	protected $result;

	public function getFetchMode() {
		$fetchMode = $this->getDriver()->fetchMode;
		switch ($fetchMode) {
			case ADODB_FETCH_NUM :
				return Db::FETCH_NUM;
			case ADODB_FETCH_ASSOC :
				return Db::FETCH_ASSOC;
			case ADODB_FETCH_BOTH :
				return Db::FETCH_BOTH;
		}
	}

	public function setFetchMode($fetchMode) {
		switch ($fetchMode) {
			case Db::FETCH_NUM :
				$fetchMode = ADODB_FETCH_NUM;
				break;
			case Db::FETCH_ASSOC :
				$fetchMode = ADODB_FETCH_ASSOC;
				break;
			case Db::FETCH_BOTH :
				$fetchMode = ADODB_FETCH_BOTH;
			default :
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid fetch mode: "%s".', array($fetchMode)));
		}
		$current = $this->getFetchMode();
		$this->getDriver()->setFetchMode($fetchMode);
		return $current;
	}

	public function getMetaData($col=null) {
		if ($this->result) {
			if ($col !== null) {
				$column = $this->result->fetchField($col);
				return new DbStatementColumn(array(
					'name' => $column->name,
					'type' => $column->type,
					'length' => $column->max_length
				));
			} else {
				$columns = array();
				for ($i=0,$s=$this->result->fieldCount(); $i<$s; $i++) {
					$column = $this->result->fetchField($i);
					$columns[] = new DbStatementColumn(array(
						'name' => $column->name,
						'type' => $column->type,
						'length' => $column->max_length
					));
				}
			}
		}
		return null;
	}

	public function execute(array $bind=array()) {
		$this->result = $this->getDriver()->execute($this->stmt, $bind);
		return $this->result;
	}

	public function fetch() {
		if (!$this->result || $this->result->EOF)
			return false;
		try {
			$result = $this->result->fields;
			$this->result->moveNext();
			return $result;
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}

	public function close() {
		if ($this->result)
			$this->result->close();
	}

	public function affectedRows() {
		return $this->getDriver()->affected_Rows();
	}

	public function columnCount() {
		return ($this->result ? $this->result->fieldCount() : 0);
	}

	public function rowCount() {
		return ($this->result ? $this->result->recordCount() : 0);
	}

	public function nextRowset() {
		return ($this->result ? $this->result->nextRecordSet() : 0);
	}

	protected function prepare($query) {
		try {
			return $this->getDriver()->prepare($query);
		} catch (ADODB_Exception $e) {
			throw new DbException($e->getMessage(), $e->getCode());
		}
	}
}