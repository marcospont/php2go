<?php

class ConsoleApplication extends Application
{
	protected $commandPath;
	protected $commands = array();
	protected $runner;

	public function __construct(array $options=array()) {
		parent::__construct($options);
		if (!isset($_SERVER['argv']))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'This script must be run from the command line.'));
		$this->runner = new ConsoleRunner();
		$this->runner->addCommandPath($this->getCommandPath());
		$this->runner->setCommands($this->commands);
	}

	public function getCommandPath() {
		if ($this->commandPath === null)
			$this->commandPath = $this->getBasePath() . DS . 'commands';
		return $this->commandPath;
	}

	public function setCommandPath($path) {
		$path = rtrim($path, '/\\');
		if (($this->commandPath = realpath($path)) === false || !is_dir($this->commandPath))
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Command path "%s" is not a valid directory.', array($path)));
	}

	public function setCommands(array $commands) {
		$this->commands = $commands;
	}

	public function getCommandRunner() {
		return $this->runner;
	}

	public function processRequest() {
		$this->runner->run();
	}

	protected function displayException(Exception $exception) {
		echo $exception;
	}

	protected function displayError($code, $message, $file, $line) {
		echo 'Error ' . $code . ' - ' . $message . "\n";
		echo 'in ' . $file . ', line ' . $line . "\n";
		debug_print_backtrace();
	}

	protected function parseOptions(array &$options) {
		parent::parseOptions($options);
		foreach (array_keys($options) as $name) {
			switch ($name) {
				case 'commandPath' :
				case 'commands' :
					$method = 'set' . $name;
					$this->{$method}(Util::consumeArray($options, $name));
					break;
			}
		}
	}
}