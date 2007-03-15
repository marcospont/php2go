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
import('php2go.util.Statement');

/**
 * Used by the forms API to validate rules
 *
 * @package validation
 * @uses Date
 * @uses FormRule
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class RuleValidator extends AbstractValidator
{
	/**
	 * Validation rule
	 *
	 * @var FormRule
	 * @access private
	 */
	var $Rule = NULL;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # rule (FormRule): rule instance
	 *
	 * @param array $params Arguments
	 * @return RuleValidator
	 */
	function RuleValidator($params = NULL) {
		parent::AbstractValidator($params);
		if (TypeUtils::isArray($params)) {
			if (isset($params['rule']))
				$this->Rule =& $params['rule'];
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param string $srcName Field name that should be validated
	 * @return bool
	 */
	function execute($srcName) {
		if (!isset($this->Rule))
			return FALSE;
		// validates the field
		$Form =& $this->Rule->getOwnerForm();
		if (TypeUtils::isNull($Form))
			return FALSE;
		$Src =& $this->Rule->getOwnerField();
		if (TypeUtils::isNull($Src)) {
			return FALSE;
		} else {
			$srcValue = $Src->getValue();
			if ($Src->isA('CheckField') && $srcValue == 'F')
				$srcValue = '';
		}
		// validates the peer field
		$trgName = $this->Rule->getTargetField();
		if (!empty($trgName)) {
			$Trg =& $Form->getField($trgName);
			if (TypeUtils::isNull($Trg)) {
				return FALSE;
			} else {
				$trgValue = $Trg->getValue();
				if ($Trg->isA('CheckField') && $trgValue == 'F')
					$trgValue = '';
			}
		}
		$matches = array();
		if ($this->Rule->getType() == 'REQIF') {
			// conditional obligatoriness (when the peer field isn't empty)
			if ($this->_isEmpty($srcValue) && !$this->_isEmpty($trgValue)) {
				$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_REQUIRED', $Src->getLabel());
				return FALSE;
			} else {
				return TRUE;
			}
		} elseif ($this->Rule->getType() == 'REGEX') {
			// field value must match a pattern
			$pattern = $this->Rule->getValueArgument();
			if (!$this->_isEmpty($srcValue) && !preg_match("{$pattern}", $srcValue)) {
				$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID', $Src->getLabel());
				return FALSE;
			} else {
				return TRUE;
			}
		} elseif (ereg("^(REQIF)?(EQ|NEQ|GT|LT|GOET|LOET)$", $this->Rule->getType(), $matches)) {
			$mask = $this->Rule->getCompareType();
			$value = $this->Rule->getValueArgument();
			if ($matches[1] != NULL) {
				// conditional obligatoriness based on an expression
				if ($this->_isEmpty($srcValue) && !$this->_isEmpty($trgValue) && $this->_compareValues($trgValue, $value, $matches[2], $mask, 'value')) {
					$result = FALSE;
					$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_REQUIRED', $Trg->getLabel());
				} else {
					$result = TRUE;
				}
			} else {
				// comparison between field x peer value
				if (!$this->_isEmpty($value)) {
					$result = $this->_compareValues($srcValue, $value, $matches[2], $mask, 'value');
					if (!$result)
						$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_VALUE_' . $matches[2], array($Src->getLabel(), $value));
				// comparison between field x peer field
				} else {
					$result = $this->_compareValues($srcValue, $trgValue, $matches[2], $mask, 'field');
					if (!$result)
						$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_' . $matches[2], array($Src->getLabel(), $Trg->getLabel()));
				}
			}
			return $result;
		}
		// JSFUNC rules run at client-side only
		elseif ($this->Rule->getType() == 'JSFUNC') {
			return TRUE;
		}
		$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID', $Src->getLabel());
		return FALSE;
	}

	/**
	 * Checks if a given value is empty
	 *
	 * @param mixed $value Input value
	 * @access private
	 * @return bool
	 */
	function _isEmpty($value) {
		if (is_array($value))
			return (empty($value));
		$value = (string)$value;
		return ($value == '');
	}

	/**
	 * Compares two operands using a given operator
	 *
	 * @param mixed $source First operand
	 * @param mixed $target Second operand
	 * @param string $operator Operator
	 * @param string $dataType Comparison data type
	 * @param string $targetType Peer type (field or value)
	 * @access private
	 * @return bool
	 */
	function _compareValues($source, $target, $operator, $dataType, $targetType) {
		if ($targetType == 'field') {
			if ($this->_isEmpty($source) || $this->_isEmpty($target))
				return TRUE;
		} else {
			if ($this->_isEmpty($source))
				return TRUE;
		}
		if ($dataType != '') {
			$dataType = strtoupper($dataType);
			switch ($dataType) {
				case 'DATE' :
					$src = Date::dateToDays($source);
					if ($targetType == 'value')
						$target = Date::parseFieldExpression($target);
					$trg = Date::dateToDays($target);
					break;
				case 'INTEGER' :
					$src = TypeUtils::parseInteger($source);
					$trg = TypeUtils::parseInteger($target);
					break;
				case 'FLOAT' :
					$src = TypeUtils::parseFloat($source);
					$trg = TypeUtils::parseFloat($target);
					break;
				case 'CURRENCY' :
					$src = TypeUtils::parseFloat(str_replace(array('.', ','), array('', '.'), $source));
					$trg = TypeUtils::parseFloat(str_replace(array('.', ','), array('', '.'), $target));
					break;
				default :
					$src = $source;
					$trg = $target;
					break;
			}
		} else {
			$src = $source;
			$trg = $target;
		}
		$result = TRUE;
		switch ($operator) {
			case 'EQ' : $result = ($src == $trg); break;
			case 'NEQ' : $result = ($src != $trg); break;
			case 'GT' : $result = ($src > $trg); break;
			case 'LT' : $result = ($src < $trg); break;
			case 'GOET' : $result = ($src >= $trg); break;
			case 'LOET' : $result = ($src <= $trg); break;
		}
		return $result;
	}
}
?>