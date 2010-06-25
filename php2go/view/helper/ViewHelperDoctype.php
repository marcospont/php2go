<?php

class ViewHelperDoctype extends ViewHelper
{
    const XHTML11 = 'XHTML11';
    const XHTML1_STRICT = 'XHTML1_STRICT';
    const XHTML1_TRANSITIONAL = 'XHTML1_TRANSITIONAL';
    const XHTML1_FRAMESET = 'XHTML1_FRAMESET';
    const XHTML_BASIC1 = 'XHTML_BASIC1';
    const XHTML5 = 'XHTML5';
    const HTML4_STRICT = 'HTML4_STRICT';
    const HTML4_LOOSE = 'HTML4_LOOSE';
    const HTML4_FRAMESET = 'HTML4_FRAMESET';
    const HTML5 = 'HTML5';
    const CUSTOM_XHTML = 'CUSTOM_XHTML';
    const CUSTOM = 'CUSTOM';
	
	protected static $types = array(
        self::XHTML11 => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
        self::XHTML1_STRICT => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        self::XHTML1_TRANSITIONAL => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        self::XHTML1_FRAMESET => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        self::XHTML_BASIC1 => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">',
        self::XHTML5 => '<!DOCTYPE html>',
        self::HTML4_STRICT => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        self::HTML4_LOOSE => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        self::HTML4_FRAMESET => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
        self::HTML5 => '<!DOCTYPE html>'
	);
	protected $defaultType = self::HTML4_LOOSE;
	protected $type;
	
	public function __construct(View $view) {
		parent::__construct($view);
		$this->setType($this->defaultType);
	}	
	
	public function doctype($type=null) {
		if ($type)
			$this->setType($type);
		return $this;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function setType($type) {
		if (isset(self::$types[$type])) {
			$this->type = $type;
		} else {
			if (substr($type, 0, 9) != '<!DOCTYPE')
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The given doctype is malformed: "%s"', array($doctype)));
			if (stristr($type, 'xhtml'))
				$customType = self::CUSTOM_XHTML;
			else
				$customType = self::CUSTOM;
			self::$types[$customType] = $type;
			$this->type = $customType;
		}
	}
	
	public function isXhtml() {
		return (stristr(self::$types[$this->type], 'xhtml') ? true : false);
	}
	
	public function isHtml4() {
		return (stristr(self::$types[$this->type], 'html4') ? true : false);
	}
	
	public function isHtml5() {
		return (stristr(self::$types[$this->type], '<!DOCTYPE html>') ? true : false);
	}
	
	public function toString() {
		return self::$types[$this->type] . PHP_EOL;
	}
}