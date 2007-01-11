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
 * Validates signed and unsigned float values
 *
 * Optionally, the class can validate maximum precision (length
 * of the decimal part).
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FloatValidator extends AbstractValidator
{
	/**
	 * Max sizes for integer and decimal parts
	 *
	 * @var array
	 * @access private
	 */
	var $limiters;

	/**
	 * Decimal separator
	 *
	 * If ommited, will be retrieved from the locale settings.
	 *
	 * @var string
	 * @access private
	 */
	var $decimalPoint;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # limiters (array): array containing max size of integer and decimal parts of the number
	 * # decimalPoint (string): decimal separator
	 *
	 * @param array $params Arguments
	 * @return FloatValidator
	 */
	function FloatValidator($params = NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['limiters']) && is_array($params['limiters']) && sizeof($params['limiters']) == 2) {
				$this->limiters = $params['limiters'];
				$this->limiters[0] = max(1, $this->limiters[0]);
				$this->limiters[1] = max(1, $this->limiters[1]);
			}
			if (isset($params['decimalPoint'])) {
				$this->decimalPoint = $params['decimalPoint'];
			} else {
				$locale = localeconv();
				$this->decimalPoint = $locale['decimal_point'];
			}
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param string &$value Value to be validated
	 * @return bool
	 */
	function execute(&$value) {
		if ($value[0] == $this->decimalPoint)
			$value = '0' . $value;
		if ($value[0] == '-' && $value[1] == $this->decimalPoint)
			$value = '-0' . substr($value, 1);
		if (isset($this->limiters)) {
			// fills the value with needed decimal places
			$p = strpos($value, $this->decimalPoint);
			if ($p === FALSE) {
				$value .= $this->decimalPoint . str_repeat('0', $this->limiters[1]);
			} else {
				$p++;
				$ss = substr($value, $p);
				if (strlen($ss) < $this->limiters[1])
					$value .= str_repeat('0', $this->limiters[1]-strlen($ss));
			}
			// validates numeric precision
			$result = preg_match("/^\-?[0-9]{1," . $this->limiters[0] . "}(" . preg_quote($this->decimalPoint, '/') . "[0-9]{1," . $this->limiters[1] . "})?$/", $value);
		} else {
			// simple float validation (no length validation)
			$result = preg_match("/^\-?[0-9]+(" . preg_quote($this->decimalPoint, '/') . "[0-9]+)?$/", $value);
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			if (isset($this->limiters)) {
				$this->errorMessage = str_replace("\\n", "<br>", PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_FLOAT', array($this->fieldLabel, $this->limiters[0], $this->limiters[1])));
			} else {
				$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
				$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['FLOAT']));
			}
		}
		return $result;
	}
}
?>