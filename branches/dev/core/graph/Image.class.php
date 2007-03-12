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

import('php2go.graph.Drawable');
import('php2go.graph.ImageUtils');
import('php2go.graph.shape.*');

/**
 * GD images
 */
define('IMAGETYPE_GD', 17);
/**
 * GD2 images
 */
define('IMAGETYPE_GD2', 18);
/**
 * Horizontal image flip
 */
define('IMAGEFLIP_HORIZONTAL', 1);
/**
 * Vertical image flip
 */
define('IMAGEFLIP_VERTICAL', 2);
/**
 * Horizontal and vertical image flip
 */
define('IMAGEFLIP_BOTH', 3);

/**
 * Builds and manipulates images using the GD library functions
 *
 * @package graph
 * @uses ImageUtils
 * @uses System
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Image extends PHP2Go
{
	/**
	 * Current image handle
	 *
	 * @var resource
	 * @access private
	 */
	var $handle;

	/**
	 * Current width
	 *
	 * @var int
	 * @access private
	 */
	var $width;

	/**
	 * Current height
	 *
	 * @var int
	 * @access private
	 */
	var $height;

	/**
	 * Index of the last color used
	 *
	 * @var int
	 * @access private
	 */
	var $currentColor;

	/**
	 * Class constructor
	 *
	 * @param resource $handle Image handle
	 * @return Image
	 */
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

	/**
	 * Class destructor
	 */
	function __destruct() {
		if (is_resource($this->handle))
			imagedestroy($this->handle);
	}

	/**
	 * Create a new image
	 *
	 * The $trueColor argument is ignored when
	 * GD version is lower than 2.
	 *
	 * @param int $width Image width
	 * @param int $height Image height
	 * @param bool $trueColor Use true color or not
	 * @return Image
	 * @static
	 */
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

	/**
	 * Loads an image from a file
	 *
	 * @param string $path File path
	 * @param int $type Image type
	 * @return Image
	 * @static
	 */
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
				$handle = imagecreatefromstring(file_get_contents($path));
		}
		$Image = new Image($handle);
		return $Image;
	}

	/**
	 * Get image handle
	 *
	 * @return resource
	 */
	function &getHandle() {
		return $this->handle;
	}

	/**
	 * Get image width
	 *
	 * @return int Width, in pixels
	 */
	function getWidth() {
		return $this->width;
	}

	/**
	 * Get image height
	 *
	 * @return int Height, in pixels
	 */
	function getHeight() {
		return $this->height;
	}

	/**
	 * Get color information given a pair of coordinates
	 *
	 * Returns an array containing values of the RGB channels.
	 *
	 * @param int $x X coordinate
	 * @param int $y Y coordinate
	 * @return array
	 */
	function getColorAt($x, $y) {
		if ($x >= 0 && $x <= $this->width && $y >= 0 && $y < $this->height) {
			$color = imagecolorsforindex($this->handle, imagecolorat($this->handle, $x, $y));
			return array($color['red'], $color['green'], $color['blue']);
		}
		return NULL;
	}

	/**
	 * Get total colors of the image's color palette
	 *
	 * If the image uses true color, this method will return 0.
	 *
	 * @return int Total colors
	 */
	function getTotalColors() {
		return imagecolorstotal($this->handle);
	}

	/**
	 * Change current color
	 *
	 * A color identifier can be an array containing values for
	 * the RGB channels or a string in the format '#NNNNNN'.
	 *
	 * @param string|array $color Color identifier
	 * @param float $alpha Transparency level (between 0 and 1)
	 */
	function setColor($color, $alpha=FALSE) {
		$this->currentColor = $this->allocateColor($color, $alpha);
	}

	/**
	 * Set current color using random values
	 *
	 * Allocates a new color using random values for the
	 * RGB channels. $min and $max arguments determine
	 * the bounds used by the {@link rand()} function.
	 *
	 * @param int $min Lower bound
	 * @param int $max Upper bound
	 */
	function randomColor($min=0, $max=255) {
		$color = array(rand($min, $max), rand($min, $max), rand($min, $max));
		$this->currentColor = $this->allocateColor($color);
	}

	/**
	 * Allocate a new color, and return its index
	 *
	 * A color identifier can be an array containing values for
	 * the RGB channels or a string in the format '#NNNNNN'.
	 *
	 * @param string|array $color Color identifier
	 * @param float $alpha Transparency level (between 0 and 1)
	 * @return int Color index
	 */
	function allocateColor($color, $alpha=FALSE) {
		// hex representation
		if (TypeUtils::isString($color) && $color[0] == '#') {
			$color = ImageUtils::fromHexToRGB($color);
		}
		// array representation
		elseif (is_array($color) && count($color) == 3) {
			$red = max(min((int)$color[0], 255), 0);
			$green = max(min((int)$color[1], 255), 0);
			$blue = max(min((int)$color[2], 255), 0);
			$color = array($red, $green, $blue);
		}
		// invalid color
		else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_COLOR_SPEC', exportVariable($color)), E_USER_ERROR, __FILE__, __LINE__);
		}
		// alpha channel
		$alpha = (float)$alpha;
		if ($alpha > 0 && $alpha < 1 && ImageUtils::getGDVersion() >= 2) {
			return imagecolorresolvealpha($this->handle, $color[0], $color[1], $color[2], round($alpha * 127));
		} else {
			// check if the color was already used
			$index = imagecolorexact($this->handle, $color[0], $color[1], $color[2]);
			if ($index == -1) {
				// allocate a new color
				$index = imagecolorallocate($this->handle, $color[0], $color[1], $color[2]);
				if ($index == -1)
					// use a similar color if max colors limit was reached
					$index = imagecolorresolve($this->handle, $color[0], $color[1], $color[2]);
			}
			if ($index == -1)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ALLOCATE_COLOR'), E_USER_ERROR, __FILE__, __LINE__);
			return $index;
		}
	}

	/**
	 * Get image's transparent color
	 *
	 * Returns an array containing the 3 RGB channels.
	 *
	 * @return array
	 */
	function getTransparency() {
		$color = imagecolorsforindex($this->handle, imagecolortransparent($this->handle));
		return array($color['red'], $color['green'], $color['blue']);
	}

	/**
	 * Set image's transparent color
	 *
	 * @param string|array $color Color identifier
	 */
	function setTransparency($color) {
		$color = $this->allocateColor($color);
		imagecolortransparent($this->handle, $color);
	}

	/**
	 * Set the blending mode for the image
	 *
	 * @param bool $setting Enable/disable
	 * @return bool
	 */
	function setAlphaBlending($setting=TRUE) {
		if (function_exists('imagealphablending'))
			return imagealphablending($this->handle, (bool)$setting);
		return FALSE;
	}

	/**
	 * Enable/disable antialias on the image
	 *
	 * @param bool $setting Enable/disable
	 * @return bool
	 */
	function setAntiAlias($setting=TRUE) {
		$setting = (bool)$setting;
		if (function_exists('imageantialias'))
			return (bool)imageantialias($this->handle, $setting);
		return FALSE;
	}

	/**
	 * Enable/disable interlace on the image
	 *
	 * @param bool $setting Enable/disable
	 * @return bool
	 */
	function setInterlace($setting=TRUE) {
		$setting = ($setting ? 1 : 0);
		return (bool)imageinterlace($this->handle, $setting);
	}

	/**
	 * Apply a gamma correction on the image
	 *
	 * @param float $in Input gamma
	 * @param float $out Output gamma
	 * @return bool
	 */
	function correctGamma($in, $out) {
		if (TypeUtils::isFloat($in) && TypeUtils::isFloat($out))
			return imagegammacorrect($this->handle, $in, $out);
		return FALSE;
	}

	/**
	 * Set the brush image to be used to draw lines and polygons
	 *
	 * The $style argument should be an array containing color identifiers.
	 *
	 * @param Image $Img Brush image
	 * @param array $style Brush style
	 * @return bool
	 */
	function setBrush($Img, $style=array()) {
		if (TypeUtils::isInstanceOf($Img, 'Image')) {
			$useStyle = $this->setStyle($style);
			$this->currentColor = ($useStyle ? IMG_COLOR_STYLEDBRUSHED : IMG_COLOR_BRUSHED);
			return imagesetbrush($this->handle, $Img->handle);
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Image'), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}

	/**
	 * Set the style to be used when drawing lines and polygons
	 *
	 * Styles are color sequences. Styles can be used, for instance,
	 * to draw dotted or dashed lines and polygons.
	 *
	 * @param array $colors Set of color identifiers
	 * @return bool
	 */
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

	/**
	 * Set the tile image for filling methods
	 *
	 * @param Image $Img Tile image
	 * @return bool
	 */
	function setFillingTile($Img) {
		if (TypeUtils::isInstanceOf($Img, 'Image')) {
			$this->currentColor = IMG_COLOR_TILED;
			return imagesettile($this->handle, $Img->handle);
		}
		PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Image'), E_USER_ERROR, __FILE__, __LINE__);
		return FALSE;
	}

	/**
	 * Set a color of a pixel given its coordinates
	 *
	 * @param int $x X coordinate
	 * @param int $y Y coordinate
	 * @param string|array $color Color identifier
	 * @return bool
	 */
	function setPixel($x, $y, $color) {
		$c = $this->allocateColor($color);
		return imagesetpixel($this->handle, $x, $y, $c);
	}

	/**
	 * Fill the image using the current color, starting at $x,$y
	 *
	 * @param int $x X start coordinate
	 * @param int $y Y start coordinate
	 * @return bool
	 */
	function fill($x=0, $y=0) {
		if ($x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height)
			return imagefill($this->handle, $x, $y, $this->currentColor);
		return FALSE;
	}

	/**
	 * Performs a flood fill on the image, using $bColor as border color
	 *
	 * @param int $x X start coordinate
	 * @param int $y Y start coordinate
	 * @param string|array $bColor Border color identifier
	 * @return bool
	 */
	function fillToBorder($x, $y, $bColor) {
		$bc = $this->allocateColor($bColor);
		if ($x >= 0 && $x <= $this->width && $y >= 0 && $y <= $this->height)
			return imagefilltoborder($this->handle, $x, $y, $bc, $this->currentColor);
		return FALSE;
	}

	/**
	 * Draw an object
	 *
	 * @param Drawable $Obj Drawable object
	 */
	function draw($Obj) {
		if (!TypeUtils::isInstanceOf($Obj, 'Drawable'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Drawable'), E_USER_ERROR, __FILE__, __LINE__);
		$Obj->draw($this);
	}

	/**
	 * Copy part of an image to the current image
	 *
	 * @param Image $SrcImg Source image
	 * @param int $destX Target X point
	 * @param int $destY Target Y point
	 * @param int $srcX Source X point
	 * @param int $srcY Source Y point
	 * @param int $srcW Width of the part to be copied
	 * @param int $srcH Height of the part to be copied
	 * @return bool
	 */
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

	/**
	 * Copy and merge part of an image on the current image
	 *
	 * The merge level is determined by the $percent argument.
	 *
	 * @param Image $SrcImg Source image
	 * @param int $percent Merge level
	 * @param int $destX Target X point
	 * @param int $destY Target Y point
	 * @param int $srcX Source X point
	 * @param int $srcY Source Y point
	 * @param int $srcW Width of the area to be copied
	 * @param int $srcH Height of the area to be copied
	 * @return bool
	 */
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

	/**
	 * Copy and resize part of an image to the current image
	 *
	 * @param Image $SrcImg Source image
	 * @param int $destX Target X point
	 * @param int $destY Target Y point
	 * @param int $srcX Source X point
	 * @param int $srcY Source Y point
	 * @param int $destW Target width
	 * @param int $destH Target height
	 * @param int $srcW Width of the source area
	 * @param int $srcH Height of the source area
	 * @return bool
	 */
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

	/**
	 * Copy and resize part of an image to the current image with resampling
	 *
	 * Pixel values are smoothly interpolated so that reducing the size of
	 * an image still retains clarity and quality. Not supported by GD
	 * versions lower than 2.
	 *
	 * @param Image $SrcImg Source image
	 * @param int $destX Target X point
	 * @param int $destY Target Y point
	 * @param int $srcX Source X point
	 * @param int $srcY Source Y point
	 * @param int $destW Target width
	 * @param int $destH Target height
	 * @param int $srcW Width of the source area
	 * @param int $srcH Height of the source area
	 * @return bool
	 */
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

	/**
	 * Flips the current image
	 *
	 * The new image is returned.
	 *
	 * @param int $type Flip orientation: {@link IMAGE_FLIP_HORIZONTAL}, {@link IMAGE_FLIP_VERTICAL} or {@link IMAGE_FLIP_BOTH}
	 * @return Image New image
	 */
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

	/**
	 * Resizes the current image
	 *
	 * The resized image is returned.
	 *
	 * @param int $width New width
	 * @param int $height New height
	 * @param bool $resample Whether to use resampling
	 * @return Image New image
	 */
	function resize($width, $height, $resample=TRUE) {
		$Img = Image::create($width, $height);
		if ($resample && ImageUtils::getGDVersion() >= 2)
			$Img->copyResampled($this);
		else
			$Img->copyResized($this);
		return $Img;
	}

	/**
	 * Rotates the image by a given $angle
	 *
	 * Returns the new rotated image.
	 *
	 * @param float $angle Angle
	 * @param string|array $bgColor Color to the uncovered zones after rotation
	 * @return Image New image
	 */
	function rotate($angle, $bgColor) {
		$bc = $this->allocateColor($bgColor);
		return new Image(imagerotate($this->handle, (float)$angle, $bc));
	}

	/**
	 * Displays the current image
	 *
	 * The $filename argument is used to send a Content-disposition header.
	 *
	 * @param int $type Image type
	 * @param string $filename File name
	 */
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

	/**
	 * Saves the current image to a file
	 *
	 * @param int $type Image type
	 * @param string $filename File path
	 */
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