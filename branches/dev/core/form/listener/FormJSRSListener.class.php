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

/**
 * Builds JSRS-based form event listeners
 *
 * This class bounds an event of a form field with the jsrsExecute
 * function, which performs a JSRS request to a remote page when
 * the event is triggered.
 *
 * Example:
 * <code>
 * <combofield name="search_options" label="Search by">
 *   <option value="1" caption="Code"/>
 *   <option value="2" caption="Name"/>
 * </combofield>
 * <editfield name="search_term" label="empty"/>
 * <button name="search" type="BUTTON">
 *   <listener
 *       type="JSRS" event="onClick" file="jsrs/search.php"
 *       remote="getOptions" callback="parseOptions" debug="F"
 *       params="Array($('search_options').value, $('search_term').value)"
 *   />
 * </button>
 * </code>
 *
 * @package form
 * @subpackage listener
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FormJSRSListener extends FormEventListener
{
	/**
	 * JSRS request URL
	 *
	 * @var string
	 * @access private
	 */
	var $remoteFile;

	/**
	 * JSRS remote function
	 *
	 * @var string
	 * @access private
	 */
	var $remoteFunction;

	/**
	 * JSRS client callback
	 *
	 * @var string
	 * @access private
	 */
	var $callback;

	/**
	 * JSRS request arguments
	 *
	 * @var string
	 * @access private
	 */
	var $params;

	/**
	 * Debug flag
	 *
	 * @var bool
	 * @access private
	 */
	var $debug;

	/**
	 * Class constructor
	 *
	 * @param string $eventName Event name
	 * @param string $autoDispatchIf Evaluates if listener should be dispatched automatically upon page load
	 * @param string $remoteFile JSRS request URL
	 * @param string $remoteFunction JSRS remote function
	 * @param string $callback JSRS client callback
	 * @param string $params JSRS request arguments
	 * @param bool $debug Enable/disable debug mode
	 * @return FormJSRSListener
	 */
	function FormJSRSListener($eventName, $autoDispatchIf='', $remoteFile='', $remoteFunction='', $callback='', $params='', $debug=FALSE) {
		parent::FormEventListener(FORM_EVENT_JSRS, $eventName, '', $autoDispatchIf);
		$this->remoteFile = (!empty($remoteFile) ? $remoteFile : HttpRequest::uri());
		$this->remoteFunction = $remoteFunction;
		$this->callback = $callback;
		$this->params = $params;
		$this->debug = TypeUtils::toBoolean($debug);
	}

	/**
	 * Based on the provided arguments, builds a call to the
	 * jsrsExecute function, which performs the JSRS remote call,
	 * and calls the response callback as soon as the response
	 * is available
	 *
	 * @param int $targetIndex Index, when bound to a group option invididually
	 * @return string Function call
	 */
	function getScriptCode($targetIndex=NULL) {
		$Form =& $this->_Owner->getOwnerForm();
		$Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'jsrsclient.js');
		$this->action = sprintf("jsrsExecute('%s', %s, '%s', %s%s);window.status=''",
			$this->remoteFile, $this->callback, $this->remoteFunction,
			(empty($this->params) ? 'null' : $this->params),
			($this->debug ? ', true' : '')
		);
		parent::renderAutoDispatch($targetIndex);
		return $this->action;
	}

	/**
	 * Validates the listener's properties
	 *
	 * @access protected
	 * @return bool
	 */
	function validate() {
		return (!empty($this->eventName) && !empty($this->remoteFile) && !empty($this->remoteFunction) && !empty($this->callback));
	}

	/**
	 * Builds a string representation of the JSRS listener
	 *
	 * @return string
	 */
	function __toString() {
		$info = $this->_Owner->getName();
		if (isset($this->_ownerIndex))
			$info .= " [option {$this->_ownerIndex}]";
		$info .= " - [{$this->type}";
		if (!empty($this->eventName))
			$info .= "; {$this->eventName}";
		if (!empty($this->remoteFile))
			$info .= "; {$this->remoteFile}";
		if (!empty($this->remoteFunction))
			$info .= "; {$this->remoteFunction}";
		if (!empty($this->callback))
			$info .= "; {$this->callback}";
		if (!empty($this->params))
			$info .= "; {$this->params}";
		$info .= ']';
		return $info;
	}
}
?>