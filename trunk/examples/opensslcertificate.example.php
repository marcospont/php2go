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

	require_once('config.example.php');
	import('php2go.security.OpenSSLCertificate');

	println('<b>PHP2Go Example</b> : php2go.security.OpenSSLCertificate');
	println('<b>Also using :</b> php2go.security.DistinguishedName<br>');
	println('Read, parse and print information about a X.509 Certificate:<br>');

	// in this example, we will use SF.net public X.509 certificate
	$Cert = new OpenSSLCertificate('resources/example.cer');
	println("Name: " . $Cert->getName());
	println("Subject/Owner: " . $Cert->ownerDN->__toString());
	$Owner = $Cert->getOwnerDN();
	println("Only the owner common name: " . $Owner->getCommonName());
	println("Hash: " . $Cert->getHash());
	println("Serial Number: " . $Cert->getSerialNumber());
	println("Version: " . $Cert->getVersion());
	println("Issuer: " . $Cert->issuerDN->__toString());
	println("Issue Date (NotBefore): " . $Cert->getIssueDate("d/m/Y H:i:s"));
	println("Expiry Date (NotAfter): " . $Cert->getExpiryDate("d/m/Y H:i:s"));
	println("Is valid?: " . ($Cert->isValid() ? "yes" : "no"));
	println("Purposes: " . dumpArray($Cert->getPurposes()));
	println("String representation:<br>" . nl2br($Cert->__toString()));


?>