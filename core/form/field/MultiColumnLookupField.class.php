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

import('php2go.form.field.DbField');

/**
 * Multi column select input
 *
 * The MultiColumnLookupField is a value selection tool that mimics
 * the behaviour of a regular select input, but displays its options
 * in a table with multiple columns. Options can be loaded from an
 * external data source.
 *
 * @package form
 * @subpackage field
 * @uses Template
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class MultiColumnLookupField extends DbField
{
	/**
	 * Option count
	 *
	 * @var int
	 * @access private
	 */
	var $optionCount = 0;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return MultiColumnLookupField
	 */
	function MultiColumnLookupField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->searchDefaults['OPERATOR'] = 'EQ';
		$this->attributes['ROWSTYLE'] = array(
			'normal' => 'mclookupNormal',
			'selected' => 'mclookupSelected',
			'hover' => 'mclookupHover'
		);
		$this->attributes['TABLESTYLE'] = 'mclookupTable';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$attrs = $this->attributes;
		if (!$attrs['TABLEHEIGHT'])
			$attrs['TABLEHEIGHT'] = 'null';
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'multicolumnlookupfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('name', $this->name);
		$Tpl->assign('label', $this->label);
		$Tpl->assign('value', $this->value);
		$Tpl->assign('attrs', $this->attributes);
		$Tpl->assignByRef('options', $this->_Rs);
		$headers = array();
		$customHeaders = (array)$this->attributes['HEADERS'];
		$colCount = $this->_Rs->fieldCount();
		for ($i=1; $i<$colCount; $i++) {
			$Fld =& $this->_Rs->fetchField($i);
			$headers[] = (isset($customHeaders[$i-1]) ? $customHeaders[$i-1] : $Fld->name);
		}
		$Tpl->assign('headers', $headers);
		$Tpl->display();
	}

	/**
	 * Traverses the options in order to build a human-readable
	 * represention of the component's value
	 *
	 * @return string
	 */
	function getDisplayValue() {
		$display = NULL;
		if (isset($this->value)) {
			while (list($value, $caption) = $this->_Rs->fetchRow()) {
				if ($value == $this->value) {
					$display = $caption;
					break;
				}
			}
			$this->_Rs->moveFirst();
		}
		return $display;
	}

	/**
	 * Define the table headers
	 *
	 * @param string $headers Comma-separated headers
	 */
	function setHeaders($headers) {
		if (!empty($headers))
			$this->attributes['HEADERS'] = explode(',', trim(resolveI18nEntry($headers)));
	}

	/**
	 * Set the width of the text field used to
	 * display the current selected value
	 *
	 * @param int $width Width, in pixels
	 */
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = " style=\"width:{$width}px\"";
		else
			$this->attributes['WIDTH'] = "";
	}

	/**
	 * Set options table width
	 *
	 * @param int $width Width, in pixels
	 */
	function setTableWidth($width) {
		$width = intval($width);
		if ($width > 0)
			$this->attributes['TABLEWIDTH'] = $width;
	}

	/**
	 * Set options table height
	 *
	 * @param int $height Height, in pixels
	 */
	function setTableHeight($height) {
		$height = intval($height);
		if ($height > 0)
			$this->attributes['TABLEHEIGHT'] = $height;
	}

	/**
	 * Set a CSS class to the options table
	 *
	 * @param string $style CSS class name
	 */
	function setTableStyle($style) {
		if (!empty($style))
			$this->attributes['TABLESTYLE'] = $style;
	}

	/**
	 * Set a CSS property of the table rows
	 *
	 * @param string $style CSS class name
	 * @param string $type Property name: normal, selected or hover
	 */
	function setRowStyle($style, $type) {
		if (!empty($style))
			$this->attributes['ROWSTYLE'][$type] = $style;
	}

	/**
	 * Get option count
	 *
	 * @return int
	 */
	function getOptionCount() {
		return $this->optionCount;
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// headers
		$this->setHeaders(@$attrs['HEADERS']);
		// text input width
		$this->setWidth(@$attrs['WIDTH']);
		// table dimensions
		$this->setTableHeight(@$attrs['TABLEHEIGHT']);
		$this->setTableWidth(@$attrs['TABLEWIDTH']);
		// style settings
		$this->setTableStyle(@$attrs['TABLESTYLE']);
		$this->setRowStyle(@$attrs['NORMALROWSTYLE'], 'normal');
		$this->setRowStyle(@$attrs['SELECTEDROWSTYLE'], 'selected');
		$this->setRowStyle(@$attrs['HOVERROWSTYLE'], 'hover');
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		parent::processDbQuery(ADODB_FETCH_NUM);
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/multicolumnlookupfield.js');
		$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'multicolumnlookupfield.css');
	}
}
?>