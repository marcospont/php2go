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
	import('php2go.datetime.Date');
	import('php2go.gui.LayerMenu');

	/**
	 * switch the following two lines to change the generation type of the menu: XML file or database query
	 */
	//define('MENU_GENERATION_TYPE', 1); // XML
	define('MENU_GENERATION_TYPE', 2); // DATABASE

	/**
	 * Catch calls to the IFRAME
	 */
	if (isset($_GET['op'])) {
		print "<div style='width:100%;text-align:center;font-family:Verdana;font-size:14px;'>";
		print "You've choosen option " . urldecode($_GET['op']) . "!";
		print "</div>";
		exit;
	}

	/**
	 * HTML document creation
	 */
	$doc = new Document('resources/layout_menu.example.tpl');


	/**
	 * HTML document configuration
	 */
	$doc->setTitle('PHP2Go Examples - php2go.gui.LayerMenu');	// page title
	$doc->setCache(TRUE);										// use or not use browser cache
	$doc->addStyle("resources/layer_menu.example.css");			// add menu CSS file
	$doc->addBodyCfg(array('style'=>'margin: 0em'));			// add BODY settings

    /**
     * create a new instance of the LayerMenu class
     */
	$layerMenu = new LayerMenu($doc);

	/**
	 * define the prefix for every link in the menu tree
	 */
    $layerMenu->setAddressPrefix('layermenu.example.php');

    /**
     * define the size of the menu (root level, if horizontal; entire menu, if vertical)
     */
    $layerMenu->setSize(500, 15);

    /**
     * set menu positioning
     * LAYER_MENU_ABSOLUTE (default)
     *   - the absolute position should be set using the setStartPoint($x, $y) method
     * LAYER_MENU_RELATIVE
     *   - to enable relative positioning, call setPositioning(LAYER_MENU_RELATIVE)
     */
    //$layerMenu->setStartPoint(15, 43);
    $layerMenu->setPositioning(MENU_RELATIVE);

	/**
	 * sets the disposition of the root level
	 * LAYER_MENU_EQUAL : all the elements have the same width
	 * LAYER_MENU_SIDE : the elements are place side by side
	 * PS: horizontal menus only
	 */
    $layerMenu->setRootDisposition(LAYER_MENU_EQUAL);

    /**
     * set the CSS style for the menu's root level (main style and "onMouseOver" style)
     */
	$layerMenu->setRootStyles('menu', 'menuOver');

    /**
     * set the CSS styles for the nested levels (main style, "onMouseOver" style and border style
     * the 4th and 5th parameters are the X and Y border sizes
     */
    $layerMenu->setChildrenStyles('menuChild', 'menuChildOver', 'menuBorder', 1, 1);

    /**
     * the height for each inner level, in pixels
     */
    $layerMenu->setChildrenHeight(20);

    /**
     * the time in miliseconds that the child level keeps visible after it loses the focus of the mouse
     */
    $layerMenu->setChildrenTimeout(200);

    /**
     * sets the minimum width of a menu child (in pixels)
     */
    $layerMenu->setMininumChildWidth(140);

    /**
     * defines the root level item spacing, in pixels (vertical or horizontal)
     */
    $layerMenu->setItemSpacing(10);

    if (MENU_GENERATION_TYPE == 1) {

		/**
	     * loads the menu tree from a XML file
	     * >> the XML must contain a MENU root tag, and 1..N ITEM nodes
	     * >> the attributes CAPTION (caption of the node) and LINK (link of the node) are mandatory
	     * >> any other attributes in the XML nodes will be ignored
	     */
	    $layerMenu->loadFromXmlFile('resources/layer_menu.example.xml');

    } else {

		$db =& Db::getInstance();
		$tables = $db->getTables();
		if (!in_array('menu', $tables)) {
			PHP2Go::raiseError("The <i>menu</i> table was not found! Please run <i>menu.sql</i>, located at the <i>ROOT/examples/sql</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
		} else {

		    /**
		     * loads the menu tree from a database
		     *
		     * >> the user must create a table with a self-association
		     *
		     * >> first parameter: the root (master) SQL
		     * the first parameter is the root sql: the result must contain the nodes that doesn't have a parent
		     *
		     * >> second parameter: the child SQL
		     * in the child SQL, you must define a link between the child levels (details)
		     * and the root level (master). here, this link is represented by the expression
		     * ~id_menu~, which will be used as placeholder to the id_menu column values
		     *
		     * >> both queries must contain two mandatory column aliases: CAPTION and LINK, even if the column names of your table are different
		     *
		     * >> optionally, you can provide a column with TARGET alias: this should be used as target to the link in the menu item
		     */
		    $layerMenu->loadFromDatabase(
		    	'select id_menu, caption, link from menu where id_parent_menu is null',
		    	'select id_menu, caption, link from menu where id_parent_menu = ~id_menu~'
		    );
		}

    }

    /**
     * generates the menu code, inserting it in the HTML document
     */
    $doc->assignByRef('menu', $layerMenu);

	/**
	 * display the page
	 */
    $doc->display();

?>