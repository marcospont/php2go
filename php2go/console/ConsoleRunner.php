<?php

class ConsoleRunner extends Component
{
	protected $app;
	protected $scriptName;
	protected $args = array();
	protected $commands = array();

	public function __construct() {
		$args = $_SERVER['argv'];
		$this->app = Php2Go::app();
		$this->scriptName = array_shift($args);
		$this->args = $args;
	}

	public function getApp() {
		return $this->app;
	}

	public function getScriptName() {
		return $this->scriptName;
	}

	public function getCommands() {
		return $this->commands;
	}

	public function addCommandPath($path) {
		$path = realpath($path);
		if ($path !== false && is_dir($path)) {
			$dir = opendir($path);
			while (($name = readdir($dir)) !== false) {
				$file = $path . DS . $name;
				if (!strcasecmp(substr($name, -11), 'Command.php') && is_file($file))
					$this->setCommand(strtolower(substr($name, 0, -11)), $file);
			}
		}
	}

	public function setCommands(array $commands) {
		foreach ($commands as $id => $options)
			$this->setCommand($id, $options);
	}

	public function setCommand($id, $options) {
		$command = array();
		if (is_string($options)) {
			if (strpos($options, '/') !== false || strpos($options, '\\') !== false) {
				$command['path'] = $options;
				$command['class'] = preg_replace('/\..+$/', '', basename($options));
			} else {
				$command['class'] = $options;
			}
		} elseif (is_array($options)) {
			if (!isset($this->commands[$id]) && !isset($options['class']))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'A class is required when the command is not on the command path.'));
			if (isset($options['class']))
				$command['class'] = Util::consumeArray($options, 'class');
			$command['options'] = $options;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid command specification.'));
		}
		$command['parent'] = 'ConsoleCommand';
		if (isset($this->commands[$id]))
			$this->commands[$id] = array_merge($this->commands[$id], $command);
		else
			$this->commands[$id] = $command;
	}

	public function run() {
		$commandId = array_shift($this->args);
		if (($command = $this->createCommand($commandId)))
			$command->run($this->args);
	}

	protected function createCommand($id) {
		$id = strtolower($id);
		if (isset($this->commands[$id])) {
			$command = $this->commands[$id];
			if (strpos($command['class'], '.') === false && isset($command['path']))
				require_once($command['path']);
			return Php2Go::newInstance($command, $id, $this);
		}
		return null;
	}
}