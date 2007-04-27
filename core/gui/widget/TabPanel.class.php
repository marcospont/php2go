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

/**
 * Tab panel widget
 *
 * This widget represents one of the tabbed views of
 * a {@link TabView} widget. The tab is loaded with
 * the contents inside the widget declaration in the
 * template.
 *
 * Available attributes:
 * # id : tab ID
 * # caption : tab caption (mandatory)
 * # disabled : whether the tab is disabled or enabled
 * # labelClass : CSS class for the tab label
 * # contentClass : CSS class for the tab content element
 * # loadUri : URI to load tab contents from
 * # loadMethod : load method (get or post)
 * # loadParams : load parameters
 *
 * @package gui
 * @subpackage widget
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TabPanel extends Widget
{
	/**
	 * Widget constructor
	 *
	 * @param array $attrs Attributes
	 * @return TabPanel
	 */
	function TabPanel($attrs) {
		parent::Widget($attrs);
		$this->isContainer = TRUE;
		$this->hasOutput = FALSE;
		$this->mandatoryAttributes[] = 'caption';
	}

	/**
	 * Returns the default values for
	 * the widget's attributes
	 *
	 * @return array Default attributes
	 */
	function getDefaultAttributes() {
		$id = PHP2Go::generateUniqueId(parent::getClassName());
		return array(
			'id' => $id,
			'disabled' => FALSE,
			'labelClass' => '',
			'contentClass' => '',
			'loadUri' => '',
			'loadMethod' => 'get',
			'loadParams' => NULL
		);
	}

	/**
	 * Registers the tab panel in the parent {@link TabView}
	 */
	function onPreRender() {
		if (!TypeUtils::isInstanceOf($this->Parent, 'TabView'))
			PHP2Go::raiseError(sprintf("The %s widget must be declared inside a %s widget!", "TabPanel", "TabView"), E_USER_ERROR, __FILE__, __LINE__);
		$this->Parent->addPanel($this);
	}
}
?>