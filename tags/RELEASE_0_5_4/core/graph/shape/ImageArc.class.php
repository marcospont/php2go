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
// $Header: /www/cvsroot/php2go/core/graph/shape/ImageArc.class.php,v 1.2 2006/02/28 21:55:57 mpont Exp $
// $Date: 2006/02/28 21:55:57 $

//!-----------------------------------------------------------------
// @class		ImageArc
// @desc		Representa um arco, com as coordenadas do centro, largura e altura,
//				ângulo inicial e final, com ou sem preenchimento
// @package		php2go.graph.shape
// @extends		Drawable
// @author		Marcos Pont
// @version		$Revision: 1.2 $
//!-----------------------------------------------------------------
class ImageArc extends Drawable
{
	var $cx;					// @var cx int				Coordenada X do centro
	var $cy;					// @var cy int				Coordenada Y do centro
	var $width;					// @var width int			Largura
	var $height;				// @var height int			Altura
	var $startAngle;			// @var startAngle int		Ângulo inicial
	var $endAngle;				// @var endAngle int		Ângulo final
	var $fill = FALSE;			// @var fill bool			"FALSE" Tipo de preenchimento
	var $shadow = 0;			// @var shadow int			"0" Tamanho de sombra para o arco
	var $shadowColor = NULL;	// @var shadowColor mixed	"NULL" Cor de sombra
	
	//!-----------------------------------------------------------------
	// @function	ImageArc::ImageArc
	// @desc		Construtor do objeto
	// @access		public
	// @param		cx int				Coordenada X do centro
	// @param		cy int				Coordenada Y do centro
	// @param		width int			Largura
	// @param		height int			Altura
	// @param		startAngle int		Ângulo inicial
	// @param		endAngle int		Ângulo final
	// @param		fill bool			"FALSE" Tipo de preenchimento
	// @param		shadow int			"0" Tamanho de sombra
	// @param		shadowColor mixed	"NULL" Cor de sombra
	// @note		O parâmetro $fill aceita os valores IMG_ARC_PIE, IMG_ARC_CHORD, IMG_ARC_NOFILL e IMG_ARC_EDGED
	//!-----------------------------------------------------------------
	function ImageArc($cx, $cy, $width, $height, $startAngle, $endAngle, $fill=FALSE, $shadow=0, $shadowColor=NULL) {
		parent::Drawable();
		$this->cx = $cx;
		$this->cy = $cy;
		$this->width = $width;
		$this->height = $height;
		while ($startAngle < 0)
			$startAngle += 360;
		$this->startAngle = $startAngle;
		while ($endAngle < 0)
			$endAngle += 360;
		$this->endAngle = $endAngle;
		$this->fill = $fill;
		$this->shadow = $shadow;
		$this->shadowColor = $shadowColor;
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageArc::draw
	// @desc		Desenha o arco na imagem $Img
	// @access		public
	// @param		&Img Image object	Imagem para inclusão do arco
	// @return		void
	//!-----------------------------------------------------------------
	function draw(&$Img) {
		if ($this->shadow > 0) {
			$sc = (TypeUtils::isNull($this->shadowColor) ? $Img->currentColor : $Img->allocateColor($this->shadowColor));
			for ($i=0; $i<$this->shadow; $i++) {				
				$cy = $this->cy + $i;
				if ($this->fill)
					imagefilledarc($Img->handle, $this->cx, $cy, $this->width, $this->height, $this->startAngle, $this->endAngle, $sc, $this->fill);
				else
					imagearc($Img->handle, $this->cx, $cy, $this->width, $this->height, $this->startAngle, $this->endAngle, $sc);
			}
			$cy++;
		} else {
			$cy = $this->cy;
		}
		if ($this->fill)
			imagefilledarc($Img->handle, $this->cx, $cy, $this->width, $this->height, $this->startAngle, $this->endAngle, $Img->currentColor, $this->fill);
		else
			imagearc($Img->handle, $this->cx, $cy, $this->width, $this->height, $this->startAngle, $this->endAngle, $Img->currentColor);
	}
}
?>