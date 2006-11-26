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
// $Header: /www/cvsroot/php2go/core/LocaleNegotiator.class.php,v 1.2 2006/02/28 21:55:49 mpont Exp $
// $Date: 2006/02/28 21:55:49 $

//!-----------------------------------------------------------------
// @class		LocaleNegotiator
// @desc		A classe LocaleNegotiator é responsável por buscar as linguagens
//				e os charsets enviados nos pelo cliente nos cabeçalhos HTTP
//				Accept-Language e Accept-Charset. No processo de inicialização do
//				framework, em caso de auto detecção habilitada, esta classe compara
//				as linguagens e charsets suportados com os disponibilizados para
//				definir as configurações a serem utilizadas
// @author		Marcos Pont
// @version		$Revision: 1.2 $
//!-----------------------------------------------------------------
class LocaleNegotiator
{
	var $languages;		// @var languages array		Conjunto de linguegens suportadas pelo cliente
	var $charsets;		// @var charsets array		Conjunto de charsets suportados pelo cliente
	
	//!-----------------------------------------------------------------
	// @function	LocaleNegotiator::LocaleNegotiator
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function LocaleNegotiator() {
		$this->languages = $this->_parseHeader(@$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$this->charsets = $this->_parseHeader(@$_SERVER['HTTP_ACCEPT_CHARSET']);		
	}
	
	//!-----------------------------------------------------------------
	// @function	LocaleNegotiator::&getInstance
	// @desc		Retorna uma instância única da classe
	// @return		LocaleNegotiator object
	// @access		public	
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new LocaleNegotiator();
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	LocaleNegotiator::getSupportedLanguages
	// @desc		Retorna o conjunto de linguagens suportadas pelo cliente
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getSupportedLanguages() {
		$tmp = array();
		foreach ($this->languages as $k => $v)
			$tmp[] = $k . ($v == 1.0 ? '' : ';' . $v);
		return $tmp;
	}
	
	//!-----------------------------------------------------------------
	// @function	LocaleNegotiator::getSupportedCharsets
	// @desc		Retorna o conjunto de charsets suportados pelo cliente
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getSupportedCharsets() {
		$tmp = array();
		foreach ($this->charsets as $k => $v)
			$tmp[] = $k . ($v == 1.0 ? '' : ';' . $v);
		return $tmp;
	}
	
	//!-----------------------------------------------------------------
	// @function	LocaleNegotiator::negotiateLanguage
	// @desc		Compara as linguagens disponíveis com as linguagens suportadas
	//				para definir o valor a ser utilizado
	// @param		available array		Linguagens disponíveis
	// @param		default string		Linguagem padrão, se nenhuma das disponíveis for suportada
	// @return		string Linguagem a ser utilizada
	// @access		public
	//!-----------------------------------------------------------------
	function negotiateLanguage($available, $default) {
		$available = (array)$available;
		foreach ($this->languages as $lang => $level) {
			$result = $this->_compare($lang, $available);
			if ($result)
				return $result;
		}
		return $default;		
	}
	
	//!-----------------------------------------------------------------
	// @function	LocaleNegotiator::negotiateCharset
	// @desc		Compara os charsets disponíveis com os suportados para definir o valor a ser utilizado
	// @param		available array		Charsets disponíveis
	// @param		default string		Charset padrão, se nenhum dos disponíveis for suportado
	// @return		string Charset a ser utilizada
	// @access		public
	//!-----------------------------------------------------------------
	function negotiateCharset($available, $default) {
		$available = (array)$available;
		foreach ($this->charsets as $charset => $level) {
			$result = $this->_compare($charset, $available);
			if ($result)
				return $result;
		}
		return $default;
	}
	
	//!-----------------------------------------------------------------
	// @function	LocaleNegotiator::_parseHeader
	// @desc		Método utilitário para interpretar o valor dos headers
	//				Accept-Language e Accept-Charset
	// @access		private
	// @param		str string	Valor do header
	// @return		array Valores capturados
	//!-----------------------------------------------------------------
	function _parseHeader($str) {
		$result = array();
		$str = (string)$str;
		if ($token = strtok($str, ', ')) {
			do {
				$pos = strpos($token, ';');
				if ($pos !== FALSE) {
					$name = substr($token, 0, $pos);
					$level = (float)substr($token, $pos+3);
				} else {
					$name = $token;
					$level = 1.0;
				}
				$result[$name] = $level;
			} while ($token = strtok(', '));
			asort($result, SORT_NUMERIC);
			return array_reverse($result);
		}
		return $result;
    }
    
    //!-----------------------------------------------------------------
    // @function	LanguageNegotiator::_compare
    // @desc		Buscar um valor em um array, utilizando comparação
    //				binary safe insensível ao caso
    // @param		value string	Valor de pesquisa
    // @param		array array		Array de valores com os quais o valor será comparado
    // @return		mixed Valor encontrado ou FALSE se nenhuma comparação retornar sucesso
    // @access		private
    //!-----------------------------------------------------------------
    function _compare($value, $array) {
    	$l = strlen($value);
    	foreach ($array as $comp) {
    		if (strncasecmp($value, $comp, $l) === 0)
    			return $comp;
    	}
    	return FALSE;
    }
}
?>