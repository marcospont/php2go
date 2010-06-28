<?php

abstract class Application extends Module
{
	const DEFAULT_CHARSET = 'utf-8';
	const DEFAULT_LOCALE = 'en_US';
	const DEFAULT_BASEPATH = 'protected';
	const SOURCE_LANGUAGE = 'en_US';

	protected $name;
	protected $sourceLanguage = self::SOURCE_LANGUAGE;
	protected $locale;
	protected $charset = self::DEFAULT_CHARSET;
	protected $modulePath;
	protected $moduleClasses = array();
	protected $moduleOptions = array();
	protected $modules = array();
	protected $defaultComponents = array();
	private $ended = false;

	public function __construct(array $options) {
		Php2Go::app($this);
		$this->initDefaultComponents();
		$this->initHandlers();
		$this->registerEvents(array('onBeginRequest', 'onEndRequest', 'onException', 'onError', 'onMissingTranslation'));
		if (!isset($options['locale']))
			$this->setLocale(self::DEFAULT_LOCALE);
		if (!isset($options['basePath']))
			$this->setBasePath(self::DEFAULT_BASEPATH);
		$this->parseOptions($options);
		$this->options = $options;
	}

	public function getId() {
		if ($this->id === null)
			$this->id = sprintf('%x', crc32($this->getBasePath() . $this->getName()));
		return $this->id;
	}

	public function getBasePath() {
		return $this->basePath;
	}

	public function setBasePath($path) {
		$path = rtrim($path, '/\\');
		if (($this->basePath = realpath($path)) === false || !is_dir($this->basePath))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Application base path "%s" is not a valid directory.', array($path)));
		Php2Go::setPathAlias('application', $path);
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		if (!is_string($name) || empty($name))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Application name must be a non empty string.'));
		$this->name = $name;
	}

	public function getSourceLanguage() {
		return $this->sourceLanguage;
	}

	public function setSourceLanguage($language) {
		$this->sourceLanguage = $language;
	}

	public function getLocale() {
		return $this->locale;
	}

	public function setLocale($locale) {
		$this->locale = Locale::findLocale($locale);
	}

	public function getCharset() {
		return $this->charset;
	}

	public function setCharset($charset) {
		$this->charset = $charset;
	}

	public function getTimezone() {
		return DateTimeUtil::getTimezone();
	}

	public function setTimezone($timezone) {
		DateTimeUtil::setTimezone($timezone);
	}

	public function getModulePath() {
		if ($this->modulePath === null)
			$this->modulePath = $this->getBasePath() . DS . 'modules';
		return $this->modulePath;
	}

	public function setModulePath($path) {
		$path = rtrim($path, '/\\');
		if (($this->modulePath = realpath($path)) === false || !is_dir($this->modulePath))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Module path "%s" is not a valid directory.', array($path)));
	}

	public function getModules() {
		return $this->modules;
	}

	public function hasModule($id) {
		if (isset($this->modules[$id]))
			return true;
		if (isset($this->moduleOptions[$id])) {
			$disabled = Util::consumeArray($this->moduleOptions[$id], 'disabled', false);
			if ($disabled !== true)
				return true;
		}
		return false;
	}

	public function getModule($id) {
		if (isset($this->modules[$id]))
			return $this->modules[$id];
		$options = Util::consumeArray($this->moduleOptions, $id, array());
		$disabled = Util::consumeArray($options, 'disabled', false);
		if (!$disabled) {
			$parentClass = ($this instanceof WebApplication ? 'WebModule' : 'Module');
			if (isset($this->moduleClasses[$id])) {
				$class = $this->moduleClasses[$id];
				return $this->modules[$id] = Php2Go::newInstance(array(
					'class' => $class,
					'parent' => $parentClass
				), $id, $options);
			} else {
				$class = ucfirst($id) . 'Module';
				$file = $this->getModulePath() . DS . $id . DS . $class . '.php';
				if (is_file($file)) {
					require_once($file);
					return $this->modules[$id] = Php2Go::newInstance(array(
						'class' => $class,
						'parent' => $parentClass
					), $id, $options);
				}
			}
		}
		return null;
	}

	public function setModules(array $modules) {
		foreach ($modules as $id => $options)
			$this->setModule($id, $options);
	}

	public function setModule($id, $options) {
		if (!isset($this->modules[$id])) {
			if (is_array($options)) {
	 			if (isset($options['class']))
					$this->moduleClasses[$id] = Util::consumeArray($options, 'class');
				$this->moduleOptions[$id] = $options;
			} else {
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid module specification.'));
			}
		}
	}

	public function createComponent($id) {
		$class = (isset($this->componentClasses[$id]) ? $this->componentClasses[$id] : (isset($this->defaultComponents[$id]) ? $this->defaultComponents[$id] : null));
		if (is_array($class)) {
			$config['factory'] = $class;
		} elseif (is_string($class)) {
			$config['class'] = $class;
			$config['parent'] = (isset($this->defaultComponents[$id]) ? $this->defaultComponents[$id] : null);
		} else {
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" component is not registered.', array($id)));
		}
		$config['options'] = Util::consumeArray($this->componentOptions, $id, array());
		return Php2Go::newInstance($config);
	}

	public function setComponent($id, $options) {
		if (!isset($this->components[$id])) {
			if (is_string($options)) {
				if (isset($this->defaultComponents[$id]) && is_array($this->defaultComponents[$id]))
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" component can not be extended.', array($id)));
				$this->componentClasses[$id] = $options;
			} elseif (is_array($options)) {
				if (isset($options['class'])) {
					if (isset($this->defaultComponents[$id]) && is_array($this->defaultComponents[$id]))
						throw new Exception(__(PHP2GO_LANG_DOMAIN, 'The "%s" component can not be extended.', array($id)));
					$this->componentClasses[$id] = Util::consumeArray($options, 'class');
				} elseif (!isset($this->defaultComponents[$id])) {
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid component specification.'));
				}
				if (!empty($options))
					$this->componentOptions[$id] = $options;
			} else {
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid component specification.'));
			}
		}
	}

	public function getCache() {
		return $this->getComponent('cache');
	}

	public function getDb() {
		return $this->getComponent('db');
	}

	public function getErrorHandler() {
		return $this->getComponent('errorHandler');
	}

	public function getLogger() {
		return $this->getComponent('logger');
	}

	public function getTranslator() {
		return $this->getComponent('translator');
	}

	public function run() {
		$this->raiseEvent('onBeginRequest');
		$this->processRequest();
		if (!$this->ended) {
			$this->raiseEvent('onEndRequest');
			$this->ended = true;
		}
		$this->stop();
	}

	abstract function processRequest();

	public function stop($status=0) {
		if (!$this->ended) {
			$this->raiseEvent('onEndRequest');
			$this->ended = true;
		}
		exit($status);
	}

	public function handleException(Exception $exception) {
		restore_error_handler();
		restore_exception_handler();
		try {
			$event = new ExceptionEvent($this, $exception);
			$this->raiseEvent('onException', $event);
			if (!$event->handled) {
				try {
					$handler = $this->getErrorHandler();
					$handler->handle($event);
				} catch (Exception $e) {
					$this->displayException($exception);
				}
			}
		} catch (Exception $e) {
			$this->displayException($e);
		}
		$this->stop(1);
	}

	public function handleError($code, $message, $file, $line) {
		if ($code && error_reporting()) {
			restore_error_handler();
			restore_exception_handler();
			try {
				$event = new ErrorEvent($this, $code, $message, $file, $line);
				$this->raiseEvent('onError', $event);
				if (!$event->handled) {
					try {
						$handler = $this->getErrorHandler();
						$handler->handle($event);
					} catch (Exception $e) {
						$this->displayError($code, $message, $file, $line);
					}
				}
			} catch (Exception $e) {
				$this->displayException($e);
			}
			$this->stop(1);
		}
	}

	protected function displayException(Exception $exception) {
		if (PHP2GO_DEBUG_MODE) {
			echo '<h1>' . get_class($exception) . "</h1>\n";
			echo '<p>' . $exception->getMessage() . '(' . $exception->getFile() . ':' . $exception->getLine() . ")</p>\n";
			echo '<pre>' . $exception->getTraceAsString() . '</pre>';
		} else {
			echo '<h1>' . get_class($exception) . "</h1>\n";
			echo '<p>' . $exception->getMessage() . "</p>\n";
		}
	}

	protected function displayError($code, $message, $file, $line) {
		if (PHP2GO_DEBUG_MODE) {
			echo "<h1>PHP Error [{$code}]</h1>\n";
			echo "<p>{$message} ({$file}:{$line})</p>\n";
			echo '<pre>';
			debug_print_backtrace();
			echo '</pre>';
		} else {
			echo "<h1>PHP Error [{$code}]</h1>\n";
			echo "<p>{$message}</p>\n";
		}
	}

	protected function parseOptions(array &$options) {
		parent::parseOptions($options);
		foreach (array_keys($options) as $name) {
			switch ($name) {
				case 'name' :
				case 'locale' :
				case 'charset' :
				case 'timezone' :
				case 'modulePath' :
				case 'modules' :
					$method = 'set' . $name;
					$this->{$method}(Util::consumeArray($options, $name));
					break;
			}
		}
	}

	protected function initDefaultComponents() {
		$this->defaultComponents = array(
			'cache' => 'Cache',
			'dao' => 'DAO',
			'db' => array('Db', 'factory'),
			'errorHandler' => 'ErrorHandler',
			'logger' => 'Logger',
			'translator' => 'Translator'
		);
	}

	private function initHandlers() {
		if (PHP2GO_CAPTURE_ERRORS)
			set_error_handler(array($this, 'handleError'), error_reporting());
		if (PHP2GO_CAPTURE_EXCEPTIONS)
			set_exception_handler(array($this, 'handleException'));
	}
}