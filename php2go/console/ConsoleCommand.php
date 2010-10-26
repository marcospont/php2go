<?php

abstract class ConsoleCommand extends Component
{
	protected $id;
	protected $runner;

	public function __construct($id, ConsoleRunner $runner) {
		$this->id = $id;
		$this->runner = $runner;
	}

	public function getId() {
		return $this->id;
	}

	public function getCommandRunner() {
		return $this->runner;
	}

	public function getHelp() {
		return __(PHP2GO_LANG_DOMAIN, 'Usage:') . ' ' . $this->getCommandRunner()->getScriptName() . ' ' . $this->id;
	}

	abstract public function run(array $args);
}