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

import('php2go.db.QueryBuilder');
import('php2go.form.field.EditableField');
import('php2go.util.json.JSONEncoder');

/**
 * Text field with autocomplete support
 *
 * Autocomplete searches are performed as the chars are entered
 * in the text field. Autocomplete choices can be filtered from a
 * local JS array or through an AJAX call.
 *
 * @package form
 * @subpackage field
 * @uses Db
 * @uses HttpRequest
 * @uses JSONEncoder
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class AutoCompleteField extends EditableField
{
	/**
	 * Configuration options
	 *
	 * @var array
	 * @access private
	 */
	var $options = array();

	/**
	 * Autocomplete choices
	 *
	 * Holds choices declared explictly in the XML specification.
	 *
	 * @var array
	 * @access private
	 */
	var $choices = array();
	var $dataSource = array();

	/**
	 * Component's constructor
	 *
	 * @param Form &$Form Parent form
	 * @param bool $child Whether the component is child of another component
	 * @return AutoCompleteField
	 */
	function AutoCompleteField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'TEXT';
		$this->options = array(
			'maxChoices' => 10,
			'separator' => ',',
			'style' => array(
				'normal' => 'autoCompleteNormal',
				'selected' => 'autoCompleteSelected',
				'hover' => 'autoCompleteHover'
			),
			'url' => HttpRequest::uri(FALSE)
		);
	}

	/**
	 * Builds the component's HTML code
	 */
	function display() {
		if ($this->options['ajax'] && !$this->options['throbber']) {
			$throbber = sprintf("\n<span id=\"%s_throbber\" style=\"display:none\"><img src=\"%sindicator.gif\" border=\"0\" align=\"top\" alt=\"\" /></span>", $this->id, PHP2GO_ICON_PATH);
			$this->options['throbber'] = "{$this->id}_throbber";
		} else {
			$throbber = '';
		}
		if ($this->options['incremental']) {
			print sprintf(
"\n<textarea id=\"%s\" name=\"%s\" cols=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s>%s</textarea>%s" .
"\n<div id=\"%s_choices\" style=\"position:absolute;display:none\" class=\"autoCompleteChoices\"></div>" .
"\n<script type=\"text/javascript\">\n\tnew AutoCompleteField('%s', %s);\n</script>",
				$this->id, $this->name, $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'],
				$this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],  $this->attributes['STYLE'],
				(!empty($this->attributes['HEIGHT']) ? " style=\"height:{$this->attributes['HEIGHT']}px;\"" : ""),
				$this->attributes['READONLY'], $this->attributes['DISABLED'], $this->attributes['DATASRC'],
				$this->attributes['DATAFLD'], $this->value, $throbber, $this->id, $this->id, JSONEncoder::encode($this->options)
			);
		} else {
			print sprintf(
"\n<input id=\"%s\" name=\"%s\" type=\"text\" value=\"%s\" maxlength=\"%s\" size=\"%s\" title=\"%s\"%s%s%s%s%s%s%s%s%s />%s" .
"\n<div id=\"%s_choices\" style=\"position:absolute;display:none\" class=\"autoCompleteChoices\"></div>" .
"\n<script type=\"text/javascript\">\n\tnew AutoCompleteField('%s', %s);\n</script>",
				$this->id, $this->name, $this->value, $this->attributes['LENGTH'], $this->attributes['SIZE'],
				$this->label, $this->attributes['SCRIPT'], $this->attributes['ACCESSKEY'], $this->attributes['TABINDEX'],
				$this->attributes['STYLE'], $this->attributes['READONLY'], $this->attributes['DISABLED'],
				$this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->attributes['AUTOCOMPLETE'],
				$throbber, $this->id, $this->id, JSONEncoder::encode($this->options)
			);
		}
	}

	/**
	 * Set the source of the autocomplete choices
	 *
	 * # LOCAL: all choices are serialized in a JS array. They
	 *   can be declared through choice nodes or a datasource
	 * # AJAX: choices are loaded on demand, through AJAX calls,
	 *   and using the datasource declared in the XML specification
	 *
	 * @param string $src Source
	 * @param array $options Configuration options
	 */
	function setSource($src, $options=array()) {
		if ($src == 'LOCAL') {
			$this->options['ajax'] = FALSE;
		} elseif ($src == 'AJAX') {
			$this->options['ajax'] = TRUE;
			if (isset($options['options']))
				$this->options['ajaxOptions'] = (string)$options;
			if (isset($options['throbber']))
				$this->options['throbber'] = $options['throbber'];
			if (isset($options['searchField']))
				$this->attributes['SEARCHFIELD'] = $options['searchField'];
		}
	}

	/**
	 * Set the search delay
	 *
	 * If the interval between 2 typed keys exceeds the delay, the
	 * autocomplete search will be performed (locally or remotely).
	 *
	 * @param float $delay Delay
	 */
	function setDelay($delay) {
		$delay = (float)$delay;
		if ($delay > 0)
		$this->options['delay'] = $delay;
	}

	/**
	 * Enable/disable multiple selection
	 *
	 * @param bool $setting Enable/disable
	 * @param string $separator Separator for multiple values
	 */
	function setMultiple($setting, $separator) {
		$this->options['incremental'] = (bool)$setting;
		if ($this->options['incremental'] && $separator)
			$this->options['separator'] = $separator;
	}

	/**
	 * Set height for results container and input field
	 * 
	 * The field height is only applicable when multiple selection is enabled.
	 *
	 * @param int $resultsHeight Results height
	 * @param int $fieldHeight Field height
	 */
	function setHeight($resultsHeight, $fieldHeight=NULL) {
		$resultsHeight = intval($resultsHeight);
		if ($resultsHeight)
			$this->options['height'] = $resultsHeight;
		$fieldHeight = intval($fieldHeight);
		if ($fieldHeight)
			$this->attributes['HEIGHT'] = $fieldHeight;
	}

	/**
	 * Set maximum choices that can be returned
	 *
	 * @param int $max Max choices
	 */
	function setMaxChoices($max) {
		$max = intval($max);
		if ($max > 0)
			$this->options['maxChoices'] = $max;
	}

	/**
	 * Set miminum chars that must be typed until a search is performed
	 *
	 * @param int $min Min chars
	 */
	function setMinChars($min) {
		$min = intval($min);
		if ($min > 0)
			$this->options['minChars'] = $min;
	}

	/**
	 * Enable/disable case insensitivity in the autocomplete search
	 *
	 * @param bool $setting Enable/disable
	 */
	function setIgnoreCase($setting) {
		$this->options['ignoreCase'] = (bool)$setting;
	}

	/**
	 * Enable/disable full search
	 *
	 * By default, the class searches only in the beginning of the
	 * choices. By enabling full search, the search engine will
	 * match the search term in any part of the choices.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setFullSearch($setting) {
		$this->options['fullSearch'] = (bool)$setting;
	}

	/**
	 * Enable/disable auto-select when the search returns only one choice
	 *
	 * @param bool $setting Enable/disable
	 */
	function setAutoSelect($setting) {
		$this->options['autoSelect'] = (bool)$setting;
	}

	/**
	 * Define the node that contains the choice value, when
	 * choices contain HTML code
	 *
	 *
	 * If your choices are returned in the following format:
	 * <code>
	 * <li><div>John Smith</div><div>john.smith@company.org</div></li>
	 * </code>
	 *
	 * The value we want to catch is in the first div node. So, the
	 * choice value node would be "div".
	 *
	 * @param string $node Node name
	 */
	function setChoiceValueNode($node) {
		if (!empty($node))
			$this->options['choiceValueNode'] = trim($node);
	}

	/**
	 * Set a CSS property of the autocomplete choices
	 *
	 * @param string $style CSS class
	 * @param string $type CSS property: normal, selected or hover
	 */
	function setItemStyle($style, $type) {
		if (!empty($style))
			$this->options['style'][$type] = $style;
	}

	/**
	 * Processes attributes and child nodes loaded from the XML specification
	 *
	 * @uses FormField::parseDataSource()
	 * @param array $attrs Node attributes
	 * @param array $children Node children
	 */
	function onLoadNode($attrs, $children) {
		// when multiple choice is enabled, masks aren't supported
		$multiple = resolveBooleanChoice(@$attrs['MULTIPLE']);
		if ($multiple)
			unset($attrs['MASK']);
		parent::onLoadNode($attrs, $children);
		// datasource
		$this->dataSource = parent::parseDataSource($children['DATASOURCE']);
		// search source
		$source = TypeUtils::ifNull(@$attrs['SOURCE'], 'LOCAL');
		if ($source == 'AJAX') {
			$opts = array();
			$opts['searchField'] = @$attrs['AJAXFIELD'];
			$opts['options'] = @$attrs['AJAXOPTIONS'];
			$opts['throbber'] = @$attrs['AJAXTHROBBER'];
			$this->setSource($source, $opts);
			$this->_Form->postbackFields[] = $this->name;
		} elseif ($source == 'LOCAL') {
			$this->setSource($source);
			$this->options['choices'] = array();
			if (is_array($children['CHOICE'])) {
				foreach ($children['CHOICE'] as $Choice) {
					if (!empty($Choice->value))
						$this->options['choices'][] = trim($Choice->value);
				}
			}
		}
		// delay
		if (isset($attrs['DELAY']))
			$this->setDelay($attrs['DELAY']);
		// multiple choices
		$this->setMultiple($multiple, @$attrs['SEPARATOR']);
		// results and field height
		$this->setHeight(@$attrs['RESULTSHEIGHT'], @$attrs['HEIGHT']);
		// max choices
		$this->setMaxChoices(@$attrs['MAXCHOICES']);
		// minimum chars for a search token
		$this->setMinChars(@$attrs['MINCHARS']);
		// ignore case when searching
		if (array_key_exists('IGNORECASE', $attrs))
			$this->setIgnoreCase(resolveBooleanChoice($attrs['IGNORECASE']));
		// full search
		$this->setFullSearch(resolveBooleanChoice(@$attrs['FULLSEARCH']));
		// auto-select when only 1 result is returned
		$this->setAutoSelect(resolveBooleanChoice(@$attrs['AUTOSELECT']));
		// node to read option's value from, when options contain HTML code
		$this->setChoiceValueNode(@$attrs['CHOICEVALUENODE']);
		// CSS styles
		$this->setItemStyle(@$attrs['NORMALSTYLE'], 'normal');
		$this->setItemStyle(@$attrs['SELECTEDSTYLE'], 'selected');
		$this->setItemStyle(@$attrs['HOVERSTYLE'], 'hover');
	}

	/**
	 * Configures component's dynamic properties
	 *
	 * This method is also used to respond to autocomplete
	 * queries via AJAX.
	 *
	 * @uses Form::resolveVariables()
	 * @access protected
	 */
	function onDataBind() {
		parent::onDataBind();
		// variables inside datasource, choices loaded from datasource
		if (!empty($this->dataSource)) {
			foreach ((array)$this->dataSource as $name => $value) {
				if (preg_match("/~[^~]+~/", $value))
					$this->dataSource[$name] = $this->_Form->resolveVariables($value);
			}
			if (!$this->_Form->isPosted())
				$this->_getChoices();
		}
		// search and print choices, responding to an AJAX request
		if (HttpRequest::isAjax()) {
			$token = HttpRequest::post($this->name);
			if ($token !== NULL) {
				$this->_printChoices($token);
				exit;
			}
		}
		// handle multiple choices
		if ($this->options['incremental']) {
			$this->searchDefaults['OPERATOR'] = 'IN';
			if ($this->_Form->isPosted()) {
				$tmp = $this->value;
				$val = array();
				if (!is_array($tmp) && $this->options['incremental']) {
					$tmp = explode($this->options['separator'], trim($tmp));
					foreach ($tmp as $item) {
						$item = trim($item);
						if (!empty($item))
							$val[] = $item;
					}
					parent::setSubmittedValue($val);
				}
			} elseif (is_array($this->value)) {
				$this->value = (string)$this->value;
			}
		}
	}

	/**
	 * Prepares the component to be rendered
	 */
	function onPreRender() {
		parent::onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'form/autocompletefield.js');
		$this->_Form->Document->addStyle(PHP2GO_CSS_PATH . 'autocompletefield.css');
	}

	/**
	 * Build the list of choices using the component's datasource
	 *
	 * @access private
	 */
	function _getChoices() {
		if (empty($this->choices)) {
			$Db =& Db::getInstance($this->dataSource['CONNECTION']);
			$old = $Db->setFetchMode(ADODB_FETCH_NUM);
			if (isset($this->dataSource['PROCEDURE'])) {
				$Rs =& $Db->execute(
					$Db->getProcedureSql($this->dataSource['PROCEDURE']),
					FALSE, @$this->dataSource['CURSORNAME']
				);
			} else {
				$Query = new QueryBuilder(
					$this->dataSource['KEYFIELD'] . ',' . $this->dataSource['DISPLAYFIELD'],
					$this->dataSource['LOOKUPTABLE'], $this->dataSource['CLAUSE'],
					$this->dataSource['GROUPBY'], $this->dataSource['ORDERBY']
				);
				if (isset($this->dataSource['LIMIT']) && preg_match("/([0-9]+)(,[0-9]+)?/", trim($this->dataSource['LIMIT']), $matches))
					$Rs =& $Db->limitQuery($Query->getQuery(), intval($matches[1]), intval($matches[2]));
				else
					$Rs =& $Db->query($Query->getQuery());
			}
			$Db->setFetchMode($old);
			$this->options['choices'] = array();
			if ($Rs) {
				while (!$Rs->EOF) {
					$this->options['choices'][] = $Rs->fields[1];
					$Rs->moveNext();
				}
			}
		}
	}

	/**
	 * Respond to an AJAX search call
	 *
	 * Execute the search query and return the results in HTML (li nodes).
	 *
	 * @param string $token Search token
	 * @access private
	 */
	function _printChoices($token) {
		$token = utf8_decode($token);
		$ign = HttpRequest::post('ignorecase');
		$full = HttpRequest::post('fullsearch');
		$fld = TypeUtils::ifNull(@$this->attributes['SEARCHFIELD'], $this->dataSource['DISPLAYFIELD']);
		$clause = $fld . " LIKE '" . ($full?'%':'') . ($ign?strtolower($token):$token) . "%'";
		$this->dataSource['CLAUSE'] = (empty($this->dataSource['CLAUSE']) ? $clause : $this->dataSource['CLAUSE'] . " AND {$clause}");
		$this->_getChoices();
		if (!empty($this->options['choices'])) {
			$cnt = 0;
			$output = "<ul>";
			foreach ($this->options['choices'] as $choice) {
				$cnt++;				
				$pos = strpos(($ign?strtolower($choice):$choice), ($ign?strtolower($token):$token));
				if ($pos == 0)
					$output .= "<li><b><u>" . substr($choice, 0, strlen($token)) . "</u></b>" . substr($choice, strlen($token)) . "</li>";
				else
					$output .= "<li>" . substr($choice, 0, $pos) . "<b><u>" . substr($choice, $pos, strlen($token)) . "</u></b>" . substr($choice, $pos+strlen($token)) . "</li>";
				if ($cnt == $this->options['maxChoices'])
					break;
			}
			$output .= "</ul>";
			header(sprintf("Content-type: text/html; charset=%s", PHP2Go::getConfigVal('CHARSET')));
			print $output;
		}
	}
}
?>