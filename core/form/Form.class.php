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

import('php2go.form.FormButton');
import('php2go.form.FormEventListener');
import('php2go.form.FormRule');
import('php2go.form.FormSection');
import('php2go.net.HttpRequest');
import('php2go.template.Template');
import('php2go.util.Statement');
import('php2go.validation.Validator');
import('php2go.xml.XmlDocument');

/**
 * Hidden field that carries the form signature upon submission
 */
define('FORM_SIGNATURE', '__form_signature');
/**
 * Error messages displayed sequentially, with line breaks
 */
define('FORM_ERROR_FLOW', 1);
/**
 * Error messages displayed in a bullet list
 */
define('FORM_ERROR_BULLET_LIST', 2);
/**
 * Error messages displayed in an alert box
 */
define('FORM_CLIENT_ERROR_ALERT', 1);
/**
 * Error messages displayed inside an element of the page
 */
define('FORM_CLIENT_ERROR_DHTML', 2);
/**
 * Inline help messages
 */
define('FORM_HELP_INLINE', 1);
/**
 * Help messages displayed in a floating popup, using the Overlib bundled library
 */
define('FORM_HELP_POPUP', 2);

/**
 * Base class of the PHP2Go forms API
 *
 * This class parses a XML file containing the specificaton of the form sections,
 * fields, event listeners, validation rules and buttons, transforming XML nodes
 * into objects which will be used by the child classes to render the form.
 *
 * The Form class also contains methods to configure form layout settings, perform
 * validation on the submitted values and render all script blocks needed by
 * form components.
 *
 * To read more about the XML specification format, please consult the DTD file
 * bundled with PHP2Go (/php2go/docs/dtd/ folder). To read more about the
 * applicability of each form component, please consult the following examples:
 * ajaxcrud.example.php, crud.example.php, formbasic.example.php,
 * formtemplate.example.php and formservervalidation.example.php.
 *
 * @package form
 * @uses HttpRequest
 * @uses ServiceJSRS
 * @uses Statement
 * @uses TypeUtils
 * @uses UserAgent
 * @uses Validator
 * @uses XmlDocument
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Form extends Component
{
	/**
	 * Form name
	 *
	 * @var string
	 */
	var $formName;

	/**
	 * Form action
	 *
	 * @var string
	 */
	var $formAction;

	/**
	 * Target of the form action
	 *
	 * @var string
	 */
	var $actionTarget;

	/**
	 * Form method
	 *
	 * @var string
	 */
	var $formMethod = 'POST';

	/**
	 * Form validation errors
	 *
	 * @var array
	 */
	var $formErrors = array();

	/**
	 * Indicates if this form is in read-only mode
	 *
	 * @var bool
	 */
	var $readonly = FALSE;

	/**
	 * CSS class for all buttons
	 *
	 * @var string
	 */
	var $buttonStyle;

	/**
	 * CSS class for all inputs
	 *
	 * @var string
	 */
	var $inputStyle;

	/**
	 * CSS class for all labels
	 *
	 * @var string
	 */
	var $labelStyle;

	/**
	 * Whether access keys should be highlighted inside component labels
	 *
	 * @var bool
	 */
	var $accessKeyHighlight = FALSE;

	/**
	 * Error style settings
	 *
	 * @var array
	 */
	var $errorStyle = array();

	/**
	 * Client error display options
	 *
	 * @var array
	 */
	var $clientErrorOptions = array();

	/**
	 * Help message options
	 *
	 * @var array
	 */
	var $helpOptions = array();

	/**
	 * Icons used by the form API
	 *
	 * @var array
	 */
	var $icons = array();

	/**
	 * Text used to indicate that a field is required
	 *
	 * @var string
	 */
	var $requiredText = "*";

	/**
	 * If required marks should be displayed
	 *
	 * @var bool
	 */
	var $requiredMark = TRUE;

	/**
	 * Color to the required field marks
	 */
	var $requiredColor = '#ff0000';

	/**
	 * Form sections
	 *
	 * Shouldn't be modified directly. Sections must be defined only in the XML file.
	 *
	 * @var array
	 */
	var $sections = array();

	/**
	 * Form fields
	 *
	 * Shouldn't be modified directly. Fields must be defined only in the XML file.
	 *
	 * @var array
	 */
	var $fields = array();

	/**
	 * Form buttons
	 *
	 * Shouldn't be modified directly. Buttons must be defined only in the XML file.
	 *
	 * @var array
	 */
	var $buttons = array();

	/**
	 * Form variables
	 *
	 * @var array
	 */
	var $variables = array();

	/**
	 * Form submitted values
	 *
	 * @var array
	 */
	var $submittedValues = array();

	/**
	 * Form postback fields
	 *
	 * @var array
	 */
	var $postbackFields = array();

	/**
	 * If the parsed XML tree was already processed
	 *
	 * @var bool
	 */
	var $formConstruct = FALSE;

	/**
	 * Indicates if the form was posted
	 *
	 * @var bool
	 */
	var $isPosted;

	/**
	 * URL to be used by BACK buttons
	 *
	 * @var string
	 */
	var $backUrl;

	/**
	 * JS validation script block
	 *
	 * @var string
	 */
	var $validatorCode = '';

	/**
	 * JS script block to be executed before client validation
	 *
	 * @var string
	 */
	var $beforeValidateCode = '';

	/**
	 * Indicates if the form contains at least one file input
	 *
	 * @var bool
	 */
	var $hasUpload = FALSE;

	/**
	 * XML root node attributes
	 *
	 * @var array
	 */
	var $rootAttrs = array();

	/**
	 * Parent HTML document
	 *
	 * @var object Document
	 */
	var $Document = NULL;

	/**
	 * Used to parse the XML specification
	 *
	 * @var object XmlDocument
	 * @access private
	 */
	var $XmlDocument = NULL;

	/**
	 * Class constructor
	 *
	 * @param string $xmlFile Form XML specification
	 * @param string $formName Form name
	 * @param Document &$Document Document instance in which the form will be inserted
	 * @return Form
	 */
	function Form($xmlFile, $formName, &$Document) {
		parent::Component();
		if ($this->isA('Form', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Form'), E_USER_ERROR, __FILE__, __LINE__);
		elseif (!TypeUtils::isInstanceOf($Document, 'Document'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		else {
			$this->Document =& $Document;
			$this->formName = $formName;
			$this->formAction = HttpRequest::basePath();
			$this->formMethod = "POST";
			$this->clientErrorOptions = array(
				'mode' => FORM_CLIENT_ERROR_ALERT
			);
			$this->helpOptions = array(
				'mode' => FORM_HELP_POPUP,
				'popup_icon' => PHP2GO_ICON_PATH . 'help.gif',
				'popup_attrs' => 'BGCOLOR,"#000000",FGCOLOR,"#ffffff"'
			);
			if ($this->isPosted() && ($savedUrl = @$_SESSION['PHP2GO_BACK_URL'][$this->formName]) !== NULL) {
				$this->backUrl = $savedUrl;
			} else {
				$this->backUrl = HttpRequest::referer();
				$_SESSION['PHP2GO_BACK_URL'][$this->formName] =& $this->backUrl;
			}
			$this->icons = array(
				'calendar' => PHP2GO_ICON_PATH . 'calendar.gif',
				'calculator' => PHP2GO_ICON_PATH . 'calculator.gif'
			);
			$this->XmlDocument = new XmlDocument();
			$this->XmlDocument->parseXml($xmlFile);
			$xmlRoot =& $this->XmlDocument->getRoot();
			$this->rootAttrs = $xmlRoot->getAttributes();
			// initialize layout properties from the global configuration settings
			$globalConf = PHP2Go::getConfigVal('FORMS', FALSE);
			if ($globalConf)
				$this->_loadGlobalSettings($globalConf);
			// process configuration options from the XML tree
			$this->_loadXmlSettings('FORM', (array)$this->rootAttrs);
			for ($i=0; $i<$xmlRoot->getChildrenCount(); $i++) {
				$node =& $xmlRoot->getChild($i);
				$tag = $node->getTag();
				if ($tag != 'VARIABLE' && $tag != 'SECTION')
					$this->_loadXmlSettings($tag, (array)$node->getAttributes());
			}
		}
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Set the form method
	 *
	 * @param string $method GET or POST
	 */
	function setFormMethod($method) {
		$method = trim($method);
		if (in_array(strtoupper($method), array('GET','POST')))
			$this->formMethod = $method;
		else
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_FORM_METHOD', array($method, $this->formName)), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Set the form action
	 *
	 * @param string $action Action URI
	 */
	function setFormAction($action) {
		$this->formAction = $action;
	}

	/**
	 * Form action's target
	 *
	 * @param string $target Action target
	 */
	function setFormActionTarget($target) {
		$this->actionTarget = $target;
	}

	/**
	 * Get the form signature
	 *
	 * @return string
	 */
	function getSignature() {
		return md5($this->formName);
	}

	/**
	 * Set the target URL for BACK buttons
	 *
	 * @param string $backUrl Back URL
	 */
	function setBackUrl($backUrl) {
		$this->backUrl = $backUrl;
	}

	/**
	 * Get the CSS class definition for all form fields
	 *
	 * @return string
	 */
	function getInputStyle() {
		$Agent =& UserAgent::getInstance();
		if (!empty($this->inputStyle) && $Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
			return " class=\"{$this->inputStyle}\"";
		return '';
	}

	/**
	 * Set a CSS class name for all form fields
	 *
	 * @param string $style CSS class
	 */
	function setInputStyle($style) {
		$this->inputStyle = $style;
	}

	/**
	 * Get the CSS class definition for all form labels
	 *
	 * @return string
	 */
	function getLabelStyle() {
		if (isset($this->labelStyle)) {
			return " class=\"{$this->labelStyle}\"";
		} else {
			return '';
		}
	}

	/**
	 * Set a CSS class name for all form labels
	 *
	 * @param string $style CSS class
	 */
	function setLabelStyle($style) {
		$this->labelStyle = $style;
	}

	/**
	 * Get the CSS class definition for all form buttons
	 *
	 * @return string
	 */
	function getButtonStyle() {
		$Agent =& UserAgent::getInstance();
		if (!empty($this->buttonStyle) && $Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
			return " class=\"{$this->buttonStyle}\"";
		return '';
	}

	/**
	 * Set a CSS class name for all form buttons
	 *
	 * @param string $style CSS class
	 */
	function setButtonStyle($style) {
		$this->buttonStyle = $style;
	}

	/**
	 * Get the CSS class definition for the validation errors summary
	 *
	 * @return string
	 */
	function getErrorStyle() {
		if (isset($this->errorStyle['class'])) {
			return " class=\"{$this->errorStyle['class']}\"";
		} else {
			return '';
		}
	}

	/**
	 * Set style properties of the validation errors summary
	 *
	 * @param string $class CSS class name
	 * @param int $listMode List mode ({@link FORM_ERROR_FLOW} or {@link FORM_ERROR_BULLET_LIST})
	 * @param string $headerText Header text
	 * @param string $headerStyle Header CSS class
	 */
	function setErrorStyle($class, $listMode=FORM_ERROR_FLOW, $headerText=NULL, $headerStyle=NULL) {
		if ($listMode != FORM_ERROR_FLOW && $listMode != FORM_ERROR_BULLET_LIST)
			$listMode = FORM_ERROR_FLOW;
		// custom header text
		if (!TypeUtils::isNull($headerText, TRUE)) {
			if (!empty($headerText))
				$headerText = (!empty($headerStyle) ? sprintf("<div class='%s'>%s</div>", $headerStyle, $headerText) : $headerText . '<br>');
		}
		// default header text
		else {
			$headerText = PHP2Go::getLangVal('ERR_FORM_ERRORS_SUMMARY');
			$headerText = (!empty($headerStyle) ? sprintf("<div class='%s'>%s</div>", $headerStyle, $headerText) : $headerText . '<br>');
		}
		$this->errorStyle = array('class' => $class, 'list_mode' => $listMode, 'header_text' => $headerText);
	}

	/**
	 * Set display options of help messages
	 *
	 * Accepted options:
	 * # popup_attrs: attributes for the Overlib popup
	 * # popup_icon: help icon
	 * # text_style: CSS class for inline help messages
	 *
	 * @link http://www.bosrup.com/web/overlib/?Command_Reference
	 * @param int $mode Display mode ({@link FORM_HELP_INLINE} or {@link FORM_HELP_POPUP})
	 * @param array $options Configuration options
	 */
	function setHelpDisplayOptions($mode, $options=array()) {
		if ($mode == FORM_HELP_INLINE || $mode == FORM_HELP_POPUP)
			$this->helpOptions = array_merge((array)$options, array('mode' => $mode));
	}

	/**
	 * Enable/disable highlight of access keys inside component labels
	 *
	 * @param bool $setting Enable/disable
	 */
	function setAccessKeyHighlight($setting) {
		$this->accessKeyHighlight = (bool)$setting;
	}

	/**
	 * Enable read-only mode on this form
	 */
	function isReadonly() {
    	$this->readonly = TRUE;
	}

	/**
	 * Create/replace a variable
	 *
	 * Variables are declared inside some special nodes and attributes of
	 * the XML specification. They can be resolved from the global scope
	 * (request, session, cookies, registry) or can be set manually through
	 * this method.
	 *
	 * @param string $name Name
	 * @param mixed $value Value
	 */
	function setVariable($name, $value) {
		if (isset($this->variables[$name]))
			$this->variables[$name]['value'] = $value;
		else
			$this->variables[$name] = array(
				'value' => $value
			);
	}

	/**
	 * Evaluates variables declared inside some special XML elements and attributes
	 *
	 * Tries to resolve variable values from the global scope and from the
	 * set of manually defined variables.
	 *
	 * @param string $source Text node or attribute value
	 * @return string Value after variables resolution
	 */
	function resolveVariables($source) {
		static $Stmt;
		if (!isset($Stmt)) {
			$Stmt = new Statement();
			$Stmt->setVariablePattern('~', '~');
			$Stmt->setShowUnassigned();
		}
		$Stmt->setStatement($source);
		if (!$Stmt->isEmpty()) {
			foreach ($Stmt->variables as $name => $variable) {
				if (isset($this->variables[$name])) {
					if (isset($this->variables[$name]['value'])) {
						$Stmt->bindByName($name, $this->variables[$name]['value'], FALSE);
					} elseif (!$Stmt->bindFromRequest($name, FALSE, @$this->variables[$name]['search'])) {
						if (isset($this->variables[$name]['default'])) {
							$Stmt->bindByName($name, $this->variables[$name]['default'], FALSE);
						}
					}
				} else {
					$Stmt->bindFromRequest($name, FALSE);
				}
			}
		}
		return $Stmt->getResult();
	}

	/**
	 * Get all field names
	 *
	 * @return array
	 */
	function getFieldNames() {
		if (!$this->formConstruct)
			$this->processXml();
		return array_keys($this->fields);
	}

	/**
	 * Get all form fields
	 *
	 * @return array
	 */
	function &getFields() {
		if (!$this->formConstruct)
			$this->processXml();
		return $this->fields;
	}

	/**
	 * Get a field by name or path in the XML tree
	 *
	 * Example:
	 * <code>
	 * /* XML specification {@*}
	 * <form>
	 *   <section id="data" name="Data">
	 *     <editfield name="code"/>
	 *   </section>
	 * </form>
	 * /* 2 different ways to get a reference to the field named "code" {@*}
	 * $name =& $form->getField('code');
	 * $name =& $form->getField('data.code');
	 * </code>
	 *
	 * Returns NULL when the field doesn't exist.
	 *
	 * @param string $fieldPath Field name or path
	 * @return FormField|NULL
	 */
	function &getField($fieldPath) {
		if (!$this->formConstruct)
			$this->processXml();
		$result = NULL;
		// check if we received a field path
		$fieldSplitted = explode('.', $fieldPath);
		if (sizeof($fieldSplitted) > 1) {
			// get first section ID
			$sectionId = $fieldSplitted[0];
			if (!isset($this->sections[$sectionId]))
				return $result;
			$section = $this->sections[$sectionId];
			// get inner subsections
			for ($i=1,$s=sizeof($fieldSplitted)-1; $i<$s; $i++) {
				$section = $section->getSubSection($fieldSplitted[$i]);
				if (TypeUtils::isNull($section))
					return $result;
			}
			// get the field
			$result =& $section->getField($fieldSplitted[sizeof($fieldSplitted)-1]);
		// a simple file name was requested
		} else {
			if (array_key_exists($fieldPath, $this->fields))
				$result =& $this->fields[$fieldPath];
		}
		return $result;
	}

	/**
	 * Get a button by name
	 *
	 * Returns NULL when the button doesn't exist.
	 *
	 * @param string $name Button name
	 * @return FormButton|NULL
	 */
	function &getButton($name) {
		if (!$this->formConstruct)
			$this->processXml();
		$result = NULL;
		if (isset($this->buttons[$name]))
			$result =& $this->buttons[$name];
		return $result;
	}

	/**
	 * Get a hash array containing the form's submitted values
	 *
	 * Returns an empty array if the form is not posted.
	 *
	 * @return array
	 */
	function getSubmittedValues() {
		return ($this->isPosted() ? $this->submittedValues : array());
	}

	/**
	 * Get all form validation errors
	 *
	 * @param string $glue Glue string to be used to join the errors list
	 * @return string|array
	 */
	function getFormErrors($glue=NULL) {
		if (empty($this->formErrors))
			return FALSE;
		if (!TypeUtils::isNull($glue))
			return implode($glue, $this->formErrors);
		return $this->formErrors;
	}

	/**
	 * Register one or more validation errors
	 *
	 * @param string|array $errors Error(s)
	 */
	function addErrors($errors) {
		if (TypeUtils::isArray($errors))
			$this->formErrors = array_merge($this->formErrors, $errors);
		else
			$this->formErrors[] = $errors;
	}

	/**
	 * Check if the form is posted
	 *
	 * @return bool
	 */
	function isPosted() {
		if (!isset($this->isPosted)) {
			if (HttpRequest::method() == $this->formMethod) {
				$signature = HttpRequest::getVar(FORM_SIGNATURE, strtolower($this->formMethod));
				if ($signature !== NULL && $signature == $this->getSignature())
					$this->isPosted = TRUE;
				else
					$this->isPosted = FALSE;
			} else {
				$this->isPosted = FALSE;
			}
		}
		return $this->isPosted;
	}

	/**
	 * Check if the form is valid
	 *
	 * Performs validation on all form fields, using the isValid() method
	 * of the {@link FormField} class. Collects all error messages found.
	 *
	 * @return bool
	 */
	function isValid() {
		if ($this->isPosted()) {
			if (!$this->formConstruct)
				$this->processXml();
			$result = TRUE;
			$keys = array_keys($this->fields);
			foreach ($keys as $name) {
				$Field =& $this->fields[$name];
				$result &= $Field->isValid();
			}
			$result = TypeUtils::toBoolean($result);
			if ($result === FALSE) {
				$this->addErrors(Validator::getErrors());
				Validator::clearErrors();
			}
			return ($result);
		}
		return FALSE;
	}

	/**
	 * Prepares the form to be rendered
	 *
	 * @uses Form::resolveVariables()
	 */
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			if (!$this->formConstruct)
				$this->processXml();
			$this->backUrl = $this->resolveVariables($this->backUrl);
			$this->formAction = $this->resolveVariables($this->formAction);
			$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form.js');
		}
	}

	/**#@+
	 * Must be implemented by child classes
     *
     * @abstract
     */
	function getContent() {
	}
	function display() {
	}
	/**#@-*/

	/**
	 * Protect against duplicated section IDs in the same form
	 *
	 * @param string $formName Form name
	 * @param string $sectionId Section ID
	 * @static
	 */
	function verifySectionId($formName, $sectionId) {
		static $sections;
		if (!isset($sections) || !isset($sections[$formName])) {
			$sections = array($formName => array($sectionId));
		} else {
			if (in_array($sectionId, $sections[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_SECTION', array($sectionId, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$sections[$formName][] = $sectionId;
			}
		}
	}

	/**
	 * Protect against duplicated field names in the same form
	 *
	 * @param string $formName Form name
	 * @param string $fieldName Field name
	 * @static
	 */
	function verifyFieldName($formName, $fieldName) {
		static $fields;
		if (!isset($fields) || !isset($fields[$formName])) {
			$fields = array($formName => array($fieldName));
		} else {
			if (in_array($fieldName, $fields[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_FIELD', array($fieldName, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$fields[$formName][] = $fieldName;
			}
		}
	}

	/**
	 * Protect against duplicated button names in the same form
	 *
	 * @param string $formName Form name
	 * @param string $btnName Button name
	 * @static
	 */
	function verifyButtonName($formName, $btnName) {
		static $buttons;
		if (!isset($buttons) || !isset($buttons[$formName])) {
			$buttons = array($formName => array($btnName));
		} else {
			if (in_array($btnName, $buttons[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_BUTTON', array($btnName, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$buttons[$formName][] = $btnName;
			}
		}
	}

	/**
	 * Renders the form validation script block
	 *
	 * @access protected
	 */
	function buildScriptCode() {
		if (!empty($this->validatorCode) || !empty($this->beforeValidateCode) || array_key_exists('VALIDATEFUNC', (array)$this->rootAttrs)) {
			$instance = $this->formName . '_validator';
			$script = "\t{$instance} = new FormValidator('{$this->formName}');\n";
			// error summary settings
			$summaryOptions = sprintf("%s, %s, %s, \"%s\"",
				$this->clientErrorOptions['mode'],
				(isset($this->clientErrorOptions['placeholder']) ? "\$('{$this->clientErrorOptions['placeholder']}')" : 'null'),
				(isset($this->errorStyle['list_mode']) ? $this->errorStyle['list_mode'] : FORM_ERROR_FLOW),
				(isset($this->errorStyle['header_text']) ? $this->errorStyle['header_text'] : PHP2Go::getLangVal('ERR_FORM_ERRORS_SUMMARY'))
			);
			$script .= "\t{$instance}.setSummaryOptions({$summaryOptions});\n";
			// validators
			if (!empty($this->validatorCode))
				$script .= $this->validatorCode;
			// transformation functions
			if (!empty($this->beforeValidateCode)) {
				$script .= "\t{$instance}.onBeforeValidate = function(validator, frm) {\n";
				$script .= $this->beforeValidateCode;
				$script .= "\t};\n";
			}
			// auxiliary validation function
			if (array_key_exists('VALIDATEFUNC', (array)$this->rootAttrs)) {
				$matches = array();
				$validateFunc = trim($this->rootAttrs['VALIDATEFUNC']);
				if (preg_match("~^(\w+)(\((.*)\))?$~", $validateFunc, $matches)) {
					if (@$matches[3])
						$script .= "\t{$instance}.onAfterValidate = function(validator) { return {$validateFunc}; };\n";
					else
						$script .= "\t{$instance}.onAfterValidate = {$matches[1]};\n";
				}
			}
			$script .= "\t{$instance}.setup();";
			$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'validator.js');
			$this->Document->addScriptCode($script, 'Javascript', SCRIPT_END);
		}
	}

	/**
	 * Collects sections, fields and buttons from the parsed XML tree
	 *
	 * @access protected
	 */
	function processXml() {
		$this->formConstruct = TRUE;
		$xmlRoot =& $this->XmlDocument->getRoot();
		if ($xmlRoot->hasChildren()) {
			$childrenCount = $xmlRoot->getChildrenCount();
			for ($i=0; $i<$childrenCount; $i++) {
				$node = $xmlRoot->getChild($i);
				if ($node->getTag() == 'VARIABLE') {
					if ($node->hasAttribute('NAME')) {
						$attrs = $node->getAttributes();
						$name = $attrs['NAME'];
						$variable = array(
							'default' => @$attrs['DEFAULT'],
							'search' => @$attrs['SEARCHORDER']
						);
						if (!isset($this->variables[$name]))
							$this->variables[$name] = $variable;
						else
							$this->variables[$name] = array_merge($this->variables[$name], $variable);
					}
				} elseif ($node->getTag() == 'SECTION') {
					if ($node->hasChildren()) {
						$FormSection =& $this->_createSection($node);
						if ($FormSection->isVisible())
							$this->sections[$FormSection->getId()] =& $FormSection;
					}
				}
			}
		}
		// process postback fields
		if (!empty($this->postbackFields)) {
			foreach ($this->postbackFields as $name) {
				$Field =& $this->fields[$name];
				if (!$Field->dataBind)
					$Field->onDataBind();
			}
			import('php2go.util.service.ServiceJSRS');
			$Service =& ServiceJSRS::getInstance();
			$Service->handleRequest();
		}
	}

	/**
	 * Creates a FormSection object from a section XML node
	 *
	 * @param XmlNode $xmlNode Section node
	 * @return FormSection
	 * @access private
	 */
	function &_createSection($xmlNode) {
		$FormSection = new FormSection($this);
		$FormSection->onLoadNode($xmlNode->getAttributes(), array());
		if ($FormSection->isVisible()) {
			$childrenCount = $xmlNode->getChildrenCount();
			for ($i=0; $i<$childrenCount; $i++) {
				$child = $xmlNode->getChild($i);
				if ($child->getName() == '#cdata-section')
					continue;
				// inner section
				if ($child->getTag() == 'CONDSECTION') {
					$child->setAttribute('CONDITION', 'T');
					$this->_createSubSection($child, $FormSection);
				// buttons group
				} else if ($child->getTag() == 'BUTTONS') {
					$this->_createButtonGroup($child, $FormSection);
				// button
				} else if ($child->getTag() == 'BUTTON') {
					$this->_createButton($child, $FormSection);
				// field
				} else {
					$this->_createField($child, $FormSection);
				}
			}
		}
		return $FormSection;
	}

	/**
	 * Creates a subsection from a XML condsection node
	 *
	 * @param XmlNode $xmlNode Section node
	 * @param FormSection &$parentSection Parent section
	 * @access private
	 */
	function _createSubSection($xmlNode, &$parentSection) {
		$parentNode =& $xmlNode->getParentNode();
		$FormSection = new FormSection($this);
		$FormSection->onLoadNode($xmlNode->getAttributes(), $parentNode->getAttributes());
		if ($FormSection->isVisible()) {
			if ($xmlNode->hasChildren()) {
				$parentSection->addChild($FormSection);
				$childrenCount = $xmlNode->getChildrenCount();
				for ($i=0; $i<$childrenCount; $i++) {
					$child = $xmlNode->getChild($i);
					if ($child->getName() == '#cdata-section')
						continue;
					// inner section
					if ($child->getTag() == 'CONDSECTION') {
						$child->setAttribute('CONDITION', 'T');
						$this->_createSubSection($child, $FormSection);
					// buttons group
					} else if ($child->getTag() == 'BUTTONS') {
						$this->_createButtonGroup($child, $FormSection);
					// button
					} else if ($child->getTag() == 'BUTTON') {
						$this->_createButton($child, $FormSection);
					// field
					} else {
						$this->_createField($child, $FormSection);
					}
				}
			}
		}
	}

	/**
	 * Creates a group of buttons from a XML buttons node
	 *
	 * @param XmlNode $buttons Buttons node
	 * @param FormSection &$parentSection Parent section
	 * @access private
	 */
	function _createButtonGroup($buttons, &$parentSection) {
		if ($parentSection->isVisible()) {
			$buttonGroup = array();
			$childrenCount = $buttons->getChildrenCount();
			for ($i=0; $i<$childrenCount; $i++) {
				$Node = $buttons->getChild($i);
				if ($Node->getTag() == 'BUTTON') {
					$obj = new FormButton($this);
					$obj->onLoadNode($Node->getAttributes(), $Node->getChildrenTagsArray());
					$this->buttons[$obj->getName()] =& $obj;
					$buttonGroup[] =& $obj;
					unset($obj);
				}
			}
			if (!empty($buttonGroup)) {
				$parentSection->addChild($buttonGroup);
			}
		}
	}

	/**
	 * Creates a FormButton from a XML button node
	 *
	 * @param XmlNode $button Button node
	 * @param FormSection &$parentSection Parent section
	 * @access private
	 */
	function _createButton($button, &$parentSection) {
		 if ($parentSection->isVisible()) {
		 	$obj = new FormButton($this);
		 	$obj->onLoadNode($button->getAttributes(), $button->getChildrenTagsArray());
		 	$this->buttons[$obj->getName()] =& $obj;
		 	$parentSection->addChild($obj);
		 }
	}

	/**
	 * Creates a FormField from a XML field node
	 *
	 * @param XmlNode $field Field node
	 * @param FormSection &$parentSection Parent section
	 * @access private
	 */
	function _createField($field, &$parentSection) {
		$fieldClassName = NULL;
 		if ($parentSection->isVisible()) {
			switch($field->getTag()) {
				case 'AUTOCOMPLETEFIELD' : $fieldClassName = 'AutoCompleteField'; break;
				case 'CAPTCHAFIELD' : $fieldClassName = 'CaptchaField'; break;
				case 'COLORPICKERFIELD' : $fieldClassName = 'ColorPickerField'; break;
				case 'CHECKFIELD' : $fieldClassName = 'CheckField'; break;
				case 'CHECKGROUP' : $fieldClassName = 'CheckGroup'; break;
				case 'COMBOFIELD' : $fieldClassName = 'ComboField'; break;
				case 'DATAGRID' : $fieldClassName = 'DataGrid'; break;
				case 'DATEPICKERFIELD' : $fieldClassName = 'DatePickerField'; break;
				case 'DBCHECKGROUP' : $fieldClassName = 'DbCheckGroup'; break;
				case 'DBRADIOFIELD' : $fieldClassName = 'DbRadioField'; break;
				case 'EDITFIELD' : $fieldClassName = 'EditField'; break;
				case 'EDITORFIELD' : $fieldClassName = 'EditorField'; break;
				case 'EDITSEARCHFIELD' : $fieldClassName = 'EditSearchField'; break;
				case 'EDITSELECTIONFIELD' : $fieldClassName = 'EditSelectionField'; break;
				case 'FILEFIELD' : $fieldClassName = 'FileField'; break;
				case 'HIDDENFIELD' : $fieldClassName = 'HiddenField'; break;
				case 'LOOKUPCHOICEFIELD' : $fieldClassName = 'LookupChoiceField'; break;
				case 'LOOKUPFIELD' : $fieldClassName = 'LookupField'; break;
				case 'LOOKUPSELECTIONFIELD' : $fieldClassName = 'LookupSelectionField'; break;
				case 'MEMOFIELD' : $fieldClassName = 'MemoField'; break;
				case 'MULTICOLUMNLOOKUPFIELD' : $fieldClassName = 'MultiColumnLookupField'; break;
				case 'PASSWDFIELD' : $fieldClassName = 'PasswdField'; break;
				case 'RADIOFIELD' : $fieldClassName = 'RadioField'; break;
				case 'RANGEFIELD' : $fieldClassName = 'RangeField'; break;
				case 'TEXTFIELD' : $fieldClassName = 'TextField'; break;
				default : PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_INVALID_FIELDTYPE', $field->getTag()), E_USER_ERROR, __FILE__, __LINE__); break;
			}
			if (!TypeUtils::isNull($fieldClassName)) {
				import("php2go.form.field.{$fieldClassName}");
				$obj = new $fieldClassName($this);
				$obj->onLoadNode($field->getAttributes(), $field->getChildrenTagsArray());
				// add the field in the parent section
				$parentSection->addChild($obj);
				// register the field in the form
				$this->fields[$obj->getName()] =& $obj;
			}
		}
	}

	/**
	 * Parses presentation and layout settings from the
	 * global configuration settings
	 *
	 * Global settings are applied to all PHP2Go forms, and should
	 * be defined using the $P2G_USER_CFG['FORMS'] configuration
	 * key.
	 *
	 * @param array $settings Global settings
	 * @access protected
	 */
	function _loadGlobalSettings($settings) {
		(isset($settings['SECTION_REQUIRED_TEXT'])) && $this->requiredText = $settings['SECTION_REQUIRED_TEXT'];
		(isset($settings['SECTION_REQUIRED_COLOR'])) && $this->requiredColor = $settings['SECTION_REQUIRED_COLOR'];
		(isset($settings['INPUT_STYLE'])) && $this->inputStyle = $settings['INPUT_STYLE'];
		(isset($settings['LABEL_STYLE'])) && $this->labelStyle = $settings['LABEL_STYLE'];
		(isset($settings['BUTTON_STYLE'])) && $this->buttonStyle = $settings['BUTTON_STYLE'];
		(array_key_exists('ACCESSKEY_HIGHLIGHT', $settings)) && $this->accessKeyHighlight = (bool)$settings['ACCESSKEY_HIGHLIGHT'];
		if (isset($settings['HELP_MODE'])) {
			$mode = @constant($settings['HELP_MODE']);
			if (!TypeUtils::isNull($mode))
				$this->setHelpDisplayOptions($mode, TypeUtils::toArray(@$settings['HELP_OPTIONS']));
		}
		if (isset($settings['ERRORS'])) {
			$mode = @constant($settings['ERRORS']['LIST_MODE']);
			$headerText = (isset($settings['ERRORS']['HEADER_TEXT']) ? resolveI18nEntry($settings['ERRORS']['HEADER_TEXT']) : NULL);
			$this->setErrorStyle(@$settings['ERRORS']['STYLE'], $mode, $headerText, @$settings['ERRORS']['HEADER_STYLE']);
		}
	}

	/**
	 * Loads presentation and layout settings from the XML specification
	 *
	 * Parses <b>form</b>, <b>style</b> and <b>errors</b> XML nodes.
	 *
	 * @param string $tag Node name
	 * @param array $attrs Node attributes
	 * @access protected
	 */
	function _loadXmlSettings($tag, $attrs) {
		switch ($tag) {
			case 'FORM' :
				(isset($attrs['METHOD'])) && ($this->setFormMethod($attrs['METHOD']));
				(isset($attrs['ACTION'])) && ($this->formAction = $attrs['ACTION']);
				(isset($attrs['TARGET'])) && ($this->actionTarget = $attrs['TARGET']);
				(isset($attrs['BACKURL'])) && ($this->backUrl = $attrs['BACKURL']);
				(array_key_exists('ACCESSKEYHIGHLIGHT', $attrs)) && ($this->accessKeyHighlight = resolveBooleanChoice($attrs['ACCESSKEYHIGHLIGHT']));
				break;
			case 'STYLE' :
				(isset($attrs['INPUT'])) && ($this->inputStyle = $attrs['INPUT']);
				(isset($attrs['LABEL'])) && ($this->labelStyle = $attrs['LABEL']);
				(isset($attrs['BUTTON'])) && ($this->buttonStyle = $attrs['BUTTON']);
				break;
			case 'ERRORS' :
				$mode = @constant($attrs['LISTMODE']);
				$headerText = (isset($attrs['HEADERTEXT']) ? resolveI18nEntry($attrs['HEADERTEXT']) : NULL);
				$this->setErrorStyle(@$attrs['STYLE'], $mode, $headerText, @$attrs['HEADERSTYLE']);
				break;
			case 'HELP' :
				$mode = @constant($attrs['MODE']);
				$options = array();
				if (isset($attrs['OPTIONS']))
					parse_str($attrs['OPTIONS'], $options);
				$this->setHelpDisplayOptions($mode, $options);
				break;
		}
	}
}
?>