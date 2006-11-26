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
// $Header: /www/cvsroot/php2go/core/LanguageBase.class.php,v 1.15 2006/10/26 04:30:43 mpont Exp $
// $Date: 2006/10/26 04:30:43 $

//!-----------------------------------------------------------------
// @class		LanguageBase
// @desc		A classe LanguageBase armazena as tabelas de linguagens
//				utilizadas no framework e nos sistemas executados sobre ele
// @author		Marcos Pont
// @version		$Revision: 1.15 $
// @note		A definição de linguagem da configuração do framework carrega
//				para a classe o vetor de linguagem correspondente. Adicionalmente,
//				o usuário pode adicionar suas próprias entradas de linguagem respeitando
//				a mesma parametrização definida na configuração (config[LANGUAGE])
// @note		Exemplo de uso:<br>
//				<pre>
//
//				... no script de inicialização do sistema
//
//				$Lang =& LanguageBase::getInstance();
//				$Lang->loadLanguageTableByValue(array('LANGUAGE_KEY'=>'value'), 'MY_DOMAIN');
//
//				... em um outro arquivo ou script pertencente ao sistema
//
//				$Lang =& LanguageBase::getInstance();
//				$value = $Lang->getLanguageValue('MY_DOMAIN.LANGUAGE_KEY', $params);
//
//				</pre>
//!-----------------------------------------------------------------
class LanguageBase
{	
	var $languageBase;		// @var languageBase array	Armazena as tabelas de linguagem ativas

	//!-----------------------------------------------------------------
	// @function	LanguageBase::LanguageBase
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function LanguageBase() {
		$this->languageBase = array();
	}
	
	//!-----------------------------------------------------------------
	// @function	LanguageBase::&getInstance
	// @desc		Retorna uma instância única (singleton) da classe LanguageBase
	// @access		public
	// @return		LanguageBase object Instância da classe LanguageBase
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new LanguageBase();
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	LanguageBase::clearLanguageBase
	// @desc		Remove todas as entradas de linguagem carregadas
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clearLanguageBase() {
		$this->languageBase = array();
	}
	
	//!-----------------------------------------------------------------
	// @function	LanguageBase::loadLanguageTableByValue
	// @desc		Carrega entrada(s) de linguagem a partir de uma variável
	// @access		public
	// @param		languageTable array	Vetor de entradas de linguagem
	// @param		domain string		Nome de domínio	
	// @return		void
	//!-----------------------------------------------------------------
	function loadLanguageTableByValue($languageTable, $domain) {
		$languageTable = (array)$languageTable;
		if (isset($this->languageBase[$domain])) {
			$temp = $this->languageBase[$domain];
			$this->languageBase[$domain] = array_merge($temp, $languageTable);
		} else
			$this->languageBase[$domain] = $languageTable;
	}
	
	//!-----------------------------------------------------------------
	// @function	LanguageBase::loadLanguageTableByFile
	// @desc		Carrega entradas de linguagem a partir de um arquivo
	// @access		public
	// @param		languageFile string	Caminho completo do arquivo de linguagem
	// @param		domain string		Nome de domínio
	// @return		void
	//!-----------------------------------------------------------------
	function loadLanguageTableByFile($languageFile, $domain) {
		$this->loadLanguageTableByValue(includeFile($languageFile, TRUE), $domain);
	}
	
	//!-----------------------------------------------------------------
	// @function	LanguageBase::getLanguageValue
	// @desc		Busca uma determinada chave na tabela de linguagem
	// @access		public
	// @param		keyName string	Nome da chave
	// @return		mixed Valor da chave ou FALSE se não encontrada
	//!-----------------------------------------------------------------
	function getLanguageValue($key, $params=NULL) {
		$key = trim($key);		
		if (($pos = strpos($key, ':')) !== FALSE) {
			$domain = substr($key, 0, $pos);
			$key = substr($key, $pos+1);
		} else {
			$domain = 'PHP2GO';
		}
		// carrega a tabela de linguagem, se for um domínio de usuário ainda não carregado
		if ($domain != 'PHP2GO' && !isset($this->languageBase[$domain]))
			$this->_loadLanguageDomain($domain);
		// executa o método de busca da chave (permite múltiplas dimensões)
		$value = (strpos($key, '.') !== FALSE ? findArrayPath(@$this->languageBase[$domain], $key) : @$this->languageBase[$domain][$key]);
		if ($value) {
			if ($params !== NULL)
				return (is_array($params) ? vsprintf($value, $params) : sprintf($value, $params));
			else
				return $value;
		}
		return NULL;
	}	
	
	//!-----------------------------------------------------------------
	// @function	LanguageBase::_loadLanguageDomain
	// @desc		Carrega a tabela de linguagem para um domínio de mensagens
	// @param		domain string	Nome do domínio
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _loadLanguageDomain($domain) {
		$Conf =& Conf::getInstance();
		$code = $Conf->getConfig('LANGUAGE_CODE');
		$path = $Conf->getConfig('LANGUAGE.MESSAGES_PATH');
		if (!empty($path)) {
			$path = rtrim($path, '/\\') . '/';
			$filename = $path . $code . '/' . $domain . '.php';
			if (file_exists($filename)) {
				$table = includeFile($filename, TRUE);
				if (is_array($table)) {
					$this->loadLanguageTableByValue($table, $domain);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_LANGDOMAIN_FILE', array($domain, $code)), E_USER_ERROR, __FILE__, __LINE__);
				}
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_LANGDOMAIN_FILE', array($domain, $code)), E_USER_ERROR, __FILE__, __LINE__);
			}
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CONFIG_ENTRY_NOT_FOUND', 'LANGUAGE/MESSAGES_PATH'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
}
?>