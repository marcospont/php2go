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
// $Header: /www/cvsroot/php2go/core/form/field/LookupChoiceField.class.php,v 1.19 2006/10/29 17:32:59 mpont Exp $
// $Date: 2006/10/29 17:32:59 $

//------------------------------------------------------------------
import('php2go.form.field.LookupField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		LookupChoiceField
// @desc		Esta classe extende a funcionalidade implementada pela
//				classe LookupField incluindo um campo texto que permite
//				filtrar a lista de valores à medida que os caracteres
//				são digitados
// @package		php2go.form.field
// @extends		LookupField
// @author		Marcos Pont
// @version		$Revision: 1.19 $
//!-----------------------------------------------------------------
class LookupChoiceField extends LookupField
{
	//!-----------------------------------------------------------------
	// @function	LookupChoiceField::LookupChoiceField
	// @desc		Construtor da classe LookupChoiceField
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @access		public
	//!-----------------------------------------------------------------
	function LookupChoiceField(&$Form) {
		parent::LookupField($Form);
	}

	//!-----------------------------------------------------------------
	// @function	LookupChoiceField::display
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		ob_start();
		parent::display();
		print sprintf("<input type=\"text\" id=\"%s_filter\" name=\"%s_filter\" value=\"%s\" maxlenght=\"60\"%s%s%s%s><br>%s<script type=\"text/javascript\">%s_instance = new LookupChoiceField('%s');</script>",
			$this->id, $this->name, PHP2Go::getLangVal('LOOKUP_CHOICE_FILTER_TIP'),
			$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED'],
			(empty($this->attributes['WIDTH']) ? " size=\"25\"" : $this->attributes['WIDTH']),
			ob_get_clean(), $this->id, $this->id
		);
	}

	//!-----------------------------------------------------------------
	// @function	LookupChoiceField::onPreRender
	// @desc		Configura alguns atributos que possuem restrições
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/lookupchoicefield.js');
		$this->isGrouping = FALSE;
		$this->disableFirstOption(TRUE);
	}
}
?>