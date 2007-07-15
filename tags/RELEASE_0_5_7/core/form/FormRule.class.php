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

import('php2go.datetime.Date');

/**
 * When a field is used as comparison peer
 */
define('RULE_PEER_FIELD', 'field');
/**
 * When a literal value is used as comparison peer
 */
define('RULE_PEER_VALUE', 'value');
/**
 * When the rule doesn't rely on a peer to execute
 */
define('RULE_PEER_NONE', 'none');

/**
 * Validation rule associated with a form field
 *
 * The PHP2Go form API supports a set of validation rules that
 * can be associated with fields in the XML specification: comparison
 * rule, between a field and a value or between 2 fields; conditional
 * obligatoriness rule, based on a value or on the value of another
 * field; regular expression rule; custom function rule.
 *
 * Examples:
 * <code>
 * <editfield name="age" label="Age" mask="DIGIT" maxlength="4">
 *   <rule type="GOET" value="18" message="Age must be higher than 18!"/>
 *   <rule type="LOET" value="50" message="Age must be lower than 50!"/>
 * </editfield>
 * <passwdfield name="password" label="Password" maxlength="20"/>
 * <passwdfield name="confirm_password" label="Confirm Password" maxlength="20">
 *   <rule type="EQ" field="password" message="Passwords must be equal!"/>
 * </passwdfield>
 * </code>
 *
 * @package form
 * @uses Date
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormRule extends PHP2Go
{
	/**
	 * Rule type
	 *
	 * @var string
	 */
	var $type;

	/**
	 * Peer field's name
	 *
	 * @var string
	 */
	var $field = NULL;

	/**
	 * Peer value
	 *
	 * @var mixed
	 */
	var $value = NULL;

	/**
	 * Comparison data type
	 *
	 * @var string
	 */
	var $compareType = NULL;

	/**
	 * Error message
	 *
	 * @var string
	 */
	var $message = NULL;

	/**
	 * Function body (for custom rules)
	 *
	 * @var string
	 */
	var $functionBody = NULL;

	/**
	 * Are the rule's properties valid?
	 *
	 * @var bool
	 */
	var $_valid = FALSE;

	/**
	 * Owner field
	 *
	 * @var object FormField
	 */
	var $_Field = NULL;

	/**
	 * Class constructor
	 *
	 * @param string $type Type
	 * @param string $field Peer field
	 * @param mixed $value Peer value
	 * @param string $compareType Comparison data type
	 * @param string $message Error message
	 * @param string $functionBody Function body
	 * @return FormRule
	 */
	function FormRule($type, $field=NULL, $value=NULL, $compareType=NULL, $message=NULL, $functionBody=NULL) {
		parent::PHP2Go();
		$this->type = strtoupper($type);
		if (!empty($field))
			$this->field = $field;
		if ($value !== NULL)
			$this->value = $value;
		if (!empty($compareType))
			$this->compareType = strtoupper($compareType);
		if (!empty($message))
			$this->message = $message;
		if (!empty($functionBody))
			$this->functionBody = $functionBody;
	}

	/**
	 * Builds a FormRule from a given rule XML node
	 *
	 * @param XmlNode $Node Rule node
	 * @return FormRule
	 * @static
	 */
	function &fromNode($Node) {
		$type = trim($Node->getAttribute('TYPE'));
		$field = trim($Node->getAttribute('FIELD'));
		$value = trim($Node->getAttribute('VALUE'));
		$compareType = trim($Node->getAttribute('COMPARETYPE'));
		if (!$compareType) {
			$Parent = $Node->getParentNode();
			if ($Parent->getTag() == 'DATEPICKERFIELD') {
				$compareType = 'DATE';
			} else {
				$compareType = trim($Parent->getAttribute('MASK'));
			}
		}
		$message = trim($Node->getAttribute('MESSAGE'));
		$functionBody = $Node->getData();
		$Rule = new FormRule($type, $field, $value, $compareType, resolveI18nEntry($message), $functionBody);
		return $Rule;
	}

	/**
	 * Get the type of the rule
	 *
	 * @return string
	 */
	function getType() {
		return $this->type;
	}

	/**
	 * Get the rule's owner form
	 *
	 * @return Form
	 */
	function &getOwnerForm() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_Field, 'FormField'))
			$result =& $this->_Field->getOwnerForm();
		return $result;
	}

	/**
	 * Get the rule's owner field
	 *
	 * @return FormField
	 */
	function &getOwnerField() {
		return $this->_Field;
	}

	/**
	 * Set the rule's owner field
	 *
	 * @param FormField &$Field Owner field
	 */
	function setOwnerField(&$Field) {
		$this->_Field =& $Field;
	}

	/**
	 * Gets the value of the owner field
	 *
	 * Returns FALSE when the owner field is invalid.
	 *
	 * @return mixed
	 */
	function getOwnerValue() {
		$Owner =& $this->getOwnerField();
		if (is_null($Owner))
			return FALSE;
		$value = $Owner->getValue();
		if ($Owner->isA('CheckField') && $value == 'F')
			$value = '';
		return $value;
	}

	/**
	 * Gets the type of the peer associated with this rule
	 *
	 * Rules of type REQIF doesn't have a peer. So, they'll
	 * return {@link RULE_PEER_NONE}.
	 *
	 * @return string
	 */
	function getPeerType() {
		if (!empty($this->field))
			return RULE_PEER_FIELD;
		elseif ($this->type != 'REQIF')
			return RULE_PEER_VALUE;
		return RULE_PEER_NONE;
	}

	/**
	 * Gets the value of the peer field
	 *
	 * Returns NULL when there's no peer field
	 * defined for this rule, or when it is invalid.
	 *
	 * @return FormField|NULL
	 */
	function &getPeerField() {
		$null = NULL;
		if (!empty($this->field)) {
			$Form =& $this->getOwnerForm();
			if (!is_null($Form)) {
				$Peer =& $Form->getField($this->field);
				if (!is_null($Peer)) {
					return $Peer;
				}
			}
		}
		return $null;
	}

	/**
	 * Gets the value of the rule's peer field
	 *
	 * Returns FALSE when there's no peer field
	 * or when the declare peer field is invalid.
	 *
	 * @return mixed
	 */
	function getPeerValue() {
		$Peer =& $this->getPeerField();
		if (!is_null($Peer)) {
			if (!$Peer->dataBind)
				$Peer->onDataBind();
			$value = $Peer->getValue();
			if ($Peer->isA('CheckField') && $value == 'F')
				$value = '';
			return $value;
		}
		return FALSE;
	}

	/**
	 * Return the rule's comparison value
	 *
	 * @return string
	 */
	function getComparisonValue() {
		return $this->value;
	}

	/**
	 * Get comparison data type
	 *
	 * @return string
	 */
	function getComparisonType() {
		return $this->compareType;
	}

	/**
	 * Get error message
	 *
	 * @return string
	 */
	function getMessage() {
		return $this->message;
	}

	/**
	 * Builds and returns Javascript statement that registers
	 * this rule in the client-side form validator
	 *
	 * @return string
	 */
	function getScriptCode() {
		if (TypeUtils::isInstanceOf($this->_Field, 'FormField')) {
			$args = array();
			$Form =& $this->_Field->_Form;
			$args[] = "ruleType:\"{$this->type}\"";
			if ($this->compareType != NULL)
				$args[] = "dataType:\"{$this->compareType}\"";
			if ($this->field != NULL)
				$args[] = "peerField:\"{$this->field}\"";
			if ($this->value != NULL) {
				$value = ($this->type == 'REGEX' ? preg_replace("|\/|", "/", $this->value) : "\"{$this->value}\"");
				$args[] = "peerValue:{$value}";
			}
			if ($this->message != NULL)
				$args[] = "msg:\"" . $this->message . "\"";
			if ($this->type == 'JSFUNC') {
				$funcName = PHP2Go::generateUniqueId(preg_replace("~[^\w]+~", "", $this->_Field->getName()) . 'ValidateFunc');
				$funcBody = rtrim(ltrim($this->functionBody, "\r\n "));
				preg_match("/^([\t]+)/", $funcBody, $matches);
				$funcBody = (isset($matches[1]) ? preg_replace("/^\t{" . strlen($matches[1]) . "}/m", "\t\t", $funcBody) : $funcBody);
				$funcBody = preg_replace("~this~", "element", $funcBody);
				$args[] = "func:{$funcName}";
				$Form->Document->addScriptCode(
					"\tfunction {$funcName}(element) {\n" . $funcBody . "\n\t}"
				);
			}
			return sprintf("\t%sValidator.add('%s', RuleValidator, {%s});\n", strtolower($Form->formName), $this->_Field->validationName, implode(',', $args));
		}
		return '';
	}

	/**
	 * Verify if the rule's properties are valid
	 *
	 * @return bool
	 */
	function isValid() {
		if ($this->_valid === TRUE)
			return $this->_valid;
		if (TypeUtils::isInstanceOf($this->_Field, 'FormField')) {
			$this->_valid = TRUE;
			// validates the rule type
			if (!ereg("^REGEX$|^REQIF$|^((REQIF)?(EQ|NEQ|GT|LT|GOET|LOET))$|^JSFUNC$", $this->type)) {
				$this->_valid = FALSE;
			// on a conditional obligatoriness rule, with or without comparison, a peer field is mandatory
			} elseif (ereg("^REQIF(EQ|NEQ|GT|LT|LOET|GOET)?$", $this->type, $matches) && (is_null($this->field) || $this->field == $this->_Field->getName())) {
				$this->_valid = FALSE;
			// on a conditional obligatoriness rule with comparison, the peer value is mandatory
			} elseif (ereg("^REQIF(EQ|NEQ|GT|LT|LOET|GOET)$", $this->type, $matches) && $this->value === NULL) {
				$this->_valid = FALSE;
			// on a comparison rule, a peer field or value is mandatory
			} elseif (ereg("^EQ|NEQ|GT|LT|GOET|LOET$", $this->type) && (is_null($this->field) || $this->field == $this->_Field->getName()) && is_null($this->value)) {
				$this->_valid = FALSE;
			// on a regular expression rule, the peer value is mandatory
			} elseif ($this->type == 'REGEX' && is_null($this->value)) {
				$this->_valid = FALSE;
			// on a rule based on a custom function, the function body is mandatory
			} elseif ($this->type == 'JSFUNC' && is_null($this->functionBody)) {
				$this->_valid = FALSE;
			// validates the comparison data type
			} elseif (!is_null($this->compareType) && !preg_match("/^(DATE|INTEGER|FLOAT|CURRENCY)$/", $this->compareType)) {
				$this->compareType = 'STRING';
			} else {
				if ($this->type == 'REQIF')
					$this->value = NULL;
				if ($this->type == 'REGEX')
					$this->compareType = NULL;
				if ($this->type == 'REGEX') {
					if ($this->value[0] == '/')
						$this->value = substr($this->value, 1);
					if ($this->value{(strlen($this->value)-1)} == '/')
						$this->value = substr($this->value, 0, -1);
					$this->value = '/' . ereg_replace('([^\])/', '\\1\/', $this->value) . '/';
				}
			}
			if (!$this->_valid)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_WRONG_RULE', $this->__toString()), E_USER_ERROR, __FILE__, __LINE__);
			return $this->_valid;
		} else {
			$this->_valid = FALSE;
			return $this->_valid;
		}
	}

	/**
	 * Configures the rule's dynamic properties
	 *
	 * @uses Date::parseFieldExpression()
	 * @uses Form::resolveVariables()
	 */
	function onDataBind() {
		// resolve variables and expressions on the VALUE attribute
		if (!empty($this->value) && preg_match("/~[^~]+~/", $this->value)) {
			$Form =& $this->getOwnerForm();
			$this->value = $Form->resolveVariables($this->value);
		}
		// evaluate date expressions
		if ($this->compareType == 'DATE' && !empty($this->value)) {
			if ($expr = Date::parseFieldExpression($this->value))
				$this->value = $expr;
		}
	}

	/**
	 * Builds a string representation of the validation rule
	 *
	 * @return string
	 */
	function __toString() {
		$info = $this->_Field->getName() . " - [{$this->type}";
		if (!empty($this->field))
			$info .= "; {$this->field}";
		if (!empty($this->value))
			$info .= "; {$this->value}";
		if (!empty($this->message))
			$info .= "; {$this->message}";
		$info .= ']';
		return $info;
	}
}

?>