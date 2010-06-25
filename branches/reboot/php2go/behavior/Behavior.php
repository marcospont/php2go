<?php

abstract class Behavior extends Component implements BehaviorInterface 
{
	protected $owner;
	protected $enabled = false;
	
	public function events() {
		return array();
	}
	
	public function getOwner() {
		return $this->owner;
	}
	
	public function attach(Component $owner) {
		$this->owner = $owner;
		foreach ($this->events() as $evtName => $listener)
			$this->owner->addEventListener($evtName, array($this, $listener));
	}
	
	public function detach(Component $owner) {
		foreach ($this->events() as $evtName => $listener)
			$this->owner->removeEventListener($evtName, array($this, $listener));
		$this->owner = null;
	}
	
	public function isEnabled() {
		return $this->enabled;
	}

	public function setEnabled($enabled) {
		$this->enabled = (bool)$enabled;
	}	
}