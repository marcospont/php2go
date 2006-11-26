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
// $Header: /www/cvsroot/php2go/core/graph/shape/ImageTTFText.class.php,v 1.5 2006/02/28 21:55:57 mpont Exp $
// $Date: 2006/02/28 21:55:57 $

//-----------------------------------------
import('php2go.graph.shape.ImageText');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		ImageTTFText
// @desc		Elemento de texto que pode ser inserido em uma imagem
//				em um determinado ponto X,Y, usando uma fonte TrueType a
//				partir de seu nome de arquivo
// @package		php2go.graph.shape
// @extends		ImageText
// @author		Marcos Pont
// @version		$Revision: 1.5 $
//!-----------------------------------------------------------------
class ImageTTFText extends ImageText
{
	var $fontFile;				// @var fontFile string		Arquivo de fonte TrueType
	var $size;					// @var size int			Altura do texto
	var $angle;					// @var angle int			Ângulo do texto
	var $shadow = 0;			// @var shadow int			"0" Tamanho de sombra para o texto
	var $shadowColor = NULL;	// @var shadowColor mixed	"NULL" Cor de sombra	
	
	//!-----------------------------------------------------------------
	// @function	ImageTTFText::ImageTTFText
	// @desc		Construtor da classe
	// @access		public
	// @param		text string			Texto
	// @param		x int				Coordenada X
	// @param		y int				Coordenada Y
	// @param		fontFile string		Nome do arquivo da fonte TrueType
	// @param		size int			Altura do texto
	// @param		angle int			"0" Ângulo do texto
	// @param		shadow int			"0" Tamanho de sombra para o texto
	// @param		shadowColor mixed	"NULL" Cor de sombra
	// @note		A coordenada X,Y informada corresponde ao canto inferior esquerdo do texto
	// @note		O parâmetro $fontFile deve conter somente o nome do arquivo. O caminho utiliza
	//				a entrada de configuração RESOURCES.TTF_PATH ou um caminho padrão dependendo do
	//				sistema operacional do servidor
	//!-----------------------------------------------------------------
	function ImageTTFText($text, $x, $y, $fontFile, $size, $angle=0, $shadow=0, $shadowColor=NULL) {
		parent::ImageText($text, $x, $y, NULL);
		$this->fontFile = $fontFile;
		$this->size = $size;
		$this->angle = $angle;
		$this->shadow = $shadow;
		$this->shadowColor = $shadowColor;
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageTTFText::draw
	// @desc		Desenha o texto na imagem Img
	// @access		public
	// @param		&Img Image object	Imagem para inclusão do texto
	// @return		void
	//!-----------------------------------------------------------------
	function draw(&$Img) {
		if (!ImageUtils::ttfSupported())
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'gd (TTF support)'), E_USER_ERROR, __FILE__, __LINE__);		
		if ($this->shadow > 0 && !empty($this->shadowColor)) {
			$sc = $Img->allocateColor($this->shadowColor);
			imagettftext($Img->handle, $this->size, $this->angle, ($this->x+$this->shadow), ($this->y+$this->shadow), $sc, $this->fontFile, $this->text);
		}			
		imagettftext($Img->handle, $this->size, $this->angle, $this->x, $this->y, $Img->currentColor, $this->fontFile, $this->text);
	}
}
?>