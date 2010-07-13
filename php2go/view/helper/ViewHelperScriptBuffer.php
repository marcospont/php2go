<?php

class ViewHelperScriptBuffer extends ViewHelper
{
	const INLINE = 'inline';
	const ONLOAD = 'onLoad';
	const DOMREADY = 'domReady';

	protected $scripts = array();
	protected $captureLock = false;
	protected $captureType;

	public function __construct(View $view) {
		parent::__construct($view);
		$this->registerEvents(array('onBeforeRender'));
	}

	public function add($script, $type=self::INLINE) {
		$this->append($script, $type);
	}

	public function begin($type=self::INLINE) {
		if ($this->captureLock)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Script buffer captures cannot be nested.'));
		$this->captureLock = true;
		$this->captureType = $type;
		ob_start();
	}

	public function end() {
		if (!$this->captureLock)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Script buffer was not started using "start()".'));
		$type = $this->captureType;
		$this->captureLock = false;
		$this->captureType = null;
		$this->append(trim(ob_get_clean()), $type);
	}

	public function toString() {
		$jQueryOnLoad = $this->view->jQuery()->renderOnLoad();
		if (!empty($this->scripts) || !empty($jQueryOnLoad)) {
			$isXhtml = $this->view->doctypeIsXhtml();
			$escapeStart = ($isXhtml ? '//<![CDATA[' : '//<!--');
			$escapeEnd = ($isXhtml ? '//]]>' : '//-->');
			$html = '<script type="text/javascript">' . $escapeStart . PHP_EOL;
			if (isset($this->scripts[self::INLINE]))
				$html .= implode(PHP_EOL, $this->scripts[self::INLINE]) . PHP_EOL;
			if (isset($this->scripts[self::ONLOAD]))
				$html .= 'window.onload = function() {' . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $this->scripts[self::ONLOAD]) . PHP_EOL . '};' . PHP_EOL;
			$html .= $jQueryOnLoad;
			$html .= $escapeEnd . '</script>' . PHP_EOL;
			return $html;
		}
		return '';
	}

	private function append($script, $type) {
		if (!in_array($type, array(self::INLINE, self::ONLOAD, self::DOMREADY)))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid script buffer type: "%s"', array($type)));
		if (!isset($this->scripts[$type]))
			$this->scripts[$type] = array();
		if ($type == self::DOMREADY) {
			$this->view->jQuery()->addOnLoad($script);
		} else {
			$this->scripts[$type][] = $script;
		}
	}
}