<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
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
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.data.DataSet');
import('php2go.net.HttpRequest');

/**
 * Default page size
 */
define('PDS_DEFAULT_PAGE_SIZE', 30);

/**
 * Handles data sets split into pages
 * 
 * The PagedDataSet class applies pagination on data sets. Data pages
 * are built by calling loadSubSet method on data adapters, using
 * current page and page size as arguments. The page number is 
 * automatically loaded from the "page" get parameter
 * 
 * The total record count (total records in all pages) can be retrieved
 * by calling {@link getTotalRecordCount}.
 * 
 * Example:
 * <code>
 * /**
 *  * create and fill data set;
 *  * page number is read from $_GET['page'] variable
 * {@*}
 * $pds =& PagedDataSet::factory('db');
 * $pds->load("select * from users");
 * 
 * /* build navigation links {@*}
 * if ($prev = $pds->getPreviousPage())
 *   print HtmlUtils::anchor(HttpRequest::basePath() . '?page=' . $prev, 'Previous');
 * if ($next = $pds->getNextPage())
 *   print HtmlUtils::anchor(HttpRequest::basePath() . '?page=' . $next, 'Next');
 * 
 * /* browse page contents {@*}
 * while (!$pds->eof()) {
 *   print $pds->getField('username') . '<br />';
 *   $pds->moveNext();
 * }
 * </code>
 * 
 * @package data
 * @uses HttpRequest
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class PagedDataSet extends DataSet
{
	/**
	 * Current page number
	 *
	 * @var int
	 * @access private
	 */
	var $_currentPage;
	
	/**
	 * Total page count
	 *
	 * @var int
	 * @access private
	 */
	var $_pageCount = 0;
	
	/**
	 * Current starting offset
	 * 
	 * Index of the first record of the current page.
	 *
	 * @var int
	 * @access private
	 */
	var $_offset;
	
	/**
	 * Page size
	 *
	 * @var int
	 * @access private
	 */
	var $_pageSize;
	
	/**
	 * Class constructor
	 * 
	 * Shouldn't be called directly. Prefer calling static method 
	 * {@link getInstance} and {@link factory}
	 *
	 * @param string $type Adapter type
	 * @return PagedDataSet
	 */
	function PagedDataSet($type) {
		parent::DataSet($type);
		$this->_pageSize = PDS_DEFAULT_PAGE_SIZE;
		$this->_currentPage = TypeUtils::ifNull(HttpRequest::get('page'), 1);
		if (!TypeUtils::isInteger($this->_currentPage) || $this->_currentPage < 1)
			$this->_currentPage = 1;
		$this->_offset = (($this->_currentPage - 1) * $this->_pageSize);
	}
	
	
	/**
	 * Creates a new paged data set of type $type, using a
	 * given set of configuration $params
	 *
	 * The argument $type is mandatory and must be one of
	 * the supported adapter types: db, xml, csv or array.
	 * 
	 * The set of parameters accepted by data adapters are:
	 * # db : debug (bool), connectionId (string), optimizeCount (bool)
	 * # xml : none
	 * # csv : none
	 * # array : none
	 *
	 * @param string $type Adapter type
	 * @param array $params Adapter parameters
	 * @return PagedDataSet
	 * @static
	 */
	function &factory($type, $params=array()) {
		$type = strtolower($type);
		$params = (array)$params;
		$instance =& new PagedDataSet($type, $params);
		return $instance;
	}
	
	/**
	 * Get the singleton of a given paged data set type
	 *
	 * The argument $type is mandatory and must be one of
	 * the supported adapter types: db, xml, csv or array.
	 *
	 * The set of parameters accepted by data adapters are:
	 * # db : debug (bool), connectionId (string), optimizeCount (bool)
	 * # xml : none
	 * # csv : none
	 * # array : none
	 *
	 * @param string $type Adapter type
	 * @param array $params Adapter parameters
	 * @return PagedDataSet
	 * @static
	 */
	function &getInstance($type, $params=array()) {
		static $instances;
		if (!isset($instances))
			$instances = array();
		$type = strtolower($type);
		$params = (array)$params;
		$hash = $type . serialize(ksort($params));
		if (!isset($instances[$hash]))
			$instances[$hash] = new PagedDataSet($type, $params);
		return $instances[$hash];
	}
	
	/**
	 * Get page size
	 *
	 * @return int
	 */
	function getPageSize() {
		return $this->_pageSize;
	}
	
	/**
	 * Set page size
	 * 
	 * Doesn't take effect if called after calling {@link load()}.
	 *
	 * @param int $pageSize New page size
	 */
	function setPageSize($pageSize) {
		$this->_pageSize = max(1, $pageSize);
		$this->_offset = (($this->_currentPage - 1) * $this->_pageSize);
	}
	
	/**
	 * Get current page number
	 *
	 * @return int
	 */
	function getCurrentPage() {
		return $this->_currentPage;
	}
	
	/**
	 * Set page number to be loaded
	 * 
	 * Page number is fetched from the request inside the class constructor.
	 * However, you can manually load a given page number by calling this
	 * method. Don't forget to call it before calling {@link load()}.
	 *
	 * @param int $page Page number
	 */
	function setCurrentPage($page) {
		if (TypeUtils::isInteger($page) && $page > 0) {
			$this->_currentPage = $page;
			$this->_offset = (($this->_currentPage - 1) * $this->_pageSize);
		}
	}
	
	/**
	 * Get previous page number
	 * 
	 * Returns FALSE if we're at the first page.
	 *
	 * @return int|FALSE
	 */
	function getPreviousPage() {
		return ($this->atFirstPage() ? FALSE : $this->_currentPage - 1);
	}
	
	/**
	 * Get next page number
	 * 
	 * Returns FALSE if we're at the last page.
	 *
	 * @return int|FALSE
	 */
	function getNextPage() {
		return ($this->atLastPage() ? FALSE : $this->_currentPage + 1);
	}
	
	/**
	 * Check if we're at the first page
	 *
	 * @return bool
	 */
	function atFirstPage() {
		return $this->_currentPage == 1;
	}
	
	/**
	 * Check if we're at the last page
	 *
	 * @return bool
	 */
	function atLastPage() {
		return $this->_currentPage == $this->_pageCount;
	}
	
	/**
	 * Get total of pages
	 *
	 * @return int
	 */
	function getPageCount() {
		return $this->_pageCount;
	}
	
	/**
	 * Get total record count
	 * 
	 * This is the total of records in all data set pages
	 *
	 * @return int
	 */
	function getTotalRecordCount() {
		return $this->adapter->totalRecordCount;
	}
	
	/**
	 * Loads a data subset onto the data adapter, using
	 * requested page number and desired page size as
	 * "offset" and "size"
	 * 
	 * This method receives a variable number of arguments,
	 * depending on the active data adapter. Internally
	 * calls loadSubSet() method of the data adapter.
	 *
	 * @see DataSetArray::loadSubSet()
	 * @see DataSetCsv::loadSubSet()
	 * @see DataSetDb::loadSubSet()
	 * @see DataSetXml::loadSubSet()
	 */
	function load() {
		$args = func_get_args();
		$args = array_merge(array($this->_offset, $this->_pageSize), $args);
		if (call_user_func_array(array(&$this->adapter, 'loadSubSet'), $args))
			$this->_calculatePages();
	}
	
	/**
	 * Anulls parent class implementation
	 */
	function loadSubSet() {
	}
	
	/**
	 * Calculate page count based on total record count and page size
	 *
	 * @access private
	 */
	function _calculatePages() {
		if (($this->adapter->totalRecordCount % $this->_pageSize) == 0)
			$this->_pageCount = ($this->adapter->totalRecordCount / $this->_pageSize);
		else
			$this->_pageCount = TypeUtils::parseInteger(($this->adapter->totalRecordCount / $this->_pageSize) + 1);
	}	
}
?>