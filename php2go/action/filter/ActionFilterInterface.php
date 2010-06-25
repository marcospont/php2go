<?php

interface ActionFilterInterface {
	public function run(ActionFilterChain $chain);
}