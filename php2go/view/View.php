<?php

class View extends Component
{
	protected $app;
	protected $controller;
	protected $layout;
	protected $helperClasses = array();
	protected $helperConfig = array();
	protected $helpers = array();
	protected $widgetStack = array();
	protected $widget;
	protected $event;
	private $__context;

	public function __construct() {
		$this->app = Php2Go::app();
		$this->controller = $this->app->getController();
		$this->addHelperPath('php2go.view.helper');
		$this->setHelpers($this->app->getOption('viewHelpers', array()));
		$this->addWidgetPath('php2go.widget');
		$this->addWidgetPath('php2go.widget.jui');
		if ($this->app->csrfValidation) {
			$this->head()->addLibrary('php2go');
			$this->scriptBuffer()->add('php2go.csrfInit("' . Csrf::getTokenName() . '", "' . Csrf::getToken() . '");', 'domReady');
		}
	}

	public function __get($name) {
		if ($this->__context && isset($this->__context->{$name}))
			return $this->__context->{$name};
		return parent::__get($name);
	}

	public function __isset($name) {
		if ($this->__context && isset($this->__context->{$name}))
			return true;
		return parent::__isset($name);
	}

	public function __call($name, $args) {
		$helper = $this->resolveHelper($name, $method);
		if ($helper) {
			if (empty($method)) {
				if (method_exists($helper, $name))
					return call_user_func_array(array($helper, $name), $args);
				return $helper;
			}
			return call_user_func_array(array($helper, $method), $args);
		}
		return parent::__call($name, $args);
	}

	public function getApp() {
		return $this->app;
	}

	public function getController() {
		return $this->controller;
	}

	public function getLayout() {
		return $this->layout;
	}

	public function getRequest() {
		return $this->app->getRequest();
	}

	public function getUser() {
		return $this->app->getAuthenticator()->getUser();
	}

	public function disableLayout() {
		$this->layout = false;
		return $this;
	}

	public function setLayout($layout) {
		$this->layout = $layout;
		return $this;
	}

	public function setHelperPaths(array $paths) {

		foreach ($paths as $prefix => $alias)
			if (is_int($prefix))
				$this->addHelperPath($alias);
			else
				$this->addHelperPath($alias, $prefix);
		return $this;
	}

	public function addHelperPath($alias, $prefix='ViewHelper') {
		if (($path = Php2Go::getPathAlias($alias)) === false || !is_dir($path))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The alias "%s" is not a valid directory.', array($alias)));
		$found = false;
		$length = strlen($prefix);
		$it = new DirectoryIterator($path);
		foreach ($it as $file) {
			if ($file->isFile()) {
				$fileName = $file->getFilename();
				if (($pos = strpos($fileName, '.')) !== false && strpos($fileName, $prefix) === 0) {
					$className = substr($fileName, 0, $pos);
					if ($className != $prefix) {
						$found = true;
						$helperId = substr($fileName, $length, $pos-$length);
						$helperId[0] = strtolower($helperId[0]);
						$this->helperClasses[$helperId] = $className;
					}
				}
			}
		}
		($found) && (Php2Go::addIncludePath($path));
		return $this;
	}

	public function setHelpers(array $helpers) {
		foreach ($helpers as $name => $options)
			$this->setHelper($name, $options);
		return $this;
	}

	public function setHelper($name, $options) {
		if (is_string($name) && is_array($options)) {
			if (isset($this->helperConfig[$name]))
				$this->helperConfig[$name] = array_merge($this->helperConfig[$name], $options);
			else
				$this->helperConfig[$name] = $options;
			return $this;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid helper options.'));
		}
	}

	public function setWidgetPaths(array $paths) {
		foreach ($paths as $alias)
			$this->addWidgetPath($alias);
		return $this;
	}

	public function addWidgetPath($alias) {
		if (($path = Php2Go::getPathAlias($alias)) === false || !is_dir($path))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The alias "%s" is not a valid directory.', array($alias)));
		Php2Go::import($alias . '.*');
		return $this;
	}

	public function escape($value) {
		return htmlspecialchars($value, ENT_QUOTES, Php2Go::app()->getCharset());
	}

	public function ifEmpty($value, $fallback) {
		return (empty($value) ? $fallback : $value);
	}

	public function url($url=null, array $params=array(), $absolute=false, $ampersand='&') {
		if (is_array($url)) {
			$tmp = (isset($url[0]) ? array_shift($url) : '');
			$params = $url;
			$url = $tmp;
		} elseif (strpos($url, '://') !== false || strpos($url, 'javascript:') === 0 || strpos($url, 'mailto:') === 0 || strpos($url, '#') === 0) {
			return $url;
		} elseif (is_file($this->app->getRootPath() . DS . ltrim($url, '/'))) {
			return $this->app->getBaseUrl() . '/' . ltrim($url, '/');
		} elseif (@strpos($url, $this->app->getBaseUrl()) === 0) {
			return $url;
		}
		if ($absolute)
			return $this->controller->createAbsoluteUrl($url, $params, $ampersand);
		else
			return $this->controller->createUrl($url, $params, false, $ampersand);
	}

	public function render($viewName, $data=null) {
		if (($viewFile = $this->getViewFile($viewName)) !== false) {
			$output = $this->renderFile($viewFile, $data);
			if (($layoutFile = $this->getLayoutFile()))
				$output = $this->renderFile($layoutFile, array('content' => $output . PHP_EOL));
			return $output;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The view "%s" is not available for the controller "%s".', array($viewName, $this->controller->getId())));
		}
	}

	public function renderText($text) {
		if (($layoutFile = $this->getLayoutFile()))
			$text = $this->renderFile($layoutFile, array('content' => $text . PHP_EOL));
		return $text;
	}

	public function renderPartial($viewName, $data=null) {
		if (($viewFile = $this->getViewFile($viewName)) !== false) {
			$output = $this->renderFile($viewFile, $data);
			return $output;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The view "%s" is not available for the controller "%s".', array($viewName, $this->controller->getId())));
		}
	}

	public function renderFile($file, $data=null) {
		$widgetCount = count($this->widgetStack);
		// save context
		$context = $this->__context;
		$this->__context = ($data ? (object)$data : $data);
		// start output buffering
		ob_start();
		ob_implicit_flush(false);
		// use widget's or internal context
		if ($this->widget)
			$this->widget->renderInternal($file);
		else
			$this->renderInternal($file);
		// restore context
		$this->__context = $context;
		$content = ob_get_clean();
		if (count($this->widgetStack) == $widgetCount)
			return $content;
		$widget = end($this->widgetStack);
		throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Missing call to endWidget() for "%s" widget.', array(get_class($widget))));
	}

	public function beginScript($type=ViewHelperScriptBuffer::INLINE) {
		$this->scriptBuffer()->begin($type);
	}

	public function endScript() {
		try {
			return $this->scriptBuffer()->end();
		} catch (Exception $e) {
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'endScript() needs to be called after a call to beginScript().'));
		}
	}

	public function beginPlaceholder($id, $type=ViewHelperPlaceholder::APPEND, $key=null) {
		return $this->placeholder()->begin($id, $type, $key);
	}

	public function endPlaceholder() {
		return $this->placeholder()->end();
	}

	public function beginCache($id, array $criteria=array()) {
		return $this->cache()->begin($id, $criteria);
	}

	public function endCache($lifetime=false) {
		try {
			return $this->cache()->end($lifetime);
		} catch (Exception $e) {
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'endCache() needs to be called only if beginCache() returns true.'));
		}
	}

	public function createWidget($class, array $options=array()) {
		$parent = (($i = sizeof($this->widgetStack)) ? $this->widgetStack[$i-1] : null);
		return Php2Go::newInstance(array(
			'class' => $class,
			'parent' => 'Widget',
			'options' => $options
		), $this, $parent);
	}

	public function beginWidget($class, array $options=array()) {
		$this->widget = $this->createWidget($class, $options);
		$this->widgetStack[] = $this->widget;
		return $this->widget;
	}

	public function endWidget() {
		if (($this->widget = array_pop($this->widgetStack)) !== null) {
			echo $this->widget->run();
		} else {
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Unbalanced endWidget() call.'));
		}
	}

	public function widget($class, array $options=array()) {
		$this->widget = $this->createWidget($class, $options);
		$this->widgetStack[] = $this->widget;
		$content = $this->widget->run();
		$this->widget = array_pop($this->widgetStack);
		return $content;
	}

	public function beginEvent($evtName) {
		if ($this->widget === null)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'An event needs to be declared inside a widget.'));
		$this->event = $evtName;
		ob_start();
		ob_implicit_flush(false);
	}

	public function endEvent() {
		if ($this->event === null)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Unbalanced endEvent() call.'));
		$this->widget->addJsListener($this->event, ob_get_clean());
		$this->event = null;
	}

	private function renderInternal($file) {
		include $file;
	}

	private function getViewFile($viewName) {
		$controllerPath = $this->controller->getViewPath();
		$module = $this->controller->getModule();
		$ownerPath = ($module ? $module->getViewPath() : $this->app->getViewPath());
		return $this->resolveViewFile($viewName, $controllerPath, $ownerPath);
	}

	private function getLayoutFile() {
		$owner = $this->app;
		if ($this->layout === null) {
			// controller default layout
			$this->layout = $this->controller->getDefaultLayout();
			if ($this->layout === null) {
				// module default layout
				if (($module = $this->controller->getModule()) !== null)
					$this->layout = $module->getDefaultLayout();
				if ($this->layout !== null) {
					$owner = $module;
				} else {
					// app default layout
					$this->layout = $this->app->getDefaultLayout();
				}
			}
		}
		// disabled layout
		if ($this->layout === false)
			return false;
		return $this->resolveViewFile($this->layout, $owner->getLayoutPath(), $owner->getViewPath());
	}

	private function resolveViewFile($viewName, $viewPath, $basePath) {
		if (!empty($viewName)) {
			$extension = '.php';
			if ($viewName[0] == '/')
				$viewFile = $basePath . $viewName . $extension;
			elseif (strpos($viewName, '.'))
				$viewFile = Php2Go::getPathAlias($viewName) . $extension;
			else
				$viewFile = $viewPath . DS . $viewName . $extension;
			if (is_file($viewFile))
				return $this->app->getTranslator()->translatePath($viewFile);
		}
		return false;
	}

	private function resolveHelper(&$name, &$method) {
		foreach (array_keys($this->helperClasses) as $prefix) {
			if ($name == $prefix) {
				if (!isset($this->helpers[$prefix])) {
					$this->helpers[$prefix] = Php2Go::newInstance(array(
						'class' => $this->helperClasses[$prefix],
						'parent' => 'ViewHelper',
						'options' => (isset($this->helperConfig[$prefix]) ? $this->helperConfig[$prefix] : array())
					), $this);
				}
				$method = '';
				return $this->helpers[$prefix];
			} elseif (strpos($name, $prefix) === 0) {
				if (!isset($this->helpers[$prefix])) {
					$this->helpers[$prefix] = Php2Go::newInstance(array(
						'class' => $this->helperClasses[$prefix],
						'parent' => 'ViewHelper',
						'options' => (isset($this->helperConfig[$prefix]) ? $this->helperConfig[$prefix] : array())
					), $this);
				}
				$method = substr($name, strlen($prefix));
				$name = $prefix;
				return $this->helpers[$prefix];
			}
		}
		return false;
	}
}