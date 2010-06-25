<?php

define('ADODB_OUTP', 'sql');
include 'bootstrap.php';

Php2Go::app()->getDb()->getDriver()->debug = 1;

function sql($sql=null, $nl=false) {
	static $db;
	if ($sql) {
		if (!isset($db))
			$db = array();
		$db[] = $sql . ($nl ? '<br/>' : '');
	} else {
		echo implode("", (array)$db);
	}
}

$person = Person::model()->findByPK(1);
$person->name = 'Teste';
$person->save();

echo sql();