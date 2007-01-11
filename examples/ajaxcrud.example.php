<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.datetime.Date');
	import('php2go.form.FormBasic');
	import('php2go.net.HttpRequest');
	import('php2go.template.Template');
	import('php2go.util.json.JSONEncoder');

	/**
	 * For coding organization purposes, we've created a class to wrap all the code
	 * necessary to this example. One can perform the same operations using other classes
	 * or simple procedural code. It's all up to you
	 */
	class AjaxPeople
	{
		var $action = NULL;
		var $request = array();
		var $dbConn = NULL;

		function AjaxPeople() {
			$this->action = @$_POST['action'];
			$this->request = $_POST;
			$this->dbConn =& Db::getInstance();
			$this->dbConn->setFetchMode(ADODB_FETCH_ASSOC);
		}

		/**
		 * This method calls the proper method to handle the requested action
		 */
		function run() {
			switch ($this->action) {
				case 'load' :
					$this->load();
					break;
				case 'save' :
					$this->save();
					break;
				case 'delete' :
					$this->delete();
					break;
				case 'multiple' :
					$this->multiple();
					break;
				default :
					$this->index();
					break;
			}
		}

		/**
		 * Action that loads a record from the database,
		 * and return its fields as a JSON string
		 */
		function load() {
			if (TypeUtils::isInteger($this->request['id_people'])) {
				$rs = $this->dbConn->query("select * from people where id_people = ?", TRUE, array($this->request['id_people']));
				/**
				 * the record data is returned in the response body,
				 * so we must send the proper Content-Type header
				 */
				header("Content-Type: application/json; charset=iso-8859-1");
				if ($rs->recordCount() > 0) {
					$fields = $rs->fields;
					/**
					 * Convert birth date and active values
					 * date: SQL -> EURO
					 * checkboxes : (1|0) -> (T|F)
					 */
					$fields['birth_date'] = Date::fromSqlToEuroDate($fields['birth_date']);
					$fields['active'] = ($fields['active'] == 1 ? 'T' : 'F');
					print JSONEncoder::encode($fields);
					return;
				}
			}
			print '{}';
		}

		/**
		 * Saves a record and returns the updated people list
		 */
		function save() {
			ob_start();
			/**
			 * Decode utf-8 request variables
			 */
			foreach ($this->request as $name => $value)
				$this->request[$name] = utf8_decode($value);
			/**
			 * Convert birth date and active values
			 * date: EURO -> SQL
			 * checkboxes : (T|F) -> (1|0)
			 */
			$this->request['birth_date'] = Date::fromEuroToSqlDate($this->request['birth_date']);
			$this->request['active'] = ($this->request['active'] == 'T' ? 1 : 0);
			/**
			 * Update or insert
			 */
			if (!empty($this->request['id_people'])) {
				$idPeople = $this->request['id_people'];
				unset($this->request['id_people']);
				$res = @$this->dbConn->update('people', $this->request, 'id_people = ' . $idPeople);
			} else {
				$this->request['add_date'] = date('Y-m-d');
				$res = @$this->dbConn->insert('people', $this->request);
				print "<script type=\"text/javascript\">$('id_people').value = {$res};</script>";
			}
			/**
			 * the message is returned using the X-JSON record
			 * generated output and list is returned in the response body
			 */
			header("X-JSON: " . JSONEncoder::encode(($res ? "<B>{$this->request['name']}</B> successfully saved!" : "Error saving data: " . $db->getError())));
			print ob_get_clean();
			$tpl = $this->getList();
			$tpl->display();
		}

		/**
		 * Deletes a record and returns the updated people list
		 */
		function delete() {
			$res = FALSE;
			if (TypeUtils::isInteger($this->request['id_people'])) {
				$res = @$this->dbConn->delete('people', 'id_people = ' . $this->request['id_people']);
			}
			/**
			 * the confirmation/error message is returned using the X-JSON header
			 * list is returned in the response body
			 */
			header("X-JSON: " . JSONEncoder::encode(($res ? "Person successfuly deleted!" : "Error deleting person: " . $this->dbConn->getError())));
			$tpl = $this->getList();
			$tpl->display();
		}

		/**
		 * Performs an operation over multiple records. Currently
		 * supports only "delete" operation
		 */
		function multiple() {
			$res = FALSE;
			$op = HttpRequest::post('operation');
			$chk = HttpRequest::post('chk');
			if (!$op || $op != 'delete') {
				header("X-JSON: " . JSONEncoder::encode("Invalid operation!"));
			} elseif (empty($chk)) {
				header("X-JSON: " . JSONEncoder::encode("Invalid arguments!"));
 			} else {
				$this->dbConn->startTransaction();
				foreach ($chk as $idx => $idPeople)
					if (!($res = $this->dbConn->delete('people', 'id_people = ' . intval($idPeople))))
						$this->dbConn->failTransaction();
				$res = $this->dbConn->completeTransaction();
				header("X-JSON: " . JSONEncoder::encode(($res ? "Person records deleted successfully!" : "Error deleting multiple person records: " . $this->dbConn->getError())));
			}
			$tpl = $this->getList();
			$tpl->display();
		}

		/**
		 * Index action. Generates and prints the main page content (form, list)
		 */
		function index() {
			/**
			 * create and configure the HTML document
			 */
			$doc = new Document('resources/ajaxcrudlayout.example.tpl');
			$doc->setFocus('form');
			$doc->setTitle("PHP2Go Examples - AJAX & Forms");
			/**
			 * create and configure the form
			 */
			$form = new FormBasic('resources/ajaxcrud.example.xml', 'form', $doc);
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
			$tpl = new Template('resources/ajaxcrudlist.example.tpl');
			$tpl->parse();
			$tpl->assign('people', $this->dbConn->query("select id_people, name, sex from people order by name"));
			return $tpl;
		}
	}

	$people = new AjaxPeople;
	$people->run();

?>