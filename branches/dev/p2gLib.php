<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
 * Imports a class or file, given its "dot path"
 *
 * @param string $modulePath Dot path of a file or class
 * @param string $extension Class or file extension
 * @param bool $isClass Indicates the path points to a class
 * @return bool
 */
function import($modulePath, $extension='class.php', $isClass=TRUE) {
	$Loader =& ClassLoader::getInstance();
	return $Loader->importPath($modulePath, $extension, $isClass);
}

/**
 * Imports a file given its path
 *
 * @param string $filePath File path
 * @uses ClassLoader::loadFile()
 * @uses include_once()
 * @return bool
 */
function importFile($filePath) {
	$Loader =& ClassLoader::getInstance();
	return $Loader->loadFile($filePath);
}

/**
 * Includes a file given its path
 *
 * @param string $filePath File path
 * @param bool $return Return the result of the {@link include} call
 * @return mixed
 */
function includeFile($filePath, $return=FALSE) {
	if ($return === TRUE) {
		return (include($filePath));
	} else {
		if (!@include($filePath)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
}

/**
 * Imports and returns the class name given a class dot path
 *
 * Examples:
 * # php2go.base.Document => imports Document class and returns "Document"
 * # php2go.auth.AuthDb => imports AuthDb class and returns "AuthDb"
 *
 * @param string $path Dot path
 * @return string Class name
 */
function classForPath($path) {
	import($path);
	$className = basename(str_replace('.', '/', $path));
	if (class_exists($className))
		return $className;
	return FALSE;
}

/**
 * Autoload interceptor
 *
 * @param string $className Class name
 * @uses ClassLoader::loadFile()
 */
function __autoload($className) {
	$Lang =& LanguageBase::getInstance();
	$Loader =& ClassLoader::getInstance();
	$fileName = (isset($Loader->importClassCache[$className]) ? $Loader->importClassCache[$className] : $className . '.class.php');
	if (!$Loader->loadFile($fileName)) {
		trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_MODULE'), $className), E_USER_ERROR);
	}
}

/**
 * Calculates the offset between current folder and PHP2Go root
 *
 * @return string
 */
function getPhp2GoOffset() {
	// host + port from p2g absolute url
	$matches = array();
	preg_match("~(https?://)([^/]+)/?(.*)?~", substr(PHP2GO_ABSOLUTE_PATH, 0, -1), $matches);
	$p2gSrvName = @$matches[2];
	// host + port from application url
	$appSrvName = @$_SERVER['SERVER_NAME'] . (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '');
	if (!empty($p2gSrvName) && $p2gSrvName != $appSrvName)
		return FALSE;
	$path1 = strval(@$matches[3]);
	$path2 = substr($_SERVER['PHP_SELF'], 1);
	$matches1 = NULL;
	$matches2 = NULL;
	$equal = TRUE;
	$back = '';
	$forward = '';
	while ($path1 != '' || $path2 != '') {
		$matches1 = array();
		$matches2 = array();
		$res1 = preg_match("~^([^/]+)(/)?(.*)?$~", $path1, $matches1);
		$res2 = preg_match("~^([^/]+)(/)?(.*)?$~", $path2, $matches2);
		if (@$matches1[1] != @$matches2[1] || !$equal) {
			if (@$matches2[2] == '/')
				$back .= '../';
			if (@$matches1[1] != '')
				$forward .= @$matches1[1] . @$matches1[2];
			$equal = FALSE;
		}
		$path1 = @$matches1[3];
		$path2 = @$matches2[3];
	}
	$finalPath = $back . $forward;
	if (substr($finalPath, -1) != '/')
		$finalPath .= '/';
	return $finalPath;
}

/**
 * Configures a list of JSRS handlers
 *
 * <code>
 * jsrsDispatch("handlerA handlerB handlerC handlerD");
 * </code>
 *
 * @param string $handlersList Space separated list of handlers
 */
function jsrsDispatch($handlersList) {
	import('php2go.util.service.ServiceJSRS');
	$Service = new ServiceJSRS();
	$handlersList = trim((string)$handlersList);
	$handlers = explode(' ', $handlersList);
	foreach ($handlers as $handler)
		$Service->registerHandler($handler);
	$Service->handleRequest();
}

/**
 * Calls registered destructors and shutdown functions
 *
 * This function is automatically called when the script
 * shuts down. It is registered as a shutdown function by the
 * framework's initialization routine.
 */
function destroyPHP2GoObjects() {
	global $PHP2Go_destructor_list, $PHP2Go_shutdown_funcs;
	import('php2go.util.TypeUtils');
	if (is_array($PHP2Go_destructor_list) && !empty($PHP2Go_destructor_list)) {
		foreach($PHP2Go_destructor_list as $destructor) {
			$object =& $destructor[0];
			$method = $destructor[1];
			$object->$method();
			unset($object);
		}
	}
	if (is_array($PHP2Go_shutdown_funcs) && !empty($PHP2Go_shutdown_funcs)) {
		foreach($PHP2Go_shutdown_funcs as $function) {
			if (sizeof($function) == 3) {
				$object =& $function[0];
				$method = $function[1];
				$args = implode(',', $function[2]);
				eval("\$object->$method($args);");
			} else {
				call_user_func_array($function[0], $function[1]);
			}
		}
	}
}

/**
 * Shortcut function to retrieve the value of a language entry
 *
 * @uses PHP2Go::getLangVal()
 * @return string|NULL
 */
function __() {
	$args = func_get_args();
	if (sizeof($args) < 1) {
		return NULL;
	} else {
		$base = array_shift($args);
		return PHP2Go::getLangVal($base, $args, FALSE);
	}
}

/**
 * Used by template engine to print variable values
 *
 * This function is only used when PHP version is lower
 * than 5. In PHP5, objects will automatically have their
 * __toString method called when used in a print statement.
 *
 * @param mixed $val Variable value
 */
function __v(&$val) {
	if (is_object($val) && is_subclass_of($val, (IS_PHP5 ? 'Component' : 'component')))
		$val->display();
	else
		print $val;
}

/**
 * Utility function to print a value and a line break
 *
 * @param string $str Input string
 * @param string $nl New line string
 */
function println($str, $nl='<br>') {
	echo $str . $nl;
}

/**
 * Dumps a variable, using pre tags
 *
 * @uses var_dump()
 * @param mixed $var Variable value
 */
function dumpVariable($var) {
	print '<pre>';
	var_dump($var);
	print '</pre>';
}

/**
 * Returns or prints a human readable version of an array
 *
 * @param array $arr Input array
 * @param bool $return Return or print
 * @param int $stringLimit Maxlength for strings
 * @param bool $deep Recurse into inner arrays or objects
 * @return string
 */
function dumpArray($arr, $return=TRUE, $stringLimit=200, $deep=FALSE, $i=0) {
	static $registry;
	($i == 0) && ($registry = array());
	$r = array();
	if (is_object($arr)) {
		if (in_array($arr, $registry)) {
			if ($return)
				return "*recursion*";
			print "*recursion*";
			return TRUE;
		} else {
			$registry[] = $arr;
		}
		(!IS_PHP5) && ($arr = get_object_vars($arr));
	}
	foreach ($arr as $k => $v) {
		if (is_string($v)) {
			$r[] = $k . "=>'" . (strlen($v) > $stringLimit ? substr($v, 0, $stringLimit) . "...(" . strlen($v) . ")" : $v) . "'";
		} elseif (is_array($v)) {
			$r[] = $k . '=>' . ($deep ? dumpArray($v, TRUE, $stringLimit, TRUE, $i+1) : 'array');
		} elseif (is_object($v)) {
			$r[] = $k . '=>' . ($deep ? dumpArray($v, TRUE, $stringLimit, TRUE, $i+1) : 'object (' . get_class($v) . ')');
		} elseif (is_bool($v)) {
			$r[] = $k . '=>' . ($v ? 'true' : 'false');
		} elseif ($v === NULL) {
			$r[] = $k . '=>null';
		} else {
			$r[] = $k . '=>' . $v;
		}
	}
	if ($return)
		return "[" . implode(", ", $r) . "]";
	print "[" . implode(", ", $r) . "]";
	return TRUE;
}

/**
 * Returns the human readable representation of a variable
 *
 * @param mixed $var Variable
 * @param bool $formatted Whether to use pre tags
 * @return string
 */
function exportVariable($var, $formatted=FALSE) {
	if (is_object($var) && !System::isPHP5() && method_exists($var, '__tostring'))
		$export = $var->__toString();
	else
		$export = var_export($var, TRUE);
	if ($formatted)
		return '<pre>' . $export . '</pre>';
	else
		return $export;
}

/**
 * Read a key from an array, and remove it
 *
 * @param array $array Input array
 * @param string $key Key
 * @return mixed
 */
function consumeArray(&$array, $key) {
	if (is_array($array)) {
		if (array_key_exists($key, $array)) {
			$return = $array[$key];
			unset($array[$key]);
			return $return;
		}
	}
	return NULL;
}

/**
 * Find a path in a multidimensional array
 *
 * Examples:
 * <code>
 * $arr = array(
 *   'connection' => array(
 *     'host' => 'localhost',
 *     'port' => 80
 *   )
 * );
 * $value = findArrayPath($arr, 'connection.host', '.');
 * $value = findArrayPath($arr, 'connection/host', '/');
 * </code>
 *
 * @param array $arr Input array
 * @param string $path Path
 * @param string $separator Path separator
 * @param mixed $fallback Fallback value
 * @return mixed
 */
function findArrayPath($arr, $path, $separator='.', $fallback=NULL) {
	if (!is_array($arr))
		return $fallback;
	$parts = explode($separator, $path);
	if (sizeof($parts) == 1) {
		return (isset($arr[$path]) ? $arr[$path] : $fallback);
	} else {
		$i = 0;
		$base = $arr;
		$size = sizeof($parts);
		while ($i < $size) {
			if (!isset($base[$parts[$i]]))
				return $fallback;
			else
				$base = $base[$parts[$i]];
			if ($i < ($size-1) && !is_array($base))
				return $fallback;
			$i++;
		}
		return $base;
	}
}

/**
 * Highlight PHP code
 *
 * @param string $code Code string or file name
 * @param int $type Type ({@link T_BYFILE} or {@link T_BYVAR})
 * @uses highlight_file()
 * @uses highlight_string()
 * @return string
 */
function highlightPHP($code, $type=T_BYVAR) {
	if ($type == T_BYFILE)
		return highlight_file($code, TRUE);
	else
		return highlight_string((string)$code, TRUE);
}

/**
 * Resolves strings representing a boolean choice: T or F
 *
 * T => TRUE, F => FALSE, other values => NULL.
 *
 * @param string $value Input value
 * @return bool|NULL
 */
function resolveBooleanChoice($value=NULL) {
	if (TypeUtils::isNull($value))
		return NULL;
	elseif (trim($value) == "T")
		return TRUE;
	elseif (trim($value) == "F")
		return FALSE;
	return NULL;
}

/**
 * Resolves language entries
 *
 * Used by the forms API to allow internationalization
 * in some special attributes of the XML specification.
 *
 * @param string $value Input string
 * @return string Translated string
 */
function resolveI18nEntry($value) {
	if (!empty($value) && preg_match(PHP2GO_I18N_PATTERN, $value, $matches))
		return PHP2Go::getLangVal($matches[1]);
	return $value;
}
?>