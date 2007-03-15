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
	import('php2go.file.ZipFile');

	println('<b>PHP2Go Examples</b> : php2go.file.ZipFile<br>');

	/**
	 * creates the instance using the factory method getInstance
	 */
	$zip =& FileCompress::getInstance('zip');
	$zip->debug = TRUE;

	/**
	 * add a file in the ZIP archive using the filename
	 */
	$zip->addFile('forms.example.xml');
	/**
	 * add the contents of a file (string parameter)
	 */
	$zip->addData(FileSystem::getContents('reports.example.xml'), 'reports.example.xml', array('time' => filemtime('reports.example.xml')));

	/**
	 * save the file
	 */
	$zip->saveFile('tmp/test.zip', 0777);

	/**
	 * extract the archived & compressed data
	 */
	$zip->saveExtractedFiles($zip->extractFile('tmp/test.zip'), 0777, 'tmp');

?>