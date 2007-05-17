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
	import('php2go.form.FormBasic');
	import('php2go.form.FormTemplate');
	import('php2go.net.HttpResponse');

	/**
	 * create and configure an instance of the class document, where the form will be included
	 */
	$doc = new Document('../common/basicwithtable.tpl');
	$doc->addStyle('../common/examples.css');
	$doc->setTitle('PHP2Go - Form validated on server');
	$doc->setFocus('serverform');

	/**
	 * create the form
	 */
	if ($_GET['frm'] == 'FormTemplate') {
		$form = new FormTemplate('form.xml', 'form.tpl', 'serverform', $doc);
	} else {
		$form = new FormBasic('form.xml', 'serverform', $doc);
	}

	/**
	 * the isPosted() method verifies if the request contains a variable called __form_signature which
	 * value is the same signature of the FormBasic instance
	 */
	if ($form->isPosted()) {
		/**
		 * the isValid() method executes the chain of validation of the form;
		 * each field has a set of validators according to its configuration;
		 */
		if ($form->isValid()) {
			HttpResponse::redirect(new Url(HttpRequest::uri()));
		}
	}

	/**
	 * create a DocumentElement instance to render the main content of the page
	 */
	$main = new DocumentElement();
	$frm = ($_GET['frm'] == 'FormTemplate' ? 'FormBasic' : 'FormTemplate');
	$main->put("
		<div class=\"sample_simple_text\">
		<b>PHP2Go Examples</b> : php2go.form.FormBasic with server validation<br/>
		<a href=\"?frm={$frm}\" class=\"sample_simple_text\">See the same example with php2go.form.{$frm}</a><br/><br/>
		<b>IMPORTANT:</b><br/>Disable JavaScript in your browser to see the server validation in action.<br/><br/>
		</div>
	");
	$main->put($form->getContent());
	$main->parse();
	$doc->assign('main', $main);

	/**
	 * display the HTML document
	 */
	$doc->display();

	/**
	 * customized function used to execute the copy/move operation on the uploaded file
	 * >> this function is defined in the form XML file (attribute SAVEFUNCTION in the FILEFIELD entity)
	 * >> the FileUpload class calls this function after checking the file integrity
	 * >> the function receives as parameter an array containing information about the upload handler
	 */
	function uploadHandler($fileData) {
		$name = $fileData['save_name'];
		// apply some transformations in the file name
		$name = trim(strtolower($name));
		$name = StringUtils::filter($name, 'blank', '_');
		$name = date('Ymd') . '_' . $name;
		$name = StringUtils::truncate($name, 32, '');
		// move the file manually from the temp directory
		if (@move_uploaded_file($fileData['tmp_name'], $fileData['save_path'] . $name)) {
			$fileData['save_name'] = $name;
			@chmod($fileData['save_path'] . $fileData['save_name'], $fileData['save_mode']);
		} else {
			$fileData['error'] = "It wasn't possible to move the uploaded file {$fileData['save_name']}";
		}
		// FileUpload class expects this function to *always* return back the modified array
		return $fileData;
	}

?>