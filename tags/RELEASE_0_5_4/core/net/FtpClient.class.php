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
// $Header: /www/cvsroot/php2go/core/net/FtpClient.class.php,v 1.20 2006/10/26 04:27:59 mpont Exp $
// $Date: 2006/10/26 04:27:59 $

// @const FTP_DEFAULT_PORT "21"
// Porta padr�o do cliente FTP
define('FTP_DEFAULT_PORT', 21);

//!-----------------------------------------------------------------
// @class 		FtpClient
// @desc		Implementa��o de um cliente FTP (protocolo de transfer�ncia de arquivos),
//				utilizando como base as fun��es da extens�o "ftp" do PHP
// @package		php2go.net
// @extends 	PHP2Go
// @uses		System
// @uses		TypeUtils
// @author 		Marcos Pont
// @version		$Revision: 1.20 $
//!-----------------------------------------------------------------
class FtpClient extends PHP2Go
{
	var $host; 						// @var host string				Nome ou IP do host
	var $port = FTP_DEFAULT_PORT;	// @var port int				"FTP_DEFAULT_PORT" Porta para a conex�o
	var $user;						// @var user string				Usu�rio para conex�o no sevidor FTP
	var $password;					// @var password string			Senha para conex�o no servidor FTP
	var $connectionId; 				// @var connectionId resource	Identificador da conex�o
	var $localPath = ''; 			// @var localPath string		"" Caminho local atual
	var $remotePath = ''; 			// @var remotePath string		"" Caminho remoto atual
	var $sysType = ''; 				// @var sysType string			"" Identificador do tipo de sistema do servidor FTP
	var $timeout;					// @var timeout int				"" Timeout da conex�o em segundos
	var $transferMode = FTP_BINARY;	// @var transferMode int		"FTP_BINARY" Modo de transfer�ncia: ASCII ou bin�rio
	var $connected = FALSE; 		// @var connected bool			"FALSE" Indica se a conex�o est� ativa ou n�o
	var $defaultSettings = array();	// @var defaultSettings array	"array()" Vetor de propriedades default da classe para utiliza��o na fun��o Reset
	var $defaultMode = "0777";		// @var defaultMode string		"0777" Modo padr�o para cria��o de arquivos

	//!-----------------------------------------------------------------
	// @function 	FtpClient::FtpClient
	// @desc 		Construtor da classe
	// @access 		public
	//!-----------------------------------------------------------------
	function FtpClient() {
		parent::PHP2Go();
		if (!System::loadExtension("ftp"))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', "ftp"), E_USER_ERROR, __FILE__, __LINE__);
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::__destruct
	// @desc 		Destrutor do objeto cliente FTP
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		$this->quit();
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::setServer
	// @desc 		Configura o servidor e a porta a serem usadas na conex�o
	// @param 		host string		Nome ou IP do servidor FTP
	// @param 		port int		Porta para conex�o
	// @access 		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setServer($host, $port=FTP_DEFAULT_PORT) {
		if (!$this->isConnected()) {
			$this->host = $host;
			$this->port = TypeUtils::parseInteger($port);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::setUserInfo
	// @desc 		Configura o nome de usu�rio e senha para a conex�o
	// @param 		user string			Nome de usu�rio ou login
	// @param 		password string		Senha do usu�rio
	// @access 		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setUserInfo($user, $password) {
		if (!$this->isConnected()) {
			$this->user = $user;
			$this->password = $password;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::setTransferMode
	// @desc 		Configura o modo de transfer�ncia dos arquivos via FTP
	// @param 		mode int	Aceita as constantes FTP_ASCII e FTP_BINARY pr�-definidas no PHP
	// @access 		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setTransferMode($mode) {
		if ($mode == FTP_ASCII || $mode == FTP_BINARY)
			$this->transferMode = $mode;
	}
	
	//!-----------------------------------------------------------------
	// @function	FtpClient::setTimeout
	// @desc		Define o timeout, em segundos, para as opera��es executadas pelo cliente FTP
	// @param		timeout int		Timeout, em segundos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTimeout($timeout) {
		if ($timeout > 0)
			$this->timeout = $timeout;
	}
	
	//!-----------------------------------------------------------------
	// @function 	FtpClient::getCurrentDir
	// @desc 		Busca o diret�rio remoto atual
	// @return 		string Diret�rio remoto atual
	// @note		Retorna FALSE se a conex�o n�o estiver ativa
	// @access 		public	
	//!-----------------------------------------------------------------
	function getCurrentDir() {
		if (!$this->isConnected())
			return FALSE;
		if (!empty($this->remotePath))
			return $this->remotePath;
		$path = ftp_pwd($this->connectionId);
		if (!$path) {
			$this->remotePath = null;
			return FALSE;
		} else {
			$this->remotePath = $path;
			return $this->remotePath;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::getSysType
	// @desc 		Busca informa��es do tipo de sistema do servidor FTP
	// @return 		string Identificador do tipo de sistema do servidor FTP ou FALSE em caso de ocorr�ncia de erros
	// @note		Retorna FALSE se a conex�o n�o estiver ativa	
	// @access 		public	
	//!-----------------------------------------------------------------
	function getSysType() {
		if (!$this->isConnected())
			return FALSE;
		if (!empty($this->sysType))
			return $this->sysType;
		$sysType = ftp_systype($this->connectionId);
		if (!$sysType) {
			$this->sysType = NULL;
			return FALSE;
		} else {
			$this->sysType = $sysType;
			return $this->sysType;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::isConnected
	// @desc 		Verifica se a conex�o com o servidor est� ativa
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isConnected() {
		return ($this->connected && isset($this->connectionId) && TypeUtils::isResource($this->connectionId));
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::connect
	// @desc 		Abre uma conex�o com o servidor FTP
	// @access 		public
	// @return 		bool
	//!-----------------------------------------------------------------
	function connect() {		
		if ($this->isConnected())
			$this->quit();
		if (!isset($this->host))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FTP_MISSING_HOST'), E_USER_ERROR, __FILE__, __LINE__);
		$this->connectionId = ftp_connect($this->host, $this->port, $this->timeout);
		if (!TypeUtils::isResource($this->connectionId))
			return FALSE;
		$this->connected = TRUE;
		// define o timeout se estiver configurado na classe
		if (isset($this->timeout))
			ftp_set_option($this->connectionId, FTP_TIMEOUT_SEC, $this->timeout);
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::login
	// @desc 		Executa autentica��o no servidor FTP
	// @param 		anonymous bool		"FALSE" Indica se deve ser utilizado usu�rio an�nimo
	// @access 		public	
	// @return 		bool
	//!-----------------------------------------------------------------
	function login($anonymous=FALSE) {
		if (!$this->isConnected())
			$this->connect();
		if ((!isset($this->user) || !isset($this->password)) && !$anonymous)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FTP_MISSING_USER_OR_PASS'), E_USER_ERROR, __FILE__, __LINE__);
		$authUser = ($anonymous ? 'anonymous' : $this->user);
		$authPass = ($anonymous ? 'anonymous@ftpclient.php2go.org' : $this->password);
		return ftp_login($this->connectionId, $authUser, $authPass);
	}

	//!-----------------------------------------------------------------
	// @function	FtpClient::restart
	// @desc		Reinicializa o cliente para execu��o de uma nova conex�o
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function restart() {
		if ($this->isConnected())
			$this->quit();
		foreach($this->defaultSettings as $property => $value)
			$this->$property = $value;
		unset($this->host);
		unset($this->user);
		unset($this->password);
		unset($this->connectionId);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::quit
	// @desc 		Encerra a conex�o com o servidor FTP, se estiver ativa
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function quit() {
		if (!$this->isConnected())
			return FALSE;
		$this->connected = (!ftp_quit($this->connectionId));
		if (!$this->connected)
			unset($this->connectionId);
		return (!$this->connected);
	}
	//!-----------------------------------------------------------------
	// @function 	FtpClient::site
	// @desc 		Executa um comando no servidor FTP
	// @param 		command string    Comando a ser executado
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function site($command) {
		return ($this->isConnected() ? ftp_site($this->connectionId, $command) : FALSE);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::togglePassiveMode
	// @desc 		Liga ou desliga o modo passivo
	// @param 		mode bool		Modo a ser setado. TRUE liga e FALSE desliga
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function togglePassiveMode($mode) {
		return ($this->isConnected() ? ftp_pasv($this->connectionId, (bool)$mode) : FALSE);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::changeDir
	// @desc 		Muda o diret�rio remoto atual
	// @param 		directory string	Novo diret�rio
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function changeDir($directory) {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_chdir($this->connectionId, $directory);
		if ($result)
			$this->remotePath = ftp_pwd($this->connectionId);
		return (bool)$result;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::changeDirUp
	// @desc 		Sobe um n�vel na �rvore de diret�rios do servidor FTP
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function changeDirUp() {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_cdup($this->connectionId);
		if ($result)
			$this->remotePath = ftp_pwd($this->connectionId);
		return (bool)$result;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::makeDir
	// @desc 		Cria um novo diret�rio no servidor FTP
	// @param 		directory string	Nome do novo diret�rio
	// @param 		moveDir bool		Move o ponteiro para o diret�rio criado
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function makeDir($directory, $moveDir=FALSE) {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_mkdir($this->connectionId, $directory);
		if ($result && $moveDir)
			return $this->changeDir($directory);
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::removeDir
	// @desc 		Remove um diret�rio no servidor FTP
	// @param 		directory string	Nome do diret�rio a ser removido
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function removeDir($directory) {
		return ($this->isConnected() ? ftp_rmdir($this->connectionId, $directory) : FALSE);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::removeDirRecursive
	// @desc 		Remove arquivos e subdiret�rios a partir do diret�rio informado no par�metro $directory
	// @param 		directory string	Nome do diret�rio a ser removido
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function removeDirRecursive($directory) {
		if ($directory != '') {
			if (!$this->changeDir($directory)) 
				return FALSE;
			$files = $this->rawList();
			if (is_array($files)) {
				for ($i=0, $s=sizeof($files); $i<$s; $i++) {
					$fileInfo = $files[$i];
					if ($fileInfo['type'] == 'dir') {
						if (!$this->removeDirRecursive($fileInfo['name']))
							return FALSE;
					} elseif (!$this->delete($fileInfo['name'])) {
						return FALSE;
					}
				}
				if ($this->changeDirUp() && $this->removeDir($directory))
					return TRUE;				
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::fileList
	// @desc 		Busca a lista de nomes de arquivos de um diret�rio remoto
	// @access 		public
	// @param		path string			"" Caminho e/ou m�scara de arquivo a ser utilizada
	// @return		mixed Vetor com a lista de arquivos ou FALSE em caso de erros
	// @note 		Se for fornecido um diret�rio e uma m�scara de arquivos como
	// 				par�metro para o m�todo (ex: folder/file*.txt), o diret�rio
	// 				atual ser� trocado para 'folder' e a m�scara de arquivos ser�
	// 				aplicada sobre o novo diret�rio
	//!-----------------------------------------------------------------
	function fileList($path='') {
		if (!$this->isConnected())
			return FALSE;
		if ($path != '') {
			list($dir, $fileMask) = $this->_parseDir($path);
			if (!empty($dir))
				$this->changeDir($dir);
		} else {
			$fileMask = '';
		}
		$result = ftp_nlist($this->connectionId, $fileMask);
		if ($result)
			return $result;
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::rawList
	// @desc 		Busca as informa��es sobre os arquivos de um diret�rio remoto
	// @param		path string			"" Caminho e/ou m�scara de arquivo a ser utilizada
	// @param 		parseInfo bool	"TRUE" Retornar as informa��es em um vetor bidimensional
	// @note 		Se for fornecido um diret�rio e uma m�scara de arquivos como
	// 				par�metro para o m�todo (ex: folder/file*.txt), o diret�rio
	// 				atual ser� trocado para 'folder' e a m�scara de arquivos ser�
	// 				aplicada sobre o novo diret�rio
	// @return		mixed Vetor com a lista de arquivos ou FALSE em caso de erros	
	// @access 		public	
	//!-----------------------------------------------------------------
	function rawList($path='', $parseInfo=TRUE) {
		if (!$this->isConnected())
			return FALSE;
		if ($path != '') {
			list($dir, $fileMask) = $this->_parseDir($path);
			if (!empty($dir))
				$this->changeDir($dir);
		} else {
			$fileMask = '';
		}
		$result = ftp_rawlist($this->connectionId, $fileMask);
		if ($result)
			return ($parseInfo ? $this->_parseRawList($result) : $result);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::get
	// @desc 		Realiza o download de um arquivo do servidor FTP remoto
	// @param 		localFile string		Nome local para o arquivo
	// @param 		remoteFile string		Nome do arquivo remoto ou caminho a partir do diret�rio atual
	// @param 		mode int				"NULL" Modo da transfer�ncia (FTP_BINARY ou FTP_ASCII)
	// @param		resume int				"NULL" Posi��o, em bytes, a partir da qual a transfer�ncia deve ser recome�ado
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function get($localFile, $remoteFile, $mode=NULL, $resume=NULL) {
		if (!$this->isConnected())
			return FALSE;
		// usa o modo padr�o, se n�o for fornecido um modo
		if (empty($mode) || ($mode != FTP_ASCII && $mode != FTP_BINARY))
			$mode = $this->transferMode;
		$result = ftp_get($this->connectionId, $localFile, $remoteFile, $mode, (TypeUtils::isInteger($resume) && $resume >= 0 ? $resume : 0));
		return ($result == FTP_FINISHED);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::fileGet
	// @desc 		Realiza o download de um arquivo do servidor FTP gravando
	// 				seu conte�do no arquivo aberto referenciado pelo par�metro $filePointer
	// @param 		filePointer resource	Arquivo aberto onde o conte�do do arquivo remoto deve ser gravado
	// @param 		remoteFile string		Nome do arquivo remoto ou caminho a partir do diret�rio atual
	// @param 		mode int				"NULL" Modo da transfer�ncia [FTP_BINARY | FTP_ASCII]
	// @param		resume bool				"FALSE" Considerar transfer�ncia parcial j� executada
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function fileGet($filePointer, $remoteFile, $mode=NULL, $resume=FALSE) {
		if (!$this->isConnected())
			return FALSE;
		if (!TypeUtils::isResource($filePointer))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_RESOURCE', array('$filePointer', '$FtpClient->fileGet')), E_USER_ERROR, __FILE__, __LINE__);
		// usa o modo padr�o, se n�o for fornecido um modo
		if (empty($mode) || ($mode != FTP_ASCII && $mode != FTP_BINARY))
			$mode = $this->transferMode;
		$result = ftp_fget($this->connectionId, $filePointer, $remoteFile, $mode, ($resume ? filesize($filePointer) : 0));
		return ($result == FTP_FINISHED);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::nGet
	// @desc 		Realiza o download de todos os arquivos e diret�rios
	// 				a partir do ponto informado no par�metro $directory
	// @param 		directory string	"" Diret�rio a ser utilizado como base	
	// @param 		mode int			"NULL" Modo da transfer�ncia: FTP_BINARY ou FTP_ASCII
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function nGet($directory='', $mode=NULL) {
		if (!$this->isConnected())
			return FALSE;
		$list = $this->rawList($directory);
		if (empty($list) || !is_array($list) || !$this->changeDir($directory))
			return FALSE;
		foreach ($list as $entry) {
			switch ($entry['type']) {
				case 'dir' :
					if (@mkdir($entry['name']) && chdir($entry['name']) && $this->changeDir($entry['name']) && $this->nGet('', $mode)) {
						chdir('..');
						$this->changeDirUp();
					}
					break;
				case 'file' :
					if (!$this->get($entry['name'], $entry['name'], $mode))
						return FALSE;
					break;
			}
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::put
	// @desc 		Copia um arquivo local para o servidor FTP remoto
	// @param 		localFile string	Nome local do arquivo
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diret�rio atual
	// @param 		mode int			"NULL" Modo da transfer�ncia: FTP_BINARY ou FTP_ASCII
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function put($localFile, $remoteFile, $mode=NULL) {
		if (!$this->isConnected())
			return FALSE;
		// muda para o diret�rio de destino, se for fornecido um caminho relativo
		list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
		if (!empty($changeDir) && !$this->changeDir($changeDir))
			return FALSE;
		// usa o modo padr�o, se n�o for fornecido um modo
		if (empty($mode) || ($mode != FTP_ASCII && $mode != FTP_BINARY))
			$mode = $this->transferMode;
		return ftp_put($this->connectionId, $remoteFile, $localFile, $mode);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::filePut
	// @desc 		Copia o conte�do do arquivo aberto referenciado pelo
	// 				ponteiro $filePointer para o servidor FTP remoto
	// @param 		filePointer resource	Arquivo aberto que dever� ser copiado para o servidor FTP
	// @param 		remoteFile string		Nome do arquivo remoto ou caminho a partir do diret�rio atual
	// @param 		mode int				"NULL" Modo da transfer�ncia: FTP_BINARY ou FTP_ASCII
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function filePut($filePointer, $remoteFile, $mode=NULL) {
		if (!$this->isConnected())
			return FALSE;
		if (!TypeUtils::isResource($filePointer))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_RESOURCE', array('$filePointer', 'FtpClient::filePut')), E_USER_ERROR, __FILE__, __LINE__);
		// muda para o diret�rio de destino, se for fornecido um caminho relativo
		list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
		if (!empty($changeDir) && !$this->changeDir($changeDir))
			return FALSE;
		// usa o modo padr�o, se n�o for fornecido um modo
		if (empty($mode) || ($mode != FTP_ASCII && $mode != FTP_BINARY))
			$mode = $this->transferMode;
		return ftp_fput($this->connectionId, $remoteFile, $filePointer, $mode);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::nPut
	// @desc 		Copia para o servidor FTP todos os arquivos e diret�rios
	// 				a partir do ponto informado no par�metro $directory
	// @param 		directory string	"" Diret�rio a ser utilizado como base
	// @param 		mode int			"NULL" Modo da transfer�ncia [FTP_BINARY | FTP_ASCII]
	// @access 		public	
	// @return	 	bool
	//!-----------------------------------------------------------------
	function nPut($directory='', $mode=NULL) {
		if (!$this->isConnected())
			return FALSE;
		if (!empty($directory)) {
			if (!is_dir($directory))
				return FALSE;
			chdir($directory);
		}
		if ($handle = opendir(getcwd())) {
			while (FALSE !== ($fileName = readdir($handle))) {
				if ($fileName != '.' && $fileName != '..') {
					if (is_dir($fileName)) {
						chdir($fileName);
						if ($this->makeDir($fileName, TRUE) && $this->nPut('', $mode)) {
							chdir('..');
							$this->changeDirUp();
						} else {
							return FALSE;
						}
					} elseif (is_file($fileName)) {
						if (!$this->put($fileName, $fileName, $mode))
							return FALSE;
					}
				}
			}
			closedir($handle);
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::delete
	// @desc 		Apaga um arquivo no servidor FTP
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diret�rio atual
	// @access 		public	
	// @return		bool
	// @see 		FtpClient::rename
	//!-----------------------------------------------------------------
	function delete($remoteFile) {
		if (!$this->isConnected())
			return FALSE;
		return ftp_delete($this->connectionId, $remoteFile);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::rename
	// @desc 		Renomeia um arquivo no servidor FTP
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diret�rio atual
	// @param 		newName string	Novo nome para o arquivo remoto
	// @access 		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function rename($remoteFile, $newName) {
		if (!$this->isConnected())
			return FALSE;
		return ftp_rename($this->connectionId, $remoteFile, $newName);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::fileLastMod
	// @desc 		Busca a data da �ltima modifica��o de um arquivo no servidor FTP
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diret�rio atual
	// @param 		formatDate bool		"TRUE" Indica se o timestamp retornado deve ser formatado
	// @return 		mixed Timestamp ou data da �ltima modifica��o do arquivo ou FALSE em caso de erros
	// @note 		Nem todos os servidores suportam a fun��o ftp_mdtm nativa do PHP.
	// 				Esta fun��o tamb�m n�o pode ser aplicada a diret�rios. Nestes casos,
	// 				fileLastMod retorna FALSE
	// @access 		public	
	//!-----------------------------------------------------------------
	function fileLastMod($remoteFile, $formatDate=TRUE) {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_mdtm($this->connectionId, $remoteFile);
		if ($result && $result != 1)
			return ($formatDate ? Date::formatTime($result) : $result);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::fileSize
	// @desc		Consulta o tamanho em bytes de um arquivo no servidor FTP
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diret�rio atual
	// @return 		mixed Tamanho do arquivo em bytes ou FALSE em caso de erros
	// @note 		Nem todos os servidores suportam a fun��o ftp_size que � executa neste m�todo	
	// @access 		public	
	//!-----------------------------------------------------------------
	function fileSize($remoteFile) {
		if (!$this->isConnected())
			return FALSE;
		$result = ftp_size($this->connectionId, $remoteFile);
		if ($result && $result != -1)
			return $result;
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::_parseDir
	// @desc 		Busca o diret�rio e a m�scara de arquivo a partir de
	// 				um par�metro $directory passado �s fun��es fileList ou rawList
	// @access 		private
	// @param 		str string	Par�metro $directory
	// @return 		array Vetor contendo diret�rio e m�scara de arquivos
	//!-----------------------------------------------------------------
	function _parseDir($str) {
		if (strpos($str, '/') !== FALSE) {
			$slashPos = strrpos($str, '/');
			return array(substr($str, 0, $slashPos + 1), substr($str, $slashPos + 1, strlen($str) - $slashPos));
		} else {
			return array('', $str);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::_parseRawList
	// @desc 		Processa as informa��es retornadas da listagem de dados
	// 				de arquivos de um diret�rio no servidor FTP, armazenando-as
	// 				em um vetor
	// @access 		private
	// @param 		rawList array	Vetor retornado pela fun��o ftp_rawlist
	// @return 		array Vetor com dados dos arquivos organizados em novos vetores ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function _parseRawList($rawList) {
		if (TypeUtils::isArray($rawList)) {
			$newList = array();
			$fileInfo = array();
			while (list($k) = each($rawList)) {
				$element = split(' {1,}', $rawList[$k], 9);
				if (TypeUtils::isArray($element) && (sizeof($element) == 9)) {
					unset($fileInfo);
					$dateF = PHP2Go::getConfigVal('LOCAL_DATE_FORMAT');
					$year = (FALSE === strpos($element[7], ':') ? $element[7] : date('Y'));
					$month = $element[5];
					$day = (strlen($element[6]) == 2 ? $element[6] : '0' . $element[6]);
					$fileInfo['name'] = $element[8];
					$fileInfo['size'] = TypeUtils::parseInteger($element[4]);
					$fileInfo['date'] = ($dateF == 'Y/m/d') ? $year . '/' . $month . '/' . $day : $day . '/' . $month . '/' . $year;
					$fileInfo['attr'] = $element[0];
					$fileInfo['type'] = ($element[0][0] == '-') ? 'file' : 'dir';
					$fileInfo['dirno'] = TypeUtils::parseInteger($element[1]);
					$fileInfo['user'] = $element[2];
					$fileInfo['group'] = $element[3];
					$newList[] = $fileInfo;
				}
			}
			return $newList;
		}
		return FALSE;
	}
}
?>