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
 * @version $Id: CollapsiblePanel.class.php 222 2007-04-21 15:23:14Z mpont $
 */

/**
 * Interleaved 2 of 5 Bar Code Widget
 *
 * This widget renders an HTML based bar code, using the
 * Interleaved 2 of 5 pattern.
 *
 * Interleaved 2 of 5 is a higher-density numeric symbology based
 * upon the Standard 2 of 5 symbology. It is used primarily in the
 * distribution and warehouse industry. Interleaved 2 of 5 encodes
 * any even number of numeric characters in the widths (either narrow
 * or wide) of the bars and spaces of the bar code. Unlike Standard 2
 * of 5, which only encodes information in the width of the bars,
 * Interleaved 2 of 5 encodes data in the width of both the bars and
 * spaces.
 *
 * Available attributes:
 * # id : widget ID
 * # barHeight : height for the bars
 * # barWidth : width for the short bar (long bar width is calculated based on it)
 * # code : code to be represented
 *
 * @package gui
 * @subpackage widget
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision: 222 $
 */
class I25BarCode extends Widget
{
	/**
	 * Bar images
	 *
	 * @var array
	 * @access private
	 */
	var $bars = array();

	/**
	 * Width for short and long bars
	 *
	 * @var array
	 * @access private
	 */
	var $barWidth = array();

	/**
	 * Widget constructor
	 *
	 * @param array $attrs Attributes
	 * @return I25BarCode
	 */
	function I25BarCode($attrs) {
		parent::Widget($attrs);
		$this->mandatoryAttributes[] = 'code';
		$this->bars = array(
			'black' => PHP2GO_ICON_PATH . 'bb.gif',
			'white' => PHP2GO_ICON_PATH . 'wb.gif'
		);
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
			'barWidth' => 1,
			'barHeight' => 52
		);
	}

	/**
	 * Applies the necessary transformation on
	 * attributes before loading them
	 *
	 * @param array $attrs Attributes
	 */
	function loadAttributes($attrs) {
		parent::loadAttributes($attrs);
		$this->attributes['code'] = preg_replace("/[^0-9]+/", "", strval($this->attributes['code']));
		$this->barWidth = array(
			'short' => $this->attributes['barWidth'],
			'long' => ($this->attributes['barWidth']*2) + 1
		);
	}

	/**
	 * Renders the I25BarCode widget
	 */
	function render() {
		$attrs =& $this->attributes;
		$encoding = array('00110', '10001', '01001', '11000', '00101', '10100', '01100', '00011', '10010', '01010');
		$html = '';
		$html .= $this->_drawBar('black', 'short');
		$html .= $this->_drawBar('white', 'short');
		$html .= $this->_drawBar('black', 'short');
		$html .= $this->_drawBar('white', 'short');
		for ($i=0; $i<strlen($attrs['code']); $i+=2) {
			$char1 = $attrs['code'][$i];
			$char2 = $attrs['code'][$i+1];
			for ($j=0; $j<5; $j++) {
				$code1 = $encoding[$char1][$j];
				$code2 = $encoding[$char2][$j];
				$html .= $this->_drawBar('black', ($code1 ? 'long' : 'short'));
				$html .= $this->_drawBar('white', ($code2 ? 'long' : 'short'));
			}
		}
		$html .= $this->_drawBar('black', 'long');
		$html .= $this->_drawBar('white', 'short');
		$html .= $this->_drawBar('black', 'short');
		$html .= $this->_drawBar('white', 'long');
		print $html;
	}

	/**
	 * Draws a bar, given its color and type
	 *
	 * @param string $color Bar color
	 * @param string $type Bar type (short or long)
	 * @access private
	 * @return string
	 */
	function _drawBar($color, $type) {
		return sprintf("<img src=\"%s\" width=\"%d\" height=\"%d\">", $this->bars[$color], $this->barWidth[$type], $this->attributes['barHeight']);
	}
}
?>