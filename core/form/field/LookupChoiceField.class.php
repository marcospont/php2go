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

import('php2go.form.field.LookupField');

/**
 * Select input with type-ahead filtering input
 *
 * The LookupChoiceField component extends LookupField by displaying it
 * along with a text input that performs type-ahead filtering on the
 * select options.
 *
 * @package form
 * @subpackage field
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class LookupChoiceField extends LookupField
{
	/**
	 * Builds the component's HTML code
	 */
	function display() {
		ob_start();
		parent::display();
		print sprintf("<input type=\"text\" id=\"%s_filter\" name=\"%s_filter\" value=\"%s\" maxlenght=\"60\"%s%s%s%s><br>%s<script type=\"text/javascript\">%s_instance = new LookupChoiceField('%s');</script>",
			$this->id, $this->name, PHP2Go::getLangVal('LOOKUP_CHOICE_FILTER_TIP'),
			$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED'],
			(empty($this->attributes['WIDTH']) ? " size=\"25\"" : $this->attributes['WIDTH']),
			ob_get_clean(), $this->id, $this->id
		);
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/lookupchoicefield.js');
		$this->isGrouping = FALSE;
		$this->disableFirstOption(TRUE);
	}
}
?>