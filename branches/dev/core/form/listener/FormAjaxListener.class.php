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

import('php2go.net.HttpRequest');

/**
 * Builds AJAX-based form event listeners
 *
 * Based on the provided arguments, this class can bind an AJAX request
 * with an event of a form component.
 *
 * The properties of the AJAX request must be defined in the form
 * XML specification, in the #text node of the listener element,
 * or inside "param" child nodes.
 *
 * Example:
 * <code>
 * <lookupfield name="states" label="States" width="200">
 *   <datasource>
 *     <keyfield>state_id</keyfield>
 *     <displayfield>name</displayfield>
 *     <lookuptable>state</lookuptable>
 *     <orderby>name</orderby>
 *   </datasource>
 *   <listener type="AJAX" event="onChange" url="get_cities.php">
 *     <param name="method">post</param>
 *     <param name="async">true</param>
 *     <param name="params">{state_id:$F('states').getValue()}</param>
 *     <param name="onSuccess"><![CDATA[
 *       var cities = $F('cities');
 *       cities.importOptions(response.responseText);
 *       cities.focus();
 *     ]]></param>
 *   </listener>
 * </lookupfield>
 * <lookupfield name="cities" label="Cities" width="200" first="Choose a state first"/>
 * </code>
 *
 * @package form
 * @subpackage listener
 * @uses HttpRequest
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormAjaxListener extends FormEventListener
{
	/**
	 * AJAX request URL
	 *
	 * @var string
	 * @access private
	 */
	var $url;

	/**
	 * AJAX request class
	 *
	 * @var string
	 * @access private
	 */
	var $class;

	/**
	 * If TRUE, indicates this listener must control
	 * the submission of the form in which it's inserted
	 *
	 * @var bool
	 * @access private
	 */
	var $formSubmit;

	/**
	 * AJAX parameters
	 *
	 * @var array
	 * @access private
	 */
	var $params;

	/**
	 * Function body, written in the #text child node of the listener,
	 * representing a JS function body that defines AJAX parameters
	 *
	 * @var string
	 * @access private
	 */
	var $paramsFuncBody;

	/**
	 * Class constructor
	 *
	 * @param string $eventName Event name
	 * @param string $autoDispatchIf Evaluates if listener should be dispatched automatically upon page load
	 * @param string $url AJAX request URL
	 * @param string $class AJAX request class
	 * @param bool $formSubmit Whether this listener submits its form
	 * @param array $params AJAX parameters
	 * @param string $paramsFuncBody Function body that defines AJAX parameters
	 * @return FormAjaxListener
	 */
	function FormAjaxListener($eventName, $autoDispatchIf='', $url='', $class='', $formSubmit=FALSE, $params=array(), $paramsFuncBody='') {
		parent::FormEventListener(FORM_EVENT_AJAX, $eventName, '', $autoDispatchIf);
		$this->url = $url;
		$this->class = (!empty($class) ? $class : 'AjaxRequest');
		$this->formSubmit = $formSubmit;
		$this->params = (array)$params;
		$this->paramsFuncBody = $paramsFuncBody;
	}

	/**
	 * Overrides the parent class implementation in order to generate
	 * the function that performs the AJAX request and add it in the
	 * end of the document's body
	 *
	 * @param int $targetIndex Index, when bound to a group option invidually
	 * @return string Function call
	 */
	function getScriptCode($targetIndex=NULL) {
		$Form =& $this->_Owner->getOwnerForm();
		$Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'ajax.js');
		// is the owner a submit button?
		$isSubmit = (TypeUtils::isInstanceOf($this->_Owner, 'FormButton') && $this->_Owner->getAttribute('TYPE') == 'SUBMIT');
		// form submission
		if ($this->formSubmit || $isSubmit) {
			$this->url = $Form->formAction;
			$this->params['method'] = $Form->formMethod;
			$this->params['form'] = $Form->formName;
		} elseif (empty($this->url)) {
			$this->url = HttpRequest::uri(FALSE);
		}
		// if a listener AJAX is declared inside a button whose type is "SUBMIT",
		// the onsubmit event handler of the form must be overlapped by a call
		// to the Form.ajaxify method
		if ($isSubmit) {
			$Form->Document->addScriptCode(
				"\tForm.ajaxify($('{$Form->formName}'), function() {\n" .
				$this->getParamsScript() .
				"\t\tvar request = new {$this->class}('{$this->url}', params);\n" .
				"\t\trequest.send();\n" .
				"\t});",
			'Javascript', SCRIPT_END);
			return NULL;
		} else {
			$funcName = PHP2Go::generateUniqueId(preg_replace("~[^\w]+~", "", $this->_Owner->getName()) . ucfirst($this->eventName));
			if ($this->custom) {
				$params = $this->getParamsScript();
				$ctorArgs = 'args';
			} else {
				$params = preg_replace('/\bthis\b/', 'element', $this->getParamsScript());
				$ctorArgs = 'element, event';
			}
			$Form->Document->addScriptCode(
				"\tfunction {$funcName}({$ctorArgs}) {\n{$params}" .
				"\t\tvar request = new {$this->class}('{$this->url}', params);\n" .
				"\t\trequest.send();\n" .
				"\t}",
			'Javascript', SCRIPT_END);
			$this->action = "{$funcName}(this, event)";
			parent::renderAutoDispatch($targetIndex);
			return $this->action;
		}
	}

	/**
	 * Build the script code that configures the
	 * parameters of the AJAX request
	 *
	 * @access protected
	 * @return string
	 */
	function getParamsScript() {
		$buf = "";
		if (!empty($this->paramsFuncBody)) {
			$funcBody = rtrim(ltrim($this->paramsFuncBody, "\r\n "));
			$funcBody = $this->_convertTabs($funcBody, 3);
			$buf .= "\t\tvar params = function() {\n{$funcBody}\n\t\t}();\n";
		} else {
			$buf .= "\t\tvar params = {};\n";
		}
		foreach ($this->params as $name => $value) {
			switch ($name) {
				case 'method' :
				case 'contentType' :
				case 'body' :
				case 'form' :
				case 'throbber' :
					$value = trim($value);
					$buf .= "\t\tparams.{$name} = '{$value}';\n";
					break;
				case 'async' :
				case 'params' :
				case 'headers' :
				case 'formFields' :
					$value = trim($value);
					$buf .= "\t\tparams.{$name} = {$value};\n";
					break;
				case 'container' :
					$value = trim($value);
					$buf .= "\t\tparams.{$name} = " . (preg_match("/{.*}/s", $value) ? $value : "'{$value}'") . ";\n";
					break;
				case 'onLoading' :
				case 'onLoaded' :
				case 'onInteractive' :
					$value = trim(rtrim($value), "\n");
					$value = $this->_convertTabs($value, 3);
					$buf .=	"\t\tparams.{$name} = function() {\n" .
							"{$value}\n" .
							"\t\t};\n";
					break;
				case 'onComplete' :
				case 'onSuccess' :
				case 'onFailure' :
				case 'onJSONResult' :
				case 'onXMLResult' :
					$value = trim(rtrim($value), "\n");
					$value = $this->_convertTabs($value, 3);
					$buf .=	"\t\tparams.{$name} = function(response) {\n" .
							"{$value}\n" .
							"\t\t};\n";
					break;
				case 'onException' :
					$value = trim(rtrim($value), "\n");
					$value = $this->_convertTabs($value, 3);
					$buf .=	"\t\tparams.{$name} = function(e) {\n" .
							"{$value}\n" .
							"\t\t};\n";
					break;
			}
		}
		return $buf;
	}

	/**
	 * Configure listener's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		// resolve variables and expressions on the URL attribute
		if (preg_match("/~[^~]+~/", $this->url)) {
			$Form =& $this->getOwnerForm();
			$this->url = $Form->resolveVariables($this->url);
		}
	}

	/**
	 * Validates the listener's properties
	 *
	 * @access protected
	 * @return bool
	 */
	function validate() {
		if (!empty($this->eventName)) {
			if ($this->class == 'AjaxUpdater')
				return (isset($this->params['container']) || isset($this->params['success']));
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Builds a string representation of the AJAX listener
	 *
	 * @return string
	 */
	function __toString() {
		$info = $this->_Owner->getName();
		if (isset($this->_ownerIndex))
			$info .= " [option {$this->_ownerIndex}]";
		$info .= " - [{$this->type}";
		if (!empty($this->eventName))
			$info .= "; {$this->eventName}";
		if (!empty($this->url))
			$info .= "; {$this->url}";
		if (!empty($this->class))
			$info .= "; {$this->class}";
		$info .= ']';
		return $info;
	}

	/**
	 * Normalize tab characters inside a function body or an AJAX param definition
	 *
	 * @param string $str Input string
	 * @param int $n Minimum tab chars on left side
	 * @access private
	 * @return string
	 */
	function _convertTabs($str, $n) {
		$matches = array();
		$tabs = str_repeat("\t", $n);
		preg_match("/^([\t]+)/", $str, $matches);
		return (isset($matches[1]) ? preg_replace("/^\t{" . strlen($matches[1]) . "}/m", $tabs, $str) : $str);
	}
}

?>