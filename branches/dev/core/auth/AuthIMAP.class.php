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

import('php2go.auth.Auth');

/**
 * IMAP connection port
 */
define('AUTH_IMAP_PORT', 143);

/**
 * Authentication driver based on an IMAP server
 *
 * Creates an IMAP connection and performs a login attempt
 * using the credentials fetched from the request.
 *
 * To use AuthIMAP as the application authenticator, set
 * AUTH.AUTHENTICATOR_PATH = 'php2go.auth.AuthIMAP'.
 *
 * @package php2go.auth
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AuthIMAP extends Auth
{
	/**
	 * IMAP hostname or IP address
	 *
	 * @var string
	 */
	var $host = 'localhost';

	/**
	 * IMAP port
	 *
	 * @var int
	 */
	var $port = AUTH_IMAP_PORT;

	/**
	 * IMAP connection flags
	 *
	 * @var string
	 */
	var $flags = '';

	/**
	 * Class constructor
	 *
	 * If this is your default authenticator, always retrieve it
	 * using {@link Auth::getInstance()}.
	 *
	 * @param string $sessionName Session name
	 * @return AuthIMAP
	 */
	function AuthIMAP($sessionName=NULL) {
		parent::Auth($sessionName);
		if (!System::loadExtension('imap'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'imap'), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Configure connection properties: {@link $host}, {@link $port} and {@link $flags}
	 *
	 * @param string $host IMAP hostname or IP address
	 * @param int $port IMAP port
	 * @param string $flags IMAP flags
	 */
	function setupConnection($host, $port=NULL, $flags=NULL) {
		$this->host = $host;
		if ((int)$port > 0)
			$this->port = $port;
		$flags = ltrim(trim($flags), '/');
		if (!empty($flags))
			$this->flags = '/' . $flags;
	}

	/**
	 * Opens an IMAP connection using the fetched credentials
	 *
	 * Returns TRUE if the connection attempt was successful.
	 *
	 * @return bool
	 */
	function authenticate() {
		$mailbox = '{' . $this->host . ':' . $this->port . $this->flags . '}';
		$conn = @imap_open($mailbox, $this->_login, $this->_password, OP_HALFOPEN);
		if (TypeUtils::isResource($conn)) {
			@imap_close($conn);
			return TRUE;
		}
		return FALSE;
	}
}
?>