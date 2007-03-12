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

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.data.DataSet');
	import('php2go.datetime.Date');
	import('php2go.file.FileManager');
	import('php2go.template.DocumentElement');

	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (!in_array('client', $tables)) {
		PHP2Go::raiseError("The <i>client</i> table was not found! Please run <i>clients.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * document creation, using a template layout containing three slots: header, menu and main
	 */
	$document = new Document('resources/layout2.example.tpl');
	$document->setTitle('PHP2Go Example - php2go.template.DocumentElement');

	/**
	 * simple element : a simple string
	 */
	$document->assign('header', "<span style='color:red'>This is an example : PAGE HEADER</span>");

	/**
	 * quickly assign the contents of a file to a document element
	 */
	$document->assign('menu', FileSystem::getContents('resources/menu.include.html'));

	/**
	 * the DocumentElement class is an special type of template that contains
	 * utility functions that help with the task of fetching data from external
	 * sources and displaying it
	 */
	$element = new DocumentElement();

	/**
	 * content add
	 * 1) put a template file
	 * 2) put another template file, below first
	 * 3) add a string variable
	 * 4) parse/compile the template
	 */
	$element->put('resources/template1.include.tpl', T_BYFILE);
	$element->put('resources/template2.include.tpl', T_BYFILE);
	$element->put('<table border="0"><tr><td>End of the page slot!</td></tr></table>', T_BYVAR);
	$element->parse();

	/**
	 * defines a global value for a variable
	 * each time that this variable is found in the template, it will be assigned to this value
	 */
	$element->globalAssign('date', Date::localDate());

	/**
	 * block replication, assigning for each instance the values returned in a database record
	 */
	$element->generateFromQuery('master_client', 'select NAME, ADDRESS from client where CATEGORY = \'Master\' order by NAME limit 5');

	/**
	 * shows a data set, with automatic creation of the container (external) block and N instances of a loop block "or" an instance
	 * of an "empty results" block. the DataSet class can load data from a database, from a XML file or from a CSV file.
	 * the "common_clients" block is the container block. it's here that we declare the table heading
	 * the "common_client_loop" block must contain all the query aliases declared as block variables
	 * the "common_clients_empty" is created when the dataset contains no data
	 */
	$dataset =& DataSet::factory('db');
	$dataset->load('select NAME, ADDRESS from client where name like \'%ma%\'');
	$element->generateFromDataSet($dataset, 'common_clients', 'common_clients_empty', 'common_client_loop');

	/**
	 * after all operations were performed on the template, the DocumentElement can now be
	 * attached into the document. Below you can see all the ways provided by PHP2Go to assign
	 * a DocumentElement instance to a document's element
	 *
	 * 1) Assign directly the "elementName" key of the "elements" property, using
	 *    the "getContent" method of the object;
	 * 2) Assign the "elementName" key of the "elements" property by reference.
	 *    As DocumentElement implements the "getContent" method, the object content
	 *    will be automatically processed by the Document class;
	 * 3) Using the assign method and sending the template contents as parameter;
	 * 4) Using the assign method and sending the template object as parameter
	 *    (same explanation from item 2);
	 * 5) Using the assignByRef method and sending the template object reference as
	 *    parameter. The difference from this item and item 4 is that here you'll be
	 *    able to operate on the DocumentElement instance even after the assignByRef
	 *    method is called.
	 */
	/* 1) */
	//$document->elements['main'] = $element->getContent();
	/* 2 */
	//$document->elements['main'] =& $element;
	/* 3 */
	//$document->assign('main', $element->getContent());
	/* 4 */
	$document->assign('main', $element);
	/* 5 */
	//$document->assignByRef('main', $element);


	/**
	 * displays the final content of the HTML document
	 */
	$document->display();

?>