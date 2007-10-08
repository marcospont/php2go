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

	require_once('../config/config.php');
	import('php2go.xml.feed.FeedReader');
	import('php2go.data.PagedDataSet');
	import('php2go.net.HttpRequest');
	import('php2go.util.HtmlUtils');

	println('<b>PHP2Go Examples</b> : php2go.xml.feed.FeedReader');
	println('<b>Also using</b> : php2go.data.DataSetArray, php2go.data.PagedDataSet<br />');

	/**
	 * create an instance of the FeedReader class
	 */
	$Reader = new FeedReader();
	/**
	 * define base directory and lifetime of the cache engine
	 */
	$Reader->setCacheProperties(PHP2GO_CACHE_PATH, 60*30);
	/**
	 * fetch the feed from the remote URL
	 * cache will be used if exists and not stale
	 */
	$Feed =& $Reader->fetch('http://www.php2go.com.br/rss.php');
	/**
	 * test the value to prevent errors
	 */
	if ($Feed) {
		println(sprintf("<b>%s</b><br />Last Modified: %s<br />", $Feed->Channel->title, $Feed->getLastModified('r')));
		/**
		 * the return value of the fetch method is an instance of php2go.xml.feed.Feed;
		 * this object has a property called Channel (php2go.xml.feed.FeedChannel);
		 * inside this channel are stored the set of items (php2go.xml.feed.FeedItem);
		 * the properties of the item differ from one specification to another (different
		 * version of RSS or ATOM pattern
		 */
		$Iterator = $Feed->Channel->itemIterator();
		while ($Iterator->hasNext()) {
			$Item = $Iterator->next();
			println(sprintf("<b>%s</b> - [%s]", $Item->title, HtmlUtils::anchor($Item->link, $Item->link)));
		}
		println('<hr /><a name=\'dataset\'></a>');
		/**
		 * the second example shows how to fill a dataset using the feed items;
		 * in this special case, we're using a PagedDataSet to browse the items
		 */
		println('<b>Example using a PagedDataSet to browse the items</b>');
		$DataSet =& PagedDataSet::factory('array');
		$DataSet->setPageSize(10);
		$DataSet->load($Feed->getItems());
		if ($DataSet->getRecordCount() > 0) {
			println(sprintf("Current page: %d", $DataSet->getCurrentPage()));
			println(sprintf("Max items per page: %d", $DataSet->getPageSize()));
			println(sprintf("Total items: %d", $DataSet->getTotalRecordCount()));
			println(sprintf("%s%s",
				($DataSet->atFirstPage() ? '' : HtmlUtils::anchor(HttpRequest::basePath() . '?page=' . $DataSet->getPreviousPage() . '#dataset', '<b>[ < Previous ]</b>') . "&nbsp"),
				($DataSet->atLastPage() ? '' : HtmlUtils::anchor(HttpRequest::basePath() . '?page=' . $DataSet->getNextPage() . '#dataset', '<b>[ Next > ]</b>'))
			));
			while (!$DataSet->eof()) {
				println(sprintf("<a href=\"%s\" target=\"_blank\">%s</a>", $DataSet->getField('link'), $DataSet->getField('title')));
				$DataSet->moveNext();
			}
		} else {
			println('Empty result set');
		}
	}

?>