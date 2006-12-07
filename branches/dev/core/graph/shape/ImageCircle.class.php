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

import('php2go.graph.shape.ImageArc');

/**
 * Draws a circle
 *
 * A circle is represented by a center point
 * and a radius.
 *
 * @package graph
 * @subpackage shape
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ImageCircle extends ImageArc
{
	/**
	 * Circle radius
	 *
	 * @var float
	 * @access private
	 */
	var $radius;

	/**
	 * Class constructor
	 *
	 * @param int $cx Center X
	 * @param int $cy Center Y
	 * @param float $radius Radius
	 * @param mixed $fill Fill style
	 * @param int $shadow Shadow size
	 * @param string|array $shadowColor Shadow color
	 * @return ImageCircle
	 */
	function ImageCircle($cx, $cy, $radius, $fill=FALSE, $shadow=0, $shadowColor=NULL) {
		parent::ImageArc($cx, $cy, $radius*2, $radius*2, 0, 360, $fill, $shadow, $shadowColor);
		$this->radius = $radius;
	}
}
?>