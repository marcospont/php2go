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

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.datetime.TimeCounter');

	$tc =& new TimeCounter();

	/**
	 * HTML document creation
	 */
	$doc = new Document('resources/layout2.example.tpl');

	/**
	 * configuration methods
	 */

	// title of the page
	$doc->setTitle('DOCUMENT EXAMPLE PAGE');
	// use or not use browser cache
	$doc->setCache(TRUE);
	// page charset. defaults to charset from the main config
	$doc->setCharset('iso-8859-1');
	// set language. defaults to language from the main config
	$doc->setLanguage('en-us');
	// set HTTP compression using gzip
	$doc->setCompression(TRUE, 9);
	// add a javascript file
	$doc->addScript("resources/javascript.example.js");
	// add a javascript instruction inside the HEAD tag
	$doc->addScriptCode("window.scrollTo(0,0);", 'JavaScript1.5', SCRIPT_START);
	// add a javascript instruction in the end of the BODY tag
	$doc->addScriptCode("document.write('this simple text was written using JavaScript');", 'JavaScript', SCRIPT_END);
	// add a CSS file
	$doc->addStyle("resources/css.example.css", 'screen');
	// the following line shows how to define a CSS file that will be used exclusively in print mode
	$doc->addStyle("resources/cssprint.example.css", 'print');
	// associate this document to an RSS feed
	$doc->addAlternateLink('application/rss+xml', "feedcreator.example.php", "Feed Creator example");
	// configure BODY tag attributes
	$doc->addBodyCfg(array('topmargin'=>0, 'leftmargin'=>0));
	// attach body events
	$doc->attachBodyEvent('onLoad', "testFunction()");
	$doc->attachBodyEvent('onBeforeUnload', "return confirm(\"Click OK to unload this window...\")");
	// append HTML content in the start of the BODY tag
	$doc->appendBodyContent("<!-- extra body content -->", BODY_START);

	/**
	 * create a new DocumentElement instance to build the header slot
	 */
	$header = new DocumentElement();
	/**
	 * put a template file in the element buffer
	 */
	$header->put('resources/template3.include.tpl', T_BYFILE);
	/**
	 * the include blocks must be resolved before the parse() call
	 */
	$header->includeAssign('include_block', 'resources/template4.include.tpl', T_BYFILE);
	/**
	 * parse the element content and assign a simple variable
	 */
	$header->parse();
	$header->assign('date_time', date("d/m/Y H:i:s"));
	/**
	 * attach the element (only accepts direct assign if the right side is a DocumentElement instance)
	 * if you want to perform operations in the template later in your code, don't forget the reference operator
	 */
	$doc->elements['header'] =& $header;
	/*

	/**
	 * create another document element using the createElement method
	 * here, you simply provide the content and the content type of the element,
	 * and you receive back an instance of the DocumentElement class already
	 * parsed and ready to assign variables and create blocks
	 */
	$menu =& $doc->createElement('menu', 'resources/template5.include.tpl');

	/**
	 * assign a simple variable
	 */
	$menu->assign('some_var', 'The quick brown fox jumps over the lazy dog');

	/**
	 * create another instance of DocumentElement to build the "main" slot
	 */
	$main = new DocumentElement();

	/**
	 * if you want to (or must) use echo and print statements, you can use output buffering
	 * however, we don't recommend that kind of freestanding code mixing php and html
	 */
	ob_start();
	echo "<table width='100%' cellpadding='2' cellspacing='0' border='0'>";
	for ($i=1; $i<=10; $i++) {
		echo "<tr><td>LINE $i</td></tr>";
	}
	echo "</table>";
	$main->put(ob_get_clean(), T_BYVAR);

	/**
	 * put simple string vars in the template
	 */
	$main->put('Main Slot<br>', T_BYVAR);
	$main->put('This is a text with a {variable}<br>', T_BYVAR);
	$main->put('Generation Time: {generation_time} seconds', T_BYVAR);

	/**
	 * parse the template, assign variables and attach it to the document
	 */
	$main->parse();
	$main->assign('variable', "VARIABLE");
	$main->assign('generation_time', round($tc->getElapsedTime(), 3));

	/**
	 * attach the element in the document using the assign method
	 * using it, all object instances that implement the "getContent"
	 * method will be automatically rendered in the element's placeholder
	 */
	$doc->assign('main', $main);

	/**
	 * save the final HTML code in a file
	 */
	$doc->toFile('tmp/document.html');

	/**
	 * output the content buffer
	 */
	$doc->display();


?>