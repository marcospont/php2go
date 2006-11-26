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
// @desc 		Esta classe � uma das extens�es da classe que constr�i
// 				formul�rios que gera o c�digo final integrando a estrutura
// 				de dados j� montada pela classe pai com um template que
// 				define a disposi��o dos elementos
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
	var $templateFile;				// @var templateFile string			Nome do arquivo template para constru��o do formul�rio
	var $Template; 					// @var Template Template object	Objeto Template para manipula��o da interface do formul�rio
	var $errorPlaceHolder;			// @var errorPlaceHolder string		Nome da vari�vel para exibi��o dos erros de valida��o

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::FormTemplate
	// @desc 		Construtor da classe FormTemplate. Inicializa a configura��o
	// 				do formul�rio controlada por este objeto e cria uma inst�ncia
	// 				da classe Template para integrar com a especifica��o XML definida
	// 				em $xmlFile
	// @param 		xmlFile string				Arquivo XML da especifica��o do formul�rio
	// @param 		templateFile string			Arquivo template para gera��o da interface do formul�rio
	// @param 		formName string				Nome do formul�rio
	// @param 		&Document Document object	Objeto Document onde o formul�rio ser� inserido
	// @param		tplIncludes array			"array()" Vetor de valores para blocos de inclus�o no template
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
	// @desc		Define o modo de exibi��o dos erros na valida��o client-side
	// @param		serverPlaceHolder string	Vari�vel do template para exibi��o dos erros de valida��o do servidor
	// @param		clientMode int				Modo de exibi��o de erros client-side
	// @param		clientContainerId string	"" ID do container (elemento HTML) para exibi��o dos erros client-side
	// @note		Os valores poss�veis para $clientMode s�o FORM_CLIENT_ERROR_ALERT e FORM_CLIENT_ERROR_DHTML
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
	// @desc		Gera todos os elementos do formul�rio no template:
	//				sum�rio de erros, se��es, campos e bot�es
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
	// @desc 		Constr�i e retorna o c�digo HTML do formul�rio
	// @return 		string C�digo HTML do Formul�rio
	// @access 		public
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		return $this->_buildFormStart() . $this->Template->getContent() . "</form>";
	}

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::display
	// @desc 		Constr�i e imprime o c�digo HTML do formul�rio
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
	// @desc		Exibe os erros resultantes de valida��es realizadas no formul�rio
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
	// @desc		Aplica no template os r�tulos e c�digos dos campos e bot�es
	//				referente a suma se��o do formul�rio
	// @param		&section FormSection object	Se��o do formul�rio
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
	// @desc		Gera o c�digo HTML de defini��o do formul�rio (tag FORM
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
	// @desc		Define op��es de apresenta��o a partir das configura��es globais, se existentes
	// @param		settings array	Conjunto de configura��es globais
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
	// @desc		Define op��es de apresenta��o provenientes da especifica��o XML
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