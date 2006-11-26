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
// $Header: /www/cvsroot/php2go/core/form/field/LookupSelectionField.class.php,v 1.35 2006/10/26 04:55:14 mpont Exp $
// $Date: 2006/10/26 04:55:14 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
import('php2go.form.field.LookupField');
import('php2go.template.Template');
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		LookupSelectionField
// @desc		Esta classe monta uma estrutura de campos contendo um LOOKUPFIELD
//				origem, contendo valores disponiveis para seleção e outro para
//				armazenamento dos valores selecionados. Dois campos escondidos são
//				definidos para armazenar os valores inseridos e removidos (INSFIELD
//				e REMFIELD)
// @package		php2go.form.field
// @uses		HtmlUtils
// @uses		LookupField
// @uses		Template
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.35 $
//!-----------------------------------------------------------------
class LookupSelectionField extends FormField
{
	var $buttonImages = array();	// @var buttonImages array					"array()" Conjunto de imagens para os botões de ação
	var $listSeparator = '#';		// @var listSeparator string				"#" Caractere usado para separar os valores nos campos escondidos (adicionados/removidos)
	var $_SourceLookup;				// @var _SourceLookup LookupField object	Cria e monta o código do campo dos valores disponiveis
	var $_TargetLookup;				// @var _TargetLookup LookupField object	Cria e monta o código do campo dos valores inseridos

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::LookupSelectionField
	// @desc		Construtor da classe
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @access		public
	//!-----------------------------------------------------------------
	function LookupSelectionField(&$Form) {
		parent::FormField($Form);
		$this->htmlType = 'SELECT';
		$this->composite = TRUE;
		$this->searchable = FALSE;
		$this->customEvents = array('onAdd', 'onRemove');
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::display
	// @desc		Gera o código HTML do componente
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'lookupselectionfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('label', $this->label);
		$Tpl->assign('separator', $this->listSeparator);
		$Tpl->assign('tableWidth', $this->attributes['TABLEWIDTH']);
		$Tpl->assign('labelStyle', $this->_Form->getLabelStyle());
		$Tpl->assign('availableId', $this->_SourceLookup->getId());
		$Tpl->assign('availableLabel', $this->_SourceLookup->getLabel());
		$Tpl->assignByRef('available', $this->_SourceLookup);
		$Tpl->assign('selectedId', $this->_TargetLookup->getId());
		$Tpl->assign('selectedLabel', $this->_TargetLookup->getLabel());
		$Tpl->assignByRef('selected', $this->_TargetLookup);
		$Tpl->assign('availableCountLabel', PHP2Go::getLangVal('SEL_AVAILABLE_VALUES_LABEL'));
		$Tpl->assign('availableCount', $this->_SourceLookup->getOptionCount());
		$Tpl->assign('selectedCountLabel', PHP2Go::getLangVal('SEL_INSERTED_VALUES_LABEL'));
		$Tpl->assign('addedName', $this->attributes['INSFIELD']);
		$Tpl->assign('removedName', $this->attributes['REMFIELD']);
		$Tpl->assign('customListeners', $this->customListeners);
		for($i=0; $i<sizeof($this->attributes['BUTTONS']); $i++)
			$Tpl->assign('button' . $i, $this->attributes['BUTTONS'][$i]);
		$Tpl->display();
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::getFocusId
	// @desc		Retorna o nome da lista de itens disponíveis, que
	//				deverá receber foco quando o label do campo for clicado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFocusId() {
		return $this->_SourceLookup->getId();
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::&getSourceLookup
	// @desc		Busca o objeto LookupField que representa a lista de itens disponíveis
	// @note		Retorna NULL caso a lista não tenha sido construída
	// @return		LookupField object	Lista de itens disponíveis
	// @access		public
	//!-----------------------------------------------------------------
	function &getSourceLookup() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_SourceLookup, 'LookupField'))
			$result =& $this->_SourceLookup;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::&getTargetLookup
	// @desc		Busca o objeto LookupField que representa a lista de itens selecionados
	// @note		Retorna NULL se o objeto ainda não foi definido
	// @return		LookupField object	Lista de itens selecionados/inseridos
	// @acces		public
	//!-----------------------------------------------------------------
	function &getTargetLookup() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_TargetLookup, 'LookupField'))
			$result =& $this->_TargetLookup;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::setInsertedValuesFieldName
	// @desc		Define o nome do campo escondido que irá armazenar os valores inseridos na caixa de seleção
	// @param		insField string		Nome para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setInsertedValuesFieldName($insField) {
		if (trim($insField) != '' && $insField != $this->_SourceLookup->name && $insField != $this->_TargetLookup->name)
			$this->attributes['INSFIELD'] = $insField;
		else
			$this->attributes['INSFIELD'] = $this->id . '_inserted';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['INSFIELD']);
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::setRemovedValuesFieldName
	// @desc		Define o nome do campo escondido que irá armazenar os valores removidos da caixa de seleção
	// @param		remField string		Nome para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRemovedValuesFieldName($remField) {
		if (trim($remField) != '' && $remField != $this->_SourceLookup->name && $remField != $this->_TargetLookup->name)
			$this->attributes['REMFIELD'] = $remField;
		else
			$this->attributes['REMFIELD'] = $this->id . '_removed';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['REMFIELD']);
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::setTableWidth
	// @desc		Seta o tamanho (valor para o atributo WIDTH) da tabela
	//				construída para os campos e botões do objeto LookupSelectionField
	// @param		tableWidth mixed	Tamanho da tabela
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " width=\"" . $tableWidth . "\"";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::setButtonImages
	// @desc		Define imagens para os botões de ação
	// @param		addAll string	Imagem para o botão "adicionar todos"
	// @param		add string		Imagem para o botão "adicionar"
	// @param		rem string		Imagem para o botão "remover"
	// @param		remAll string	Imagem para o botão "remover todos"
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setButtonImages($addAll, $add, $rem, $remAll) {
		(trim($addAll) != '') && ($this->buttonImages['ADDALL'] = $addAll);
		(trim($add) != '') && ($this->buttonImages['ADD'] = $add);
		(trim($rem) != '') && ($this->buttonImages['REM'] = $rem);
		(trim($remAll) != '') && ($this->buttonImages['REMALL'] = $remAll);
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (isset($children['LOOKUPFIELD']) && TypeUtils::isArray($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'][0], 'XmlNode') &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'][1], 'XmlNode')) {
			$srcLookupChildren = $children['LOOKUPFIELD'][0]->getChildrenTagsArray();
			if (!isset($srcLookupChildren['DATASOURCE']))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_SOURCELOOKUP_DATASOURCE', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			$this->_SourceLookup = new LookupField($this->_Form, TRUE);
			$this->_SourceLookup->onLoadNode($children['LOOKUPFIELD'][0]->getAttributes(), $children['LOOKUPFIELD'][0]->getChildrenTagsArray());
			$this->_TargetLookup = new LookupField($this->_Form, TRUE);
			$this->_TargetLookup->onLoadNode($children['LOOKUPFIELD'][1]->getAttributes(), $children['LOOKUPFIELD'][1]->getChildrenTagsArray());
			// campo para valores inseridos
			$this->setInsertedValuesFieldName(@$attrs['INSFIELD']);
			// campo para valores removidos
			$this->setRemovedValuesFieldName(@$attrs['REMFIELD']);
			// largura da tabela
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// imagens para os botões de ação
			$this->setButtonImages(@$attrs['ADDALLIMG'], @$attrs['ADDIMG'], @$attrs['REMIMG'], @$attrs['REMALLIMG']);
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_LOOKUPSELECTION_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::onDataBind
	// @desc		Define o valor submetido do campo como sendo um array
	//				contendo os itens inseridos e os itens removidos
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		// define valores submetidos
		if ($this->_Form->isPosted()) {
			$inserted = HttpRequest::getVar($this->attributes['INSFIELD'], $this->_Form->formMethod);
			$removed = HttpRequest::getVar($this->attributes['REMFIELD'], $this->_Form->formMethod);
			parent::setSubmittedValue(array(
				$this->attributes['INSFIELD'] => (!empty($inserted) ? explode('#', $inserted) : array()),
				$this->attributes['REMFIELD'] => (!empty($removed) ? explode('#', $removed) : array())
			));
		}
	}

	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::onPreRender
	// @desc		Configura os botões de ação do componente e configura
	//				os atributos dos campos de seleção de origem e destino
	//				que possuem restrições
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/lookupselectionfield.js');
		$this->attributes['BUTTONS'] = array();
		$buttonMessages = PHP2Go::getLangVal('LOOKUP_SELECTION_BUTTON_TITLES');
		$imgActCode = "<button id=\"%s\" name=\"%s\" type=\"button\" title=\"%s\" onClick=\"%s.%s();\" style=\"cursor:pointer;background-color:transparent;border:none\"%s%s><img src=\"%s\" alt=\"\" border=\"0\"></button>";
		$btnActCode = "<button id=\"%s\" name=\"%s\" type=\"button\" style=\"width:30px;padding:0\" title=\"%s\" onClick=\"%s.%s();\"%s%s%s>%s</button>";
		$actHash = array(
			array('ADDALL', 'addall', 'addAll', '>>'),
			array('ADD', 'add', 'add', '>'),
			array('REM', 'rem', 'remove', '<'),
			array('REMALL', 'remall', 'removeAll', '<<')
		);
		for ($i=0; $i<sizeof($actHash); $i++) {
			if (isset($this->buttonImages[$actHash[$i][0]]))
				$this->attributes['BUTTONS'][] = sprintf($imgActCode,
					$this->id . '_' . $actHash[$i][1],
					$this->id . '_' . $actHash[$i][1],
					$buttonMessages[$actHash[$i][1]],
					$this->id . '_instance', $actHash[$i][2],
					$this->attributes['DISABLED'],
					$this->_SourceLookup->attributes['TABINDEX'],
					$this->buttonImages[$actHash[$i][0]]
				);
			else
				$this->attributes['BUTTONS'][] = sprintf($btnActCode,
					$this->id . '_' . $actHash[$i][1],
					$this->id . '_' . $actHash[$i][1],
					$buttonMessages[$actHash[$i][1]],
					$this->id . '_instance', $actHash[$i][2],
					$this->_Form->getButtonStyle(), $this->attributes['DISABLED'],
					$this->_SourceLookup->attributes['TABINDEX'], $actHash[$i][3]
				);
		}
		// configurações da lista de itens disponíveis
		$this->_SourceLookup->setDisabled($this->disabled);
		$this->_SourceLookup->setRequired(FALSE);
		$this->_SourceLookup->disableFirstOption(TRUE);
		$this->_SourceLookup->setMultiple();
		$this->_SourceLookup->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onDblClick', sprintf("%s_instance.add();", $this->id)));
		if (max(1, $this->_SourceLookup->getAttribute('INTSIZE')) < 2)
			$this->_SourceLookup->setSize(8);
		if ($this->accessKey)
			$this->_SourceLookup->setAccessKey($this->accessKey);
		$this->_SourceLookup->onPreRender();
		// configurações da lista de itens selecionados
		$this->_TargetLookup->setDisabled($this->disabled);
		$this->_TargetLookup->setRequired(FALSE);
		if (trim($this->_TargetLookup->getAttribute('FIRST')) == "")
			$this->_TargetLookup->setFirstOption(PHP2Go::getLangVal('LOOKUP_SELECTION_DEFAULT_SELFIRST'));
		$this->_TargetLookup->disableFirstOption(FALSE);
		$this->_TargetLookup->isGrouping = FALSE;
		$this->_TargetLookup->setMultiple();
		$this->_TargetLookup->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onDblClick', sprintf("%s_instance.remove();", $this->id)));
		if (max(1, $this->_TargetLookup->getAttribute('INTSIZE')) < 2)
			$this->_TargetLookup->setSize(8);
		$this->_TargetLookup->onPreRender();
	}
}
?>