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
// $Header: /www/cvsroot/php2go/core/gui/Menu.class.php,v 1.20 2006/10/26 04:39:22 mpont Exp $
// $Date: 2006/10/26 04:39:22 $

//------------------------------------------------------------------
import('php2go.util.Statement');
import('php2go.xml.XmlDocument');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class 		Menu
// @desc 		A classe Menu constrói estruturas de árvore para a
//				montagem de menus a partir de consultas SQL a banco
//				de dados ou a partir de uma especificação XML
// @package		php2go.gui
// @extends 	Component
// @uses 		Db
// @uses		ADORecordSet
// @uses 		Statement
// @uses		XmlDocument
// @author 		Marcos Pont
// @version		$Revision: 1.20 $
//!-----------------------------------------------------------------
class Menu extends Component
{
	var $name; 				// @var name string						Nome do menu, gerado automaticamente
	var $tree; 				// @var tree array						Vetor de dados da árvore de páginas/links do menu
	var $rootSql; 			// @var rootSql string					Consulta SQL que monta o nível zero do menu, ou raiz
	var $rootSize; 			// @var rootSize int					Número de opções da raiz do menu
	var $childSql; 			// @var childSql string					Consulta SQL no formato statement - parametrizada - para buscar os demais níveis do menu
	var $limit; 			// @var limit int						Limite de níveis que pode ser estabelecido pelo usuário
	var $lastLevel = 0; 	// @var lastLevel int					"0" Nível mais alto gerado para o menu
	var $_Db = NULL; 		// @var _Db Db object					"NULL" Conexão com o banco de dados
	var $_Document; 		// @var _Document Document object		Instância da classe Document onde o menu é incluído

	//!-----------------------------------------------------------------
	// @function 	Menu::Menu
	// @desc 		Construtor da classe
	// @param 		&Document Document object	Objeto Document onde o menu será inserido
	// @access 		public
	//!-----------------------------------------------------------------
	function Menu(&$Document) {
		parent::Component();
		if ($this->isA('Menu', FALSE))
        	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Menu'), E_USER_ERROR, __FILE__, __LINE__);
        if (!TypeUtils::isInstanceOf($Document, 'Document'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		$this->name = PHP2Go::generateUniqueId(parent::getClassName());
		$this->_Document =& $Document;
	}

	//!-----------------------------------------------------------------
	// @function 	Menu::loadFromDatabase
	// @desc        Constrói a árvore do menu através de duas consultas SQL:
	//				uma para a raiz ou nível zero e outra para os níveis subseqüentes
	// @param 		rootSql string		Consulta SQL que gera o primeiro nível no menu
	// @param 		childSql string		Consulta SQL para geração dos demais níveis
	// @param		limit int			"0"	Limite de níveis
	// @param		connectionId string	"NULL" ID da conexão a banco de dados a ser utilizada
	// @note 		A consulta da raiz deve retornar ao menos dois campos, onde o
	// 				primeiro será interpretado como índice do menu e o segundo
	// 				como o seu rótulo. O terceiro, se existir, será usado como
	// 				o link acessível através de cada opção deste nível
	// @note 		A consulta dos filhos deve trazer no mínimo duas colunas, que serão
	// 				interpretadas respectivamente como o índice e o rótulo da
	// 				opção de menu em um determinado nível. A cláusula de condição
	// 				deve fazer referência ao nível superior da árvore de menus
	// 				em uma construção do tipo 'WHERE cod_menu = ~cod_menu~',
	// 				onde a variável cod_menu do statement em questão será buscada
	// 				no nível imediatamente superior do menu, se este existir
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadFromDatabase($rootSql, $childSql, $limit=0, $connectionId=NULL) {
		$this->_Db = Db::getInstance($connectionId);
		$this->rootSql = $rootSql;
		$this->childSql = ereg_replace("~([^~])~", "~".strtoupper("\\1")."~", $childSql);
		$this->limit = TypeUtils::parseIntegerPositive($limit);
	}

	//!-----------------------------------------------------------------
	// @function	Menu::loadFromXmlFile
	// @desc		Constrói a árvore do menu a partir de um arquivo XML
	// @param		xmlFile string	Arquivo XML com a definição do menu
	// @note		Os itens do menu são construídos a partir dos filhos da raiz
	//				da árvore. Qualquer tag pode ser utilizada nos níveis da árvore,
	//				desde que cada nodo que deve representar um item do menu
	//				contenha os atributos 'LINK' (vazio para itens de menu não
	//				clicáveis) e 'CAPTION'. O atributo 'TARGET' também pode
	//				ser informado, tanto para o nível raiz como para os níveis
	//				inferiores
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadFromXmlFile($xmlFile, $byFile=TRUE) {
		$XmlDoc = new XmlDocument();
		$XmlDoc->parseXml($xmlFile, ($byFile === TRUE ? T_BYFILE : T_BYVAR));
		$this->xmlRoot = $XmlDoc->getRoot();
		if (!$this->xmlRoot->hasChildren()) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_EMPTY_XML_ROOT'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Menu::buildMenu
	// @desc		Executa as operações e métodos necessários à construção
	//				da estrutura de dados do menu, dependendo da forma de
	//				input dos dados: sql ou xml
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function buildMenu() {
		// menu a partir de consultas SQL
		if (isset($this->rootSql) && isset($this->childSql)) {
			$oldFetchMode = $this->_Db->setFetchMode(ADODB_FETCH_ASSOC);
			$RootRs =& $this->_Db->query($this->rootSql);
			if ($this->_verifyRootSql($RootRs->fields)) {
				$this->_buildTreeFromDatabase($RootRs, 0, $this->tree);
				$this->rootSize = sizeof($this->tree);
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_WRONG_ROOT_SQL'), E_USER_ERROR, __FILE__, __LINE__);
			}
			$this->_Db->setFetchMode($oldFetchMode);
		// menu a partir de arquivo XML
		} elseif (isset($this->xmlRoot)) {
			$this->_buildTreeFromXmlFile($this->xmlRoot, 0, $this->tree);
			$this->rootSize = sizeof($this->tree);
		// menu não inicializado
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_NOT_FOUND'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Menu::_buildTreeFromXmlFile
	// @desc		Função recursiva que constrói a estrutura de dados do
	//				menu a partir dos nodos definidos no arquivo XML
	// @param		Node XmlNode object	Objeto XMLNode cujos filhos devem ser inseridos no menu
	// @param		i int				Índice do nível atual do menu
	// @param		&Tree array			Ponteiro onde devem ser inseridos os valores de novos nodos a cada iteração
	// @note		Este método é executado em Menu::buildMenu caso a opção
	//				de geração por XML tenha sido escolhida
	// @accesss		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildTreeFromXmlFile($Node, $i, &$Tree) {
		$cCount = 0;
		for ($i=0,$s=$Node->getChildrenCount(); $i<$s; $i++) {
			$Child = $Node->getChild($i);
			$Tree[$cCount] = array(
				'CAPTION' => $Child->getAttribute('CAPTION'),
				'LINK' => $Child->getAttribute('LINK'),
				'TARGET' => $Child->getAttribute('TARGET'),
				'CHILDREN' => array()
			);
			if ($i < $this->limit || $this->limit == 0) {
				if ($Child->hasChildren()) {
					$TreePtr =& $Tree[$cCount]['CHILDREN'];
					$this->_buildTreeFromXmlFile($Child, $i+1, $TreePtr);
				} elseif ($i >= $this->lastLevel) {
					$this->lastLevel = $i;
				}
			}
			$cCount++;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Menu::_buildTreeFromDatabase
	// @desc 		Função recursiva que armazena em uma estrutura de dados
	// 				as definições do menu que resultam das consultas SQL fornecidas
	// @param 		rs ADORecordSet object	Result Set de consulta ativo
	// @param 		i int					Índice do nível atual do menu
	// @param 		&Tree array				Ponteiro onde devem ser inseridos os valores de novos nodos a cada iteração
	// @note		Este método é executado em Menu::buildMenu caso tenha
	//				sido escolhida a construção do menu por consultas SQL
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildTreeFromDatabase($rs, $i, &$Tree) {
		$cCount = 0;
		while ($cData = $rs->FetchRow()) {
			$cData = array_change_key_case($cData, CASE_UPPER);
			$Tree[$cCount] = array(
				'CAPTION' => $cData['CAPTION'],
				'LINK' => $cData['LINK'],
				'TARGET' => (array_key_exists('TARGET', $cData) ? $cData['TARGET'] : ''),
				'CHILDREN' => array()
			);
			if ($i < $this->limit || $this->limit == 0) {
				$ChildRs = $this->_verifyChildrenSql($this->childSql, $cData);
				if ($ChildRs === FALSE) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_WRONG_CHILDREN_STATEMENT'), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					if ($ChildRs->recordCount() > 0) {
						$TreePtr =& $Tree[$cCount]['CHILDREN'];
						$this->_buildTreeFromDatabase($ChildRs, $i+1, $TreePtr);
					} elseif ($i >= $this->lastLevel) {
						$this->lastLevel = $i;
					}
				}
			}
			$cCount++;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Menu::_verifyRootSql
	// @desc 		Verifica se a consulta da raiz do menu retorna resultados
	// 				e se possui as colunas obrigatórias caption e link
	// @param 		rootSql string	Consulta SQL da raiz do menu
	// @access 		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _verifyRootSql($rootData) {
		$rootData = array_change_key_case($rootData, CASE_UPPER);
		if (!array_key_exists('CAPTION', $rootData) || !array_key_exists('LINK', $rootData))
			return FALSE;
		else
			return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function 	Menu::_verifyChildrenSql
	// @desc 		Verifica se a consulta que gera as opções internas de menu
	// 				possui parâmetros válidos para o nível superior ('WHERE codigo = ~codigo~')
	//				e se possui as colunas obrigatórias caption e link
	// @param 		childSql string	Statement/Consulta SQL das subopções de menu
	// @access 		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _verifyChildrenSql($childSql, $parentFields) {
		$Child = new Statement($childSql);
		$stmtVars = $Child->getDefinedVars();
		if (empty($stmtVars)) {
			return FALSE;
		} else {
			foreach ($stmtVars as $varName) {
				$varNameUpper = strtoupper($varName);
				if (array_key_exists($varNameUpper, $parentFields)) {
					$Child->bindByName($varName, $parentFields[$varNameUpper]);
				} else {
					return FALSE;
				}
			}
			if (!$Child->isAllBound())
				return FALSE;
			$ChildRs =& $this->_Db->query($Child->getResult());
			if ($ChildRs->recordCount() > 0) {
				$fieldNames = array_change_key_case($ChildRs->fields, CASE_UPPER);
				if (!array_key_exists('CAPTION', $fieldNames) || !array_key_exists('LINK', $fieldNames))
					return FALSE;
			}
			return $ChildRs;
		}
	}
}
?>