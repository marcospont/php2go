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

import('php2go.auth.Auth');
import('php2go.net.ldap.LdapClient');
import('php2go.security.UnixCrypt');

/**
 * Authentication driver based on an LDAP server
 *
 * Creates an LDAP connection, searches for an
 * entry identified by the provided username and
 * verify if the password is valid.
 *
 * @package auth
 * @uses LdapClient
 * @uses UnixCrypt
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AuthLdap extends Auth
{
	/**
	 * Connection parameters
	 *
	 * @var array
	 */
	var $connParams = array();
	/**
	 * User ID attribute name
	 *
	 * This is the attribute name used to build the
	 * search that tries to find the provided username
	 * on the LDAP server.
	 *
	 * @var string
	 */
	var $uidAttr = 'uid';
	/**
	 * Password attribute name
	 *
	 * This is the attribute name that represents
	 * the user password that must be verified.
	 *
	 * @var string
	 */
	var $pwdAttr = 'userPassword';
	/**
	 * Required user attributes
	 *
	 * @var array
	 */
	var $userAttrs = array();

	/**
	 * Class constructor
	 *
	 * If this is your default authenticator, always retrieve
	 * it using {@link Auth::getInstance()}.
	 *
	 * @param string $sessionName Session name
	 * @return AuthLdap
	 */
	function AuthLdap($sessionName=NULL) {
		parent::Auth($sessionName);
		$this->connParams = array(
			'host' => 'localhost'
		);
	}

	/**
	 * Set connection parameters
	 *
	 * Allowed parameters:
	 * # host : LDAP host
	 * # port : LDAP port
	 * # bindDN : bind DN
	 * # bindPassword : bind password
	 * # baseDN : base DN used to perform the user search
	 * # useTLS : enable/disable TLS/SSL
	 *
	 * @param array $params Parameters
	 */
	function setConnectionParameters($params) {
		if (is_array($params))
			$this->connParams = array_merge($this->connParams, $params);
	}

	/**
	 * Set user ID attribute name
	 *
	 * @param string $attrName
	 */
	function setUIDAttributeName($attrName) {
		$this->uidAttr = $attrName;
	}

	/**
	 * Set password attribute name
	 *
	 * @param string $attrName
	 */
	function setPasswordAttributeName($attrName) {
		$this->pwdAttr = $attrName;
	}

	/**
	 * Defines which attributes must be
	 * loaded from the LDAP server
	 *
	 * @param array $attrs Attributes
	 */
	function setRequiredAttributes($attrs=array()) {
		$this->userAttrs = $attrs;
	}

	/**
	 * Performs the authentication attempt
	 *
	 * Opens an LDAP connection, tries to fetch a
	 * user identified by the given username and
	 * tests the stored user password against the
	 * keyed one.
	 *
	 * @return array|FALSE
	 */
	function authenticate() {
		// instantiate the client and open connection
		$client = new LdapClient($this->connParams);
		if (@$client->connect()) {
			$settings = array();
			// attributes to load
			if (!empty($this->userAttrs)) {
				$settings['attributes'] = $this->userAttrs;
				// password must always be loaded
				if (!in_array('userPassword', $settings['attributes']))
					$settings['attributes'][] = 'userPassword';
			}
			// execute search
			$result = @$client->search("({$this->uidAttr}={$this->_login})", $settings);
			// ensure a single result entry
			if ($result && $result->getCount() == 1) {
				$entry = $result->fetchEntry();
				// verify password
				if ($this->verifyPassword($entry)) {
					$attributes = $entry->getAttributes();
					consumeArray($attributes, 'userPassword');
					return $attributes;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Verifies user password
	 *
	 * @param LdapEntry $entry User entry
	 * @access protected
	 * @return bool
	 */
	function verifyPassword(&$entry) {
		$userPwd = $entry->getAttribute($this->pwdAttr);
		return UnixCrypt::verify($userPwd[0], $this->_password);
	}
}
?>