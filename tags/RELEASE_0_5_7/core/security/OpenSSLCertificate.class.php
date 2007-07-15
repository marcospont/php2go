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

import('php2go.security.DistinguishedName');

/**
 * Reads and parses X509 certificates
 *
 * This class is built over the functions provided by
 * the openssl PHP extension.
 *
 * @package security
 * @uses Conf
 * @uses Date
 * @uses DistinguishedName
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class OpenSSLCertificate extends PHP2Go
{
	/**
	 * Certificate's name
	 *
	 * @var string
	 */
	var $name;

	/**
	 * Owner's distinguished name
	 *
	 * @var object DistinguishedName
	 */
	var $ownerDN;

	/**
	 * Certificate's hash
	 *
	 * @var string
	 */
	var $hash;

	/**
	 * Certificate's serial number
	 *
	 * @var string
	 */
	var $serialNumber;

	/**
	 * Certificate's version
	 *
	 * @var string
	 */
	var $version;

	/**
	 * Issuer's distinguished name
	 *
	 * @var object DistinguishedName
	 */
	var $issuerDN;

	/**
	 * Initial validity timestamp
	 *
	 * @var int
	 */
	var $validFrom;

	/**
	 * Expiration timestamp
	 *
	 * @var int
	 */
	var $validTo;

	/**
	 * Certificate's purposes
	 *
	 * @var string
	 */
	var $purposes;

	/**
	 * Full certificate
	 *
	 * @var string
	 */
	var $contents;

	/**
	 * X509 handle
	 *
	 * @access private
	 * @var resource
	 */
	var $handle;

	/**
	 * Class constructor
	 *
	 * @param string $path Certificate's path
	 * @return OpenSSLCertificate
	 */
	function OpenSSLCertificate($path) {
		parent::PHP2Go();
		if (!function_exists('openssl_x509_read'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'openssl'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_readCertificate($path);
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		if (is_resource($this->handle))
			openssl_x509_free($this->handle);
		unset($this);
	}

	/**
	 * Get certificate's content
	 *
	 * @return string
	 */
	function getContent() {
		return (isset($this->contents) ? $this->contents : NULL);
	}

	/**
	 * Get certificate's name
	 *
	 * @return string
	 */
	function getName() {
		return (isset($this->name) ? $this->name : NULL);
	}

	/**
	 * Get owner's distinguished name
	 *
	 * @return DistinguishedName
	 */
	function getOwnerDN() {
		return (isset($this->ownerDN) ? $this->ownerDN : NULL);
	}

	/**
	 * Get certificate's hash
	 *
	 * @return string
	 */
	function getHash() {
		return (isset($this->hash) ? $this->hash : NULL);
	}

	/**
	 * Get certificate's serial number
	 *
	 * @return string
	 */
	function getSerialNumber() {
		return (isset($this->serialNumber) ? $this->serialNumber : NULL);
	}

	/**
	 * Get certificate's version
	 *
	 * @return string
	 */
	function getVersion() {
		return (isset($this->version) ? $this->version : NULL);
	}

	/**
	 * Get issuer's distinguished name
	 *
	 * @return DistinguishedName
	 */
	function getIssuerDN() {
		return (isset($this->issuerDN) ? $this->issuerDN : NULL);
	}

	/**
	 * Get issue date
	 *
	 * The $fmt argument represents the date format
	 * to be applied on the initial validity timestamp.
	 * If this parameter is missing, local date format
	 * will be used.
	 *
	 * @param string $fmt Date format
	 * @return string
	 */
	function getIssueDate($fmt='') {
		if (isset($this->validFrom)) {
			if (empty($fmt)) {
				$settings = PHP2Go::getConfigVal('DATE_FORMAT_SETTINGS');
				$fmt = $settings['format'];
			}
			return date($fmt, $this->validFrom);
		}
		return NULL;
	}

	/**
	 * Get expiry date
	 *
	 * The $fmt argument represents the date format
	 * to be applied on the expiration timestamp. If
	 * missing, local date format will be used.
	 *
	 * @param string $fmt Date format
	 * @return string
	 */
	function getExpiryDate($fmt='') {
		if (isset($this->validTo)) {
			if (empty($fmt)) {
				$settings = PHP2Go::getConfigVal('DATE_FORMAT_SETTINGS');
				$fmt = $settings['format'];
			}
			return date($fmt, $this->validTo);
		}
		return NULL;
	}

	/**
	 * Validates the certificate's expiration timestamp
	 *
	 * @return bool
	 */
	function isValid() {
		if (isset($this->validTo)) {
			$now = time();
			return ($now > $this->validTo);
		}
		return TRUE;
	}

	/**
	 * Get certificate's purposes
	 *
	 * @return array
	 */
	function getPurposes() {
		return (isset($this->purposes) ? $this->purposes : NULL);
	}

	/**
	 * Builds and returns a string representation of the certificate
	 *
	 * @return string
	 */
	function __toString() {
		return sprintf("X.509 Certificate object{\n Name: %s\n Owner: %s\n Hash: %s\n SerialNumber: %s\n Version: %s\n Issuer: %s\n NotBefore: %s\n NotAfter: %s\n}",
			$this->getName(), $this->ownerDN->__toString(),
			$this->getHash(), $this->getSerialNumber(),
			$this->getVersion(), $this->issuerDN->__toString(),
			$this->getIssueDate(), $this->getExpiryDate()
		);
	}

	/**
	 * Reads, parses and validates the contents of the certificate
	 *
	 * @param string $path Certificate's path
	 * @return bool
	 */
	function _readCertificate($path) {
		if (!file_exists($path)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_OPENSSL_CERT_PATH'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if (($fp = @fopen($path, 'rb')) === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_OPENSSL_READ_CERT', ''), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// read file
		$this->contents = fread($fp, 8192);
		fclose($fp);
		// read certificate
		if (!is_resource($this->handle = @openssl_x509_read($this->contents))) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_OPENSSL_READ_CERT', '<br /><i>OpenSSL Error:</i> ' . openssl_error_string()), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// parse certificate
		if (!is_array($info = @openssl_x509_parse($this->handle))) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_OPENSSL_READ_CERT', '<br /><i>OpenSSL Error:</i> ' . openssl_error_string()), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		// populate class properties
		$this->name = $info['name'];
		$this->ownerDN = new DistinguishedName($info['subject']);
		$this->hash = $info['hash'];
		$this->serialNumber = $info['serialNumber'];
		$this->version = $info['version'];
		$this->issuerDN = new DistinguishedName($info['issuer']);
		$this->validFrom = $info['validFrom_time_t'];
		$this->validTo = $info['validTo_time_t'];
		if (is_array($info['purposes'])) {
			$tmp = array();
			foreach ($info['purposes'] as $purpose)
				$tmp[$purpose[2]] = $purpose[1];
			$this->purposes = $tmp;
		}
		return TRUE;
	}
}
?>