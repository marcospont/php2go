<?php

	// $Header: /www/cvsroot/php2go/examples/report.example.php,v 1.22 2006/10/19 00:49:45 mpont Exp $
	// $Revision: 1.22 $
	// $Date: 2006/10/19 00:49:45 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.data.Report');
	import('php2go.net.HttpRequest');

	/**
	 * validate if client table is present in the active database.
	 * this test routine is not mandatory. we're just using here in
	 * order to improve usability of PHP2Go use cases
	 */
	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (!in_array('client', $tables)) {
		PHP2Go::raiseError("The <i>client</i> table was not found! Please run <i>clients.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * define report parameters based on "rep" request parameter
	 */
	$rep = HttpRequest::get('rep');
	if (intval($rep) > 1 && $rep <= 2) {
		Registry::set('rep', $rep);
		$reportConfig = array(
			'id' => intval($rep),
			'xml' => "resources/report{$rep}.example.xml",
			'tpl' => "resources/report{$rep}.example.tpl",
			'extraVars' => "&rep={$rep}"
		);
	} else {
		$reportConfig = array(
			'id' => 1,
			'xml' => "resources/report.example.xml",
			'tpl' => "resources/report.example.tpl",
			'extraVars' => ""
		);
	}

	/**
	 * create a document instance using a layout with a single element "main"
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->addStyle('resources/css.example.css');

	/**
	 * create the report instance
	 */
	$report = new Report($reportConfig['xml'], $reportConfig['tpl'], $doc);

	/**
	 * fill report's template with available report options
	 */
	$report->Template->assign('options', array(
		1 => 'Grid, headers, sortable',
		2 => 'Table, 2 records per line'
	));

	/**
	 * define extraVars : variables that must be added in every link
	 * or form action built by the class
	 */
	$report->setExtraVars($reportConfig['extraVars']);

	/**
	 * define report's line handler : a function or method used to transform
	 * every row right before being displayed
	 */
	$report->setLineHandler('lineHandler');

	/**
	 * calling "build" separetely gives you the opportunity of checking
	 * SQL clause and dataset before generating report output
	 */
	$report->build();

	/**
	 * the assignByRef method of the Document class allows you to bind
	 * any object containing a "getContent" method to a given element
	 * declared in the layout template
	 */
	$doc->assignByRef('main', $report);

	/**
	 * generate and display the document's output
	 */
	$doc->display();

	/**
	 * this is the line handler, used to transform values of each record included in a report's page;
	 * the handlers in php2go.data.Report can be any kind of callbacks: functions, static or dynamic methods;
	 * a line handler must receive the hash array of a record as parameter and return it with the necessary transformations
	 */
	function lineHandler($data) {
		/**
		 * inside a line handler, we can transform a data column in a list of anchors pointing to actions;
		 * in this simple case, we will simulate anchors to edit and delete the record in the database
		 */
		if ($rep = Registry::get('rep'))
			$rep = "&rep={$rep}";
		$actions = array(
			HtmlUtils::anchor(HttpRequest::basePath() . "?edit=" . $data['client_id'] . $rep, 'Edit', 'Edit this record'),
			HtmlUtils::anchor(HttpRequest::basePath() . "?delete=" . $data['client_id'] . $rep, 'Delete', 'Delete this record')
		);
		$data['client_id'] = join('&nbsp;', $actions);
		return $data;
	}

?>