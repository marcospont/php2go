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
 * Validates CPF/CNPJ values
 *
 * CPF and CNPJ are codes used to identify individuals
 * and companies in Brazil.
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CPFCNPJValidator extends AbstractValidator
{
	/**
	 * Runs the validation
	 *
	 * @param string $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		$value = ereg_replace("[^0-9]", "", $value);
		if (empty($value)) {
			$result = TRUE;
		} elseif (strlen($value) == 14) {
			$s1 = (
				($value[0] * 5) + ($value[1] * 4) + ($value[2] * 3) +
				($value[3] * 2) + ($value[4] * 9) + ($value[5] * 8) +
				($value[6] * 7) + ($value[7] * 6) + ($value[8] * 5) +
				($value[9] * 4) + ($value[10] * 3) + ($value[11] * 2)
			);
			$r = $s1 % 11;
			$d1 = ($r < 2 ? 0 : (11 - $r));
			$s2 = (
				($value[0] * 6) + ($value[1] * 5) + ($value[2] * 4) +
				($value[3] * 3) + ($value[4] * 2) + ($value[5] * 9) +
				($value[6] * 8) + ($value[7] * 7) + ($value[8] * 6) +
				($value[9] * 5) + ($value[10] * 4) + ($value[11] * 3) +
				($value[12] * 2)
			);
			$r = ($s2 % 11);
			$d2 = ($r < 2 ? 0 : (11 - $r));
			$result = ($value[12] == $d1 && $value[13] == $d2);
		} elseif (strlen($value) == 11) {
			$s1 = (
				($value[0] * 10) + ($value[1] * 9) + ($value[2] * 8) +
				($value[3] * 7) + ($value[4] * 6) + ($value[5] * 5) +
				($value[6] * 4) + ($value[7] * 3) + ($value[8] * 2)
			);
			$r = ($s1 % 11);
			$d1 = ($r < 2 ? 0 : (11 - $r));
			$s2 = (
				($value[0] * 11) + ($value[1] * 10) + ($value[2] * 9) +
				($value[3] * 8) + ($value[4] * 7) + ($value[5] * 6) +
				($value[6] * 5) + ($value[7] * 4) + ($value[8] * 3) +
				($value[9] * 2)
			);
			$r = ($s2 % 11);
			$d2 = ($r < 2 ? 0 : (11 - $r));
			$result = ($value[9] == $d1 && $value[10] == $d2);
		} else {
			$result = FALSE;
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['CPFCNPJ']));
		}
		return $result;
	}
}
?>