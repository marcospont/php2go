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
// $Header: /www/cvsroot/php2go/core/xml/feed/FeedItem.class.php,v 1.3 2006/02/28 21:56:05 mpont Exp $
// $Date: 2006/02/28 21:56:05 $

//------------------------------------------------------------------
import('php2go.xml.feed.FeedNode');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FeedItem
// @desc		Esta classe representa um item dentro de um canal de informaчуo
//				do tipo RSS ou ATOM. O item contщm atributos sobre o registro,
//				como descriчуo, URL de localizaчуo do conteњdo completo, sumсrio,
//				resumo, data de publicaчуo/modificaчуo, entre outros
// @package		php2go.xml.feed
// @extends		FeedNode
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class FeedItem extends FeedNode
{
	//!-----------------------------------------------------------------
	// @function	FeedItem::FeedItem
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function FeedItem() {
		parent::FeedNode();
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedItem::setElement
	// @desc		Define o valor de um elemento do item
	// @access		public
	// @param		name string		Nome do elemento
	// @param		value mixed		Valor do elemento
	// @return		void
	//!-----------------------------------------------------------------
	function setElement($name, $value) {
		$upper = strtoupper($name);
		if ($upper == 'PUBDATE' || $upper == 'MODIFIED' || 
			$upper == 'ISSUED' || $upper == 'CREATED' ||
			$upper == 'PUBLISHED' || $upper == 'UPDATED')
			$value = parent::parseDate($value);
		parent::setElement($name, $value); 
	}
}
?>