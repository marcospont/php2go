<?php

	// $Header: /www/cvsroot/php2go/examples/ajax.example.php,v 1.2 2006/07/12 07:39:29 mpont Exp $
	// $Revision: 1.2 $
	// $Date: 2006/07/12 07:39:29 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('config.example.php');
	import('php2go.base.Document');
	import('php2go.net.HttpRequest');
	import('php2go.session.SessionObject');
	import('php2go.template.Template');

	/**
	 * Here, we've created a simple class called Cart to encapsulate
	 * some methods needed by this example and extending SessionObject,
	 * so that the cart items can be persisted in the session scope
	 */
	class Cart extends SessionObject
	{
		/**
		 * Cart items
		 * @var array
		 */
		var $items = array();

		/**
		 * Constructor
		 */
		function Cart() {
			parent::SessionObject('cart');
			if (!parent::isRegistered())
				parent::register();
		}

		/**
		 * Add a new cart item
		 */
		function addItem($id, $desc, $price) {
			$desc = (get_magic_quotes_gpc() ? stripslashes($desc) : $desc);
			if (isset($this->items[$id]))
				$this->items[$id]['units']++;
			else
				$this->items[$id] = array('units'=>1, 'desc'=>$desc, 'price'=>$price);
			$this->update();
		}

		/**
		 * Update item units based on the request values
		 */
		function updateItemUnits($itemSet) {
			foreach ($itemSet as $id => $units) {
				$units = intval($units);
				if (array_key_exists($id, $this->items) && $units >= 0) {
					if ($units == 0)
						unset($this->items[$id]);
					else
						$this->items[$id]['units'] = $units;
				}
			}
			$this->update();
		}

		/**
		 * Remove a cart item
		 */
		function removeItem($id) {
			unset($this->items[$id]);
			$this->update();
		}

		/**
		 * This method prints out the HTML contents.
		 * This is the response returned by the AJAX actions
		 */
		function returnCart() {
			$tpl = new Template('resources/ajaxcart.example.tpl');
			$tpl->parse();
			$tpl->assignByRef('cart', $this);
			$tpl->display();
		}

		/**
		 * Sorts the cart items by description. This
		 * method is called from inside ajaxcart.template.tpl
		 */
		function sort() {
			function __sort($a, $b) {
				return strcasecmp($a['desc'], $b['desc']);
			}
			uasort($this->items, '__sort');
		}

		/**
		 * Calculates the total price based on the cart
		 * item prices and units. This method is called
		 * from inside ajaxcart.template.tpl
		 */
		function getTotal() {
			$total = 0;
			foreach ($this->items as $id => $data)
				$total += ($data['units'] * (float)$data['price']);
			return $total;
		}
	}

	$cartObj = new Cart();
	$request =& $_POST;
	$action = $request['action'];
	switch ($action) {
		/**
		 * Add item AJAX action:
		 * - insert a new cart item or update unit count
		 * - update the cart in the session scope (SessionObject::update)
		 * - return the cart HTML contents
		 */
		case 'add_item' :
			if (isset($request['id']))
				$cartObj->addItem($request['id'], $request['desc'], $request['price']);
			$cartObj->returnCart();
			break;
		/**
		 * Remove item AJAX action:
		 * - remove the given product id from the cart
		 * - update the cart in the session scope (SessionObject::update)
		 * - return the cart HTML contents
		 */
		case 'remove_item' :
			if (isset($request['id']))
				$cartObj->removeItem($request['id']);
			$cartObj->returnCart();
			break;
		/**
		 * Update the cart totals
		 * - update totals for all IDs found in the request
		 * - update the cart in the session scope (SessionObject::update)
		 * - return the cart HTML contents
		 */
		case 'update_totals' :
			if (isset($request['units']))
				$cartObj->updateItemUnits($request['units']);
			$cartObj->returnCart();
			break;
		/**
		 * Normal page execution: print list of products and cart contents
		 */
		default :
			displayPage($cartObj);
	}

	function displayPage($cartObj) {

		/**
		 * Create and configure an instance of the Document
		 * class. ajax.js, form.js and inputmask.js scripts
		 * will be needed and must be included
		 */
		$doc = new Document('resources/basicLayout.example.tpl');
		$doc->addScript(PHP2GO_JAVASCRIPT_PATH . 'ajax.js');
		$doc->addScript(PHP2GO_JAVASCRIPT_PATH . 'form.js');
		$doc->addScript(PHP2GO_JAVASCRIPT_PATH . 'inputmask.js');
		$doc->addStyle('resources/css.example.css');

		/**
		 * Setup the main document element, assigning the
		 * products recordset and the cart instance
		 */
		$db =& Db::getInstance();
		$main =& $doc->createElement('main', 'resources/ajax.example.tpl');
		$main->assign('products', $db->query("select * from products order by short_desc"));
		$main->assign('cart', $cartObj);
		$doc->display();

	}

?>