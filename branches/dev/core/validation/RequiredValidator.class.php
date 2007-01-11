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
 * Used by the forms API to validate required fields
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class RequiredValidator extends AbstractValidator
{
	/**
	 * Field's class name
	 *
	 * @var string
	 * @access private
	 */
	var $fieldClass;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # fieldClass (string): field's class name
	 *
	 * @param array $params Arguments
	 * @return RequiredValidator
	 */
	function RequiredValidator($params=NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['fieldClass']))
				$this->fieldClass = $params['fieldClass'];
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param mixed $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		if (is_array($value)) {
			$result = (!empty($value));
		} elseif ($this->fieldClass == 'checkfield' && $value == 'F') {
			$result = FALSE;
		} else {
			$value = (string)$value;
			$result = (trim($value) != '');
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_REQUIRED', $this->fieldLabel);
		return $result;
	}
}
?>