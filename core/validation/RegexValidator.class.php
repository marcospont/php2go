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
 * Validates values against a regular expression pattern
 *
 * @package validation
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class RegexValidator extends AbstractValidator
{
	/**
	 * Regular expression pattern
	 *
	 * @var string
	 * @access private
	 */
	var $pattern;

	/**
	 * Regular expression type
	 *
	 * Accepts 2 values: POSIX or PCRE.
	 *
	 * @var string
	 * @access private
	 */
	var $type = 'POSIX';

	/**
	 * Class constructor
	 *
	 * Accepted arguments:
	 * # pattern (string): REGEXP pattern
	 * # type (string): REGEXP type (POSIX or PCRE)
	 *
	 * @param array $params Arguments
	 * @return RegexValidator
	 */
	function RegexValidator($params = NULL) {
		parent::AbstractValidator($params);
		if (is_array($params)) {
			if (isset($params['pattern']))
				$this->pattern = $params['pattern'];
			if (isset($params['type']) && in_array(strtoupper($params['type']), array('POSIX','PCRE')))
				$this->type = $params['type'];
		}
	}

	/**
	 * Runs the validation
	 *
	 * @param string $value Value to be validated
	 * @return bool
	 */
	function execute($value) {
		$value = strval($value);
		if (!isset($this->pattern)) {
			$result = TRUE;
		} else {
			switch($this->type) {
				case 'POSIX' :
					$expFunction = 'eregi';
					break;
				case 'PCRE' :
					$expFunction = 'preg_match';
					break;
				default :
					$expFunction = 'eregi'; break;
			}
			$result = (@$expFunction($this->pattern, $value));
		}
		if ($result === FALSE && isset($this->fieldLabel))
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID', $this->fieldLabel);
		return $result;
	}
}
?>