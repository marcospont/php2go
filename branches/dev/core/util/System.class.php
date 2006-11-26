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
// $Header: /www/cvsroot/php2go/core/util/System.class.php,v 1.13 2006/10/26 04:32:49 mpont Exp $
// $Date: 2006/10/26 04:32:49 $

//------------------------------------------------------------------
import('php2go.util.Environment');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		System
// @desc		Esta classe possui mщtodos para busca de informaчѕes
//				sobre o sistema operacional do servidor e para carregamento
//				de bibliotecas dinтmicas
// @package		php2go.util
// @extends		PHP2Go
// @uses		Environment
// @author		PHP2Go
// @version		$Revision: 1.13 $
//!-----------------------------------------------------------------
class System extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	System::getInstance
	// @desc		Retorna uma instтncia њnica da classe
	// @access		public
	// @return		System object	Instтncia da classe
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			$instance = new System;
		}
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	System::isWindows
	// @desc		Verifica se o sistema operacional do servidor щ Windows
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isWindows() {
		return (substr(PHP_OS, 0, 3) == 'WIN');
	}

	//!-----------------------------------------------------------------
	// @function	System::isPHP5
	// @desc		Verifica se a versуo do PHP utilizada щ a 5
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isPHP5() {
		return IS_PHP5;
	}

	//!-----------------------------------------------------------------
	// @function	System::isGlobalsOn
	// @desc		Mщtodo especial utilizado para verificar se a opчуo
	//				de inicializaчуo register_globals estс habilitada
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function isGlobalsOn() {
		return (System::getIni("register_globals"));
	}

	//!-----------------------------------------------------------------
	// @function	System::getOs
	// @desc		Retorna a descriчуo do sistema operacional do servidor
	// @return 		string Descriчуo do sistema operacional
	// @static
	//!-----------------------------------------------------------------
	function getOs() {
		return PHP_OS;
	}

	//!-----------------------------------------------------------------
	// @function 	System::getSystemName
	// @desc 		Retorna o nome do servidor
	// @return		string Nome do servidor ou NULL caso este valor nуo esteja disponэvel
	// @static
	//!-----------------------------------------------------------------
	function getSystemName() {
		return Environment::get('COMPUTERNAME');
	}

	//!-----------------------------------------------------------------
	// @function	System::getServerAPIName
	// @desc		Retorna o tipo da interface entre o servidor e o PHP
	// @access		public
	// @return		string Uma string lowercase com o nome da interface
	// @static
	//!-----------------------------------------------------------------
	function getServerAPIName() {
		return php_sapi_name();
	}

	//!-----------------------------------------------------------------
	// @function	System::getIni
	// @desc		Retorna o valor de um parтmetro de inicializaчуo do PHP
	// @access		public
	// @param		key string	Nome do parтmetro
	// @return		mixed Valor do parтmetro
	// @static
	//!-----------------------------------------------------------------
	function getIni($key) {
		return ini_get($key);
	}

	//!-----------------------------------------------------------------
	// @function	System::setIni
	// @desc		Altera o valor de um parтmetro de inicializaчуo do PHP
	// @access		public
	// @param		key string	Nome do parтmetro
	// @param		value mixed	Novo valor
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function setIni($key, $value) {
		return @ini_set(TypeUtils::parseString($key), $value);
	}

	//!-----------------------------------------------------------------
	// @function 	System::getTempDir
	// @desc 		Busca o caminho para o diretѓrio temporсrio do servidor
	// @return		string Nome do diretѓrio temporсrio
	// @static
	//!-----------------------------------------------------------------
	function getTempDir() {
		if (System::isWindows())
			if (Environment::has('TEMP'))
				return Environment::get('TEMP');
			else if (Environment::has('TMP'))
				return Environment::get('TMP');
			else if (Environment::has('windir'))
				return Environment::get('windir') . '\temp';
			else
				return Environment::get('SystemRoot') . '\temp';
		else if (Environment::has('TMPDIR'))
			return Environment::get('TMPDIR');
		else
			return '/tmp';
	}

	//!-----------------------------------------------
	// @function	System::loadExtension
	// @desc		Carrega uma extensуo do PHP em tempo de
	//				execuчуo, caso ela jс nуo tenha sido incluэda
	//				nos arquivos de configuraчуo
	// @param		extensionName string		Nome da extensуo
	// @return		bool
	// @static
	//!-----------------------------------------------
	function loadExtension($extensionName) {
		$extensionMap = array(
			'HP-UX' => '.sl',
			'AIX' => '.a',
			'OSX' => '.bundle',
			'LINUX' => '.so'
		);
		if (!extension_loaded($extensionName)) {
            if (System::getIni('enable_dl') != 1 || System::getIni('safe_mode') == 1) {
                return FALSE;
            }
			$osName = System::getOs();
			if (System::isWindows()) {
				$resourceName = $extensionName . '.dll';
			} else if (isset($extensionMap[strtoupper($osName)])) {
				$resourceName = $extensionName . $extensionMap[$osName];
			} else {
				$resourceName = $extensionName . '.so';
			}
			return @dl('php_' . $resourceName) || @dl($resourceName);
		}
		return TRUE;
	}

	//!-----------------------------------------------
	// @function	System::getMicrotime
	// @desc		Retorna o timestamp atual do servidor
	//				incluindo casas decimais para os segundos
	// @return		float Timestamp atual em segundos e microsegundos
	// @static
	//!-----------------------------------------------
	function getMicrotime() {
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>