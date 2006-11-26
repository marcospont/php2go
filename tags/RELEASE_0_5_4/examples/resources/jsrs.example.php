<?php
	
	// $Header: /www/cvsroot/php2go/examples/resources/jsrs.example.php,v 1.3 2005/08/30 19:36:42 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2005/08/30 19:36:42 $
	// vim: set expandtab tabstop=4 shiftwidth=4:
	
	require_once("../../p2gConfig.php");	
	import('php2go.util.service.ServiceJSRS');
	
	$jsrs = new ServiceJSRS();
	$jsrs->registerHandler('jsrsTest');
	$jsrs->registerHandler('jsrsTest2');
	$jsrs->handleRequest();
	
	function jsrsTest($selected) {
		return "You've chosen option {$selected}. PHP was successfully called! PHP Version: " . PHP_VERSION;
	}
	
	function jsrsTest2() {
		$db = Db::getInstance();
		$db->setFetchMode(ADODB_FETCH_NUM);
		$rs = $db->getAll("
			select
				CLIENT_ID, NAME
			from
				client
			order by
				NAME
		");
		return ServiceJSRS::arrayToString($rs);
	}

?>