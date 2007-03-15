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

import('php2go.datetime.Date');

/**
 * Validates date and datetime values
 *
 * @package validation
 * @uses Date
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DateValidator extends AbstractValidator
{
	/**
	 * Date type
	 *
	 * @var string
	 * @access private
	 */
	var $type;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # type (string): EURO, US or SQL
	 *
	 * @param array $params Arguments
	 * @return DateValidator
	 */
	function DateValidator($params = NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['type']) && in_array(strtoupper($params['type']), array('EURO', 'SQL', 'US'))) {
				$this->type = $params['type'];
			} else {
				$this->type = PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
			}
		} else {
			$this->type = PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param string &$value Date to be validated
	 * @return bool
	 */
	function execute(&$value) {
		$result = TRUE;
		$matches = array();
		$value = strval($value);
		$type = strval($this->type);
		switch ($type) {
			case 'EURO' :
				if (Date::isEuroDate($value, $matches)) {
					list(,$day,$month,$year,$hours,$minutes,$seconds) = $matches;
				} else {
					$result = FALSE;
				}
				break;
			case 'SQL' :
				if (Date::isSqlDate($value, $matches)) {
					list(,$year,$month,$day,$hours,$minutes,$seconds) = $matches;
				} else {
					$result = FALSE;
				}
				break;
			case 'US' :
				if (Date::isUsDate($value, $matches)) {
					list(,$year,$month,$day,$hours,$minutes,$seconds) = $matches;
				} else {
					$result = FALSE;
				}
				break;
			default :
				$result = FALSE;
		}
		if ($result) {
			// validates day ( > 0 && <= 31 )
			if (TypeUtils::parseInteger($day) < 1 || TypeUtils::parseInteger($day) > 31)
				$result = FALSE;
			// validates month ( > 0 && <= 12 )
			if (TypeUtils::parseInteger($month) < 1 || TypeUtils::parseInteger($month)  > 12)
				$result = FALSE;
			// validates day against month and year
			$daysInMonth = Date::daysInMonth($month, $year);
			if ($day > $daysInMonth) {
				$result = FALSE;
			} else {
				// validates hour ( >= 0 && <= 24 )
				if ($hours && (TypeUtils::parseInteger($hours) < 0 || TypeUtils::parseInteger($hours) > 24))
					$result = FALSE;
				// validates minutes ( >= 0 && <= 59 )
				if ($minutes && (TypeUtils::parseInteger($minutes) < 0 || TypeUtils::parseInteger($minutes) > 59))
					$result = FALSE;
				// validates seconds ( >= 0 && <= 59 )
				if ($seconds && (TypeUtils::parseInteger($seconds) < 0 || TypeUtils::parseInteger($seconds) > 59))
					$result = FALSE;
			}
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, $maskLabels['DATE']));
		}
		return $result;
	}
}
?>