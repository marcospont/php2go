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
// $Header: /www/cvsroot/php2go/core/util/Statement.class.php,v 1.25 2006/10/26 04:32:49 mpont Exp $
// $Date: 2006/10/26 04:32:49 $

//------------------------------------------------------------------
import('php2go.file.FileSystem');
import('php2go.net.HttpRequest');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Statement
// @desc		Um statement representa uma string que pode conter refer�ncia para
//				vari�veis ou trechos de c�digo PHP. O funcionamento desta classe consiste
//				em identificar as vari�veis declaradas no conte�do da string e oferecer mecanismos
//				de atribui��o de valores �s vari�veis e transforma��o da string original por outra
//				com as vari�veis substitu�das pelos valores a elas atribu�dos
// @package		php2go.util
// @extends		PHP2Go
// @uses		FileSystem
// @uses		HttpRequest
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.25 $
// @note		O formato padr�o de vari�vel �: ~variavel~. Para c�digo PHP, o padr�o � ~#codigo#~. O prefixo "~"
//				e o sufixo "~" podem ser alterados utilizando o m�todo setVariablePattern
// @note		Esta classe � utilizada para resolver valores para as vari�veis declaradas em alguns
//				atributos especiais das declara��es XML de formul�rios e listas paginadas no PHP2Go
//!-----------------------------------------------------------------
class Statement extends PHP2Go
{
	var $source = '';					// @var source string			"" Conte�do original da string
	var $result = '';					// @var result string			"" Resultado final, com substitui��o de trechos de c�digo e vari�veis declaradas
	var $prefix;						// @var prefix string			Prefixo de vari�veis no statement
	var $suffix;						// @var suffix string			Sufixo de vari�veis no statement
	var $variables = array();			// @var variables array			"array()" Conjunto de vari�veis do statement
	var $code = array();				// @var code array				"array()" Conjunto de trechos de c�digo PHP declarados no statement
	var $showUnassigned = FALSE;		// @var showUnassigned bool		"FALSE" Exibir ou n�o vari�veis n�o atribu�das no resultado final
	var $prepared = FALSE;				// @var prepared bool			"FALSE" Indica que existe um statement interpretado armazenado

	//!-----------------------------------------------------------------
	// @function	Statement::Statement
	// @desc		Construtor da classe. O par�metro opcional $source permite
	//				informar o statement a ser utilizado
	// @param		source string	"" Statement a ser utilizado
	// @access		public
	//!-----------------------------------------------------------------
	function Statement($source='') {
		parent::PHP2Go();
		$this->prefix = '~';
		$this->suffix = '~';
		if (!empty($source)) {
			$this->source = $source;
			$this->result = $source;
			$this->_parseStatement();
		}
		PHP2Go::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	Statement::__destruct
	// @desc		Destrutor da classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	Statement::evaluate
	// @desc		M�todo est�tico de avalia��o de um statement utilizando
	//				apenas vari�veis do escopo global, dos objetos de sess�o
	//				e do Registry
	// @access		public
	// @param		value string		C�digo do statement
	// @param		prefix string		"~" Prefixo para vari�veis
	// @param		suffix string		"~" Sufixo para vari�veis
	// @param		showUnassigned bool	"TRUE" Exibir ou n�o vari�veis n�o atribu�das
	// @return		string C�digo resultante
	// @static
	//!-----------------------------------------------------------------
	function evaluate($value, $prefix='~', $suffix='~', $showUnassigned=TRUE) {
		static $Stmt;
		if (!isset($Stmt))
			$Stmt = new Statement();
		$Stmt->setVariablePattern($prefix, $suffix);
		$Stmt->setStatement($value);
		$Stmt->setShowUnassigned($showUnassigned);
		$Stmt->bindVariables(FALSE);
		return $Stmt->getResult();
	}

	//!-----------------------------------------------------------------
	// @function	Statement::getStatement
	// @desc		Retorna o conte�do original do statement
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getStatement() {
		return $this->source;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::displayStatement
	// @desc		Exibe o conte�do original do statement
	// @param		pre bool	"TRUE" Utilizar pr�-formata��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function displayStatement($pre=TRUE) {
		print ($pre ? '<pre>' . $this->source . '</pre>' : $this->source);
	}

	//!-----------------------------------------------------------------
	// @function	Statement::setStatement
	// @desc		Reinicializa o objeto com um novo statement. As informa��es
	//				sobre vari�veis e trechos de c�digo s�o resetadas
	// @param		source string	Novo statement
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setStatement($source) {
		$this->source = $source;
		$this->result = $source;
		$this->code = array();
		$this->variables = array();
		$this->_parseStatement();
	}

	//!-----------------------------------------------------------------
	// @function	Statement::loadFromFile
	// @desc		Define o statement a partir do conte�do de um arquivo
	// @param		fileName string		Caminho do arquivo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadFromFile($fileName) {
		$this->setStatement(FileSystem::getContents($fileName));
	}

	//!-----------------------------------------------------------------
	// @function	Statement::setVariablePattern
	// @desc		Define o padr�o de reconhecimento de vari�veis no statement
	// @param		prefix string	"" Prefixo
	// @param		suffix string	"" Sufixo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setVariablePattern($prefix='', $suffix='') {
		if (!empty($prefix) || !empty($suffix)) {
			$this->prefix = $prefix;
			$this->suffix = $suffix;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Statement::setShowUnassigned
	// @desc		Define a visibilidade de vari�veis sem valor atribu�do
	// @param		setting bool	"TRUE" Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setShowUnassigned($setting=TRUE) {
		$this->showUnassigned = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	Statement::getDefinedVars
	// @desc		Retorna a lista de vari�veis declaradas no statement
	// @access		public
	// @return		array Vetor de vari�veis
	//!-----------------------------------------------------------------
	function getDefinedVars() {
		return array_keys($this->variables);
	}

	//!-----------------------------------------------------------------
	// @function	Statement::getVariablesCount
	// @desc		Retorna o total de vari�veis declaradas
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getVariablesCount() {
		return sizeof($this->variables);
	}

	//!-----------------------------------------------------------------
	// @function	Statement::isEmpty
	// @desc		Retorna TRUE se o statement n�o possui vari�veis ou trechos de c�digo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isEmpty() {
		return (empty($this->variables) && empty($this->code));
	}

	//!-----------------------------------------------------------------
	// @function	Statement::isPrepared
	// @desc		Retorna TRUE se o objeto possui um statement configurado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isPrepared() {
		return $this->prepared;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::isDefined
	// @desc		Verifica se uma determinada vari�vel est� declarada no statement
	// @param		variable string		Nome da vari�vel
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isDefined($variable) {
		return (array_key_exists($variable, $this->variables));
	}

	//!-----------------------------------------------------------------
	// @function	Statement::isBound
	// @desc		Verifica se uma vari�vel possui valor atribu�do
	// @param		variable string		Nome da vari�vel
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isBound($variable) {
		return (isset($this->variables[$variable]) && !TypeUtils::isNull($this->variables[$variable]['value'], TRUE));
	}

	//!-----------------------------------------------------------------
	// @function	Statement::isAllBound
	// @desc		Verifica se todas as vari�veis declaradas no statement possuem valor atribu�do
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isAllBound() {
		if (!$this->prepared)
			return FALSE;
		reset($this->variables);
		foreach ($this->variables as $variable) {
			if ($variable['value'] === NULL)
				return FALSE;
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::getVariableValue
	// @desc		Busca o valor atribu�do a uma vari�vel
	// @param		variable string		Nome da vari�vel
	// @note		Retorna NULL se a vari�vel n�o possui valor ou n�o foi declarada
	// @access		public
	// @return		mixed Valor da vari�vel
	//!-----------------------------------------------------------------
	function getVariableValue($variable) {
		return (array_key_exists($variable, $this->variables) ? $this->variables[$variable]['value'] : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	Statement::bindByName
	// @desc		Define valor para uma vari�vel
	// @param		variable string		Nome da vari�vel
	// @param		value mixed			Valor para a vari�vel
	// @param		quote bool			"TRUE" Inserir double quotes se o valor for uma string
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function bindByName($variable, $value, $quote=TRUE) {
		if (array_key_exists($variable, $this->variables)) {
			if ($quote && TypeUtils::isString($value))
				$value = "\"" . $value . "\"";
			$this->variables[$variable]['value'] = $value;
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::bindFromRequest
	// @desc		Este m�todo realiza uma tentativa de atribuir valor
	//				para uma determinada vari�vel a partir do escopo global:
	//				requisi��o (GET, POST, COOKIE), vari�veis de ambiente,
	//				sess�o (b�sica ou objetos) e o objeto global Registry
	// @param		name string			Nome da vari�vel
	// @param		quote bool			"TRUE" Inserir quotes se o valor atribu�do for uma string
	// @param		searchOrder string	"NULL" Ordem de pesquisa no escopo global
	// @note		O padr�o da ordem de pesquisa � "ROEGPCS"
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function bindFromRequest($name, $quote=TRUE, $searchOrder=NULL) {
		if (empty($searchOrder))
			$searchOrder = 'ROEGPCS';
		if (isset($this->variables[$name])) {
			$variable = $this->variables[$name];
			if ($variable['array'] == TRUE) {
				$base = HttpRequest::getVar($variable['base'], 'all', $searchOrder);
				if (is_array($base) && array_key_exists($variable['key'], $base))
					$value = $base[$variable['key']];
				else
					$value = NULL;
			} else {
				$value = HttpRequest::getVar($name, 'all', $searchOrder);
			}
			if (!TypeUtils::isNull($value, TRUE)) {
				$this->variables[$name]['value'] = ($quote && TypeUtils::isString($value) ? "\"" . $value . "\"" : $value);
				return TRUE;
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::appendByName
	// @desc		Adiciona valor a uma vari�vel
	// @param		variable string		Nome da vari�vel
	// @param		value mixed			Valor para a vari�vel
	// @param		quote bool			"TRUE" Inserir double quotes se o valor for uma string
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function appendByName($variable, $value, $quote=TRUE) {
		if (array_key_exists($variable, $this->variables)) {
			if ($quote && TypeUtils::isString($value))
				$value = "\"" . $value . "\"";
			$this->variables[$variable]['value'] .= $value;
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::bindVariables
	// @desc		Este m�todo pesquisa no escopo global buscando definir um
	//				valor para as vari�veis do statement. Os superglobals GET,
	//				POST, COOKIE, SESSION e ENV mais o objeto global Registry e os
	//				objetos de sess�o s�o os locais utilizados para a pesquisa
	// @param		quote bool			"TRUE" Inserir quotes se os valores encontrados forem strings
	// @param		searchOrder string	"ROEGPCS" Ordem de pesquisa
	// @param		replace bool		"TRUE" Substituir o valor das vari�veis que j� possuem valor atribu�do
	// @note		R-Registry, O-objetos de sess�o, E-environment, G-get, P-post, C-cookie, S-session
	// @return		int Quantidade de vari�veis atribu�das automaticamente
	// @access		public
	//!-----------------------------------------------------------------
	function bindVariables($quote=TRUE, $searchOrder='ROEGPCS', $replace=TRUE) {
		$affected = 0;
		reset($this->variables);
		foreach ($this->variables as $name => $variable) {
			if ($variable['value'] === NULL || $replace) {
				if ($this->bindFromRequest($name, $quote, $searchOrder))
					$affected++;
			}
		}
		return $affected;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::debugVariables
	// @desc		Monta e exibe o debug das vari�veis definidas/amarradas
	//				no statement atual. Pode ser utilizado ap�s a fun��o isAllBound,
	//				para verificar vari�veis n�o amarradas
	// @param		pre bool	"TRUE" Utilizar pr�-formata��o
	// @return		string Depura��o das vari�veis do statement
	// @access		public
	//!-----------------------------------------------------------------
	function debugVariables($pre=TRUE) {
		$str = '';
		reset($this->variables);
		foreach($this->variables as $name => $variable) {
			if ($variable['value'] === NULL)
				$str .= "<b>Variable:</b> {$name} => <b>*NOT BOUND*</b><br>";
			else
				$str .= "<b>Variable:</b> {$name} => <b>{$variable['value']}</b><br>";
		}
		print ($pre ? '<pre>' . $str . '</pre>' : $str);
	}

	//!-----------------------------------------------------------------
	// @function	Statement::getResult
	// @desc		Monta o resultado do statement, baseado nos valores atribu�dos �s vari�veis
	//				e aplicando o resultado dos trechos de c�digo PHP declarados
	// @return		string Resultado do statement
	// @access		public
	//!-----------------------------------------------------------------
	function getResult() {
		$this->result = $this->source;
		// substitui��o de trechos de c�digo ([prefixo]#codigo#[sufixo])
		reset($this->code);
		foreach ($this->code as $code) {
			ob_start();
			eval("\$value = " . substr($code, 1, -1) . ";");
			$error = ob_get_clean();
			if (!empty($error))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_STATEMENT_EVAL', substr($code, 1, -1)), E_USER_ERROR, __FILE__, __LINE__);
			$pattern = preg_quote("{$this->prefix}{$code}{$this->suffix}", '/');
			$this->result = preg_replace("/{$pattern}/", $value, $this->result, -1);
		}
		// substitui��o de vari�veis por seus valores
		// atribui string vazia a vari�veis sem valor se a propriedade showUnassigned for igual a FALSE
		reset($this->variables);
		foreach ($this->variables as $name => $variable) {
			if ($variable['array'] == TRUE) {
				$pattern = $this->prefix . $variable['base'] . "\[\'?" . preg_quote($variable['key'] , '/') . "\'?\]" . $this->suffix;
				if ($variable['value'] !== NULL)
					$this->result = preg_replace("/{$pattern}/", $variable['value'], $this->result, -1);
				elseif (!$this->showUnassigned)
					$this->result = preg_replace("/{$pattern}/", '', $this->result, -1);
			} else {
				$pattern = preg_quote("{$this->prefix}{$name}{$this->suffix}", '/');
				if ($variable['value'] !== NULL)
					$this->result = preg_replace("/{$pattern}/", $variable['value'], $this->result, -1);
				elseif (!$this->showUnassigned)
					$this->result = preg_replace("/{$pattern}/", '', $this->result, -1);
			}
		}
		return $this->result;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::displayResult
	// @desc		Imprime o resultado do processamento do statement
	// @param		pre bool	"TRUE" Utilizar pr�-formata��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function displayResult($pre=TRUE) {
		$result = $this->getResult();
		print ($pre ? '<pre>' . $result . '</pre>' : $result);
	}

	//!-----------------------------------------------------------------
	// @function	Statement::_parseStatement
	// @desc		Interpreta o conte�do do statement, buscando por declara��es
	//				de vari�veis de trechos de c�digo PHP
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _parseStatement() {
		$this->prepared = TRUE;
		$matches = array();
		$pattern = "/{$this->prefix}(#[^#]+#|[[:alnum:]_\:\[\'\]]+){$this->suffix}/";
		preg_match_all($pattern, $this->source, $matches, PREG_PATTERN_ORDER);
		if (!empty($matches[1])) {
			foreach ($matches[1] as $match) {
				if ($match[0] == '#' && $match{strlen($match)-1} == '#') {
					if (!in_array($match, $this->code))
						$this->code[] = $match;
				} else {
					if (!in_array($match, $this->variables)) {
						if (preg_match("/([[:alnum:]_]+)\[\'?([[:alnum:]_]+)\'?\]/", $match, $parts)) {
							$variable = array(
								'array' => TRUE,
								'base' => $parts[1],
								'key' => $parts[2],
								'value' => NULL
							);
						} else {
							$variable = array(
								'array' => FALSE,
								'base' => NULL,
								'key' => NULL,
								'value' => NULL
							);
						}
						$this->variables[$match] = $variable;
					}
				}
			}
		}
	}
}
?>