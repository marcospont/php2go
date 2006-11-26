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
// $Header: /www/cvsroot/php2go/core/graph/CaptchaImage.class.php,v 1.2 2006/02/28 21:55:56 mpont Exp $
// $Date: 2006/02/28 21:55:56 $

//-----------------------------------------
import('php2go.graph.Image');
import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		CaptchaImage
// @desc		A classe CaptchaImage tem como propósito a construção de imagens
//				contendo um texto e elementos gráficos de ruído, a fim de identificar
//				que o usuário é humano e não uma máquina. CAPTCHA significa
//				"Completely Automated Public Test to tell Computer from Humans apart"
//				e é um mecanismo de segurança bastante utilizado em aplicações Web
// @package		php2go.graph
// @extends		PHP2Go
// @uses		HtmlUtils
// @uses		Image
// @uses		StringUtils
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.2 $
//!-----------------------------------------------------------------
class CaptchaImage extends PHP2Go
{
	var $text;				// @var text					String captcha
	var $textLength = 6;	// @var textLength int			"6" Comprimento da string captcha
	var $width = 130;		// @var width int				"130" Largura da imagem
	var $height = 40;		// @var height int				"40" Altura da imagem
	var $noiseLevel = 15;	// @var noiseLevel int			"15" Nível de ruído a ser inserido na imagem
	var $fontSize = 15;		// @var fontSize int			"15" Tamanho médio dos caracteres, com variação de +/- 3
	var $fontShadow = 3;	// @var fontShadow int			"3" Tamanho da sombra dos caracteres
	var $fontAngle = 30;	// @var fontAngle int			"30" Ângulo mínimo e máximo do texto
	var $sessionVarName;	// @var sessionVarName string	Nome da variável de sessão que deve armazenar a string captcha
	var $imagePath;			// @var imagePath string		Armazena o caminho completo para a imagem se ela for gerada no sistema de arquivos	
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::CaptchaImage
	// @desc		Construtor da classe
	// @param		varName string	"CAPTCHA" Nome da variável de sessão que deve armazenar a mensagem
	// @access		public
	//!-----------------------------------------------------------------
	function CaptchaImage($varName='CAPTCHA') {
		parent::PHP2Go();
		$this->sessionVarName = $varName;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::setTextLength
	// @desc		Seta o tamanho da string captcha, formada por caracteres e números aleatórios
	// @param		length int	Tamanho para a string captcha
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setTextLength($length) {
		$length = (int)$length;
		if ($length > 0)
			$this->textLength = $length;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::setWidth
	// @desc		Define a largura da imagem
	// @param		width int	Largura
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setWidth($width) {
		$width = TypeUtils::parseInteger($width);
		if ($width > 0)
			$this->width = $width;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::setHeight
	// @desc		Define a altura da imagem
	// @param		height int	Altura
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setHeight($height) {
		$height = TypeUtils::parseInteger($height);
		if ($height > 0)
			$this->height = $height;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::setNoiseLevel
	// @desc		Define o nível de ruído que deve ser inserido na imagem
	// @note		Para cada unidade do nível de ruído, é inserido um caractere
	//				de cor, tamanho, posição e ângulo aleatórios, mais 10 pixels
	//				de cor e posição aleatórias na imagem
	// @param		level int	Nível de ruído
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setNoiseLevel($level) {
		$level = TypeUtils::parseInteger($level);
		if ($level > 0)
			$this->noiseLevel = $level;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::setFontSize
	// @desc		Define o tamanho médio dos caracteres da string captcha
	// @note		Os caracteres da string terão um tamanho variável, entre
	//				$size-3 e $size+3
	// @param		size int	Tamanho
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setFontSize($size) {
		$size = TypeUtils::parseInteger($size);
		if ($size > 0)
			$this->fontSize = $size;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::setFontShadow
	// @desc		Seta o tamanho da sombra para os caracteres da string captcha
	// @access		public
	// @param		shadow int	Tamanho da sombra, em pixels
	// @return		void
	//!-----------------------------------------------------------------	
	function setFontShadow($shadow) {
		$this->fontShadow = TypeUtils::parseIntegerPositive($shadow);
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::setFontAngle
	// @desc		Define o ângulo máximo dos caracteres da string captcha
	// @note		Os caracteres da string terão um ângulo variável entre
	//				-$angle e $angle. Ex: para $angle=30, varia entre -30 e 30
	// @param		angle int	Ângulo máximo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFontAngle($angle) {
		$angle = (int)$angle;
		while ($angle < -360)
			$angle += 360;
		while ($angle > 360)
			$angle -= 360;
		$this->fontAngle = $angle;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::build
	// @desc		Constrói a imagem e salva seu conteúdo em um arquivo,
	//				utilizando $savePath como diretório base e $imageType
	//				como tipo de imagem a ser gerada
	// @access		public
	// @param		savePath string		"NULL" Caminho onde a imagem deve ser salva
	// @param		imageType int		"IMAGETYPE_PNG" Tipo de imagem
	// @note		Para permitir a exibição da imagem no código HTML,
	//				utilize um caminho que pode ser acessado pelo seu web server
	// @return		void
	//!-----------------------------------------------------------------
	function build($savePath=NULL, $imageType=IMAGETYPE_PNG) {
		$basePath = (!empty($savePath) ? rtrim($savePath, "\\/") . PHP2GO_DIRECTORY_SEPARATOR : '');
		$this->imagePath = $basePath . ImageUtils::getTempName($imageType);
		$Img =& $this->_createImage();
		$Img->toFile($imageType, $this->imagePath);		
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::buildHTML
	// @desc		Constrói a imagem, salva em arquivo e retorna o código HTML
	//				de uma tag IMG apontando para a imagem gerada
	// @access		public
	// @param		savePath string		"NULL" Caminho onde a imagem deve ser salva
	// @param		imageType int		"IMAGETYPE_PNG" Tipo de imagem
	// @note		Para permitir a exibição da imagem no código HTML,
	//				utilize um caminho que pode ser acessado pelo seu web server
	// @return		string Código HTML da tag IMG
	//!-----------------------------------------------------------------
	function buildHTML($savePath=NULL, $imageType=IMAGETYPE_PNG) {
		$this->build($savePath, $imageType);
		return HtmlUtils::image($this->imagePath, '', $this->width, $this->height, 0, 0, 'middle');
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::verify
	// @desc		Realiza comparação de um valor fornecido contra a string
	//				privada armazenada na sessão
	// @access		public
	// @param		text string		String de comparação
	// @return		bool
	//!-----------------------------------------------------------------
	function verify($text) {
		if (isset($_SESSION[$this->sessionVarName])) {
			if ($_SESSION[$this->sessionVarName] == $text) {
				unset($_SESSION[$this->sessionVarName]);
				return TRUE;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaImage::&_createImage
	// @desc		Cria e configura a imagem contendo a string captcha
	//				e os elementos de ruído gráfico para dificultar a interpretação
	//				por reconhecedores de caracteres
	// @access		private
	// @return		Image object
	//!-----------------------------------------------------------------
	function &_createImage() {
		$ttfTable = array('cour', 'georgia', 'trebuc', 'verdana', 'times', 'comic', 'arial', 'tahoma');
		$ttfCount = sizeof($ttfTable);
		// define a string captcha
		$this->text = StringUtils::randomString($this->textLength, FALSE);
		// armazena a string na sessão
		$_SESSION[$this->sessionVarName] = $this->text;
		// cria a imagem
		$Img =& Image::create($this->width, $this->height);
		// aplica cor de fundo
		$Img->randomColor(224, 255);
		$Img->draw(new ImageRectangle(0, 0, $this->width, $this->height));
		// linhas horizontais com cor clara aleatória
		for ($i=0; $i<$this->height; $i++) {
			$Img->randomColor(224, 255);
			$Img->draw(new ImageLine(0, $i, $this->width, $i));
		}
		// caracteres com cor (clara), posição, tamanho e ângulo aleatórios
		// pixels com cor e posição aleatórias
		// linhas em posição e cor aleatória
		for ($i=0; $i<$this->noiseLevel; $i++) {
			$Img->randomColor(160, 224);
			$Img->draw(new ImageTTFText(
				chr(rand(45, 250)),
				rand(0, $this->width-10),
				rand(10, $this->height),
				$ttfTable[rand(0, $ttfCount-1)],
				rand(6, 14), rand(0, 360)
			));
			for ($j=0; $j<10; $j++)
				$Img->setPixel(
					rand(0, $this->width), rand(0, $this->height), 
					array(rand(0, 255), rand(0, 255), rand(0, 255))
				);
			if (($i%3) == 0) {
				$Img->randomColor(64, 224);
				$Img->draw(new ImageLine(
					0, rand(0, $this->height),
					$this->width, rand(0, $this->height)
				));
			}
		}
		// insere os caracteres da string captcha (cores mais escuras)
		for ($i=0, $n=strlen($this->text), $x=10, $y=round(($this->height/2)+($this->fontSize/2)-5); $i<$n; $i++) {
			$red = rand(0, 128);
			$green = rand(0, 128);
			$blue = rand(0, 128);
			$color = array($red, $green, $blue);
			$size = rand($this->fontSize-3, $this->fontSize+3);
			$Img->setColor($color);
			$Img->draw(new ImageTTFText(
				$this->text{$i}, $x, $y,
				$ttfTable[rand(0, $ttfCount-1)],
				$size, rand(-$this->fontAngle, $this->fontAngle), 
				$this->fontShadow, array($red+128, $green+128, $blue+128)
			));
			$x += $size + 5;
		}
		// borda da imagem
		$Img->setColor('#000000');
		$Img->draw(new ImageRectangle(0, 0, $this->width-1, $this->height-1));
		return $Img;
	}
}
?>