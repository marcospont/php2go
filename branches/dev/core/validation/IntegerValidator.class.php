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
 * Validates signed and unsigned integer numbers
 *
 * @package validation
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class IntegerValidator extends AbstractValidator
{
	/**
	 * If TRUE, only unsigned numbers are accepted
	 *
	 * @var bool
	 * @access private
	 */
	var $unsigned = FALSE;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # unsigned (boolean): accept unsigned integers only
	 *
	 * @param array $params Arguments
	 * @return IntegerValidator
	 */
	function IntegerValidator($params=NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['unsigned']))
				$this->unsigned = TypeUtils::toBoolean($params['unsigned']);
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param mixed $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		$result = TypeUtils::isInteger($value);
		if ($this->unsigned && TypeUtils::parseInteger($value) < 0)
			$result = FALSE;
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS');
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_DATATYPE', array($this->fieldLabel, ($this->unsigned ? $maskLabels['DIGIT'] : $maskLabels['INTEGER'])));
		}
		return $result;
	}
}
?>