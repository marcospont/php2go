<?php

	// $Header: /www/cvsroot/php2go/examples/layermenu.example.php,v 1.7 2006/10/26 04:55:43 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2006/10/26 04:55:43 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

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
	 * HTML document creation
	 */
	$doc = new Document('resources/layout_menu.example.tpl');


	/**
	 * HTML document configuration
	 */
	$doc->setTitle('LAYER MENU EXAMPLE');						// title of the page
	$doc->setCache(TRUE);										// use or not use browser cache
	$doc->addStyle("resources/css.example.css");				// add a css stylesheet
	$doc->addStyle("resources/layer_menu.example.css");			// menu CSS styles
	$doc->addBodyCfg(array('style'=>'margin: 0em'));			// add BODY settings

    /**
     * create a new instance of the LayerMenu class
     */
	$layerMenu = new LayerMenu($doc);

	/**
	 * define the prefix for every link in the menu tree
	 */
    $layerMenu->setAddressPrefix('http://' . $_SERVER['SERVER_NAME'] . '/');

    /**
     * define the size of the menu (root level, if horizontal; entire menu, if vertical)
     */
    $layerMenu->setSize(500, 15);

    /**
     * the absolute X,Y start point of the main menu layer
     */
    $layerMenu->setStartPoint(25, 5);

    /**
     * set the CSS style for the root level of the menu (main style and "onMouseOver" style)
     */
	$layerMenu->setRootStyles('menu', 'menuOver');

	/**
	 * sets the disposition of the root level
	 * LAYER_MENU_EQUAL : all the elements have the same width
	 * LAYER_MENU_SIDE : the elements are place side by side
	 * PS: horizontal menus only
	 */
    $layerMenu->setRootDisposition(LAYER_MENU_EQUAL);

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
    $layerMenu->setChildrenTimeout(500);

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
			PHP2Go::raiseError("The <i>menu</i> table was not found! Please run <i>menu.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
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
     * insert some content in the main slot
     */
    $main = new DocumentElement();

    ob_start();
    echo "
    <p class='sample_style'>
    	<b>PHP2Go Examples: php2go.gui.LayerMenu</b><br>
    	Current Date: {date}<br>
    	API Version: {version}
    </p>
    ";
    $main->put(ob_get_clean(), T_BYVAR);
    $main->parse();
    $main->assign('date', Date::localDate());
    $main->assign('version', PHP2GO_VERSION);
    $doc->elements['main'] =& $main;

	/**
	 * output the content buffer
	 */
    $doc->display();

?>