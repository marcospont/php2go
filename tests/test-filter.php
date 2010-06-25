<?php

include 'bootstrap.php';

$chain = new FilterChain();
$chain->addFilter(new FilterRegex('/[\(\)]/'));
$chain->addFilter(new FilterAlpha());
$chain->addFilter(new FilterAlphanum());

$str = array(
	'12311231klçjkhjhAFKJSK uyDFKDJF DJF JDFiuyui ads(*#(3))',
	'adfasdfkja sdei(*@*#$#$(#$) 23423 dsfa9349394d*@*#;;'
);

echo '<pre>';
var_dump($str);
var_dump($chain->filterArray($str));
echo '</pre>';

$values = array('0', '', array(), 0, false);
$null = new FilterNull(FilterNull::ALL);
var_dump($null->filterArray($values));

$values = array('1', '', array(), 1, false, null, 'true', 'si');
$bool = new FilterBoolean(FilterBoolean::ALL);
$bool->setLocale(new Locale('es'));
var_dump($bool->filterArray($values));

