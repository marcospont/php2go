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
// $Header: /www/cvsroot/php2go/core/data/adapter/DataSetXml.class.php,v 1.6 2006/05/07 15:10:49 mpont Exp $
// $Date: 2006/05/07 15:10:49 $

//-----------------------------------------
import('php2go.data.adapter.DataAdapter');
import('php2go.util.AbstractList');
import('php2go.xml.XmlDocument');
//-----------------------------------------

// @const DS_XML_CDATA	"1"
// Representa conjuntos de dados XML onde as colunas estão em seções CDATA
define('DS_XML_CDATA', 1);
// @const DS_XML_ATTR "2"
// Representa conjuntos de dados XML onde as colunas estão armazenadas em atributos
define('DS_XML_ATTR', 2);

//!-----------------------------------------------------------------
// @class		DataSetXml
// @desc		Implementa um adaptador para a construção de um DataSet baseado
//				em um arquivo XML.<br><br>
//				Os arquivos carregados para a classe podem possuir dois formatos:<br>
//				- um deles interpreta um nodo raiz, cada registro do conjunto como um
//				nodo e cada coluna do registro como um nodo no terceiro nível da
//				árvore, contendo o valor do campo na CDATA-section;<br>
//				- o outro formato interpreta um nodo raiz, um nodo para cada registro 
//				e as colunas do registro como atributos do nodo
// @package		php2go.data.adapter
// @extends		DataAdapter
// @uses		AbstractList
// @uses		ListIterator
// @uses		TypeUtils
// @uses		XmlParser
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class DataSetXml extends DataAdapter 
{
	var $Iterator;				// @var Iterator ListIterator object	Objeto Iterator para navegação nos registros do conjunto
	var $rootAttrs;				// @var rootAttrs array					Vetor de atributos da tag raiz do XML
	var $structType;			// @var structType int					Indica a forma como os dados estão estruturados no arquivo XML	
	var $fields;				// @var fields array					Armazena as colunas do registro apontado pelo cursor
	var $eof = TRUE;			// @var eof bool						"FALSE" Indica se o final do conjunto foi alcançado
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::DataSetXml
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"array()" Vetor de parâmetros de inicialização
	//!-----------------------------------------------------------------
	function DataSetXml($params=array()) {
		parent::DataAdapter($params);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::load
	// @desc		Constrói o conjunto de dados a partir do caminho completo
	//				de um arquivo XML armazenado no servidor
	// @access		public
	// @param		fileName string		Caminho completo do arquivo XML
	// @param		structType int		"DS_XML_CDATA" Formato dos registros no arquivo (vide constantes da classe)	
	// @return		bool
	//!-----------------------------------------------------------------
	function load($fileName, $structType=DS_XML_CDATA) {
		$this->structType = $structType;
		$XmlDocument = new XmlDocument();
		$XmlDocument->parseXml($fileName);
		$RootNode =& $XmlDocument->getRoot();
		$this->rootAttrs = $RootNode->getAttributes();
		if ($RootNode->hasChildren()) {
			$DataList = new AbstractList($RootNode->getChildNodes());
			$this->Iterator =& $DataList->iterator();
			$this->recordCount = $RootNode->getChildrenCount();
			$this->fields = $this->_buildRecord($this->Iterator->next());
			$this->fieldNames = array_keys($this->fields);
			$this->fieldCount = sizeof($this->fieldNames);
			$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::loadSubSet
	// @desc		Monta um subconjunto dos registros contidos no arquivo XML
	//				a partir de um deslocamento, um tamanho, o nome e o formato
	//				do arquivo
	// @access		public
	// @param		offset int			Deslocamento a partir do início do conjunto (baseado em zero)
	// @param		size int			Tamanho do subconjunto
	// @param		fileName string		Caminho completo do arquivo XML
	// @param		structType int		"DS_XML_CDATA" Formato dos registros no arquivo (vide constantes da classe)
	// @return		bool
	//!-----------------------------------------------------------------
	function loadSubSet($offset, $size, $fileName, $structType=DS_XML_CDATA) {
		$this->structType = $structType;
		$XmlDocument = new XmlDocument();
		$XmlDocument->parseXml($fileName);
		$RootNode =& $XmlDocument->getRoot();		
		$subSet = array_slice($RootNode->getChildNodes(), $offset, $size);		
		if ($RootNode->hasChildren() && sizeof($subSet) > 0) {
			$DataList = new AbstractList($subSet);
			$this->Iterator =& $DataList->iterator();
			$this->recordCount = sizeof($subSet);
			$this->totalRecordCount = $RootNode->getChildrenCount();
			$this->fields = $this->_buildRecord($this->Iterator->next());
			$this->fieldNames = array_keys($this->fields);
			$this->fieldCount = sizeof($this->fieldNames);
			$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::getRootAttributes
	// @desc		Busca os atributos da tag raiz do dataset XML
	// @access		public
	// @return		mixed Atributos da raiz do dataset XML
	//!-----------------------------------------------------------------
	function getRootAttributes() {
		return (isset($this->rootAttrs) ? $this->rootAttrs : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::getField
	// @desc		Retorna o valor de um determinado campo a partir de seu índice ou de seu nome
	// @access		public
	// @param		fieldId mixed	
	// @return		mixed Valor do campo
	//!-----------------------------------------------------------------
	function getField($fieldId) {
		if (!TypeUtils::isNull($this->Iterator))
			return isset($this->fields[$fieldId]) ? $this->fields[$fieldId] : NULL;						
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::getAbsolutePosition
	// @desc		Retorna a posição atual do cursor de registros
	// @access		public
	// @return		int Posição atual do cursor
	//!-----------------------------------------------------------------
	function getAbsolutePosition() {
		if (!TypeUtils::isNull($this->Iterator))
			return $this->Iterator->getCurrentIndex();
		return 0;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::current
	// @desc		Retorna o registro apontado pela posição atual do cursor
	// @access		public
 	// @return		array Vetor contendo o registro atual
	//!-----------------------------------------------------------------
	function current() {
		if (!TypeUtils::isNull($this->Iterator))
			return $this->fields;
		return array();
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::fetch
	// @desc		Retorna um vetor contendo o registro atual
	// @access		public
	// @return		array Vetor contendo o registro atual
	//!-----------------------------------------------------------------
	function fetch() {
		if (!TypeUtils::isNull($this->Iterator) && !$this->eof()) {
			$dataArray = $this->fields;
			$this->moveNext();
			return $dataArray;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::fetchInto
	// @desc		Copia para o vetor passado no parâmetro $dataArray o
	//				conteúdo do registro atual
	// @access		public
	// @param		&dataArray array	Vetor para armazenamento do registro
	// @return		bool
	//!-----------------------------------------------------------------
	function fetchInto(&$dataArray) {
		if (!TypeUtils::isNull($this->Iterator) && !$this->eof()) {
			$dataArray = $this->fields;
			$this->moveNext();
			return TRUE;
		}
		return FALSE;
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::eof
	// @desc		Verifica se o final do conjunto de resultados foi alcançado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function eof() {
		if (!TypeUtils::isNull($this->Iterator))
			return $this->eof;
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::move
	// @desc		Move o cursor para uma determinada posição
	// @access		public
	// @param		recordNumber int	Número do registro
	// @return		bool
	//!-----------------------------------------------------------------
	function move($recordNumber) {
		if (!TypeUtils::isNull($this->Iterator) && TypeUtils::isInteger($recordNumber)) {
			if ($this->Iterator->moveToIndex($recordNumber)) {
				$this->fields = $this->_buildRecord($this->Iterator->next());
				$this->eof = FALSE;
				return TRUE;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::moveNext
	// @desc		Move o cursor para a próxima posição, se existente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function moveNext() {		
		if (!TypeUtils::isNull($this->Iterator) && $this->Iterator->hasNext()) {
			$this->fields = $this->_buildRecord($this->Iterator->next());
			return TRUE;
		}
		$this->eof = TRUE;
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::movePrevious
	// @desc		Move o cursor para a posição anterior, se existente
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function movePrevious() {
		if (!TypeUtils::isNull($this->Iterator) && $this->getAbsolutePosition() > 0) {
			$this->fields = $this->_buildRecord($this->Iterator->previous());
			if ($this->eof())
				$this->eof = FALSE;
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSetXml::_buildRecord
	// @desc		Monta um vetor associativo para uma linha de dados,
	//				a partir dos nomes dos campos e dos valores de uma linha
	// @access		private
	// @param		RecordNode XmlNode object	Representa um registro no conjunto de dados
	// @return		array Registro montado
	//!-----------------------------------------------------------------
	function _buildRecord($RecordNode) {
		switch ($this->structType) {
			// formato de arquivo XML onde as colunas estão em seções CDATA
			case DS_XML_CDATA :
				$result = array();
				$fields = $RecordNode->getChildrenTagsArray();
				foreach ($fields as $name => $node) {
					$result[$name] = $node->getData();
				}
				return $result;
			// formato de arquivo onde as colunas estão nos atributos
			case DS_XML_ATTR :
				return $RecordNode->getAttributes();
			// padrão é formato CDATA
			default :
				$result = array();
				$fields = $RecordNode->getChildrenTagsArray();
				foreach ($fields as $name => $node) {
					$result[$name] = $node->getData();
				}
				return $result;			
		}
		return array();
	}	
}
?>