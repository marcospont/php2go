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
// $Header: /www/cvsroot/php2go/core/util/Environment.class.php,v 1.11 2006/02/28 21:56:01 mpont Exp $
// $Date: 2006/02/28 21:56:01 $

//!-----------------------------------------------------------------
// @class		Environment
// @desc		Classe para consulta e manipulaзгo das variбveis de ambiente do sistema
// @package		php2go.util
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.11 $
//!-----------------------------------------------------------------
class Environment extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	Environment::&getInstance
	// @desc		Retorna uma instвncia ъnica da classe
	// @access		public
	// @return		Environment object Instвncia da classe Environment
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			$instance = new Environment;
		}
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	Environment::has
	// @desc		Verifica se uma determinada chave possui valor nas variбveis de ambiente
	// @access		public
	// @param		key string	Nome da chave buscada
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function has($key) {
		if (isset($_SERVER[$key])) {
			return TRUE;
		}
		return TypeUtils::toBoolean(@getenv($key));
	}
	
	//!-----------------------------------------------------------------
	// @function	Environment::get
	// @desc		Busca o valor de uma variбvel de ambiente
	// @access		public
	// @param		key string	Nome da chave buscada	
	// @return		mixed Valor da variбvel de ambiente ou FALSE se ela nгo existir
	// @static	
	//!-----------------------------------------------------------------	
	function get($key) {
		if (isset($_SERVER[$key])) {
			return $_SERVER[$key];
		}
		if (@getenv($key))
			return getenv($key);
		else
			return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Environment::set
	// @desc		Seta o valor de uma variбvel de ambiente
	// @access		public
	// @param		key string	Nome da chave
	// @param		value mixed	Valor para a chave
	// @return		void
	// @note		Este mйtodo nгo irб executar se o servidor estiver configurado com safe_mode
	// @static		
	//!-----------------------------------------------------------------
	function set($key, $value) {
		if (System::getIni('safe_mode') != '1')
			@putenv("$key=$value");
	}
}
?>