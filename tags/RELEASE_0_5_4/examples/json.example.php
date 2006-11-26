<?php

	// $Header: /www/cvsroot/php2go/examples/json.example.php,v 1.1 2006/06/23 04:09:27 mpont Exp $
	// $Revision: 1.1 $
	// $Date: 2006/06/23 04:09:27 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.util.json.*');

	println('<b>PHP2Go Examples: JSON encoding/decoding</b>');
	println('<br><b>Encoder</b>');

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

	println('<br><b>Decoder</b>');

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