<?php

	// $Header: /www/cvsroot/php2go/examples/querybuilder.example.php,v 1.9 2006/06/17 14:59:51 mpont Exp $
	// $Revision: 1.9 $
	// $Date: 2006/06/17 14:59:51 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.db.QueryBuilder');

	println('<b>PHP2Go Examples</b> : php2go.db.QueryBuilder<br>');

	/**
	 * creates a new instance of the class
	 */
	$query = new QueryBuilder();
	/**
	 * make all reserved words lowercase
	 */
	$query->upCaseWords = false;
	/**
	 * add some fields
	 */
	$query->addFields('client.name, client.address, client.category');
	/**
	 * add a table (from clause)
	 */
	$query->addTable('client');
	/**
	 * add a join in the from clause and a field from the new table
	 */
	//$query->addFields('count(dependant.client_id) as dep_count');
	//$query->joinTable('dependant', 'inner join', 'client.client_id=dependant.client_id');
	/**
	 * add a condition clause
	 */
	//$query->addClause('dependant.status=1');
	/**
	 * add another clause, using the AND operador
	 */
	$query->addClause('client.active=1', QUERY_BUILDER_AND, QUERY_BUILDER_OP_NONE);
	/**
	 * add another clause, using the OR operator and using all the existing condition as the first operand
	 */
	$query->addClause('client.category=\'Master\'', QUERY_BUILDER_OR, QUERY_BUILDER_OP_ALL);
	/**
	 * add an order clause
	 */
	$query->addOrder('client.name asc');
	/**
	 * prefixes another order clause
	 */
	$query->prefixOrder('client.address desc');
	/**
	 * set the group condition
	 */
	$query->setGroup('client.client_id');
	/**
	 * display the SQL query
	 */
	$query->displayQuery();

	/**
	 * the following lines show how to run the query built with QueryQuilder using Db class
	 * the results are listed using ADORecordSet class
	 */
	println('<b>Execute query using simple DB query</b><br>');
	$resultSet =& $query->executeQuery();
	while (!$resultSet->EOF) {
		println($resultSet->fields['name'] . ' - ' . $resultSet->fields['dep_count'] . ' dependant(s)');
		$resultSet->MoveNext();
	}

	/**
	 * another way of executing the SQL query is using the DataSet class
	 */
	println('<br><br><b>Build DB dataset using the query</b><br>');
	$dataset =& $query->createDataSet();
	while (!$dataset->eof()) {
		println($dataset->getField('name') . ' - ' . $dataset->getField('dep_count') . ' dependant');
		$dataset->moveNext();
	}

?>