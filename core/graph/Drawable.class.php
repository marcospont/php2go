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
 * Base class for all drawable objects
 *
 * @package graph
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract
 */
class Drawable extends PHP2Go
{
	/**
	 * Class constructor
	 *
	 * @return Drawable
	 */
	function Drawable() {
		parent::PHP2Go();
		if ($this->isA('Drawable', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Drawable'), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Must be implemented by child classes
	 *
	 * @param Image &$Img Target image
	 * @abstract
	 */
	function draw(&$Img) {
	}
}
?>