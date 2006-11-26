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
	// Representa a URL absoluta do framework, informada no vetor de configurações do usuário
	define("PHP2GO_ABSOLUTE_PATH", $Conf->getConfig('ABSOLUTE_URI'));
	// @const PHP2GO_OFFSET_PATH "Retorno da função getPhp2GoOffset(), em p2gLib.php"
	// Caminho relativo calculado entre o domínio atual e a URL informada para o PHP2Go
	$offset = getPhp2GoOffset();
	define("PHP2GO_OFFSET_PATH", ($offset !== FALSE ? $offset : PHP2GO_ABSOLUTE_PATH));
	// @const PHP2GO_CSS_PATH "PHP2GO_OFFSET_PATH . 'resources/css/'"
	// Define o caminho absoluto http para o diretório de folhas de estilo do PHP2Go
	define("PHP2GO_CSS_PATH", PHP2GO_OFFSET_PATH . "resources/css/");
	// @const PHP2GO_ICON_PATH "PHP2GO_OFFSET_PATH . 'resources/icon/'"
	// Define o caminho absoluto http para o diretório de ícones e imagens do PHP2Go
	define("PHP2GO_ICON_PATH", PHP2GO_OFFSET_PATH . "resources/icon/");
	// @const PHP2GO_JAVASCRIPT_PATH "PHP2GO_OFFSET_PATH . 'resources/jsrun/'"
	// Constante a ser utilizada no momento de inserir scripts JavaScript que estão incluídos no framework
	define("PHP2GO_JAVASCRIPT_PATH", PHP2GO_OFFSET_PATH . "resources/jsrun/");
	// @const PHP2GO_CACHE_PATH "PHP2GO_ROOT . 'cache/'"
	// Constante que define o caminho para o diretório 'cache/' do PHP2Go, utilizado para o armazenamento de arquivos temporários
	define("PHP2GO_CACHE_PATH", PHP2GO_ROOT . "cache/");
	// @const PHP2GO_TEMPLATE_PATH "PHP2GO_ROOT . 'resources/template/'"
	// Constante que representa o caminho no servidor onde os templates HTML do PHP2Go estão armazenados
	define("PHP2GO_TEMPLATE_PATH", PHP2GO_ROOT . "resources/template/");

	/**
	 * Other constants
	 */

	// @const PHP2GO_VERSION "0.5.4"
	/// Versão do framework
	define("PHP2GO_VERSION", "0.5.4");
	// @const PHP2GO_RELEASE_DATE "25/11/2006"
	// Data de lançamento da última versão
	define("PHP2GO_RELEASE_DATE", "25/11/2006");
	// @const PHP2GO_INCLUDE_KEY "php2go"
	// Nome da chave de inclusão de módulos padrão a ser utilizada
	define("PHP2GO_INCLUDE_KEY", 'php2go');
	// @const PHP2GO_DIRECTORY_SEPARATOR "/"
	// Separador padrão de diretórios do framework
	define("PHP2GO_DIRECTORY_SEPARATOR", '/');
	// @const PHP2GO_PATH_SEPARATOR
	// Separador padrão para PATH
	define("PHP2GO_PATH_SEPARATOR", (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? ';' : ':'));
	// @const IS_PHP5 ""
	// Guarda, em um valor booleano, se a versão do PHP é 5.0.0 ou superior
	define("IS_PHP5", (floatval(PHP_VERSION) >= 5));
	// @const T_BYFILE "0"
	// Identifica que um determinado parâmetro de uma função ou método é um caminho para um arquivo no filesystem
	define('T_BYFILE', 0);
	// @const T_BYVAR "1"
	// Identifica que um determinado parâmetro de uma função ou método é uma string
	define('T_BYVAR', 1);
	// @const LONG_MAX
	// Valor máximo de número inteiro com sinal
	define('LONG_MAX', is_int(2147483648) ? 9223372036854775807 : 2147483647);
	// @const LONG_MIN
	// Valor mínimo de número inteiro com sinal
	define('LONG_MIN', -LONG_MAX - 1);
	// @const PHP2GO_I18N_PATTERN "/#i18n.([^#]+)#/"
	// Padrão para referência à mensagens internacionalizadas em templates e definições de formulário
	define('PHP2GO_I18N_PATTERN', '/#i18n:([^#]+)#/');
	// @const PHP2GO_MASK_PATTERN ""
	// Expressão regular de validação de máscaras para campos editable ou filtros de busca
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