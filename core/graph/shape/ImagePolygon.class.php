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
// $Header: /www/cvsroot/php2go/core/graph/shape/ImagePolygon.class.php,v 1.3 2006/02/28 21:55:57 mpont Exp $
// $Date: 2006/02/28 21:55:57 $

//!-----------------------------------------------------------------
// @class		ImagePolygon
// @desc		Representa um polνgono com N pontos X,Y
// @package		php2go.graph.shape
// @extends		Drawable
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class ImagePolygon extends Drawable
{
	var $points;		// @var points array	Conjunto de pontos
	var $fill = FALSE;	// @var fill bool		"FALSE" Com ou sem preenchimento

	//!-----------------------------------------------------------------
	// @function	ImagePolygon::ImagePolygon
	// @desc		Construtor do objeto
	// @access		public
	// @param		points array	Conjunto de pontos
	// @param		fill bool		"FALSE" Habilita ou desabilita preenchimento no polνgono
	// @note		O array de pontos deve ser uma seqόκncia alternada
	//				de pontos x e y: x1,y1,x2,y2,x3,y3,...,xn,yn
	// @note		A biblioteca GD exige que um polνgono tenha ao menos 3 pontos
	//!-----------------------------------------------------------------
	function ImagePolygon($points, $fill=FALSE) {
		parent::Drawable();
		$this->points = (array)$points;
		$this->fill = (bool)$fill;
	}
	
	//!-----------------------------------------------------------------
	// @function	ImagePolygon::draw
	// @desc		Desenha o polνgono na imagem $Img
	// @access		public
	// @param		&Img Image object	Imagem onde o polνgono deve ser inserido
	// @return		void
	//!-----------------------------------------------------------------
	function draw(&$Img) {
		$numPoints = floor(sizeof($this->points)/2);
		if ($this->fill)		
			imagefilledpolygon($Img->handle, $this->points, $numPoints, $Img->currentColor);
		else
			imagepolygon($Img->handle, $this->points, $numPoints, $Img->currentColor);
	}
}
?>