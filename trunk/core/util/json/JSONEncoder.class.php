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
// $Header: /www/cvsroot/php2go/core/util/json/JSONEncoder.class.php,v 1.1 2006/06/23 04:09:27 mpont Exp $
// $Date: 2006/06/23 04:09:27 $

//!-----------------------------------------------------------------
// @class		JSONEncoder
// @desc		Esta classe codifica strings na notação JSON (Javascript
//				Object Notation). Tem muita utilidade em aplicações que
//				adotam XMLHttpRequest para realizar comunicações entre o lado
//				cliente (Javascript) e o lado servidor (PHP). Além de ser um
//				formato leve e de fácil leitura, é de fácil interpretação: pode
//				ser diretamente processado pela função eval(), sem necessidade de
//				qualquer outro componente ou biblioteca
// @extends		PHP2Go
// @package		php2go.util.json
// @author		Marcos Pont
// @version		$Revision: 1.1 $
//!-----------------------------------------------------------------
class JSONEncoder extends PHP2Go
{
	var $objRef = array();		// @var objRef array		Guarda objetos já visitados, para evitar ciclos/recursões
	var $throwErrors = TRUE;	// @var throwErrors bool	"TRUE" Flag que indica se erros devem ser reportados ou ignorados

	//!-----------------------------------------------------------------
	// @function	JSONEncoder::JSONEncoder
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function JSONEncoder() {
		parent::PHP2Go();
	}

	//!-----------------------------------------------------------------
	// @function	JSONEncoder::encode
	// @desc		Método utilitário para codificação de um
	//				valor PHP em notação JSON
	// @param		value mixed			Valor a ser codificado
	// @param		throwErrors bool	"TRUE" Reportar ou ignorar errors
	// @return		string
	// @static
	//!-----------------------------------------------------------------
	function encode($value, $throwErrors=TRUE) {
		$encoder = new JSONEncoder();
		$encoder->throwErrors = $throwErrors;
		return $encoder->encodeValue($value);
	}

	//!-----------------------------------------------------------------
	// @function	JSONEncoder::encodeValue
	// @desc		Codifica um determinado valor em notação JSON
	// @note		Variáveis cujo tipo ou valor não puder ser convertido
	//				irão gerar um erro e retornarão NULL
	// @param		value mixed	Valor a ser convertido
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function encodeValue($value) {
		if (is_bool($value)) {
			return ($value ? 'true' : 'false');
		} elseif ($value === NULL) {
			return 'null';
		} elseif (is_numeric($value)) {
			$type = gettype($value);
			$value = ($type == 'integer' ? intval($value) : doubleval($value));
			return str_replace(',', '.', $value);
		} elseif (is_string($value)) {
			return $this->_encodeString($value);
		} elseif (is_array($value)) {
			return $this->_encodeArray($value);
		} elseif (is_object($value)) {
			if ($this->_wasVisited($value)) {
				if ($this->throwErrors)
					PHP2Go::raiseError(sprintf("O codificador JSON encontrou um ciclo em uma instância da classe %s!", get_class($value)), E_USER_ERROR, __FILE__, __LINE__);
				return NULL;
			}
			$this->objRef[] = $value;
			return $this->_encodeObject($value);
		} else {
			if ($this->throwErrors)
				PHP2Go::raiseError(sprintf("Ocorreu um erro ao converter o tipo %s para uma string JSON.", gettype($value)), E_USER_ERROR, __FILE__, __LINE__);
			return NULL;
		}
	}

	//!-----------------------------------------------------------------
	// @function	JSONEncoder::_encodeObject
	// @desc		Codifica um objeto
	// @param		&obj object	Objeto a ser codificado
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _encodeObject(&$obj) {
		$vars = get_object_vars($obj);
		$items = array();
		foreach ($vars as $name => $value) {
			$encoded = $this->encodeValue($value);
			if ($encoded)
				$items[] = '"' . strval($name) . '":' . $encoded;
		}
		return '{' . join(',', $items) . '}';
	}

	//!-----------------------------------------------------------------
	// @function	JSONEncoder::_encodeArray
	// @desc		Codifica um array
	// @note		Arrays associativos correspondem a objetos anônimos na
	//				notação JSON. Um array somente é numérico quando suas chaves
	//				são todas numéricas, começando em zero e sem falhas
	// @param		&arr array	Array a ser codificado
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _encodeArray(&$arr) {
		$items = array();
		if (TypeUtils::isHashArray($arr)) {
			foreach ($arr as $key => $value) {
				//$encoded = $value;
				$encoded = $this->encodeValue($value);
				if ($encoded)
					$items[] = '"' . strval($key) . '":' . $encoded;
			}
			return '{' . implode(',', $items) . '}';
		} else {
			for ($i=0,$s=sizeof($arr); $i<$s; $i++) {
				$encoded = $this->encodeValue($arr[$i]);
				if ($encoded)
					$items[] = $encoded;
			}
			return '[' . implode(',', $items) . ']';
		}
	}

	//!-----------------------------------------------------------------
	// @function	JSONEncoder::_encodeString
	// @desc		Codifica uma string
	// @param 		&str string	Texto a ser codificado em notação JSON
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _encodeString(&$str) {
		$result = '';
		$len = strlen($str);
		for ($c=0; $c<$len; $c++) {
			$ord = ord($str[$c]);
			if ($ord == 0x08) {
				$result .= '\b';
			} elseif ($ord == 0x09) {
				$result .= '\t';
			} elseif ($ord == 0x0A) {
				$result .= '\n';
			} elseif ($ord == 0x0C) {
				$result .= '\f';
			} elseif ($ord == 0x0D) {
				$result .= '\r';
			} elseif ($ord == 0x22 || $ord == 0x2F || $ord == 0x5C) {
				$result .= '\\' . $str[$c];
			} else {
				$result .= $str[$c];
			}
		}
		return '"' . $result . '"';
	}

	//!-----------------------------------------------------------------
	// @function	JSONEncoder::_wasVisited
	// @desc		Verifica se um determinado objeto já foi visitado
	// @param		&obj object	Objeto
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _wasVisited(&$obj) {
		foreach ($this->objRef as $ref) {
			if ($ref === $obj)
				return TRUE;
		}
		return FALSE;
	}
}
?>