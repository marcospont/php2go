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
// $Header: /www/cvsroot/php2go/core/form/FormTemplate.class.php,v 1.42 2006/11/21 23:24:23 mpont Exp $
// $Date: 2006/11/21 23:24:23 $

//------------------------------------------------------------------
import('php2go.form.Form');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class 		FormTemplate
// @desc 		Esta classe é uma das extensões da classe que constrói
// 				formulários que gera o código final integrando a estrutura
// 				de dados já montada pela classe pai com um template que
// 				define a disposição dos elementos
// @package		php2go.form
// @extends 	Form
// @uses 		Template
// @author 		Marcos Pont
// @version		$Revision: 1.42 $
// @note		Exemplo de uso:
//				<pre>
//
//				$form = new FormTemplate('file.xml', 'file.tpl', 'formName', $Doc);
//				$form->setFormMethod('POST');
//				$form->setInputStyle('input_style');
//				$content = $form->getContent();
//
//				</pre>
//!-----------------------------------------------------------------
class FormTemplate extends Form
{
	var $templateFile;				// @var templateFile string			Nome do arquivo template para construção do formulário
	var $Template; 					// @var Template Template object	Objeto Template para manipulação da interface do formulário
	var $errorPlaceHolder;			// @var errorPlaceHolder string		Nome da variável para exibição dos erros de validação

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::FormTemplate
	// @desc 		Construtor da classe FormTemplate. Inicializa a configuração
	// 				do formulário controlada por este objeto e cria uma instância
	// 				da classe Template para integrar com a especificação XML definida
	// 				em $xmlFile
	// @param 		xmlFile string				Arquivo XML da especificação do formulário
	// @param 		templateFile string			Arquivo template para geração da interface do formulário
	// @param 		formName string				Nome do formulário
	// @param 		&Document Document object	Objeto Document onde o formulário será inserido
	// @param		tplIncludes array			"array()" Vetor de valores para blocos de inclusão no template
	// @access 		public
	//!-----------------------------------------------------------------
	function FormTemplate($xmlFile, $templateFile, $formName, &$Document, $tplIncludes=array()) {
		parent::Form($xmlFile, $formName, $Document);
		$this->Template = new Template($templateFile);
		if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {
			foreach ($tplIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
	}

	//!-----------------------------------------------------------------
	// @function	FormTemplate::setErrorDisplayOptions
	// @desc		Define o modo de exibição dos erros na validação client-side
	// @param		serverPlaceHolder string	Variável do template para exibição dos erros de validação do servidor
	// @param		clientMode int				Modo de exibição de erros client-side
	// @param		clientContainerId string	"" ID do container (elemento HTML) para exibição dos erros client-side
	// @note		Os valores possíveis para $clientMode são FORM_CLIENT_ERROR_ALERT e FORM_CLIENT_ERROR_DHTML
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setErrorDisplayOptions($serverPlaceHolder, $clientMode, $clientContainerId='') {
		$this->errorPlaceHolder = $serverPlaceHolder;
		if ($clientMode == FORM_CLIENT_ERROR_DHTML && !empty($clientContainerId)) {
			$this->clientErrorOptions = array(
				'mode' => FORM_CLIENT_ERROR_DHTML,
				'placeholder' => $clientContainerId
			);
		} else {
			$this->clientErrorOptions = array(
				'mode' => FORM_CLIENT_ERROR_ALERT
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormTemplate::onPreRender
	// @desc		Gera todos os elementos do formulário no template:
	//				sumário de erros, seções, campos e botões
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			$this->_buildErrors();
			$sectionIds = array_keys($this->sections);
			foreach ($sectionIds as $sectionId) {
				$section =& $this->sections[$sectionId];
				$this->_buildSection($section);
			}
			$this->Template->onPreRender();
			parent::buildScriptCode();
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::getContent
	// @desc 		Constrói e retorna o código HTML do formulário
	// @return 		string Código HTML do Formulário
	// @access 		public
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		return $this->_buildFormStart() . $this->Template->getContent() . "</form>";
	}

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::display
	// @desc 		Constrói e imprime o código HTML do formulário
	// @access 		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		$this->onPreRender();
		print $this->_buildFormStart();
		$this->Template->display();
		print "</form>";
	}

	//!-----------------------------------------------------------------
	// @function	FormTemplate::_buildErrors
	// @desc		Exibe os erros resultantes de validações realizadas no formulário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildErrors() {
		$this->Template->setCurrentBlock(TP_ROOTBLOCK);
		$this->Template->assign('errorStyle', parent::getErrorStyle());
		if (isset($this->errorPlaceHolder) && ($errors = parent::getFormErrors())) {
			$mode = @$this->errorStyle['list_mode'];
			$errors = ($mode == FORM_ERROR_BULLET_LIST ? "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>" : implode("<br>", $errors));
			$this->Template->assign('errorDisplay', " style=\"display:block\"");
			$this->Template->assign($this->errorPlaceHolder, @$this->errorStyle['header_text'] . $errors);
		} else {
			$this->Template->assign('errorDisplay', " style=\"display:none\"");
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormTemplate::_buildSection
	// @desc		Aplica no template os rótulos e códigos dos campos e botões
	//				referente a suma seção do formulário
	// @param		&section FormSection object	Seção do formulário
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildSection(&$section) {
		$sectionId = $section->getId();
		if ($section->isConditional()) {
			if (!$this->Template->isBlockDefined($sectionId))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_SECTION_TPLBLOCK', array($section->getId(), $section->getId())), E_USER_ERROR, __FILE__, __LINE__);
			if ($section->isVisible()) {
				$this->Template->createBlock($sectionId);
				$this->Template->assign("$sectionId.section_" . $sectionId, $section->name);
				for ($i = 0; $i < sizeof($section->getChildren()); $i++) {
					$object =& $section->getChild($i);
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSection($object);
					} else if ($section->getChildType($i) == 'BUTTON') {
						$this->Template->assignByRef("$sectionId." . $object->getName(), $object);
					} else if ($section->getChildType($i) == 'BUTTONGROUP') {
						for ($j=0; $j<sizeof($object); $j++) {
							$button =& $object[$j];
							$this->Template->assignByRef("{$sectionId}." . $button->getName(), $button);
						}
					} else if ($section->getChildType($i) == 'FIELD') {
						$this->Template->assign("{$sectionId}.label_" . $object->getName(), $object->getLabelCode($section->attributes['REQUIRED_FLAG'], $section->attributes['REQUIRED_COLOR'], $section->attributes['REQUIRED_TEXT']));
						$this->Template->assign("{$sectionId}.help_" . $object->getName(), $object->getHelpCode());
						$this->Template->assignByRef("{$sectionId}." . $object->getName(), $object);
					}
				}
			}
		} else {
			$this->Template->assign("_ROOT.section_{$sectionId}", $section->name);
			for ($i = 0; $i < sizeof($section->getChildren()); $i++) {
				$object =& $section->getChild($i);
				if ($section->getChildType($i) == 'SECTION') {
					$this->_buildSection($object);
				} else if ($section->getChildType($i) == 'BUTTON') {
					$this->Template->assignByRef("_ROOT." . $object->getName(), $object);
				} else if ($section->getChildType($i) == 'BUTTONGROUP') {
					for ($j=0; $j<sizeof($object); $j++) {
						$button =& $object[$j];
						$this->Template->assignByRef("_ROOT." . $button->getName(), $button);
					}
				} else if ($section->getChildType($i) == 'FIELD') {
					$this->Template->assign("_ROOT.label_" . $object->getName(), $object->getLabelCode($section->attributes['REQUIRED_FLAG'], $section->attributes['REQUIRED_COLOR'], $section->attributes['REQUIRED_TEXT']));
					$this->Template->assign("_ROOT.help_" . $object->getName(), $object->getHelpCode());
					$this->Template->assignByRef("_ROOT." . $object->getName(), $object);
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::_buildFormStart
	// @desc		Gera o código HTML de definição do formulário (tag FORM
	//				e campo hidden contendo a assinatura do form)
	// @access 		private
	// @return		string
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
	// @function	FormTemplate::_loadGlobalSettings
	// @desc		Define opções de apresentação a partir das configurações globais, se existentes
	// @param		settings array	Conjunto de configurações globais
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function _loadGlobalSettings($settings) {
		parent::_loadGlobalSettings($settings);
		if (isset($settings['ERRORS']['CLIENT_MODE']) && isset($settings['ERRORS']['TEMPLATE_PLACEHOLDER'])) {
			$mode = @constant($settings['ERRORS']['CLIENT_MODE']);
			if ($mode)
				$this->setErrorDisplayOptions($settings['ERRORS']['TEMPLATE_PLACEHOLDER'], $mode, @$settings['ERRORS']['CLIENT_CONTAINER']);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormTemplate::_loadXmlSettings
	// @desc		Define opções de apresentação provenientes da especificação XML
	// @param		tag string		Nome do nodo
	// @param		attrs array		Atributos do nodo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function _loadXmlSettings($tag, $attrs) {
		parent::_loadXmlSettings($tag, $attrs);
		if ($tag == 'ERRORS')
			$this->setErrorDisplayOptions(@$attrs['TPLPLACEHOLDER'], @constant($attrs['CLIENTMODE']), @$attrs['CLIENTCONTAINER']);
	}
}
?>