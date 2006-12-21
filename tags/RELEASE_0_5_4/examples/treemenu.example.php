<?php

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.gui.TreeMenu');

	/**
	 * switch the following two lines to change the generation type of the menu: XML file or database query
	 */
	define('MENU_GENERATION_TYPE', 1); // XML
	//define('MENU_GENERATION_TYPE', 2); // DATABASE

	/**
	 * Database connection
	 */
	$db =& Db::getInstance();
	$db->setFetchMode(ADODB_FETCH_ASSOC);

	/**
	 * HTML document creation
	 */
	$doc = new Document('resources/layout2_menu.example.tpl');


	/**
	 * HTML document configuration
	 */
	$doc->setTitle('TREE MENU EXAMPLE');						// title of the page
	$doc->setCache(TRUE);										// use or not use browser cache
	$doc->addStyle("resources/css.example.css");				// add a css stylesheet
	$doc->addStyle("resources/layer_menu.example.css");			// menu CSS styles
	$doc->addBodyCfg(array('style'=>'margin: 0em'));			// add BODY settings

	/**
	 * Create an instance of the TreeMenu class,
	 * providing as parameter the active instance of the Document class
	 */
	$treeMenu = new TreeMenu($doc);

	/**
	 * Define the prefix of all tree node's links
	 */
	$treeMenu->setAddressPrefix('http://' . $_SERVER['SERVER_NAME'] . '/');

	/**
	 * The TreeMenu class generates a DIV element whose attribute "position" is
	 * always "absolute". So, we must define an absolute position in order to
	 * place the menu contents
	 */
	$treeMenu->setStartPoint(5, 55);

	/**
	 * Set the default CSS class for the tree nodes
	 */
	$treeMenu->setDefaultStyle('sample_menu_child');

	/**
	 * The setLevelStyle method allows to customize the CSS class of one of the menu levels (0 is the root level)
	 */
	$treeMenu->setLevelStyle(0, 'sample_menu');

	/**
	 * Configure padding and spacing of the tree nodes
	 */
	$treeMenu->setItemPadding(1);
	$treeMenu->setItemSpacing(1);

	/**
	 * In this example, we're using the default icons for folder, buttons.
	 * But when using customized images, this method could be useful in order
	 * to generate HTML images with explicit width and height
	 */
	$treeMenu->setImageSize(16, 16);

	/**
	 * Hide the open/close buttons of the tree
	 */
	$treeMenu->hideButtons();

	/**
	 * Hide folder/file icons in the menu tree
	 * IMPORTANT: if you hide buttons and folders simultaneously,
	 * there will be no way to expand/collapse inner levels without
	 * loading the links associated with the nodes
	 */
	//$treeMenu->hideFolders();

	/**
	 * Allow only one tree branch at once
	 */
	$treeMenu->oneBranchAtOnce();

	/**
	 * Background-color of the container DIV and tree nodes
	 * This color is replaced by the background-color of the CSS class defined to the menu levels/nodes
	 */
	$treeMenu->setBackgroundColor('#f4f4f4');

    if (MENU_GENERATION_TYPE == 1) {

		/**
	     * loads the menu tree from a XML file
	     * >> the XML must contain a MENU root tag, and 1..N ITEM nodes
	     * >> the attributes CAPTION (caption of the node) and LINK (link of the node) are mandatory
	     * >> any other attributes in the XML nodes will be ignored
	     */
	    $treeMenu->loadFromXmlFile('resources/layer_menu.example.xml');

    } else {

	    /**
	     * loads the menu tree from a database
	     * >> the user must create a table with a self-association (the SQL insert script is commented in the beginning of this script)
	     * >> the first parameter is the root (master) sql: the result must contain the nodes that doesn't have a parent
	     * >> the second parameter is the child (detail) sql: the query that fetches the 2...N levels of the
	     * tree, using a bind variable pointing to the field in the last query that represents the parent id
	     * >> both queries must contain two mandatory column aliases: CAPTION and LINK, even if the column names of your table are different
	     */
		$tables = $db->getTables();
		if (!in_array('menu', $tables)) {
			PHP2Go::raiseError("The <i>menu</i> table was not found! Please run <i>menu.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
		} else {
		    $treeMenu->loadFromDatabase(
		    	'select id_menu, caption, link from menu where id_parent_menu is null',
		    	'select id_menu, caption, link from menu where id_parent_menu = ~id_menu~'
		    );
		}

    }

    $doc->assignByRef('menu', $treeMenu);

	/**
	 * Load some content in the main slot
	 */
	$main = new DocumentElement();
	$main->put('resources/main1.example.tpl', T_BYFILE);
	$main->parse();
	$rs =& $db->query("select code as 'Code', short_desc as 'Short Desc' from products");
	$main->assign('data', $rs->getArray());
	$doc->elements['main'] =& $main;

	$doc->display();

?>