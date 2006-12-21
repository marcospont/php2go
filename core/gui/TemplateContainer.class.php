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

import('php2go.template.Template');

/**
 * Template container widget
 *
 * A template container is very useful in page templates, once it
 * can be used to define pieces of HTML code that commonly surround
 * other interface elements. Examples: tables that surround lists or
 * datasets, tables that surround forms, ...
 *
 * The template is loaded with the local variables of the scope
 * where the widget was declared.
 *
 * Available attributes:
 * # tpl : template file
 *
 * @package gui
 * @uses Template
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TemplateContainer extends Widget
{
	/**
	 * Generates the widget's interface
	 *
	 * @access protected
	 * @var object Template
	 */
	var $Template = NULL;

	/**
	 * Class constructor
	 *
	 * @param array $attrs Attributes
	 * @return TemplateContainer
	 */
	function TemplateContainer($attrs) {
		parent::Widget($attrs);
		$this->isContainer = TRUE;
		$this->mandatoryAttributes[] = 'tpl';
	}

	/**
	 * Prepares the widget to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->Template = new Template($this->attributes['tpl'], T_BYFILE);
		$this->Template->parse();
		$this->Template->assign($this->attributes['localVars']);
		if (!$this->Template->isVariableDefined('_ROOT.body'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_VARIABLE', array('body', $this->attributes['tpl'], 'body')), E_USER_ERROR, __FILE__, __LINE__);
		$this->Template->assign('_ROOT.body', $this->content);
	}

	/**
	 * Renders the widget's HTML code
	 */
	function render() {
		$this->Template->display();
	}
}
?>