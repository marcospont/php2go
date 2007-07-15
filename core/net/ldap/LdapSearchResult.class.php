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
 * LDAP Search Result class
 *
 * This class handles the results of an LDAP query. The result entries
 * are parsed and returned as instances of the {@link LdapEntry} class.
 *
 * @package net
 * @subpackage ldap
 * @uses LdapEntry
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LdapSearchResult extends PHP2Go
{
	/**
	 * Connection handle
	 *
	 * @var resource
	 * @access private
	 */
	var $conn;
	/**
	 * Result handle
	 *
	 * @var resource
	 * @access private
	 */
	var $handle;
	/**
	 * Last fetched entry
	 *
	 * @var resource
	 * @access private
	 */
	var $entry;

	/**
	 * Class constructor
	 *
	 * @param resource &$conn Connection handle
	 * @param resource &$handle Result handle
	 * @return LdapSearchResult
	 */
	function LdapSearchResult(&$conn, &$handle) {
		parent::PHP2Go();
		$this->conn = (TypeUtils::isInstanceOf($conn, 'LdapClient') ? $conn->getHandle() : $conn);
		$this->handle = $handle;
		$this->entry = ($this->handle ? ldap_first_entry($this->conn, $this->handle) : NULL);
		$this->attrs = $this->_parseAttrs($this->entry);
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		if (is_resource($this->handle)) {
			@ldap_free_result($this->handle);
			unset($this->handle);
		}
	}

	/**
	 * Get total number of entries
	 *
	 * @return int
	 */
	function getCount() {
		if ($this->handle)
			return ldap_count_entries($this->conn, $this->handle);
		return 0;
	}

	/**
	 * Get all entries as an array
	 *
	 * @uses LdapEntry::createFromResult()
	 * @return array
	 */
	function getAllEntries() {
		if ($this->handle) {
			$entries = array();
			$entry = ldap_first_entry($this->conn, $this->handle);
			$entries[] = LdapEntry::createFromResult($this->conn, $entry);
			while ($entry = ldap_next_entry($this->conn, $entry))
				$entries[] = LdapEntry::createFromResult($this->conn, $entry);
			return $entries;
		}
		return FALSE;
	}

	/**
	 * Creates a data set and fills it with all search result entries
	 *
	 * @uses DataSet::factory()
	 * @return DataSet
	 */
	function &createDataSet() {
		import('php2go.data.DataSet');
		$Dataset =& DataSet::factory('array');
		$Dataset->load($this->getAllEntries());
		return $Dataset;
	}

	/**
	 * Sort entries by a given set of attributes
	 *
	 * All sort attributes must be present
	 * in the LDAP entries.
	 *
	 * @param array $attrs Sort attributes
	 */
	function sortEntries($attrs=array()) {
		if ($this->handle && is_array($attrs)) {
			foreach ($attrs as $attr) {
				if (in_array($attr, $this->attrs)) {
					$bool = @ldap_sort($this->conn, $this->handle, $attr);
					if (!$bool) {
						PHP2Go::raiseError(vsprintf("Erro ao executar o comando %s no servidor LDAP: %s", array('sort', $this->_getErrorMessage())));
					}
				}
			}
		}
	}

	/**
	 * Fetches the next result entry
	 *
	 * @uses LdapEntry::createFromResult()
	 * @return LdapEntry|FALSE
	 */
	function fetchEntry() {
		if ($this->entry) {
			$entry = LdapEntry::createFromResult($this->conn, $this->entry);
			$this->entry = ldap_next_entry($this->conn, $this->entry);
			return $entry;
		}
		return FALSE;
	}

	/**
	 * Checks if there are more entries to fetch
	 *
	 * @return bool
	 */
	function hasNextEntry() {
		return !!$this->entry;
	}

	/**
	 * Parses an error message from the LDAP server
	 *
	 * @return string
	 * @access private
	 */
	function _getErrorMessage() {
		$err = @ldap_errno($this->handle);
		if ($err)
			return @ldap_err2str($err);
		return "Erro desconhecido";
	}

	/**
	 * Parses the attributes of an LDAP entry
	 *
	 * @param resource $entry Entry handle
	 * @access private
	 * @return array
	 */
	function _parseAttrs($entry) {
		$result = array();
		if ($entry) {
			$attrs = ldap_get_attributes($this->conn, $entry);
			foreach ($attrs as $key => $value) {
				if (is_string($key) && $key != 'count')
					$result[] = $key;
			}
		}
		return $result;
	}
}
?>