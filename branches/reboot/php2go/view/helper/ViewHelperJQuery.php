<?php

class ViewHelperJQuery extends ViewHelper
{
	protected $noConflict = false;
	protected $uiPath;
	protected $uiTheme;
	protected $onLoad = array();

	public function __construct(View $view) {
		parent::__construct($view);
		$this->uiPath = Php2Go::getPathAlias('php2go.library.jquery.jquery-ui');
	}

	public function enable() {
		if (!$this->view->head()->hasLibrary('jquery')) {
			$this->view->head()->addLibrary('jquery');
			if ($this->noConflict)
				$this->view->head()->addScript('$j = jQuery.noConflict();');
		}
		return $this;
	}

	public function enableUi() {
		$this->view->head()->addLibrary('jquery-ui');
		if ($this->uiTheme !== null) {
			$this->view->head()->addStyleSheet(
				$this->view->app->getAssetManager()->publish($this->uiPath . DS . 'css' . DS . $this->uiTheme) .
				'/jquery-ui.css', array(), 0
			);
		}
		return $this;
	}

	public function setNoConflict($noConflict) {
		$this->noConflict = (bool)$noConflict;
		return $this;
	}

	public function getUiPath() {
		return $this->uiPath;
	}

	public function getUiTheme() {
		return $this->uiTheme;
	}

	public function setUiTheme($theme) {
		if (!is_dir($this->uiPath . DS . 'css'. DS . $theme))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid theme: "%s".', array($theme)));
		$this->uiTheme = $theme;
		return $this;
	}

	public function handler() {
		return ($this->noConflict ? '$j' : '$');
	}

	public function selector($selector) {
		return Js::identifier(sprintf('%s("%s")', $this->handler(), $selector));
	}

	public function addCall($expression) {
		$this->enable();
		$args = func_get_args();
		if (sizeof($args) > 1) {
			$onLoad = "$(\"" . $expression . "\")";
			for ($i=1; $i<sizeof($args); $i+=2) {
				if (isset($args[$i+1]) && !empty($args[$i+1]))
					$onLoad .= '.' . $args[$i] . '(' . implode(',', array_map(array('Js', 'encode'), $args[$i+1])) . ')';
				else
					$onLoad .= '.' . $args[$i] . '()';
			}
			$onLoad .= ';';
		}
		$this->addOnLoad($onLoad);
	}

	public function addCallById($id) {
		$this->enable();
		$args = func_get_args();
		$args[0] = '#' . $id;
		call_user_func_array(array($this, 'addCall'), $args);
	}

	public function addOnLoad($script) {
		$this->enable();
		if (!empty($script))
			$this->onLoad[] = $script;
		return $this;
	}

	public function renderOnLoad() {
		if (!empty($this->onLoad))
			return $this->handler() . '(function($) {' . PHP_EOL . implode(PHP_EOL, $this->onLoad) . PHP_EOL . '});' . PHP_EOL;
		return '';
	}
}