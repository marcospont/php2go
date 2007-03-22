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
	 * Define framework's root folder
	 */
	define("PHP2GO_ROOT", str_replace("\\", "/", dirname(__FILE__)) . '/');

	/**
	 * Require framework's initialization modules
	 */
	require_once(PHP2GO_ROOT . 'errorHandler.php');
	require_once(PHP2GO_ROOT . 'p2gLib.php');
	require_once(PHP2GO_ROOT . 'core/Conf.class.php');
	require_once(PHP2GO_ROOT . 'core/Init.class.php');
	require_once(PHP2GO_ROOT . 'core/LanguageBase.class.php');
	require_once(PHP2GO_ROOT . 'core/LocaleNegotiator.class.php');
	require_once(PHP2GO_ROOT . 'core/ClassLoader.class.php');
	require_once(PHP2GO_ROOT . 'core/base/PHP2Go.class.php');

	/**
	 * Initialize configuration manager and initialization manager
	 */
	$Conf =& Conf::getInstance();
	$Init =& Init::getInstance();

	/**
	 * Represents the frameworks's absolute URI
	 */
	define('PHP2GO_ABSOLUTE_PATH', $Conf->getConfig('ABSOLUTE_URI'));
	$offset = getPhp2GoOffset();
	/**
	 * Represents the offset between the current dir and the framework's root
	 */
	define('PHP2GO_OFFSET_PATH', ($offset !== FALSE ? $offset : PHP2GO_ABSOLUTE_PATH));
	/**
	 * Framework's CSS path
	 */
	define('PHP2GO_CSS_PATH', PHP2GO_OFFSET_PATH . "resources/css/");
	/**
	 * Framework's icons path
	 */
	define('PHP2GO_ICON_PATH', PHP2GO_OFFSET_PATH . "resources/icon/");
	/**
	 * Framework's Javascript libraries path
	 */
	$useCompressedJs = $Conf->getConfig('USE_COMPRESSED_JS', TRUE);
	define('PHP2GO_JAVASCRIPT_PATH', PHP2GO_OFFSET_PATH . ($useCompressedJs ? "resources/jsrun/" : "resources/javascript/"));
	/**
	 * Framework's cache path
	 */
	define('PHP2GO_CACHE_PATH', PHP2GO_ROOT . "cache/");
	/**
	 * Framework's templates path
	 */
	define('PHP2GO_TEMPLATE_PATH', PHP2GO_ROOT . "resources/template/");
	/**
	 * Framework's version
	 */
	define('PHP2GO_VERSION', '0.5.5');
	/**
	 * Date when latest framework's version was released
	 */
	define('PHP2GO_RELEASE_DATE', '12/03/2007');
	/**
	 * Special key representing framework's modules in import operations
	 */
	define('PHP2GO_INCLUDE_KEY', 'php2go');
	/**
	 * Default directory separator
	 */
	define('PHP2GO_DIRECTORY_SEPARATOR', '/');
	/**
	 * Path separator
	 */
	define('PHP2GO_PATH_SEPARATOR', (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? ';' : ':'));
	/**
	 * Indicates if we're running PHP 5.0.0 or higher
	 */
	define('IS_PHP5', (floatval(PHP_VERSION) >= 5));
	/**
	 * Indicates a file name parameter
	 */
	define('T_BYFILE', 0);
	/**
	 * Indicates a string or variable parameter
	 */
	define('T_BYVAR', 1);
	/**
	 * Highest signed integer
	 */
	define('LONG_MAX', is_int(2147483648) ? 9223372036854775807 : 2147483647);
	/**
	 * Lowest signed integer
	 */
	define('LONG_MIN', -LONG_MAX - 1);
	/**
	 * Internationalization pattern for templates and XML specifications
	 */
	define('PHP2GO_I18N_PATTERN', '/#i18n:([^#]+)#/');
	/**
	 * Input masks pattern
	 */
	define('PHP2GO_MASK_PATTERN', "/^(CPFCNPJ|CURRENCY|DATE|EMAIL|FLOAT|(FLOAT)(\-([1-9][0-9]*)\:([1-9][0-9]*))?|DIGIT|INTEGER|LOGIN|WORD|TIME(?:\-AMPM)?|URL|(ZIP)(\-?([1-9])\:?([1-9])))$/");

	/**
	 * Set INI variables
	 */
	ini_set('short_open_tag', 'on');
	ini_set('asp_tags', 'off');
	ini_set('arg_separator.output', "&amp;");
	ini_set('register_globals', 'off');
	ini_set('register_argc_argv', 'on');
	ini_set('magic_quotes_gpc', 'off');
	ini_set('magic_quotes_runtime', 'off');

	/**
	 * Mandatory classes
	 */
	import('php2go.base.Component');
	import('php2go.base.PHP2GoError');
	import('php2go.base.Registry');
	import('php2go.util.System');
	import('php2go.util.TypeUtils');
	import('php2go.db.Db');

	/**
	 * Setup error handling and shutdown function
	 */
	error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
	set_error_handler("php2GoErrorHandler");
	register_shutdown_function("destroyPHP2GoObjects");

?>