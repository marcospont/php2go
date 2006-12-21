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
 * Encrypts and verifies data using the standard Unix DES-based encryption algorithm
 *
 * @package security
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class UnixCrypt extends PHP2Go
{
	/**
	 * Encrypts a given value
	 *
	 * @link http://www.php.net/crypt
	 * @param string $data Data to encrypt
	 * @param string $salt Encoding type
	 * @return string
	 * @static
	 */
	function encrypt($data, $salt=NULL) {
		return crypt($data, $salt);
	}

	/**
	 * Verifies data against its encrypted value
	 *
	 * @param string $encrypted Encrypted value
	 * @param string $data Decrypted value
	 * @param string $salt Encoding type
	 * @return bool
	 * @static
	 */
	function verify($encrypted, $data, $salt=NULL) {
		return (UnixCrypt::encrypt($data, $salt) == $encrypted);
	}
}
?>