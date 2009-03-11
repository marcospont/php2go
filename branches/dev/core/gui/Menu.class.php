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

import('php2go.util.Statement');

/**
 * Absolute positioning
 */
define('MENU_ABSOLUTE', 1);
/**
 * Relative positioning
 */
define('MENU_RELATIVE', 2);

/**
 * Base class to build tree based menus
 *
 * Menu items can be loaded either from the database or from a
 * structured XML file.
 *
 * @package gui
 * @uses ADORecordSet
 * @uses Db
 * @uses Statement
 * @uses XmlDocument
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 * @abstract
 */
class Menu extends Component
{
	/**
	 * Auto generated menu ID
	 *
	 * @var string
	 * @access private
	 */
	var $id;

	/**
	 * Menu data
	 *
	 * @var array
	 * @access private
	 */
	var $tree;

	/**
	 * Root item count
	 *
	 * @var int
	 * @access private
	 */
	var $rootSize;

	/**
	 * SQL to load root level (master)
	 *
	 * @var string
	 * @access private
	 */
	var $rootSql;

	/**
	 * SQL to load child levels (details)
	 *
	 * @var string
	 * @access private
	 */
	var $childSql;

	/**
	 * Limit of menu levels
	 *
	 * @var int
	 * @access private
	 */
	var $limit;

	/**
	 * Holds the deepest menu level (zero-based)
	 *
	 * @var int
	 * @access private
	 */
	var $lastLevel = 0;

	/**
	 * Database connection
	 *
	 * @var object Db
	 * @access private
	 */
	var $_Db = NULL;

	/**
	 * Parent document
	 *
	 * @var object Document
	 * @access private
	 */
	var $_Document;

	/**
	 * Class constructor
	 *
	 * @param Document &$Document Document instance in which the menu should be rendered
	 * @return Menu
	 */
	function Menu(&$Document) {
		parent::Component();
		if ($this->isA('Menu', FALSE))
        	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Menu'), E_USER_ERROR, __FILE__, __LINE__);
        if (!TypeUtils::isInstanceOf($Document, 'Document'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		$this->id = PHP2Go::generateUniqueId(parent::getClassName());
		$this->_Document =& $Document;
	}

	/**
	 * Loads the menu items from the database
	 *
	 * Expects 2 SQL queries: the first ($rootSql) will load the items of
	 * the root level, and the second ($childSql) will load the child levels
	 * (from the second to the last).
	 *
	 * Both SQL queries must contain 3 mandatory columns: LINK, CAPTION
	 * and an identifier column, used to link between menu levels. A TARGET
	 * column is also supported, but optional.
	 *
	 * The $childSql must contain a placeholder whose name matches the
	 * column that holds the item's identifier. For instance, if your
	 * menu items are identified by a column named MENU_ID, you could
	 * build a menu using the following code:
	 * <code>
	 * $rootSql = "select LINK, CAPTION, MENU_ID from MENU order by CAPTION";
	 * $childSql = "select LINK, CAPTION, MENU_ID from MENU where PARENT_MENU_ID = ~MENU_ID~ order by CAPTION";
	 * $menu->loadFromDatabase($rootSql, $childSql);
	 * </code>
	 *
	 * @param string $rootSql SQL query for the root level
	 * @param string $childSql SQL query for the inner levels
	 * @param int $limit Max levels
	 * @param string $connectionId Database connection ID
	 */
	function loadFromDatabase($rootSql, $childSql, $limit=0, $connectionId=NULL) {
		import('php2go.util.Statement');
		$this->_Db = Db::getInstance($connectionId);
		$this->rootSql = $rootSql;
		$this->childSql = ereg_replace("~([^~])~", "~".strtoupper("\\1")."~", $childSql);
		$this->limit = abs(intval($limit));
	}

	/**
	 * Loads the menu tree from a structured XML file or string
	 *
	 * Node names doesn't matter. The only requirement is that
	 * each node (except the tree's root) must contain 2 mandatory
	 * attributes: LINK and CAPTION. The TARGET attribute is also
	 * supported, but is optional.
	 *
	 * Example:
	 * <code>
	 * <menu>
	 *   <item link="page1.php" caption="Item 1"/>
	 *   <item link="page2.php" caption="Item 2"/>
	 *   <item link="page3.php" caption="Item 3">
	 *       <item link="page31.php" caption="Item 3.1"/>
	 *       <item link="page32.php" caption="Item 3.2"/>
	 *       <item link="page33.php" caption="Item 3.3"/>
	 *       <item link="page34.php" caption="Item 3.4"/>
	 *   </item>
	 *   <item link="page4.php" caption="Item 1">
	 *       <item link="page41.php" caption="Item 4.1"/>
	 *       <item link="page42.php" caption="Item 4.2">
	 *         <item link="page421.php" caption="Item 4.2.1"/>
	 *         <item link="page422.php" caption="Item 4.2.2"/>
	 *       </item>
	 *   </item>
	 * </menu>
	 * </code>
	 *
	 * @param string $xmlFile XML file or string
	 * @param bool $byFile By file (TRUE) or by var (FALSE)
	 */
	function loadFromXmlFile($xmlFile, $byFile=TRUE) {
		import('php2go.xml.XmlDocument');
		$XmlDoc = new XmlDocument();
		$XmlDoc->parseXml($xmlFile, ($byFile === TRUE ? T_BYFILE : T_BYVAR));
		$this->xmlRoot =& $XmlDoc->getRoot();
		if (!$this->xmlRoot->hasChildren()) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_EMPTY_XML_ROOT'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**#@+
	 * Must be implemented by child classes
     *
     * @abstract
     */
	function onPreRender() {
	}
	function getContent() {
	}
	function display() {
	}
	/**#@-*/

	/**
	 * Builds the menu tree
	 *
	 * @access protected
	 */
	function buildMenu() {
		// from database
		if (isset($this->rootSql) && isset($this->childSql)) {
			$oldFetchMode = $this->_Db->setFetchMode(ADODB_FETCH_ASSOC);
			$RootRs =& $this->_Db->query($this->rootSql);
			if ($this->_verifyRootSql($RootRs->fields)) {
				$this->_buildTreeFromDatabase($RootRs, 0, $this->tree);
				$this->rootSize = sizeof($this->tree);
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_WRONG_ROOT_SQL'), E_USER_ERROR, __FILE__, __LINE__);
			}
			$this->_Db->setFetchMode($oldFetchMode);
		// from XML
		} elseif (isset($this->xmlRoot)) {
			$this->_buildTreeFromXmlFile($this->xmlRoot, 0, $this->tree);
			$this->rootSize = sizeof($this->tree);
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_NOT_FOUND'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Recursive method used to build the menu
	 * tree based on a XML file or string
	 *
	 * @param XmlNode $Node Node to be processed
	 * @param int $i Current menu level
	 * @param array &$Tree Menu tree
	 * @access private
	 */
	function _buildTreeFromXmlFile($Node, $i, &$Tree) {
		$cCount = 0;
		for ($i=0,$s=$Node->getChildrenCount(); $i<$s; $i++) {
			$Child = $Node->getChild($i);
			$childCaption = resolveI18nEntry($Child->getAttribute('CAPTION'));
			$childLink = Statement::evaluate($Child->getAttribute('LINK'));
			$childTarget = $Child->getAttribute('TARGET');
			$Tree[$cCount] = array(
				'CAPTION' => $childCaption,
				'LINK' => $childLink,
				'TARGET' => $childTarget,
				'CHILDREN' => array()
			);
			if ($i < $this->limit || $this->limit == 0) {
				if ($Child->hasChildren()) {
					$TreePtr =& $Tree[$cCount]['CHILDREN'];
					$this->_buildTreeFromXmlFile($Child, $i+1, $TreePtr);
				} elseif ($i >= $this->lastLevel) {
					$this->lastLevel = $i;
				}
			}
			$cCount++;
		}
	}

	/**
	 * Recursive method used to build the menu tree
	 * based on a database
	 *
	 * @param ADORecordSet $rs Result set to be processed
	 * @param int $i Current menu level
	 * @param array &$Tree Menu tree
	 * @access private
	 */
	function _buildTreeFromDatabase($rs, $i, &$Tree) {
		$cCount = 0;
		while ($cData = $rs->FetchRow()) {
			$cData = array_change_key_case($cData, CASE_UPPER);
			$Tree[$cCount] = array(
				'CAPTION' => $cData['CAPTION'],
				'LINK' => $cData['LINK'],
				'TARGET' => (array_key_exists('TARGET', $cData) ? $cData['TARGET'] : ''),
				'CHILDREN' => array()
			);
			if ($i < $this->limit || $this->limit == 0) {
				$ChildRs = $this->_verifyChildrenSql($this->childSql, $cData);
				if ($ChildRs === FALSE) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_WRONG_CHILDREN_STATEMENT'), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					if ($ChildRs->recordCount() > 0) {
						$TreePtr =& $Tree[$cCount]['CHILDREN'];
						$this->_buildTreeFromDatabase($ChildRs, $i+1, $TreePtr);
					} elseif ($i >= $this->lastLevel) {
						$this->lastLevel = $i;
					}
				}
			}
			$cCount++;
		}
	}

	/**
	 * Check if the root SQL query contains the mandatory
	 * columns LINK and CAPTION
	 *
	 * @param array $rootData First row returned by the root SQL
	 * @access private
	 * @return bool
	 */
	function _verifyRootSql($rootData) {
		$rootData = array_change_key_case($rootData, CASE_UPPER);
		if (!array_key_exists('CAPTION', $rootData) || !array_key_exists('LINK', $rootData))
			return FALSE;
		return TRUE;
	}

	/**
	 * Validates and executes the child's SQL query
	 *
	 * Checks if the child SQL query contains the mandatory
	 * columns LINK and CAPTION and the placeholder to link
	 * it with the parent menu level.
	 *
	 * @param string $childSql Child levels SQL query
	 * @param array $parentFields Parent level's fields
	 * @return ADORecordSet|FALSE Menu items
	 * @access private
	 */
	function _verifyChildrenSql($childSql, $parentFields) {
		$Child = new Statement($childSql);
		$stmtVars = $Child->getDefinedVars();
		if (empty($stmtVars)) {
			return FALSE;
		} else {
			foreach ($stmtVars as $varName) {
				$varNameUpper = strtoupper($varName);
				if (array_key_exists($varNameUpper, $parentFields)) {
					$Child->bindByName($varName, $parentFields[$varNameUpper], FALSE);
				} else {
					return FALSE;
				}
			}
			if (!$Child->isAllBound())
				return FALSE;
			$ChildRs =& $this->_Db->query($Child->getResult());
			if ($ChildRs->recordCount() > 0) {
				$fieldNames = array_change_key_case($ChildRs->fields, CASE_UPPER);
				if (!array_key_exists('CAPTION', $fieldNames) || !array_key_exists('LINK', $fieldNames))
					return FALSE;
			}
			return $ChildRs;
		}
	}
}
?>