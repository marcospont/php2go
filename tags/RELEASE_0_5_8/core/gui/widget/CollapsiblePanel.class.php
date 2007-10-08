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

import('php2go.text.StringUtils');


/**
 * Collapsible panel widget
 *
 * This widget renders a panel that can be expanded and collapsed by
 * clicking on its header. The header contains a caption, an optional
 * tip - that can be used to show different messages in expanded and
 * collapsed mode - and an icon, that shows the proper icon according
 * to the status of the panel contents (collapsed or expanded).
 *
 * Available attributes:
 * # id : panel ID
 * # caption : caption (mandatory)
 * # width : panel width
 * # class : panel CSS class
 * # captionClass: caption's CSS class
 * # collapsed: TRUE or FALSE
 * # expandedTip: tip that must be shown when the panel is expanded
 * # collapsedTip: tip that must be shown when the panel is collapsed
 * # collapseIcon: collapse icon
 * # expandIcon: expand icon
 * # contentClass: panel contents CSS class
 *
 * @package gui
 * @subpackage widget
 * @uses JSONEncoder
 * @uses StringUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class CollapsiblePanel extends Widget
{
	/**
	 * Widget constructor
	 *
	 * @param array $attrs Attributes
	 * @return CollapsiblePanel
	 */
	function CollapsiblePanel($attrs) {
		parent::Widget($attrs);
		$this->isContainer = TRUE;
		$this->mandatoryAttributes[] = 'caption';
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
			'class' => '',
			'width' => '100%',
			'collapsed' => FALSE,
			'captionClass' => '',
			'expandedTip' => '',
			'collapsedTip' => '',
			'collapseIcon' => PHP2GO_ICON_PATH . 'panel_collapse.gif',
			'expandIcon' => PHP2GO_ICON_PATH . 'panel_expand.gif',
			'contentClass' => ''
		);
	}

	/**
	 * Applies the necessary transformation on
	 * attributes before loading them
	 *
	 * @param array $attrs Attributes
	 */
	function loadAttributes($attrs) {
		$attrs['id'] = StringUtils::normalize($attrs['id']);
		if (is_int($attrs['width']))
			$attrs['width'] .= 'px';
		parent::loadAttributes($attrs);
	}

	/**
	 * Renders the CollapsiblePanel widget
	 */
	function render() {
		$attrs =& $this->attributes;
		$code = sprintf("\n<div id=\"%s\"%s style=\"width:%s;\">", $attrs['id'], (!empty($attrs['class']) ? " class=\"{$attrs['class']}\"" : ""), $attrs['width'], $attrs['id']);
		$code .= sprintf("\n  <div id=\"%s_header\"style=\"cursor:pointer;\">", $attrs['id']);
		$code .= sprintf("\n    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%%\"%s><tr>", (!empty($attrs['captionClass']) ? " class=\"{$attrs['captionClass']}\"" : ""));
		$code .= sprintf("\n      <td id=\"%s_caption\" align=\"left\">%s</td>", $attrs['id'], $attrs['caption']);
		if (!empty($attrs['collapsedTip']) && !empty($attrs['expandedTip']))
			$code .= sprintf("\n      <td style=\"vertical-align:middle;\" align=\"right\"><span id=\"%s_tip\" style=\"margin-right:4px;\">%s</span><img id=\"%s_icon\" src=\"%s\" style=\"border:0\" alt=\"\" /></td>", $attrs['id'], ($attrs['collapsed'] ? $attrs['collapsedTip'] : $attrs['expandedTip']), $attrs['id'], ($attrs['collapsed'] ? $attrs['expandIcon'] : $attrs['collapseIcon']));
		else
			$code .= sprintf("\n      <td style=\"vertical-align:middle;\" align=\"right\"><img id=\"%s_icon\" src=\"%s\" style=\"border:0\" alt=\"\" /></td>", $attrs['id'], ($attrs['collapsed'] ? $attrs['expandIcon'] : $attrs['collapseIcon']));
		$code .= "\n    </tr></table>";
		$code .= "\n  </div>";
		$code .= sprintf("\n  <div id=\"%s_content\"%s style=\"display:%s;\">", $attrs['id'], (!empty($attrs['contentClass']) ? " class=\"{$attrs['contentClass']}\"" : ""), ($attrs['collapsed'] ? "none" : "block"));
		$code .= $this->content;
		$code .= "\n  </div>";
		$code .= "\n</div>";
		print $code;
		parent::renderJS(array(
			'id' => $attrs['id'],
			'collapsedTip' => $attrs['collapsedTip'],
			'expandedTip' => $attrs['expandedTip'],
			'collapseIcon' => $attrs['collapseIcon'],
			'expandIcon' => $attrs['expandIcon'],
			'class' => $attrs['class']
		));
	}
}
?>