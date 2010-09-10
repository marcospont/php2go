<?php

define('MINIMUM_PHP_VERSION', '5.2.0');
define('DS', DIRECTORY_SEPARATOR);
define('IS_WINDOWS', (substr(strtoupper(PHP_OS), 0, 3) == 'WIN'));
define('PHP2GO_LANG_DOMAIN', 'php2go');
define('PHP2GO_LOADED', true);
define('PHP2GO_PATH', dirname(__FILE__));
define('PHP2GO_VERSION', '1.0.0');
defined('PHP2GO_CAPTURE_ERRORS') or define('PHP2GO_CAPTURE_ERRORS', true);
defined('PHP2GO_CAPTURE_EXCEPTIONS') or define('PHP2GO_CAPTURE_EXCEPTIONS', true);
defined('PHP2GO_DEBUG_MODE') or define('PHP2GO_DEBUG_MODE', true);

final class Php2Go
{
	private static $app;
	private static $translator;
	private static $includePaths;
	private static $imports = array();
	private static $aliases = array(
		'php2go' => PHP2GO_PATH
	);
	private static $autoloaders = array(array('Php2Go', 'loadClass'));
	private static $classes = array();
	private static $coreClasses = array(
		'Application' => '/Application.php',
		'Component' => '/Component.php',
		'ErrorHandler' => '/ErrorHandler.php',
		'Module' => '/Module.php',
		'Action' => '/action/Action.php',
		'ActionInline' => '/action/ActionInline.php',
		'ActionInterface' => '/action/ActionInterface.php',
		'ActionFilter' => '/action/ActionFilter.php',
		'ActionFilterChain' => '/action/filter/ActionFilterChain.php',
		'ActionFilterInline' => '/action/filter/ActionFilterInline.php',
		'ActionFilterInterface' => '/action/filter/ActionFilterInterface.php',
		'ActiveRecord' => '/activeRecord/ActiveRecord.php',
		'ActiveRecordBehavior' => '/activeRecord/ActiveRecordBehavior.php',
		'ActiveRecordFormatter' => '/activeRecord/ActiveRecordFormatter.php',
		'ActiveRecordRelation' => '/activeRecord/ActiveRecordRelation.php',
		'ActiveRecordRelationCollection' => '/activeRecord/relation/ActiveRecordRelationCollection.php',
		'Authenticator' => '/auth/Authenticator.php',
		'AuthenticatorAdapter' => '/auth/AuthenticatorAdapter.php',
		'AuthorizationFilter' => '/auth/AuthorizationFilter.php',
		'UserContainer' => '/auth/UserContainer.php',
		'Behavior' => '/behavior/Behavior.php',
		'BehaviorInterface' => '/behavior/BehaviorInterface.php',
		'Cache' => '/cache/Cache.php',
		'CacheBackend' => '/cache/CacheBackend.php',
		'CacheProxy' => '/cache/CacheProxy.php',
		'ClassCache' => '/cache/ClassCache.php',
		'FunctionCache' => '/cache/FunctionCache.php',
		'OutputCache' => '/cache/OutputCache.php',
		'PageCache' => '/cache/PageCache.php',
		'Config' => '/config/Config.php',
		'ConsoleApplication' => '/console/ConsoleApplication.php',
		'ConsoleCommand' => '/console/ConsoleCommand.php',
		'ConsoleRunner' => '/console/ConsoleRunner.php',
		'Controller' => '/controller/Controller.php',
		'DateTimeFormatter' => '/datetime/DateTimeFormatter.php',
		'DateTimeParser' => '/datetime/DateTimeParser.php',
		'DateTimeUtil' => '/datetime/DateTimeUtil.php',
		'DAO' => '/db/DAO.php',
		'Db' => '/db/Db.php',
		'DbAdapter' => '/db/DbAdapter.php',
		'DbColumn' => '/db/DbColumn.php',
		'DbCommandBuilder' => '/db/DbCommandBuilder.php',
		'DbForeignKey' => '/db/DbForeignKey.php',
		'DbIndex' => '/db/DbIndex.php',
		'DbStatement' => '/db/DbStatement.php',
		'DbTable' => '/db/DbTable.php',
		'DirectoryUtil' => '/file/DirectoryUtil.php',
		'FileUtil' => '/file/FileUtil.php',
		'Filter' => '/filter/Filter.php',
		'FilterAlpha' => '/filter/FilterAlpha.php',
		'FilterAlphanum' => '/filter/FilterAlphanum.php',
		'FilterBoolean' => '/filter/FilterBoolean.php',
		'FilterCallback' => '/filter/FilterCallback.php',
		'FilterChain' => '/filter/FilterChain.php',
		'FilterDigits' => '/filter/FilterDigits.php',
		'FilterFloat' => '/filter/FilterFloat.php',
		'FilterHtmlEntities' => '/filter/FilterHtmlEntities.php',
		'FilterInteger' => '/filter/FilterInteger.php',
		'FilterInterface' => '/filter/FilterInterface.php',
		'FilterLower' => '/filter/FilterLower.php',
		'FilterNull' => '/filter/FilterNull.php',
		'FilterRegex' => '/filter/FilterRegex.php',
		'FilterUpper' => '/filter/FilterUpper.php',
		'FormModel' => '/form/FormModel.php',
		'Json' => '/json/Json.php',
		'JsonDecoder' => '/json/JsonDecoder.php',
		'JsonEncoder' => '/json/JsonEncoder.php',
		'Locale' => '/locale/Locale.php',
		'LocaleDate' => '/locale/LocaleDate.php',
		'LocaleDateTime' => '/locale/LocaleDateTime.php',
		'LocaleNumber' => '/locale/LocaleNumber.php',
		'LocaleNumberFormatter' => '/locale/LocaleNumberFormatter.php',
		'LocaleTime' => '/locale/LocaleTime.php',
		'Logger' => '/logger/Logger.php',
		'LoggerAppender' => '/logger/LoggerAppender.php',
		'LoggerEvent' => '/logger/LoggerEvent.php',
		'LoggerFilter' => '/logger/LoggerFilter.php',
		'LoggerFormatter' => '/logger/LoggerFormatter.php',
		'Model' => '/model/Model.php',
		'ModelBehavior' => '/model/ModelBehavior.php',
		'ModelValidator' => '/model/ModelValidator.php',
		'Navigation' => '/navigation/Navigation.php',
		'NavigationContainer' => '/navigation/NavigationContainer.php',
		'NavigationItem' => '/navigation/NavigationItem.php',
		'Paginator' => '/paginator/Paginator.php',
		'PaginatorAdapter' => '/paginator/PaginatorAdapter.php',
		'Router' => '/router/Router.php',
		'RouterRule' => '/router/RouterRule.php',
		'SearchModel' => '/search/SearchModel.php',
		'Translator' => '/translator/Translator.php',
		'TranslatorAdapter' => '/translator/TranslatorAdapter.php',
		'UploadFile' => '/upload/UploadFile.php',
		'UploadFileCollection' => '/upload/UploadFileCollection.php',
		'UploadManager' => '/upload/UploadManager.php',
		'HashMap' => '/util/HashMap.php',
		'Inflector' => '/util/Inflector.php',
		'Js' => '/util/Js.php',
		'System' => '/util/System.php',
		'Util' => '/util/Util.php',
		'Validator' => '/validator/Validator.php',
		'ValidatorChoice' => '/validator/ValidatorChoice.php',
		'ValidatorCnpj' => '/validator/ValidatorCnpj.php',
		'ValidatorComparison' => '/validator/ValidatorComparison.php',
		'ValidatorCpf' => '/validator/ValidatorCpf.php',
		'ValidatorDataType' => '/validator/ValidatorDataType.php',
		'ValidatorEmail' => '/validator/ValidatorEmail.php',
		'ValidatorInline' => '/validator/ValidatorInline.php',
		'ValidatorLength' => '/validator/ValidatorLength.php',
		'ValidatorNoWhitespace' => '/validator/ValidatorNoWhitespace.php',
		'ValidatorNumber' => '/validator/ValidatorNumber.php',
		'ValidatorRegex' => '/validator/ValidatorRegex.php',
		'ValidatorRequired' => '/validator/ValidatorRequired.php',
		'ValidatorUnique' => '/validator/ValidatorUnique.php',
		'ValidatorUpload' => '/validator/ValidatorUpload.php',
		'ValidatorUrl' => '/validator/ValidatorUrl.php',
		'ValidatorFileCount' => '/validator/file/ValidatorFileCount.php',
		'ValidatorFileExtension' => '/validator/file/ValidatorFileExtension.php',
		'ValidatorFileMimeType' => '/validator/file/ValidatorFileMimeType.php',
		'ValidatorFileSize' => '/validator/file/ValidatorFileSize.php',
		'ValidatorImageSize' => '/validator/image/ValidatorImageSize.php',
		'View' => '/view/View.php',
		'ViewHelper' => '/view/ViewHelper.php',
		'AssetManager' => '/web/AssetManager.php',
		'Cookie' => '/web/Cookie.php',
		'CookieCollection' => '/web/CookieCollection.php',
		'Csrf' => '/web/Csrf.php',
		'Flash' => '/web/Flash.php',
		'Request' => '/web/Request.php',
		'Response' => '/web/Response.php',
		'Session' => '/web/Session.php',
		'WebApplication' => '/web/WebApplication.php',
		'WebModule' => '/web/WebModule.php',
		'Widget' => '/widget/Widget.php'
	);

	public static function app(Application $app=null) {
		if ($app !== null) {
			if (self::$app === null)
				self::$app = $app;
			else
				throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Application can only be created once.'));
		}
		return self::$app;
	}

	public static function checkRequirements() {
		if (version_compare(PHP_VERSION, MINIMUM_PHP_VERSION) <= 0)
			die(sprintf('Your PHP version is %s. PHP %s or higher is required.', PHP_VERSION, MINIMUM_PHP_VERSION));
	}

	public static function addIncludePath($path) {
		$path = realpath($path);
		if (self::$includePaths === null) {
			self::$includePaths = array_unique(explode(PATH_SEPARATOR, get_include_path()));
			if (($pos = array_search('.', self::$includePaths, true)) !== false)
				unset(self::$includePaths[$pos]);
		}
		array_unshift(self::$includePaths, $path);
		if (set_include_path('.' . PATH_SEPARATOR . implode(PATH_SEPARATOR, self::$includePaths)) === false)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'It was not possible to change PHP include path.'));
	}

	public static function import($alias, $forceInclude=false) {
		if (isset(self::$imports[$alias]))
			return self::$imports[$alias];
		if (class_exists($alias, false) || interface_exists($alias, false))
			return (self::$imports[$alias] = $alias);
		if (($pos = strrpos($alias, '.')) === false) {
			if ($forceInclude && self::autoload($alias))
				self::$imports[$alias] = $alias;
			return $alias;
		}
		if (($class = (string)substr($alias, $pos+1)) !== '*' && (class_exists($class, false) || interface_exists($class, false)))
			return (self::$imports[$alias] = $class);
		if (($path = self::getPathAlias($alias)) !== false) {
			if ($class != '*') {
				if ($forceInclude) {
					require($path . '.php');
					self::$imports[$alias] = $class;
				} else {
					self::$classes[$class] = $path . '.php';
				}
				return $class;
			} else {
				self::addIncludePath($path);
				return (self::$imports[$alias] = $path);
			}
		}
		throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The alias "%s" is invalid.', array($alias)));
	}

	public static function getPathAlias($path) {
		if (isset(self::$aliases[$path]))
			return self::$aliases[$path];
		if (($pos = strpos($path, '.')) !== false) {
			$root = substr($path, 0, $pos);
			if (isset(self::$aliases[$root]))
				return (self::$aliases[$path] = rtrim(self::$aliases[$root] . DS . str_replace('.', DS, substr($path, $pos+1)), '*' . DS));
		}
		return false;
	}

	public static function setPathAlias($alias, $path) {
		self::$aliases[$alias] = rtrim($path, '\\/');
	}

	public static function autoload($class) {
		foreach (self::$autoloaders as $callback) {
			try {
				call_user_func($callback, $class);
				if (class_exists($class, false) || interface_exists($class, false))
					return true;
			} catch (Exception $e) {
				trigger_error($e->getMessage());
				exit(1);
			}
		}
		trigger_error(__(PHP2GO_LANG_DOMAIN, 'The class or interface "%s" was not found.', array($class)), E_USER_ERROR);
		exit(1);
	}

	protected static function loadClass($class) {
		if (isset(self::$coreClasses[$class])) {
			include(PHP2GO_PATH . self::$coreClasses[$class]);
		} elseif (isset(self::$classes[$class])) {
			include(self::$classes[$class]);
		} else {
			@include($class . '.php');
		}
		return true;
	}

	public static function pushAutoloader($callback) {
		if (is_callable($callback))
			self::$autoloaders[] = $callback;
	}

	public static function unshiftAutoloader($callback) {
		if (is_callable($callback))
			array_unshift(self::$autoloaders, $callback);
	}

	public static function newInstance($config) {
		if (is_string($config)) {
			$class = $config;
			$parent = null;
			$factory = null;
			$options = array();
			$behaviors = null;
		} elseif (is_array($config)) {
			$class = (isset($config['class']) ? $config['class'] : null);
			$parent = (isset($config['parent']) ? $config['parent'] : null);
			$factory = (isset($config['factory']) ? $config['factory'] : null);
			$options = (isset($config['options']) ? $config['options'] : array());
			$behaviors = Util::consumeArray($options, 'behaviors');
			if (($class === null && !is_array($factory)) || !is_array($options))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid instance configuration.'));
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid instance configuration.'));
		}
		if ($factory) {
			if (!class_exists($factory[0], false))
				$factory[0] = self::import($factory[0], true);
			$object = call_user_func_array($factory, array($options));
			if ($object instanceof Component && is_array($behaviors))
				$object->attachBehaviors($behaviors);
			return $object;
		} else {
			if (!class_exists($class, false))
				$class = self::import($class, true);
			if (($n = func_num_args()) > 1) {
				$args = func_get_args();
				if ($n == 2) {
					$object = new $class($args[1]);
				} elseif ($n == 3) {
					$object = new $class($args[1], $args[2]);
				} elseif ($n == 4) {
					$object = new $class($args[1], $args[2], $args[3]);
				} else {
					unset($args[0]);
					$reflection = new ReflectionClass($class);
					$object = call_user_func_array(array($reflection, 'newInstance'), $args);
				}
			} else {
				$object = new $class;
			}
			if ($parent !== null && $parent != $class) {
				$reflection = new ReflectionClass($class);
				if (!$reflection->isSubclassOf($parent))
					throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Class "%s" needs to be a subclass of "%s".', array($class, $parent)));
			}
			if ($object instanceof Component && is_array($behaviors))
				$object->attachBehaviors($behaviors);
			if (method_exists($object, 'loadOptions')) {
				$object->loadOptions($options);
			} else {
				foreach ($options as $name => $value)
					$object->{$name} = $value;
			}
			if (method_exists($object, 'init'))
				$object->init();
			return $object;
		}
	}

	public static function newInstanceArgs(array $args) {
		return call_user_func_array(array(__CLASS__, 'newInstance'), $args);
	}

	public static function translate($key, $domain=null, array $params=array(), $locale=null) {
		if ($domain == PHP2GO_LANG_DOMAIN) {
			if (!self::$translator) {
				self::$translator = new Translator();
				self::$translator->setSourceLanguage(Application::SOURCE_LANGUAGE);
				self::$translator->adapter->setBasePath(Php2Go::getPathAlias('php2go.translator.messages'));
			}
			$translated = self::$translator->translate($key, $domain);
		} else {
			$translated = Php2Go::app()->getTranslator()->translate($key, $domain, $locale);
		}
		return Util::buildMessage($translated, $params);
	}

	public static function createWebApplication($options) {
		if ($options instanceof Config)
			$options = $options->toArray();
		elseif (!is_array($options))
			throw new InvalidArgumentException('Invalid application config. An array or Config instance must be provided.');
		$class = Util::consumeArray($options, 'class');
		if ($class) {
			$reflection = new ReflectionClass($class);
			if (!$reflection->isSubclassOf('WebApplication'))
				throw new InvalidArgumentException('The "class" option must contain a child class of "WebApplication".');
		} else {
			$class = 'WebApplication';
		}
		return new $class($options);
	}

	public static function createConsoleApplication($options) {
		if ($options instanceof Config)
			$options = $options->toArray();
		elseif (!is_array($options))
			throw new InvalidArgumentException('Invalid application config. An array or Config instance must be provided.');
		$class = Util::consumeArray($options, 'class');
		if ($class) {
			$reflection = new ReflectionClass($class);
			if (!$reflection->isSubclassOf('ConsoleApplication'))
				throw new InvalidArgumentException('The "class" option must contain a child class of "ConsoleApplication".');
		} else {
			$class = 'ConsoleApplication';
		}
		return new $class($options);
	}

	public static function version() {
		return PHP2GO_VERSION;
	}
}

Php2Go::checkRequirements();
Php2Go::import('php2go.event.*');
Php2Go::import('php2go.exception.*');

spl_autoload_register(array('Php2Go', 'autoload'));

function __($key) {
	$args = array_slice(func_get_args(), 1);
	if (isset($args[0])) {
		if (is_string($args[0])) {
			$domain = $key;
			$key = array_shift($args);
		} else {
			$domain = null;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$params = array_shift($args);
			$locale = (isset($args[0]) ? $args[0] : null);
		} else {
			$params = array();
			$locale = null;
		}
	} else {
		$domain = null;
		$params = array();
		$locale = null;
	}
	return Php2Go::translate($key, $domain, $params, $locale);
}