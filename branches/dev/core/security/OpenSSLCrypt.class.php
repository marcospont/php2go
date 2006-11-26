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
// @desc		Cria um nível de abstração sobre as funções de criptografia
//				da biblioteca openssl. Permite encriptar utilizando uma chave pública,
//				decriptar utilizando chave privada, assinar e verificar digitalmente
//				conteúdo ou arquivos
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
	var $certificatePath;	// @var certificatePath string					Arquivo que contém o certificado digital (chave pública)
	var $privateKeyPath;	// @var privateKeyPath string					Arquivo que contém a chave privada utilizada para decriptar e assinar dados
	var $passPhrase;		// @var passPhrase string						Frase de passagem utilizada em conjunto com a chave privada
	var $throwErrors;		// @var throwErrors bool						Indica se os erros devem ser exibidos como erros de aplicação ou não	
	var $errorMsg;			// @var errorMsg string							Armazena mensagens de erro geradas pela classe
	var $openSSLError;		// @var openSSLError string						Armazena mensagens de erro das funções da bilioteca open_ssl
	var $publicKeyRes;		// @var publicKeyRes resource					Armazena a chave pública ativa
	var $privateKeyRes;		// @var privateKeyRes resource					Armazena a chave privada ativa
	var $_Mgr;				// @var _Mgr FileManager object					Utilizado na manipulação de arquivos
	var $_Certificate;		// @var _Certificate OpenSSLCertificate object	Objeto para manipulação do certificado

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
	// @desc		Retorna a última mensagem de erro gerada pela classe
	// @access		public
	// @return		mixed Mensagem de erro ou NULL se não existem erros armazenados
	//!-----------------------------------------------------------------
	function getLastError() {
		return (isset($this->errorMsg)) ? $this->errorMsg : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::getLastInternalError
	// @desc		Retorna a última mensagem de erro gerada pela biblioteca openssl
	// @access		public
	// @return		mixed Mensagem de erro ou NULL se não existem erros armazenados
	//!-----------------------------------------------------------------
	function getLastInternalError() {
		return (isset($this->openSSLError) && !empty($this->openSSLError)) ? $this->openSSLError : NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	OpenSSLCrypt::&getCertificate
	// @desc		Retorna um objeto contendo informações sobre o certificado
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
	// @desc		Define o arquivo que contém a chave pública
	// @access		public
	// @param		pathToKey string	Caminho completo para o arquivo do certificado (chave pública)
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
	// @desc		Define o arquivo que contém a chave privada
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
	// @desc		Reseta a informação de chaves armazenada na classe
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
	// @desc		Método para encriptar dados utilizando a chave pública
	//				contida no certificado fornecido à classe
	// @access		public
	// @param		data string	Dados a serem encriptados
	// @param		saveTo string	"" Arquivo onde o resultado deve ser salvo
	// @return		mixed TRUE (utilizando arquivo de saída) ou o conteúdo encriptado
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
	// @desc		Encripta o conteúdo de um arquivo, gravando a saída em
	//				um outro arquivo
	// @access		public
	// @param		inFileName string		Arquivo com os dados a serem encriptados
	// @param		outFileName string	Arquivo onde a saída deve ser gravada
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
	// @desc		Método para decriptar dados utilizando a chave privada
	//				fornecida através do método OpenSSLCrypt::setPrivateKeyPath
	// @access		public
	// @param		data string	Dados encriptados
	// @param		saveTo string	"" Arquivo de saída
	// @return		mixed TRUE (utilizando arquivo de saída) ou o conteúdo decriptado
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
	// @desc		Método para decriptar dados de um arquivo e gerar a
	//				saída em um outro arquivo
	// @access		public
	// @param		inFileName string		Arquivo com dados encriptados
	// @param		outFileName string	Arquivo de saída
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
	// @desc		Método para gerar uma assinatura digital de um conteúdo
	// @access		public
	// @param		&data string			Dados a serem assinados digitalmente
	// @param		appendSignature bool	Indica se a assinatura deve ser concatenada ao conteúdo
	// @return		string Assinatura digital do conteúdo
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
	// @desc		Método para assinar digitalmente o conteúdo de um arquivo
	// @access		public
	// @param		inFileName string		Nome do arquivo
	// @param		appendFile bool			"FALSE" Se TRUE, gravará a assinatura ao final do próprio arquivo
	// @return		mixed TRUE (se $appendFile == TRUE) ou a assinatura do conteúdo do arquivo; FALSE em caso de quaisquer erros
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
	// @desc		Verifica a assinatura digital de conteúdo
	// @access		public
	// @param		data string		Conteúdo
	// @param		signature string	Assinatura do conteúdo
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
	// @desc		Extrai a chave pública do arquivo fornecido no método
	//				setPrivateKeyPath(), preparando-a para utilização
	// @access		private
	// @return		resource Chave privada
	// @note		Retorna FALSE se não for possível buscar a chave privada
	//!-----------------------------------------------------------------
	function _getPrivateKey() {
		if (!isset($this->privateKeyPath))
			return FALSE;
		// abre o arquivo que contém a chave privada e lê seu conteúdo
		$this->_Mgr->open($this->privateKeyPath, FILE_MANAGER_READ_BINARY);
		$privateKey = $this->_Mgr->read(8192);
		// extrai a chave privada utilizando ou não frase de passagem
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
	// @desc		Extrai a chave pública do certificado fornecido no método
	//				setCertificatePath, preparando-a para utilização
	// @access		private
	// @return		resource Chave pública
	// @note		Retorna FALSE se não for possível buscar a chave pública
	//!-----------------------------------------------------------------
	function _getPublicKey() {
		if (!isset($this->certificatePath))
			return FALSE;
		// busca o conteúdo do certificado no objeto OpenSSLCertificate criado
		$certificate = $this->_Certificate->getContent();
		if (!$certificate)
			return FALSE;
		// extrai a chave pública do conteúdo do arquivo
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