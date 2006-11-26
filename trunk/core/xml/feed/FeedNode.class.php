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
// $Header: /www/cvsroot/php2go/core/xml/feed/FeedNode.class.php,v 1.6 2006/05/07 15:12:41 mpont Exp $
// $Date: 2006/05/07 15:12:41 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FeedNode
// @desc		Classe base para as classes FeedChannel e FeedItem, com métodos
//				utilitários para a manipulação de atributos e nodos filhos. Também
//				utilizada na renderização do conteúdo de um canal ou item
// @package		php2go.xml.feed
// @extends		PHP2Go
// @uses		Date
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class FeedNode extends PHP2Go
{
	var $children = array();	// @var children array		"array()" Array com os nodos filhos

	//!-----------------------------------------------------------------
	// @function	FeedNode::FeedNode
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function FeedNode() {
		parent::PHP2Go();
	}

	//!-----------------------------------------------------------------
	// @function	FeedNode::getElement
	// @desc		Busca o valor de um determinado elemento
	// @param		name string		Nome do elemento
	// @param		fallback mixed	"NULL" Valor de retorno, caso o elemento não exista
	// @return		mixed Valor do elemento se encontrado, ou o valor do param. $fallback
	// @access		public	
	//!-----------------------------------------------------------------
	function getElement($name, $fallback=NULL) {
		if (array_key_exists($name, get_object_vars($this)))
			return $this->{$name};
		return $fallback;
	}

	//!-----------------------------------------------------------------
	// @function	FeedNode::getChildren
	// @desc		Retorna o conjunto de nodos filhos
	// @return		array Nodos filhos
	// @access		public	
	//!-----------------------------------------------------------------
	function getChildren() {
		return $this->children;
	}

	//!-----------------------------------------------------------------
	// @function	FeedNode::setElement
	// @desc		Define o valor de um elemento do nodo
	// @param		name string		Nome do elemento
	// @param		value mixed		Valor do elemento
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setElement($name, $value) {
		if (!empty($name))
			$this->{$name} = $value;
	}

	//!-----------------------------------------------------------------
	// @function	FeedNode::addElement
	// @desc		Adiciona uma nova entrada de um elemento do nodo (elementos compostos)
	// @param		name string		Nome do elemento
	// @param		value mixed		Valor para a nova instância do elemento
	// @note		Este método deve ser utilizado em elementos que podem se repetir
	//				dentro dos nodos filhos de um canal ou item
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function addElement($name, $value) {
		$this->{$name}[] = $value;
	}

	//!-----------------------------------------------------------------
	// @function	FeedNode::addChild
	// @desc		Adiciona um nodo filho
	// @param		Child FeedNode object	Nodo filho
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function addChild($Child) {
		$this->children[] = $Child;
	}

	//!-----------------------------------------------------------------
	// @function	FeedNode::parseDate
	// @desc		Tenta converter um valor de data de um atributo de canal
	//				ou item para unix timestamp, considerando que os valores de
	//				data de feeds são apresentados em formatos padrão
	// @param		date mixed	Valor do tipo data
	// @return		mixed Timestamp correspondente ou a própria data se o formato não for reconhecido
	// @access		protected	
	//!-----------------------------------------------------------------
	function parseDate($date) {
		$matches = array();
		// data em formato unix timestamp
		if (TypeUtils::isInteger($date) && $date >= 0 && $date <= LONG_MAX) {
			return $date;
		}
		// data no formato do RFC 822
		elseif (preg_match("~(?:(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s+)?(\\d{1,2})\\s+([a-zA-Z]{3})\\s+(\\d{4})\\s+(\\d{2}):(\\d{2}):(\\d{2})\\s+(.*)~", $date, $matches)) {
			$months = Array("Jan"=>1, "Feb"=>2, "Mar"=>3, "Apr"=>4, "May"=>5, "Jun"=>6, "Jul"=>7, "Aug"=>8, "Sep"=>9, "Oct"=>10, "Nov"=>11, "Dec"=>12);
			$ts = mktime($matches[4], $matches[5], $matches[6], $months[$matches[2]], $matches[1], $matches[3]);
			if ($matches[7])
				$ts += Date::getTZDiff($matches[7]);
			return $ts;
		}
		// data no formato ISO 8601
		elseif (preg_match("~(\\d{4})-(\\d{2})-(\\d{2})T(\\d{2}):(\\d{2}):(\\d{2})(.*)~", $date, $matches)) {
			$ts = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
			if ($matches[7])
				$ts += Date::getTZDiff($matches[7]);
			return $ts;
		}
		// formato não reconhecido
		return $date;
	}

	//!-----------------------------------------------------------------
	// @function	FeedNode::buildDate
	// @desc		Aplica formatação a um atributo de data de um canal ou item de feed,
	//				de acordo com o tipo e a versão
	// @param		date int		Timestamp da data
	// @param		type int		Tipo do feed
	// @param		version string	Versão do feed
	// @return		string Data formatada
	// @access		public	
	//!-----------------------------------------------------------------
	function buildDate($date, $type, $version=NULL) {
		if (($type == FEED_RSS && $version == '1.0') || $type == FEED_ATOM)
			return Date::formatTime($date, DATE_FORMAT_ISO8601);
		return Date::formatTime($date, DATE_FORMAT_RFC822);
	}
}
?>