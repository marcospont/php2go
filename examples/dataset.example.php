<?php

	// $Header: /www/cvsroot/php2go/examples/dataset.example.php,v 1.12 2006/06/17 14:59:51 mpont Exp $
	// $Revision: 1.12 $
	// $Date: 2006/06/17 14:59:51 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.data.DataSet');

	println('<b>PHP2Go Example</b> : php2go.data.DataSet');
	println('<b>Also using:</b> php2go.data.adapter.DataSetDb, php2go.data.adapter.DataSetXml, php2go.data.adapter.DataSetCsv, php2go.data.adapter.DataSetArray');

	/**
	 * Type "db" - dataset using a database as external source
	 * Available optional parameters:
	 *	- debug (bool): enable or disable debug in the DB connection
	 *	- connectionId (string): ID of the database connection to be used
	 * Example using eof and moveNext methods
	 */
	print('<br><hr>');
	println('<b>DataSet from a database : select * from client where category = \'Master\' order by name</b>');
	$DataSet =& DataSet::factory('db', array(
		'debug' => TRUE,
		'connectionId' => 'DEFAULT'
	));
	$DataSet->load('select * from client where category = ? order by name', array('Master'));
	while (!$DataSet->eof()) {
		println($DataSet->getField('name'));
		$DataSet->moveNext();
	}

	/**
	 * Type "xml" - dataset using a XML file as external source
	 * Available optional parameters: none
	 * Example using fetchInto method
	 */
	print('<br><hr>');
	println('<b>DataSet from XML file : resources/datasetxml.example.xml</b>');
	$DataSet =& DataSet::factory('xml');
	$DataSet->load('resources/datasetxml.example.xml', DS_XML_CDATA);
	while ($DataSet->fetchInto($fields)) {
		// the field names are always uppercased
		println($fields['FIELD']);
	}

	/**
	 * Type "csv" - create a dataset based on the contents of a CSV file (comma-separated values)
	 * Available optional parameters: none
	 * Example using eof, moveNext and fetch methods
	 */
	print('<br><hr>');
	println('<b>DataSet from CSV file : resources/datasetcsv.example.csv</b>');
	$DataSet =& DataSet::factory('csv');
	$DataSet->load('resources/datasetcsv.example.csv');
	println('10 last lines');
	$DataSet->move(16);
	while (!$DataSet->eof()) {
		println($DataSet->getField('letter'));
		$DataSet->moveNext();
	}
	println('10 first lines');
	$DataSet->moveFirst();
	while ($fields = $DataSet->fetch()) {
		println($fields['letter']);
		if ($DataSet->getAbsolutePosition() == 10) break;
	}

	/**
	 * Type "array" - create an array based dataset
	 * Available optional parameters: none
	 * Example using fetch method
	 */
	print('<br><hr>');
	println('<b>DataSet from a bidimensional array</b>');
	$DataSet =& DataSet::factory('array');
	$DataSet->load(array(
		array('KEY' => 1),
		array('KEY' => 2),
		array('KEY' => 3),
		array('KEY' => 4),
		array('KEY' => 5),
		array('KEY' => 6),
		array('KEY' => 7),
		array('KEY' => 8),
		array('KEY' => 9),
		array('KEY' => 10)
	));
	while ($fields = $DataSet->fetch()) {
		println($fields['KEY']);
	}

?>