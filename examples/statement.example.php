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
	import('php2go.net.HttpRequest');
	import('php2go.util.Statement');

	println('<b>PHP2Go Example</b> : php2go.util.Statement<br>');

	/**
	 * Create a new Statement class instance
	 */
	$statement = new Statement();

	/**
	 * Set the statement source
	 */
	$stCode = "You are running PHP ~version~, and the name of this script is ~script~";
	println('New Statement code : ' . $stCode);
	$statement->setStatement($stCode);

	/**
	 * Bind statement variables
	 */
	println('Bind variable version = PHP_VERSION');
	$statement->bindByName('version', PHP_VERSION, FALSE);
	println('Bind variable script = HttpRequest::basePath()');
	$statement->bindByName('script', HttpRequest::basePath());

	/**
	 * Print result
	 */
	println('Result : ' . $statement->getResult() . '<br>');

	/**
	 * Setup a new statement
	 */
	$stCode = "Running PHP on ~_SERVER['SERVER_NAME']~, ~_SERVER['SERVER_ADDR']~";
	println('New Statement code : ' . $stCode);
	$statement->setStatement($stCode);

	/**
	 * Bind variables using a magic method that searches
	 * for the placeholder replacements in external scopes
	 */
	println('Bind variables (method that tries to find all the variables in the DATA REPOSITORIES (request, session, registry)');
	$statement->bindVariables(FALSE, 'ROEGP');

	/**
	 * Prints out the statement result
	 */
	println('Result : ' . $statement->getResult());

?>