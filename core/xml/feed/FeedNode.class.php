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

import('php2go.datetime.Date');

/**
 * Base class of FeedChannel and FeedItem classes
 *
 * Contains utility methos to handle attributes and child nodes present
 * in the data structure of a feed. Also used as a helper of a feed
 * rendering process.
 *
 * @package xml
 * @subpackage feed
 * @uses Date
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FeedNode extends PHP2Go
{
	/**
	 * Node children
	 *
	 * @var array
	 */
	var $children = array();

	/**
	 * Class constructor
	 *
	 * @return FeedNode
	 */
	function FeedNode() {
		parent::PHP2Go();
	}

	/**
	 * Reads the value of a given element
	 *
	 * @param string $name Element name
	 * @param mixed $fallback Fallback value (used when element is not found)
	 * @return mixed
	 */
	function getElement($name, $fallback=NULL) {
		if (array_key_exists($name, get_object_vars($this)))
			return $this->{$name};
		return $fallback;
	}

	/**
	 * Defines the value of a node's element
	 *
	 * @param string $name Element name
	 * @param mixed $value Element value
	 */
	function setElement($name, $value) {
		if (!empty($name))
			$this->{$name} = $value;
	}

	/**
	 * Adds a new entry on a composite element
	 *
	 * Composite elements are represented as arrays inside
	 * feed channels and feed items.
	 *
	 * @param string $name Element name
	 * @param mixed $value New element entry
	 */
	function addElement($name, $value) {
		$this->{$name}[] = $value;
	}

	/**
	 * Get child nodes
	 *
	 * @return array
	 */
	function getChildren() {
		return $this->children;
	}

	/**
	 * Adds a child node
	 *
	 * @param FeedNode $Child New child
	 */
	function addChild($Child) {
		$this->children[] = $Child;
	}

	/**
	 * Tries to convert a datetime value to UNIX timestamp
	 *
	 * The method is able to convert date strings written in the
	 * RFC822 and ISO8601 formats into UNIX timestamp values.
	 *
	 * @uses Date::getTZDiff()
	 * @param string|int $date Date string or timestamp
	 * @return int Timestamp
	 * @access protected
	 */
	function parseDate($date) {
		$matches = array();
		// timestamp
		if (TypeUtils::isInteger($date) && $date >= 0 && $date <= LONG_MAX) {
			return $date;
		}
		// RFC822 date
		elseif (preg_match("~(?:(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s+)?(\\d{1,2})\\s+([a-zA-Z]{3})\\s+(\\d{4})\\s+(\\d{2}):(\\d{2}):(\\d{2})\\s+(.*)~", $date, $matches)) {
			$months = Array("Jan"=>1, "Feb"=>2, "Mar"=>3, "Apr"=>4, "May"=>5, "Jun"=>6, "Jul"=>7, "Aug"=>8, "Sep"=>9, "Oct"=>10, "Nov"=>11, "Dec"=>12);
			$ts = mktime($matches[4], $matches[5], $matches[6], $months[$matches[2]], $matches[1], $matches[3]);
			if ($matches[7])
				$ts += Date::getTZDiff($matches[7]);
			return $ts;
		}
		// ISO8601 date
		elseif (preg_match("~(\\d{4})-(\\d{2})-(\\d{2})T(\\d{2}):(\\d{2}):(\\d{2})(.*)~", $date, $matches)) {
			$ts = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
			if ($matches[7])
				$ts += Date::getTZDiff($matches[7]);
			return $ts;
		}
		// unrecognized format
		return $date;
	}

	/**
	 * Builds a date value according with a feed type and version
	 *
	 * @uses Date::formatTime()
	 * @param int $date Timestamp
	 * @param string $type Feed type
	 * @param string $version Format version
	 * @return string Date string
	 * @access protected
	 */
	function buildDate($date, $type, $version=NULL) {
		if (($type == FEED_RSS && $version == '1.0') || $type == FEED_ATOM)
			return Date::formatTime($date, DATE_FORMAT_ISO8601);
		return Date::formatTime($date, DATE_FORMAT_RFC822);
	}
}
?>