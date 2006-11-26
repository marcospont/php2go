<?php

	// $Header: /www/cvsroot/php2go/examples/directorymanager.example.php,v 1.3 2006/06/09 04:38:45 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2006/06/09 04:38:45 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.datetime.Date');
	import('php2go.file.DirectoryManager');

	echo '<b>PHP2Go Example</b> : php2go.file.DirectoryManager<br><br>';

	// create an instance of DirectoryManager
	$dir = new DirectoryManager();
	// open the current directory
	$dir->open(getcwd());

	echo '<b>List current directory content</b><br>';
	echo '<b><pre>while ($entry = $dir->read()) { ... print entry info ... } </pre></b>';

	// while loop that reads the directory content
	while ($entry = $dir->read()) {
		print 'Name: ' . $entry->getName() . ' - Size: ' . $entry->getSize() . ' bytes - Last modified: ' . Date::localDate($entry->getAttribute('mTime')) . '<br>';
	}
	// close the dir handle
	$dir->close();

	// open another directory
	$dir->open('resources/');

	echo '<hr><br>';
	echo '<b>List relative path content using getFiles, filename mask (.tpl) and name sorting</b><br>';
	echo '<b><pre>$dir->getFiles("\.tpl", TRUE);</pre></b>';

	// while loop that reads the directory content
	$entries = $dir->getFiles("\.tpl");
	foreach ($entries as $entry) {
		print 'Name: ' . $entry->getName() . ' - Size: ' . $entry->getSize() . ' bytes - Last modified: ' . Date::localDate($entry->getAttribute('mTime')) . '<br>';
	}

	echo '<hr><br>';
	echo '<b>Get total size of a directory, using recursion</b><br>';
	echo '<b><pre>$dir->getSize(\'K\', 2, TRUE);</pre></b>';

	// gets the parent directory (another DirectoryManager instance)
	$parent =& $dir->getParentDirectory();
	// gets the total size of a directory, using recursion
	$size = $parent->getSize('K', 2, TRUE);
	echo 'Total size: ' . $size;

	// close opened directories
	$parent->close();
	$dir->close();

?>