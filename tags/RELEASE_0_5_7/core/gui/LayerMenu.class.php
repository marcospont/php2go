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
 * Root items are rendered side by side
 */
define('LAYER_MENU_SIDE', 1);
/**
 * Root items are all rendered with the same width
 */
define('LAYER_MENU_EQUAL', 2);

/**
 * Builds a DHTML drop down menu
 *
 * This class uses the bundled library Coolmenus to
 * build the menu widget.
 *
 * Example:
 * <code>
 * /* layout.tpl {@*}
 * <div style="height:25px;">{$menu}</div>
 * <div>{$main}</div>
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
 * .menu, .menuOver, .menuChild, .menuChildOver, .menuBorder {
 *   position: absolute;
 *   cursor: pointer;
 * }
 * .menu, .menuOver, .menuChild, .menuChildOver {
 *   font-family: Verdana, Arial, Helvetica, sans-serif;
 *   font-size: 10px;
 *   font-weight: bold;
 *   text-decoration: none;
 * }
 * .menu, .menuChild {
 *   color: #000;
 *   background-color: #dde4ea;
 * }
 * .menuOver, .menuChildOver {
 *   color: #ed1d24;
 *   background-color: #eff2f7;
 * }
 * .menuBorder {
 *   background-color: #6d6d6d;
 * }
 *
 * /* page.php {@*}
 * $doc = new Document('layout.tpl');
 * $doc->addStyle('menu.css');
 * $menu = new LayerMenu($doc);
 * $menu->setSize(500, 15);
 * $menu->setStartPoint(25, 5);
 * $menu->setRootStyles('menu', 'menuOver');
 * $menu->setRootDisposition(LAYER_MENU_EQUAL);
 * $menu->setChildrenStyles('menuChild', 'menuChildOver', 'menuBorder', 1, 1);
 * $menu->loadFromXmlFile('menu.xml');
 * $doc->assignByRef('menu', $menu);
 * $doc->assign('main', "Hello World!");
 * $doc->display();
 * </code>
 *
 * @package gui
 * @link http://www.dhtmlcentral.com/
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LayerMenu extends Menu
{
	/**
	 * Whether to use horizontal (TRUE) or vertical (FALSE) orientation
	 *
	 * @var bool
	 */
	var $isHorizontal = TRUE;

	/**
	 * Menu width
	 *
	 * Total width for horizontal menus, item width for vertical menus.
	 *
	 * @var int
	 */
	var $width;

	/**
	 * Menu height
	 *
	 * Total height for vertical menus, item height for horizontal menus.
	 *
	 * @var int
	 */
	var $height = 20;

	/**
	 * Menu positioning
	 *
	 * @var int
	 */
	var $positioning = MENU_ABSOLUTE;

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
	 * Spacing between root level items
	 *
	 * @var int
	 */
	var $itemSpacing = 0;

	/**
	 * Average char width, in pixels
	 *
	 * @var int
	 */
	var $charWidth = 7;

	/**
	 * Prefix for all item links
	 *
	 * @var string
	 */
	var $addressPrefix = '';

	/**
	 * Root level disposition
	 *
	 * @var int
	 */
	var $rootDisposition;

	/**
	 * Style settings for the root level
	 *
	 * @var array
	 */
	var $rootStyles = array();

	/**
	 * Style settings for the child levels
	 *
	 * @var array
	 */
	var $childrenStyles = array();

	/**
	 * Height of the items of child levels
	 *
	 * @var int
	 */
	var $childrenHeight = 18;

	/**
	 * Menu level timeout, in miliseconds
	 *
	 * @var int
	 */
	var $childrenTimeout = 100;

	/**
	 * Minimum width of a menu item
	 *
	 * @var int
	 */
	var $minimumChildWidth = 0;

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
	 * @return LayerMenu
	 */
	function LayerMenu(&$Document) {
		parent::Menu($Document);
		$this->rootDisposition = LAYER_MENU_EQUAL;
	}

	/**
	 * Defines menu positioning
	 *
	 * @param int $pos {@link MENU_ABSOLUTE} or {@link MENU_RELATIVE}
	 */
	function setPositioning($pos) {
		if ($pos == MENU_ABSOLUTE || $pos == MENU_RELATIVE)
			$this->positioning = $pos;
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
		$this->positioning = MENU_ABSOLUTE;
	}

	/**
	 * Changes menu orientation to vertical
	 */
	function setVertical() {
		$this->isHorizontal = false;
	}

	/**
	 * Set menu size
	 *
	 * On horizontal menus, $width means total width and $height
	 * means height of the root level items.
	 *
	 * On vertical menus, $width means width of the root level
	 * items and $height means total menu height.
	 *
	 * @param int $width Width
	 * @param int $height Height
	 */
	function setSize($width, $height = 0) {
		$this->width = abs($width);
		if ($height) $this->height = abs($height);
	}

	/**
	 * Set the width of the chars used at the menu item captions
	 *
	 * @param int $width Char width
	 */
	function setCharWidth($width) {
		$this->charWidth = abs(intval($width));
	}

	/**
	 * Set the spacing between root level items
	 *
	 * @param int $itemSpacing Spacing, in pixels
	 */
	function setItemSpacing($itemSpacing) {
		$this->itemSpacing = abs(intval($itemSpacing));
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
	 * Set the disposition of the root level items
	 *
	 * This setting is ignored by vertical menus.
	 *
	 * @param int $disposition {@link LAYER_MENU_SIDE} or {@link LAYER_MENU_EQUAL}
	 */
	function setRootDisposition($disposition) {
		if ($disposition == LAYER_MENU_SIDE || $disposition == LAYER_MENU_EQUAL) {
			$this->rootDisposition = $disposition;
		}
	}

	/**
	 * Defines style settings to the menu's root level
	 *
	 * @param string $reg CSS class
	 * @param string $over Hover CSS class
	 * @param string $border Border CSS class
	 * @param int $borderX Size of the top/bottom borders
	 * @param int $borderY Size of the left/right borders
	 */
	function setRootStyles($reg, $over = '', $border = '', $borderX = '', $borderY = '') {
		$this->rootStyles['reg'] = $reg;
		$this->rootStyles['over'] = ($over == '') ? $reg : $over;
		$this->rootStyles['border'] = $border;
		$this->rootStyles['borderX'] = !empty($borderX) ? $borderX : '0.00001';
		$this->rootStyles['borderY'] = !empty($borderY) ? $borderY : '0.00001';
	}

	/**
	 * Defines style settings to the menu's child levels
	 *
	 * @param string $reg CSS class
	 * @param string $over Hover CSS class
	 * @param string $border Border CSS class
	 * @param int $borderX Size of the top/bottom borders
	 * @param int $borderY Size of the left/right borders
	 */
	function setChildrenStyles($reg, $over = '', $border = '', $borderX = '', $borderY = '') {
		$this->childrenStyles['reg'] = $reg;
		$this->childrenStyles['over'] = ($over == '') ? $reg : $over;
		$this->childrenStyles['border'] = $border;
		$this->childrenStyles['borderX'] = !empty($borderX) ? $borderX : '0.00001';
		$this->childrenStyles['borderY'] = !empty($borderY) ? $borderY : '0.00001';
	}

	/**
	 * Set the item height for all child levels
	 *
	 * @param int $height Item height
	 */
	function setChildrenHeight($height) {
		$this->childrenHeight = abs(intval($height));
	}

	/**
	 * Set the timeout of a menu level
	 *
	 * Time to wait, in miliseconds, until a menu level is closed,
	 * right after it looses focus.
	 *
	 * @param int $timeout Timeout
	 */
	function setChildrenTimeout($timeout) {
		$this->childrenTimeout = abs(intval($timeout));
	}

	/**
	 * Set the minimum width of an item of the menu's child levels
	 *
	 * @param int $min Minimum width
	 */
	function setMininumChildWidth($min) {
		$this->minimumChildWidth = $min;
	}

	/**
	 * Prepares the menu to be rendered
	 */
	function onPreRender() {
		if (!$this->preRendered) {
			parent::onPreRender();
			parent::buildMenu();
			$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "vendor/coolmenus/coolmenus4.js");
			$this->_Document->appendHeaderContent(
				"<style type=\"text/css\">\n" .
				"     .clCMEvent { position:absolute; z-index:300; width:100%; height:100%; clip:rect(0,100%,100%,0); left:0; top:0; visibility:hidden }\n" .
				"     .clCMAbs { position:absolute; width:10; height:10; left:0; top:0; visibility:hidden }\n" .
				"</style>\n"
			);
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
		// calculate dimensions when menu is vertical
		if (!$this->isHorizontal) {
			if (!isset($this->width)) {
				$textSize = 1;
				for ($i = 0; $i < sizeof($this->tree); $i++)
					$textSize = (strlen($this->tree[$i]['CAPTION']) > $textSize) ? strlen($this->tree[$i]['CAPTION']) : $textSize;
				$textSize = ($textSize * $this->charWidth) + 15;
				$this->width = $textSize;
			}
			if (!isset($this->height))
				$this->height = $this->rootSize * $this->childrenHeight;
		}
		if ($this->positioning == MENU_RELATIVE)
			$this->menuCode .= "\n<div id=\"{$this->id}\"></div>";
		$this->menuCode .=
			"\n<script type=\"text/javascript\" language=\"Javascript\">" .
			"\n\tvar {$this->id} = new makeCM(\"{$this->id}\");" .
			"\n\t{$this->id}.resizeCheck = 1;" .
			"\n\t{$this->id}.openOnClick = 1;" .
			"\n\t{$this->id}.rows = " . ((int)$this->isHorizontal) . ";" .
			"\n\t{$this->id}.onlineRoot = \"{$this->addressPrefix}\";" .
			"\n\t{$this->id}.menuPlacement = 0;" .
			"\n\t{$this->id}.pxBetween = {$this->itemSpacing};" .
			"\n\t{$this->id}.wait = {$this->childrenTimeout};" .
			"\n\t{$this->id}.zIndex = 400;";
		// absolute positioning
		if ($this->positioning == MENU_ABSOLUTE) {
			$this->menuCode .=
				"\n\t{$this->id}.fromLeft = {$this->offsetX};" .
				"\n\t{$this->id}.fromTop = {$this->offsetY};";
		}
		// relative positioning
		else {
			$this->menuCode .=
				"\n\t{$this->id}.parentPos = $(\"{$this->id}\").getPosition();" .
				"\n\t{$this->id}.fromLeft = {$this->id}.parentPos.x;" .
				"\n\t{$this->id}.fromTop = {$this->id}.parentPos.y;" .
				"\n\t{$this->id}.onresize = function() {" .
				"\n\t\t{$this->id}.parentPos = $(\"{$this->id}\").getPosition();" .
				"\n\t\t{$this->id}.fromLeft = {$this->id}.parentPos.x;" .
				"\n\t\t{$this->id}.fromTop = {$this->id}.parentPos.y;" .
				"\n\t}";
		}
		// horizontal menu
		if ($this->isHorizontal) {
			// root height
			$rootHeight = $this->height+1;
			// equal width for all root items
			if ($this->rootDisposition == LAYER_MENU_EQUAL) {
				$textSize = 1;
				for ($i = 0; $i < sizeof($this->tree); $i++)
					$textSize = (strlen($this->tree[$i]['CAPTION']) > $textSize) ? strlen($this->tree[$i]['CAPTION']) : $textSize;
				$textSize = ($textSize * $this->charWidth) + 15;
				for ($i=0; $i<sizeof($this->tree); $i++)
					$rootWidth[$i] = $textSize;
			}
			// root items rendered side by side
			elseif ($this->rootDisposition == LAYER_MENU_SIDE) {
				for ($i=0; $i<sizeof($this->tree); $i++)
					$rootWidth[$i] = (strlen($this->tree[$i]['CAPTION']) * $this->charWidth) + 15;
			}
			$this->menuCode .=
				"\n\t{$this->id}.useBar = 1;" .
				"\n\t{$this->id}.barWidth = " . (isset($this->width) ? $this->width : "screen.width") . "-" . ($this->offsetX) . "-22;" .
				"\n\t{$this->id}.barHeight = {$this->height};" .
				"\n\t{$this->id}.barX = {$this->offsetX};" .
				"\n\t{$this->id}.barY = {$this->offsetY};" .
				"\n\t{$this->id}.barClass = \"" . $this->_getStyle(0, 'reg') . "\";" .
				"\n\t{$this->id}.barBorderX = 0;" .
				"\n\t{$this->id}.barBorderY = 0;";
			for ($i = 0; $i <= $this->lastLevel; $i++)
				$this->menuCode .= "\n\t{$this->id}.level[$i] = new cm_makeLevel(80, " . (($i==0) ? $this->height : $this->childrenHeight) . ", \"" . $this->_getStyle($i, 'reg') . "\", \"" . $this->_getStyle($i, 'over') . "\", " . $this->_getStyle($i, 'borderX') . ", " . $this->_getStyle($i, 'borderY') . ", \"" . $this->_getStyle($i, 'border') . "\" , 0, \"bottom\", -1, -1, \"\", 10, 10, 0);";
		// vertical menu
		} else {
			// root height
			$rootHeight = $this->childrenHeight;
			// define placement of the root items
			$placement = $this->offsetY;
			$rootWidth[0] = $this->width;
			for ($i = 1; $i < $this->rootSize; $i++) {
				$rootWidth[$i] = $this->width;
				$placement .= "," . ($this->offsetY + ($i*$this->childrenHeight));
			}
			$this->menuCode .= "\n\t{$this->id}.menuPlacement = new Array($placement);";
			// build the menu levels
			for ($i = 0; $i <= $this->lastLevel; $i++)
				$this->menuCode .= "\n\t{$this->id}.level[$i] = new cm_makeLevel(" . $this->width . ", " . $this->childrenHeight . ", \"" . $this->_getStyle($i, 'reg') . "\", \"" . $this->_getStyle($i, 'over') . "\", " . $this->_getStyle($i, 'borderX') . ", " . $this->_getStyle($i, 'borderY') . ", \"" . $this->_getStyle($i, 'border') . "\" , 0, \"right\", 0, -1, \"\", 10, 10);";
		}
		// recursively build the menu items
		for ($i = 0; $i < sizeof($this->tree); $i++) {
			$thisId = "m" . $i;
			$this->menuCode .= "\n\t{$this->id}.makeMenu('{$thisId}', '', '{$this->tree[$i]['CAPTION']}', '{$this->tree[$i]['LINK']}', '{$this->tree[$i]['TARGET']}', {$rootWidth[$i]}, {$rootHeight});";
			if (!empty($this->tree[$i]['CHILDREN']))
				$this->_buildChildrenCode($this->tree[$i]['CHILDREN'], $thisId, 0);
		}
		$this->menuCode .= "\n\t{$this->id}.construct();";
		$this->menuCode .= "\n</script>\n";
	}

	/**
	 * Recursively builds menu levels
	 *
	 * @param array $children Child items
	 * @param string $parentId Parent item's ID
	 * @param int $parentLevel Parent level
	 * @access private
	 */
	function _buildChildrenCode($children, $parentId, $parentLevel) {
		$textSize = 1;
		for ($i = 0; $i < sizeof($children); $i++) {
			$textSize = (strlen($children[$i]['CAPTION']) > $textSize) ? strlen($children[$i]['CAPTION']) : $textSize;
		}
		$itemWidth = max(($textSize * $this->charWidth) + 15, $this->minimumChildWidth);
		for ($i = 0; $i < sizeof($children); $i++) {
			$thisId = $parentId . "_c" . $i;
			$this->menuCode .= "\n\t{$this->id}.makeMenu('{$thisId}', '{$parentId}', '{$children[$i]['CAPTION']}', '{$children[$i]['LINK']}', '{$children[$i]['TARGET']}', {$itemWidth}, {$this->childrenHeight}, '', '', '', '', 'right');";
			if (!empty($children[$i]['CHILDREN'])) {
				$this->_buildChildrenCode($children[$i]['CHILDREN'], $thisId, ($parentLevel + 1));
			}
		}
	}

	/**
	 * Utility method to read style properties
	 *
	 * @param int $level Current menu level
	 * @param string $element Style property
	 * @access private
	 * @return mixed
	 */
	function _getStyle($level, $element) {
		$repository = ($level > 0) ? $this->childrenStyles : $this->rootStyles;
		$value = (isset($repository[$element])) ? $repository[$element] : ($element == 'borderX' || $element == 'borderY' ? 1 : '');
		return $value;
	}
}
?>