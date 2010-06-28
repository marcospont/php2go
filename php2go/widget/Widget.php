<?php

abstract class Widget extends ViewHelper
{
	private static $viewPaths = array();
	protected $parent;
	protected $params = array();
	protected $jsEvents = array();
	protected $jsListeners = array();

	public function __construct(View $view, Widget $parent=null) {
		parent::__construct($view);
		$this->parent = $parent;
		$this->preInit();
	}

	public function __get($name) {
		if (($value = $this->view->__get($name)))
			return $value;
		return parent::__get($name);
	}

	public function __isset($name) {
		if ($this->view->__isset($name))
			return true;
		return parent::__isset($name);
	}

	public function __call($name, $args) {
		return call_user_func_array(array($this->view, $name), $args);
	}

	public function getController() {
		return $this->view->getController();
	}

	public function getParent() {
		return $this->parent;
	}

	public function getViewPath() {
		$className = get_class($this);
		if (!isset(self::$viewPaths[$className])) {
			$class = new ReflectionClass($className);
			self::$viewPaths[$className] = dirname($class->getFileName()) . DS . 'views';
		}
		return self::$viewPaths[$className];
	}

	public function addJsListener($evtName, $callback) {
		if (!isset($this->jsEvents[$evtName]))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Javascript event "%s.%s" is not defined.', array(get_class($this), $evtName)));
		$this->jsListeners[$evtName] = Js::callback($callback, $this->jsEvents[$evtName]);
		return $this;
	}

	public function clearJsListeners($evtName=null) {
		if ($evtName !== null)
			unset($this->jsListeners[$evtName]);
		else
			$this->jsListeners = array();
		return $this;
	}

	public function preInit() {
		$this->params = $this->getDefaultParams();
	}

	public function init() {
	}

	public function run() {
	}

	public function renderInternal($file) {
		include $file;
	}

	protected function render($viewName, $data=null, $return=false) {
		if (($viewFile = $this->getViewFile($viewName)) !== false) {
			$output = $this->view->renderFile($viewFile, $data);
			if ($return)
				return $output;
			echo $output . PHP_EOL;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The view "%s" is not available for the widget "%s".', array($viewName, get_class($this))));
		}
	}

	protected function getViewFile($viewName) {
		$extension = '.php';
		if (strpos($viewName, '.'))
			$viewFile = Php2Go::getPathAlias($viewName) . $extension;
		else
			$viewFile = $this->getViewPath() . DS . $viewName . $extension;
		return (is_file($viewFile) ? $this->view->app->getTranslator()->translatePath($viewFile) : false);
	}

	protected function getDefaultParams() {
		return array();
	}

	protected function registerJsEvents($evts) {
		$evts = (is_string($evts) ? preg_split('/[\s,]+/', $attrs, -1, PREG_SPLIT_NO_EMPTY) : $evts);
		if (Util::isMap($evts)) {
			$this->jsEvents = array_merge($this->jsEvents, $evts);
		} elseif (is_array($evts)) {
			foreach ($evts as $evtName)
				$this->jsEvents[$evtName] = array();
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid Javascript events specification.'));
		}
	}
}