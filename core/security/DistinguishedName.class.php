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
 * Representation of a DN (Distinguished Name)
 *
 * A Distinguished name is a structure used to represent a global
 * unique key. Some common usages of this approach are the LDAP
 * protocol and digital certificates.
 *
 * The data structure handled by this class is compliant with
 * RFC2253 and RFC1779.
 *
 * @package security
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DistinguishedName extends PHP2Go
{
	/**
	 * DN information hash
	 *
	 * @access private
	 * @var array
	 */
	var $info;

	/**
	 * Class constructor
	 *
	 * @param string|array $info DN info
	 * @return DistinguishedName
	 */
	function DistinguishedName($info) {
		parent::PHP2Go();
		if (is_array($info)) {
			$this->info = array_change_key_case($info, CASE_UPPER);
		} else {
			$matches = array();
			$tmp = explode('/', $info);
			foreach ($tmp as $entry) {
				if (!empty($entry) && preg_match("/^([^=])=(.*)$/", $entry, $matches))
					$this->info[strtoupper($matches[1])] = $matches[2];
			}
		}
	}

	/**
	 * Get common name field
	 *
	 * @return string
	 */
	function getCommonName() {
		return (array_key_exists('CN', $this->info) ? $this->info['CN'] : NULL);
	}

	/**
	 * Get e-mail field
	 *
	 * @return string
	 */
	function getEmail() {
		return (array_key_exists('EMAIL', $this->info) ? $this->info['EMAIL'] : NULL);
	}

	/**
	 * Get country field
	 *
	 * Returns the ISO code of the DN's country.
	 *
	 * @return string
	 */
	function getCountry() {
		return (array_key_exists('C', $this->info) ? $this->info['C'] : NULL);
	}

	/**
	 * Get state/province field
	 *
	 * @return string
	 */
	function getState() {
		return (array_key_exists('ST', $this->info) ? $this->info['ST'] : NULL);
	}

	/**
	 * Get locality field
	 *
	 * @return string
	 */
	function getLocality() {
		return (array_key_exists('L', $this->info) ? $this->info['L'] : NULL);
	}

	/**
	 * Get organization field
	 *
	 * @return string
	 */
	function getOrganization() {
		return (array_key_exists('O', $this->info) ? $this->info['O'] : NULL);
	}

	/**
	 * Get organization unit field
	 *
	 * @return string
	 */
	function getOrganizationalUnit() {
		return (array_key_exists('OU', $this->info) ? $this->info['OU'] : NULL);
	}

	/**
	 * Builds and returns a string representation of the DN
	 *
	 * @return string
	 */
	function __toString() {
		$result = '';
		foreach ($this->info as $k => $v) {
			$result .= "/{$k}={$v}";
		}
		return $result;
	}
}
?>