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
 * LDAP entry class
 *
 * Representation of an LDAP entry, composed by a
 * DN (distinguished name) and a set of attributes.
 *
 * @package net
 * @subpackage ldap
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LdapEntry extends PHP2Go
{
	/**
	 * Entry DN
	 *
	 * @var string
	 * @access private
	 */
	var $dn = '';
	/**
	 * Entry attributes
	 *
	 * @var array
	 * @access private
	 */
	var $attrs = array();
	/**
	 * Attribute changes
	 *
	 * @var array
	 * @access private
	 */
	var $changes = array(
		'dn' => NULL,
		'add' => array(),
		'replace' => array(),
		'delete' => array()
	);
	/**
	 * Original attribute values
	 *
	 * @var array
	 * @access private
	 */
	var $original = array();

	/**
	 * Class constructor
	 *
	 * @param string $dn Entry DN
	 * @param array $attrs Entry attributes
	 * @return LdapEntry
	 */
	function LdapEntry($dn, $attrs=array()) {
		parent::PHP2Go();
		$this->dn = $dn;
		$this->original = $this->attrs = (array)$attrs;
	}

	/**
	 * Parses an entry from a search result
	 *
	 * Creates an LdapEntry instance based on a
	 * connection handle and an entry handle, commonly
	 * returned by {@link ldap_first_entry()} and
	 * {@link ldap_next_entry()} functions.
	 *
	 * @param resource &$conn LDAP connection handle
	 * @param resource &$entry LDAP entry handle
	 * @return LdapEntry
	 * @static
	 */
	function createFromResult(&$conn, &$entry) {
		$dn = ldap_get_dn($conn, $entry);
		$attrs = array();
		$ber = NULL;
		do {
			$attrName = (
				empty($attrName)
				? ldap_first_attribute($conn, $entry, $ber)
				: ldap_next_attribute($conn, $entry, $ber)
			);
			if ($attrName) {
				$attrValue = ldap_get_values($conn, $entry, $attrName);
				consumeArray($attrValue, 'count');
				$attrs[$attrName] = $attrValue;
			}
		} while($attrName);
		return new LdapEntry($dn, $attrs);
	}

	/**
	 * Attribute getter
	 *
	 * @param string $prop Property name
	 * @return mixed
	 */
	function __get($prop) {
		return $this->getAttribute($prop);
	}

	/**
	 * Attribute setter
	 *
	 * @param string $prop Property name
	 * @param mixed $value Property value
	 */
	function __set($prop, $value) {
		$this->setAttribute($prop, $value);
	}

	/**
	 * Get entry DN
	 *
	 * @return string
	 */
	function getDN() {
		return $this->dn;
	}

	/**
	 * Set entry DN
	 *
	 * @param string $dn New entry DN
	 */
	function setDN($dn) {
		if ($dn != $this->dn) {
			$this->changes['dn'] = $this->dn;
			$this->dn = $dn;
		}
	}

	/**
	 * Get all attributes
	 *
	 * @return array
	 */
	function getAttributes() {
		$result = array();
		foreach ($this->attrs as $key => $value) {
			if (sizeof($value) > 1)
				$result[$key] = $value;
			else
				$result[$key] = $value[0];
		}
		return $result;
	}

	/**
	 * Checks if a given attribute exists
	 *
	 * @param string $name Attribute name
	 * @return bool
	 */
	function hasAttribute($name) {
		return (array_key_exists($name, $this->attrs));
	}

	/**
	 * Reads the value of an attribute
	 *
	 * @param string $name Attribute name
	 * @param mixed $fallback Fallback return value
	 * @return mixed
	 */
	function getAttribute($name, $fallback=NULL) {
		if (array_key_exists($name, $this->attrs))
			return $this->attrs[$name];
		return $fallback;
	}

	/**
	 * Tests a given attribute against a regular expression pattern
	 *
	 * @param string $name Attribute name
	 * @param string $pattern Pattern
	 * @return array|FALSE Result matches or FALSE in case of failure
	 */
	function matchAttribute($name, $pattern) {
		if (array_key_exists($name, $this->attrs)) {
			$matches = array();
			foreach ($this->attrs[$name] as $idx => $value) {
				$results = array();
				if (preg_match($pattern, $value, $results)) {
					$matches[] = $results;
				}
			}
			return (!empty($matches) ? $matches : FALSE);
		}
		return FALSE;
	}

	/**
	 * Writes an attribute
	 *
	 * @param string $name Attribute name
	 * @param mixed $value Attribute value
	 */
	function setAttribute($name, $value) {
		if (!empty($value)) {
			// force array type with numeric indexes
			$value = (is_array($value) ? array_values($value) : array($value));
			// existent attribute
			if (array_key_exists($name, $this->attrs)) {
				// attribute is saved
				if (array_key_exists($name, $this->original)) {
					$this->changes['replace'][$name] = $value;
				}
				// attribute is not saved
				else {
					$this->changes['add'][$name] = $value;
					unset($this->changes['delete'][$name]);
				}
			}
			// new attribute
			else {
				$this->changes['add'][$name] = $value;
			}
			$this->attrs[$name] = $value;
		}
	}

	/**
	 * Removes an attribute
	 *
	 * @param string $name Attribute name
	 */
	function removeAttribute($name) {
		if (array_key_exists($name, $this->attrs)) {
			// attribute is saved
			if (array_key_exists($name, $this->original))
				$this->changes['delete'][$name] = NULL;
			// attribute is not saved
			else
				unset($this->changes['delete'][$name]);
			unset($this->changes['add'][$name]);
			unset($this->changes['replace'][$name]);
			unset($this->attrs[$name]);
		}
	}

	/**
	 * Adds new values to an entry's attribute
	 *
	 * If the attribute doesn't exist, it will be created.
	 *
	 * @param string $attr Attribute name
	 * @param array $values New values
	 */
	function addValues($attr, $values) {
		if (!empty($values)) {
			// normalize values
			if (!is_array($values))
				$values = array($values);
			// new attribute
			if (!array_key_exists($attr, $this->attrs)) {
				$this->attrs[$attr] = $values;
				$this->changes['add'][$attr] = $value;
				unset($this->changes['delete'][$attr]);
			}
			// existent attribute
			else {
				// attribute was already replaced
				if (isset($this->changes['replace'][$attr])) {
					$this->attrs[$name] = array_merge($this->attrs[$name], $values);
					$this->changes['replace'][$attr] = $this->attrs[$name];
 				}
 				// add values on the original attribute
 				else {
					for ($i=0; $i<sizeof($values); $i++) {
						if (!in_array($values[$i], $this->attrs[$attr])) {
							$this->attrs[$attr][] = $values[$i];
							// register value to add
							$this->changes['add'][$attr][] = $values[$i];
							// remove values or add new ones?
							$pos = array_search($values[$i], (array)$this->changes['delete'][$attr]);
							if ($pos !== FALSE)
								array_splice($this->changes['delete'][$attr], $pos, 1);
						}
					}
 				}
			}
		}
	}

	/**
	 * Removes values from an entry's attribute
	 *
	 * If all values are removed, the attribute is removed.
	 *
	 * @param string $attr Attribute name
	 * @param array $values Values to remove
	 */
	function removeValues($attr, $values) {
		if (array_key_exists($attr, $this->attrs) && !empty($values)) {
			// normalize values
			if (!is_array($values))
				$values = array($values);
			// initialize delete changes for this attribute
			if (!is_array($this->changes['delete'][$attr]))
				$this->changes['delete'][$attr] = array();
			// attribute is not saved
			if (isset($this->changes['replace'][$attr])) {
				for ($i=0; $i<sizeof($values); $i++) {
					if (in_array($values[$i], $this->changes['replace'][$attr]))
						array_splice($this->changes['replace'][$attr], array_search($values[$i], $this->changes['replace'][$attr]), 1);
				}
				if (empty($this->changes['replace'][$attr]))
					unset($this->changes['replace'][$attr]);
			}
			// attribute is saved
			else {
				for ($i=0; $i<sizeof($values); $i++) {
					if (in_array($values[$i], $this->attrs[$attr])) {
						// find position and remove value
						array_splice($this->attrs[$attr], array_search($values[$i], $this->attrs[$attr]), 1);
						// empty attribute: must be removed
						if (empty($this->attrs[$attr])) {
							$this->removeAttribute($attr);
							break;
						}
						// register removed value
						$this->changes['delete'][$attr][] = $values[$i];
						// remove from added values
						$pos = array_search($values[$i], (array)$this->changes['add'][$attr]);
						if ($pos !== FALSE)
							array_splice($this->changes['add'][$attr], $pos, 1);
					}
				}
			}
		}
	}

	/**
	 * Get all changes made so far
	 *
	 * @return array
	 */
	function &getChanges() {
		return $this->changes;
	}

	/**
	 * Confirm changes
	 *
	 * Used by {@link LdapClient::update()} to reset
	 * internal entry changes after a successful update
	 * operation.
	 */
	function confirmChanges() {
		$this->changes = array(
			'dn' => NULL,
			'add' => array(),
			'replace' => array(),
			'delete' => array()
		);
		$this->original = $this->attrs;
	}

	/**
	 * Implements data set row interface
	 */
	function toArray() {
		$attrs = $this->getAttributes();
		$attrs['dn'] = $this->dn;
		return $attrs;
	}
}
?>