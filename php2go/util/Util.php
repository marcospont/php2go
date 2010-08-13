<?php

final class Util
{
	private static $idCounter = array();

	public static function id($namespace) {
		if (!isset(self::$idCounter[$namespace]))
			self::$idCounter[$namespace] = 0;
		return $namespace . (++self::$idCounter[$namespace]);
	}

	public static function consumeArray(array &$array, $key, $fallback=null) {
		if (array_key_exists($key, $array)) {
			$value = $array[$key];
			unset($array[$key]);
			return $value;
		}
		return $fallback;
	}

	public static function findArrayPath(array $array, $path, $separator='.', $fallback=null) {
		$parts = explode($separator, $path);
		if (sizeof($parts) == 1) {
			return (array_key_exists($path, $array) ? $array[$path] : $fallback);
		} else {
			$i = 0;
			$base = $array;
			$size = sizeof($parts);
			while ($i < $size) {
				if (!array_key_exists($parts[$i], $base))
					return $fallback;
				else
					$base = $base[$parts[$i]];
				if ($i < ($size-1) && !is_array($base))
					return $fallback;
				$i++;
			}
			return $base;
		}
	}

	public static function mergeArray($a, $b) {
		foreach($b as $k => $v) {
			if (is_integer($k))
				$a[] = $v;
			elseif (is_array($v) && isset($a[$k]) && is_array($a[$k]))
				$a[$k] = self::mergeArray($a[$k], $v);
			else
				$a[$k] = $v;
		}
		return $a;
	}

	public static function ifNull($value, $fallback=null) {
		return ($value === null ? $fallback : $value);
	}

	public static function isEmpty($value, $trim=false) {
		return ($value === null || $value === array() || $value === '' || ($trim && is_string($value) && trim($value) === ''));
	}

	public static function ifEmpty($value, $fallback=null) {
		return (self::isEmpty($value) ? $fallback : $value);
	}

	public static function isMap($array) {
		if (is_array($array)) {
			if (!empty($array)) {
				$i = 0;
				foreach ($array as $k => $v) {
					if ($k !== $i)
						return true;
					$i++;
				}
			}
		}
		return false;
	}

	public static function isUTF8($str) {
		for ($i = 0; $i < strlen($string); $i++) {
        	if (ord($string[$i]) < 0x80) // 0bbbbbbb
        		continue;
        	elseif ((ord($string[$i]) & 0xE0) == 0xC0) // 110bbbbb
        		$n = 1;
        	elseif ((ord($string[$i]) & 0xF0) == 0xE0) // 1110bbbb
        		$n = 2;
        	elseif ((ord($string[$i]) & 0xF8) == 0xF0) // 11110bbb
        		$n = 3;
        	elseif ((ord($string[$i]) & 0xFC) == 0xF8) // 111110bb
        		$n = 4;
        	elseif ((ord($string[$i]) & 0xFE) == 0xFC) // 1111110b
        		$n = 5;
        	else // does not match any model
        		return false;
        	// n bytes matching 10bbbbbb follow?
        	for ($j=0; $j<$n; $j++) {
				if ((++$i == strlen($string)) || ((ord($string[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}

	public static function hash($value, $salt='') {
		return sprintf('%x', crc32($value . strval($salt)));
	}

	public static function fromByteString($byteStr, $fallback=null) {
		$matches = array();
		if (preg_match('/[0-9\.]+([kmgtpezy]b?|b)$/i', $byteStr, $matches)) {
			switch (strtolower($matches[1])) {
				case 'b' :
					$mul = 1;
					break;
				case 'k' :
				case 'kb' :
					$mul = 1024;
					break;
				case 'm' :
				case 'mb' :
					$mul = pow(1024, 2);
					break;
				case 'g' :
				case 'gb' :
					$mul = pow(1024, 3);
					break;
				case 't' :
				case 'tb' :
					$mul = pow(1024, 4);
					break;
				case 'p' :
				case 'pb' :
					$mul = pow(1024, 5);
					break;
				case 'e' :
				case 'eb' :
					$mul = pow(1024, 6);
					break;
				case 'z' :
				case 'zb' :
					$mul = pow(1024, 7);
					break;
				case 'y' :
				case 'yb' :
					$mul = pow(1024, 8);
					break;
			}
			return (str_replace($matches[1], '', $byteStr) * $mul);
		} elseif (is_numeric($byteStr)) {
			return $byteStr;
		}
		return $fallback;
	}

	public static function toByteString($size, $precision=2) {
		if (!is_numeric($size))
			$size = self::fromByteString($size);
		if (is_numeric($size)) {
	        $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	        for ($i=0; $size >= 1024 && $i < 9; $i++)
	            $size /= 1024;
	        return round($size, $precision) . $sizes[$i];
		}
		return null;
	}

	public static function buildPathInfo(array $params, $equal='=', $ampersand='&', $key=null) {
		$pairs = array();
		foreach ($params as $k => $v) {
			if ($v === null)
				continue;
			if ($key !== null)
				$k = $key . '[' . $k . ']';
			if (is_array($v))
				$pairs[] = self::buildPathInfo($v, $equal, $ampersand, $k);
			else
				$pairs[] = urlencode($k) . $equal . urlencode($v);
		}
		return implode($ampersand, $pairs);
	}

	public static function parsePathInfo($pathInfo) {
		if ($pathInfo === '')
			return array();
		$params = array();
		$segs = explode('/', $pathInfo . '/');
		$n = count($segs);
		for ($i=0; $i<$n-1; $i+=2) {
			$key = $segs[$i];
			if ($key === '')
				continue;
			$value = $segs[$i+1];
			if (($pos = strpos($key,'[')) !== false && ($pos2 = strpos($key, ']', $pos+1)) !== false) {
				$name = substr($key, 0, $pos);
				if ($pos2 === $pos+1) {
					$params[$name][] = $value;
				} else {
					$key = substr($key, $pos+1, $pos2-$pos-1);
					$params[$name][$key] = $value;
				}
			} else {
				$params[$key] = $value;
			}
		}
		return $params;
	}

	public static function buildMessage($msg, $params=array()) {
		if (!empty($params)) {
			if (Util::isMap($params)) {
				$indexArgs = array();
				foreach ($params as $k=>$v) {
					if (is_string($k))
						$msg = preg_replace('/\{' . preg_quote($k) . '\}/', $v, $msg);
					else
						$indexArgs[] = $v;
				}
				return (empty($indexArgs) ? $msg : vsprintf($msg, $indexArgs));
			}
			return vsprintf($msg, $params);
		}
		return $msg;
	}

	public static function evaluateExpression($expression, $data=array(), $context=null) {
		if (is_string($expression)) {
			extract($data);
			return eval('return (' . $expression . ');');
		} else {
			if ($context)
				$data[] = $context;
			return call_user_func_array($expression, $data);
		}
	}
}