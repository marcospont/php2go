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
 * Validates signed currency values
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CurrencyValidator extends AbstractValidator
{
	/**
	 * Runs the validation
	 *
	 * @param string &$value Value to be validated
	 * @return bool
	 */
	function execute(&$value) {
		if (strlen($value) == 1) {
			$result = FALSE;
		} else {
			if (strlen($value) == 2) {
				if ($value[0] == '-')
					$value = "-0,0" . $value[1];
				else
					$value = "0," . $value;
			}
			if (strlen($value) == 3 && $value[0] == '-')
				$value = "-0," . substr($value, 1);
			$strRegExp = '';
			$size = strlen($value);
			if ($value[0] == '-')
				$size--;
			$start = ($size - 3) % 4;
			$rest = $size - $start - 3;
			if ($size == 0) {
				$result = TRUE;
			} else {
				$strRegExp = "/^\-?[0-9]{" . $start . "}";
				for ($i=0; $i<$rest; $i=$i+4)
					$strRegExp .= "\.[0-9]{3}";
				$strRegExp .= "\,[0-9]{2}$/";
				$result = (bool)preg_match($strRegExp, $value);
			}
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['CURRENCY']));
		}
		return $result;
	}
}
?>