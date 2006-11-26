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
// $Header: /www/cvsroot/php2go/core/form/field/LookupField.class.php,v 1.36 2006/10/29 17:32:52 mpont Exp $
// $Date: 2006/10/29 17:32:52 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		LookupField
// @desc		A classe LookupField monta campos de seleção de valores
//				provenientes de uma base de dados. A especificação do
//				elemento DATASOURCE define os elementos da consulta SQL
// @package		php2go.form.field
// @uses		TypeUtils
// @extends		DbField
// @author		Marcos Pont
// @version		$Revision: 1.36 $
//!-----------------------------------------------------------------
class LookupField extends DbField
{
	var $optionCount = 0;		// @var optionCount integer		Total de opções do campo

	//!-----------------------------------------------------------------
	// @function	LookupField::LookupField
	// @desc		Construtor da classe LookupField
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function LookupField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::display
	// @desc		Gera o código HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		// código do campo SELECT
		$name = ($this->attributes['MULTIPLE'] && substr($this->name, -2) != '[]' ? $this->name . '[]' : $this->name);
		print sprintf("\n<select id=\"%s\" name=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s%s>\n",
			$this->id, $name, $this->label, $this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'],
			$this->attributes['TABINDEX'], $this->attributes['STYLE'], ($this->attributes['MULTIPLE'] ? ' multiple' : ''),
			$this->attributes['SIZE'], $this->attributes['WIDTH'], $this->attributes['DISABLED'],
			$this->attributes['DATASRC'], $this->attributes['DATAFLD']
		);
		// primeira opção
		if (!$this->attributes['NOFIRST'])
			print sprintf("<option value=\"\">%s</option>\n", $this->attributes['FIRST']);
		// código das opções da lista de seleção
		if ($this->_Rs->recordCount() > 0) {
			$this->optionCount = $this->_Rs->recordCount();
			$hasValue = ((is_array($this->value) && !empty($this->value)) || $this->value != '');
			$arrayValue = (is_array($this->value));
			if ($this->isGrouping) {
				$groupVal = '';
				while (list($key, $display, $group, $groupDisplay) = $this->_Rs->fetchRow()) {
					if (strcasecmp($group, $groupVal)) {
						if ($groupVal != '')
							print "</optgroup>\n";
						print sprintf("<optgroup label=\"%s\">\n", $groupDisplay);
					}
					if ($hasValue) {
						if ($arrayValue)
							$optionSelected = (in_array($key, $this->value) ? ' selected' : '');
						else
							$optionSelected = (!strcasecmp($key, $this->value) ? ' selected' : '');
					} else {
						$optionSelected = '';
					}
					print sprintf("<option value=\"%s\"%s>%s</option>\n", $key, $optionSelected, $display);
					$groupVal = $group;
				}
				print "</optgroup>\n";
			} else {
				while (list($key, $display) = $this->_Rs->fetchRow()) {
					if ($hasValue) {
						if ($arrayValue)
							$optionSelected = (in_array($key, $this->value) ? ' selected' : '');
						else
							$optionSelected = (!strcasecmp($key, $this->value) ? ' selected' : '');
					} else {
						$optionSelected = '';
					}
					print sprintf("<option value=\"%s\"%s>%s</option>\n", $key, $optionSelected, $display);
				}
			}
		}
		print "</select>";
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::setName
	// @desc		Define o campo como escolha múltipla se o nome
	//				contiver os caracteres "[]" no final
	// @param		newName string	Nome para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setName($newName) {
		if (preg_match("/\[\]$/", $newName))
			$this->setMultiple();
		parent::setName($newName);
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::getDisplayValue
	// @desc		Monta uma representação compreensível
	//				do valor do campo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		$display = NULL;
		if (isset($this->value) && !TypeUtils::isInstanceOf($this->_Rs, 'ADORecordSet_empty')) {
			$arrayValue = is_array($this->value);
			while (list($value, $caption) = $this->_Rs->fetchRow()) {
				if ($arrayValue) {
					if (in_array($value, $this->value))
						$display[] = $caption;
				} else {
					if ($value == $this->value) {
						$display = $caption;
						break;
					}
				}
			}
			$this->_Rs->moveFirst();
		}
		return (is_array($display) ? '(' . implode(', ', $display) . ')' : $display);
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::getOptionCount
	// @desc		Retorna o total de opções do campo, baseado no total
	//				de registros retornados da consulta realizada
	// @return		int Total de opções disponíveis
	// @access		public
	//!-----------------------------------------------------------------
	function getOptionCount() {
		return $this->optionCount;
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::setFirstOption
	// @desc		Os campos do tipo LookupField possuem por padrão
	//				uma primeira opção em branco não selecionável na lista
	//				de opções. Este método permite definir um texto para este item
	// @param		first string	Texto para a primeira opção
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFirstOption($first) {
		$this->attributes['FIRST'] = ($first ? resolveI18nEntry($first) : '');
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::disableFirstOption
	// @desc		Desabilita ou habilita a inserção de uma primeira opção
	//				em branco na lista de opções
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function disableFirstOption($setting=TRUE) {
		$this->attributes['NOFIRST'] = (bool)$setting;
		if ($this->attributes['NOFIRST'])
			$this->attributes['FIRST'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::setMultiple
	// @desc		Habilita ou desabilita a possibilidade de seleção de múltiplas opções na lista
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setMultiple($setting=TRUE) {
		$this->attributes['MULTIPLE'] = (bool)$setting;
		$this->searchDefaults['OPERATOR'] = ($this->attributes['MULTIPLE'] ? 'IN' : 'EQ');
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::setSize
	// @desc		O atributo SIZE de um campo do tipo SELECT define o número
	//				de opções exibidas na construção do campo, ou seja, a altura
	//				do campo em número de linhas
	// @param		size int	Tamanho (número de linhas exibidas) para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = " size=\"{$size}\"";
		else
			$this->attributes['SIZE'] = '';
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::setWidth
	// @desc		Define a largura da lista de opções, em pixels
	// @param		width int	Largura para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = " style=\"width:{$width}px\"";
		else
			$this->attributes['WIDTH'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// escolha múltipla
		$this->setMultiple($this->attributes['MULTIPLE'] || resolveBooleanChoice(@$attrs['MULTIPLE']));
		// número de opções visíveis
		$size = @$attrs['SIZE'];
		(!$size && $this->attributes['MULTIPLE']) && ($size = 2);
		($size) && ($attrs['NOFIRST'] = 'T');
		$this->setSize($size);
		// texto da primeira opção
		$this->setFirstOption(@$attrs['FIRST']);
		// primeira opção (vazia ou não) desabilitada
		$this->disableFirstOption(resolveBooleanChoice(@$attrs['NOFIRST']));
		// largura em pixels
		$this->setWidth(@$attrs['WIDTH']);
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::onDataBind
	// @desc		Executa a consulta ao banco para montar o
	//				conjunto de opções para a lista de seleção
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		if (!isset($this->_Rs))
			parent::processDbQuery(ADODB_FETCH_NUM);
	}

	//!-----------------------------------------------------------------
	// @function	LookupField::onPreRender
	// @desc		Define um tamanho mínimo para campos SELECT de escolha múltipla
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if ($this->attributes['MULTIPLE'] && substr($this->validationName, -2) != '[]')
			$this->validationName .= '[]';
		parent::onPreRender();
		if ($this->attributes['MULTIPLE'] && !$this->attributes['SIZE'])
			$this->attributes['SIZE'] = " size=\"2\"";
	}
}
?>