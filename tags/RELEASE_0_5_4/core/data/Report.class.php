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
// $Header: /www/cvsroot/php2go/core/data/Report.class.php,v 1.75 2006/11/19 18:31:30 mpont Exp $
// $Date: 2006/11/19 18:31:30 $

//------------------------------------------------------------------
import('php2go.data.PagedDataSet');
import('php2go.data.ReportSimpleSearch');
import('php2go.db.QueryBuilder');
import('php2go.net.Url');
import('php2go.net.UserAgent');
import('php2go.text.StringUtils');
import('php2go.xml.XmlDocument');
import('php2go.util.Callback');
import('php2go.util.Statement');
//------------------------------------------------------------------

// @const REPORT_DEFAULT_VISIBLE_PAGES "10"
// Define o n�mero padr�o de links para outras p�ginas vis�veis
define('REPORT_DEFAULT_VISIBLE_PAGES', 10);
// @const REPORT_DEFAULT_PAGE_BREAK "20"
// N�mero de linhas padr�o por p�gina em modo de impress�o
define('REPORT_DEFAULT_PAGE_BREAK', 20);
// @const REPORT_COLUMN_SIZES_CUSTOM "1"
// C�lulas cuja largura deve ser definida pelo programador
define('REPORT_COLUMN_SIZES_CUSTOM', 1);
// @const REPORT_COLUMN_SIZES_FIXED "2"
// C�lulas s�o geradas com o mesmo tamanho
define('REPORT_COLUMN_SIZES_FIXED', 2);
// @const REPORT_COLUMN_SIZES_FREE "3"
// C�lulas s�o geradas sem atributo de tamanho - a largura � definida pelo browser
define('REPORT_COLUMN_SIZES_FREE', 3);
// @const REPORT_PAGING_DEFAULT "1"
// Sistema de pagina��o padr�o, com links para N pr�ximas p�ginas, primeira e �ltima
define('REPORT_PAGING_DEFAULT', 1);
// @const REPORT_PREVNEXT "2"
// Sistema de pagina��o com apenas dois links, para a p�gina anterior e a pr�xima
define('REPORT_PREVNEXT', 2);
// @const REPORT_FIRSTPREVNEXTLAST "3"
// Sistema de pagina��o com links para a primeira, anterior, pr�xima e �ltima p�ginas
define('REPORT_FIRSTPREVNEXTLAST', 3);

//!-----------------------------------------------------------------
// @class		Report
// @desc 		A classe Report constr�i relat�rios a partir de consultas
// 				SQL, permitindo a exibi��o por colunas ou por c�lulas, o
// 				agrupamento do relat�rio por uma ou mais colunas, pagina��o
// 				autom�tica, filtros simples ou compostos e ordena��o on-the-fly
// @package		php2go.data
// @extends 	PagedDataSet
// @uses		Db
// @uses		HtmlUtils
// @uses		HttpRequest
// @uses		QueryBuilder
// @uses		ReportSimpleSearch
// @uses		Statement
// @uses		StringUtils
// @uses		Template
// @uses		XmlDocument
// @author 		Marcos Pont
// @version		$Revision: 1.75 $
// @note		Confira exemplo de uso em examples/report.example.php
// @note		Se estiver utilizando PHP5, n�o esque�a de incluir a declara��o XML na primeira linha do arquivo de especifica��o
//!-----------------------------------------------------------------
class Report extends PagedDataSet
{
	var $id;						// @var id string					ID do relat�rio, utilizando para identifica-lo em p�ginas que utilizam mais de uma inst�ncia
	var $title;						// @var title string				T�tulo do relat�rio
	var $debug = FALSE;				// @var debug bool					"FALSE" Habilita ou desabilita debug na execu��o da consulta do relat�rio
	var $baseUri;					// @var baseUri string				URI base para os links montados pela classe
	var $rootAttrs = array();		// @var rootAttrs array				"array()" Vetor de atributos da raiz do XML do relat�rio
	var $hasHeader = FALSE;			// @var hasHeader bool				"FALSE" Indica que o relat�rio � montado em colunas com os cabe�alhos na primeira linha
	var $numCols = 1;				// @var numCols int					"1" N�mero de registros por linha (quando hasHeader = FALSE), modelo de relat�rio em "c�lulas"
	var $isSortable = TRUE;			// @var isSortable bool				"TRUE" Se verdadeiro, habilita a gera��o de links nos cabe�alhos das colunas para reordena��o do relat�rio
	var $isPrintable = FALSE;		// @var isPrintable bool			"FALSE" Indica se o relat�rio est� sendo gerado para impress�o
	var $pageBreak;					// @var pageBreak int				Quebra de p�gina (para a vers�o de impress�o)
	var $extraVars;					// @var extraVars string			Par�metros extra que devem ser enviados junto com requisi��es de pagina��o e reordenamento
	var $emptyBlock;				// @var emptyBlock string			Permite a defini��o de um bloco alternativo a ser criado para relat�rios por c�lula quando o n�mero de c�lulas n�o completa a linha
	var $handlers = array();		// @var handlers array				"array()" Conjunto de handlers (pageStart, pageEnd, line)
	var $pagination = array();		// @var pagination array			"array()" Vetor contendo configura��es de pagina��o
	var $jsListeners = array();		// @var jsListeners array			"array()" Conjunto de tratadores Javascript (onChangePage, onSort, onSearch)
	var $style = array();			// @var style array					"array()" Vetor que armazena as configura��es de estilo do relat�rio (t�tulo, links, formul�rio de busca, hints de ajuda)
	var $icons = array(); 			// @var icons array					"array()" Vetor de �cones da classe (orderasc = ordena��o ascendente, orderdesc = ordena��o descendente)
	var $variables = array();		// @var variables array				"array()" Defini��es de vari�veis: valor, valor default e ordem de pesquisa
	var $columns = array();			// @var columns array				"array()" Armazena configura��es das colunas do relat�rio (alias, tamanho, alinhamento, texto de ajuda)
	var $hidden = array();			// @var hidden array				"array()" Vetor de colunas a serem escondidas na exibi��o do relat�rio
	var $unsortable = array();		// @var unsortable array			"array()" Conjunto de colunas para as quais n�o deve ser habilitada ordena��o a partir dos cabe�alhos
	var $colSizes;					// @var colSizes array				Vetor que permite definir tamanhos customizados para as colunas (hasHeader = TRUE)
	var $colSizesMode;				// @var colSizesMode int			Modo utilizado para definir os tamanhos das colunas
	var $group;						// @var group array					Vetor de colunas pelas quais os dados devem ser agrupados
	var $groupDisplay = array();	// @var groupDisplay array			"array()" Vetor de colunas a serem exibidas a cada novo agrupamento
	var $emptyTemplate;				// @var emptyTemplate array			Armazena o arquivo e as vari�veis de substitui��o do template customizado para tratamento de relat�rio vazio
	var $searchTemplate;			// @var searchTemplate array		Armazena o arquivo e as vari�veis de substitui��o do template do formul�rio de filtros de pesquisa
	var $Template = NULL;			// @var Template Template object	Template para gera��o do conte�do do relat�rio
	var $_Document = NULL;			// @var _Document Document object	Documento onde o relat�rio ser� inserido
	var $_SimpleSearch = NULL;		// @var _SimpleSearch ReportSimpleSearch object		Controla o formul�rio de busca/filtro
	var $_loaded = FALSE;			// @var _loaded bool				"FALSE" Indica se o relat�rio j� foi constru�do com o m�todo build
	var $_dataSource = array();		// @var _dataSource array			"array()" Dados da consulta SQL, extra�dos do arquivo XML
	var $_sqlCode = '';				// @var _sqlCode string				"" C�digo SQL final do relat�rio
	var $_bindVars = array();		// @var _bindVars array				"array()" Vari�veis de amarra��o da consulta SQL
	var $_currentGroup;				// @var _currentGroup mixed			Armazena o agrupamento ativo
	var $_order;					// @var _order string				Coluna atual da ordena��o customizada pelo usu�rio
	var $_orderType;				// @var _orderType string			Tipo atual da ordena��o customizada pelo usu�rio

	//!-----------------------------------------------------------------
	// @function	Report::Report
	// @desc		Construtor da classe de relat�rios do PHP2Go. Cria uma
	// 				conex�o com o banco e realiza as configura��es iniciais
	// @param		xmlFile string				Arquivo XML com as defini��es de gera��o do relat�rio
	// @param		templateFile string			Arquivo template da interface do relat�rio
	// @param		&Document Document object	Objeto Document onde o relat�rio ser� inserido
	// @param		tplIncludes array			"array()" Vetor de valores para blocos de inclus�o no template
	// @access 		public
	//!-----------------------------------------------------------------
	function Report($xmlFile, $templateFile, &$Document, $tplIncludes=array()) {
		parent::PagedDataSet('db');
		if (!TypeUtils::isInstanceOf($Document, 'Document'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		$this->id = sprintf("%u", crc32($xmlFile . $templateFile));
		$this->baseUri = HttpRequest::basePath();
		$this->pagination = array(
			'visiblePages' => REPORT_DEFAULT_VISIBLE_PAGES,
			'style' => array(
				REPORT_PAGING_DEFAULT, array(
					'useSymbols' => FALSE,
					'useButtons' => FALSE,
					'hideInvalid' => TRUE
				)
			)
		);
		$this->style = array(
			'help' => 'BGCOLOR,"#000000",FGCOLOR,"#ffffff"'
		);
		$this->colSizesMode = REPORT_COLUMN_SIZES_FREE;
		$this->icons = array(
			'orderasc' => PHP2GO_ICON_PATH . "report_order_asc.gif",
			'orderdesc' => PHP2GO_ICON_PATH . "report_order_desc.gif",
			'help' => PHP2GO_ICON_PATH . "help.gif"
		);
		$this->Template = new Template($templateFile);
		if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {
			foreach ($tplIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
		$this->_order = HttpRequest::get('order');
		$this->_orderType = TypeUtils::ifNull(HttpRequest::get('ordertype'), 'a');
		$this->_Document =& $Document;
		$this->_SimpleSearch = new ReportSimpleSearch();
		// inicializa configura��es de apresenta��o a partir da configura��o global do PHP2Go
		$globalConf = PHP2Go::getConfigVal('REPORTS', FALSE);
		if ($globalConf)
			$this->_loadGlobalSettings($globalConf);
		$this->_processXml($xmlFile);
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	Report::__destruct
	// @desc		Destrutor do objeto Report
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	Report::getSql
	// @desc		Retorna o c�digo final da consulta SQL ou do statement
	//				utilizado para gerar o relat�rio
	// @return		string C�digo SQL do relat�rio
	// @access		public
	//!-----------------------------------------------------------------
	function getSql() {
		return $this->_sqlCode;
	}

	//!-----------------------------------------------------------------
	// @function	Report::getFirstPageUrl
	// @desc		Retorna a URL que aponta para a primeira p�gina do relat�rio
	// @return		string	URL apontando para a primeira p�gina
	// @access		public
	//!-----------------------------------------------------------------
	function getFirstPageUrl() {
		return $this->_generatePageLink(1);
	}

	//!-----------------------------------------------------------------
	// @function	Report::getLastPageUrl
	// @desc		Retorna a URL que aponta para a �ltima p�gina do relat�rio
	// @return		string	URL apontando para a �ltima p�gina
	// @access		public
	//!-----------------------------------------------------------------
	function getLastPageUrl() {
		return $this->_generatePageLink(parent::getPageCount());
	}

	//!-----------------------------------------------------------------
	// @function	Report::getPreviousPageUrl
	// @desc		Retorna a URL que aponta para a p�gina anterior do relat�rio,
	//				ou FALSE se a p�gina atual � a primeira
	// @note		Para que este m�todo possa ser utilizado, deve ser executado
	//				ap�s a chamada do m�todo Report::build()
	// @return		mixed	URL da p�gina anterior ou FALSE
	// @access		public
	//!-----------------------------------------------------------------
	function getPreviousPageUrl() {
		if ($previousPage = parent::getPreviousPage())
			return $this->_generatePageLink($previousPage);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Report::getNextPageUrl
	// @desc		Retorna a URL que aponta para a pr�xima p�gina do relat�rio,
	//				ou FALSE se a p�gina atual � a �ltima
	// @note		Para que este m�todo possa ser utilizado, deve ser executado
	//				ap�s a chamada do m�todo Report::build()
	// @return		mixed	URL da pr�xima p�gina ou FALSE
	// @access		public
	//!-----------------------------------------------------------------
	function getNextPageUrl() {
		if ($nextPage = parent::getNextPage())
			return $this->_generatePageLink($nextPage);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Report::setTitle
	// @desc 		Configura o t�tulo do relat�rio
	// @param 		title string	T�tulo para o relat�rio
	// @param 		docTitle bool	"FALSE" Concatenar este t�tulo ao documento
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTitle($title, $docTitle=FALSE) {
		$this->title = $title;
		if (!!$docTitle)
			$this->_Document->appendTitle($this->title, TRUE);
	}

	//!-----------------------------------------------------------------
	// @function	Report::setBaseUri
	// @desc		Define o endere�o base para montagem de links
	// @param		uri string		URI base
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setBaseUri($uri) {
		$this->baseUri = $uri;
	}

	//!-----------------------------------------------------------------
	// @function 	Report::useHeader
	// @desc 		Configura o relat�rio para exibir 1 registro apenas por
	// 				linha, criando no topo da p�gina cabe�alhos para todas
	// 				as colunas
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function useHeader() {
		$this->hasHeader = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setColumns
	// @desc 		Informa quantos registros por linha o relat�rio deve exibir
	// @param 		numCols int		N�mero de registros por linha
	// @see 		Report::useHeader
	// @note 		Esta fun��o s� ter� efeito sobre o relat�rio se o uso
	// 				de cabe�alhos n�o estiver ativo
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setColumns($numCols) {
		$this->numCols = max(intval($numCols), 1);
	}

	//!-----------------------------------------------------------------
	// @function	Report::disableOrderByLinks
	// @desc		Desabilita a funcionalidade de ordena��o de colunas a partir dos cabe�alhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function disableOrderByLinks() {
		$this->isSortable = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Report::isPrintable
	// @desc		Indica que o relat�rio est� sendo gerado para impress�o. Desta forma, �
	//				necess�rio informar o valor de quebra de p�gina. A pagina��o e a ordena��o
	//				din�mica ser�o desabilitadas
	// @param		pageBreak int	Quebra de p�gina
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function isPrintable($pageBreak) {
		@set_time_limit(0);
		$this->isPrintable = TRUE;
		$pageBreak = intval($pageBreak);
		$this->pageBreak = ($pageBreak ? $pageBreak : REPORT_DEFAULT_PAGE_BREAK);
		$this->_SimpleSearch->clear();
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setExtraVars
	// @desc 		Concatena o texto indicado pelo par�metro $extraVars a todos os links
	//				para outras p�ginas, submiss�o de busca e reordena��o dos dados
	// @param 		extraVars string		Texto em formato 'urlencode'
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setExtraVars($extraVars) {
		if (ereg("[^=]+=[^=]+", $extraVars)) {
			$this->extraVars = ltrim($extraVars);
			if ($this->extraVars[0] == '&') {
				$this->extraVars = substr($this->extraVars, 1);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Report::setEmptyBlock
	// @desc		Permite definir um bloco a ser utilizado em relat�rios do tipo
	//				CELL (N c�lulas por linha com M placeholders em cada c�lula) para
	//				o caso da �ltima linha de dados tiver de ser completada com c�lulas
	//				em branco ou com um c�digo diferente do bloco loop_cell
	// @param		blockName string	Nome do bloco
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setEmptyBlock($blockName) {
		$this->emptyBlock = $blockName;
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setVisiblePages
	// @desc 		Configura o n�mero m�ximo de p�ginas vis�veis na tela
	// @param 		pages int		N�mero de p�ginas vis�veis na tela
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setVisiblePages($pages) {
		$this->pagination['visiblePages'] = max(intval($pages), 1);
	}

	//!-----------------------------------------------------------------
	// @function	Report::setPagingStyle
	// @desc		Define o estilo dos links de pagina��o que ser�o gerados
	//				para o relat�rio. Os tipos est�o definidos em constantes
	//				da pr�pria classe
	// @param		style int			Estilo de links de pagina��o
	// @param		params array		"NULL" Par�metros para montagem da pagina��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setPagingStyle($style, $params = NULL) {
		$this->pagination['style'][0] = intval($style);
		if (is_array($params)) {
			foreach ($params as $key => $value) {
				switch ($key) {
					case 'useButtons' :
					case 'useSymbols' :
					case 'hideInvalid' :
						$this->pagination['style'][1][$key] = (bool)$value;
						break;
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setStyleMapping
	// @desc 		Configura os estilos CSS do relat�rio
	// @param 		link string			"" Estilo CSS para os links e outros textos
	// @param 		filter string		"" Estilo CSS para os campos do formul�rio de busca
	// @param 		button string		"" Estilo CSS para os bot�es do formul�rio de busca
	// @param 		title string		"" Estilo CSS para o t�tulo do relat�rio
	// @param		header string		"" Estilo CSS para os cabe�alhos do relat�rio, se habilitados
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setStyleMapping($link='', $filter='', $button='', $title='', $header='') {
		if (trim($header) == '')
			$header = $link;
		$this->style = array(
			'link' => $link,
			'filter' => $filter,
			'button' => $button,
			'title' => $title,
			'header' => $header
		);
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setAlternateStyle
	// @desc 		Configura estilos CSS para serem alternados nas linhas do relat�rio
	// @note 		A fun��o recebe N par�metros de entrada, que ser�o os
	// 				estilos exibidos alternadamente no relat�rio. A vari�vel
	// 				{alt_style} deve ser declarada no bloco 'loop_cell'
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAlternateStyle() {
		if (func_num_args() < 2)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MIN_ALT_STYLE'), E_USER_ERROR, __FILE__, __LINE__);
		else
			$this->style['altstyle'] = func_get_args();
	}

	//!-----------------------------------------------------------------
	// @function 	Report::enableHighlight
	// @desc 		Configura cores para destacar valores de busca nos resultados de uma consulta
	// @param 		fgColor string	Cor em formato RGB para o texto
	// @param 		bgColor string	"" Cor em formato RGB para o fundo
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function enableHighlight($fgColor, $bgColor = "") {
		$this->style['highlight'] = "color:$fgColor";
		if ($bgColor != "")
			$this->style['highlight'] .= ";background-color:$bgColor";
	}

	//!-----------------------------------------------------------------
	// @function	Report::setVariable
	// @desc		Define ou altera o valor para uma vari�vel declarada na especifica��o XML
	// @param		name string		Nome da vari�vel
	// @param		value mixed		Valor da vari�vel
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setVariable($name, $value) {
		if (isset($this->variables[$name]))
			$this->variables[$name]['value'] = $value;
		else
			$this->variables[$name] = array(
				'value' => $value
			);
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setGroup
	// @desc 		Define o agrupamento que deve ser realizado no relat�rio
	// @param 		groupBy mixed	Coluna ou vetor de colunas que devem ser usadas para agrupamento
	// @param 		display mixed	"" Coluna ou vetor de colunas que devem ser exibidas
	// 								a cada troca de grupo. Se n�o for informado, exibir�
	// 								as mesmas colunas indicadas no par�metro $groupBy
	// @note 		Todas as colunas devem ser nomes/alias v�lidos para a consulta
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setGroup($groupBy, $display = '') {
		$this->group = (!is_array($groupBy) ? array($groupBy) : $groupBy);
		if ($display == '')
			$this->groupDisplay = (!is_array($groupBy) ? array($groupBy) : $groupBy);
		elseif (is_scalar($display))
			$this->groupDisplay = (!is_array($display) ? array($display) : $display);
		else
			$this->groupDisplay = (!is_array($groupBy) ? array($groupBy) : $groupBy);
		foreach ($this->groupDisplay as $field)
			if (in_array($field, $this->hidden))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_HIDDEN_GROUP', $field), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	Report::setStartPageHandler
	// @desc		Define uma fun��o ou m�todo a ser executado no in�cio da constru��o
	//				da p�gina de dados. O m�todo recebe o objeto Report por
	//				refer�ncia
	// @param		callback mixed		Nome de fun��o ou vetor contendo objeto/classe e m�todo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setStartPageHandler($callback) {
		$this->handlers['pageStart'] = new Callback($callback);
	}

	//!-----------------------------------------------------------------
	// @function	Report::setEndPageHandler
	// @desc		Define uma fun��o ou m�todo a ser executado no final da constru��o
	//				da p�gina de dados. O m�todo recebe o objeto Report por
	//				refer�ncia
	// @note		Este m�todo pode ser �til para a constru��o de um rodap� para o relat�rio,
	//				contendo totalizadores
	// @note		No momento da chamada deste tratador, o dataset possui a propriedade EOF=true.
	//				Para acessar a �ltima linha dos dados, por exemplo, voc� dever� executar o m�todo
	//				$Report->movePrevious()
	// @param		callback mixed		Nome de fun��o ou vetor contendo objeto/classe e m�todo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setEndPageHandler($callback) {
		$this->handlers['pageEnd'] = new Callback($callback);
	}

	//!-----------------------------------------------------------------
	// @function	Report::setLineHandler
	// @desc		Define uma fun��o ou m�todo que deve tratar cada linha
	//				do relat�rio, para fins de altera��o ou formata��o de valores
	// @param		callback mixed		Nome de fun��o ou vetor contendo objeto e m�todo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLineHandler($callback) {
		$this->handlers['line'] = new Callback($callback);
	}

	//!-----------------------------------------------------------------
	// @function	Report::setColumnHandler
	// @desc		Define uma fun��o ou m�todo que deve tratar uma coluna espec�fica do relat�rio
	// @param		columnName string	Nome da coluna
	// @param		callback mixed		Nome de fun��o ou vetor contendo objeto e m�todo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setColumnHandler($columnName, $callback) {
		$this->columns[$columnName]['handler'] = new Callback($callback);
	}

	//!-----------------------------------------------------------------
	// @function	Report::setColumnAlias
	// @desc		Define um alias para uma determinada coluna do relat�rio
	// @param		columnName mixed	Nome da coluna ou hash array com colunas=>aliases
	// @param		alias string		"" Alias para a coluna
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setColumnAlias($columnName, $alias = '') {
		if (TypeUtils::isHashArray($columnName)) {
			foreach ($columnName as $key => $value) {
				$this->columns[$key]['alias'] = $value;
			}
		} else {
			$this->columns[$columnName]['alias'] = $alias;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setColumnSizes
	// @desc 		Define os tamanhos das colunas em cada linha do relat�rio
	// @param 		sizes mixed		"NULL" Modo de constru��o (vide constantes da classe) ou vetor de tamanhos customizados para as colunas (deve somar 100)
	// @note 		Esta fun��o s� ter� efeito se os cabe�alhos forem habilitados com a fun��o useHeader
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setColumnSizes($param=NULL) {
		if (is_array($param)) {
			if (array_sum($param) != 100) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_SIZES_SUM'), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$this->colSizesMode = REPORT_COLUMN_SIZES_CUSTOM;
				$this->colSizes = $param;
			}
		} elseif ($param == REPORT_COLUMN_SIZES_FIXED || $param == REPORT_COLUMN_SIZES_FREE) {
			$this->colSizesMode = $param;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_INVALID_COLSIZES', $param), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Report::setHidden
	// @desc		Define como escondido um ou mais campos do relat�rio
	// @param		fieldName mixed	Nome de campo ou vetor de campos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHidden($fieldName) {
		if (is_array($fieldName)) {
			// as colunas n�o podem pertencer ao conjunto de colunas de cabe�alho de grupo (groupDisplay)
			foreach ($fieldName as $field)
				if (in_array($field, $this->groupDisplay))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_GROUP_HIDDEN', $field), E_USER_ERROR, __FILE__, __LINE__);
			if (!isset($this->hidden))
				$this->hidden = $fieldName;
			else
				$this->hidden = array_merge($this->hidden, $fieldName);
		} else {
			// a coluna n�o pode pertencer ao conjunto de colunas de cabe�alho de grupo (groupDisplay)
			if (in_array($fieldName, $this->groupDisplay))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_GROUP_HIDDEN', $fieldName), E_USER_ERROR, __FILE__, __LINE__);
			if (!isset($this->hidden))
				$this->hidden = array($fieldName);
			else
				$this->hidden[] = $fieldName;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Report::setUnsortableColumns
	// @desc		Define para quais colunas deve ser omitido o link para
	//				reordena��o da lista a partir do cabe�alho
	// @note		Por padr�o, a classe habilita ordena��o em todas as colunas
	// @note		Para desabilitar por completo a ordena��o nos cabe�alhos,
	//				utilize o m�todo disableOrderByLinks
	// @param		unsortable array	Array contendo nomes das colunas
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setUnsortableColumns($unsortable) {
		$this->unsortable = (array)$unsortable;
	}

	//!-----------------------------------------------------------------
	// @function	Report::bind
	// @desc		Adiciona uma vari�vel ou um conjunto de vari�veis para
	//				relat�rios que utilizam stored procedures
	// @param		variable mixed	Nome da vari�vel ou array associativo de vari�veis e valores
	// @param		value mixed		"" Valor da vari�vel
	// @note		Este m�todo n�o dever� ser utilizado para a atribui��o de valores
	//				para vari�veis no padr�o ~var~ utilizado pelo PHP2Go
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function bind($variable, $value = '') {
		if (TypeUtils::isHashArray($variable)) {
			foreach ($variable as $key => $value)
				$this->_bindVars[$key] = $value;
		} elseif (is_string($variable)) {
			$this->_bindVars[$variable] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setSearchMaskFunction
	// @desc 		Associa uma fun��o ou m�todo a uma m�scara de dados, para realizar
	// 				convers�o de valor nos par�metros de busca utilizados
	// @param 		mask string			Nome da m�scara
	// @param 		callback string		Nome da fun��o ou vetor objeto+m�todo a ser executada
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSearchMaskFunction($mask, $callback) {
		$this->_SimpleSearch->addMaskFunction($mask, $callback);
	}

	//!-----------------------------------------------------------------
	// @function 	Report::setEmptyTemplate
	// @desc 		Configura o template a ser utilizado quando o relat�rio
	// 				ou o filtro de pesquisa n�o retornarem resultados
	// @param 		templateFile string	Nome do arquivo template
	// @param 		templateVars array	"array()" Vetor associativo com as vari�veis a serem substitu�das no template
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setEmptyTemplate($templateFile, $templateVars = array()) {
		$this->emptyTemplate = array(
			'file' => $templateFile,
			'vars' => (TypeUtils::isHashArray($templateVars)) ? $templateVars : array()
		);
	}

	//!-----------------------------------------------------------------
	// @function	Report::disableEmptyTemplate
	// @desc		Desabilita a utiliza��o de um template secund�rio quando
	//				o relat�rio n�o cont�m registros
	// @note		Desta forma, o template principal ser� exibido mesmo quando
	//				o relat�rio estiver vazio, e a exibi��o de uma mensagem
	//				especial para aus�ncia de resultados fica a cargo do
	//				desenvolvedor, atrav�s de comandos IF e acesso �s vari�veis
	//				globais publicadas pela classe Report
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function disableEmptyTemplate() {
		$this->emptyTemplate['disabled'] = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Report::setSearchTemplate
	// @desc		Define um template alternativo para o formul�rio de busca simples
	// @param		searchTemplate string	Caminho completo do arquivo template
	// @param		templateVars array		"array()" Conjunto de vari�veis para atribui��o
	// @note		O template criado deve possuir as mesmas vari�veis declaradas no template original,
	//				localizado em PHP2GO_ROOT/resources/templates/simplesearch.tpl
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSearchTemplate($searchTemplate, $templateVars = array()) {
		$this->searchTemplate = array(
			'file' => $searchTemplate,
			'vars' => (TypeUtils::isHashArray($templateVars)) ? $templateVars : array()
		);
	}

	//!-----------------------------------------------------------------
	// @function	Report::build
	// @desc		Constr�i o conjunto de dados da p�gina atual do relat�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function build() {
		if (!$this->_loaded) {
			$this->_buildDataSet();
			$this->_buildLimits();
			$this->_loaded = TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Report::onPreRender
	// @desc		Prepara o relat�rio para renderiza��o: constru��o
	//				do template de conte�do (ou do template para resultados
	//				vazios), biblioteca JS de busca simples
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			if (!$this->_loaded)
				$this->build();
			$this->baseUri = $this->_evaluateStatement($this->baseUri);
			if (!$this->_SimpleSearch->isEmpty())
				$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "widgets/simplesearch.js");
			if (parent::getTotalRecordCount() > 0 || @$this->emptyTemplate['disabled'] === TRUE) {
				$this->_buildContent();
			} else {
				$this->Template = new Template(isset($this->emptyTemplate['file']) ? $this->emptyTemplate['file'] : PHP2GO_TEMPLATE_PATH . 'emptyreport.tpl');
				$this->Template->parse();
				$this->Template->assign('name', $this->id);
				$this->Template->assign('title', (!empty($this->title) ? sprintf("<span class=\"%s\">%s</span>", $this->style['title'], $this->title) : ''));
				$this->Template->assign('report', array(
					'base_uri' => $this->baseUri,
					'search_sent' => $this->_SimpleSearch->searchSent,
					'style' => $this->style
				));
				$this->Template->assign((array)PHP2Go::getLangVal('REPORT_EMPTY_VALUES'));
				if (!empty($this->emptyTemplate['vars']))
					$this->Template->assign((array)$this->emptyTemplate['vars']);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::getContent
	// @desc 		Constr�i e retorna o conte�do do relat�rio
	// @access 		public
	// @return		string
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		return $this->Template->getContent();
	}

	//!-----------------------------------------------------------------
	// @function 	Report::display
	// @desc 		Constr�i e imprime o relat�rio
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		$this->onPreRender();
		$this->Template->display();
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_processXml
	// @desc 		Processa o arquivo XML que cont�m a especifica��o da
	// 				consulta SQL do relat�rio e os par�metros de filtragem
	// 				de dados
	// @param		xmlFile string	Caminho completo para o arquivo XML
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _processXml($xmlFile) {
		$XmlDocument = new XmlDocument();
		$XmlDocument->parseXml($xmlFile);
		$XmlRoot =& $XmlDocument->getRoot();
		$this->rootAttrs = $XmlRoot->getAttributes();
		// t�tulo
		if (isset($this->rootAttrs['TITLE']))
			$this->setTitle($this->rootAttrs['TITLE'], TRUE);
		// flag de debug
		$this->debug = (bool)resolveBooleanChoice(consumeArray($this->rootAttrs, 'DEBUG'));
		// base URI
		if ($uri = consumeArray($this->rootAttrs, 'BASEURI'))
			$this->baseUri = $uri;
		if ($XmlRoot->getChildrenCount() > 0) {
			$count = $XmlRoot->getChildrenCount();
			for ($i=0; $i<$count; $i++) {
				$Node =& $XmlRoot->getChild($i);
				// defini��es de layout (pagina��o, estilo, �cones, colunas)
				if ($Node->getTag() == 'LAYOUT') {
					$this->_buildLayout($Node);
				}
				// defini��es de vari�veis
				elseif ($Node->getTag() == 'VARIABLE') {
					$attrs = $Node->getAttributes();
					if ($name = @$attrs['NAME']) {
						$variable = array(
							'default' => @$attrs['DEFAULT'],
							'search' => @$attrs['SEARCHORDER']
						);
						if (!isset($this->variables[$name]))
							$this->variables[$name] = $variable;
						else
							$this->variables[$name] = array_merge($this->variables[$name], $variable);
					}
				}
				// fonte de dados
				elseif ($Node->getTag() == 'DATASOURCE' && $dsChildren = $Node->getChildrenTagsArray()) {
					$this->adapter->setParameter('connectionId', TypeUtils::ifFalse($Node->getAttribute('CONNECTION'), NULL));
					$this->_dataSource = array(
						'PROCEDURE' => isset($dsChildren['PROCEDURE']) ? $dsChildren['PROCEDURE']->value : '',
						'FIELDS' => isset($dsChildren['FIELDS']) ? $dsChildren['FIELDS']->value : '',
						'TABLES' => isset($dsChildren['TABLES']) ? $dsChildren['TABLES']->value : '',
						'CLAUSE' => isset($dsChildren['CLAUSE']) ? $dsChildren['CLAUSE']->value : '',
						'GROUPBY' => isset($dsChildren['GROUPBY']) ? trim($dsChildren['GROUPBY']->value) : '',
						'ORDERBY' => isset($dsChildren['ORDERBY']) ? trim($dsChildren['ORDERBY']->value) : ''
					);
					if (isset($dsChildren['PROCEDURE'])) {
						$this->_dataSource['CURSORNAME'] = $dsChildren['PROCEDURE']->getAttribute('CURSORNAME');
					} else {
						$optimizeCount = $Node->getAttribute('OPTIMIZECOUNT');
						$this->adapter->setParameter('optimizeCount', ($optimizeCount === FALSE ? TRUE : resolveBooleanChoice($optimizeCount)));
					}
				}
				// filtros para o formul�rio de busca simples
				elseif ($Node->getTag() == 'DATAFILTERS' && $Node->hasChildren()) {
					for ($j=0; $j<$Node->getChildrenCount(); $j++) {
						$Child =& $Node->getChild($j);
						$attrs = $Child->getAttributes();
						if ($attrs['LABEL'])
							$attrs['LABEL'] = resolveI18nEntry($attrs['LABEL']);
						$this->_SimpleSearch->addFilter($attrs);
					}
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Report::_buildLayout
	// @desc		Processa as informa��es de layout provenientes da
	//				especifica��o XML do relat�rio
	// @param		&Layout XmlNode object	Nodo LAYOUT presente na especifica��o XML
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildLayout(&$Layout) {
		$attrs = $Layout->getAttributes();
		// header
		$useHeader = resolveBooleanChoice(@$attrs['GRID']);
		if (is_bool($useHeader))
			$this->hasHeader = $useHeader;
		// columns
		$columns = intval(@$attrs['NUMCOLS']);
		if ($columns >= 1 && !$this->hasHeader)
			$this->setColumns($columns);
		// printable + page break
		$printable = resolveBooleanChoice(@$attrs['PRINTABLE']);
		if ($printable)
			$this->isPrintable(@$attrs['PAGEBREAK']);
		// sortable
		if (($sortable = resolveBooleanChoice(@$attrs['SORTABLE'])) === FALSE)
			$this->isSortable = FALSE;
		// empty block
		if ($emptyBlock = @$attrs['EMPTYBLOCK'])
			$this->emptyBlock = $emptyBlock;
		// empty template
		$emptyTemplate = @$attrs['EMPTYTEMPLATE'];
		if (resolveBooleanChoice($emptyTemplate) === FALSE)
			$this->emptyTemplate['disabled'] = TRUE;
		elseif (file_exists($emptyTemplate))
			$this->emptyTemplate['file'] = $emptyTemplate;
		$count = $Layout->getChildrenCount();
		for ($i=0; $i<$count; $i++) {
			$ChildNode =& $Layout->getChild($i);
			switch ($ChildNode->getName()) {
				// configura��es de pagina��o
				case 'PAGINATION' :
					$attrs = $ChildNode->getAttributes();
					if ($pageSize = @$attrs['PAGESIZE'])
						parent::setPageSize($pageSize);
					if ($visiblePages = @$attrs['VISIBLEPAGES'])
						$this->setVisiblePages($visiblePages);
					if ($style = @$attrs['STYLE']) {
						if (defined($style)) {
							$paramList = array();
							$params = $ChildNode->getElementsByTagName('PARAM');
							if (is_array($params)) {
								foreach ($params as $paramNode) {
									$paramName = $paramNode->getAttribute('NAME');
									$paramValue = $paramNode->getAttribute('VALUE');
									if (!empty($paramName) && strlen($paramValue) > 0) {
										if (in_array($paramName, array('useButtons', 'useSymbols', 'hideInvalid')))
											$paramValue = resolveBooleanChoice($paramValue);
										$paramList[$paramName] = $paramValue;
									}
								}
							}
							$this->setPagingStyle(constant($style), $paramList);
						}
					}
					break;
				// listeners Javascript
				case 'LISTENER' :
					$attrs = $ChildNode->getAttributes();
					if (isset($attrs['EVENT']) && in_array($attrs['EVENT'], array('onChangePage', 'onSort', 'onSearch'))) {
						$funcName = sprintf("report%s%s", $this->id, ucfirst($attrs['EVENT']));
						if (isset($attrs['ACTION'])) {
							$funcBody = "\t\t" . trim($attrs['ACTION']);
						} else {
							$funcBody = rtrim(ltrim($ChildNode->getData(), "\r\n"));
							if (preg_match("/^([\t]+)/", $funcBody, $matches))
								$funcBody = preg_replace("/^\t{" . strlen($matches[1]) . "}/m", "\t\t", $funcBody);
						}
						if ($attrs['EVENT'] != 'onSearch') {
							$this->_Document->addScriptCode(sprintf("\tfunction %s(args) {\n%s\n\t}", $funcName, $funcBody), 'Javascript', SCRIPT_END);
							$this->jsListeners[$attrs['EVENT']] = $funcName;
						} else {
							$this->jsListeners[$attrs['EVENT']] = $funcBody;
						}
					}
					break;
				// configura��es de estilo
				case 'STYLE' :
					$attrs = $ChildNode->getAttributes();
					foreach ($attrs as $attrName => $attrValue) {
						switch ($attrName) {
							case 'LINK' :
							case 'FILTER' :
							case 'BUTTON' :
							case 'TITLE' :
							case 'HEADER' :
							case 'HELP' :
								$this->style[strtolower($attrName)] = $attrValue;
								break;
							case 'ALTSTYLE' :
								$altStyle = explode(',', $attrValue);
								if (!empty($altStyle))
									$this->style['altstyle'] = $altStyle;
								break;
							case 'HIGHLIGHT' :
								if (!empty($attrValue)) {
									$highlight = explode(',', $attrValue);
									$this->enableHighlight($highlight[0], @$highlight[1]);
								}
								break;
						}
					}
					break;
				// �cones
				case 'ICON' :
					$name = $ChildNode->getAttribute('NAME');
					$path = $ChildNode->getAttribute('PATH');
					if ($name && $path)
						$this->icons[$name] = $path;
					break;
				// defini��es de colunas
				case 'COLUMNS' :
					$attrs = $ChildNode->getAttributes();
					// tamanhos
					if ($sizes = @$attrs['SIZES']) {
						if (defined($sizes)) {
							$this->setColumnSizes(constant($sizes));
						} else {
							$this->setColumnSizes(explode(',', trim($sizes)));
						}
					}
					// agrupamento
					if ($column = @$attrs['GROUP']) {
						$display = @$attrs['GROUPDISPLAY'];
						if ($display)
							$display = explode(',', $display);
						$this->setGroup(explode(',', $column), $display);
					}
					$columns = $ChildNode->getElementsByTagName('COLUMN');
					foreach ($columns as $idx => $ColumnNode) {
						$colAttrs = $ColumnNode->getAttributes();
						$name = consumeArray($colAttrs, 'NAME');
						if ($name) {
							$sortable = resolveBooleanChoice(consumeArray($colAttrs, 'SORTABLE'));
							if ($sortable === FALSE && !in_array($name, $this->unsortable))
								$this->unsortable[] = $name;
							$hidden = resolveBooleanChoice(consumeArray($colAttrs, 'HIDDEN'));
							if ($hidden === TRUE && !in_array($name, $this->hidden))
								$this->hidden[] = $name;
							if (isset($colAttrs['ALIAS']))
								$colAttrs['ALIAS'] = resolveI18nEntry($colAttrs['ALIAS']);
							if (isset($colAttrs['HELP']))
								$colAttrs['HELP'] = resolveI18nEntry($colAttrs['HELP']);
							$this->columns[$name] = array_change_key_case(array_merge((array)$this->columns[$name], $colAttrs), CASE_LOWER);
						}
					}
					break;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Report::_buildDataSet
	// @desc		M�todo que constr�i o conjunto de dados da p�gina atual do relat�rio,
	//				incluindo filtros de pesquisa se existentes e cl�usulas de ordena��o customizadas
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildDataSet() {
		// processa a substitui��o de poss�veis vari�veis nos membros da consulta SQL
		foreach ($this->_dataSource as $element => $value) {
			if (preg_match("/~[^~]+~/", $value))
				$this->_dataSource[$element] = $this->_evaluateStatement($value);
		}
		// debug do datasource montado
		if ($this->debug) {
			print('REPORT DEBUG --- DATASOURCE ELEMENTS :');
			dumpVariable($this->_dataSource);
			$this->adapter->setParameter('debug', TRUE);
		} else {
			$this->adapter->setParameter('debug', FALSE);
		}
		// verifica se a consulta ir� executar uma procedure no banco de dados
		if ($this->_dataSource['PROCEDURE'] != '') {
			$isProcedure = TRUE;
			$cursorName = @$this->_dataSource['CURSORNAME'];
			$this->_sqlCode = trim($this->_dataSource['PROCEDURE']);
			if (ereg(':CLAUSE', $this->_dataSource['PROCEDURE']))
				$this->_bindVars['CLAUSE'] = $this->_SimpleSearch->getSearchClause();
			$this->_bindVars['ORDER'] = $this->_orderByClause();
		// do contr�rio, constr�i a SQL a partir dos elementos declarados no arquivo XML
		} else {
			$isProcedure = FALSE;
			$cursorName = NULL;
			$Query = new QueryBuilder($this->_dataSource['FIELDS'], $this->_dataSource['TABLES'], $this->_dataSource['CLAUSE'], $this->_dataSource['GROUPBY']);
			$Query->addClause($this->_SimpleSearch->getSearchClause());
			$Query->setOrder($this->_orderByClause());
			$this->_sqlCode = $Query->getQuery();
		}
		// constru��o da p�gina de resultados
		if (!$this->isPrintable)
			PagedDataSet::load($this->_sqlCode, $this->_bindVars, $isProcedure, $cursorName);
		else {
			DataSet::load($this->_sqlCode, $this->_bindVars, $isProcedure, $cursorName);
		}
		// colunas
		$fieldNames = parent::getFieldNames();
		foreach ($fieldNames as $fieldName) {
			if (!isset($this->columns[$fieldName]))
				$this->columns[$fieldName] = array();
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_buildLimits
	// @desc 		Calcula os dados de pagina��o: p�gina atual, primeira p�gina exibida
	//				na tela, �ltima p�gina exibida na tela. Verifica se � poss�vel navegar
	//				para a primeira, N anteriores, N pr�ximas e �ltima p�ginas
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildLimits() {
		// valida��o para evitar p�ginas fora do escopo do relat�rio
		$basePage = (parent::getCurrentPage() > parent::getPageCount() ? parent::getPageCount() : parent::getCurrentPage());
		// c�lculo do primeiro link de p�gina vis�vel na tela
		if (($basePage % $this->pagination['visiblePages']) == 0) {
			$this->pagination['firstVisiblePage'] = ((TypeUtils::parseInteger($basePage / $this->pagination['visiblePages']) - 1) * $this->pagination['visiblePages']) + 1;
		} else {
			$this->pagination['firstVisiblePage'] = (TypeUtils::parseInteger($basePage / $this->pagination['visiblePages']) * $this->pagination['visiblePages']) + 1;
		}
		// c�lculo do �ltimo link de p�gina vis�vel na tela
		if (($this->pagination['firstVisiblePage'] + $this->pagination['visiblePages'] - 1) <= parent::getPageCount()) {
			$this->pagination['lastVisiblePage'] = $this->pagination['firstVisiblePage'] + $this->pagination['visiblePages'] - 1;
		} else {
			$this->pagination['lastVisiblePage'] = parent::getPageCount();
		}
		// � poss�vel navegar para a primeira p�gina ?
		if (parent::getCurrentPage() > 1) {
			$this->pagination['firstPage'] = 1;
		}
		// existe uma p�gina anterior ?
		if (parent::getCurrentPage() > 1) {
			if (parent::getCurrentPage() > $this->pagination['visiblePages'] && parent::getCurrentPage() == $this->pagination['firstVisiblePage'])
				$this->pagination['previousPage'] = $this->pagination['firstVisiblePage'] - 1;
			else
				$this->pagination['previousPage'] = parent::getCurrentPage() - 1;
		}
		// existe uma tela anterior ?
		if (parent::getCurrentPage() > $this->pagination['visiblePages']) {
			$this->pagination['previousScreen'] = $this->pagination['firstVisiblePage'] - 1;
		}
		// existe uma pr�xima p�gina ?
		if (parent::getCurrentPage() < parent::getPageCount()) {
			if ($this->pagination['lastVisiblePage'] < parent::getPageCount() && parent::getCurrentPage() == $this->pagination['lastVisiblePage'])
				$this->pagination['nextPage'] = $this->pagination['lastVisiblePage'] + 1;
			else
				$this->pagination['nextPage'] = parent::getCurrentPage() + 1;
		}
		// existe uma pr�xima tela ?
		if ($this->pagination['lastVisiblePage'] < parent::getPageCount()) {
			$this->pagination['nextScreen'] = $this->pagination['lastVisiblePage'] + 1;
		}
		// � poss�vel navegar para a �ltima p�gina
		if (parent::getCurrentPage() < parent::getPageCount()) {
			$this->pagination['lastPage'] = parent::getPageCount();
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_buildContent
	// @desc 		Esta fun��o constr�i o conte�do da p�gina. Exibe registros de acordo com
	//				as op��es do usu�rio (uso de header, m�ltiplas colunas, uso de agrupamento,
	//				fun��es de dados, etc...). Gera tamb�m o formul�rio de busca, se estiver habilitado
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildContent() {
		$aRow = 0;	// contador de registros utilizados no result set
		$aLine = 0;	// contador de linhas de relat�rio geradas (somente usado quando hasHeader==FALSE)
		$aCell = 1;	// contador da c�lula atual (somente usado quando hasHeader==FALSE)
		$errorMsg = NULL;
		if (!$this->_checkVariables($errorMsg))
			PHP2Go::raiseError($errorMsg, E_USER_ERROR, __FILE__, __LINE__);
		if (!$this->_checkHidden($errorMsg))
			PHP2Go::raiseError($errorMsg, E_USER_ERROR, __FILE__, __LINE__);
		if (isset($this->group)) {
			if (!$this->_checkGroup($errorMsg)) {
				PHP2Go::raiseError($errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			} else {
				if (isset($this->handlers['pageStart']) && TypeUtils::isInstanceOf($this->handlers['pageStart'], 'Callback'))
					$this->handlers['pageStart']->invokeByRef($this);
				// inicializa result set e template
				parent::moveFirst();
				$this->Template->setCurrentBlock(TP_ROOTBLOCK);
				if ($this->hasHeader) {
					while ($lineData = parent::fetch()) {
						$this->Template->createBlock('loop_line');
						// houve troca de agrupamento? gera grupo, cabe�alho e gera nova linha
						if ($this->_matchGroup($lineData)) {
							$this->_dataGroup($lineData);
							$this->Template->createBlock('loop_line');
							$this->_dataHeader();
							$this->Template->createBlock('loop_line');
						}
						// gera as n colunas
						$this->_dataColumns($lineData, NULL);
						$aRow++;
						if ($this->isPrintable && (($aRow % $this->pageBreak) == 0))
							$this->_buildPageBreak();
					}
				} else {
					// exp�e para o template os aliases e os textos de ajuda
					foreach ($this->columns as $name => $config) {
						if (isset($config['alias']))
							$this->Template->globalAssign("{$name}_alias", $config['alias']);
						if (isset($config['help']))
							$this->Template->globalAssign("{$name}_help", sprintf("<img id=\"%s\" src=\"%s\" alt=\"\" style=\"cursor:pointer\" border=\"0\"%s/>", "{$name}_help", $this->icons['help'], HtmlUtils::overPopup($this->_Document, $config['help'], $this->style['help'])));
					}
					$this->Template->createBlock('loop_line');
					while ($lineData = parent::fetch()) {
						// houve troca de agrupamento ?
						if ($this->_matchGroup($lineData)) {
							// � a primeira linha ? gera grupo e uma c�lula
							if ($aRow == 0) {
								$this->_dataGroup($lineData);
								$this->Template->createBlock('loop_line');
								$this->_dataColumns($lineData, $aCell);
								$aCell++;
								if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
							// n�o � a primeira linha
							} else {
								// verifica se � necess�rio completar a linha com c�lulas vazias
								if (($aCell > 1) && ($aCell <= $this->numCols)) {
									for ($i = $aCell; $i <= $this->numCols; $i++)
										$this->_dataColumns(NULL, $i);
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
								// gera grupo, c�lula e quebra linha se necess�rio
								$this->_dataGroup($lineData);
								$this->Template->createBlock('loop_line');
								$this->_dataColumns($lineData, $aCell);
								$aCell++;
								if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
							}
						// n�o houve troca de agrupamento. Gera c�lula e quebra linha se necess�rio
						} else {
							$this->_dataColumns($lineData, $aCell);
							$aCell++;
							if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
								$aLine++;
								if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
									$this->_buildPageBreak();
								$this->Template->createBlock('loop_line');
								$aCell = 1;
							}
						}
						$aRow++;
					}
					// completa com c�lulas vazias se necess�rio ao final da p�gina
					if ($aCell <= $this->numCols) {
						for ($i = $aCell; $i <= $this->numCols; $i++)
							$this->_dataColumns(NULL, $i);
					}
				}
				if (isset($this->handlers['pageEnd']) && TypeUtils::isInstanceOf($this->handlers['pageEnd'], 'Callback'))
					$this->handlers['pageEnd']->invokeByRef($this);
			}
		} else {
			if (isset($this->handlers['pageStart']) && TypeUtils::isInstanceOf($this->handlers['pageStart'], 'Callback'))
				$this->handlers['pageStart']->invokeByRef($this);
			parent::moveFirst();
			$this->Template->setCurrentBlock(TP_ROOTBLOCK);
			if ($this->hasHeader) {
				// gera o cabe�alho
				$this->Template->createBlock('loop_line');
				$this->_dataHeader();
				// gera as n linhas com as n colunas
				while ($lineData = parent::fetch()) {
					$this->Template->createBlock('loop_line');
					$this->_dataColumns($lineData, NULL);
					$aRow++;
					if ($this->isPrintable && (($aRow % $this->pageBreak) == 0))
						$this->_buildPageBreak();
				}
			} else {
				// exp�e para o template os aliases e os textos de ajuda
				foreach ($this->columns as $name => $config) {
					if (isset($config['alias']))
						$this->Template->globalAssign("{$name}_alias", $config['alias']);
					if (isset($config['help']))
						$this->Template->globalAssign("{$name}_help", sprintf("<img id=\"%s\" src=\"%s\" alt=\"\" style=\"cursor:pointer\" border=\"0\"%s/>", "{$name}_help", $this->icons['help'], HtmlUtils::statusBar($config['help'], TRUE)));
				}
				// cria a primeira linha
				$this->Template->createBlock('loop_line');
				$aLine++;
				// gera os N registros da p�gina
				while ($lineData = parent::fetch()) {
					// gera uma c�lula e quebra linha se necess�rio
					$this->_dataColumns($lineData, $aCell);
					$aCell++;
					if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
						$this->Template->createBlock('loop_line');
						$aCell = 1;
						$aLine++;
						if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
							$this->_buildPageBreak();
					}
					$aRow++;
				}
				// completa com c�lulas vazias se necess�rio no final da p�gina
				if ($aCell <= $this->numCols) {
					for ($i = $aCell; $i <= $this->numCols; $i++)
						$this->_dataColumns(NULL, $i);
				}
			}
			if (isset($this->handlers['pageEnd']) && TypeUtils::isInstanceOf($this->handlers['pageEnd'], 'Callback'))
				$this->handlers['pageEnd']->invokeByRef($this);
		}
		// atribui o t�tulo do relat�rio
		$this->Template->globalAssign("title", (!empty($this->title) ? sprintf("<span class=\"%s\">%s</span>", $this->style['title'], $this->title) : ''));
		// atribui as vari�veis de controle
		$this->Template->globalAssign('report', array(
			'base_uri' => $this->baseUri,
			'use_header' => $this->hasHeader,
			'search_sent' => $this->_SimpleSearch->searchSent,
			'style' => $this->style,
			'total_rows' => (int)parent::getTotalRecordCount(),
			'page_rows' => (int)parent::getRecordCount(),
			'page_size' => (int)parent::getPageSize(),
			'current_page' => (int)parent::getCurrentPage(),
			'total_pages' => (int)parent::getPageCount(),
			'at_first_page' => parent::atFirstPage(),
			'at_last_page' => parent::atLastPage()
		));
		// atribui no template as fun��es pr�-definidas: conjunto de links para outras p�ginas,
		// mensagens contento total de p�ginas, p�gina atual, total de linhas, formul�rio de
		// acesso r�pido a outras p�ginas, etc...
		$functionMessages = PHP2Go::getLangVal('REPORT_FUNCTION_MESSAGES');
		$this->Template->globalAssign(array(
			'page_links' => $this->_pageLinks($functionMessages),
			'order_options_combo' => $this->_orderOptions('combo'),
			'order_options_links' => $this->_orderOptions('links'),
			'row_count' => $this->_rowCount($functionMessages),
			'rows_per_page' => $this->_rowsPerPage($functionMessages),
			'this_page' => $this->_thisPage($functionMessages),
			'row_interval' => $this->_rowInterval($functionMessages),
			'go_to_page' => $this->_goToPage($functionMessages)
		));
		// exibe o formul�rio de busca r�pida apenas se
		// o relat�rio n�o � de impress�o
		if (!$this->isPrintable)
			$this->_buildSearchForm();
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_dataHeader
	// @desc 		Exibe o cabe�alho de dados com os nomes das colunas do relat�rio
	// @note 		Esta fun��o s� � executada no modo em que os cabe�alhos
	// 				s�o exibidos no topo das p�ginas ou nos in�cios de grupo
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _dataHeader() {
		// verifica tamanhos de coluns
		$visibleCols = (parent::getFieldCount() - count($this->groupDisplay) - count($this->hidden));
		if (isset($this->colSizes) && sizeof($this->colSizes) != $visibleCols)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_COUNT_MISMATCH', array(sizeof($this->colSizes), $visibleCols, sizeof($this->groupDisplay))), E_USER_ERROR, __FILE__, __LINE__);
		// gera N cabe�alhos de colunas
		for ($i=0,$c=0; $i<parent::getFieldCount(); $i++) {
			$colName = parent::getFieldName($i);
			$colConfig =& $this->columns[$colName];
			$colAlias = (isset($colConfig['alias']) ? $colConfig['alias'] : $colName);
			if (!in_array($colName, $this->groupDisplay) && !in_array($colName, $this->hidden)) {
				$colWidth = ($this->colSizesMode == REPORT_COLUMN_SIZES_CUSTOM && isset($this->colSizes) ? $this->colSizes[$c] . '%' : ($this->colSizesMode == REPORT_COLUMN_SIZES_FIXED ? TypeUtils::parseInteger(100 / $visibleCols) . '%' : ''));
				$this->Template->createBlock('loop_header_cell');
				$this->Template->assign('col_id', $colName);
				$this->Template->assign('col_wid', $colWidth);
				if (isset($colConfig['help']))
					$this->Template->assign('col_help', sprintf("&nbsp;<img id=\"%s\" src=\"%s\" alt=\"\" style=\"cursor:pointer\" border=\"0\"%s/>", "{$this->id}_{$colName}_help", $this->icons['help'], HtmlUtils::overPopup($this->_Document, $colConfig['help'], $this->style['help'])));
				// se for vers�o de impress�o, ou se ordena��o por colunas estiver desabilitada
				// ou se a coluna n�o estiver na lista das colunas onde ordena��o � permitida,
				// exibe apenas o nome da coluna (sem o link de ordena��o)
				if ($this->isPrintable || $this->isSortable === FALSE || in_array($colName, $this->unsortable)) {
					$this->Template->assign('col_name', (!empty($this->style['header']) ? "<span class='{$this->style['header']}'>{$colAlias}</span>" : $colAlias));
				} else {
					$onSort = @$this->jsListeners['onSort'];
					$this->Template->assign('col_name', HtmlUtils::anchor($this->_generatePageLink(parent::getCurrentPage(), $colName), $colAlias, PHP2Go::getLangVal('REPORT_ORDER_TIP', $colAlias), $this->style['header'], ($onSort ? array('onClick' => $onSort . '()') : array()), '', "{$this->id}_header{$c}"));
					$this->Template->assign('col_order', (urldecode(HttpRequest::get('order')) == $colName ? '&nbsp;' . HtmlUtils::image($this->_orderTypeIcon()) : ''));
				}
				$c++;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_dataColumns
	// @desc 		Exibe um registro da consulta no relat�rio. Formata de acordo com a
	//				op��o, utilizando colunas ou inserindo os dados em uma c�lula
	// @param 		colData array	"NULL" Dados do registro
	// @param		cellIdx int		"NULL" N�mero da c�lula, quando se trata de um relat�rio por c�lulas
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _dataColumns($colData=NULL, $cellIdx=NULL) {
		if (!empty($this->style['altstyle'])) {
			$altStyle = current($this->style['altstyle']);
			if (is_null($cellIdx) || $cellIdx == $this->numCols) {
				if (!next($this->style['altstyle']))
					reset($this->style['altstyle']);
			}
		}
		if (!is_null($colData)) {
			(isset($this->handlers['line'])) && ($colData = $this->handlers['line']->invoke($colData));
			(!empty($this->style['highlight'])) && ($colData = $this->_highlightSearch($colData));
			if ($this->hasHeader) {
				$visibleCols = (parent::getFieldCount() - count($this->groupDisplay) - count($this->hidden));
				if (isset($this->colSizes) && sizeof($this->colSizes) != $visibleCols)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_COUNT_MISMATCH', array(sizeof($this->colSizes), $visibleCols, sizeof($this->groupDisplay))), E_USER_ERROR, __FILE__, __LINE__);
				for ($i=0,$c=0; $i<parent::getFieldCount(); $i++) {
					$colName = parent::getFieldName($i);
					$colConfig =& $this->columns[$colName];
					if (!in_array($colName, $this->groupDisplay) && !in_array($colName, $this->hidden)) {
						$this->Template->createBlock('loop_cell');
						$this->Template->assign('col_wid', ($this->colSizesMode == REPORT_COLUMN_SIZES_CUSTOM && isset($this->colSizes) ? $this->colSizes[$c] . '%' : ($this->colSizesMode == REPORT_COLUMN_SIZES_FIXED ? intval(100/$visibleCols) . '%' : '')));
						(isset($colConfig['handler'])) && ($colData[$colName] = $colConfig['handler']->invoke($colData[$colName]));
						(isset($colConfig['align']) && stristr($colConfig['align'], 'left') === FALSE) && ($colData[$colName] = "<div align='{$colConfig['align']}'>{$colData[$colName]}</div>");
						$this->Template->assign('col_data', $colData[$colName]);
						if (!empty($this->style['altstyle'])) {
							$this->Template->assign('alt_style', $altStyle);
							$this->Template->assign('loop_line.alt_style', $altStyle);
						}
						$c++;
					}
				}
			} else {
				$this->Template->createBlock('loop_cell');
				$this->Template->assign('col_wid', intval(100/$this->numCols) . '%');
				(!empty($this->style['altstyle'])) && ($this->Template->assign('alt_style', $altStyle));
				for ($i=0; $i<parent::getFieldCount(); $i++) {
					$colName = parent::getFieldName($i);
					$colConfig =& $this->columns[$colName];
					if (!in_array($colName, $this->groupDisplay) && !in_array($colName, $this->hidden)) {
						if (isset($colConfig['handler']))
							$colData[$colName] = $colConfig['handler']->invoke($colData[$colName]);
						$this->Template->assign("{$colName}", $colData[$colName]);
					}
				}
			}
		} else {
			$blockName = (isset($this->emptyBlock) ? $this->emptyBlock : 'loop_cell');
			$this->Template->createBlock($blockName);
			$this->Template->assign(parent::getFieldName(0), '&nbsp;');
			if (!empty($this->style['altstyle']) && $this->Template->isVariableDefined("{$blockName}.alt_style")) {
				$this->Template->assign('alt_style', $altStyle);
				$this->Template->assign('loop_line.alt_style', $altStyle);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_dataGroup
	// @desc 		Gera um cabe�alho de grupo. A caption do grupo � formada
	//				pela concatena��o das colunas definidas na propriedade groupDisplay
	// @param 		colData array	Dados do registro
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _dataGroup($colData) {
		$groupDisplay = '';
		foreach ($this->groupDisplay as $colName)
			$groupDisplay .= (empty($groupDisplay) ? $colData[$colName] : ' - ' . $colData[$colName]);
		$groupSpan = ($this->hasHeader ? (parent::getFieldCount() - count($this->groupDisplay)) : $this->numCols);
		$this->Template->createAndAssign('loop_group', array(
			'group_display' => $groupDisplay,
			'group_span' => $groupSpan
		));
	}

	//!-----------------------------------------------------------------
	// @function	Report::_checkVariables
	// @desc		Verifica se as vari�veis obrigat�rias, dependendo das
	//				configura��es fornecidas ao relat�rio, foram declaradas
	//				corretamente no template
	// @param		&errorMsg string	Vari�vel de retorno da mensagem de erro
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _checkVariables(&$errorMsg) {
		if ($this->hasHeader) {
			if (!$this->Template->isVariableDefined('loop_cell.col_data')) {
				$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_data', 'loop_cell'));
				return FALSE;
			}
			if (!$this->Template->isVariableDefined('loop_header_cell.col_name')) {
				$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_name', 'loop_header_cell'));
				return FALSE;
			}
			if ($this->colSizesMode != REPORT_COLUMN_SIZES_FREE) {
				if (!$this->Template->isVariableDefined('loop_cell.col_wid')) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_wid', 'loop_cell'));
					return FALSE;
				} elseif (!$this->Template->isVariableDefined('loop_header_cell.col_wid')) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_wid', 'loop_header_cell'));
					return FALSE;
				}
			}
		}
		if (!empty($this->group) && !empty($this->groupDisplay)) {
			if (!$this->Template->isVariableDefined('loop_group.group_display')) {
				$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('group_display', 'loop_group'));
				return FALSE;
			}
			if (!$this->Template->isVariableDefined('loop_group.group_span')) {
				$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('group_span', 'loop_group'));
				return FALSE;
			}
		}
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Report::_checkHidden
	// @desc		Realiza valida��o nas colunas definidas como escondidas, verificando
	//				se o m�ximo foi excedido e se todas as colunas existem no result set
	// @param		&errorMsg string	Vari�vel de retorno da mensagem de erro
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _checkHidden(&$errorMsg) {
		$check = TRUE;
		$fieldNames = parent::getFieldNames();
		if (sizeof($this->hidden) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_HIDDEN_COLS');;
			$check = FALSE;
		} else {
			for ($i=0; $i<sizeof($this->hidden); $i++) {
				if (!in_array($this->hidden[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_HIDDEN_COL', $this->hidden[$i]);
					$check = FALSE;
				}
			}
		}
		return $check;
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_checkGroup
	// @desc 		Verifica se todas as colunas de agrupamento s�o v�lidas
	// @param		&errorMsg string	Vari�vel de retorno da mensagem de erro
	// @access 		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _checkGroup(&$errorMsg) {
		$check = TRUE;
		$fieldNames = parent::getFieldNames();
		if (sizeof($this->group) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_GROUP_COLS');
			$check = FALSE;
		} else {
			for ($i=0; $i<sizeof($this->group); $i++) {
				if (!in_array($this->group[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_GROUP_COL', $this->group[$i]);
					$check = FALSE;
				}
			}
		}
		if (sizeof($this->groupDisplay) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_GROUP_COLS');
			$check = FALSE;
		} else {
			for ($i=0; $i<sizeof($this->groupDisplay); $i++) {
				if (!in_array($this->groupDisplay[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_GROUP_COL', $this->groupDisplay[$i]);
					$check = FALSE;
				}
			}
		}
		return $check;
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_matchGroup
	// @desc 		Verifica se houve troca de agrupamento em uma linha de resultados
	// @param 		data array		Vetor com dados de uma linha de resultados
	// @access 		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _matchGroup($data) {
		if (!isset($this->_currentGroup)) {
			foreach ($this->group as $value)
				$this->_currentGroup[$value] = $data[$value];
			return TRUE;
		} else {
			$sizeof = sizeof($this->group);
			for ($i = 0; $i < $sizeof; $i++) {
				if ($this->_currentGroup[$this->group[$i]] != $data[$this->group[$i]]) {
					foreach ($this->group as $value)
						$this->_currentGroup[$value] = $data[$value];
					return TRUE;
				}
			}
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_buildSearchForm
	// @desc 		Constr�i o formul�rio de busca simples
	// @access 		private
	// @return 		void
	//!-----------------------------------------------------------------
	function _buildSearchForm() {
		if (!$this->_SimpleSearch->isEmpty()) {
			if (!$this->Template->isVariableDefined('simple_search'))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_SEARCH_VARIABLE', array('simple_search', 'simple_search')));
			if (isset($this->searchTemplate)) {
				$SearchTpl = new Template($this->searchTemplate['file']);
				$SearchTpl->parse();
				if (!empty($this->searchTemplate['vars']))
					$SearchTpl->assign($this->searchTemplate['vars']);
			} else {
				$SearchTpl = new Template(PHP2GO_TEMPLATE_PATH . 'simplesearch.tpl');
				$SearchTpl->parse();
			}
			$vars = PHP2Go::getLangVal('REPORT_SEARCH_VALUES');
			$vars['name'] = $this->id;
			$vars['searchUrl'] = $this->baseUri;
			if (isset($this->extraVars) && !empty($this->extraVars))
				$vars['searchUrl'] .= (strpos($this->baseUri, '?') !== FALSE ? '&' : '?') . $this->extraVars;
			$vars['labelStyle'] = (!empty($this->style['link']) ? " class=\"{$this->style['link']}\"" : '');
			$Agent =& UserAgent::getInstance();
			if ($Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+'))) {
				$vars['inputStyle'] = (!empty($this->style['filter']) ? " class=\"{$this->style['filter']}\"" : '');
				$vars['buttonStyle'] = (!empty($this->style['button']) ? " class=\"{$this->style['button']}\"" : '');
			}
			$masks = array();
			$vars['filterOptions'] = '';
			$filters = $this->_SimpleSearch->iterator();
			while ($filter = $filters->next()) {
				if ($filter['mask'] == 'DATE')
					$filter['mask'] .= '-' . PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
				$vars['filterOptions'] .= "<option value=\"{$filter['field']}\">{$filter['label']}</option>\n";
				$masks[] = "'{$filter['mask']}'";
			}
			$vars['masks'] = implode(",", $masks);
			$vars['operatorOptions'] = '';
			foreach((array)PHP2Go::getLangVal('REPORT_STRING_OPERATORS') as $key => $value)
				$vars['operatorOptions'] .= "<option value=\"{$key}\">{$value}</option>\n";
			$vars['onSearch'] = @$this->jsListeners['onSearch'];
			$SearchTpl->assign($vars);
			$this->Template->assign("_ROOT.simple_search", $SearchTpl->getContent());
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_buildPageBreak
	// @desc 		Insere uma quebra de p�gina na vers�o de impress�o do relat�rio
	// @access 		private
	// @return 		void
	//!-----------------------------------------------------------------
	function _buildPageBreak() {
		if ($this->Template->isVariableDefined("loop_line.page_break")) {
			$this->Template->assign("loop_line.page_break", "<tr style=\"page-break-after: always\"></tr>");
			if ($this->hasHeader) {
				$this->Template->createBlock('loop_line');
				$this->_dataHeader();
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_generatePageLink
	// @desc 		Gera uma URL de troca de p�gina ou reordena��o
	// @param 		page int	P�gina alvo
	// @param 		order int	"" �ndice para ordena��o
	// @access 		private
	// @return		string
	//!-----------------------------------------------------------------
	function _generatePageLink($page, $order='') {
		if (isset($this->_order) && $order == $this->_order)
			$ot = ($this->_orderType == 'a' ? 'd' : 'a');
		else
			$ot = $this->_orderType;
		$char = (strpos($this->baseUri, '?') !== FALSE ? '&' : '?');
		return sprintf("%s%spage=%s%s%s%s%s",
					$this->baseUri, $char, $page,
					($order != '' ? '&order=' . urlencode($order) : (isset($this->_order) ? '&order=' . $this->_order : '')),
					'&ordertype=' . $ot,
					($this->_SimpleSearch->searchSent ? $this->_SimpleSearch->getUrlString() : ''),
					(isset($this->extraVars) ? '&' . $this->extraVars : '')
		);
	}

	//!-----------------------------------------------------------------
	// @function	Report::_generateNavigationLink
	// @desc		M�todo usado para construir um link de navega��o para
	//				uma determinada p�gina, quando o modo de pagina��o for
	//				diferente de REPORT_PAGING_DEFAULT
	// @param		&links array	Conjunto de links
	// @param		page int		"NULL" N�mero da p�gina alvo
	// @param		name string		Nome do bot�o
	// @param		symbol string	S�mbolo para o bot�o (quando useSymbols=true)
	// @param		text string		Texto para o bot�o (quando useSymbols=false)
	// @param		tip string		"" Tooltip para o bot�o
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _generateNavigationLink(&$links, $page=NULL, $name, $symbol, $text, $tip='') {
		$useSymbols = $this->pagination['style'][1]['useSymbols'];
		$useButtons = $this->pagination['style'][1]['useButtons'];
		$hideInvalid = $this->pagination['style'][1]['hideInvalid'];
		$onChangePage = @$this->jsListeners['onChangePage'];
		$buttonCode = "<button id=\"%s\" name=\"%s\" type=\"button\" onClick=\"%s%s\" title=\"%s\" class=\"%s\"%s>%s</button>";
		$noLinkCode = "<span class=\"%s\" style=\"filter:alpha(opacity=30);opacity:0.3;-moz-opacity:0.3;-khtml-opacity:0.3\">%s</span>";
		$link = ($page ? $this->_generatePageLink($page) : NULL);
		if ($page) {
			if ($useButtons) {
				$links[] = sprintf($buttonCode, $this->id . $name, $name, ($onChangePage ? $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $page . '});' : ''), "location.href='{$link}'", $tip, $this->style['button'], '', ($useSymbols ? $symbol : $text));
			} else {
				$links[] = HtmlUtils::anchor($link, ($useSymbols ? $symbol : $text), $tip, $this->style['link']);
			}
		} elseif (!$hideInvalid) {
			if ($useButtons) {
				$links[] = sprintf($buttonCode, $this->id . $name, $name, '', "javascript:void(0)", '', $this->style['button'], " disabled", ($useSymbols ? $symbol : $text));
			} else {
				$links[] = sprintf($noLinkCode, $this->style['link'], ($useSymbols ? $symbol : $text));
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_pageLinks
	// @desc 		Constr�i os links para outras p�ginas do relat�rio
	// @param		lang array	Conjunto de entradas de idioma (mensagens)
	// @access 		private
	// @return		string
	//!-----------------------------------------------------------------
	function _pageLinks($lang) {
		if ($this->pagination['lastVisiblePage'] == 0)
			return NULL;
		$links = array();
		$linkLimiter = sprintf("<span class=\"%s\"> | </span>", $this->style['link']);
		$linkGlue = ($this->pagination['style'][1]['useButtons'] || $this->pagination['style'][1]['useSymbols'] ? "&nbsp;&nbsp;" : $linkLimiter);
		$onChangePage = @$this->jsListeners['onChangePage'];
		if ($this->pagination['style'][0] == REPORT_PAGING_DEFAULT) {
			$linkStr = '';
			// de 1 a 10 p�ginas, come�ando em um m�ltiplo de 10 + 1
			for ($i = $this->pagination['firstVisiblePage']; $i <= $this->pagination['lastVisiblePage']; $i++) {
				if ($i == parent::getCurrentPage()) {
					$linkStr .= sprintf("<span class=\"%s\">%d</span>\n", $this->style['link'], $i);
				} else {
					$linkStr .= HtmlUtils::anchor($this->_generatePageLink($i), "<u>$i</u>", sprintf($lang['pageTip'], $i, parent::getPageCount()), $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $i . '})') : array()));
				}
				if ($i < $this->pagination['lastVisiblePage'])
					$linkStr .= $linkLimiter;
				else
					$linkStr .= '<br>';
			}
			// links para primeira p�gina, voltar 10, avan�ar 10 e �ltima p�gina
			if (isset($this->pagination['firstPage'])) {
				$linkStr .= HtmlUtils::anchor($this->_generatePageLink($this->pagination['firstPage']), $lang['firstTit'], $lang['firstTip'], $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $this->pagination['firstPage'] . '})') : array()));
			}
			if (isset($this->pagination['previousScreen'])) {
				if (isset($this->pagination['firstPage']))
					$linkStr .= $linkLimiter;
				$linkStr .= HtmlUtils::anchor($this->_generatePageLink($this->pagination['previousScreen']), sprintf($lang['prevScrTit'], $this->pagination['visiblePages']), sprintf($lang['prevScrTip'], $this->pagination['visiblePages']), $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $this->pagination['previousScreen'] . '})') : array()));
			}
			if (isset($this->pagination['nextScreen'])) {
				if (isset($this->pagination['firstPage']) || isset($this->pagination['previousScreen']))
					$linkStr .= $linkLimiter;
				$linkStr .= HtmlUtils::anchor($this->_generatePageLink($this->pagination['nextScreen']), sprintf($lang['nextScrTit'], $this->pagination['visiblePages']), sprintf($lang['nextScrTip'], $this->pagination['visiblePages']), $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $this->pagination['nextScreen'] . '})') : array()));
			}
			if (isset($this->pagination['lastPage'])) {
				if (isset($this->pagination['firstPage']) || isset($this->pagination['previousScreen']) || isset($this->pagination['nextScreen']))
					$linkStr .= $linkLimiter;
				$linkStr .= HtmlUtils::anchor($this->_generatePageLink($this->pagination['lastPage']), $lang['lastTit'], $lang['lastTip'], $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $this->pagination['lastPage'] . '})') : array()));
			}
			return $linkStr;
		} elseif ($this->pagination['style'][0] == REPORT_PREVNEXT) {
			$this->_generateNavigationLink($links, @$this->pagination['previousPage'], 'prev', '<', $lang['prevTit'], $lang['prevTip']);
			$this->_generateNavigationLink($links, @$this->pagination['nextPage'], 'next', '>', $lang['nextTit'], $lang['nextTip']);
			return implode($linkGlue, $links);
		} elseif ($this->pagination['style'][0] == REPORT_FIRSTPREVNEXTLAST) {
			$this->_generateNavigationLink($links, @$this->pagination['firstPage'], 'first', '<<', $lang['firstTit'], $lang['firstTip']);
			$this->_generateNavigationLink($links, @$this->pagination['previousPage'], 'prev', '<', $lang['prevTit'], $lang['prevTip']);
			$this->_generateNavigationLink($links, @$this->pagination['nextPage'], 'next', '>', $lang['nextTit'], $lang['nextTip']);
			$this->_generateNavigationLink($links, @$this->pagination['lastPage'], 'last', '>>', $lang['lastTit'], $lang['lastTip']);
			return implode($linkGlue, $links);
		}
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	Report::_orderOptions
	// @desc		Constr�i uma ferramenta de sele��o da ordena��o da listagem.
	//				O par�metro $type recebe os valores 'combo' ou 'links'
	// @param		type string	Tipo
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _orderOptions($type) {
		if (!$this->isPrintable && $this->isSortable !== FALSE) {
			$onSort = @$this->jsListeners['onSort'];
			if ($type == 'combo') {
				$opts = '';
				for ($i=0; $i<parent::getFieldCount(); $i++) {
					$colName = parent::getFieldName($i);
					$colConfig =& $this->columns[$colName];
					if (!in_array($colName, $this->unsortable)) {
						$value = $this->_generatePageLink(parent::getCurrentPage(), $colName);
						$text = StringUtils::ifEmpty(@$colConfig['alias'], $colName);
						$opts .= "<option value=\"{$value}\">{$text}</option>";
					}
				}
				if (!empty($opts)) {
					return sprintf("<label for=\"order_combo_%s\"%s>%s</label>&nbsp;<select id=\"order_combo_%s\" name=\"order_combo_%s\"%s onChange=\"var url = this.options[this.selectedIndex].value; if (url) { %swindow.location.href = url; }\"><option value=\"\">%s</option>%s</select>",
						$this->id, (!empty($this->style['filter']) ? " class=\"" . $this->style['filter'] . "\"" : ''),
						PHP2Go::getLangVal('REPORT_ORDER_OPTIONS_LABEL'), $this->id, $this->id,
						(isset($this->style['filter']) ? " class=\"" . $this->style['filter'] . "\"" : ''),
						($onSort ? $onSort . '();' : ''), PHP2Go::getLangVal('REPORT_SEARCH_VALUES.filtersTitle'), $opts
					);
				}
			} elseif ($type == 'links') {
				$items = array();
				for ($i=0; $i<parent::getFieldCount(); $i++) {
					$colName = parent::getFieldName($i);
					$colConfig =& $this->columns[$colName];
					if (!in_array($colName, $this->unsortable)) {
						$url = $this->_generatePageLink(parent::getCurrentPage(), $colName);
						$caption = StringUtils::ifEmpty(@$colConfig['alias'], $colName);
						$items[] = HtmlUtils::anchor($url, $caption, PHP2Go::getLangVal('REPORT_ORDER_TIP', $caption), $this->style['link'], ($onSort ? array('onClick' => $onSort . '()') : array()));
					}
				}
				if (!empty($items))
					return sprintf("<span%s>%s&nbsp;%s",
						(isset($this->style['filter']) ? " class=\"{$this->style['filter']}\"" : ''),
						PHP2Go::getLangVal('REPORT_ORDER_OPTIONS_LABEL'), join(' | ', $items)
					);
			}
		}
		return '';
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_rowCount
	// @desc 		Constr�i a mensagem de n�mero total de registros
	// @param		lang array Array de mensagens
	// @access 		private
	// @return		string
	//!-----------------------------------------------------------------
	function _rowCount($lang) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($lang['rowCount'], parent::getTotalRecordCount());
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_rowsPerPage
	// @desc 		Constr�i a mensagem de n�mero de registros por p�gina
	// @param		lang array Array de mensagens
	// @access 		private
	// @return		string
	//!-----------------------------------------------------------------
	function _rowsPerPage($lang) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($lang['rowsPerPage'], parent::getPageSize());
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_thisPage
	// @desc 		Constr�i a mensagem que indica a p�gina atual
	// @param		lang array Array de mensagens
	// @access 		private
	// @return		string
	//!-----------------------------------------------------------------
	function _thisPage($lang) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($lang['thisPage'], parent::getCurrentPage(), parent::getPageCount());
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	Report::_rowInterval
	// @desc		Constr�i a mensagem do intervalo de registros que est� sendo exibido
	// @param		lang array Array de mensagens
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _rowInterval($lang) {
		if (parent::getTotalRecordCount() > 0) {
			$lowerBound = ($this->_offset + 1);
			$upperBound = (($this->_offset + parent::getPageSize()) > parent::getTotalRecordCount()) ? parent::getTotalRecordCount() : ($this->_offset + parent::getPageSize());
			return sprintf($lang['rowInterval'], $lowerBound, $upperBound, parent::getTotalRecordCount());
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	Report::_goToPage
	// @desc		Gera o formul�rio e o campo que permite o salto para
	//				uma determinada p�gina do relat�rio atual
	// @param		lang array Array de mensagens
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _goToPage($lang) {
		if (parent::getTotalRecordCount() > 0) {
			$goToUrl = ereg_replace("(\?|&)(page=[0-9]+)(&?)", "\\1", $this->_generatePageLink(parent::getCurrentPage()));
			$goToLabel = sprintf("<label for=\"{$this->id}_page\" id=\"{$this->id}_page_label\" class=\"%s\">%s</label>", $this->style['filter'], $lang['goTo']);
			$goToField = sprintf("<input type=\"text\" id=\"{$this->id}_page\" name=\"page\" size=\"5\" maxlength=\"10\" class=\"%s\"/>", $this->style['filter']);
			return sprintf("<form id=\"%s_form\" name=\"%s\" method=\"POST\" action=\"%s\" style=\"display:inline\" onSubmit=\"return Report.goToPage(this, %d, %d, %s);\">\n%s\n&nbsp;%s\n</form>\n",
				$this->id, $this->id, $goToUrl, parent::getCurrentPage(), parent::getPageCount(),
				TypeUtils::ifNull($this->jsListeners['onChangePage'], 'null'),
				$goToLabel, $goToField
			);
		}
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	Report::_evaluateStatement
	// @desc		M�todo respons�vel pela resolu��o de vari�veis e express�es
	//				nos membros do DATASOURCE do relat�rio
	// @param		source string	C�digo a ser interpretado
	// @note		O elemento VARIABLE pode ser utilizado, na especifica��o XML,
	//				para definir valores padr�o e ordem de pesquisa na requisi��o
	//				para as vari�veis
	// @return		string Statement com vari�veis dispon�veis e express�es substitu�das
	// @access		public
	//!-----------------------------------------------------------------
	function _evaluateStatement($source) {
		static $Stmt;
		if (!isset($Stmt)) {
			$Stmt = new Statement();
			$Stmt->setVariablePattern('~', '~');
			$Stmt->setShowUnassigned();
		}
		$Stmt->setStatement($source);
		if (!$Stmt->isEmpty()) {
			foreach ($Stmt->variables as $name => $variable) {
				if (isset($this->variables[$name])) {
					if (isset($this->variables[$name]['value'])) {
						$Stmt->bindByName($name, $this->variables[$name]['value'], FALSE);
					} elseif (!$Stmt->bindFromRequest($name, FALSE, @$this->variables[$name]['search'])) {
						if (isset($this->variables[$name]['default'])) {
							$Stmt->bindByName($name, $this->variables[$name]['default'], FALSE);
						}
					}
				} else {
					$Stmt->bindFromRequest($name, FALSE);
				}
			}
		}
		return $Stmt->getResult();
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_highlightSearch
	// @desc 		Aplica destaque nos valores de busca em uma linha de resultados,
	// 				de acordo com os padr�es setados atrav�s da fun��o enableHighlight
	// @param 		data array		Vetor com dados de uma linha de resultados
	// @return 		array Vetor com as colunas modificadas destacando os valores de pesquisa encontrados
	// @access 		private
	//!-----------------------------------------------------------------
	function _highlightSearch($data) {
		$newData = $data;
		if ($this->_SimpleSearch->searchSent) {
			$fields = explode('|', $this->_SimpleSearch->getFields());
			$operators = explode('|', $this->_SimpleSearch->getOperators());
			$values = explode('|', $this->_SimpleSearch->getValues());
			$size = sizeof($fields);
			for($i = 0; $i < $size; $i++) {
				$filters = $this->_SimpleSearch->iterator();
				while ($filter = $filters->next()) {
					if ($filter['field'] == $fields[$i]) {
						$patt = ($operators[$i] == 'LIKEI' ? '^' . $values[$i] : ($operators[$i] == 'LIKEF' ? $values[$i] . '$' : $values[$i]));
						$repl = "<span style=\"{$this->style['highlight']}\">\\1</span>";
						foreach ($data as $key => $value) {
							if ($key == $filter['field'])
								$newData[$key] = preg_replace("'(?!<.*?)($patt)(?![^<>]*?>)'si", $repl, $newData[$key]);
							elseif (StringUtils::normalize(trim(strtolower($key))) == StringUtils::normalize(trim(strtolower($filter['label']))))
								$newData[$key] = preg_replace("'(?!<.*?)($patt)(?![^<>]*?>)'si", $repl, $newData[$key]);
						}
					}
				}
			}
		}
		return $newData;
	}

	//!-----------------------------------------------------------------
	// @function	Report::_orderByClause
	// @desc		M�todo privado de constru��o da cl�usula de ordena��o,
	//				baseado nas configura��es de grupo, na ordena��o definida pelo
	//				usu�rio (cabe�alhos) e na ordena��o padr�o do DATASOURCE
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _orderByClause() {
		$orderMembers = array();
		// 1) ordena��o pelas colunas de agrupamento deve vir em primeiro lugar
		if (isset($this->group)) {
			foreach ($this->group as $field)
				$orderMembers[] = "\"{$field}\"";
		}
		// 2) ordena��o manual, ativada pelos cabe�alhos da listagem, em segundo lugar
		// OBS: Se a coluna de ordena��o a partir dos cabe�alhos estiver na primeira posi��o da
		// ordena��o definida no XML, esta coluna � retirada da ordena��o do XML
		if (isset($this->_order) && !in_array($this->_order, $this->unsortable) && !in_array($this->_order, $this->hidden)) {
			$orderMembers[] = "\"{$this->_order}\" " . ($this->_orderType == 'd' ? ' DESC' : ' ASC');
			$matches = array();
			if (preg_match("/^\s*{$this->_order}(\s*(asc|desc)?\s*,)?/is", $this->_dataSource['ORDERBY'], $matches)) {
				$this->_dataSource['ORDERBY'] = preg_replace('/' . $matches[0] . '/', '', $this->_dataSource['ORDERBY']);
			}
		}
		// 3) ordena��o fixa definida na especifica��o XML da consulta
		if (!empty($this->_dataSource['ORDERBY']))
			$orderMembers[] = $this->_dataSource['ORDERBY'];
		return (!empty($orderMembers) ? implode(',', $orderMembers) : NULL);
	}

	//!-----------------------------------------------------------------
	// @function	Report::_orderTypeIcon
	// @desc		Retorna o nome da imagem de acordo com a orienta��o da ordena��o
	// @access		private
	// @return		string Nome do �cone de ordena��o
	//!-----------------------------------------------------------------
	function _orderTypeIcon() {
		switch ($this->_orderType) {
			case 'a' : return $this->icons['orderasc'];
			case 'd' : return $this->icons['orderdesc'];
			default : return $this->icons['orderasc'];
		}
	}

	//!-----------------------------------------------------------------
	// @function	Report::_loadGlobalSettings
	// @desc		Define op��es de pagina��o, estilo, �cones e outras
	//				op��es a partir	das configura��es globais, se existentes
	// @param		settings array	Conjunto de configura��es globais
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _loadGlobalSettings($settings) {
		(@$settings['EMPTYTEMPLATE'] === FALSE) && ($this->emptyTemplate['disabled'] = TRUE);
		(isset($settings['EMPTYBLOCK'])) && ($this->emptyBlock = $settings['EMPTYBLOCK']);
		$pagination = @$settings['PAGINATION'];
		if (is_array($pagination)) {
			(isset($pagination['STYLE']) && defined($pagination['STYLE'])) && ($this->pagination['style'][0] = constant($pagination['STYLE']));
			(isset($pagination['PAGESIZE']) && $pagination['PAGESIZE'] > 0) && (parent::setPageSize($pagination['PAGESIZE']));
			(isset($pagination['VISIBLEPAGES']) && $pagination['VISIBLEPAGES'] > 0) && ($this->pagination['visiblePages'] = $pagination['VISIBLEPAGES']);
			if (is_array($pagination['PARAMS'])) {
				foreach ($pagination['PARAMS'] as $name => $value)
					$this->pagination['style'][1][$name] = $value;
			}
		}
		foreach (TypeUtils::toArray(@$settings['STYLE']) as $name => $value) {
			switch ($name) {
				case 'LINK' :
				case 'FILTER' :
				case 'BUTTON' :
				case 'HEADER' :
				case 'TITLE' :
				case 'HELP' :
					$this->style[strtolower($name)] = $value;
					break;
				case 'ALTSTYLE' :
					$altStyle = explode(',', $value);
					if (!empty($altStyle))
						$this->style['altstyle'] = $altStyle;
					break;
				case 'HIGHLIGHT' :
					if (!empty($value)) {
						$highlight = explode(',', $value);
						$this->enableHighlight($highlight[0], @$highlight[1]);
					}
					break;
			}
		}
		foreach (TypeUtils::toArray(@$settings['ICONS']) as $name => $path)
			$this->icons[strtolower($name)] = $path;
		foreach (TypeUtils::toArray(@$settings['MASKFUNCTIONS']) as $mask => $function)
			$this->setSearchMaskFunction($mask, $function);
	}
}
?>