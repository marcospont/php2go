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
// $Header: /www/cvsroot/php2go/core/form/field/ColorPickerField.class.php,v 1.3 2006/10/29 18:27:58 mpont Exp $
// $Date: 2006/10/29 18:27:58 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		ColorPickerField
// @desc		Componente de formulário que apresenta um controle de escolha
//				de cor através de um paleta de 228 cores. A cor escolhida é
//				armazenada em um campo escondido. O padrão de saída é o formato
//				RGB (#999999)
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class ColorPickerField extends FormField
{
	//!-----------------------------------------------------------------
	// @function	ColorPickerField::ColorPickerField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function ColorPickerField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	//!-----------------------------------------------------------------
	// @function	ColorPickerField::display
	// @desc		Gera o código HTML do componente de seleção de cor
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		if ($this->attributes['MODE'] == 'flat') {
			$options = "{mode:\"flat\",container:\"{$this->id}_container\"}";
			print sprintf(
				"<input id=\"%s\" name=\"%s\" type=\"hidden\" value=\"%s\" title=\"%s\"%s%s%s%s>" .
				"<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td id=\"%s_container\"></td></tr></table>" .
				"<script type=\"text/javascript\">new ColorPickerField(\"%s\", %s);</script>",
				$this->id, $this->name, $this->value, $this->label, $this->attributes['SCRIPT'],
				$this->attributes['TABINDEX'], $this->attributes['ACCESSKEY'], $this->attributes['DISABLED'],
				$this->id, $this->id, $options
			);
		} else {
			$ua =& UserAgent::getInstance();
			$options = "{mode:\"popup\",trigger:\"{$this->id}_button\"}";
			print sprintf(
				"<input id=\"%s\" name=\"%s\" type=\"text\" value=\"%s\" size=\"8\" maxlength=\"7\" title=\"%s\"%s%s%s%s%s%s%s>" .
				"<button id=\"%s_button\" type=\"button\" %s%s style=\"cursor:pointer;%s;background:transparent;border:none;vertical-align:text-bottom\"><img src=\"%s\" border=\"0\" alt=\"\"/></button>" .
				"<script type=\"text/javascript\">new ColorPickerField(\"%s\", %s);</script>",
				$this->id, $this->name, $this->value, $this->label, $this->attributes['SCRIPT'], $this->attributes['TABINDEX'],
				$this->attributes['ACCESSKEY'], $this->attributes['DISABLED'], $this->attributes['STYLE'], $this->attributes['DATASRC'],
				$this->attributes['DATAFLD'], $this->id, HtmlUtils::statusBar('Selecionar uma cor'), $this->attributes['TABINDEX'],
				($ua->matchBrowser('opera') ? "padding-left:4px;padding-right:0" : "width:20px"), PHP2GO_ICON_PATH . 'colorpicker.gif',
				$this->id, $options
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	ColorPickerField::setMode
	// @desc		Define o modo de funcionamento da ferramenta de
	//				seleção de cor : FLAT ou POPUP
	// @param		mode string	Modo de funcionamento
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMode($mode) {
		$mode = strtolower(strval($mode));
		$this->attributes['MODE'] = ($mode == 'popup' ? 'popup' : 'flat');
	}

	//!-----------------------------------------------------------------
	// @function	ColorPickerField::onLoadNode
	// @desc		Processa atributos e nodos filhos provenientes
	//				da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// modo de operação
		$this->setMode(@$attrs['MODE']);
	}

	//!-----------------------------------------------------------------
	// @function	ColorPickerField::onPreRender
	// @desc		Adiciona o CSS e a biblioteca JS necessários ao
	//				funcionamento do componente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->importStyle(PHP2GO_CSS_PATH . "colorpicker.css");
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "form/colorpickerfield.js");
	}
}
?>