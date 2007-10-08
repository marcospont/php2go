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

import('php2go.form.field.EditableField');

/**
 * Builds password inputs
 *
 * @package form
 * @subpackage field
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class PasswdField extends EditableField
{
	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return PasswdField
	 */
	function PasswdField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'PASSWORD';
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && parent::onPreRender());
		print sprintf("<input type=\"password\" id=\"%s\" name=\"%s\" value=\"%s\" maxlength=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s />",
			$this->id, $this->name, $this->value, $this->attributes['LENGTH'], $this->attributes['SIZE'],
			$this->label, $this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
			$this->attributes['STYLE'],  $this->attributes['READONLY'], $this->attributes['DISABLED'], $this->attributes['DATASRC'],
			$this->attributes['DATAFLD'], $this->attributes['AUTOCOMPLETE']
		);
	}

	/**
	 * Configures component's dynamic properties
	 */
	function onDataBind() {
		parent::onDataBind();
		if (is_array($this->value))
			$this->value = '';
	}
}
?>