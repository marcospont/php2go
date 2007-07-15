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
	import('php2go.db.DMLBuilder');
	import('php2go.datetime.Date');
	import('php2go.data.Report');
	import('php2go.form.FormTemplate');
	import('php2go.net.HttpRequest');
	import('php2go.net.HttpResponse');

	/**
	 * Validate involved tables
	 */
	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (!in_array('projects', $tables)) {
		PHP2Go::raiseError("The <i>projects</i> table was not found! Please run <i>projects.sql</i>, located at the <i>ROOT/examples/sql</i> folder.<br />P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}
	if (!in_array('projects_people', $tables)) {
		PHP2Go::raiseError("The <i>projects_people</i> table was not found! Please run <i>projects_people.sql</i>, located at the <i>ROOT/examples/sql</i> folder.<br />P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}
	if (!in_array('people', $tables)) {
		PHP2Go::raiseError("The <i>people</i> table was not found! Please run <i>people.sql</i>, located at the <i>ROOT/examples/sql</i> folder.<br />P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}
	if (!in_array('tasks', $tables)) {
		PHP2Go::raiseError("The <i>tasks</i> table was not found! Please run <i>tasks.sql</i>, located at the <i>ROOT/examples/sql</i> folder.<br />P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Determine the requested action and call the proper handler
	 */
	$ajax = (HttpRequest::isAjax() && HttpRequest::isPost());
	$action = ($ajax ? HttpRequest::post('action') : HttpRequest::get('action'));
	if ($ajax) {
		switch ($action) {
			case 'add_task' :
			case 'delete_tasks' :
				ajaxAction($action, HttpRequest::post());
				break;
			default :
				HttpResponse::setStatus(HTTP_STATUS_NOT_FOUND);
				print "Invalid action!";
				break;
		}
	} else {
		$doc = new Document('../common/basicwithtable.tpl');
		$doc->setCache(FALSE);
		$doc->addBodyCfg(array('style'=>'background-color:#fff;margin:0px'));
		$doc->addStyle('../common/examples.css');
		$doc->setTitle('PHP2Go Examples - CRUD with Form Components');
		switch ($action) {
			case 'create' :
			case 'update' :
				formAction($doc, $action);
				break;
			case 'delete' :
				deleteAction($doc);
				break;
			default :
				listAction($doc);
				break;
		}
		$doc->display();
	}

	/**
	 * Builds the list of projects
	 */
	function listAction(&$doc, $msg='') {
		$report = new Report('list.xml', 'list.tpl', $doc);
		$report->setLineHandler('lineHandler');
		$report->Template->assign('message', $msg);
		$doc->assignByRef('main', $report);
	}

	/**
	 * Transforms each line of the projects list
	 */
	function lineHandler($line) {
		$line['start_date'] = Date::fromSqlDate($line['start_date']);
		$line['end_date'] = Date::fromSqlDate($line['end_date']);
		$actions = array(
			HtmlUtils::anchor(HttpRequest::basePath() . '?action=update&id_project=' . $line['id_project'], 'Edit', 'Edit', 'input_style'),
			HtmlUtils::anchor(HttpRequest::basePath() . '?action=delete&id_project=' . $line['id_project'], 'Delete', 'Delete', 'input_style', array('onclick' => 'return confirm("Are you sure?")'))
		);
		$line['id_project'] = join('&nbsp;', $actions);
		return $line;
	}

	/**
	 * Handles "create" and "update" actions
	 */
	function formAction(&$doc, $action) {
		$db =& Db::getInstance();
		if ($action == 'update') {
			/**
			 * check if the project id is valid
			 * and exists in the database
			 */
			$id = HttpRequest::get('id_project');
			if (!TypeUtils::isInteger($id)) {
				listAction($doc);
				return;
			}
			$result = $db->toGlobals("
				select
					name, id_manager,
					date_format(start_date, '%d/%m/%Y') start_date,
					date_format(end_date, '%d/%m/%Y') end_date
				from
					projects
				where
					id_project = {$id}
			", FALSE, TRUE);
			if ($result === FALSE) {
				listAction($doc);
				return;
			}
		}
		/**
		 * Create the form and check if it's posted and valid
		 */
		$form = new FormTemplate('form.xml', 'form.tpl', 'project', $doc);
		$form->Template->assign('action', $action);
		if ($form->isPosted()) {
			if ($form->isValid()) {
				/**
				 * Get submitted values
				 */
				$values = $form->getSubmittedValues();
				/**
				 * Create and configure the DML builder
				 */
				$error = FALSE;
				$dml = new DMLBuilder($db);
				$dml->ignoreEmptyValues = TRUE;
				if ($action == 'create') {
					/**
					 * Prepare and run the insert statement
					 */
					$dml->prepare(DML_BUILDER_INSERT, 'projects', $values);
					if ($dml->execute()) {
						$id = $db->lastInsertId();
						/**
						 * Process added members
						 */
						$members = $values['members']['added_members'];
						$stmt = $db->prepare("insert into projects_people values (?, ?)");
						foreach ($members as $member) {
							if (!@$db->execute($stmt, array($id, $member))) {
								$error = $db->getError();
								break;
							}
						}
						/**
						 * Redirect in case of success
						 */
						if (!$error) {
							HttpResponse::redirect(new Url(HttpRequest::basePath() . '?action=list'));
							exit;
						}
					} else {
						$error = $db->getError();
					}
				} else {
					/**
					 * Prepare and run the update statement
					 */
					$dml->prepare(DML_BUILDER_UPDATE, 'projects', $values, 'id_project = ?', array($id));
					if ($dml->execute()) {
						$error = FALSE;
						/**
						 * Process removed members
						 */
						$removedMembers = $values['members']['removed_members'];
						$removedStmt = $db->prepare("delete from projects_people where id_project = ? and id_people = ?");
						foreach ($removedMembers as $member) {
							/**
							 * A member that owns tasks in this project can't be removed
							 */
							$count = $db->getFirstCell("select count(*) from tasks where id_project = ? and id_owner = ?", array($id, $member));
							if ($count > 0) {
								$error = "This member can't be removed.";
								break;
							}
							/**
							 * Remove the member
							 */
							elseif (!@$db->execute($removedStmt, array($id, $member))) {
								$error = $db->getError();
								break;
							}
						}
						/**
						 * Process added members
						 */
						$addedMembers = $values['members']['added_members'];
						$addedStmt = $db->prepare("insert into projects_people values (?, ?)");
						if (!$error) {
							foreach ($addedMembers as $member) {
								if (!@$db->execute($addedStmt, array($id, $member))) {
									$error = $db->getError();
									break;
								}
							}
						}
						/**
						 * Process task updates
						 */
						if (!$error) {
							$tasks = $values['tasks'];
							foreach ($tasks as $id => $task) {
								$dml->prepare(DML_BUILDER_UPDATE, 'tasks', $task, 'id_task = ?', array($id));
								if (!@$dml->execute()) {
									$error = $db->getError();
									break;
								}
							}
						}
						/**
						 * Redirect in case of success
						 */
						if (!$error) {
							HttpResponse::redirect(new Url(HttpRequest::basePath() . '?action=list'));
						}
					} else {
						$error = $db->getError();
					}
				}
				/**
				 * Register database errors on the form
				 */
				$form->addErrors($error);
			}
		}
		$doc->setFocus('project');
		$doc->assignByRef('main', $form);
	}

	/**
	 * Evaluates the visibility of the
	 * "details" conditional section
	 */
	function evalCondSection(&$section) {
		return (HttpRequest::get('action') == 'update');
	}

	/**
	 * Handles the delete action
	 */
	function deleteAction(&$doc) {
		$db =& Db::getInstance();
		$id = HttpRequest::get('id_project');
		if (!TypeUtils::isInteger($id)) {
			listAction($doc, "Invalid project ID!");
			return;
		}
		$count = $db->getFirstCell("select count(*) from projects where id_project = ?", array($id));
		if (!$count) {
			listAction($doc, "Invalid project ID!");
			return;
		}
		$db->delete('tasks', 'id_project = ?', array($id));
		$db->delete('projects_people', 'id_project = ?', array($id));
		$db->delete('projects', 'id_project = ?', array($id));
		HttpResponse::redirect(new Url(HttpRequest::basePath() . '?action=list'));
	}

	/**
	 * Handles the AJAX actions
	 * # add task
	 * # delete tasks
	 */
	function ajaxAction($action, $request) {
		$db =& Db::getInstance();
		if ($action == 'add_task') {
			if (trim($request['task_name'] == '') ||
				trim($request['task_description'] == '') ||
				empty($request['task_id_owner']) ||
				trim($request['task_start_date']) == '' ||
				!Date::isValid($request['task_start_date']) ||
				trim($request['task_end_date']) == '' ||
				!Date::isValid($request['task_end_date'])
			) {
				HttpResponse::setStatus(HTTP_STATUS_SERVER_ERROR);
				print "The task contains one or more invalid fields.";
			} else {
				$dml = new DMLBuilder($db);
				$dml->prepare(DML_BUILDER_INSERT, 'tasks', array(
					'id_project' => $request['id_project'],
					'name' => $request['task_name'],
					'description' => $request['task_description'],
					'id_owner' => $request['task_id_owner'],
					'status' => $request['task_status'],
					'priority' => $request['task_priority'],
					'start_date' => $request['task_start_date'],
					'end_date' => $request['task_end_date']
				));
				$result = @$dml->execute();
				if (!$result) {
					HttpResponse::setStatus(HTTP_STATUS_SERVER_ERROR);
					print $db->getError();
				} else {
					print "Task created!";
				}
			}
		} else {
			$deleteCount = 0;
			$stmt = $db->prepare("delete from tasks where id_task = ?");
			foreach ((array)$request['tasks'] as $id => $task) {
				if (isset($task['checked'])) {
					$result = @$db->execute($stmt, array($id));
					if (!$result) {
						HttpResponse::setStatus(HTTP_STATUS_SERVER_ERROR);
						print $db->getError();
						exit;
					}
					$deleteCount++;
				}
			}
			if (!$deleteCount) {
				HttpResponse::setStatus(HTTP_STATUS_SERVER_ERROR);
				print "Select one or more tasks!";
			} else {
				print "Task(s) deleted!";
			}
		}
	}

?>