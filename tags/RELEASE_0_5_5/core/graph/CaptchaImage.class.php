<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.graph.Image');
import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');

/**
 * Builds CAPTCHA security images
 *
 * CAPTCHA means "Completely Automated Public Test to tell Computer
 * from Humans apart", and is used to identify that the user of an
 * application is human and not a computer. It's a very common security
 * pattern for web applications.
 *
 * @package graph
 * @uses HtmlUtils
 * @uses Image
 * @uses StringUtils
 * @uses TypeUtils
 * @link http://en.wikipedia.org/wiki/CAPTCHA
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CaptchaImage extends PHP2Go
{
	/**
	 * CAPTCHA string
	 *
	 * @var string
	 */
	var $text;

	/**
	 * CAPTCHA string length
	 *
	 * @var int
	 */
	var $textLength = 6;

	/**
	 * Image width
	 *
	 * @var int
	 */
	var $width = 130;

	/**
	 * Image height
	 *
	 * @var int
	 */
	var $height = 40;

	/**
	 * Noise level
	 *
	 * @var int
	 */
	var $noiseLevel = 15;

	/**
	 * Average font size (vary +/- 3 pixels)
	 *
	 * @var int
	 */
	var $fontSize = 15;

	/**
	 * Shadow size
	 *
	 * @var int
	 */
	var $fontShadow = 3;

	/**
	 * Minimum and maximum font angle
	 *
	 * @var float
	 */
	var $fontAngle = 30;

	/**
	 * Session variable to be used to hold the original CAPTCHA string
	 *
	 * @var string
	 */
	var $sessionVarName;

	/**
	 * Image save path
	 *
	 * @var string
	 */
	var $imagePath;

	/**
	 * Class constructor
	 *
	 * @param string $varName Session variable name
	 * @return CaptchaImage
	 */
	function CaptchaImage($varName='CAPTCHA') {
		parent::PHP2Go();
		$this->sessionVarName = $varName;
	}

	/**
	 * Set CAPTCHA string length
	 *
	 * @param int $length New length
	 */
	function setTextLength($length) {
		$length = (int)$length;
		if ($length > 0)
			$this->textLength = $length;
	}

	/**
	 * Set image width
	 *
	 * @param int $width Width, in pixels
	 */
	function setWidth($width) {
		$width = TypeUtils::parseInteger($width);
		if ($width > 0)
			$this->width = $width;
	}

	/**
	 * Set image height
	 *
	 * @param int $height Height, in pixels
	 */
	function setHeight($height) {
		$height = TypeUtils::parseInteger($height);
		if ($height > 0)
			$this->height = $height;
	}

	/**
	 * Set noise level
	 *
	 * For each noise level, a char is inserted in the image, using
	 * random color, size, position and angle, plus 10 pixels placed
	 * in random positions of the image and using random colors.
	 *
	 * @param int $level Noise level
	 */
	function setNoiseLevel($level) {
		$level = TypeUtils::parseInteger($level);
		if ($level > 0)
			$this->noiseLevel = $level;
	}

	/**
	 * Set average font size
	 *
	 * Font sizes will vary from $size-3 to $size+3.
	 *
	 * @param int $size Average font size
	 */
	function setFontSize($size) {
		$size = TypeUtils::parseInteger($size);
		if ($size > 0)
			$this->fontSize = $size;
	}

	/**
	 * Set the shadow size of the CAPTCHA chars
	 *
	 * @param int $shadow Shadow size, in pixels
	 */
	function setFontShadow($shadow) {
		$this->fontShadow = TypeUtils::parseIntegerPositive($shadow);
	}

	/**
	 * Set minimum and maximum font angle
	 *
	 * Font angles will vary from -$angle to +$angle.
	 *
	 * @param float $angle Font angle
	 */
	function setFontAngle($angle) {
		$angle = (int)$angle;
		while ($angle < -360)
			$angle += 360;
		while ($angle > 360)
			$angle -= 360;
		$this->fontAngle = $angle;
	}

	/**
	 * Builds the CAPTCHA image using $imageType and
	 * save it at $filePath
	 *
	 * @param string $savePath Save path
	 * @param int $imageType Image type
	 */
	function build($savePath=NULL, $imageType=IMAGETYPE_PNG) {
		$basePath = (!empty($savePath) ? rtrim($savePath, "\\/") . PHP2GO_DIRECTORY_SEPARATOR : '');
		$this->imagePath = $basePath . ImageUtils::getTempName($imageType);
		$Img =& $this->_createImage();
		$Img->toFile($imageType, $this->imagePath);
	}

	/**
	 * Builds and saves the CAPTCHA image in the file system and
	 * returns an IMG HTML tag pointing to the generated file
	 *
	 * @param string $savePath Save path
	 * @param int $imageType Image type
	 * @return string
	 */
	function buildHTML($savePath=NULL, $imageType=IMAGETYPE_PNG) {
		$this->build($savePath, $imageType);
		return HtmlUtils::image($this->imagePath, '', $this->width, $this->height, 0, 0, 'middle');
	}

	/**
	 * Verify if $text matches the CAPTCHA string
	 * stored in the session scope
	 *
	 * @param string $text Input text
	 * @return bool
	 */
	function verify($text) {
		if (isset($_SESSION[$this->sessionVarName])) {
			if ($_SESSION[$this->sessionVarName] == $text) {
				unset($_SESSION[$this->sessionVarName]);
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Creates and configures the CAPTCHA image
	 *
	 * @uses StringUtils::randomString()
	 * @return Image
	 * @access private
	 */
	function &_createImage() {
		$ttfTable = array('cour', 'georgia', 'trebuc', 'verdana', 'times', 'comic', 'arial', 'tahoma');
		$ttfCount = sizeof($ttfTable);
		// define the CAPTCHA string
		$this->text = StringUtils::randomString($this->textLength, FALSE);
		// save it in the session scope
		$_SESSION[$this->sessionVarName] = $this->text;
		// create the image
		$Img =& Image::create($this->width, $this->height);
		// background color
		$Img->randomColor(224, 255);
		$Img->draw(new ImageRectangle(0, 0, $this->width, $this->height));
		// horizontal lines on random colors
		for ($i=0; $i<$this->height; $i++) {
			$Img->randomColor(224, 255);
			$Img->draw(new ImageLine(0, $i, $this->width, $i));
		}
		/**
		 * noise:
		 * chars with light colors, random position, size and angle
		 * pixels with random color and position
		 * for each 3 chars, a line with random color and position
		 */
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
		// CAPTCHA characters (dark colors)
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
		// image border
		$Img->setColor('#000000');
		$Img->draw(new ImageRectangle(0, 0, $this->width-1, $this->height-1));
		return $Img;
	}
}
?>