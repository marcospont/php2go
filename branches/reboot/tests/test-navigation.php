<?php

include 'bootstrap.php';

$navigation = new Navigation(array(
	array(
		'label' => 'Home',
		'url' => 'sandbox/index',
		'items' => array(
			array(
				'label' => 'Logout',
				'url' => 'sandbox/logout'
			)
		)
	),
	array(
		'label' => 'XML',
		'url' => 'sandbox/xml',
		'visible' => true
	),
	array(
		'label' => 'External',
		'url' => 'http://www.php2go.com.br',
		'target' => '_blank'
	)
));

function printNavigation(NavigationContainer $nav) {
	if ($nav->hasItems()) {
		echo "<ul>" . PHP_EOL;
		foreach ($nav as $item) {
			if ($item->visible) {
				print "<li><a href=\"{$item->href}\" target=\"{$item->target}\">{$item->label}</a></li>" . PHP_EOL;
				printNavigation($item);
			}
		}
		echo "</ul>" . PHP_EOL;
	}
}
printNavigation($navigation);