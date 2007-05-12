<?php

	require_once('config/config.php');
	import('php2go.form.FormBasic');
	import('php2go.net.HttpRequest');
	import('subscribe.MyDoc');

	/**
	 * In this script, we build a simple subscribe form. The main goal with this demo is
	 * to show how the XML form descriptor works, and how you can build the most common
	 * types of inputs. In the lines above, we create an instance of MyDoc (extending
	 * the base class Document), build the form and put its content into the "main" slot
	 * of the page
	 */	 
	$doc =& new MyDoc();
	
	if (isset($_GET['action']) && $_GET['action'] == 'subscribe') {
		
		/**
		 * Get the submited values and dump them on the screen
		 */
		$submittedValues = '<PRE>' . exportVariable(HttpRequest::post()) . '</PRE><BR>';
		$backAnchor = HtmlUtils::anchor('index.php', 'Back', 'Back', 'link');
		$doc->elements['main'] = $submittedValues . $backAnchor;
		
	} else {	
	
		/**
		 * Create a new form instance
		 */
		$form =& new FormBasic(XML_PATH . 'subscribe.xml', 'subscribe', $doc);
	
		/**
		 * Define the form method (the default method is POST)
		 */
		$form->setFormMethod('POST');
		
		/**
		 * Define the form action (the default is $PHP_SELF or HttpRequest::basePath())
		 */
		$form->setFormAction(HttpRequest::basePath() . '?action=subscribe');
		
		/**
		 * Configure some CSS styles
		 */
		$form->setInputStyle('input');
		$form->setLabelStyle('label');
		$form->setButtonStyle('button');
		
		/**
		 * Do some customization in the form table
		 */
		$form->setFormTableProperties(2, 1);
		$form->setFormWidth(530);
		$form->setLabelAlign('left');
		
		/**
		 * Generate and copy the form content to the main slot of the page
		 */
		$doc->elements['main'] = $form->getContent();
		
		/**
		 * Define the field to focus when the page is loaded (parameters: form name and field name)
		 */
		$doc->setFocus('subscribe', 'username');
	
	}
	
	/**
	 * Output the final HTML code
	 */
	$doc->display();

?>