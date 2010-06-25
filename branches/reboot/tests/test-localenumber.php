<?php

include 'bootstrap.php';

header('Content-Type: text/html;charset=utf-8');

print LocaleNumber::isNumber('10') . "<br/>";

print LocaleNumber::isNumber('-10') . "<br/>";

print LocaleNumber::isNumber(',1') . "<br/>";

print LocaleNumber::isNumber('1,1') . "<br/>";

print LocaleNumber::isNumber('-1,1') . "<br/>";

print LocaleNumber::isNumber('1.000') . "<br/>";

print LocaleNumber::isNumber('-1.000') . "<br/>";

print LocaleNumber::isNumber('1000,00') . "<br/>";

print LocaleNumber::isNumber('-1000,00') . "<br/>";

print LocaleNumber::isNumber('1.000.000') . "<br/>";

print LocaleNumber::isNumber('-1.000.000') . "<br/>";

print LocaleNumber::isNumber('1e1') . "<br/>";

print LocaleNumber::isNumber('-1e1') . "<br/>";

print LocaleNumber::getInteger('1') . "<br/>";

print LocaleNumber::getInteger('1,22') . "<br/>";

print LocaleNumber::getInteger('1.000') . "<br/>";

print LocaleNumber::getFloat('1') . "<br/>";

print LocaleNumber::getFloat('1,22') . "<br/>";

print LocaleNumber::getFloat('1.000') . "<br/>";

print LocaleNumberFormatter::format(10000000000, '##0') . "<br/>";

print LocaleNumberFormatter::formatInteger(10000.55) . "<br/>";

print LocaleNumberFormatter::formatFloat(10000.55) . "<br/>";