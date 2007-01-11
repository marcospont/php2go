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
 * Validates if a value is included in a list of choices
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ChoiceValidator extends AbstractValidator
{
	/**
	 * Array of possible choices
	 *
	 * @var array
	 * @access private
	 */
	var $options;

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # options (array): list of valid choices
	 *
	 * @param array $params Arguments
	 * @return ChoiceValidator
	 */
	function ChoiceValidator($params = NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['options']) && is_array($params['options']) && !TypeUtils::isHashArray($params['options']))
				$this->options = $params['options'];
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param mixed $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		if (!isset($this->options))
			return FALSE;
		$result = (in_array($value, $this->options));
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2GO::getLangVal('ERR_FORM_FIELD_CHOICE', array($this->fieldLabel, implode(',', $this->options)));
		return $result;
	}
}
?>