<?php

	// $Header: /www/cvsroot/php2go/examples/number.example.php,v 1.3 2006/06/09 04:38:45 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2006/06/09 04:38:45 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.util.Number');

	println('<b>PHP2Go Example</b> : php2go.util.Number<br>');

	// base conversion
	println('<b>Convert a number from base M to base N:</b>');
	println('2 from base 10 to base 2 => ' . Number::numberConversion(2, 10, 2));
	println('65534 from base 10 to base 16 => ' . Number::numberConversion(65534, 10, 16));
	println('1111111111111111 from base 2 to base 10 => ' . Number::numberConversion('1111111111111111', 2, 10));
	println('FFFFFF from base 16 to base 10 => ' . Number::numberConversion('FFFFFF', 16, 10) . '<br>');

	// hexbin conversion
	println('<b>Convert an hexadecimal number to binary representation:</b>');
	println('FEFE => ' . Number::fromHexToBin('FEFE') . '<br>');

	// decimal to currency conversion
	println('<b>Show string representation of a decimal number (using fraction notation)</b>');
	println('1.4 => ' . Number::fromDecimalToFraction(1.4));
	println('6.25 => ' . Number::fromDecimalToFraction(6.25));
	println('18.875 => ' . Number::fromDecimalToFraction(18.875));
	println('9.99 => ' . Number::fromDecimalToFraction(9.99) . '<br>');

	// decimal to fraction conversion
	println('<b>Convert decimal number into currency value:</b>');
	println('.01 => ' . Number::fromDecimalToCurrency('.01'));
	println('1000,25 => ' . Number::fromDecimalToCurrency('1000,25'));
	println('2188.76 => ' . Number::fromDecimalToCurrency('2188.76'));
	println('-455.33 => ' . Number::fromDecimalToCurrency('-455.33'));
	println('1000.00, forcing locale settings => ' . Number::fromDecimalToCurrency(1000.00, 'USD', '.', ',', 2, 'left') . '<br>');

	// arabic-roman and roman-arabic conversions
	println('<b>Conversion ARABIC=>ROMAN and ROMAN=>ARABIC:</b>');
	println('100 in ROMAN => ' . Number::fromArabicToRoman('100'));
	println('CMXCVIII in ARABIC => ' . Number::fromRomanToArabic('CMXCVIII'));
	println('1999 in ROMAN => ' . Number::fromArabicToRoman('1999'));
	println('555 in ROMAN => ' . Number::fromArabicToRoman('555'));
	println('DCCCXXXIII IN ARABIC => ' . Number::fromRomanToArabic('DCCCXXXIII') . '<br>');

	// human readable byte amount
	println('<b>Convert a byte amount to human readable representation:</b>');
	println('1024 => ' . Number::formatByteAmount(1024, 'K', 0));
	println('39292839 => ' . Number::formatByteAmount(39292839, 'M', 2));
	println('40 * 1024 * 1024 * 1024 => ' . Number::formatByteAmount(40 * 1024 * 1024 * 1024, 'G', 0) . '<br>');

	// random number
	println('<b>Generate a random number:</b>');
	for ($i=0; $i<10; $i++) {
		println('Random number ' . $i . ': ' . Number::randomize(1, 100));
	}

?>