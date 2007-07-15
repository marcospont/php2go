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
 * Abstract validator class
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AbstractValidator extends PHP2Go
{
	/**
	 * Holds the label of the field being validated
	 *
	 * @var string
	 * @access private
	 */
	var $fieldLabel;

	/**
	 * Error message
	 *
	 * @var string
	 * @access private
	 */
	var $errorMessage;

	/**
	 * Class constructor
	 *
	 * @param array $params Validator arguments
	 * @return AbstractValidator
	 */
	function AbstractValidator($params=NULL) {
		parent::PHP2Go();
		if (is_array($params))
			$this->fieldLabel = @$params['fieldLabel'];
	}

	/**
	 * Must be implemented by child classes
	 *
	 * @abstract
	 */
	function execute() {
	}

	/**
	 * Get the validation error message
	 *
	 * @return string
	 */
	function getError() {
		return $this->errorMessage;
	}
}
?>