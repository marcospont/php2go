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
	import('php2go.form.FormBasic');

	$doc = new Document('resources/layout.example.tpl');
	$doc->addScript('resources/javascript.example.js');
	$doc->addStyle('resources/css.example.css');
	$doc->addBodyCfg(array('bgcolor'=>'#ffffff', 'style'=>'margin:0'));
	$doc->setTitle('PHP2Go Example : Form Components');

	/**
	 * This example was created to demonstrate special components provided by the
	 * php2go.form.field package. Here you can check how these components can be defined,
	 * how they are validated and how their values are returned by getSubmittedValues()
	 */
	$part = HttpRequest::get('subset');
	if (file_exists("resources/formcomponents{$part}.example.xml"))
		$xml = "resources/formcomponents{$part}.example.xml";
	else
		$xml = "resources/formcomponents.example.xml";
	$form = new FormBasic($xml, 'form', $doc);
	$form->setFormAction(HttpRequest::uri(FALSE));
	$form->setFormWidth(600);
	$form->setLabelWidth(0.25);
	$form->setInputStyle('input_style');
	$form->setLabelStyle('label_style');
	$form->setButtonStyle('button_style');
	$form->setFormTableProperties(12, 1);
	$form->setBackUrl($_SERVER['REQUEST_URI']);

	if ($form->isPosted()) {
		if ($form->isValid()) {
			$doc->assign('main',
				"<br><div align='center'><fieldset style='width:576px;text-align:left;padding:8px'><legend class='label_style'>Submitted Values</legend>" .
				"<button id=\"back\" onClick=\"location.href='" . $_SERVER['PHP_SELF'] . "'\" class='button_style'>Back</button>" .
				exportVariable($form->getSubmittedValues(), TRUE) .
				"</fieldset></div>"
			);
		} else {
			$doc->assignByRef('main', $form);
		}
	} else {
		$doc->assignByRef('main', $form);
	}

	$doc->display();

?>