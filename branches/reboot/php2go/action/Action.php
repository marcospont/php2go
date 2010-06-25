<?php

abstract class Action extends Component implements ActionInterface
{
	public $controller;
	public $id;
	
	public function __construct(Controller $controller, $id) {
		$this->controller = $controller;
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getController() {
		return $this->controller;
	}
	
	public function run() {		
	}
}