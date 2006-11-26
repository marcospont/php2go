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
// $Header: /www/cvsroot/php2go/core/xml/feed/FeedChannel.class.php,v 1.4 2006/05/07 15:22:23 mpont Exp $
// $Date: 2006/05/07 15:22:23 $

//------------------------------------------------------------------
import('php2go.xml.feed.FeedNode');
import('php2go.xml.feed.FeedItem');
import('php2go.util.AbstractList');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FeedChannel
// @desc		Esta classe funciona como base para armazenamento de informações
//				de um canal de informação estruturado de acordo com os padrões RSS
//				e ATOM. Os atributos e itens associados a uma instância da classe 
//				FeedChannel são populados através das classes FeedReader e FeedCreator
// @package		php2go.xml.feed
// @extends		FeedNode
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class FeedChannel extends FeedNode 
{
	//!-----------------------------------------------------------------
	// @function	FeedChannel::FeedChannel
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------	
	function FeedChannel() {
		parent::FeedNode();
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedChannel::setElement
	// @desc		Seta o valor de um atributo do canal
	// @access		public
	// @param		name string		Nome do atributo
	// @param		value mixed		Valor do atributo
	// @return		void
	// @note		Atributos do tipo DATE serão convertidos para timestamp
	//				se estiverem nos formatos RFC 822 ou ISO 8601
	//!-----------------------------------------------------------------
	function setElement($name, $value) {
		$upper = strtoupper($name);
		if ($upper == 'LASTBUILDDATE' || $upper == 'PUBDATE' || 
			$upper == 'MODIFIED' || $upper == 'UPDATED')
			$value = parent::parseDate($value);
		parent::setElement($name, $value);
	}	
	
	//!-----------------------------------------------------------------
	// @function	FeedChannel::addItem
	// @desc		Adiciona um item ao canal
	// @access		public
	// @param		Item FeedItem object	Item a ser adicionado
	// @return		bool
	//!-----------------------------------------------------------------
	function addItem($Item) {
		if (TypeUtils::isInstanceOf($Item, 'FeedItem')) {
			parent::addChild($Item);
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedChannel::itemIterator
	// @desc		Constrói um iterator para a lista de itens do canal
	// @access		public
	// @return		ListIterator object	
	//!-----------------------------------------------------------------
	function itemIterator() {
		$List = new AbstractList($this->getChildren());
		return $List->iterator();
	}
}
?>