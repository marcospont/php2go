<?php

	// $Header: /www/cvsroot/php2go/examples/xmlparserrender.example.php,v 1.15 2006/06/09 04:38:46 mpont Exp $
	// $Revision: 1.15 $
	// $Date: 2006/06/09 04:38:46 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.xml.XmlDocument');
	import('php2go.xml.XmlRender');

	println('<b>PHP2Go Examples</b> : php2go.xml package<br>');

	/**
	 * create a new XML document
	 */
	$doc = new XmlDocument();
	/**
	 * call the parseXml method (creates and executes a PHP xml_parser)
	 */
	$file = 'resources/tennis.example.xml';
	echo 'Parse ' . $file . '<br>';
	$doc->parseXml($file, T_BYFILE);

	/**
	 * get the document element (root node) to print the nodes info
	 */
	echo 'Print nodes at first level (root children)<br>';
	$root = $doc->getDocumentElement();
	if ($root->hasChildren()) {
		$total = $root->getChildrenCount();
		for ($i=0; $i<$total; $i++) {
			$child =& $root->getChild($i);
			print $child->getName() . '<br>';
		}
	}

	/**

	XML rendering

	*/

	/**
	 * create a new XML renderer, set the default charset and add an stylesheet file
	 */
	echo '<br>Render XML file<br>';
	$render = new XmlRender('root_tag', array('root_attr_1' => 'value', 'root_attr_2' => 'anotherValue'));
	$render->setCharset('UTF-8');
	//$render->Document->addStylesheet('http://stylesheet/link/stylesheet.css');

	/**
	 * adding new nodes manually : the nodes must be added using the addChild method of the root node
	 */
	$root =& $render->getRoot();
	for ($i=0; $i<10; $i++) {
		$root->addChild(
			new XmlNode('child_node_tag', array('child_attr' => 'value'), NULL, NULL)
		);
	}

	/**
	 * adding content in the XML tree : the method addContent can handle objects, arrays and scalar values
	 */
	$obj = new stdClass();
	$obj->name = 'Foo';
	$obj->address = 'Elm Street';
	$obj->children = array('Anna','John');
	$render->setAddOptions(array('defaultNodeName'=>'person','createArrayNode'=>FALSE), FALSE);
	$render->addContent($obj);
	$rdfData = array(
		'channel' => array(
			'title' => 'PHP2Go RDF Channel',
			'link' => 'http://www.php2go.com.br',
			'image' => array(
				'title' => 'PHP2Go Logo',
				'url' => 'http://www.php2go.com.br/resources/images/p2g_logo2.jpg',
				'link' => 'http://www.php2go.com.br'
			),
			'item' => array(
				array(
					'title' => 'Item 1',
					'link' => 'http://www.example.org'
				),
				array(
					'title' => 'Another Item',
					'link' => 'http://www.someurl.com'
				)
			)
		)
	);
	$render->setAddOptions(array('arrayEntryAsRepeat'=>TRUE,'createArrayNode'=>FALSE), FALSE);
	$render->addContent($rdfData);


	/**
	 * render and save the content in the file system
	 */
	$render->render("\n", "    ");
	$saveTo = 'tmp/xmlrender.example.xml';
	$render->toFile($saveTo);
	echo 'XML file saved at <a href=\'' . $saveTo . '\' title=\'view file\'>' . $saveTo . '</a>';

?>