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

	require_once('../config/config.php');
	import('php2go.base.Document');
	import('php2go.datetime.Date');
	import('php2go.form.FormBasic');
	import('php2go.service.AjaxService');
	import('php2go.template.Template');

	/**
	 * For coding organization purposes, we've created a class to wrap all the code
	 * necessary to this example. One can perform the same operations using other classes
	 * or simple procedural code. It's all up to you
	 */
	class AjaxPeople
	{
		var $dbConn = NULL;
		var $integrityRef = array();
		var $service = NULL;

		function AjaxPeople() {
			$this->service = new AjaxService();
			$this->service->registerObject($this, 'ajax');
			$this->dbConn =& Db::getInstance();
			$this->dbConn->setFetchMode(ADODB_FETCH_ASSOC);
			$tables = $this->dbConn->getTables();
			if (in_array('projects', $tables))
				$this->integrityRef['projects'] = 'id_manager';
			if (in_array('projects_people', $tables))
				$this->integrityRef['projects_people'] = 'id_people';
		}

		/**
		 * This method runs the AJAX handler. If an AJAX action is
		 * not performed, the index action takes place
		 */
		function run() {
			$this->service->handleRequest();
			$this->index();
		}

		/**
		 * Action that loads a record from the database
		 */
		function ajaxLoadRecord($params) {
			$response = new AjaxResponse();
			if (TypeUtils::isInteger($params['id_people'])) {
				$rs = $this->dbConn->query('select * from people where id_people = ?', TRUE, array($params['id_people']));
				if ($rs->recordCount() > 0) {
					$response->resetForm('people_form');
					$fields =& $rs->fields;
					// convert birth date and active fields
					$fields['birth_date'] = Date::fromSqlDate($fields['birth_date']);
					$fields['active'] = ($fields['active'] == 1 ? 'T' : 'F');
					$response->setValue($fields);
					$response->focus('name');
				} else {
					$response->updateContents('msg', 'Record not found!');
					$response->show('msg');
				}
			} else {
				$response->updateContents('msg', 'Invalid people ID!');
				$response->show('msg');
			}
			return $response;
		}

		/**
		 * Saves a record and returns the updated people list
		 */
		function ajaxSaveRecord($params) {
			$response = new AjaxResponse();
			// convert birth date and active values
			$params['active'] = ($params['active'] == 'T' ? 1 : 0);
			// update or insert
			if (!empty($params['id_people'])) {
				$idPeople = consumeArray($params, 'id_people');
				$res = @$this->dbConn->update('people', $params, 'id_people = ' . $idPeople);
			} else {
				// set 'add_date' field
				$params['add_date'] = date('Y-m-d');
				$res = @$this->dbConn->insert('people', $params);
				// expose new record key to a hidden field for update purposes
				$response->setValue('id_people', $res);
			}
			// set result message
			$response->updateContents('msg', ($res ? "<B>{$params['name']}</B> successfully saved!" : "Error saving data: " . $this->dbConn->getError()));
			$response->show('msg');
			// show updated list
			$tpl =& $this->getList();
			$response->updateContents('list_container', $tpl->getContent(), TRUE);
			return $response;
		}

		/**
		 * Deletes a record and returns the updated people list
		 */
		function ajaxDeleteRecord($params) {
			$response = new AjaxResponse();
			if (TypeUtils::isInteger($params['id_people'])) {
				// check integrity against other example tables
				if (!$this->dbConn->checkIntegrity('people', 'id_people', $params['id_people'], $this->integrityRef)) {
					$msg = 'This person can\'t be deleted!';
				}
				// execute the delete statement
				elseif (!@$this->dbConn->delete('people', 'id_people = ' . $params['id_people'])) {
					$msg = 'Error deleting person: ' . $this->dbConn->getError();
				}
				// delete OK
				else {
					if ($params['id_people'] == $params['current_loaded']) {
						$response->setValue('id_people', '');
						$response->resetForm('people_form');
					}
				}
			} else {
				$msg = 'Invalid people ID!';
			}
			$response->updateContents('msg', (isset($msg) ? $msg : 'Person successfuly deleted!'));
			$response->show('msg');
			// show updated list
			$tpl =& $this->getList();
			$response->updateContents('list_container', $tpl->getContent(), TRUE);
			return $response;
		}

		/**
		 * Performs an operation over multiple records. Currently
		 * supports only "delete" operation
		 */
		function ajaxMultiple($params) {
			$response = new AjaxResponse();
			if (!isset($params['operation']) || $params['operation'] != 'delete') {
				$response->updateContents('msg', 'Invalid operation!');
			} elseif (empty($params['chk'])) {
				$response->updateContents('msg', 'Invalid arguments!');
			} else {
				$this->dbConn->startTransaction();
				foreach ($params['chk'] as $idx => $idPeople) {
					// check integrity
					if (!($res = $this->dbConn->checkIntegrity('people', 'id_people', intval($idPeople), $this->integrityRef))) {
						$this->dbConn->failTransaction();
						$msg = "The person {$idPeople} can't be deleted!";
					}
					// delete the record
					elseif (!($res = $this->dbConn->delete('people', 'id_people = ' . intval($idPeople)))) {
						$this->dbConn->failTransaction();
						$msg = $this->dbConn->getError();
					}
				}
				$res = $this->dbConn->completeTransaction();
				$response->updateContents('msg', ($res ? 'Person records deleted successfully!' : 'Error deleting multiple person records: ' . $msg));
			}
			$response->show('msg');
			// show updated list
			$tpl =& $this->getList();
			$response->updateContents('list_container', $tpl->getContent(), TRUE);
			return $response;
		}

		/**
		 * Index action. Generates and prints the main page content (form, list)
		 */
		function index() {
			/**
			 * create and configure the HTML document
			 */
			$doc = new Document('layout.tpl');
			$doc->setFocus('people_form');
			$doc->setTitle('PHP2Go Examples - AJAX + Forms');
			/**
			 * create and configure the form
			 */
			$form = new FormBasic('form.xml', 'people_form', $doc);
			$form->setFormWidth(430);
			$form->setErrorStyle('error', FORM_ERROR_FLOW, NULL, 'error_header');
			$form->setErrorDisplayOptions(FORM_CLIENT_ERROR_DHTML);
			$form->setLabelWidth(0.25);
			$form->setAccessKeyHighlight(TRUE);
			/**
			 * add form and list in the document
			 */
			$doc->assignByRef('form', $form);
			$doc->assignByRef('list', $this->getList());
			$doc->display();
		}

		/**
		 * Parse and configure a simple template containing the people list
		 */
		function &getList() {
			$tpl = new Template('list.tpl');
			$tpl->parse();
			$tpl->assign('people', $this->dbConn->query("select id_people, name, sex from people order by name"));
			return $tpl;
		}
	}

	$people = new AjaxPeople;
	$people->run();

?>