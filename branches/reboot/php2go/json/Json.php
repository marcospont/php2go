<?php

class Json
{
	const RETURN_OBJECT = 1;
	const RETURN_ARRAY = 0;

	public static $useExtension = true;

	public static function encode($value, array $options=array()) {
		$expressions = array();
		$ensureUTF8 = !!@$options['ensureUTF8'];
		if ($ensureUTF8)
			$value = self::ensureUTF8($value);
		$findExpressions = !!@$options['findExpressions'];
		if ($findExpressions)
			$value = self::findExpressions($value, $expressions);
		if (function_exists('json_encode') && self::$useExtension)
			$result = json_encode($value);
		else
			$result = JsonEncoder::encode($value, $options);
		if ($findExpressions && sizeof($expressions) > 0) {
			for ($i=0,$count=sizeof($expressions); $i<$count; $i++) {
				$key = $expressions[$i]['key'];
				$value = $expressions[$i]['value'];
				$result = str_replace('"' . $key . '"', $value, $result);
			}
		}
		return $result;
	}

	public static function decode($value, $returnType=self::RETURN_ARRAY) {
		$value = (string)$value;
		if (function_exists('json_decode') && self::$useExtension) {
			$result = json_decode($value, $returnType);
			if (!function_exists('json_last_error')) {
				if ($result === $value)
					throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error.'));
			} elseif (($error = json_last_error()) != JSON_ERROR_NONE) {
				switch ($error) {
					case JSON_ERROR_DEPTH :
						throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - maximum stack depth exceeded.'));
					case JSON_ERROR_CTRL_CHAR :
						throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - unexpected control char found.'));
					case JSON_ERROR_SYNTAX :
						throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - syntax error.'));
					default :
						throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error.'));
				}
			}
			return $result;
		}
		return JsonDecoder::decode($value, $returnType);
	}

	public static function expression($expression) {
		return new JsonExpression($expression);
	}

	public static function ensureUTF8($value, $key=null) {
		if (is_array($value)) {
			array_walk($value, array(__CLASS__, 'ensureUTF8'));
			return $value;
		} elseif (is_object($value)) {
			foreach ($value as $k => $v)
				$value->{$k} = self::ensureUTF8($v);
			return $value;
		} elseif (is_string($value)) {
			return utf8_encode($value);
		}
		return $value;
	}

	private static function findExpressions(&$value, &$expressions, $currentKey=null) {
		if ($value instanceof JsonExpression) {
			$key = '__expr__' . ($currentKey ? $currentKey . '__' : '') . sizeof($expressions) . '__';
			$expressions[] = array(
				'key' => JsonEncoder::encodeUnicodeString($key),
				'value' => $value->__toString()
			);
			$value = $key;
		} elseif (is_array($value)) {
			foreach ($value as $k => $v)
				$value[$k] = self::findExpressions($value[$k], $expressions, $k);
		} elseif (is_object($value)) {
			foreach ($value as $k => $v)
				$value[$k] = self::findExpressions($value[$k], $expressions, $k);
		}
		return $value;
	}
}

class JsonException extends Exception
{
}

class JsonExpression
{
	private $expression;

	public function __construct($expression) {
		$this->expression = $expression;
	}

	public function __toString() {
		return $this->expression;
	}
}