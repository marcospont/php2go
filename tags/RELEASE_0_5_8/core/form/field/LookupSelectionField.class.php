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

import('php2go.form.field.FormField');
import('php2go.form.field.LookupField');
import('php2go.template.Template');
import('php2go.util.HtmlUtils');

/**
 * Value selection tool based on a pair of select inputs that exchange options
 *
 * Options of the left select input can be selected and copied to the right
 * select input. The "remove" and "remove all" can be used to remove options
 * from the right select. Copy operations can also be triggered by double
 * clicking on the options.
 *
 * Added values and removed values are stored in 2 hidden fields. These fields are
 * also used to defined the submitted value of this component.
 *
 * @package form
 * @subpackage field
 * @uses LookupField
 * @uses Template
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LookupSelectionField extends FormField
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
	 * Source LookupField
	 *
	 * @var object LookupField
	 * @access private
	 */
	var $_SourceLookup;

	/**
	 * Target LookupField
	 *
	 * @var object LookupField
	 * @access private
	 */
	var $_TargetLookup;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the componet is child of another component
	 * @return LookupSelectionField
	 */
	function LookupSelectionField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->composite = TRUE;
		$this->searchable = FALSE;
		$this->customEvents = array('onadd', 'onremove');
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'lookupselectionfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('label', $this->label);
		$Tpl->assign('style', $this->attributes['USERSTYLE']);
		$Tpl->assign('separator', $this->listSeparator);
		$Tpl->assign('tableWidth', $this->attributes['TABLEWIDTH']);
		$Tpl->assign('labelStyle', $this->_Form->getLabelStyle());
		$Tpl->assign('availableId', $this->_SourceLookup->getId());
		$Tpl->assign('availableLabel', $this->_SourceLookup->getLabel());
		$Tpl->assignByRef('available', $this->_SourceLookup);
		$Tpl->assign('selectedId', $this->_TargetLookup->getId());
		$Tpl->assign('selectedLabel', $this->_TargetLookup->getLabel());
		$Tpl->assignByRef('selected', $this->_TargetLookup);
		$Tpl->assign('availableCountLabel', PHP2Go::getLangVal('SEL_AVAILABLE_VALUES_LABEL'));
		$Tpl->assign('availableCount', $this->_SourceLookup->getOptionCount());
		$Tpl->assign('selectedCountLabel', PHP2Go::getLangVal('SEL_INSERTED_VALUES_LABEL'));
		$Tpl->assign('addedName', $this->attributes['INSFIELD']);
		$Tpl->assign('removedName', $this->attributes['REMFIELD']);
		$Tpl->assign('customListeners', $this->customListeners);
		for($i=0; $i<sizeof($this->attributes['BUTTONS']); $i++)
			$Tpl->assign('button' . $i, $this->attributes['BUTTONS'][$i]);
		$Tpl->display();
	}

	/**
	 * Define the source select input as the control that
	 * should be activated when the component's label is clicked
	 *
	 * @return string
	 */
	function getFocusId() {
		return $this->_SourceLookup->getId();
	}

	/**
	 * Get the source LookupField
	 *
	 * @return LookupField
	 */
	function &getSourceLookup() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_SourceLookup, 'LookupField'))
			$result =& $this->_SourceLookup;
		return $result;
	}

	/**
	 * Get the target LookupField
	 *
	 * @return LookupField
	 */
	function &getTargetLookup() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_TargetLookup, 'LookupField'))
			$result =& $this->_TargetLookup;
		return $result;
	}

	/**
	 * Set the name of the hidden field that will hold added values
	 *
	 * @param string $insField
	 */
	function setInsertedValuesFieldName($insField) {
		if (trim($insField) != '' && $insField != $this->_SourceLookup->name && $insField != $this->_TargetLookup->name)
			$this->attributes['INSFIELD'] = $insField;
		else
			$this->attributes['INSFIELD'] = $this->id . '_inserted';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['INSFIELD']);
	}

	/**
	 * Set the name of the hidden field that will hold removed values
	 *
	 * @param string $remField
	 */
	function setRemovedValuesFieldName($remField) {
		if (trim($remField) != '' && $remField != $this->_SourceLookup->name && $remField != $this->_TargetLookup->name)
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
			$this->attributes['TABLEWIDTH'] = " width=\"" . $tableWidth . "\"";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	/**
	 * Set images for the action buttons
	 *
	 * @param string $addAll "Add all" button image
	 * @param string $add "Add" button image
	 * @param string $rem "Remove" button image
	 * @param string $remAll "Remove all" button image
	 */
	function setButtonImages($addAll, $add, $rem, $remAll) {
		(trim($addAll) != '') && ($this->buttonImages['ADDALL'] = $addAll);
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
		if (isset($children['LOOKUPFIELD']) && is_array($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'][0], 'XmlNode') &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'][1], 'XmlNode')) {
			$srcLookupChildren = $children['LOOKUPFIELD'][0]->getChildrenTagsArray();
			if (!isset($srcLookupChildren['DATASOURCE']))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_SOURCELOOKUP_DATASOURCE', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			$this->_SourceLookup = new LookupField($this->_Form, TRUE);
			$this->_SourceLookup->onLoadNode($children['LOOKUPFIELD'][0]->getAttributes(), $children['LOOKUPFIELD'][0]->getChildrenTagsArray());
			$this->_TargetLookup = new LookupField($this->_Form, TRUE);
			$this->_TargetLookup->onLoadNode($children['LOOKUPFIELD'][1]->getAttributes(), $children['LOOKUPFIELD'][1]->getChildrenTagsArray());
			// hidden field for added values
			$this->setInsertedValuesFieldName(@$attrs['INSFIELD']);
			// hidden field for removed values
			$this->setRemovedValuesFieldName(@$attrs['REMFIELD']);
			// table width
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// button images
			$this->setButtonImages(@$attrs['ADDALLIMG'], @$attrs['ADDIMG'], @$attrs['REMIMG'], @$attrs['REMALLIMG']);
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_LOOKUPSELECTION_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
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
				$this->attributes['INSFIELD'] => (!empty($inserted) ? explode('#', $inserted) : array()),
				$this->attributes['REMFIELD'] => (!empty($removed) ? explode('#', $removed) : array())
			));
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/lookupselectionfield.js');
		$this->attributes['BUTTONS'] = array();
		$buttonMessages = PHP2Go::getLangVal('LOOKUP_SELECTION_BUTTON_TITLES');
		$imgActCode = "<button id=\"%s\" name=\"%s\" type=\"button\" title=\"%s\" onclick=\"%s.%s();\" style=\"cursor:pointer;background-color:transparent;border:none\"%s%s><img src=\"%s\" alt=\"\" border=\"0\" /></button>";
		$btnActCode = "<button id=\"%s\" name=\"%s\" type=\"button\" style=\"width:30px;padding:0\" title=\"%s\" onclick=\"%s.%s();\"%s%s%s>%s</button>";
		$actHash = array(
			array('ADDALL', 'addall', 'addAll', '&gt;&gt;'),
			array('ADD', 'add', 'add', '&gt;'),
			array('REM', 'rem', 'remove', '&lt;'),
			array('REMALL', 'remall', 'removeAll', '&lt;&lt;')
		);
		for ($i=0; $i<sizeof($actHash); $i++) {
			if (isset($this->buttonImages[$actHash[$i][0]]))
				$this->attributes['BUTTONS'][] = sprintf($imgActCode,
					$this->id . '_' . $actHash[$i][1],
					$this->id . '_' . $actHash[$i][1],
					$buttonMessages[$actHash[$i][1]],
					$this->id . '_instance', $actHash[$i][2],
					$this->attributes['DISABLED'],
					$this->_SourceLookup->attributes['TABINDEX'],
					$this->buttonImages[$actHash[$i][0]]
				);
			else
				$this->attributes['BUTTONS'][] = sprintf($btnActCode,
					$this->id . '_' . $actHash[$i][1],
					$this->id . '_' . $actHash[$i][1],
					$buttonMessages[$actHash[$i][1]],
					$this->id . '_instance', $actHash[$i][2],
					$this->_Form->getButtonStyle(), $this->attributes['DISABLED'],
					$this->_SourceLookup->attributes['TABINDEX'], $actHash[$i][3]
				);
		}
		// source LookupField settings
		$this->_SourceLookup->setDisabled($this->disabled);
		$this->_SourceLookup->setRequired(FALSE);
		$this->_SourceLookup->disableFirstOption(TRUE);
		$this->_SourceLookup->setMultiple();
		$this->_SourceLookup->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onDblClick', sprintf("%s_instance.add();", $this->id)));
		if (max(1, $this->_SourceLookup->getAttribute('INTSIZE')) < 2)
			$this->_SourceLookup->setSize(8);
		if ($this->accessKey)
			$this->_SourceLookup->setAccessKey($this->accessKey);
		$this->_SourceLookup->onPreRender();
		// target LookupField settings
		$this->_TargetLookup->setDisabled($this->disabled);
		$this->_TargetLookup->setRequired(FALSE);
		if (trim($this->_TargetLookup->getAttribute('FIRST')) == "")
			$this->_TargetLookup->setFirstOption(PHP2Go::getLangVal('LOOKUP_SELECTION_DEFAULT_SELFIRST'));
		$this->_TargetLookup->disableFirstOption(FALSE);
		$this->_TargetLookup->isGrouping = FALSE;
		$this->_TargetLookup->setMultiple();
		$this->_TargetLookup->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onDblClick', sprintf("%s_instance.remove();", $this->id)));
		if (max(1, $this->_TargetLookup->getAttribute('INTSIZE')) < 2)
			$this->_TargetLookup->setSize(8);
		$this->_TargetLookup->onPreRender();
	}
}
?>