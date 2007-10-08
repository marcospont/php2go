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
import('php2go.net.Pop3');

/**
 * Authentication driver based on a POP3 server
 *
 * Creates a POP3 connection and performs a login attempt
 * using the credentials fetched from the request.
 *
 * To use AuthPOP3 as the application authenticator, set
 * AUTH.AUTHENTICATOR_PATH = 'php2go.auth.AuthPOP3'.
 *
 * @package auth
 * @uses Pop3
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AuthPop3 extends Auth
{
	/**
	 * POP3 hostname or IP address
	 *
	 * @var string
	 */
	var $host = 'localhost';

	/**
	 * POP3 port. Defaults to {@link POP3_DEFAULT_PORT}.
	 *
	 * @var int
	 */
	var $port;

	/**
	 * POP3 connection timeout, in seconds. Defaults to {@link POP3_DEFAULT_TIMEOUT}.
	 *
	 * @var int
	 */
	var $timeout;

	/**
	 * POP3 client used by the class
	 *
	 * @var Pop3
	 * @access private
	 */
	var $Pop = NULL;

	/**
	 * Class constructor
	 *
	 * If this is your default authenticator, always retrieve it
	 * using {@link Auth::getInstance()}.
	 *
	 * @param string $sessionName Session name
	 * @return AuthPop3
	 */
	function AuthPop3($sessionName=NULL) {
		parent::Auth($sessionName);
		$this->Pop = new Pop3();
		$this->port = POP3_DEFAULT_PORT;
		$this->timeout = POP3_DEFAULT_TIMEOUT;
	}

	/**
	 * Configure connection properties: {@link $host}, {@link $port} and {@link $timeout}
	 *
	 * @param string $host POP3 hostname or IP address
	 * @param int $port POP3 port
	 * @param int $timeout POP3 connection timeout
	 */
	function setupConnection($host, $port=NULL, $timeout=NULL) {
		$this->host = $host;
		if ((int)$port > 0)
			$this->port = $port;
		if ((int)$timeout > 0)
			$this->timeout = $timeout;
	}

	/**
	 * Opens a POP3 connection and performs a login attempt using the
	 * credentials fetched from the request
	 *
	 * @return bool
	 */
	function authenticate() {
		$result = FALSE;
		if (@$this->Pop->connect($this->host, $this->port, $this->timeout)) {
			$result = @$this->Pop->login($this->_login, $this->_password);
			$this->Pop->close();
		}
		return $result;
	}
}
?>