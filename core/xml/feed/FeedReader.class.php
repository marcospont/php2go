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

import('php2go.cache.CacheManager');
import('php2go.net.HttpClient');
import('php2go.net.Url');
import('php2go.xml.XmlParser');
import('php2go.xml.feed.Feed');

/**
 * Parses feeds from external URLs
 *
 * Transforms the parsed data into a tree of objects (FeedChannel
 * and FeedItem). Supports caching of feed data (based on a lifetime),
 * and supports RSS 0.9x, 1.0, 2.0 and ATOM 0.x formats.
 *
 * Example:
 * <code>
 * $reader = new FeedReader();
 * $reader->setCacheProperties('cache/feeds/', 60*30);
 * $feed =& $reader->fetch('http://www.php2go.com.br/rss.php');
 * $iterator =& $feed->Channel->itemIterator();
 * while ($iterator->hasNext()) {
 *   $item = $iterator->next();
 *   println($item->title);
 * }
 * </code>
 *
 * @package xml
 * @subpackage feed
 * @uses CacheManager
 * @uses Feed
 * @uses FeedChannel
 * @uses FeedItem
 * @uses HttpClient
 * @uses TypeUtils
 * @uses Url
 * @uses XmlParser
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FeedReader extends PHP2Go
{
	/**
	 * Target encoding, to be used when parsing feeds
	 *
	 * @var string
	 */
	var $targetEncoding;

	/**
	 * User agent to be sent when fetching feeds
	 *
	 * @var string
	 */
	var $userAgent;

	/**
	 * Last fetched feed URL
	 *
	 * @var object Url
	 */
	var $Url = NULL;

	/**
	 * Cache options
	 *
	 * @var array
	 */
	var $cacheOptions = array();

	/**
	 * Last HTTP response
	 *
	 * @var array
	 * @access private
	 */
	var $_lastResponse = NULL;

	/**
	 * Current feed being processed
	 *
	 * @var object Feed
	 * @access private
	 */
	var $_currentFeed;

	/**
	 * Controls items while parsing the feed
	 *
	 * @var object FeedItem
	 * @access private
	 */
	var $_currentItem;

	/**
	 * Controls composite elements while parsing the feed
	 *
	 * @var array
	 * @access private
	 */
	var $_currentCompElement;

	/**
	 * Controls elements while parsing the feed
	 *
	 * @var array
	 * @access private
	 */
	var $_currentElement;

	/**
	 * Controls attributes while parsing the feed
	 *
	 * @var array
	 * @access private
	 */
	var $_currentAttrs;

	/**
	 * Class constructor
	 *
	 * @return FeedReader
	 */
	function FeedReader() {
		parent::PHP2Go();
		if (!function_exists('xml_parser_create'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'xml'), E_USER_ERROR, __FILE__, __LINE__);
		$this->targetEncoding = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->userAgent = 'PHP2Go Feed Reader ' . PHP2GO_VERSION . ' (compatible; MSIE 6.0; Linux)';
		$this->cacheOptions['enabled'] = TRUE;
		$this->cacheOptions['initialized'] = FALSE;
		$this->cacheOptions['group'] = 'php2goFeedReader';
	}

	/**
	 * Gets the response produced from the last request
	 *
	 * Returns a hash array containing 2 keys: headers (response headers)
	 * and body (response body). If a feed is loaded from cache, the headers
	 * represent the last time the feed was fetched from its original source.
	 *
	 * @return array
	 */
	function getLastResponse() {
		return $this->_lastResponse;
	}

	/**
	 * Set target encoding
	 *
	 * Defines which encoding must be used by PHP's expat parser
	 * when parsing the contents of feeds.
	 *
	 * @param string $encoding Encoding
	 */
	function setTargetEncoding($encoding) {
		$this->targetEncoding = $encoding;
	}

	/**
	 * Set user agent to be used when fetching feeds
	 *
	 * @param string $userAgent User agent
	 */
	function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
	}

	/**
	 * Set cache properties
	 *
	 * @param string $dir Base dir
	 * @param int $lifeTime Lifetime, in seconds
	 * @param string $group Cache group
	 */
	function setCacheProperties($dir, $lifeTime=NULL, $group=NULL) {
		$this->cacheOptions['baseDir'] = $dir;
		if ($lifeTime)
			$this->cacheOptions['lifeTime'] = $lifeTime;
		if (!empty($group))
			$this->cacheOptions['group'] = $group;
	}

	/**
	 * Fetch a feed from an external URL
	 *
	 * When caching is enabled, the method tries to load the feed from
	 * cache. If not found, or if cache is not enabled, the feed is
	 * fetched using an HTTP connection and parsed through an expat
	 * parser.
	 *
	 * @param string $url URL
	 * @return FeedChannel
	 */
	function &fetch($url) {
		$fallback = NULL;
		$this->_reset();
		$this->Url = (TypeUtils::isInstanceOf($url, 'Url') ? $url : new Url($url));
		// cache enabled?
		if ($this->cacheOptions['enabled']) {
			if (!$this->cacheOptions['initialized']) {
				$Cache = CacheManager::factory('file');
				if ($this->cacheOptions['lifeTime'])
					$Cache->Storage->setLifeTime($this->cacheOptions['lifeTime']);
				if ($this->cacheOptions['baseDir'])
					$Cache->Storage->setBaseDir($this->cacheOptions['baseDir']);
				$this->cacheOptions['initialized'] = TRUE;
			}
			// cache hit
			$data = $Cache->load($this->Url->getUrl(), $this->cacheOptions['group']);
			if ($data !== FALSE) {
				$this->_lastResponse = $data['response'];
				$this->_currentFeed = $data['feed'];
				return $this->_currentFeed;
			// cache miss
			} elseif ($this->_fetchFeed()) {
				$data = array(
					'response' => $this->_lastResponse,
					'feed' => $this->_currentFeed
				);
				$Cache->save($data, $this->Url->getUrl(), $this->cacheOptions['group']);
				return $this->_currentFeed;
			}
		// normal fetch
		} elseif ($this->_fetchFeed()) {
			return $this->_currentFeed;
		}
		return $fallback;
	}

	/**
	 * Internal method used to fetch feed contents through an HTTP connection
	 *
	 * @access private
	 * @return bool
	 */
	function _fetchFeed() {
		static $Http;
		if (!isset($Http)) {
			$Http = new HttpClient();
			$Http->setFollowRedirects(TRUE);
			$Http->setUserAgent($this->userAgent);
		}
		$Http->setHost($this->Url->getHost());
		$status = $Http->doGet(TypeUtils::ifNull($this->Url->getPath() . $this->Url->getQueryString(TRUE), '/'));
		$this->_lastResponse = array(
			'headers' => $Http->responseHeaders,
			'body' => $Http->responseBody
		);
		if ($status == HTTP_STATUS_OK) {
			return $this->_parseFeed($this->_lastResponse['body']);
		}
		return FALSE;
	}

	/**
	 * Internal method used to parse the XML contents of a feed
	 *
	 * @uses XmlParser::createParser()
	 * @param string $content Feed contents
	 * @access private
	 * @return bool
	 */
	function _parseFeed($content) {
		$parser = XmlParser::createParser(
			$content, NULL,
			array(
				XML_OPTION_TARGET_ENCODING => $this->targetEncoding,
				XML_OPTION_SKIP_WHITE => 1,
				XML_OPTION_CASE_FOLDING => 0
			)
		);
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, '_startElement', '_endElement');
		xml_set_character_data_handler($parser, '_characterData');
		if (!xml_parse($parser, $content)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_XML_PARSE', array(xml_error_string(xml_get_error_code($parser)), xml_get_current_line_number($parser), xml_get_current_column_number($parser))), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		xml_parser_free($parser);
		return TRUE;
	}

	/**
	 * Start element handler of the feed parser
	 *
	 * @param resource $parser Parser
	 * @param string $element Element name
	 * @param array $attrs Element attributes
	 * @access private
	 */
	function _startElement($parser, $element, $attrs) {
		$name = NULL; $ns = NULL;
		$this->_parseNodeName($element, $name, $ns);
		if (!isset($this->_currentFeed)) {
			$this->_currentFeed = new Feed($name, @$attrs['version']);
			$this->_currentFeed->setEtag(@$this->_lastResponse['headers']['Etag']);
			$this->_currentFeed->setLastModified(@$this->_lastResponse['headers']['Last-Modified']);
			$this->_currentFeed->setSyndicationURL($this->Url->getUrl());
			$this->_currentFeed->setChannel(new FeedChannel());
		} else {
			switch (strtolower($name)) {
				// channel: FEED_RSS only
				case 'channel' :
					break;
				// item and entry: specify an item
				case 'entry' :
				case 'item' :
					$this->_currentItem = new FeedItem();
					if (isset($attrs['rdf:about']))
						$this->_currentItem->setElement('rdf:about', $attrs['rdf:about']);
					break;
				// image and textinput: elements with internal attributes
				case 'image' :
				case 'textinput' :
					$this->_currentCompElement = array($name, (!empty($attrs) ? $attrs : array()), FALSE);
					break;
				// contributor: multiple and internal attributes
				case 'contributor' :
					$this->_currentCompElement = array($name, (!empty($attrs) ? $attrs : array()), TRUE);
					break;
				// author: internal attributes when type is FEED_ATOM
				case 'author' :
					if ($this->_currentFeed->isATOM())
						$this->_currentCompElement = array($name, array(), FALSE);
					else
						$this->_currentElement = array($name, '', FALSE);
					break;
				// other tags
				default :
					$multiple = (($this->_currentFeed->isATOM() && $name == 'LINK') || $name == 'CATEGORY' ? TRUE : FALSE);
					if ($this->_currentFeed->isATOM())
						$this->_currentElement = array($name, '', $multiple);
					else
						$this->_currentElement = array($element, '', $multiple);
					$this->_currentAttrs = $attrs;
					break;
			}
		}
	}

	/**
	 * End element handler of the feed parser
	 *
	 * @param resource $parser Parser
	 * @param string $element Element name
	 * @access private
	 */
	function _endElement($parser, $element) {
		$name = NULL; $ns = NULL;
		$this->_parseNodeName($element, $name, $ns);
		switch (strtolower($name)) {
			// root node
			case 'rss' :
			case 'rdf' :
				break;
			// channel and feed
			case 'channel' :
			case 'feed' :
				break;
			// entry and item: the current item must be added in the channel
			case 'entry' :
			case 'item' :
				$this->_currentFeed->Channel->addItem($this->_currentItem);
				$this->_currentItem = NULL;
				break;
			default :
				// inclusion of a composite or multiple element
				if (isset($this->_currentCompElement) && $name == $this->_currentCompElement[0]) {
					if ($this->_currentCompElement[2] === TRUE)
						$this->_currentFeed->Channel->addElement($this->_currentCompElement[0], $this->_currentCompElement[1]);
					else
						$this->_currentFeed->Channel->setElement($this->_currentCompElement[0], $this->_currentCompElement[1]);
					$this->_currentCompElement = NULL;
				// inclusion of a simple element
				} else {
					if (empty($this->_currentElement[1]) && !empty($this->_currentAttrs))
						$this->_currentElement[1] = $this->_currentAttrs;
					if (isset($this->_currentCompElement)) {
						$this->_currentCompElement[1][$this->_currentElement[0]] = $this->_currentElement[1];
					} else {
						// item element
						if (isset($this->_currentItem)) {
							if ($this->_currentElement[2] === TRUE)
								$this->_currentItem->addElement($this->_currentElement[0], $this->_currentElement[1]);
							else
								$this->_currentItem->setElement($this->_currentElement[0], $this->_currentElement[1]);
						}
						// channel element
						else {
							if ($this->_currentElement[2] === TRUE)
								$this->_currentFeed->Channel->addElement($this->_currentElement[0], $this->_currentElement[1]);
							else
								$this->_currentFeed->Channel->setElement($this->_currentElement[0], $this->_currentElement[1]);
						}
					}
					$this->_currentElement = NULL;
					$this->_currentAttrs = NULL;
				}
		}
	}

	/**
	 * CDATA handler of the feed parser
	 *
	 * @param resource $parser Parser
	 * @param string $text CDATA contents
	 * @access private
	 */
	function _characterData($parser, $text) {
		if (isset($this->_currentElement))
			$this->_currentElement[1] .= $text;
	}

	/**
	 * Retrieves the namespace and the local name from a qualified name
	 *
	 * @param string $qualifiedName Qualified name
	 * @param string &$name Used to return the local name
	 * @param string &$ns Used to return the namespace prefix
	 * @access private
	 */
	function _parseNodeName($qualifiedName, &$name, &$ns) {
		$matches = array();
		if (preg_match("/^(([^\:]+)\:)?(.*)$/", $qualifiedName, $matches)) {
			$name = $matches[3];
			$ns = TypeUtils::ifNull($matches[2], '');
		}
	}

	/**
	 * Resets all control variables before fetching a new feed
	 *
	 * @access private
	 */
	function _reset() {
		$this->_lastResponse = NULL;
		$this->_currentFeed = NULL;
		$this->_currentItem = NULL;
		$this->_currentCompElement = NULL;
		$this->_currentElement = NULL;
		$this->_currentAttrs = array();
	}
}
?>