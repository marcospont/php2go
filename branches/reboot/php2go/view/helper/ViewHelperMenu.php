<?php

class ViewHelperMenu extends ViewHelper
{
	protected $container = null;
	protected $class;
	protected $activeClass;
	protected $activeBranchOnly = false;
	protected $renderParents = true;

	public function menu(NavigationContainer $container=null) {
		if ($container !== null)
			$this->setContainer($container);
		return $this;
	}

	public function getContainer() {
		if ($this->container === null)
			$this->container = new Navigation();
		return $this->container;
	}

	public function setContainer(NavigationContainer $container) {
		$this->container = $container;
		return $this;
	}

	public function getClass() {
		return $this->class;
	}

	public function setClass($class) {
		$this->class = (string)$class;
		return $this;
	}

	public function getActiveClass() {
		return $this->activeClass;
	}

	public function setActiveClass($activeClass) {
		$this->activeClass = (string)$activeClass;
		return $this;
	}

	public function getActiveBranchOnly() {
		return $this->activeBranchOnly;
	}

	public function setActiveBranchOnly($activeBranchOnly) {
		$this->activeBranchOnly = (bool)$activeBranchOnly;
		return $this;
	}

	public function getRenderParents() {
		return $this->renderParents;
	}

	public function setRenderParents($renderParents) {
		$this->renderParents = (bool)$renderParents;
		return $this;
	}

	public function active(NavigationContainer $container=null, array $options=array()) {
		return $this->render($container, array_merge($options, array(
			'activeBranchOnly' => true,
			'renderParents' => false
		)));
	}

	public function render(NavigationContainer $container=null, array $options=array()) {
		if ($container === null)
			$container = $this->getContainer();
		$options = array_merge(array(
			'class' => $this->class,
			'activeClass' => $this->activeClass,
			'activeBranchOnly' => $this->activeBranchOnly,
			'renderParents' => $this->renderParents
		), $options);
		if ($options['activeBranchOnly'] && !$options['renderParents'])
			return $this->renderActive($container, $options);
		else
			return $this->renderMenu($container, $options);
	}

	public function toString() {
		return $this->render();
	}

	protected function renderActive(NavigationContainer $container, $options) {
		if (!($active = $this->findActive($container)))
			return '';
		if (!$active['item']->hasItems())
			$active['item'] = $active['item']->getParent();
		$result = '';
		foreach ($active['item'] as $item) {
			if (!$this->accept($item))
				continue;
			$attrs = ($item->isActive(true) && isset($options['activeClass']) ? array('class' => $options['activeClass']) : array());
			$result .= '<li' . $this->renderAttrs($attrs) . '>' . $this->renderItem($item) . '</li>';
		}
		if ($result != '') {
			$attrs = array();
			if (isset($options['id']))
				$attrs['id'] = $options['id'];
			if (isset($options['class']))
				$attrs['class'] = $options['class'];
			return '<ul' . $this->renderAttrs($attrs) . '>' . $result . '</ul>';
		}
		return $result;
	}

	protected function renderMenu(NavigationContainer $container, $options) {
		$result = '';
		$prevDepth = -1;
		if (($found = $this->findActive($container))) {
			$foundItem = $found['item'];
			$foundDepth = $found['depth'];
		} else {
			$foundItem = null;
		}
		$iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::SELF_FIRST);
		foreach ($iterator as $item) {
			$depth = $iterator->getDepth();
			$active = $item->isActive(true);
			if (!$this->accept($item)) {
				continue;
			} elseif ($options['activeBranchOnly'] && !$active) {
				$accept = false;
				if ($foundItem) {
					if ($foundItem->hasItem($item)) {
						$accept = true;
					} elseif ($foundItem->getParent()->hasItem($item)) {
						if (!$foundItem->hasItems())
							$accept = true;
					}
				}
				if (!$accept)
					continue;
			}
			if ($depth > $prevDepth) {
				$attrs = array();
				if ($depth == 0) {
					if (isset($options['id']))
						$attrs['id'] = $options['id'];
					if (isset($options['class']))
						$attrs['class'] = $options['class'];
				}
				$result .= '<ul' . $this->renderAttrs($attrs) . '>';
			} elseif ($prevDepth > $depth) {
				for ($i=$prevDepth; $i>$depth; $i--)
					$result .= '</li></ul>';
				$result .= '</li>';
			} else {
				$result .= '</li>';
			}
			$attrs = ($active && isset($options['activeClass']) ? array('class' => $options['activeClass']) : array());
			$result .= '<li' . $this->renderAttrs($attrs) . '>';
			$result .= $this->renderItem($item);
			$prevDepth = $depth;
		}
		if ($result != '') {
			for ($i=$prevDepth; $i>=0; $i--) {
				$result .= '</li></ul>';
			}
		}
		return $result;
	}

	protected function renderItem(NavigationItem $item) {
		$label = $item->getLabel();
		$title = $item->getTitle();
		$attrs = array(
			'id' => $item->getId(),
			'title' => $title,
			'class' => $item->getClass()
		);
		if (($href = $item->getHref())) {
			$attrs['href'] = $href;
			$attrs['target'] = $item->getTarget();
			return '<a' . $this->renderAttrs($attrs) . '>' . $this->view->escape($label) . '</a>';
		} else {
			return '<a href="#"' . $this->renderAttrs($attrs) . '>' . $this->view->escape($label) . '</a>';
		}
	}

	protected function findActive(NavigationContainer $container) {
		$found = null;
		$foundDepth = -1;
		$iterator = new RecursiveIteratorIterator($container, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($iterator as $item) {
			$depth = $iterator->getDepth();
			if (!$this->accept($item))
				continue;
			if ($item->isActive(false) && $depth > $foundDepth) {
				$found = $item;
				$foundDepth = $depth;
			}
		}
		if ($found)
			return array('item' => $found, 'depth' => $foundDepth);
		return null;
	}

	protected function accept(NavigationItem $item, $deep=true) {
		$result = true;
		if (!$item->isVisible(false))
			$result = false;
		if ($result && $deep) {
			$parent = $item->getParent();
			if ($parent instanceof NavigationItem)
				$result = $this->accept($parent, true);
		}
		return $result;
	}
}