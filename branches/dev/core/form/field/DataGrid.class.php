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
 * Builds a grid of fields based on database records
 *
 * Based on the data source and on a set of fields defined in the
 * XML specification, this class builds a grid of fields. The grid
 * fieldset supports a subset of the available form components.
 *
 * @package form
 * @subpackage field
 * @uses Template
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DataGrid extends DbField
{
	/**
	 * Grid field names
	 *
	 * @var array
	 * @access private
	 */
	var $fieldNames = array();

	/**
	 * Grid fields
	 *
	 * @var array
	 * @access private
	 */
	var $fieldSet = array();

	/**
	 * Grid cell sizes
	 *
	 * @var array
	 * @access private
	 */
	var $cellSizes = array();

	/**
	 * Used to render the grid's HTML ouput
	 *
	 * @var object Template
	 * @access private
	 */
	var $Template = NULL;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return DataGrid
	 */
	function DataGrid(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->composite = TRUE;
		$this->searchable = FALSE;
	}

	/**
	 * Component's destructor
	 */
	function __destruct() {
		parent::__destruct();
		unset($this->fieldset);
		unset($this->Template);
	}

	/**
	 * Displays the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$this->Template->display();
	}

	/**
	 * Enable/disable visibility of grid headers
	 *
	 * By default, grid headers are visible.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setShowHeader($setting) {
		$this->attributes['SHOWHEADER'] = (bool)$setting;
	}

	/**
	 * Define labels to the grid headers
	 *
	 * @param string $headers Comma-separated header labels
	 */
	function setHeaders($headers) {
		if ($headers) {
			$headers = (!is_array($headers) ? explode(',', resolveI18nEntry(trim($headers))) : $headers);
			$this->attributes['HEADERS'] = $headers;
		}
	}

	/**
	 * Set grid headers CSS class
	 *
	 * @param string $headerStyle CSS class
	 */
	function setHeaderStyle($headerStyle) {
		if ($headerStyle)
			$this->attributes['HEADERSTYLE'] = " class=\"$headerStyle\"";
		else
			$this->attributes['HEADERSTYLE'] = $this->_Form->getLabelStyle();
	}

	/**
	 * Set grid cells CSS class
	 *
	 * @param string $cellStyle CSS class
	 */
	function setCellStyle($cellStyle) {
		if ($cellStyle)
			$this->attributes['CELLSTYLE'] = " class=\"$cellStyle\"";
		else
			$this->attributes['CELLSTYLE'] = $this->_Form->getLabelStyle();
	}

	/**
	 * Set width of the grid table
	 *
	 * @param int $tableWidth Table width in pixels
	 */
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " width=\"{$tableWidth}\"";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	/**
	 * Set cell sizes
	 *
	 * The $sizes argument must be an array of integers whose sum is 100.
	 *
	 * @param array $sizes Cell sizes
	 */
	function setCellSizes($sizes) {
		if (sizeof($sizes) != (sizeof($this->fieldSet) + 1) || array_sum($sizes) != 100) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALID_CELLSIZES', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			array_walk($sizes, 'trim');
			$this->cellSizes = $sizes;
		}
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// datasource and fieldset are mandatory
		if (isset($children['DATASOURCE']) && isset($children['FIELDSET']) &&
			TypeUtils::isInstanceOf($children['FIELDSET'], 'XmlNode') &&
			$children['FIELDSET']->hasChildren()) {
			// create fieldset members
			$globalDisabled = resolveBooleanChoice(@$attrs['DISABLED']);
			for ($i=0, $s=$children['FIELDSET']->getChildrenCount(); $i<$s; $i++) {
				$Child =& $children['FIELDSET']->getChild($i);
				if ($Child->getName() == '#cdata-section')
					continue;
				switch($Child->getTag()) {
					case 'EDITFIELD' : $fieldClassName = 'EditField'; break;
					case 'PASSWDFIELD' : $fieldClassName = 'PasswdField'; break;
					case 'MEMOFIELD' : $fieldClassName = 'MemoField'; break;
					case 'CHECKFIELD' : $fieldClassName = 'CheckField'; break;
					case 'FILEFIELD' : $fieldClassName = 'FileField'; break;
					case 'LOOKUPFIELD' : $fieldClassName = 'LookupField'; break;
					case 'COMBOFIELD' : $fieldClassName = 'ComboField'; break;
					case 'RADIOFIELD' : $fieldClassName = 'RadioField'; break;
					case 'DBRADIOFIELD' : $fieldClassName = 'DbRadioField'; break;
					case 'HIDDENFIELD' : $fieldClassName = 'HiddenField'; break;
					case 'TEXTFIELD' : $fieldClassName = 'TextField'; break;
					default : PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALID_FIELDTYPE', $Child->getTag()), E_USER_ERROR, __FILE__, __LINE__); break;
				}
				import("php2go.form.field.{$fieldClassName}");
				$Field = new $fieldClassName($this->_Form, TRUE);
				$Field->onLoadNode($Child->getAttributes(), $Child->getChildrenTagsArray());
				// disabled flag propagates to the fieldset members
				if ($globalDisabled)
					$Field->setDisabled(TRUE);
				// store field and field name
				$this->fieldSet[] = $Field;
				$this->fieldNames[] = $Field->getName();
			}
			// are headers visible?
			if (isset($attrs['SHOWHEADER']))
				$this->setShowHeader(resolveBooleanChoice($attrs['SHOWHEADER']));
			else
				$this->setShowHeader(TRUE);
			// headers
			$this->setHeaders(@$attrs['HEADERS']);
			// headers CSS class
			$this->setHeaderStyle(@$attrs['HEADERSTYLE']);
			// cells CSS class
			$this->setCellStyle(@$attrs['CELLSTYLE']);
			// table width
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// cell sizes
			if (isset($attrs['CELLSIZES']))
				$this->setCellSizes(explode(',', $attrs['CELLSIZES']));
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATAGRID_STRUCTURE', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		parent::processDbQuery(ADODB_FETCH_NUM);
		if ($this->_Rs->fieldCount() != (sizeof($this->fieldSet) + 2))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALID_FIELDCOUNT', $this->name), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->Template = new Template(PHP2GO_TEMPLATE_PATH . 'datagrid.tpl');
		$this->Template->parse();
		$this->Template->globalAssign('id', $this->id);
		$this->Template->assign('style', $this->attributes['USERSTYLE']);
		$this->Template->assign('width', $this->attributes['TABLEWIDTH']);
		if ($this->attributes['SHOWHEADER']) {
			$headers = TypeUtils::toArray(@$this->attributes['HEADERS']);
			$this->Template->createBlock('loop_line');
			$this->Template->assign('row_id', 'header');
			for ($i=1,$s=$this->_Rs->fieldCount(); $i<$s; $i++) {
				$Field =& $this->_Rs->fetchField($i);
				$this->Template->createAndAssign('loop_header_cell', array(
					'style' => $this->attributes['HEADERSTYLE'],
					'width' => (isset($this->cellSizes[$i-1]) ? " WIDTH=\"{$this->cellSizes[$i-1]}%\"" : ''),
					'col_name' => (isset($headers[$i-1]) ? $headers[$i-1] : $Field->name)
				));
			}
		}
		$isPosted = ($this->_Form->isPosted() && !empty($this->value));
		while ($dataRow = $this->_Rs->fetchRow()) {
			$submittedRow = ($isPosted ? @$this->value[$dataRow[0]] : NULL);
			$this->Template->createBlock('loop_line');
			$this->Template->assign('row_id', 'row_' . $dataRow[0]);
			$this->Template->createAndAssign('loop_cell', array(
				'align' => 'left',
				'style' => $this->attributes['CELLSTYLE'],
				'width' => (isset($this->cellSizes[0]) ? " width=\"{$this->cellSizes[0]}%\"" : ''),
				'col_data' => $dataRow[1]
			));
			for ($i=0, $s=sizeof($this->fieldSet); $i<$s; $i++) {
				$Field = $this->fieldSet[$i];
				$Field->preRendered = FALSE;
				$Field->setId("{$this->name}_{$dataRow[0]}_{$this->fieldNames[$i]}");
				$Field->setName("{$this->name}[{$dataRow[0]}][{$this->fieldNames[$i]}]");
				if ($isPosted) {
					// special fix for checkbox inputs
					if ($Field->getFieldTag() == 'CHECKFIELD') {
						eval("\$submittedRow['{$this->fieldNames[$i]}'] = \$_{$this->_Form->formMethod}['V_{$this->name}'][{$dataRow[0]}]['{$this->fieldNames[$i]}'];");
						$Field->setValue($submittedRow[$this->fieldNames[$i]]);
					}
					// apply submitted value even when empty
					elseif (isset($submittedRow[$this->fieldNames[$i]])) {
						$Field->setValue($submittedRow[$this->fieldNames[$i]]);
					}
					// apply original value from the database
					else {
						$Field->setValue($dataRow[$i+2]);
					}
				} else {
					$Field->setValue($dataRow[$i+2]);
				}
				$Field->onPreRender();
				$this->Template->createBlock('loop_cell');
				$this->Template->assign(array(
					'align' => 'center',
					'style' => $this->attributes['CELLSTYLE'],
					'width' => (isset($this->cellSizes[$i+1]) ? " width=\"{$this->cellSizes[$i+1]}%\"" : ''),
					'col_data' => $Field->getContent()
				));
			}
		}
	}
}
?>