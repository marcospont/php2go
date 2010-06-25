<?php

include 'bootstrap.php';

// basic cache
echo '<pre>';
$cache = Php2Go::app()->getCache();
if (!($data = $cache->load('test'))) {
	$huge = str_repeat('a', 500000);
	$cache->save($huge, 'test', 10);
	print 'miss' . PHP_EOL;
} else {
	print 'hit' . PHP_EOL;
}
var_dump($cache->getIds());
var_dump($cache->getUsage());
var_dump(get_class($cache->backend));
echo '</pre>';

class Cacheable
{
	public static function func1() {
		return 'foo';
	}
	public function func2($str) {
		return $str;
	}
}
$cacheable = new Cacheable();
function cacheable() {
	return 'baz';
}

// function cache
$func = new FunctionCache();
echo $func->call(array('Cacheable', 'func1'));
echo $func->call(array($cacheable, 'func2'), array('bar'));
echo $func->call('cacheable');

echo '<br/>';

// class cache
$func = new ClassCache(array('instance' => $cacheable));
echo $func->func2('bla!');

echo '<br/>';

// output cache
// using a specific cache layer
$output = new OutputCache(array('backend' => 'file'));
if ($output->begin('output1')) {
	echo 'outputcache';
	$output->end(10);
}