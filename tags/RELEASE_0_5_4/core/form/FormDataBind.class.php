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
// $Header: /www/cvsroot/php2go/core/form/FormDataBind.class.php,v 1.45 2006/11/21 23:24:23 mpont Exp $
// $Date: 2006/11/21 23:24:23 $

// ------------------------------------------------
import('php2go.data.DataSet');
import('php2go.db.QueryBuilder');
import('php2go.form.Form');
import('php2go.template.Template');
import('php2go.util.service.ServiceJSRS');
// ------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormDataBind
// @desc		Esta classe, que possui funcionamento restrito no ambiente
//				Microsoft/Internet Explorer, gera um formul�rio que utiliza
//				Data Binding. O formul�rio gerado est� associado a uma fonte
//				de dados do tipo TDC (Tabular Data Control), ou seja, um
//				arquivo texto (ou CSV) contendo os dados da tabela
// @package		php2go.form
// @extends		Form
// @uses		DataSet
// @uses		QueryBuilder
// @uses		Template
// @author		Marcos Pont
// @version		$Revision: 1.45 $
// @note		O funcionamento deste componente � restrito ao Internet Explorer
// @note		Exemplo de uso:<br>
//				<pre>
//
//				$form = new FormDataBind('file.xml', 'file.tpl', 'formName', $doc, 'table', 'primary_key');
//				$form->setFormMethod('POST');
//				$form->setInputStyle('input_style');
//				$form->setButtonStyle('button_style');
//				$form->setDataSetQuery('columnA, columnB', 'table', 'active=1');
//				$form->setFilterSortOptions('columnA#Column A|columnB#Column B');
//				$content = $form->getContent();
//
//				</pre>
//!-----------------------------------------------------------------
class FormDataBind extends Form
{
    var $templateFile;		  		// @var templateFile string 		Nome do arquivo template do formul�rio
    var $tableName;					// @var tableName string 			Nome da tabela que est� sendo manipulada
    var $primaryKey;				// @var primaryKey string 			Chave prim�ria da tabela que est� sendo manipulada
    var $queryFields;				// @var queryFields string			Campos da consulta para gera��o da navega��o
    var $queryTables;				// @var queryTables string			Tabelas da consulta para gera��o da navega��o
    var $queryClause;				// @var queryClause string			Cl�usula WHERE de condi��o
    var $queryOrder;				// @var queryOrder string			Coluna ou colunas de ordena��o da consulta
    var $queryLimit;				// @var queryLimit int				Limite ou n�mero de registros desejados na consulta
    var $csvDbName;					// @var csvDbName string 			Nome do objeto de data bind utilizado
    var $csvFile;					// @var csvFile string 				Nome do arquivo CSV para armazenamento dos dados
    var $extraFunctions = array();	// @var extraFunctions array 		"array()" Vetor de fun��es a serem executadas nos bot�es de a��o/navega��o
    var $forcePost = FALSE;			// @var forcePost bool 				"FALSE" Salvar/Excluir com submiss�o do formul�rio e n�o com JSRS
    var $parsFilterSort = '';		// @var parsFilterSort string		"" Valores de campo/valor para filtragem/ordena��o, no modelo campo1#valor1|campo2#valor2|...|campon#valorn
	var $Template = NULL;			// @var Template Template object	"NULL" Objeto Template para manipula��o do arquivo indicado em $templateFile

	//!-----------------------------------------------------------------
	// @function	FormDataBind::FormDataBind
	// @desc		Constr�i a inst�ncia do objeto FormDataBind, inicializando
	// 				a conex�o ao banco, o template do conte�do do formul�rio
	// 				e as defini��es para cria��o da estrutura de Data Binding
	// @access		public
	// @param		xmlFile string				Arquivo XML da especifica��o do formul�rio
	// @param 		templateFile string			Arquivo template para gera��o da interface do formul�rio
	// @param 		formName string				Nome do formul�rio
	// @param 		&Document Document object	Objeto Document onde o formul�rio ser� inserido
	// @param		tplIncludes array			"array()" Vetor de valores para blocos de inclus�o no template
	// @param 		tableName string			Nome da tabela envolvida nos dados que ser�o manipulados
	// @param 		primaryKey string			Nome da coluna que representa a chave prim�ria da tabela indicada em $tableName
	//!-----------------------------------------------------------------
	function FormDataBind($xmlFile, $templateFile, $formName, &$Document, $tplIncludes=array(), $tableName, $primaryKey) {
		parent::Form($xmlFile, $formName, $Document);
		// inicializa e parseia o template principal
		$this->templateFile = $templateFile;
		$this->Template = new Template($templateFile);
		if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {
			foreach ($tplIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
		// configura��es principais da gera��o dos dados
		$this->csvDbName = "db_" . strtolower($tableName);
		$this->tableName = $tableName;
		$this->primaryKey = $primaryKey;
		// inicializa as imagens de ordem asc/desc com os valores pr�-definidos
		$this->icons['sortasc'] = PHP2GO_ICON_PATH . "fdb_order_asc.gif";
		$this->icons['sortdesc'] = PHP2GO_ICON_PATH . "fdb_order_desc.gif";
		// inicializa os handlers JSRS de persist�ncia
		$Service = new ServiceJSRS();
		$Service->registerHandler(array($this, '_saveRecord'), 'saveRecord');
		$Service->registerHandler(array($this, '_deleteRecord'), 'deleteRecord');
		$Service->handleRequest();
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setImageSortAsc
	// @desc		Configura o �cone de ordena��o ascendente
	// @param		igmAsc string		Caminho da nova imagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setImageSortAsc($imgAsc) {
		$this->icons['sortasc'] = $imgAsc;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setImageSortDesc
	// @desc		Configura o �cone de ordena��o descendente
	// @param		igmDesc string	Caminho da nova imagem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setImageSortDesc($imgDesc) {
		$this->icons['sortdesc'] = $imgDesc;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setDataSetQuery
	// @desc		Define a consulta SQL a ser utilizada para montar
	//				o dataset dos registros que ir�o popular o arquivo
	//				CSV. Os membros $fields, $tables e $clause s�o
	//				obrigat�rios
	// @param		fields string	Campos da consulta
	// @param		tables string	"NULL" Tabelas da consulta. Default � a tabela informada no construtor
	// @param		clause string	"NULL" Cl�usula de condi��o. Default � cl�usula vazia
	// @param		order string	"NULL" Cl�usula de ordena��o
	// @param		limit string	"NULL" Permite expressar um limite para a consulta
	// @return		void
	//!-----------------------------------------------------------------
	function setDataSetQuery($fields, $tables=NULL, $clause=NULL, $order=NULL, $limit=NULL) {
		$this->queryFields = $fields;
		$this->queryTables = $tables;
		$this->queryClause = $clause;
		if ($order)
			$this->queryOrder = $order;
		if ($limit)
			$this->queryLimit = $limit;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setFilterParameters
	// @desc		Configura a lista de op��es para filtragem e ordena��o
	// 				dos dados. A lista deve respeitar o formato
	// 				campo1#r�tulo1|campo2#r�tulo2|...|campoN#r�tuloN, onde
	// campoN		referencia nomes de campos no formul�rio
	// @param		options string	Lista de op��es de filtragem/ordena��o
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setFilterSortOptions($options) {
		$this->parsFilterSort = $options;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setExtraButtonFunction
	// @desc		Permite associar a um dos bot�es de a��o/navega��o uma
	// 				fun��o extra no evento 'onClick'
	// @param		button string		Nome do bot�o: FIRST, PREVIOUS, NEXT, LAST, NEW, EDIT, SAVE, DELETE ou CANCEL
	// @param		function string	Nome da fun��o de script a ser executada
	// @return 		bool Retorna FALSE se a fun��o n�o foi corretamente aplicada
	// @access		public
	//!-----------------------------------------------------------------
	function setExtraButtonFunction($button, $function) {
		$button = strtoupper($button);
		if (in_array($button, array('FIRST', 'PREVIOUS', 'NEXT', 'LAST', 'NEW', 'EDIT', 'SAVE', 'DELETE', 'CANCEL'))) {
			$this->extraFunctions[$button] = " onClick=setTimeout(\"" . str_replace("\"", "'", $function) . "\", 100);";
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::disableJsrs
	// @desc		Desabilita as opera��es de inser��o/altera��o/exclus�o
	// 				utilizando JSRS. Neste caso, estas opera��es dever�o
	// 				ser processadas fora da classe, atrav�s do tratamento
	// 				da submiss�o do formul�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function disableJsrs() {
		$this->forcePost = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::onPreRender
	// @desc		Gera todos os elementos do formul�rio no template:
	//				toolbar, se��es, campos, bot�es
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			$this->_createDbCsvFile();
			$this->_buildCsvDbToolbar();
			$sectionIds = array_keys($this->sections);
			foreach ($sectionIds as $sectionId) {
				$section =& $this->sections[$sectionId];
				$this->_buildSection($section);
			}
			$this->Template->onPreRender();
			parent::buildScriptCode();
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::display
	// @desc	  	Constr�i e retorna o c�digo HTML do formul�rio
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getContent() {
		$this->onPreRender();
		return $this->_buildFormStart() . $this->Template->getContent() . "</form>";
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::display
	// @desc		Constr�i e imprime o c�digo HTML do formul�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function display() {
		$this->onPreRender();
		print $this->_buildFormStart();
		$this->Template->display();
		print "</form>";
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_saveRecord
	// @desc		M�todo privado que responde � requisi��o de inser��o
	//				ou atualiza��o de um registro via JSRS
	// @param		values string	Conjunto de valores do registro
	// @param		table string	Nome da tabela
	// @param		pk string		Nome da chave prim�ria
	// @access		private
	// @return		mixed
	//!-----------------------------------------------------------------
	function _saveRecord($values, $table, $pk) {
		$Db =& Db::getInstance();
		// monta o vetor de campos
		$arrFields = array();
		$values = explode("#", $values);
		for($i=0,$s=sizeof($values); $i<$s; $i++) {
			$fields = explode("|", $values[$i]);
			if (sizeof($fields) == 2)
				$arrFields[$fields[0]] = $fields[1];
		}
		// executa insert ou update
		if (empty($arrFields[$pk])) {
			$res = @$Db->insert($table, $arrFields);
			if ($res)
				return $res;
		} else {
			$res = @$Db->update($table, $arrFields, "{$pk} = " . $Db->quoteString($arrFields[$pk]));
			if ($res)
				return 1;
		}
		return PHP2Go::getLangVal('ERR_CSV_DB_JSRS');
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_deleteRecord
	// @desc		M�todo privado que responde � requisi��o de exclus�o
	//				de um registro via JSRS
	// @param		table string	Nome da tabela
	// @param		pk string		Nome da chave prim�ria
	// @param		value mixed		Valor da chave prim�ria
	// @access		private
	// @return		mixed
	//!-----------------------------------------------------------------
	function _deleteRecord($table, $pk, $value) {
		$Db =& Db::getInstance();
		$res = @$Db->delete($table, "{$pk} = " . $Db->quoteString($value));
		if ($res)
			return 1;
		return PHP2Go::getLangVal('ERR_DB_CSV_JSRS');
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_createDbCsvFile
	// @desc		Cria o arquivo .csv que ser� utilizado para navega��o
	//				nos registros da tabela a partir dos resultados da
	//				consulta
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _createDbCsvFile() {
		// remove os arquivos anteriormente armazenados
		$dir = @opendir(PHP2GO_CACHE_PATH);
		while (FALSE !== ($file = readdir($dir))) {
			if (preg_match("~db.*\.csv~", $file)) {
				if (!@unlink(PHP2GO_CACHE_PATH . $file))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_DELETE_FILE', $file), E_USER_ERROR, __FILE__, __LINE__);
			}
		}
		// constru��o do dataset
		$Query = new QueryBuilder(TypeUtils::ifNull($this->queryFields, '*'), TypeUtils::ifNull($this->queryTables, $this->tableName), $this->queryClause, '', $this->queryOrder);
		$Query->setLimit($this->queryLimit);
		$DataSet =& $Query->createDataSet();
		// serializa o dataset em um arquivo CSV
		$this->csvFile = $this->csvDbName . '_' . time() . '.csv';
		$fp = @fopen(PHP2GO_CACHE_PATH . $this->csvFile, 'wb');
		if ($fp === FALSE) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', PHP2GO_CACHE_PATH . $this->csvFile), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			fputs($fp, implode(',', $DataSet->getFieldNames()) . "\n");
			while (!$DataSet->eof()) {
				$row = $DataSet->current();
				foreach ($row as $column => $value)
					$row[$column] = "'" . preg_replace("/\'/", "\\\'", $value) . "'";
				fputs($fp, implode(',', $row) . "\n");
				$DataSet->moveNext();
			}
			fclose($fp);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildCsvDbToolbar
	// @desc		Constr�i os bot�es e ferramentas de navega��o, manipula��o,
	// 				ordena��o e filtragem de registros e aplica os valores obtidos
	//				no template do formul�rio
	// @note 		Gera um erro caso a barra de navega��o n�o tenha sido
	// 				definida no template principal
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildCsvDbToolbar() {
		// constr�i a barra de ferramentas da classe a partir de um template auxiliar pr�-definido
		if ($this->Template->isVariableDefined("_ROOT.databind_toolbar")) {
			$toolbarValues = PHP2Go::getLangVal('FORM_DATA_BIND_TOOLBAR_VALUES');
			$Tpl = new Template(PHP2GO_TEMPLATE_PATH . "formdatabind.tpl");
			$Tpl->parse();
			$Tpl->assign('tableName', $this->tableName);
			$Tpl->assign('primaryKey', $this->primaryKey);
			$Tpl->assign('formName', $this->formName);
			$Tpl->assign('csvDbName', $this->csvDbName);
			$Tpl->assign('databindSource', PHP2GO_OFFSET_PATH . 'cache/' . $this->csvFile);
			$Tpl->assign('lang', $toolbarValues);
			$Tpl->assign('forcePost', ($this->forcePost ? 'true' : 'false'));
			$Tpl->assign('readonlyForm', ($this->readonly ? 'true' : 'false'));
			$Tpl->assign('globalDisabled', ($this->readonly ? ' disabled' : ''));
			$Tpl->assign('buttonStyle', parent::getButtonStyle());
			$Tpl->assign('inputStyle', parent::getInputStyle());
			$Tpl->assign('labelStyle', parent::getLabelStyle());
			$Tpl->assign('icons', $this->icons);
			$Tpl->assign('extraFunctions', $this->extraFunctions);
			$Tpl->assign('filterOptions', $this->_buildOptionsList('filter', $toolbarValues));
			$Tpl->assign('sortOptions', $this->_buildOptionsList('sort', $toolbarValues));
			$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/formdatabind.js');
			$this->Document->addScriptCode("\tInputMask.setup('{$this->formName}_gotoField', DigitMask);", 'Javascript', SCRIPT_END);
			$this->Template->assignByRef("_ROOT.databind_toolbar", $Tpl);
 		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_VARIABLE', array('databind_toolbar', $this->templateFile, 'databind_toolbar')), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildSection
	// @desc		Atribui no template os r�tulos e c�digos dos campos e
	//				bot�es de uma se��o de formul�rio
	// @param		&section FormSection object	Se��o do formul�rio
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildSection(&$section) {
		$sectionId = $section->getId();
		if ($section->isConditional()) {
			if ($section->isVisible()) {
				if (!$this->Template->isBlockDefined($sectionId)) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_SECTION_TPLBLOCK', array($section->getId(), $section->getId())), E_USER_ERROR, __FILE__, __LINE__);
				}
				$this->Template->createBlock($sectionId);
				$this->Template->assign("$sectionId.section_" . $sectionId, $section->name);
				for ($i = 0; $i < sizeof($section->getChildren()); $i++) {
					$object =& $section->getChild($i);
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSection($object);
					}
					else if ($section->getChildType($i) == 'BUTTON') {
						$this->Template->assignByRef("$sectionId." . $object->getName(), $object);
					}
					else if ($section->getChildType($i) == 'BUTTONGROUP') {
						for ($j=0; $j<sizeOf($object); $j++) {
							$button = $object[$j];
							$this->Template->assignByRef("$sectionId." . $button->getName(), $button);
						}
					}
					else if ($section->getChildType($i) == 'FIELD') {
						$this->Template->assign("$sectionId.label_" . $object->getName(), $object->getLabelCode($section->attributes['REQUIRED_FLAG'], $section->attributes['REQUIRED_COLOR'], $section->attributes['REQUIRED_TEXT']));
						$this->Template->assignByRef("$sectionId." . $object->getName(), $object);
					}
				}
			}
		// se��o normal
		} else {
			$this->Template->assign("_ROOT.section_" . $sectionId, $section->name);
			for ($i = 0; $i < sizeOf($section->getChildren()); $i++) {
				$object =& $section->getChild($i);
				if ($section->getChildType($i) == 'SECTION') {
					$this->_buildSection($object);
				}
				else if ($section->getChildType($i) == 'BUTTON') {
					$this->Template->assignByRef("_ROOT." . $object->getName(), $object);
				}
				else if ($section->getChildType($i) == 'BUTTONGROUP') {
					for ($j=0; $j<sizeOf($object); $j++) {
						$button = $object[$j];
						$this->Template->assignByRef("_ROOT." . $button->getName(), $button);
					}
				}
				else if ($section->getChildType($i) == 'FIELD') {
					$this->Template->assign("_ROOT.label_" . $object->getName(), $object->getLabelCode($section->attributes['REQUIRED_FLAG'], $section->attributes['REQUIRED_COLOR'], $section->attributes['REQUIRED_TEXT']));
					$this->Template->assignByRef("_ROOT." . $object->getName(), $object);
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildFormStart
	// @desc		Gera o c�digo HTML de defini��o do formul�rio mais os
	//				campos escondidos de controle (assinatura, �ltima posi��o
	//				acessada e ID de remo��o)
	// @access 		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildFormStart() {
		$target = (isset($this->actionTarget)) ? " target=\"" . $this->actionTarget . "\"" : '';
		$enctype = ($this->hasUpload) ? " enctype=\"multipart/form-data\"" : '';
		$signature = sprintf("\n<input type=\"hidden\" id=\"%s_signature\" name=\"%s\" value=\"%s\">", $this->formName, FORM_SIGNATURE, parent::getSignature());
		$lastPosition = sprintf("\n<input type=\"hidden\" name=\"lastposition\" value=\"%s\">", HttpRequest::getVar('lastposition'));
		$removeId = sprintf("\n<input type=\"hidden\" name=\"removeid\" value=\"%s\">", HttpRequest::getVar('removeid'));
		return sprintf("<form id=\"%s\" name=\"%s\" action=\"%s\" method=\"%s\" style=\"display:inline\"%s%s>%s%s%s\n",
			$this->formName, $this->formName, $this->formAction, $this->formMethod,
			$target, $enctype, $signature, $lastPosition, $removeId
		);
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildOptionsList
	// @desc		Constr�i a lista de op��es para as caixas de sele��o
	// 				dos campos de filtragem e ordena��o
	// @param		type string	Tipo: sort ou filter
	// @access		private
	// @return		string
	//!-----------------------------------------------------------------
	function _buildOptionsList($type, $toolbarValues) {
		switch ($type) {
			case 'sort' :
				$text = $toolbarValues['sortFirst'];
				break;
			case 'filter' :
				$text = $toolbarValues['filterFirst'];
				break;
			default:
				$text = "";
				break;
		}
		$options = "";
		$options .= "<option value=\"\">" . $text . "</option>";
		$pairs = explode("|", $this->parsFilterSort);
		for($i=0,$s=sizeof($pairs); $i<$s; $i++) {
			$pair = explode("#", $pairs[$i]);
			if (!empty($pair[0]) && !empty($pair[1]))
				$options .= "<option value=\"" . $pair[0] . "\">" . $pair[1] . "</option>";
		}
		return $options;
	}
}
?>