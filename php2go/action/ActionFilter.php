<?php

abstract class ActionFilter implements ActionFilterInterface
{
	public function run(ActionFilterChain $chain) {
		if ($this->preFilter($chain)) {
			$chain->run();
			$this->postFilter($chain);
		}
	}
	
	protected function preFilter(ActionFilterChain $chain) {
		return true;
	}
	
	protected function postFilter(ActionFilterChain $chain) {		
	}
}