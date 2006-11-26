<?php

	// $Header: /www/cvsroot/php2go/examples/crypt.example.php,v 1.9 2006/06/09 04:38:44 mpont Exp $
	// $Revision: 1.9 $
	// $Date: 2006/06/09 04:38:44 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.security.Crypt');

	println(
		'<b>PHP2Go Examples</b> : php2go.security.Crypt<br><br>' .
		'Crypt cipher : blowfish (MCRYPT_BLOWFISH)<br>' .
		'Crypt mode : CBC (MCRYPT_MODE_CBC)<br>' .
		'Crypt key : \'this is the encrypt key\'<br>' .
		'Crypt data : \'this is secret data that must be encrypted\''
	);

	$crypt = new Crypt();
	$crypt->setCipher(MCRYPT_BLOWFISH);
	$crypt->setCipherMode(MCRYPT_MODE_CBC);
	$crypt->setKey('this is the encrypt key');
	$data = 'this is secret data that must be encrypted';
	$encrypted = $crypt->engineEncrypt($data);
	println('Encrypted : ' . $encrypted);
	$decrypted = $crypt->engineDecrypt($encrypted);
	println('Decrypted : ' . $decrypted);

?>