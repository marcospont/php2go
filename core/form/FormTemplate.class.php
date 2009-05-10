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
 * Renders forms based on user defined template files
 *
 * The FormTemplate class creates forms based on 2 basic arguments: a
 * <b>XML file</b>, describing sections, fields and buttons, and a
 * <b>template file</b>, determining how this elements should be
 * displayed.
 *
 * You should follow a pattern when configuring template placeholders
 * for fields, labels and other form elements:
 * <code>
 * /* place holder of a field whose NAME attribute is 'product_code' {@*}
 * {$product_code}
 * /* place holder of the label of a field whose NAME attribute is 'country_id' {@*}
 * {$label_country_id}
 * /* place holder of the help message of a field whose NAME attribute is 'country_id' {@*}
 * {$help_country_id}
 * /* place holder of the name of the section whose ID attribute is 'main' {@*}
 * {$section_main}
 * /* block definition for a conditional section whose ID attribute is 'secure_data' {@*}
 * <!-- START BLOCK : secure_data -->
 * ... section content ...
 * <!-- END BLOCK : secure_data -->
 * </code>
 *
 * @package form
 * @uses Template
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormTemplate extends Form
{
	/**
	 * Template used to render the form
	 *
	 * @var object Template
	 */
	var $Template;

	/**
	 * Template placeholder used to display
	 * the validation errors summary
	 *
	 * @var string
	 * @access private
	 */
	var $errorPlaceHolder;

	/**
	 * Class constructor
	 *
	 * @param string $xmlFile Form XML specification file
	 * @param string $templateFile Template file
	 * @param string $formName Form name
	 * @param Document &$Document Document instance in which the form will be inserted
	 * @param array $tplIncludes Hash array of template includes
	 * @return FormTemplate
	 */
	function FormTemplate($xmlFile, $templateFile, $formName, &$Document, $tplIncludes=array()) {
		parent::Form($xmlFile, $formName, $Document);
		$this->Template = new Template($templateFile);
		if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {
			foreach ($tplIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
	}

	/**
	 * Configures how validation error(s) should be displayed
	 *
	 * The {@link FORM_CLIENT_ERROR_DHTML} mode will only be enabled
	 * if the $clientContainerId argument is not empty.
	 *
	 * @param string $serverPlaceHolder Template placeholder to display server-side error(s)
	 * @param int $clientMode Display mode to the client validation error(s) ({@link FORM_CLIENT_ERROR_ALERT} or {@link FORM_CLIENT_ERROR_DHTML})
	 * @param string $clientContainerId Element ID used to display client validation error(s) (when $mode=={@link FORM_CLIENT_ERROR_DHTML})
	 * @param bool $showAll Show all errors or just the first one
	 */
	function setErrorDisplayOptions($serverPlaceHolder, $clientMode=NULL, $clientContainerId='', $showAll=TRUE) {
		$this->errorPlaceHolder = $serverPlaceHolder;
		if (in_array($clientMode, array(FORM_CLIENT_ERROR_ALERT, FORM_CLIENT_ERROR_DHTML)))
			$this->clientErrorOptions['mode'] = $clientMode;
		$this->clientErrorOptions['placeholder'] = $clientContainerId;
		$this->clientErrorOptions['showAll'] = (bool)$showAll;
	}

	/**
	 * Prepares the form to be rendered
	 */
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			$this->_buildErrors();
			$sectionIds = array_keys($this->sections);
			foreach ($sectionIds as $sectionId) {
				$section =& $this->sections[$sectionId];
				$this->_buildSection($section);
			}
			$this->Template->onPreRender();
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
		return $this->_buildFormStart() . $this->Template->getContent() . "\n</form>";
	}

	/**
	 * Builds and displays the form's HTML code
	 */
	function display() {
		$this->onPreRender();
		print $this->_buildFormStart();
		$this->Template->display();
		print "\n</form>";
	}

	/**
	 * Display the summary of server-side validation errors
	 *
	 * @access private
	 */
	function _buildErrors() {
		$this->Template->setCurrentBlock(TP_ROOTBLOCK);
		$this->Template->assign('errorStyle', parent::getErrorStyle());
		if (isset($this->errorPlaceHolder) && ($errors = parent::getFormErrors())) {
			if ($this->clientErrorOptions['showAll']) {
				$mode = @$this->errorStyle['list_mode'];
				$errors = ($mode == FORM_ERROR_BULLET_LIST ? "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>" : implode("<br />", $errors));
				$this->Template->assign('errorDisplay', " style=\"display:block\"");
				$this->Template->assign($this->errorPlaceHolder, @$this->errorStyle['header_text'] . $errors);
			} else {
				$this->Template->assign($this->errorPlaceHolder, $errors[0]);
			}
		} else {
			$this->Template->assign('errorDisplay', " style=\"display:none\"");
		}
	}


	/**
	 * Renders a form section
	 *
	 * @param FormSection &$section Form section
	 * @access private
	 */
	function _buildSection(&$section) {
		$sectionId = $section->getId();
		if ($section->isConditional()) {
			if (!$this->Template->isBlockDefined($sectionId))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_SECTION_TPLBLOCK', array($section->getId(), $section->getId())), E_USER_ERROR, __FILE__, __LINE__);
			if ($section->isVisible()) {
				$this->Template->createBlock($sectionId);
				$this->Template->assign("$sectionId.section_" . $sectionId, $section->name);
				for ($i = 0; $i < sizeof($section->getChildren()); $i++) {
					$object =& $section->getChild($i);
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSection($object);
					} else if ($section->getChildType($i) == 'BUTTON') {
						$this->Template->assignByRef("$sectionId." . $object->getName(), $object);
					} else if ($section->getChildType($i) == 'BUTTONGROUP') {
						for ($j=0; $j<sizeof($object); $j++) {
							$button =& $object[$j];
							$this->Template->assignByRef("{$sectionId}." . $button->getName(), $button);
						}
					} else if ($section->getChildType($i) == 'FIELD') {
						$this->Template->assign("{$sectionId}.label_" . $object->getName(), $object->getLabelCode($section->attributes['REQUIRED_FLAG'], $section->attributes['REQUIRED_COLOR'], $section->attributes['REQUIRED_TEXT']));
						$this->Template->assign("{$sectionId}.help_" . $object->getName(), $object->getHelpCode());
						$this->Template->assignByRef("{$sectionId}." . $object->getName(), $object);
					}
				}
			}
		} else {
			$this->Template->assign("_ROOT.section_{$sectionId}", $section->name);
			for ($i = 0; $i < sizeof($section->getChildren()); $i++) {
				$object =& $section->getChild($i);
				if ($section->getChildType($i) == 'SECTION') {
					$this->_buildSection($object);
				} else if ($section->getChildType($i) == 'BUTTON') {
					$this->Template->assignByRef("_ROOT." . $object->getName(), $object);
				} else if ($section->getChildType($i) == 'BUTTONGROUP') {
					for ($j=0; $j<sizeof($object); $j++) {
						$button =& $object[$j];
						$this->Template->assignByRef("_ROOT." . $button->getName(), $button);
					}
				} else if ($section->getChildType($i) == 'FIELD') {
					$this->Template->assign("_ROOT.label_" . $object->getName(), $object->getLabelCode($section->attributes['REQUIRED_FLAG'], $section->attributes['REQUIRED_COLOR'], $section->attributes['REQUIRED_TEXT']));
					$this->Template->assign("_ROOT.help_" . $object->getName(), $object->getHelpCode());
					$this->Template->assignByRef("_ROOT." . $object->getName(), $object);
				}
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
		if (is_array(@$settings['ERRORS'])) {
			$showAll = (array_key_exists('SHOW_ALL', $settings['ERRORS']) ? (bool)$settings['ERRORS']['SHOW_ALL'] : TRUE);
			$this->setErrorDisplayOptions(@$settings['ERRORS']['TEMPLATE_PLACEHOLDER'], @constant($settings['ERRORS']['CLIENT_MODE']), @$settings['ERRORS']['CLIENT_CONTAINER'], $showAll);
		}
	}

	/**
	 * Parses presentation and layout settings
	 * from the XML specification
	 *
	 * Parses <b>errors</b> XML node.
	 *
	 * @param string $tag Node name
	 * @param array $attrs Node attributes
	 * @access private
	 */
	function _loadXmlSettings($tag, $attrs) {
		parent::_loadXmlSettings($tag, $attrs);
		if ($tag == 'ERRORS') {
			$showAll = (isset($attrs['SHOWALL']) ? resolveBooleanChoice($attrs['SHOWALL']) : TRUE);
			$this->setErrorDisplayOptions(@$attrs['TPLPLACEHOLDER'], @constant($attrs['CLIENTMODE']), @$attrs['CLIENTCONTAINER'], $showAll);
		}
	}
}
?>