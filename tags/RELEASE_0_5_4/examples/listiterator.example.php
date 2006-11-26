<?php

	// $Header: /www/cvsroot/php2go/examples/listiterator.example.php,v 1.7 2006/10/26 04:55:43 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2006/10/26 04:55:43 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.util.AbstractList');

	echo '<b>PHP2Go Example</b> : php2go.util.AbstractList<br><br>';

	$list = new AbstractList(array(1,2,3,4,5,6,7,8,9,0));
	
	$iterator =& $list->iterator();
	println('From start to end');
	while ($iterator->hasNext()) {
		println($iterator->next());
	}
	println('From end to start');
	while ($iterator->hasPrevious()) {
		println($iterator->previous());
	}
	println('Move to index 4, to end');
	if ($iterator->moveToIndex(4)) {
		while ($iterator->hasNext()) {
			println($iterator->next());
		}
	}


?>