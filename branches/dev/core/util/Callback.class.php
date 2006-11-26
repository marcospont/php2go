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
// $Header: /www/cvsroot/php2go/core/util/Callback.class.php,v 1.11 2006/05/07 15:10:16 mpont Exp $
// $Date: 2006/05/07 15:10:16 $

// @const	CALLBACK_DYNAMIC_METHOD "1"	M�todo din�mico (objeto + m�todo)
define('CALLBACK_DYNAMIC_METHOD', 1);
// @const	CALLBACK_STATIC_METHOD "2" M�todo est�tico (classe + m�todo)
define('CALLBACK_STATIC_METHOD', 2);
// @const	CALLBACK_FUNCTION "3" Fun��o procedural simples
define('CALLBACK_FUNCTION', 3);

//!-----------------------------------------------------------------
// @class		Callback
// @desc		Esta classe valida e executa m�todos ou fun��es, quando estes s�o
//				utilizados como callbacks dentro de outras classes do framework. Os tipos
//				de callbacks permitidos s�o pares objeto-m�todo e pares
//				classe-m�todo (na forma de um array), chamadas est�ticas de m�todos no
//				formato classe::metodo e chamadas de fun��es simples
// @package		php2go.util
// @extends		PHP2Go
// @uses		System
// @uses		TypeUtils
// @version		$Revision: 1.11 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class Callback extends PHP2Go
{
	var $function = NULL;			// @var function mixed		"NULL" Cont�m a fun��o ou m�todo atualmente ativo na classe
	var $type;						// @var type int			Tipo da callback atual
	var $valid = FALSE;				// @var valid bool			"FALSE" Indica se a callback ativa � v�lida
	var $throwErrors = TRUE;		// @var throwErrors bool	"TRUE" Se esta propriedade for verdadeira, o uso de callbacks inv�lidas ir� disparar um erro
	
	//!-----------------------------------------------------------------
	// @function	Callback::Callback
	// @desc		Construtor da classe
	// @access		public
	// @param		function mixed		Fun��o ou m�todo
	//!-----------------------------------------------------------------
	function Callback($function=NULL) {
		parent::PHP2Go();
		if (!TypeUtils::isNull($function)) {
			$this->function = $function;
			$this->_parseFunction();
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::&getInstance
	// @desc		Retorna o singleton da classe
	// @access		public
	// @return		Callback object		Inst�ncia �nica da classe Callback
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Callback();
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::getType
	// @desc		Busca o tipo da callback ativa
	// @access		public
	// @return		int Tipo da calllback ativa
	// @note		Verifique a lista de tipos de callbacks na se��o de constantes desta classe
	//!-----------------------------------------------------------------
	function getType() {
		return $this->type;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::isType
	// @desc		Testa se a callback ativa � de um determinado tipo
	// @access		public
	// @param		type int	Tipo de callback
	// @return		bool
	//!-----------------------------------------------------------------
	function isType($type) {
		return ($this->type == $type);
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::isValid
	// @desc		Consulta pelo status de validade da callback ativa
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		return $this->valid;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::setFunction
	// @desc		Altera ou define a fun��o/m�todo armazenada na classe
	// @access		public
	// @param		function mixed		Defini��o da fun��o ou m�todo
	// @return		void
	//!-----------------------------------------------------------------
	function setFunction($function) {
		$this->function = $function;
		$this->_parseFunction();
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::setThrowErrors
	// @desc		Define se os erros de valida��o nas fun��es � m�todos devem ser disparados
	// @access		public
	// @param		setting bool		Valor para a propriedade
	// @return		void
	//!-----------------------------------------------------------------
	function setThrowErrors($setting) {
		$this->throwErrors = TypeUtils::toBoolean($setting);
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::invoke
	// @desc		Executa a fun��o ou m�todo atual, utilizando ou n�o um conjunto de argumentos
	// @access		public
	// @param		args mixed			"NULL" Recebe um ou mais argumentos para a execu��o da fun��o/m�todo
	// @param		nargs bool			"FALSE" Indica que o par�metro args � um conjunto de argumentos para a callback e n�o um �nico argumento
	// @return		mixed Devolve o retorno da fun��o ou m�todo ou NULL por padr�o se a callback atual n�o for v�lida
	//!-----------------------------------------------------------------
	function invoke($args=NULL, $nargs=FALSE) {
		if ($this->isValid())
			return (TypeUtils::isNull($args) ? call_user_func($this->function) : ($nargs && TypeUtils::isArray($args) ? call_user_func_array($this->function, $args) : call_user_func($this->function, $args)));
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::invokeByRef
	// @desc		Executa a fun��o ou m�todo atual, enviado UM par�metro por refer�ncia
	// @access		public
	// @param		&argument mixed		Argumento - passado por refer�ncia
	// @return		mixed Devolve o retorno da fun��o ou NULL por padr�o se a callback atual n�o for v�lida
	//!-----------------------------------------------------------------
	function invokeByRef(&$argument) {
		if ($this->isValid()) {
			$params = array();
			$params[] =& $argument;
			return call_user_func_array($this->function, $params);
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::toString
	// @desc		Exporta informa��o sobre a fun��o/m�todo ativo na classe
	// @access		private
	// @return		string Nome da fun��o ou m�todo
	//!-----------------------------------------------------------------
	function toString() {
		if (isset($this->function))
			return (is_array($this->function) ? (is_object($this->function[0]) ? get_class($this->function[0]) . '=>' . $this->function[1] : $this->function[0] . '=>' . $this->function[1]) : $this->function);
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::_parseFunction
	// @desc		Define o tipo e a validade da fun��o/m�todo atual
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _parseFunction() {
		if (TypeUtils::isArray($this->function) && sizeof($this->function) == 2) {			
			if (TypeUtils::isObject($this->function[0])) {
				$this->type = CALLBACK_DYNAMIC_METHOD;
				$this->valid = (method_exists($this->function[0], $this->function[1]));
			} else {
				if (!System::isPHP5()) {
					$this->function[0] = strtolower($this->function[0]);
					$this->function[1] = strtolower($this->function[1]);
				}
				$this->type = CALLBACK_STATIC_METHOD;
				$this->valid = (in_array($this->function[1], TypeUtils::toArray(get_class_methods($this->function[0]))));
			}
		} else {
			$tmp = (!System::isPHP5() ? strtolower($this->function) : $this->function);
			if (strpos($tmp, '::') !== FALSE) {
				$this->type = CALLBACK_STATIC_METHOD;
				$this->function = explode('::', $tmp);
				$this->valid = (in_array($this->function[1], TypeUtils::toArray(get_class_methods($this->function[0]))));
			} else {
				$this->type = CALLBACK_FUNCTION;
				$this->valid = (function_exists($this->function));
			}
		}
		if (!$this->valid && $this->throwErrors)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_CALLBACK', $this->toString()), E_USER_ERROR, __FILE__, __LINE__);
	}
}