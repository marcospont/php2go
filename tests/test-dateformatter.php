<?php

include 'bootstrap.php';

header('Content-Type: text/html;charset=utf-8');

$time = DateTimeUtil::getTime('now');

$locale = Php2Go::app()->getLocale();

print DateTimeUtil::date('r', $time) . '<br/>';

print DateTimeUtil::date('r', $time, true) . '<br/>';

print DateTimeFormatter::formatIso($time, 'G GG GGG GGGG GGGGG') . "<br/>";

print DateTimeFormatter::formatIso($time, 'y yy yyy yyyy yyyyy') . "<br/>";

print DateTimeFormatter::formatIso($time, 'Y YY YYY YYYY YYYYY') . "<br/>";

print DateTimeFormatter::formatIso($time, 'l') . "<br/>";

print DateTimeFormatter::formatIso($time, 'M MM MMM MMMM MMMMM') . "<br/>";

print DateTimeFormatter::formatIso($time, 'w ww') . "<br/>";

print DateTimeFormatter::formatIso($time, 'W WW') . "<br/>";

print DateTimeFormatter::formatIso($time, 'D DD DDD') . "<br/>";

print DateTimeFormatter::formatIso($time, 'd dd') . "<br/>";

print DateTimeFormatter::formatIso($time, 'SS') . "<br/>";

print DateTimeFormatter::formatIso($time, 'ddd') . "<br/>";

print DateTimeFormatter::formatIso($time, 'F') . "<br/>";

print DateTimeFormatter::formatIso($time, 'E EE EEE EEEE EEEEE') . "<br/>";

print DateTimeFormatter::formatIso($time, 'e ee') . "<br/>";

print DateTimeFormatter::formatIso($time, 'a') . "<br/>";

print DateTimeFormatter::formatIso($time, 'B') . "<br/>";

print DateTimeFormatter::formatIso($time, 'h hh H HH') . "<br/>";

print DateTimeFormatter::formatIso($time, 'm mm') . "<br/>";

print DateTimeFormatter::formatIso($time, 's ss') . "<br/>";

print DateTimeFormatter::formatIso($time, 'I') . "<br/>";

print DateTimeFormatter::formatIso($time, 'z zz zzz zzzz') . "<br/>";

print DateTimeFormatter::formatIso($time, 'Z ZZ ZZZ ZZZZ') . "<br/>";

print DateTimeFormatter::formatIso($time, 'c') . "<br/>";

print DateTimeFormatter::formatIso($time, 'r') . "<br/>";

print DateTimeFormatter::formatIso($time, 'r', null, true) . "<br/>";

print DateTimeFormatter::formatIso($time, 'U') . "<br/>";

print DateTimeFormatter::formatIso($time, $locale->getDateInputFormat()) . "<br/>";

print DateTimeFormatter::formatIso($time, $locale->getTimeInputFormat()) . "<br/>";

print DateTimeFormatter::formatIso($time, $locale->getDateTimeInputFormat()) . "<br/>";

print DateTimeFormatter::formatIso($time, $locale->getDateTimeInputFormat(), null, true) . "<br/>";