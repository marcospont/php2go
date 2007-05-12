<?php

/**
 * Perform the necessary imports
 */
import('php2go.auth.User');

/**
 * The class MyUser is a customized user container, extending the base container php2go.auth.User,
 * allowing the developer to add additional properties and methods
 */
class MyUser extends User 
{
	/**
	 * In the container ctor, you must explicitly call the parent ctor
	 */
	function MyUser() {
		parent::User();
	}
	
	/** 
	 * An example of additional method defined by the developer
	 */
	function getName() {
		return parent::getPropertyValue('name');
	}
}

?>