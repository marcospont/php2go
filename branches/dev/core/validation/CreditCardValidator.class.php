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

import('php2go.util.Number');

/**
 * Mastercard credit card
 */
define("CREDIT_CARD_TYPE_MC", 1);
/**
 * VISA credit card
 */
define("CREDIT_CARD_TYPE_VS", 2);
/**
 * American Express credit card
 */
define("CREDIT_CARD_TYPE_AX", 3);
/**
 * Diners Club credit card
 */
define("CREDIT_CARD_TYPE_DC", 4);
/**
 * Discovery credit card
 */
define("CREDIT_CARD_TYPE_DS", 5);
/**
 * JCB credit card
 */
define("CREDIT_CARD_TYPE_JC", 6);

/**
 * Validates credit card numbers
 *
 * @package validation
 * @uses Number
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CreditCardValidator extends AbstractValidator
{
	/**
	 * Credit card type
	 *
	 * @var int
	 * @access private
	 */
	var $type;

	/**
	 * Credit card number
	 *
	 * @var string
	 * @access private
	 */
	var $number;

	/**
	 * Credit card expiry month
	 *
	 * @var int
	 * @access private
	 */
	var $expiryMonth;

	/**
	 * Credit card expiry year
	 *
	 * @var int
	 * @access private
	 */
	var $expiryYear;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # type (int): credit card type
	 * # expiryMonth (int): expiry month
	 * # expiryYear: (int): expiry year
	 *
	 * Supported credit card types:
	 * # mastercard ({@link CREDIT_CARD_TYPE_MC})
	 * # visa ({@link CREDIT_CARD_TYPE_VS})
	 * # american express ({@link CREDIT_CARD_TYPE_AX})
	 * # diners club ({@link CREDIT_CARD_TYPE_DC})
	 * # discovery ({@link CREDIT_CARD_TYPE_DS})
	 * # jcb ({@link CREDIT_CARD_TYPE_JC})
	 *
	 * @param array $params Arguments
	 * @return CreditCardValidator
	 */
	function CreditCardValidator($params=NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			$this->_parseType(@$params['type']);
			$this->_parseExpiryDate(@$params['expiryMonth'], @$params['expiryYear']);
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param string $value Credit card number
	 * @return bool
	 */
	function execute($value) {
		$value = strval($value);
		$value = ereg_replace("[^0-9]", "", $value);
		if (!empty($value))
			$this->number = $value;
		if (!isset($this->type) || !isset($this->number) || !isset($this->expiryMonth) || !isset($this->expiryYear)) {
			$result = FALSE;
		} else {
			$validRegex = TRUE;
			switch ($this->type) {
				case CREDIT_CARD_TYPE_MC :
					$validRegex = ereg("^5[1-5][0-9]{14}$", $this->number);
					break;
				case CREDIT_CARD_TYPE_VS :
					$validRegex = ereg("^4[0-9]{12}([0-9]{3})?$", $this->number);
					break;
				case CREDIT_CARD_TYPE_AX :
					$validRegex = ereg("^3[47][0-9]{13}$", $this->number);
					break;
				case CREDIT_CARD_TYPE_DC :
					$validRegex = ereg("^3(0[0-5]|[68][0-9])[0-9]{11}$", $this->number);
					break;
				case CREDIT_CARD_TYPE_DS :
					$validRegex = ereg("^6011[0-9]{12}$", $this->number);
					break;
				case CREDIT_CARD_TYPE_JC :
					$validRegex = ereg("^(3[0-9]{4}|2131|1800)[0-9]{11}$", $this->number);
					break;
			}
			$result = ($validRegex && Number::modulus10($value));
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			$ccNames = array(
				CREDIT_CARD_TYPE_MC => 'Mastercard',
				CREDIT_CARD_TYPE_VS => 'VISA',
				CREDIT_CARD_TYPE_AX => 'American Express',
				CREDIT_CARD_TYPE_DC => 'Diners Club',
				CREDIT_CARD_TYPE_DS => 'Discovery',
				CREDIT_CARD_TYPE_JC => 'JCB'
			);
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_CREDITCARD', array($this->fieldLabel, $ccNames[$this->type]));
		}
		return $result;
	}

	/**
	 * Parses credit card type
	 *
	 * @param string $type Provided type
	 * @access private
	 */
	function _parseType($type) {
		$type = strtolower(trim(strval($type)));
		switch ($type) {
			case 'mc' :
			case 'mastercard' :
			case 'm' :
			case '1' :
				$this->type = CREDIT_CARD_TYPE_MC;
				break;
			case 'vs' :
			case 'visa' :
			case 'v' :
			case '2' :
				$this->type = CREDIT_CARD_TYPE_VS;
				break;
			case 'ax' :
			case 'american express' :
			case 'a' :
			case '3' :
				$this->type = CREDIT_CARD_TYPE_AX;
				break;
			case 'dc' :
			case 'diners' :
			case 'diners club' :
			case '4' :
				$this->type = CREDIT_CARD_TYPE_DC;
				break;
			case 'ds' :
			case 'discover' :
			case '5' :
				$this->type = CREDIT_CARD_TYPE_DS;
				break;
			case 'jcb' :
			case 'j' :
				$this->type = CREDIT_CARD_TYPE_JC;
				break;
		}
	}

	/**
	 * Parses and validates expiry month and expiry year
	 *
	 * @param int $month Expiry month
	 * @param int $year Expiry year
	 * @access private
	 */
	function _parseExpiryDate($month, $year) {
		if (isset($month)) {
			if (is_numeric($month) && $month >= 1 && $month <= 12)
				$this->expiryMonth = $month;
		}
		if (isset($year)) {
			$currentYear = date("Y");
			$yearPrefix = substr($currentYear, 0, 2);
			$currentYear = intval($currentYear);
			if (is_numeric($year)) {
				if ($year < 100)
					$year = intval($yearPrefix . $year);
				if ($year >= $currentYear && $year <= ($currentYear + 10)) {
					$this->expiryYear = $year;
				}
			}
		}
	}
}
?>