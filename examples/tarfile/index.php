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

	require_once('../config/config.php');
	import('php2go.file.TarFile');

	echo
		'<b>PHP2Go Examples</b> : php2go.file.TarFile<br /><br />';

	/**
	 * create the instance using the factory method getInstance
	 */
	$tar =& FileCompress::getInstance('tar');
	$tar->debug = TRUE;

	/**
	 * add some files in the tarball
	 */
	$tar->addFile('file.txt');
	$tar->addFile('logo.jpg');

	/**
	 * save the file with and without gzip compression
	 */
	$tar->saveFile('../tmp/test.tar', 0777);
	$tar->saveGzip('../tmp/test.tar.gz', 0777);

	/**
	 * extract the compressed archive
	 */
	$tar->extractFileTo('../tmp/test.tar.gz', '../tmp/', 0777);

?>