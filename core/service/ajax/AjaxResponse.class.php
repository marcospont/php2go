<?php

import('php2go.util.json.JSONEncoder');

/**
 * Ajax response builder
 *
 * This class contains methods that allow to build
 * a sequence of Javascript commands to be executed
 * as the response of an AJAX call.
 *
 * @package service
 * @subpackage ajax
 * @uses JSONEncoder
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AjaxResponse extends PHP2Go
{
	/**
	 * Response commands
	 *
	 * @var array
	 * @access private
	 */
	var $commands = array();
	/**
	 * Response charset
	 *
	 * @var string
	 * @access private
	 */
	var $charset = 'iso-8859-1';

	/**
	 * Class constructor
	 *
	 * @return AjaxResponse
	 */
	function AjaxResponse() {
		parent::PHP2Go();
	}

	/**
	 * Set response charset
	 *
	 * @param string $charset Charset
	 */
	function setCharset($charset) {
		$this->charset = $charset;
	}

	/**
	 * Renders all response commands as a JSON string
	 */
	function render() {
		header("Content-type: application/json; charset={$this->charset}");
		print JSONEncoder::encode(array(
			'commands' => $this->commands
		));
	}

	/**
	 * Defines the value of a form field given its ID
	 *
	 * @param string $id Field ID
	 * @param mixed $value Field value
	 */
	function setValue($id, $value) {
		$this->commands[] = array(
			'cmd' => 'value',
			'id' => $id,
			'arg' => $value
		);
	}

	/**
	 * Defines the value of a field given its name and the form ID
	 *
	 * @param string $formId Form ID
	 * @param string $fieldName Field name
	 * @param mixed $value Field value
	 */
	function setValueByName($formId, $fieldName, $value) {
		$this->commands[] = array(
			'cmd' => 'value',
			'frm' => $formId,
			'fld' => $fieldName,
			'arg' => $value
		);
	}

	/**
	 * Populates a combo box with a given set of options
	 *
	 * @param string $id Combo ID
	 * @param array $options Combo options
	 */
	function fillCombo($id, $options, $clear=TRUE, $value=NULL) {
		$options = (array)$options;
		$this->commands[] = array(
			'cmd' => 'combo',
			'id' => $id,
			'arg' => array(
				'options' => $options,
				'clear' => !!$clear,
				'value' => $value
			)
		);
	}

	/**
	 * Enables one or more form fields
	 *
	 * @param string|array $id Field ID or IDs
	 */
	function enable($id) {
		if (is_array($id)) {
			foreach ($id as $element) {
				$this->commands[] = array(
					'cmd' => 'enable',
					'id' => $element
				);
			}
		} else {
			$this->commands[] = array(
				'cmd' => 'enable',
				'id' => $id
			);
		}
	}

	/**
	 * Enables a field given its name and form ID
	 *
	 * @param string $formId Form ID
	 * @param string $fieldName Field name
	 */
	function enableByName($formId, $fieldName) {
		$this->commands[] = array(
			'cmd' => 'enable',
			'frm' => $formId,
			'fld' => $fieldName
		);
	}

	/**
	 * Disables one or more form fields
	 *
	 * @param string|array $id Field ID or IDs
	 */
	function disable($id) {
		if (is_array($id)) {
			foreach ($id as $element) {
				$this->commands[] = array(
					'cmd' => 'disable',
					'id' => $element
				);
			}
		} else {
			$this->commands[] = array(
				'cmd' => 'disable',
				'id' => $id
			);
		}
	}

	/**
	 * Disables a field given its name and form ID
	 *
	 * @param string $formId Form ID
	 * @param string $fieldName Field name
	 */
	function disableByName($formId, $fieldName) {
		$this->commands[] = array(
			'cmd' => 'disable',
			'frm' => $formId,
			'fld' => $fieldName
		);
	}

	/**
	 * Give focus to a given field
	 *
	 * @param string $id Field ID
	 */
	function focus($id) {
		$this->commands[] = array(
			'cmd' => 'focus',
			'id' => $id
		);
	}

	/**
	 * Give focus to a given field given its name and form ID
	 *
	 * @param string $formId Form ID
	 * @param string $fieldName Field name
	 */
	function focusByName($formId, $fieldName) {
		$this->commands[] = array(
			'cmd' => 'focus',
			'frm' => $formId,
			'fld' => $fieldName
		);
	}

	/**
	 * Clears one or more fields
	 *
	 * @param string $id Field ID or IDs
	 */
	function clear($id) {
		if (is_array($id)) {
			foreach ($id as $element) {
				$this->commands[] = array(
					'cmd' => 'clear',
					'id' => $element
				);
			}
		} else {
			$this->commands[] = array(
				'cmd' => 'clear',
				'id' => $id
			);
		}
	}

	/**
	 * Clears a given field given its name and form ID
	 *
	 * @param string $formId Form ID
	 * @param string $fieldName Field name
	 */
	function clearByName($formId, $fieldName) {
		$this->commands[] = array(
			'cmd' => 'clear',
			'frm' => $formId,
			'fld' => $fieldName
		);
	}

	/**
	 * Shows one or more elements
	 *
	 * @param string|array $id Element ID or IDs
	 */
	function show($id) {
		if (is_array($id)) {
			foreach ($id as $element) {
				$this->commands[] = array(
					'cmd' => 'show',
					'id' => $element
				);
			}
		} else {
			$this->commands[] = array(
				'cmd' => 'show',
				'id' => $id
			);
		}
	}

	/**
	 * Hides one or more elements
	 *
	 * @param string|array $id Element ID or IDs
	 */
	function hide($id) {
		if (is_array($id)) {
			foreach ($id as $element) {
				$this->commands[] = array(
					'cmd' => 'hide',
					'id' => $element
				);
			}
		} else {
			$this->commands[] = array(
				'cmd' => 'hide',
				'id' => $id
			);
		}
	}

	/**
	 * Set one or more attributes of an element
	 *
	 * @param string $id Element ID
	 * @param string|array $attr Attribute name or attributes hash
	 * @param mixed $value Attribute value
	 */
	function setAttribute($id, $attr, $value='') {
		if (!TypeUtils::isHashArray($attr))
			$attrs = array($attr => $value);
		else
			$attrs = $attr;
		$this->commands[] = array(
			'cmd' => 'attr',
			'id' => $id,
			'arg' => $attrs
		);
	}

	/**
	 * Set one or more style properties of an element
	 *
	 * @param string $id Element ID
	 * @param string|array $attr Attribute name or attributes hash
	 * @param mixed $value Attribute value
	 */
	function setStyle($id, $attr, $value='') {
		if (!TypeUtils::isHashArray($attr))
			$attrs = array($attr => $value);
		else
			$attrs = $attr;
		$this->commands[] = array(
			'cmd' => 'style',
			'id' => $id,
			'arg' => $attrs
		);
	}

	/**
	 * Creates a new element in the DOM tree
	 *
	 * Example:
	 * <code>
	 * $response->create('input', array('name' => 'text_box', 'type'=>'text'), NULL, 'my_form');
	 * $response->create('div', array('id' => 'message'), 'Hello World!', JSONEncoder::jsIdentifier('document.body'));
	 * </code>
	 *
	 * @param string $tag Tag name
	 * @param array $attrs Element attributes
	 * @param array $contents Element contents
	 * @param string $parent Parent node
	 */
	function create($tag, $attrs=array(), $contents=NULL, $parent=NULL) {
		$id = consumeArray($attrs, 'id', NULL);
		$styles = consumeArray($attrs, 'style', NULL);
		$this->commands[] = array(
			'cmd' => 'create',
			'id' => $id,
			'arg' => array(
				'tag' => $tag,
				'cont' => $contents,
				'styles' => (TypeUtils::isHashArray($styles) ? $styles : NULL),
				'attrs' => (TypeUtils::isHashArray($attrs) ? $attrs : NULL),
				'parent' => $parent
			)
		);
	}

	/**
	 * Clear the contents of an element
	 *
	 * @param string $id Element ID
	 */
	function clearContents($id) {
		$this->commands[] = array(
			'cmd' => 'clear',
			'id' => $id
		);
	}

	/**
	 * Updates the contents of an element
	 *
	 * @param string $id Element ID
	 * @param string $code Code
	 * @param bool $evalScrits If scripts must be evaluated
	 * @param bool $useDom If DOM must be used to update the contents
	 */
	function updateContents($id, $code, $evalScrits=FALSE, $useDom=FALSE) {
		$this->commands[] = array(
			'cmd' => 'update',
			'id' => $id,
			'arg' => array(
				'code' => $code . '',
				'eval' => !!$evalScrits,
				'dom' => !!$useDom
			)
		);
	}

	/**
	 * Insert content on an element
	 *
	 * @param string $id Element ID
	 * @param string $code Code
	 * @param string $pos Insert position (before, top, after or bottom)
	 * @param bool $evalScripts If scripts must be evaluated
	 */
	function insertContents($id, $code, $pos='bottom', $evalScripts=FALSE) {
		$this->commands[] = array(
			'cmd' => 'insert',
			'id' => $id,
			'arg' => array(
				'code' => $code . '',
				'pos' => strtolower($pos),
				'eval' => !!$evalScripts
			)
		);
	}

	/**
	 * Replace an element of the DOM tree by a given HTML code
	 *
	 * @param string $id Element ID
	 * @param string $code Replace code
	 * @param bool $evalScripts If scripts must be evaluated
	 */
	function replaceContents($id, $code, $evalScripts=FALSE) {
		$this->commands[] = array(
			'cmd' => 'replace',
			'id' => $id,
			'arg' => array(
				'code' => $code . '',
				'eval' => !!$evalScripts
			)
		);
	}

	/**
	 * Removes an element from the tree
	 *
	 * @param string $id Element ID
	 */
	function remove($id) {
		$this->commands[] = array(
			'cmd' => 'remove',
			'id' => $id
		);
	}

	/**
	 * Adds an event handler on an element
	 *
	 * Example:
	 * <code>
	 * $response->addEvent('my_button', 'click', "alert('Hello World!');");
	 * $response->addEvent('my_button', 'click', JSONEncoder::jsIdentifier('function_name'));
	 * </code>
	 *
	 * @param string $id Element ID
	 * @param string $type Event type
	 * @param string $func Function
	 * @param bool $capture Capture flag
	 */
	function addEvent($id, $type, $func, $capture=TRUE) {
		if (is_string($func))
			$func = JSONEncoder::jsFunction($func, array('evt'));
		$this->commands[] = array(
			'cmd' => 'addev',
			'id' => $id,
			'arg' => array(
				'type' => $type,
				'func' => $func,
				'capt' => !!$capture
			)
		);
	}

	/**
	 * Removes an event handler from an element
	 *
	 * @param string $id Element ID
	 * @param string $type Event type
	 * @param string $func Function
	 * @param bool $capture Capture flag
	 */
	function removeEvent($id, $type, $func, $capture=TRUE) {
		if (is_string($func))
			$func = JSONEncoder::jsFunction($func, array('evt'));
		$this->commands[] = array(
			'cmd' => 'remev',
			'id' => $id,
			'arg' => array(
				'type' => $type,
				'func' => $func,
				'capt' => !!$capture
			)
		);
	}

	/**
	 * Shows an alert message
	 *
	 * @param string $msg Message
	 */
	function alert($msg) {
		$this->commands[] = array(
			'cmd' => 'alert',
			'arg' => $msg
		);
	}

	/**
	 * Shows a confirmation message
	 *
	 * Set skip to 0 to skip all next commands
	 * if the user chooses 'no'.
	 *
	 * @param string $msg Message
	 * @param int $skip Number of commands to skip if user chooses 'no'
	 */
	function confirm($msg, $skip=1) {
		$this->commands[] = array(
			'cmd' => 'confirm',
			'arg' => array(
				'msg' => $msg,
				'skip' => max(intval($skip), 0)
			)
		);
	}

	/**
	 * Redirects to a given URL
	 *
	 * @param string $url URL
	 */
	function redirect($url) {
		$this->commands[] = array(
			'cmd' => 'redirect',
			'arg' => $url
		);
	}

	/**
	 * Runs a block of JS code
	 *
	 * Example:
	 * <code>
	 * $response->runScript("var div = $('my_div');");
	 * $response->runScript("globalArray = [1, 2, 3];");
	 * </code>
	 *
	 * @param string $script JS code
	 */
	function runScript($script) {
		$this->commands[] = array(
			'cmd' => 'eval',
			'arg' => $script
		);
	}

	/**
	 * Calls a JS function
	 *
	 * Example:
	 * <code>
	 * $response->callFunction('myFunction', array(1, 'str', true), 'myObject');
	 * </code>
	 *
	 * @param string $func Function ID
	 * @param array $params Function params
	 * @param string $scope Function scope
	 */
	function callFunction($func, $params=array(), $scope=NULL) {
		$this->commands[] = array(
			'cmd' => 'func',
			'id' => JSONEncoder::jsIdentifier($func),
			'arg' => array(
				'params' => (is_array($params) ? $params : array()),
				'scope' => (!empty($scope) ? JSONEncoder::jsIdentifier($scope) : NULL)
			)
		);
	}
}
?>