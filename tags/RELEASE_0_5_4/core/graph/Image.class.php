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
// $Header: /www/cvsroot/php2go/core/graph/Image.class.php,v 1.7 2006/05/07 15:23:54 mpont Exp $
// $Date: 2006/05/07 15:23:54 $

//-----------------------------------------
import('php2go.file.FileSystem');
import('php2go.graph.Drawable');
import('php2go.graph.ImageUtils');
import('php2go.graph.shape.*');
//-----------------------------------------

// @const IMAGETYPE_GD "17"
// Representa arquivos de imagem do tipo GD
define('IMAGETYPE_GD', 17);
// @const IMAGETYPE_GD2 "18"
// Representa arquivos de imagem do tipo GD2
define('IMAGETYPE_GD2', 18);
// @const IMAGEFLIP_HORIZONTAL "1"
// Inversão horizontal de imagem
define('IMAGEFLIP_HORIZONTAL', 1);
// @const IMAGEFLIP_VERTICAL "2"
// Inversão vertical de imagem
define('IMAGEFLIP_VERTICAL', 2);
// @const IMAGEFLIP_BOTH "3"
// Inversão horizontal e vertical de imagem
define('IMAGEFLIP_BOTH', 3);

//!-----------------------------------------------------------------
// @class		Image
// @desc		A class Image representa uma imagem existente ou em processo de criação,
//				utilizando a biblioteca GD. Através de seus métodos, é possível obter informações
//				sobre a imagem, adicionar elementos gráficos, manipular cores e pixels de seu conteúdo
// @package		php2go.graph
// @extends		PHP2Go
// @uses		FileSystem
// @uses		ImageUtils
// @uses		System
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.7 $
//!-----------------------------------------------------------------
class Image extends PHP2Go
{
	var $handle;		// @var handle resource		Resource da imagem
	var $width;			// @var width int			Largura da imagem
	var $height;		// @var height int			Altura da imagem
	var $currentColor;	// @var currentColor int	Índice da última cor utilizada
	
	//!-----------------------------------------------------------------
	// @function	Image::Image
	// @desc		Construtor da classe Image
	// @param		handle resource		Referência para o resource da imagem	
	// @access		public	
	//!-----------------------------------------------------------------
	function Image($handle) {
		parent::PHP2Go();
		if (!System::loadExtension('gd'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'gd'), E_USER_ERROR, __FILE__, __LINE__);			
		$this->handle = $handle;
		$this->width = @imagesx($this->handle);
		$this->height = @imagesy($this->handle);
		$this->currentColor = $this->allocateColor('#ffffff');
		PHP2Go::registerDestructor($this, '__destruct');
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::__destruct
	// @desc		Destrutor da classe, destrói o resource utilizado para manipular a imagem
	// @acess		public
	// @return		void	
	//!-----------------------------------------------------------------
	function __destruct() {
		if (TypeUtils::isResource($this->handle))
			imagedestroy($this->handle);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::&create
	// @desc		Cria uma nova imagem a partir de determinadas dimensões
	// @param		width int		Largura
	// @param		height int		Altura
	// @param		trueColor bool	"TRUE" Criar a imagem utilizando truecolor
	// @note		Se a versão disponível do GD for menor do que 2, a opção truecolor será ignorada
	// @access		public
	// @return		Image object	
	// @static
	//!-----------------------------------------------------------------
	function &create($width, $height, $trueColor=TRUE) {
		if (!System::loadExtension('gd'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'gd'), E_USER_ERROR, __FILE__, __LINE__);
		$version = ImageUtils::getGDVersion();
		if ($trueColor && $version >= 2)
			$handle = imagecreatetruecolor((int)$width, (int)$height);
		else
			$handle = imagecreate((int)$width, (int)$height);
		$Image = new Image($handle);
		return $Image;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::&loadFromFile
	// @desc		Cria uma imagem a partir de um arquivo
	// @param		path string		Caminho completo para o arquivo
	// @param		type string		"NULL" Tipo do arquivo (ver constantes da classe)
	// @access		public	
	// @return		Image object
	// @static
	//!-----------------------------------------------------------------
	function &loadFromFile($path, $type=NULL) {
		if (!System::loadExtension('gd'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'gd'), E_USER_ERROR, __FILE__, __LINE__);
		if (!is_readable($path))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $path), E_USER_ERROR, __FILE__, __LINE__);
		switch ($type) {
			case IMAGETYPE_GD :
				$handle = imagecreatefromgd($path);
				break;
			case IMAGETYPE_GD2 :
				$handle = imagecreatefromgd2($path);
				break;
			case IMAGETYPE_GIF :
				$handle = imagecreatefromgif($path);
				break;
			case IMAGETYPE_JPEG :
				$handle = imagecreatefromjpeg($path);
				break;
			case IMAGETYPE_PNG :
				$handle = imagecreatefrompng($path);
				break;
			case IMAGETYPE_WBMP :
				$handle = imagecreatefromwbmp($path);
				break;
			case IMAGETYPE_XBM :
				$handle = imagecreatefromxbm($path);
				break;
			default :
				$handle = imagecreatefromstring(FileSystem::getContents($path));					
		}
		$Image = new Image($handle);
		return $Image;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::&getHandle
	// @desc		Retorna o handle associado à imagem
	// @access		public
	// @return		resource
	//!-----------------------------------------------------------------
	function &getHandle() {
		return $this->handle;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::getWidth
	// @desc		Retorna a largura da imagem, em pixels
	// @access		public
	// @return		int Largura da imagem
	//!-----------------------------------------------------------------
	function getWidth() {
		return $this->width;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::getHeight
	// @desc		Retorna a altura da imagem, em pixels
	// @access		public
	// @return		int Altura da imagem
	//!-----------------------------------------------------------------
	function getHeight() {
		return $this->height;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::getColorAt
	// @desc		Retorna informações de cor para um determinado ponto x,y da imagem
	// @access		public
	// @param		x int		Coordenada X
	// @param		y int		Coordenada Y
	// @return		array Vetor associativo contendo os valores RGB da cor do pixel selecionado
	//!-----------------------------------------------------------------
	function getColorAt($x, $y) {
		if ($x >= 0 && $x <= $this->width && $y >= 0 && $y < $this->height) {
			$color = imagecolorsforindex($this->handle, imagecolorat($this->handle, $x, $y));
			return array($color['red'], $color['green'], $color['blue']);
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::getTotalColors
	// @desc		Retorna o número de cores presentes na paleta de cores da imagem
	// @note		Se a imagem criada utiliza truecolor - em sua criação ou por ter sido
	//				carregada de um arquivo - esta função irá retornar zero
	// @return		int Número de cores da imagem
	// @access		public
	//!-----------------------------------------------------------------
	function getTotalColors() {
		return imagecolorstotal($this->handle);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::setColor
	// @desc		Define a cor a ser utilizada na imagem na próxima operação
	//				de preenchimento ou desenho
	// @param		color mixed	Identificador da cor (vetor com 3 componentes RGB ou string hexa)
	// @param		alpha int	"FALSE" Índice de transparência, entre 0 e 1
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setColor($color, $alpha=FALSE) {
		$this->currentColor = $this->allocateColor($color, $alpha);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::randomColor
	// @desc		Aloca uma cor aleatória, dados os limites do intervalo, entre 0 e 255
	// @param		min int	"0" Limite inferior para os valores dos componentes RGB
	// @param		max int	"255" Limite superior para os valores dos componentes RGB
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function randomColor($min=0, $max=255) {
		$color = array(rand($min, $max), rand($min, $max), rand($min, $max));
		$this->currentColor = $this->allocateColor($color);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::allocateColor
	// @desc		Recebe um identificador de cor, e retorna o índice correspondente.
	//				Utiliza um índice existente se a cor já existe na imagem, ou aloca
	//				uma nova posição se necessário
	// @access		public
	// @param		color mixed	Identificador de cor
	// @param		alpha int	"FALSE" Índice de transparência da cor, entre 0 e 1
	// @return		int Índice alocado para a cor
	//!-----------------------------------------------------------------
	function allocateColor($color, $alpha=FALSE) {
		// representação string hexadecimal
		if (TypeUtils::isString($color) && $color[0] == '#') {
			$color = ImageUtils::fromHexToRGB($color);		
		} 
		// representação array com valores dos 3 componentes
		elseif (TypeUtils::isArray($color) && count($color) == 3) {
			$red = max(min((int)$color[0], 255), 0);
			$green = max(min((int)$color[1], 255), 0);
			$blue = max(min((int)$color[2], 255), 0);
			$color = array($red, $green, $blue);
		} 
		// cor inválida
		else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_COLOR_SPEC', exportVariable($color)), E_USER_ERROR, __FILE__, __LINE__);
		}
		// cor com transparência
		$alpha = (float)$alpha;
		if ($alpha > 0 && $alpha < 1 && ImageUtils::getGDVersion() >= 2) {
			return imagecolorresolvealpha($this->handle, $color[0], $color[1], $color[2], round($alpha * 127));
		} else {
			// verifica se a cor já foi utilizada
			$index = imagecolorexact($this->handle, $color[0], $color[1], $color[2]);
			if ($index == -1) {
				// aloca uma nova cor
				$index = imagecolorallocate($this->handle, $color[0], $color[1], $color[2]);
				if ($index == -1)
					// utiliza uma cor aproximada se o limite foi excedido
					$index = imagecolorresolve($this->handle, $color[0], $color[1], $color[2]);
			}
			if ($index == -1)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ALLOCATE_COLOR'), E_USER_ERROR, __FILE__, __LINE__);
			return $index;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::getTransparency
	// @desc		Retorna a cor determinada como transparente na imagem
	// @access		public
	// @return		array Vetor associativo contendo os valores dos componentes
	//				vermelho, verde e azul da cor
	//!-----------------------------------------------------------------
	function getTransparency() {
		$color = imagecolorsforindex($this->handle, imagecolortransparent($this->handle));
		return array($color['red'], $color['green'], $color['blue']);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::setTransparency
	// @desc		Define uma determinada cor como transparente na imagem
	// @access		public
	// @param		color mixed	Cor para transparência
	// @return		void
	//!-----------------------------------------------------------------
	function setTransparency($color) {
		$color = $this->allocateColor($color);
		imagecolortransparent($this->handle, $color);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image:setAlphaBlending
	// @desc		Habilita ou desabilita combinação de cores na imagem
	// @access		public
	// @param		setting bool	"TRUE" Valor para o flag
	// @return		bool
	//!-----------------------------------------------------------------
	function setAlphaBlending($setting=TRUE) {
		if (function_exists('imagealphablending'))
			return imagealphablending($this->handle, (bool)$setting);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::setAntiAlias
	// @desc		Habilita ou desabilita anti alias na imagem
	// @access		public
	// @param		setting bool	"TRUE" Valor para o flag
	// @return		bool
	//!-----------------------------------------------------------------
	function setAntiAlias($setting=TRUE) {
		if (function_exists('imageantialias'))
			return imageantialias($this->handle, (bool)$setting);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::setInterlace
	// @desc		Habilita ou desabilita o bit de interlace da imagem
	// @access		public
	// @param		setting bool	"TRUE" Valor para o bit
	// @return		int Retorna 1 ou 0, indicando se o bit foi ligado ou desligado
	//!-----------------------------------------------------------------
	function setInterlace($setting=TRUE) {
		return imageinterlace($this->handle, ($setting ? 1 : 0));
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::correctGamma
	// @desc		Aplica correção gama na imagem
	// @access		public
	// @param		in float 	Gama de entrada
	// @param		out float	Gama de saída
	// @return		bool
	//!-----------------------------------------------------------------
	function correctGamma($in, $out) {
		if (TypeUtils::isFloat($in) && TypeUtils::isFloat($out))
			return imagegammacorrect($this->handle, $in, $out);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::setBrush
	// @desc		Define a imagem a ser utilizada como ferramenta de desenho
	//				de linhas e polígonos na imagem
	// @access		public
	// @param		Img Image object	Imagem a ser utilizada
	// @param		style array			"array()" Estilo (opcional) para o traço
	// @return		bool
	//!-----------------------------------------------------------------
	function setBrush($Img, $style=array()) {
		if (TypeUtils::isInstanceOf($Img, 'Image')) {
			$useStyle = $this->setStyle($style);
			$this->currentColor = ($useStyle ? IMG_COLOR_STYLEDBRUSHED : IMG_COLOR_BRUSHED);
			return imagesetbrush($this->handle, $Img->handle);			
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Image'), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::setStyle
	// @desc		Define um estilo a ser usado pelas funções de desenho na imagem
	// @note		Um estilo é um conjunto de pixels com definição de cor. Estilos podem
	//				ser usados para criar linhas com características como pontilhado ou tracejado
	// @param		colors array	Vetor de cores. Cada cor deve ser uma string hexadecimal ou um
	//								array com os 3 componentes RGB
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function setStyle($colors) {
		$colors = (array)$colors;
		if (!empty($colors)) {
			$tmp = array();
			foreach ($colors as $color)
				$tmp[] = $this->allocateColor($color);
			$this->currentColor = IMG_COLOR_STYLED;
			return imagesetstyle($this->handle, $tmp);
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::setFillingTile
	// @desc		Define a imagem de preenchimento, para uso em funções
	//				de preenchimento ou desenho de objetos com preenchimento
	// @param		Img Image object	Imagem de preenchimento
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function setFillingTile($Img) {
		if (TypeUtils::isInstanceOf($Img, 'Image')) {
			$this->currentColor = IMG_COLOR_TILED;
			return imagesettile($this->handle, $Img->handle);			
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Image'), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::setPixel
	// @desc		Define a cor de um pixel da imagem a partir de suas coordenadas
	// @access		public
	// @param		x int		Coordenada X
	// @param		y int		Coordenada Y
	// @param		color mixed	Cor para o pixel
	// @return		bool
	//!-----------------------------------------------------------------
	function setPixel($x, $y, $color) {
		$c = $this->allocateColor($color);
		return imagesetpixel($this->handle, $x, $y, $c);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::fill
	// @desc		Preenche a imagem com a cor $color, iniciando nas coordenadas $x e $y
	// @param		color int	Cor de preenchimento
	// @param		x int		"0" Coordenada x inicial
	// @param		y int		"0" Coordenada y inicial
	// @note		A cor deverá ser previamente alocada utilizando o método allocateColor
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function fill($x=0, $y=0) {
		if ($x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height)
			return imagefill($this->handle, $x, $y, $this->currentColor);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::fillToBorder
	// @desc		Preenche uma região da imagem delimitada pela cor $borderColor
	//				com a última cor alocada, iniciando nas coordenadas $x e $y	
	// @param		x int			Coordenada x inicial
	// @param		y int			Coordenada y inicial
	// @param		bColor mixed	Cor que delimita a região a ser preenchida
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function fillToBorder($x, $y, $bColor) {
		$bc = $this->allocateColor($bColor);
		if ($x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height)
			return imagefilltoborder($this->handle, $x, $y, $bc, $this->currentColor);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::draw
	// @desc		Insere um objeto Drawable na imagem
	// @access		public
	// @param		Obj Drawable object	Objeto a ser desenhado
	// @return		void
	//!-----------------------------------------------------------------
	function draw($Obj) {
		if (!TypeUtils::isInstanceOf($Obj, 'Drawable'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Drawable'), E_USER_ERROR, __FILE__, __LINE__);
		$Obj->draw($this);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::copy
	// @desc		Copia uma parte de uma outra imagem para dentro desta imagem
	// @note		Se os parâmetros de largura e altura forem omitidos, a imagem fonte inteira será copiada	
	// @param		SrcImg Image object	Imagem fonte
	// @param		destX int			"0" Coordenada inicial X na fonte
	// @param		destY int			"0" Coordenada inicial Y na fonte
	// @param		srcX int			"0" Coordenada inicial X no destino
	// @param		srcY int			"0" Coordenada inicial Y no destino
	// @param		srcW int			"-1" Largura da área a ser copiada
	// @param		srcH int			"-1" Altura da área a ser copiada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function copy($SrcImg, $destX=0, $destY=0, $srcX=0, $srcY=0, $srcW=-1, $srcH=-1) {
		if (TypeUtils::isInstanceOf($SrcImg, 'Image')) {
			return imagecopy(
				$this->handle, $SrcImg->getHandle(), 
				$destX, $destY, $srcX, $srcY,
				($srcW < 0 ? $SrcImg->getWidth() : $srcW),
				($srcH < 0 ? $SrcImg->getHeight() : $srcH)
			);
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Image'), E_USER_WARNING, __FILE__, __LINE__);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::copyMerge
	// @desc		Copia uma parte de outra imagem para esta imagem mesclando as cores
	//				dos pixels de acordo com uma porcentagem informada
	// @note		Se os parâmetros de largura e altura forem omitidos, a imagem fonte inteira será copiada	
	// @param		SrcImg Image object	Imagem fonte
	// @param		percent int			Percentual de mescla, de 0 a 100
	// @param		destX int			"0" Coordenada inicial X na fonte
	// @param		destY int			"0" Coordenada inicial Y na fonte
	// @param		srcX int			"0" Coordenada inicial X no destino
	// @param		srcY int			"0" Coordenada inicial Y no destino
	// @param		srcW int			"-1" Largura da área a ser copiada
	// @param		srcH int			"-1" Altura da área a ser copiada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function copyMerge($SrcImg, $percent, $destX=0, $destY=0, $srcX=0, $srcY=0, $srcW=-1, $srcH=-1) {
		if (TypeUtils::isInstanceOf($SrcImg, 'Image')) {
			return imagecopymerge(
				$this->handle, $SrcImg->getHandle(),
				$destX, $destY, $srcX, $srcY,
				($srcW < 0 ? $SrcImg->getWidth() : $srcW),
				($srcH < 0 ? $SrcImg->getHeight() : $srcH),
				($percent >=0 && $percent <= 100 ? $percent : 50)
			);
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Image'), E_USER_WARNING, __FILE__, __LINE__);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::copyResized
	// @desc		Copia e redimensiona uma área de uma imagem para dentro desta imagem
	// @note		Se os parâmetros de largura e altura forem omitidos, largura e altura da imagem
	//				fonte e desta imagem serão utilizadas
	// @param		SrcImg Image object	Imagem fonte
	// @param		percent int			Percentual de mescla, de 0 a 100
	// @param		destX int			"0" Coordenada inicial X na fonte
	// @param		destY int			"0" Coordenada inicial Y na fonte
	// @param		srcX int			"0" Coordenada inicial X no destino
	// @param		srcY int			"0" Coordenada inicial Y no destino
	// @param		destW int			"-1" Largura da área onde a região será inserida
	// @param		destH int			"-1" Altura da área onde a região será inserida
	// @param		srcW int			"-1" Largura da área a ser copiada
	// @param		srcH int			"-1" Altura da área a ser copiada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function copyResized($SrcImg, $destX=0, $destY=0, $srcX=0, $srcY=0, $destW=-1, $destH=-1, $srcW=-1, $srcH=-1) {
		if (TypeUtils::isInstanceOf($SrcImg, 'Image')) {
			return imagecopyresized(
				$this->handle, $SrcImg->getHandle(),
				$destX, $destY, $srcX, $srcY,
				($destW < 0 ? $this->width : $destW),
				($destH < 0 ? $this->height : $destH),
				($srcW < 0 ? $SrcImg->getWidth() : $srcW),
				($srcH < 0 ? $SrcImg->getHeight() : $srcH)
			);
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Image'), E_USER_WARNING, __FILE__, __LINE__);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::copyResampled
	// @desc		Copia e redimensiona uma área de uma imagem para dentro desta imagem, com interpolação
	// @note		Se os parâmetros de largura e altura forem omitidos, largura e altura da imagem
	//				fonte e desta imagem serão utilizadas
	// @param		SrcImg Image object	Imagem fonte
	// @param		percent int			Percentual de mescla, de 0 a 100
	// @param		destX int			"0" Coordenada inicial X na fonte
	// @param		destY int			"0" Coordenada inicial Y na fonte
	// @param		srcX int			"0" Coordenada inicial X no destino
	// @param		srcY int			"0" Coordenada inicial Y no destino
	// @param		destW int			"-1" Largura da área onde a região será inserida
	// @param		destH int			"-1" Altura da área onde a região será inserida
	// @param		srcW int			"-1" Largura da área a ser copiada
	// @param		srcH int			"-1" Altura da área a ser copiada
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function copyResampled($SrcImg, $destX=0, $destY=0, $srcX=0, $srcY=0, $destW=-1, $destH=-1, $srcW=-1, $srcH=-1) {
		if (ImageUtils::getGDVersion() < 2)
			return FALSE;
		if (TypeUtils::isInstanceOf($SrcImg, 'Image')) {
			return imagecopyresampled(
				$this->handle, $SrcImg->getHandle(),
				$destX, $destY, $srcX, $srcY,
				($destW < 0 ? $this->width : $destW),
				($destH < 0 ? $this->height : $destH),
				($srcW < 0 ? $SrcImg->getWidth() : $srcW),
				($srcH < 0 ? $SrcImg->getHeight() : $srcH)
			);
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Image'), E_USER_WARNING, __FILE__, __LINE__);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::flip
	// @desc		Inverte o conteúdo de imagem em uma determinada orientação
	// @param		type int	Orientação da inversão: IMAGE_FLIP_HORIZONTAL, IMAGE_FLIP_VERTICAL ou IMAGE_FLIP_BOTH
	// @access		public
	// @return		Image object
	//!-----------------------------------------------------------------
	function flip($type) {
		$Img = Image::create($this->width, $this->height);
		switch ($type) {
			case IMAGEFLIP_HORIZONTAL :				
				for ($x=0 ; $x<$this->width; $x++)
               		imagecopy($Img->handle, $this->handle, $this->width-$x-1, 0, $x, 0, 1, $this->height);
               	return $Img;
			case IMAGEFLIP_VERTICAL :
				for ($y=0; $y<$this->height; $y++)
               		imagecopy($Img->handle, $this->handle, 0, $this->height-$y-1, 0, $y, $this->width, 1);
               	return $Img;
			case IMAGEFLIP_BOTH :
				for ($x=0; $x<$this->width; $x++)
					imagecopy($Img->handle, $this->handle, $this->width-$x-1, 0, $x, 0, 1, $this->height);
     			$buffer = (ImageUtils::getGDVersion() > 2 ? imagecreatetruecolor($this->width, 1) : imagecreate($this->width, 1));
				for ($y=0; $y<($this->height/2); $y++) {
					imagecopy($buffer, $Img->handle, 0, 0, 0, $this->height-$y-1, $this->width, 1);
					imagecopy($Img->handle, $Img->handle, 0, $this->height-$y-1, 0, $y, $this->width, 1);
					imagecopy($Img->handle, $buffer, 0, $y, 0, 0, $this->width, 1);
				}
				imagedestroy($buffer);
				return $Img;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::resize
	// @desc		Cria uma nova imagem a partir desta, com novas dimensões
	// @access		public
	// @param		width int		Nova largura
	// @param		height int		Nova altura
	// @param		resample bool	"TRUE" Utilizar interpolação
	// @return		Image object Nova imagem
	//!-----------------------------------------------------------------
	function resize($width, $height, $resample=TRUE) {
		$Img = Image::create($width, $height);
		if ($resample && ImageUtils::getGDVersion() >= 2)
			$Img->copyResampled($this);
		else
			$Img->copyResized($this);
		return $Img;
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::rotate
	// @desc		Rotaciona a imagem
	// @param		angle float		Ângulo de rotação
	// @param		bgColor int		Cor de fundo para as áreas descobertas após a rotação
	// @return		Image object Nova imagem
	// @access		public
	//!-----------------------------------------------------------------	
	function rotate($angle, $bgColor) {
		$bc = $this->allocateColor($bgColor);
		return new Image(imagerotate($this->handle, (float)$angle, $bc));
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::display
	// @desc		Exibe o conteúdo da imagem, enviando o header apropriado
	//				de acordo com o tipo de imagem solicitado
	// @param		type int		Tipo de imagem
	// @param		filename string	"NULL" Nome de arquivo opcional para ser enviado no header "Content-disposition"
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display($type, $filename=NULL) {
		switch ($type) {
			case IMAGETYPE_GD :
				$mime = 'image/gd';
				$func = 'imagegd';
				break;
			case IMAGETYPE_GD2 :
				$mime = 'image/gd2';
				$func = 'imagegd2';
				break;
			case IMAGETYPE_GIF :
				$mime = 'image/gif';
				$func = 'imagegif';
				break;
			case IMAGETYPE_JPEG :
				$mime = 'image/jpeg';
				$func = 'imagejpeg';
				break;
			case IMAGETYPE_PNG :
				$mime = 'image/png';
				$func = 'imagepng';
				break;
			case IMAGETYPE_WBMP :
				$mime = 'image/vnd.wap.wbmp';
				$func = 'imagewbmp';
				break;
			case IMAGETYPE_XBM :
				$mime = 'image/xbm';
				$func = 'imagexbm';
				break;
		}
		header("Content-type: {$mime}");
		if ($filename)
			header("Content-disposition: inline; filename={$filename}");
		$func($this->handle);
	}
	
	//!-----------------------------------------------------------------
	// @function	Image::toFile
	// @desc		Salva a imagem em um arquivo
	// @param		type int		Tipo de imagem
	// @param		filename string	Caminho do arquivo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function toFile($type, $filename) {		
		$res = FALSE;
		switch ($type) {
			case IMAGETYPE_GD :
				$res = @imagegd($this->handle, $filename);
				break;
			case IMAGETYPE_GD2 :
				$res = @imagegd2($this->handle, $filename);
				break;
			case IMAGETYPE_GIF :
				$res = @imagegif($this->handle, $filename);
				break;
			case IMAGETYPE_JPEG :
				$res = @imagejpeg($this->handle, $filename);
				break;
			case IMAGETYPE_PNG :
				$res = @imagepng($this->handle, $filename);
				break;
			case IMAGETYPE_WBMP :
				$res = @imagewbmp($this->handle, $filename);
				break;
			case IMAGETYPE_XBM :
				$res = @imagexbm($this->handle, $filename);
				break;
		}
		if (!$res)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $filename), E_USER_ERROR, __FILE__, __LINE__);
	}
}
?>