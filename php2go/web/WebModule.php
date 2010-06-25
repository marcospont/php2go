<?php

class WebModule extends Module
{
	protected $app;
	protected $controllerPath;
	protected $layoutPath;
	protected $viewPath;
	protected $defaultLayout;	
	protected $defaultController = 'default';
	protected $controllerClasses = array();
	
	public function __construct($id, array $options=array()) {
		parent::__construct($id, $options);
		$this->registerEvents(array('onBeforeControllerAction', 'onAfterControllerAction'));
		$this->addEventListener('onBeforeControllerAction', array(Php2Go::app(), 'checkPageCache'));
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
	
	public function getControllerClass($id) {
		return (isset($this->controllerClasses[$id]) ? $this->controllerClasses[$id] : null);
	}
	
	public function setControllerClasses(array $classes) {
		$this->controllerClasses = $classes;
	}

	protected function parseOptions(array &$options) {
		parent::parseOptions($options);
		foreach (array_keys($options) as $name) {
			switch ($name) {
				case 'controllerPath' :
				case 'layoutPath' :
				case 'viewPath' :
				case 'defaultLayout' :
				case 'defaultController' :
				case 'controllerClasses' :
					$method = 'set' . $name;
					$this->{$method}(Util::consumeArray($options, $name));
					break;
			}
		}		
	}
}