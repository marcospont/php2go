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
 * @version $Id: TemplateContainer.class.php 177 2007-03-21 21:22:08Z mpont $
 */

/**
 * Slide show widget
 *
 * The purpose of this widget is to generate a slide show where one or
 * more images are displayed in sequence, rotating automatically.
 *
 * Available attributes
 * # id : widget ID
 * # images : array of images of the slide show (mandatory)
 * # imagesFolder : a folder name where the images should be collected from
 * # imagesFolderFilter : the filter that must be applied when listing the images folder
 * # delay : slide show delay, in microseconds
 * # play : initial state (playing or stopped)
 * # playCaption : button caption when stopped
 * # pauseCaption : button caption when playing
 * # class : CSS class for the slide show container
 * # toolbarClass : CSS class for the slide show toolbar
 * # width : width for the slide show container
 * # height : height for the slide show container
 *
 * @package gui
 * @subpackage widget
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class SlideShow extends Widget
{
	/**
	 * Widget constructor
	 *
	 * @param array $attrs Attributes
	 * @return SlideShow
	 */
	function SlideShow($attrs) {
		parent::Widget($attrs);
		$this->mandatoryAttributes[] = 'images';
	}

	/**
	 * Loads the resources needed by the
	 * widget onto the active DocumentHead
	 *
	 * @param DocumentHead &$Head Document head
	 * @static
	 */
	function loadResources(&$Head) {
		$Head->addStyle(PHP2GO_CSS_PATH . 'slideshow.css');
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
			'images' => array(),
			'imagesFolder' => '',
			'imagesFolderFilter' => '',
			'delay' => 4000,
			'play' => TRUE,
			'playCaption' => 'Play',
			'pauseCaption' => 'Pause',
			'class' => '',
			'toolbarClass' => '',
			'width' => '400px',
			'height' => '400px'
		);
	}

	/**
	 * Applies the necessary transformation on
	 * attributes before loading them
	 *
	 * @param array $attrs Attributes
	 */
	function loadAttributes($attrs) {
		// normalize dimensions
		if (is_int($attrs['width']))
			$attrs['width'] .= 'px';
		if (is_int($attrs['height']))
			$attrs['height'] .= 'px';
		// load images from a folder
		if (empty($attrs['images']) && !empty($attrs['imagesFolder'])) {
			import('php2go.file.DirectoryManager');
			$Dir = new DirectoryManager();
			$Dir->open($attrs['imagesFolder']);
			while ($Entry = $Dir->read(strval($attrs['imagesFolderFilter']))) {
				$attrs['images'][] = array('url' => rtrim($attrs['imagesFolder'], '/') . '/' . $Entry->getName(), 'description' => $Entry->getName());
			}
			$Dir->close();
		}
		parent::loadAttributes($attrs);
	}

	/**
	 * Validates the widget's properties
	 */
	function validate() {
		parent::validate();
		if (empty($this->attributes['images']))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_WIDGET_MANDATORY_PROPERTY', array('images', parent::getClassName())), E_USER_ERROR, __FILE__, __LINE__);
	}

	/**
	 * Renders the SlideShow widget
	 */
	function render() {
		$attrs =& $this->attributes;
		$code = sprintf("\n<div id=\"%s\"%s style=\"width:%s;\">", $attrs['id'], (!empty($attrs['class']) ? " class=\"{$attrs['class']}\"" : ""), $attrs['width'], $attrs['height']);
		$code .= sprintf("\n  <div style=\"position:relative;width:%s;height:%s;vertical-align:middle\">", $attrs['width'], $attrs['height']);
		$code .= sprintf("\n    <img id=\"%s_foreground\" style=\"z-index:1;visibility:hidden;\" class=\"slideShowImg\">", $attrs['id']);
		$code .= sprintf("\n    <img id=\"%s_background\" style=\"z-index:0;visibility:hidden;\" class=\"slideShowImg\">", $attrs['id']);
		$code .= "\n  </div>";
		$code .= sprintf("\n  <table id=\"%s_toolbar\" width=\"100%%\" cellpadding=\"3\" cellspacing=\"0\" border=\"0\" style=\"margin-top:5px;\"%s><tr>", $attrs['id'], (!empty($attrs['toolbarClass']) ? " class=\"{$attrs['toolbarClass']}\"" : ""));
		$code .= sprintf("\n    <td align=\"left\"><button id=\"%s_toggle\" type=\"button\">%s</button><span id=\"%s_count\" style=\"margin-left:8px;\"></span></td>", $attrs['id'], ($attrs['play'] ? $attrs['pauseCaption'] : $attrs['playCaption']), $attrs['id']);
		$code .= sprintf("\n    <td align=\"center\" id=\"%s_description\"></td>", $attrs['id']);
		$code .= sprintf("\n    <td align=\"right\">%s<input id=\"%s_delay\" type=\"text\" size=\"4\" maxlength=\"2\" value=\"%s\" class=\"slideShowInput\"></td>", 'Delay', $attrs['id'], ($attrs['delay']/1000));
		$code .= "\n  </tr></table>";
		$code .= "\n</div>";
		print $code;
		parent::renderJS(array(
			'id' => $attrs['id'],
			'width' => intval(preg_replace("/[^0-9]+/", "", $this->attributes['width'])),
			'images' => $attrs['images'],
			'delay' => $attrs['delay'],
			'play' => $attrs['play'],
			'playCaption' => $attrs['playCaption'],
			'pauseCaption' => $attrs['pauseCaption']
		));
	}
}
?>