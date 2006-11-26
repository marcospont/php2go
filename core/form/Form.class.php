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
// $Header: /www/cvsroot/php2go/core/form/Form.class.php,v 1.66 2006/11/21 23:24:22 mpont Exp $
// $Date: 2006/11/21 23:24:22 $

//------------------------------------------------------------------
import('php2go.form.FormButton');
import('php2go.form.FormEventListener');
import('php2go.form.FormRule');
import('php2go.form.FormSection');
import('php2go.net.HttpRequest');
import('php2go.template.Template');
import('php2go.util.HtmlUtils');
import('php2go.xml.XmlDocument');
//------------------------------------------------------------------

// @const FORM_SIGNATURE "__form_signature"
// Nome do campo escondido que � inclu�do no formul�rio contendo a assinatura do mesmo
define('FORM_SIGNATURE', '__form_signature');
// @const FORM_ERROR_FLOW	"1"
// Indica que os erros ser�o exibidos um abaixo do outro, apenas com quebra de linha como separador
define('FORM_ERROR_FLOW', 1);
// @const FORM_ERROR_BULLET_LIST	"2"
// Indica que os erros ser�o exibidos em uma lista com marcadores
define('FORM_ERROR_BULLET_LIST', 2);
// @const FORM_CLIENT_ERROR_ALERT	"1"
// Tipo de exibi��o de erros de vali��o JavaScript que utiliza um di�logo do tipo "alert"
define('FORM_CLIENT_ERROR_ALERT', 1);
// @const FORM_CLIENT_ERROR_DHTML	"2"
// Tipo de exibi��o de erros da valida��o JavaScript que utiliza DHTML
define('FORM_CLIENT_ERROR_DHTML', 2);
// @const FORM_HELP_INLINE "1"
// Tipo de exibi��o de ajuda de campos que exibe diretamente o conte�do em um elemento do tipo LABEL
define('FORM_HELP_INLINE', 1);
// @const FORM_HELP_POPUP "2"
// Tipo de exibi��o de ajuda de campos que exibe um �cone, e uma popup flutuante quando se passa o mouse sobre o �cone
define('FORM_HELP_POPUP', 2);

//!-----------------------------------------------------------------
// @class		Form
// @desc		A classe Form funciona como base para a constru��o de formul�rios
//				a partir de uma especifica��o XML dos campos, se��es e bot�es. O conte�do
//				XML � interpretado e mapeado para uma estrutura de dados, e utilizada
//				pelas classes filhas (FormBasic, FormTemplate e FormDataBind) para renderizar
//				o conte�do HTML final do formul�rio. Atrav�s da classe Form, tamb�m s�o
//				reunidas e organizadas as rotinas de valida��o e tratamento de
//				eventos nos campos e bot�es
// @package		php2go.form
// @extends		Component
// @uses		ADORecordSet
// @uses		Db
// @uses		FormButton
// @uses		FormField
// @uses		FormSection
// @uses		HttpRequest
// @uses		XmlDocument
// @author		Marcos Pont
// @version		$Revision: 1.66 $
// @note		Os formul�rios no PHP2Go aplicam valida��o sobre as informa��es fornecidas tanto
//				no cliente (JavaScript) quanto no servidor, desde que o m�todo isValid seja executado
//				(o que faz com que a cadeia de valida��es seja processada).<br><br>
// @note		Para conhecer mais sobre o formato da especifica��o XML, um arquivo DTD com as defini��es
//				� inclu�do junto com o framework (docs/dtd/). Para conhecer mais sobre a aplicabilidade de
//				cada campo, o diret�rio examples/ que acompanha o framework possui alguns exemplos de
//				utiliza��o desta classe: formbasic.example.php, formtemplate.example.php, formdatabind.example.php
//				e formservervalidation.example.php
// @note		Se estiver utilizando PHP5, n�o esque�a de incluir a declara��o XML na primeira linha do arquivo de especifica��o
//!-----------------------------------------------------------------
class Form extends Component
{
	var $formName;						// @var formName string				Nome do formul�rio
	var $formAction;					// @var formAction string			Action ou URL destino da submiss�o do formul�rio, padr�o � o script atual
	var $actionTarget;					// @var actionTarget string			Alvo da resposta da requisi��o
	var $formConstruct = FALSE;			// @var formConstruct bool			"FALSE" Indica que o formul�rio j� foi constru�do (se��es, campos, bot�es)
	var $formMethod = 'POST';			// @var formMethod string			"POST" M�todo da submiss�o da requisi��o, padr�o � POST
	var $formErrors = array();			// @var formErrors string			Mensagens de erro resultantes da valida��o do formul�rio
	var $readonly = FALSE;				// @var readonly bool				"FALSE" Indica se o formul�rio � somente para visualiza��o
	var $buttonStyle;					// @var buttonStyle string			Estilo para os bot�es do formul�rio
	var $inputStyle;					// @var inputStyle string			Estilo para os campos do formul�rio
	var $labelStyle;					// @var labelStyle string			Estilo para os r�tulos dos campos do formul�rio
	var $accessKeyHighlight = FALSE;	// @var accessKeyHighlight bool		"FALSE" Indica se o highlight das teclas de atalho nos r�tulos est� habilitado
	var $errorStyle = array();			// @var errorStyle string			Configura��es de estilo para exibi��o dos erros ocorridos no formul�rio
	var $clientErrorOptions = array();	// @var clientErrorOptions array	"array()" Configura��es para erros na valida��o executada no cliente com JavaScript
	var $helpOptions = array();			// @var helpOptions array			"array()" Configura��es para exibi��o de textos de ajuda de campos do formul�rio
	var $icons = array();				// @var icons array					"array()" Vetor de �cones e imagens utilizados pela classe
	var $requiredText = "*";			// @var requiredText string			"*" Texto utilizado para indicar um campo obrigat�rio, inserido automaticamente ao lado dos r�tulos dos campos
	var $requiredMark = TRUE;			// @var requiredMark bool			"TRUE" Habilita ou desabilita a exibi��o de marcas ao lado do r�tulo dos campos obrigat�rios
	var $requiredColor = '#ff0000';		// @var requiredColor string		"#ff0000" Padr�o de cor textual ou c�digo RGB para a marca de campo obrigat�rio
	var $sections = array();			// @var sections array				"array()" Vetor de se��es do formul�rio
	var $fields = array();				// @var fields array				"array()" Vetor de campos do formul�rio
	var $buttons = array();				// @var buttons array				"array()" Vetor de bot�es do formul�rio
	var $variables = array();			// @var variables array				"array()" Defini��es de vari�veis: valor, valor default e ordem de pesquisa
	var $submittedValues = array();		// @var submittedValues array		"array()" Vetor associativo contendo os dados submetidos, se o formul�rio foi postado
	var $postbackFields = array();		// @var postbackFields array		"array()" Conjunto de campos "postback" (exemplo: inst�ncias de EditSearchField)
	var $isPosted;						// @var isPosted bool				Armazena o estado do formul�rio: postado ou n�o postado
	var $backUrl;						// @var backUrl string				URL a ser utilizada por bot�es do tipo BACK; por padr�o, utiliza o HTTP_REFERER da p�gina
	var $validatorCode = '';			// @var validatorCode string		"" C�digo de valida��o client side
	var $beforeValidateCode = '';		// @var beforeValidateCode string	"" C�digo Javascript a ser executado antes da valida��o (transforma��es de valores)
	var $hasUpload = FALSE;				// @var hasUpload bool				"FALSE" Indica se h� um campo do tipo FILE no formul�rio
	var $hasRequired = FALSE;			// @var hasRequired bool			"FALSE" Indica se pelo menos um campo do formul�rio � obrigat�rio
	var $rootAttrs = array();			// @var rootAttrs array				"array()" Vetor de atributos da tag raiz do XML, pode ser utilizado pelo usu�rio para alguma customiza��o
	var $Document = NULL;				// @var Document Document object		"NULL" Documento HTML ao qual o formul�rio est� subordinado
	var $XmlDocument = NULL;			// @var XmlDocument XmlDocument object	"NULL" Documento XML que cont�m os dados da especifica��o do formul�rio

	//!-----------------------------------------------------------------
	// @function	Form::Form
	// @desc		Construtor da classe de ger�ncia de formul�rios
	// @param		xmlFile string				Nome do arquivo XML que especifica o formul�rio
	// @param		formName string				Nome do formul�rio
	// @param		&Document Document object	Objeto Document onde o formul�rio ser� inserido
	// @access		public
	//!-----------------------------------------------------------------
	function Form($xmlFile, $formName, &$Document) {
		parent::Component();
		// a classe Form � abstrata e n�o pode ser instanciada
		if ($this->isA('Form', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Form'), E_USER_ERROR, __FILE__, __LINE__);
		// o par�metro Document deve ser uma inst�ncia v�lida de documento
		elseif (!TypeUtils::isInstanceOf($Document, 'Document'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		else {
			// refer�ncia para o documento
			$this->Document =& $Document;
			// inicializa as propriedades
			$this->formName = $formName;
			$this->formAction = HttpRequest::basePath();
			$this->formMethod = "POST";
			$this->clientErrorOptions = array(
				'mode' => FORM_CLIENT_ERROR_ALERT
			);
			$this->helpOptions = array(
				'mode' => FORM_HELP_POPUP,
				'popup_icon' => PHP2GO_ICON_PATH . 'help.gif',
				'popup_attrs' => 'BGCOLOR,"#000000",FGCOLOR,"#ffffff"'
			);
			if ($this->isPosted() && ($savedUrl = @$_SESSION['PHP2GO_BACK_URL'][$this->formName]) !== NULL) {
				$this->backUrl = $savedUrl;
			} else {
				$this->backUrl = HttpRequest::referer();
				$_SESSION['PHP2GO_BACK_URL'][$this->formName] =& $this->backUrl;
			}
			$this->icons = array(
				'calendar' => PHP2GO_ICON_PATH . 'calendar.gif',
				'calculator' => PHP2GO_ICON_PATH . 'calculator.gif'
			);
			// interpreta o arquivo de especifica��o XML
			$this->XmlDocument = new XmlDocument();
			$this->XmlDocument->parseXml($xmlFile);
			$xmlRoot =& $this->XmlDocument->getRoot();
			$this->rootAttrs = $xmlRoot->getAttributes();
			// inicializa configura��es de apresenta��o a partir da configura��o global do PHP2Go
			$globalConf = PHP2Go::getConfigVal('FORMS', FALSE);
			if ($globalConf)
				$this->_loadGlobalSettings($globalConf);
			// processa os nodos de configura��o
			$this->_loadXmlSettings('FORM', (array)$this->rootAttrs);
			for ($i=0; $i<$xmlRoot->getChildrenCount(); $i++) {
				$node =& $xmlRoot->getChild($i);
				$tag = $node->getTag();
				if ($tag != 'VARIABLE' && $tag != 'SECTION')
					$this->_loadXmlSettings($tag, (array)$node->getAttributes());
			}
		}
		parent::registerDestructor($this, '__destruct');
	}

	//!-----------------------------------------------------------------
	// @function	FormField::__destruct
	// @desc		Destrutor do objeto
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function __destruct() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	Form::getInputStyle
	// @desc		Monta a defini��o CSS configurada para os campos
	//				em formato atributo-valor (CLASS='estilo')
	// @access		public
	// @return		string Defini��o do estilo
	// @note		A defini��o de estilos para campos s� � v�lida para
	//				alguns browsers. Se o browser do cliente n�o suportar
	//				esta funcionalidade, n�o ser� gerada a defini��o
	//!-----------------------------------------------------------------
	function getInputStyle() {
		$Agent =& UserAgent::getInstance();
		if (!empty($this->inputStyle) && $Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
			return " class=\"{$this->inputStyle}\"";
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	Form::setInputStyle
	// @desc		Configura o estilo CSS dos campos do formul�rio
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void
	// @note		O par�metro $style deve ser um estilo tal que possa
	//				ser referenciado em uma tag CLASS='estilo'
	// @see			Form::setButtonStyle
	// @see			Form::setLabelStyle
	//!-----------------------------------------------------------------
	function setInputStyle($style) {
		$this->inputStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	Form::getLabelStyle
	// @desc		Monta a defini��o CSS configurada para os r�tulos
	//				em formato atributo-valor (CLASS='estilo')
	// @access		public
	// @return		string Defini��o do estilo
	//!-----------------------------------------------------------------
	function getLabelStyle() {
		if (isset($this->labelStyle)) {
			return " class=\"{$this->labelStyle}\"";
		} else {
			return '';
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::setLabelStyle
	// @desc		Configura o estilo CSS dos r�tulos de campos do formul�rio
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void
	// @note		O par�metro $style deve ser um estilo tal que possa
	//				ser referenciado em uma tag CLASS='estilo'
	// @see			Form::setButtonStyle
	// @see			Form::setInputStyle
	//!-----------------------------------------------------------------
	function setLabelStyle($style) {
		$this->labelStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	Form::getButtonStyle
	// @desc		Monta a defini��o CSS configurada para os bot�es
	//				em formato atributo-valor (CLASS='estilo')
	// @access		public
	// @return		string Defini��o do estilo
	// @note		A defini��o de estilos para bot�es s� � v�lida para
	//				alguns browsers. Se o browser do cliente n�o suportar
	//				esta funcionalidade, n�o ser� gerada a defini��o
	//!-----------------------------------------------------------------
	function getButtonStyle() {
		$Agent =& UserAgent::getInstance();
		if (!empty($this->buttonStyle) && $Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
			return " class=\"{$this->buttonStyle}\"";
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	Form::setButtonStyle
	// @desc		Configura o estilo CSS dos bot�es
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void
	// @note		O par�metro $style deve ser um estilo tal que possa
	//				ser referenciado em uma tag CLASS='estilo'
	// @see			Form::setInputStyle
	// @see			Form::setLabelStyle
	//!-----------------------------------------------------------------
	function setButtonStyle($style) {
		$this->buttonStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	Form::getErrorStyle
	// @desc		Monta a defini��o CSS configurada para as mensagens
	//				de erro geradas pela valida��o do formul�rio
	//				em formato atributo-valor (CLASS='estilo')
	// @access		public
	// @return		string Defini��o do estilo
	//!-----------------------------------------------------------------
	function getErrorStyle() {
		if (isset($this->errorStyle['class'])) {
			return " class=\"{$this->errorStyle['class']}\"";
		} else {
			return '';
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::setErrorStyle
	// @desc		Define o estilo de apresenta��o dos erros encontrados
	//				na valida��o do formul�rio
	// @access		public
	// @param		class string		Nome do estilo CSS para as mensagens de erro
	// @param		listMode int		"FORM_ERROR_FLOW" Modo de exibi��o (ver constantes da classe)
	// @param		headerText string	"NULL" Permite customizar o  texto do cabe�alho do sum�rio de erros
	// @param		headerStyle string	"NULL" Nome do estilo CSS para o cabe�alho do sum�rio de erros
	// @return		void
	//!-----------------------------------------------------------------
	function setErrorStyle($class, $listMode=FORM_ERROR_FLOW, $headerText=NULL, $headerStyle=NULL) {
		// valida��o do tipo de listagem de erros
		if ($listMode != FORM_ERROR_FLOW && $listMode != FORM_ERROR_BULLET_LIST)
			$listMode = FORM_ERROR_FLOW;
		// mensagem customizada
		if (!TypeUtils::isNull($headerText, TRUE)) {
			if (!empty($headerText))
				$headerText = (!empty($headerStyle) ? sprintf("<div class='%s'>%s</div>", $headerStyle, $headerText) : $headerText . '<br>');
		}
		// mensagem padr�o
		else {
			$headerText = PHP2Go::getLangVal('ERR_FORM_ERRORS_SUMMARY');
			$headerText = (!empty($headerStyle) ? sprintf("<div class='%s'>%s</div>", $headerStyle, $headerText) : $headerText . '<br>');
		}
		// armazena as configura��es
		$this->errorStyle = array('class' => $class, 'list_mode' => $listMode, 'header_text' => $headerText);
	}

	//!-----------------------------------------------------------------
	// @function	Form::setHelpDisplayOptions
	// @desc		Define as op��es de apresenta��o dos textos de ajuda dos campos do formul�rio
	// @access		public
	// @param		mode int		Modo de aprensenta��o (ver constantes da classe)
	// @param		options array	"array()" Vetor de op��es adicionais
	// @return		void
	// @note		Conjunto de op��es dispon�veis:<br>
	//				popup_attrs - atributos para a popup flutuante (http://www.bosrup.com/web/overlib/?Command_Reference),<br>
	//				popup_icon - �cone de ajuda,<br>
	//				text_style - estilo para o texto de ajuda
	//!-----------------------------------------------------------------
	function setHelpDisplayOptions($mode, $options=array()) {
		if ($mode == FORM_HELP_INLINE || $mode == FORM_HELP_POPUP)
			$this->helpOptions = array_merge((array)$options, array('mode' => $mode));
	}

	//!-----------------------------------------------------------------
	// @function	Form::setAccessKeyHighlight
	// @desc		Habilita o highlight da access key dos campos, se
	//				esta for encontrada no conte�do do label
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAccessKeyHighlight($setting) {
		$this->accessKeyHighlight = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	Form::&getField
	// @desc		Busca o objeto correspondente a um determinado campo,
	//				a fim de aplicar modifica��es/customiza��es ao mesmo
	// @param		fieldPath string	Caminho do campo na �vore de elementos do formul�rio
	// @return		mixed	Objeto que representa o campo (package php2go.form.field) ou NULL se n�o existir no formul�rio
	// @access		public
	//!-----------------------------------------------------------------
	function &getField($fieldPath) {
		if (!$this->formConstruct)
			$this->processXml();
		$result = NULL;
		// o caminho completo para o caminho foi fornecido (secao.campo ou secao.subsecao.campo)
		$fieldSplitted = explode('.', $fieldPath);
		if (sizeof($fieldSplitted) > 1) {
			// busca a primeira se��o
			$sectionId = $fieldSplitted[0];
			if (!isset($this->sections[$sectionId]))
				return $result;
			$section = $this->sections[$sectionId];
			// busca subse��es se fazem parte do caminho
			for ($i=1,$s=sizeof($fieldSplitted)-1; $i<$s; $i++) {
				$section = $section->getSubSection($fieldSplitted[$i]);
				if (TypeUtils::isNull($section))
					return $result;
			}
			// busca o campo
			$result =& $section->getField($fieldSplitted[sizeof($fieldSplitted)-1]);
		// apenas o nome do campo foi fornecido
		} else {
			if (array_key_exists($fieldPath, $this->fields))
				$result =& $this->fields[$fieldPath];
		}
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	Form::&getFields
	// @desc		Retorna um vetor contendo todos os campos do formul�rio
	// @return		array Vetor de campos
	// @access		public
	//!-----------------------------------------------------------------
	function &getFields() {
		if (!$this->formConstruct)
			$this->processXml();
		return $this->fields;
	}

	//!-----------------------------------------------------------------
	// @function	Form::getFieldNames
	// @desc		Retorna um vetor contendo todos os nomes de campos do formul�rio
	// @return		array Vetor de nomes de campos
	// @access		public
	//!-----------------------------------------------------------------
	function getFieldNames() {
		if (!$this->formConstruct)
			$this->processXml();
		return array_keys($this->fields);
	}

	//!-----------------------------------------------------------------
	// @function	Form::&getButton
	// @desc		Busca o objeto FormButton correspondente a um determinado bot�o do formul�rio,
	//				a partir de seu nome (atributo NAME na especifica��o XML)
	// @param		name string		Nome do bot�o
	// @return		FormButton object
	// @access		public
	//!-----------------------------------------------------------------
	function &getButton($name) {
		if (!$this->formConstruct)
			$this->processXml();
		$result = NULL;
		if (isset($this->buttons[$name]))
			$result =& $this->buttons[$name];
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	Form::getSubmittedValues
	// @desc		Retorna o array associativo campo=>valor contendo os
	//				dados enviados na �ltima submiss�o deste formul�rio
	// @access		public
	// @return		array Valores submetidos
	//!-----------------------------------------------------------------
	function getSubmittedValues() {
		return ($this->isPosted() ? $this->submittedValues : array());
	}

	//!-----------------------------------------------------------------
	// @function	Form::getFormErrors
	// @desc		Busca o conjunto de erros ocorridos na submiss�o do formul�rio
	// @access		public
	// @param		glue string			"NULL" String a ser utilizada para separar os erros na string resultante
	// @return		mixed Erros em forma de texto, se for fornecido um separador. Do contr�rio, retorna um array
	//!-----------------------------------------------------------------
	function getFormErrors($glue=NULL) {
		if (empty($this->formErrors))
			return FALSE;
		if (!TypeUtils::isNull($glue))
			return implode($glue, $this->formErrors);
		return $this->formErrors;
	}

	//!-----------------------------------------------------------------
	// @function	Form::addErrors
	// @desc		Adiciona uma ou mais mensagens de erro resultantes
	//				de valida��es sobre os dados submetidos
	// @access		public
	// @param		errors mixed	Mensagem ou vetor de mensagens
	// @return		void
	//!-----------------------------------------------------------------
	function addErrors($errors) {
		if (TypeUtils::isArray($errors))
			$this->formErrors = array_merge($this->formErrors, $errors);
		else
			$this->formErrors[] = $errors;
	}

	//!-----------------------------------------------------------------
	// @function	Form::getSignature
	// @desc		Monta a assinatura do formul�rio, que � enviada na submiss�o
	//				como um campo escondido
	// @access		protected
	// @return		string Assinatura do formul�rio
	//!-----------------------------------------------------------------
	function getSignature() {
		return md5($this->formName);
	}

	//!-----------------------------------------------------------------
	// @function	Form::setFormMethod
	// @desc		Configura o m�todo de submiss�o do formul�rio
	// @access		public
	// @param		method    string  M�todo de submiss�o do formul�rio
	// @return		void
	// @see			Form::setFormAction
	// @see			Form::setFormActionTarget
	// @see			Form::setFormAlign
	//!-----------------------------------------------------------------
	function setFormMethod($method) {
		$method = trim($method);
		if (in_array(strtoupper($method), array('GET','POST')))
			$this->formMethod = $method;
		else
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_FORM_METHOD', array($method, $this->formName)), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	Form::setFormAction
	// @desc		Configura a URL a ser buscada na submiss�o do formul�rio
	// @access		public
	// @param		action    string  URL alvo da submiss�o do formul�rio
	// @return		void
	// @see			Form::setFormMethod
	// @see			Form::setFormActionTarget
	// @see			Form::setFormAlign
	//!-----------------------------------------------------------------
	function setFormAction($action) {
		$this->formAction = $action;
	}

	//!-----------------------------------------------------------------
	// @function	Form::setFormActionTarget
	// @desc		Configura onde a URL definida na propriedade $formAction
	//				da classe deve ser aberta. Aceita valores como '_blank',
	//				'_self', '_parent', '_top', ...
	// @access		public
	// @param		target    string  Local onde a URL alvo do formul�rio ser� aberta
	// @return		void
	// @see			Form::setFormAction
	// @see			Form::setFormMethod
	// @see			Form::setFormAlign
	//!-----------------------------------------------------------------
	function setFormActionTarget($target) {
		$this->actionTarget = $target;
	}

	//!-----------------------------------------------------------------
	// @function	Form::setBackUrl
	// @desc		Define a URL que ser� usada como alvo para todos os
	//				bot�es do tipo BACK inclu�dos neste formul�rio
	// @param		backUrl string	URL para bot�es BACK
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setBackUrl($backUrl) {
		$this->backUrl = $backUrl;
	}

	//!-----------------------------------------------------------------
	// @function	Form::setVariable
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
	// @function	Form::isPosted
	// @desc		Indica se o formul�rio foi postado, verificando o m�todo
	//				da requisi��o e a presen�a de uma vari�vel com a mesma
	//				assinatura do formul�rio
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isPosted() {
		if (!isset($this->isPosted)) {
			if (HttpRequest::method() == $this->formMethod) {
				$signature = HttpRequest::getVar(FORM_SIGNATURE);
				if (!TypeUtils::isNull($signature) && $signature == $this->getSignature())
					$this->isPosted = TRUE;
				else
					$this->isPosted = FALSE;
			} else {
				$this->isPosted = FALSE;
			}
		}
		return $this->isPosted;
	}

	//!-----------------------------------------------------------------
	// @function	Form::isValid
	// @desc		Executa a valida��o em todos os campos do formul�rio,
	//				somente se o mesmo foi postado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if ($this->isPosted()) {
			if (!$this->formConstruct)
				$this->processXml();
			$result = TRUE;
			$keys = array_keys($this->fields);
			foreach ($keys as $name) {
				$Field =& $this->fields[$name];
				$result &= $Field->isValid();
			}
			$result = TypeUtils::toBoolean($result);
			if ($result === FALSE) {
				$this->addErrors(Validator::getErrors());
				Validator::clearErrors();
			}
			return ($result);
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Form::isReadonly
	// @desc		Indica para a classe que o formul�rio constru�do �
	//				somente para visualiza��o
	// @note		Com a utiliza��o deste m�todo, todos os campos e bot�es
	//				do formul�rio ser�o desabilitados
	// @return		void
	//!-----------------------------------------------------------------
	function isReadonly() {
    	$this->readonly = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Form::onPreRender
	// @desc		Prepara o formul�rio para renderiza��o: prepara��o
	//				dos campos e bot�es para renderiza��o, gera��o do
	//				script de valida��es
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			if (!$this->formConstruct)
				$this->processXml();
			$this->backUrl = $this->evaluateStatement($this->backUrl);
			$this->formAction = $this->evaluateStatement($this->formAction);
			$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form.js');
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::evaluateStatement
	// @desc		M�todo respons�vel pela resolu��o de vari�veis e express�es
	//				em alguns atributos de membros do formul�rio
	// @param		source string	C�digo a ser interpretado
	// @note		O elemento VARIABLE pode ser utilizado, na especifica��o XML,
	//				para definir valores padr�o e ordem de pesquisa na requisi��o
	//				para vari�veis de formul�rio
	// @return		string Statement com vari�veis dispon�veis e express�es substitu�das
	// @access		public
	//!-----------------------------------------------------------------
	function evaluateStatement($source) {
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
	// @function	Form::verifySectionId
	// @desc		Verifica a declara��o duplicada de um ID de se��o no formul�rio
	// @param		formName string		Nome do formul�rio
	// @param		sectionId string	ID de uma se��o
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function verifySectionId($formName, $sectionId) {
		static $sections;
		if (!isset($sections) || !isset($sections[$formName])) {
			$sections = array($formName => array($sectionId));
		} else {
			if (in_array($sectionId, $sections[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_SECTION', array($sectionId, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$sections[$formName][] = $sectionId;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::verifyFieldName
	// @desc		Verifica a declara��o duplicada de um nome de campo no formul�rio
	// @param		formName string	Nome do formul�rio
	// @param		fieldName string	Nome do campo a ser verificado
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function verifyFieldName($formName, $fieldName) {
		static $fields;
		if (!isset($fields) || !isset($fields[$formName])) {
			$fields = array($formName => array($fieldName));
		} else {
			if (in_array($fieldName, $fields[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_FIELD', array($fieldName, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$fields[$formName][] = $fieldName;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::verifyButtonName
	// @desc		Verifica a declara��o duplicada de um nome de bot�o no formul�rio
	// @param		formName string	Nome do formul�rio
	// @param		btnName string	Nome do bot�o a ser verificado
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function verifyButtonName($formName, $btnName) {
		static $buttons;
		if (!isset($buttons) || !isset($buttons[$formName])) {
			$buttons = array($formName => array($btnName));
		} else {
			if (in_array($btnName, $buttons[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_BUTTON', array($btnName, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$buttons[$formName][] = $btnName;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::buildScriptCode
	// @desc		Constr�i a fun��o de valida��o da submiss�o do
	//				formul�rio a partir valida��es necess�rias aos
	//				campos requeridos e campos com checagem de m�scara
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function buildScriptCode() {
		if (!empty($this->validatorCode) || !empty($this->beforeValidateCode) || array_key_exists('VALIDATEFUNC', (array)$this->rootAttrs)) {
			$instance = $this->formName . '_validator';
			$script = "\t{$instance} = new FormValidator('{$this->formName}');\n";
			// op��es do sum�rio de erros
			$summaryOptions = sprintf("%s, %s, %s, \"%s\"",
				$this->clientErrorOptions['mode'],
				(isset($this->clientErrorOptions['placeholder']) ? "\$('{$this->clientErrorOptions['placeholder']}')" : 'null'),
				(isset($this->errorStyle['list_mode']) ? $this->errorStyle['list_mode'] : FORM_ERROR_FLOW),
				(isset($this->errorStyle['header_text']) ? $this->errorStyle['header_text'] : PHP2Go::getLangVal('ERR_FORM_ERRORS_SUMMARY'))
			);
			$script .= "\t{$instance}.setSummaryOptions({$summaryOptions});\n";
			// defini��o dos validadores
			if (!empty($this->validatorCode))
				$script .= $this->validatorCode;
			// fun��es de transforma��o
			if (!empty($this->beforeValidateCode)) {
				$script .= "\t{$instance}.onBeforeValidate = function(validator, frm) {\n";
				$script .= $this->beforeValidateCode;
				$script .= "\t};\n";
			}
			// fun��o auxiliar de valida��o
			if (array_key_exists('VALIDATEFUNC', (array)$this->rootAttrs)) {
				$matches = array();
				$validateFunc = trim($this->rootAttrs['VALIDATEFUNC']);
				if (preg_match("~^(\w+)(\((.*)\))?$~", $validateFunc, $matches)) {
					if (@$matches[3])
						$script .= "\t{$instance}.onAfterValidate = function(validator) { return {$validateFunc}; };\n";
					else
						$script .= "\t{$instance}.onAfterValidate = {$matches[1]};\n";
				}
			}
			$script .= "\t{$instance}.setup();";
			$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'validator.js');
			$this->Document->addScriptCode($script, 'Javascript', SCRIPT_END);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::processXml
	// @desc		Inicia o processamento da �rvore XML a partir de
	//				sua raiz, processando se��es de formul�rio e seus
	//				bot�es
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function processXml() {
		$this->formConstruct = TRUE;
		$xmlRoot =& $this->XmlDocument->getRoot();
		if ($xmlRoot->hasChildren()) {
			$childrenCount = $xmlRoot->getChildrenCount();
			for ($i=0; $i<$childrenCount; $i++) {
				$node = $xmlRoot->getChild($i);
				if ($node->getTag() == 'VARIABLE') {
					if ($node->hasAttribute('NAME')) {
						$attrs = $node->getAttributes();
						$name = $attrs['NAME'];
						$variable = array(
							'default' => @$attrs['DEFAULT'],
							'search' => @$attrs['SEARCHORDER']
						);
						if (!isset($this->variables[$name]))
							$this->variables[$name] = $variable;
						else
							$this->variables[$name] = array_merge($this->variables[$name], $variable);
					}
				} elseif ($node->getTag() == 'SECTION') {
					if ($node->hasChildren()) {
						$FormSection =& $this->_createSection($node);
						if ($FormSection->isVisible())
							$this->sections[$FormSection->getId()] =& $FormSection;
					}
				}
			}
		}
		// processamento de campos postback
		if (!empty($this->postbackFields)) {
			foreach ($this->postbackFields as $name) {
				$Field =& $this->fields[$name];
				if (!$Field->dataBind)
					$Field->onDataBind();
			}
			import('php2go.util.service.ServiceJSRS');
			$Service =& ServiceJSRS::getInstance();
			$Service->handleRequest();
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::&_createSection
	// @desc		Processa uma se��o do formul�rio (conjunto de campos)
	// @param		xmlNode XmlNode object	Nodo que representa a se��o
	// @return		FormSection object Se��o criada
	// @access		private
	//!-----------------------------------------------------------------
	function &_createSection($xmlNode) {
		$FormSection = new FormSection($this);
		$FormSection->onLoadNode($xmlNode->getAttributes(), array());
		if ($FormSection->isVisible()) {
			$childrenCount = $xmlNode->getChildrenCount();
			for ($i=0; $i<$childrenCount; $i++) {
				$child = $xmlNode->getChild($i);
				if ($child->getName() == '#cdata-section')
					continue;
				// se��o condicional
				if ($child->getTag() == 'CONDSECTION') {
					$child->setAttribute('CONDITION', 'T');
					$this->_createSubSection($child, $FormSection);
				// grupo de bot�es
				} else if ($child->getTag() == 'BUTTONS') {
					$this->_createButtonGroup($child, $FormSection);
				// bot�o
				} else if ($child->getTag() == 'BUTTON') {
					$this->_createButton($child, $FormSection);
				// campo
				} else {
					$this->_createField($child, $FormSection);
				}
			}
		}
		return $FormSection;
	}

	//!-----------------------------------------------------------------
	// @function	Form::_createSubSection
	// @desc		Processa uma subse��o de formul�rio, que depende de uma
	//				condi��o para ser inclu�da no formul�rio
	// @param		xmlNode XmlNode object				Representa a subse��o na �rvore XML
	// @param		&parentSection FormSection object	Refer�ncia para a se��o ou subse��o superior
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _createSubSection($xmlNode, &$parentSection) {
		$parentNode =& $xmlNode->getParentNode();
		$FormSection = new FormSection($this);
		$FormSection->onLoadNode($xmlNode->getAttributes(), $parentNode->getAttributes());
		if ($FormSection->isVisible()) {
			if ($xmlNode->hasChildren()) {
				$parentSection->addChild($FormSection);
				$childrenCount = $xmlNode->getChildrenCount();
				for ($i=0; $i<$childrenCount; $i++) {
					$child = $xmlNode->getChild($i);
					if ($child->getName() == '#cdata-section')
						continue;
					// se��o condicional
					if ($child->getTag() == 'CONDSECTION') {
						$child->setAttribute('CONDITION', 'T');
						$this->_createSubSection($child, $FormSection);
					// grupo de bot�es
					} else if ($child->getTag() == 'BUTTONS') {
						$this->_createButtonGroup($child, $FormSection);
					// bot�o
					} else if ($child->getTag() == 'BUTTON') {
						$this->_createButton($child, $FormSection);
					// campo
					} else {
						$this->_createField($child, $FormSection);
					}
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::_createButtonGroup
	// @desc		Processa um grupo de bot�es, inserindo-os em uma se��o ou subse��o
	// @param		buttons XmlNode object			Grupo de bot�es de uma se��o
	// @param		&FormSection FormSection object Se��o � qual o grupo de bot�es pertence
	// @access		private
	// @return		void
	// @see			Form::_processSection
	// @see			Form::_processField
	//!-----------------------------------------------------------------
	function _createButtonGroup($buttons, &$FormSection) {
		if ($FormSection->isVisible()) {
			$buttonGroup = array();
			$childrenCount = $buttons->getChildrenCount();
			for ($i=0; $i<$childrenCount; $i++) {
				$Node = $buttons->getChild($i);
				if ($Node->getTag() == 'BUTTON') {
					$obj = new FormButton($this);
					$obj->onLoadNode($Node->getAttributes(), $Node->getChildrenTagsArray());
					$this->buttons[$obj->getName()] =& $obj;
					$buttonGroup[] =& $obj;
					unset($obj);
				}
			}
			if (!empty($buttonGroup)) {
				$FormSection->addChild($buttonGroup);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::_createButton
	// @desc		Adiciona um bot�o a uma se��o ou subse��o
	// @param		button FormButton object		Bot�o a ser inserido
	// @param		&FormSection FormSection object	Se��o � qual o bot�o pertence
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _createButton($button, &$FormSection) {
		 if ($FormSection->isVisible()) {
		 	$obj = new FormButton($this);
		 	$obj->onLoadNode($button->getAttributes(), $button->getChildrenTagsArray());
		 	$this->buttons[$obj->getName()] =& $obj;
		 	$FormSection->addChild($obj);
		 }
	}

	//!-----------------------------------------------------------------
	// @function	Form::_createField
	// @desc		Cria um objeto FormField, construindo o c�digo HTML
	//				do campo, e gera o c�digo JavaScript para as valida��es
	//				e checagens configuradas na especifica��o XML
	// @param		field XmlNode object			Objecto XmlNode referente a um campo de formul�rio
	// @param		&FormSection FormSection object	Se��o ou subse��o onde o campo est� inclu�do
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _createField($field, &$FormSection) {
		$fieldClassName = NULL;
 		if ($FormSection->isVisible()) {
			switch($field->getTag()) {
				case 'AUTOCOMPLETEFIELD' : $fieldClassName = 'AutoCompleteField'; break;
				case 'CAPTCHAFIELD' : $fieldClassName = 'CaptchaField'; break;
				case 'COLORPICKERFIELD' : $fieldClassName = 'ColorPickerField'; break;
				case 'CHECKFIELD' : $fieldClassName = 'CheckField'; break;
				case 'CHECKGROUP' : $fieldClassName = 'CheckGroup'; break;
				case 'COMBOFIELD' : $fieldClassName = 'ComboField'; break;
				case 'DATAGRID' : $fieldClassName = 'DataGrid'; break;
				case 'DATEPICKERFIELD' : $fieldClassName = 'DatePickerField'; break;
				case 'DBCHECKGROUP' : $fieldClassName = 'DbCheckGroup'; break;
				case 'DBRADIOFIELD' : $fieldClassName = 'DbRadioField'; break;
				case 'EDITFIELD' : $fieldClassName = 'EditField'; break;
				case 'EDITORFIELD' : $fieldClassName = 'EditorField'; break;
				case 'EDITSEARCHFIELD' : $fieldClassName = 'EditSearchField'; break;
				case 'EDITSELECTIONFIELD' : $fieldClassName = 'EditSelectionField'; break;
				case 'FILEFIELD' : $fieldClassName = 'FileField'; break;
				case 'HIDDENFIELD' : $fieldClassName = 'HiddenField'; break;
				case 'LOOKUPCHOICEFIELD' : $fieldClassName = 'LookupChoiceField'; break;
				case 'LOOKUPFIELD' : $fieldClassName = 'LookupField'; break;
				case 'LOOKUPSELECTIONFIELD' : $fieldClassName = 'LookupSelectionField'; break;
				case 'MEMOFIELD' : $fieldClassName = 'MemoField'; break;
				case 'MULTICOLUMNLOOKUPFIELD' : $fieldClassName = 'MultiColumnLookupField'; break;
				case 'PASSWDFIELD' : $fieldClassName = 'PasswdField'; break;
				case 'RADIOFIELD' : $fieldClassName = 'RadioField'; break;
				case 'RANGEFIELD' : $fieldClassName = 'RangeField'; break;
				case 'TEXTFIELD' : $fieldClassName = 'TextField'; break;
				default : PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_INVALID_FIELDTYPE', $field->getTag()), E_USER_ERROR, __FILE__, __LINE__); break;
			}
			if (!TypeUtils::isNull($fieldClassName)) {
				// instancia e inicializa o campo
				import("php2go.form.field.{$fieldClassName}");
				$obj = new $fieldClassName($this);
				$obj->onLoadNode($field->getAttributes(), $field->getChildrenTagsArray());
				// adiciona o campo na se��o
				$FormSection->addChild($obj);
				// adiciona o campo neste formul�rio
				$this->fields[$obj->getName()] =& $obj;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::_loadGlobalSettings
	// @desc		Define op��es de apresenta��o, configura��es de erros e ajuda
	//				a partir das configura��es globais, se existentes
	// @param		settings array	Conjunto de configura��es globais
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function _loadGlobalSettings($settings) {
		(isset($settings['SECTION_REQUIRED_TEXT'])) && $this->requiredText = $settings['SECTION_REQUIRED_TEXT'];
		(isset($settings['SECTION_REQUIRED_COLOR'])) && $this->requiredColor = $settings['SECTION_REQUIRED_COLOR'];
		(isset($settings['INPUT_STYLE'])) && $this->inputStyle = $settings['INPUT_STYLE'];
		(isset($settings['LABEL_STYLE'])) && $this->labelStyle = $settings['LABEL_STYLE'];
		(isset($settings['BUTTON_STYLE'])) && $this->buttonStyle = $settings['BUTTON_STYLE'];
		(array_key_exists('ACCESSKEY_HIGHLIGHT', $settings)) && $this->accessKeyHighlight = (bool)$settings['ACCESSKEY_HIGHLIGHT'];
		if (isset($settings['HELP_MODE'])) {
			$mode = @constant($settings['HELP_MODE']);
			if (!TypeUtils::isNull($mode))
				$this->setHelpDisplayOptions($mode, TypeUtils::toArray(@$settings['HELP_OPTIONS']));
		}
		if (isset($settings['ERRORS'])) {
			$mode = @constant($settings['ERRORS']['LIST_MODE']);
			$headerText = (isset($settings['ERRORS']['HEADER_TEXT']) ? resolveI18nEntry($settings['ERRORS']['HEADER_TEXT']) : NULL);
			$this->setErrorStyle(@$settings['ERRORS']['STYLE'], $mode, $headerText, @$settings['ERRORS']['HEADER_STYLE']);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::_loadXmlSettings
	// @desc		Define op��es de apresenta��o provenientes da especifica��o XML
	// @param		tag string		Nome do nodo
	// @param		attrs array		Atributos do nodo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function _loadXmlSettings($tag, $attrs) {
		switch ($tag) {
			case 'FORM' :
				(isset($attrs['METHOD'])) && ($this->setFormMethod($attrs['METHOD']));
				(isset($attrs['ACTION'])) && ($this->formAction = $attrs['ACTION']);
				(isset($attrs['TARGET'])) && ($this->actionTarget = $attrs['TARGET']);
				(isset($attrs['BACKURL'])) && ($this->backUrl = $attrs['BACKURL']);
				(array_key_exists('ACCESSKEYHIGHLIGHT', $attrs)) && ($this->accessKeyHighlight = resolveBooleanChoice($attrs['ACCESSKEYHIGHLIGHT']));
				break;
			case 'STYLE' :
				(isset($attrs['INPUT'])) && ($this->inputStyle = $attrs['INPUT']);
				(isset($attrs['LABEL'])) && ($this->labelStyle = $attrs['LABEL']);
				(isset($attrs['BUTTON'])) && ($this->buttonStyle = $attrs['BUTTON']);
				break;
			case 'ERRORS' :
				$mode = @constant($attrs['LISTMODE']);
				$headerText = (isset($attrs['HEADERTEXT']) ? resolveI18nEntry($attrs['HEADERTEXT']) : NULL);
				$this->setErrorStyle(@$attrs['STYLE'], $mode, $headerText, @$attrs['HEADERSTYLE']);
				break;
		}
	}
}
?>