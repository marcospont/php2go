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

/**
 * Event listeners that execute Javascript code
 */
define('FORM_EVENT_JS', 'JS');
/**
 * Event listeners that perform JSRS requests
 */
define('FORM_EVENT_JSRS', 'JSRS');
/**
 * Event listeners that perform AJAX calls
 */
define('FORM_EVENT_AJAX', 'AJAX');

/**
 * Builds form event listeners
 *
 * The forms XML specification allows to define event listeners for fields
 * and/or buttons. This is an organized and simple way to bind Javascript
 * function calls with DOM events of form elements.
 *
 * Examples:
 * <code>
 * <editfield name="product_code" label="Product Code" size="20" maxlength="20">
 *   <listener type="JS" event="onFocus" action="myFunction()"/>
 *   <listener type="JS" event="onKeyDown"><![CDATA[
 *     event = $EV(event);
 *     if (k == 13) {
 *       callAnotherFunction();
 *       event.stop();
 *     }
 *   ]]></listener>
 *   <listener
 *       type="JSRS" event="onChange" file="product_changed.php"
 *       remote="productChanged" callback="productChangedResult"
 *       params="this.value" debug="F"
 *   />
 * </editfield>
 * </code>
 *
 * @package form
 * @uses FormEventListener
 * @uses FormRule
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormEventListener extends PHP2Go
{
	/**
	 * Type of the listener
	 *
	 * @var string
	 */
	var $type;

	/**
	 * Event to which the listener is bound
	 *
	 * @var string
	 */
	var $eventName;

	/**
	 * Action of the listener
	 *
	 * @var string
	 */
	var $action;

	/**
	 * Evalutes if the listener should be triggered upon page load
	 *
	 * @var string
	 */
	var $autoDispatchIf;

	/**
	 * Listener's function body
	 *
	 * @var unknown_type
	 */
	var $functionBody;

	/**
	 * Whether this listener handles a custom event
	 *
	 * @var bool
	 */
	var $custom = FALSE;

	/**
	 * Is the listener properties valid?
	 *
	 * @var bool
	 * @access private
	 */
	var $_valid = FALSE;

	/**
	 * Listener's owner
	 *
	 * @var object Component
	 * @access private
	 */
	var $_Owner = NULL;

	/**
	 * Option index to which the listener is bound
	 *
	 * @var int
	 * @access private
	 */
	var $_ownerIndex;

	/**
	 * Class constructor
	 *
	 * @param string $type Type ({@link FORM_EVENT_JS}, {@link FORM_EVENT_JSRS} or {@link FORM_EVENT_AJAX})
	 * @param string $eventName Event name
	 * @param string $action Action
	 * @param string $autoDispatchIf Auto dispatch expression
	 * @param string $functionBody Function body
	 * @return FormEventListener
	 */
	function FormEventListener($type, $eventName, $action='', $autoDispatchIf='', $functionBody='') {
		parent::PHP2Go();
		$this->type = $type;
		$this->eventName = $eventName;
		$this->action = $action;
		$this->autoDispatchIf = $autoDispatchIf;
		$this->functionBody = $functionBody;
	}

	/**
	 * Factory method used to translate a listener node from the
	 * XML specification of a form into a listener object, according
	 * to the node's properties
	 *
	 * @param XmlNode $Node
	 * @return FormEventListener
	 * @static
	 */
	function &fromNode($Node) {
		$type = trim($Node->getAttribute('TYPE'));
		$eventName = trim($Node->getAttribute('EVENT'));
		$autoDispatchIf = trim($Node->getAttribute('AUTODISPATCHIF'));
		$custom = resolveBooleanChoice($Node->getAttribute('CUSTOM'));
		switch ($type) {
			case FORM_EVENT_JS :
				$action = trim($Node->getAttribute('ACTION'));
				$functionBody = $Node->getData();
				$Listener = new FormEventListener(FORM_EVENT_JS, $eventName, $action, $autoDispatchIf, $functionBody, $custom);
				break;
			case FORM_EVENT_JSRS :
				import('php2go.form.listener.FormJSRSListener');
				$remoteFile = trim($Node->getAttribute('FILE'));
				$remoteFunction = trim($Node->getAttribute('REMOTE'));
				$callback = trim($Node->getAttribute('CALLBACK'));
				$params = trim($Node->getAttribute('PARAMS'));
				$debug = resolveBooleanChoice($Node->getAttribute('DEBUG'));
				$Listener = new FormJSRSListener($eventName, $autoDispatchIf, $remoteFile, $remoteFunction, $callback, $params, $debug, $custom);
				break;
			case FORM_EVENT_AJAX :
				import('php2go.form.listener.FormAjaxListener');
				$url = trim($Node->getAttribute('URL'));
				$class = trim($Node->getAttribute('CLASS'));
				$formSubmit = resolveBooleanChoice($Node->getAttribute('FORMSUBMIT'));
				$args = array();
				$argsBody = $Node->getData();
				$children = $Node->getChildrenTagsArray();
				$params = ($children['PARAM'] ? (is_array($children['PARAM']) ? $children['PARAM'] : array($children['PARAM'])) : array());
				foreach ($params as $idx => $ParamNode) {
					$args[$ParamNode->getAttribute('NAME')] = $ParamNode->getData();
				}
				$Listener = new FormAjaxListener($eventName, $autoDispatchIf, $url, $class, $formSubmit, $args, $argsBody, $custom);
				break;
			default :
				$Listener = NULL;
		}
		return $Listener;
	}

	/**
	 * Get the rule's owner field
	 *
	 * @return FormField
	 */
	function &getOwner() {
		return $this->_Owner;
	}
	/**
	 * Get the listener's owner form
	 *
	 * @return Form
	 */
	function &getOwnerForm() {
		$result = NULL;
		if (is_object($this->_Owner))
			$result =& $this->_Owner->getOwnerForm();
		return $result;
	}
	/**
	 * Set listener's owner component
	 *
	 * @param Component &$Owner Owner component (form field or form button)
	 * @param int $ownerIndex Option index to which the listener must be bound
	 */
	function setOwner(&$Owner, $ownerIndex=NULL) {
		$this->_Owner =& $Owner;
		$this->_ownerIndex = $ownerIndex;
		if (TypeUtils::isInstanceOf($Owner, 'FormField') && in_array($this->eventName, $Owner->customEvents))
			$this->custom = TRUE;
	}

	/**
	 * Builds the listener's action
	 *
	 * When an <b>action</b> is provided, it is returned without
	 * modifications. When a <b>Javascript function body</b> is used,
	 * the body is enclosed in a function definition appended in the
	 * end of the document's body and the returned value is a call
	 * to the generated function.
	 *
	 * @param int $targetIndex Option index to which the listener is bound
	 * @return string
	 */
	function getScriptCode($targetIndex=NULL) {
		$Form =& $this->_Owner->getOwnerForm();
		if (!empty($this->functionBody)) {
			$funcName = PHP2Go::generateUniqueId(preg_replace("~[^\w]+~", "", $this->_Owner->getName()) . ucfirst($this->eventName));
			$funcBody = rtrim(ltrim($this->functionBody, "\r\n"));
			if (preg_match("/^([\t]+)/", $funcBody, $matches))
				$funcBody = preg_replace("/^\t{" . strlen($matches[1]) . "}/m", "\t\t", $funcBody);
			if ($this->custom) {
				$Form->Document->addScriptCode("\tfunction {$funcName}(obj, args) {\n{$funcBody}\n\t}", 'Javascript', SCRIPT_END);
				$this->action = "{$funcName}(this, args)";
			} else {
				$funcBody = preg_replace('/\bthis\b/', 'element', $funcBody);
				$Form->Document->addScriptCode("\tfunction {$funcName}(element, event) {\n{$funcBody}\n\t}", 'Javascript', SCRIPT_END);
				$this->action = "{$funcName}(this, event)";
			}
		} else {
			$this->action = preg_replace("/;\s*$/", '', $this->action);
		}
		$this->renderAutoDispatch($targetIndex);
		return $this->action;
	}

	/**
	 * Verify if the listener is valid
	 *
	 * Calls the {@link validate()} method, which normally will ensure
	 * all mandatory properties are filled.
	 *
	 * @access protected
	 * @return bool
	 */
	function isValid() {
		if ($this->_valid == TRUE)
			return $this->_valid;
		if (TypeUtils::isInstanceOf($this->_Owner, 'FormField') || TypeUtils::isInstanceOf($this->_Owner, 'FormButton')) {
			$this->_valid = $this->validate();
			if (!$this->_valid)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_WRONG_LISTENER', $this->__toString()), E_USER_ERROR, __FILE__, __LINE__);
			return $this->_valid;
		} else {
			$this->_valid = FALSE;
			return $this->_valid;
		}
	}

	/**
	 * Configure listener's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
	}

	/**
	 * Validate listener's properties
	 *
	 * @access protected
	 * @return bool
	 */
	function validate() {
		return (!empty($this->eventName) && (!empty($this->action) || !empty($this->functionBody)));
	}

	/**
	 * Renders the auto dispatch script block
	 *
	 * @param int $targetIndex Option index to which the listener is bound
	 * @access protected
	 */
	function renderAutoDispatch($targetIndex=NULL) {
		if (!$this->custom && !empty($this->autoDispatchIf)) {
			$Form =& $this->_Owner->getOwnerForm();
			if (isset($this->_ownerIndex))
				$fldRef = sprintf("$('%s_%s')", $this->_Owner->getName(), $this->_ownerIndex);
			elseif ($targetIndex !== NULL)
				$fldRef = sprintf("$('%s_%s')", $this->_Owner->getName(), $targetIndex);
			else
				$fldRef = sprintf("$('%s').elements['%s']", $Form->formName, $this->_Owner->getName());
			$dispatchTest = preg_replace('/\bthis\b/', 'fld', $this->autoDispatchIf);
			$dispatchAction = preg_replace(array('/\bthis\b/', '/, event/'), array('fld', ', null'), $this->action);
			$Form->Document->addOnloadCode(sprintf("var fld = %s; if (%s) { %s; }", $fldRef, $dispatchTest, $dispatchAction));
		}
	}

	/**
	 * Builds a string representation of the listener
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
		if (!empty($this->action))
			$info .= "; {$this->action}";
		$info .= ']';
		return $info;
	}
}
?>