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
// $Header: /www/cvsroot/php2go/core/security/Crypt.class.php,v 1.13 2006/04/05 23:43:21 mpont Exp $
// $Date: 2006/04/05 23:43:21 $

//------------------------------------------------------------------
import('php2go.file.FileManager');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

// @const CRYPT_DEFAULT_CIPHER		"MCRYPT_3DES"
// O 3DES é o algoritmo padrão utilizado na classe
define('CRYPT_DEFAULT_CIPHER', MCRYPT_3DES);

// @const CRYPT_DEFAULT_MODE		"MCRYPT_MODE_CFB"
// O CFB, ou Cipher Feedback, é o modo padrão de ciframento de bloco utilizado
define('CRYPT_DEFAULT_MODE', MCRYPT_MODE_CFB);

//!-----------------------------------------------------------------
// @class		Crypt
// @desc		A classe Crypt é uma abstração sobre as funções da biblioteca
//				mcrypt, que é uma das extensões disponíveis na instalação do PHP.
//				Possui métodos que tornam fácil a tarefa de encriptar ou desencriptar
//				dados, utilizando qualquer dos algoritmos disponíveis.
// @package		php2go.security
// @uses		FileManager
// @uses		StringUtils
// @uses		System
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.13 $
// @note		Exemplo de uso:
//				<pre>
//
//				$c = new Crypt();
//				$c->setCipher(MCRYPT_BLOWFISH);
//				$c->setCipherMode(MCRYPT_MODE_CBC);
//				$c->setKey('this is the encrypt key');
//				$encrypted = $c->engineEncrypt('this is secret data that must be encrypted');
//				$decrypted = $c->engineDecrypt($encrypted);
//
//				</pre>
//!-----------------------------------------------------------------
class Crypt extends PHP2Go
{
	var $cipher;		// @var cipher string			Armazena o algoritmo utilizado para encriptar/desencriptar dados
	var $cipherMode;	// @var cipherMode string		Armazena o modo de criptografia utilizado
	var $key;			// @var key string				Armazena a chave de criptografia
	var $cResource;		// @var cResource resource		Resource que representa o módulo de criptografia ativo, para o algoritmo e o modo escolhidos
	var $iVector;		// @var iVector string			Vetor de inicialização criado para o módulo aberto

	//!-----------------------------------------------------------------
	// @function	Crypt::Crypt
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Crypt() {
		parent::PHP2Go();
		if (!System::loadExtension('mcrypt'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'mcrypt'), E_USER_ERROR, __FILE__, __LINE__);
		$this->cipher = CRYPT_DEFAULT_CIPHER;
		$this->cipherMode = CRYPT_DEFAULT_MODE;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::getCipher
	// @desc		Busca o algoritmo ativo na configuração da classe
	// @access		public
	// @return		string Nome do algoritmo
	//!-----------------------------------------------------------------
	function getCipher() {
		return $this->cipher;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::getCipherList
	// @desc		Busca a lista de algoritmos disponíveis na biblioteca carregada no PHP
	// @access		public
	// @return		array Vetor contendo os nomes dos algoritmos
	//!-----------------------------------------------------------------
	function getCipherList() {
		return mcrypt_list_algorithms();
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::getCipherMode
	// @desc		Busca o modo de criptografia ativo
	// @access		public
	// @return		string Nome do modo (cfb, efb, ofb, etc...)
	//!-----------------------------------------------------------------
	function getCipherMode() {
		return $this->cipherMode;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::getModeList
	// @desc		Busca a lista de modos de criptografia de bloco disponíveis
	// @access		public
	// @return		array Vetor contendo os nomes dos modos
	//!-----------------------------------------------------------------
	function getModeList() {
		return mcrypt_list_modes();
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::getKey
	// @desc		Busca o valor da chave atualmente setada na classe
	// @access		public
	// @return		mixed Chave de criptografia utilizada ou NULL se ainda não foi fornecida uma chave à classe
	//!-----------------------------------------------------------------
	function getKey() {
		if (!isset($this->key))
			return NULL;
		else
			return $this->key;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::setCipher
	// @desc		Altera o algoritmo a ser utilizado
	// @access		public
	// @param		cipher string		Nome do algoritmo
	// @return		bool Retorna FALSE se o algoritmo não for válido para a lista de disponíveis
	//!-----------------------------------------------------------------
	function setCipher($cipher) {
		if (in_array($cipher, $this->getCipherList()) && $cipher != $this->cipher) {
			$this->cipher = $cipher;
			if (isset($this->key))
				$this->clearKey();
			return TRUE;
		} else
			return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::setCipherMode
	// @desc		Configura o modo de criptografia a ser utilizado
	// @access		public
	// @param		cipherMode string	Nome do modo
	// @return		bool Retorna FALSE se o modo não for válido
	//!-----------------------------------------------------------------
	function setCipherMode($cipherMode) {
		if (in_array($cipherMode, $this->getModeList()) && $cipherMode != $this->cipherMode) {
			$this->cipherMode = $cipherMode;
			return TRUE;
		} else
			return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::setKey
	// @desc		Configura a chave de criptografia a ser utilizada
	// @access		public
	// @param		key string	Chave de criptografia
	// @return		void
	// @note		A chave de criptografia utilizada para encriptar uma string ou o
	//				conteúdo de um arquivo deve ser a mesma utilizada para desencriptá-lo
	//!-----------------------------------------------------------------
	function setKey($key) {
		if (!empty($key)) {
			$keySize = @mcrypt_get_key_size($this->cipher, $this->cipherMode);
			if (strlen($key) < $keySize)
				$this->key = md5($key);
			elseif (strlen($key) > $keySize)
				$this->key = StringUtils::left($key, $keySize);
			$this->key = $key;
		}

	}

	//!-----------------------------------------------------------------
	// @function	Crypt::clearKey
	// @desc		Limpa o valor atualmente armazenado na classe para a chave de criptografia
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clearKey() {
		unset($this->key);
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::engineEncrypt
	// @desc		Método genérico para encriptar dados, baseado no algoritmo,
	//				no modo e na chave setados nas propriedades da classe
	// @access		public
	// @param		data string		Dados a serem encriptados
	// @param		saveTo string		"" Nome do arquivo no qual o resultado deve ser salvo
	// @return		mixed Em caso de erros, retorna FALSE. Se for solicitada a gravação
	//				do resultado em um arquivo, retorna TRUE se a operação for realizada com
	//				sucesso. Se não for fornecido um arquivo, retorna o conteúdo
	//				encriptado
	//!-----------------------------------------------------------------
	function engineEncrypt($data, $saveTo='') {
		if ($this->_initialize()) {
			mcrypt_generic_init($this->cResource, $this->key, $this->iVector);
			$encryptedData = mcrypt_generic($this->cResource, $data);
			mcrypt_generic_deinit($this->cResource);
			mcrypt_module_close($this->cResource);
			if (trim($saveTo) != '') {
				$Mgr =& new FileManager();
				$Mgr->open($saveTo, FILE_MANAGER_WRITE_BINARY);
				$Mgr->write(base64_encode($this->iVector . $encryptedData));
				$Mgr->close();
				return TRUE;
			}
			return base64_encode($this->iVector . $encryptedData);
		} else
			return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::engineEncryptFile
	// @desc		Método para encriptar o conteúdo de um arquivo, exportando
	//				o resultado da operação para um outro arquivo
	// @access		public
	// @param		inFileName string		Caminho completo do arquivo de entrada, que contém os dados a serem encriptados
	// @param		outFileName string	Caminho completo do arquivo de saída
	// @return		bool
	//!-----------------------------------------------------------------
	function engineEncryptFile($inFileName, $outFileName) {
		$Mgr = new FileManager();
		if ($Mgr->open($inFileName, FILE_MANAGER_READ_BINARY)) {
			$fileContents = $Mgr->readFile();
			$Mgr->close();
			if ($Mgr->open($outFileName, FILE_MANAGER_WRITE_BINARY)) {
				if ($encryptedData = $this->engineEncrypt($fileContents)) {
					$Mgr->write($encryptedData);
					$Mgr->close();
					return TRUE;
				} else {
					$Mgr->close();
					return FALSE;
				}
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::engineDecrypt
	// @desc		Método genérico para a decodificação de dados encriptados
	//				em outra oportunidade pelo método engineEncrypt
	// @access		public
	// @param		data string		Dados codificados
	// @param		saveTo string		"" Arquivo onde a saída da operação deve ser gravada
	// @return		mixed Retorna FALSE em caso de erros. Se for fornecido um arquivo para a gravação
	//				da saída da operação, retorna TRUE. Em caso contrário, retorna o valor
	//				desencriptado do parâmetro $data
	// @note		Ao desencriptar dados, devem ser utilizados o mesmo algoritmo, mesmo modo
	//				e mesma chave de criptografia
	//!-----------------------------------------------------------------
	function engineDecrypt($data, $saveTo='') {
		if ($this->_initialize()) {
			$data = base64_decode($data);
			$iVectorSize = mcrypt_enc_get_iv_size($this->cResource);
			$iVector = substr($data, 0, $iVectorSize);
			$data = substr($data, $iVectorSize);
			mcrypt_generic_init($this->cResource, $this->key, $iVector);
			$decryptedData = mdecrypt_generic($this->cResource, $data);
			mcrypt_generic_deinit($this->cResource);
			mcrypt_module_close($this->cResource);
			if (trim($saveTo) != '') {
				$Mgr =& new FileManager();
				$Mgr->open($saveTo, FILE_MANAGER_WRITE_BINARY);
				$Mgr->write($decryptedData);
				$Mgr->close();
				return TRUE;
			}
			return $decryptedData;
		} else
			return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::engineDecryptFile
	// @desc		Permite desencriptar o conteúdo de um arquivo, redirecionando
	//				a saída da operação para um outro arquivo
	// @access		public
	// @param		inFileName string		Caminho completo do arquivo que contém os dados encriptados
	// @param		outFileName string	Caminho completo do arquivo de saída
	// @return		bool
	//!-----------------------------------------------------------------
	function engineDecryptFile($inFileName, $outFileName) {
		$Mgr = new FileManager();
		if ($Mgr->open($inFileName, FILE_MANAGER_READ_BINARY)) {
			$fileContents = $Mgr->readFile();
			$Mgr->close();
			if ($Mgr->open($outFileName, FILE_MANAGER_WRITE_BINARY)) {
				if ($decryptedData = $this->engineDecrypt($fileContents)) {
					$Mgr->write($decryptedData);
					$Mgr->close();
					return TRUE;
				} else {
					$Mgr->close();
					return FALSE;
				}
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Crypt::_initialize
	// @desc		Inicializa o módulo de criptografia para o algoritmo
	//				e o modo escolhidos
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _initialize() {
		if (!isset($this->key)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CRYPT_MISSING_KEY'), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if (!$this->cResource = @mcrypt_module_open($this->cipher, '', $this->cipherMode, '')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CRYPT_OPEN_MODULE', array($this->cipher, $this->cipherMode)), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		if (!$this->iVector = @mcrypt_create_iv(mcrypt_enc_get_iv_size($this->cResource), MCRYPT_RAND))
			return FALSE;
		else
			return TRUE;
	}
}
?>