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
// $Header: /www/cvsroot/php2go/core/util/json/JSONDecoder.class.php,v 1.1 2006/06/23 04:09:26 mpont Exp $
// $Date: 2006/06/23 04:09:26 $

// @const JSON_LEFT_BRACE "1"
// Identifica o início de uma declaração de objeto
define('JSON_LEFT_BRACE', 1);
// @const JSON_RIGHT_BRACE "2"
// Identifica o fim da declaração de um objeto
define('JSON_RIGHT_BRACE', 2);
// @const JSON_LEFT_BRACKET "3"
// Identifica o início da declaração de um array
define('JSON_LEFT_BRACKET', 3);
// @const JSON_RIGHT_BRACKET "4"
// Identifica o fim da declaração de um array
define('JSON_RIGHT_BRACKET', 4);
// @const JSON_COMMA "5"
// Separador de propriedades de objetos ou itens de arrays
define('JSON_COMMA', 5);
// @const JSON_COLON "6"
// Separador entre nome e valor de uma propriedade de objeto
define('JSON_COLON', 6);
// @const JSON_DATA "7"
// Tokens do tipo string ou número (int, float)
define('JSON_DATA', 7);
// @const JSON_CONTINUE "8"
// Tipo de token utilizado para detectar nomes de propriedades de objetos
define('JSON_CONTINUE', 8);

//!-----------------------------------------------------------------
// @class		JSONDecoder
// @desc		Esta classe permite decodificar strings em notação JSON
//				para valores nativos PHP, como objetos, arrays, strings,
//				números e constantes TRUE, FALSE e NULL. É útil nos casos
//				em que a comunicação entre cliente/servidor utiliza JSON
//				nas duas direções
// @extends		PHP2Go
// @package		php2go.util.json
// @author		Marcos Pont
// @version		$Revision: 1.1 $
//!-----------------------------------------------------------------
class JSONDecoder extends PHP2Go
{
	var $src = NULL;			// @var src string		"NULL" String JSON ativa
	var $length = 0;			// @var length int		"0" Tamanho da string JSON
	var $token = NULL;			// @var token string	"NULL" Último token processado
	var $tokenType = FALSE;		// @var tokenType bool	"FALSE" Último tipo de token
	var $pos = 0;				// @var pos int			"0" Offset atual dentro da string JSON
	var $looseType;				// @var looseType bool	Indica como objetos devem ser decodificados

	//!-----------------------------------------------------------------
	// @function	JSONDecoder::JSONDecoder
	// @desc		Construtor da classe
	// @param		looseType bool	"FALSE" Indica se objetos JSON serão tratados como objetos PHP ou arrays associativos
	// @access		public
	//!-----------------------------------------------------------------
	function JSONDecoder($looseType=FALSE) {
		parent::PHP2Go();
		$this->looseType = (bool)$looseType;
	}

	//!-----------------------------------------------------------------
	// @function	JSONDecoder::decode
	// @desc		Método estático para decodificação de uma string JSON
	// @param		str string		String no formato JSON
	// @param		looseType bool	"FALSE" Interpretar objetos JSON como arrays (TRUE) ou como objetos PHP (FALSE)
	// @return		mixed
	// @static
	//!-----------------------------------------------------------------
	function decode($str, $looseType=FALSE) {
		$decode = new JSONDecoder($looseType);
		return $decode->decodeValue($str);
	}

	//!-----------------------------------------------------------------
	// @function	JSONDecoder::decodeValue
	// @desc		Decodifica uma string JSON
	// @param		str string	String JSON
	// @return		mixed
	//!-----------------------------------------------------------------
	function decodeValue($str) {
		// remove comentários da string JSON
		$str = preg_replace(array(
			'~^\s*//(.+)$#~',
			'~^\s*/\*(.+)\*/~Us',
			'~/\*(.+)\*/\s*$~Us'
		), '', $str);
		$this->src = $str;
		$this->length = strlen($str);
		$this->pos = 0;
		$this->_getToken();
		return $this->_decodeToken();
	}

	//!-----------------------------------------------------------------
	// @function	JSONDecoder::_decodeToken
	// @desc		Processa o último token lido da string JSON
	// @access		private
	// @return		mixed
	//!-----------------------------------------------------------------
	function _decodeToken() {
		if ($this->tokenType == JSON_DATA) {
			$token = $this->token;
			$this->_getToken();
			return $token;
		} elseif ($this->tokenType == JSON_LEFT_BRACE) {
			return $this->_decodeObject();
		} elseif ($this->tokenType == JSON_LEFT_BRACKET) {
			return $this->_decodeArray();
		} else {
			return NULL;
		}
	}

	//!-----------------------------------------------------------------
	// @function	JSONDecoder::_decodeObject
	// @desc		Decodifica um objeto JSON
	// @access		private
	// @return		mixed
	//!-----------------------------------------------------------------
	function _decodeObject() {
		$properties = array();
		$tokenType = $this->_getToken();
		while ($tokenType && $tokenType != JSON_RIGHT_BRACE) {
			if ($tokenType == JSON_CONTINUE) {
				$key = '';
				while ($tokenType == JSON_CONTINUE) {
					$key .= $this->token;
					$tokenType = $this->_getToken();
				}
			} elseif (is_string($this->token)) {
				$key = $this->token;
				$tokenType = $this->_getToken();
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_INVALID_PROPNAME', $this->token), E_USER_ERROR, __FILE__, __LINE__);
			}
			if ($tokenType != JSON_COLON)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_MISSING_COLON', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
			$tokenType = $this->_getToken();
			if ($tokenType && $tokenType != JSON_CONTINUE && $tokenType != JSON_RIGHT_BRACE) {
				$properties[$key] = $this->_decodeToken();
				$tokenType = $this->tokenType;
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_INVALID_PROPVALUE', $this->token), E_USER_ERROR, __FILE__, __LINE__);
			}
			if ($tokenType == JSON_RIGHT_BRACE) {
				break;
			}
			if ($tokenType != JSON_COMMA)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_MISSING_COMMA', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
			$tokenType = $this->_getToken();
		}
		if ($tokenType == JSON_RIGHT_BRACE) {
			$this->_getToken();
		}
		if ($this->looseType) {
			return $properties;
		} else {
			$obj = new stdClass();
			foreach ($properties as $key => $value)
				$obj->{$key} = $value;
			return $obj;
		}
	}

	//!-----------------------------------------------------------------
	// @function	JSONDecoder::_decodeArray
	// @desc		Decodifica um array JSON
	// @access		private
	// @return		array
	//!-----------------------------------------------------------------
	function _decodeArray() {
		$items = array();
		$tokenType = $this->_getToken();
		while ($tokenType && $tokenType != JSON_RIGHT_BRACKET) {
			$items[] = $this->_decodeToken();
			$tokenType = $this->tokenType;
			if ($tokenType == JSON_RIGHT_BRACKET) {
				break;
			}
			if ($tokenType != JSON_COMMA)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_MISSING_COMMA', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
			$tokenType = $this->_getToken();
		}
		if ($tokenType == JSON_RIGHT_BRACKET) {
			$this->_getToken();
		}
		return $items;
	}

	//!-----------------------------------------------------------------
	// @function	JSONDecoder::_getToken
	// @desc		Lê um token da string JSON
	// @return		int Tipo de token encontrado (vide constantes da classe)
	// @note		Retorna FALSE quando o fim da string é atingido
	// @access		private
	//!-----------------------------------------------------------------
	function _getToken() {
		$this->token = '';
		$this->_skipWhitespace();
		if ($this->pos >= $this->length) {
			$this->tokenType = FALSE;
			return FALSE;
		}
		$str =& $this->src;
		$start = $i = $this->pos;
		switch ($str[$i]) {
			case '{' :
				$this->tokenType = JSON_LEFT_BRACE;
				$this->token = $str[$i];
				$i++;
				break;
			case '}' :
				$this->tokenType = JSON_RIGHT_BRACE;
				$this->token = $str[$i];
				$i++;
				break;
			case '[' :
				$this->tokenType = JSON_LEFT_BRACKET;
				$this->token = $str[$i];
				$i++;
				break;
			case ']' :
				$this->tokenType = JSON_RIGHT_BRACKET;
				$this->token = $str[$i];
				$i++;
				break;
			case ',' :
				$this->tokenType = JSON_COMMA;
				$this->token = $str[$i];
				$i++;
				break;
			case ':' :
				$this->tokenType = JSON_COLON;
				$this->token = $str[$i];
				$i++;
				break;
			case '/' :
				$i++;
				if ($i < $this->length) {
					if ($str[$i] == '/') {
						$i++;
						$char = NULL;
						while ($i < $this->length && $char != "\n") {
							$char = $str[$i];
							$i++;
						}
						$this->pos = $i;
						return $this->_getToken();
					}
					if ($str[$i] == '*') {
						$i++;
						$char = NULL;
						$found = FALSE;
						while ($i < $this->length) {
							$char = $str[$i];
							$i++;
							if ($char == '*' && @$str[$i] == '/') {
								$i++;
								break;
							}
						}
						$this->pos = $i;
						return $this->_getToken();
					}
				}
			case '"' :
			case '\'' :
				$delimiter = $str[$i];
				$buf = '';
				while ($i < ($this->length-1)) {
					$i++;
					$char = $str[$i];
					if ($char == '\\') {
						$i++;
						if ($i >= $this->length)
							break;
						$char = $str[$i];
						switch ($char) {
							case $delimiter : $buf .= $delimiter; break;
							case '\\' : $buf .= '\\'; break;
							case '/' : $buf .= '/'; break;
							case 'b' : $buf .= chr(8); break;
							case 't' : $buf .= chr(9); break;
							case 'n' : $buf .= chr(10); break;
							case 'f' : $buf .= chr(12); break;
							case 'r' : $buf .= chr(13); break;
							default : PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_ESCAPE_SEQUENCE', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
						}
					} elseif ($char == $delimiter) {
						$i++;
						break;
					} else {
						$buf .= $char;
					}
				}
				if ($char != $delimiter)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_JSON_UNTERMINATED_STRING', $this->pos), E_USER_ERROR, __FILE__, __LINE__);
				$this->tokenType = JSON_DATA;
				$this->token = $buf;
				break;
			default :
				if ($this->tokenType != JSON_CONTINUE) {
					$matches = array();
					preg_match('/true|false|null/', $str, $matches, PREG_OFFSET_CAPTURE, $this->pos);
					if (preg_match('/true|false|null/', $str, $matches, PREG_OFFSET_CAPTURE, $this->pos) && $matches[0][1] == $this->pos) {
						$this->tokenType = JSON_DATA;
						$this->token = ($matches[0][0] == "true" ? TRUE : ($matches[0][0] == "false" ? FALSE : NULL));
						$i += strlen($matches[0][0]);
					} elseif (preg_match('/-?([0-9])+(\.[0-9]*)?((e|E)((-|\+)?)[0-9]+)?/s', $str, $matches, PREG_OFFSET_CAPTURE, $this->pos) && $matches[0][1] == $this->pos) {
						$this->tokenType = JSON_DATA;
						$int = intval($matches[0][0]);
						$float = doubleval($matches[0][0]);
						$this->token = ($int == $float ? $int : $float);
						$i += strlen($matches[0][0]);
					} else {
						$this->tokenType = JSON_CONTINUE;
						$this->token = $str[$i];
						$i++;
					}
				} else {
					$this->tokenType = JSON_CONTINUE;
					$this->token = $str[$i];
					$i++;
				}
				break;
		}
		$this->pos = $i;
		return $this->tokenType;
	}

	//!-----------------------------------------------------------------
	// @function	JSONDecoder::_skipWhitespace
	// @desc		Ignora quaisquer caracteres brancos antes do próximo token a ser lido
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _skipWhitespace() {
		$matches = array();
		$result = preg_match("/(\s|\t|\n|\r|\f|\b)*/", $this->src, $matches, PREG_OFFSET_CAPTURE, $this->pos);
		if ($result && $matches[0][1] == $this->pos)
			$this->pos += strlen($matches[0][0]);
	}
}
?>