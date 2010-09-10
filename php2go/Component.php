<?php

class Component
{
	private $events = array();
	private $listeners = array();
	private $behaviors = array();

	public function __get($name) {
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return $this->{$getter}();
		if ($this->hasEvent($name)) {
			$evtName = strtolower($name);
			if (isset($this->listeners[$evtName]))
				return $this->listeners[$evtName];
		}
		if (isset($this->behaviors[$name]))
			return $this->behaviors[$name];
		if (!empty($this->behaviors)) {
			foreach ($this->behaviors as $behavior) {
				if ($behavior->isEnabled() && property_exists($behavior, $name))
					return $behavior->{$name};
			}
		}
		return null;
	}

	public function __set($name, $value) {
		$setter = 'set' . $name;
		if (method_exists($this, $setter))
			return $this->{$setter}($value);
		if ($this->hasEvent($name)) {
			$evtName = strtolower($name);
			if (!isset($this->listeners[$evtName]))
				$this->listeners[$evtName] = array();
			$this->listeners[$evtName][] = $value;
			return true;
		} elseif (!empty($this->behaviors)) {
			foreach ($this->behaviors as $behavior) {
				if ($behavior->isEnabled() && property_exists($behavior, $name)) {
					$behavior->{$name} = $value;
					return true;
				}
			}
		}
		if (method_exists($this, 'get' . $name))
			throw new ComponentException(__(PHP2GO_LANG_DOMAIN, 'Property "%s.%s" is read only.', array(get_class($this), $name)));
		throw new ComponentException(__(PHP2GO_LANG_DOMAIN, 'Component "%s" does not have property "%s".', array(get_class($this), $name)));
	}

	public function __isset($name) {
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return ($this->{$getter}() !== null);
		if ($this->hasEventListener($name))
			return true;
		if (!empty($this->behaviors)) {
			if (isset($this->behaviors[$name]))
				return true;
			foreach ($this->behaviors as $behavior) {
				if ($behavior->isEnabled() && property_exists($behavior, $name))
					return true;
			}
		}
		return false;
	}

	public function __unset($name) {
		$setter = 'set' . $name;
		if (method_exists($this, $setter)) {
			$this->{$setter}(null);
		} elseif ($this->hasEvent($name)) {
			unset($this->listeners[strtolower($name)]);
		} else {
			if (!empty($this->behaviors)) {
				if (isset($this->behaviors[$name])) {
					$this->detachBehavior($name);
					return;
				}
				foreach ($this->behaviors as $behavior) {
					if ($behavior->isEnabled()) {
						if (property_exists($behavior, $name)) {
							$behavior->{$name} = null;
							return;
						} elseif (method_exists($behavior, 'set' . $name)) {
							$behavior->{'set' . $name}(null);
							return;
						}
					}
				}
			}
			if (method_exists($this, 'get' . $name)) {
				throw new ComponentException(__(PHP2GO_LANG_DOMAIN, 'Property "%s.%s" is read only.', array(get_class($this), $name)));
			}
		}
	}

	public function __call($name, $args) {
		if (!empty($this->behaviors)) {
			foreach ($this->behaviors as $behavior) {
				if ($behavior->isEnabled() && method_exists($behavior, $name))
					return call_user_func_array(array($behavior, $name), $args);
			}
		}
		if (class_exists('Closure', false) && $this->{$name} instanceof Closure)
			return call_user_func_array($this->{$name}, $args);
		throw new BadMethodCallException(__(PHP2GO_LANG_DOMAIN, 'Component "%s" does not have the method "%s".', array(get_class($this), $name)));
	}

	public function hashCode() {
		return spl_object_hash($this);
	}

	public function hasEvent($evtName) {
		return isset($this->events[strtolower($evtName)]);
	}

	public function hasEventListener($evtName) {
		$evtName = strtolower($evtName);
		return (isset($this->listeners[$evtName]) && sizeof($this->listeners[$evtName]) > 0);
	}

	public function addEventListener($evtName, $listener) {
		$listeners =& $this->getEventListeners($evtName);
		$listeners[] = $listener;
	}

	public function removeEventListener($evtName, $listener) {
		$listeners =& $this->getEventListeners($evtName);
		if (($index = array_search($listener, $listeners, true)) !== false)
			$listeners = array_splice($listeners, $index, 1);
	}

	public function raiseEvent($evtName, $evt=null) {
		if ($this->hasEvent($evtName)) {
			$evtName = strtolower($evtName);
			if (isset($this->listeners[$evtName])) {
				foreach ($this->listeners[$evtName] as $listener) {
					if (is_string($listener)) {
						$result = call_user_func($listener, $evt);
					} elseif (is_callable($listener, true)) {
						if (is_array($listener)) {
							list($object, $method) = $listener;
							if (is_string($object))
								$result = call_user_func(array($object, $method), $evt);
							elseif (method_exists($object, $method))
								$result = $object->{$method}($evt);
							else
								throw new ComponentException(__(PHP2GO_LANG_DOMAIN, 'Event "%s.%s" has an invalid listener: "%s"', array(get_class($this), $evtName, $listener)));
						} else {
							$result = call_user_func($listener, $evt);
						}
					} else {
						throw new ComponentException(__(PHP2GO_LANG_DOMAIN, 'Event "%s.%s" has an invalid listener: "%s"', array(get_class($this), $evtName, $listener)));
					}
					if ($result === false) {
						if ($evt instanceof Event)
							$evt->handled = true;
						return false;
					} elseif ($evt instanceof Event && $evt->handled) {
						return true;
					}
				}
			}
			if (method_exists($this, $evtName)) {
				$result = $this->{$evtName}($evt);
				if ($result === false) {
					if ($evt instanceof Event)
						$evt->handled = true;
					return false;
				} elseif ($evt instanceof Event && $evt->handled) {
					return true;
				}
			}
			return true;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Event "%s.%s" is not defined.', array(get_class($this), $evtName)));
		}
	}

	public function attachBehaviors(array $behaviors) {
		foreach ($behaviors as $name => $behavior)
			$this->attachBehavior($name, $behavior);
	}

	public function detachBehaviors() {
		foreach ($this->behaviors as $name => $behavior)
			$this->detachBehavior($name);
		$this->behaviors = array();
	}

	public function enableBehaviors() {
		foreach ($this->behaviors as $name => $behavior)
			$behavior->setEnabled(true);
	}

	public function disableBehaviors() {
		foreach ($this->behaviors as $name => $behavior)
			$behavior->setEnabled(false);
	}

	public function enableBehavior($name) {
		if ($this->behaviors[$name])
			$this->behaviors[$name]->setEnabled(true);
	}

	public function disableBehavior($name) {
		if ($this->behaviors[$name])
			$this->behaviors[$name]->setEnabled(false);
	}

	public function attachBehavior($name, $behavior) {
		if (!($behavior instanceof BehaviorInterface)) {
			if (is_array($behavior) && isset($behavior[0])) {
				$config = array(
					'class' => $this->resolveBehavior(array_shift($behavior)),
					'parent' => 'Behavior',
					'options' => $behavior
				);
			} elseif (is_string($behavior)) {
				$config = array(
					'class' => $this->resolveBehavior($behavior),
					'parent' => 'Behavior'
				);
			} else {
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid behavior specification.'));
			}
			$behavior = Php2Go::newInstance($config);
		}
		$behavior->setEnabled(true);
		$behavior->attach($this);
		return $this->behaviors[$name] = $behavior;
	}

	public function detachBehavior($name) {
		if (isset($this->behaviors[$name])) {
			$this->behaviors[$name]->detach($this);
			$behavior = $this->behaviors[$name];
			unset($this->behaviors[$name]);
			return $behavior;
		}
		return null;
	}

	protected function resolveBehavior($class) {
		return $class;
	}

	protected function registerEvents($evts) {
		$evts = (is_string($evts) ? preg_split('/[\s,]+/', $attrs, -1, PREG_SPLIT_NO_EMPTY) : $evts);
		if (is_array($evts)) {
			foreach ($evts as $evtName)
				$this->events[strtolower($evtName)] = true;
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid events specification.'));
		}
	}

	protected function &getEventListeners($evtName) {
		if ($this->hasEvent($evtName)) {
			$evtName = strtolower($evtName);
			if (!isset($this->listeners[$evtName]))
				$this->listeners[$evtName] = array();
			return $this->listeners[$evtName];
		}
		throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Event "%s.%s" is not defined.', array(get_class($this), $evtName)));
	}
}

class ComponentException extends Exception
{
}