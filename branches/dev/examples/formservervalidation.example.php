<?php

	// $Header: /www/cvsroot/php2go/examples/formservervalidation.example.php,v 1.9 2006/11/03 04:01:25 mpont Exp $
	// $Revision: 1.9 $
	// $Date: 2006/11/03 04:01:25 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.form.FormBasic');
	import('php2go.form.FormTemplate');
	import('php2go.net.HttpResponse');

	/**
	 * create and configure an instance of the class document, where the form will be included
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->addStyle('resources/css.example.css');
	$doc->setTitle('PHP2Go - Form validated on server');
	$doc->setFocus('serverform');

	/**
	 * create the form
	 */
	if ($_GET['frm'] == 'FormTemplate') {
		$form = new FormTemplate('resources/formservervalidation.example.xml', 'resources/formservervalidation.example.tpl', 'serverform', $doc);
	} else {
		$form = new FormBasic('resources/formservervalidation.example.xml', 'serverform', $doc);
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