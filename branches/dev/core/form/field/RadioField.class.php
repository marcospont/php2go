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
// $Header: /www/cvsroot/php2go/core/form/field/RadioField.class.php,v 1.24 2006/11/02 19:21:46 mpont Exp $
// $Date: 2006/11/02 19:21:46 $

//------------------------------------------------------------------
import('php2go.form.field.GroupField');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		RadioField
// @desc		Classe que constrói um grupo de campos do tipo RADIO BUTTON
// @package		php2go.form.field
// @extends		GroupField
// @uses		TypeUtils
// @uses		Template
// @author		Marcos Pont
// @version		$Revision: 1.24 $
//!-----------------------------------------------------------------
class RadioField extends GroupField
{
	//!-----------------------------------------------------------------
	// @function	RadioField::RadioField
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function RadioField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'RADIO';
		$this->searchDefaults['OPERATOR'] = 'EQ';
		$this->templateFile = PHP2GO_TEMPLATE_PATH . 'radiofield.tpl';
	}

	//!-----------------------------------------------------------------
	// @function	RadioField::renderGroup
	// @desc		Implementa o método da classe pai para construir o conjunto
	//				de dados sobre os elementos do grupo (botões RADIO)
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function renderGroup() {
		$group = array();
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			if ($this->optionAttributes[$i]['VALUE'] == parent::getValue())
				$this->optionAttributes[$i]['SELECTED'] = " checked";
			else
				$this->optionAttributes[$i]['SELECTED'] = "";
			$accessKey = TypeUtils::ifNull($this->optionAttributes[$i]['ACCESSKEY'], $this->accessKey);
			$input = sprintf("<input type=\"radio\" id=\"%s\" name=\"%s\" title=\"%s\" value=\"%s\"%s%s%s%s%s%s%s%s>",
				$this->id . "_$i", $this->name, $this->label, $this->optionAttributes[$i]['VALUE'],
				($accessKey ? " accesskey=\"{$accessKey}\"" : ''), $this->attributes['TABINDEX'],
				$this->optionAttributes[$i]['SCRIPT'], $this->attributes['STYLE'],
				$this->optionAttributes[$i]['DISABLED'], $this->attributes['DATASRC'],
				$this->attributes['DATAFLD'], $this->optionAttributes[$i]['SELECTED']
			);
			$caption = $this->optionAttributes[$i]['CAPTION'];
			if ($accessKey && $this->_Form->accessKeyHighlight) {
				$pos = strpos(strtoupper($caption), strtoupper($accessKey));
				if ($pos !== FALSE)
					$caption = substr($caption, 0, $pos) . '<u>' . $caption[$pos] . '</u>' . substr($caption, $pos+1);
			}
			$group[] = array(
				'input' => $input,
				'id' => $this->id,
				'caption' => $caption,
				'alt' => (!empty($this->optionAttributes[$i]['ALT']) ? " title=\"{$this->optionAttributes[$i]['ALT']}\"" : '')
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