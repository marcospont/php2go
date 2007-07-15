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
 * Validates values against an interval
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class IntervalValidator extends AbstractValidator
{
	/**
	 * Lower bound
	 *
	 * @var int
	 * @access private
	 */
	var $min;

	/**
	 * Upper bound
	 *
	 * @var int
	 * @access private
	 */
	var $max;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # min (int): lower bound
	 * # max (int): upper bound
	 *
	 * @param array $params Arguments
	 * @return IntervalValidator
	 */
	function IntervalValidator($params = NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['min']))
				$this->min = $params['min'];
			if (isset($params['max']))
				$this->max = $params['max'];
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param int $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		$value = floatval($value);
		if (!isset($this->max) || !isset($this->min)) {
			$result = TRUE;
		} else {
			$result = ($value >= $this->min && $value <= $this->max);
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_VALUE_OUT_OF_BOUNDS', array($this->fieldLabel, $this->min, $this->max));
		return $result;
	}
}
?>