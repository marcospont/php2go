<?php

	// this file is used to answer to curlclient example requests
	if (!empty($_POST)) {
		print "POST superglobal dump:<br />";
		echo '<ul>';
		foreach ($_POST as $key=>$value)
			print "<li>{$key}=>{$value}</li>";
		echo '</ul>';
	} else {
		print "this is the remote page response body";
	}

?>