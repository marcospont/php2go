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
	import('php2go.service.ServiceJSRS');

	$jsrs = new ServiceJSRS();
	$jsrs->registerHandler('jsrsTest');
	$jsrs->registerHandler('jsrsTest2');
	$jsrs->handleRequest();

	function jsrsTest($selected) {
		return "You've chosen option {$selected}. PHP was successfully called! PHP Version: " . PHP_VERSION;
	}

	function jsrsTest2() {
		$db = Db::getInstance();
		$db->setFetchMode(ADODB_FETCH_NUM);
		$rs = $db->getAll("
			select
				CLIENT_ID, NAME
			from
				client
			order by
				NAME
		");
		return ServiceJSRS::arrayToString($rs);
	}

?>