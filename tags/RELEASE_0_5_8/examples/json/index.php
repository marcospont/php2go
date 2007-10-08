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
	import('php2go.util.json.*');

	println('<b>PHP2Go Examples: JSON encoding/decoding</b>');
	println('<br /><b>Encoder</b>');

	$encoder = new JSONEncoder();
	$encoder->throwErrors = FALSE;
	$decoder = new JSONDecoder();

	// Encoding scalar values
	print('<pre>');
	println($encoder->encodeValue(TRUE), "\n");
	println($encoder->encodeValue(FALSE), "\n");
	println($encoder->encodeValue(NULL), "\n");
	println($encoder->encodeValue(1), "\n");
	println($encoder->encodeValue(-1.00002999), "\n");
	println($encoder->encodeValue("hello world"), "\n");
	println($encoder->encodeValue("hello\t\"world\""), "\n");
	println($encoder->encodeValue("hello\"],[world!"), "\n");
	print('</pre>');

	// Encoding arrays
	print('<pre>');
	println($encoder->encodeValue(range(0, 10)), "\n");
	println($encoder->encodeValue(array(1=>'apples', 'fruits'=>'bananas', 3=>'watermelons')), "\n");
	println($encoder->encodeValue(array(
		'params' => array(
			'style' => 'default',
			'options' => array(
				'add-ons' => array(),
				'values' => array(
					'name' => 'Name',
					'age' => 30,
					'country' => 'Spain'
				),
				'applyAll' => TRUE,
				'wait' => FALSE,
				'align' => 'right',
				'messages' => array(
					'',
					'Hello world!'
				)
			)
		)
	)), "\n");
	println($encoder->encodeValue($_SERVER), "\n");
	print('</pre>');

	// Encoding objects
	$obj = new stdClass();
	$obj->string = 'My string';
	$obj->number = 100;
	$obj->floatingPoint = -1.2;
	$obj->null = NULL;
	$obj->bool = FALSE;
	$obj->arr = array(array(4), array(1=>2), array(0=>1));
	$obj->inner = new stdClass();
	$obj->inner->arr = array(1, 2, 3);
	print('<pre>' . $encoder->encodeValue($obj) . '</pre>');

	// Encoding Javascript special types
	print('<pre>');
	println($encoder->encodeValue(JSONEncoder::jsIdentifier('identifier')), "\n");
	println($encoder->encodeValue(JSONEncoder::jsFunction('return true;', array('obj', 'event'))), "\n");
	print('</pre>');

	println('<b>Decoder</b>');

	/**
	 * Decoding scalar values
	 */
	dumpVariable($decoder->decodeValue('true'));
	dumpVariable($decoder->decodeValue('false'));
	dumpVariable($decoder->decodeValue('null'));
	dumpVariable($decoder->decodeValue('7'));
	dumpVariable($decoder->decodeValue('-1.2543e+2'));
	dumpVariable($decoder->decodeValue('"hello world"'));
	dumpVariable($decoder->decodeValue('\'hello "world"\''));

	/**
	 * Decoding arrays
	 */
	dumpVariable($decoder->decodeValue('[false,null,true,"hello world",1,-0.0001]'));
	dumpVariable($decoder->decodeValue('[0,1,2,3,4,5,6,7,8,9,10]'));

	/**
	 * Decoding objects
	 */
	dumpVariable($decoder->decodeValue('{"string":"\"Hello\":\t\tWorld}:{!!","array":[-1,0,1],"inner":{"number":123}}'));
	dumpVariable($decoder->decodeValue("
	/* this is a multiline
	comment */
	{'teste' : {
		// options
		'options' : [],
		// values
		'values' : [1, 2, /* here */ 3]
	}, /* comment */ 'flag' : true, 'mode' : 1 }"));

?>