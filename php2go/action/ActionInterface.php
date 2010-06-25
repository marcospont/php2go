<?php

interface ActionInterface {
	public function getId();
	public function getController();
	public function run();
}