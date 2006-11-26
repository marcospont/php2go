<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/p2gConfig.php,v 1.67 2006/11/25 17:20:23 mpont Exp $
// $Date: 2006/11/25 17:20:23 $
// $Revision: 1.67 $

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
	 * Constants used to store global and absolute paths
	 */

	// @const PHP2GO_ABSOLUTE_PATH "ABSOLUTE_URI"
	// Representa a URL absoluta do framework, informada no vetor de configura��es do usu�rio
	define("PHP2GO_ABSOLUTE_PATH", $Conf->getConfig('ABSOLUTE_URI'));
	// @const PHP2GO_OFFSET_PATH "Retorno da fun��o getPhp2GoOffset(), em p2gLib.php"
	// Caminho relativo calculado entre o dom�nio atual e a URL informada para o PHP2Go
	$offset = getPhp2GoOffset();
	define("PHP2GO_OFFSET_PATH", ($offset !== FALSE ? $offset : PHP2GO_ABSOLUTE_PATH));
	// @const PHP2GO_CSS_PATH "PHP2GO_OFFSET_PATH . 'resources/css/'"
	// Define o caminho absoluto http para o diret�rio de folhas de estilo do PHP2Go
	define("PHP2GO_CSS_PATH", PHP2GO_OFFSET_PATH . "resources/css/");
	// @const PHP2GO_ICON_PATH "PHP2GO_OFFSET_PATH . 'resources/icon/'"
	// Define o caminho absoluto http para o diret�rio de �cones e imagens do PHP2Go
	define("PHP2GO_ICON_PATH", PHP2GO_OFFSET_PATH . "resources/icon/");
	// @const PHP2GO_JAVASCRIPT_PATH "PHP2GO_OFFSET_PATH . 'resources/jsrun/'"
	// Constante a ser utilizada no momento de inserir scripts JavaScript que est�o inclu�dos no framework
	define("PHP2GO_JAVASCRIPT_PATH", PHP2GO_OFFSET_PATH . "resources/jsrun/");
	// @const PHP2GO_CACHE_PATH "PHP2GO_ROOT . 'cache/'"
	// Constante que define o caminho para o diret�rio 'cache/' do PHP2Go, utilizado para o armazenamento de arquivos tempor�rios
	define("PHP2GO_CACHE_PATH", PHP2GO_ROOT . "cache/");
	// @const PHP2GO_TEMPLATE_PATH "PHP2GO_ROOT . 'resources/template/'"
	// Constante que representa o caminho no servidor onde os templates HTML do PHP2Go est�o armazenados
	define("PHP2GO_TEMPLATE_PATH", PHP2GO_ROOT . "resources/template/");

	/**
	 * Other constants
	 */

	// @const PHP2GO_VERSION "0.5.4"
	/// Vers�o do framework
	define("PHP2GO_VERSION", "0.5.4");
	// @const PHP2GO_RELEASE_DATE "25/11/2006"
	// Data de lan�amento da �ltima vers�o
	define("PHP2GO_RELEASE_DATE", "25/11/2006");
	// @const PHP2GO_INCLUDE_KEY "php2go"
	// Nome da chave de inclus�o de m�dulos padr�o a ser utilizada
	define("PHP2GO_INCLUDE_KEY", 'php2go');
	// @const PHP2GO_DIRECTORY_SEPARATOR "/"
	// Separador padr�o de diret�rios do framework
	define("PHP2GO_DIRECTORY_SEPARATOR", '/');
	// @const PHP2GO_PATH_SEPARATOR
	// Separador padr�o para PATH
	define("PHP2GO_PATH_SEPARATOR", (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? ';' : ':'));
	// @const IS_PHP5 ""
	// Guarda, em um valor booleano, se a vers�o do PHP � 5.0.0 ou superior
	define("IS_PHP5", (floatval(PHP_VERSION) >= 5));
	// @const T_BYFILE "0"
	// Identifica que um determinado par�metro de uma fun��o ou m�todo � um caminho para um arquivo no filesystem
	define('T_BYFILE', 0);
	// @const T_BYVAR "1"
	// Identifica que um determinado par�metro de uma fun��o ou m�todo � uma string
	define('T_BYVAR', 1);
	// @const LONG_MAX
	// Valor m�ximo de n�mero inteiro com sinal
	define('LONG_MAX', is_int(2147483648) ? 9223372036854775807 : 2147483647);
	// @const LONG_MIN
	// Valor m�nimo de n�mero inteiro com sinal
	define('LONG_MIN', -LONG_MAX - 1);
	// @const PHP2GO_I18N_PATTERN "/#i18n.([^#]+)#/"
	// Padr�o para refer�ncia � mensagens internacionalizadas em templates e defini��es de formul�rio
	define('PHP2GO_I18N_PATTERN', '/#i18n:([^#]+)#/');
	// @const PHP2GO_MASK_PATTERN ""
	// Express�o regular de valida��o de m�scaras para campos editable ou filtros de busca
	define('PHP2GO_MASK_PATTERN', "/^(CPFCNPJ|CURRENCY|DATE|EMAIL|FLOAT|(FLOAT)(\-([1-9][0-9]*)\:([1-9][0-9]*))?|DIGIT|INTEGER|LOGIN|WORD|TIME(?:\-AMPM)?|URL|(ZIP)(\-?([1-9])\:?([1-9])))$/");

	/**
	 * Set INI variables
	 */
	ini_set('magic_quotes_gpc', 'off');
	ini_set('register_argc_argv', 'off');
	ini_set('register_globals', 'off');
	ini_set('short_open_tag', 'off');
	ini_set('variables_order', 'EPROSGC');

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