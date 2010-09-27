<?php

class NavigationItem extends NavigationContainer
{
	protected $id;
	protected $label;
	protected $class;
	protected $title;
	protected $url;
	protected $params = array();
	protected $target;
	protected $roles;
	protected $order;
	protected $active = false;
	protected $visible = true;
	protected $parent;
	private $href;

	public static function factory($options) {
		if ($options instanceof Config)
			$options = $options->toArray();
		elseif (!is_array($options))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid options specification.'));
		$item = new self();
		foreach ($options as $name => $value)
			$item->__set($name, $value);
		return $item;
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = ($id !== null ? (string)$id : $id);
		return $this;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = ($label !== null ? (string)$label : $label);
		return $this;
	}

	public function getClass() {
		return $this->class;
	}

	public function setClass($class) {
		$this->class = ($class !== null ? (string)$class : $class);
		return $this;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = ($title !== null ? (string)$title : $title);
		return $this;
	}

	public function getHref() {
		if (isset($this->url)) {
			if ($this->href === null) {
				$app = Php2Go::app();
				if (strpos($this->url, '://') !== false || strpos($this->url, 'javascript:') !== false || strpos($this->url, '#') === 0)
					$this->href = $this->url;
				elseif (is_file($app->getRootPath() . DS . trim($this->url, '/')))
					$this->href = $app->getBaseUrl() . '/' . trim($url, '/');
				elseif (@strpos($this->url, $app->getBaseUrl()) === 0)
					$this->href = $this->url;
				else
					$this->href = $app->createUrl($this->url, $this->params);
			}
			return $this->href;
		}
		return null;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url) {
		if (is_array($url) && isset($url[0])) {
			$this->url = ltrim((string)array_shift($url), '/');
			$this->params = $url;
		} else {
			$this->url = ($url !== null ? ltrim((string)$url, '/') : $url);
		}
		$this->href = null;
		return $this;
	}

	public function getParams() {
		return $this->params;
	}

	public function setParams(array $params) {
		$this->params = $params;
		$this->href = null;
		return $this;
	}

	public function getTarget() {
		return $this->target;
	}

	public function setTarget($target) {
		$this->target = ($target !== null ? (string)$target : $target);
		return $this;
	}

	public function getRoles() {
		return $this->roles;
	}

	public function setRoles($roles) {
		if (is_string($roles))
			$this->roles = explode(',', $roles);
		elseif (is_array($roles))
			$this->roles = array_values($roles);
		else
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Roles must be an array or comma separated string.'));
	}

	public function getOrder() {
		return $this->order;
	}

	public function setOrder($order) {
		$this->order = ($target !== null ? (int)$order : $order);
		return $this;
	}

	public function getActive($deep=false) {
		return $this->isActive($deep);
	}

	public function isActive($deep=false) {
		if (!$this->active) {
			if (isset($this->url)) {
				$route = Php2Go::app()->getRoute();
				if (strpos($route, $this->url) === 0)
					return true;
			}
			if ($deep) {
				foreach ($this->items as $item) {
					if ($item->isActive(true))
						return true;
				}
			}
			return false;
		}
		return $this->active;
	}

	public function setActive($active) {
		$this->active = (bool)$active;
		return $this;
	}

	public function getVisible($deep=false) {
		return $this->isVisible($deep);
	}

	public function isVisible($deep=false) {
		if ($deep && isset($this->parent) && $this->parent instanceof NavigationItem) {
			if (!$this->parent->isVisible(true))
				return false;
		}
		return $this->visible;
	}

	public function setVisible($visible) {
		$this->visible = (bool)$visible;
		return $this;
	}

	public function getParent() {
		return $this->parent;
	}

	public function setParent(NavigationContainer $parent=null) {
		if ($parent === $this)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'A navigation item cannot have itself as parent.'));
		if ($this->parent === $parent)
			return $this;
		if ($this->parent !== null)
			$this->parent->removeItem($this);
		$this->parent = $parent;
		if ($this->parent !== null && !$this->parent->hasItem($this, false))
			$this->parent->addItem($this);
		return $this;
	}

	public function toArray() {
		return array(
			'id' => $this->id,
			'label' => $this->label,
			'class' => $this->class,
			'title' => $this->title,
			'url' => $this->url,
			'params' => $this->params,
			'target' => $this->target,
			'order' => $this->order,
			'active' => $this->active,
			'visible' => $this->visible,
			'pages' => parent::toArray()
		);
	}

	public function __toString() {
		return $this->label;
	}
}