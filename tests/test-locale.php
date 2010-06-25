<?php

include 'bootstrap.php';

header('Content-Type: text/html;charset=utf-8');

echo '<pre>';

$locale = new Locale('de_DE');

var_dump($locale->getMonths());
var_dump($locale->getWeekDays());

echo '</pre>';