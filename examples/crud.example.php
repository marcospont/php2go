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
	import('php2go.db.DMLBuilder');
	import('php2go.data.Report');
	import('php2go.form.FormBasic');
	import('php2go.net.HttpRequest');
	import('php2go.net.HttpResponse');

	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (!in_array('people', $tables)) {
		PHP2Go::raiseError("The <i>people</i> table was not found! Please run <i>people.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}
	if (!in_array('country', $tables)) {
		PHP2Go::raiseError("The <i>country</i> table was not found! Please run <i>country.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * create and configure an instance of the class document, which will control
	 * the HTML document that will be built by this script
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->setCache(FALSE);
	$doc->addBodyCfg(array('style'=>'margin:0', 'bgcolor'=>'#ffffff'));
	$doc->addStyle('resources/css.example.css');
	$doc->setTitle('PHP2Go Examples - CRUD');

	/**
	 * In this simple example, we will switch between different operations over the
	 * same table based on a GET parameter called "action". This parameter indicates
	 * which action must be performed: list, create, update or delete. If this parameter
	 * is missing, the default action is performed (list). Otherwise, a function that handles
	 * the action is called.
	 */
	$action = HttpRequest::get('action');
	if ($action == 'list') {
		listAction($doc);
	} elseif ($action == 'create' || $action == 'update') {
		formAction($doc, $action);
	} elseif ($action == 'delete') {
		deleteAction($doc);
	} else {
		listAction($doc);
	}

	/**
	 * Display the final HTML content
	 */
	$doc->display();

	/**
	 * This function hanldes the "create" and "update" actions. It has the responsability
	 * of building and processing the form, and saving the record data in the database
	 */
	function formAction(&$doc, $action) {
		/**
		 * Create a database connection.
		 * It's very important to always use the getInstance method, because it guarantees that
		 * only a single connection to the database is opened in each script.
		 */
		$db =& Db::getInstance();
		/**
		 * Now we verify if the request action is "update". If so, we must read the requested
		 * record id and load its data from the database to the form.
		 */
		if ($action == 'update') {
			$id = HttpRequest::get('id_people');
			/**
			 * We also validate if the provided id is a valid integer number. If this test fails,
			 * the listAction function is called. You'd better change this to show some error message, but
			 * to keep it simple, we've implemented this simple "forward".
			 */
			if (!TypeUtils::isInteger($id)) {
				listAction($doc);
				return;
			}
			/*
			 * The "toGlobals" method receives an SQL query as parameter. This query is executed, and each
			 * field of the first returned row is published in the global scope. When this operation is done,
			 * a form will be able to read these values. If you pay attention, you'll notice that each field of
			 * the database query maps to a form field (attribute "name").
			*/
			$result = $db->toGlobals("
				select
					name,
					sex,
					date_format(birth_date, '%d/%m/%Y') birth_date,
					address,
					id_country,
					notes,
					active
				from
					people
				where
					id_people = {$id}
			", FALSE, TRUE);
			/**
			 * A "false" return value in the "toGlobals" method means that the database query returned
			 * an empty result set. In this case, we also perform a forward to the list action.
			 */
			if ($result === FALSE) {
				listAction($doc);
				return;
			}
		}
		/**
		 * Create and apply some presentation settings to an instance of the FormBasic class
		 */
		$form = new FormBasic('resources/form.people.xml', 'people', $doc);
		/**
		 * Verify if the form is posted and if it's valid, according to the rules specified in the XML file
		 */
		if ($form->isPosted()) {
			if ($form->isValid()) {
				/**
				 * The "getSubmittedValues" is the best way to read the information posted by a form.
				 * In the following lines, we perform the necessary transformations for dates (input is
				 * using d/m/Y format, and mySQL uses Y-m-d format) and checkboxes (PHP2Go checkboxes
				 * send T or F, and the active column is 0 or 1 in the database)
				 */
				$values = $form->getSubmittedValues();
				$values['birth_date'] = Date::fromEuroToSqlDate($values['birth_date']);
				$values['active'] = ($values['active'] == 'T' ? 1 : 0);
				/**
				 * Create an instance of the DMLBuilder class, which will be used to build the INSERT and UPDATE statements
				 */
				$dml = new DMLBuilder($db);
				$dml->ignoreEmptyValues = TRUE;
				if ($action == 'create') {
					/**
					 * When creating a new record in the "people" table, the "add_date" field must be filled with the current timestamp.
					 * This field is not changed anymore, so we must set this value here, before the insert operation.
					 */
					$values['add_date'] = date('d/m/Y H:i:s');
					/**
					 * Prepare and execute the INSERT statement
					 */
					$dml->prepare(DML_BUILDER_INSERT, 'people', $values);
					$result = $dml->execute();

					/**
					 * As we're using a POST request, it's very common to redirect after success.
					 * This is done here using utility methods of php2go.net.HttpResponse and
					 * php2go.net.HttpRequest classes
					 */
					if ($result) {
						HttpResponse::redirect(new Url(HttpRequest::basePath() . '?action=list'));
						exit;
					}
				} else {
					/**
					 * Prepare and execute the UPDATE statement
					 */
					$dml->prepare(DML_BUILDER_UPDATE, 'people', $values, 'id_people = ' . $id);
					$result = $dml->execute();
					if ($result) {
						HttpResponse::redirect(new Url(HttpRequest::basePath() . '?action=list'));
						exit;
					}
				}
				/**
				 * If we reach this line, the form is valid but the database couldn't save the record data.
				 * So, we use the addErrors method of the Form class to register the error(s) occurred.
				 * IMPORTANT: Using FormTemplate, you're able to change the error presentation settings or
				 * show these errors in a custom block/variable of your template
				 */
				$form->addErrors($db->getError());
			}
		}
		/**
		 * Add the form to the HTML document
		 */
		$doc->setFocus('people', 'name');
		$doc->assignByRef('main', $form);
	}

	/**
	 * This action is used to list the records currently stored in the "people" table
	 */
	function listAction(&$doc) {
		/**
		 * Create and configure an instance of the Report class
		 */
		$report = new Report('resources/report.people.xml',  'resources/report.people.tpl', $doc);
		$report->setLineHandler('lineHandler');
		$doc->assignByRef('main', $report);
	}

	/**
	 * Function used to transform each line of the list, building links to another actions
	 */
	function lineHandler($line) {
		$actions = array(
			HtmlUtils::anchor(HttpRequest::basePath() . '?action=update&id_people=' . $line['id_people'], 'Edit', 'Edit', 'input_style'),
			HtmlUtils::anchor(HttpRequest::basePath() . '?action=delete&id_people=' . $line['id_people'], 'Delete', 'Delete', 'input_style', array('onClick' => 'return confirm("Are you sure?")'))
		);
		$line['id_people'] = join('&nbsp;', $actions);
		return $line;
	}

	/**
	 * Function used to delete records of the "people" table
	 */
	function deleteAction(&$doc) {
		/**
		 * We must validate if we received a valid record id from the request.
		 * If this test fails, we perform a "forward" to the list action.
		 */
		$id = HttpRequest::get('id_people');
		if (!TypeUtils::isInteger($id)) {
			listAction($doc);
			return;
		}
		/**
		 * Pick up a database connection, and perform a delete operation in the
		 * "people" table, based on the provided record id.
		 */
		$db =& Db::getInstance();
		$db->delete('people', 'id_people = ' . $id);
		/**
		 * Redirect after the execution of the delete operation, preventing
		 * browser reload and usability problems :-)
		 */
		HttpResponse::redirect(new Url(HttpRequest::basePath() . '?action=list'));
	}

?>