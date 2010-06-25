<?php

class Navigation extends NavigationContainer
{
	public function __construct($items=null) {
		if ($items !== null)
			$this->addItems($items);
	}
}