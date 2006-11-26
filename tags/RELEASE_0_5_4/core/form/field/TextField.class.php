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
// $Header: /www/cvsroot/php2go/core/form/field/TextField.class.php,v 1.18 2006/10/26 04:55:14 mpont Exp $
// $Date: 2006/10/26 04:55:14 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		TextField
// @desc		Classe que permite a inclusão de porções de texto dinâmicas
//				dentro de um formulário, utilizando tags &lt;SPAN&gt;. A utilização
//				de um TEXTFIELD pode ser substituída pelo uso de templates e variáveis
//				de substituição
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.18 $
//!-----------------------------------------------------------------
class TextField extends FormField
{
	//!-----------------------------------------------------------------
	// @function	TextField::TextField
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function TextField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'SPAN';
		$this->searchable = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	TextField::display
	// @desc		Gera o código HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered) && (parent::onPreRender());
		print sprintf("<span id=\"%s\" title=\"%s\"%s>%s</span>",
			$this->id, $this->label, $this->attributes['STYLE'], $this->value
		);
	}
}
?>