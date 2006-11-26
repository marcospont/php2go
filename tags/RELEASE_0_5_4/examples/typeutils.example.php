<?php

	// $Header: /www/cvsroot/php2go/examples/typeutils.example.php,v 1.4 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.4 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

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