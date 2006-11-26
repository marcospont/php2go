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
// $Header: /www/cvsroot/php2go/core/form/field/DataGrid.class.php,v 1.29 2006/10/29 17:38:19 mpont Exp $
// $Date: 2006/10/29 17:38:19 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DataGrid
// @desc		A classe DataGrid permite que um conjunto de grid de campos seja gerado
//				em um formul�rio contendo os dados presentes no resultado de uma consulta ao banco de dados.
//				Desta forma, � poss�vel criar um mecanismo de edi��o simult�nea de v�rios registros de uma
//				tabela, por exemplo. A especifica��o XML de um DataGrid deve conter uma fonte de dados, ou
//				<i>DATASOURCE</i>, e um conjunto de campos, ou <i>FIELDSET</i>
// @package		php2go.form.field
// @extends		DbField
// @uses		Template
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.29 $
//!-----------------------------------------------------------------
class DataGrid extends DbField
{
	var $fieldNames = array();		// @var fieldNames array			"array()" Conjunto de nomes dos campos do fieldset
	var $fieldSet = array();		// @var fieldSet array				"array()" Conjunto de campos do grid
	var $cellSizes = array();		// @var cellSizes array				"array()" Conjunto de tamanhos para as colunas do grid
	var $Template = NULL;			// @var Template Template object	"NULL" Template utilizado para construir o grid

	//!-----------------------------------------------------------------
	// @function	DataGrid::DataGrid
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object		Formul�rio onde o campo ser� inserido
	//!-----------------------------------------------------------------
	function DataGrid(&$Form) {
		parent::DbField($Form);
		$this->composite = TRUE;
		$this->searchable = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::__destruct
	// @desc		Destrutor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function __destruct() {
		unset($this->fieldset);
		unset($this->Template);
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::display
	// @desc		Exibe o c�digo gerado para o grid
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		(!$this->preRendered && $this->onPreRender());
		$this->Template->display();
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::setShowHeader
	// @desc		Define se os cabe�alhos das colunas da tabela devem ser exibidos
	// @param		setting bool	Valor para o flag
	// @note		O padr�o deste atributo � TRUE (os cabe�alhos s�o exibidos)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setShowHeader($setting) {
		$this->attributes['SHOWHEADER'] = (bool)$setting;
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::setHeaders
	// @desc		Define labels para os cabe�alhos das colunas do grid
	// @param		headers string	Headers
	//!-----------------------------------------------------------------
	function setHeaders($headers) {
		if ($headers) {
			$headers = (!is_array($headers) ? explode(',', resolveI18nEntry(trim($headers))) : $headers);
			$this->attributes['HEADERS'] = $headers;
		}
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::setHeaderStyle
	// @desc		Define o estilo CSS para as c�lulas do cabe�alho da tabela
	// @param		headerStyle string	Estilo para o cabe�alho
	// @note		O valor padr�o para esta propriedade � o estilo definido para os r�tulos do formul�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHeaderStyle($headerStyle) {
		if ($headerStyle)
			$this->attributes['HEADERSTYLE'] = " class=\"$headerStyle\"";
		else
			$this->attributes['HEADERSTYLE'] = $this->_Form->getLabelStyle();
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::setCellStyle
	// @desc		Seta o estilo CSS das c�lulas do conte�do da tabela
	// @param		cellStyle string	Estilo para o conte�do
	// @note		O valor padr�o para esta propriedade � o estilo definido para os r�tulos do formul�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCellStyle($cellStyle) {
		if ($cellStyle)
			$this->attributes['CELLSTYLE'] = " class=\"$cellStyle\"";
		else
			$this->attributes['CELLSTYLE'] = $this->_Form->getLabelStyle();
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::setTableWidth
	// @desc		Define o tamanho da tabela que cont�m o grid de campos
	// @param		tableWidth int		Tamanho para a tabela
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " width=\"{$tableWidth}\"";
		else
			$this->attributes['TABLEWIDTH'] = "";
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::setCellSizes
	// @desc		Define um vetor de tamanhos para as c�lulas do grid
	// @param		sizes array			Vetor de tamanhos contendo N+1 valores inteiros
	//									para os tamanhos das colunas da tabela, onde N �
	//									o n�mero de campos definidos no FIELDSET
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setCellSizes($sizes) {
		if (sizeof($sizes) != (sizeof($this->fieldSet) + 1) || array_sum($sizes) != 100) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALID_CELLSIZES', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			array_walk($sizes, 'trim');
			$this->cellSizes = $sizes;
		}
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::onLoadNode
	// @desc		M�todo respons�vel por processar atributos e nodos filhos
	//				provenientes da especifica��o XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// datasource e fieldset obrigat�rios
		if (isset($children['DATASOURCE']) && isset($children['FIELDSET']) &&
			TypeUtils::isInstanceOf($children['FIELDSET'], 'XmlNode') &&
			$children['FIELDSET']->hasChildren()) {
			// instancia e adiciona os campos no fieldset
			$globalDisabled = resolveBooleanChoice(@$attrs['DISABLED']);
			for ($i=0, $s=$children['FIELDSET']->getChildrenCount(); $i<$s; $i++) {
				$Child =& $children['FIELDSET']->getChild($i);
				if ($Child->getName() == '#cdata-section')
					continue;
				switch($Child->getTag()) {
					case 'EDITFIELD' : $fieldClassName = 'EditField'; break;
					case 'PASSWDFIELD' : $fieldClassName = 'PasswdField'; break;
					case 'MEMOFIELD' : $fieldClassName = 'MemoField'; break;
					case 'CHECKFIELD' : $fieldClassName = 'CheckField'; break;
					case 'FILEFIELD' : $fieldClassName = 'FileField'; break;
					case 'LOOKUPFIELD' : $fieldClassName = 'LookupField'; break;
					case 'COMBOFIELD' : $fieldClassName = 'ComboField'; break;
					case 'RADIOFIELD' : $fieldClassName = 'RadioField'; break;
					case 'DBRADIOFIELD' : $fieldClassName = 'DbRadioField'; break;
					case 'HIDDENFIELD' : $fieldClassName = 'HiddenField'; break;
					case 'TEXTFIELD' : $fieldClassName = 'TextField'; break;
					default : PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALID_FIELDTYPE', $Child->getTag()), E_USER_ERROR, __FILE__, __LINE__); break;
				}
				import("php2go.form.field.{$fieldClassName}");
				$Field = new $fieldClassName($this->_Form, TRUE);
				$Field->onLoadNode($Child->getAttributes(), $Child->getChildrenTagsArray());
				// campos recebem o atributo disabled do grid
				if ($globalDisabled)
					$Field->setDisabled(TRUE);
				// registra o campo e o nome do campos
				$this->fieldSet[] = $Field;
				$this->fieldNames[] = $Field->getName();
			}
			// exibir ou n�o cabe�alhos no grid
			if (isset($attrs['SHOWHEADER']))
				$this->setShowHeader(resolveBooleanChoice($attrs['SHOWHEADER']));
			else
				$this->setShowHeader(TRUE);
			// cabe�alhos das colunas
			$this->setHeaders(@$attrs['HEADERS']);
			// estilo do cabe�alho do grid
			$this->setHeaderStyle(@$attrs['HEADERSTYLE']);
			// estilo da c�lula
			$this->setCellStyle(@$attrs['CELLSTYLE']);
			// largura da tabela
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// tamanhos das c�lulas
			if (isset($attrs['CELLSIZES']))
				$this->setCellSizes(explode(',', $attrs['CELLSIZES']));
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATAGRID_STRUCTURE', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::onDataBind
	// @desc		Executa a consulta associada ao grid, validando
	//				o n�mero de colunas retornadas contra o n�mero de campos
	//				definidos
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		parent::processDbQuery(ADODB_FETCH_NUM);
		if ($this->_Rs->fieldCount() != (sizeof($this->fieldSet) + 2))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALID_FIELDCOUNT', $this->name), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	DataGrid::onPreRender
	// @desc		Constr�i o template de conte�do do grid
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		$this->Template = new Template(PHP2GO_TEMPLATE_PATH . 'datagrid.tpl');
		$this->Template->parse();
		$this->Template->globalAssign('id', $this->id);
		$this->Template->assign('width', $this->attributes['TABLEWIDTH']);
		if ($this->attributes['SHOWHEADER']) {
			$headers = TypeUtils::toArray(@$this->attributes['HEADERS']);
			$this->Template->createBlock('loop_line');
			for ($i=1,$s=$this->_Rs->fieldCount(); $i<$s; $i++) {
				$Field =& $this->_Rs->fetchField($i);
				$this->Template->createAndAssign('loop_header_cell', array(
					'style' => $this->attributes['HEADERSTYLE'],
					'width' => (isset($this->cellSizes[$i-1]) ? " WIDTH=\"{$this->cellSizes[$i-1]}%\"" : ''),
					'col_name' => (isset($headers[$i-1]) ? $headers[$i-1] : $Field->name)
				));
			}
		}
		$isPosted = ($this->_Form->isPosted() && !empty($this->value));
		while ($dataRow = $this->_Rs->fetchRow()) {
			$submittedRow = ($isPosted ? @$this->value[$dataRow[0]] : NULL);
			$this->Template->createBlock('loop_line');
			$this->Template->assign('row_id', $dataRow[0]);
			$this->Template->createAndAssign('loop_cell', array(
				'align' => 'left',
				'style' => $this->attributes['CELLSTYLE'],
				'width' => (isset($this->cellSizes[0]) ? " width=\"{$this->cellSizes[0]}%\"" : ''),
				'col_data' => $dataRow[1]
			));
			for ($i=0, $s=sizeof($this->fieldSet); $i<$s; $i++) {
				$Field = $this->fieldSet[$i];
				$Field->preRendered = FALSE;
				$Field->setId("{$this->name}_{$dataRow[0]}_{$this->fieldNames[$i]}");
				$Field->setName("{$this->name}[{$dataRow[0]}][{$this->fieldNames[$i]}]");
				if ($isPosted) {
					// corre��o especial para checkboxes
					if ($Field->getFieldTag() == 'CHECKFIELD') {
						eval("\$submittedRow['{$this->fieldNames[$i]}'] = \$_{$this->_Form->formMethod}['V_{$this->name}'][{$dataRow[0]}]['{$this->fieldNames[$i]}'];");
						$Field->setValue($submittedRow[$this->fieldNames[$i]]);
					}
					// aplica o valor submetido se ele existir, mesmo vazio
					elseif (isset($submittedRow[$this->fieldNames[$i]])) {
						$Field->setValue($submittedRow[$this->fieldNames[$i]]);
					}
					// aplica o valor original da coluna
					else {
						$Field->setValue($dataRow[$i+2]);
					}
				} else {
					$Field->setValue($dataRow[$i+2]);
				}
				$Field->onPreRender();
				$this->Template->createBlock('loop_cell');
				$this->Template->assign(array(
					'align' => 'center',
					'style' => $this->attributes['CELLSTYLE'],
					'width' => (isset($this->cellSizes[$i+1]) ? " width=\"{$this->cellSizes[$i+1]}%\"" : ''),
					'col_data' => $Field->getContent()
				));
			}
		}
	}
}
?>