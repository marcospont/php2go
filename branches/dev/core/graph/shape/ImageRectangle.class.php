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
 * Draws a rectangle
 *
 * A rectangle is represented by a pair of points: left upper
 * and bottom right.
 *
 * @package graph
 * @subpackage shape
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ImageRectangle extends Drawable
{
	/**
	 * Left X coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $x1;

	/**
	 * Upper Y coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $y1;

	/**
	 * Right X coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $x2;

	/**
	 * Bottom Y coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $y2;

	/**
	 * Fill style
	 *
	 * @var mixed
	 * @access private
	 */
	var $fill = FALSE;

	/**
	 * Class constructor
	 *
	 * @param int $x1 Left X
	 * @param int $y1 Upper Y
	 * @param int $x2 Right X
	 * @param int $y2 Bottom Y
	 * @param mixed $fill Fill style
	 * @return ImageRectangle
	 */
	function ImageRectangle($x1, $y1, $x2, $y2, $fill=FALSE) {
		parent::Drawable();
		$this->x1 = $x1;
		$this->y1 = $y1;
		$this->x2 = $x2;
		$this->y2 = $y2;
		$this->fill = (bool)$fill;
	}

	/**
	 * Draws the rectangle in a given image
	 *
	 * @param Image &$Img Target image
	 */
	function draw(&$Img) {
		if ($this->fill)
			imagefilledrectangle($Img->handle, $this->x1, $this->y1, $this->x2, $this->y2, $Img->currentColor);
		else
			imagerectangle($Img->handle, $this->x1, $this->y1, $this->x2, $this->y2, $Img->currentColor);
	}
}
?>