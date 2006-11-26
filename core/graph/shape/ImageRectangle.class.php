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
// $Header: /www/cvsroot/php2go/core/graph/shape/ImageRectangle.class.php,v 1.3 2006/02/28 21:55:57 mpont Exp $
// $Date: 2006/02/28 21:55:57 $

//!-----------------------------------------------------------------
// @class		ImageRectangle
// @desc		Representa um retângulo com duas coordenadas X,Y, com ou sem preenchimento
// @package		php2go.graph.shape
// @extends		Drawable
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class ImageRectangle extends Drawable
{
	var $x1;			// @var x1 int		Coordenada X inicial
	var $y1;			// @var y1 int		Coordenada Y inicial
	var $x2;			// @var x2 int		Coordenada X final
	var $y2;			// @var y2 int		Coordenada Y final
	var $fill = FALSE;	// @var fill bool	"FALSE" Indica se o retângulo deve ser preenchido
	
	//!-----------------------------------------------------------------
	// @function	ImageRectangle::ImageRectangle
	// @desc		Construtor do objeto
	// @access		public
	// @param		x1 int		Coordenada X inicial
	// @param		y1 int		Coordenada Y inicial
	// @param		x2 int		Coordenada X final
	// @param		y2 int		Coordenada Y final
	// @param		fill bool	"FALSE" Preencher ou não o retângulo ao desenhar
	//!-----------------------------------------------------------------
	function ImageRectangle($x1, $y1, $x2, $y2, $fill=FALSE) {
		parent::Drawable();
		$this->x1 = $x1;
		$this->y1 = $y1;
		$this->x2 = $x2;
		$this->y2 = $y2;
		$this->fill = (bool)$fill;
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageRectangle::draw
	// @desc		Desenha o retângulo na imagem $Img
	// @access		public
	// @param		&Img Image object	Imagem onde o retângulo deve ser desenhado
	// @return		void
	//!-----------------------------------------------------------------
	function draw(&$Img) {
		if ($this->fill)		
			imagefilledrectangle($Img->handle, $this->x1, $this->y1, $this->x2, $this->y2, $Img->currentColor);
		else
			imagerectangle($Img->handle, $this->x1, $this->y1, $this->x2, $this->y2, $Img->currentColor);
	}
}
?>