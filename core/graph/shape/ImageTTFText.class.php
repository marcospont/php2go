<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.graph.shape.ImageText');

/**
 * Draws text using a TrueType font
 *
 * A TTF text is represented by a point (left lower corner of
 * the text), the path to the font file, font size and font angle.
 *
 * @package graph
 * @subpackage shape
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ImageTTFText extends ImageText
{
	/**
	 * Font file
	 *
	 * @var string
	 * @access private
	 */
	var $fontFile;

	/**
	 * Font size
	 *
	 * @var int
	 * @access private
	 */
	var $size;

	/**
	 * Font angle
	 *
	 * @var float
	 * @access private
	 */
	var $angle;

	/**
	 * Shadow size
	 *
	 * @var int
	 * @access private
	 */
	var $shadow = 0;

	/**
	 * Shadow color
	 *
	 * @var string|array
	 * @access private
	 */
	var $shadowColor = NULL;

	/**
	 * Class constructor
	 *
	 * Depending on the GD version, if $fontFile doesn't contain a
	 * leading "/", then ".ttf" will be appended to the filename and
	 * GD will attempt to load the font file from the default font
	 * path provided by the operating system.
	 *
	 * @param string $text Text value
	 * @param int $x Left X
	 * @param int $y Lower Y
	 * @param string $fontFile Font file
	 * @param int $size Font size
	 * @param float $angle Font angle
	 * @param int $shadow Shadow size
	 * @param string|array $shadowColor Shadow color
	 * @return ImageTTFText
	 */
	function ImageTTFText($text, $x, $y, $fontFile, $size, $angle=0, $shadow=0, $shadowColor=NULL) {
		parent::ImageText($text, $x, $y, NULL);
		$this->fontFile = $fontFile;
		$this->size = $size;
		$this->angle = $angle;
		$this->shadow = $shadow;
		$this->shadowColor = $shadowColor;
	}

	/**
	 * Draws the text in a given image
	 *
	 * @param Image &$Img Target image
	 */
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