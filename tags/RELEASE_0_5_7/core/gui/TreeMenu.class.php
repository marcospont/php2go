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

import('php2go.gui.Menu');

/**
 * Builds an expandable/collapsible menu tree
 *
 * This class uses the bundled library CoolJSTree to
 * build the menu widget.
 *
 * Example:
 * <code>
 * /* layout.tpl {@*}
 * {$menu}
 * <table width="780" cellpadding="0" cellspacing="0" border="0">
 *   <tr>
 *     <td style="width:170px" valign="top">&nbsp;</td>
 *     <td>{$main}</td>
 *   </tr>
 * </table>
 *
 * /* menu.xml {@*}
 * <menu>
 *   <item link="page1.php" caption="Page 1"/>
 *   <item link="page2.php" caption="Page 2">
 *     <item link="page21.php" caption="Page 2.1"/>
 *     <item link="page22.php" caption="Page 2.2"/>
 *     <item link="page23.php" caption="Page 2.3"/>
 *     <item link="page24.php" caption="Page 2.4"/>
 *   </item>
 *   <item link="page3.php" caption="Page 3"/>
 *   <item link="page4.php" caption="Page 4">
 *     <item link="page41.php" caption="Page 4.1"/>
 *     <item link="page42.php" caption="Page 4.2"/>
 *   </item>
 * </menu>
 *
 * /* menu.css {@*}
 * .menu, .menuChild {
 *   font-family: Verdana;
 *   font-size: 11px;
 *   text-decoration: none;
 * }
 * .menu {
 *   color: #333;
 * }
 * .menuChild {
 *   color: #666;
 * }
 *
 * /* page.php {@*}
 * $doc = new Document('layout.tpl');
 * $doc->addStyle('menu.css');
 * $menu = new TreeMenu($doc);
 * $menu->setStartPoint(5, 55);
 * $menu->setDefaultStyle('menuChild');
 * $menu->setLevelStyle(0, 'menu');
 * $menu->hideButtons();
 * $menu->oneBranchAtOnce();
 * $menu->loadFromXmlFile('menu.xml');
 * $doc->assignByRef('menu', $menu);
 * $doc->assign('main', "Hello World!");
 * $doc->display();
 * </code>
 *
 * @package gui
 * @link http://javascript.cooldev.com/scripts/cooltree/
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TreeMenu extends Menu
{
	/**
	 * Left X coordinate (absolute position)
	 *
	 * @var int
	 */
	var $offsetX = 0;

	/**
	 * Upper Y coordinate (absolute position)
	 *
	 * @var int
	 */
	var $offsetY = 0;

	/**
	 * Whether expand/collapse buttons should be displayed
	 *
	 * @var bool
	 */
	var $showButtons = TRUE;

	/**
	 * Whether folder icons should be displayed
	 *
	 * @var bool
	 */
	var $showFolders = TRUE;

	/**
	 * Whether to open one branch at once
	 *
	 * @var bool
	 */
	var $oneBranch = FALSE;

	/**
	 * Tree's background color
	 *
	 * @var string
	 */
	var $backgroundColor = "";

	/**
	 * Default CSS class for all tree levels
	 *
	 * @var string
	 */
	var $defaultStyle = "null";

	/**
	 * Custom CSS classes for the menu levels
	 *
	 * @var array
	 */
	var $customStyles = array();

	/**
	 * Indentation between the tree levels, in pixels
	 *
	 * @var int
	 */
	var $levelIdent = 16;

	/**
	 * Prefix for all item links
	 *
	 * @var string
	 */
	var $addressPrefix;

	/**
	 * Internal padding for all tree nodes
	 *
	 * @var int
	 */
	var $itemPadding = 0;

	/**
	 * Internal spacing for all tree nodes
	 *
	 * @var int
	 */
	var $itemSpacing = 0;

	/**
	 * Default tree icons
	 *
	 * @var array
	 */
	var $icons;

	/**
	 * Default width for all tree icons
	 *
	 * @var int
	 */
	var $iconWidth = 16;

	/**
	 * Default height for all tree icons
	 *
	 * @var int
	 */
	var $iconHeight = 16;

	/**
	 * Generated JS code
	 *
	 * @var string
	 * @access private
	 */
	var $menuCode = '';

	/**
	 * Class constructor
	 *
	 * @param Document &$Document Document instance in which the menu should be rendered
	 * @return TreeMenu
	 */
	function TreeMenu(&$Document) {
    	parent::Menu($Document);
		$this->icons = array(
							'collapse' => PHP2GO_ICON_PATH . 'tree_collapse.gif',
							'expand' => PHP2GO_ICON_PATH . 'tree_expand.gif',
							'blank' => PHP2GO_ICON_PATH . 'tree_blank.gif',
							'document' => PHP2GO_ICON_PATH . 'tree_document.gif',
							'folder_opened' => PHP2GO_ICON_PATH . 'tree_folder_opened.gif',
							'folder_closed' => PHP2GO_ICON_PATH . 'tree_folder_closed.gif');
	}

	/**
	 * Set the menu's absolute position
	 *
	 * @param int $left Left X coordinate
	 * @param int $top Upper Y coordinate
	 */
	function setStartPoint($left, $top) {
		$this->offsetX = abs(intval($left));
		$this->offsetY = abs(intval($top));
	}

	/**
	 * Flags the class to hide expand/collapse buttons
	 *
	 * By default, expand/collapse buttons are visible.
	 */
	function hideButtons() {
    	$this->showButtons = FALSE;
	}

	/**
	 * Flags the class to hide folder icons
	 *
	 * By default, tree folders are visible.
	 */
	function hideFolders() {
    	$this->showFolders = FALSE;
	}

	/**
	 * Configures the menu to keep just one
	 * branch opened at once
	 *
	 * Defaults to FALSE.
	 */
	function oneBranchAtOnce() {
		$this->oneBranch = TRUE;
	}

	/**
	 * Set the menu's background color
	 *
	 * Defaults to 'transparent'.
	 *
	 * @param string $color Background color
	 */
	function setBackgroundColor($color) {
    	$this->backgroundColor = $color;
	}

	/**
	 * Set the default CSS class for all tree levels
	 *
	 * @param string $style CSS class
	 */
	function setDefaultStyle($style) {
		$this->defaultStyle = $style;
	}

	/**
	 * Set a CSS class name for a specific tree level
	 *
	 * @param int $which Level
	 * @param string $style CSS class
	 */
	function setLevelStyle($which, $style) {
		if (TypeUtils::isInteger($which)) {
        	$this->customStyles[$which] = $style;
		}
	}

	/**
	 * Set indentation between tree levels, in pixels
	 *
	 * @param int $ident Indentation
	 */
	function setLevelIdent($ident) {
		$this->levelIdent = abs(intval($ident));
	}

	/**
	 * Define a prefix for all links inside menu items
	 *
	 * @param string $prefix Link prefix
	 */
	function setAddressPrefix($prefix) {
		$this->addressPrefix = $prefix;
	}

	/**
	 * Set an internal padding for all tree nodes
	 *
	 * @param int $padding Padding
	 */
	function setItemPadding($padding) {
		$this->itemPadding = abs(intval($padding));
	}

	/**
	 * Set the spacing between tree nodes
	 *
	 * @param int $spacing Spacing
	 */
	function setItemSpacing($spacing) {
		$this->itemSpacing = abs(intval($spacing));
	}

	/**
	 * Change a tree icon given its name
	 *
	 * @param string $which Icon name
	 * @param string $imageName Icon path
	 */
	function setImage($which, $imageName) {
    	if (isset($this->icons[$which])) {
        	$this->icons[$which] = $imageName;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_INVALID_IMAGE', array($which, 'collapse, expand, blank, document, folder_opened, folder_closed')), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Changes the dimensions of tree icons
	 *
	 * @param int $width Icons width
	 * @param int $height Icons height
	 */
	function setImageSize($width, $height) {
		$this->iconWidth = abs(intval($width));
		$this->iconHeight = abs(intval($height));
	}

	/**
	 * Prepares the menu to be rendered
	 */
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			parent::buildMenu();
			$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/cooljstree/cooltree.js");
		}
	}

	/**
	 * Builds and returns the menu's Javascript code
	 *
	 * @return string
	 */
	function getContent() {
		$this->onPreRender();
		$this->_buildCode();
		return $this->menuCode;
	}

	/**
	 * Builds and displays the menu's Javascript code
	 */
	function display() {
		$this->onPreRender();
		$this->_buildCode();
		print $this->menuCode;
	}

	/**
	 * Internal method used to build the menu's JS code
	 *
	 * @access private
	 */
	function _buildCode() {
    	$this->menuCode = "\n<script type=\"text/javascript\" language=\"Javascript\">";
		$this->menuCode .= "\nvar {$this->id}Format = [";
		$this->menuCode .= $this->offsetX . ', ' . $this->offsetY . ', ';
		$this->menuCode .= $this->showButtons ? 'true, ' : 'false, ';
		$this->menuCode .= "[\"" . $this->icons['collapse'] . "\", \"" . $this->icons['expand'] . "\", \"" . $this->icons['blank'] . "\"], ";
		$this->menuCode .= '[' . $this->iconWidth . ', ' . $this->iconHeight . ', 0], ';
		$this->menuCode .= $this->showFolders ? 'true, ' : 'false, ';
		$this->menuCode .= "[\"" . $this->icons['folder_closed'] . "\", \"" . $this->icons['folder_opened'] . "\", \"" . $this->icons['document'] . "\"], ";
		$this->menuCode .= '[' . $this->iconWidth . ', ' . $this->iconHeight . '], ';
		$this->menuCode .= '[';
		for ($i=0; $i<=$this->lastLevel; $i++) {
			$this->menuCode .= $i * $this->levelIdent;
			if ($i < $this->lastLevel) $this->menuCode .= ", ";
		}
		$this->menuCode .= '], ';
		$this->menuCode .= "\"" . $this->backgroundColor . "\", ";
		$this->menuCode .= "\"" . $this->defaultStyle . "\", ";
		$this->menuCode .= "[";
		for ($i=0; $i<=$this->lastLevel; $i++) {
			if (isset($this->customStyles[$i])) {
				$this->menuCode .= "\"" . $this->customStyles[$i] . "\"";
			}
			if ($i < $this->lastLevel) $this->menuCode .= ",";
		}
		$this->menuCode .= "], ";
		$this->menuCode .= $this->oneBranch ? 'true, ' : 'false, ';
		$this->menuCode .= '[' . $this->itemPadding . ', ' . $this->itemSpacing . ']];';
		$this->menuCode .= "\nvar " . $this->id . "Nodes = [";
		for ($i=0; $i<$this->rootSize; $i++) {
			$this->menuCode .= "['" . $this->tree[$i]['CAPTION'] . "',";
			$this->menuCode .= " '" . (isset($this->addressPrefix) ? $this->addressPrefix : '') . $this->tree[$i]['LINK'] . "', '" . $this->tree[$i]['TARGET'] . "'";
			$this->menuCode .= $this->_buildChildrenCode($this->tree[$i]['CHILDREN']) . "]";
			if ($i < ($this->rootSize - 1)) $this->menuCode .= ", ";
		}
		$this->menuCode .= "];\n";
		$this->menuCode .= "new COOLjsTree (\"" . $this->id . "\", " . $this->id . "Nodes, " . $this->id . "Format);";
		$this->menuCode .= "\n</script>\n";
	}

	/**
	 * Recursive method used to build menu branches
	 *
	 * @param array $children Child items
	 * @access private
	 */
	function _buildChildrenCode($children) {
		$childrenSize = (is_array($children) ? sizeof($children) : 0);
    	if (!$childrenSize) {
        	return '';
 		} else {
			$code = ', ';
			for ($i=0; $i<$childrenSize; $i++) {
				$code .= "['" . $children[$i]['CAPTION'] . "',";
				$code .= " '" . $children[$i]['LINK'] . "', '" . $children[$i]['TARGET'] . "'";
				$code .= $this->_buildChildrenCode($children[$i]['CHILDREN']) . "]";
				if ($i < ($childrenSize - 1)) $code .= ", ";
			}
			return $code;
		}
	}
}
?>