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
// $Header: /www/cvsroot/php2go/core/security/OpenSSLCrypt.class.php,v 1.11 2006/04/05 23:43:21 mpont Exp $
// $Date: 2006/04/05 23:43:21 $

//------------------------------------------------------------------
import('php2go.file.FileManager');
import('php2go.security.OpenSSLCertificate');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		OpenSSLCrypt
// @desc		Cria um n�vel de abstra��o sobre as fun��es de criptografia
//				da biblioteca openssl. Permite encriptar utilizando uma chave p�blica,
//				decriptar utilizando chave privada, assinar e verificar digitalmente
//				conte�do ou arquivos
// @package		php2go.security
// @uses		FileManager
// @uses		FileSystem
// @uses		OpenSSLCertificate
// @uses		TypeUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.11 $
//!-----------------------------------------------------------------
class OpenSSLCrypt extends PHP2Go
{	
	var $certificatePath;	// @var certificatePath string					Arquivo que cont�m o certificado digital (chave p�blica)
	var $privateKeyPath;	// @var privateKeyPath string					Arquivo que cont�m a chave privada utilizada para decriptar e assinar dados
	var $passPhrase;		// @var passPhrase string						Frase de passagem utilizada em conjunto com a chave privada
	var $throwErrors;		// @var throwErrors bool						Indica se os erros devem ser exibidos como erros de aplica��o ou n�o	
	var $errorMsg;			// @var errorMsg string							Armazena mensagens de erro geradas pela classe
	var $openSSLError;		// @var openSSLError string						Armazena mensagens de erro das fun��es da bilioteca open_ssl
	var $publicKeyRes;		// @var publicKeyRes resource					Armazena a chave p�blica ativa
	var $privateKeyRes;		// @var privateKeyRes resource					Armazena a chave privada ativa
	var $_Mgr;				// @var _Mgr FileManager object					Utilizado na manipula��o de arquivos
	var $_Certificate;		// @var _Certificate OpenSSLCertificate object	Objeto para manipula��o do certificado

	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::OpenSSLCrypt
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function OpenSSLCrypt() {
		parent::PHP2Go();
		if (!function_exists('openssl_get_publickey'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'openssl'), E_USER_ERROR, __FILE__, __LINE__);
		$this->throwErrors = FALSE;
		$this->_Mgr = new FileManager();
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::getLastError
	// @desc		Retorna a �ltima mensagem de erro gerada pela classe
	// @access		public
	// @return		mixed Mensagem de erro ou NULL se n�o existem erros armazenados
	//!-----------------------------------------------------------------
	function getLastError() {
		return (isset($this->errorMsg)) ? $this->errorMsg : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::getLastInternalError
	// @desc		Retorna a �ltima mensagem de erro gerada pela biblioteca openssl
	// @access		public
	// @return		mixed Mensagem de erro ou NULL se n�o existem erros armazenados
	//!-----------------------------------------------------------------
	function getLastInternalError() {
		return (isset($this->openSSLError) && !empty($this->openSSLError)) ? $this->openSSLError : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::&getCertificate
	// @desc		Retorna um objeto contendo informa��es sobre o certificado
	// @access		public
	// @return		OpenSSLCertificate object
	//!-----------------------------------------------------------------
	function &getCertificate() {
		$result = NULL;
		if (isset($this->certificatePath))
			$result =& $this->_Certificate;
		return $result;
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::setCertificate
	// @desc		Define o arquivo que cont�m a chave p�blica
	// @access		public
	// @param		pathToKey string	Caminho completo para o arquivo do certificado (chave p�blica)
	// @return		bool
	//!-----------------------------------------------------------------
	function setCertificate($pathToCertificate) {
		if (!FileSystem::exists($pathToCertificate)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_OPENSSL_CERT_PATH'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$this->certificatePath = $pathToCertificate;			
			$this->_Certificate = new OpenSSLCertificate($this->certificatePath);
			return TRUE;
		}
	}	
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::setPrivateKey
	// @desc		Define o arquivo que cont�m a chave privada
	// @access		public
	// @param		pathToKey string	Caminho completo para o arquivo da chave privada
	// @return		bool
	//!-----------------------------------------------------------------
	function setPrivateKey($pathToKey) {
		if (!FileSystem::exists($pathToKey)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $pathToKey), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$this->privateKeyPath = $pathToKey;
			return TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::setPassPhrase
	// @desc		Define a frase de passagem a ser utilizada
	// @access		public
	// @param		passPhrase string	Frase de passagem
	// @return		void
	//!-----------------------------------------------------------------
	function setPassPhrase($passPhrase) {
		$this->passPhrase = TypeUtils::parseString($passPhrase);
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::clearKeys
	// @desc		Reseta a informa��o de chaves armazenada na classe
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function clearKeys() {
		unset($this->certificatePath);
		unset($this->privateKeyPath);
		unset($this->passPhrase);
		unset($this->_Certificate);
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::engineEncrypt
	// @desc		M�todo para encriptar dados utilizando a chave p�blica
	//				contida no certificado fornecido � classe
	// @access		public
	// @param		data string	Dados a serem encriptados
	// @param		saveTo string	"" Arquivo onde o resultado deve ser salvo
	// @return		mixed TRUE (utilizando arquivo de sa�da) ou o conte�do encriptado
	//				e FALSE em caso de erros
	//!-----------------------------------------------------------------
	function engineEncrypt($data, $saveTo = '') {
		$publicKey = $this->_getPublicKey();
		if (!$publicKey) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_OPENSSL_PUBKEY_ENCRYPT');
			if (isset($this->openSSLError))
				$this->errorMsg .= '<br><i>OpenSSL Error:</i> ' . $this->openSSLError;
			if ($this->throwErrors)
				PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$crypted = '';
			if (!@openssl_public_encrypt($data, $crypted, $publicKey)) {
				$this->openSSLError = @openssl_error_string();
				return FALSE;
			}
			@openssl_free_key($this->publicKeyRes);
			if ($saveTo != '') {
				$this->_Mgr->open($saveTo, FILE_MANAGER_WRITE_BINARY);
				$this->_Mgr->write($crypted);
				$this->_Mgr->close();
				return TRUE;
			} else {
				return $crypted;
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::engineEncryptFile
	// @desc		Encripta o conte�do de um arquivo, gravando a sa�da em
	//				um outro arquivo
	// @access		public
	// @param		inFileName string		Arquivo com os dados a serem encriptados
	// @param		outFileName string	Arquivo onde a sa�da deve ser gravada
	// @return		bool
	//!-----------------------------------------------------------------
	function engineEncryptFile($inFileName, $outFileName) {
		if ($this->_Mgr->open($inFileName, FILE_MANAGER_READ_BINARY)) {
			$fileContents = $this->_Mgr->readFile();
			$this->_Mgr->close();
			if ($this->_Mgr->open($outFileName, FILE_MANAGER_WRITE_BINARY)) {
				if ($encryptedData = $this->engineEncrypt($fileContents)) {
					$this->_Mgr->write($encryptedData);
					$this->_Mgr->close();
					return TRUE;
				} else {
					$this->_Mgr->close();
					return FALSE;
				}
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::engineDecrypt
	// @desc		M�todo para decriptar dados utilizando a chave privada
	//				fornecida atrav�s do m�todo OpenSSLCrypt::setPrivateKeyPath
	// @access		public
	// @param		data string	Dados encriptados
	// @param		saveTo string	"" Arquivo de sa�da
	// @return		mixed TRUE (utilizando arquivo de sa�da) ou o conte�do decriptado
	//				e FALSE em caso de erros
	//!-----------------------------------------------------------------
	function engineDecrypt($data, $saveTo = '') {
		$privateKey = $this->_getPrivateKey();
		if (!$privateKey) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_OPENSSL_PRIVKEY_DECRYPT');
			if (isset($this->openSSLError))
				$this->errorMsg .= '<br><i>OpenSSL Error:</i> ' . $this->openSSLError;
			if ($this->throwErrors)
				PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$decrypted = '';
			if (!@openssl_private_decrypt($data, $decrypted, $privateKey)) {
				$this->openSSLError = @openssl_error_string();
				return FALSE;
			}
			@openssl_free_key($this->privateKeyRes);
			if ($saveTo != '') {
				$this->_Mgr->open($saveTo, FILE_MANAGER_WRITE_BINARY);
				$this->_Mgr->write($decrypted);
				$this->_Mgr->close();
				return TRUE;
			} else {
				return $decrypted;
			}
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::engineDecryptFile
	// @desc		M�todo para decriptar dados de um arquivo e gerar a
	//				sa�da em um outro arquivo
	// @access		public
	// @param		inFileName string		Arquivo com dados encriptados
	// @param		outFileName string	Arquivo de sa�da
	// @return		bool
	//!-----------------------------------------------------------------
	function engineDecryptFile($inFileName, $outFileName) {
		if ($this->_Mgr->open($inFileName, FILE_MANAGER_READ_BINARY)) {
			$fileContents = $this->_Mgr->readFile();
			$this->_Mgr->close();
			if ($this->_Mgr->open($outFileName, FILE_MANAGER_WRITE_BINARY)) {
				if ($decryptedData = $this->engineDecrypt($fileContents)) {
					$this->_Mgr->write($decryptedData);
					$this->_Mgr->close();
					return TRUE;
				} else {
					$this->_Mgr->close();
					return FALSE;
				}
			}
		}
		return FALSE;	
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::engineSign
	// @desc		M�todo para gerar uma assinatura digital de um conte�do
	// @access		public
	// @param		&data string			Dados a serem assinados digitalmente
	// @param		appendSignature bool	Indica se a assinatura deve ser concatenada ao conte�do
	// @return		string Assinatura digital do conte�do
	// @note		Retorna FALSE em caso de erros
	//!-----------------------------------------------------------------
	function engineSign(&$data, $appendSignature=FALSE) {
		$privateKey = $this->_getPrivateKey();
		if (!$privateKey) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_OPENSSL_PRIVKEY_SIGN');
			if (isset($this->openSSLError))
				$this->errorMsg .= '<br><i>OpenSSL Error:</i> ' . $this->openSSLError;
			if ($this->throwErrors)
				PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;			
		} else {
			$signature = '';
			if (!@openssl_sign($data, $signature, $privateKey)) {
				$this->openSSLError = @openssl_error_string();
				return FALSE;
			}
			@openssl_free_key($this->privateKeyRes);
			if ($appendSignature) {
				$data .= $signature;
			}
			return $signature;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::engineSignFile
	// @desc		M�todo para assinar digitalmente o conte�do de um arquivo
	// @access		public
	// @param		inFileName string		Nome do arquivo
	// @param		appendFile bool			"FALSE" Se TRUE, gravar� a assinatura ao final do pr�prio arquivo
	// @return		mixed TRUE (se $appendFile == TRUE) ou a assinatura do conte�do do arquivo; FALSE em caso de quaisquer erros
	//!-----------------------------------------------------------------
	function engineSignFile($inFileName, $appendFile=FALSE) {
		$this->_Mgr->open($inFileName, FILE_MANAGER_READ_BINARY);
		$signature = $this->engineSign($this->_Mgr->readFile());
		if (!$signature) {
			return FALSE;
		} else {
			if ($appendFile) {
				$this->_Mgr->close();
				$this->_Mgr->open($inFileName, FILE_MANAGER_APPEND_BINARY);
				$this->_Mgr->write($signature);
				$this->_Mgr->close();
				return TRUE;
			} else {
				$this->_Mgr->close();
				return $signature;
			}
		}		
	}	
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::engineVerify
	// @desc		Verifica a assinatura digital de conte�do
	// @access		public
	// @param		data string		Conte�do
	// @param		signature string	Assinatura do conte�do
	// @return		bool
	//!-----------------------------------------------------------------
	function engineVerify($data, $signature) {
		$publicKey = $this->_getPublicKey();
		if (!$publicKey) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_OPENSSL_PUBKEY_VERIFY');
			if (isset($this->openSSLError))
				$this->errorMsg .= '<br><i>OpenSSL Error:</i> ' . $this->openSSLError;
			if ($this->throwErrors)
				PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			return @openssl_verify($data, $signature, $publicKey);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::engineVerifyFile
	// @desc		Verifica a assinatura digital de um determinado arquivo
	// @access		public
	// @param		inFileName string		Caminho completo do arquivo
	// @param		signatureLength int		"128" Tamanho da assinatura, em bytes
	// @return		bool
	//!-----------------------------------------------------------------
	function engineVerifyFile($inFileName, $signatureLength=128) {
		if ($this->_Mgr->open($inFileName, FILE_MANAGER_READ_BINARY)) {
			$size = $this->_Mgr->getAttribute('size');
			$dataLength = $size - $signatureLength;
			$data = $this->_Mgr->read($dataLength);
			$this->_Mgr->seek($dataLength);
			$signature = $this->_Mgr->read($signatureLength);
			$this->_Mgr->close();
			return $this->engineVerify($data, $signature);
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::_getPrivateKey
	// @desc		Extrai a chave p�blica do arquivo fornecido no m�todo
	//				setPrivateKeyPath(), preparando-a para utiliza��o
	// @access		private
	// @return		resource Chave privada
	// @note		Retorna FALSE se n�o for poss�vel buscar a chave privada
	//!-----------------------------------------------------------------
	function _getPrivateKey() {
		if (!isset($this->privateKeyPath))
			return FALSE;
		// abre o arquivo que cont�m a chave privada e l� seu conte�do
		$this->_Mgr->open($this->privateKeyPath, FILE_MANAGER_READ_BINARY);
		$privateKey = $this->_Mgr->read(8192);
		// extrai a chave privada utilizando ou n�o frase de passagem
		if (isset($this->passPhrase))
			$keyResource = @openssl_get_privatekey($privateKey, $this->passPhrase);
		else
			$keyResource = @openssl_get_privatekey($privateKey);
		if (!TypeUtils::isResource($keyResource)) {
			$this->openSSLError = @openssl_error_string();
			return FALSE;
		} else {
			$this->privateKeyRes = $keyResource;
			return $keyResource;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::_getPublicKey
	// @desc		Extrai a chave p�blica do certificado fornecido no m�todo
	//				setCertificatePath, preparando-a para utiliza��o
	// @access		private
	// @return		resource Chave p�blica
	// @note		Retorna FALSE se n�o for poss�vel buscar a chave p�blica
	//!-----------------------------------------------------------------
	function _getPublicKey() {
		if (!isset($this->certificatePath))
			return FALSE;
		// busca o conte�do do certificado no objeto OpenSSLCertificate criado
		$certificate = $this->_Certificate->getContent();
		if (!$certificate)
			return FALSE;
		// extrai a chave p�blica do conte�do do arquivo
		$keyResource = @openssl_get_publickey($certificate);
		if (!TypeUtils::isResource($keyResource)) {
			$this->openSSLError = @openssl_error_string();
			return FALSE;
		} else {
			$this->publicKeyRes = $keyResource;
			return $keyResource;
		}
	}
}
?>