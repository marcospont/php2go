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

import('php2go.form.Form');

/**
 * Renders forms using a basic bundled layout template
 *
 * Sections are rendered as <b>fieldset</b> elements. Fields and
 * buttons are rendered one per line. CSS classes of the fieldsets,
 * fieldset labels and tables are customizable.
 *
 * Example:
 * <code>
 * /* layout.tpl {@*}
 * <table width="779" cellpadding="0" cellspacing="0" border="0">
 *   <tr><td>
 *     {$main}
 *   </td></tr>
 * </table>
 * /* my_form.xml {@*}
 * <form method="post" action="another_page.php" target="_blank">
 *   <style width="500" align="center" input="input_style" labelwidth="0.2" labelalign="left"/>
 *   <section id="main" name="Add Person">
 *     <editfield name="name" label="Name" size="30" maxlength="50"/>
 *     <editfield name="address" label="Address" size="40" maxlength="80"/>
 *     <buttons>
 *       <button name="save" type="SUBMIT" value="Save"/>
 *       <button name="reset" type="RESET" value="Reset"/>
 *     </buttons>
 *   </section>
 * </form>
 * /* page.php {@*}
 * $doc = new Document('layout.tpl');
 * $form = new FormBasic('my_form.xml', 'my_form', $doc);
 * $doc->assignByRef('main', $form);
 * $doc->display();
 * </code>
 *
 * In order to build forms with full layout customization,
 * please refer to {@link FormTemplate} class.
 *
 * @package form
 * @uses Template
 * @uses TypeUtils
 * @uses UserAgent
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormBasic extends Form
{
	/**
	 * Form align (left, center or right)
	 *
	 * @var string
	 */
	var $formAlign = 'left';

	/**
	 * Form width, in pixels
	 *
	 * @var int
	 */
	var $formWidth;

	/**
	 * Labels width (proportional value between 0 and 1)
	 *
	 * @var float
	 */
	var $labelW = 0.2;

	/**
	 * Labels align (left, center or right)
	 *
	 * @var string
	 */
	var $labelAlign = 'right';

	/**
	 * CSS class for fieldsets
	 *
	 * @var string
	 */
	var $fieldSetStyle;

	/**
	 * CSS class for fieldset labels (<b>legend</b> elements)
	 *
	 * @var string
	 */
	var $sectionTitleStyle;

	/**
	 * Padding for the section table cells
	 *
	 * @var int
	 */
	var $tblCPadding = 3;

	/**
	 * Spacing for the section table cells
	 *
	 * @var int
	 */
	var $tblCSpacing = 2;

	/**
	 * Template used to render the form
	 *
	 * @var object Template
	 * @access private
	 */
	var $_Template;

	/**
	 * Class constructor
	 *
	 * @param string $xmlFile Form XML specification file
	 * @param string $formName Form name
	 * @param Document &$Document Document instance in which the form will be inserted
	 * @return FormBasic
	 */
	function FormBasic($xmlFile, $formName, &$Document) {
		parent::Form($xmlFile, $formName, $Document);
		$this->_Template = new Template(PHP2GO_TEMPLATE_PATH . "basicform.tpl");
		$this->_Template->parse();
	}

	/**
	 * Set form's align
	 *
	 * @param string $align Alignment of the form
	 */
	function setFormAlign($align) {
		$this->formAlign = $align;
	}

	/**
	 * Set form's width
	 *
	 * Accepts integer or string values: 500, "500", "100%".
	 *
	 * @param int $width Width of the form's outer table
	 */
	function setFormWidth($width) {
		$this->formWidth = $width;
	}

	/**
	 * Get the CSS class definition for all section fieldsets
	 *
	 * @return string
	 */
	function getFieldsetStyle() {
		if (!empty($this->fieldSetStyle))
			return " class=\"{$this->fieldSetStyle}\"";
		return '';
	}

	/**
	 * Set a CSS class name for all section fieldsets
	 *
	 * @param string $style CSS class name
	 */
	function setFieldsetStyle($style) {
		$this->fieldSetStyle = $style;
	}

	/**
	 * Get the CSS class definition for all section labels
	 *
	 * @return string
	 */
	function getSectionTitleStyle() {
		if (!empty($this->sectionTitleStyle))
			return " class=\"{$this->sectionTitleStyle}\"";
		return '';
	}

	/**
	 * Set a CSS class name for all section labels
	 *
	 * @param string $style CSS class
	 */
	function setSectionTitleStyle($style) {
		$this->sectionTitleStyle = $style;
	}

	/**
	 * Set the proportional width of the form labels
	 *
	 * The value must be a float number and between 0 and 1.
	 *
	 * @param float $width Labels width
	 */
	function setLabelWidth($width) 	{
		$width = abs(floatval($width));
		if ($width <= 0 || $width > 1) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_VALUE_OUT_OF_BOUNDS', array("width (FormBasic::setLabelWidth)", 0, 1)), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->labelW = $width;
		}
	}

	/**
	 * Set labels align
	 *
	 * @param string $align Labels align
	 */
	function setLabelAlign($align) {
		$this->labelAlign = $align;
	}

	/**
	 * Set padding and spacing values to the form section tables
	 *
	 * @param int $cellpadding Cell padding
	 * @param int $cellspacing Cell spacing
	 */
	function setFormTableProperties($cellpadding, $cellspacing) {
		$this->tblCPadding = abs(intval($cellpadding));
		$this->tblCSpacing = abs(intval($cellspacing));
	}

	/**
	 * Configures how validation error(s) should be displayed
	 *
	 * @param int $mode Display mode ({@link FORM_CLIENT_ERROR_ALERT} or {@link FORM_CLIENT_ERROR_DHTML})
	 * @param bool $showAll Show all errors or just the first one
	 */
	function setErrorDisplayOptions($mode=NULL, $showAll=TRUE) {
		if (in_array($mode, array(FORM_CLIENT_ERROR_ALERT, FORM_CLIENT_ERROR_DHTML)))
			$this->clientErrorOptions['mode'] = $mode;
		$this->clientErrorOptions['showAll'] = (bool)$showAll;
	}

	/**
	 * Prepares the form to be rendered
	 */
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			$this->_buildFormInterface();
			$this->_Template->onPreRender();
			parent::buildScriptCode();
		}
	}

	/**
	 * Builds and returns the form's HTML code
	 *
	 * @return string
	 */
	function getContent() {
		$this->onPreRender();
		return $this->_buildFormStart() . $this->_Template->getContent() . "</form>";
	}

	/**
	 * Builds and prints the form's HTML code
	 */
	function display() {
		$this->onPreRender();
		print $this->_buildFormStart();
		$this->_Template->display();
		print "</form>";
	}

	/**
	 * Render all form sections in the internal template file
	 *
	 * @access private
	 */
	function _buildFormInterface() {
		$this->_Template->assign('_ROOT.formWidth', (isset($this->formWidth) ? " width=\"{$this->formWidth}\"" : ''));
		$this->_Template->assign('_ROOT.formAlign', TypeUtils::ifNull($this->formAlign, 'left'));
		$this->_Template->assign('_ROOT.errorStyle', parent::getErrorStyle());
		// display validation errors
		if ($errors = parent::getFormErrors()) {
			$this->_Template->assign('_ROOT.errorDisplay', " style=\"display:block\"");
			if ($this->clientErrorOptions['showAll']) {
				$mode = @$this->errorStyle['list_mode'];
				$errors = ($mode == FORM_ERROR_BULLET_LIST ? "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>" : implode("<br />", $errors));
				$this->_Template->assign('_ROOT.errorTitle', @$this->errorStyle['header_text']);
				$this->_Template->assign('_ROOT.errorMessages', $errors);
			} else {
				$this->_Template->assign('_ROOT.errorMessages', $errors[0]);
			}
		} else {
			$this->_Template->assign('_ROOT.errorDisplay', " style=\"display:none\"");
		}
		// configure client errors display mode
		if ($this->clientErrorOptions['mode'] == FORM_CLIENT_ERROR_DHTML)
			$this->clientErrorOptions['placeholder'] = 'form_client_errors';
		$sectionIds = array_keys($this->sections);
		foreach ($sectionIds as $sectionId) {
			$section =& $this->sections[$sectionId];
			if ($section->isVisible() && $section->hasChildren()) {
				$this->_Template->createBlock('loop_section');
				$this->_Template->assign('sectionName', $section->name);
				$this->_Template->assign('sectionTitleStyle', (!empty($this->sectionTitleStyle) ? $this->getSectionTitleStyle() : parent::getLabelStyle()));
				$this->_Template->assign('fieldsetStyle', $this->getFieldsetStyle());
				$this->_Template->assign("tablePadding", $this->tblCPadding);
				$this->_Template->assign("tableSpacing", $this->tblCSpacing);
				// generate sections with their children
				$buttons = array();
				for ($i = 0; $i < sizeof($section->getChildren()); $i++) {
					$object =& $section->getChild($i);
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSubSection($object);
					} elseif ($section->getChildType($i) == 'BUTTON') {
						$this->_Template->createBlock('section_item');
						$this->_Template->assign('itemType', 'button');
						$this->_Template->assignByRef('button', $object);
					} elseif ($section->getChildType($i) == 'BUTTONGROUP') {
						$this->_buildButtonGroup($object);
					} elseif ($section->getChildType($i) == 'FIELD') {
						if ($object->getFieldTag() == 'HIDDENFIELD') {
							$this->_Template->createBlock('hidden_field');
							$this->_Template->assignByRef('field', $object);
						} else {
							$this->_Template->createBlock('section_item');
							$this->_Template->assign('itemType', 'field');
							$this->_Template->assign('labelWidth', ($this->labelW * 100) . '%');
							$this->_Template->assign('labelAlign', $this->labelAlign);
							$this->_Template->assign('label', $object->getLabelCode($section->attributes['REQUIRED_FLAG'], $section->attributes['REQUIRED_COLOR'], $section->attributes['REQUIRED_TEXT']));
							$this->_Template->assign('fieldWidth', (100 - ($this->labelW * 100)) . '%');
							$this->_Template->assignByRef('field', $object);
							$helpCode = $object->getHelpCode();
							if (!empty($helpCode)) {
								if ($this->helpOptions['mode'] == FORM_HELP_POPUP)
									$this->_Template->assign('popupHelp', "&nbsp;" . $helpCode);
								else
									$this->_Template->assign('inlineHelp', "<br />" . $helpCode);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Render an internal subsection
	 *
	 * @param FormSection &$subSection Conditional subsection
	 * @access private
	 */
	function _buildSubSection(&$subSection) {
		if ($subSection->isVisible()) {
			for ($i = 0; $i < sizeof($subSection->getChildren()); $i++) {
				$object =& $subSection->getChild($i);
				if ($subSection->getChildType($i) == 'SECTION') {
					$this->_buildSubSection($object);
				} elseif ($subSection->getChildType($i) == 'BUTTON') {
					$this->_Template->createBlock('section_item');
					$this->_Template->assign('itemType', 'button');
					$this->_Template->assignByRef('button', $object);
				} elseif ($subSection->getChildType($i) == 'BUTTONGROUP') {
					$this->_buildButtonGroup($object);
				} else {
					if ($object->getFieldTag() == 'HIDDENFIELD') {
						$this->_Template->createBlock('hidden_field');
						$this->_Template->assignByRef('field', $object);
					} else {
						$this->_Template->createBlock('section_item');
						$this->_Template->assign('itemType', 'field');
						$this->_Template->assign('labelWidth', ($this->labelW * 100) . '%');
						$this->_Template->assign('labelAlign', $this->labelAlign);
						$this->_Template->assign('label', $object->getLabelCode($subSection->attributes['REQUIRED_FLAG'], $subSection->attributes['REQUIRED_COLOR'], $subSection->attributes['REQUIRED_TEXT']));
						$this->_Template->assign('fieldWidth', (100 - ($this->labelW * 100)) . '%');
						$this->_Template->assignByRef('field', $object);
						$helpCode = $object->getHelpCode();
						if (!empty($helpCode)) {
							if ($this->helpOptions['mode'] == FORM_HELP_POPUP)
								$this->_Template->assign('popupHelp', "&nbsp;" . $helpCode);
							else
								$this->_Template->assign('inlineHelp', "<br />" . $helpCode);
						}
					}
				}
			}
		}
	}

	/**
	 * Render a group of buttons
	 *
	 * @param array &$buttonGroup Group of buttons
	 * @access private
	 */
	function _buildButtonGroup(&$buttonGroup) {
		$this->_Template->createBlock('section_item');
		$this->_Template->assign('itemType', 'button_group');
		if (sizeof($buttonGroup) > 0) {
			for ($j=0,$s=sizeof($buttonGroup); $j<$s; $j++) {
				$this->_Template->createBlock('loop_button_group');
				$this->_Template->assign('btnW', round(100 / sizeof($buttonGroup)) . '%');
				$this->_Template->assignByRef('button', $buttonGroup[$j]);
			}
		}
	}

	/**
	 * Builds and returns the initial part of the form definition
	 *
	 * @access private
	 * @return string
	 */
	function _buildFormStart() {
		$target = (isset($this->actionTarget) ? " target=\"" . $this->actionTarget . "\"" : '');
		$enctype = ($this->hasUpload ? " enctype=\"multipart/form-data\"" : '');
		$signature = sprintf("\n<input type=\"hidden\" id=\"%s_signature\" name=\"%s\" value=\"%s\" />", $this->formName, FORM_SIGNATURE, parent::getSignature());
		return sprintf("<form id=\"%s\" name=\"%s\" action=\"%s\" method=\"%s\" style=\"display:inline\"%s%s>%s\n",
			$this->formName, $this->formName, $this->formAction,
			strtolower($this->formMethod), $target, $enctype, $signature
		);
	}

	/**
	 * Parses presentation and layout settings from the
	 * global configuration settings
	 *
	 * @param array $settings Global settings
	 * @access private
	 */
	function _loadGlobalSettings($settings) {
		parent::_loadGlobalSettings($settings);
		if (is_array(@$settings['BASIC'])) {
			$basic = $settings['BASIC'];
			(isset($basic['FIELDSET_STYLE'])) && $this->setFieldsetStyle($basic['FIELDSET_STYLE']);
			(isset($basic['SECTION_TITLE_STYLE'])) && $this->setSectionTitleStyle($basic['SECTION_TITLE_STYLE']);
			(isset($basic['ALIGN'])) && $this->setFormAlign($basic['ALIGN']);
			(isset($basic['WIDTH'])) && $this->setFormWidth($basic['WIDTH']);
			(isset($basic['LABEL_ALIGN'])) && $this->setLabelAlign($basic['LABEL_ALIGN']);
			(array_key_exists('LABEL_WIDTH', $basic)) && $this->setLabelWidth($basic['LABEL_WIDTH']);
			(array_key_exists('TABLE_PADDING', $basic) && array_key_exists('TABLE_SPACING', $basic)) && $this->setFormTableProperties($basic['TABLE_PADDING'], $basic['TABLE_SPACING']);
		}
		if (is_array(@$settings['ERRORS'])) {
			$showAll = (array_key_exists('SHOW_ALL', $settings['ERRORS']) ? (bool)$settings['ERRORS']['SHOW_ALL'] : TRUE);
			$this->setErrorDisplayOptions(@constant($settings['ERRORS']['CLIENT_MODE']), $showAll);
		}
	}

	/**
	 * Parses presentation and layout settings
	 * from the XML specification
	 *
	 * Parses <b>style</b> and <b>errors</b> XML nodes.
	 *
	 * @param string $tag Node name
	 * @param array $attrs Node attributes
	 * @access private
	 */
	function _loadXmlSettings($tag, $attrs) {
		parent::_loadXmlSettings($tag, $attrs);
		if ($tag == 'STYLE') {
			(isset($attrs['FIELDSET']))	&& ($this->fieldSetStyle = $attrs['FIELDSET']);
			(isset($attrs['SECTIONTITLE'])) && ($this->sectionTitleStyle = $attrs['SECTIONTITLE']);
			(isset($attrs['WIDTH'])) && ($this->formWidth = intval($attrs['WIDTH']));
			(isset($attrs['ALIGN']) && in_array(strtolower($attrs['ALIGN']), array('left', 'center', 'right'))) && ($this->formAlign = strtolower($attrs['ALIGN']));
			(isset($attrs['LABELALIGN']) && in_array(strtolower($attrs['LABELALIGN']), array('left', 'center', 'right'))) && ($this->labelAlign = strtolower($attrs['LABELALIGN']));
			(array_key_exists('LABELWIDTH', $attrs)) && ($this->labelW = floatval($attrs['LABELWIDTH']));
			(array_key_exists('TABLEPADDING', $attrs)) && ($this->tblCPadding = intval($attrs['TABLEPADDING']));
			(array_key_exists('TABLESPACING', $attrs)) && ($this->tblCPadding = intval($attrs['TABLESPACING']));
		} elseif ($tag == 'ERRORS') {
			$showAll = (isset($attrs['SHOWALL']) ? resolveBooleanChoice($attrs['SHOWALL']) : TRUE);
			$this->setErrorDisplayOptions(@constant($attrs['CLIENTMODE']), $showAll);
		}
	}
}
?>