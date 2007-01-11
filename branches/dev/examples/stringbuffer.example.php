<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

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
	println ($sb->__toString());

?>