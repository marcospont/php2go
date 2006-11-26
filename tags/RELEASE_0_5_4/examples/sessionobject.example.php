<?php

	// $Header: /www/cvsroot/php2go/examples/sessionobject.example.php,v 1.12 2006/05/07 15:24:24 mpont Exp $
	// $Revision: 1.12 $
	// $Date: 2006/05/07 15:24:24 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.net.HttpRequest');
	import('php2go.session.SessionObject');
	session_save_path(PHP2GO_ROOT . 'examples/resources/');
	
	$sess = new SessionObject('MY_SESSION');
	echo '<b>PHP2Go Example</b> : php2go.session.SessionObject<br><br>';	
	if (!$sess->isRegistered()) {
		echo 'START...<br><a href=\'' . HttpRequest::basePath() . '\'>reload page</a>';
		$sess->createProperty('PAGE_VIEWS', 1);
		$sess->createTimeCounter('PAGE_TIME');	
		$timeCounter = $sess->getTimeCounter('PAGE_TIME');
		$sess->createProperty('URLS', array(array(HttpRequest::basePath(), NULL)));
		$sess->register();
	} else {
		$sess->setPropertyValue('PAGE_VIEWS', $sess->getPropertyValue('PAGE_VIEWS')+1);
		echo 'Page Views : ' . $sess->getPropertyValue('PAGE_VIEWS') . '<br><a href=\'' . HttpRequest::basePath() . '\'>reload page</a><br><br>';
		$timeCounter =& $sess->getTimeCounter('PAGE_TIME');
		$u = $sess->getPropertyValue('URLS');
		$timeCounter->stop();
		$u[sizeof($u)-1][1] = $timeCounter->getMinutes();
		echo 'Visited URLs :<pre>';
		var_dump($u);
		echo '</pre>';	
		$timeCounter->restart();
		$u[] = array(HttpRequest::basePath(), NULL);
		$sess->setPropertyValue('URLS', $u);
		$sess->update();
	}		

?>