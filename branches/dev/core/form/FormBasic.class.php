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
// $Header: /www/cvsroot/php2go/core/form/FormBasic.class.php,v 1.41 2006/11/21 23:24:23 mpont Exp $
// $Date: 2006/11/21 23:24:23 $

//------------------------------------------------------------------
import('php2go.form.Form');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormBasic
// @desc		Esta classe extende a classe Form, que interpreta a
// 				especifica��o XML do formul�rio e armazena a informa��o
//				extra�da em uma estrutura de dados, gerando uma interface
//				pr�-definida para as se��es, campos e bot�es encontrados
// @package		php2go.form
// @extends		Form
// @uses		HttpRequest
// @uses		Template
// @uses		TypeUtils
// @uses		UserAgent
// @author		Marcos Pont
// @version		$Revision: 1.41 $
// @note		Exemplo de uso:
//				<pre>
//
//				$Form = new FormBasic("file.xml", "myForm", $Doc);
//				$Form->setFormMethod("POST");
//				$Form->setFormAlign("center");
//				$Form->setFormWidth(500);
//				$Form->setFormAction("anotherpage.php");
//				$Form->setFormActionTarget("_blank");
//				$Form->setInputStyle("input_style");
//				$Form->setLabelWidth(0.35);
//				$Form->setLabelAlign("right");
//				$content = $Form->getContent();
//
//				</pre>
//!-----------------------------------------------------------------
class FormBasic extends Form
{
	var $formAlign = 'left';			// @var formAlign string			"left" Define o alinhamento do c�digo do formul�rio
	var $formWidth;						// @var formWidth string			Largura do formul�rio, expressa em n�mero de pixels
	var $labelW = 0.2; 					// @var labelW float				"0.2" Largura da coluna dos r�tulos, entre 0 e 1, em rela��o ao tamanho total da tabela do formul�rio
	var $labelAlign = 'right'; 			// @var labelAlign string			"right" Alinhamento dos r�tulos: left, right, ...
	var $fieldSetStyle;					// @var fieldSetStyle string		Estilo para os fieldsets criados para representar as sections
	var $sectionTitleStyle;				// @var sectionTitleStyle string	Estilo para os t�tulos das se��es
	var $tblCPadding = 3; 				// @var tblCPadding int				"3" Espa�amento interno dos campos em rela��o � coluna onde eles est�o inseridos
	var $tblCSpacing = 2; 				// @var tblCSpacing int				"2" Espa�amento entre as c�lulas da tabela do formul�rio
	var $_Template; 					// @var _Template Template object	Objeto Template para constru��o da interface do formul�rio

	//!-----------------------------------------------------------------
	// @function	FormBasic::FormBasic
	// @desc		Construtor da classe FormBasic. Executa o construtor
	// 				da classe pai, que gera a estrutura de dados do formul�rio,
	// 				e instancia o template de interface pr�-definida que ser�
	// 				utilizado
	// @param 		xmlFile string	Arquivo XML da especifica��o do formul�rio
	// @param 		formName string	Nome do formul�rio
	// @param 		&Document Document object	Objeto Document onde o formul�rio ser� inserido
	// @access 		public
	//!-----------------------------------------------------------------
	function FormBasic($xmlFile, $formName, &$Document) {
		parent::Form($xmlFile, $formName, $Document);
		$this->_Template = new Template(PHP2GO_TEMPLATE_PATH . "basicform.tpl");
		$this->_Template->parse();
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::getFieldsetStyle
	// @desc		Monta a defini��o CSS configurada para a tag FIELDSET das se��es
	// @return		string Defini��o do estilo
	// @access		public
	//!-----------------------------------------------------------------
	function getFieldsetStyle() {
		if (!empty($this->fieldSetStyle))
			return " class=\"{$this->fieldSetStyle}\"";
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setFieldsetStyle
	// @desc		Define o estilo CSS interno dos fieldsets criados para representar as sections do formul�rio
	// @param		style string		Nome do estilo CSS
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFieldsetStyle($style) {
		$this->fieldSetStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::getSectionTitleStyle
	// @desc		Monta a defini��o CSS configurada para os t�tulos das se��es
	// @return		string Defini��o do estilo
	// @access		public
	//!-----------------------------------------------------------------
	function getSectionTitleStyle() {
		if (!empty($this->sectionTitleStyle))
			return " class=\"{$this->sectionTitleStyle}\"";
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setSectionTitleStyle
	// @desc		Configura o estilo CSS para os t�tulos das sections
	// @param		style string		Nome do estilo CSS
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSectionTitleStyle($style) {
		$this->sectionTitleStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setFormAlign
	// @desc		Configura o alinhamento do formul�rio em rela��o ao elemento
	//				onde ele ser� inserido dentro do documento HTML
	// @param		align string		Alinhamento para o formul�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFormAlign($align) {
		$this->formAlign = $align;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setFormWidth
	// @desc		Define a largura da tabela externa ao formul�rio
	// @param		width int			Largura, expressa em n�mero de pixels
	// @note		Exemplos de valores: "100%", "95%", "500px", "600"
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFormWidth($width) {
		$this->formWidth = $width;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setLabelWidth
	// @desc		Configura o tamanho que os r�tulos do formul�rio ter�o
	// 				em propor��o ao tamanho total da tabela. Aceita valores
	// 				decimais de 0 a 1 (Exemplos: 0.2, 0.25, 0.3)
	// @param 		width float		Tamanho dos r�tulos, de 0 a 1
	// @see 		FormBasic::setLabelAlign
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLabelWidth($width) 	{
		if (TypeUtils::parseFloatPositive($width) > 1 || TypeUtils::parseFloatPositive($width) <= 0) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_VALUE_OUT_OF_BOUNDS', array("width (FormBasic::setLabelWidth)", 0, 1)), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->labelW = $width;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setLabelAlign
	// @desc		Configura o alinhamento dos r�tulos do formul�rio
	// @param		align string		Alinhamento: left, center, right
	// @see 		FormBasic::setLabelWidth
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLabelAlign($align) {
		$this->labelAlign = $align;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setFormTableProperties
	// @desc		Configura as propriedades da tabela que conter� os
	// 				campos e bot�es do formul�rio quanto ao espa�amento
	// 				entre as c�lulas e interno �s c�lulas
	// @param 		cellpadding int	Espa�amento interno das c�lulas
	// @param 		cellspacing int	Espa�amento entre as c�lulas
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFormTableProperties($cellpadding, $cellspacing) {
		$this->tblCPadding = TypeUtils::parseIntegerPositive($cellpadding);
		$this->tblCSpacing = TypeUtils::parseIntegerPositive($cellspacing);
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setErrorDisplayOptions
	// @desc		Define o modo de exibi��o dos erros client-side
	// @param		mode int	Modo de exibi��o
	// @note		Os valores poss�veis para o par�metro $mode s�o
	//				FORM_CLIENT_ERROR_ALERT e FORM_CLIENT_ERROR_DHTML
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setErrorDisplayOptions($mode) {
		if (in_array($mode, array(FORM_CLIENT_ERROR_ALERT, FORM_CLIENT_ERROR_DHTML))) {
			$this->clientErrorOptions['mode'] = $mode;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::onPreRender
	// @desc		Gera todos os elementos do formul�rio no template:
	//				se��es, campos e bot�es
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			$this->_buildFormInterface();
			$this->_Template->onPreRender();
			parent::buildScriptCode();
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::getContent
	// @desc		Constr�i e retorna o c�digo HTML do formul�rio
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		return $this->_buildFormStart() . $this->_Template->getContent() . "</form>";
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::display
	// @desc		Constr�i e imprime o c�digo HTML do formul�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		$this->onPreRender();
		print $this->_buildFormStart();
		$this->_Template->display();
		print "</form>";
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::_buildFormInterface
	// @desc		Esta fun��o constr�i a interface do formul�rio a partir
	// 				da estrutura gerada pela classe Form e de um template
	// 				pr�-definido de interface
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _buildFormInterface() {
		$this->_Template->assign('_ROOT.formWidth', (isset($this->formWidth) ? " width=\"{$this->formWidth}\"" : ''));
		$this->_Template->assign('_ROOT.formAlign', TypeUtils::ifNull($this->formAlign, 'left'));
		$this->_Template->assign('_ROOT.errorStyle', parent::getErrorStyle());
		$this->_Template->assign('_ROOT.errorTitle', @$this->errorStyle['header_text']);
		// exibe erros da valida��o server-side
		if ($errors = parent::getFormErrors()) {
			$mode = @$this->errorStyle['list_mode'];
			$errors = ($mode == FORM_ERROR_BULLET_LIST ? "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>" : implode("<br>", $errors));
			$this->_Template->assign('_ROOT.errorDisplay', " style=\"display:block\"");
			$this->_Template->assign('_ROOT.errorMessages', $errors);
		} else {
			$this->_Template->assign('_ROOT.errorDisplay', " style=\"display:none\"");
		}
		// configura exibi��o de erros client-side
		if ($this->clientErrorOptions['mode'] == FORM_CLIENT_ERROR_DHTML)
			$this->clientErrorOptions['placeholder'] = 'form_client_errors';
		// verifica se a classe deve trabalhar em modo de compatibilidade (browsers antigos)
		$Agent =& UserAgent::getInstance();
		$compatMode = ($Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')) === FALSE);
		$sectionIds = array_keys($this->sections);
		foreach ($sectionIds as $sectionId) {
			$section =& $this->sections[$sectionId];
			if ($section->isVisible() && $section->hasChildren()) {
				// cria��o do bloco
				$this->_Template->createBlock('loop_section');
				$this->_Template->assign('compatMode', $compatMode);
				$this->_Template->assign('sectionName', $section->name);
				$this->_Template->assign('sectionTitleStyle', (!empty($this->sectionTitleStyle) ? $this->getSectionTitleStyle() : parent::getLabelStyle()));
				// legenda do fielset, dependente do browser
				$this->_Template->assign('fieldsetStyle', $this->getFieldsetStyle());
				if ($compatMode)
					$this->_Template->assign('sectionTableStyle', $this->getFieldsetStyle());
				// configura��es da tabela
				$this->_Template->assign("tablePadding", $this->tblCPadding);
				$this->_Template->assign("tableSpacing", $this->tblCSpacing);
				// gera as subse��es e os campos da se��o
				$buttons = array();
				for ($i = 0; $i < sizeof($section->getChildren()); $i++) {
					$object =& $section->getChild($i);
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSubSection($object);
					} elseif ($section->getChildType($i) == 'BUTTON') {
						$this->_Template->createBlock('section_item');
						$this->_Template->assign('itemType', 'button');
						$this->_Template->assignByRef('button', $object);
					} elseif ($section->getChildType($i) == 'BUTTONGROUP') {
						$this->_buildButtonGroup($object);
					} elseif ($section->getChildType($i) == 'FIELD') {
						if ($object->getFieldTag() == 'HIDDENFIELD') {
							$this->_Template->createBlock('hidden_field');
							$this->_Template->assignByRef('field', $object);
						} else {
							$this->_Template->createBlock('section_item');
							$this->_Template->assign('itemType', 'field');
							$this->_Template->assign('labelWidth', ($this->labelW * 100) . '%');
							$this->_Template->assign('labelAlign', $this->labelAlign);
							$this->_Template->assign('label', $object->getLabelCode($section->attributes['REQUIRED_FLAG'], $section->attributes['REQUIRED_COLOR'], $section->attributes['REQUIRED_TEXT']));
							$this->_Template->assign('fieldWidth', (100 - ($this->labelW * 100)) . '%');
							$this->_Template->assignByRef('field', $object);
							$helpCode = $object->getHelpCode();
							if (!empty($helpCode)) {
								if ($this->helpOptions['mode'] == FORM_HELP_POPUP)
									$this->_Template->assign('popupHelp', "&nbsp;" . $helpCode);
								else
									$this->_Template->assign('inlineHelp', "<br>" . $helpCode);
							}
						}
					}
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::_buildSubSection
	// @desc		M�todo recursivo que constr�i as subse��es condicionais
	//				definidas na especifica��o XML do formul�rio
	// @param		&subSection FormSection object	Subse��o do formul�rio
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildSubSection(&$subSection) {
		if ($subSection->isVisible()) {
			for ($i = 0; $i < sizeof($subSection->getChildren()); $i++) {
				$object =& $subSection->getChild($i);
				if ($subSection->getChildType($i) == 'SECTION') {
					$this->_buildSubSection($object);
				} elseif ($subSection->getChildType($i) == 'BUTTON') {
					$this->_Template->createBlock('section_item');
					$this->_Template->assign('itemType', 'button');
					$this->_Template->assignByRef('button', $object);
				} elseif ($subSection->getChildType($i) == 'BUTTONGROUP') {
					$this->_buildButtonGroup($object);
				} else {
					if ($object->getFieldTag() == 'HIDDENFIELD') {
						$this->_Template->createBlock('hidden_field');
						$this->_Template->assignByRef('field', $object);
					} else {
						$this->_Template->createBlock('section_item');
						$this->_Template->assign('itemType', 'field');
						$this->_Template->assign('labelWidth', ($this->labelW * 100) . '%');
						$this->_Template->assign('labelAlign', $this->labelAlign);
						$this->_Template->assign('label', $object->getLabelCode($subSection->attributes['REQUIRED_FLAG'], $subSection->attributes['REQUIRED_COLOR'], $subSection->attributes['REQUIRED_TEXT']));
						$this->_Template->assign('fieldWidth', (100 - ($this->labelW * 100)) . '%');
						$this->_Template->assignByRef('field', $object);
						$helpCode = $object->getHelpCode();
						if (!empty($helpCode)) {
							if ($this->helpOptions['mode'] == FORM_HELP_POPUP)
								$this->_Template->assign('popupHelp', "&nbsp;" . $helpCode);
							else
								$this->_Template->assign('inlineHelp', "<br>" . $helpCode);
						}
					}
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::_buildButtonGroup
	// @desc		Constr�i um grupo de bot�es (tag BUTTONS declarada no arquivo XML)
	// @param		&buttonGroup array	Vetor de objetos do tipo FormButton
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildButtonGroup(&$buttonGroup) {
		$this->_Template->createBlock('section_item');
		$this->_Template->assign('itemType', 'button_group');
		if (sizeof($buttonGroup) > 0) {
			for ($j=0,$s=sizeof($buttonGroup); $j<$s; $j++) {
				$this->_Template->createBlock('loop_button_group');
				$this->_Template->assign('btnW', round(100 / sizeof($buttonGroup)) . '%');
				$this->_Template->assignByRef('button', $buttonGroup[$j]);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::_buildFormStart
	// @desc		Gera o c�digo HTML de defini��o do formul�rio (tag FORM
	//				e campo hidden contendo a assinatura do form)
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildFormStart() {
		$target = (isset($this->actionTarget) ? " target=\"" . $this->actionTarget . "\"" : '');
		$enctype = ($this->hasUpload ? " enctype=\"multipart/form-data\"" : '');
		$signature = sprintf("\n<input type=\"hidden\" id=\"%s_signature\" name=\"%s\" value=\"%s\">", $this->formName, FORM_SIGNATURE, parent::getSignature());
		return sprintf("<form id=\"%s\" name=\"%s\" action=\"%s\" method=\"%s\" style=\"display:inline\"%s%s>%s\n",
			$this->formName, $this->formName, $this->formAction,
			$this->formMethod, $target, $enctype, $signature
		);
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::_loadGlobalSettings
	// @desc		Define op��es de apresenta��o do formul�rio a partir das
	//				configura��es globais, se existentes
	// @param		settings array	Conjunto de configura��es globais
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function _loadGlobalSettings($settings) {
		parent::_loadGlobalSettings($settings);
		if (isset($settings['ERRORS']['CLIENT_MODE'])) {
			$mode = @constant($settings['ERRORS']['CLIENT_MODE']);
			if ($mode)
				$this->setErrorDisplayOptions($mode);
		}
		if (isset($settings['BASIC'])) {
			$basic = $settings['BASIC'];
			(isset($basic['FIELDSET_STYLE'])) && $this->setFieldsetStyle($basic['FIELDSET_STYLE']);
			(isset($basic['SECTION_TITLE_STYLE'])) && $this->setSectionTitleStyle($basic['SECTION_TITLE_STYLE']);
			(isset($basic['ALIGN'])) && $this->setFormAlign($basic['ALIGN']);
			(isset($basic['WIDTH'])) && $this->setFormWidth($basic['WIDTH']);
			(isset($basic['LABEL_ALIGN'])) && $this->setLabelAlign($basic['LABEL_ALIGN']);
			(isset($basic['LABEL_WIDTH'])) && $this->setLabelWidth($basic['LABEL_WIDTH']);
			(isset($basic['TABLE_PADDING']) && isset($basic['TABLE_SPACING'])) && $this->setFormTableProperties($basic['TABLE_PADDING'], $basic['TABLE_SPACING']);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::_loadXmlSettings
	// @desc		Define op��es de apresenta��o provenientes da especifica��o XML
	// @param		tag string		Nome do nodo
	// @param		attrs array		Atributos do nodo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function _loadXmlSettings($tag, $attrs) {
		parent::_loadXmlSettings($tag, $attrs);
		if ($tag == 'STYLE') {
			(isset($attrs['FIELDSET']))	&& ($this->fieldSetStyle = $attrs['FIELDSET']);
			(isset($attrs['SECTIONTITLE'])) && ($this->sectionTitleStyle = $attrs['SECTIONTITLE']);
			(isset($attrs['WIDTH'])) && ($this->formWidth = intval($attrs['WIDTH']));
			(isset($attrs['ALIGN']) && in_array(strtoupper($attrs['ALIGN']), array('LEFT', 'CENTER', 'RIGHT'))) && ($this->formAlign = strtolower($attrs['ALIGN']));
			(array_key_exists('TABLEPADDING', $attrs)) && ($this->tblCPadding = intavl($attrs['TABLEPADDING']));
			(array_key_exists('TABLESPACING', $attrs)) && ($this->tblCPadding = intavl($attrs['TABLESPACING']));
			(array_key_exists('LABELWIDTH', $attrs)) && ($this->labelW = floatval($attrs['LABELWIDTH']));
			(isset($attrs['LABELALIGN']) && in_array(strtoupper($attrs['LABELALIGN']), array('LEFT', 'CENTER', 'RIGHT'))) && ($this->labelAlign = strtolower($attrs['LABELALIGN']));
		} elseif ($tag == 'ERRORS') {
			$mode = @constant($attrs['CLIENTMODE']);
			if ($mode)
				$this->setErrorDisplayOptions($mode);
		}
	}
}
?>