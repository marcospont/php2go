<?php

abstract class LocaleNumber
{
	private static $regexes = array();

	public static function getInteger($value, $locale=null) {
		return intval(self::getNumber($value, $locale, 0));
	}

	public static function getFloat($value, $locale=null, $precision=null) {
		return floatval(self::getNumber($value, $locale, $precision));
	}

	public static function getNumber($value, $locale=null, $precision=null) {
        if (!is_string($value))
            return $value;
		if ($locale === null) {
			$locale = Php2Go::app()->getLocale();
		} elseif (Locale::isLocale($locale)) {
			$locale = ($locale instanceof Locale ? $locale : new Locale($locale));
		} elseif (is_int($locale) && $locale >= 0 && $locale <= 30) {
			$precision = $locale;
			$locale = Php2Go::app()->getLocale();
		}
        if (!self::isNumber($value, $locale))
        	throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid localized number for locale "%s".', array($locale)));
        $symbols = $locale->getNumberSymbols();
		if (!empty($value) && $value[0] == $symbols['decimal'])
			$value = '0' . $value;
        if ((strpos($value, $symbols['minusSign']) !== false) || (strpos($value, '-') !== false)) {
            $value = strtr($value, array($symbols['minusSign'] => '', '-' => ''));
            $value = '-' . $value;
        }
        $value = str_replace($symbols['group'], '', $value);
        if (strpos($value, $symbols['decimal']) !== false) {
            if ($symbols['decimal'] != '.')
                $value = str_replace($symbols['decimal'], '.', $value);
            $prec = substr($value, strpos($value, '.') + 1);
            if ($precision === null)
                $precision = strlen($prec);
            if (strlen($prec) >= $precision)
                $value = substr($value, 0, strlen($value) - strlen($prec) + $precision);
            if (($precision == 0) && ($value[strlen($value) - 1] == '.'))
                $value = substr($value, 0, -1);
        }
        return $value;
	}

    public static function isInteger($value, $locale=null) {
        if (!self::isNumber($value, $locale))
            return false;
        if (self::getInteger($value, $locale) == self::getFloat($value, $locale))
            return true;
        return false;
    }

    public static function isFloat($value, $locale=null) {
    	return self::isNumber($value, $locale=null);
    }

	public static function isNumber($value, $locale=null) {
		if ($locale === null)
			$locale = Php2Go::app()->getLocale();
		else
			$locale = Locale::findLocale($locale);
		$symbols = $locale->getNumberSymbols();
		$regexes = self::getNumberRegex('decimal', $locale);
		$regexes = array_merge($regexes, self::getNumberRegex('scientific', $locale));
		if (!empty($value) && $value[0] == $symbols['decimal'])
			$value = '0' . $value;
		$matches = array();
		foreach ($regexes as $regex) {
			if (preg_match($regex, $value, $matches)) {
				if (isset($matches[0]))
					return true;
			}
		}
		return false;
	}

	private static function getNumberRegex($type, Locale $locale) {
		if (isset(self::$regexes[(string)$locale][$type]))
			return self::$regexes[(string)$locale][$type];
		$regex = array();
		$symbols = $locale->getNumberSymbols();
		$format = $locale->{'get' . ucfirst($type) . 'Format'}();
		$format = preg_replace('/[^#0,;\.\-Ee]/u', '', $format);
		// detect negavite format
		$patterns = explode(';', $format);
		if (count($patterns) == 1)
			$patterns[1] = '-' . $patterns[0];
		foreach ($patterns as $i => $pattern) {
			$regex[$i] = '/^';
			$rest = 0;
			$end = null;
			// split decimal part
			if (($dot = strpos($pattern, '.')) !== false) {
				$end = substr($pattern, $dot+1);
				$pattern = substr($pattern, 0, $dot);
			}
			// groups
			if (strpos($pattern, ',') !== false) {
				$parts = explode(',', $pattern);
				$count = count($parts);
				foreach ($parts as $j => $part) {
					switch ($part) {
						case '#' :
						case '-#' :
							if ($part[0] == '-')
								$regex[$i] .= '[' . $symbols['minusSign'] . '-]{0,1}';
							else
								$regex[$i] .= '[' . $symbols['plusSign'] . '+]{0,1}';
							if ($parts[$j+1] == '##0')
								$regex[$i] .= '[0-9]{1,3}';
							elseif ($parts[$j+1] == '##')
								$regex[$i] .= '[0-9]{1,2}';
							else
								throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid numeric format.'));
							break;
						case '##' :
							if ($parts[$j+1] == '##0')
								$regex[$i] .= '(\\' . $symbols['group'] . '{0,1}[0-9]{2})*';
							else
								throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid numeric format.'));
							break;
						case '##0' :
							if ($parts[$j-1] == '##')
								$regex[$i] .= '[0-9]';
							elseif ($parts[$j-1] == '#' || $parts[$j-1] == '-#')
								$regex[$i] .= '(\\' . $symbols['group'] . '{0,1}[0-9]{3})*';
							else
								throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid numeric format.'));
							break;
						case '#0' :
							if ($j == 0)
								$regex[$i] .= '[0-9]*';
							else
								throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid numeric format.'));
							break;
						default :
							throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid numeric format.'));
					}
				}
			}
			// scientific number
			if (strpos($pattern, 'E') !== false) {
				if ($pattern == '#E0' || $pattern == '#E00')
					$regex[$i] .= '[' . $symbols['plusSign'] . '+]{0,1}[0-9]{1,}(\\' . $symbols['decimal'] . '[0-9]{1,})*[eE][' . $symbols['plusSign'] . '+]{0,1}[0-9]{1,}';
				elseif ($pattern == '-#E0' || $pattern == '-#E00')
					$regex[$i] .= '[' . $symbols['minusSign'] . '-]{0,1}[0-9]{1,}(\\' . $symbols['decimal'] . '[0-9]{1,})*[eE][' . $symbols['minusSign'] . '-]{0,1}[0-9]{1,}';
				else
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid numeric format.'));
			}
			// decimal part
			if (!empty($end)) {
				if ($end == '###')
					$regex[$i] .= '(\\' . $symbols['decimal'] . '{1}[0-9]{1,}){0,1}';
				elseif ($end == '###-')
					$regex[$i] .= '(\\' . $symbols['decimal'] . '{2}[0-9]{1,}){0,1}[' . $symbols['minusSign'] . '-]';
				else
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Invalid numeric format.'));
			}
			$regex[$i] .= '$/';
		}
		return (self::$regexes[(string)$locale][$type] = $regex);
	}
}