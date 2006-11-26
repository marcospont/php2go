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
// $Header: /www/cvsroot/php2go/core/graph/shape/ImageLine.class.php,v 1.3 2006/02/28 21:55:57 mpont Exp $
// $Date: 2006/02/28 21:55:57 $

//!-----------------------------------------------------------------
// @class		ImageLine
// @desc		Representa uma linha reta com duas coordenadas X,Y
// @package		php2go.graph.shape
// @extends		Drawable
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class ImageLine extends Drawable
{
	var $x1;	// @var x1 int		Coordenada X inicial
	var $y1;	// @var y1 int		Coordenada Y inicial
	var $x2;	// @var x2 int		Coordenada X final
	var $y2;	// @var y2 int		Coordenada Y final
	
	//!-----------------------------------------------------------------
	// @function	ImageLine::ImageLine
	// @desc		Construtor do objeto
	// @access		public
	// @param		x1 int	Coordenada X inicial
	// @param		y1 int	Coordenada Y inicial
	// @param		x2 int	Coordenada X final
	// @param		y2 int	Coordenada Y final
	//!-----------------------------------------------------------------
	function ImageLine($x1, $y1, $x2, $y2) {
		parent::Drawable();
		$this->x1 = $x1;
		$this->y1 = $y1;
		$this->x2 = $x2;
		$this->y2 = $y2;
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageLine::draw
	// @desc		Desenha a linha na imagem $Img
	// @access		public
	// @param		&Img Image object	Imagem onde a linha deve ser inserida
	// @return		void
	//!-----------------------------------------------------------------
	function draw(&$Img) {		
		imageline($Img->handle, $this->x1, $this->y1, $this->x2, $this->y2, $Img->currentColor);
	}
}
?>