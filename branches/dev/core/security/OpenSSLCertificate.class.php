<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/core/security/OpenSSLCertificate.class.php,v 1.12 2006/04/05 23:43:21 mpont Exp $
// $Date: 2006/04/05 23:43:21 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.file.FileManager');
import('php2go.security.DistinguishedName');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		OpenSSLCertificate
// @desc		Classe que armazena e retorna informa��es sobre um
//				certificado digital do tipo X509. Utiliza as fun��es
//				da biblioteca openssl
// @package		php2go.security
// @uses		Conf
// @uses		DistinguishedName
// @uses		FileManager
// @uses		FileSystem
// @uses		TypeUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.12 $
//!-----------------------------------------------------------------
class OpenSSLCertificate extends PHP2Go
{
	var $name;				// @var name string							Nome completo do certificado
	var $ownerDN;			// @var ownerDN DistinguishedName object	Armazena informa��es do propriet�rio do certificado
	var $hash;				// @var hash string							Hash do certificado
	var $serialNumber;		// @var serialNumber string					N�mero serial
	var $version;			// @var version string						Vers�o do certificado
	var $issuerDN;			// @var issuerDN DistinguishedName object	Armazena informa��es sobre o provedor do certificado
	var $validFrom;			// @var validFrom int						Timestamp inicial da validade
	var $validTo;			// @var validTo int							Timestamp de expira��o do certificado
	var $purposes;			// @var purposes array						Vetor de prop�sitos do certificado
	var $contents;			// @var contents string						Conte�do do certificado, capturado do arquivo no servidor
	var $resource;			// @var resource resource					Resource obtido na opera��o de leitura do conte�do do certificado

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::OpenSSLCertificate
	// @desc		Construtor da classe
	// @access		public
	// @param		path string		Caminho do certificado no servidor
	//!-----------------------------------------------------------------
	function OpenSSLCertificate($path) {
		parent::PHP2Go();
		// verifica a disponibilidade da extens�o openssl
		if (!function_exists('openssl_x509_read'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'openssl'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_readCertificate($path);
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::__destruct
	// @desc		Destrutor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function __destruct() {
		if (TypeUtils::isResource($this->resource))
			openssl_x509_free($this->resource);
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getContent
	// @desc		Retorna o conte�do do certificado
	// @access		public
	// @return		string Conte�do do certificado
	//!-----------------------------------------------------------------
	function getContent() {
		return (isset($this->contents) ? $this->contents : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getName
	// @desc		Retorna o nome completo do certificado
	// @access		public
	// @return		string Nome completo do certificado
	//!-----------------------------------------------------------------
	function getName() {
		return (isset($this->name) ? $this->name : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getOwnerDN
	// @desc		Retorna o distinguished name do propriet�rio do certificado
	// @access		public
	// @return		DistinguishedName object DN do propriet�rio do certificado
	//!-----------------------------------------------------------------
	function getOwnerDN() {
		return (isset($this->ownerDN) ? $this->ownerDN : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getHash
	// @desc		Busca o hash do certificado
	// @access		public
	// @return		string Hash do certificado
	//!-----------------------------------------------------------------
	function getHash() {
		return (isset($this->hash) ? $this->hash : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getSerialNumber
	// @desc		Retorna o n�mero serial do certificado
	// @access		public
	// @return		string N�mero serial do certificado
	//!-----------------------------------------------------------------
	function getSerialNumber() {
		return (isset($this->serialNumber) ? $this->serialNumber : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getVersion
	// @desc		Busca a vers�o do certificado digital
	// @access		public
	// @return		string Vers�o do certificado
	//!-----------------------------------------------------------------
	function getVersion() {
		return (isset($this->version) ? $this->version : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getIssuerDN
	// @desc		Busca o distinguished name associado ao provedor do certificado
	// @access		public
	// @return		DistinguishedName object DN do provedor
	//!-----------------------------------------------------------------
	function getIssuerDN() {
		return (isset($this->issuerDN) ? $this->issuerDN : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getIssueDate
	// @desc		Busca a data inicial de validade do certificado digital
	// @access		public
	// @param		fmt string	"" Formato da data
	// @return		string Data inicial de validade
	// @note		O par�metro $fmt deve representar um formato v�lido segundo
	//				as especifica��es da fun��o date() no PHP. Se n�o for fornecido,
	//				o formato de data utilizado ser� buscado na configura��o
	//!-----------------------------------------------------------------
	function getIssueDate($fmt='') {
		$Conf =& Conf::getInstance();
		if (isset($this->validFrom)) {
			if (!empty($fmt))
				return date($fmt, $this->validFrom);
			else
				return date($Conf->getConfig('LOCAL_DATE_FORMAT'), $this->validFrom);
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getExpiryDate
	// @desc		Busca a data de expira��o do certificado digital
	// @access		public
	// @param		fmt string	"" Formato da data
	// @return		string Data de expira��o
	// @note		O par�metro $fmt deve representar um formato v�lido segundo
	//				as especifica��es da fun��o date() no PHP. Se n�o for fornecido,
	//				o formato de data utilizado ser� buscado na configura��o
	//!-----------------------------------------------------------------
	function getExpiryDate($fmt='') {
		$Conf =& Conf::getInstance();
		if (isset($this->validTo)) {
			if (!empty($fmt))
				return date($fmt, $this->validTo);
			else
				return date($Conf->getConfig('LOCAL_DATE_FORMAT', $this->validTo));
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::isValid
	// @desc		Verifica a data de expira��o do certificado digital
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if (isset($this->validTo)) {
			$Conf =& Conf::getInstance();
			$expiryDate = date($Conf->getConfig('LOCAL_DATE_FORMAT'), $this->validTo);
			return (!Date::isPast($expiryDate));
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::getPurposes
	// @desc		Retorna o vetor de prop�sitos do certificado digital
	// @access		public
	// @return		array Vetor de prop�sitos
	//!-----------------------------------------------------------------
	function getPurposes() {
		return (isset($this->purposes) ? $this->purposes : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::toString
	// @desc		Monta uma representa��o string do certificado OpenSSL
	// @access		public
	// @return		string Representa��o string do objeto
	// @see			PHP2Go::toString
	//!-----------------------------------------------------------------
	function toString() {
		return sprintf("X.509 Certificate object{\n Name: %s\n Owner: %s\n Hash: %s\n SerialNumber: %s\n Version: %s\n Issuer: %s\n NotBefore: %s\n NotAfter: %s\n}",
					$this->getName(), $this->ownerDN->toString(),
					$this->getHash(), $this->getSerialNumber(),
					$this->getVersion(), $this->issuerDN->toString(),
					$this->getIssueDate(), $this->getExpiryDate()
		);
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::_readCertificate
	// @desc		L� o conte�do do arquivo do certificado e verifica a validade do conte�do
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _readCertificate($path) {
		if (!FileSystem::exists($path)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_OPENSSL_CERT_PATH'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$result = FALSE;
			$Mgr =& new FileManager();
			if ($Mgr->open($path, FILE_MANAGER_READ_BINARY)) {
				$this->contents = $Mgr->read(8192);
				if (TypeUtils::isResource($this->resource = @openssl_x509_read($this->contents))) {
					if (TypeUtils::isArray($info = @openssl_x509_parse($this->resource))) {
						$this->_parseCertificate($info);
						$result = TRUE;
					}
				}
			}
			if (!$result)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_OPENSSL_READ_CERT', '<br><i>OpenSSL Error:</i> ' . openssl_error_string()), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	OpenSSLCertificate::_parseCertificate
	// @desc		Parseia as informa��es acerca do certificado
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _parseCertificate($info) {
		$this->name = $info['name'];
		$this->ownerDN =& new DistinguishedName($info['subject']);
		$this->hash = $info['hash'];
		$this->serialNumber = $info['serialNumber'];
		$this->version = $info['version'];
		$this->issuerDN =& new DistinguishedName($info['issuer']);
		$this->validFrom = $info['validFrom_time_t'];
		$this->validTo = $info['validTo_time_t'];
		if (TypeUtils::isArray($info['purposes'])) {
			$tmp = array();
			foreach ($info['purposes'] as $purpose)
				$tmp[$purpose[2]] = $purpose[1];
			$this->purposes = $tmp;
		}
	}
}
?>