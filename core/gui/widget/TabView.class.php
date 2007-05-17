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
 * Top orientation
 */
define('TABVIEW_ORIENTATION_TOP', 'top');
/**
 * Bottom orientation
 */
define('TABVIEW_ORIENTATION_BOTTOM', 'bottom');
/**
 * Left orientation
 */
define('TABVIEW_ORIENTATION_LEFT', 'left');
/**
 * Right orientation
 */
define('TABVIEW_ORIENTATION_RIGHT', 'right');

/**
 * Tab view widget
 *
 * The TabView widget provides control over a set
 * of {@link TabPanel} widgets (tabbed views).
 *
 * Available attributes:
 * # id : widget ID
 * # orientation : orientation
 * # activeIndex : active tab index
 * # class : CSS class of the widget's main element
 * # width : widget's width
 * # navigationClass : CSS class of the widget's navigation bar (tab labels)
 * # navigationWidth : navigation bar width (applies to left and right orientations only)
 * # containerClass : CSS class of the tabs container
 * # contentHeight : height of the tab contents
 * # loadCache : enable/disable caching tab content caching, when loaded through AJAX calls
 *
 * Available client events:
 * # onInit
 * # onBeforeChange
 * # onAfterChange
 * # onBeforeLoad
 * # onAfterLoad
 * # onBeforeRemove
 * # onAfterRemove
 *
 * @package gui
 * @subpackage widget
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TabView extends Widget
{
	/**
	 * Tab panels
	 *
	 * @var array
	 */
	var $panels = array();

	/**
	 * Widget constructor
	 *
	 * @param array $attrs Attributes
	 * @return TabView
	 */
	function TabView($attrs) {
		parent::Widget($attrs);
		$this->isContainer = FALSE;
	}

	/**
	 * Loads the resources needed by the
	 * widget onto the active DocumentHead
	 *
	 * @param DocumentHead &$Head Document head
	 * @static
	 */
	function loadResources(&$Head) {
		$Head->addStyle(PHP2GO_CSS_PATH . 'tabview.css');
	}

	/**
	 * Returns the default values for
	 * the widget's attributes
	 *
	 * @return array Default attributes
	 */
	function getDefaultAttributes() {
		return array(
			'id' => PHP2Go::generateUniqueId(parent::getClassName()),
			'orientation' => TABVIEW_ORIENTATION_TOP,
			'activeIndex' => 1,
			'class' => '',
			'width' => '500px',
			'navigationClass' => '',
			'navigationWidth' => '100px',
			'containerClass' => '',
			'contentHeight' => '250px',
			'loadCache' => TRUE
		);
	}

	/**
	 * Applies the necessary transformation on
	 * attributes before loading them
	 *
	 * @param array $attrs Attributes
	 */
	function loadAttributes($attrs) {
		if (is_int($attrs['width']))
			$attrs['width'] .= 'px';
		if (is_int($attrs['navigationWidth']))
			$attrs['navigationWidth'] .= 'px';
		if (is_int($attrs['contentHeight']))
			$attrs['contentHeight'] .= 'px';
		if (!in_array($attrs['orientation'], array(
			TABVIEW_ORIENTATION_TOP, TABVIEW_ORIENTATION_BOTTOM,
			TABVIEW_ORIENTATION_LEFT, TABVIEW_ORIENTATION_RIGHT
		))) {
			$attrs['orientation'] = TABVIEW_ORIENTATION_TOP;
		}
		parent::loadAttributes($attrs);
	}

	/**
	 * Adds a tab panel
	 *
	 * @param TabPanel $TabPanel New tab panel
	 */
	function addPanel($TabPanel) {
		$this->panels[] = $TabPanel;
	}

	/**
	 * Renders the TabView widget
	 */
	function render() {
		if (empty($this->panels))
			PHP2Go::raiseError(sprintf("At least one %s must be declared inside the %s widget!", "TabPanel", "TabView"), E_USER_ERROR, __FILE__, __LINE__);
		$orientationClasses = array(
			TABVIEW_ORIENTATION_TOP => 'tabView',
			TABVIEW_ORIENTATION_BOTTOM => 'tabViewBottom',
			TABVIEW_ORIENTATION_LEFT => 'tabViewLeft',
			TABVIEW_ORIENTATION_RIGHT => 'tabViewRight'
		);
		$attrs =& $this->attributes;
		$code = "\n<style type=\"text/css\">";
		$code .= sprintf("\n#%s .tabContainer div { height: %s; }", $attrs['id'], $attrs['contentHeight']);
		if ($attrs['orientation'] == TABVIEW_ORIENTATION_TOP || $attrs['orientation'] == TABVIEW_ORIENTATION_BOTTOM) {
			$code .= sprintf("\n#%s .tabScrollContainer { width: %s; }", $attrs['id'], $attrs['width']);
		} else {
			$code .= sprintf("\n#%s.tabViewLeft .tabNavigationContainer, #%s.tabViewRight .tabNavigationContainer { width: %s; height: %s; }", $attrs['id'], $attrs['id'], $attrs['navigationWidth'], $attrs['contentHeight']);
			$code .= sprintf("\n#%s .tabScrollContainer { height: %s; }", $attrs['id'], $attrs['contentHeight']);
		}
		if ($attrs['orientation'] == TABVIEW_ORIENTATION_LEFT)
			$code .= sprintf("\n#%s.tabViewLeft { padding-left: %s; }", $attrs['id'], $attrs['navigationWidth']);
		if ($attrs['orientation'] == TABVIEW_ORIENTATION_RIGHT)
			$code .= sprintf("\n#%s.tabViewRight { padding-right: %s; }", $attrs['id'], $attrs['navigationWidth']);
		$code .= sprintf("\n#%s { width: %s; }", $attrs['id'], $attrs['width']);
		$code .= "\n</style>";
		$code .= sprintf("\n<div id=\"%s\" class=\"%s%s\">", $attrs['id'], $orientationClasses[$attrs['orientation']], (!empty($attrs['class']) ? " {$attrs['class']}" : ""));
		if ($attrs['orientation'] != TABVIEW_ORIENTATION_BOTTOM)
			$this->renderNavigation($code);
		$this->renderContent($code);
		if ($attrs['orientation'] == TABVIEW_ORIENTATION_BOTTOM)
			$this->renderNavigation($code);
		$code .= "\n<div>";
		print $code;
		$tabs = array();
		foreach ($this->panels as $panel) {
			$tabs[] = array(
				'id' => $panel->getAttribute('id'),
				'disabled' => $panel->getAttribute('disabled'),
				'loadUri' => $panel->getAttribute('loadUri'),
				'loadMethod' => $panel->getAttribute('loadMethod'),
				'loadParams' => $panel->getAttribute('loadParams')
			);
		}
		parent::renderJS(array(
			'id' => $attrs['id'],
			'activeIndex' => $attrs['activeIndex'],
			'orientation' => $attrs['orientation'],
			'tabs' => $tabs,
			'loadCache' => $attrs['loadCache']
		));
	}

	/**
	 * Render TabView's navigation bar
	 *
	 * @param string &$code Buffer
	 * @access private
	 */
	function renderNavigation(&$code) {
		$attrs =& $this->attributes;
		$code .= "\n  <div class=\"tabScrollContainer\">";
		if ($attrs['orientation'] == TABVIEW_ORIENTATION_TOP || $attrs['orientation'] == TABVIEW_ORIENTATION_BOTTOM) {
			$img1 = 'tabview_sleft.gif';
			$img2 = 'tabview_sright.gif';
		} else {
			$img1 = 'tabview_stop.gif';
			$img2 = 'tabview_sbottom.gif';
		}
		$code .= sprintf("\n    <button class=\"tabScrollArrow\"><img src=\"%s%s\" border=\"0\" alt=\"\" /></button>", PHP2GO_ICON_PATH, $img1);
		$code .= sprintf("\n    <button class=\"tabScrollArrow\"><img src=\"%s%s\" border=\"0\" alt=\"\" /></button>", PHP2GO_ICON_PATH, $img2);
		$code .= "\n  </div>";
		$code .= sprintf("\n  <div class=\"tabNavigationContainer\">");
		$code .= sprintf("\n    <ul class=\"tabNavigation%s\">", (!empty($attrs['navigationClass']) ? " {$attrs['navigationClass']}" : ""));
		foreach ($this->panels as $idx => $panel) {
			$cssClasses = array();
			if (($idx+1) == $attrs['activeIndex'])
				$cssClasses[] = "tabViewSelected";
			$labelClass = $panel->getAttribute('labelClass');
			if (!empty($labelClass))
				$cssClasses[] = $labelClass;
			$code .= sprintf("<li%s><a href=\"javascript:;\" title=\"%s\"%s><em>%s</em></a></li>", (!empty($cssClasses) ? " class=\"" . join(' ', $cssClasses) . "\"" : ""), $panel->getAttribute('caption'), ($panel->getAttribute('disabled') ? " disabled=\"disabled\"" : ""), $panel->getAttribute('caption'));
		}
		$code .= "</ul>";
		$code .= "\n  </div>";
	}

	/**
	 * Render TabView's panels
	 *
	 * @param string &$code Buffer
	 */
	function renderContent(&$code) {
		$attrs =& $this->attributes;
		$code .= sprintf("\n  <div class=\"tabContainer%s\">", (!empty($attrs['containerClass']) ? " {$attrs['containerClass']}" : ""));
		foreach ($this->panels as $idx => $panel) {
			$cssClasses = array();
			if (($idx+1) == $attrs['activeIndex'])
				$cssClasses[] = "tabViewVisible";
			$contentClass = $panel->getAttribute('contentClass');
			if (!empty($contentClass))
				$cssClasses[] = $contentClass;
			$code .= sprintf("\n    <div id=\"%s\"%s>", $panel->getAttribute('id'), (!empty($cssClasses) ? " class=\"" . join(' ', $cssClasses) . "\"" : ""));
			$code .= $panel->content;
			$code .= "\n    </div>";
		}
		$code .= "\n  </div>";
	}
}
?>