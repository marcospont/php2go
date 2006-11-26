<?php

	// $Header: /www/cvsroot/php2go/examples/stringutils.example.php,v 1.4 2006/11/03 04:01:25 mpont Exp $
	// $Revision: 1.4 $
	// $Date: 2006/11/03 04:01:25 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.text.StringUtils');

	println('<b>PHP2Go Example</b> : php2go.text.StringUtils<br>');

	println('<b>Trim and remove unnecessary whitespaces and blank chars from a string:</b>');
	println(StringUtils::stripBlank('  the quick   brown fox		jumps over 	the 		lazy dog') . '<br>');

	println('<b>Extract portions of a string (shortcuts to PHP function substr)</b>');
	println(StringUtils::left('PHP2Go Web Development Framework', 10));
	println(StringUtils::right('PHP2Go Web Development Framework', 10));
	println(StringUtils::mid('PHP2Go Web Development Framework', 10, 5));
	println(StringUtils::charAt('foo bar baz', 4) . '<br>');

	println('<b>Insert a substring into an existent string</b>');
	println(StringUtils::insert('foo baz', 'bar ', 4) . '<br>');

	println('<b>Match a substring against an existent string (using strpos)</b>');
	dumpVariable(StringUtils::match('foo bar baz', 'oo', TRUE)) . '<br><br>';

	println('<b>Verify if all chars in a string are lower or upper (using regexp)</b>');
	println('All lower? (all Lower):');
	dumpVariable(StringUtils::isAllLower('all Lower'));
	println('All upper? (ALL UPPER):');
	dumpVariable(StringUtils::isAllUpper('ALL UPPER'));

	println('<b>String transformation methods</b>');
	println('Encode (using base64): ' . StringUtils::encode('this is a test', 'base64'));
	println('Encode (using quoted-printable): ' . StringUtils::encode('Hi! How are you?', 'quoted-printable', array('charset' => 'utf8')));
	println('Decode (using base64): ' . StringUtils::decode(base64_encode('this is a test'), 'base64'));
	println('Filter (accept only numbers): ' . StringUtils::filter('11249dhahd93848', 'num', ''));
	println('Filter (remove htmlentities, using replace string): ' . StringUtils::filter("one&nbsp;two&nbsp;three", 'htmlentities', '*'));
	println('Escape (convert html special chars): ' . StringUtils::escape('this is a <tag>', 'html'));
	println('Escape (convert all html entities): ' . StringUtils::escape('this is a <tag> and this is a string with accents: βγκυτ', 'htmlall'));
	println('Capitalize: ' . StringUtils::capitalize('this is an example of a capitalized text. the first letter of each word is uppercased.'));
	println('Normalize (convert all accents): ' . StringUtils::normalize('remove all accents: βγκτυ'));
	println('Camelize (convert to camel case): ' . StringUtils::camelize('TRANSFORM TO CAMEL CASE') . '<br>');

	println('<b>Generate a random string</b>');
	println(StringUtils::randomString(10, TRUE, TRUE) . '<br>');

	println('<b>String formatting methods</b>');
	println('Indent text (using tab character, size 1):<br><pre>' . StringUtils::indent("this is an indented text.\nit must be moved 1 \"tab\" from the left side of the page.", 1, chr(9)) . '</pre>');
	println('Truncate text: ' . StringUtils::truncate("this is an example of a long sentence, that must be truncate when it reaches 100 chars. The portion of the sentence after this number of chars must not be displayed.", 100, '...'));
	println('Spacify text: ' . StringUtils::insertChar("PHP2GO WEB DEVELOPMENT FRAMEWORK", ' ', FALSE));
	println('Wrap text (size 20):<br><pre>' . StringUtils::wrap("this is a long text that must be wrapped to have 20 chars per line. the method also cares about the word breaks, to preserve its integrity in the displayed content.", 20) . '</pre>');
	$d = fread(fopen('resources/css.example.css', 'rb'), filesize('resources/css.example.css'));
	println('Add line numbers<br>' . StringUtils::addLineNumbers($d, 1, 3, ')', '<br>'));

?>