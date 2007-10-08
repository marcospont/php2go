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

import('php2go.validation.RegexValidator');

/**
 * Validates if a string contains alphanumeric chars only
 *
 * The default behaviour of this validator is to accept lowercase (a-z)
 * and uppercase (A-Z) letters only. In order to accept more chars, you
 * need to provide one or more of the following flags:
 * # space: accepts whitespace chars
 * # number: accepts numbers
 * # punctuation: accepts punctuation chars
 * # acclower: accepts lowercase accented chars
 * # accupper: accepts uppercase accented chars
 *
 * @package validation
 * @uses RegexValidator
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AlphaCharsValidator extends AbstractValidator
{
	/**
	 * Regular expression pattern
	 *
	 * @var string
	 * @access private
	 */
	var $pattern;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # space (boolean): whether whitespace chars should be accepted
	 * # number (boolean): whether numeric chars should be accepted
	 * # punctuation (boolean): whether punctuation chars should be accepted
	 * # acclower (boolean): whether lowercase accented chars should be accepted
	 * # accupper (boolean): whether uppercase accented chars should be accepted
	 *
	 * @param array $params Arguments
	 * @return AlphaCharsValidator
	 */
	function AlphaCharsValidator($params=NULL) {
		parent::AbstractValidator($params);
		$this->pattern = "a-zA-Z";
		if (is_array($params)) {
			if (isset($params['space']) && $params['space'] == TRUE)
				$this->pattern .= "[:space:]";
			if (isset($params['number']) && $params['number'] == TRUE)
				$this->pattern .= "0-9";
			if (isset($params['punctuation']) && $params['punctuation'] == TRUE)
				$this->pattern .= "\.,;\:&\"'\?\!\(\)";
			if (isset($params['acclower']) && $params['acclower'] == TRUE)
				$this->pattern .= "à-åæçè-ëì-ïðñò-öø÷ù-üý-ÿ";
			if (isset($params['accupper']) && $params['accupper'] == TRUE)
				$this->pattern .= "À-ÅÆÇÈ-ËÌ-ÏÐÑÒ-ÖØ×Ù-ÜÝß";
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param mixed $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		$RegexValidator =& new RegexValidator(array(
			'pattern' => "^[{$this->pattern}]+$",
			'type' => "POSIX"
		));
		$result = $RegexValidator->execute($value);
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_ALPHANUM', $this->fieldLabel);
		return $result;
	}
}
?>