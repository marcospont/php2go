<?php

	// $Header: /www/cvsroot/php2go/examples/htmlutils.example.php,v 1.12 2006/06/15 01:34:53 mpont Exp $
	// $Revision: 1.12 $
	// $Date: 2006/06/15 01:34:53 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once("../p2gConfig.php");
	import('php2go.util.HtmlUtils');

	println('<b>PHP2Go Examples</b> : php2go.util.HtmlUtils<br>');

	/**
	 * Utility method to build HTML anchors
	 */
	$anchor = HtmlUtils::anchor('javascript:void(0);', 'Click me!', 'Text hint in the status bar', '', array('onClick'=>"alert(\"onClick\")"));
	println("<b>Anchor:</b> $anchor<br>");

	/**
	 * Method that builds mailto: anchors with obfuscation, protecting the e-mail address against robots
	 */
	$mailto = HtmlUtils::mailtoAnchor('mpont@users.sourceforge.net', 'Obfuscated mailto anchor', 'Obfuscated mailto anchor');
	println("<b>Mailto anchor:</b> $mailto<br>");

	/**
	 * Utility method to buil IMG tags
	 */
	$image = HtmlUtils::image("http://www.php2go.com.br/resources/images/p2g_powered.gif");
	println("<b>Image:</b> $image<br>");

	/**
	 * Build an HTML table
	 */
	$db = Db::getInstance();
	$db->setFetchMode(ADODB_FETCH_ASSOC);
	println("<b>HTML table:</b>");
	println(HtmlUtils::table($db->getAll("select * from products where amount > 35"), TRUE, "cellpadding='4' style='border:1px solid #000'"));

	/**
	 * HTML lists (unordered, ordered, definition)
	 */
	println("<b>HTML lists: ordered, unordered, definitions:</b>");
	print(HtmlUtils::itemList(array('PHP2Go', 'Web', 'Development', 'Framework'), TRUE));
	print(HtmlUtils::itemList(array('This', 'is', 'an', 'unordered', 'list'), FALSE));
	print(HtmlUtils::definitionList(PHP2Go::getConfigVal('LANGUAGE')));

	/**
	 * Another way of using HtmlUtils::anchor, this time with an event handler that opens a popup window
	 * IMPORTANT NOTE : The HtmlUtils::window method will not work in a script that doesn't have an instance of the Document class. In these cases,
	 * you must include PHP2GO_JAVASCRIPT_PATH . 'php2go.js' to make it work
	 */
	$script = "<script language='JavaScript' type='text/javascript' src='" . PHP2GO_JAVASCRIPT_PATH . 'php2go.js' . "'></script>";
	$anchor = HtmlUtils::anchor('javascript:void(0)', 'Open a new window', 'Open a new window', '', array('onClick' => HtmlUtils::window('http://www.php2go.com.br', 32, 800, 600)));
	println("<b>Popup window anchor:</b> $script$anchor<br>");

	/**
	 * Utility method to repeat the same tag $n times
	 */
	$repeat = HtmlUtils::tagRepeat('big', 'PHP2Go', 3);
		println("<b>Tag repeat:</b> " . $repeat . "<br>");

	/**
	 * The following method can build buttons, with JS events and CSS support
	 */
	$button = HtmlUtils::button('BUTTON', 'btnTest', 'Click me!', "onClick=\"alert('onClick');\"", 'button alt');
	println("<b>Button:</b> " . $button . "<br>");

	/**
	 * You can parse the links included in a text using the following method
	 */
	$parsedText = HtmlUtils::parseLinks("Visit http://www.php2go.com.br, and download PHP2Go! You can also visit the SF project page : http://sourceforge.net/projects/php2go/");
	println("<b>Parse links in text:</b> $parsedText<br>");

?>