<?php

class AuthenticatorAdapterDb extends AuthenticatorAdapter
{
	protected $db;
	protected $tableName;
	protected $usernameColumn;
	protected $passwordColumn;
	protected $passwordFunction = 'md5';
	protected $extraCondition;
	protected $extraParams = array();

	public function __construct() {
		$this->db = Db::instance();
	}

	public function getTableName() {
		return $this->tableName;
	}

	public function setTableName($name) {
		$this->tableName = $name;
	}

	public function getUsernameColumn() {
		return $this->usernameColumn;
	}

	public function setUsernameColumn($column) {
		$this->usernameColumn = $column;
	}

	public function getPasswordColumn() {
		return $this->passwordColumn;
	}

	public function setPasswordColumn($column) {
		$this->passwordColumn = $column;
	}

	public function getPasswordFunction() {
		return $this->passwordFunction;
	}

	public function setPasswordFunction($function) {
		if (!is_callable($function))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid password function.'));
		$this->passwordFunction = $function;
	}

	public function getExtraCondition() {
		return $this->extraCondition;
	}

	public function setExtraCondition($condition) {
		$this->extraCondition = $condition;
	}

	public function getExtraParams() {
		return $this->extraParams;
	}

	public function setExtraParams(array $params) {
		$this->extraParams = $params;
	}

	public function authenticate($username, $password) {
		$this->validateQuery();
		return $this->executeQuery($this->buildQuery(), $username, $password);
	}

	private function validateQuery() {
		if (empty($this->tableName) || empty($this->usernameColumn) || empty($this->passwordColumn))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "tableName", "usernameColumn" and "passwordColumn" options are mandatory for DbAuthenticatorAdapter.'));
	}

	private function buildQuery() {
		$criteria = array('condition' => array(
				$this->usernameColumn . ' = ?',
				$this->passwordColumn . ' = ?'
		));
		if ($this->extraCondition)
			$criteria['condition'][] = $this->extraCondition;
		return $this->db->getCommandBuilder()->buildFind($this->tableName, $criteria);
	}

	private function executeQuery($query, $username, $password) {
		$params = array_merge(array(
			$username,
			($this->passwordFunction !== null ? call_user_func($this->passwordFunction, $password) : $password)
		), $this->extraParams);
		$stmt = $this->db->execute($query, $params);
		if ($stmt->rowCount() == 1)
			return $stmt->fetch();
		return false;
	}
}