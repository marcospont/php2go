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
// $Header: /www/cvsroot/php2go/core/form/field/CheckField.class.php,v 1.33 2006/10/26 04:55:12 mpont Exp $
// $Date: 2006/10/26 04:55:12 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		CheckField
// @desc		Esta classe constrói um campo de formulário do tipo CHECKBOX
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.33 $
//!-----------------------------------------------------------------
class CheckField extends FormField
{
	//!-----------------------------------------------------------------
	// @function	CheckField::CheckField
	// @desc		Construtor da classe CheckField, inicializa os atributos do campo
	// @param		&Form Form object		Formulário onde o campo será inserido
	// @param		child bool				"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function CheckField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'CHECKBOX';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::display
	// @desc		Gera o código HTML do checkbox, junto com sua caption	
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$captionString = '';
		if (!empty($this->attributes['CAPTION'])) {
			$caption = @$this->attributes['CAPTION'];
			if ($caption == 'empty')
				$caption = '';
			elseif (!$caption)
				$caption = $this->name;
			if ($this->accessKey && $this->_Form->accessKeyHighlight) {
				$pos = strpos(strtoupper($caption), strtoupper($this->accessKey));
				if ($pos !== FALSE)
					$caption = substr($caption, 0, $pos) . '<u>' . $caption[$pos] . '</u>' . substr($caption, $pos+1);
			}
			$captionString = sprintf("&nbsp;<label for=\"%s\" id=\"%s\"%s>%s</label>",
				$this->id, $this->id . "_label", $this->_Form->getLabelStyle(), $caption
			);
		}
		print sprintf("<input type=\"checkbox\" id=\"%s\" name=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s>%s",
			$this->id, $this->name, $this->attributes['CAPTION'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
			$this->attributes['STYLE'], $this->attributes['DISABLED'], $this->attributes['CHECKED'], $this->attributes['DATASRC'],
			$this->attributes['DATAFLD'], $this->attributes['SCRIPT'], $captionString);
		print sprintf("<input type=\"hidden\" id=\"%s\" name=\"%s\" value=\"%s\">",
				"V_{$this->id}", "V_{$this->name}",
				(empty($this->attributes['DISABLED']) ? (empty($this->value) ? 'F' : $this->value) : '')
		);
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::setLabel
	// @desc		Como o rótulo (atributo LABEL) não é obrigatório para um campo
	//				CHECKFIELD, este método retornará a CAPTION se o label for vazio
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getLabel() {
		if (empty($this->label) || $this->label == 'empty') {
			if ($this->attributes['CAPTION'] != '' && $this->attributes['CAPTION'] != 'empty')
				return $this->attributes['CAPTION'];
			return '';
		}
		return $this->label;
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::getDisplayValue
	// @desc		Monta uma representação compreensível do valor do campo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		$descriptions = PHP2Go::getLangVal('CHECKBOX_DESCRIPTIONS');
		return sprintf($descriptions[$this->value], $this->attributes['CAPTION']);
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::getSearchData
	// @desc		Retorna o conjunto de informações de busca para este campo
	// @return		array Dados de busca deste campo
	// @access		public
	//!-----------------------------------------------------------------
	function getSearchData() {
		$search = array_merge($this->searchDefaults, $this->search);
		if ($this->_Form->isPosted()) {
			$search['VALUE'] = $this->getValue();
			$search['DISPLAYVALUE'] = $this->getDisplayValue();
		}
		return $search;
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::setValue
	// @desc		Sobrescreve o método setValue em FormField para converter
	//				o valor do campo para T ou F (a requisição envia "on" ou vazio)
	// @param		aValue string	Valor para o checkbox
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setValue($value) {
		if (!$this->dataBind)
			$this->onDataBind();
		// traduz o valor do campo
		$value = (string)$value;
		switch ($value) {
			case 'T' :
			case 'on' :
			case '1' :
				$value = 'T';
				break;
			case 'F' :
			case '0' :
				$value = 'F';
				break;
			default :
				$value = 'F';
				break;
		}
		// armazena o novo valor nos arrays globais
		$method = '_' . HttpRequest::method();
		$$method["V_{$this->name}"] = $_REQUEST["V_{$this->name}"] = $value;
		// define o valor do atributo CHECKED
		$this->attributes['CHECKED'] = ($value == 'T' ? ' checked' : '');
		$this->value = $value;
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::setCaption
	// @desc		Altera ou define uma caption para o campo checkbox
	// @param		caption string	Texto da caption
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCaption($caption) {
		if ($caption)
			$this->attributes['CAPTION'] = $caption;
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::setChecked
	// @desc		Define se o campo deverá ser marcado
	// @param		setting bool	"TRUE" Marcar ou não o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setChecked($setting=TRUE) {
		$setting = (bool)$setting;
		if ($setting) {
			$this->attributes['CHECKED'] = ' checked';
			$this->value = 'T';
		} else {
			$this->attributes['CHECKED'] = '';
			$this->value = 'F';
		}
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// caption
		$this->setCaption(@$attrs['CAPTION']);
		// label vazio se não fornecido
		if (!isset($attrs['LABEL']))
			$this->setLabel('empty');
	}

	//!-----------------------------------------------------------------
	// @function	CheckField::onDataBind
	// @desc		Tenta determinar o valor do campo buscando o nome do
	//				campo escondido associado na requisição
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		if (empty($this->value) || $this->value == @$this->attributes['DEFAULT']) {
			// tenta buscar o valor a partir do nome do campo escondido associado
			$hiddenValue = HttpRequest::getVar('V_' . $this->name, 'all', 'ROSGPCE');
			if ($hiddenValue)
				$this->setValue($hiddenValue);
		}
	}
}
?>