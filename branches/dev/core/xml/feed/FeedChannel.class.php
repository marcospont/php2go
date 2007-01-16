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

import('php2go.xml.feed.FeedNode');
import('php2go.xml.feed.FeedItem');
import('php2go.util.AbstractList');

/**
 * Represents a feed channel
 *
 * A channel is a collection of items. Its set of elements and
 * child nodes depend on the feed type and version.
 *
 * @package xml
 * @subpackage feed
 * @uses AbstractList
 * @uses FeedItem
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FeedChannel extends FeedNode
{
	/**
	 * Defines the value of a channel's element
	 *
	 * @param string $name Element name
	 * @param mixed $value Element value
	 */
	function setElement($name, $value) {
		$upper = strtoupper($name);
		if ($upper == 'LASTBUILDDATE' || $upper == 'PUBDATE' ||
			$upper == 'MODIFIED' || $upper == 'UPDATED')
			$value = parent::parseDate($value);
		parent::setElement($name, $value);
	}

	/**
	 * Adds an item in the channel
	 *
	 * @param FeedItem $Item New item
	 * @return bool
	 */
	function addItem($Item) {
		if (TypeUtils::isInstanceOf($Item, 'FeedItem')) {
			parent::addChild($Item);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get items
	 *
	 * @return array
	 */
	function getItems() {
		return $this->children;
	}

	/**
	 * Returns an iterator of the channel's items
	 *
	 * @uses AbstractList::iterator()
	 * @return ListIterator
	 */
	function itemIterator() {
		$List = new AbstractList($this->getChildren());
		return $List->iterator();
	}
}
?>