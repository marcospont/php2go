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
// $Header: /www/cvsroot/php2go/core/form/field/MultiColumnLookupField.class.php,v 1.4 2006/10/26 04:55:14 mpont Exp $
// $Date: 2006/10/26 04:55:14 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
//------------------------------------------------------------------

// @const MCLOOKUP_NORMAL "normal"
// Style key for normal option rows
define('MCLOOKUP_NORMAL', 'normal');
// @const MCLOOKUP_SELECTED "selected"
// Style key for selected option rows
define('MCLOOKUP_SELECTED', 'selected');
// @const MCLOOKUP_HOVER "hover"
// Style key for option rows when hovered
define('MCLOOKUP_HOVER', 'hover');

//!-----------------------------------------------------------------
// @class		MultiColumnLookupField
// @desc
// @package		php2go.form.field
// @extends		DbField
// @uses		Template
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class MultiColumnLookupField extends DbField
{
	var $optionCount = 0;		// @var optionCount integer		Total de opções do campo

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::MultiColumnLookupField
	// @desc		Construtor da classe
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function MultiColumnLookupField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->searchDefaults['OPERATOR'] = 'EQ';
		$this->attributes['ROWSTYLE'] = array(
			MCLOOKUP_NORMAL => 'mclookupNormal',
			MCLOOKUP_SELECTED => 'mclookupSelected',
			MCLOOKUP_HOVER => 'mclookupHover'
		);
		$this->attributes['TABLESTYLE'] = 'mcLookupTable';
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::display
	// @desc		Gera o código HTML do componente
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		// processamento do template
		$attrs = $this->attributes;
		if (!$attrs['TABLEHEIGHT'])
			$attrs['TABLEHEIGHT'] = 'null';
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'multicolumnlookupfield.tpl');
		$Tpl->parse();
		$Tpl->assign('id', $this->id);
		$Tpl->assign('name', $this->name);
		$Tpl->assign('label', $this->label);
		$Tpl->assign('value', $this->value);
		$Tpl->assign('attrs', $this->attributes);
		$Tpl->assignByRef('options', $this->_Rs);
		$headers = array();
		$customHeaders = (array)$this->attributes['HEADERS'];
		$colCount = $this->_Rs->fieldCount();
		for ($i=1; $i<$colCount; $i++) {
			$Fld =& $this->_Rs->fetchField($i);
			$headers[] = (isset($customHeaders[$i-1]) ? $customHeaders[$i-1] : $Fld->name);
		}
		$Tpl->assign('headers', $headers);
		$Tpl->display();
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::getDisplayValue
	// @desc		Monta uma representação compreensível
	//				do valor do campo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		$display = NULL;
		if (isset($this->value)) {
			while (list($value, $caption) = $this->_Rs->fetchRow()) {
				if ($value == $this->value) {
					$display = $caption;
					break;
				}
			}
			$this->_Rs->moveFirst();
		}
		return $display;
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::getOptionCount
	// @desc		Retorna o total de opções do campo, baseado no total
	//				de registros retornados da consulta realizada
	// @return		int Total de opções disponíveis
	// @access		public
	//!-----------------------------------------------------------------
	function getOptionCount() {
		return $this->optionCount;
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::setHeaders
	// @desc		Define os nomes dos cabeçalhos para as colunas
	//				da tabela de opções gerada
	// @param		headers string Nomes para os cabeçalhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHeaders($headers) {
		if (!empty($headers))
			$this->attributes['HEADERS'] = explode(',', trim(resolveI18nEntry($headers)));
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::setWidth
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
	// @function	MultiColumnLookupField::setTableHeight
	// @desc		Permite definir a altura, em pixels, da tabela de opções
	// @param		height int	Altura da tabela em pixels
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTableHeight($height) {
		$height = intval($height);
		if ($height > 0)
			$this->attributes['TABLEHEIGHT'] = $height;
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::setTableWidth
	// @desc		Permite definir a largura, em pixels, da tabela de opções
	// @param		width int	Largura da tabela em pixels
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($width) {
		$width = intval($width);
		if ($width > 0)
			$this->attributes['TABLEWIDTH'] = $width;
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::setTableStyle
	// @desc		Define o estilo (nome de classe CSS) para a tabela de opções
	// @param		style string	Nome do estilo (classe)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTableStyle($style) {
		if (!empty($style))
			$this->attributes['TABLESTYLE'] = $style;
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::setRowStyle
	// @desc		Associa um nome de classe CSS a um determinado estilo
	//				de linha na tabela de opções
	// @param		style string	Nome do estilo
	// @param		type string		Tipo da linha (vide constantes da classe)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRowStyle($style, $type) {
		if (!empty($style))
			$this->attributes['ROWSTYLE'][$type] = $style;
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// headers
		$this->setHeaders(@$attrs['HEADERS']);
		// altura e largura do componente da tabela
		$this->setWidth(@$attrs['WIDTH']);
		$this->setTableHeight(@$attrs['TABLEHEIGHT']);
		$this->setTableWidth(@$attrs['TABLEWIDTH']);
		// estilos
		$this->setTableStyle(@$attrs['TABLESTYLE']);
		$this->setRowStyle(@$attrs['NORMALROWSTYLE'], MCLOOKUP_NORMAL);
		$this->setRowStyle(@$attrs['SELECTEDROWSTYLE'], MCLOOKUP_NORMAL);
		$this->setRowStyle(@$attrs['HOVERROWSTYLE'], MCLOOKUP_NORMAL);
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::onDataBind
	// @desc		Executa a consulta ao banco para montar o
	//				conjunto de opções para a lista de seleção
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		parent::processDbQuery(ADODB_FETCH_NUM);
	}

	//!-----------------------------------------------------------------
	// @function	MultiColumnLookupField::onPreRender
	// @desc		Adiciona no documento HTML o arquivo CSS e a biblioteca
	//				JS necessários para o funcionamento do componente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/multicolumnlookupfield.js');
		$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'multicolumnlookupfield.css');
	}
}
?>