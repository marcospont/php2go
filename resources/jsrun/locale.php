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

ob_start();
require_once('../../p2gConfig.php');
import('php2go.text.StringUtils');
$errorMsg = ob_get_clean();

/**
 * This script reads from the main language table (domain PHP2GO) all the
 * entries that are necessary inside PHP2Go Javascript libraries. The result
 * array is then converted into a JS object and printed out. This file
 * is included in every HTML page prduced by php2go.base.Document
 */

if (!empty($errorMsg)) {
	header("Content-type: text/javascript");
	die("var Locale = {}; var Lang = {};");
}

$Conf =& Conf::getInstance();
$LangBase =& LanguageBase::getInstance();

// locale settings
$locale = array();
$settings = $Conf->getConfig('DATE_FORMAT_SETTINGS');
$locale['date'] = array();
$locale['date']['type'] = $settings['type'];
$locale['date']['regexp'] = new stdClass();
$locale['date']['regexp']->__json__ = 'new RegExp("' . $settings['regexp'] . '")';
$locale['date']['matches'] = $settings['matches'];
$locale['date']['mask'] = $settings['mask'];

// language entries
$lang = array();
$base =& $LangBase->languageBase['PHP2GO'];
$lang['invalidValue'] = $base['ERR_INVALID_VALUE'];
$lang['duplicatedValue'] = $base['ERR_DUPLICATED_VALUE'];
$lang['commFailure'] = $base['ERR_COMM_FAILURE'];
$lang['ajaxSupport'] = $base['ERR_AJAX_SUPPORT'];
$lang['editor'] = array(
	'validateMode' => $base['EDITOR_VARS']['validateMode'],
	'createLink' => $base['EDITOR_VARS']['createLink'],
	'insertImage' => $base['EDITOR_VARS']['insertImage']
);
$lang['colorPicker'] = $base['COLOR_PICKER_VARS'];
$lang['dataBind'] = $base['FORM_DATA_BIND_MESSAGES'];
$lang['report'] = array(
	'invalidPage' => $base['REPORT_INVALID_PAGE'],
	'emptyFilters' => $base['REPORT_SEARCH_VALUES']['emptyFilters'],
	'closeFilters' => $base['REPORT_SEARCH_VALUES']['closeFilters'],
	'removeFilter' => $base['REPORT_SEARCH_VALUES']['removeFilter'],
	'addedFilter' => $base['REPORT_SEARCH_VALUES']['addedFilter'],
	'numberOperators' => $base['REPORT_NUMBER_OPERATORS'],
	'stringOperators' => $base['REPORT_STRING_OPERATORS'],
	'resendConfirmation' => $base['REPORT_SEARCH_VALUES']['resendConfirmation']
);
$lang['search'] = array(
	'emptySearch' => $base['EMPTY_SEARCH'],
	'emptyResults' => $base['EMPTY_RESULTS'],
	'searchingBtnValue' => $base['EDITSEARCH_BTN_VALUE']
);
$lang['inputMask'] = $base['FORM_INPUT_MASK_MESSAGES'];
$lang['masks'] = $base['FORM_MASKS'];
$lang['validator'] = array(
	'invalidFloat' => $base['ERR_FORM_FIELD_INVALID_FLOAT'],
	'requiredFields' => $base['ERR_FORM_REQUIRED_SUMMARY'],
	'invalidFields' => $base['ERR_FORM_ERRORS_SUMMARY'],
	'completeFields' => $base['ERR_FORM_COMPLETE_FIELDS'],
	'fixFields' => $base['ERR_FORM_FIX_FIELDS'],
	'requiredField' => $base['ERR_FORM_FIELD_REQUIRED'],
	'invalidField' => $base['ERR_FORM_FIELD_INVALID'],
	'invalidDataType' => $base['ERR_FORM_FIELD_INVALID_DATATYPE'],
	'minLengthField' => $base['ERR_FORM_FIELD_MIN_LENGTH'],
	'maxLengthField' => $base['ERR_FORM_FIELD_MAX_LENGTH'],
	'eqField' => $base['ERR_FORM_FIELD_EQ'],
	'neqField' => $base['ERR_FORM_FIELD_NEQ'],
	'gtField' => $base['ERR_FORM_FIELD_GT'],
	'ltField' => $base['ERR_FORM_FIELD_LT'],
	'goetField' => $base['ERR_FORM_FIELD_GOET'],
	'loetField' => $base['ERR_FORM_FIELD_LOET'],
	'eqValue' => $base['ERR_FORM_FIELD_VALUE_EQ'],
	'neqValue' => $base['ERR_FORM_FIELD_VALUE_NEQ'],
	'gtValue' => $base['ERR_FORM_FIELD_VALUE_GT'],
	'ltValue' => $base['ERR_FORM_FIELD_VALUE_LT'],
	'goetValue' => $base['ERR_FORM_FIELD_VALUE_GOET'],
	'loetValue' => $base['ERR_FORM_FIELD_VALUE_LOET']
);

header("Content-type: text/javascript");
echo "var Locale = ", jsLocaleSerialize($locale), ";";
echo "var Lang = ", jsLocaleSerialize($lang), ";";

/**
 * Serializes a PHP variable into a JSON string
 *
 * @param mixed $value PHP variable
 * @return mixed
 */
function jsLocaleSerialize($value) {
	if (TypeUtils::isHashArray($value)) {
		$result = array();
		foreach ($value as $k => $v)
			$result[] = "'{$k}':" . jsLocaleSerialize($v);
		return '{' . implode(',', $result) . '}';
	} elseif (is_array($value)) {
		$result = array();
		for ($i=0; $i<sizeof($value); $i++)
			$result[] = jsLocaleSerialize($value[$i]);
		return '[' . implode(',', $result) . ']';
	} elseif (is_numeric($value)) {
		return $value;
	} elseif (is_bool($value)) {
		return ($value ? 'true' : 'false');
	} elseif (is_null($value)) {
		return 'null';
	} elseif (is_object($value) && $value->__json__) {
		return $value->__json__;
	} else {
		return jsLocaleFormatString(strval($value));
	}
}

/**
 * Perform a replacement from
 * an sprintf placeholder by a numeric
 * placeholder (%s => %1, %s => %2)
 *
 * @param array $item Regexp matches
 * @return string
 */
function jsLocaleDoReplacement($item) {
	global $count;
	return '%' . (++$count);
}

/**
 * Format a language entry value, escaping it as a Javascript
 * string and replacing sprintf placeholders by ordered %N holders
 *
 * @param string $value
 * @return string Processed value
 */
function jsLocaleFormatString($value) {
	global $count;
	$count = 0;
	$value = preg_replace_callback('/(?<!%)%[bcdufosxX\.0-9]+/', 'jsLocaleDoReplacement', $value);
	$value = StringUtils::escape($value, 'javascript');
	return "\"" . $value . "\"";
}
?>