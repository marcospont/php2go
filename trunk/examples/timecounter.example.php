<?php

	// $Header: /www/cvsroot/php2go/examples/timecounter.example.php,v 1.9 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.9 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.datetime.TimeCounter');

	println('<b>PHP2Go Examples</b> : php2go.datetime.TimeCounter<br>');

	$timeCounter = new TimeCounter();
	println('Start<br>Sleep 2 seconds...');
	flush();
	sleep(2);
	println('Elapsed time : ' . $timeCounter->getElapsedTime() . '<br>Sleep 2 seconds...');
	flush();
	sleep(2);
	println('Elapsed time : ' . $timeCounter->getElapsedTime() . '<br>Sleep 2 seconds...');
	flush();
	sleep(2);
	$timeCounter->stop();
	println('Stop<br>Final interval : ' . $timeCounter->getInterval() . '');


?>