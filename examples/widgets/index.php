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
	import('php2go.base.Document');
	import('php2go.net.HttpRequest');
	import('php2go.template.Template');

	/**
	 * document creation and configuration
	 */
	$doc = new Document('../common/basicwithtable.tpl');
	$doc->setTitle('PHP2Go Examples - Widgets');
	$doc->addStyle('../common/examples.css');

	/**
	 * setup some sample variables that will be used inside widgets
	 */
	$db =& Db::getInstance();
	$clients = $db->query("select * from client order by name");
	$products = $db->query("select code, short_desc, date_added, price from products order by id_product");
	$options = array(
		'collapsiblepanel' => 'CollapsiblePanel',
		'datatable' => 'DataTable',
		'googlemap' => 'GoogleMap',
		'i25barcode' => 'I25BarCode',
		'slideshow' => 'SlideShow',
		'tabview' => 'TabView',
		'templatecontainer' => 'TemplateContainer',
		'toolbar' => 'Toolbar'
	);

	/**
	 * widget choice
	 */
	$widget = HttpRequest::get('widget');
	switch ($widget) {
		case 'collapsiblepanel' :
			$tpl = new Template('collapsiblepanel.tpl');
			$tpl->parse();
			break;
		case 'datatable' :
			$tpl = new Template('datatable.tpl');
			$tpl->parse();
			$tpl->assignByRef('products', $products);
			break;
		case 'googlemap' :
			$tpl = new Template('googlemap.tpl');
			$tpl->parse();
			$tpl->assign('locations', array(
				array('lng' => 176.157493, 'lat' => -37.705618), array('lng' => 176.19935, 'lat' => -37.655354), array('lng' => 176.16687, 'lat' => -37.692613), array('lng' => 176.165891, 'lat' => -37.706493),
				array('lng' => 176.204689, 'lat' => -37.667294), array('lng' => 176.203218, 'lat' => -37.661064), array('lng' => 176.169288, 'lat' => -37.68327), array('lng' => 176.168478, 'lat' => -37.672081),
				array('lng' => 176.180724, 'lat' => -37.635872), array('lng' => 176.091445, 'lat' => -37.677173), array('lng' => 176.161794, 'lat' => -37.703732), array('lng' => 176.203303, 'lat' => -37.660507),
				array('lng' => 176.184663, 'lat' => -37.640374), array('lng' => 176.184771, 'lat' => -37.637439), array('lng' => 176.18364, 'lat' => -37.639004), array('lng' => 176.153611, 'lat' => -37.707934),
				array('lng' => 176.139651, 'lat' => -37.736847), array('lng' => 176.138309, 'lat' => -37.736759), array('lng' => 176.193725, 'lat' => -37.657282), array('lng' => 176.155199, 'lat' => -37.710751),
				array('lng' => 176.168771, 'lat' => -37.680237), array('lng' => 176.207925, 'lat' => -37.666967), array('lng' => 176.145402, 'lat' => -37.718243), array('lng' => 176.203331, 'lat' => -37.658349)
			));
			break;
		case 'i25barcode' :
			$tpl = new Template('i25barcode.tpl');
			$tpl->parse();
			$tpl->assign('code1', 1234567890);
			$tpl->assign('code2', 1610200299);
			break;
		case 'slideshow' :
			$tpl = new Template('slideshow.tpl');
			$tpl->parse();
			break;
		case 'templatecontainer' :
			$tpl = new Template('templatecontainer.tpl');
			$tpl->parse();
			$tpl->assignByRef('clients', $clients);
			break;
		default :
			$tpl = new Template('header.tpl');
			$tpl->parse();
			break;
	}

	/**
	 * display document
	 */
	$tpl->assign('options', $options);
	$doc->assignByRef('main', $tpl);
	$doc->display();


?>