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

import('php2go.form.field.GroupField');

/**
 * Builds a group of radio buttons
 *
 * Group options are defined statically inside the XML
 * specification through option nodes.
 *
 * @package form
 * @subpackage field
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class RadioField extends GroupField
{
	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return RadioField
	 */
	function RadioField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'RADIO';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	/**
	 * Builds the HTML code of the radio buttons
	 *
	 * @access protected
	 * @return array
	 */
	function renderGroup() {
		$group = array();
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			if ($this->optionAttributes[$i]['VALUE'] == parent::getValue())
				$this->optionAttributes[$i]['SELECTED'] = " checked";
			else
				$this->optionAttributes[$i]['SELECTED'] = "";
			$accessKey = TypeUtils::ifNull($this->optionAttributes[$i]['ACCESSKEY'], $this->accessKey);
			$input = sprintf("<input type=\"radio\" id=\"%s\" name=\"%s\" title=\"%s\" value=\"%s\"%s%s%s%s%s%s%s%s>",
				$this->id . "_$i", $this->name, $this->label, $this->optionAttributes[$i]['VALUE'],
				($accessKey ? " accesskey=\"{$accessKey}\"" : ''), $this->attributes['TABINDEX'],
				$this->optionAttributes[$i]['SCRIPT'], $this->attributes['STYLE'],
				$this->optionAttributes[$i]['DISABLED'], $this->attributes['DATASRC'],
				$this->attributes['DATAFLD'], $this->optionAttributes[$i]['SELECTED']
			);
			$caption = $this->optionAttributes[$i]['CAPTION'];
			if ($accessKey && $this->_Form->accessKeyHighlight) {
				$pos = strpos(strtoupper($caption), strtoupper($accessKey));
				if ($pos !== FALSE)
					$caption = substr($caption, 0, $pos) . '<u>' . $caption[$pos] . '</u>' . substr($caption, $pos+1);
			}
			$group[] = array(
				'input' => $input,
				'id' => $this->id,
				'caption' => $caption,
				'alt' => (!empty($this->optionAttributes[$i]['ALT']) ? " title=\"{$this->optionAttributes[$i]['ALT']}\"" : '')
			);
		}
		return array(
			'prepend' => '',
			'append' => '',
			'group' => $group
		);
	}
}
?>