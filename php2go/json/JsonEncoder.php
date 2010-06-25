<?php

class JsonEncoder
{
	private $options = array();
	private $visited = array();
	public static $classNameProp = '__className';
	
	protected function __construct(array $options=array()) {
		$this->options = $options;	
	}
	
	public static function encode($value, array $options=array()) {
		$encoder = new self($options);
		return $encoder->encodeValue($value);
	}
	
	protected function encodeValue($value) {
		if (is_object($value))
			return $this->encodeObject($value);
		if (is_array($value))
			return $this->encodeArray($value);
		return $this->encodeData($value);
	}
	
	protected function encodeObject(&$object) {
		if (!!$this->options['recursionCheck']) {
			if ($this->wasVisited($object)) {
				if (!!$this->options['silentRecursionException'])
					return '"* RECURSION (' . get_class($object) . ') *"';
				throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Recursion is not supported by JSON encoding. Recursion introduced by class "%s".', array(get_class($value))));
			}
			$this->visited[] = $object;
		}
		$props = '';
		if ($object instanceof Iterator)
			$collection = $object;
		else
			$collection = get_object_vars($object);
		foreach ($collection as $name => $value) {
			if (isset($value))
				$props .= ',' . $this->encodeValue($name) . ':' . $this->encodeValue($value);
		}
		return '{"' . self::$classNameProp . '":"' . get_class($object) . '"' . $props . '}';
	}
	
	protected function wasVisited(&$object) {
		return (in_array($object, $this->visited, true));
	}	
	
	protected function encodeArray(&$array) {
		$props = array();
		if (!empty($array) && Util::isMap($array)) {
			foreach ($array as $key => $value) {
				$key = (string)$key;
				$props[] = $this->encodeString($key) . ':' . $this->encodeValue($value);				
			}
			return '{' . implode(',', $props) . '}';
		} else {
			for ($i=0,$count=sizeof($array); $i<$count; $i++)
				$props[] = $this->encodeValue($array[$i]);
			return '[' . implode(',', $props) . ']';
		}
	}
	
	protected function encodeData(&$value) {
		if (is_int($value) || is_float($value)) {
			$value = (string)$value;
			return str_replace(',', '.', $value);
		} elseif (is_string($value)) {
			return $this->encodeString($value);
		} elseif (is_bool($value)) {
			return ($value ? 'true' : 'false');
		}
		return 'null';
	}
	
	protected function encodeString(&$string) {
        $search = array('\\', "\n", "\t", "\r", "\b", "\f", '"', '/');
        $replace = array('\\\\', '\\n', '\\t', '\\r', '\\b', '\\f', '\"', '\\/');
        $string = str_replace($search, $replace, $string);
        $string = str_replace(array(chr(0x08), chr(0x0C)), array('\b', '\f'), $string);
        $string = self::encodeUnicodeString($string);
        return '"' . $string . '"';		
	}
	
	public static function encodeUnicodeString($string) {
        $strlen_var = strlen($string);
        $ascii = '';
        for($i = 0; $i < $strlen_var; $i++) {
            $ord_var_c = ord($string[$i]);
            switch (true) {
                case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)) :
                    // characters U-00000000 - U-0000007F (same as ASCII)
                    $ascii .= $string[$i];
                    break;
                case (($ord_var_c & 0xE0) == 0xC0) :
                    // characters U-00000080 - U-000007FF, mask 110XXXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c, ord($string[$i + 1]));
                    $i += 1;
                    $utf16 = self::_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;
                case (($ord_var_c & 0xF0) == 0xE0) :
                    // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($string[$i + 1]),
                                 ord($string[$i + 2]));
                    $i += 2;
                    $utf16 = self::utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;
                case (($ord_var_c & 0xF8) == 0xF0) :
                    // characters U-00010000 - U-001FFFFF, mask 11110XXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($string[$i + 1]),
                                 ord($string[$i + 2]),
                                 ord($string[$i + 3]));
                    $i += 3;
                    $utf16 = self::utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;
                case (($ord_var_c & 0xFC) == 0xF8) :
                    // characters U-00200000 - U-03FFFFFF, mask 111110XX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($string[$i + 1]),
                                 ord($string[$i + 2]),
                                 ord($string[$i + 3]),
                                 ord($string[$i + 4]));
                    $i += 4;
                    $utf16 = self::utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;
                case (($ord_var_c & 0xFE) == 0xFC) :
                    // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($string[$i + 1]),
                                 ord($string[$i + 2]),
                                 ord($string[$i + 3]),
                                 ord($string[$i + 4]),
                                 ord($string[$i + 5]));
                    $i += 5;
                    $utf16 = self::utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;
            }
        }
        return $ascii;		
	}
	
	protected static function utf82utf16($utf8) {
        if (function_exists('mb_convert_encoding'))
            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        switch (strlen($utf8)) {
            case 1:
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return $utf8;
            case 2:
                // return a UTF-16 character from a 2-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x07 & (ord($utf8{0}) >> 2)) . chr((0xC0 & (ord($utf8{0}) << 6)) | (0x3F & ord($utf8{1})));
            case 3:
                // return a UTF-16 character from a 3-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr((0xF0 & (ord($utf8{0}) << 4)) | (0x0F & (ord($utf8{1}) >> 2))) . chr((0xC0 & (ord($utf8{1}) << 6)) | (0x7F & ord($utf8{2})));
        }
        return '';		
	}
}