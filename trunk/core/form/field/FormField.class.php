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
// $Header: /www/cvsroot/php2go/core/form/field/FormField.class.php,v 1.63 2006/11/02 19:14:40 mpont Exp $
// $Date: 2006/11/02 19:14:40 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.net.HttpRequest');
import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
import('php2go.util.Statement');
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormField
// @desc		Classe abstrata que atua como base para a construção
//				de campos de formulário a partir dos atributos definidos
//				na especificação XML
// @package		php2go.form.field
// @extends		Component
// @uses		Form
// @uses		FormEventListener
// @uses		FormRule
// @uses		HtmlUtils
// @uses		HttpRequest
// @uses		Statement
// @author		Marcos Pont
// @version		$Revision: 1.63 $
//!-----------------------------------------------------------------
class FormField extends Component
{
	var $id;							// @var id string				ID do campo
	var $name;							// @var name string				Nome do campo
	var $validationName;				// @var validationName string	Nome a ser utilizado em validações Javascript
	var $label;							// @var label string			Rótulo do campo
	var $accessKey;						// @var accessKey string		Tecla de atalho do campo
	var $value = '';					// @var value mixed				"" Valor do campo
	var $fieldTag;						// @var fieldTag string			Nome da tag no arquivo XML
	var $htmlType;						// @var htmlType string			Tipo da tag INPUT construída
	var $rules = array();				// @var rules array				"array()" Regras de igualdade, desigualdade e obrigatoriedade condicional para o campo
	var $listeners = array();			// @var listeners array			"array()" Conjunto de tratadores de eventos associados a este campo
	var $customEvents = array();		// @var customEvents array		"array()" Conjunto de eventos customizados (fora do DOM) tratados por este campo de formulário
	var $customListeners = array();		// @var customListeners array	"array()" Conjunto de tratadores de eventos custom (fora do DOM) associados a este campo
	var $search = array();				// @var search array			"array()" Configurações customizadas de pesquisa para este campo
	var $searchDefaults = array();		// @var searchDefaults array	"array()" Configurações padrão de pesquisa para este campo
	var $required = FALSE;				// @var required bool			"FALSE" Indica se o campo é obrigatório ou não
	var $disabled = NULL;				// @var disabled bool			"NULL" Indica se o campo está desabilitado
	var $child = FALSE;					// @var child bool				"FALSE" Indica que o campo é um membro de um campo composto (DataGrid, RangeField, ...)
	var $composite = FALSE;				// @var composite bool			"FALSE" Indica que é um campo composto
	var $searchable = TRUE;				// @var searchable bool			"TRUE" Se esta propriedade for FALSE, indica que o campo não é válido para um formulário de pesquisa
	var $dataBind = FALSE;				// @var dataBind bool			"FALSE" Indica se o método onDataBind já foi executado (definição de valor, resolução de expressões e variáveis)
	var $_Form = NULL;					// @var _Form Form object		Objeto Form no qual o campo será incluído

	//!-----------------------------------------------------------------
	// @function	FormField::FormField
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @param		&Form Form object		Formulário onde o campo será inserido
	// @param		child bool				"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function FormField(&$Form, $child=FALSE) {
		parent::Component();
		if ($this->isA('FormField', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'FormField'), E_USER_ERROR, __FILE__, __LINE__);
		$this->_Form =& $Form;
		$this->child = $child;
		$this->fieldTag = strtoupper(parent::getClassName());
		$this->searchDefaults = array(
			'FIELDTYPE' => $this->fieldTag,
			'OPERATOR' => 'CONTAINING',
			'DATATYPE' => 'STRING',
			'DESCSOURCE' => 'DISPLAY'
		);
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
	// @function	FormField::getId
	// @desc		Retorna o ID do campo
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getId() {
		return $this->id;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getFocusId
	// @desc		Retorna o ID do elemento com o qual o LABEL
	//				do campo deve ser associado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFocusId() {
		return $this->id;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setId
	// @desc		Define o ID do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setId($id) {
		if (!empty($id))
			$this->id = $id;
		else
			$this->id = PHP2Go::generateUniqueId(parent::getClassName());
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getName
	// @desc		Busca o nome do campo
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setName
	// @desc		Altera ou define o nome do campo
	// @param		newName string	Novo nome para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setName($newName) {
		$oldName = $this->name;
		if ($newName != '')
			$this->name = $newName;
		else
			$this->name = $this->id;
		$this->validationName = $this->name;
		$this->searchDefaults['ALIAS'] = $this->name;
		Form::verifyFieldName($this->_Form->formName, $this->name);
		if (!empty($oldName) && isset($this->_Form->fields[$oldName])) {
			unset($this->_Form->fields[$oldName]);
			$this->_Form->fields[$this->name] =& $this;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getLabel
	// @desc		Busca o rótulo do campo
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getLabel() {
		return $this->label;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getLabelCode
	// @desc		Método responsável pela construção do código HTML
	//				do rótulo do campo, incluindo indicativo de obrigatoriedade
	// @param		reqFlag bool	Exibir ou não indicativo de obrigatoriedade
	// @param		reqColor string	Cor do indicativo
	// @param		reqText string	Texto do indicativo
	// @return		string Código da tag LABEL - rótulo do campo
	// @access		public
	//!-----------------------------------------------------------------
	function getLabelCode($reqFlag, $reqColor, $reqText) {
		$UserAgent =& UserAgent::getInstance();
		$label = $this->label;
		if ($label != 'empty') {
			if ($this->accessKey && $this->_Form->accessKeyHighlight) {
				$pos = strpos(strtoupper($label), strtoupper($this->accessKey));
				if ($pos !== FALSE)
					$label = substr($label, 0, $pos) . '<u>' . $label[$pos] . '</u>' . substr($label, $pos+1);
			}
			$required = ($this->required && !$this->disabled && $reqFlag ? "<span style=\"color:{$reqColor}\">{$reqText}</span>" : '');
			if ($this->htmlType == 'SELECT' && $UserAgent->matchBrowser('ie'))
				return sprintf("<label id=\"%s\" onClick=\"var target=$('%s');if(target && !target.disabled)target.focus();\"%s>%s%s</label>",
						$this->getName() . '_label', $this->getFocusId(),
						$this->_Form->getLabelStyle(), $label, $required
				);
			else
				return sprintf("<label for=\"%s\" id=\"%s\"%s>%s%s</label>",
						$this->getFocusId(), $this->getName() . '_label',
						$this->_Form->getLabelStyle(), $label, $required
				);
		} else {
			return '';
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setLabel
	// @desc		Altera ou define o rótulo do campo
	// @param		label string		Novo rótulo para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLabel($label) {
		if (!StringUtils::isEmpty($label))
			$this->label = resolveI18nEntry($label);
		else
			$this->label = $this->name;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getValue
	// @desc		Busca o valor atribuído ao campo
	// @return		mixed Valor do campo
	// @access		public
	//!-----------------------------------------------------------------
	function getValue() {
		return $this->value;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getDisplayValue
	// @desc		Monta uma representação compreensível do valor do campo
	// @note		Este método é utilizado na montagem de uma descrição
	//				"human readable" das cláusulas de busca construídas
	//				pela classe php2go.form.SearchForm
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		return $this->value;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getSearchData
	// @desc		Retorna o conjunto de informações específicas de busca
	//				para este campo: o valor submetido e os parâmetros de configuração
	//				(operador, tipo de dado, alias)
	// @return		array Dados de busca deste campo
	// @access		public
	//!-----------------------------------------------------------------
	function getSearchData() {
		$search = array_merge($this->searchDefaults, $this->search);
		if ($this->_Form->isPosted()) {
			$operators = PHP2Go::getLangVal('OPERATORS');
			$search['VALUE'] = $this->getValue();
			$display = ($search['DESCSOURCE'] == 'DISPLAY' ? $this->getDisplayValue() : $this->getValue());
			if ($search['DATATYPE'] == 'STRING' && $search['OPERATOR'] != 'BETWEEN')
				$display = "\"{$display}\"";
			$search['DISPLAYVALUE'] = sprintf("%s %s %s", $this->getLabel(), $operators[$search['OPERATOR']], $display);
		}
		return $search;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setValue
	// @desc		Altera ou define valor para o campo
	// @param		value mixed		Valor para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setValue($value) {
		if (!$this->dataBind)
			$this->onDataBind();
		$this->value = $value;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setSubmittedValue
	// @desc		Adiciona um valor no conjunto de valores submetidos do formulário
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function setSubmittedValue($value=NULL) {
		$sv =& $this->_Form->submittedValues;
		$value = TypeUtils::ifNull($value, $this->getValue());
		if (preg_match("/([^\[]+)\[([^\]]+)\]/", $this->name, $matches)) {
			if (!isset($sv[$matches[1]]))
				$sv[$matches[1]] = array();
			$sv[$matches[1]][$matches[2]] = $value;
		} else {
			$sv[$this->name] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getFieldTag
	// @desc		Retorna o nome da tag do campo no arquivo XML
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFieldTag() {
		return $this->fieldTag;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::&getOwnerForm
	// @desc		Retorna o formulário no qual o campo está inserido
	// @return		Form object
	// @access		public
	//!-----------------------------------------------------------------
	function &getOwnerForm() {
		return $this->_Form;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getHtmlType
	// @desc		Retorna o tipo de INPUT ou elemento HTML que este campo representa
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getHtmlType() {
		return $this->htmlType;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::getHelpCode
	// @desc		Método responsável pela construção do código HTML de apresentação
	//				do texto de ajuda atrelado ao campo, proveniente do atributo HELP
	//				da especificação XML
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getHelpCode() {
		if ($this->attributes['HELP'] != '') {
			if ($this->_Form->helpOptions['mode'] == FORM_HELP_INLINE) {
				$style = (isset($this->_Form->helpOptions['text_style']) ? " class=\"{$this->_Form->helpOptions['text_style']}\"" : $this->_Form->getLabelStyle());
				return sprintf("<div id=\"%s\"%s>%s</div>",
					$this->getName() . '_help',
					$style, $this->attributes['HELP']);
			} else {
				return sprintf("<img id=\"%s\" src=\"%s\" alt=\"\" border=\"0\"%s/>",
					$this->getName() . '_help', $this->_Form->helpOptions['popup_icon'],
					' ' . HtmlUtils::overPopup($this->_Form->Document, $this->attributes['HELP'], $this->_Form->helpOptions['popup_attrs']));
			}
		}
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setHelp
	// @desc		Atribui um texto de ajuda ao campo
	// @param		help string		Texto de ajuda para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHelp($help) {
		$help = trim($help);
		if ($help != '')
			$this->attributes['HELP'] = resolveI18nEntry($help);
		else
			$this->attributes['HELP'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	FormField::isRequired
	// @desc		Consulta se o campo é ou não obrigatório
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isRequired() {
		return $this->required;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setRequired
	// @desc		Altera a obrigatoriedade do campo
	// @param		setting bool		"TRUE" Valor para a obrigatoriedade
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRequired($setting=TRUE) {
		$this->required = (bool)$setting;
		if ($this->required && !$this->_Form->hasRequired)
			$this->_Form->hasRequired = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setDisabled
	// @desc		Altera o valor do atributo que desabilita o campo
	// @param		setting bool	"TRUE" Indica desabilitação ou habilitação do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setDisabled($setting=TRUE) {
		if ($setting) {
			$this->attributes['DISABLED'] = " disabled";
			$this->disabled = TRUE;
		} else {
			$this->attributes['DISABLED'] = "";
			$this->disabled = FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setStyle
	// @desc		Define o estilo CSS do campo
	// @param		style string	Estilo CSS para o campo (classe CSS)
	// @note		Este método permite customizar o estilo de um determinado
	//				campo em relação à configuração global definida para todo
	//				o formulário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setStyle($style) {
		$style = trim($style);
		if ($style == 'empty')
			$this->attributes['STYLE'] = '';
		elseif ($style != '')
			$this->attributes['STYLE'] = " class=\"{$style}\"";
		else
			$this->attributes['STYLE'] = $this->_Form->getInputStyle();
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setAccessKey
	// @desc		Define a tecla de atalho do campo
	// @param		accessKey string	Tecla de atalho
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAccessKey($accessKey) {
		if (trim($accessKey) != '') {
			$this->attributes['ACCESSKEY'] = " accesskey=\"$accessKey\"";
			$this->accessKey = $accessKey;
		} else {
			$this->attributes['ACCESSKEY'] = '';
			$this->accessKey = '';
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormField::setTabIndex
	// @desc		Define o índice de tab order do campo
	// @param		tabIndex int		Índice para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTabIndex($tabIndex) {
		if (TypeUtils::isInteger($tabIndex))
			$this->attributes['TABINDEX'] = " tabindex=\"$tabIndex\"";
		else
			$this->attributes['TABINDEX'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	FormField::addEventListener
	// @desc		Adiciona um novo tratador de eventos no campo
	// @param		Listener FormEventListener object	Tratador de evento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addEventListener($Listener) {
		$Listener->setOwner($this);
		if ($Listener->isValid())
			$this->listeners[] =& $Listener;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::addRule
	// @desc		Adiciona uma regra de validação para o campo
	// @param		Rule FormRule object	Regra de validação
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function addRule($Rule) {
		$Rule->setOwnerField($this);
		if ($Rule->isValid())
			$this->rules[] =& $Rule;
	}

	//!-----------------------------------------------------------------
	// @function	FormField::isValid
	// @desc		Aplica as validações de obrigatoriedade e de regras no campo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if (!$this->dataBind)
			$this->onDataBind();
		$validators = array();
		if ($this->required && !$this->composite) {
			$params = array();
			$params['fieldClass'] = strtolower($this->fieldTag);
			$validators[] = array('php2go.validation.RequiredValidator', $params, NULL);
		}
		for ($i=0,$s=sizeof($this->rules); $i<$s; $i++) {
			$params = array();
			$params['rule'] =& $this->rules[$i];
			$validators[] = array('php2go.validation.RuleValidator', $params, $this->rules[$i]->getMessage());
		}
		$result = TRUE;
		foreach ($validators as $validator)
			$result &= Validator::validateField($this, $validator[0], $validator[1], $validator[2]);
		return TypeUtils::toBoolean($result);
	}

	//!-----------------------------------------------------------------
	// @function	FormField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		$isDataBind = $this->_Form->isA('FormDataBind');
		// id
		$this->setId(TypeUtils::ifNull(@$attrs['ID'], @$attrs['NAME']));
		// nome
		$this->setName(@$attrs['NAME']);
		// rótulo
		$this->setLabel(@$attrs['LABEL']);
		// armazena atributos VALUE e DEFAULT para processamento posterior
		if (isset($attrs['VALUE']))
			$this->attributes['VALUE'] = ($attrs['VALUE'] == 'empty' ? '' : $attrs['VALUE']);
		if (isset($attrs['DEFAULT']))
			$this->attributes['DEFAULT'] = ($attrs['DEFAULT'] == 'empty' ? '' : $attrs['DEFAULT']);
		// texto de ajuda
		$this->setHelp(@$attrs['HELP']);
		// classe CSS
		$this->setStyle(@$attrs['STYLE']);
		// access key
		$this->setAccessKey(@$attrs['ACCESSKEY']);
		// tab index
		$this->setTabIndex(@$attrs['TABINDEX']);
		// status
		$disabled = (resolveBooleanChoice(@$attrs['DISABLED']) || $isDataBind || $this->_Form->readonly);
		if ($disabled)
			$this->setDisabled();
		// obrigatoriedade
		$this->setRequired(resolveBooleanChoice(@$attrs['REQUIRED']));
		// tratadores de eventos
		if (isset($children['LISTENER'])) {
			$listeners = TypeUtils::toArray($children['LISTENER']);
			foreach ($listeners as $listenerNode)
				$this->addEventListener(FormEventListener::fromNode($listenerNode));
		}
		// regras de validação
		if (isset($children['RULE']) && !$this->composite) {
			$rules = TypeUtils::toArray($children['RULE']);
			foreach ($rules as $ruleNode)
				$this->addRule(FormRule::fromNode($ruleNode));
		}
		// configurações de busca, utilizadas pela classe php2go.form.SearchForm
		if (!$this->child && isset($children['SEARCH'])) {
			$this->search = TypeUtils::toArray(@$children['SEARCH']->getAttributes());
			$this->search['IGNORE'] = resolveBooleanChoice(@$this->search['IGNORE']);
		}
		// atributos de data bind
		if ($isDataBind && !$this->composite) {
			$this->attributes['DATASRC'] = " datasrc=\"#{$this->_Form->csvDbName}\"";
			$this->attributes['DATAFLD'] = " datafld=\"{$this->name}\"";
		} else {
			$this->attributes['DATASRC'] = '';
			$this->attributes['DATAFLD'] = '';
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormField::onDataBind
	// @desc		Este método é responsável por executar configurações no campo posteriores
	//				à interpretação da especificação XML
	// @note		Dentro deste método, é executada a rotina de definição do valor do campo,
	//				a partir da requisição, do escopo global ou dos atributos VALUE e DEFAULT
	// @note		No método onDataBind também são resolvidas expressões e variáveis dentro
	//				das propriedades do campo (atributos, datasource, regras de validação)
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		$this->dataBind = TRUE;
		if (!$this->composite || $this->isA('RangeField') || $this->isA('DataGrid')) {
			$magicq = (System::getIni('magic_quotes_gpc') == 1);
			if ($this->_Form->isPosted()) {
				// 1) valor submetido
				$submittedValue = HttpRequest::getVar(preg_replace("/\[\]$/", '', $this->name), $this->_Form->formMethod);
				if ($submittedValue !== NULL) {
					if (!is_array($submittedValue) && $magicq)
						$submittedValue = stripslashes($submittedValue);
					$this->setValue($submittedValue);
					$this->setSubmittedValue();
				}
				// 2) valor submetido == NULL significa valor F para checkboxes
				elseif ($this->isA('CheckField')) {
					$this->setValue('F');
					$this->setSubmittedValue();
				}
				// 3) atributo VALUE - valor estático
				elseif (isset($this->attributes['VALUE'])) {
					if (preg_match("/~[^~]+~/", $this->attributes['VALUE']))
						$this->setValue($this->_Form->evaluateStatement($this->attributes['VALUE']));
					else
						$this->setValue($this->attributes['VALUE']);
				}
				// 4) atributo DEFAULT
				elseif (isset($this->attributes['DEFAULT'])) {
					if (preg_match("/~[^~]+~/", $this->attributes['DEFAULT']))
						$this->setValue($this->_Form->evaluateStatement($this->attributes['DEFAULT']));
					else
						$this->setValue($this->attributes['DEFAULT']);
				}
			} else {
				// 1) atributo VALUE - valor estático
				if (isset($this->attributes['VALUE'])) {
					if (preg_match("/~[^~]+~/", $this->attributes['VALUE']))
						$this->setValue($this->_Form->evaluateStatement($this->attributes['VALUE']));
					else
						$this->setValue($this->attributes['VALUE']);
				} else {
					// 2) valor da requisição
					$requestValue = HttpRequest::getVar(preg_replace("/\[\]$/", '', $this->name), 'all', 'ROSGPCE');
					if ($requestValue !== NULL) {
						if (!is_array($requestValue) && $magicq)
							$requestValue = stripslashes($requestValue);
						$this->setValue($requestValue);
					}
					// 3) atributo DEFAULT
					elseif (isset($this->attributes['DEFAULT'])) {
						if (preg_match("/~[^~]+~/", $this->attributes['DEFAULT']))
							$this->setValue($this->_Form->evaluateStatement($this->attributes['DEFAULT']));
						else
							$this->setValue($this->attributes['DEFAULT']);
					}
				}
			}
		}
		if (!$this->composite) {
			for ($i=0,$s=sizeof($this->rules); $i<$s; $i++) {
				$Rule =& $this->rules[$i];
				$Rule->onDataBind();
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormField::onPreRender
	// @desc		Constrói e adiciona no formulário o código JavaScript de
	//				controle de obrigratoriedade e o código gerado pelas regras
	//				de validação associadas ao campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		if (!$this->dataBind)
			$this->onDataBind();
		// revalida a propriedade "disabled"
		if ($this->disabled === NULL) {
			if ($this->_Form->readonly)
				$this->setDisabled();
			else
				$this->setDisabled(FALSE);
		}
		// um campo desabilitado não pode ser obrigatório
		if ($this->disabled)
			$this->setRequired(FALSE);
		// adiciona script para controle de obrigatoriedade
		// os componentes RangeField e DataGrid controlam obrigatoriedade individualmente por campo
		if ($this->required && !$this->isA('RangeField') && !$this->isA('DataGrid'))
			$this->_Form->validatorCode .= sprintf("\t%s_validator.add('%s', RequiredValidator);\n", $this->_Form->formName, $this->validationName);
		// constrói e adiciona o código das regras
		if (!empty($this->rules)) {
			foreach ($this->rules as $Rule)
				$this->_Form->validatorCode .= $Rule->getScriptCode();
		}
		// executa a função de construção do atributo SCRIPT (tratadores de eventos)
		if (@$this->attributes['SCRIPT'] === NULL)
			$this->renderListeners();
	}

	//!-----------------------------------------------------------------
	// @function	FormField::renderListeners
	// @desc		Define o valor do atributo SCRIPT a partir dos tratadores
	//				de eventos definidos para o campo, bem como o código dos
	//				tratadores de eventos 'custom'
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function renderListeners() {
		$events = array();
		$custom = array();
		$this->attributes['SCRIPT'] = '';
		foreach ($this->listeners as $listener) {
			$eventName = $listener->eventName;
			if ($listener->custom) {
				if (!isset($custom[$eventName]))
					$custom[$eventName] = array();
				$custom[$eventName][] = $listener->getScriptCode();
			} else {
				if (!isset($events[$eventName]))
					$events[$eventName] = array();
				$events[$eventName][] = $listener->getScriptCode();
			}
		}
		foreach ($events as $name => $actions)
			$this->attributes['SCRIPT'] .= " {$name}=\"" . str_replace('\"', '\'', implode(';', $actions)) . ";\"";
		foreach ($custom as $name => $actions) {
			$actions = implode(';', $actions);
			$this->customListeners[$name] = "function(args) {\n\t" . $actions . ";\n}";
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormField::parseDataSource
	// @desc		Extrai as informações necessárias de um nodo DATASOURCE,
	//				utilizado para indicar fontes de dados utilizadas em
	//				campos de formulário
	// @param		&DataSource XmlNode object	Nodo DATASOURCE
	// @access		protected
	// @return		array
	//!-----------------------------------------------------------------
	function parseDataSource(&$DataSource) {
		$result = array();
		if (TypeUtils::isInstanceOf($DataSource, 'XmlNode')) {
			$elements = $DataSource->getChildrenTagsArray();
			$connectionId = $DataSource->attrs['CONNECTION'];
			if (!$connectionId)
				$connectionId = NULL;
			$result['CONNECTIONID'] = $connectionId;
			$dataSourceElements = $DataSource->getChildrenTagsArray();
			if (empty($dataSourceElements))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATASOURCE_SYNTAX', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			foreach($dataSourceElements as $name => $node) {
				if ($name == 'PROCEDURE')
					$result['CURSORNAME'] = ($node->hasAttribute('CURSORNAME') ? $node->getAttribute('CURSORNAME') : NULL);
				$result[$name] = $node->value;
			}
			if (!isset($result['PROCEDURE']) && (!isset($result['KEYFIELD']) || !isset($result['LOOKUPTABLE'])))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATASOURCE_SYNTAX', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			if (!isset($result['PROCEDURE'])) {
				if (!isset($result['DISPLAYFIELD']))
					$result['DISPLAYFIELD'] = $result['KEYFIELD'];
				if (!isset($result['CLAUSE']))
					$result['CLAUSE'] = '';
				if (!isset($result['GROUPBY']))
					$result['GROUPBY'] = '';
				if (!isset($result['ORDERBY']))
					$result['ORDERBY'] = '';
				if (!isset($this->dataSource['GROUPFIELD']))
					$this->dataSource['GROUPFIELD'] = '';
				if (!isset($this->dataSource['GROUPDISPLAY']))
					$this->dataSource['GROUPDISPLAY'] = $this->dataSource['GROUPFIELD'];
			}
		}
		return $result;
	}
}
?>