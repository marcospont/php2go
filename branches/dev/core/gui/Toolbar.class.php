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

import('php2go.template.Template');

/**
 * Each toolbar item is an icon
 */
define('TOOLBAR_MODE_ICONS', 1);
/**
 * Each toolbar item is a button
 */
define('TOOLBAR_MODE_BUTTONS', 2);
/**
 * Each toolbar item is a link
 */
define('TOOLBAR_MODE_LINKS', 3);

/**
 * Generates a table (horizontal or vertical) of action links
 *
 * Available attributes:
 * # id : toolbar ID
 * # mode : toolbar mode ({@link TOOLBAR_MODE_ICONS}, {@link TOOLBAR_MODE_BUTTONS} or {@link TOOLBAR_MODE_LINKS})
 * # align : toolbar align
 * # class : toolbar CSS class
 * # horizontal : TRUE or FALSE (vertical)
 * # width : toolbar width
 * # items : toolbar items
 * # itemClass : CSS class for toolbar items
 * # itemHeight : height for the toolbar items
 * # descriptionAlign : align of the layer used to show the item's description when the mouse is over it
 * # descriptionClass : CSS class for the description layer
 * # activeIndex : index of the active toolbar item (zero based)
 * # activeClass : CSS class for the active toolbar item
 *
 * @package gui
 * @uses Template
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Toolbar extends Widget
{
	/**
	 * Generates the toolbar code
	 *
	 * @access protected
	 * @var object Template
	 */
	var $Template = NULL;

	/**
	 * Class constructor
	 *
	 * @param array $attrs Attributes
	 * @return Toolbar
	 */
	function Toolbar($attrs) {
		parent::Widget($attrs);
		$this->isContainer = FALSE;
		$this->mandatoryAttributes[] = 'items';
	}

	/**
	 * Merges user defined attributes with default ones
	 *
	 * @param array $attrs Toolbar attributes
	 */
	function loadAttributes($attrs) {
		$defaults = array(
			'id' => PHP2Go::generateUniqueId(parent::getClassName()),
			'mode' => TOOLBAR_MODE_LINKS,
			'align' => 'center',
			'horizontal' => TRUE,
			'itemHeight' => '20px',
			'descriptionAlign' => 'center',
			'activeIndex' => NULL
		);
		$attrs = array_merge($defaults, $attrs);
		if (TypeUtils::isInteger($attrs['width']))
			$attrs['width'] .= 'px';
		if (TypeUtils::isInteger($attrs['itemHeight']))
			$attrs['itemHeight'] .= 'px';
		parent::loadAttributes($attrs);
	}

	/**
	 * Creates and configures the toolbar template
	 */
	function onPreRender() {
		$this->Template = new Template(PHP2GO_TEMPLATE_PATH . 'toolbar.tpl');
		$this->Template->parse();
		$this->Template->assign('id', $this->attributes['id']);
		$this->Template->assign('mode', $this->attributes['mode']);
		$this->Template->assign('align', $this->attributes['align']);
		$this->Template->assign('horizontal', $this->attributes['horizontal']);
		if (isset($this->attributes['class']))
			$this->Template->assign('class', " class=\"{$this->attributes['class']}\"");
		if (isset($this->attributes['width']))
			$this->Template->assign('width', $this->attributes['width']);
		$this->Template->assign('items', $this->attributes['items']);
		$this->Template->assign('itemHeight', $this->attributes['itemHeight']);
		if (isset($this->attributes['itemClass']))
			$this->Template->assign('itemClass', " class=\"{$this->attributes['itemClass']}\"");
		$this->Template->assign('descriptionAlign', $this->attributes['descriptionAlign']);
		if (isset($this->attributes['descriptionClass']))
			$this->Template->assign('descriptionClass', " class=\"{$this->attributes['descriptionClass']}\"");
		$this->Template->assign('activeIndex', $this->attributes['activeIndex']);
		if (isset($this->attributes['activeClass']))
			$this->Template->assign('activeClass', " class=\"{$this->attributes['activeClass']}\"");
	}

	/**
	 * Renders the toolbar
	 */
	function render() {
		$this->Template->display();
	}
}
?>