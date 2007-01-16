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

import('php2go.data.PagedDataSet');
import('php2go.data.ReportSimpleSearch');
import('php2go.db.QueryBuilder');
import('php2go.net.Url');
import('php2go.net.UserAgent');
import('php2go.text.StringUtils');
import('php2go.xml.XmlDocument');
import('php2go.util.Callback');
import('php2go.util.Statement');

/**
 * Default number of visible page links
 * when using REPORT_PAGING_DEFAULT paging style
 */
define('REPORT_DEFAULT_VISIBLE_PAGES', 10);
/**
 * Default lines per page, to be used on print page breaks
 */
define('REPORT_DEFAULT_PAGE_BREAK', 20);
/**
 * Column sizes defined by the developer
 */
define('REPORT_COLUMN_SIZES_CUSTOM', 1);
/**
 * Fixed and equal sizes for all columns
 */
define('REPORT_COLUMN_SIZES_FIXED', 2);
/**
 * Don't use width attribute on columns. Size will be defined by the browser rendering engine
 */
define('REPORT_COLUMN_SIZES_FREE', 3);
/**
 * Default paging style: displays N links to another pages and links to another sets of pages
 */
define('REPORT_PAGING_DEFAULT', 1);
/**
 * Displays links or buttons pointing to previous and next pages
 */
define('REPORT_PREVNEXT', 2);
/**
 * Displays links or buttons pointing to first, previous, next and last pages
 */
define('REPORT_FIRSTPREVNEXTLAST', 3);

/**
 * Builds and displays paged data sets loaded from a database
 *
 * Based on a XML specification, which contains data source
 * information and configuration settings, and a template file
 * that determines user interface, the Report class builds
 * fully featured data sets split into pages.
 *
 * It offers lots of features, like automatic generation of
 * pagination links, simple search tool with highlight support,
 * multiple page layouts, ordering, single level grouping and
 * much more.
 *
 * Basic example of a XML file:
 * <code>
 * <?xml version="1.0" encoding="iso-8859-1"?>
 * <report title="My Report">
 *   <layout grid="T" sortable="T">
 *     <pagination style="REPORT_PAGING_PREVNEXT">
 *       <param name="useButtons" value="T"/>
 *     </pagination>
 *     <style altstyle="odd,even"/>
 *     <columns>
 *       <column name="name" alias="Name"/>
 *       <column name="address" alias="Address"/>
 *       <column name="category" alias="Category" help="Client category"/>
 *     </columns>
 *   </layout>
 *   <datasource>
 *     <fields>name, address, category</fields>
 *     <tables>client</tables>
 *     <clause/>
 *     <groupby/>
 *     <orderby/>
 *   </datasource>
 * </report>
 * </code>
 *
 * Basic example of a template file:
 * <code>
 * <table align='center' width='700'>
 *   <tr>
 *     <td align='center' colspan='2'>{$title}</td>
 *   </tr>
 *   <tr>
 *     <td align='left'>{$rows_per_page}</td>
 *     <td align='right'>{$page_links}</td>
 *   </tr>
 * </table>
 * <table align='center' width='700'>
 *   <!-- START BLOCK : loop_line -->
 *   <tr>
 *     <!-- START BLOCK : loop_header_cell -->
 *     <th width='{$col_width}'>{$col_help}{$col_name}{$col_order}</th>
 *     <!-- END BLOCK : loop_header_cell -->
 *     <!-- START BLOCK : loop_cell -->
 *     <td width='{$col_width}' class='{$alt_style}'>{$col_data}</td>
 *     <!-- END BLOCK : loop_cell -->
 *   </tr>
 *   <!-- END BLOCK : loop_line -->
 * </table>
 * <table align='center' width='700'>
 *   <tr>
 *     <td align='left'>{$this_page}</td>
 *     <td align='right'>{$row_interval}</td>
 *   </tr>
 * </table>
 * </code>
 *
 * @package data
 * @uses Db
 * @uses HtmlUtils
 * @uses HttpRequest
 * @uses QueryBuilder
 * @uses ReportSimpleSearch
 * @uses Statement
 * @uses Template
 * @uses XmlDocument
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision
 */
class Report extends PagedDataSet
{
	/**
	 * Report id
	 *
	 * @var string
	 */
	var $id;

	/**
	 * Report title
	 *
	 * @var string
	 */
	var $title;

	/**
	 * Debug flag
	 *
	 * @var bool
	 */
	var $debug = FALSE;

	/**
	 * Base URI for all links (paging, sorting, ...)
	 *
	 * Defaults to $_SERVER['PHP_SELF'].
	 *
	 * @var string
	 */
	var $baseUri;

	/**
	 * Attributes of the XML root
	 *
	 * @var array
	 */
	var $rootAttrs = array();

	/**
	 * Whether grid mode is enabled
	 *
	 * @var bool
	 */
	var $hasHeader = FALSE;

	/**
	 * Number of records per row (when grid mode is disabled)
	 *
	 * @var int
	 */
	var $numCols = 1;

	/**
	 * Indicates sorting is enabled
	 *
	 * @var bool
	 */
	var $isSortable = TRUE;

	/**
	 * Enable/disable print mode
	 *
	 * @var bool
	 */
	var $isPrintable = FALSE;

	/**
	 * Rows per page (when building print page breaks)
	 *
	 * @var int
	 */
	var $pageBreak;

	/**
	 * Extra URL variables that must be included in all report links (paging, sorting, filtering, ...)
	 *
	 * @var string
	 */
	var $extraVars;

	/**
	 * Template block used to generate cells
	 * to fill incomplete lines (when grid mode
	 * is disabled)
	 *
	 * @var string
	 */
	var $emptyBlock;

	/**
	 * Set of icons used by the class
	 *
	 * @var array
	 */
	var $icons = array();

	/**
	 * Report handlers (pageStart, pageEnd, line, column)
	 *
	 * @var array
	 * @access private
	 */
	var $handlers = array();

	/**
	 * Holds pagination settings
	 *
	 * @var array
	 * @access private
	 */
	var $pagination = array();

	/**
	 * Holds JS event listeners (onChangePage, onSort, onSearch)
	 *
	 * @var array
	 * @access private
	 */
	var $jsListeners = array();

	/**
	 * Style settings
	 *
	 * @var array
	 * @access private
	 */
	var $style = array();

	/**
	 * Substitution variables for the XML file
	 *
	 * @var array
	 * @access private
	 */
	var $variables = array();

	/**
	 * Column settings
	 *
	 * @var array
	 * @access private
	 */
	var $columns = array();

	/**
	 * Column names that must be hidden
	 *
	 * @var array
	 * @access private
	 */
	var $hidden = array();

	/**
	 * Column names that can't be used to sort the report
	 *
	 * @var array
	 * @access private
	 */
	var $unsortable = array();

	/**
	 * Custom column sizes
	 *
	 * @var array
	 * @access private
	 */
	var $colSizes;

	/**
	 * Column sizes mode
	 *
	 * @var int
	 * @access private
	 */
	var $colSizesMode;

	/**
	 * Column name(s) to be used to group report data
	 *
	 * @var array
	 * @access private
	 */
	var $group;

	/**
	 * Column name(s) that must be displayed to identify a group of data
	 *
	 * @var array
	 * @access private
	 */
	var $groupDisplay = array();

	/**
	 * Empty template settings
	 *
	 * @var array
	 * @access private
	 */
	var $emptyTemplate;

	/**
	 * Simple search template settings
	 *
	 * @var array
	 * @access private
	 */
	var $searchTemplate;

	/**
	 * Template instance used to build and display the report
	 *
	 * @var object Template
	 */
	var $Template = NULL;

	/**
	 * Reference to the Document where the report is inserted
	 *
	 * @var object Document
	 * @access private
	 */
	var $_Document = NULL;

	/**
	 * Simple search tool based on filters defined in the XML file
	 *
	 * @var object ReportSimpleSearch
	 * @access private
	 */
	var $_SimpleSearch = NULL;

	/**
	 * Indicates report data was already been loaded
	 *
	 * @var bool
	 * @access private
	 */
	var $_loaded = FALSE;

	/**
	 * Data source settings (connection ID, SQL query)
	 *
	 * @var array
	 * @access private
	 */
	var $_dataSource = array();

	/**
	 * SQL code used to build the internal data set
	 *
	 * @var string
	 * @access private
	 */
	var $_sqlCode = '';

	/**
	 * Bind vars to be used in the SQL query or procedure call
	 *
	 * @var array
	 * @access private
	 */
	var $_bindVars = array();

	/**
	 * Used to control data grouping
	 *
	 * @var mixed
	 * @access private
	 */
	var $_currentGroup;

	/**
	 * Current sort column
	 *
	 * @var string
	 * @access private
	 */
	var $_order;

	/**
	 * Current sort type
	 *
	 * @var string
	 * @access private
	 */
	var $_orderType;

	/**
	 * Class constructor
	 *
	 * Parses the XML specification and initializes the internal template.
	 *
	 * @param string $xmlFile Path to the XML file
	 * @param string $templateFile Path to the template file
	 * @param Document &$Document Document where the report will be inserted
	 * @param array $tplIncludes Include blocks for the template
	 * @return Report
	 */
	function Report($xmlFile, $templateFile, &$Document, $tplIncludes=array()) {
		parent::PagedDataSet('db');
		if (!TypeUtils::isInstanceOf($Document, 'Document'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		$this->id = sprintf("%u", crc32($xmlFile . $templateFile));
		$this->baseUri = HttpRequest::basePath();
		$this->pagination = array(
			'visiblePages' => REPORT_DEFAULT_VISIBLE_PAGES,
			'style' => array(
				REPORT_PAGING_DEFAULT, array(
					'useSymbols' => FALSE,
					'useButtons' => FALSE,
					'hideInvalid' => TRUE
				)
			)
		);
		$this->style = array(
			'help' => 'BGCOLOR,"#000000",FGCOLOR,"#ffffff"'
		);
		$this->colSizesMode = REPORT_COLUMN_SIZES_FREE;
		$this->icons = array(
			'orderasc' => PHP2GO_ICON_PATH . "report_order_asc.gif",
			'orderdesc' => PHP2GO_ICON_PATH . "report_order_desc.gif",
			'help' => PHP2GO_ICON_PATH . "help.gif"
		);
		$this->Template = new Template($templateFile);
		if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {
			foreach ($tplIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
		$this->_order = HttpRequest::get('order');
		$this->_orderType = TypeUtils::ifNull(HttpRequest::get('ordertype'), 'a');
		$this->_Document =& $Document;
		$this->_SimpleSearch = new ReportSimpleSearch();
		// parse layout settings from the global configuration
		$globalConf = PHP2Go::getConfigVal('REPORTS', FALSE);
		if ($globalConf)
			$this->_loadGlobalSettings($globalConf);
		$this->_processXml($xmlFile);
	}

	/**
	 * Class destructor
	 */
	function __destruct() {
		parent::__destruct();
		unset($this);
	}

	/**
	 * Get SQL code or procedure call used to build the internal data set
	 *
	 * @return string
	 */
	function getSql() {
		return $this->_sqlCode;
	}

	/**
	 * Builds an URL pointing to the first report page
	 *
	 * Returns FALSE if we're at the first page.
	 *
	 * @return string|FALSE
	 */
	function getFirstPageUrl() {
		return $this->_generatePageLink(1);
	}

	/**
	 * Builds an URL pointing to the last report page
	 *
	 * Returns FALSE if we're at the last page.
	 *
	 * @return string|FALSE
	 */
	function getLastPageUrl() {
		return $this->_generatePageLink(parent::getPageCount());
	}

	/**
	 * Builds an URL pointing to the previous report page
	 *
	 * Returns FALSE if we're at the first page.
	 *
	 * @return string|FALSE
	 */
	function getPreviousPageUrl() {
		if ($previousPage = parent::getPreviousPage())
			return $this->_generatePageLink($previousPage);
		return FALSE;
	}

	/**
	 * Builds an URL pointing to the next report page
	 *
	 * Returns FALSE if we're at the last page.
	 *
	 * @return string|FALSE
	 */
	function getNextPageUrl() {
		if ($nextPage = parent::getNextPage())
			return $this->_generatePageLink($nextPage);
		return FALSE;
	}

	/**
	 * Set report title
	 *
	 * @param string $title New title
	 * @param bool $docTitle Whether report title must be appended in the {@link _Document}
	 */
	function setTitle($title, $docTitle=FALSE) {
		$this->title = $title;
		if (!!$docTitle)
			$this->_Document->appendTitle($this->title, TRUE);
	}

	/**
	 * Set base URI for all links built by the class
	 *
	 * @param string $uri Base URI
	 */
	function setBaseUri($uri) {
		$this->baseUri = $uri;
	}

	/**
	 * Enable grid mode
	 */
	function useHeader() {
		$this->hasHeader = TRUE;
	}

	/**
	 * Define how much records must be displayed per line
	 *
	 * This method only take effect when grid mode is disabled.
	 *
	 * @param int $numCols Records per line
	 * @see hasHeader
	 * @see useHeader
	 */
	function setColumns($numCols) {
		$this->numCols = max(intval($numCols), 1);
	}

	/**
	 * Disable sorting links on grid header
	 */
	function disableOrderByLinks() {
		$this->isSortable = FALSE;
	}

	/**
	 * Enable print mode for this report
	 *
	 * @param int $pageBreak Lines per page (to generate CSS based page breaks)
	 */
	function isPrintable($pageBreak) {
		@set_time_limit(0);
		$this->isPrintable = TRUE;
		$pageBreak = intval($pageBreak);
		$this->pageBreak = ($pageBreak ? $pageBreak : REPORT_DEFAULT_PAGE_BREAK);
		$this->_SimpleSearch->clear();
	}

	/**
	 * Set the piece of query string that should be included in
	 * all links built by the class
	 *
	 * <code>
	 * $report = new Report('report.xml', 'report.tpl', $doc);
	 * $report->setExtraVars('some_param=some_value');
	 * </code>
	 *
	 * You can get the same effect by calling:
	 * <code>
	 * $report->setBaseUri(HttpRequest::basePath() . '?some_param=some_value');
	 * </code>
	 *
	 * @param string $extraVars Extra URL arguments
	 */
	function setExtraVars($extraVars) {
		if (ereg("[^=]+=[^=]+", $extraVars)) {
			$this->extraVars = ltrim($extraVars);
			if ($this->extraVars[0] == '&') {
				$this->extraVars = substr($this->extraVars, 1);
			}
		}
	}

	/**
	 * Set the template block to be used to generate empty
	 * cells for an incomplete line
	 *
	 * When grid mode is disabled, sometimes the total number
	 * of records is not divisible by the number of records per
	 * line. Thus, one or more cell blocks of the incomplete line
	 * won't contain any data. Use this method to give this cell
	 * blocks a better appearance.
	 *
	 * <code>
	 * /* inside your template file {@*}
	 * <!-- START BLOCK : loop_cell -->
	 * <td><table width="100%">
	 *   <tr>
	 *     <td>
	 *       {$name_alias}: {$name}<br/>
	 *       {$address_alias}: {$address}
	 *     </td>
	 *   </tr>
	 * </tr></table></td>
	 * <!-- END BLOCK : loop_cell -->
	 * <!-- START BLOCK : empty_cell -->
	 * <td>&nbsp;</td>
	 * <!-- END BLOCK : empty_cell -->
	 *
	 * /* inside your PHP file {@*}
	 * $report->setEmptyBlock('empty_cell');
	 * </code>
	 *
	 * @param string $blockName Template block name
	 */
	function setEmptyBlock($blockName) {
		$this->emptyBlock = $blockName;
	}

	/**
	 * Define the number of visible page links when
	 * using {@link REPORT_PAGING_DEFAULT} as paging style
	 *
	 * @param int $pages Number of page links
	 */
	function setVisiblePages($pages) {
		$this->pagination['visiblePages'] = max(intval($pages), 1);
	}

	/**
	 * Set paging style and paging parameters
	 *
	 * Paging parameters (not applicable when style == {@link REPORT_PAGING_DEFAULT}):
	 * # useButtons : use buttons instead of normal anchor tags
	 * # useSymbols : use symbols (<<, <, >, >>) instead of internationalized messages
	 * # hideInvalid : hide invalid navigation actions
	 *
	 * @param int $style Paging style: {@link REPORT_PAGING_DEFAULT}, {@link REPORT_PAGING_FIRSTPREVNEXTLAST} or {@link REPORT_PAGING_PREVNEXT}
	 * @param array $params Set of paging parameters
	 */
	function setPagingStyle($style, $params = NULL) {
		$this->pagination['style'][0] = intval($style);
		if (is_array($params)) {
			foreach ($params as $key => $value) {
				switch ($key) {
					case 'useButtons' :
					case 'useSymbols' :
					case 'hideInvalid' :
						$this->pagination['style'][1][$key] = (bool)$value;
						break;
				}
			}
		}
	}

	/**
	 * Set CSS classes to be used on the report
	 *
	 * @param string $link CSS class for links
	 * @param string $filter CSS class for simple search fields
	 * @param string $button CSS class for paging and simple search buttons
	 * @param string $title CSS class for report title
	 * @param string $header CSS class for headers, when grid mode is enabled
	 */
	function setStyleMapping($link='', $filter='', $button='', $title='', $header='') {
		if (trim($header) == '')
			$header = $link;
		$this->style = array(
			'link' => $link,
			'filter' => $filter,
			'button' => $button,
			'title' => $title,
			'header' => $header
		);
	}

	/**
	 * Set alternating class names for table rows
	 *
	 * This feature is enabled when {$alt_style} variable is declared
	 * inside the "loop_cell" template block
	 * <code>
	 * <!-- START BLOCK : loop_cell -->
	 * <td class="{$alt_style}">{$col_data}</td>
	 * <!-- END BLOCK : loop_cell -->
	 * </code>
	 *
	 * Accepts a variable number of CSS classes. These classes alternate
	 * as the rows are displayed.
	 */
	function setAlternateStyle() {
		if (func_num_args() < 2)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MIN_ALT_STYLE'), E_USER_ERROR, __FILE__, __LINE__);
		else
			$this->style['altstyle'] = func_get_args();
	}

	/**
	 * Set highlight colors for search terms on the simple search results
	 *
	 * @param string $fgColor Foreground color
	 * @param string $bgColor Background color
	 */
	function enableHighlight($fgColor, $bgColor = "") {
		$this->style['highlight'] = "color:$fgColor";
		if ($bgColor != "")
			$this->style['highlight'] .= ";background-color:$bgColor";
	}

	/**
	 * Create/replace a variable
	 *
	 * Variables are declared inside some special nodes and attributes of
	 * the XML specification. They can be resolved from the global scope
	 * (request, session, cookies, registry) or can be set manually through
	 * this method.
	 *
	 * @param string $name Name
	 * @param mixed $value Value
	 */
	function setVariable($name, $value) {
		if (isset($this->variables[$name]))
			$this->variables[$name]['value'] = $value;
		else
			$this->variables[$name] = array(
				'value' => $value
			);
	}

	/**
	 * Set report grouping
	 *
	 * If $display is missing, $groupBy columns will be used.
	 * # when grid mode is enabled, grouping columns aren't displayed
	 * # when grid mode is disabled, grouping columns aren't exposed to the template
	 *
	 * @param string|array $groupBy Column or columns to group by
	 * @param string|array $display Column or columns used to display a group
	 */
	function setGroup($groupBy, $display = '') {
		$this->group = (!is_array($groupBy) ? array($groupBy) : $groupBy);
		if ($display == '')
			$this->groupDisplay = (!is_array($groupBy) ? array($groupBy) : $groupBy);
		elseif (is_scalar($display))
			$this->groupDisplay = (!is_array($display) ? array($display) : $display);
		else
			$this->groupDisplay = (!is_array($groupBy) ? array($groupBy) : $groupBy);
		foreach ($this->groupDisplay as $field)
			if (in_array($field, $this->hidden))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_HIDDEN_GROUP', $field), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Set a callback to be executed before a page is rendered
	 *
	 * This callback receives a reference to the Report instance.
	 *
	 * @param mixed $callback Function name, class/method or object/method
	 */
	function setStartPageHandler($callback) {
		$this->handlers['pageStart'] = new Callback($callback);
	}

	/**
	 * Set a callback to be executed after a page is rendered
	 *
	 * This callback receives a reference to the Report instance.
	 *
	 * When this callback is called, the internal cursor points to the
	 * end of the data set. In order to perform any operation on the
	 * records, you'll have to move the cursor to the desired position
	 * using the navigation methods ({@link move}, {@link moveFirst},
	 * {@link movePrevious}, {@link moveNext} and {@link moveLast})
	 *
	 * @param mixed $callback Function name, class/method or object/method
	 */
	function setEndPageHandler($callback) {
		$this->handlers['pageEnd'] = new Callback($callback);
	}

	/**
	 * Set a callback function to process each report line
	 *
	 * The callback receives a hash array containing the record fields
	 * (including grouping and hidden ones), and should return the
	 * same hash array after performing operations on it.
	 *
	 * A line handler <b>can't</b> be used to add new fields on
	 * the records. Only fields present in the database results
	 * will be displayed.
	 *
	 * Using a line handler, you're able to:
	 * # format record fields
	 * # transform a column in a list of links (e.g.: edit, view, delete)
	 * # read values of hidden fields
	 *
	 * @param mixed $callback Function name, class/method or object/method
	 */
	function setLineHandler($callback) {
		$this->handlers['line'] = new Callback($callback);
	}

	/**
	 * Set a callback function to handle a specific column of the data set
	 *
	 * @param string $columnName Column name
	 * @param mixed $callback Function name, class/method or object/method
	 */
	function setColumnHandler($columnName, $callback) {
		$this->columns[$columnName]['handler'] = new Callback($callback);
	}

	/**
	 * Set the alias of a given column or columns
	 *
	 * @param string|array $columnName Column name or hash array of columns and aliases
	 * @param string $alias Column alias
	 */
	function setColumnAlias($columnName, $alias = '') {
		if (TypeUtils::isHashArray($columnName)) {
			foreach ($columnName as $key => $value) {
				$this->columns[$key]['alias'] = $value;
			}
		} else {
			$this->columns[$columnName]['alias'] = $alias;
		}
	}

	/**
	 * Set how the class should handle sizes (width) of the columns
	 *
	 * The default behaviour of the class is let the browser decide
	 * the width of the report columns ({@link REPORT_COLUMN_SIZES_FREE}).
	 *
	 * Examples:
	 * <code>
	 * $report->setColumnSizes(REPORT_COLUMN_SIZES_FREE);
	 * $report->setColumnSizes(REPORT_COLUMN_SIZES_FIXED);
	 * /* when customizing column sizes, you must provide an array of integers {@*}
	 * $report->setColumnSizes(array(10, 20, 20, 20, 30));
	 * </code>
	 *
	 * @param int|array $param Size mode or array of custom column sizes
	 */
	function setColumnSizes($param=NULL) {
		if (is_array($param)) {
			if (array_sum($param) != 100) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_SIZES_SUM'), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$this->colSizesMode = REPORT_COLUMN_SIZES_CUSTOM;
				$this->colSizes = $param;
			}
		} elseif ($param == REPORT_COLUMN_SIZES_FIXED || $param == REPORT_COLUMN_SIZES_FREE) {
			$this->colSizesMode = $param;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_INVALID_COLSIZES', $param), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Set a column or a set of columns as hidden
	 *
	 * # when grid mode is enabled, these columns won't be displayed
	 * # when grid mode is disabled, these columns won't be assigned in the report template
	 *
	 * @param string|array $fieldName Column name or array of column names
	 */
	function setHidden($fieldName) {
		if (is_array($fieldName)) {
			// as colunas não podem pertencer ao conjunto de colunas de cabeçalho de grupo (groupDisplay)
			foreach ($fieldName as $field)
				if (in_array($field, $this->groupDisplay))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_GROUP_HIDDEN', $field), E_USER_ERROR, __FILE__, __LINE__);
			if (!isset($this->hidden))
				$this->hidden = $fieldName;
			else
				$this->hidden = array_merge($this->hidden, $fieldName);
		} else {
			// a coluna não pode pertencer ao conjunto de colunas de cabeçalho de grupo (groupDisplay)
			if (in_array($fieldName, $this->groupDisplay))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_GROUP_HIDDEN', $fieldName), E_USER_ERROR, __FILE__, __LINE__);
			if (!isset($this->hidden))
				$this->hidden = array($fieldName);
			else
				$this->hidden[] = $fieldName;
		}
	}

	/**
	 * Define which columns can't be sorted
	 *
	 * # when grid mode is enabled, the header of theses columns won't contain a sort link
	 * # these columns won't be added in the order_options_combo and order_options_links variables
	 *
	 * In order to disable sorting for all columns, call
	 * {@link disableOrderByLinks}.
	 *
	 * @param array $unsortable Unsortable column names
	 */
	function setUnsortableColumns($unsortable) {
		$this->unsortable = (array)$unsortable;
	}

	/**
	 * Register a bind variable or a set of bind variables
	 *
	 * Bind variables are used in the report's data source
	 *
	 * @param string|array $variable Variable name or hash array of variables
	 * @param mixed $value Variable value
	 */
	function bind($variable, $value = '') {
		if (TypeUtils::isHashArray($variable)) {
			foreach ($variable as $key => $value)
				$this->_bindVars[$key] = $value;
		} elseif (is_string($variable)) {
			$this->_bindVars[$variable] = $value;
		}
	}

	/**
	 * Define a callback function to handle search terms of a given mask
	 *
	 * Example:
	 * <code>
	 * $report->setMaskFunction('DATE', 'Date::fromEuroToSqlDate');
	 * </code>
	 *
	 * @param string $mask Mask name
	 * @param mixed $callback Function name, class/method or object/method
	 * @uses ReportSimpleSearch::addMaskFunction()
	 */
	function setSearchMaskFunction($mask, $callback) {
		$this->_SimpleSearch->addMaskFunction($mask, $callback);
	}

	/**
	 * Set the template file to be displayed when report data set is
	 * empty or when the simple search query returns an empty result set
	 *
	 * @param string $templateFile Template file
	 * @param array $templateVars Template variables
	 */
	function setEmptyTemplate($templateFile, $templateVars = array()) {
		$this->emptyTemplate = array(
			'file' => $templateFile,
			'vars' => (TypeUtils::isHashArray($templateVars)) ? $templateVars : array()
		);
	}

	/**
	 * Disable the use of a secondary template file when report's data
	 * set is empty or when search results are empty
	 *
	 * When empty template is disabled, it's up to the developer to control
	 * how information is displayed. The code snippet below demonstrates
	 * how to use a single template even when the data set is empty:
	 * <code>
	 * <!-- IF $report.total_rows gt 0 -->
	 * <table width='700'>
	 *   <!-- START BLOCK : loop_line -->
	 *   <tr>
	 *     ...
	 *   </tr>
	 *   <!-- END BLOCK : loop_line -->
	 * </table>
	 * <!-- ELSE -->
	 * <div>No records found.</div>
	 * <!-- END IF -->
	 * </code>
	 */
	function disableEmptyTemplate() {
		$this->emptyTemplate['disabled'] = TRUE;
	}

	/**
	 * Change the template file used to display the simple search form
	 *
	 * The template must contain the same variables and structures declared in the
	 * original template located at PHP2GO_ROOT/resources/template/simplesearch.tpl.
	 *
	 * @param string $searchTemplate File path
	 * @param array $templateVars Template variables
	 */
	function setSearchTemplate($searchTemplate, $templateVars = array()) {
		$this->searchTemplate = array(
			'file' => $searchTemplate,
			'vars' => (TypeUtils::isHashArray($templateVars)) ? $templateVars : array()
		);
	}

	/**
	 * Build the report's data set
	 *
	 * This method is automatically called inside {@link onPreRender}, if not
	 * called before. Call it manually only if you need to access or modify
	 * the data set before the report is rendered.
	 */
	function build() {
		if (!$this->_loaded) {
			$this->_buildDataSet();
			$this->_buildLimits();
			$this->_loaded = TRUE;
		}
	}

	/**
	 * Prepare the report to be rendered: build the content template
	 */
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			if (!$this->_loaded)
				$this->build();
			$this->baseUri = $this->_evaluateStatement($this->baseUri);
			if (!$this->_SimpleSearch->isEmpty())
				$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "widgets/simplesearch.js");
			if (parent::getTotalRecordCount() > 0 || @$this->emptyTemplate['disabled'] === TRUE) {
				$this->_buildContent();
			} else {
				$this->Template = new Template(isset($this->emptyTemplate['file']) ? $this->emptyTemplate['file'] : PHP2GO_TEMPLATE_PATH . 'emptyreport.tpl');
				$this->Template->parse();
				$this->Template->assign('name', $this->id);
				$this->Template->assign('title', (!empty($this->title) ? sprintf("<span class=\"%s\">%s</span>", $this->style['title'], $this->title) : ''));
				$this->Template->assign('report', array(
					'base_uri' => $this->baseUri,
					'search_sent' => $this->_SimpleSearch->searchSent,
					'style' => $this->style
				));
				$this->Template->assign((array)PHP2Go::getLangVal('REPORT_EMPTY_VALUES'));
				if (!empty($this->emptyTemplate['vars']))
					$this->Template->assign((array)$this->emptyTemplate['vars']);
			}
		}
	}

	/**
	 * Build and return the contents of the report
	 *
	 * @return string
	 */
	function getContent() {
		$this->onPreRender();
		return $this->Template->getContent();
	}

	/**
	 * Build and display the contents of the report
	 */
	function display() {
		$this->onPreRender();
		$this->Template->display();
	}

	/**
	 * Parse and process the XML specification
	 *
	 * @param string $xmlFile Path to the XML file
	 * @access private
	 */
	function _processXml($xmlFile) {
		$XmlDocument = new XmlDocument();
		$XmlDocument->parseXml($xmlFile);
		$XmlRoot =& $XmlDocument->getRoot();
		$this->rootAttrs = $XmlRoot->getAttributes();
		// title
		if (isset($this->rootAttrs['TITLE']))
			$this->setTitle($this->rootAttrs['TITLE'], TRUE);
		// debug flag
		$this->debug = (bool)resolveBooleanChoice(consumeArray($this->rootAttrs, 'DEBUG'));
		// base URI
		if ($uri = consumeArray($this->rootAttrs, 'BASEURI'))
			$this->baseUri = $uri;
		if ($XmlRoot->getChildrenCount() > 0) {
			$count = $XmlRoot->getChildrenCount();
			for ($i=0; $i<$count; $i++) {
				$Node =& $XmlRoot->getChild($i);
				// layout definitions
				if ($Node->getTag() == 'LAYOUT') {
					$this->_buildLayout($Node);
				}
				// variable definitions
				elseif ($Node->getTag() == 'VARIABLE') {
					$attrs = $Node->getAttributes();
					if ($name = @$attrs['NAME']) {
						$variable = array(
							'default' => @$attrs['DEFAULT'],
							'search' => @$attrs['SEARCHORDER']
						);
						if (!isset($this->variables[$name]))
							$this->variables[$name] = $variable;
						else
							$this->variables[$name] = array_merge($this->variables[$name], $variable);
					}
				}
				// data source
				elseif ($Node->getTag() == 'DATASOURCE' && $dsChildren = $Node->getChildrenTagsArray()) {
					$this->adapter->setParameter('connectionId', TypeUtils::ifFalse($Node->getAttribute('CONNECTION'), NULL));
					$this->_dataSource = array(
						'PROCEDURE' => isset($dsChildren['PROCEDURE']) ? $dsChildren['PROCEDURE']->value : '',
						'FIELDS' => isset($dsChildren['FIELDS']) ? $dsChildren['FIELDS']->value : '',
						'TABLES' => isset($dsChildren['TABLES']) ? $dsChildren['TABLES']->value : '',
						'CLAUSE' => isset($dsChildren['CLAUSE']) ? $dsChildren['CLAUSE']->value : '',
						'GROUPBY' => isset($dsChildren['GROUPBY']) ? trim($dsChildren['GROUPBY']->value) : '',
						'ORDERBY' => isset($dsChildren['ORDERBY']) ? trim($dsChildren['ORDERBY']->value) : ''
					);
					if (isset($dsChildren['PROCEDURE'])) {
						$this->_dataSource['CURSORNAME'] = $dsChildren['PROCEDURE']->getAttribute('CURSORNAME');
					} else {
						$optimizeCount = $Node->getAttribute('OPTIMIZECOUNT');
						$this->adapter->setParameter('optimizeCount', ($optimizeCount === FALSE ? TRUE : resolveBooleanChoice($optimizeCount)));
					}
				}
				// simple search filters
				elseif ($Node->getTag() == 'DATAFILTERS' && $Node->hasChildren()) {
					for ($j=0; $j<$Node->getChildrenCount(); $j++) {
						$Child =& $Node->getChild($j);
						$attrs = $Child->getAttributes();
						if ($attrs['LABEL'])
							$attrs['LABEL'] = resolveI18nEntry($attrs['LABEL']);
						$this->_SimpleSearch->addFilter($attrs);
					}
				}
			}
		}
	}

	/**
	 * Process layout settings coming from the XML specification
	 *
	 * @param XmlNoe &$Layout Layout node
	 * @access private
	 */
	function _buildLayout(&$Layout) {
		$attrs = $Layout->getAttributes();
		// header
		$useHeader = resolveBooleanChoice(@$attrs['GRID']);
		if (is_bool($useHeader))
			$this->hasHeader = $useHeader;
		// columns
		$columns = intval(@$attrs['NUMCOLS']);
		if ($columns >= 1 && !$this->hasHeader)
			$this->setColumns($columns);
		// printable + page break
		$printable = resolveBooleanChoice(@$attrs['PRINTABLE']);
		if ($printable)
			$this->isPrintable(@$attrs['PAGEBREAK']);
		// sortable
		if (($sortable = resolveBooleanChoice(@$attrs['SORTABLE'])) === FALSE)
			$this->isSortable = FALSE;
		// empty block
		if ($emptyBlock = @$attrs['EMPTYBLOCK'])
			$this->emptyBlock = $emptyBlock;
		// empty template
		$emptyTemplate = @$attrs['EMPTYTEMPLATE'];
		if (resolveBooleanChoice($emptyTemplate) === FALSE)
			$this->emptyTemplate['disabled'] = TRUE;
		elseif (file_exists($emptyTemplate))
			$this->emptyTemplate['file'] = $emptyTemplate;
		$count = $Layout->getChildrenCount();
		for ($i=0; $i<$count; $i++) {
			$ChildNode =& $Layout->getChild($i);
			switch ($ChildNode->getName()) {
				// paging settings
				case 'PAGINATION' :
					$attrs = $ChildNode->getAttributes();
					if ($pageSize = @$attrs['PAGESIZE'])
						parent::setPageSize($pageSize);
					if ($visiblePages = @$attrs['VISIBLEPAGES'])
						$this->setVisiblePages($visiblePages);
					if ($style = @$attrs['STYLE']) {
						if (defined($style)) {
							$paramList = array();
							$params = $ChildNode->getElementsByTagName('PARAM');
							if (is_array($params)) {
								foreach ($params as $paramNode) {
									$paramName = $paramNode->getAttribute('NAME');
									$paramValue = $paramNode->getAttribute('VALUE');
									if (!empty($paramName) && strlen($paramValue) > 0) {
										if (in_array($paramName, array('useButtons', 'useSymbols', 'hideInvalid')))
											$paramValue = resolveBooleanChoice($paramValue);
										$paramList[$paramName] = $paramValue;
									}
								}
							}
							$this->setPagingStyle(constant($style), $paramList);
						}
					}
					break;
				// js listeners
				case 'LISTENER' :
					$attrs = $ChildNode->getAttributes();
					if (isset($attrs['EVENT']) && in_array($attrs['EVENT'], array('onChangePage', 'onSort', 'onSearch'))) {
						$funcName = sprintf("report%s%s", $this->id, ucfirst($attrs['EVENT']));
						if (isset($attrs['ACTION'])) {
							$funcBody = "\t\t" . trim($attrs['ACTION']);
						} else {
							$funcBody = rtrim(ltrim($ChildNode->getData(), "\r\n"));
							if (preg_match("/^([\t]+)/", $funcBody, $matches))
								$funcBody = preg_replace("/^\t{" . strlen($matches[1]) . "}/m", "\t\t", $funcBody);
						}
						if ($attrs['EVENT'] != 'onSearch') {
							$this->_Document->addScriptCode(sprintf("\tfunction %s(args) {\n%s\n\t}", $funcName, $funcBody), 'Javascript', SCRIPT_END);
							$this->jsListeners[$attrs['EVENT']] = $funcName;
						} else {
							$this->jsListeners[$attrs['EVENT']] = $funcBody;
						}
					}
					break;
				// style settings
				case 'STYLE' :
					$attrs = $ChildNode->getAttributes();
					foreach ($attrs as $attrName => $attrValue) {
						switch ($attrName) {
							case 'LINK' :
							case 'FILTER' :
							case 'BUTTON' :
							case 'TITLE' :
							case 'HEADER' :
							case 'HELP' :
								$this->style[strtolower($attrName)] = $attrValue;
								break;
							case 'ALTSTYLE' :
								$altStyle = explode(',', $attrValue);
								if (!empty($altStyle))
									$this->style['altstyle'] = $altStyle;
								break;
							case 'HIGHLIGHT' :
								if (!empty($attrValue)) {
									$highlight = explode(',', $attrValue);
									$this->enableHighlight($highlight[0], @$highlight[1]);
								}
								break;
						}
					}
					break;
				// icons
				case 'ICON' :
					$name = $ChildNode->getAttribute('NAME');
					$path = $ChildNode->getAttribute('PATH');
					if ($name && $path)
						$this->icons[$name] = $path;
					break;
				// column definitions
				case 'COLUMNS' :
					$attrs = $ChildNode->getAttributes();
					// sizes
					if ($sizes = @$attrs['SIZES']) {
						if (defined($sizes)) {
							$this->setColumnSizes(constant($sizes));
						} else {
							$this->setColumnSizes(explode(',', trim($sizes)));
						}
					}
					// grouping
					if ($column = @$attrs['GROUP']) {
						$display = @$attrs['GROUPDISPLAY'];
						if ($display)
							$display = explode(',', $display);
						$this->setGroup(explode(',', $column), $display);
					}
					$columns = $ChildNode->getElementsByTagName('COLUMN');
					foreach ($columns as $idx => $ColumnNode) {
						$colAttrs = $ColumnNode->getAttributes();
						$name = consumeArray($colAttrs, 'NAME');
						if ($name) {
							$sortable = resolveBooleanChoice(consumeArray($colAttrs, 'SORTABLE'));
							if ($sortable === FALSE && !in_array($name, $this->unsortable))
								$this->unsortable[] = $name;
							$hidden = resolveBooleanChoice(consumeArray($colAttrs, 'HIDDEN'));
							if ($hidden === TRUE && !in_array($name, $this->hidden))
								$this->hidden[] = $name;
							if (isset($colAttrs['ALIAS']))
								$colAttrs['ALIAS'] = resolveI18nEntry($colAttrs['ALIAS']);
							if (isset($colAttrs['HELP']))
								$colAttrs['HELP'] = resolveI18nEntry($colAttrs['HELP']);
							$this->columns[$name] = array_change_key_case(array_merge((array)$this->columns[$name], $colAttrs), CASE_LOWER);
						}
					}
					break;
			}
		}
	}

	/**
	 * Build the report data set
	 *
	 * @access private
	 */
	function _buildDataSet() {
		// process variable substitution on data source members
		foreach ($this->_dataSource as $element => $value) {
			if (preg_match("/~[^~]+~/", $value))
				$this->_dataSource[$element] = $this->_evaluateStatement($value);
		}
		// show data source debug
		if ($this->debug) {
			print('REPORT DEBUG --- DATASOURCE ELEMENTS :');
			dumpVariable($this->_dataSource);
			$this->adapter->setParameter('debug', TRUE);
		} else {
			$this->adapter->setParameter('debug', FALSE);
		}
		// check if the data source uses a stored procedure
		if ($this->_dataSource['PROCEDURE'] != '') {
			$isProcedure = TRUE;
			$cursorName = @$this->_dataSource['CURSORNAME'];
			$this->_sqlCode = trim($this->_dataSource['PROCEDURE']);
			if (ereg(':CLAUSE', $this->_dataSource['PROCEDURE']))
				$this->_bindVars['CLAUSE'] = $this->_SimpleSearch->getSearchClause();
			$this->_bindVars['ORDER'] = $this->_orderByClause();
		// build an SQL query based on the data source elements
		} else {
			$isProcedure = FALSE;
			$cursorName = NULL;
			$Query = new QueryBuilder($this->_dataSource['FIELDS'], $this->_dataSource['TABLES'], $this->_dataSource['CLAUSE'], $this->_dataSource['GROUPBY']);
			$Query->addClause($this->_SimpleSearch->getSearchClause());
			$Query->setOrder($this->_orderByClause());
			$this->_sqlCode = $Query->getQuery();
		}
		// load data onto the internal DB adapter
		if (!$this->isPrintable)
			PagedDataSet::load($this->_sqlCode, $this->_bindVars, $isProcedure, $cursorName);
		else {
			DataSet::load($this->_sqlCode, $this->_bindVars, $isProcedure, $cursorName);
		}
		// initialize column settings
		$fieldNames = parent::getFieldNames();
		foreach ($fieldNames as $fieldName) {
			if (!isset($this->columns[$fieldName]))
				$this->columns[$fieldName] = array();
		}
	}

	/**
	 * Calculate the pagination variables
	 *
	 * @access private
	 */
	function _buildLimits() {
		// avoid pages out of the range
		$basePage = (parent::getCurrentPage() > parent::getPageCount() ? parent::getPageCount() : parent::getCurrentPage());
		// define first visible page link
		if (($basePage % $this->pagination['visiblePages']) == 0) {
			$this->pagination['firstVisiblePage'] = ((TypeUtils::parseInteger($basePage / $this->pagination['visiblePages']) - 1) * $this->pagination['visiblePages']) + 1;
		} else {
			$this->pagination['firstVisiblePage'] = (TypeUtils::parseInteger($basePage / $this->pagination['visiblePages']) * $this->pagination['visiblePages']) + 1;
		}
		// calculate last visible page link
		if (($this->pagination['firstVisiblePage'] + $this->pagination['visiblePages'] - 1) <= parent::getPageCount()) {
			$this->pagination['lastVisiblePage'] = $this->pagination['firstVisiblePage'] + $this->pagination['visiblePages'] - 1;
		} else {
			$this->pagination['lastVisiblePage'] = parent::getPageCount();
		}
		// is it possible to navigate to the first page?
		if (parent::getCurrentPage() > 1) {
			$this->pagination['firstPage'] = 1;
		}
		// is there a previous page?
		if (parent::getCurrentPage() > 1) {
			if (parent::getCurrentPage() > $this->pagination['visiblePages'] && parent::getCurrentPage() == $this->pagination['firstVisiblePage'])
				$this->pagination['previousPage'] = $this->pagination['firstVisiblePage'] - 1;
			else
				$this->pagination['previousPage'] = parent::getCurrentPage() - 1;
		}
		// is there a previous screen (set of page links)?
		if (parent::getCurrentPage() > $this->pagination['visiblePages']) {
			$this->pagination['previousScreen'] = $this->pagination['firstVisiblePage'] - 1;
		}
		// is there a next page?
		if (parent::getCurrentPage() < parent::getPageCount()) {
			if ($this->pagination['lastVisiblePage'] < parent::getPageCount() && parent::getCurrentPage() == $this->pagination['lastVisiblePage'])
				$this->pagination['nextPage'] = $this->pagination['lastVisiblePage'] + 1;
			else
				$this->pagination['nextPage'] = parent::getCurrentPage() + 1;
		}
		// is there a next screen (set of page links)?
		if ($this->pagination['lastVisiblePage'] < parent::getPageCount()) {
			$this->pagination['nextScreen'] = $this->pagination['lastVisiblePage'] + 1;
		}
		// is it possible to navigate to the last page?
		if (parent::getCurrentPage() < parent::getPageCount()) {
			$this->pagination['lastPage'] = parent::getPageCount();
		}
	}

	/**
	 * Builds the report content template
	 *
	 * Display records according with the layout settings: display mode
	 * (grid mode on or off), grouping.
	 *
	 * Expose to the template the report title, the report utility variables
	 * and report control variable.
	 *
	 * Generates the simple search form, if the XML specification contains
	 * any <b>data filters</b>.
	 *
	 * @access private
	 */
	function _buildContent() {
		$aRow = 0;	// total of records already fetched
		$aLine = 0;	// count of loop_line blocks already created
		$aCell = 1;	// current cell (only when grid mode is disabled)
		$errorMsg = NULL;
		if (!$this->_checkVariables($errorMsg))
			PHP2Go::raiseError($errorMsg, E_USER_ERROR, __FILE__, __LINE__);
		if (!$this->_checkHidden($errorMsg))
			PHP2Go::raiseError($errorMsg, E_USER_ERROR, __FILE__, __LINE__);
		if (isset($this->group)) {
			if (!$this->_checkGroup($errorMsg)) {
				PHP2Go::raiseError($errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			} else {
				if (isset($this->handlers['pageStart']) && TypeUtils::isInstanceOf($this->handlers['pageStart'], 'Callback'))
					$this->handlers['pageStart']->invokeByRef($this);
				// move data set to the first position and move template to the root block
				parent::moveFirst();
				$this->Template->setCurrentBlock(TP_ROOTBLOCK);
				if ($this->hasHeader) {
					while ($lineData = parent::fetch()) {
						$this->Template->createBlock('loop_line');
						// has the group changed? create a group block, a header block and a new line block
						if ($this->_matchGroup($lineData)) {
							$this->_dataGroup($lineData);
							$this->Template->createBlock('loop_line');
							$this->_dataHeader();
							$this->Template->createBlock('loop_line');
						}
						// generate the record columns
						$this->_dataColumns($lineData, NULL);
						$aRow++;
						if ($this->isPrintable && (($aRow % $this->pageBreak) == 0))
							$this->_buildPageBreak();
					}
				} else {
					// expose to the template column aliases and help messages
					foreach ($this->columns as $name => $config) {
						if (isset($config['alias']))
							$this->Template->globalAssign("{$name}_alias", $config['alias']);
						if (isset($config['help']))
							$this->Template->globalAssign("{$name}_help", sprintf("<img id=\"%s\" src=\"%s\" alt=\"\" style=\"cursor:pointer\" border=\"0\"%s/>", "{$name}_help", $this->icons['help'], HtmlUtils::overPopup($this->_Document, $config['help'], $this->style['help'])));
					}
					$this->Template->createBlock('loop_line');
					while ($lineData = parent::fetch()) {
						// has group changed?
						if ($this->_matchGroup($lineData)) {
							// is this the first line? generate a group block and a cell
							if ($aRow == 0) {
								$this->_dataGroup($lineData);
								$this->Template->createBlock('loop_line');
								$this->_dataColumns($lineData, $aCell);
								$aCell++;
								if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
							} else {
								// check if it's necessary to fill the line with empty cells
								if (($aCell > 1) && ($aCell <= $this->numCols)) {
									for ($i = $aCell; $i <= $this->numCols; $i++)
										$this->_dataColumns(NULL, $i);
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
								// generate a group block, a cell and a new line, if necessary
								$this->_dataGroup($lineData);
								$this->Template->createBlock('loop_line');
								$this->_dataColumns($lineData, $aCell);
								$aCell++;
								if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
							}
						// group hasn't changed. generate a cell block and a new line if necessary
						} else {
							$this->_dataColumns($lineData, $aCell);
							$aCell++;
							if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
								$aLine++;
								if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
									$this->_buildPageBreak();
								$this->Template->createBlock('loop_line');
								$aCell = 1;
							}
						}
						$aRow++;
					}
					// fill the line with empty cells if necessary
					if ($aCell <= $this->numCols) {
						for ($i = $aCell; $i <= $this->numCols; $i++)
							$this->_dataColumns(NULL, $i);
					}
				}
				if (isset($this->handlers['pageEnd']) && TypeUtils::isInstanceOf($this->handlers['pageEnd'], 'Callback'))
					$this->handlers['pageEnd']->invokeByRef($this);
			}
		} else {
			if (isset($this->handlers['pageStart']) && TypeUtils::isInstanceOf($this->handlers['pageStart'], 'Callback'))
				$this->handlers['pageStart']->invokeByRef($this);
			parent::moveFirst();
			$this->Template->setCurrentBlock(TP_ROOTBLOCK);
			if ($this->hasHeader) {
				// gera o cabeçalho
				$this->Template->createBlock('loop_line');
				$this->_dataHeader();
				// generate all line blocks
				while ($lineData = parent::fetch()) {
					$this->Template->createBlock('loop_line');
					$this->_dataColumns($lineData, NULL);
					$aRow++;
					if ($this->isPrintable && (($aRow % $this->pageBreak) == 0))
						$this->_buildPageBreak();
				}
			} else {
				// expose to the template column aliases and help messages
				foreach ($this->columns as $name => $config) {
					if (isset($config['alias']))
						$this->Template->globalAssign("{$name}_alias", $config['alias']);
					if (isset($config['help']))
						$this->Template->globalAssign("{$name}_help", sprintf("<img id=\"%s\" src=\"%s\" alt=\"\" style=\"cursor:pointer\" border=\"0\"%s/>", "{$name}_help", $this->icons['help'], HtmlUtils::statusBar($config['help'], TRUE)));
				}
				// create the first line block
				$this->Template->createBlock('loop_line');
				$aLine++;
				// loop through all records
				while ($lineData = parent::fetch()) {
					// generate a cell block, and a new line if necessary
					$this->_dataColumns($lineData, $aCell);
					$aCell++;
					if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
						$this->Template->createBlock('loop_line');
						$aCell = 1;
						$aLine++;
						if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
							$this->_buildPageBreak();
					}
					$aRow++;
				}
				// fill the line with empty cells if necessary
				if ($aCell <= $this->numCols) {
					for ($i = $aCell; $i <= $this->numCols; $i++)
						$this->_dataColumns(NULL, $i);
				}
			}
			if (isset($this->handlers['pageEnd']) && TypeUtils::isInstanceOf($this->handlers['pageEnd'], 'Callback'))
				$this->handlers['pageEnd']->invokeByRef($this);
		}
		// expose the report title
		$this->Template->globalAssign("title", (!empty($this->title) ? sprintf("<span class=\"%s\">%s</span>", $this->style['title'], $this->title) : ''));
		// expose the $report control variable
		$this->Template->globalAssign('report', array(
			'base_uri' => $this->baseUri,
			'use_header' => $this->hasHeader,
			'search_sent' => $this->_SimpleSearch->searchSent,
			'style' => $this->style,
			'total_rows' => (int)parent::getTotalRecordCount(),
			'page_rows' => (int)parent::getRecordCount(),
			'page_size' => (int)parent::getPageSize(),
			'current_page' => (int)parent::getCurrentPage(),
			'total_pages' => (int)parent::getPageCount(),
			'at_first_page' => parent::atFirstPage(),
			'at_last_page' => parent::atLastPage()
		));
		// expose the report utility variables
		$functionMessages = PHP2Go::getLangVal('REPORT_FUNCTION_MESSAGES');
		$this->Template->globalAssign(array(
			'page_links' => $this->_pageLinks($functionMessages),
			'order_options_combo' => $this->_orderOptions('combo'),
			'order_options_links' => $this->_orderOptions('links'),
			'row_count' => $this->_rowCount($functionMessages),
			'rows_per_page' => $this->_rowsPerPage($functionMessages),
			'this_page' => $this->_thisPage($functionMessages),
			'row_interval' => $this->_rowInterval($functionMessages),
			'go_to_page' => $this->_goToPage($functionMessages)
		));
		// generate the simple search form (only when not in print mode)
		if (!$this->isPrintable)
			$this->_buildSearchForm();
	}

	/**
	 * Generate a header block
	 *
	 * @access private
	 */
	function _dataHeader() {
		// check column sizes
		$visibleCols = (parent::getFieldCount() - count($this->groupDisplay) - count($this->hidden));
		if (isset($this->colSizes) && sizeof($this->colSizes) != $visibleCols)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_COUNT_MISMATCH', array(sizeof($this->colSizes), $visibleCols, sizeof($this->groupDisplay))), E_USER_ERROR, __FILE__, __LINE__);
		// loop through all field names
		for ($i=0,$c=0; $i<parent::getFieldCount(); $i++) {
			$colName = parent::getFieldName($i);
			$colConfig =& $this->columns[$colName];
			$colAlias = (isset($colConfig['alias']) ? $colConfig['alias'] : $colName);
			if (!in_array($colName, $this->groupDisplay) && !in_array($colName, $this->hidden)) {
				$colWidth = ($this->colSizesMode == REPORT_COLUMN_SIZES_CUSTOM && isset($this->colSizes) ? $this->colSizes[$c] . '%' : ($this->colSizesMode == REPORT_COLUMN_SIZES_FIXED ? TypeUtils::parseInteger(100 / $visibleCols) . '%' : ''));
				$this->Template->createBlock('loop_header_cell');
				$this->Template->assign('col_id', $colName);
				$this->Template->assign('col_wid', $colWidth);
				if (isset($colConfig['help']))
					$this->Template->assign('col_help', sprintf("&nbsp;<img id=\"%s\" src=\"%s\" alt=\"\" style=\"cursor:pointer\" border=\"0\"%s/>", "{$this->id}_{$colName}_help", $this->icons['help'], HtmlUtils::overPopup($this->_Document, $colConfig['help'], $this->style['help'])));
				// if print mode is on, or if sorting is disabled, or if column is unsortable,
				// display only the column name. otherwise, display a sort link
				if ($this->isPrintable || $this->isSortable === FALSE || in_array($colName, $this->unsortable)) {
					$this->Template->assign('col_name', (!empty($this->style['header']) ? "<span class='{$this->style['header']}'>{$colAlias}</span>" : $colAlias));
				} else {
					$orderTypeIcon = (
						$this->_orderType == 'a' ? $this->icons['orderasc'] : (
							$this->_orderType == 'd' ? $this->icons['orderdesc'] : $this->icons['orderasc']
						)
					);
					$onSort = @$this->jsListeners['onSort'];
					$this->Template->assign('col_name', HtmlUtils::anchor($this->_generatePageLink(parent::getCurrentPage(), $colName), $colAlias, PHP2Go::getLangVal('REPORT_ORDER_TIP', $colAlias), $this->style['header'], ($onSort ? array('onClick' => $onSort . '()') : array()), '', "{$this->id}_header{$c}"));
					$this->Template->assign('col_order', (urldecode(HttpRequest::get('order')) == $colName ? '&nbsp;' . HtmlUtils::image($orderTypeIcon) : ''));
				}
				$c++;
			}
		}
	}

	/**
	 * Generate template blocks for a data set record
	 *
	 * @param array $colData Data set record
	 * @param int $cellIdx Cell index (when grid mode is disabled)
	 * @access private
	 */
	function _dataColumns($colData=NULL, $cellIdx=NULL) {
		if (!empty($this->style['altstyle'])) {
			$altStyle = current($this->style['altstyle']);
			if (is_null($cellIdx) || $cellIdx == $this->numCols) {
				if (!next($this->style['altstyle']))
					reset($this->style['altstyle']);
			}
		}
		if (!is_null($colData)) {
			(isset($this->handlers['line'])) && ($colData = $this->handlers['line']->invoke($colData));
			(!empty($this->style['highlight'])) && ($colData = $this->_highlightSearch($colData));
			if ($this->hasHeader) {
				$visibleCols = (parent::getFieldCount() - count($this->groupDisplay) - count($this->hidden));
				if (isset($this->colSizes) && sizeof($this->colSizes) != $visibleCols)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_COUNT_MISMATCH', array(sizeof($this->colSizes), $visibleCols, sizeof($this->groupDisplay))), E_USER_ERROR, __FILE__, __LINE__);
				for ($i=0,$c=0; $i<parent::getFieldCount(); $i++) {
					$colName = parent::getFieldName($i);
					$colConfig =& $this->columns[$colName];
					if (!in_array($colName, $this->groupDisplay) && !in_array($colName, $this->hidden)) {
						$this->Template->createBlock('loop_cell');
						$this->Template->assign('col_wid', ($this->colSizesMode == REPORT_COLUMN_SIZES_CUSTOM && isset($this->colSizes) ? $this->colSizes[$c] . '%' : ($this->colSizesMode == REPORT_COLUMN_SIZES_FIXED ? intval(100/$visibleCols) . '%' : '')));
						(isset($colConfig['handler'])) && ($colData[$colName] = $colConfig['handler']->invoke($colData[$colName]));
						(isset($colConfig['align']) && stristr($colConfig['align'], 'left') === FALSE) && ($colData[$colName] = "<div align='{$colConfig['align']}'>{$colData[$colName]}</div>");
						$this->Template->assign('col_data', $colData[$colName]);
						if (!empty($this->style['altstyle'])) {
							$this->Template->assign('alt_style', $altStyle);
							$this->Template->assign('loop_line.alt_style', $altStyle);
						}
						$c++;
					}
				}
			} else {
				$this->Template->createBlock('loop_cell');
				$this->Template->assign('col_wid', intval(100/$this->numCols) . '%');
				(!empty($this->style['altstyle'])) && ($this->Template->assign('alt_style', $altStyle));
				for ($i=0; $i<parent::getFieldCount(); $i++) {
					$colName = parent::getFieldName($i);
					$colConfig =& $this->columns[$colName];
					if (!in_array($colName, $this->groupDisplay) && !in_array($colName, $this->hidden)) {
						if (isset($colConfig['handler']))
							$colData[$colName] = $colConfig['handler']->invoke($colData[$colName]);
						$this->Template->assign("{$colName}", $colData[$colName]);
					}
				}
			}
		} else {
			$blockName = (isset($this->emptyBlock) ? $this->emptyBlock : 'loop_cell');
			$this->Template->createBlock($blockName);
			$this->Template->assign(parent::getFieldName(0), '&nbsp;');
			if (!empty($this->style['altstyle']) && $this->Template->isVariableDefined("{$blockName}.alt_style")) {
				$this->Template->assign('alt_style', $altStyle);
				$this->Template->assign('loop_line.alt_style', $altStyle);
			}
		}
	}

	/**
	 * Generate a group block
	 *
	 * @param array $colData Data set record
	 * @access private
	 */
	function _dataGroup($colData) {
		$groupDisplay = '';
		foreach ($this->groupDisplay as $colName)
			$groupDisplay .= (empty($groupDisplay) ? $colData[$colName] : ' - ' . $colData[$colName]);
		$groupSpan = ($this->hasHeader ? (parent::getFieldCount() - count($this->groupDisplay)) : $this->numCols);
		$this->Template->createAndAssign('loop_group', array(
			'group_display' => $groupDisplay,
			'group_span' => $groupSpan
		));
	}

	/**
	 * Check all mandatory template variables
	 *
	 * @param string &$errorMsg Used to return the error message
	 * @access private
	 * @return bool
	 */
	function _checkVariables(&$errorMsg) {
		if ($this->hasHeader) {
			if (!$this->Template->isVariableDefined('loop_cell.col_data')) {
				$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_data', 'loop_cell'));
				return FALSE;
			}
			if (!$this->Template->isVariableDefined('loop_header_cell.col_name')) {
				$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_name', 'loop_header_cell'));
				return FALSE;
			}
			if ($this->colSizesMode != REPORT_COLUMN_SIZES_FREE) {
				if (!$this->Template->isVariableDefined('loop_cell.col_wid')) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_wid', 'loop_cell'));
					return FALSE;
				} elseif (!$this->Template->isVariableDefined('loop_header_cell.col_wid')) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_wid', 'loop_header_cell'));
					return FALSE;
				}
			}
		}
		if (!empty($this->group) && !empty($this->groupDisplay)) {
			if (!$this->Template->isVariableDefined('loop_group.group_display')) {
				$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('group_display', 'loop_group'));
				return FALSE;
			}
			if (!$this->Template->isVariableDefined('loop_group.group_span')) {
				$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('group_span', 'loop_group'));
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Validate hidden columns
	 *
	 * # all hidden columns must be in the report data set
	 * # at least one data set field must be visible
	 *
	 * @param string &$errorMsg Used to return the error message
	 * @access private
	 * @return bool
	 */
	function _checkHidden(&$errorMsg) {
		$check = TRUE;
		$fieldNames = parent::getFieldNames();
		if (sizeof($this->hidden) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_HIDDEN_COLS');;
			$check = FALSE;
		} else {
			for ($i=0; $i<sizeof($this->hidden); $i++) {
				if (!in_array($this->hidden[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_HIDDEN_COL', $this->hidden[$i]);
					$check = FALSE;
				}
			}
		}
		return $check;
	}

	/**
	 * Validate grouping columns
	 *
	 * # all grouping columns must be in the report data set
	 * # a column used to display a group can't be hidden
	 *
	 * @param string &$errorMsg Used to return the error message
	 * @access private
	 * @return bool
	 */
	function _checkGroup(&$errorMsg) {
		$check = TRUE;
		$fieldNames = parent::getFieldNames();
		if (sizeof($this->group) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_GROUP_COLS');
			$check = FALSE;
		} else {
			for ($i=0; $i<sizeof($this->group); $i++) {
				if (!in_array($this->group[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_GROUP_COL', $this->group[$i]);
					$check = FALSE;
				}
			}
		}
		if (sizeof($this->groupDisplay) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_GROUP_COLS');
			$check = FALSE;
		} else {
			for ($i=0; $i<sizeof($this->groupDisplay); $i++) {
				if (!in_array($this->groupDisplay[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_GROUP_COL', $this->groupDisplay[$i]);
					$check = FALSE;
				}
			}
		}
		return $check;
	}

	/**
	 * Check if group changed
	 *
	 * @param array $data Data set record
	 * @access private
	 * @return bool
	 */
	function _matchGroup($data) {
		if (!isset($this->_currentGroup)) {
			foreach ($this->group as $value)
				$this->_currentGroup[$value] = $data[$value];
			return TRUE;
		} else {
			$sizeof = sizeof($this->group);
			for ($i = 0; $i < $sizeof; $i++) {
				if ($this->_currentGroup[$this->group[$i]] != $data[$this->group[$i]]) {
					foreach ($this->group as $value)
						$this->_currentGroup[$value] = $data[$value];
					return TRUE;
				}
			}
			return FALSE;
		}
	}

	/**
	 * Build the simple search form
	 *
	 * @access private
	 */
	function _buildSearchForm() {
		if (!$this->_SimpleSearch->isEmpty()) {
			if (!$this->Template->isVariableDefined('simple_search'))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_SEARCH_VARIABLE', array('simple_search', 'simple_search')));
			if (isset($this->searchTemplate)) {
				$SearchTpl = new Template($this->searchTemplate['file']);
				$SearchTpl->parse();
				if (!empty($this->searchTemplate['vars']))
					$SearchTpl->assign($this->searchTemplate['vars']);
			} else {
				$SearchTpl = new Template(PHP2GO_TEMPLATE_PATH . 'simplesearch.tpl');
				$SearchTpl->parse();
			}
			$vars = PHP2Go::getLangVal('REPORT_SEARCH_VALUES');
			$vars['name'] = $this->id;
			$vars['searchUrl'] = $this->baseUri;
			if (isset($this->extraVars) && !empty($this->extraVars))
				$vars['searchUrl'] .= (strpos($this->baseUri, '?') !== FALSE ? '&' : '?') . $this->extraVars;
			$vars['labelStyle'] = (!empty($this->style['link']) ? " class=\"{$this->style['link']}\"" : '');
			$Agent =& UserAgent::getInstance();
			if ($Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+'))) {
				$vars['inputStyle'] = (!empty($this->style['filter']) ? " class=\"{$this->style['filter']}\"" : '');
				$vars['buttonStyle'] = (!empty($this->style['button']) ? " class=\"{$this->style['button']}\"" : '');
			}
			$masks = array();
			$vars['filterOptions'] = '';
			$filters = $this->_SimpleSearch->iterator();
			while ($filter = $filters->next()) {
				if ($filter['mask'] == 'DATE')
					$filter['mask'] .= '-' . PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
				$vars['filterOptions'] .= "<option value=\"{$filter['field']}\">{$filter['label']}</option>\n";
				$masks[] = "'{$filter['mask']}'";
			}
			$vars['masks'] = implode(",", $masks);
			$vars['operatorOptions'] = '';
			foreach((array)PHP2Go::getLangVal('REPORT_STRING_OPERATORS') as $key => $value)
				$vars['operatorOptions'] .= "<option value=\"{$key}\">{$value}</option>\n";
			$vars['onSearch'] = @$this->jsListeners['onSearch'];
			$SearchTpl->assign($vars);
			$this->Template->assign("_ROOT.simple_search", $SearchTpl->getContent());
		}
	}

	/**
	 * Add a CSS page break in the template, when print mode is on
	 *
	 * @access private
	 */
	function _buildPageBreak() {
		if ($this->Template->isVariableDefined("loop_line.page_break")) {
			$this->Template->assign("loop_line.page_break", "<tr style=\"page-break-after: always\"></tr>");
			if ($this->hasHeader) {
				$this->Template->createBlock('loop_line');
				$this->_dataHeader();
			}
		}
	}

	/**
	 * Generate a link to a page or to sort report by a given column name
	 *
	 * @param int $page Target page
	 * @param string $order Target sort column
	 * @access private
	 * @return string
	 */
	function _generatePageLink($page, $order='') {
		if (isset($this->_order) && $order == $this->_order)
			$ot = ($this->_orderType == 'a' ? 'd' : 'a');
		else
			$ot = $this->_orderType;
		$char = (strpos($this->baseUri, '?') !== FALSE ? '&' : '?');
		return sprintf("%s%spage=%s%s%s%s%s",
					$this->baseUri, $char, $page,
					($order != '' ? '&order=' . urlencode($order) : (isset($this->_order) ? '&order=' . $this->_order : '')),
					'&ordertype=' . $ot,
					($this->_SimpleSearch->searchSent ? $this->_SimpleSearch->getUrlString() : ''),
					(isset($this->extraVars) ? '&' . $this->extraVars : '')
		);
	}

	/**
	 * Generate a link to a given page, when paging style is not {@link REPORT_PAGING_DEFAULT}
	 *
	 * @param array &$links Paging links
	 * @param int $page Target page
	 * @param string $name Link/button name
	 * @param string $symbol Link/button symbol
	 * @param string $text Link/button text
	 * @param string $tip Link tooltip
	 * @access private
	 */
	function _generateNavigationLink(&$links, $page=NULL, $name, $symbol, $text, $tip='') {
		$useSymbols = $this->pagination['style'][1]['useSymbols'];
		$useButtons = $this->pagination['style'][1]['useButtons'];
		$hideInvalid = $this->pagination['style'][1]['hideInvalid'];
		$onChangePage = @$this->jsListeners['onChangePage'];
		$buttonCode = "<button id=\"%s\" name=\"%s\" type=\"button\" onClick=\"%s%s\" title=\"%s\" class=\"%s\"%s>%s</button>";
		$noLinkCode = "<span class=\"%s\" style=\"filter:alpha(opacity=30);opacity:0.3;-moz-opacity:0.3;-khtml-opacity:0.3\">%s</span>";
		$link = ($page ? $this->_generatePageLink($page) : NULL);
		if ($page) {
			if ($useButtons) {
				$links[] = sprintf($buttonCode, $this->id . $name, $name, ($onChangePage ? $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $page . '});' : ''), "location.href='{$link}'", $tip, $this->style['button'], '', ($useSymbols ? $symbol : $text));
			} else {
				$links[] = HtmlUtils::anchor($link, ($useSymbols ? $symbol : $text), $tip, $this->style['link']);
			}
		} elseif (!$hideInvalid) {
			if ($useButtons) {
				$links[] = sprintf($buttonCode, $this->id . $name, $name, '', "javascript:void(0)", '', $this->style['button'], " disabled", ($useSymbols ? $symbol : $text));
			} else {
				$links[] = sprintf($noLinkCode, $this->style['link'], ($useSymbols ? $symbol : $text));
			}
		}
	}

	/**
	 * Build paging links variable
	 *
	 * @param array $lang Report language entries
	 * @access private
	 * @return string
	 */
	function _pageLinks($lang) {
		if ($this->pagination['lastVisiblePage'] == 0)
			return NULL;
		$links = array();
		$linkLimiter = sprintf("<span class=\"%s\"> | </span>", $this->style['link']);
		$linkGlue = ($this->pagination['style'][1]['useButtons'] || $this->pagination['style'][1]['useSymbols'] ? "&nbsp;&nbsp;" : $linkLimiter);
		$onChangePage = @$this->jsListeners['onChangePage'];
		if ($this->pagination['style'][0] == REPORT_PAGING_DEFAULT) {
			$linkStr = '';
			// loop through all visible pages
			for ($i = $this->pagination['firstVisiblePage']; $i <= $this->pagination['lastVisiblePage']; $i++) {
				if ($i == parent::getCurrentPage()) {
					$linkStr .= sprintf("<span class=\"%s\">%d</span>\n", $this->style['link'], $i);
				} else {
					$linkStr .= HtmlUtils::anchor($this->_generatePageLink($i), "<u>$i</u>", sprintf($lang['pageTip'], $i, parent::getPageCount()), $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $i . '})') : array()));
				}
				if ($i < $this->pagination['lastVisiblePage'])
					$linkStr .= $linkLimiter;
				else
					$linkStr .= '<br>';
			}
			// link to first page, back N pages, forward N pages and last page
			if (isset($this->pagination['firstPage'])) {
				$linkStr .= HtmlUtils::anchor($this->_generatePageLink($this->pagination['firstPage']), $lang['firstTit'], $lang['firstTip'], $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $this->pagination['firstPage'] . '})') : array()));
			}
			if (isset($this->pagination['previousScreen'])) {
				if (isset($this->pagination['firstPage']))
					$linkStr .= $linkLimiter;
				$linkStr .= HtmlUtils::anchor($this->_generatePageLink($this->pagination['previousScreen']), sprintf($lang['prevScrTit'], $this->pagination['visiblePages']), sprintf($lang['prevScrTip'], $this->pagination['visiblePages']), $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $this->pagination['previousScreen'] . '})') : array()));
			}
			if (isset($this->pagination['nextScreen'])) {
				if (isset($this->pagination['firstPage']) || isset($this->pagination['previousScreen']))
					$linkStr .= $linkLimiter;
				$linkStr .= HtmlUtils::anchor($this->_generatePageLink($this->pagination['nextScreen']), sprintf($lang['nextScrTit'], $this->pagination['visiblePages']), sprintf($lang['nextScrTip'], $this->pagination['visiblePages']), $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $this->pagination['nextScreen'] . '})') : array()));
			}
			if (isset($this->pagination['lastPage'])) {
				if (isset($this->pagination['firstPage']) || isset($this->pagination['previousScreen']) || isset($this->pagination['nextScreen']))
					$linkStr .= $linkLimiter;
				$linkStr .= HtmlUtils::anchor($this->_generatePageLink($this->pagination['lastPage']), $lang['lastTit'], $lang['lastTip'], $this->style['link'], ($onChangePage ? array('onClick' => $onChangePage . '({from:' . parent::getCurrentPage() . ',to:' . $this->pagination['lastPage'] . '})') : array()));
			}
			return $linkStr;
		} elseif ($this->pagination['style'][0] == REPORT_PREVNEXT) {
			$this->_generateNavigationLink($links, @$this->pagination['previousPage'], 'prev', '<', $lang['prevTit'], $lang['prevTip']);
			$this->_generateNavigationLink($links, @$this->pagination['nextPage'], 'next', '>', $lang['nextTit'], $lang['nextTip']);
			return implode($linkGlue, $links);
		} elseif ($this->pagination['style'][0] == REPORT_FIRSTPREVNEXTLAST) {
			$this->_generateNavigationLink($links, @$this->pagination['firstPage'], 'first', '<<', $lang['firstTit'], $lang['firstTip']);
			$this->_generateNavigationLink($links, @$this->pagination['previousPage'], 'prev', '<', $lang['prevTit'], $lang['prevTip']);
			$this->_generateNavigationLink($links, @$this->pagination['nextPage'], 'next', '>', $lang['nextTit'], $lang['nextTip']);
			$this->_generateNavigationLink($links, @$this->pagination['lastPage'], 'last', '>>', $lang['lastTit'], $lang['lastTip']);
			return implode($linkGlue, $links);
		}
		return '';
	}

	/**
	 * Generate an order options variable
	 *
	 * Declare {$order_options_combo} or {$order_options_links}
	 * in the report template file to see this in action
	 *
	 * @uses StringUtils::ifEmpty()
	 * @param string $type Type
	 * @access private
	 * @return string
	 */
	function _orderOptions($type) {
		if (!$this->isPrintable && $this->isSortable !== FALSE) {
			$onSort = @$this->jsListeners['onSort'];
			if ($type == 'combo') {
				$opts = '';
				for ($i=0; $i<parent::getFieldCount(); $i++) {
					$colName = parent::getFieldName($i);
					$colConfig =& $this->columns[$colName];
					if (!in_array($colName, $this->unsortable)) {
						$value = $this->_generatePageLink(parent::getCurrentPage(), $colName);
						$text = StringUtils::ifEmpty(@$colConfig['alias'], $colName);
						$opts .= "<option value=\"{$value}\">{$text}</option>";
					}
				}
				if (!empty($opts)) {
					return sprintf("<label for=\"order_combo_%s\"%s>%s</label>&nbsp;<select id=\"order_combo_%s\" name=\"order_combo_%s\"%s onChange=\"var url = this.options[this.selectedIndex].value; if (url) { %swindow.location.href = url; }\"><option value=\"\">%s</option>%s</select>",
						$this->id, (!empty($this->style['filter']) ? " class=\"" . $this->style['filter'] . "\"" : ''),
						PHP2Go::getLangVal('REPORT_ORDER_OPTIONS_LABEL'), $this->id, $this->id,
						(isset($this->style['filter']) ? " class=\"" . $this->style['filter'] . "\"" : ''),
						($onSort ? $onSort . '();' : ''), PHP2Go::getLangVal('REPORT_SEARCH_VALUES.filtersTitle'), $opts
					);
				}
			} elseif ($type == 'links') {
				$items = array();
				for ($i=0; $i<parent::getFieldCount(); $i++) {
					$colName = parent::getFieldName($i);
					$colConfig =& $this->columns[$colName];
					if (!in_array($colName, $this->unsortable)) {
						$url = $this->_generatePageLink(parent::getCurrentPage(), $colName);
						$caption = StringUtils::ifEmpty(@$colConfig['alias'], $colName);
						$items[] = HtmlUtils::anchor($url, $caption, PHP2Go::getLangVal('REPORT_ORDER_TIP', $caption), $this->style['link'], ($onSort ? array('onClick' => $onSort . '()') : array()));
					}
				}
				if (!empty($items))
					return sprintf("<span%s>%s&nbsp;%s",
						(isset($this->style['filter']) ? " class=\"{$this->style['filter']}\"" : ''),
						PHP2Go::getLangVal('REPORT_ORDER_OPTIONS_LABEL'), join(' | ', $items)
					);
			}
		}
		return '';
	}

	/**
	 * Build row_count message
	 *
	 * @param array $lang Report language entries
	 * @access private
	 * @return string
	 */
	function _rowCount($lang) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($lang['rowCount'], parent::getTotalRecordCount());
		return NULL;
	}

	/**
	 * Build rows_per_page message
	 *
	 * @param array $lang Report language entries
	 * @access private
	 * @return string
	 */
	function _rowsPerPage($lang) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($lang['rowsPerPage'], parent::getPageSize());
		return NULL;
	}

	/**
	 * Build this_page message
	 *
	 * @param array $lang Report language entries
	 * @access private
	 * @return string
	 */
	function _thisPage($lang) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($lang['thisPage'], parent::getCurrentPage(), parent::getPageCount());
		return NULL;
	}

	/**
	 * Build row_interval message
	 *
	 * @param array $lang Report language entries
	 * @access private
	 * @return string
	 */
	function _rowInterval($lang) {
		if (parent::getTotalRecordCount() > 0) {
			$lowerBound = ($this->_offset + 1);
			$upperBound = (($this->_offset + parent::getPageSize()) > parent::getTotalRecordCount()) ? parent::getTotalRecordCount() : ($this->_offset + parent::getPageSize());
			return sprintf($lang['rowInterval'], $lowerBound, $upperBound, parent::getTotalRecordCount());
		}
		return NULL;
	}

	/**
	 * Build the form that allows user to jump to another page
	 *
	 * @param array $lang Report language entries
	 * @return string Form code
	 * @access private
	 */
	function _goToPage($lang) {
		if (parent::getTotalRecordCount() > 0) {
			$goToUrl = ereg_replace("(\?|&)(page=[0-9]+)(&?)", "\\1", $this->_generatePageLink(parent::getCurrentPage()));
			$goToLabel = sprintf("<label for=\"{$this->id}_page\" id=\"{$this->id}_page_label\" class=\"%s\">%s</label>", $this->style['filter'], $lang['goTo']);
			$goToField = sprintf("<input type=\"text\" id=\"{$this->id}_page\" name=\"page\" size=\"5\" maxlength=\"10\" class=\"%s\"/>", $this->style['filter']);
			return sprintf("<form id=\"%s_form\" name=\"%s\" method=\"POST\" action=\"%s\" style=\"display:inline\" onSubmit=\"return Report.goToPage(this, %d, %d, %s);\">\n%s\n&nbsp;%s\n</form>\n",
				$this->id, $this->id, $goToUrl, parent::getCurrentPage(), parent::getPageCount(),
				TypeUtils::ifNull($this->jsListeners['onChangePage'], 'null'),
				$goToLabel, $goToField
			);
		}
		return NULL;
	}

	/**
	 * Used to evaluate variables declared inside some
	 * special nodes and attributes of the XML specification
	 *
	 * @param string $source Source
	 * @return string Source with variables replaced
	 * @access private
	 */
	function _evaluateStatement($source) {
		static $Stmt;
		if (!isset($Stmt)) {
			$Stmt = new Statement();
			$Stmt->setVariablePattern('~', '~');
			$Stmt->setShowUnassigned();
		}
		$Stmt->setStatement($source);
		if (!$Stmt->isEmpty()) {
			foreach ($Stmt->variables as $name => $variable) {
				if (isset($this->variables[$name])) {
					if (isset($this->variables[$name]['value'])) {
						$Stmt->bindByName($name, $this->variables[$name]['value'], FALSE);
					} elseif (!$Stmt->bindFromRequest($name, FALSE, @$this->variables[$name]['search'])) {
						if (isset($this->variables[$name]['default'])) {
							$Stmt->bindByName($name, $this->variables[$name]['default'], FALSE);
						}
					}
				} else {
					$Stmt->bindFromRequest($name, FALSE);
				}
			}
		}
		return $Stmt->getResult();
	}

	/**
	 * Highlight search terms inside a record
	 *
	 * @uses StringUtils::normalize()
	 * @param array $data Data set record
	 * @return array Modified record
	 * @access private
	 */
	function _highlightSearch($data) {
		$newData = $data;
		if ($this->_SimpleSearch->searchSent) {
			$fields = explode('|', $this->_SimpleSearch->getFields());
			$operators = explode('|', $this->_SimpleSearch->getOperators());
			$values = explode('|', $this->_SimpleSearch->getValues());
			$size = sizeof($fields);
			for($i = 0; $i < $size; $i++) {
				$filters = $this->_SimpleSearch->iterator();
				while ($filter = $filters->next()) {
					if ($filter['field'] == $fields[$i]) {
						$patt = ($operators[$i] == 'LIKEI' ? '^' . $values[$i] : ($operators[$i] == 'LIKEF' ? $values[$i] . '$' : $values[$i]));
						$repl = "<span style=\"{$this->style['highlight']}\">\\1</span>";
						foreach ($data as $key => $value) {
							if ($key == $filter['field'])
								$newData[$key] = preg_replace("'(?!<.*?)($patt)(?![^<>]*?>)'si", $repl, $newData[$key]);
							elseif (StringUtils::normalize(trim(strtolower($key))) == StringUtils::normalize(trim(strtolower($filter['label']))))
								$newData[$key] = preg_replace("'(?!<.*?)($patt)(?![^<>]*?>)'si", $repl, $newData[$key]);
						}
					}
				}
			}
		}
		return $newData;
	}

	/**
	 * Build the 'order by' clause, based on default orderby, on grouping
	 * columns and on user requested sort column
	 *
	 * @access private
	 * @return string
	 */
	function _orderByClause() {
		$orderMembers = array();
		// 1) order by grouping columns
		if (isset($this->group)) {
			foreach ($this->group as $field)
				$orderMembers[] = "\"{$field}\"";
		}
		// 2) order by user requested column
		// If the user requested sort column is on the first position of the default
		// orderby clause, it is removed from the default clause
		if (isset($this->_order) && !in_array($this->_order, $this->unsortable) && !in_array($this->_order, $this->hidden)) {
			$orderMembers[] = "\"{$this->_order}\" " . ($this->_orderType == 'd' ? ' DESC' : ' ASC');
			$matches = array();
			if (preg_match("/^\s*{$this->_order}(\s*(asc|desc)?\s*,)?/is", $this->_dataSource['ORDERBY'], $matches)) {
				$this->_dataSource['ORDERBY'] = preg_replace('/' . $matches[0] . '/', '', $this->_dataSource['ORDERBY']);
			}
		}
		// 3) default orderby clause, defined in the XML specification
		if (!empty($this->_dataSource['ORDERBY']))
			$orderMembers[] = $this->_dataSource['ORDERBY'];
		return (!empty($orderMembers) ? implode(',', $orderMembers) : NULL);
	}

	/**
	 * Load paging settings, style settings, icon paths and
	 * other options from the global configuration settings
	 *
	 * The global report settings can be defined in the
	 * $P2G_USER_CFG array, using the 'REPORTS' key.
	 *
	 * @param array $settings Global report settings
	 * @access private
	 */
	function _loadGlobalSettings($settings) {
		(@$settings['EMPTYTEMPLATE'] === FALSE) && ($this->emptyTemplate['disabled'] = TRUE);
		(isset($settings['EMPTYBLOCK'])) && ($this->emptyBlock = $settings['EMPTYBLOCK']);
		$pagination = @$settings['PAGINATION'];
		if (is_array($pagination)) {
			(isset($pagination['STYLE']) && defined($pagination['STYLE'])) && ($this->pagination['style'][0] = constant($pagination['STYLE']));
			(isset($pagination['PAGESIZE']) && $pagination['PAGESIZE'] > 0) && (parent::setPageSize($pagination['PAGESIZE']));
			(isset($pagination['VISIBLEPAGES']) && $pagination['VISIBLEPAGES'] > 0) && ($this->pagination['visiblePages'] = $pagination['VISIBLEPAGES']);
			if (is_array($pagination['PARAMS'])) {
				foreach ($pagination['PARAMS'] as $name => $value)
					$this->pagination['style'][1][$name] = $value;
			}
		}
		foreach (TypeUtils::toArray(@$settings['STYLE']) as $name => $value) {
			switch ($name) {
				case 'LINK' :
				case 'FILTER' :
				case 'BUTTON' :
				case 'HEADER' :
				case 'TITLE' :
				case 'HELP' :
					$this->style[strtolower($name)] = $value;
					break;
				case 'ALTSTYLE' :
					$altStyle = explode(',', $value);
					if (!empty($altStyle))
						$this->style['altstyle'] = $altStyle;
					break;
				case 'HIGHLIGHT' :
					if (!empty($value)) {
						$highlight = explode(',', $value);
						$this->enableHighlight($highlight[0], @$highlight[1]);
					}
					break;
			}
		}
		foreach (TypeUtils::toArray(@$settings['ICONS']) as $name => $path)
			$this->icons[strtolower($name)] = $path;
		foreach (TypeUtils::toArray(@$settings['MASKFUNCTIONS']) as $mask => $function)
			$this->setSearchMaskFunction($mask, $function);
	}
}
?>