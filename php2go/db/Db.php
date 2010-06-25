<?php

Php2Go::import('php2go.db.adapter.*');

final class Db
{
	const ADAPTER_ADO = 'ado';
	const ADAPTER_PDO = 'pdo';
	const FETCH_NUM = 1;
	const FETCH_ASSOC = 2;
	const FETCH_BOTH = 3;
	const FETCH_OBJ = 4;

	private static $adapters = array(
		self::ADAPTER_ADO => 'DbAdapterADO',
		self::ADAPTER_PDO => 'DbAdapterPDO'
	);
	private static $inst;
	public static $instance;

	public static function factory(array $options=array()) {
		$adapter = Util::consumeArray($options, 'adapter', self::ADAPTER_ADO);
		if (!empty($adapter)) {
			$config = array();
			if (isset(self::$adapters[$adapter])) {
				$config['class'] = self::$adapters[$adapter];
			} else {
				$config['class'] = $adapter;
				$config['parent'] = 'DbAdapter';
			}
			return Php2Go::newInstance($config, $options);
		}
	}

	public static function instance() {
		return Php2Go::app()->getDb();
	}
}

class DbException extends Exception
{
}

class DbExpression
{
	private $expression;

	public function __construct($expression) {
		$this->expression = $expression;
	}

	public function __toString() {
		return $this->expression;
	}
}