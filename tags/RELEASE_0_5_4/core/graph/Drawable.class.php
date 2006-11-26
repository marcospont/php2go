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
// $Header: /www/cvsroot/php2go/core/graph/Drawable.class.php,v 1.4 2006/05/07 15:21:50 mpont Exp $
// $Date: 2006/05/07 15:21:50 $

//!-----------------------------------------------------------------
// @class		Drawable
// @desc		Classe abstrata que serve como base para todos os objetos
//				que podem ser desenhados em uma imagem
// @package		php2go.graph
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class Drawable extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	Drawable::Drawable
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Drawable() {
		parent::PHP2Go();
		if ($this->isA('Drawable', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Drawable'), E_USER_ERROR, __FILE__, __LINE__);
	}
	
	//!-----------------------------------------------------------------
	// @function	Drawable::draw
	// @desc		Desenha o objeto na imagem $Img
	// @access		public
	// @param		&Img Image object Imagem onde o objeto deve ser desenhado
	//!-----------------------------------------------------------------
	function draw(&$Img) {
	}
}
?>