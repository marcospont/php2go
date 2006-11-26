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
// $Header: /www/cvsroot/php2go/core/form/field/DbField.class.php,v 1.27 2006/11/02 19:25:18 mpont Exp $
// $Date: 2006/11/02 19:25:18 $

//------------------------------------------------------------------
import('php2go.db.QueryBuilder');
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DbField
// @desc		Os campos que utilizam dados provenientes de uma base
//				de dados utilizam esta classe como base para montagem
//				da fonte de dados para gerar os valores do campo
// @package		php2go.form.field
// @uses		ADORecordSet
// @uses		Db
// @uses		QueryBuilder
// @uses		TypeUtils
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.27 $
//!-----------------------------------------------------------------
class DbField extends FormField
{
	var $dataSource = array();		// @var dataSource array		"array()" Vetor de nodos XML que representam elementos de uma consulta SQL
	var $dataSourceLoaded = FALSE;	// @var dataSourceLoaded bool	"FALSE" Armazena o valor TRUE depois que a consulta for executada, evitando a repetiчуo da operaчуo
	var $isGrouping = FALSE;		// @var isGrouping bool			"FALSE" Indica que o datasource utiliza agrupamento simples
	var $_Db;						// @var _Db Db object			Conexуo com o banco de dados
	var $_Rs;						// @var _Rs ADORecordSet		Result set para manipulaчуo de registros do banco de dados

	//!-----------------------------------------------------------------
	// @function	DbField::DbField
	// @desc		Construtor da classe DbField
	// @param		&Form FormObject	Objeto Form onde o campo serс inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo щ membro de um campo composto
	// @access		public
	//!-----------------------------------------------------------------
	function DbField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		if ($this->isA('DbField', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'DbField'), E_USER_ERROR, __FILE__, __LINE__);
	}

	//!-----------------------------------------------------------------
	// @function	DbField::setRecordSet
	// @desc		Substitui o recordset do componente
	// @param		&Rs ADORecordSet	Novo recordset
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setRecordSet(&$Rs) {
		if (TypeUtils::isInstanceOf($Rs, 'ADORecordSet'))
			$this->_Rs =& $Rs;
	}

	//!-----------------------------------------------------------------
	// @function	DbField::onLoadNode
	// @desc		Mщtodo responsсvel por processar atributos e nodos filhos
	//				provenientes da especificaчуo XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (isset($children['DATASOURCE']))
			$this->dataSource = parent::parseDataSource($children['DATASOURCE']);
		else
			$this->dataSource = array();
	}

	//!-----------------------------------------------------------------
	// @function	DbField::onDataBind
	// @desc		Sobrecarrega o mщtodo onDataBind da classe FormField para
	//				resolver variсveis declaradas nos elementos do DATASOURCE
	//				associado a este campo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onDataBind() {
		parent::onDataBind();
		foreach ($this->dataSource as $name => $value) {
			if (preg_match("/~[^~]+~/", $value))
				$this->dataSource[$name] = $this->_Form->evaluateStatement($value);
		}
	}

	//!-----------------------------------------------------------------
	// @function	DbField::processDbQuery
	// @desc		Reњne as informaчѕes definidas no datasource da especificaчуo
	//				XML e constrѓi o conjunto de resultados
	// @param		fetchMode int	Fetch mode a ser utilizado
	// @param		debug bool		"FALSE" Habilitar ou nуo debug da consulta
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function processDbQuery($fetchMode=ADODB_FETCH_DEFAULT, $debug=FALSE) {
		$this->_Db = Db::getInstance($this->dataSource['CONNECTIONID']);
		$this->_Db->setDebug($debug);
		if ($this->dataSourceLoaded) {
			$this->_Rs->moveFirst();
		} else {
			$this->dataSourceLoaded = TRUE;
			if (!isset($this->dataSource['KEYFIELD'])) {
				$this->_Rs = $this->_Db->emptyRecordSet();
			} else {
				$oldMode = $this->_Db->setFetchMode($fetchMode);
				if (isset($this->dataSource['PROCEDURE'])) {
					$this->_Rs =& $this->_Db->execute(
						$this->_Db->getProcedureSql($this->dataSource['PROCEDURE']),
						FALSE, @$this->dataSource['CURSORNAME']
					);
					if ($this->_Rs === FALSE)
						$this->_Rs = $this->_Db->emptyRecordSet();
				} else {
					$Query = new QueryBuilder(
						$this->dataSource['KEYFIELD'] . ',' . $this->dataSource['DISPLAYFIELD'],
						$this->dataSource['LOOKUPTABLE'], $this->dataSource['CLAUSE'],
						$this->dataSource['GROUPBY'], $this->dataSource['ORDERBY']
					);
					if ($this->isGrouping) {
						$Query->addFields($this->dataSource['GROUPFIELD']);
						$Query->addFields($this->dataSource['GROUPDISPLAY']);
						$Query->prefixOrder($this->dataSource['GROUPDISPLAY']);
					}
					if (isset($this->dataSource['LIMIT']) && preg_match("/([0-9]+)(,[0-9]+)?/", trim($this->dataSource['LIMIT']), $matches))
						$this->_Rs =& $this->_Db->limitQuery($Query->getQuery(), intval($matches[1]), intval(@$matches[2]));
					else
						$this->_Rs =& $this->_Db->query($Query->getQuery());
				}
				$this->_Db->setFetchMode($oldMode);
			}
		}
	}
}
?>