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
// $Header: /www/cvsroot/php2go/core/util/TypeUtils.class.php,v 1.21 2006/10/26 04:28:37 mpont Exp $
// $Date: 2006/10/26 04:28:37 $

//!----------------------------------------------
// @class		TypeUtils
// @desc		Classe que cont�m fun��es utilit�rias para verifica��o de tipagem
//				de dados e convers�o (cast) entre tipos primitivos de dado no PHP
// @package		php2go.util
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.21 $
// @static
//!----------------------------------------------
class TypeUtils extends PHP2Go
{
	//!----------------------------------------------
	// @function	TypeUtils::getType
	// @desc		Retorna o tipo de uma vari�vel
	// @access		public
	// @return		string
	// @static
	//!----------------------------------------------
	function getType($value) {
		return gettype($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isFloat
	// @desc		Determina se um valor � do tipo float
	// @param		&value mixed	Valor a ser testado
	// @param		strict bool	"FALSE" Se TRUE, realiza o teste de sintaxe e formato de vari�vel
	// @note		Se o par�metro $strict for mantido FALSE, um n�mero inteiro ou string, se respeitar
	//				a sintaxe de um decimal - 999[.999] - ser� convertido para float
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isFloat(&$value, $strict=FALSE) {
		$locale = localeconv();
		$dp = $locale['decimal_point'];
		$exp = "/^\-?[0-9]+(\\" . $dp . "[0-9]+)?$/";
		if (preg_match($exp, $value)) {
			if (!$strict && !is_float($value)) {
				$value = TypeUtils::parseFloat($value);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!----------------------------------------------
	// @function	TypeUtils::parseFloat
	// @desc		Cria a representa��o de n�mero decimal para um valor
	// @param		value mixed	Valor a ser convertido
	// @access		public
	// @return		float
	// @static
	//!----------------------------------------------
	function parseFloat($value) {
		if (TypeUtils::isString($value)) {
			$locale = localeconv();
			if ($locale['decimal_point'] != '.') {
				$value = str_replace($locale['decimal_point'], '.', $value);
			}
		}
		return floatval($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::parseFloatPositive
	// @desc		Cria a representa��o de n�mero decimal positivo para um valor
	// @param		value mixed	Valor a ser convertido
	// @access		public
	// @return		float
	// @static
	//!----------------------------------------------
	function parseFloatPositive($value) {
		return abs(floatval($value));
	}

	//!----------------------------------------------
	// @function	TypeUtils::isInteger
	// @desc		Testa se um valor � um n�mero inteiro
	// @param		&value int		Valor a testar
	// @param		strict bool	"FALSE" Se TRUE, realiza o teste de sintaxe e formato de vari�vel
	// @note		Se o par�metro $strict for mantido FALSE, uma string que respeite a sintaxe de
	//				n�meros inteiros - 999 - ser� convertida para integer
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isInteger(&$value, $strict=FALSE) {
		$exp = "/^\-?[0-9]+$/";
		if (preg_match($exp, $value)) {
			if (!$strict && !is_int($value)) {
				$value = TypeUtils::parseInteger($value);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!----------------------------------------------
	// @function	TypeUtils::parseInteger
	// @desc		Cria a representa��o de n�mero inteiro para um valor
	// @param		value mixed	Valor a ser convertido
	// @access		public
	// @return		int
	// @static
	//!----------------------------------------------
	function parseInteger($value) {
		return intval($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::parseIntegerPositive
	// @desc		Cria a representa��o de n�mero inteiro positivo para um valor
	// @param		value mixed	Valor a ser convertido
	// @access		public
	// @return		int
	// @static
	//!----------------------------------------------
	function parseIntegerPositive($value) {
		return abs(intval($value));
	}

	//!----------------------------------------------
	// @function	TypeUtils::isString
	// @desc		Testa se um determinado valor � string
	// @param		value mixed	Valor a ser testado
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isString($value) {
		return is_string($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::parseString
	// @desc		Retorna a representa��o string de um valor
	// @param		value mixed	Valor a ser convertido
	// @return		string Resultado da convers�o
	// @access		public
	// @static
	//!----------------------------------------------
	function parseString($value) {
		return strval($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isArray
	// @desc		Verifica se uma vari�vel � um array
	// @param		value mixed	Valor a testar
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isArray($value) {
		return is_array($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isHashArray
	// @desc		Verifica se uma vari�vel � um array do tipo hash (associativo)
	// @param		value array	Valor a testar
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isHashArray($value) {
		if (is_array($value) && sizeof($value)) {
			$i = 0;
			$keys = array_keys($value);
			foreach ($keys as $k=>$v) {
				if ($v !== $i) {
					return TRUE;
				}
				$i++;
			}
		}
		return FALSE;
	}

	//!----------------------------------------------
	// @function	TypeUtils::toArray
	// @desc		Cria uma representa��o de array para um valor qualquer
	// @param		value mixed	Valor a converter
	// @access		public
	// @return		array
	// @static
	//!----------------------------------------------
	function toArray($value) {
		return is_array($value) ? $value : array($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isObject
	// @desc		Verifica se uma determinada vari�vel fornecida � um objeto
	// @param		value mixed	Valor a testar
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isObject($value) {
		return is_object($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isInstanceOf
	// @desc		Verifica se um determinado objeto � uma inst�ncia da classe fornecida no par�metro $className
	// @note		Utiliza recursividade para os n�veis superiores se o par�metro $recurse for TRUE
	// @param		object object		Objeto a ser testado
	// @param		className string	Nome da classe
	// @param		recurse bool		Testar os ascendentes do objeto
	// @return		bool
	// @static
	//!----------------------------------------------
	function isInstanceOf($object, $className, $recurse=TRUE) {
		if (!is_object($object))
			return FALSE;
		$objClass = get_class($object);
		$otherClass = (System::isPHP5() ? $className : strtolower($className));
		if ($recurse)
			return ($objClass == $otherClass || is_subclass_of($object, $otherClass));
		return ($objClass == $otherClass);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isResource
	// @desc		Valida se um valor � do tipo resource
	// @param		value mixed	Valor a testar
	// @return		string Tipo de resource. Retorna FALSE se o valor n�o pertencer � classe resource
	// @access		public
	// @static
	//!----------------------------------------------
	function isResource($value) {
		if (is_resource($value))
			return get_resource_type($value);
		return FALSE;
	}

	//!----------------------------------------------
	// @function	TypeUtils::isNull
	// @desc		Verifica se um determinado valor � NULL
	// @param		value mixed	Valor a testar
	// @param		strict bool	"FALSE" Se TRUE, leva em considera��o o tipo do dado
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isNull($value, $strict = FALSE) {
		return ($strict) ? (NULL === $value) : (NULL == $value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::ifNull
	// @desc		Realiza o teste se um valor � NULL, retornando um
	//				valor padr�o determinado
	// @param		value mixed	Valor a testar
	// @param		default mixed	"NULL" Valor padr�o quando o teste de null for verdadeiro
	// @return		mixed $default se $value for null, em caso contr�rio $value
	// @see			TypeUtils::ifFalse
	// @access		public
	// @static
	//!----------------------------------------------
	function ifNull($value, $default = NULL) {
		if ($value === NULL)
			return $default;
		return $value;
	}

	//!----------------------------------------------
	// @function	TypeUtils::isBoolean
	// @desc		Verifica se um valor � do tipo boolean
	// @param		value mixed	Valor a testar
	// @see			TypeUtils::isTrue
	// @see			TypeUtils::isFalse
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isBoolean($value) {
		return ($value === TRUE || $value === FALSE);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isTrue
	// @desc		Verifica se um valor � TRUE utilizando compara��o por valor e tipagem
	// @param		value mixed	Valor a testar
	// @see			TypeUtils::isBoolean
	// @see			TypeUtils::isFalse
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isTrue($value) {
		return ($value === TRUE);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isFalse
	// @desc		Verifica se um valor � FALSE utilizando compara��o por valor e tipagem
	// @param		value mixed	Valor a testar
	// @see			TypeUtils::isBoolean
	// @see			TypeUtils::isTrue
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function isFalse($value) {
		return ($value === FALSE);
	}

	//!----------------------------------------------
	// @function	TypeUtils::ifFalse
	// @desc		Realiza o teste se um valor � FALSE, retornando um
	//				valor padr�o determinado
	// @param		value mixed	Valor a testar
	// @param		default mixed	"FALSE" Valor a ser retornado quando o valor testado for false
	// @return		mixed $default se $value for false, em caso contr�rio $value
	// @see			TypeUtils::ifNull
	// @access		public
	// @static
	//!----------------------------------------------
	function ifFalse($value, $default = FALSE) {
		if ($value === FALSE)
			return $default;
		return $value;
	}

	//!----------------------------------------------
	// @function	TypeUtils::toBoolean
	// @desc		Converte um valor qualquer para sua representa��o booleana
	// @param		value mixed	Valor a converter
	// @access		public
	// @return		bool
	// @static
	//!----------------------------------------------
	function toBoolean($value) {
		return (bool)$value;
	}

	//!----------------------------------------------
	// @function	TypeUtils::isEmpty
	// @desc		Verifica se um valor � vazio, utilizando a fun��o empty
	// @param		value mixed	Valor a testar
	// @access		public
	// @return		bool
	//!----------------------------------------------
	function isEmpty($value) {
		$result = empty($value);
		return $result;
	}
}
?>