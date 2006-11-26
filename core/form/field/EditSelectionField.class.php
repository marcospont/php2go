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
// $Header: /www/cvsroot/php2go/core/form/field/EditSelectionField.class.php,v 1.30 2006/10/26 04:55:13 mpont Exp $
// $Date: 2006/10/26 04:55:13 $

//------------------------------------------------------------------
import('php2go.form.field.EditField');
import('php2go.form.field.LookupField');
import('php2go.template.Template');
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		EditSelectionField
// @desc		Esta classe monta uma estrutura de campos contendo um EDITFIELD para digitação
//				de valores que são inseridos sem repetição em um LOOKUPFIELD. Este último pode
//				ser definido com ou sem DATASOURCE. São definidos, também, dois campos escondidos
//				(INSFIELD e REMFIELD), que armazenam os valores inseridos e removidos
// @package		php2go.form.field
// @uses		EditField
// @uses		HtmlUtils
// @uses		LookupField
// @uses		Template
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.30 $
//!-----------------------------------------------------------------
class EditSelectionField extends FormField
{
	var $buttonImages = array();	// @var buttonImages array				"array()" Vetor armazenando as imagens para os botões de ação
	var $listSeparator = '#';		// @var listSeparator string			"#" Caractere usado para separar os valores nos campos escondidos (adicionados/removidos)
	var $_EditField;				// @var _EditField EditField object		Objeto EditField que cria e monta o código do campo de edição
	var $_LookupField;				// @var _LookupField LookupField object	Objeto LookupField que cria e monta o código do campo que armazena os valores inseridos

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::EditSelectionField
	// @desc		Construtor da classe
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @access		public
	//!-----------------------------------------------------------------
	function EditSelectionField(&$Form) {
		parent::FormField($Form);
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
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'editselectionfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('label', $this->label);
		$Tpl->assign('editId', $this->_EditField->getId());
		$Tpl->assign('editLabel', $this->_EditField->getLabel());
		$Tpl->assignByRef('edit', $this->_EditField);
		$Tpl->assign('separator', $this->listSeparator);
		$Tpl->assign('tableWidth', $this->attributes['TABLEWIDTH']);
		$Tpl->assign('labelStyle', $this->_Form->getLabelStyle());
		$Tpl->assign('lookupId', $this->_LookupField->getId());
		$Tpl->assign('lookupLabel', $this->_LookupField->getLabel());
		$Tpl->assignByRef('lookup', $this->_LookupField);
		$Tpl->assign('addedName', $this->attributes['INSFIELD']);
		$Tpl->assign('removedName', $this->attributes['REMFIELD']);
		$Tpl->assign('countLabel', PHP2Go::getLangVal('SEL_INSERTED_VALUES_LABEL'));
		$Tpl->assign('customListeners', $this->customListeners);
		for ($i=0; $i<sizeof($this->attributes['BUTTONS']); $i++)
			$Tpl->assign('button' . $i, $this->attributes['BUTTONS'][$i]);
		$Tpl->display();
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::getFocusId
	// @desc		Retornao ID do campo de inserção de valores, que
	//				deverá receber foco quando o label do campo for clicado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFocusId() {
		return $this->_EditField->getId();
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::&getEditField
	// @desc		Busca o objeto EditField que representa a caixa de edição
	// @return		EditField object	Caixa de edição
	// @note		Retorna NULL se o objeto ainda não foi definido
	// @access		public
	//!-----------------------------------------------------------------
	function &getEditField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_EditField, 'EditField'))
			$result =& $this->_EditField;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::&getLookupField
	// @desc		Retorna o objecto LookupField que representa a lista de
	//				itens inseridos na estrutura do campo
	// @return		LookupField object	Lista de itens inseridos
	// @note		Retorna NULL se o objeto não foi definido
	// @access		public
	//!-----------------------------------------------------------------
	function &getLookupField() {
		$result = NULL;
		if (TypeUtils::isInstanceOf($this->_LookupField, 'LookupField'))
			$result =& $this->_LookupField;
		return $result;
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::setInsertedValuesFieldName
	// @desc		Define o nome do campo escondido que irá armazenar os valores inseridos na caixa de seleção
	// @param		insField string		Nome para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setInsertedValuesFieldName($insField) {
		if (trim($insField) != '' && $insField != $this->_EditField->name && $insField != $this->_LookupField->name)
			$this->attributes['INSFIELD'] = $insField;
		else
			$this->attributes['INSFIELD'] = $this->id . '_inserted';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['INSFIELD']);
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::setRemovedValuesFieldName
	// @desc		Define o nome do campo escondido que irá armazenar os valores removidos da caixa de seleção
	// @param		remField string		Nome para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRemovedValuesFieldName($remField) {
		if (trim($remField) != '' && $remField != $this->_EditField->name && $remField != $this->_LookupField->name)
			$this->attributes['REMFIELD'] = $remField;
		else
			$this->attributes['REMFIELD'] = $this->id . '_removed';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['REMFIELD']);
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::setTableWidth
	// @desc		Os campos do tipo EditSelection são gerados em um template
	//				pré-definido no framework. Este método permite customizar
	//				o tamanho da tabela principal deste template
	// @param		tableWidth string	Tamanho para a tabela, a ser utilizado no atributo WIDTH da tabela
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " width='" . $tableWidth . "'";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::setButtonImages
	// @desc		Define imagens para os botões de ação
	// @param		add string		Imagem para o botão "adicionar"
	// @param		rem string		Imagem para o botão "remover"
	// @param		remAll string	Imagem para o botão "remover todos"
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setButtonImages($add, $rem, $remAll) {
		(trim($add) != '') && ($this->buttonImages['ADD'] = $add);
		(trim($rem) != '') && ($this->buttonImages['REM'] = $rem);
		(trim($remAll) != '') && ($this->buttonImages['REMALL'] = $remAll);
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// verifica se a estrutura de nodos filhos está correta
		if (isset($children['EDITFIELD']) && isset($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['EDITFIELD'], 'XmlNode') &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'], 'XmlNode')) {
			// instancia os campos filhos
			$this->_EditField = new EditField($this->_Form, TRUE);
			$this->_EditField->onLoadNode($children['EDITFIELD']->getAttributes(), $children['EDITFIELD']->getChildrenTagsArray());
			$this->_LookupField = new LookupField($this->_Form, TRUE);
			$this->_LookupField->onLoadNode($children['LOOKUPFIELD']->getAttributes(), $children['LOOKUPFIELD']->getChildrenTagsArray());
			// copia o atributo disabled para os filhos
			$this->_EditField->attributes['DISABLED'] = $this->attributes['DISABLED'];
			$this->_LookupField->attributes['DISABLED'] = $this->attributes['DISABLED'];
			// campo para valores inseridos
			$this->setInsertedValuesFieldName(@$attrs['INSFIELD']);
			// campo para valores removidos
			$this->setRemovedValuesFieldName(@$attrs['REMFIELD']);
			// largura da tabela
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// imagens dos botões
			$this->setButtonImages(@$attrs['ADDIMG'], @$attrs['REMIMG'], @$attrs['REMALLIMG']);
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_EDITSELECTION_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::onDataBind
	// @desc		Transforma o valor submetido em um array contendo
	//				valores inseridos e valores removidos
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		// registra valores submetidos
		if ($this->_Form->isPosted()) {
			$inserted = HttpRequest::getVar($this->attributes['INSFIELD'], $this->_Form->formMethod);
			$removed = HttpRequest::getVar($this->attributes['REMFIELD'], $this->_Form->formMethod);
			parent::setSubmittedValue(array(
				$this->attributes['INSFIELD'] => (!empty($inserted) ? explode($this->listSeparator, $inserted) : array()),
				$this->attributes['REMFIELD'] => (!empty($removed) ? explode($this->listSeparator, $removed) : array())
			));
		}
	}

	//!-----------------------------------------------------------------
	// @function	EditSelectionField::onPreRender
	// @desc		Configura os botões de ação e algumas propriedades dos
	//				campos de valor e seleção que possuem restrições em seus
	//				valores quando usadas dentro desta classe
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/editselectionfield.js');
		$this->attributes['BUTTONS'] = array();
		$buttonMessages = PHP2Go::getLangVal('EDIT_SELECTION_BUTTON_TITLES');
		$imgActCode = "<button id=\"%s\" name=\"%s\" type=\"button\" title=\"%s\" onClick=\"%s.%s(%s);\" style=\"cursor:pointer;background-color:transparent;border:none\"%s%s><img src=\"%s\" alt=\"\" border=\"0\"></button>";
		$btnActCode = "<button id=\"%s\" name=\"%s\" type=\"button\" style=\"width:25px\" title=\"%s\" onClick=\"%s.%s(%s);\"%s%s%s> %s </button>";
		$addOptions = sprintf("{upper: %s, lower: %s, trim: %s, capitalize: %s}",
			($this->_EditField->attributes['UPPER'] == 'T' ? 'true' : 'false'),
			($this->_EditField->attributes['LOWER'] == 'T' ? 'true' : 'false'),
			($this->_EditField->attributes['AUTOTRIM'] == 'T' ? 'true' : 'false'),
			($this->_EditField->attributes['CAPITALIZE'] == 'T' ? 'true' : 'false')
		);
		$actHash = array(
			array('ADD', 'add', 'add', '+', $addOptions),
			array('REM', 'rem', 'remove', '-', ''),
			array('REMALL', 'remall', 'removeAll', 'X', '')
		);
		for ($i=0; $i<sizeof($actHash); $i++) {
			if (isset($this->buttonImages[$actHash[$i][0]]))
				$this->attributes['BUTTONS'][] = sprintf($imgActCode,
					$this->id . '_' . $actHash[$i][1],
					$this->id . '_' . $actHash[$i][1],
					$buttonMessages[$actHash[$i][1]],
					$this->id . '_instance', $actHash[$i][2],
					$actHash[$i][4], $this->attributes['DISABLED'],
					$this->_EditField->attributes['TABINDEX'],
					$this->buttonImages[$actHash[$i][0]]
				);
			else
				$this->attributes['BUTTONS'][] = sprintf($btnActCode,
					$this->id . '_' . $actHash[$i][1],
					$this->id . '_' . $actHash[$i][1],
					$buttonMessages[$actHash[$i][1]],
					$this->id . '_instance', $actHash[$i][2],
					$actHash[$i][4], $this->_Form->getButtonStyle(),
					$this->_EditField->attributes['TABINDEX'],
					$this->attributes['DISABLED'], $actHash[$i][3]
				);
		}
		// configurações da caixa de texto
		$this->_EditField->setRequired(FALSE);
		if ($this->accessKey)
			$this->_EditField->setAccessKey($this->accessKey);
		$this->_EditField->onPreRender();
		// configurações da lista de itens selecionados
		if (trim($this->_LookupField->getAttribute('FIRST')) == "")
			$this->_LookupField->setFirstOption(PHP2Go::getLangVal('LOOKUP_SELECTION_DEFAULT_SELFIRST'));
		$this->_LookupField->disableFirstOption(FALSE);
		$this->_LookupField->setMultiple();
		$this->_LookupField->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onDblClick', sprintf("%s_instance.remove();", $this->id)));
		if (max(1, $this->_LookupField->getAttribute('INTSIZE')) < 2)
			$this->_LookupField->setSize(8);
		$this->_LookupField->onPreRender();
	}
}
?>