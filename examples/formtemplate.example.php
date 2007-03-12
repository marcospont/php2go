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
	import('php2go.form.FormTemplate');

	/**
	 * create and configure an instance of the class document, where the form will be included
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->addScript('resources/javascript.example.js');
	$doc->addStyle('resources/css.example.css');
	$doc->addBodyCfg(array('topmargin'=>0, 'leftmargin'=>0));
	$doc->setTitle('PHP2Go Example : php2go.form.FormTemplate');
	$doc->setFocus('myForm');

	/**
	 * in the initialization of each field, the framework search the value of the field in the superglobal arrays,
	 * in the cookies, in the global scope (Registry) and in the session objects. therefore, if you add a variable in the request
	 * with the same name of a form field, the form API will catch that value and assign it to the form field
	 *
	 * the Registry class have static methods can be used to set the value of a form field in the global scope
	 * Registry is a global variable repository containing key=>value pairs that is initialized with the content of the $GLOBALS array
	 * >>> attention: to use Registry as a simple way of defining form values, execute the operation below before instantiating a Form child class
	 */
	Registry::set('memo_field', 'Type your message here');

	/**
	 * create an instance of FormTemplate
	 * this form component is the most flexible, because the user can define a totally customizable template
	 * a) each field must have a place holder - e.g. {fieldname}
	 * b) each field label also is defined as a place holder - e.g. {label_fieldname}
	 * c) each section name may also be a variable in the template - e.g. {section_mysection}
	 * the framework will replace all these place holders with the proper values
	 * p.s.: the fields/labels place holders of the outermost sections must not be declared inside template blocks
	 */
	$form = new FormTemplate('resources/form.example.xml', 'resources/formtemplate.example.tpl', 'myForm', $doc);
	$form->setFormMethod('POST');
	$form->setInputStyle('input_style');
	$form->setLabelStyle('label_style');
	$form->setButtonStyle('button_style');
	$form->setErrorStyle('error_style', FORM_ERROR_BULLET_LIST, 'One or more errors occurred:', 'error_header');
	$form->setErrorDisplayOptions('error', FORM_CLIENT_ERROR_DHTML, 'form_client_errors');
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

	/**
	 * display the HTML document
	 */
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