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
// $Header: /www/cvsroot/php2go/core/form/field/EditField.class.php,v 1.43 2006/10/29 17:54:39 mpont Exp $
// $Date: 2006/10/29 17:54:39 $

//------------------------------------------------------------------
import('php2go.form.field.EditableField');
import('php2go.datetime.Date');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		EditField
// @desc		Classe respons�vel por construir um INPUT HTML do
//				tipo TEXT, para simples edi��o de texto
// @package		php2go.form.field
// @uses		Date
// @uses		HtmlUtils
// @extends		EditableField
// @author		Marcos Pont
// @version		$Revision: 1.43 $
//!-----------------------------------------------------------------
class EditField extends EditableField
{
	//!-----------------------------------------------------------------
	// @function	EditField::EditField
	// @desc		Construtor da classe EditField, inicializa os atributos do campo
	// @access		public
	// @param		&Form Form object	Formul�rio no qual o campo � inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo � membro de um campo composto
	//!-----------------------------------------------------------------
	function EditField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'TEXT';
	}

	//!-----------------------------------------------------------------
	// @function	EditField::display
	// @desc		Gera o c�digo HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		print sprintf("<input type=\"text\" id=\"%s\" name=\"%s\" value=\"%s\" maxlength=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s%s>%s%s",
			$this->id, $this->name, $this->value, $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'],
			$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'], $this->attributes['ALIGN'], $this->attributes['STYLE'],
			$this->attributes['READONLY'], $this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'],
			$this->attributes['AUTOCOMPLETE'], $this->attributes['CALENDAR'], $this->attributes['CALCULATOR']
		);
	}

	//!-----------------------------------------------------------------
	// @function	EditField::setAlign
	// @desc		Seta o alinhamento do texto digitado no campo
	// @param		align string	Alinhamento (left, right, center)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setAlign($align) {
		if (!empty($align))
			$this->attributes['ALIGN'] = " style=\"text-align:" . trim($align) . "\"";
		else
			$this->attributes['ALIGN'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	EditField::setCapitalize
	// @desc		Habilita ou desabilita a transforma��o do valor do campo, no momento
	//				da submiss�o, para possuir o primeiro caractere de cada palavra em
	//				mai�scula e o resto em letras min�sculas (capitaliza��o)
	// @access		public
	// @param		setting bool	"TRUE" Valor para o atributo
	// @return		void
	//!-----------------------------------------------------------------
	function setCapitalize($setting=TRUE) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['CAPITALIZE'] = "T";
		else
			$this->attributes['CAPITALIZE'] = "F";
	}

	//!-----------------------------------------------------------------
	// @function	EditField::setAutoTrim
	// @desc		Habilita ou desabilita a remo��o autom�tica dos caracteres
	//				brancos no in�cio e no fim do valor informado no campo no
	//				momento da submiss�o do formul�rio
	// @access		public
	// @param		setting bool	"TRUE" Valor para o atributo
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoTrim($setting=TRUE) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['AUTOTRIM'] = "T";
		else
			$this->attributes['AUTOTRIM'] = "F";
	}

	//!-----------------------------------------------------------------
	// @function	EditField::isValid
	// @desc		Sobrecarrega o m�todo EditableField::isValid a fim de executar
	//				as convers�es de valor necess�rias no momento da valida��o do campo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if ($this->attributes['CAPITALIZE'] == "T")
			$this->value = StringUtils::capitalize($this->value);
		if ($this->attributes['AUTOTRIM'] == "T")
			$this->value = trim($this->value);
		return parent::isValid();
	}

	//!-----------------------------------------------------------------
	// @function	EditField::onLoadNode
	// @desc		M�todo respons�vel por processar atributos e nodos filhos
	//				provenientes da especifica��o XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// calculator
		$this->attributes['CALCULATOR'] = resolveBooleanChoice(@$attrs['CALCULATOR']);
		// align
		$this->setAlign(@$attrs['ALIGN']);
		// capitalize
		$this->setCapitalize(resolveBooleanChoice(@$attrs['CAPITALIZE']));
		// autotrim
		$this->setAutoTrim(resolveBooleanChoice(@$attrs['AUTOTRIM']));
	}

	//!-----------------------------------------------------------------
	// @function	EditField::onDataBind
	// @desc		Sobrecarrega o m�todo onDataBind da classe FormField para interpretar
	//				express�es de data do tipo TODAY+1D, TODAY-2M, etc.. no valor do campo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		// express�es de data
		$regs = array();
		if (preg_match('/^DATE/', $this->mask) && !empty($this->value) && !Date::isEuroDate($this->value, $regs) && !Date::isUsDate($this->value, $regs))
			parent::setValue(Date::parseFieldExpression($this->value));
		if (is_array($this->value))
			$this->value = '';
	}

	//!-----------------------------------------------------------------
	// @function	EditField::onPreRender
	// @desc		Constr�i o c�digo JavaScript relacionado com os atributos
	//				CAPITALIZE e AUTOTRIM (transforma��o de valor antes da submiss�o)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		// adiciona c�digo para transforma��es de valor
		if ($this->attributes['CAPITALIZE'] == 'T')
			$this->_Form->beforeValidateCode .= sprintf("\t\tfrm.elements['%s'].value = frm.elements['%s'].value.capitalize();\n", $this->name, $this->name);
		if ($this->attributes['AUTOTRIM'] == 'T')
			$this->_Form->beforeValidateCode .= sprintf("\t\tfrm.elements['%s'].value = frm.elements['%s'].value.trim();\n", $this->name, $this->name);
		$btnDisabled = ($this->attributes['READONLY'] != '' || $this->attributes['DISABLED'] != '' || $this->_Form->readonly ? " DISABLED" : "");
		// bot�o de exibi��o do calend�rio (date picker)
		if (preg_match('/^DATE/', $this->mask)) {
			$this->_Form->Document->importStyle(PHP2GO_JAVASCRIPT_PATH . "vendor/jscalendar/calendar-system.css");
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/jscalendar/calendar_stripped.js");
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/jscalendar/calendar-setup_stripped.js");
			$this->_Form->Document->addScriptCode(sprintf("\tCalendar.setup( {\n\t\tinputField:\"%s\", ifFormat:\"%s\", button:\"%s\", singleClick:true, align:\"Bl\", cache:true, showOthers:true, weekNumbers:false\n\t} );",
				$this->id, (PHP2Go::getConfigVal('LOCAL_DATE_TYPE') == 'EURO' ? "%d/%m/%Y" : "%Y/%m/%d"),
				$this->id . '_calendar'), 'Javascript', SCRIPT_END
			);
			$ua =& UserAgent::getInstance();
			$this->attributes['CALENDAR'] = sprintf("<button id=\"%s\" type=\"button\" %s style=\"cursor:pointer;%s;background:transparent;border:none;vertical-align:text-bottom\"%s><img src=\"%s\" border=\"0\" alt=\"\"/></button>",
					$this->id . '_calendar',
					HtmlUtils::statusBar(PHP2Go::getLangVal('CALENDAR_LINK_TITLE')),
					($ua->matchBrowser('opera') ? "padding-left:4px;padding-right:0" : "width:20px"),
					$this->attributes['TABINDEX'],
					$this->_Form->icons['calendar']
			);
		} else {
			$this->attributes['CALENDAR'] = '';
		}
		// bot�o de exibi��o da calculadora
		if ($this->attributes['CALCULATOR'] && ($this->mask == 'INTEGER' || $this->mask == 'FLOAT' || $this->mask == 'CURRENCY' || $this->mask == '')) {
			$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'calculator.css');
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'widgets/calculator.js');
			$this->_Form->Document->addScriptCode(sprintf("\tCalculator.setup({trigger:'%s_calculator',target:'%s',align:'bottom'});", $this->id, $this->id), 'Javascript', SCRIPT_END);
			$this->attributes['CALCULATOR'] = sprintf("<button id=\"%s_calculator\" type=\"button\" %s style=\"cursor:pointer;padding-left:3px;background:transparent;border:none;vertical-align:text-bottom\"%s><img name=\"%s_calculator_img\" src=\"%s\" border=\"0\" alt=\"\"></button>",
					$this->id, HtmlUtils::statusBar(PHP2Go::getLangVal('CALCULATOR_LINK_TITLE')),
					$this->attributes['TABINDEX'], $this->id, $this->_Form->icons['calculator']
			);
		} else {
			$this->attributes['CALCULATOR'] = '';
		}
	}
}
?>