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

import('php2go.form.field.EditField');
import('php2go.form.field.LookupField');
import('php2go.template.Template');
import('php2go.util.HtmlUtils');

/**
 * Value selection tool based on a text input, a set of buttons and a select input
 *
 * New values entered in the text input are added to the select input through an
 * "add" button. Existent select options can be removed using the "remove" and
 * "remove all" buttons. The select input can also load previously saved values,
 * using a data source.
 *
 * Added values and removed values are stored in 2 hidden fields. These fields are
 * also used to defined the submitted value of this component.
 *
 * @package form
 * @subpackage field
 * @uses EditField
 * @uses HtmlUtils
 * @uses LookupField
 * @uses Template
 * @uses TypeUtils
 * @author Marcos Pont
 * @version $Revision$
 */
class EditSelectionField extends FormField
{
	/**
	 * Images for the action buttons
	 *
	 * @var array
	 * @access private
	 */
	var $buttonImages = array();

	/**
	 * Separator used in hidden control fields
	 *
	 * @var string
	 * @access private
	 */
	var $listSeparator = '#';

	/**
	 * EditField component used to add values
	 *
	 * @var object EditField
	 * @access private
	 */
	var $_EditField;

	/**
	 * LookupField component used to present saved values
	 * and to add values entered in the text input
	 *
	 * @var object LookupField
	 * @access private
	 */
	var $_LookupField;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return EditSelectionField
	 */
	function EditSelectionField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->composite = TRUE;
		$this->searchable = FALSE;
		$this->customEvents = array('onAdd', 'onRemove');
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'editselectionfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('label', $this->label);
		$Tpl->assign('editId', $this->_EditField->getId());
		$Tpl->assign('editLabel', $this->_EditField->getLabel());
		$Tpl->assignByRef('edit', $this->_EditField);
		$Tpl->assign('separator', $this->listSeparator);
		$Tpl->assign('tableWidth', $this->attributes['TABLEWIDTH']);
		$Tpl->assign('labelStyle', $this->_Form->getLabelStyle());
		$Tpl->assign('lookupId', $this->_LookupField->getId());
		$Tpl->assign('lookupLabel', $this->_LookupField->getLabel());
		$Tpl->assignByRef('lookup', $this->_LookupField);
		$Tpl->assign('addedName', $this->attributes['INSFIELD']);
		$Tpl->assign('removedName', $this->attributes['REMFIELD']);
		$Tpl->assign('countLabel', PHP2Go::getLangVal('SEL_INSERTED_VALUES_LABEL'));
		$Tpl->assign('customListeners', $this->customListeners);
		for ($i=0; $i<sizeof($this->attributes['BUTTONS']); $i++)
			$Tpl->assign('button' . $i, $this->attributes['BUTTONS'][$i]);
		$Tpl->display();
	}

	/**
	 * Define the text input as the control the should be
	 * activated when the component's label is clicked
	 *
	 * @return string
	 */
	function getFocusId() {
		return $this->_EditField->getId();
	}

	/**
	 * Get the internal EditField
	 *
	 * @return EditField
	 */
	function &getEditField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_EditField, 'EditField'))
			$result =& $this->_EditField;
		return $result;
	}

	/**
	 * Get the internal LookupField
	 *
	 * @return LookupField
	 */
	function &getLookupField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_LookupField, 'LookupField'))
			$result =& $this->_LookupField;
		return $result;
	}

	/**
	 * Set the name of the hidden field that will hold added values
	 *
	 * @param string $insField Field name
	 */
	function setInsertedValuesFieldName($insField) {
		if (trim($insField) != '' && $insField != $this->_EditField->name && $insField != $this->_LookupField->name)
			$this->attributes['INSFIELD'] = $insField;
		else
			$this->attributes['INSFIELD'] = $this->id . '_inserted';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['INSFIELD']);
	}

	/**
	 * Set the name of the hidden field that will hold removed values
	 *
	 * @param string $remField Field name
	 */
	function setRemovedValuesFieldName($remField) {
		if (trim($remField) != '' && $remField != $this->_EditField->name && $remField != $this->_LookupField->name)
			$this->attributes['REMFIELD'] = $remField;
		else
			$this->attributes['REMFIELD'] = $this->id . '_removed';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['REMFIELD']);
	}

	/**
	 * Set component's table width in pixels
	 *
	 * @param int $tableWidth Table width
	 */
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " width='" . $tableWidth . "'";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	/**
	 * Set images for the action buttons
	 *
	 * @param string $add "Add" button image
	 * @param string $rem "Remove" button image
	 * @param string $remAll "Remove all" button image
	 */
	function setButtonImages($add, $rem, $remAll) {
		(trim($add) != '') && ($this->buttonImages['ADD'] = $add);
		(trim($rem) != '') && ($this->buttonImages['REM'] = $rem);
		(trim($remAll) != '') && ($this->buttonImages['REMALL'] = $remAll);
	}


	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// check if children structure is valid
		if (isset($children['EDITFIELD']) && isset($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['EDITFIELD'], 'XmlNode') &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'], 'XmlNode')) {
			// create EditField and LookupField child components
			$this->_EditField = new EditField($this->_Form, TRUE);
			$this->_EditField->onLoadNode($children['EDITFIELD']->getAttributes(), $children['EDITFIELD']->getChildrenTagsArray());
			$this->_LookupField = new LookupField($this->_Form, TRUE);
			$this->_LookupField->onLoadNode($children['LOOKUPFIELD']->getAttributes(), $children['LOOKUPFIELD']->getChildrenTagsArray());
			// disabled attribute is propagated to the children
			$this->_EditField->attributes['DISABLED'] = $this->attributes['DISABLED'];
			$this->_LookupField->attributes['DISABLED'] = $this->attributes['DISABLED'];
			// hidden field for added values
			$this->setInsertedValuesFieldName(@$attrs['INSFIELD']);
			// hidden field for removed values
			$this->setRemovedValuesFieldName(@$attrs['REMFIELD']);
			// table width
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// button images
			$this->setButtonImages(@$attrs['ADDIMG'], @$attrs['REMIMG'], @$attrs['REMALLIMG']);
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_EDITSELECTION_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		if ($this->_Form->isPosted()) {
			$inserted = HttpRequest::getVar($this->attributes['INSFIELD'], $this->_Form->formMethod);
			$removed = HttpRequest::getVar($this->attributes['REMFIELD'], $this->_Form->formMethod);
			parent::setSubmittedValue(array(
				$this->attributes['INSFIELD'] => (!empty($inserted) ? explode($this->listSeparator, $inserted) : array()),
				$this->attributes['REMFIELD'] => (!empty($removed) ? explode($this->listSeparator, $removed) : array())
			));
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/editselectionfield.js');
		$this->attributes['BUTTONS'] = array();
		$buttonMessages = PHP2Go::getLangVal('EDIT_SELECTION_BUTTON_TITLES');
		$imgActCode = "<button id=\"%s\" name=\"%s\" type=\"button\" title=\"%s\" onClick=\"%s.%s(%s);\" style=\"cursor:pointer;background-color:transparent;border:none\"%s%s><img src=\"%s\" alt=\"\" border=\"0\"></button>";
		$btnActCode = "<button id=\"%s\" name=\"%s\" type=\"button\" style=\"width:25px\" title=\"%s\" onClick=\"%s.%s(%s);\"%s%s%s> %s </button>";
		$addOptions = sprintf("{upper: %s, lower: %s, trim: %s, capitalize: %s}",
			($this->_EditField->attributes['UPPER'] == 'T' ? 'true' : 'false'),
			($this->_EditField->attributes['LOWER'] == 'T' ? 'true' : 'false'),
			($this->_EditField->attributes['AUTOTRIM'] == 'T' ? 'true' : 'false'),
			($this->_EditField->attributes['CAPITALIZE'] == 'T' ? 'true' : 'false')
		);
		$actHash = array(
			array('ADD', 'add', 'add', '+', $addOptions),
			array('REM', 'rem', 'remove', '-', ''),
			array('REMALL', 'remall', 'removeAll', 'X', '')
		);
		for ($i=0; $i<sizeof($actHash); $i++) {
			if (isset($this->buttonImages[$actHash[$i][0]]))
				$this->attributes['BUTTONS'][] = sprintf($imgActCode,
					$this->id . '_' . $actHash[$i][1],
					$this->id . '_' . $actHash[$i][1],
					$buttonMessages[$actHash[$i][1]],
					$this->id . '_instance', $actHash[$i][2],
					$actHash[$i][4], $this->attributes['DISABLED'],
					$this->_EditField->attributes['TABINDEX'],
					$this->buttonImages[$actHash[$i][0]]
				);
			else
				$this->attributes['BUTTONS'][] = sprintf($btnActCode,
					$this->id . '_' . $actHash[$i][1],
					$this->id . '_' . $actHash[$i][1],
					$buttonMessages[$actHash[$i][1]],
					$this->id . '_instance', $actHash[$i][2],
					$actHash[$i][4], $this->_Form->getButtonStyle(),
					$this->_EditField->attributes['TABINDEX'],
					$this->attributes['DISABLED'], $actHash[$i][3]
				);
		}
		// EditField settings
		$this->_EditField->setRequired(FALSE);
		if ($this->accessKey)
			$this->_EditField->setAccessKey($this->accessKey);
		$this->_EditField->onPreRender();
		// LookupField settings
		if (trim($this->_LookupField->getAttribute('FIRST')) == "")
			$this->_LookupField->setFirstOption(PHP2Go::getLangVal('LOOKUP_SELECTION_DEFAULT_SELFIRST'));
		$this->_LookupField->disableFirstOption(FALSE);
		$this->_LookupField->setMultiple();
		$this->_LookupField->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onDblClick', sprintf("%s_instance.remove();", $this->id)));
		if (max(1, $this->_LookupField->getAttribute('INTSIZE')) < 2)
			$this->_LookupField->setSize(8);
		$this->_LookupField->onPreRender();
	}
}
?>