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

import('php2go.form.field.DbGroupField');

/**
 * Builds a group of radio buttons
 *
 * The group options are loaded from a data source.
 *
 * @package form
 * @subpackage field
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DbRadioField extends DbGroupField
{
	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return DbRadioField
	 */
	function DbRadioField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->htmlType = 'RADIO';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}

	/**
	 * Builds the HTML code of the radio buttons
	 *
	 * @return array
	 * @access protected
	 */
	function renderGroup() {
		$group = array();
		while (list($value, $caption) = $this->_Rs->fetchRow()) {
			$index = $this->_Rs->absolutePosition() - 1;
			$optionSelected = ($value == $this->value ? ' checked' : '');
			$input = sprintf("<input type=\"radio\" id=\"%s\" name=\"%s\" value=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s>",
				"{$this->id}_{$index}", $this->name, $value, $this->label, $this->attributes['ACCESSKEY'],
				$this->attributes['TABINDEX'], $this->attributes['SCRIPT'], $this->attributes['STYLE'],
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'],
				$optionSelected
			);
			$group[] = array(
				'input' => $input,
				'id' => $this->id,
				'caption' => $caption
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