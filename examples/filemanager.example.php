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
	import('php2go.file.FileManager');
	import('php2go.util.Number');

	echo '<b>PHP2Go Example</b> : php2go.file.FileManager<br><br>';

	/**
	 * create an instance of the FileManager class
	 */
	$mgr = new FileManager();

	/**
	 * open using READ_BINARY mode ("rb")
	 */
	println('<b>Display the contents of a file, line by line</b>');
	$mgr->open('sql/menu.sql', FILE_MANAGER_READ_BINARY);
	print('<pre>');
	while ($line = $mgr->readLine()) {
		print($line);
	}
	print('</pre>');

	println('<hr>');
	println('<b>Pointer operations: tell, rewind, seek</b>');
	println('Current position: ' . $mgr->getCurrentPosition());
	$mgr->rewind();
	$mgr->seek(100);
	println($mgr->readChar());
	$mgr->seek(0);
	println($mgr->readChar());

	println('<hr>');
	println('<b>Display the attributes of the current opened file</b>');
	print('<b><pre>$attrs = $mgr->getAttributes();</pre></b>');
	/**
	 * get all attributes
	 */
	$attrs = $mgr->getAttributes();
	foreach ($attrs as $name => $value) {
		println("{$name}: $value");
	}

	println('<hr>');
	println('<b>Transform the value of an attribute</b>');
	println('Last modified: ' . Date::localDate($mgr->getAttribute('mTime')));
	println('Total size in KB: ' . Number::formatByteAmount($mgr->getAttribute('size'), 'K', 2));
	println('Is writeable?: ' . ($mgr->getAttribute('isWriteable') ? 'yes' : 'no'));
	$mgr->close();

	println('<hr>');
	println('<b>Read a file as an array</b>');
	print('<b><pre>$file = $mgr->readArray(\'file_path\');</pre></b>');
	/**
	 * using readArray, it's not necessary to open or close the file,
	 * because the class uses the file() function internally
	 */
	$file = $mgr->readArray('resources/javascript.example.js');
	print('<pre>');
	foreach ($file as $line)
		print($line);
	print('</pre>');

	println('<hr>');
	println('<b>Create a new file and write some data</b>');
	println("<b><pre>\$mgr->open('file_path', FILE_MANAGER_WRITE_BINARY);\n\$mgr->writeLine('content');</pre></b>");
	$mgr->open('tmp/filemanager.example.txt', FILE_MANAGER_WRITE_BINARY);
	$mgr->writeLine('the quick brown fox jumps over the lazy dog', "\r\n");
	$mgr->writeLine('PHP2Go Web Development Framework', "\r\n");

	/**
	 * if this line is missing, the class destructor will close all the active
	 * and valid pointers and release all the valid file locks
	 */
	$mgr->close();

?>