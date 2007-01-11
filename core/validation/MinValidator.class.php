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
 * Validates values against a minimum value
 *
 * @package validation
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class MinValidator extends AbstractValidator
{
	/**
	 * Minimum value
	 *
	 * @var float
	 * @access private
	 */
	var $min;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # min (float): minimum value
	 *
	 * @param array $params Arguments
	 * @return MaxValidator
	 */
	function MinValidator($params = NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['min']))
				$this->min = $params['min'];
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param float $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		if (!isset($this->min)) {
			$result = TRUE;
		} else {
			$value = TypeUtils::parseFloat($value);
			$result = ($value >= $this->min);
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_VALUE_GOET', array($this->fieldLabel, $this->min));
		return $result;
	}
}
?>