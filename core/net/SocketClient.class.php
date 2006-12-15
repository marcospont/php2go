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
 * Client class that creates and manages UNIX sockets
 *
 * @package net
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class SocketClient extends PHP2Go
{
	/**
	 * Active host
	 *
	 * @var string
	 */
	var $host = '';

	/**
	 * Active port
	 *
	 * @var int
	 */
	var $port = 0;

	/**
	 * Indicates if blocking mode is enabled
	 *
	 * @var bool
	 */
	var $blocking = TRUE;

	/**
	 * Indicates if connection is persistent
	 *
	 * @var bool
	 */
	var $persistent = FALSE;

	/**
	 * Socket timeout
	 *
	 * @var int|bool
	 */
	var $timeout = FALSE;

	/**
	 * Read buffer size
	 *
	 * @var int
	 */
	var $bufferSize = 2048;

	/**
	 * Line end characters
	 *
	 * @var string
	 */
	var $lineEnd = "\r\n";

	/**
	 * Last error message
	 *
	 * @var string
	 */
	var $errorMsg = '';

	/**
	 * Socket stream
	 *
	 * @var resource
	 * @access private
	 */
	var $stream;

	/**
	 * Class constructor
	 *
	 * @return SocketClient
	 */
	function SocketClient() {
		parent::PHP2Go();
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		$this->close();
	}

	/**
	 * Get active remote host
	 *
	 * @return string
	 */
	function getRemoteHost() {
		return $this->host;
	}

	/**
	 * Get active remote port
	 *
	 * @return int
	 */
	function getRemotePort() {
		return $this->port;
	}

	/**
	 * Check if blocking mode is enabled
	 *
	 * @return bool
	 */
	function isBlocking() {
		return $this->blocking;
	}

	/**
	 * Enable/disable blocking mode on the current connection
	 *
	 * This method simply returns FALSE when there's no
	 * active socket connection.
	 *
	 * @param bool $setting Enable/disable
	 * @return bool
	 */
	function setBlocking($setting) {
		if ($this->isConnected()) {
			return @socket_set_blocking($this->stream, (bool)$setting);
		} else {
			return FALSE;
		}
	}

	/**
	 * Check if persistent mode is enabled
	 *
	 * @return bool
	 */
	function isPersistent() {
		return $this->persistent;
	}

	/**
	 * Get socket timeout
	 *
	 * @return int
	 */
	function getTimeout() {
		return $this->timeout;
	}

	/**
	 * Check if the active connection is timed out
	 *
	 * @return bool
	 */
	function isTimedOut() {
		if ($this->isConnected()) {
			if ($status = $this->getStatus())
				return $status['timed_out'];
		}
		return FALSE;
	}

	/**
	 * Set a new timeout for the active socket connection
	 *
	 * @param int $timeout Timeout, in seconds
	 * @return bool
	 */
	function setTimeout($timeout) {
		if ($this->isConnected()) {
			$seconds = TypeUtils::parseInteger($timeout);
			$microseconds = $timeout % $seconds;
			return @socket_set_timeout($this->stream, $seconds, $microseconds);
		} else {
			return FALSE;
		}
	}

	/**
	 * Get last error message
	 *
	 * @return string|FALSE
	 */
	function getLastError() {
		return (!empty($this->errorMsg)) ? $this->errorMsg : FALSE;
	}

	/**
	 * Check if the socket connection is active
	 *
	 * @return bool
	 */
	function isConnected() {
		if (!isset($this->stream) || !is_resource($this->stream))
			return FALSE;
		return TRUE;
	}

	/**
	 * Get the status of the current connection
	 *
	 * @link http://www.php.net/socket_get_status
	 * @return array|bool
	 */
	function getStatus() {
		if ($this->isConnected()) {
			$status = @socket_get_status($this->stream);
			if (is_array($status)) {
				return $status;
			}
		}
		return FALSE;
	}

	/**
	 * Set read buffer size
	 *
	 * The default buffer size for read operations is 2048 bytes.
	 *
	 * @param int $bufferSize Buffer size
	 */
	function setBufferSize($bufferSize) {
		$this->bufferSize = TypeUtils::parseIntegerPositive($bufferSize);
	}

	/**
	 * Set line end characters
	 *
	 * The default line end characters are "\r\n".
	 *
	 * @param string $lineEnd Chars
	 */
	function setLineEnd($lineEnd) {
		$this->lineEnd = $lineEnd;
	}

	/**
	 * Creates a socket using the given remote host and port
	 *
	 * @param string $host Host or IP address
	 * @param int $port Port
	 * @param bool $persistent Should this socket be persistent?
	 * @param int|NULL $timeout Connection timeout, in seconds
	 * @return bool
	 */
	function connect($host='', $port=0, $persistent=FALSE, $timeout=NULL) {
		if (!$this->host = $this->_checkHostAddress($host)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_HOST_INVALID', $host), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$this->port = TypeUtils::parseIntegerPositive($port % 65536);
		$this->persistent = (bool)$persistent;
		$this->timeout = TypeUtils::ifNull($timeout, FALSE);
		if ($this->isConnected())
			$this->close();
		$errNo = NULL;
		$errMsg = NULL;
		$openFunc = $this->persistent ? 'pfsockopen' : 'fsockopen';
		if (is_numeric($this->timeout)) {
			$this->stream = @$openFunc($this->host, $this->port, $errNo, $errMsg, $this->timeout);
		} else {
			$this->stream = @$openFunc($this->host, $this->port, $errNo, $errMsg);
		}
		if (!$this->stream) {
			$errDetail = ($errNo > 0 ? '<br>[Error ' . $errNo . '] - ' . $errMsg : '');
			$this->errorMsg = PHP2Go::getLangVal('ERR_CANT_OPEN_SOCKET', array($this->port, $this->host, $errDetail));
			PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			@socket_set_blocking($this->stream, $this->blocking);
			return TRUE;
		}
	}

	/**
	 * Reads an amount of bytes from the socket connection
	 *
	 * Returns FALSE if the socket is not active or if the
	 * connection is timed out.
	 *
	 * @param int $size Amount of bytes
	 * @return string|bool
	 */
	function read($size=1) {
		if ($this->isConnected()) {
			if ($content = @fread($this->stream, $size)) {
				return $content;
			} else if ($this->isTimedOut()) {
				$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_TIMEOUT');
			}
			return FALSE;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}

	/**
	 * Reads a char from the socket
	 *
	 * Returns FALSE if the socket is not active or if the
	 * connection is timed out.
	 *
	 * @return string|bool
	 */
	function readChar() {
		if ($buffer = $this->read())
			return ord($buffer);
		return FALSE;
	}

	/**
	 * Reads a word from the socket
	 *
	 * Returns FALSE if the socket is not active or if the
	 * connection is timed out.
	 *
	 * @return string|bool
	 */
	function readWord() {
		if ($buffer = $this->read(2))
			return (ord($buffer[0]) + (ord($buffer[1]) << 8));
		return FALSE;
	}

	/**
	 * Reads an integer number from the socket
	 *
	 * Returns FALSE if the socket is not active or if the
	 * connection is timed out.
	 *
	 * @return int|bool
	 */
	function readInteger() {
		if ($buffer = $this->read(4))
			return (ord($buffer[0]) + (ord($buffer[1]) << 8) + (ord($buffer[2]) << 16) + (ord($buffer[3]) << 24));
		return FALSE;
	}

	/**
	 * Reads a string from the socket connection
	 *
	 * Returns FALSE if the socket is not active.
	 *
	 * @return string|bool
	 */
	function readString() {
		if ($this->isConnected()) {
			$string = '';
			while (!$this->eof() && ($char = $this->read()) != "\x00") {
				$string .= $char;
			}
			return $string;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}

	/**
	 * Reads a line from the socket connection
	 *
	 * Read blocks of {@link bufferSize} bytes, until a line
	 * end char is found, or the socket eof.
	 *
	 * Returns FALSE if the socket is not active or if the
	 * connection is timed out.
	 *
	 * @return string|bool
	 */
	function readLine() {
		if ($this->isConnected()) {
			$line = '';
			$timeout = time() + $this->timeout;
			while (!$this->eof() && (!$this->timeout || time() < $timeout)) {
				$line .= @fgets($this->stream, $this->bufferSize);
				if (strlen($line) >= 2 && (substr($line, -2) == "\r\n" || substr($line, -1) == "\n")) {
					return $line;
				}
			}
			return $line;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}

	/**
	 * Read all remaining contents from the socket connection
	 *
	 * Read blocks of {@link bufferSize} bytes, until eof is reached.
	 *
	 * @return string|bool
	 */
	function readAllContents() {
		if ($this->isConnected()) {
			$buffer = '';
			while (!$this->eof())
				$buffer .= $this->read($this->bufferSize);
			return $buffer;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}

	/**
	 * Writes a string on the socket connection
	 *
	 * @param string $str Input string
	 * @return bool
	 */
	function write($str) {
		if ($this->isConnected()) {
			if (@fwrite($this->stream, $str, strlen($str))) {
				return TRUE;
			} else if ($this->isTimedOut()) {
				$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_TIMEOUT');
			}
			return FALSE;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}

	/**
	 * Writes a line on the socket connection
	 *
	 * @param string $line Line contents, without the line end char
	 * @return bool
	 */
	function writeLine($line) {
		return $this->write($line . $this->lineEnd);
	}

	/**
	 * Check if socket eof was reached
	 *
	 * @return bool
	 */
	function eof() {
		return ($this->isConnected() && @feof($this->stream));
	}

	/**
	 * Closes the current connection
	 *
	 * @return bool
	 */
	function close() {
		if ($this->isConnected()) {
			@fclose($this->stream);
			unset($this->stream);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Clears the last captured error message
	 */
	function resetError() {
		unset($this->errorMsg);
	}

	/**
	 * Validate a host before opening a socket connection
	 *
	 * @param string $host Host name or IP address
	 * @access private
	 * @return bool
	 */
	function _checkHostAddress($host) {
		if (ereg("[a-zA-Z]+", $host)) {
			return gethostbyname($host);
		} else if (strspn($host, '0123456789.') == strlen($host)) {
			return $host;
		} else {
			return FALSE;
		}
	}
}
?>