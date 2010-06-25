<?php

Php2Go::import('php2go.view.helper.placeholder.*');

class ViewHelperPlaceholder extends ViewHelper
{
	const SET = 'set';
	const PREPEND = 'prepend';
	const APPEND = 'append';

	protected $registry;
	protected $containerStack = array();

	public function __construct(View $view) {
		parent::__construct($view);
		$this->registry = ViewHelperPlaceholderRegistry::instance();
	}

	public function placeholder($id=null) {
		if ($id !== null)
			return $this->registry->get($id);
		return $this;
	}

	public function getContainer($id) {
		return $this->registry->get($id);
	}

	public function getRegistry() {
		return $this->registry;
	}

	public function prepend($id, $content) {
		$this->registry->get($id)->prepend($content);
		return $this;
	}

	public function set($id, $content) {
		$this->registry->get($id)->set($content);
		return $this;
	}

	public function begin($id, $type=self::APPEND, $key=null) {
		$this->containerStack[] = $container = $this->registry->get($id);
		$container->begin($type, $key);
	}

	public function end() {
		if (($container = array_pop($this->containerStack)) !== null)
			$container->end();
		else
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Unbalanced placeholder capture.'));
	}
}