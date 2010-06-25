<?php

interface BehaviorInterface {
	public function getOwner();
	public function attach(Component $owner);
	public function detach(Component $owner);
	public function isEnabled();
	public function setEnabled($enabled);
}