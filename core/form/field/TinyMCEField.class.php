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

import('php2go.util.json.JSONEncoder');

/**
 * Default width for the editor's area
 */
define('TINYMCE_DEFAULT_WIDTH', 600);
/**
 * Default height for the editor's area
 */
define('TINYMCE_DEFAULT_HEIGHT', 300);
/**
 * Default theme to be used
 */
define('TINYMCE_DEFAULT_THEME', 'advanced');

/**
 * Builds a WYSIWYG HTML editor
 *
 * This form component is a WYSIWYG HTML editor based on the
 * third party library tinyMCE. More information can be found
 * in the library's webpage.
 *
 * @package form
 * @subpackage field
 * @link http://tinymce.moxiecode.com
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TinyMCEField extends FormField
{
	/**
	 * Indicates if component is in read-only mode
	 *
	 * @var bool
	 * @access private
	 */
	var $readOnly = FALSE;

	/**
	 * Tiny MCE default parameters
	 *
	 * @var array
	 * @access private
	 */
	var $editorParams = array();

	/**
	 * Tiny MCE parameters defined on the XML CDATA section
	 *
	 * @var string
	 * @access private
	 */
	var $editorJsParams = '';

	/**
	 * Language code mappings
	 *
	 * @var array
	 * @access private
	 */
	var $editorLangMap = array(
		'pt-br' => 'pt',
		'en-us' => 'en',
		'es' => 'es',
		'cs' => 'cs',
		'it' => 'it',
		'de-de' => 'de',
		'fr-fr' => 'fr',
		'th' => 'en'
	);

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return TinyMCEField
	 */
	function TinyMCEField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'TEXTAREA';
		$this->editorParams = array(
			'theme' => TINYMCE_DEFAULT_THEME,
			'tabfocus_elements' => ':prev,:next',
			'language' => $this->editorLangMap[PHP2Go::getConfigVal('LANGUAGE_CODE')],
			'width' => TINYMCE_DEFAULT_WIDTH,
			'height' => TINYMCE_DEFAULT_HEIGHT
		);
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		print sprintf("<textarea id=\"%s\" name=\"%s\" cols=\"\" rows=\"\" title=\"%s\"%s style=\"width:%spx;height:%spx;\"%s%s%s>%s</textarea>",
			$this->id, $this->name, $this->label, $this->attributes['STYLE'], $this->editorParams['width'], $this->editorParams['height'], $this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->value
		);
		print sprintf("<script type=\"text/javascript\">new TinyMCEField($('%s'), %s);</script>",
			$this->id, $this->_getParamsString()
		);
	}

	/**
	 * Define the editor IFRAME as the control to be activated
	 * when the component's label is clicked
	 *
	 * @return string
	 */
	function getFocusId() {
		return "{$this->id}_ifr";
	}

	/**
	 * Enable/disable read-only mode
	 *
	 * @param bool $setting Enable/disable
	 */
	function setReadonly($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->editorParams['readonly'] = 1;
			$this->readOnly = TRUE;
		} else {
			$this->editorParams['readonly'] = 0;
			$this->readOnly = FALSE;
		}
	}

	/**
	 * Set editor's theme
	 *
	 * @param string $theme Theme name
	 */
	function setTheme($theme) {
		if (!empty($theme))
			$this->editorParams['theme'] = $theme;
	}

	/**
	 * Set editor's skin
	 *
	 * @param string $skin Skin
	 * @param string $variant Skin variant
	 */
	function setSkin($skin, $variant='') {
		if (!empty($skin)) {
			$this->editorParams['skin'] = $skin;
			if (!empty($variant))
				$this->editorParams['skin_variant'] = $variant;
		}
	}

	/**
	 * Defines what tinyMCE plugins will be loaded
	 *
	 * @param mixed $plugins Array or comma separated list of plugins
	 */
	function setPlugins($plugins) {
		if (is_array($plugins))
			$plugins = implode(',', $plugins);
		if (!empty($plugins))
			$this->editorParams['plugins'] = $plugins;
	}

	/**
	 * Set editor's width
	 *
	 * @param int $width Width, in pixels
	 */
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->editorParams['width'] = $width;
	}

	/**
	 * Set editor's height
	 *
	 * @param int $height Height, in pixels
	 */
	function setHeight($height) {
		if (TypeUtils::isInteger($height))
			$this->attributes['height'] = $height;
	}

	/**
	 * Set a CSS file to be used on the editor's content
	 *
	 * @param string $css URL of the CSS file
	 */
	function setContentCSS($css) {
		if (!empty($css))
			$this->editorParams['content_css'] = $css;
	}

	/**
	 * Processes attributes, child nodes and CDATA loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 * @param string $data Node CDATA
	 */
	function onLoadNode($attrs, $children, $data) {
		parent::onLoadNode($attrs, $children);
		// readonly
		$readOnly = (resolveBooleanChoice(@$attrs['READONLY']) || $this->_Form->readonly);
		if ($readOnly)
			$this->setReadonly();
		// theme
		$this->setTheme(@$attrs['THEME']);
		// skin and skin variant
		$this->setSkin(@$attrs['SKIN'], @$attrs['SKINVARIANT']);
		// plugins
		$this->setPlugins(@$attrs['PLUGINS']);
		// width
		$this->setWidth(@$attrs['WIDTH']);
		// height
		$this->setHeight(@$attrs['HEIGHT']);
		// content css
		$this->setContentCSS(@$attrs['CONTENTCSS']);
		// parameters written in js from the node's CDATA section
		$this->editorJsParams = $data;
	}

	/**
	 * Configure component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		$Form =& $this->getOwnerForm();
		// resolve variables and expressions on the content CSS URL
		if (preg_match("/~[^~]+~/", $this->editorParams['content_css'])) {
			$this->editorParams['content_css'] = $Form->resolveVariables($this->editorParams['content_css']);
		}
		// resolve variables in the tinyMCE JS params
		if (preg_match("/~[^~]+~/", $this->editorJsParams)) {
			$this->editorJsParams = $Form->resolveVariables($this->editorJsParams);
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/tinymcefield.js');
		if ($this->readOnly === NULL) {
			if ($this->_Form->readonly)
				$this->setReadonly();
			else
				$this->setReadonly(FALSE);
		}
	}

	/**
	 * Builds tinyMCE's parameters
	 *
	 * @access private
	 * @return string
	 */
	function _getParamsString() {
		$paramString = trim($this->editorJsParams);
		$paramArray =& $this->editorParams;
		if (empty($paramString)) {
			$paramArray['mode'] = 'exact';
			$paramArray['elements'] = $this->id;
			return JSONEncoder::encode($paramArray);
		} else {
			return sprintf("Object.extend(Object.extend(%s, %s), {mode: 'exact', elements: '%s'})", JSONEncoder::encode($paramArray), $paramString, $this->id);
		}
	}
}
?>