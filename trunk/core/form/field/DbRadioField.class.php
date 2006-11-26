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
// $Header: /www/cvsroot/php2go/core/form/field/DbRadioField.class.php,v 1.27 2006/11/02 19:21:46 mpont Exp $
// $Date: 2006/11/02 19:21:46 $

//------------------------------------------------------------------
import('php2go.form.field.DbGroupField');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DbRadioField
// @desc		Classe que constrói um grupo de campos do tipo RADIO BUTTON,
//				carregando os elementos do grupo a partir de uma consulta ao
//				banco de dados
// @package		php2go.form.field
// @extends		DbGroupField
// @author		Marcos Pont
// @version		$Revision: 1.27 $
//!-----------------------------------------------------------------
class DbRadioField extends DbGroupField
{
	//!-----------------------------------------------------------------
	// @function	DbRadioField::DbRadioField
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @param		&Form Form object		Formulário no qual o campo é inserido
	// @param		child bool				"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function DbRadioField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->htmlType = 'RADIO';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	//!-----------------------------------------------------------------
	// @function	DbRadioField::renderGroup
	// @desc		Implementa o método renderGroup da classe pai, onde
	//				os dados dos elementos do grupo (botões RADIO) são
	//				armazenados em um array para serem utilizados na
	//				renderização
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function renderGroup() {
		$group = array();
		while (list($value, $caption) = $this->_Rs->fetchRow()) {
			$index = $this->_Rs->absolutePosition() - 1;
			$optionSelected = ($value == $this->value ? ' checked' : '');
			$input = sprintf("<input type=\"radio\" id=\"%s\" name=\"%s\" value=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s>",
				"{$this->id}_{$index}", $this->name, $value, $this->label, $this->attributes['ACCESSKEY'],
				$this->attributes['TABINDEX'], $this->attributes['SCRIPT'], $this->attributes['STYLE'],
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'],
				$optionSelected
			);
			$group[] = array(
				'input' => $input,
				'id' => $this->id,
				'caption' => $caption
			);
		}
		return array(
			'prepend' => '',
			'append' => '',
			'group' => $group
		);
	}
}
?>