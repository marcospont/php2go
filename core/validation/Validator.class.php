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
// $Header: /www/cvsroot/php2go/core/validation/Validator.class.php,v 1.18 2006/05/07 15:35:44 mpont Exp $
// $Date: 2006/05/07 15:35:44 $

//!-----------------------------------------------------------------
// @class		Validator
// @desc		A classe Validator funciona como um interface única
//				para todas as classes de validação implementadas no
//				framework. O método validate instancia e executa a validação
//				implementada utilizando o módulo definido pelo usuário
// @package		php2go.validation
// @uses		TypeUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.18 $
// @note		Exemplo de uso:<br>
// 				<pre>
//
//				/* Valor simples e chamada estática da validação */
//				$value = 'foo@bar.com';
//				if (Validator::validate('php2go.validation.EmailValidator', $value)) {
//				&nbsp;&nbsp;&nbsp;print 'valid e-mail!';
//				} else {
//				&nbsp;&nbsp;&nbsp;print 'bad e-mail syntax!';
//				}
//
//				/* Múltiplos valores com criação de instância */
//				$values[0] = 1.24;
//				$values[1] = 2.01;
//				$Validator =& Validator::getInstance();
//				if ($Validator->validateMultiple('php2go.validation.MaxValidation', $values, array('max'=>2), $wrongValues)) {
//				&nbsp;&nbsp;&nbsp;print 'max exceeded! => wrong values : ' . var_dump($wrongValues);
//				} else {
//				&nbsp;&nbsp;&nbsp;print 'values ok';
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class Validator extends PHP2Go
{
	var $errorStack = array();	// @var errorStack array	Vetor de erros das validações executadas

	//!-----------------------------------------------------------------
	// @function	Validator::Validator
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Validator() {
		parent::PHP2Go();
	}

	//!-----------------------------------------------------------------
	// @function	Validator::&getInstance
	// @desc		Retorna uma instância única da classe Validator
	// @access		public
	// @return		Validator object
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Validator();
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	Validator::validate
	// @desc		Executa validação em um valor utilizando um validador
	//				a partir de seu caminho na árvore de módulos
	// @param		path string			Caminho do módulo de validação utilizado
	// @param		&value mixed		Valor a ser validado
	// @param		params array		"NULL" Vetor de parâmetros necessários à validação
	// @param		userMessage string	"NULL" Mensagem customizada para o validador
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function validate($path, &$value, $params=NULL, $userMessage=NULL) {
		$validatorClass = basename(str_replace('.', '/', $path));
		// importa o módulo
		if (!import($path))
			return FALSE;
		// verifica a existência do objeto $validator
		if (!class_exists($validatorClass)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_INSTANTIATE_VALIDATOR', $validatorClass), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$Validator = new $validatorClass($params);
		// verifica a existência do método $validator::execute()
		if (!method_exists($Validator, 'execute')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_VALIDATOR', array($validatorClass, $validatorClass)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// executa a validação
		$result = TypeUtils::toBoolean($Validator->execute($value));
		if ($result === FALSE) {
			// mensagem customizada
			if (!TypeUtils::isNull($userMessage)) {
				Validator::addError($userMessage);
			// método getError do validador
			} elseif (method_exists($Validator, 'getError')) {
				$errMsg = $Validator->getError();
				if (!empty($errMsg))
					Validator::addError($errMsg);
			}
		}
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	Validator::validateField
	// @desc		Método que executa todos os tipos de validações em um determinado
	//				campo de formulário
	// @param		&Field FormField object		Campo a ser validado
	// @param		path string					Caminho do validador
	// @param		params array				"NULL" Parâmetros para o validador
	// @param		userMessage string			"NULL" Mensagem de erro do usuário, sobrescrevendo a mensagem padrão
	// @note		As validações que falharem irão armazenar as mensagens de erro na
	//				classe Validator. Elas podem ser recuperadas através do método
	//				estático Validator::getErrors
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function validateField(&$Field, $path, $params=NULL, $userMessage=NULL) {
		// valida o campo fornecido
		if (TypeUtils::isInstanceOf($Field, 'FormField')) {
			// validação de regra
			if ($path == 'php2go.validation.RuleValidator') {
				$name = $Field->getName();
				return Validator::validate($path, $name, $params, $userMessage);
			// validação de valor do campo
			} else {
				$value = $Field->getValue();
				$params['fieldLabel'] = $Field->getLabel();
				$result = Validator::validate($path, $value, $params, $userMessage);
				$currentValue = $Field->getValue();
				if ($result !== FALSE) {
					if (is_array($value) && $value === $currentValue)
						$Field->setValue($value);
					if (is_string($value) && strcmp(strval($value), strval($currentValue)))
						$Field->setValue($value);
					$Field->setValue($value);
				}
				return $result;
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Validator::validateMultiple
	// @desc		Valida um vetor de valores utilizando um determinado validador
	// @param		path string		Caminho do módulo de validação utilizado
	// @param		value array		Vetor de valores a serem validados
	// @param		params array		"NULL" Vetor de parâmetros para o validador
	// @param		wrongValues array	Vetor de valores que não satisfazem a validação
	// @return		bool Retorna FALSE se pelo menos um dos valores não satisfizer a validação
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function validateMultiple($path, $value, $params=NULL, &$wrongValues) {
		$wrongValues = array();
		$validatorClass = basename(str_replace('.', '/', $path));
		$value = TypeUtils::toArray($value);
		// importa o módulo
		if (!import($path))
			return FALSE;
		// verifica a existência da classe
		if (!class_exists($validatorClass)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_INSTANTIATE_VALIDATOR', $validatorClass), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// verifica a existência do método $validator::execute()
		$Validator = new $validatorClass($params);
		if (!method_exists($Validator, 'execute')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_VALIDATOR', array($validatorClass, $validatorClass)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// valida todos os elementos do vetor
		$result = TRUE;
		$hasErrorGetter = method_exists($Validator, 'getError');
		foreach ($value as $k=>$v) {
			if (!$Validator->execute($v)) {
				$wrongValues[$k] = $v;
				if ($hasErrorGetter) {
					$errMsg = $Validator->getError();
					if (!empty($errMsg))
						Validator::addError($errMsg);
				}
				$result = FALSE;
			}
		}
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	Validator::addError
	// @desc		Adiciona um erro de validação
	// @param		msg string	Mensagem do erro
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function addError($msg) {
		$Validator =& Validator::getInstance();
		$Validator->errorStack[] = $msg;
	}

	//!-----------------------------------------------------------------
	// @function	Validator::getErrors
	// @desc		Busca o vetor de erros de validação armazenado na classe
	// @access		public
	// @return		array Vetor de erros
	// @note		Para que os erros de validação possam ser armazenados e
	//				consultados, o validador deve implementar um método chamado
	//				getError()
	// @static
	//!-----------------------------------------------------------------
	function getErrors() {
		$Validator =& Validator::getInstance();
		return $Validator->errorStack;
	}

	//!-----------------------------------------------------------------
	// @function	Validator::clearErrors
	// @desc		Remove os erros armazenados
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function clearErrors() {
		$Validator =& Validator::getInstance();
		$Validator->errorStack = array();
	}
}
?>