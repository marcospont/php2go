<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/languages/langCheck.php,v 1.1 2006/06/15 01:26:44 mpont Exp $
// $Date: 2006/06/15 01:26:44 $
// $Revision: 1.1 $

require_once('../p2gConfig.php');

/**
 * This script can be executed to calculate the differences
 * between the base language file (brazilian-portuguese.inc)
 * and other framework language files. The goal is to detect
 * keys that are out-of-sync
 */

$langBase = 'brazilian-portuguese';
$langNames = array(
	'czech', 'de-german', 'french', 'italian', 'spanish', 'us-english'
);
$base = (array)include($langBase . '.inc');
foreach ($langNames as $lang) {
	$diff1 = $diff2 = array();
	$compare = (array)include($lang . '.inc');
	getDiff($base, $compare, $diff1);
	getDiff($compare, $base, $diff2);		
	print '<b>' . $lang . '</b>';		
	dumpVariable($diff1);
	dumpVariable($diff2);
}

/**
 * Builds the difference array between 2 arrays, recursively 
 *
 * @param array $a Base array
 * @param array $b Comparison array
 * @param array $diff Computed diff
 * @param string $pfx Accumulative prefix for recursive calls
 * @return void
 */
function getDiff($a, $b, &$diff, $pfx='') {
	foreach ($a as $key => $value) {
		if (!array_key_exists($key, $b)) {
			$diff[] = (!empty($pfx) ? $pfx . '.' . $key : $key);
		} else {
			if (is_array($value) && is_array($b[$key]))
				getDiff($value, $b[$key], $diff, (empty($pfx) ? $key : $pfx . '.' . $key));
		}
	};
}	

?>