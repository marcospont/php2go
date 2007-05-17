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

	/**
	 * create and configure an instance of the class document, where the form will be included
	 */
	$doc = new Document('../common/basicwithtable.tpl');
	$doc->setCache(FALSE);
	$doc->addScript('../common/examples.js');
	$doc->addStyle('../common/examples.css');
	$doc->addBodyCfg(array('bgcolor'=>'#ffffff', 'style'=>'margin:0'));
	$doc->setTitle('PHP2Go Example : php2go.form.FormBasic');
	$doc->setFocus('myForm');

	/**
	 * in the initialization of each field, the framework search the value of the field in the superglobal arrays,
	 * in the cookies, in the global scope and in the session objects. therefore, if you add a variable in the request
	 * with the same name of a form field, the form API will catch that value and assign it to the form field
	 *
	 * the Registry class static methods can be used to set the value of a form field in the global scope
	 * Registry is a global variable repository containing key=>value pairs that is initialized with the content of the $GLOBALS array
	 * >>> attention: to use Registry as a simple way of defining form values, execute the operation below before instantiating a Form class
	 */
	Registry::set('memo_field', 'Type your message here');

	/**
	 * instantiate FormBasic and define some properties
	 * this form component has its own HTML table renderer, so the user don't need to build a template
	 */
	$form = new FormBasic('form.xml', 'myForm', $doc);
	$form->setFormMethod('POST');
	$form->setFormWidth(550);
	$form->setLabelWidth(0.2);
	//$form->setFormAction('target.php'); The default value points to the current script
	/**
	 * this style will be used in all form inputs (excluding buttons)
	 * to use a different style in one or more fields, use the STYLE attribute in the XML specification
	 */
	$form->setInputStyle('input_style');
	/**
	 * this style will be used in all form labels
	 */
	$form->setLabelStyle('label_style');
	/**
	 * the style to be used in buttons (only when they don't use images)
	 */
	$form->setButtonStyle('button_style');
	/**
	 * configure the css style of the error messages
	 * the second parameter is the list mode: FORM_ERROR_FLOW (messages separated by a line break) or
	 * FORM_ERROR_BULLET_LIST (renders a bullet list)
	 */
	$form->setErrorStyle('error_style', FORM_ERROR_BULLET_LIST, 'Some error(s) occurred while processing the form:', 'error_header');
	/**
	 * remove the comment of the following line to see the errors displayed in a
	 * DIV container using the style defined above
	 */
	$form->setErrorDisplayOptions(FORM_CLIENT_ERROR_DHTML);
	/**
	 * enable highlight of fields access keys
	 */
	$form->setAccessKeyHighlight(TRUE);

	if ($form->isPosted()) {
		/**
		 * here, we know that the form was posted
		 */
		if ($form->isValid()) {
			/**
			 * here, we know that the form passed through all the validation rules
			 * any code related with persistence or data processing must be put here
			 */
		}
	} else {
		/**
		 * you can set the value of a field using the getField method
		 * the method receives as parameter the path of the field in the XML tree
		 * e.g.: if you have a field called combo_field in the section mysection, the value of the parameter would be mysection.combo_field
		 * >>> attention : in PHP 4, always use the reference operator & when using the getField method
		 */
		if ($combo =& $form->getField('section.combo_field'))
			$combo->setValue('M');
	}

	/**
	 * attach the form content to the document
	 */
	$doc->assignByRef('main', $form);

	$doc->display();

	/**
	 * this function is used to evaluate the visibility of a conditional section included in the form
	 */
	function evaluateSection($section) {
		if ($section->getId() == 'condsection') {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * this function is called to define the value of a TEXTFIELD in the form
	 */
	function testFunction() {
		return 'PHP ' . PHP_VERSION;
	}

?>