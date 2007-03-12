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
 * Validates time values (hour, minute and second)
 *
 * @package validation
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TimeValidator extends AbstractValidator
{
	/**
	 * Runs the validation
	 *
	 * @param string $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		$regExp = "/^\d{1,2}\:\d{1,2}(\:\d{1,2})?$/";
		if (!preg_match($regExp, $value)) {
			$result = FALSE;
		} else {
			$result = TRUE;
			$h = TypeUtils::parseInteger(substr($value, 0, 2));
			$m = TypeUtils::parseInteger(substr($value, 3, 2));
			if (strlen($value) == 8) {
				$s = TypeUtils::parseInteger(substr($value, 6, 2));
				if ($s < 0 || $s > 59)
					$result = FALSE;
			}
			if ($result && ($h < 0 || $h > 23 || $m < 0 || $m > 59))
				$result = FALSE;
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['TIME']));
		}
		return $result;
	}
}
?>