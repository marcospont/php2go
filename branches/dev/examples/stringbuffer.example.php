<?php

	// $Header: /www/cvsroot/php2go/examples/stringbuffer.example.php,v 1.7 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.text.StringBuffer');

	println('<b>PHP2Go Example</b> : php2go.text.StringBuffer<br>');

	$sb = new StringBuffer("the quick");
	$sb->append(" brown fox");
	$sb->append(" jumps over the lazy dog");
	println ($sb->charAt(1));
	println ($sb->indexOf("over"));
	println ($sb->indexOf("o"));
	println ($sb->indexOf("o", 20));
	println ($sb->lastIndexOf("o"));
	println ($sb->lastIndexOf("foo"));
	println ($sb->lastIndexOf("x"));
	println ($sb->lastIndexOf("x", 20));
	$dst = NULL;
	$sb->getChars(0, 10, $dst);
	println ($dst);
	dumpVariable($sb);
	$sb->setLength(15);
	dumpVariable($sb);
	$sb->insert(15, " fox jumps over the lazy dog");
	$sb->ensureCapacity(40);
	dumpVariable($sb);
	$sb->setCharAt(0, "T");
	println ($sb->toString());

?>