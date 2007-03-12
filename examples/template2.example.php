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
	import('php2go.auth.User');
	import('php2go.base.Document');
	import('php2go.session.SessionObject');
	import('php2go.template.Template');

	/**
	 * register some variables, so that some features
	 * can be shown in this template example
	 */
	$_GET['param'] = 'GET parameter sample';
	Registry::set('global', 'Global variable');
	$session = new SessionObject('sample');
	if (!$session->isRegistered()) {
		$session->createProperty('property', 'value');
		$session->register();
	}
	$user =& User::getInstance();
	if (!$user->isAuthenticated()) {
		$user->authenticate('guest', array('name' => 'Guest User'));
	}

	/**
	 * Container function that will be called from inside the template.
	 * Each container function is called twice: in START/END definitions.
	 * In the first call, the $output argument is NULL; in the second,
	 * it contains the container contents
	 */
	function parseEntities($args, $output, &$tpl) {
		if ($output != NULL)
			return htmlentities($output);
	}

	/**
	 * Class created to demonstrate how the template engine
	 * deals with object properties and object methods
	 */
	class TplObj
	{
		var $bool = TRUE;
		var $numeric = array();
		var $associative = array();
		var $object = NULL;
		var $rs = NULL;
		var $total = 0;

		function TplObj() {
			$this->numeric = array('apple', 'orange', 'lemon');
			$this->associative = array('foo' => 'This is Foo', 'bar' => 'This is bar');
			$this->object = new stdClass();
			$this->object->string = "string";
			$db =& Db::getInstance();
			$db->setFetchMode(ADODB_FETCH_ASSOC);
			/**
			 * This member will be used to perform a loop inside the template.
			 * Template loops can iterate over arrays, data sets or ADODb recordsets
			 */
			$this->rs =& $db->query("select * from products where active = 1");
		}

		function sum($line) {
			$this->total += (float)$line['price'];
		}
	}

	/**
	 * document creation, using a layout with a single slot {main}
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->setTitle('PHP2Go Examples - php2go.template.Template');
	$doc->addStyle('resources/css.example.css');

	/**
	 * creation and parsing of the template
	 */
	$tpl = new Template('resources/template2.example.tpl');
	$tpl->parse();

	$tpl->assign('items', array(
		array('link'=>'/test', 'caption'=>'Test', 'description'=>'Test Page'),
		array('link'=>'/about', 'caption'=>'About', 'description'=>'About Us'),
		array('link'=>'/help', 'caption'=>'Help', 'description'=>'Help & Manuals')
	));

	/**
	 * assign variables
	 */
	$tpl->assign('simpleVar', 'this is a simple var');
	$tpl->assign('numericKey', 1);
	$tpl->assign('numeric', array('foo', 'bar', 'baz'));
	$tpl->assign('associativeKey', 'REMOTE_ADDR');
	$tpl->assign('associative', $_SERVER);
	$tpl->assign('ndimension', array(
		'firstname' => 'Foo',
		'lastname' => 'Bar',
		'address' => array(
			'street' => 'Garden St.',
			'number' => '128',
			'city' => 'Gothan City',
			'country' => 'United States'
		)
	));
	$tpl->assign('object', new TplObj());
	$tpl->assign('ifVariable', rand(1, 10));

	/**
	 * assign the template in the document and display it
	 */
	$doc->assign('main', $tpl);
	$doc->display();

?>