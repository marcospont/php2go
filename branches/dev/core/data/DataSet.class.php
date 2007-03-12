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

/**
 * Base wrapper for data sets
 *
 * The DataSet class exposes an interface to load, navigate, inspect and
 * read data sets. Different types of information can be processed through
 * a set of <b>data adapters</b>.
 *
 * The existent adapters are:
 * # <b>db</b> (records loaded from the database)
 * # <b>xml</b> (records loaded from a XML file respecting a special structure)
 * # <b>csv</b> (records from a CSV file)
 * # <b>array</b> (data loaded from a PHP array)
 *
 * @package data
 * @uses TypeUtils
 * @author Marcos Pont
 * @version $Revision$
 */
class DataSet extends Component
{
	/**
	 * Data adapter
	 *
	 * @var object DataAdapter
	 * @access protected
	 */
	var $adapter = NULL;

	/**
	 * Adapter type
	 *
	 * @var string
	 * @access protected
	 */
	var $adapterType;

	/**
	 * Class constructor
	 *
	 * Shouldn't be called directly. Prefer calling the static
	 * methods {@link factory} and {@link getInstance}.
	 *
	 * @param string $type Adapter type (db, csv, xml or array)
	 * @param array $params Adapter parameters
	 * @return DataSet
	 */
	function DataSet($type, $params=array()) {
		parent::Component();
		$type = ucfirst(strtolower($type));
		$adapterClass = 'DataSet' . $type;
		if (!empty($type) && import("php2go.data.adapter.{$adapterClass}")) {
			$this->adapter = new $adapterClass($params);
			$this->adapterType = $type;
			parent::registerDestructor($this, '__destruct');
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATASET_INVALID_TYPE', $type), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Creates a new dataset of type $type, using a
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
	 * @return DataSet
	 * @static
	 */
	function &factory($type, $params=array()) {
		$type = strtolower($type);
		$params = (array)$params;
		$instance = new DataSet($type, $params);
		return $instance;
	}

	/**
	 * Get the singleton of a given dataset type
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
	 * @return DataSet
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
			$instances[$hash] = new DataSet($type, $params);
		return $instances[$hash];
	}

	/**
	 * Destructor method
	 */
	function __destruct() {
		$this->close();
	}

	/**
	 * Loads information onto the data adapter
	 *
	 * Receives a variable number of arguments, depending on
	 * the active data adapter. Internally calls load() method
	 * of the data adapter.
	 *
	 * Below, some examples using different data adapters:
	 * <code>
	 * /* db adapter {@*}
	 * $dataset->load("select * from my_table where status = ?", array($status));
	 * /* xml adapter {@*}
	 * $dataset->load("my_data.xml", DS_XML_CDATA);
	 * /* csv adapter {@*}
	 * $dataset->load("my_data.csv");
	 * /* array adapter {@*}
	 * $dataset->load($myArray);
	 * </code>
	 *
	 * @see DataSetArray::load()
	 * @see DataSetCsv::load()
	 * @see DataSetDb::load()
	 * @see  DataSetXml::load()
	 */
	function load() {
		$args = func_get_args();
		@call_user_func_array(array(&$this->adapter, 'load'), $args);
	}

	/**
	 * Loads a subset of a data source onto the data adapter
	 *
	 * From the third parameter to the last, this method receives
	 * a variable number of arguments, depending on the active
	 * data adapter. Internally calls loadSubSet() method
	 * of the data adapter.
	 *
	 * @param int $offset Starting offset (zero-based)
	 * @param int $size Subset size
	 * @see DataSetArray::loadSubSet()
	 * @see DataSetCsv::loadSubSet()
	 * @see DataSetDb::loadSubSet()
	 * @see  DataSetXml::loadSubSet()
	 */
	function loadSubSet() {
		$args = func_get_args();
		@call_user_func_array(array(&$this->adapter, 'loadSubSet'), $args);
	}

	/**
	 * Get the field count of the data set
	 *
	 * @uses DataAdapter::getFieldCount()
	 * @return int
	 */
	function getFieldCount() {
		return $this->adapter->getFieldCount();
	}

	/**
	 * Get the field names of the data set
	 *
	 * @uses DataAdapter::getFieldNames()
	 * @return array
	 */
	function getFieldNames() {
		return $this->adapter->getFieldNames();
	}

	/**
	 * Get the name of the field indexed by $i
	 *
	 * @uses DataAdapter::getFieldNames()
	 * @param int $i Field index
	 * @return string
	 */
	function getFieldName($i) {
		$fieldNames = $this->adapter->getFieldNames();
		return (isset($fieldNames[$i]) ? $fieldNames[$i] : NULL);
	}

	/**
	 * Get the value of a field
	 *
	 * @uses DataAdapter::getField()
	 * @param string $fieldId Field name
	 * @return mixed
	 */
	function getField($fieldId) {
		return $this->adapter->getField($fieldId);
	}

	/**
	 * Get current cursor position
	 *
	 * @uses DataAdapter::getAbsolutePosition()
	 * @return int
	 */
	function getAbsolutePosition() {
		return $this->adapter->getAbsolutePosition();
	}

	/**
	 * Get the number of records in the data set
	 *
	 * @uses DataAdapter::getRecordCount()
	 * @return int
	 */
	function getRecordCount() {
		return $this->adapter->getRecordCount();
	}

	/**
	 * Get current record
	 *
	 * Retrieve the contents of the record pointed by
	 * the current cursor position.
	 * <code>
	 * $ds =& DataSet::factory('db');
	 * $ds->load('select * from my_table');
	 * while (!$ds->eof()) {
	 *   $current = $ds->current();
	 *   $ds->moveNext();
	 * }
	 * </code>
	 *
	 * @uses DataAdapter::current()
	 * @return array
	 */
	function current() {
		return $this->adapter->current();
	}

	/**
	 * Check if end of data set was reached
	 *
	 * Don't forget to manually advance the cursor
	 * position when using eof() inside a while loop.
	 *
	 * <code>
	 * $ds =& DataSet::factory('db');
	 * $ds->load('select * from my_table');
	 * while (!$ds->eof()) {
	 *   $row = $ds->current();
	 *   /* don't forget this :) {@*}
	 *   $ds->moveNext();
	 * }
	 * </code>
	 *
	 * @uses DataAdapter::eof()
	 * @return bool
	 */
	function eof() {
		return $this->adapter->eof();
	}

	/**
	 * Fetches the record pointed by the active cursor position
	 *
	 * When using fetch(), it's not necessary to increment the
	 * cursor position by calling {@link moveNext}.
	 * <code>
	 * $ds->load($data);
	 * while ($row = $ds->fetch()) {
	 *   /* there's no need to call moveNext() here {@*}
	 * }
	 * </code>
	 *
	 * IMPORTANT: fetch returns the current record and moves to
	 * the next, if existent. So calls to {@link current} and
	 * {@link getField} won't operate on previously returned
	 * record.
	 *
	 * @uses DataAdapter::fetch()
	 * @return array|FALSE Returns FALSE when end of data set is reached
	 * @see fetchInto
	 */
	function fetch() {
		return $this->adapter->fetch();
	}

	/**
	 * Fetches the current record into $dataArray variable
	 *
	 * When using fetchInto, $dataArray is taken by reference and
	 * the cursor position is automatically incremented.
	 * <code>
	 * $ds->load($data);
	 * while ($ds->fetchInto($row)) {
	 *   /* there's no need to call moveNext() here {@*}
	 * }
	 * </code>
	 *
	 * IMPORTANT: fetchInto returns the current record and moves to
	 * the next, if existent. So calls to {@link current} and
	 * {@link getField} won't operate on previously returned
	 * record.
	 *
	 * @uses DataAdapter::fetchInto()
	 * @param array $dataArray Variable to copy record data
	 * @return bool Returns FALSE when end of data set is reached
	 * @see fetch
	 */
	function fetchInto(&$dataArray) {
		return $this->adapter->fetchInto($dataArray);
	}

	/**
	 * Move internal pointer to a given position
	 *
	 * @uses DataAdapter::move()
	 * @param int $index Record index
	 * @return bool
	 */
	function move($index) {
		return $this->adapter->move($index);
	}

	/**
	 * Move internal pointer to the previous position
	 *
	 * @uses DataAdapter::movePrevious()
	 * @return bool
	 */
	function movePrevious() {
		return $this->adapter->movePrevious();
	}

	/**
	 * Move internal pointer to the next position
	 *
	 * @uses DataAdapter::moveNext()
	 * @return bool
	 */
	function moveNext() {
		return $this->adapter->moveNext();
	}

	/**
	 * Move internal pointer to the first record
	 *
	 * @uses DataAdapter::moveFirst()
	 * @return bool
	 */
	function moveFirst() {
		return $this->adapter->moveFirst();
	}

	/**
	 * Move internal pointer to the last record
	 *
	 * @uses DataAdapter::moveLast()
	 * @return bool
	 */
	function moveLast() {
		return $this->adapter->moveLast();
	}

	/**
	 * Closes the data set
	 */
	function close() {
		$this->adapter->close();
	}
}
?>