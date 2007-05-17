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

import('php2go.security.OpenSSLCertificate');

/**
 * Signs, encrypts and decrypts data using the openssl extension
 *
 * @package security
 * @uses OpenSSLCertificate
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class OpenSSLCrypt extends PHP2Go
{
	/**
	 * Path to the public key (certificate) path
	 *
	 * @var string
	 */
	var $certificatePath;

	/**
	 * OpenSSLCertificate object, containing
	 * information about the public key
	 *
	 * @var object OpenSSLCertificate
	 */
	var $Certificate;

	/**
	 * Path to the private key file
	 *
	 * @var string
	 */
	var $privateKeyPath;

	/**
	 * Private key's passphrase
	 *
	 * @var string
	 */
	var $passPhrase;

	/**
	 * Latest error message (produced by this class)
	 *
	 * @var string
	 */
	var $errorMsg;

	/**
	 * Latest internal error (produced by OpenSSL)
	 *
	 * @var string
	 */
	var $openSSLError;

	/**
	 * Whether errors must be thrown
	 *
	 * @var bool
	 */
	var $throwErrors;

	/**
	 * Public key handle
	 *
	 * @access private
	 * @var resource
	 */
	var $publicKeyRes;

	/**
	 * Private key handle
	 *
	 * @access private
	 * @var resource
	 */
	var $privateKeyRes;

	/**
	 * Class constructor
	 *
	 * @return OpenSSLCrypt
	 */
	function OpenSSLCrypt() {
		parent::PHP2Go();
		if (!function_exists('openssl_get_publickey'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'openssl'), E_USER_ERROR, __FILE__, __LINE__);
		$this->throwErrors = FALSE;
	}

	/**
	 * Get last error message
	 *
	 * @return string
	 */
	function getLastError() {
		return (isset($this->errorMsg)) ? $this->errorMsg : NULL;
	}

	/**
	 * Get last internal error message (OpenSSL errors)
	 *
	 * @return string
	 */
	function getLastInternalError() {
		return (isset($this->openSSLError) && !empty($this->openSSLError)) ? $this->openSSLError : NULL;
	}

	/**
	 * Get public key's certificate
	 *
	 * @return OpenSSLCertificate
	 */
	function &getCertificate() {
		$result = NULL;
		if (isset($this->certificatePath))
			$result =& $this->Certificate;
		return $result;
	}

	/**
	 * Set the public key (certificate) path
	 *
	 * @param string $pathToCertificate File path
	 * @return bool
	 */
	function setCertificate($pathToCertificate) {
		if (!file_exists($pathToCertificate)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_OPENSSL_CERT_PATH'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$this->certificatePath = $pathToCertificate;
			$this->Certificate = new OpenSSLCertificate($this->certificatePath);
			return TRUE;
		}
	}

	/**
	 * Set private key file
	 *
	 * @param string $pathToKey Key path
	 * @return bool
	 */
	function setPrivateKey($pathToKey) {
		if (!file_exists($pathToKey)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $pathToKey), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$this->privateKeyPath = $pathToKey;
			return TRUE;
		}
	}

	/**
	 * Set key's passphrase
	 *
	 * @param string $passPhrase
	 */
	function setPassPhrase($passPhrase) {
		$this->passPhrase = strval($passPhrase);
	}

	/**
	 * Clear stored keys
	 */
	function clearKeys() {
		unset($this->certificatePath);
		unset($this->privateKeyPath);
		unset($this->passPhrase);
		unset($this->Certificate);
	}

	/**
	 * Encrypts data using the public key from
	 * the certificate
	 *
	 * @uses _getPublicKey()
	 * @param string $data Data to encrypt
	 * @param string $saveTo Optional file where encrypted data must be saved
	 * @return string|bool
	 */
	function engineEncrypt($data, $saveTo = '') {
		$pub = $this->_getPublicKey();
		if (!$pub) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_OPENSSL_PUBKEY_ENCRYPT');
			if (isset($this->openSSLError))
				$this->errorMsg .= '<br /><i>OpenSSL Error:</i> ' . $this->openSSLError;
			if ($this->throwErrors)
				PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$crypted = '';
		if (!@openssl_public_encrypt($data, $crypted, $pub)) {
			$this->openSSLError = @openssl_error_string();
			return FALSE;
		}
		@openssl_free_key($this->publicKeyRes);
		if ($saveTo != '') {
			$fp = @fopen($saveTo, 'wb');
			if ($fp === FALSE) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $saveTo), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			fputs($fp, $crypted);
			fclose($fp);
			return $crypted;
		} else {
			return $crypted;
		}
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
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $outFileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if ($encrypted = $this->engineDecrypt($in)) {
			fputs($fp, $encrypted);
			fclose($fp);
			return TRUE;
		}
		fclose($fp);
		return FALSE;
	}

	/**
	 * Decrypts data using the configured private key
	 *
	 * @uses _getPrivateKey()
	 * @param string $data Data to decrypt
	 * @param string $saveTo Optional file where decrypted data must be saved
	 * @return string|bool
	 */
	function engineDecrypt($data, $saveTo = '') {
		$pk = $this->_getPrivateKey();
		if (!$pk) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_OPENSSL_PRIVKEY_DECRYPT');
			if (isset($this->openSSLError))
				$this->errorMsg .= '<br /><i>OpenSSL Error:</i> ' . $this->openSSLError;
			if ($this->throwErrors)
				PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$decrypted = '';
		if (!@openssl_private_decrypt($data, $decrypted, $pk)) {
			$this->openSSLError = @openssl_error_string();
			return FALSE;
		}
		@openssl_free_key($this->privateKeyRes);
		if ($saveTo != '') {
			$fp = @fopen($saveTo, 'wb');
			if ($fp === FALSE) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $saveTo), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			fputs($fp, $decrypted);
			fclose($fp);
			return $decrypted;
		} else {
			return $decrypted;
		}
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
			fputs($fp, $decrypted);
			fclose($fp);
			return TRUE;
		}
		fclose($fp);
		return FALSE;
	}

	/**
	 * Signs data using the configured private key
	 *
	 * The generated signature will be returned. Optionally, this signature
	 * can be appended in the end of the original string.
	 *
	 * @uses _getPrivateKey()
	 * @param string &$data Data to sign
	 * @param bool $appendSignature Whether signature must be appended in $data
	 * @return string|bool
	 */
	function engineSign(&$data, $appendSignature=FALSE) {
		$pk = $this->_getPrivateKey();
		if (!$pk) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_OPENSSL_PRIVKEY_SIGN');
			if (isset($this->openSSLError))
				$this->errorMsg .= '<br /><i>OpenSSL Error:</i> ' . $this->openSSLError;
			if ($this->throwErrors)
				PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$signature = '';
		if (!@openssl_sign($data, $signature, $privateKey)) {
			$this->openSSLError = @openssl_error_string();
			return FALSE;
		}
		@openssl_free_key($this->privateKeyRes);
		if ($appendSignature)
			$data .= $signature;
		return $signature;
	}

	/**
	 * Signs the contents of a file
	 *
	 * @param string $inFileName Input file name
	 * @param bool $appendFile Whether to append signature in the end of the file
	 * @return string|bool
	 */
	function engineSignFile($inFileName, $appendFile=FALSE) {
		$in = @file_get_contents($inFileName);
		if ($in === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $inFileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$signature = $this->engineSign($in);
		if ($signature) {
			if ($appendFile) {
				$fp = @fopen($inFileName, 'ab');
				if ($fp === FALSE) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $inFileName), E_USER_ERROR, __FILE__, __LINE__);
					return FALSE;
				}
				fputs($fp, $signature);
				fclose($fp);
				return $signature;
			}
			return $signature;
		}
		return FALSE;
	}

	/**
	 * Verifies the validity of the signed value
	 *
	 * @uses _getPublicKey()
	 * @param string $data Signed data to verify
	 * @param string $signature Signature
	 * @return bool
	 */
	function engineVerify($data, $signature) {
		$pub = $this->_getPublicKey();
		if (!$pub) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_OPENSSL_PUBKEY_VERIFY');
			if (isset($this->openSSLError))
				$this->errorMsg .= '<br /><i>OpenSSL Error:</i> ' . $this->openSSLError;
			if ($this->throwErrors)
				PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		return @openssl_verify($data, $signature, $pub);
	}

	/**
	 * Verifies the validity of a signed file
	 *
	 * @param string $inFileName File name
	 * @param int $signatureLength Length of the signature
	 * @return bool
	 */
	function engineVerifyFile($inFileName, $signatureLength=128) {
		$in = @fopen($inFileName, 'rb');
		if ($in === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $inFileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$size = filesize($inFileName);
		$dataLength = $size - $signatureLength;
		$data = fread($fp, $dataLength);
		fseek($fp, $dataLength);
		$signature = fread($fp, $signatureLength);
		fclose($fp);
		return $this->engineVerify($data, $signature);
	}

	/**
	 * Extracts the private key from the file whose path was
	 * configured through the {@link setPrivateKeyPath()} method
	 *
	 * @access private
	 * @return resource|bool
	 */
	function _getPrivateKey() {
		if (!isset($this->privateKeyPath))
			return FALSE;
		$fp = @fopen($this->privateKeyPath, 'rb');
		if ($fp === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $this->privateKeyPath), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$pk = fread($fp, 8192);
		fclose($fp);
		if (isset($this->passPhrase))
			$pkRes = @openssl_get_privatekey($pk, $this->passPhrase);
		else
			$pkRes = @openssl_get_privatekey($pk);
		if (!is_resource($pkRes)) {
			$this->openSSLError = @openssl_error_string();
			return FALSE;
		}
		$this->privateKeyRes = $pkRes;
		return $pkRes;
	}

	/**
	 * Extracts the public key from the certificate configured
	 * through the {@link setCertificatePath()} method
	 *
	 * @access private
	 * @return resource|bool
	 */
	function _getPublicKey() {
		if (!isset($this->certificatePath))
			return FALSE;
		if (!($certificate = $this->Certificate->getContent()))
			return FALSE;
		$pubRes = @openssl_get_publickey($certificate);
		if (!is_resource($pubRes)) {
			$this->openSSLError = @openssl_error_string();
			return FALSE;
		}
		$this->publicKeyRes = $pubRes;
		return $pubRes;
	}
}
?>