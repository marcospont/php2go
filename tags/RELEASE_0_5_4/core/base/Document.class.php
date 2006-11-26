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
// $Header: /www/cvsroot/php2go/core/base/Document.class.php,v 1.51 2006/10/26 04:42:10 mpont Exp $
// $Date: 2006/10/26 04:42:10 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.datetime.TimeCounter');
import('php2go.file.FileManager');
import('php2go.net.HttpRequest');
import('php2go.net.HttpResponse');
import('php2go.template.DocumentElement');
import('php2go.text.StringBuffer');
import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

// @const SCRIPT_START "1"
// Valor de posicionamento de scripts gerados dentro da tag HEAD
define('SCRIPT_START', 1);
// @const SCRIPT_END "2"
// Valor de posicionamento de scripts gerados no final da tag BODY
define('SCRIPT_END', 2);
// @const BODY_START "1"
// Valor de posicionamento de conteúdo adicionado no início da tag BODY
define('BODY_START', 1);
// @const BODY_END "2"
// Valor de posicionamento de conteúdo adicionado no final da tag BODY
define('BODY_END', 2);

//!-----------------------------------------------------------------
// @class		Document
// @desc		Responsável por gerenciar e gerar os documentos HTML
//				do sistema. Gerencia o esqueleto HTML fornecido ao
//				documento (Layout) e os elementos declarados no mesmo
//				(Elementos de documento). Controla a geração do cabeçalho
//				do documento, configurações de interface, cache, entre
//				outras funcionalidades.
// @package		php2go.base
// @extends		PHP2Go
// @uses		Db
// @uses		DocumentElement
// @uses		FileManager
// @uses		HtmlUtils
// @uses		HttpRequest
// @uses		HttpResponse
// @uses		StringBuffer
// @uses		StringUtils
// @uses		System
// @uses		TimeCounter
// @author		Marcos Pont
// @version		$Revision: 1.51 $
// @note		Exemplo de uso:
//				<pre>
//
//				$doc = new Document('page_layout.tpl');
//				$doc->setTitle('Page Title');
//				$doc->setCache(FALSE);
//				$doc->setCompression(TRUE, 9);
//				$doc->addBodyCfg(array('bgcolor'=>'#ffffff'));
//				$doc->addScript('functions.js');
//				$doc->addStyle('style.css');
//				$doc->createElement('header', 'header.tpl');
//				$menu =& $doc->createElement('menu');
//				$menu->put('menu.tpl', T_BYFILE);
//				$menu->put('ads.tpl', T_BYFILE);
//				$menu->parse();
//				... other elements ...
//				$doc->display();
//
//				</pre>
// @note		Os scripts JS libs/div.js, libs/object.js e libs/window.js
//				já são adicionados automaticamente a todo documento instanciado
//!-----------------------------------------------------------------
class Document extends PHP2Go
{
	var $docTitle;						// @var	docTitle string					Título do documento
	var $docCharset;					// @var	docCharset string				Charset do conteúdo do documento
	var $docLanguage;					// @var	docLanguage string				Linguagem do documento
	var $docLayout;						// @var	docLayout string				Nome do Template base que serve como 'esqueleto' para o documento
	var $metaTagsName = array();		// @var	metaTagsName array				"array()" Conjunto de tags META do tipo NAME e seus valores
	var $metaTagsHttp = array();		// @var	metaTagsHttp array				"array()" Conjunto de tags META do tipo HTTP-EQUIV
	var $scriptFiles = array();			// @var	scriptFiles array				"array()" Conjunto de arquivos de script adicionados no documento
	var $scriptExtCode = array();		// @var	scriptExtCode string			"array()" Código de script inserido direto pelo usuário
	var $onLoadCode = array();			// @var onLoadCode array				"array()" Conjunto de instruções (Javascript) a serem executadas no evento onLoad da página
	var $styles = array();				// @var	styles string					"array()" Conjunto de arquivos de folha de estilo adicionados no documento
	var $importedStyles = array();		// @var importedStyles array			"array()" Array de controle para folhas de estilo CSS importadas
	var $styleExtCode = '';				// @var styleExtCode string				"" Código de estilo inserido diretamente pelo usuário
	var $alternateLinks = array();		// @var alternateLinks array			"array()" Conjunto de alternate links do documento
	var $extraHeaderCode = '';			// @var	extraHeaderCode string			"" Código extra a ser incluído no header do documento
	var $bodyEvents = array();			// @var	bodyEvents array				"array()" Vetor associativo contendo eventos e respectivas ações tratadas na tag BODY
	var $bodyCfg = array();				// @var	bodyCfg array					"array()" Vetor associativo contendo as configuração da tag BODY do documento
	var $extraBodyContent = array();	// @var extraBodyContent array			"array()" Código extra que será incluído no corpo do documento
	var $allowRobots = TRUE;			// @var allowRobots bool				"TRUE" Se for FALSE, inclui a tag META que previne contra a ação de robôs de pesquisa
	var $makeCache = FALSE;				// @var	makeCache bool					"FALSE" Indica a utilização ou não de headers HTTP para habilitação de cache
	var $makeCompression = FALSE;		// @var	makeCompression bool			"FALSE" Indica que o conteúdo HTML gerado deve ser compactado ao enviar para o cliente
	var $compressionLevel;				// @var	compressionLevel int			Nível de compressão aplicado ao conteúdo do documento
	var $Template;						// @var	Template Template object		Template de manipulação do layout do documento
	var $TimeCounter;					// @var TimeCounter TimeCounter object	Utilizado para calcular o tempo de geração da página
	var $elements;						// @var	elements array					Vetor de elementos declarados no layout do documento

	//!-----------------------------------------------------------------
	// @function	Document::Document
	// @desc		Construtor da classe Document. Cria uma instância
	//				da classe Template para manipulação do layout de
	//				documento e parseia seu conteúdo
	// @param		docLayout string	Nome do arquivo template base do documento
	// @param		docIncludes array	"array()" Vetor de templates de inclusão
	// @access		public
	//!-----------------------------------------------------------------
	function Document($docLayout, $docIncludes=array()) {
		parent::PHP2Go();
		$this->docLayout = $docLayout;
		$this->docCharset = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->docLanguage = PHP2Go::getConfigVal('LOCALE', FALSE);
		$this->docTitle = PHP2Go::getConfigVal('TITLE', FALSE);
		$this->Template = new Template($docLayout);
		if (!empty($docIncludes) && TypeUtils::isHashArray($docIncludes)) {
			foreach ($docIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
		$this->TimeCounter = new TimeCounter();
		$this->_initialize();
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	Document::__destruct
	// @desc		Destrutor da classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	Document::getTitle
	// @desc		Busca o título do documento
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getTitle() {
		return $this->docTitle;
	}

	//!-----------------------------------------------------------------
	// @function	Document::setTitle
	// @desc		Configura o título do documento a partir da variável $title
	// @param		title string		Título para o documento
	// @param		ignoreSpaces bool	"FALSE"	Ignorar espaços à esquerda e à direita do título
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTitle($title, $ignoreSpaces=FALSE) {
		if ($ignoreSpaces)
			$this->docTitle = $title;
		else
			$this->docTitle = trim($title);
	}

	//!-----------------------------------------------------------------
	// @function	Document::setTitleFromDb
	// @desc		Configura o título a partir de uma consulta SQL
	// @param		sql string				Consulta SQL para o título
	// @param		connectionId string		"NULL" ID da conexão ao BD
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTitleFromDb($sql, $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		$dbTitle = $Db->getFirstCell($sql);
		if ($dbTitle)
			$this->docTitle = $dbTitle;
	}

	//!-----------------------------------------------------------------
	// @function	Document::appendTitle
	// @desc		Concatena um valor ao título do documento
	// @param		aTitle string		Valor a ser concatenado ao título
	// @param		useSeparator bool	"TRUE"	Utilizar ou não separador com relação ao título existente
	// @param		separator string	"-"		Separador com relação ao título existente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function appendTitle($aTitle, $useSeparator=TRUE, $separator='-') {
		if ($this->docTitle == "") {
			$this->setTitle($aTitle);
		} else {
			if ($useSeparator)
				$this->docTitle .= ' ' . $separator;
			$this->docTitle .= ' ' . ltrim($aTitle);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::appendTitleFromDb
	// @desc		Concatena um valor ao título do documento a partir de uma consulta SQL
	// @param		sql string			Consulta SQL para concatenação no título do documento
	// @param		useSeparator bool	"TRUE" Utilizar ou não separador com relação ao título existente, padrão é TRUE
	// @param		separator string	"-" Separador com relação ao título existente, padrão é '-'
	// @param		connectionId string	"NULL" ID da conexão ao BD
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function appendTitleFromDb($sql, $useSeparator=TRUE, $separator='-', $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		$dbTitle = $Db->getFirstCell($sql);
		if ($dbTitle)
			$this->appendTitle($dbTitle, $useSeparator, $separator);
	}

	//!-----------------------------------------------------------------
	// @function	Document::setCache
	// @desc		Seta o flag de utilização de cache no documento
	// @param		flag bool		"TRUE"	Valor para o parâmetro de utilização de cache
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCache($flag=TRUE) {
		$this->makeCache = TypeUtils::toBoolean($flag);
	}

	//!-----------------------------------------------------------------
	// @function	Document::preventRobots
	// @desc		Indica que a página deve incluir um cabaçalho de prevenção contra robôs
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function preventRobots() {
		$this->allowRobots = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Document::getCharset
	// @desc		Retorna o conjunto de caracteres setado para o documento
	// @return		string
	// @access		public
	//!-----------------------------------------------------------------
	function getCharset() {
		return $this->docCharset;
	}

	//!-----------------------------------------------------------------
	// @function	Document::setCharset
	// @desc		Configura o conjunto de caracteres do documento
	// @param		charset string		Conjunto de caracteres. Ex: iso-8859-1, UTF-8, etc...
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCharset($charset) {
		$this->docCharset = $charset;
	}

	//!-----------------------------------------------------------------
	// @function	Document::setCompression
	// @desc		Configura o objeto para realizar compressão no conteúdo HTML do documento
	// @param		flag bool			"TRUE"	Habilitação ou desabilitação da compressão de documento
	// @param		level int			"9"		Nível de compressão, de 1 a 9. Será ignorado se $flag for TRUE
	// @note		Atualmente, a funcionalidade de compressão de documento não funciona em versões do PHP para Windows
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCompression($flag=TRUE, $level=9) {
		$this->makeCompression = TypeUtils::toBoolean($flag);
		if ($this->makeCompression)
			$this->compressionLevel = ($level >= 1 ? min($level, 9) : 9);
	}

	//!-----------------------------------------------------------------
	// @function	Document::setFocus
	// @desc		Configura o campo de formulário que deve receber foco após a geração do documento HTML
	// @note		Se o segundo parâmetro for NULL, o foco será direcionado
	//				para o primeiro campo não desabilitado do formulário
	// @param		formName string		Nome do formulário
	// @param		formField string	"NULL" Nome do campo do formulário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFocus($formName, $formField=NULL) {
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . 'form.js');
		if (empty($formField))
			$this->addOnloadCode(sprintf("Form.focusFirstField('%s');", $formName));
		else
			$this->addOnloadCode(sprintf("if (__fld = \$FF('%s', '%s')) { __fld.focus(); }", $formName, $formField));
	}

	//!-----------------------------------------------------------------
	// @function	Document::setLanguage
	// @desc		Configura a linguagem do documento HTML
	// @param		lang string			Linguagem a ser utilizada
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLanguage($lang) {
		$this->docLanguage = $lang;
	}

	//!-----------------------------------------------------------------
	// @function	Document::addScript
	// @desc		Adiciona um arquivo de script ao cabeçalho do documento
	// @param		scriptFile string	Nome do arquivo de script
	// @param		language string		"Javascript" Linguagem do script
	// @param		charset string		"NULL" Charset para o arquivo de script
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addScript($scriptFile, $language="Javascript", $charset=NULL) {
		if (!array_key_exists($scriptFile, $this->scriptFiles)) {
			$this->scriptFiles[$scriptFile] = sprintf("<script language=\"%s\" src=\"%s\" type=\"text/%s\"%s></script>\n",
				$language, $scriptFile, strtolower(preg_replace("/[^a-zA-Z]/", "", $language)), (!empty($charset) ? " charset=\"{$charset}\"" : '')
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::addScriptCode
	// @desc		Adiciona um código de script ao cabeçalho do documento
	// @param		scriptCode string	Código de script
	// @param		language string		"Javascript" Linguagem do script
	// @param		position int		"SCRIPT_START" Posição onde o código deve ficar
	// @note		A classe agrupa as funções de linguagens diferentes
	//				em estruturas separadas, para geração uma tag SCRIPT
	//				para cada linguagem no momento da construção do documento
	// @note		Além da linguagem, é possível definir a posição onde o código
	//				será exibido: a constante SCRIPT_START posiciona o código dentro
	//				da tag HEAD do documento, enquanto a constante SCRIPT_END posiciona
	//				o código no fim da tag BODY
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addScriptCode($scriptCode, $language="Javascript", $position=SCRIPT_START) {
		if ($position != SCRIPT_START && $position != SCRIPT_END)
			$position = SCRIPT_START;
		$this->scriptExtCode[$position][$language] = isset($this->scriptExtCode[$position][$language]) ? $this->scriptExtCode[$position][$language] . $scriptCode . "\n" : $scriptCode . "\n";
	}

	//!-----------------------------------------------------------------
	// @function	Document::addOnloadCode
	// @desc		Adiciona código que deve ser executado no carregamento da página
	// @param		instruction string	Instrução Javascript (uma ou mais linhas, será transformado em uma só linha no código fonte)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addOnloadCode($instruction) {
		$instruction = ltrim(preg_replace("/\s{1,}/", ' ', $instruction));
		$this->onLoadCode[] = $instruction;
	}

	//!-----------------------------------------------------------------
	// @function	Document::setShortcutIcon
	// @desc		Define o ícone do sistema, utilizado para criação de shortcuts
	//				ou identificação nos bookmarks nos navegadores
	// @param		iconUrl string		URL do ícone
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setShortcutIcon($iconUrl) {
		$this->appendHeaderContent("<link rel=\"shortcut icon\" href=\"{$iconUrl}\">");
	}

	//!-----------------------------------------------------------------
	// @function	Document::addStyle
	// @desc		Adiciona um arquivo do tipo stylesheet ao documento
	// @param		styleFile string	Arquivo de estilos CSS
	// @param		media string		"NULL" Mídia para a qual o arquivo de estilos será utilizado
	// @param		charset string		"NULL" Charset do arquivo de estilos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addStyle($styleFile, $media=NULL, $charset=NULL) {
		if (!array_key_exists($styleFile, $this->styles))
			$this->styles[$styleFile] = sprintf("<link rel=\"stylesheet\" type=\"text/css\" href=\"%s\"%s%s>\n",
				$styleFile, (!empty($media) ? " media=\"{$media}\"" : ''), (!empty($charset) ? " charset=\"{$charset}\"" : '')
			);
	}

	//!-----------------------------------------------------------------
	// @function	Document::importStyle
	// @desc		Importa um estilo CSS a partir de uma fonte externa para o documento HTML
	// @param		styleUrl string		URL onde se encontra o arquivo CSS
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function importStyle($styleUrl) {
		if (!in_array($styleUrl, $this->importedStyles)) {
			$this->importedStyles[] = $styleUrl;
			$this->styleExtCode .= sprintf("@import url(%s);\n", trim($styleUrl));
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::addStyleCode
	// @desc		Adiciona declarações de estilo explícitas,
	//				a serem inseridas dentro do cabeçalho do documento
	// @param		styleCode string	Código de definição de estilos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addStyleCode($styleCode) {
		$this->styleExtCode .= ltrim($styleCode) . "\n";
	}

	//!-----------------------------------------------------------------
	// @function	Document::addAlternateLink
	// @desc		Adiciona um alternate link ao documento
	// @param		type string			Mime-type do conteúdo do link
	// @param		linkUrl string		URL do link
	// @param		linkTitle string	Título do link
	// @note		Este método pode ser utilizado para incluir links para feeds
	//				associados a este documento (RSS, ATOM, ...)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addAlternateLink($type, $linkUrl, $linkTitle) {
		if (!array_key_exists($linkUrl, $this->alternateLinks))
			$this->alternateLinks[$linkUrl] = sprintf("<link rel=\"alternate\" type=\"%s\" href=\"%s\"%s>\n", $type, $linkUrl, (!empty($linkTitle) ? " title=\"" . $linkTitle . "\"" : ""));
	}

	//!-----------------------------------------------------------------
	// @function	Document::addMetaData
	// @desc		Adiciona uma nova tag META ao documento
	// @param		name string			Nome da meta informação
	// @param		value mixed			Valor
	// @param		httpEquiv bool		Se TRUE, indica equivalência com um header HTTP (http-equiv)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addMetaData($name, $value, $httpEquiv=FALSE) {
		if ($httpEquiv) {
			$this->metaTagsHttp[$name] = $value;
		} else {
			$name = strtoupper($name);
			$this->metaTagsName[$name] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::appendHeaderContent
	// @desc		Insere um valor extra ao cabeçalho do documento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function appendHeaderContent($value) {
		$this->extraHeaderCode .= $value . "\n";
	}

	//!-----------------------------------------------------------------
	// @function	Document::addBodyCfg
	// @desc		Configura uma propriedade da tag BODY do documento,
	//				sobrescrevendo valores anteriormente setados
	// @param		attr mixed		Nome do atributo ou vetor associativo de atributos
	// @param		value string	""	Valor para o atributo em caso de atributo único
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addBodyCfg($attr, $value="") {
		if (TypeUtils::isArray($attr)) {
			foreach($attr as $key => $value)
				$this->bodyCfg[strtoupper($key)] = $value;
		} else {
			$this->bodyCfg[strtoupper($attr)] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::attachBodyEvent
	// @desc		Adiciona um ação a um determinado evento tratado
	//				na tag BODY do documento
	// @param		event string	Nome de evento, como onLoad ou onUnload
	// @param		action string	Ação para o evento
	// @note		Use aspas simples na definição das ações dos eventos. Exemplo: "onLoad","funcao('parametro')"
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function attachBodyEvent($event, $action) {
		$action = str_replace("\"", "'", $action);
		if (substr($action, -1, 1) != ';')
			$action .= ';';
		$this->bodyEvents[$event] = (isset($this->bodyEvents[$event])) ? $this->bodyEvents[$event] . $action : $action;
	}

	//!-----------------------------------------------------------------
	// @function	Document::appendBodyContent
	// @desc		Insere um trprint de código HTML no início ou no final do corpo do documento (tag BODY)
	// @param		content string		Valor a ser incluído
	// @param		position int		"BODY_END" Início (BODY_START) ou final (BODY_END) do corpo do documento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function appendBodyContent($content, $position=BODY_END) {
		if ($position != BODY_START && $position != BODY_END)
			$position = BODY_START;
		$this->extraBodyContent[$position] = isset($this->extraBodyContent[$position]) ? $this->extraBodyContent[$position] . $content . "\n" : $content . "\n";
	}

	//!-----------------------------------------------------------------
	// @function	Document::&createElement
	// @desc		Método utilitário para criação de um DocumentElement
	//				a partir do nome, do conteúdo e do tipo de conteúdo
	// @param		elementName string	Nome do elemento, como declarado no template do documento
	// @param		elementSrc string	Conteúdo do elemento (arquivo ou string)
	// @param		srcType int			"T_BYFILE" Tipo do conteúdo
	// @return		DocumentElement object
	// @access		public
	//!-----------------------------------------------------------------
	function &createElement($elementName, $elementSrc='', $srcType=T_BYFILE) {
		if (!empty($elementSrc))
			$this->elements[$elementName] =& DocumentElement::factory($elementSrc, $srcType);
		else
			$this->elements[$elementName] =& new DocumentElement();
		return $this->elements[$elementName];
	}

	//!-----------------------------------------------------------------
	// @function	Document::assign
	// @desc		Define o valor de um elemento do documento
	// @param		elementName string	Nome do elemento
	// @param		elementValue string	Conteúdo para o elemento
	// @note		<strong>ATENÇÃO:</strong> no PHP4, não utilize este método
	//				com instâncias de objetos que irão modificar o documento
	//				dentro do método "getContent" como, por exemplo,
	//				formulários
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function assign($elementName, $elementValue) {
		$this->elements[$elementName] = $elementValue;
	}

	//!-----------------------------------------------------------------
	// @function	Document::assignByRef
	// @desc		Atribui a um elemento do documento um objeto por referência.
	//				Este objeto	deve implementar o método "getContent"
	// @param		elementName string	Nome do elemento
	// @param		&ContentObj object	Objeto a ser atribuído ao elemento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function assignByRef($elementName, &$ContentObj) {
		$this->elements[$elementName] =& $ContentObj;
	}

	//!-----------------------------------------------------------------
	// @function	Document::display
	// @desc		Envia para a tela o conteúdo do documento HTML
	// @note		Se a compressão tiver sido habilitada através da
	//				função setCompression(), o conteúdo compactado
	//				será enviado à tela e as configurações de cache
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		$this->_buildBodyContent();
		$this->_preRenderHeader(TRUE);
		$Agent =& UserAgent::getInstance();
		if ($this->makeCompression && !HttpResponse::headersSent() && !connection_aborted() && extension_loaded('zlib') && ($encoding = $Agent->matchAcceptList(array('x-gzip', 'gzip'), 'encoding'))) {
			System::setIni('zlip.output_compression', $this->compressionLevel);
			ob_start('ob_gzhandler');
			$this->_printDocumentHeader();
			$this->_printDocumentBody();
			print "\n" . PHP2Go::getLangVal('COMPRESS_USE_MSG', $encoding) . "\n";
			ob_end_flush();
		} else {
			$this->_printDocumentHeader();
			$this->_printDocumentBody();
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::getContent
	// @desc		Constrói o cabeçalho e o corpo do documento e retorna o código gerado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getContent() {
		$this->_buildBodyContent();
		$this->_preRenderHeader(FALSE);
		ob_start();
		$this->_printDocumentHeader();
		$this->_printDocumentBody();
		return ob_get_clean();
	}

	//!-----------------------------------------------------------------
	// @function	Document::toFile
	// @desc		Permite gerar o conteúdo do documento e salvá-lo em um arquivo
	// @param		fileName string		Nome do arquivo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function toFile($fileName) {
		$fp = @fopen($fileName, 'wb');
		if ($fp === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			fputs($fp, $this->getContent());
			fclose($fp);
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::_initialize
	// @desc		Inicializa o documento HTML
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initialize() {
		// inicializa as meta tags do documento
		$this->metaTagsName['TITLE'] =& $this->docTitle;
		$this->metaTagsName['AUTHOR'] = PHP2Go::getConfigVal('AUTHOR', FALSE);
		$this->metaTagsName['DESCRIPTION'] = PHP2Go::getConfigVal('DESCRIPTION', FALSE);
		$this->metaTagsName['KEYWORDS'] = PHP2Go::getConfigVal('KEYWORDS', FALSE);
		$this->metaTagsName['CATEGORY'] = PHP2Go::getConfigVal('CATEGORY', FALSE);
		$this->metaTagsName['CODE_LANGUAGE'] = 'PHP';
		$this->metaTagsName['GENERATOR'] = 'PHP2Go Web Development Framework ' . PHP2GO_VERSION;
		$this->metaTagsName['DATE_CREATION'] = PHP2Go::getConfigVal('DATE_CREATION', FALSE);
		$this->metaTagsHttp['Content-Language'] = $this->docLanguage;
		// inicializa os elementos (slots) do documento
		$elements = $this->Template->getDefinedVariables();
		$elementCount = sizeof($elements);
		if (!$elementCount) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_DOC_LAYOUT'), E_USER_ERROR, __FILE__, __LINE__);
		}
		for ($i=0; $i<$elementCount; $i++)
			$this->elements[$elements[$i]] = '';
		// adiciona as bibliotecas JS básicas
		$Conf =& Conf::getInstance();
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . 'php2go.js?locale=' . $Conf->getConfig('LANGUAGE_CODE') . '&charset=' . $Conf->getConfig('CHARSET'));
	}

	//!-----------------------------------------------------------------
	// @function	Document::_preRenderHeader
	// @desc		Executa operações antes da construção do cabeçalho da página
	// @param		display bool	"TRUE" A página será exibida ou armazenada em um buffer
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _preRenderHeader($display=TRUE) {
		if (!$this->makeCache) {
			if ($display && !headers_sent() && !$this->makeCompression) {
				@header('Expires: Tue, 1 Jan 1980 12:00:00 GMT');
				@header('Last-Modified: ', gmdate('D, d M Y H:i:s') . ' GMT');
				@header('Cache-Control: no-cache');
				@header('Pragma: no-cache');
			}
			$this->metaTagsHttp['Expires'] = 'Tue, 1 Jan 1980 12:00:00 GMT';
			$this->metaTagsHttp['Last-Modified'] = gmdate('D, d M Y H:i:s') . ' GMT';
			$this->metaTagsHttp['Cache-Control'] = 'no-cache';
			$this->metaTagsHttp['Pragma'] = 'no-cache';
		}
		if (!$this->allowRobots)
			$this->metaTagsName['ROBOTS'] = 'NOINDEX,NOFOLLOW,NOARCHIVE';
	}

	//!-----------------------------------------------------------------
	// @function	Document::_printDocumentHeader
	// @desc		Imprime o cabeçalho do documento HTML a partir
	//				dos headers, meta tags, scripts e estilos
	//				inseridos no objeto
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _printDocumentHeader() {
		print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
		print "<html>\n<head>\n";
		print sprintf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=%s\">\n", $this->docCharset);
		foreach($this->metaTagsHttp as $name => $content) {
			if (!empty($content))
				print sprintf("<meta http-equiv=\"%s\" content=\"%s\">\n", $name, htmlspecialchars($content));
		}
		foreach($this->metaTagsName as $name => $content) {
			if (!empty($content))
				print sprintf("<meta name=\"%s\" content=\"%s\">\n", $name, htmlspecialchars($content));
		}
		print "<title>{$this->docTitle}</title>\n";
		// base URL
		$baseUrl = PHP2Go::getConfigVal('BASE_URL', FALSE);
		if (!empty($baseUrl)) {
			$baseUrl = rtrim($baseUrl, '/') . '/';
			print sprintf("<base href=\"%s\">\n", $baseUrl);
		}
		print join("", array_values($this->styles));
		if (!empty($this->styleExtCode))
			print sprintf("<style type=\"text/css\">\n<!--\n%s//-->\n</style>\n", $this->styleExtCode);
		print join("", array_values($this->alternateLinks));
		print join("", array_values($this->scriptFiles));
		if (!empty($this->onLoadCode)) {
			$onLoad = "\tfunction p2gOnLoad() {\n";
			foreach ($this->onLoadCode as $instruction)
				$onLoad .= "\t\t$instruction\n";
			$onLoad .= "\t}";
			$this->addScriptCode($onLoad, 'Javascript');
			$this->attachBodyEvent('onLoad', 'p2gOnLoad();');
		}
		// scripts direcionados para a tag HEAD
		if (isset($this->scriptExtCode[SCRIPT_START])) {
			foreach($this->scriptExtCode[SCRIPT_START] as $language => $scripts) {
				if (StringUtils::right($scripts, 1) != "\n")
					$scripts .= "\n";
				print sprintf("<script language=\"%s\" type=\"text/%s\">\n<!--\n%s//-->\n</script>\n", $language, strtolower(preg_replace("/[^a-zA-Z]/", "", $language)), $scripts);
			}
		}
		print $this->extraHeaderCode;
		print "</head>\n";
	}

	//!-----------------------------------------------------------------
	// @function	Document::_printDocumentBody
	// @desc		Imprime o corpo do documento HTML, a partir dos eventos
	//				e scripts associados à tag BODY, do conteúdo do template
	//				de layout
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _printDocumentBody() {
		print "<body";
		foreach($this->bodyCfg as $attr => $value)
			print sprintf(" %s=\"%s\"", strtolower($attr), str_replace('"', '\'', $value));
		foreach($this->bodyEvents as $event => $actions)
			print sprintf(" %s=\"%s\"", $event, $actions);
		print ">\n<a id=\"php2go_top\" name=\"php2go_top\"></a>\n";
		if (!empty($this->extraBodyContent[BODY_START]))
			print $this->extraBodyContent[BODY_START];
		$this->Template->display();
		if (!empty($this->extraBodyContent[BODY_END]))
			print "\n" . $this->extraBodyContent[BODY_END];
		// scripts direcionados para o fim da tag BODY
		if (isset($this->scriptExtCode[SCRIPT_END])) {
			foreach($this->scriptExtCode[SCRIPT_END] as $language => $scripts) {
				if (StringUtils::right($scripts, 1) != "\n")
					$scripts .= "\n";
				print sprintf("\n<script language=\"%s\" type=\"text/%s\">\n<!--\n%s//-->\n</script>", $language, strtolower(preg_replace("/[^a-zA-Z]/", "", $language)), $scripts);
			}
		}
		print "\n</body>\n</html>";
		print "\n<!-- This content is powered by PHP2Go v. " . PHP2GO_VERSION . " (http://www.php2go.com.br) -->";
		$this->TimeCounter->stop();
		print sprintf("\n<!-- Timespent : %.3f -->", $this->TimeCounter->getElapsedTime());
	}

	//!-----------------------------------------------------------------
	// @function	Document::_buildBodyContent
	// @desc		Constrói o conteúdo do corpo do documento a partir
	//				do conteúdo de cada elemento armazenado no objeto
	//				através do atributo elements
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildBodyContent() {
		$elmNames = array_keys($this->elements);
		foreach ($elmNames as $name) {
			$element =& $this->elements[$name];
			if (is_object($element)) {
				if (TypeUtils::isInstanceOf($element, 'Template') && !$element->isPrepared())
					$element->parse();
				if (TypeUtils::isInstanceOf($element, 'Component'))
					$element->onPreRender();
				$this->Template->assignByRef($name, $element);
			} elseif (is_scalar($element) && !is_bool($element)) {
				$this->Template->assign($name, $element);
			}
		}
	}
}
?>
