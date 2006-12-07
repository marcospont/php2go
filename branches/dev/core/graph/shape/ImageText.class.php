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
 * GD font with size 1
 */
define('GDFONT_1', 1);
/**
 * GD font with size 2
 */
define('GDFONT_2', 2);
/**
 * GD font with size 3
 */
define('GDFONT_3', 3);
/**
 * GD font with size 4
 */
define('GDFONT_4', 4);
/**
 * GD font with size 5
 */
define('GDFONT_5', 5);

/**
 * Draws text using internal GD fonts
 *
 * A text is represented by a point (left lower corner
 * of the text) and a font size.
 *
 * @package graph
 * @subpackage shape
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ImageText extends Drawable
{
	/**
	 * Text
	 *
	 * @var string
	 * @access private
	 */
	var $text;

	/**
	 * Left X coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $x;

	/**
	 * Upper Y coordinate
	 *
	 * @var int
	 * @access private
	 */
	var $y;

	/**
	 * Font size
	 *
	 * @var int
	 * @access private
	 */
	var $font;

	/**
	 * Text orientation: vertical (TRUE) or horizontal (FALSE)
	 *
	 * @var bool
	 * @access private
	 */
	var $vertical = FALSE;

	/**
	 * Class constructor
	 *
	 * 5 different font sizes are accepted: {@link GDFONT_1}, {@link GDFONT_2},
	 * {@link GDFONT_3}, {@link GDFONT_4} and {@link GDFONT_5}.
	 *
	 * @param string $text Text value
	 * @param int $x Left X
	 * @param int $y Lower Y
	 * @param int $font Font size
	 * @param bool $vertical Vertical or horizontal orientation
	 * @return ImageText
	 */
	function ImageText($text, $x, $y, $font=GDFONT_1, $vertical=FALSE) {
		parent::Drawable();
		$this->text = $text;
		$this->x = (int)$x;
		$this->y = (int)$y;
		$this->font = $font;
		$this->vertical = (bool)$vertical;
	}

	/**
	 * Draws the text in a given image
	 *
	 * @param Image &$Img Target image
	 */
	function draw(&$Img) {
		$func = ($this->vertical ? 'imagestringup' : 'imagestring');
		$func($Img->handle, $this->font, $this->x, $this->y, $this->text, $Img->currentColor);
	}
}

?>