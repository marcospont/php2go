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
 * TripleDES is the default cipher algorithm
 */
define('CRYPT_DEFAULT_CIPHER', MCRYPT_3DES);

/**
 * CFB (Cipher Feedback) is the default cipher mode
 */
define('CRYPT_DEFAULT_MODE', MCRYPT_MODE_CFB);

/**
 * Encrypts and decrypts data using the mcrypt extension
 *
 * The Crypt class is an abstraction layer over the functions provided
 * by the mcrypt extension, which supports a wide variety of algorithms
 * to encrypt/decrypt data, such as DES, TripleDES, Blowfish, and much
 * more.
 *
 * Example:
 * <code>
 * $crypt = new Crypt();
 * $crypt->setCipher(MCRYPT_BLOWFISH);
 * $crypt->setCipherMode(MCRYPT_MODE_CBC);
 * $crypt->setKey('this is the key');
 * $encrypted = $crypt->engineEncrypt('secret data');
 * $decrypted = $crypt->engineDecrypt($encrypted);
 * </code>
 *
 * @package security
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Crypt extends PHP2Go
{
	/**
	 * Current cipher algorithm
	 *
	 * @var string
	 */
	var $cipher = CRYPT_DEFAULT_CIPHER;

	/**
	 * Current cipher mode
	 *
	 * @var string
	 */
	var $cipherMode = CRYPT_DEFAULT_MODE;

	/**
	 * Cipher key
	 *
	 * @var string
	 */
	var $key;

	/**
	 * Internal handle, provided my {@link mcrypt_generic_init()}
	 *
	 * @access private
	 * @var resource
	 */
	var $handle;

	/**
	 * Initialization vector
	 *
	 * @access private
	 * @var string
	 */
	var $iv;

	/**
	 * Class constructor
	 *
	 * @return Crypt
	 */
	function Crypt() {
		parent::PHP2Go();
		if (!System::loadExtension('mcrypt'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'mcrypt'), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Get all available cipher algorithms
	 *
	 * @return array
	 */
	function getCipherList() {
		return mcrypt_list_algorithms();
	}

	/**
	 * Get current cipher
	 *
	 * @return string
	 */
	function getCipher() {
		return $this->cipher;
	}

	/**
	 * Set cipher algorithm
	 *
	 * @param string $cipher New cipher
	 * @return bool
	 */
	function setCipher($cipher) {
		if (in_array($cipher, $this->getCipherList()) && $cipher != $this->cipher) {
			$this->cipher = $cipher;
			if (isset($this->key))
				$this->clearKey();
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get all available cipher modes
	 *
	 * @return array
	 */
	function getModeList() {
		return mcrypt_list_modes();
	}

	/**
	 * Get current cipher mode
	 *
	 * @return string
	 */
	function getCipherMode() {
		return $this->cipherMode;
	}

	/**
	 * Set cipher mode
	 *
	 * @param string $cipherMode New cipher mode
	 * @return bool
	 */
	function setCipherMode($cipherMode) {
		if (in_array($cipherMode, $this->getModeList()) && $cipherMode != $this->cipherMode) {
			$this->cipherMode = $cipherMode;
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get cipher key
	 *
	 * @return string
	 */
	function getKey() {
		if (!isset($this->key))
			return NULL;
		return $this->key;
	}


	/**
	 * Set cipher key
	 *
	 * @param string $key
	 */
	function setKey($key) {
		if (!empty($key)) {
			$keySize = @mcrypt_get_key_size($this->cipher, $this->cipherMode);
			if (strlen($key) < $keySize)
				$this->key = md5($key);
			elseif (strlen($key) > $keySize)
				$this->key = substr($key, 0, $keySize);
			$this->key = $key;
		}

	}

	/**
	 * Unset the cipher key
	 */
	function clearKey() {
		unset($this->key);
	}

	/**
	 * Encrypt the passed $data, using current configurations
	 * for cipher, cipher mode and cipher key
	 *
	 * @uses _initialize()
	 * @param string $data Data to encrypt
	 * @param string $saveTo Optional file where encrypted data must be saved
	 * @return string|bool Encrypted data or FALSE in case of errors
	 */
	function engineEncrypt($data, $saveTo='') {
		if ($this->_initialize()) {
			mcrypt_generic_init($this->handle, $this->key, $this->iv);
			$encryptedData = mcrypt_generic($this->handle, $data);
			mcrypt_generic_deinit($this->handle);
			mcrypt_module_close($this->handle);
			if (trim($saveTo) != '') {
				$fp = @fopen($saveTo, 'wb');
				if ($fp === FALSE) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $saveTo), E_USER_ERROR, __FILE__, __LINE__);
					return FALSE;
				} else {
					$result = base64_encode($this->iv . $encryptedData);
					fputs($fp, $result);
					fclose($fp);
					return $result;
				}
			}
			return base64_encode($this->iv . $encryptedData);
		}
		return FALSE;
	}

	/**
	 * Encrypts the contents of a file, saving the results in another file
	 *
	 * @param string $inFileName Input file name
	 * @param string $outFileName Output file name
	 * @return bool
	 */
	function engineEncryptFile($inFileName, $outFileName) {
		$in = @file_get_contents($inFileName);
		if ($in === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $inFileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$out = @fopen($outFileName, 'wb');
		if ($out === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CAT_WRITE_FILE', $outFileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if ($encrypted = $this->engineEncrypt($in)) {
			fputs($out, $encrypted);
			fclose($out);
			return TRUE;
		}
		fclose($out);
		return FALSE;
	}

	/**
	 * Decrypts the passed $data, using current configurations
	 * for cipher, cipher mode and cipher key
	 *
	 * @uses _initialize()
	 * @param string $data Data to decrypt
	 * @param string $saveTo Optional file where decrypted data must be saved
	 * @return string|bool Decrypted data or FALSE in case of errors
	 */
	function engineDecrypt($data, $saveTo='') {
		if ($this->_initialize()) {
			$data = base64_decode($data);
			$ivSize = mcrypt_enc_get_iv_size($this->handle);
			$iv = substr($data, 0, $ivSize);
			$data = substr($data, $ivSize);
			mcrypt_generic_init($this->handle, $this->key, $iv);
			$decrypted = mdecrypt_generic($this->handle, $data);
			mcrypt_generic_deinit($this->handle);
			mcrypt_module_close($this->handle);
			if (trim($saveTo) != '') {
				$fp = @fopen($saveTo, 'wb');
				if ($fp === FALSE) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $saveTo), E_USER_ERROR, __FILE__, __LINE__);
					return FALSE;
				} else {
					fputs($fp, $decrypted);
					fclose($fp);
					return $decrypted;
				}
			}
			return $decrypted;
		}
		return FALSE;
	}

	/**
	 * Decrypts the contents of a file, saving the results in another file
	 *
	 * @param string $inFileName Input file name
	 * @param string $outFileName Output file name
	 * @return bool
	 */
	function engineDecryptFile($inFileName, $outFileName) {
		$in = @file_get_contents($inFileName);
		if ($in === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $inFileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$out = @fopen($outFileName, 'wb');
		if ($out === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $outFileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if ($decrypted = $this->engineDecrypt($in)) {
			fputs($out, $decrypted);
			fclose($out);
			return TRUE;
		}
		fclose($out);
		return FALSE;
	}

	/**
	 * Initializes the cipher module before encrypting or decrypting data
	 *
	 * @access private
	 * @return bool
	 */
	function _initialize() {
		if (!isset($this->key)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CRYPT_MISSING_KEY'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if (!$this->handle = @mcrypt_module_open($this->cipher, '', $this->cipherMode, '')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CRYPT_OPEN_MODULE', array($this->cipher, $this->cipherMode)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if (!$this->iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($this->handle), MCRYPT_RAND))
			return FALSE;
		return TRUE;
	}
}
?>