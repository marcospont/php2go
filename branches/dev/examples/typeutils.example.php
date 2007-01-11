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

	require_once('config.example.php');

	println('<b>PHP2Go Example</b> : php2go.util.TypeUtils<br>');
	println('The class TypeUtils is automatically imported when you require p2gConfig.php. Besides, it\'s a static class.');

	$number = 4;
	println('Is integer ('.$number.') ? ' . TypeUtils::parseInteger(TypeUtils::isInteger($number)));
	$float = 'abc';
	println('Is float ('.$float.') ? ' . TypeUtils::parseInteger(TypeUtils::isFloat($float)));
	$toParse = "test00001";
	println('Parse integer ('.$toParse.') = ' . TypeUtils::parseInteger($toParse));
	$toParse = "9,1";
	println('Parse float ('.$toParse.') = ' . TypeUtils::parseFloat($toParse));
	$isNull = '';
	println('Is null (empty string) ? ' . TypeUtils::parseInteger(TypeUtils::isNull($isNull)));
	$false = strpos('teste', 'a');
	println('Is false (strpos of a in string teste) ? ' . TypeUtils::parseInteger(TypeUtils::isFalse($false)));
	class type_utils_example {
		function type_utils_example() {
		}
	}
	$t = new type_utils_example();
	println('Is instance of type_utils_example (' . exportVariable($t) . ') ? ' . TypeUtils::parseInteger(TypeUtils::isInstanceOf($t, 'type_utils_example')));

?>