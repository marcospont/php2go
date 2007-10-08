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
 * Data table widget
 *
 * This widget has the ability to add dynamic behaviours to a regular HTML table,
 * such as sorting data, applying alternate row styles, highlighting rows and
 * allowing single or multiple row selection.
 *
 * Available attributes:
 * # id : widget ID
 * # sortable : whether the table must be sortable
 * # sortTypes : array or comma separated list of sort types
 * # descending : whether to use descending the default sort order
 * # orderAscIcon : ascending order icon
 * # orderDescIcon : descending order icon
 * # selectable : whether to make the table rows selectable
 * # multiple : enable multiple selection or not
 * # scrollable : whether to add vertical scroll bar when needed
 * # maxHeight : allows to determine a maximum height for the table body
 * # headerClass : CSS class for the table header
 * # rowClass : CSS class for the table rows
 * # alternateRowClass : CSS class for alternate (odd) rows
 * # hightlightClass : CSS class to add in the row the mouse is over
 * # selectedClass : CSS class to highlight selected rows
 *
 * Available sort types:
 * # NUMBER
 * # DATE
 * # DATETIME
 * # CURRENCY
 * # STRING
 * # ISTRING
 * # NONE
 *
 * Available client events:
 * # onInit
 * # onBeforeSort
 * # onSort
 * # onChangeSelection
 *
 * @package gui
 * @subpackage widget
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DataTable extends Widget
{
	/**
	 * Available sort types
	 *
	 * @var array
	 * @access private
	 */
	var $availableSortTypes = array(
			'NUMBER',
			'DATE',
			'CURRENCY',
			'STRING',
			'ISTRING',
			'NONE'
	);

	/**
	 * Widget constructor
	 *
	 * @param array $attrs Attributes
	 * @return DataTable
	 */
	function DataTable($attrs) {
		parent::Widget($attrs);
		$this->isContainer = TRUE;
	}

	/**
	 * Loads the resources needed by the
	 * widget onto the active DocumentHead
	 *
	 * @param DocumentHead &$Head Document head
	 * @static
	 */
	function loadResources(&$Head) {
		$Head->addStyle(PHP2GO_CSS_PATH . 'datatable.css');
	}

	/**
	 * Returns the default values for
	 * the widget's attributes
	 *
	 * @return array Default attributes
	 */
	function getDefaultAttributes() {
		return array(
			'id' => PHP2Go::generateUniqueId(parent::getClassName()),
			'sortable' => FALSE,
			'sortTypes' => array(),
			'descending' => FALSE,
			'orderAscIcon' => PHP2GO_ICON_PATH . 'order_asc.gif',
			'orderDescIcon' => PHP2GO_ICON_PATH . 'order_desc.gif',
			'selectable' => FALSE,
			'multiple' => FALSE,
			'scrollable' => FALSE,
			'headerClass' => 'dataTableHeader',
			'rowClass' => '',
			'alternateRowClass' => '',
			'highlightClass' => '',
			'selectedClass' => 'dataTableSelected'
		);
	}

	/**
	 * Applies the necessary transformation on
	 * attributes before loading them
	 *
	 * @param array $attrs Attributes
	 */
	function loadAttributes($attrs) {
		if (is_string($attrs['sortTypes']))
			$attrs['sortTypes'] = explode(',', $attrs['sortTypes']);
		array_walk($attrs['sortTypes'], 'trim');
		for ($i=0; $i<sizeof($attrs['sortTypes']); $i++) {
			if (empty($attrs['sortTypes'][$i]))
				$attrs['sortTypes'][$i] = '';
			if (!in_array($attrs['sortTypes'][$i], $this->availableSortTypes))
				$attrs['sortTypes'][$i] = 'STRING';
		}
		parent::loadAttributes($attrs);
	}

	/**
	 * Renders the DataTable widget
	 */
	function render() {
		$attrs =& $this->attributes;
		$code = sprintf("<div id=\"%s\" class=\"dataTable\">\n", $attrs['id']);
		$code .= $this->content;
		$code .= "</div>\n";
		print $code;
		parent::renderJS(array(
			'id' => $attrs['id'],
			'sortable' => $attrs['sortable'],
			'sortTypes' => $attrs['sortTypes'],
			'descending' => $attrs['descending'],
			'orderAscIcon' => $attrs['orderAscIcon'],
			'orderDescIcon' => $attrs['orderDescIcon'],
			'selectable' => $attrs['selectable'],
			'multiple' => $attrs['multiple'],
			'scrollable' => $attrs['scrollable'],
			'maxHeight' => @$attrs['maxHeight'],
			'headerClass' => $attrs['headerClass'],
			'rowClass' => $attrs['rowClass'],
			'alternateRowClass' => $attrs['alternateRowClass'],
			'highlightClass' => $attrs['highlightClass'],
			'selectedClass' => $attrs['selectedClass']
		));
	}
}
?>