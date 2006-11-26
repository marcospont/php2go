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
// $Header: /www/cvsroot/php2go/core/file/FileUpload.class.php,v 1.18 2006/05/07 15:13:07 mpont Exp $
// $Date: 2006/05/07 15:13:07 $

//------------------------------------------------------------------
import('php2go.file.FileManager');
import('php2go.text.StringUtils');
import('php2go.util.Callback');
//------------------------------------------------------------------

// Define as constantes de erro de upload que só foram inseridas na versão 4.3.0
// http://docs.php.net/en/features.file-upload.html#features.file-upload.errors
if (!defined('UPLOAD_ERR_OK'))
	define('UPLOAD_ERR_OK', 0);
if (!defined('UPLOAD_ERR_INI_SIZE'))
	define('UPLOAD_ERR_INI_SIZE', 1);
if (!defined('UPLOAD_ERR_FORM_SIZE'))
	define('UPLOAD_ERR_FORM_SIZE', 2);
if (!defined('UPLOAD_ERR_PARTIAL'))
	define('UPLOAD_ERR_PARTIAL', 3);
if (!defined('UPLOAD_ERR_NO_FILE'))
	define('UPLOAD_ERR_NO_FILE', 4);
if (!defined('UPLOAD_ERR_NO_TMP_DIR'))
	define('UPLOAD_ERR_NO_TMP_DIR', 6);

// @const	FILE_UPLOAD_MAX_SIZE		"2000000"
// Limite padrão de tamanho para upload de arquivos
define('FILE_UPLOAD_MAX_SIZE', 2000000);

//!-----------------------------------------------------------------
// @class		FileUpload
// @desc		Classe responsável por verificar a validade e a integridade
//				de uma operação de upload de arquivos via HTTP POST. Se as
//				checagens forem feitas com sucesso, o arquivo será movido
// @package		php2go.file
// @extends		PHP2Go
// @uses		FileManager
// @author		Marcos Pont
// @version		$Revision: 1.18 $
//!-----------------------------------------------------------------
class FileUpload extends PHP2Go
{
	var $uploadHandlers = array();			// @var uploadHandlers array				"array()" Vetor contendo os tratadores de upload criados pelo usuário
	var $allowedFileTypes = array();		// @var allowedFileTypes array				"array()" Vetor de tipos MIME permitidos nas operações de upload
	var $maxFileSize;						// @var maxFileSize int						Tamanho máximo permitido para upload de arquivos
	var $verboseSigns;						// @var verboseSigns string					Caracteres especiais a serem retirados de nomes de arquivos
	var $overwriteFiles = TRUE;				// @var overwriteFiles bool					"TRUE" Indica que os arquivos já existentes no servidor devem ser sobrescritos
	var $SaveCallback = NULL;				// @var SaveCallback Callback object		"NULL" Função global customizada para mover/salvar arquivos

	//!-----------------------------------------------------------------
	// @function	FileUpload::FileUpload
	// @desc		Construtor da classe FileUpload
	// @access		public
	//!-----------------------------------------------------------------
	function FileUpload() {
		parent::PHP2Go();
		$this->maxFileSize = FILE_UPLOAD_MAX_SIZE;
		$this->verboseSigns = "[[:space:]]|[\"\*\\\'\%\$\&\@\<\>]";
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::&getInstance
	// @desc		Retorna um singleton da classe FileUpload
	// @access		public
	// @return		FileUpload object
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new FileUpload();
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::addHandler
	// @desc		Adiciona um tratador de upload para uma determinada variável
	//				de formulário (campo tipo file)
	// @access		public
	// @param		fieldName string Nome do campo de formulário enviado via POST
	// @param		savePath string	Caminho onde o arquivo deve ser salvo
	// @param		saveName string	"" Nome para o arquivo criado
	// @param		mode string	"NULL" Modo para o arquivo criado, mantém o modo padrão se não for fornecido
	// @param		callback mixed "NULL" Função a ser utilizada para salvar o arquivo
	// @return		int Índice criado para o handler
	//!-----------------------------------------------------------------
	function addHandler($fieldName, $savePath, $saveName='', $mode=0644, $callback=NULL) {
		$newHandler = array(
			'field' => $fieldName,
			'save_path' => (!empty($savePath) ? $savePath : getcwd()),
			'save_name' => $saveName,
			'save_mode' => $mode,
			'error' => ''
		);
		if (!TypeUtils::isNull($callback))
			$newHandler['save_callback'] =& new Callback($callback);
		else
			$newHandler['save_callback'] = NULL;
		$this->uploadHandlers[] =& $newHandler;
		return sizeof($this->uploadHandlers) - 1;
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::setAllowedTypes
	// @desc		Configura os tipos MIME permitidos nas operações de upload da classe
	// @access		public
	// @return		void
	// @note		Este método pode receber N parâmetros, cada um com uma
	//				extensão MIME permitida. Ex: image/jpeg, image/gif, text/plain...
	//!-----------------------------------------------------------------
	function setAllowedTypes() {
		$this->allowedFileTypes = func_get_args();
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::setMaxFileSize
	// @desc		Indica o tamanho máximo permitido para a operação de upload
	// @access		public
	// @param		maxSize int			Tamanho máximo permitido para upload de arquivos
	// @return		void
	// @note		O tamanho máximo fornecido não poderá ser maior do que o tamanho
	//				máximo de upload definido na inicialização do PHP
	//!-----------------------------------------------------------------
	function setMaxFileSize($maxSize) {
		$maxPHP = str_replace(array('g', 'G', 'm', 'M', 'k', 'K'), array('000000000', '000000000', '000000', '000000', '000', '000'), System::getIni('upload_max_filesize'));
		$maxUser = str_replace(array('g', 'G', 'm', 'M', 'k', 'K'), array('000000000', '000000000', '000000', '000000', '000', '000'), $maxSize);
		$this->maxFileSize = min(TypeUtils::parseInteger($maxPHP), TypeUtils::parseIntegerPositive($maxUser));
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::setOverwriteFiles
	// @desc		Define se arquivos existentes devem ser sobrescritos ou não
	// @access		public
	// @param		setting bool	"TRUE" Valor para a propriedade
	// @return		void
	//!-----------------------------------------------------------------
	function setOverwriteFiles($setting=TRUE) {
		$this->overwriteFiles = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::setSaveCallback
	// @desc		Define a função a ser utilizada para salvar os arquivos, sobrescrevendo
	//				o mecanismo interno da classe
	// @access		public
	// @param		callback mixed	Definição da função ou método de callback
	// @return		void
	//!-----------------------------------------------------------------
	function setSaveCallback($callback) {
		$this->SaveCallback =& new Callback($callback);
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::getHandlerByName
	// @desc		Busca um determinado handler de upload a partir do nome do campo de formulário
	// @access		public
	// @param		field string	Nome do campo de formulário
	// @return		array Vetor contendo os dados do handler: field, save_path, save_name, save_mode
	// @note		Se for executado após o método upload(), este método irá conter
	//				os valores já processados do nome do arquivo, incluindo possíveis
	//				alterações de segurança realizadas no nome ou no caminho de gravação,
	//				bem como os erros ocorridos na operação de upload
	//!-----------------------------------------------------------------
	function getHandlerByName($field) {
		if (empty($this->uploadHandlers))
			return FALSE;
		for ($i=0; $i<sizeof($this->uploadHandlers); $i++) {
			if ($this->uploadHandlers[$i]['field'] == $field)
				return $this->uploadHandlers[$i];
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::getErrorAt
	// @desc		Retorna o erro gerado pelo upload de um determinado handler
	// @access		public
	// @param		index int		Índice do handler
	// @return		string Texto do erro ou FALSE caso a operação tenha sido realizada com sucesso
	//!-----------------------------------------------------------------
	function getErrorAt($index) {
		if (isset($this->uploadHandlers[$index])) {
			return $this->uploadHandlers[$index]['error'] != '' ? $this->uploadHandlers[$index]['error'] : FALSE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::logErrors
	// @desc		Gera um arquivo de log dos erros ocorridos nas operações de upload
	// @access		public
	// @param		logFile string	Nome do arquivo do log dos erros
	// @param		lineEnd string	Caractere a ser utilizado no fim de cada linha
	// @return		void
	// @note		O arquivo de log é aberto para concatenação
	// @note		Cada linha do log gravado é composta pelas informações : nome do campo
	//				utilizado para enviar o arquivo via POST, tipo MIME do arquivo, tamanho do
	//				arquivo, caminho alvo, nome alvo e erro encontrado
	//!-----------------------------------------------------------------
	function logErrors($logFile, $lineEnd="\n") {
		$errors = '';
		for ($i=0; $i<sizeof($this->uploadHandlers); $i++) {
			if ($this->uploadHandlers[$i]['error'] != '')
				$errors .= $this->uploadHandlers[$i]['name'] . ';' . $this->uploadHandlers[$i]['type'] . ';' . $this->uploadHandlers[$i]['size'] . ';' . $this->uploadHandlers[$i]['save_path'] . ';' . $this->uploadHandlers[$i]['save_name'] . ';' . $this->uploadHandlers[$i]['error'] . $lineEnd;
		}
		if (!empty($errors)) {
			$Mgr =& new FileManager();
			if ($Mgr->open($logFile, FILE_MANAGER_APPEND_BINARY)) {
				$Mgr->write($errors);
				$Mgr->close();
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::upload
	// @desc		Executa o upload de um arquivo ou de todos os arquivos configurados na classe,
	//				retornando o resultado global da operação
	// @access		public
	// @param		i int	Permite realizar upload de apenas um dos handlers já adicionados na classe
	// @return		bool
	// @note		Os erros de upload podem ser obtidos através do método FileUpload::getErrorAt
	//!-----------------------------------------------------------------
	function upload($i=NULL) {
		if (TypeUtils::isInteger($i)) {
			return $this->_uploadFile($i);
		} else {
			$operationResult = TRUE;
			for ($i=0; $i<sizeof($this->uploadHandlers); $i++) {
				$operationResult &= $this->_uploadFile($i);
			}
			return TypeUtils::toBoolean($operationResult);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::_uploadFile
	// @desc		Executa a operação de upload para um determinado
	//				handler (arquivo) a partir de seu índice
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _uploadFile($i) {
		if (!array_key_exists($i, $this->uploadHandlers)) {
			return FALSE;
		} else {
			$fileData =& $this->uploadHandlers[$i];
			if (isset($_FILES[$fileData['field']]) && !empty($_FILES[$fileData['field']]['name'])) {
				$file = $_FILES[$fileData['field']];
				$fileData['uploaded'] = TRUE;
				$fileData['name'] = $file['name'];
				$fileData['type'] = $file['type'];
				$fileData['tmp_name'] = $file['tmp_name'];
				$fileData['size'] = $file['size'];
				// erros reportados pelo PHP
				if (isset($file['error']) && $file['error'] != UPLOAD_ERR_OK) {
					switch ($file['error']) {
						case UPLOAD_ERR_INI_SIZE :
						case UPLOAD_ERR_FORM_SIZE :
							$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_TOO_BIG');
							break;
						case UPLOAD_ERR_PARTIAL :
						case UPLOAD_ERR_NO_FILE :
						case UPLOAD_ERR_NO_TMP_DIR :
							$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_NOT_FOUND');
							break;
						default :
							$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_NOT_FOUND');
							break;
					}
					return FALSE;
				// validação interna da classe FileUpload
				} else {
					if ($this->_checkFile($i)) {
						if (!$this->_moveFile($i)) {
							return FALSE;
						}
						return TRUE;
					} else {
						return FALSE;
					}
				}
			} else {
				$fileData['uploaded'] = FALSE;
				return TRUE;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::_checkFile
	// @desc		Executa checagens em um determinado arquivo, como o nome, o tamanho e a extensão
	// @access		private
	// @param		index int		Índice do handler de upload utilizado
	// @return		bool
	//!-----------------------------------------------------------------
	function _checkFile($index) {
		$fileData =& $this->uploadHandlers[$index];
		if ($fileData['size'] <= 0) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_NOT_FOUND');
			return FALSE;
		}
		else if (!empty($this->allowedFileTypes) && !in_array($fileData['type'], $this->allowedFileTypes)) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_INVALID_TYPE', $fileData['type']);
			return FALSE;
		}
		else if ($fileData['size'] > $this->maxFileSize) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_TOO_BIG');
			return FALSE;
		}
		else if (!is_uploaded_file($fileData['tmp_name'])) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_NOT_FOUND');
			return FALSE;
		}
		else if (ereg("\.+.+\.+", $fileData['name'])){
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_INVALID_NAME', $fileData['name']);
			return FALSE;
		}
		else if (System::isGlobalsOn() && ( isset($_GET[$fileData['name']]) || isset($_POST[$fileData['name']]) || isset($_COOKIE[$fileData['name']]) )) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_INVALID_NAME', $fileData['name']);
			return FALSE;
		}
		else {
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileUpload::_moveFile
	// @desc		Realiza a movimentação do arquivo temporário criado pela operação de upload
	//				para o diretório alvo desejado, armazenando possíveis erros encontrados
	// @access		private
	// @param		index int	 	Índice do handler de upload utilizado
	// @return		bool
	//!-----------------------------------------------------------------
	function _moveFile($index) {
		$fileData =& $this->uploadHandlers[$index];
		$fileData['save_name'] = empty($fileData['save_name']) ? $fileData['name'] : $fileData['save_name'];
		if (!StringUtils::endsWith($fileData['save_path'], '/'))
			$fileData['save_path'] .= '/';
		if (!TypeUtils::isNull($fileData['save_callback'])) {
			$ret = $fileData['save_callback']->invoke($fileData);
			if (!TypeUtils::isArray($ret)) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_UPLOAD_CANT_MOVE');
				return FALSE;
			} else {
				$fileData = array_merge($fileData, $ret);
				return (empty($fileData['error']));
			}
		} elseif (!TypeUtils::isNull($this->SaveCallback)) {
			$ret = $this->SaveCallback->invoke($fileData);
			if (!TypeUtils::isArray($ret)) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_UPLOAD_CANT_MOVE');
				return FALSE;
			} else {
				$fileData = array_merge($fileData, $ret);
				return (empty($fileData['error']));
			}
		} else {
			$fileData['name'] = eregi_replace($this->verboseSigns, '', $fileData['name']);
			if (!is_dir($fileData['save_path'])) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_CANT_FIND_FILE', $fileData['save_path']);
				return FALSE;
			}
			else if (!$this->overwriteFiles && FileSystem::exists($fileData['save_path'] . $fileData['save_name'])) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_UPLOAD_FILE_EXISTS', $fileData['save_path'] . $fileData['save_name']);
				return FALSE;
			}
			else if (!@is_writable($fileData['save_path'])) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $fileData['save_path']);
				return FALSE;
			}
			else if (!@move_uploaded_file($fileData['tmp_name'], $fileData['save_path'] . $fileData['save_name'])) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_UPLOAD_CANT_MOVE');
				return FALSE;
			}
			else {
				if (!is_null($fileData['save_mode']) && !@chmod($fileData['save_path'] . $fileData['save_name'], $fileData['save_mode'])) {
					$fileData['error'] .= PHP2Go::getLangVal('ERR_CANT_CHANGE_MODE', array($fileData['save_mode'], $fileData['save_path'] . $fileData['save_name']));
					return FALSE;
				}
				return TRUE;
			}
		}
	}
}
?>