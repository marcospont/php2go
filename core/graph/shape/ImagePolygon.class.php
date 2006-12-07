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
 * Draws a polygon
 *
 * A polygon is represented by a set of N points.
 *
 * @package graph
 * @subpackage shape
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ImagePolygon extends Drawable
{
	/**
	 * Set of polygon's points
	 *
	 * @var array
	 * @access private
	 */
	var $points;

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
	 * The set of points must be defined in the
	 * following format: x1,y1,x2,y2,...,xN,yN.
	 *
	 * The GD library requires at least 3 points in
	 * order to draw the polygon in the target image.
	 *
	 * The $fill argument accepts the following
	 * values: IMG_ARC_PIE, IMG_ARC_CHORD,
	 * IMG_ARC_NOFILL and IMG_ARC_EDGED.
	 *
	 * @param array $points Polygon's points
	 * @param mixed $fill Fill style
	 * @return ImagePolygon
	 */
	function ImagePolygon($points, $fill=FALSE) {
		parent::Drawable();
		$this->points = (array)$points;
		$this->fill = (bool)$fill;
	}

	/**
	 * Draws the polygon in a given image
	 *
	 * @param Image &$Img Target image
	 */
	function draw(&$Img) {
		$numPoints = floor(sizeof($this->points)/2);
		if ($this->fill)
			imagefilledpolygon($Img->handle, $this->points, $numPoints, $Img->currentColor);
		else
			imagepolygon($Img->handle, $this->points, $numPoints, $Img->currentColor);
	}
}
?>