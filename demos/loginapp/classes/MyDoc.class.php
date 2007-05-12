<?php

/**
 * Perform the necessary imports
 */
import('php2go.auth.Auth');
import('php2go.base.Document');
import('php2go.net.Url');

class MyDoc extends Document 
{
	var $auth;
	
	function MyDoc() 
	{	
		/**
		 * The following line retrieves the singleton of the authenticator class.
		 * This special method checks in the configuration if there's a class path (in this case, loginapp.MyAuth),
		 * and instantiates the defined authenticator. Otherwise, it will use php2go.auth.AuthDb as default authenticator
		 */
		$this->auth =& Auth::getInstance();
		
		/**
		 * Configure the HTML document
		 * IMPORTANT: First of all, we must call the parent ctor
		 */
		parent::Document(TEMPLATE_PATH . 'doc_layout.tpl');
		parent::addStyle(CSS_PATH . 'loginapp.css');		
		parent::setCache(TRUE);
		parent::setCompression();
		parent::preventRobots();
	}
	
	function display() 
	{
		/**
		 * Builds the slots and output the final page content.
		 * You can assign simple strings or DocumentElement instances to the entries of the array elements.
		 * The _buildMenu() method builds an instance of DocumentElement and returns a reference to it.
		 */
		$this->elements['header'] = '<h1>PHP2Go Login Application</h1>Header Slot';
		$this->elements['menu'] =& $this->_buildMenu();
		$this->elements['footer'] = 'Footer Slot';
		parent::display();
	}
	
	function &_buildMenu() 
	{
		/**
		 * This is an example of a page slot built using the DocumentElement class.
		 * The instance uses the menu_layout.tpl, that contains some user variables.
		 * These variables receive the values stored in the session object
		 */
		$user =& User::getInstance();
		$menu =& new DocumentElement();
		$menu->put(TEMPLATE_PATH . 'menu_layout.tpl', T_BYFILE);
		$menu->parse();
		$menu->assign('name', $user->getName());
		$menu->assign('login', $user->getUsername());
		$menu->assign('time', intval($user->getElapsedTime()));
		$menu->assign('idle_time', intval($user->getLastIdleTime()));
		return $menu;
	}
}

?>