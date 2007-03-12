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

	define('ADODB_OUTP', 'addToBuffer');
	$output = '';

	$doc = new Document('resources/layout.example.tpl');
	$doc->addStyle('resources/css.example.css');

	/**
	 * Connecting to the database
	 *
	 * a) Db::getInstance()
	 *		Calling this method without parameters, the class will
	 * 		establish a connection with the default connection of
	 * 		the DATABASE configuration settings
	 * b) Db::getInstance("id")
	 * 		Calling this way will result in a connection with the
	 * 		database whose connection parameters are identified by
	 * 		"id" in the DATABASE configuration settings
	 *
	 * In PHP4, with or without the & sign, the getInstance method will
	 * guarantee a single connection per connection ID. However, using &,
	 * you can persist modifications made to the Db instances
	 */
	$db =& Db::getInstance();

	/**
	 * Configure connection
	 * 1) Enabling or disabling debug
	 * 2) Defining default fetch mode (ADODB_FETCH_NUM, ADODB_FETCH_ASSOC, ADODB_FETCH_BOTH or ADODB_FETCH_DEFAULT)
	 */
	$db->setDebug(TRUE);
	$db->setFetchMode(ADODB_FETCH_ASSOC);

	ob_start();
	println('<b>PHP2Go Example</b> : php2go.db.Db<br>');
	print('<table cellpadding=8 width=700 style=\'margin-right:6px;border:1px solid #000\' align=\'left\' class=\'sample_simple_text\'><tr><td valign=\'top\'>');

	/**
	 * Executing a simple query, and printing
	 * information about the returned record set
	 */
	$rs =& $db->query("select * from client");
	println('Record count: ' . $rs->recordCount());
	println('Field count: ' . $rs->fieldCount());
	print('<br>');

	/**
	 * Executing a simple query using bind parameters
	 * and iterating the results using fetchRow
	 */
	$rs =& $db->query("select * from client where name like ?", TRUE, array('A%'));
	while ($row = $rs->fetchRow())
		println($row['name']);
	print('<br>');

	/**
	 * Iterating using EOF and moveNext
	 */
	$rs->moveFirst();
	while (!$rs->EOF) {
		println($rs->fields['name']);
		$rs->moveNext(); // always remember to call this when using EOF :-)
	}
	print('<br>');

	/**
	 * Using limit expressions
	 *
	 * a) second parameter is the offset
	 * b) third parameter is the lower bound (defaults to 0)
	 */
	$rs =& $db->limitQuery("select * from client order by name, address", 10, 10);
	while (!$rs->EOF) {
		println($rs->fields['name']);
		$rs->moveNext();
	}
	print('<br>');

	/**
	 * Executing a prepared statement and
	 * iterating using fetchInto
	 */
	$stmt = $db->prepare("select * from products where active = ? and code like ?");
	$rs =& $db->execute($stmt, array(1, 'BK%'));
	while ($rs->fetchInto($row)) {
		println($row['short_desc']);
	}
	print('</td><td valign=\'top\'>');

	/**
	 * Shortcut functions
	 *
	 * a) retrieving the first cell (0, 0)
	 * b) retrieving the first row
	 * c) retrieving the first column
	 * d) retrieving all as a 2-dimension array
	 * e) retrieving the record count (modifying the SQL query)
	 */
	$value = $db->getFirstCell("select code from products");
	println($value);
	$row = $db->getFirstRow("select code, short_desc, price from products");
	dumpVariable($row);
	$col = $db->getFirstCol("select code from products where code like 'BK00%'");
	dumpVariable($col);
	$grid = $db->getAll("select name from country");
	$count = $db->getCount("select * from client");
	dumpVariable($count);
	print('</td><td valign=\'top\'>');

	/**
	 * Insert, update and delete, using ADODb Smart Transactions
	 */
	$db->startTransaction();
	$rs =& $db->query("select id_people from people where name = " . $db->quoteString('Example people'));
	if ($rs->recordCount() == 0) {
		$res = @$db->insert('people', array(
			'name' => 'Example people',
			'sex' => 'M',
			'birth_date' => $db->date('01/01/1970'),
			'address' => 'First Av., 1000',
			'id_country' => 1,
			'add_date' => date('Y-m-d'),
			'active' => 1
		));
		if ($res) {
			println('Inserted id: ' . $res . '.<br>Refresh to see the update operation.');
		} else {
			println('Insert failed: ' . $db->getError());
			$db->failTransaction();
		}
	} else {
		$res = @$db->update('people', array(
			'id_country' => 30
		), 'id_people = ' . $rs->fields['id_people']);
		if ($res) {
			println('Updated ID: ' . $rs->fields['id_people']);
		} else {
			println('Update failed: ' . $db->getError());
			$db->failTransaction();
		}
	}
	$db->delete('people', 'id_people > 20');
	$db->completeTransaction();
	print('<br>');

	/**
	 * Retrieving DB meta data
	 * 1) server info
	 * 2) databases
	 * 3) tables
	 * 4) table columns
	 * 5) table primary keys
	 */
	dumpVariable($db->getServerInfo());
	dumpVariable($db->getDatabases());
	dumpVariable($db->getTables());
	dumpVariable($db->getColumnNames('country'));
	dumpVariable($db->getPrimaryKeys('country'));
	dumpVariable($db->getIndexes('client'));

	print('</td></tr></table>');
	print('<div class=\'sample_simple_text\' style=\'padding:6px;border:1px solid #000;height:400px;overflow:auto\'><b>SQL log:</b><br><br>');
	print $output;
	print('</div>');

	$doc->assign('main', ob_get_clean());
	$doc->display();

	function addToBuffer($sql) {
		global $output;
		$output .= $sql . '<br>';
	}

?>