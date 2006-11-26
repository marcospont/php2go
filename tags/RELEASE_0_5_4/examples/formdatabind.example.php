<?php

	// $Header: /www/cvsroot/php2go/examples/formdatabind.example.php,v 1.7 2006/09/20 23:51:52 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2006/09/20 23:51:52 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.form.FormDataBind');

	/**
	 * create and configure an instance of the class document, where the form will be included
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->setCache(FALSE);
	$doc->setCompression();
	$doc->addScript('resources/javascript.example.js');
	$doc->addStyle('resources/css.example.css');
	$doc->addBodyCfg(array('bgcolor'=>'#ffffff', 'style'=>'margin:0em'));

	/**
	 * create an instance of FormDataBind. this form uses an external template to apply the fields HTML code
	 * besides, the fields are populated with the content of a database record, and the user can navigate
	 * through the table content using DataBind. this component works only under Internet Explorer
	 * the 6th parameter is the table name (CLIENT)
	 * the 7th parameter is the table primary key (CLIENT_ID)
	 */
	$form = new FormDataBind('resources/formdatabind.example.xml', 'resources/formdatabind.example.tpl', 'myForm', $doc, array(), 'client', 'client_id');

	/**
	 * here we define the SQL query that will be used to retrieve the table values and create the DBCSV file
	 */
	$form->setDataSetQuery('client_id,name,address,category,active', 'client', '', 'name');

	/**
	 * the databind tool can filter and sort data using user defined criteria
	 * the following method receives as parameter a formatted string: name#caption|name#caption|...
	 */
	$form->setFilterSortOptions('name#Name|address#Address|category#Category');

	/**
	 * uncomment the following line to force records to be inserted/updated/deleted using
	 * a default POST request, instead of inserting/updating/deleting records via JSRS
	 */
	//$form->disableJsrs();

	/**
	 * assign the form in the "main" element of the document
	 */
	$doc->assignByRef('main', $form);

	$doc->display();

?>