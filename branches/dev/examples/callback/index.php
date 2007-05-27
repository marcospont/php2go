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
	import('php2go.util.Callback');
	import('php2go.net.HttpRequest');
	import('php2go.net.Url');

	println('<b>PHP2Go Examples</b> : php2go.util.Callback<br />');

	$callback = new Callback();

	/**
	 * validate and invoke a callback based on an object's method
	 */
	println('<b>Callback based on an object\'s method : $url->encode()</b>');
	$url = new Url();
	$url->setFromCurrent();
	$url->setParameters('arg1=test&arg2=foo');
	$callback->setFunction(array($url, 'encode'));
	$return = $callback->invoke(array(NULL, 'q'), TRUE);
	println('Return: ' . $return . '<br />');

	println('<b>Callback defined by reference : $url->setParameters()</b>');
	$func = array(&$url, 'setParameters');
	$callback->setFunctionByRef($func);
	$callback->invoke('g=1&x=2');
	println('Return: ' . $url->getUrl() . '<br />');

	/**
	 * validate and invoke a callback based on a static class method
	 */
	println('<b>Callback based on a static method : HttpRequest::basePath()</b>');
	$callback->setFunction('HttpRequest::basePath');
	$return = $callback->invoke();
	println('Return: ' . $return . '<br />');

	/**
	 * validate and invoke a callback based on a procedural function
	 */
	println('<b>Callback based on a procedural function : myDummyFunction</b>');
	function myDummyFunction($arg1, $arg2) {
		return $arg1 + $arg2;
	}
	$callback->setFunction('myDummyFunction');
	$return = $callback->invoke(array(1,1), TRUE);
	println('Return: ' . $return . '<br />');

	/**
	 * force an invalid callback: an error is thrown
	 */
	println('<b>Callback based on an inexistent function : an error is thrown</b>');
	$callback->setFunction('i am not a funcion');
	$return = $callback->invoke();
	println('Return: ' . $return . '<br />');

?>