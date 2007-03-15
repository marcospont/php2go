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

/**
 * Draws a line
 *
 * A line is represented by a pair of points.
 *
 * @package graph
 * @subpackage shape
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ImageLine extends Drawable
{
	/**
	 * Start X coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $x1;

	/**
	 * Start Y coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $y1;

	/**
	 * End X coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $x2;

	/**
	 * End Y coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $y2;

	/**
	 * Class constructor
	 *
	 * @param int $x1 Start X
	 * @param int $y1 Start Y
	 * @param int $x2 End X
	 * @param int $y2 End Y
	 * @return ImageLine
	 */
	function ImageLine($x1, $y1, $x2, $y2) {
		parent::Drawable();
		$this->x1 = $x1;
		$this->y1 = $y1;
		$this->x2 = $x2;
		$this->y2 = $y2;
	}

	/**
	 * Draws the line in a given image
	 *
	 * @param Image &$Img Target image
	 */
	function draw(&$Img) {
		imageline($Img->handle, $this->x1, $this->y1, $this->x2, $this->y2, $Img->currentColor);
	}
}
?>