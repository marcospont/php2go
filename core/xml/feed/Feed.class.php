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

import('php2go.xml.feed.FeedChannel');

/**
 * RSS feed
 */
define('FEED_RSS', 'RSS');
/**
 * ATOM feed
 */
define('FEED_ATOM', 'ATOM');

/**
 * Implementation of a feed
 *
 * A Feed instance contains a type, a version, a hash (etag), a last
 * modified date, a syndication URL and a channel ({@link FeedChannel}).
 *
 * When parsing external feeds using {@link FeedReader}, an instance of feed is
 * used to collect the parsed information. When creating feeds through
 * the {@link FeedCreator} class, this class contains utility methods to
 * render the feed XML according with the feed type and version.
 *
 * @package xml
 * @subpackage feed
 * @uses Date
 * @uses FeedChannel
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Feed extends FeedNode
{
	/**
	 * Feed type
	 *
	 * It can be {@link FEED_RSS} or {@link FEED_ATOM}.
	 *
	 * @var string
	 */
	var $type;

	/**
	 * Format version (specially for {@link FEED_RSS})
	 *
	 * @var string
	 */
	var $version;

	/**
	 * Content type
	 *
	 * @var string
	 */
	var $contentType;

	/**
	 * Etag (hash)
	 *
	 * @var string
	 */
	var $etag;

	/**
	 * Last modified time
	 *
	 * @var int
	 */
	var $lastModified;

	/**
	 * Syndication URL
	 *
	 * @var string
	 */
	var $syndicationURL;

	/**
	 * Feed's channel
	 *
	 * @var FeedChannel
	 */
	var $Channel = NULL;

	/**
	 * Class constructor
	 *
	 * @param string $type Feed type
	 * @param string $version Format version
	 * @return Feed
	 */
	function Feed($type, $version=NULL) {
		parent::FeedNode();
		switch (strtoupper($type)) {
			case 'RDF' :
				$this->type = FEED_RSS;
				$this->version = '1.0';
				$this->contentType = 'application/xml';
				break;
			case 'RSS' :
				$this->type = FEED_RSS;
				$this->version = TypeUtils::ifNull($version, '2.0');
				$this->contentType = 'application/rss+xml';
				break;
			case 'ATOM' :
			case 'FEED' :
				$this->type = FEED_ATOM;
				$this->version = TypeUtils::ifNull($version, '0.3');
				$this->contentType = 'application/atom+xml';
				break;
			default :
				$this->type = FEED_RSS;
				$this->version = '2.0';
				$this->contentType = 'application/rss+xml';
				break;
		}
	}

	/**
	 * Checks if this feed is an ATOM feed
	 *
	 * @return bool
	 */
	function isATOM() {
		return ($this->type == FEED_ATOM);
	}

	/**
	 * Checks if this feed is an RSS feed
	 *
	 * @return bool
	 */
	function isRSS() {
		return ($this->type == FEED_RSS);
	}

	/**
	 * Get feed's etag
	 *
	 * @return string
	 */
	function getEtag() {
		return $this->etag;
	}

	/**
	 * Set feed's etag
	 *
	 * @param string $hash Etag
	 */
	function setETag($hash) {
		$this->etag = $hash;
	}

	/**
	 * Get the last modified time of this feed
	 *
	 * @param string $fmt Date format
	 * @return string
	 */
	function getLastModified($fmt='r') {
		return (TypeUtils::isInteger($this->lastModified) ? date($fmt, $this->lastModified) : $this->lastModified);
	}

	/**
	 * Set feed's last modified time
	 *
	 * The $lastModified argument can be provided as a timestamp value
	 * (integer), or a date written in RFC822 or ISO8601 formats.
	 *
	 * @param int|string $lastModified Last modified time
	 */
	function setLastModified($lastModified) {
		$this->lastModified = parent::parseDate($lastModified);
	}

	/**
	 * Get feed's syndication URL
	 *
	 * @return unknown
	 */
	function getSyndicationURL() {
		return $this->syndicationURL;
	}

	/**
	 * Set the syndication URL of this feed
	 *
	 * @param string $url Syndication URL
	 */
	function setSyndicationURL($url) {
		$this->syndicationURL = $url;
	}

	/**
	 * Get this feed's channel
	 *
	 * @return FeedChannel
	 */
	function &getChannel() {
		return $this->Channel;
	}

	/**
	 * Set this feed's channel
	 *
	 * @param FeedChannel $Channel
	 */
	function setChannel($Channel) {
		if (TypeUtils::isInstanceOf($Channel, 'FeedChannel'))
			$this->Channel = $Channel;
	}

	/**
	 * Get feed items
	 *
	 * @return array
	 */
	function getItems() {
		return $this->Channel->getChildren();
	}

	/**
	 * Creates a data set and fills it with the feed items
	 *
	 * @return DataSet
	 */
	function &createDataSet() {
		import('php2go.data.DataSet');
		$Dataset = DataSet::factory('array');
		$Dataset->load(isset($this->Channel) ? $this->Channel->getChildren() : array());
		return $Dataset;
	}

	/**
	 * Get a list of valid channel properties, according with feed type and version
	 *
	 * @return array
	 */
	function getChannelElementNames() {
		if ($this->isRSS()) {
			switch ($this->version) {
				// RSS 0.9 e 0.91
				case '0.9' :
				case '0.91' :
					return array(
						'title', 'description', 'link', 'image', 'textinput'
					);
				// RSS 0.92, 0.93 e 0.94
				case '0.92' :
				case '0.93' :
				case '0.94' :
					return array(
						'title', 'description', 'link', 'category', 'image', 'textinput',
						'cloud', 'language', 'copyright', 'docs', 'lastBuildDate',
						'managingEditor', 'pubDate', 'rating', 'skipDays', 'skipHours'
					);
				// RSS 1.0
				case '1.0' :
					return array(
						'title', 'description', 'link', 'image', 'textinput', 'language',
						'copyright', 'docs', 'lastBuildDate', 'managingEditor', 'pubDate',
						'rating', 'skipDays', 'skipHours'
					);
				// RSS 2.0
				default :
					return array(
						'title', 'description', 'link', 'category', 'image', 'textinput',
						'cloud', 'language', 'copyright', 'docs', 'lastBuildDate',
						'managingEditor', 'webMaster', 'pubDate', 'rating', 'skipDays',
						'skipHours', 'generator', 'ttl'
					);
			}
		} else {
			// ATOM 0.x
			return array(
				'title', 'tagline', 'link', 'author', 'contributor', 'id', 'generator',
				'copyright', 'info', 'created', 'issued', 'published', 'updated', 'modified'
			);
		}
	}

	/**
	 * Get a list of valid item attributes, according with feed type and version
	 *
	 * @return array
	 */
	function getItemElementNames() {
		if ($this->isRSS()) {
			switch ($this->version) {
				// RSS 0.9
				case '0.9' :
					return array(
						'title', 'link'
					);
				// RSS 0.91
				case '0.91 ':
					return array(
						'title', 'description', 'link'
					);
				// RSS 1.0
				case '1.0' :
					return array(
						'title', 'description', 'link', 'dc:date', 'dc:creator', 'dc:source', 'dc:format'
					);
				// RSS 0.92, 0.93 e 0.94
				case '0.92' :
				case '0.93' :
				case '0.94' :
					return array(
						'title', 'description', 'link', 'category', 'enclosure', 'source'
					);
				// RSS 2.0
				default :
					return array(
						'title', 'description', 'link', 'guid', 'author', 'pubDate', 'category', 'enclosure', 'source', 'comments'
					);
			}
		} else {
			// ATOM 0.x
			return array(
				'title', 'link', 'author', 'contributor', 'id', 'created',
				'issued', 'published', 'modified', 'updated', 'content', 'summary'
			);
		}
	}

	/**
	 * Collects attributes of the root node, when rendering feeds
	 *
	 * @return array
	 */
	function renderRootProperties() {
		if ($this->isRSS()) {
			if ($this->version == '1.0') {
				// RSS 1.0
				return array(
					'name' => 'rdf:RDF',
					'attrs' => array('xmlns' => 'http://purl.org/rss/1.0', 'xmlns:rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'xmlns:slash' => 'http://purl.org/rss/1.0/modules/slash/', 'xmlns:dc' => 'http://purl.org/dc/elements/1.1/')
				);
			} else {
				// RSS 0.9x e 2.0
				return array(
					'name' => 'rss',
					'attrs' => array('version' => $this->version)
				);
			}
		} else {
			// ATOM 0.x
			return array(
				'name' => 'feed',
				'attrs' => array('version' => $this->version, 'xmlns' => 'http://purl.org/atom/ns#')
			);
		}
	}

	/**
	 * Collects rendering data of the feed's channel
	 *
	 * @uses Date::formatTime()
	 * @return array
	 */
	function renderChannelElements() {
		if (TypeUtils::isInstanceOf($this->Channel, 'FeedChannel')) {
			$result = array();
			$elements = $this->getChannelElementNames();
			foreach ($elements as $element) {
				$value = $this->Channel->getElement($element);
				if (!$value)
					continue;
				// date/timestamp elements
				if (in_array($element, array('lastBuildDate', 'pubDate', 'modified', 'updated'))) {
					$result[$element] = parent::buildDate($value, $this->type, $this->version);
				}
				// elements whose properties are node attributes, instead of child nodes
				elseif ($element == 'cloud' || ($element == 'link' && $this->isATOM())) {
					$result[$element] = array('_attrs' => $this->_formatElementValue($value));
				}
				// other elements
				else {
					$result[$element] = $this->_formatElementValue($value);
				}
			}
			// attributes and special elements
			if ($this->isRSS() && $this->version == '1.0') {
				$result['_attrs'] = array('rdf:about' => htmlspecialchars($this->syndicationURL));
				$result['dc:date'] = Date::formatTime(time(), DATE_FORMAT_ISO8601);
				if (isset($result['image']) && isset($result['image']['link']))
					$result['image']['_attrs'] = array('rdf:about' => htmlspecialchars($result['image']['link']));
				$items = array();
				foreach ($this->Channel->getChildren() as $item)
					$items[] = array('_attrs' => array('rdf:resource' => htmlspecialchars($item->getElement('link', ''))));
				$result['items'] = array(
					'rdf:Seq' => array(
						'rdf:li' => $items
					)
				);
			}
			return $result;
		}
		return array();
	}

	/**
	 * Collects rendering data of the channel's items
	 *
	 * @return array
	 */
	function renderItems() {
		if (TypeUtils::isInstanceOf($this->Channel, 'FeedChannel')) {
			$itemList = array();
			$itemElements = $this->getItemElementNames();
			$items = $this->Channel->getChildren();
			foreach ($items as $item) {
				$itemData = array();
				reset($itemElements);
				foreach ($itemElements as $element) {
					$value = $item->getElement($element);
					if (!$value)
						continue;
					// date/timestamp elements
					if (in_array($element, array('pubDate', 'created', 'issued', 'published', 'modified', 'updated'))) {
						$itemData[$element] = parent::buildDate($value, $this->type, $this->version);
					}
					// enclosure: attributes and no child nodes
					elseif ($element == 'enclosure') {
						$itemData[$element] = array('_attrs' => $this->_formatElementValue($value));
					}
					// ATOM link: attributes, can be multiple
					elseif ($element == 'link' && $this->isATOM()) {
						if (is_array($value)) {
							// link set
							if (!TypeUtils::isHashArray($value) && !empty($value)) {
								foreach ($value as $key=>$link) {
									if (is_array($link))
										$value[$key] = array('_attrs' => $link);
									else
										$value[$key] = array('_attrs' => array('href' => htmlspecialchars($link)));
								}
								$itemData[$element] = $value;
							}
							// single link with attributes
							else {
								$value = $this->_formatElementValue($value);
								$itemData[$element] = array('_attrs' => $value);
							}
						}
						// simple string link: transform into element containing the href attribute
						else {
							$value = array('href' => htmlspecialchars($value));
							$itemData[$element] = array('_attrs' => $value);
						}
					}
					// other elements
					else {
						$itemData[$element] = $value;
					}
				}
				// attributes and special elements
				if ($this->isRSS() && $this->version == '1.0')
					$itemData['_attrs'] = array('rdf:about' => htmlspecialchars($item->getElement('link', '')));
				if ($this->isRSS() && !in_array($this->version, array('0.9', '0.91')))
					$itemData['source'] = array('_attrs' => array('url' => htmlspecialchars($this->syndicationURL)), '_cdata' => $this->Channel->getElement('title', ''));
				if ($this->isRSS() && $this->version == '2.0') {
					if (isset($itemData['guid']))
						$itemData['guid'] = array('_attrs' => array('isPermaLink' => 'true'), '_cdata' => $itemData['guid']);
				}
				$itemList[] = $itemData;
			}
			if ($this->isRSS())
				return array('item' => $itemList);
			else
				return array('entry' => $itemList);
		}
		return array();
	}

	/**
	 * Escapes HTML special chars on an element's value
	 *
	 * This method is able to process scalar values, single
	 * and 2-dimension numeric or hash arrays.
	 *
	 * @param mixed $value Element's value
	 * @access private
	 * @return mixed
	 */
	function _formatElementValue($value) {
		// composite or multiple elements (image, textinput, author, contributor)
		if (is_array($value)) {
			foreach ($value as $k=>$v) {
				// multiple AND composite elements (ex: contributor)
				if (is_array($value[$k])) {
					foreach ($value[$k] as $_k => $_v)
						$value[$k][$_k] = htmlspecialchars($_v);
				} else {
					$value[$k] = htmlspecialchars($v);
				}
			}
		} else {
			$value = htmlspecialchars($value);
		}
		return $value;
	}
}
?>