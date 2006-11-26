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
// $Header: /www/cvsroot/php2go/core/graph/shape/ImageText.class.php,v 1.3 2006/02/28 21:55:57 mpont Exp $
// $Date: 2006/02/28 21:55:57 $

// @const GDFONT_1 "1"
// Fonte interna do GD tamanho 1
define('GDFONT_1', 1);
// @const GDFONT_2 "2"
// Fonte interna do GD tamanho 2
define('GDFONT_2', 2);
// @const GDFONT_3 "3"
// Fonte interna do GD tamanho 3
define('GDFONT_3', 3);
// @const GDFONT_4 "4"
// Fonte interna do GD tamanho 4
define('GDFONT_4', 4);
// @const GDFONT_5 "5"
// Fonte interna do GD tamanho 5
define('GDFONT_5', 5);

//!-----------------------------------------------------------------
// @class		ImageText
// @desc		Elemento de texto que pode ser inserido em uma imagem
//				em um determinado ponto X,Y, usando as fontes internas do GD
// @package		php2go.graph.shape
// @extends		Drawable
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class ImageText extends Drawable 
{
	var $text;				// @var text string		Texto
	var $x;					// @var x int			Coordenada X	
	var $y;					// @var y int			Coordenada Y
	var $font;				// @var font int		Fonte
	var $vertical = FALSE;	// @var vertical bool	"FALSE" Orientaчуo do texto: horizontal (FALSE) ou vertical (TRUE)
	
	//!-----------------------------------------------------------------
	// @function	ImageText::ImageText
	// @desc		Construtor da classe
	// @access		public
	// @param		text string		Texto
	// @param		x int			Coordenada X
	// @param		y int			Coordenada Y
	// @param		font int		"GDFONT_1" Fonte a ser utilizada (ver constantes da classe)
	// @param		vertical bool	"FALSE" TRUE para texto vertical, FALSE para horizontal
	// @note		A coordenada X,Y informada corresponde ao canto superior esquerdo para texto
	//				horizontal e ao canto superior direito para texto vertical
	// @note		As contantes GDFONT_[1-5] contidas na classe funcionam como um atalho para
	//				os 5 tamanhos da fonte interna da biblioteca GD
	//!-----------------------------------------------------------------
	function ImageText($text, $x, $y, $font=GDFONT_1, $vertical=FALSE) {
		parent::Drawable();
		$this->text = $text;
		$this->x = (int)$x;
		$this->y = (int)$y;
		$this->font = $font;
		$this->vertical = (bool)$vertical;
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageText::draw
	// @desc		Desenha o texto na imagem Img
	// @access		public
	// @param		&Img Image object	Imagem para inclusуo do texto
	// @return		void
	//!-----------------------------------------------------------------
	function draw(&$Img) {
		$func = ($this->vertical ? 'imagestringup' : 'imagestring');
		$func($Img->handle, $this->font, $this->x, $this->y, $this->text, $Img->currentColor);		
	}
}

?>