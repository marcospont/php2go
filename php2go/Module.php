<?php

abstract class Module extends Component
{
	protected $id;
	protected $basePath;
	protected $componentClasses = array();
	protected $componentOptions = array();
	protected $components = array();
	public $options = array();

	public function __construct($id, array $options=array()) {
		$this->id = $id;
		$this->parseOptions($options);
		$this->options = $options;
	}

	public function getOptions() {
		return $this->options;
	}

	public function getOption($path, $default=null) {
		return Util::findArrayPath($this->options, $path, '.', $default);
	}

	public function setOption($path, $value) {
		Util::setArrayPath($this->options, $path, $value, '.');
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getBasePath() {
		if ($this->basePath === null) {
			$class = new ReflectionClass(get_class($this));
			$this->basePath = dirname($class->getFileName());
		}
		return $this->basePath;
	}

	public function setBasePath($path) {
		$path = rtrim($path, '/\\');
		if (($this->basePath = realpath($path)) === false || !is_dir($this->basePath))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Base path "%s" is not a valid directory.', array($path)));
	}

	public function setAliases($aliases) {
		if (is_array($aliases)) {
			foreach ($aliases as $alias => $path) {
				if (($existent = Php2Go::getPathAlias($path)) !== false)
					Php2Go::setPathAlias($alias, $existent);
				else
					Php2Go::setPathAlias($alias, $path);
			}
		}
	}

	public function setImports($imports) {
		if (is_array($imports)) {
			foreach ($imports as $alias)
				Php2Go::import($alias);
		}
	}

	public function getComponents() {
		return $this->components;
	}

	public function getComponent($id) {
		if (!isset($this->components[$id]))
			$this->components[$id] = $this->createComponent($id);
		return $this->components[$id];
	}

	public function createComponent($id) {
		$class = (isset($this->componentClasses[$id]) ? $this->componentClasses[$id] : null);
		if (is_array($class))
			$config['factory'] = $class;
		elseif (is_string($class))
			$config['class'] = $class;
		else
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" component is not registered.', array($id)));
		$config['options'] = Util::consumeArray($this->componentOptions, $id, array());
		return Php2Go::newInstance($config);
	}

	public function setComponents(array $components) {
		foreach ($components as $id => $options)
			$this->setComponent($id, $options);
	}

	public function setComponent($id, $options) {
		if (!isset($this->components[$id])) {
			if (is_string($options)) {
				$this->componentClasses[$id] = $options;
			} elseif (is_array($options) && isset($options['class'])) {
				$this->componentClasses[$id] = Util::consumeArray($options, 'class');
				if (!empty($options))
					$this->componentOptions[$id] = $options;
			} else {
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid component specification.'));
			}
		}
	}

	protected function parseOptions(array &$options) {
		foreach (array_keys($options) as $name) {
			switch ($name) {
				case 'id' :
				case 'basePath' :
				case 'aliases' :
				case 'imports' :
				case 'components' :
					$method = 'set' . $name;
					$this->{$method}(Util::consumeArray($options, $name));
					break;
				case 'behaviors' :
					$this->attachBehaviors(Util::consumeArray($options, $name));
					break;
			}
		}
	}
}