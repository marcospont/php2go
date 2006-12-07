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

/**
 * Draws an arc
 *
 * An arc is represented by a center coordinate, width,
 * height, start and end angle and fill style.
 *
 * @package graph
 * @subpackage shape
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ImageArc extends Drawable
{
	/**
	 * Center's X coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $cx;

	/**
	 * Center's Y coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $cy;

	/**
	 * Width
	 *
	 * @var int
	 * @access private
	 */
	var $width;

	/**
	 * Height
	 *
	 * @var int
	 * @access private
	 */
	var $height;

	/**
	 * Start angle
	 *
	 * @var float
	 * @access private
	 */
	var $startAngle;

	/**
	 * End angle
	 *
	 * @var float
	 * @access private
	 */
	var $endAngle;

	/**
	 * Fill style
	 *
	 * @var mixed
	 * @access private
	 */
	var $fill = FALSE;

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
	 * The $fill argument accepts the following
	 * values: IMG_ARC_PIE, IMG_ARC_CHORD,
	 * IMG_ARC_NOFILL and IMG_ARC_EDGED.
	 *
	 * @param int $cx Center X
	 * @param int $cy Center Y
	 * @param int $width Width
	 * @param int $height Height
	 * @param float $startAngle Start angle
	 * @param float $endAngle End angle
	 * @param mixed $fill Fill style
	 * @param int $shadow Shadow size
	 * @param string|array $shadowColor Shadow color
	 * @return ImageArc
	 */
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

	/**
	 * Draws the arc in a given image
	 *
	 * @param Image &$Img Target image
	 */
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