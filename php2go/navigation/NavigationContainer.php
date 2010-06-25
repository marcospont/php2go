<?php

abstract class NavigationContainer extends Component implements RecursiveIterator, Countable
{
	protected $items = array();
	protected $index = array();
	protected $dirtyIndex = false;
	
	public function __call($name, $args) {
		$matches = array();
		if (preg_match('/^(find(?:All)?By)(\w+)$/', $name, $matches))
			return $this->{$matches[1]}($matches[2], $args[0]);
		return parent::__call($name, $args);
	}
	
	public function getItems() {
		return $this->items;
	}
	
	public function hasItems() {
		return (count($this->index) > 0);
	}
	
	public function hasItem(NavigationItem $item, $deep=false) {
		if (array_key_exists($item->hashCode(), $this->index))
			return true;
		if ($deep) {
			foreach ($this->items as $child) {
				if ($child->hasItem($item, true))
					return true;
			}
		}
		return false;
	}
	
	public function setItems(array $items) {
		$this->removeItems();
		$this->addItems($items);
	}
	
	public function addItems($items) {
		if ($items instanceof Config)
			$items = $items->toArray();
		elseif (!is_array($items))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid navigation items.'));
		foreach ($items as $item)
			$this->addItem($item);
		return $this;
	}
	
	public function addItem($item) {
		if ($item === $this)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'A navigation item cannot have itself as parent.'));
		if (is_array($item) || $item instanceof Config) {
			$item = NavigationItem::factory($item);
		} elseif (!$item instanceof NavigationItem) {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid navigation item.'));
		}
		$hash = $item->hashCode();
		if (array_key_exists($hash, $this->index))
			return $this;
		$this->items[$hash] = $item;
		$this->index[$hash] = $item->getOrder();
		$this->dirtyIndex = true;
		$item->setParent($this);
		return $this;			
	}
	
	public function removeItems() {
		$this->items = array();
		$this->index = array();
	}
	
	public function removeItem($item) {
		if ($item instanceof NavigationItem) {
			$hash = $item->hashCode();
		} elseif (is_int($item)) {
			$this->sort();
			if (($hash = array_search($item, $this->index)) === false)
				return false;
		} else {
			return false;
		}
		if (isset($this->items[$hash])) {
			unset($this->items[$hash]);
			unset($this->index[$hash]);
			$this->dirtyIndex = true;
			return true;
		}
		return false;			
	}
	
	public function notifyOrderChange() {
		$this->dirtyIndex = true;
	}
	
	public function findBy($prop, $value) {
		$iterator = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::SELF_FIRST);
		foreach ($iterator as $item) {
			if ($item->{$prop} == $value)
				return $item;
		}
		return null;
	}
	
	public function findAllBy($prop, $value) {
		$result = array();
		$iterator = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::SELF_FIRST);
		foreach ($iterator as $item) {
			if ($item->{$prop} == $value)
				$result[] = $item;
		}
		return $result;
	}
	
	public function toArray() {
		$result = array();
		$this->dirtyIndex = true;
		$this->sort();
		$indexes = array_keys($this->index);
		foreach ($indexes as $hash)
			$result[] = $this->items[$hash]->toArray();
		return $result;
	}
	
	public function current() {
		$this->sort();
		current($this->index);
		return $this->items[key($this->index)];
	}
	
	public function key() {
		$this->sort();
		return key($this->index);
	}
	
	public function next() {
		$this->sort();
		next($this->index);
	}
	
	public function rewind() {
		$this->sort();
		reset($this->index);
	}
	
	public function valid() {
		$this->sort();
		return (current($this->index) !== false);
	}
	
	public function hasChildren() {
		return $this->hasItems();
	}
	
	public function getChildren() {
		$hash = key($this->index);
		if (isset($this->items[$hash]))
			return $this->items[$hash];
		return null;
	}
	
	public function count() {
		return count($this->index);
	}
	
	protected function sort() {
		if ($this->dirtyIndex) {
			$indexes = array();
			$index = 0;
			foreach ($this->items as $hash => $item) {
				$order = $item->getOrder();
				if ($order === null) {
					$indexes[$hash] = $index;
					$index++;
				} else {
					$indexes[$hash] = $order;
				}
			}
			asort($indexes);
			$this->index = $indexes;
			$this->dirtyIndex = false;
		}
	}
}