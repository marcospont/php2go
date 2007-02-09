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

import('php2go.db.QueryBuilder');
import('php2go.form.field.FormField');

/**
 * Base class for form components based on a data source
 *
 * @package form
 * @subpackage field
 * @uses Db
 * @uses QueryBuilder
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract
 */
class DbField extends FormField
{
	/**
	 * Component's data source
	 *
	 * @var array
	 * @access protected
	 */
	var $dataSource = array();

	/**
	 * Indicates if the data source was already loaded
	 *
	 * @var bool
	 * @access private
	 */
	var $dataSourceLoaded = FALSE;

	/**
	 * Whether the data source contains grouping clauses
	 *
	 * @var bool
	 * @access protected
	 */
	var $isGrouping = FALSE;

	/**
	 * Database connection
	 *
	 * @var object Db
	 * @access private
	 */
	var $_Db;

	/**
	 * Database result set
	 *
	 * @var ADORecordSet
	 * @access private
	 */
	var $_Rs;

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return DbField
	 */
	function DbField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		if ($this->isA('DbField', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'DbField'), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Must be implemented by child classes
	 *
	 * @abstract
	 */
	function display() {
	}

	/**
	 * Set the component's result set
	 *
	 * @param ADORecordSet &$Rs
	 */
	function setRecordSet(&$Rs) {
		if (TypeUtils::isInstanceOf($Rs, 'ADORecordSet'))
			$this->_Rs =& $Rs;
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @uses FormField::parseDataSource()
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (isset($children['DATASOURCE'])) {
			$this->dataSource = parent::parseDataSource($children['DATASOURCE']);
			$this->isGrouping = (!empty($this->dataSource['GROUPFIELD']));
		} else {
			$this->dataSource = array();
		}
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @uses Form::resolveVariables()
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		foreach ($this->dataSource as $name => $value) {
			if (preg_match("/~[^~]+~/", $value))
				$this->dataSource[$name] = $this->_Form->resolveVariables($value);
		}
	}

	/**
	 * Build component's result set based on the data source
	 *
	 * @param int $fetchMode Fetch mode
	 * @param bool $debug Show debug information
	 * @access protected
	 */
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