<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.data.DataSet');
import('php2go.db.QueryBuilder');
import('php2go.form.Form');
import('php2go.template.Template');
import('php2go.util.service.ServiceJSRS');

/**
 * Builds a form based on data binding
 *
 * The FormDataBind builds forms associated with a TDC (tabular data
 * control) data source. When the form loads, the class serializes the
 * query results in a CSV file. This file is loaded by the browser,
 * loaded, allowing the user to navigate through the records. Save
 * and delete operations are performed through JSRS requests.
 *
 * Example:
 * <code>
 * /* my_form.xml {@*}
 * <form method="post">
 *   <style input="input_style" button="button_style"/>
 *   <section name="Main">
 *     <editfield name="columnA" label="Column A"/>
 *     <editfield name="columnB" label="Column B"/>
 *   </section>
 * </form>
 * /* my_form.tpl {@*}
 * <div>
 *   <div>{$databind_toolbar}</div>
 *   <div>
 *     {$label_columnA}<br/>
 *     {$columnA}<br/>
 *     {$label_columnB}<br/>
 *     {$columnB}
 *   </div>
 * </div>
 * /* page.php {@*}
 * $doc = new Document('layout.tpl');
 * $form = new FormDataBind('my_form.xml', 'my_form.tpl', 'my_form', $doc, 'table', 'primary_key');
 * $form->setDataSetQuery('columnA, columnB', 'table', 'active=1');
 * $form->setFilterSortOptions('columnA#Column A|columnB#Column B');
 * $doc->assignByRef('main', $form);
 * </code>
 *
 * Forms built with this class work only under MS Internet Explorer
 * 5 or higher.
 *
 * @package form
 * @uses DataSet
 * @uses QueryBuilder
 * @uses ServiceJSRS
 * @uses Template
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormDataBind extends Form
{
    /**
     * Name of the table used to load data
     *
     * @var string
     */
	var $tableName;

	/**
	 * Primary key of the table used to load data
	 *
	 * @var string
	 */
    var $primaryKey;

    /**
     * Query fields (comma-separated)
     *
     * @var string
     */
    var $queryFields;

    /**
     * Query tables (including join operations)
     *
     * @var string
     */
    var $queryTables;

    /**
     * Query condition clause
     *
     * @var string
     */
    var $queryClause;

    /**
     * Query ordering clause
     *
     * @var string
     */
    var $queryOrder;

    /**
     * Query limit
     *
     * @var int
     */
    var $queryLimit;

    /**
     * Name of the data bind object
     *
     * @var string
     * @access private
     */
    var $csvDbName;

    /**
     * Name of the CSV file
     *
     * @var string
     * @access private
     */
    var $csvFile;

    /**
     * Extra event listeners for the toolbar buttons
     *
     * @var array
     * @access private
     */
    var $extraFunctions = array();

    /**
     * Whether to save form data using a JSRS request
     *
     * @var bool
     * @access private
     */
    var $jsrsSubmit = TRUE;

    /**
     * Data filters
     *
     * @var string
     * @access private
     */
    var $parsFilterSort = '';

    /**
     * Template used to render the form
     *
     * @var object Template
     */
	var $Template = NULL;

	/**
	 * Class constructor
	 *
	 * @param string $xmlFile Form XML specification
	 * @param string $templateFile Template file
	 * @param string $formName Form name
	 * @param Document &$Document Document instance in which the form will be inserted
	 * @param array $tplIncludes Hash array of template includes
	 * @param string $tableName Table used to load form data
	 * @param string $primaryKey Table's primary key
	 * @return FormDataBind
	 */
	function FormDataBind($xmlFile, $templateFile, $formName, &$Document, $tplIncludes=array(), $tableName, $primaryKey) {
		parent::Form($xmlFile, $formName, $Document);
		$this->Template = new Template($templateFile);
		if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {
			foreach ($tplIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
		$this->csvDbName = "db_" . strtolower($tableName);
		$this->tableName = $tableName;
		$this->primaryKey = $primaryKey;
		$this->icons['sortasc'] = PHP2GO_ICON_PATH . "fdb_order_asc.gif";
		$this->icons['sortdesc'] = PHP2GO_ICON_PATH . "fdb_order_desc.gif";
		$Service = new ServiceJSRS();
		$Service->registerHandler(array($this, '_saveRecord'), 'saveRecord');
		$Service->registerHandler(array($this, '_deleteRecord'), 'deleteRecord');
		$Service->handleRequest();
	}

	/**
	 * Get the name of the data bind object
	 *
	 * @return string
	 */
	function getDbName() {
		return $this->csvDbName;
	}

	/**
	 * Changes the "sortasc" icon used in the databind toolbar
	 *
	 * @param string $imgAsc Icon URL
	 */
	function setImageSortAsc($imgAsc) {
		$this->icons['sortasc'] = $imgAsc;
	}

	/**
	 * Changes the "sortdesc" icon used in the databind toolbar
	 *
	 * @param string $imgDesc Icon URL
	 */
	function setImageSortDesc($imgDesc) {
		$this->icons['sortdesc'] = $imgDesc;
	}

	/**
	 * Configure the SQL query used to build the CSV file
	 *
	 * By default, the class loads the table data using a
	 * 'select * from table' query. Calling this method,
	 * you're able to customize this query, by defining
	 * which fields should be fetched, join operations,
	 * a condition clause, an ordering clause or a row
	 * limit restriction.
	 *
	 * @param string $fields Query fields. Defaults to '*'
	 * @param string $tables Query tables. Defaults to the table name provided in the constructor
	 * @param string $clause Condition clause
	 * @param string $order Ordering clause
	 * @param int $limit Limit setting
	 */
	function setDataSetQuery($fields, $tables=NULL, $clause=NULL, $order=NULL, $limit=NULL) {
		$this->queryFields = $fields;
		$this->queryTables = $tables;
		$this->queryClause = $clause;
		if ($order)
			$this->queryOrder = $order;
		if ($limit)
			$this->queryLimit = $limit;
	}

	/**
	 * Define a set of data filters
	 *
	 * Data filters will be displayed inside the databind toolbar. Each field
	 * will be a valid option to filter or sort records. The $filters argument
	 * must be in the format field#label|field#label...
	 *
	 * Example:
	 * <code>
	 * $form->setFilterSortOptions("columnA#Column A|columnB#Column B");
	 * </code>
	 *
	 * @param string $filters Filter options
	 */
	function setFilterSortOptions($filters) {
		$this->parsFilterSort = $filters;
	}

	/**
	 * Binds an extra callback function with one of the toolbar buttons
	 *
	 * Example:
	 * <code>
	 * $form->setExtraButtonFunction("NEW", "myCallbackFunc()");
	 * </code>
	 *
	 * @param string $button Button name
	 * @param unknown_type $function Function call
	 * @return bool
	 */
	function setExtraButtonFunction($button, $function) {
		$button = strtoupper($button);
		if (in_array($button, array('FIRST', 'PREVIOUS', 'NEXT', 'LAST', 'NEW', 'EDIT', 'SAVE', 'DELETE', 'CANCEL'))) {
			$this->extraFunctions[$button] = " onClick=setTimeout(\"" . str_replace("\"", "'", $function) . "\", 100);";
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Disable form submission via JSRS
	 *
	 * Form will be submited using a regular POST request.
	 */
	function disableJsrs() {
		$this->jsrsSubmit = FALSE;
	}

	/**
	 * Prepares the form to be rendered
	 */
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

	/**
	 * Builds and returns the form's HTML code
	 *
	 * @return string
	 */
	function getContent() {
		$this->onPreRender();
		return $this->_buildFormStart() . $this->Template->getContent() . "</form>";
	}

	/**
	 * Builds and displays the form's HTML code
	 */
	function display() {
		$this->onPreRender();
		print $this->_buildFormStart();
		$this->Template->display();
		print "</form>";
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_saveRecord
	// @desc		Método privado que responde à requisição de inserção
	//				ou atualização de um registro via JSRS
	// @param		values string	Conjunto de valores do registro
	// @param		table string	Nome da tabela
	// @param		pk string		Nome da chave primária
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
	// @desc		Método privado que responde à requisição de exclusão
	//				de um registro via JSRS
	// @param		table string	Nome da tabela
	// @param		pk string		Nome da chave primária
	// @param		value mixed		Valor da chave primária
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
	// @desc		Cria o arquivo .csv que será utilizado para navegação
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
		// construção do dataset
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
	// @desc		Constrói os botões e ferramentas de navegação, manipulação,
	// 				ordenação e filtragem de registros e aplica os valores obtidos
	//				no template do formulário
	// @note 		Gera um erro caso a barra de navegação não tenha sido
	// 				definida no template principal
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildCsvDbToolbar() {
		// constrói a barra de ferramentas da classe a partir de um template auxiliar pré-definido
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
			$Tpl->assign('jsrsSubmit', ($this->jsrsSubmit ? 'true' : 'false'));
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
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_VARIABLE', array('databind_toolbar', $this->Template->Parser->tplBase['src'], 'databind_toolbar')), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildSection
	// @desc		Atribui no template os rótulos e códigos dos campos e
	//				botões de uma seção de formulário
	// @param		&section FormSection object	Seção do formulário
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
		// seção normal
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
	// @desc		Gera o código HTML de definição do formulário mais os
	//				campos escondidos de controle (assinatura, última posição
	//				acessada e ID de remoção)
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
	// @desc		Constrói a lista de opções para as caixas de seleção
	// 				dos campos de filtragem e ordenação
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