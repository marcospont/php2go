<?php

class JsonDecoder
{
    const EOF = 0;
    const DATA = 1;
    const LBRACE = 2;
    const LBRACKET = 3;
    const RBRACE = 4;
    const RBRACKET = 5;
    const COMMA = 6;
    const COLON = 7;

    protected $value;
    protected $valueLength;
    protected $offset;
    protected $token;
    protected $tokenValue;
    protected $returnType;

    protected function __construct($source, $returnType=Json::RETURN_ARRAY) {
        $this->value = self::decodeUnicodeString($source);
        $this->valueLength = strlen($this->value);
        $this->token = self::EOF;
        $this->offset = 0;
        if (!in_array($returnType, array(Json::RETURN_ARRAY, Json::RETURN_OBJECT)))
            $returnType = Json::RETURN_ARRAY;
        $this->returnType = $returnType;
        $this->getNextToken();
    }

    public static function decode($value, $returnType=Json::RETURN_ARRAY) {
        if ($value === null || !is_string($value))
        	throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - input is not a string.'));
        $decoder = new self($value, $returnType);
        return $decoder->decodeValue();
    }
 
    protected function decodeValue() {
        switch ($this->token) {
            case self::DATA :
                $result = $this->tokenValue;
                $this->getNextToken();
                return $result;
                break;
            case self::LBRACE :
                return $this->decodeObject();
                break;
            case self::LBRACKET :
                return $this->decodeArray();
                break;
            default :
                return null;
                break;
        }
    }

    protected function decodeObject() {
        $members = array();
        $tok = $this->getNextToken();
        while ($tok && $tok != self::RBRACE) {
            if ($tok != self::DATA || ! is_string($this->tokenValue))
            	throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - missing key in object encoding.'));
            $key = $this->tokenValue;
            $tok = $this->getNextToken();
            if ($tok != self::COLON)
            	throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - missing ":" in object encoding.'));
            $tok = $this->getNextToken();
            $members[$key] = $this->decodeValue();
        	
            $tok = $this->token;
            if ($tok == self::RBRACE)
                break;
            if ($tok != self::COMMA)
            	throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - missing "," in object encoding.'));
            $tok = $this->getNextToken();
        }
        switch ($this->returnType) {
            case Json::RETURN_OBJECT :
                $result = new StdClass();
                foreach ($members as $key => $value)
                    $result->$key = $value;
                break;
            case Json::RETURN_ARRAY :
            default :
                $result = $members;
                break;
        }
        $this->getNextToken();
        return $result;
    }

    protected function decodeArray() {
        $result = array();
        $starttok = $tok = $this->getNextToken();
        $index = 0;
        while ($tok && $tok != self::RBRACKET) {
            $result[$index++] = $this->decodeValue();
            $tok = $this->token;
            if ($tok == self::RBRACKET || !$tok)
                break;
            if ($tok != self::COMMA)
            	throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - missing "," in array encoding.'));
            $tok = $this->getNextToken();
        }

        $this->getNextToken();
        return($result);
    }

    protected function eatWhitespace() {
        if (preg_match('/([\t\b\f\n\r ])*/s', $this->value, $matches, PREG_OFFSET_CAPTURE, $this->offset) && $matches[0][1] == $this->offset)
			$this->offset += strlen($matches[0][0]);
    }


    protected function getNextToken() {
		$this->token = self::EOF;
		$this->tokenValue = null;
		$this->eatWhitespace();
		if ($this->offset >= $this->valueLength)
			return(self::EOF);
		$str = $this->value;
		$length = $this->valueLength;
		$i = $this->offset;
		$start = $i;
		switch ($str{$i}) {
			case '{' :
				$this->token = self::LBRACE;
				break;
			case '}' :
				$this->token = self::RBRACE;
				break;
			case '[' :
				$this->token = self::LBRACKET;
				break;
			case ']' :
				$this->token = self::RBRACKET;
				break;
			case ',' :
				$this->token = self::COMMA;
				break;
			case ':' :
				$this->token = self::COLON;
				break;
			case '"':
				$result = '';
				do {
					$i++;
					if ($i >= $length)
						break;		
					$chr = $str{$i};		
					if ($chr == '\\') {
						$i++;
						if ($i >= $length)
							break;
						$chr = $str{$i};
						switch ($chr) {
							case '"' :
								$result .= '"';
								break;
							case '\\' :
								$result .= '\\';
								break;
							case '/' :
								$result .= '/';
								break;
							case 'b' :
								$result .= chr(8);
								break;
							case 'f' :
								$result .= chr(12);
								break;
							case 'n' :
								$result .= chr(10);
								break;
							case 'r' :
								$result .= chr(13);
								break;
							case 't' :
								$result .= chr(9);
								break;
							case '\'' :
								$result .= '\'';
								break;
							default :
								throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - illegal escape sequence: "%s".', array($chr)));
						}
					} elseif ($chr == '"') {
						break;
					} else {
						$result .= $chr;
					}
				} while ($i < $length);		
				$this->token = self::DATA;
				$this->tokenValue = $result;
				break;
			case 't' :
				if (($i+ 3) < $length && substr($str, $start, 4) == "true")
					$this->token = self::DATA;
				$this->tokenValue = true;
				$i += 3;
				break;
			case 'f' :
				if (($i+ 4) < $length && substr($str, $start, 5) == "false")
					$this->token = self::DATA;
				$this->tokenValue = false;
				$i += 4;
				break;
			case 'n' :
				if (($i+ 3) < $length && substr($str, $start, 4) == "null")
					$this->token = self::DATA;
				$this->tokenValue = null;
				$i += 3;
				break;
		}		
		if ($this->token != self::EOF) {
			$this->offset = $i + 1;
			return $this->token;
		}		
		$chr = $str{$i};
		if ($chr == '-' || $chr == '.' || ($chr >= '0' && $chr <= '9')) {
			if (preg_match('/-?([0-9])*(\.[0-9]*)?((e|E)((-|\+)?)[0-9]+)?/s', $str, $matches, PREG_OFFSET_CAPTURE, $start) && $matches[0][1] == $start) {		
				$data = $matches[0][0];		
				if (is_numeric($data)) {
					if (preg_match('/^0\d+$/', $data)) {
						throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - octal notation not supported: %s.', array($data)));
					} else {
						$int = intval($data);
						$float = floatval($data);
						$this->tokenValue = ($int == $float ? $int : $float);
					}
				} else {
					throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - illegal number format: %s.', array($data)));
				}		
				$this->token = self::DATA;
				$this->offset = $start + strlen($data);
			}
		} else {
			throw new JsonException(__(PHP2GO_LANG_DOMAIN, 'Decode error - illegal token.'));
		}		
		return $this->token;
    }

    public static function decodeUnicodeString($string) {
        $delim = substr($string, 0, 1);
        $utf8 = '';
        $strlen_chrs = strlen($string);
        for($i = 0; $i < $strlen_chrs; $i++) {
            $substr_chrs_c_2 = substr($string, $i, 2);
            $ord_chrs_c = ord($string[$i]);
            switch (true) {
                case preg_match('/\\\u[0-9A-F]{4}/i', substr($string, $i, 6)) :
                    // single, escaped unicode character
                    $utf16 = chr(hexdec(substr($string, ($i + 2), 2))) . chr(hexdec(substr($string, ($i + 4), 2)));
                    $utf8 .= self::utf162utf8($utf16);
                    $i += 5;
                    break;
                case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F) :
                    $utf8 .= $string{$i};
                    break;
                case ($ord_chrs_c & 0xE0) == 0xC0 :
                    // characters U-00000080 - U-000007FF, mask 110XXXXX
                    //see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($string, $i, 2);
                    ++$i;
                    break;
                case ($ord_chrs_c & 0xF0) == 0xE0 :
                    // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($string, $i, 3);
                    $i += 2;
                    break;
                case ($ord_chrs_c & 0xF8) == 0xF0 :
                    // characters U-00010000 - U-001FFFFF, mask 11110XXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($string, $i, 4);
                    $i += 3;
                    break;
                case ($ord_chrs_c & 0xFC) == 0xF8 :
                    // characters U-00200000 - U-03FFFFFF, mask 111110XX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($string, $i, 5);
                    $i += 4;
                    break;
                case ($ord_chrs_c & 0xFE) == 0xFC :
                    // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $utf8 .= substr($string, $i, 6);
                    $i += 5;
                    break;
            }
        }
        return $utf8;
    }

    protected static function utf162utf8($utf16) {
        if (function_exists('mb_convert_encoding'))
            return mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
        $bytes = (ord($utf16{0}) << 8) | ord($utf16{1});
        switch (true) {
            case ((0x7F & $bytes) == $bytes):
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x7F & $bytes);
            case (0x07FF & $bytes) == $bytes:
                // return a 2-byte UTF-8 character
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xC0 | (($bytes >> 6) & 0x1F)) . chr(0x80 | ($bytes & 0x3F));
            case (0xFFFF & $bytes) == $bytes:
                // return a 3-byte UTF-8 character
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0xE0 | (($bytes >> 12) & 0x0F)) . chr(0x80 | (($bytes >> 6) & 0x3F)) . chr(0x80 | ($bytes & 0x3F));
        }
        return '';
    }	
}