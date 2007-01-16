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
 * Validates the length of strings against a maximum limit
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class MaxLengthValidator extends AbstractValidator
{
	/**
	 * Maximum length
	 *
	 * @var int
	 * @access private
	 */
	var $maxLength;

	/**
	 * Whether empty values should be ignored or not
	 *
	 * @var bool
	 * @access private
	 */
	var $bypassEmpty = FALSE;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # maxlength (int): maximum length
	 * # bypassEmpty (boolean): bypass empty values
	 *
	 * @param array $params Arguments
	 * @return MaxLengthValidator
	 */
	function MaxLengthValidator($params=NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['maxlength']))
				$this->maxLength = $params['maxlength'];
			if (isset($params['bypassEmpty']))
				$this->bypassEmpty = (bool)$params['bypassEmpty'];
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param string $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		if (!isset($this->maxLength)) {
			$result = TRUE;
		} else {
			$str = strval($value);
			$result = ($this->bypassEmpty ? (empty($value) || strlen($str) <= $this->maxLength) : (strlen($str) <= $this->maxLength));
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_MAX_LENGTH', array($this->fieldLabel, $this->maxLength));
		return $result;
	}
}
?>