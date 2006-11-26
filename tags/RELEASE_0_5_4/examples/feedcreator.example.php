<?php

	// $Header: /www/cvsroot/php2go/examples/feedcreator.example.php,v 1.2 2006/06/09 04:38:45 mpont Exp $
	// $Revision: 1.2 $
	// $Date: 2006/06/09 04:38:45 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.xml.feed.FeedCreator');

	/**
	 * create an instance of the FeedCreator class, specifying the feed type
	 * (FEED_RSS) and the format version to be used (2.0)
	 */
	$Feed = new FeedCreator(FEED_RSS, '2.0');

	/**
	 * Define the target encoding of the XML file
	 */
	$Feed->setEncoding('UTF-8');

	/**
	 * add/set elements in the feed channel providing name and value
	 * of the element or an array of name=>value pairs;
	 *
	 * the list of elements that can be added in the feed channel depend on the type
	 * and on the version of the feed (RSS 0.9x, 2.0 and ATOM 0.x specification)
	 */
	$Feed->setChannelElement('title', 'My Site Announcements');
	$Feed->setChannelElement(array(
		'description' => 'This is the announcements of my site. Please follow the link in each item to see more details.',
		'lastBuildDate' => time()
	));
	/**
	 * setChannelElement is a shortcut method that maps to methods of the FeedChannel class;
	 * you can call them directly, as shown below
	 */
	$Feed->Feed->Channel->setElement('language', PHP2Go::getConfigVal('LOCALE'));
	$Feed->Feed->Channel->addElement('category', 'News');
	$Feed->Feed->Channel->addElement('category', 'Programming');

	/**
	 * add a set of items in the feed channel
	 */
	for ($i=1; $i<=10; $i++) {
		$Item = new FeedItem;
		$Item->title = "Feed item nr. $i";
		$Item->description = "This is the item nr. $i. Please follow the link to see more details.";
		$Item->link = "http://mysite.org/news/load/$i";
		$Item->guid = "http://mysite.org/news/load/$i";
		$Feed->addItem($Item);
	}

	/**
	 * Send the contents of the feed to the browser
	 */
	$Feed->downloadFeed('announcements.xml');

?>