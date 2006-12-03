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

import('php2go.datetime.Date');
import('php2go.form.field.FormField');
import('php2go.util.json.JSONEncoder');

/**
 * Date selection tool
 *
 * Implements a date selection tool based on the bundled library
 * JSCalendar. Accepts single or multiple selection.
 *
 * @package form
 * @subpackage field
 * @uses Date
 * @uses JSONEncoder
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class DatePickerField extends FormField
{
	/**
	 * Configuration options
	 *
	 * @var array
	 * @access private
	 */
	var $options = array();

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return DatePickerField
	 */
	function DatePickerField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->searchDefaults['DATATYPE'] = 'DATE';
		$this->options = array(
			'cache' => TRUE,
			'selectDefault' => FALSE,
			'firstDay' => 0,
			'showOthers' => FALSE,
			'weekNumbers' => FALSE,
			'electric' => TRUE,
			'ifFormat' => (PHP2Go::getConfigVal('LOCAL_DATE_TYPE') == 'EURO' ? "%d/%m/%Y" : "%Y/%m/%d"),
			'dateSep' => '#',
			'range' => array(),
			'statusFunc' => NULL
		);
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		(!$this->preRendered && $this->onPreRender());
		print sprintf(
			"<input id=\"%s\" name=\"%s\" type=\"hidden\" value=\"%s\" title=\"%s\"%s%s/><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td id=\"%s_calendar\"%s></td></tr></table>" .
			"<script type=\"text/javascript\">new DatePickerField(\"%s\", %s);</script>",
			$this->id, $this->name, $this->value, $this->label, $this->attributes['SCRIPT'],
			$this->attributes['DISABLED'], $this->id, $this->attributes['STYLE'],
			$this->id, JSONEncoder::encode($this->options)
		);
	}

	/**
	 * Enable/disable multiple selection
	 *
	 * @param bool $setting Enable/disable
	 */
	function setMultiple($setting) {
		$this->attributes['MULTIPLE'] = (bool)$setting;
		$this->searchDefaults['OPERATOR'] = ($this->attributes['MULTIPLE'] ? 'IN' : 'EQ');
	}

	/**
	 * Set a Javascript function to define enable state
	 * for each calendar date
	 *
	 * @param string $funcName Function name
	 */
	function setDateStatusFunc($funcName) {
		if ($funcName)
			$this->options['statusFunc'] = $funcName;
	}

	/**
	 * Enable/disable time selection
	 *
	 * Defaults to FALSE.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setShowTime($setting) {
		$setting = (bool)$setting;
		$dateType = PHP2Go::getConfigVal('LOCAL_DATE_TYPE');
		if ($setting) {
			$this->searchDefaults['DATATYPE'] = 'DATETIME';
			$this->options['showsTime'] = TRUE;
			$this->options['ifFormat'] = ($dateType == 'EURO' ? "%d/%m/%Y %H:%M:%S" : "%Y/%m/%d %H:%M:%S");
		} else {
			$this->searchDefaults['DATATYPE'] = 'DATE';
			$this->options['showsTime'] = FALSE;
			$this->options['ifFormat'] = ($dateType == 'EURO' ? "%d/%m/%Y" : "%Y/%m/%d");
		}
	}

	/**
	 * Set first day of the week
	 *
	 * Defaults to 0 (Sunday).
	 *
	 * @param int $day Day number (0-sunday, 1-monday, ...)
	 */
	function setFirstWeekDay($day) {
		$day = intval($day);
		if ($day >= 0 && $day <= 6)
			$this->options['firstDay'] = $day;
	}

	/**
	 * Enable/disable display of days from other months
	 *
	 * Defaults to FALSE.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setShowOthers($setting) {
		$this->options['showOthers'] = (bool)$setting;
	}

	/**
	 * Enable/disable display of week numbers
	 *
	 * Defaults to FALSE.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setShowWeekNumbers($setting) {
		$this->options['weekNumbers'] = (bool)$setting;
	}

	/**
	 * Set calendar year range
	 *
	 * @param int $start Minimum year
	 * @param int $end Maximum year
	 */
	function setYearRange($start, $end) {
		if ($start && $end)
			$this->options['range'] = array($start, $end);
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// multiple selection
		$this->setMultiple(resolveBooleanChoice(@$attrs['MULTIPLE']));
		// date status function
		$this->setDateStatusFunc(@$attrs['DATESTATUSFUNC']);
		// time selection
		$this->setShowTime(resolveBooleanChoice(@$attrs['TIME']));
		// first week day
		$this->setFirstWeekDay(@$attrs['FIRSTWEEKDAY']);
		// show days from other months and years
		$this->setShowOthers(resolveBooleanChoice(@$attrs['SHOWOTHERS']));
		// show week numbers
		$this->setShowWeekNumbers(resolveBooleanChoice(@$attrs['SHOWWEEKNUMBERS']));
		// year range
		$matches = array();
		$range = @$attrs['YEARRANGE'];
		if ($range && preg_match('/^([0-9]{4})\s*,\s*([0-9]{4})$/', $range, $matches))
			$this->setYearRange($matches[1], $matches[2]);
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		$regs = array();
		if (!$this->attributes['MULTIPLE'] && !empty($this->value) && !Date::isEuroDate($this->value, $regs) && !Date::isUsDate($this->value, $regs))
			parent::setValue(Date::parseFieldExpression($this->value));
		if ($this->attributes['MULTIPLE'] && is_array($this->value))
			$this->value = (!empty($this->value) ? join($this->options['dateSep'], $this->value) : "");
		if ($this->_Form->isPosted())
			parent::setSubmittedValue(!empty($this->value) ? explode($this->options['dateSep'], $this->value) : array());
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->importStyle(PHP2GO_JAVASCRIPT_PATH . "vendor/jscalendar/calendar-system.css");
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "form/datepickerfield.js");
		if ($this->attributes['MULTIPLE']) {
			$multiple = array();
			$list = (!empty($this->value) ? explode($this->options['dateSep'], $this->value) : array());
			foreach ($list as $date) {
				if ($this->options['showsTime'])
					$multiple[] = date("F d, Y H:i:s", Date::dateToTime($date));
				else
					$multiple[] = date("F d, Y", Date::dateToTime($date));
			}
			$this->options['multiple'] = $list;//$multiple;
		} else {
			if (!empty($this->value)) {
				if ($this->options['showsTime'])
					$date = date("F d, Y H:i:s", Date::dateToTime($this->value));
				else
					$date = date("F d, Y", Date::dateToTime($this->value));
			} else {
				$date = NULL;
			}
			$this->options['date'] = $date;
		}
	}
}
?>