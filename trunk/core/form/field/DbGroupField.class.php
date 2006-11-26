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
// $Header: /www/cvsroot/php2go/core/form/field/DbGroupField.class.php,v 1.11 2006/11/02 19:21:45 mpont Exp $
// $Date: 2006/11/02 19:21:45 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DbGroupField
// @desc		A classe GroupField serve de base para a construção
//				de um grupo de campos RADIO ou um grupo de campos
//				CHECKBOX cujos elementos são carregados a partir de
//				uma consulta ao banco de dados
// @package		php2go.form.field
// @extends		DbField
// @uses		Template
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.11 $
//!-----------------------------------------------------------------
class DbGroupField extends DbField
{
	var $optionCount = 0;		// @var optionCount int			"0" Total de opções do grupo

	//!-----------------------------------------------------------------
	// @function	DbGroupField::display
	// @desc		Monta o código HTML do grupo de campos
	// @note		O nome do arquivo de template e os dados dos elementos do
	//				grupo são definidos nas classes filhas pela propriedade
	//				templateFile e pelo método renderGroup()
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$group = $this->renderGroup();
		$elements =& $group['group'];
		print $group['prepend'];
		print sprintf("\n<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"%s>\n  <tr>", $this->attributes['TABLEWIDTH']);
		for ($i=0,$s=sizeof($elements); $i<$s; $i++) {
			print sprintf("\n    <td style=\"width:15px;height:15px;\">%s</td>", $elements[$i]['input']);
			print sprintf("\n    <td><label for=\"%s_%s\" id=\"%s_label\"%s%s>%s</label></td>",
				$elements[$i]['id'], $i, $elements[$i]['id'], $elements[$i]['alt'], $this->_Form->getLabelStyle(), $elements[$i]['caption']
			);
			if ((($i+1) % $this->attributes['COLS']) == 0 && $i<($s-1))
				print "\n  </tr><tr>";
		}
		$diff = ($i % $this->attributes['COLS']);
		if ($diff && $this->attributes['COLS'] > 1) {
			for ($i=$diff; $i<$this->attributes['COLS']; $i++)
				print "\n    <td colspan=\"2\"></td>";
		}
		print "\n  </tr>\n</table>";
		print $group['append'];
	}

	//!-----------------------------------------------------------------
	// @function	DbGroupField::renderGroup
	// @desc		O método renderGroup deve ser implementado nas classes
	//				filhas retornando um array com os dados dos elementos do
	//				grupo. Cada item do array deve conter as chaves "input"
	//				(código do elemento do grupo), "name" e "caption"
	// @note		O atributo "alt" é opcional e pode ser incluído na
	//				especificação de cada item do grupo
	// @access		public
	// @return		array
	// @abstract
	//!-----------------------------------------------------------------
	function renderGroup() {
		return array();
	}

	//!-----------------------------------------------------------------
	// @function	DbGroupField::getFocusId
	// @desc		Retorna o ID do primeiro elemento do grupo, que
	//				deverá receber foco quando o LABEL do campo for clicado
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getFocusId() {
		return "{$this->id}_0";
	}

	//!-----------------------------------------------------------------
	// @function	DbGroupField::getDisplayValue
	// @desc		Busca a representação compreensível do valor do campo
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getDisplayValue() {
		$display = NULL;
		if (isset($this->value)) {
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
	// @function	DbGroupField::setCols
	// @desc		Seta o número de colunas da tabela que contém o grupo de campos,
	//				definindo assim quantos elementos devem ser exibidos por linha
	// @param		cols int	Número de colunas ou campos por linha
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCols($cols) {
		$this->attributes['COLS'] = max(1, $cols);
	}

	//!-----------------------------------------------------------------
	// @function	DbGroupField::setTableWidth
	// @desc		Seta o tamanho (valor para o atributo WIDTH) da tabela
	//				construída para o grupo de campos RADIO
	// @param		tableWidth mixed	Tamanho da tabela
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " width=\"" . $tableWidth . "\"";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	DbGroupField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// número de colunas
		$this->setCols(@$attrs['COLS']);
		// largura da tabela
		$this->setTableWidth(@$attrs['TABLEWIDTH']);
		// datasource obrigatório
		if (empty($this->dataSource))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_DBGROUPFIELD_DATASOURCE', array($this->fieldTag, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	DbGroupField::onDataBind
	// @desc		Executa a consulta ao banco de dados para montar
	//				o conjunto de opções do grupo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		parent::processDbQuery(ADODB_FETCH_NUM);
		$this->optionCount = $this->_Rs->recordCount();
		if ($this->optionCount == 0)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_DBGROUPFIELD_RESULTS', array($this->fieldTag, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
	}
}
?>