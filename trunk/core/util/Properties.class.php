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
// $Header: /www/cvsroot/php2go/core/util/Properties.class.php,v 1.6 2006/03/15 23:02:42 mpont Exp $
// $Date: 2006/03/15 23:02:42 $

//------------------------------------------------------------------
import('php2go.file.FileManager');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Properties
// @desc		Esta classe tem como prop�sito a interpreta��o e a gera��o de
//				arquivos contendo dados de configura��o no formato .ini, recuperando,
//				alterando e criando se��es e/ou chaves de configura��o
// @package		php2go.util
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class Properties extends PHP2Go
{
	var $filename;					// @var filename string			Nome do arquivo
	var $table;						// @var table array				Tabela de armazenamento dos dados de configura��o
	var $processSections;			// @var processSections bool	Processamento (leitura/escrita) de se��es habilitado ou desabilitado
	var $caseSensitive;				// @var caseSensitive bool		Leitura/escrita de chaves e se��es sens�vel ao caso
	var $currentSection = NULL;		// @var currentSection string	"NULL" Se��o ativa (�ltima carregada ou criada)
	
	//!-----------------------------------------------------------------
	// @function	Properties::Properties
	// @desc		Construtor da classe
	// @param		filename string			Caminho do arquivo a ser lido ou criado
	// @param		processSections bool	"FALSE" Processar se��es para leitura/escrita
	// @param		caseSensitive bool		"FALSE" Leitura/escrita de chaves e se��es sens�vel ao caso
	// @access		public
	//!-----------------------------------------------------------------
	function Properties($filename, $processSections=FALSE, $caseSensitive=FALSE) {
		parent::PHP2Go();
		$this->filename = $filename;
		$this->processSections = TypeUtils::toBoolean($processSections);
		$this->caseSensitive = TypeUtils::toBoolean($caseSensitive);
		if (FileSystem::exists($filename)) {
			$this->_loadFile();
		} else {
			$this->table = array();
			$this->_createFile();
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::getFirstSection
	// @desc		Move o ponteiro de se��es para o in�cio e retorna o nome
	//				da primeira se��o do arquivo
	// @note		Se o processamento de se��es estiver desabilitado, retorna NULL
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFirstSection() {
		if ($this->processSections) {
			reset($this->table);
			return key($this->table);
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::fetchFirstSection
	// @desc		Retorna o conte�do (chaves e valores) da primeira se��o do arquivo
	// @return		mixed Conte�do da se��o
	// @access		public
	//!-----------------------------------------------------------------
	function fetchFirstSection() {
		return $this->getSection($this->getFirstSection());
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::getCurrentSection
	// @desc		Retorna o nome da se��o ativa
	// @return		string Nome da se��o
	// @access		public
	//!-----------------------------------------------------------------
	function getCurrentSection() {
		return $this->currentSection;
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::fetchCurrentSection
	// @desc		Retorna o conte�do da se��o ativa
	// @return		mixed Conte�do da se��o
	// @access		public
	//!-----------------------------------------------------------------
	function fetchCurrentSection() {
		return $this->getSection($this->currentSection);
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::getNextSection
	// @desc		Retorna o nome da se��o ativa, e move o ponteiro da lista
	//				de se��es uma posi��o � frente
	// @note		Este m�todo retornar� FALSE quando o apontador de se��es chegar ao final da lista
	// @return		string Nome da se��o ativa
	// @access		public
	//!-----------------------------------------------------------------
	function getNextSection() {
		if ($this->processSections) {
			if (key($this->table)) {
				$this->currentSection = key($this->table);
				next($this->table);
				return $this->currentSection;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::fetchNextSection
	// @desc		Retorna o conte�do da se��o ativa, e move o ponteiro da lista
	//				de se��es uma posi��o � frente
	// @note		Este m�todo retornar� FALSE quando o apontador de se��es chegar ao final da lista	
	// @return		mixed Conte�do da se��o ativa
	// @access		public
	//!-----------------------------------------------------------------
	function fetchNextSection() {
		return $this->getSection($this->getNextSection());
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::hasSection
	// @desc		Verifica a exist�ncia de uma se��o na tabela de configura��es
	// @param		section string		Nome da se��o
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasSection($section) {
		return ($this->processSections ? ($this->table[$section]) : FALSE);
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::getSection
	// @desc		Retorna o conte�do de uma se��o de configura��o
	// @param		section string		Nome da se��o
	// @return		Valor da se��o ou FALSE se ela n�o existir
	// @access		public
	//!-----------------------------------------------------------------
	function getSection($section) {
		if (!empty($section)) {
			if ($this->processSections) {
				if (!$this->caseSensitive)
					$section = strtoupper($section);
				return (isset($this->table[$section]) ? $this->table[$section] : FALSE);
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::getValue
	// @desc		Busca o valor de uma chave de configura��o
	// @param		key string		Nome da chave
	// @param		fallback mixed	"NULL" Valor a ser retornado se a chave n�o for encontrada
	// @param		section string	"NULL" Nome da se��o
	// @note		Se o processamento de se��es estiver habilitado e o par�metro $section
	//				for omitido, a classe utilizar� a �ltima se��o carregada
	// @return		mixed Valor da chave (ou valor de $fallback)
	//!-----------------------------------------------------------------
	function getValue($key, $fallback=NULL, $section=NULL) {
		if (!$this->caseSensitive) {
			$key = strtoupper($key);
			$section = strtoupper((string)$section);
		}
		if ($this->processSections) {
			$section = StringUtils::ifEmpty($section, $this->currentSection);
			if (!empty($section))
				return (isset($this->table[$section][$key]) ? $this->table[$section][$key] : $fallback);
			return $fallback;
		} else {
			return (isset($this->table[$key]) ? $this->table[$key] : $fallback);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::matchValue
	// @desc		Verifica se o valor de uma chave se enquadra em um determinado padr�o
	// @param		key string		Nome da chave
	// @param		pattern string	Padr�o, no formato utilizado pela fun��o preg_match
	// @param		section string	"NULL" Nome da se��o
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------	
	function matchValue($key, $pattern, $section=NULL) {
		$tmp = $this->getValue($key, NULL, $section);
		if (!TypeUtils::isNull($tmp, TRUE))
			return preg_match($pattern, $tmp);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::getArray
	// @desc		L� uma chave de configura��o na forma de um array, usando um separador fornecido
	// @param		key string			Nome da chave
	// @param		separator string	"|" Separador de valores
	// @param		fallback array		"array()" Valor a ser retornado caso a chave n�o exista
	// @param		section string		"NULL" Nome da se��o
	// @return		mixed Valor da chave (ou valor de $fallback)
	// @access		public
	//!-----------------------------------------------------------------
	function getArray($key, $separator='|', $fallback=array(), $section=NULL) {
		$tmp = $this->getValue($key, $fallback, $section);
		if ($tmp != $fallback)
			return explode($separator, (string)$tmp);
		return $fallback;
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::getBool
	// @desc		L� uma chave de um valor booleano
	// @param		key string		Nome da chave
	// @param		fallback mixed	"FALSE" Valor de retorno caso a chave n�o exista
	// @param		trueValue mixed	"1" Valor TRUE para executar a compara��o
	// @param		section string	"NULL" Nome da se��o
	// @return		bool Valor da chave (ou valor de $fallback)
	// @access		public
	//!-----------------------------------------------------------------
	function getBool($key, $fallback=FALSE, $trueValue='1', $section=NULL) {
		$tmp = $this->getValue($key, $fallback, $section);
		if ($tmp != $fallback)
			return ($tmp == $trueValue);
		return $fallback;
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::addSection
	// @desc		Adiciona uma se��o � tabela de configura��o
	// @param		section string	Nome da se��o
	// @param		overwrite bool	"FALSE" Sobrescrever, se existente
	// @param		entries mixed	"array()" Permite inicializar a sess�o com um valor ou um array de valores
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addSection($section, $overwrite=FALSE, $entries=array()) {
		if ($this->processSections) {
			if ($overwrite || !isset($this->table[$section]))
				$this->table[$section] = (array)$entries;
			$this->currentSection = $section;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::setValue
	// @desc		Define o valor de uma chave de configura��o
	// @param		key string		Nome da chave
	// @param		value mixed		Valor da chave
	// @param		section string	"NULL" Nome da se��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setValue($key, $value, $section=NULL) {
		if (!$this->caseSensitive) {
			$key = strtoupper($key);
			$section = strtoupper($section);
		}
		// cria chave de um se��o		
		if ($this->processSections) {
			$section = StringUtils::ifEmpty($section, $this->currentSection);
			if (!empty($section))
				$this->table[$section][$key] = (string)$value;
		}
		// cria uma nova chave no map simples
		else {
			$this->table[$key] = (string)$value;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::setArray
	// @desc		Define um array como o valor de uma chave, definindo o caractere
	//				a ser usado para unir os valores do array
	// @param		key string		Nome da chave
	// @param		value array		Array de valores
	// @param		glue string		"|" String para unir os valores do vetor
	// @param		section string	"NULL" Nome da se��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setArray($key, $value, $glue='|', $section=NULL) {
		$value = implode($glue, $value);
		$this->setValue($key, $value, $section);
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::setBool
	// @desc		Converte um valor booleano em uma representa��o string e
	//				usa o resultado para definir o valor de uma chave de configura��o
	// @param		key string		Nome da chave
	// @param		value bool		Valor da chave
	// @param		section string	"NULL" Nome da se��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setBool($key, $value, $section=NULL) {
		$value = ($value ? "1" : "0");
		$this->setValue($key, $value, $section);
	}

	//!-----------------------------------------------------------------
	// @function	Properties::addComment
	// @desc		Adiciona um coment�rio na tabela de configura��o
	// @param		comment string	Coment�rio
	// @param		section string	"NULL" Nome da se��o onde o coment�rio deve ser adicionado
	// @note		Os coment�rios s�o sempre adicionados ao final das se��es, ou ao final
	//				da tabela, se o processamento de se��es estiver desabilitado na classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addComment($comment, $section=NULL) {
		if (!$this->caseSensitive)
			$section = strtoupper((string)$section);
		// cria um coment�rio junto com as chaves da se��o
		if ($this->processSections) {
			$section = StringUtils::ifEmpty($section, $this->currentSection);
			if (!empty($section)) {
				$size = (isset($this->table[$section]) ? sizeof($this->table[$section]) : 0);
				$this->table[$section][";{$size}"] = $comment;
			}
		// cria um coment�rio no final da tabela
		} else {
			$size = sizeof($this->table);
			$this->table[";{$size}"] = $comment;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::getContent
	// @desc		Monta e retorna o conte�do da tabela de configura��o
	// @param		lineEnd string	"\n" Quebra de linha
	// @param		indent string	"" Indenta��o
	// @return		string Conte�do da tabela formatado para exibi��o	
	// @access		public
	//!-----------------------------------------------------------------
	function getContent($lineEnd="\n", $indent='') {
		$buffer = '';
		if ($this->processSections) {
			foreach ($this->table as $section => $keys) {
				if (!$this->caseSensitive)
					$section = strtoupper($section);
				$buffer .= "[{$section}]" . $lineEnd;
				foreach ($keys as $key => $value) { 
					if ($key[0] == ';') {
						$buffer .= $indent . '; ' . $value . $lineEnd;
					} else {
						if (!$this->caseSensitive)
							$key = strtoupper($key);
						$buffer .= $indent . "$key = \"$value\"" . $lineEnd;
					}
				}
				$buffer .=  $lineEnd;
			}
		} else {
			foreach ($this->table as $key => $value) {
				if ($key[0] == ';')
					$buffer .= $indent . '; ' . $value . $lineEnd;
				else
					$buffer .= $indent . "$key = \"$value\"" . $lineEnd;
			}
		}
		return $buffer;
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::reset
	// @desc		Remonta a tabela de configura��o a partir do conte�do do arquivo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function reset() {
		$this->_loadFile(TRUE);
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::save
	// @desc		Salva para o arquivo a tabela de configura��es
	// @param		lineEnd string		"\n" Quebra de linha
	// @param		indent string		"" Indenta��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function save($lineEnd="\n", $indent='') {
		$Mgr = new FileManager();
		$Mgr->throwErrors = FALSE;
		if ($Mgr->open($this->filename, FILE_MANAGER_WRITE_BINARY, LOCK_EX)) {
			$Mgr->write($this->getContent($lineEnd, $indent));
			$Mgr->unlock($this->filename, FILE_MANAGER_WRITE_BINARY);
			$Mgr->close();
			return TRUE;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $file), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::_createFile
	// @desc		Cria o arquivo de configura��es
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _createFile() {
		$fp = @fopen($this->filename, 'w');
		if ($fp === FALSE)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $this->filename), E_USER_ERROR, __FILE__, __LINE__);
		ftruncate($fp, 0);			
		fclose($fp);
	}
	
	//!-----------------------------------------------------------------
	// @function	Properties::_loadFile
	// @desc		Cria a tabela de configura��es a partir do conte�do do arquivo,
	//				utilizando a fun��o parse_ini_file do PHP. Se a classe
	//				estiver usando processamento insens�vel ao caso, todas as chaves
	//				e nomes de se��es ser�o convertidos para letras mai�sculas
	// @param		force bool		"FALSE" For�ar remontagem da tabela
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _loadFile($force=FALSE) {
		if (!isset($this->table) || $force) {
			$tmp = @parse_ini_file($this->filename, $this->processSections);
			if ($tmp !== FALSE) {
				if ($this->processSections) {
					foreach ($tmp as $section => $values) {
						if (!$this->caseSensitive)
							$this->table[strtoupper($section)] = (TypeUtils::isArray($values) ? array_change_key_case($values, CASE_UPPER) : $values);
						else
							$this->table[$section] = $values;
					}
				} else {
					if (!$this->caseSensitive)
						$this->table = array_change_key_case($tmp, CASE_UPPER);
					else
						$this->table = $tmp;
				}
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_PROPERTIES_FILE', $this->filename), E_USER_ERROR, __FILE__, __LINE__);
			}
		}
	}
}
?>