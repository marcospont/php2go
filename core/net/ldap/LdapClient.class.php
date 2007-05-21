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

import('php2go.net.ldap.LdapSearchResult');
import('php2go.net.ldap.LdapEntry');

/**
 * LDAP default port
 */
define('LDAP_DEFAULT_PORT', 389);
/**
 * Single level search scope
 */
define('LDAP_SEARCH_SINGLE_LEVEL', 1);
/**
 * Base search scope
 */
define('LDAP_SEARCH_BASE', 2);
/**
 * Subtree search scope
 */
define('LDAP_SEARCH_SUBTREE', 3);

/**
 * LDAP client class
 *
 * Implementation of an LDAP client, which is able to
 * search for entries, add new entries and modify or
 * delete existing entries.
 *
 * Example:
 * <code>
 * $ldap = new LdapClient(array(
 *   'host' => 'ldap.mydomain.com',
 *   'bindDN' => 'cn=Administrator,dc=yourcompany,dc=com',
 *   'bindPassword' => 'password',
 *   'baseDN' => 'dc=yourcompany,dc=com'
 * ));
 * if ($ldap->connect()) {
 *   $result = $ldap->search('(cn=*)');
 *   while ($entry = $result->fetchEntry()) {
 *     println($entry->getAttribute('cn');
 *   }
 *   $ldap->close();
 * }
 * </code>
 *
 * @package net
 * @subpackage ldap
 * @uses LdapSearchResult
 * @uses LdapEntry
 * @uses System
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LdapClient extends PHP2Go
{
	/**
	 * LDAP protocol version
	 *
	 * @var int
	 */
	var $protocolVersion = 3;
	/**
	 * LDAP host
	 *
	 * @var string
	 */
	var $host = 'localhost';
	/**
	 * LDAP port
	 *
	 * @var int
	 */
	var $port = LDAP_DEFAULT_PORT;
	/**
	 * Base DN used on searches
	 *
	 * @var string
	 */
	var $baseDN;
	/**
	 * DN used to bind
	 *
	 * @var string
	 */
	var $bindDN;
	/**
	 * Password used to bind
	 *
	 * @var string
	 */
	var $bindPassword;
	/**
	 * Enable or disable TLS (Transport Layer Security)
	 *
	 * @var bool
	 */
	var $useTLS = TRUE;
	/**
	 * Connection handle
	 *
	 * @var resource
	 * @access private
	 */
	var $handle;

	/**
	 * Class constructor
	 *
	 * Allowed config arguments:
	 * # host
	 * # bindDN
	 * # bindPassword
	 * # baseDN
	 * # useTLS
	 * # protocolVersion
	 *
	 * @param array $config Configuration parameters
	 * @return LdapClient
	 */
	function LdapClient($config=array()) {
		parent::PHP2Go();
		if (!System::loadExtension("ldap"))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', "ldap"), E_USER_ERROR, __FILE__, __LINE__);
		if (isset($config['host']))
			$this->setServer($config['host'], @$config['port']);
		if (isset($config['baseDN']))
			$this->setBaseDN($config['baseDN']);
		if (isset($config['bindDN']) && isset($config['bindPassword']))
			$this->setBindParameters($config['bindDN'], $config['bindPassword']);
		if (array_key_exists('useTLS', $config))
			$this->useTLS = (bool)$config['useTLS'];
		if (array_key_exists('protocolVersion', $config))
			$this->protocolVersion = $config['protocolVersion'];
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 *
	 * Closes the LDAP connection if opened.
	 */
	function __destruct() {
		$this->close();
	}

	/**
	 * Set LDAP protocol version
	 *
	 * @param int $version Protocol version
	 */
	function setProtocolVersion($version) {
		$this->protocolVersion = $version;
	}

	/**
	 * Set LDAP host and port
	 *
	 * @param string $host Host name or IP address
	 * @param int $port Port number
	 */
	function setServer($host, $port=NULL) {
		if (!$this->isConnected()) {
			$this->host = $host;
			if ($port > 0)
				$this->port = $port;
		}
	}

	/**
	 * Set base DN used to perform searches
	 *
	 * @param string $baseDN Base DN
	 */
	function setBaseDN($baseDN) {
		$this->baseDN = $baseDN;
	}

	/**
	 * Set bind parameters
	 *
	 * When using LDAP, $bindDN is a distinguished name.
	 * When using AD, $bindDN is an email address.
	 *
	 * Examples:
	 * <code>
	 * /* LDAP {@*}
	 * $client = new LdapClient();
	 * $client->setServer('ldap.yourcompany.com', 389);
	 * $client->setBindParameters('cn=Administrator,dc=yourcompany,dc=com', 'secret');
	 * /* Microsoft AD {@*}
	 * $client = new LdapClient();
	 * $client->setServer('ad.yourcompany.com', 389);
	 * $client->setBindParameters('admin@yourcompany.com', 'secret');
	 * </code>
	 *
	 * @param string $bindDN Bind DN
	 * @param string $bindPassword Bind password
	 */
	function setBindParameters($bindDN, $bindPassword) {
		if (!$this->isConnected()) {
			$this->bindDN = $bindDN;
			$this->bindPassword = $bindPassword;
		}
	}

	/**
	 * Opens the connection with the LDAP server
	 *
	 * @return bool
	 */
	function connect() {
		if (!$this->isConnected()) {
			$this->handle = @ldap_connect($this->host, $this->port);
			if ($this->handle !== FALSE) {
				if ($this->setOption(LDAP_OPT_PROTOCOL_VERSION, $this->protocolVersion)) {
					// start TLS when requested
					if ($this->useTLS && function_exists('ldap_start_tls')) {
						@ldap_start_tls($this->handle);
						if (ldap_errno($this->handle) != 0) {
							@ldap_close($this->handle);
							$this->handle = @ldap_connect($this->host, $this->port);
							$this->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
						}
					}
					// bind
					$bind = (!empty($this->bindDN) && !empty($this->bindPassword) ? @ldap_bind($this->handle, $this->bindDN, $this->bindPassword) : @ldap_bind($this->handle));
					if ($bind === FALSE) {
						@ldap_close($this->handle);
						PHP2Go::raiseError(vsprintf("Erro ao executar o comando %s no servidor LDAP: %s", array('bind', $this->_getErrorMessage())), E_USER_ERROR, __FILE__, __LINE__);
						return FALSE;
					}
					return TRUE;
				}
				return FALSE;
			}
			PHP2Go::raiseError(vsprintf("Não foi possível abrir uma conexão com o servidor LDAP %s, na porta %s", array($this->host, $this->port)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
	}

	/**
	 * Checks if connection is active
	 *
	 * @return bool
	 */
	function isConnected() {
		return (isset($this->handle) && is_resource($this->handle));
	}

	/**
	 * Get connection handle
	 *
	 * @return resource
	 */
	function &getHandle() {
		return $this->handle;
	}

	/**
	 * Reads an option from the LDAP server
	 *
	 * @param int $option Option number
	 * @link http://br2.php.net/ldap#ldap.constants
	 * @return mixed
	 */
	function getOption($option) {
		$this->connect();
		$res = @ldap_get_option($this->handle, $option);
		if ($res === FALSE) {
			PHP2Go::raiseError(vsprintf("Erro ao executar o comando %s no servidor LDAP: %s", array('get_option', $this->_getErrorMessage())), E_USER_ERROR, __FILE__, __LINE__);
			return NULL;
		}
		return $res;
	}

	/**
	 * Changes an option on the LDAP server
	 *
	 * @param int $option Option number
	 * @param mixed $value Option value
	 * @link http://br2.php.net/ldap#ldap.constants
	 * @return bool
	 */
	function setOption($option, $value) {
		$this->connect();
		$res = @ldap_set_option($this->handle, $option, $value);
		if ($res === FALSE) {
			@ldap_close($this->handle);
			PHP2Go::raiseError(vsprintf("Erro ao executar o comando %s no servidor LDAP: %s", array('set_option', $this->_getErrorMessage())), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Performs a search on the LDAP server
	 *
	 * Allowed keys for $settings:
	 * # sizeLimit : limit of entries to be fetched
	 * # timeLimit : search timeout
	 * # attrsOnly : set this to 1 if only attribute types are wanted
	 * # attributes : required attributes
	 * # scope : search scope ({@link LDAP_SEARCH_SINGLE_LEVEL}, {@link LDAP_SEARCH_BASE} or {@link LDAP_SEARCH_SUBTREE})
	 *
	 * @param string $filter Search filter
	 * @param array $settings Search settings
	 * @return LdapSearchResult|NULL
	 */
	function search($filter, $settings=array(), $sort=array()) {
		$this->connect();
		if (empty($this->baseDN))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_LDAP_MISSING_BASEDN'), E_USER_ERROR, __FILE__, __LINE__);
		$sizeLimit = (isset($settings['sizeLimit']) ? $settings['sizeLimit'] : 0);
		$timeLimit = (isset($settings['timeLimit']) ? $settings['timeLimit'] : 0);
		$attrsOnly = (isset($settings['attrsOnly']) ? $settings['attrsOnly'] : 0);
		$attributes = (is_array($settings['attributes']) ? array_values($settings['attributes']) : array());
		$scope = @$settings['scope'];
		switch ($scope) {
			case LDAP_SEARCH_SINGLE_LEVEL :
				$func = 'ldap_list';
				break;
			case LDAP_SEARCH_BASE :
				$func = 'ldap_read';
				break;
			default :
				$func = 'ldap_search';
				break;
		}
		$search = @call_user_func_array($func, array($this->handle, $this->baseDN, $filter, $attributes, $attrsOnly, $sizeLimit, $timeLimit));
		if (!$search) {
			$err = @ldap_errno($this->handle);
			if ($err != 32) {
				PHP2Go::raiseError(vsprintf("Ocorreu um erro ao pesquisar no servidor LDAP usando o filtro %s: %s", array($filter, $this->_getErrorMessage())), E_USER_ERROR, __FILE__, __LINE__);
				return NULL;
			}
		}
		$result = new LdapSearchResult($this, $search);
		if (!empty($sort))
			$result->sortEntries($sort);
		return $result;
	}

	/**
	 * Searches for a given DN in the LDAP directory
	 *
	 * Returns FALSE in case of error or entry not found.
	 * When DN is incomplete, base DN will be added.
	 *
	 * Examples:
	 * <code>
	 * $client = new LdapClient(array(
	 *   'bindDN' => 'cn=Administrator,dc=yourcompany,dc=com',
	 *   'bindPassword' => 'secret',
	 *   'baseDN' => 'dc=yourcompany,dc=com'
	 * ));
	 * $entry->find('uid=foo');
	 * $entry->find('uid=foo,ou=people,dc=yourcompany,dc=com');
	 * </code>
	 *
	 * @param string $dn Entry DN
	 * @return LdapEntry|FALSE
	 */
	function find($dn) {
		$this->connect();
		$tmp = explode(',', $dn, 2);
		if (sizeof($tmp)) {
			$filter = $tmp[0];
			$base = $this->baseDN;
		} else {
			$filter = $tmp[0];
			$base = $tmp[1];
		}
		$search = @ldap_list($this->handle, $base, $filter);
		if ($search && ($entry = ldap_first_entry($this->handle, $search)))
			return LdapEntry::createFromResult($this->handle, $entry);
		return FALSE;
	}

	/**
	 * Adds a new entry on the LDAP server
	 *
	 * Example:
	 * <code>
	 * import('php2go.net.ldap.LdapClient');
	 * import('php2go.security.UnixCrypt');
	 * $ldap = new LdapClient(array(
	 *   'host' => 'ldap.yourdomain.com',
	 *   'bindDN' => 'cn=Administrator,dc=yourcompany,dc=com',
	 *   'bindPassword' => '123456',
	 *   'baseDN' => 'dc=yourcompany,dc=com'
	 * ));
	 * $entry = new LdapEntry('uid=foo,dc=yourcompany,dc=com', array(
	 *   'cn' => 'Foo Bar',
	 *   'gn' => 'Foo',
	 *   'sn' => 'Bar',
	 *   'uid' => 'foo',
	 *   'userPassword' => UnixCrypt::encrypt('123456'),
	 *   'objectClass' => 'inetOrgPerson'
	 * ));
	 * $ldap->add($entry);
	 * </code>
	 *
	 * @param LdapEntry $entry New entry
	 * @return bool
	 */
	function add($entry) {
		if (TypeUtils::isInstanceOf($entry, 'LdapEntry')) {
			$this->connect();
			$res = @ldap_add($this->handle, $entry->getDN(), $entry->getAttributes());
			if (!$res) {
				PHP2Go::raiseError(vsprintf("Erro ao executar o comando %s no servidor LDAP: %s", array('add', $this->_getErrorMessage())), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			return TRUE;
		}
		return FALSE;
	}

	function update($entry) {
		if (TypeUtils::isInstanceOf($entry, 'LdapEntry')) {
			$this->connect();

		}
	}

	/**
	 * Deletes an entry from the LDAP server
	 *
	 * @param LdapEntry|string $entry Entry object or entry DN
	 * @param bool $recursive Delete entry's children recursively
	 * @return bool
	 */
	function delete($entry, $recursive=TRUE) {
		$this->connect();
		if (TypeUtils::isInstanceOf($entry, 'LdapEntry'))
			$dn = $entry->getDN();
		else
			$dn = strval($entry);
		// recursively delete entry and all its children
		if ($recursive) {
			$res = @ldap_list($this->handle, $dn, '(objectClass=*)', array(NULL));
			if (@ldap_count_entries($this->handle, $res)) {
				$child = @ldap_first_entry($this->handle, $res);
				$this->delete(@ldap_get_dn($this->handle, $child));
				while ($child = @ldap_next_entry($this->handle, $child))
					$this->delete(@ldap_get_dn($this->handle, $child));
			}
		}
		// delete a DN
		$res = @ldap_delete($this->handle, $dn);
		if ($res === FALSE) {
			$err = @ldap_errno($this->handle);
			if ($err == 66)
				PHP2Go::raiseError(vsprintf("Não foi possível deletar a entrada <b>%s</b>. Tente utilizar o parâmetro \$recursive.", array($dn)), E_USER_ERROR, __FILE__, __LINE__);
			else
				PHP2Go::raiseError(vsprintf("Erro ao executar o comando %s no servidor LDAP: %s", array('delete', $this->_getErrorMessage())), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Closes the LDAP connection
	 */
	function close() {
		if ($this->isConnected()) {
			@ldap_unbind($this->handle);
			unset($this->handle);
		}
	}

	/**
	 * Retrieves an error message from the server
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
	 * Wrapper over the function {@link ldap_explode_dn()}
	 *
	 * Ensures that a DN is properly escaped and encoded.
	 *
	 * @param string $dn DN
	 * @return array Exploded DN
	 * @access private
	 */
	function _explodeDN($dn) {
		$dn = addcslashes($dn, '<>');
		$res = ldap_explode_dn($dn, 0);
		consumeArray($res, 'count');
		foreach ($res as $key => $value)
            $res[$key] = preg_replace("/\\\([0-9A-Fa-f]{2})/e", "''.chr(hexdec('\\1')).''", $value);
        return $res;
	}
}
?>