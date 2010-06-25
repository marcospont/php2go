<?php

include 'bootstrap.php';

$db = Db::instance();

$stmt = $db->prepare("select * from people where active = ?");
$stmt->execute(array(1));
while ($row = $stmt->fetch()) {
	print $row['name'] . '<br/>';
}
print('<br/>');

$row = array();
$stmt = $db->prepare("select * from people where active = ?");
$stmt->execute(array(1));
while ($stmt->fetchInto($row)) {
	print $row['name'] . '<br/>';
}
print('<br/>');

$row = array();
$stmt = $db->prepare("select * from people where active = ?");
$stmt->execute(array(1));
while ($obj = $stmt->fetchObject()) {
	print $obj->name . '<br/>';
}
print('<br/>');

$all = $db->fetchAll("select * from country limit 10");
foreach ($all as $row) {
	print $row['name'] . '<br/>';
}
print('<br/>');

$all = $db->fetchCol("select * from country limit 10");
foreach ($all as $value) {
	print $value . '<br/>';
}
print('<br/>');

$all = $db->fetchPairs("select * from country limit 10");
foreach ($all as $key => $value) {
	print $key . ' => ' . $value . '<br/>';
}
print('<br/>');

$row = $db->fetchRow("select * from people where active = ?", array(1));
var_dump($row);
print('<br/><br/>');

$db->execute("update people set active = 1");
print $db->affectedRows() . '<br/>';
print '<br/>';