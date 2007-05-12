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

	require_once("../p2gConfig.php");
	import('php2go.util.Callback');
	import('php2go.net.*');

	$url = new Url();

	println('<b>PHP2Go Examples</b> : php2go.util.Callback<br>');

	function myDummyFunction($arg1, $arg2) {
		return $arg1 + $arg2;
	}

	$callback = new Callback();

	// validate and invoke a callback using object and method
	println('<b>1) Callback using object instance and method : php2go.net.Url instance + encode method</b>');
	$callback->setFunction(array($url, 'encode'));
	$return = $callback->invoke(array(HttpRequest::basePath() . '?arg=test', 'q'), TRUE);
	println('Return: ' . $return . '<br>');

	// validate and invoke a callback using a static method call
	println('<b>2) Callback using a static method call : php2go.net.HttpRequest + basePath method</b>');
	$callback->setFunction('HttpRequest::basePath');
	$return = $callback->invoke();
	println('Return: ' . $return . '<br>');

	// validate and invoke a callback using a simple function
	println('<b>3) Callback using a simple function call</b>');
	$callback->setFunction('myDummyFunction');
	$return = $callback->invoke(array(1,1), TRUE);
	println('Return: ' . $return . '<br>');

	// force an error without disabling the internal error handling of the class
	println('<b>4) Force an error without disabling the internal error of the class</b>');
	$callback->setFunction('i am not a funcion');
	$return = $callback->invoke();
	println('Return: ' . $return . '<br>');

?>