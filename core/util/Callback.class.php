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

// @const	CALLBACK_DYNAMIC_METHOD "1"	Método dinâmico (objeto + método)
define('CALLBACK_DYNAMIC_METHOD', 1);
// @const	CALLBACK_STATIC_METHOD "2" Método estático (classe + método)
define('CALLBACK_STATIC_METHOD', 2);
// @const	CALLBACK_FUNCTION "3" Função procedural simples
define('CALLBACK_FUNCTION', 3);

//!-----------------------------------------------------------------
// @class		Callback
// @desc		Esta classe valida e executa métodos ou funções, quando estes são
//				utilizados como callbacks dentro de outras classes do framework. Os tipos
//				de callbacks permitidos são pares objeto-método e pares
//				classe-método (na forma de um array), chamadas estáticas de métodos no
//				formato classe::metodo e chamadas de funções simples
// @package		php2go.util
// @extends		PHP2Go
// @uses		System
// @uses		TypeUtils
// @version		$Revision: 1.11 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class Callback extends PHP2Go
{
	var $function = NULL;			// @var function mixed		"NULL" Contém a função ou método atualmente ativo na classe
	var $type;						// @var type int			Tipo da callback atual
	var $valid = FALSE;				// @var valid bool			"FALSE" Indica se a callback ativa é válida
	var $throwErrors = TRUE;		// @var throwErrors bool	"TRUE" Se esta propriedade for verdadeira, o uso de callbacks inválidas irá disparar um erro
	
	//!-----------------------------------------------------------------
	// @function	Callback::Callback
	// @desc		Construtor da classe
	// @access		public
	// @param		function mixed		Função ou método
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
	// @return		Callback object		Instância única da classe Callback
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
	// @note		Verifique a lista de tipos de callbacks na seção de constantes desta classe
	//!-----------------------------------------------------------------
	function getType() {
		return $this->type;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::isType
	// @desc		Testa se a callback ativa é de um determinado tipo
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
	// @desc		Altera ou define a função/método armazenada na classe
	// @access		public
	// @param		function mixed		Definição da função ou método
	// @return		void
	//!-----------------------------------------------------------------
	function setFunction($function) {
		$this->function = $function;
		$this->_parseFunction();
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::setThrowErrors
	// @desc		Define se os erros de validação nas funções é métodos devem ser disparados
	// @access		public
	// @param		setting bool		Valor para a propriedade
	// @return		void
	//!-----------------------------------------------------------------
	function setThrowErrors($setting) {
		$this->throwErrors = TypeUtils::toBoolean($setting);
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::invoke
	// @desc		Executa a função ou método atual, utilizando ou não um conjunto de argumentos
	// @access		public
	// @param		args mixed			"NULL" Recebe um ou mais argumentos para a execução da função/método
	// @param		nargs bool			"FALSE" Indica que o parâmetro args é um conjunto de argumentos para a callback e não um único argumento
	// @return		mixed Devolve o retorno da função ou método ou NULL por padrão se a callback atual não for válida
	//!-----------------------------------------------------------------
	function invoke($args=NULL, $nargs=FALSE) {
		if ($this->isValid())
			return (TypeUtils::isNull($args) ? call_user_func($this->function) : ($nargs && TypeUtils::isArray($args) ? call_user_func_array($this->function, $args) : call_user_func($this->function, $args)));
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::invokeByRef
	// @desc		Executa a função ou método atual, enviado UM parâmetro por referência
	// @access		public
	// @param		&argument mixed		Argumento - passado por referência
	// @return		mixed Devolve o retorno da função ou NULL por padrão se a callback atual não for válida
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
	// @desc		Exporta informação sobre a função/método ativo na classe
	// @access		private
	// @return		string Nome da função ou método
	//!-----------------------------------------------------------------
	function toString() {
		if (isset($this->function))
			return (is_array($this->function) ? (is_object($this->function[0]) ? get_class($this->function[0]) . '=>' . $this->function[1] : $this->function[0] . '=>' . $this->function[1]) : $this->function);
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Callback::_parseFunction
	// @desc		Define o tipo e a validade da função/método atual
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