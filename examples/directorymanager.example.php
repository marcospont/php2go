<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

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