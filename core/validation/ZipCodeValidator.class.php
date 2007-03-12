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

/**
 * Validates postal codes
 *
 * Postal codes must be composed by N numeric chars, an "-" char and
 * N numeric chars. The sizes of the left and right parts must be
 * provided to the validator.
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ZipCodeValidator extends AbstractValidator
{
	/**
	 * Contains the sizes of the left and right parts of the postal code
	 *
	 * @var array
	 * @access private
	 */
	var $limiters;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # limiters (array): array containing the sizes of left and right parts of the postal code
	 *
	 * @param array $params Arguments
	 * @return ZipCodeValidator
	 */
	function ZipCodeValidator($params = NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['limiters']) && is_array($params['limiters']) && sizeof($params['limiters']) == 2) {
				$this->limiters = $params['limiters'];
				$this->limiters[0] = max(1, $this->limiters[0]);
				$this->limiters[1] = max(1, $this->limiters[1]);
			}
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param string $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		if (isset($this->limiters))
			$result = (bool)preg_match("/^[0-9]{" . $this->limiters[0] . "}\-[0-9]{" . $this->limiters[1] . "}$/", $value);
		else
			$result = FALSE;
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['ZIP']));
		}
		return $result;
	}
}
?>