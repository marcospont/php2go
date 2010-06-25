<?php

final class Js
{
	public static function escape($js) {
		return strtr($js, array(
			"\t" => '\t',
			"\r" => '\r',
			"\n" => '\n',
			'"' => '\"',
			'\'' => '\\\'',
			'\\' => '\\\\',
			'</' => '<\/'
		));
	}

	public static function callback($value, array $params=array()) {
		if ($value instanceof JsFunc)
			$value = $value->__toString();
		elseif ($value instanceof JsIdentifier)
			return $value;
		elseif (preg_match('/^[\w\.]+$/', $value))
			return new JsIdentifier($value);
		return new JsFunc($value, $params);
	}

	public static function emptyObject() {
		return new JsIdentifier('{}');
	}

	public static function func($body, array $params=array()) {
		return new JsFunc($body, $params);
	}

	public static function identifier($identifier) {
		return new JsIdentifier($identifier);
	}

	public static function encode($value) {
		if (is_string($value)) {
			return '"' . self::escape($value) . '"';
		} elseif (is_null($value)) {
			return 'null';
		} elseif (is_bool($value)) {
			return ($value ? 'true' : 'false');
		} elseif (is_int($value)) {
			return $value;
		} elseif (is_float($value)) {
			if ($value == INF)
				return 'Number.POSITIVE_INFINITY';
			elseif ($value == -INF)
				return 'Number.NEGATIVE_INFINITY';
			else
				return $value;
		} elseif (is_object($value)) {
			if ($value instanceof JsIdentifier)
				return $value->__toString();
			else
				return self::encode(get_object_vars($value));
		} elseif (is_array($value)) {
			if (Util::isMap($value)) {
				$values = array();
				foreach ($value as $k => $v)
					$values[] = '"' . self::escape($k) . '":' . self::encode($v);
				return '{' . implode(',', $values) . '}';
			} else {
				$values = array();
				foreach ($value as $v)
					$values[] = self::encode($v);
				return '[' . implode(',', $values) . ']';
			}
		} else {
			return '';
		}
	}
}

class JsIdentifier
{
	protected $identifier;

	public function __construct($identifier) {
		$this->identifier = $identifier;
	}

	public function __toString() {
		return $this->identifier;
	}
}

class JsFunc extends JsIdentifier
{
	public function __construct($body, array $params=array()) {
		$body = preg_replace("/" . PHP_EOL . "\s*/", PHP_EOL . "\t", $body);
		$body = trim($body);
		parent::__construct('function(' . implode(',', $params) . ') {' . PHP_EOL . "\t" . $body . PHP_EOL . '}');
	}
}