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

import('php2go.form.field.FormField');

/**
 * Builds labels rendered inside SPAN tags
 *
 * TextField components are very useful when building forms
 * with the {@link FormBasic} class.
 *
 * @package form
 * @subpackage field
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class TextField extends FormField
{
	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return TextField
	 */
	function TextField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'SPAN';
		$this->searchable = FALSE;
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered) && (parent::onPreRender());
		print sprintf("<span id=\"%s\" title=\"%s\"%s>%s</span>",
			$this->id, $this->label, $this->attributes['STYLE'], $this->value
		);
	}
}
?>