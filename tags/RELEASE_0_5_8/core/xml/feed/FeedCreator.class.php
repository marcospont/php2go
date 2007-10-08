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

import('php2go.net.HttpRequest');
import('php2go.xml.XmlRender');
import('php2go.xml.feed.Feed');

/**
 * Builds and renders feeds
 *
 * Example:
 * <code>
 * $feed = new FeedCreator(FEED_RSS, '2.0');
 * $feed->setEncoding('iso-8859-1');
 * $feed->setChannelElement('title', 'My Site Announcements');
 * $feed->setChannelElement('description', 'This is the announcements of my site.');
 * $feed->setChannelElement('lastBuildDate', time());
 * for ($i=1; $i<=10; $i++) {
 *   $item = new FeedItem;
 *   $item->title = "Feed item nr. {$i}";
 *   $item->description = "This is the item nr. {$i}";
 *   $item->link = "http://my.site/news/{$i}";
 *   $feed->addItem($item);
 * }
 * $feed->downloadFeed('announcements.xml');
 * </code>
 *
 * @package xml
 * @subpackage feed
 * @uses Feed
 * @uses TypeUtils
 * @uses XmlRender
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FeedCreator extends PHP2Go
{
	/**
	 * Feed XML encoding
	 *
	 * @var string
	 */
	var $encoding;

	/**
	 * CSS stylesheet
	 *
	 * @var string
	 */
	var $css;

	/**
	 * XSL stylesheet
	 *
	 * @var string
	 */
	var $xsl;

	/**
	 * Internal Feed instance
	 *
	 * @var object Feed
	 */
	var $Feed = NULL;

	/**
	 * Class constructor
	 *
	 * @param string $feedType Feed type
	 * @param string $feedVersion Format version
	 * @return FeedCreator
	 */
	function FeedCreator($feedType, $feedVersion=NULL) {
		parent::PHP2Go();
		$this->encoding = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->Feed = new Feed($feedType, $feedVersion);
		$this->Feed->setSyndicationURL(HttpRequest::url());
		$this->Feed->setChannel(new FeedChannel());
		$this->Feed->Channel->setElement('generator', 'PHP2Go Feed Creator ' . PHP2GO_VERSION);
	}

	/**
	 * Sets the encoding of the feed XML
	 *
	 * @param string $encoding Encoding
	 */
	function setEncoding($encoding) {
		$this->encoding = $encoding;
	}

	/**
	 * Defines a CSS stylesheet for the feed
	 *
	 * @param string $url Stylesheet URL
	 */
	function setCssStylesheet($url) {
		$this->css = $url;
	}

	/**
	 * Defines a XSL stylesheet for the feed
	 *
	 * @param string $url Stylesheet URL
	 */
	function setXslStylesheet($url) {
		$this->xsl = $url;
	}

	/**
	 * Defines one or more channel attributes
	 *
	 * @param string|array $name Attribute name or hash of attributes and values
	 * @param mixed $value Attribute value
	 */
	function setChannelElement($name, $value='') {
		if (TypeUtils::isHashArray($name)) {
			foreach ($name as $key => $value)
				$this->Feed->Channel->setElement($key, $value);
		} else {
			$this->Feed->Channel->setElement($name, $value);
		}
	}

	/**
	 * Adds a new entry of a channel's composite element
	 *
	 * @param string $name Element value
	 * @param mixed $value Element's new entry
	 */
	function addChannelElement($name, $value) {
		$this->Feed->Channel->addElement($name, $value);
	}

	/**
	 * Adds an item on the feed's channel
	 *
	 * @param FeedItem $Item
	 */
	function addItem($Item) {
		$this->Feed->Channel->addItem($Item);
	}

	/**
	 * Renders and displays the feed, along with the download HTTP headers
	 *
	 * @uses _renderFeed()
	 * @param string $fileName File name to be used on the response headers
	 */
	function downloadFeed($fileName) {
		header('Content-type: ' . $this->Feed->contentType);
		/**
		 * @todo how to generate an Etag header?
		 */
		$Rend =& $this->_renderFeeed();
		$Rend->download($fileName, TRUE, $this->Feed->contentType);
	}

	/**
	 * Renders and saves the feed on a file
	 *
	 * @uses _renderFeed()
	 * @param string $fileName File path
	 * @return bool
	 */
	function saveFeed($fileName) {
		$Rend =& $this->_renderFeeed();
		return $Rend->toFile($fileName);
	}

	/**
	 * Renders the feed
	 *
	 * Creates an instance of the {@link XmlRender} class, and populates
	 * the XmlDocument with the provided properties: encoding, CSS stylesheet,
	 * XSL stylesheet, channel elements and feed items.
	 *
	 * Returns the renderer instance, so that other methods can decide what
	 * to do with the generated XML string.
	 *
	 * @return XmlRender
	 * @access private
	 */
	function &_renderFeeed() {
		// last modified time
		$this->Feed->setLastModified(time());
		$rootProperties = $this->Feed->renderRootProperties();
		$Rend = new XmlRender($rootProperties['name'], $rootProperties['attrs']);
		// target encoding
		$Rend->setCharset($this->encoding);
		// stylesheet links
		if (!empty($this->css))
			$Rend->Document->addStylesheet($this->css, FALSE, 'text/css');
		if (!empty($this->xsl))
			$Rend->Document->addStylesheet($this->xsl, FALSE, 'text/xsl');
		// adds channel and items
		if ($this->Feed->isRSS())
			$Node =& $Rend->Document->DocumentElement->addChild(new XmlNode('channel', array()));
		else
			$Node =& $Rend->getRoot();
		$Rend->addContentAt($Node, $this->Feed->renderChannelElements(), array('createArrayNode' => FALSE, 'arrayEntryAsRepeat' => TRUE, 'attributeKey' => '_attrs', 'cdataKey' => '_cdata'));
		$Rend->addContentAt($Node, $this->Feed->renderItems(), array('createArrayNode' => FALSE, 'arrayEntryAsRepeat' => TRUE, 'attributeKey' => '_attrs', 'cdataKey' => '_cdata'));
		$Rend->render();
		return $Rend;
	}
}
?>