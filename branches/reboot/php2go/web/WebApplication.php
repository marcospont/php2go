<?php

class WebApplication extends Application
{
	protected $controllerPath;
	protected $layoutPath;
	protected $viewPath;
	protected $systemViewPath;
	protected $defaultLayout;
	protected $defaultController = 'site';
	protected $controllerClasses = array();
	protected $controller;
	protected $csrfValidation = array();
	private $rootUrl;
	private $libraries = array();

	public function __construct(array $options=array()) {
		parent::__construct($options);
		$this->libraries = array_merge(include(Php2Go::getPathAlias('php2go.library.libraries') . '.php'), $this->libraries);
		$this->registerEvents(array('onBeforeControllerAction', 'onAfterControllerAction'));
		if (@$this->csrfValidation['enabled'])
			$this->addEventListener('onBeginRequest', array('Csrf', 'validate'));
		$this->addEventListener('onBeforeControllerAction', array($this, 'checkPageCache'));
		$this->addEventListener('onException', array($this, 'cancelPageCache'));
		$this->addEventListener('onError', array($this, 'cancelPageCache'));
		$this->setIniVars();
		$this->initSession();
	}

	public function getControllerPath() {
		if ($this->controllerPath === null)
			$this->controllerPath = $this->getBasePath() . DS . 'controllers';
		return $this->controllerPath;
	}

	public function setControllerPath($path) {
		$path = rtrim($path, '/\\');
		if (($this->controllerPath = realpath($path)) === false || !is_dir($this->controllerPath))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Controller path "%s" is not a valid directory.', array($path)));
	}

	public function getLayoutPath() {
		if ($this->layoutPath === null)
			$this->layoutPath = $this->getBasePath() . DS . 'layouts';
		return $this->layoutPath;
	}

	public function setLayoutPath($path) {
		$path = rtrim($path, '/\\');
		if (($this->layoutPath = realpath($path)) === false || !is_dir($this->layoutPath))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Layout path "%s" is not a valid directory.', array($path)));
	}

	public function getViewPath() {
		if ($this->viewPath === null)
			$this->viewPath = $this->getBasePath() . DS . 'views';
		return $this->viewPath;
	}

	public function setViewPath($path) {
		$path = rtrim($path, '/\\');
		if (($this->viewPath = realpath($path)) === false || !is_dir($this->viewPath))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'View path "%s" is not a valid directory.', array($path)));
	}

	public function getSystemViewPath() {
		if ($this->systemViewPath === null)
			$this->systemViewPath = $this->getViewPath() . DS . 'system';
		return $this->systemViewPath;
	}

	public function setSystemViewPath($path) {
		$path = rtrim($path, '/\\');
		if (($this->systemViewPath = realpath($path)) === false || !is_dir($this->systemViewPath))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'System view path "%s" is not a valid directory.', array($path)));
	}

	public function getDefaultLayout() {
		return $this->defaultLayout;
	}

	public function setDefaultLayout($layout) {
		$this->defaultLayout = $layout;
	}

	public function getDefaultController() {
		return $this->defaultController;
	}

	public function setDefaultController($id) {
		$this->defaultController = $id;
	}

	public function getController() {
		return $this->controller;
	}

	public function getControllerClass($id) {
		return (isset($this->controllerClasses[$id]) ? $this->controllerClasses[$id] : null);
	}

	public function setControllerClasses(array $classes) {
		$this->controllerClasses = $classes;
	}

	public function getLibraries() {
		return $this->libraries;
	}

	public function getLibrary($name) {
		if (isset($this->libraries[$name])) {
			return $this->libraries[$name];
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The library "%s" does not exist.', array($name)));
		}
	}

	public function setLibraries($libraries) {
		if (is_array($libraries)) {
			foreach ($libraries as $name => $library) {
				if (array_key_exists($name, $this->libraries))
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The library "%s" already exists.', array($name)));
				if (!is_array($library) || !isset($library['files']))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid library specification: "%s".', array($name)));
				if (is_string($library['files']))
					$library['files'] = explode(',', $library['files']);
				$library['files'] = array_unique($library['files']);
				if (isset($library['dependencies'])) {
					if (is_string($library['dependencies']))
						$library['dependencies'] = explode(',', $library['dependencies']);
					$library['dependencies'] = array_unique($library['dependencies']);
				}
				$this->libraries[$name] = $library;
			}
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The configuration option "%s" must be an array.', array('libraries')));
		}
	}

	public function getCsrfValidation() {
		return $this->csrfValidation;
	}

	public function setCsrfValidation($validation) {
		if (is_bool($validation)) {
			$this->csrfValidation = array(
				'enabled' => $validation,
				'exceptions' => array()
			);
		} elseif (Util::isMap($validation) && isset($validation['enabled'])) {
			$this->csrfValidation = array(
				'enabled' => !!$validation['enabled'],
				'exceptions' => (is_array(@$validation['exceptions']) ? $validation['exceptions'] : array())
			);
		}
	}

	public function getAssetManager() {
		return $this->getComponent('assetManager');
	}

	public function getAuthenticator() {
		return $this->getComponent('authenticator');
	}

	public function getUser() {
		return $this->getComponent('authenticator')->getUser();
	}

	public function getRouter() {
		return $this->getComponent('router');
	}

	public function getRoute() {
		if ($this->controller)
			return $this->controller->getRoute();
		return null;
	}

	public function getRequest() {
		return $this->getComponent('request');
	}

	public function getResponse() {
		return $this->getComponent('response');
	}

	public function getSession() {
		return $this->getComponent('session');
	}

	public function getPageCache() {
		return $this->getComponent('pageCache');
	}

	public function createView() {
		return $this->createComponent('view');
	}

	public function getBaseUrl($absolute=false) {
		return $this->getRequest()->getBaseUrl($absolute);
	}

	public function getRootPath() {
		return $this->getRequest()->getScriptPath();
	}

	public function getRootUrl($absolute=false) {
		if (!$this->rootUrl) {
			$router = $this->getRouter();
			if ($router->showScriptFile)
				$this->rootUrl = $this->getRequest()->getScriptUrl($absolute);
			else
				$this->rootUrl = $this->getRequest()->getBaseUrl($absolute);
		}
		return $this->rootUrl;
	}

	public function createUrl($route, array $params=array(), $ampersand='&') {
		return $this->getRouter()->createUrl($route, $params, false, $ampersand);
	}

	public function createAbsoluteUrl($route, array $params=array(), $ampersand='&') {
		return $this->getRouter()->createUrl($route, $params, true, $ampersand);
	}

	public function processRequest() {
		$request = $this->getRequest();
		$response = $this->getResponse();
		$route = $this->getRouter()->parseUrl($request);
		$this->dispatch($route);
		$response->sendResponse();
	}

	public function dispatch($route) {
		if (($pair = $this->createController($route)) !== null) {
			list($controller, $actionId) = $pair;
			$oldController = $this->controller;
			$this->controller = $controller;
			$this->controller->init();
			$this->controller->run($actionId);
			$this->controller = $oldController;
		} else {
			$this->missingController($route);
		}
	}

	public function stop($status=0) {
		if (Session::isStarted())
			$this->getAuthenticator()->saveUser();
		parent::stop($status);
	}

	protected function checkPageCache() {
		return $this->getPageCache()->start();
	}

	protected function cancelPageCache() {
		$this->getPageCache()->cancel();
	}

	protected function parseOptions(array &$options) {
		parent::parseOptions($options);
		foreach (array_keys($options) as $name) {
			switch ($name) {
				case 'controllerPath' :
				case 'layoutPath' :
				case 'viewPath' :
				case 'systemViewPath' :
				case 'defaultLayout' :
				case 'defaultController' :
				case 'controllerClasses' :
				case 'libraries' :
				case 'csrfValidation';
					$method = 'set' . $name;
					$this->{$method}(Util::consumeArray($options, $name));
					break;
			}
		}
	}

	protected function initDefaultComponents() {
		parent::initDefaultComponents();
		$this->defaultComponents = Util::mergeArray($this->defaultComponents, array(
			'assetManager' => 'AssetManager',
			'authenticator' => 'Authenticator',
			'pageCache' => 'PageCache',
			'request' => 'Request',
			'response' => 'Response',
			'router' => 'Router',
			'session' => 'Session',
			'view' => 'View'
		));
	}

	private function createController($route, $owner=null) {
		if ($owner === null) {
			$owner = $this;
		}
		if (($route = trim($route, '/')) == '')
			$route = $owner->getDefaultController();
		$route .= '/';
		$pos = strpos($route, '/');
		$id = substr($route, 0, $pos);
		if (!preg_match('/^[a-z]\w*$/', $id))
			return null;
		$route = substr($route, $pos+1);
		// custom controller
		if (($class = $owner->getControllerClass($id))) {
			return array(
				Php2Go::newInstance(array(
					'class' => $class,
					'parent' => 'Controller'
				), $id, ($owner === Php2Go::app() ? null : $owner)),
				$this->parseParams($route)
			);
		}
		// module
		if ($owner === $this && ($module = $this->getModule($id))) {
			return $this->createController($route, $module);
		}
		// controller
		$basePath = $owner->getControllerPath();
		$class = ucfirst($id) . 'Controller';
		$file = $basePath . DS . $class . '.php';
		if (is_file($file)) {
			if (!class_exists($class, false))
				include_once $file;
			$id[0] = strtolower($id[0]);
			return array(
				Php2Go::newInstance(array(
					'class' => $class,
					'parent' => 'Controller'
				), $id, ($owner === Php2Go::app() ? null : $owner)),
				$this->parseParams($route)
			);
		}
		return null;
	}

	private function parseParams($route) {
		$route = rtrim($route, '/');
		if (($pos = strpos($route, '/')) !== false) {
			$params = Util::parsePathInfo((string)substr($route, $pos+1));
			foreach ($params as $name => $value)
				$_GET[$name] = $value;
			$route = substr($route, 0, $pos);
		}
		return $route;
	}

	private function missingController($route) {
		throw new HttpException(404, __(PHP2GO_LANG_DOMAIN, 'The system was enable to resolve the requested route "%s"', array($route)));
	}

	private function setIniVars() {
		ini_set('track_vars', 'On');
		ini_set('register_globals', 'Off');
		ini_set('register_long_arrays', 'Off');
		ini_set('magic_quotes_gpc', 'Off');
		ini_set('magic_quotes_runtime', 'Off');
	}

	private function initSession() {
		$options = Util::consumeArray($this->options, 'session');
		$name = (isset($options['name']) ? $options['name'] : session_name());
		$request = $this->getRequest();
		if ($request->isFlash()) {
			if (($id = $request->getPost($name)))
				$options['id'] = $id;
			if (($id = $request->getQuery($name)))
				$options['id'] = $id;
		}
		Session::start($options);
	}
}