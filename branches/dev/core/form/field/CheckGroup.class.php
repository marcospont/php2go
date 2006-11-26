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
// $Header: /www/cvsroot/php2go/core/form/field/CheckGroup.class.php,v 1.16 2006/11/02 19:21:34 mpont Exp $
// $Date: 2006/11/02 19:21:34 $

//------------------------------------------------------------------
import('php2go.form.field.GroupField');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		CheckGroup
// @desc		Classe que constrói um grupo de campos do tipo CHECKBOX, com controle
//				de obrigatoriedade de seleção de um dos itens, pelo menos
// @package		php2go.form.field
// @extends		GroupField
// @uses		TypeUtils
// @uses		Template
// @author		Marcos Pont
// @version		$Revision: 1.16 $
//!-----------------------------------------------------------------
class CheckGroup extends GroupField
{
	//!-----------------------------------------------------------------
	// @function	CheckGroup::CheckGroup
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function CheckGroup(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'CHECKBOX';
		$this->searchDefaults['OPERATOR'] = 'IN';
		$this->templateFile = PHP2GO_TEMPLATE_PATH . 'checkgroup.tpl';
		$this->attributes['MULTIPLE'] = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	CheckGroup::renderGroup
	// @desc		Implementa o método da classe pai para construir o conjunto
	//				de dados sobre os elementos do grupo (checkboxes)
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function renderGroup() {
		$group = array();
		$groupName = (substr($this->name, -2) != '[]' ? $this->name . '[]' : $this->name);
		$hasValue = (!empty($this->value));
		$arrayValue = (TypeUtils::isArray($this->value));
		if ($this->attributes['SHORTCUTS']) {
			$msgs = PHP2Go::getLangVal('CHECKGROUP_SHORTCUTS');
			$id = preg_replace("/\[\]$/", '', $this->name);
			$labelStyle = $this->_Form->getLabelStyle();
			$prepend = sprintf(
				"\n<div style=\"padding:0px 0px 5px 5px;\"%s>" .
				"\n  <a id=\"%s_all\" href=\"javascript:;\"%s>%s</a> | " .
				"\n  <a id=\"%s_none\" href=\"javascript:;\"%s>%s</a> | " .
				"\n  <a id=\"%s_invert\" href=\"javascript:;\"%s>%s</a>" .
				"\n</div>",
				$labelStyle,
				$id, $labelStyle, $msgs['all'],
				$id, $labelStyle, $msgs['none'],
				$id, $labelStyle, $msgs['invert']
			);
			$append = sprintf(
				"\n<script type=\"text/javascript\">new CheckboxController(\"%s\", \"%s\", {all:\"%s_all\", none:\"%s_none\", invert:\"%s_invert\"});</script>",
				$this->_Form->formName, $groupName,
				$id, $id, $id, $id
			);
		} else {
			$prepend = '';
			$append = '';
		}
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			if ($hasValue) {
				if ($arrayValue)
					$this->optionAttributes[$i]['SELECTED'] = (in_array($this->optionAttributes[$i]['VALUE'], $this->value) ? ' checked' : '');
				else
					$this->optionAttributes[$i]['SELECTED'] = (!strcasecmp($this->optionAttributes[$i]['VALUE'], $this->value) ? ' checked' : '');
			} else {
				$this->optionAttributes[$i]['SELECTED'] = '';
			}
			$accessKey = TypeUtils::ifNull($this->optionAttributes[$i]['ACCESSKEY'], $this->accessKey);
			$input = sprintf("<input type=\"checkbox\" id=\"%s\" name=\"%s\" title=\"%s\" value=\"%s\"%s%s%s%s%s%s%s%s>",
				"{$this->id}_{$i}", $groupName, $this->label, $this->optionAttributes[$i]['VALUE'],
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
			'prepend' => $prepend,
			'append' => $append,
			'group' => $group
		);
	}

	//!-----------------------------------------------------------------
	// @function	CheckGroup::setShortcuts
	// @desc		Habilita/desabilita shortcuts (Todos/Nenhum/Inverter)
	// @param		setting bool	Valor para o flag
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setShortcuts($setting) {
		$this->attributes['SHORTCUTS'] = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	CheckGroup::onLoadNode
	// @desc		Processa atributos e nodos filhos da especificação XML do campo
	// @param		attrs array		Atributos
	// @param		children array	Nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// shortcuts
		$this->setShortcuts(resolveBooleanChoice(@$attrs['SHORTCUTS']));
	}

	//!-----------------------------------------------------------------
	// @function	CheckGroup::onPreRender
	// @desc		Insere o sufixo "[]" na propriedade validationName,
	//				que indica como o campo deve ser referenciado pela
	//				API de validação client-side
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (substr($this->validationName, -2) != '[]')
			$this->validationName .= '[]';
		parent::onPreRender();
	}
}
?>