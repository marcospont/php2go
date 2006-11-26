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
// $Header: /www/cvsroot/php2go/core/graph/ImageUtils.class.php,v 1.5 2006/10/26 04:30:57 mpont Exp $
// $Date: 2006/10/26 04:30:57 $

//-----------------------------------------
import('php2go.graph.Image');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		ImageUtils
// @desc		Esta classe contщm mщtodos utilitсrios relacionados р manipulaчуo
//				ou captura de informaчѕes de imagens, usando a biblioteca GD
// @package		php2go.graph
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.5 $
// @static
//!-----------------------------------------------------------------
class ImageUtils extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	ImageUtils::gdVersion
	// @desc		Busca a versуo da biblioteca GD contida na instalaчуo do PHP
	// @access		public
	// @return		int Versуo GD instalada ou NULL se nуo disponэvel
	// @static
	//!-----------------------------------------------------------------
	function getGDVersion() {
		static $version;
		if (!isset($version)) {
			if (!extension_loaded('gd')) {
				$version = NULL;
			} elseif (function_exists('gd_info')) {				
				$info = gd_info();
				preg_match("/\d/", $info['GD Version'], $match);
				$version = $match[0];
			} elseif (!preg_match("/phpinfo/", ini_get('disable_functions'))) {
				ob_start();
				phpinfo(8);
				$info = ob_get_clean();
				$info = stristr($info, 'gd version');
				preg_match("/\d/", $info, $match);
				$version = $match[0];
			} else {
				$version = NULL;
			}
		}
		return $version;
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageUtils::jpgSupported
	// @desc		Verifica se existe suporte р criaчуo e manipulaчуo de imagens JPG na instalaчуo do PHP
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function jpgSupported() {
		return function_exists('imagecreatefromjpeg');
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageUtils::ttfSupported
	// @desc		Verifica se existe suporte a fontes TrueType na instalaчуo do PHP
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function ttfSupported() {
		return function_exists('imagettftext');
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageUtils::getTempName
	// @desc		Gera um nome temporсrio de imagem, a partir do tipo
	// @access		public
	// @param		imageType int	"IMAGETYPE_PNG" Tipo de imagem
	// @param		length int		"8" Comprimento do nome
	// @return		Nome de arquivo temporсrio
	// @static
	//!-----------------------------------------------------------------
	function getTempName($imageType=IMAGETYPE_PNG, $length=8) {
		switch ($imageType) {
			case IMAGETYPE_GD :
				$extension = '.gd';
				break;
			case IMAGETYPE_GD2 :
				$extension = '.gd2';
				break;
			case IMAGETYPE_GIF :
				$extension = '.gif';
				break;
			case IMAGETYPE_JPEG :
				$extension = '.jpg';
				break;
			case IMAGETYPE_PNG :
				$extension = '.png';
				break;
			case IMAGETYPE_WBMP :
				$extension = '.wbmp';
				break;
			case IMAGETYPE_XBM :
				$extension = '.xbm';
				break;
			default :
				$extension = '';
		}
		$filename = substr(md5(uniqid(rand(), TRUE)), 0, $length);
		return $filename . $extension;
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageUtils::fromRGBToHex
	// @desc		Converte 3 valores de componentes vermelho, verde e azul em uma
	//				representaчуo hexadecimal de cor
	// @access		public
	// @param		red int		Componente vermelho
	// @param		green int	Componente verde
	// @param		blue int	Componente azul
	// @return		string Representaчуo hexadecimal da cor
	// @static
	//!-----------------------------------------------------------------
	function fromRGBToHex($red, $green, $blue) {
		return '#' . dechex($red) . dechex($green) . dechex($blue);
	}
	
	//!-----------------------------------------------------------------
	// @function	ImageUtils::fromHexToRGB
	// @desc		Converte um identificador de cor no formato hexadecimal
	//				para um array com os componentes vermelho, verde e azul
	// @access		public
	// @param		hex string	Cor no formato hexadecimal
	// @return		array Vetor com os 3 componentes RGB da cor
	// @static		
	//!-----------------------------------------------------------------
	function fromHexToRGB($hex) {
		if (preg_match("/#[0-9A-Fa-f]{6}/", $hex)) {
			$tmp = substr($hex, 1);
			$red = hexdec(substr($tmp, 0, 2));
			$green = hexdec(substr($tmp, 2, 2));
			$blue = hexdec(substr($tmp, 4, 2));
			return array($red, $green, $blue);
		}
		return NULL;
	}
}
?>