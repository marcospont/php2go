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
// $Header: /www/cvsroot/php2go/core/form/field/HiddenField.class.php,v 1.15 2006/10/26 04:55:14 mpont Exp $
// $Date: 2006/10/26 04:55:14 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		HiddenField
// @desc		Classe que constrói um grupo de campos do tipo RADIO BUTTON
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.15 $
//!-----------------------------------------------------------------
class HiddenField extends FormField
{
	//!-----------------------------------------------------------------
	// @function	HiddenField::HiddenField
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @access		public
	//!-----------------------------------------------------------------
	function HiddenField(&$Form) {
		parent::FormField($Form);
		$this->htmlType = 'HIDDEN';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	//!-----------------------------------------------------------------
	// @function	HiddenField::display
	// @desc		Gera o código HTML do campo
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && parent::onPreRender());
		print sprintf("<input type=\"hidden\" id=\"%s\" name=\"%s\" value=\"%s\"%s%s>",
				$this->id, $this->name, $this->value, $this->attributes['DATASRC'], $this->attributes['DATAFLD']
		);
	}
}
?>