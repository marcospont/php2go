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
// $Header: /www/cvsroot/php2go/core/data/adapter/DataSetDb.class.php,v 1.8 2006/10/11 22:11:37 mpont Exp $
// $Date: 2006/10/11 22:11:37 $

//-----------------------------------------
import('php2go.data.adapter.DataAdapter');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		DataSetDb
// @desc		Esta classe tem como funcionalidade implementar um adaptador para
//				construir um DataSet a partir dos resultados de uma consulta ou
//				procedimento armazenado no banco de dados
// @package		php2go.data.adapter
// @extends		DataAdapter
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.8 $
// @note		Exemplo de uso:<pre>
//
//				# exemplo b�sico
//				$dataset =& DataSet::factory('db');
//				$dataset->load("select * from products");
//				while (!$dataset->eof()) {
//				&nbsp;&nbsp;&nbsp;&nbsp;print $dataset->getField('short_desc');
//				}
//
//				# utilizando uma conex�o ao banco diferente da default
//				$dataset =& DataSet::factory('db', array('connectionId' => 'CONN_ID'));
//				$dataset->load("select * from TABLE_NAME");
//				while ($row = $dataset->fetch()) {
//				&nbsp;&nbsp;&nbsp;&nbsp;print $row['column'];
//				}
//
//				</pre>
//!-----------------------------------------------------------------
class DataSetDb extends DataAdapter
{
	var $RecordSet = NULL;		// @var RecordSet ADORecordSet object	RecordSet para navega��o nos registros da consulta ao banco

	//!-----------------------------------------------------------------
	// @function	DataSetDb::DataSetDb
	// @desc		Construtor da classe
	// @param		params array	"array()" Vetor de par�metros de inicializa��o
	// @access		public
	//!-----------------------------------------------------------------
	function DataSetDb($params=array()) {
		parent::DataAdapter($params);
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::load
	// @desc		Constr�i o conjunto de dados a partir de uma consulta
	//				SQL, ou um statement ou procedure previamente preparados
	//				com o m�todo Db::prepare
	// @param		stmt mixed			Consulta ou statement SQL ou de procedure previamente preparados
	// @param		bindVars mixed		"FALSE" Vetor de vari�veis de amarra��o
	// @param		isProcedure bool	"FALSE" Indica se a fonte de dados � um procedimento armazenado no banco de dados
	// @param		cursorName string	"NULL" Nome da vari�vel do cursor (somente para o driver oci8)
	// @note		Define $isProcedure=TRUE quando for utilizar stored procedures
	//				para a montagem do dataset. Para os drivers oci8, mysqli, db2 e
	//				sybase, apenas � necess�rio fornecer o nome da procedure: o
	//				restante da sintaxe para execu��o do procedimento ser� inserido
	//				automaticamente
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function load($stmt, $bindVars=FALSE, $isProcedure=FALSE, $cursorName=NULL) {
		// cria a conex�o e prepara o statement
		$Db =& Db::getInstance(@$this->params['connectionId']);
		$Db->setDebug(@$this->params['debug']);
		if (TypeUtils::isString($stmt))
			$stmt = $Db->prepare(($isProcedure ? $Db->getProcedureSQL($stmt) : $stmt), $isProcedure);
		// executa o statement
		$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
		$this->RecordSet =& $Db->execute($stmt, $bindVars, ($isProcedure ? $cursorName : NULL));
		$Db->setFetchMode($oldMode);
		// seta as propriedades do result set
		if ($this->RecordSet) {
			$this->fieldCount = $this->RecordSet->fieldCount();
			$this->recordCount = $this->RecordSet->recordCount();
			$this->totalRecordCount = $this->recordCount;
			$this->_buildFieldNames();
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::loadSubSet
	// @desc		Constr�i um subconjunto dos dados, a partir de uma consulta SQL ou
	//				statement preparado, um deslocamento a partir do in�cio do conjunto
	//				e um tamanho (n�mero de linhas)
	// @param		offset int			Deslocamento a partir do in�cio do conjunto (baseado em zero)
	// @param		size int			Tamanho do conjunto
	// @param		stmt mixed			Consulta ou statement SQL ou de procedure previamente preparados
	// @param		bindVars array		"FALSE" Vetor de vari�veis de amarra��o
	// @param		isProcedure bool	"FALSE" Indica se o statement passado em $stmt � uma chamada de procedure
	// @param		cursorName string	"NULL" Nome da vari�vel do cursor (somente para o driver oci8)
	// @note		Quando o par�mtro $stmt for uma chamada para uma stored procedure, esta
	//				deve possuir tr�s paremtros obrigat�rios: RECORD_COUNT (par�metro de retorno,
	//				deve ser usado para retornar o total de registros na consulta), OFFSET (deslocamento
	//				inicial) e SIZE (tamanho da p�gina)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadSubSet($offset, $size, $stmt, $bindVars=FALSE, $isProcedure=FALSE, $cursorName=NULL) {
		// cria a conex�o e prepara o statement
		$Db =& Db::getInstance(@$this->params['connectionId']);
		$Db->setDebug(@$this->params['debug']);
		if (TypeUtils::isString($stmt))
			$stmt = $Db->prepare(($isProcedure ? $Db->getProcedureSQL($stmt) : $stmt), $isProcedure);
		// executa o statement
		$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
		if ($isProcedure) {
			$Db->bind($stmt, &$this->totalRecordCount, 'RECORD_COUNT');
			$Db->bind($stmt, $offset, 'OFFSET');
			$Db->bind($stmt, $size, 'SIZE');
			$this->RecordSet =& $Db->execute($stmt, $bindVars, ($isProcedure ? $cursorName : NULL));
		} else {
			$optimize = (isset($this->params['optimizeCount']) ? (bool)$this->params['optimizeCount'] : TRUE);
			$this->totalRecordCount = $Db->getCount($stmt, $bindVars, $optimize);
			$this->RecordSet =& $Db->limitQuery((TypeUtils::isArray($stmt) ? $stmt[0] : $stmt), $size, $offset, TRUE, $bindVars);
		}
		$Db->setFetchMode($oldMode);
		// seta as propriedades do result set
		if ($this->RecordSet) {
			$this->fieldCount = $this->RecordSet->fieldCount();
			$this->recordCount = $this->RecordSet->recordCount();
			$this->_buildFieldNames();
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::getField
	// @desc		Retorna o valor de um determinado campo a partir de seu �ndice ou de seu nome
	// @param		fieldId mixed
	// @access		public
	// @return		mixed
	//!-----------------------------------------------------------------
	function getField($fieldId) {
		if (!TypeUtils::isNull($this->RecordSet))
			return (isset($this->RecordSet->fields[$fieldId])) ? $this->RecordSet->fields[$fieldId] : NULL;
		return NULL;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::getAbsolutePosition
	// @desc		Retorna a posi��o atual do cursor de registros
	// @return		int Posi��o atual do cursor
	// @access		public
	//!-----------------------------------------------------------------
	function getAbsolutePosition() {
		if (!TypeUtils::isNull($this->RecordSet))
			return $this->RecordSet->absolutePosition();
		return 0;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::current
	// @desc		Retorna o registro apontado pela posi��o atual do cursor
	// @return		array Vetor contendo o registro atual
	// @access		public
	//!-----------------------------------------------------------------
	function current() {
		if (!TypeUtils::isNull($this->RecordSet))
			return $this->RecordSet->fields;
		return array();
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::fetch
	// @desc		Retorna um vetor contendo o registro atual
	// @return		array Vetor contendo o registro atual
	// @access		public
	//!-----------------------------------------------------------------
	function fetch() {
		if (!TypeUtils::isNull($this->RecordSet))
			return $this->RecordSet->fetchRow();
		return array();
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::fetchInto
	// @desc		Copia para o vetor passado no par�metro $dataArray o
	//				conte�do do registro atual
	// @param		&dataArray array	Vetor para armazenamento do registro
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function fetchInto(&$dataArray) {
		if (!TypeUtils::isNull($this->RecordSet))
			return $this->RecordSet->fetchInto($dataArray);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::eof
	// @desc		Verifica se o final do conjunto de resultados foi alcan�ado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function eof() {
		if (!TypeUtils::isNull($this->RecordSet))
			return $this->RecordSet->EOF;
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::move
	// @desc		Move o cursor para uma determinada posi��o
	// @param		recordNumber int	N�mero do registro
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function move($recordNumber) {
		if (!TypeUtils::isNull($this->RecordSet) && TypeUtils::isInteger($recordNumber))
			return $this->RecordSet->move($recordNumber);
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::moveNext
	// @desc		Move o cursor para a pr�xima posi��o, se existente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function moveNext() {
		if (!TypeUtils::isNull($this->RecordSet))
			return $this->RecordSet->moveNext();
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::movePrevious
	// @desc		Move o cursor para a posi��o anterior, se existente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function movePrevious() {
		if (!TypeUtils::isNull($this->RecordSet))
			return ($this->RecordSet->absolutePosition() > 1) ? $this->RecordSet->move($this->RecordSet->absolutePosition()-1) : FALSE;
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	DataSetDb::_buildFieldNames
	// @desc		Monta um vetor contendo os nomes das colunas presentes no conjunto de dados
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildFieldNames() {
		$this->fieldNames = array();
		if (!TypeUtils::isNull($this->RecordSet)) {
			for ($i=0, $s=$this->RecordSet->fieldCount(); $i<$s; $i++) {
				$FieldObject =& $this->RecordSet->fetchField($i);
				$this->fieldNames[] = $FieldObject->name;
			}
		}
	}
}
?>
