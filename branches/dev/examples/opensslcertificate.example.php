<?php

	// $Header: /www/cvsroot/php2go/examples/opensslcertificate.example.php,v 1.3 2006/06/09 04:38:45 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2006/06/09 04:38:45 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.security.OpenSSLCertificate');

	println('<b>PHP2Go Example</b> : php2go.security.OpenSSLCertificate');
	println('<b>Also using :</b> php2go.security.DistinguishedName<br>');
	println('Read, parse and print information about a X.509 Certificate:<br>');

	// in this example, we will use SF.net public X.509 certificate
	$Cert = new OpenSSLCertificate('resources/example.cer');
	println("Name: " . $Cert->getName());
	println("Subject/Owner: " . $Cert->ownerDN->toString());
	$Owner = $Cert->getOwnerDN();
	println("Only the owner common name: " . $Owner->getCommonName());
	println("Hash: " . $Cert->getHash());
	println("Serial Number: " . $Cert->getSerialNumber());
	println("Version: " . $Cert->getVersion());
	println("Issuer: " . $Cert->issuerDN->toString());
	println("Issue Date (NotBefore): " . $Cert->getIssueDate("d/m/Y H:i:s"));
	println("Expiry Date (NotAfter): " . $Cert->getExpiryDate("d/m/Y H:i:s"));
	println("Is valid?: " . ($Cert->isValid() ? "yes" : "no"));
	println("Purposes: " . dumpArray($Cert->getPurposes()));
	println("String representation:<br>" . nl2br($Cert->toString()));


?>