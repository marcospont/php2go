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
// $Header: /www/cvsroot/php2go/core/ClassLoader.class.php,v 1.5 2006/10/26 04:24:59 mpont Exp $
// $Date: 2006/10/26 04:24:59 $

//!-----------------------------------------------------------------
// @class		ClassLoader
// @desc		O PHP2Go utiliza um padrão de caminho para classes
//				e arquivos utilizando o caractere "." como separador,
//				seguindo o padrão adotado por outras linguagens e tecnologias.
//				Esta classe é responsável por traduzir estes caminhos
//				em caminhos reais para diretórios, arquivos ou classes,
//				para que o PHP possa interpretá-los
// @note		Em caso de sucesso, cada caminho separado por pontos é incluído
//				em uma cache, para que os mesmos arquivos ou diretórios não
//				sejam processados duas ou mais vezes
// @note		Cada diretório processado é incluído no include_path do
//				PHP, para melhorar a performance dos próximos acessos
//				ao mesmo diretório
// @note		No PHP4, arquivos e classes serão interpretados no
//				momento da importação. No PHP5, arquivos serão interpretados
//				no momento da importação e classes serão importadas sob
//				demanda, através da função __autoload
// @author		Marcos Pont
// @version		$Revision: 1.5 $
//!-----------------------------------------------------------------
class ClassLoader
{
	var $importCache = array();			// @var importCache array		"array()" Cache de caminhos separados por pontos já processados anteriormente
	var $importClassCache = array();	// @var importClassCache array	"array()" Cache de classes requisitadas em PHP5, para que a função __autoload conheça a extensão de arquivo correta a ser utilizada

	//!-----------------------------------------------------------------
	// @function	ClassLoader::ClassLoader
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function ClassLoader() {
	}

	//!-----------------------------------------------------------------
	// @function	ClassLoader::&getInstance
	// @desc		Retorna o singleton da classe ClassLoader
	// @return		ClassLoader object
	// @access		public
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new ClassLoader();
		return $instance;
	}

	//!-----------------------------------------------------------------
	// @function	ClassLoader::importPath
	// @desc		Importa o(s) arquivo(s) correspondentes ao caminho fornecido
	// @param		path string			Caminho para a(s) classe(s) ou arquivo(s)
	// @param		extension string	"class.php" Extensão do arquivo
	// @param		isClass bool		"TRUE" O caminho representa uma classe ou conjunto de classes
	// @note		Traduz um caminho para uma classe, arquivo ou conjunto
	//				de arquivos separado por pontos para um caminho no
	//				sistema de arquivos, e registra este caminho no
	//				include_path do PHP. Exemplo: "php2go.base.Document"
	// @note		Para utilizar classes ou arquivos que não pertencem ao PHP2Go,
	//				crie a chave "INCLUDE_PATH" no vetor de configurações $P2G_USER_CFG,
	//				contendo um vetor associativo chave => caminho.<br><br>
	//				Exemplo: para incluir "/www/project/classes/MyClass.class.php",
	//				inclua "project => '/www/project/'" na chave "INCLUDE_PATH" e
	//				execute import('project.classes.MyClass');
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function importPath($path, $extension='class.php', $isClass=TRUE) {
		if (isset($this->importCache[$path]))
			return TRUE;
		$Lang =& LanguageBase::getInstance();
		if ($translated = $this->_translateDotPath($path)) {
			$this->_registerIncludePath($translated['path']);
			$result = ($isClass && IS_PHP5 ? TRUE : ($translated['file'] == '*' ? $this->loadDirectory($translated['path']) : $this->loadFile($translated['file'] . '.' . $extension)));
			if ($result) {
				$this->importCache[$path] = TRUE;
				if ($translated['file'] != '*')
					$this->importClassCache[$translated['file']] = $translated['file'] . '.' . $extension;
				return TRUE;
			}
		}
		trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_MODULE'), $path), E_USER_ERROR);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	ClassLoader::loadFile
	// @desc		Inclui um arquivo a partir de seu caminho ou nome
	// @param		filePath string		Caminho do arquivo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function loadFile($filePath) {
		return ($this->_isReadable($filePath) ? (include_once($filePath)) : FALSE);
	}

	//!-----------------------------------------------------------------
	// @function	ClassLoader::loadDirectory
	// @desc		Inclui todo o conteúdo de um determinado diretório
	// @param		directoryPath string	Caminho do diretório
	// @access		protected
	// @return		bool
	//!-----------------------------------------------------------------
	function loadDirectory($directoryPath) {
		$Lang =& LanguageBase::getInstance();
		$directoryPath = rtrim($directoryPath, "\\/");
		$handle = @dir($directoryPath);
		if ($handle) {
			while ($file = $handle->read()) {
				if ($file == '.' || $file == '..' || is_dir($directoryPath . PHP2GO_DIRECTORY_SEPARATOR . $file))
					continue;
				if (!include_once($directoryPath . PHP2GO_DIRECTORY_SEPARATOR . $file)) {
					trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_DIR_MODULE'), $file, $directoryPath), E_USER_ERROR);
					return FALSE;
				}
			}
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	ClassLoader::_translateDotPath
	// @desc		Traduz um caminho com pontos como separadores, a fim
	//				de determinar o caminho real no sistema de arquivos
	// @note		O primeiro elemento do caminho pode ser qualquer um
	//				dos valores definidos na entrada de configuração
	//				"INCLUDE_PATH"
	// @param		dotPath string	Caminho separado por pontos
	// @return		mixed Informações do caminho processadas
	// @access		private
	//!-----------------------------------------------------------------
	function _translateDotPath($dotPath) {
		$matches = array();
		if (preg_match("/^([^\.]+)\.(.*\.)?([^\.]+)/i", $dotPath, $matches)) {
			if ($matches[1] == PHP2GO_INCLUDE_KEY) {
				$basePath = PHP2GO_ROOT . 'core/';
			} else {
				$Conf =& Conf::getInstance();
				$includePaths = $Conf->getConfig('INCLUDE_PATH');
				if (array_key_exists($matches[1], (array)$includePaths)) {
					$basePath = $includePaths[$matches[1]];
				} else {
					return FALSE;
				}
			}
			$basePath .= ($matches[2] ? strtr($matches[2], '.', PHP2GO_DIRECTORY_SEPARATOR) : '');
			return array(
				'path' => $basePath,
				'file' => $matches[3]
			);
		} elseif ($dotPath != '*') {
			return array(
				'path' => getcwd(),
				'file' => $dotPath
			);
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	ClassLoader::_isReadable
	// @desc		Verifica se um determinado arquivo pode ser lido,
	//				utilizando o include_path como base de pesquisa
	// @param		filePath string		Caminho do arquivo
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _isReadable($filePath) {
		$handle = @fopen($filePath, 'r', TRUE);
		if (is_resource($handle)) {
			fclose($handle);
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	ClassLoader::_registerIncludePath
	// @desc		Registra um diretório no include_path do PHP
	// @param		includePath string	Diretório a ser incluído
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _registerIncludePath($includePath) {
		$ps = PATH_SEPARATOR;
		$includePath = strtr($includePath, '\\', '/');
		$curIncPath = ini_get('include_path');
		if (!preg_match("~{$includePath}(?:{$ps}|$)~", $curIncPath))
			ini_set('include_path', $curIncPath . $ps . $includePath);
	}
}
?>