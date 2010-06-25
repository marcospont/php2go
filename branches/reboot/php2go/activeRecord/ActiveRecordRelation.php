<?php

abstract class ActiveRecordRelation
{
	protected $name;
	protected $options;
	protected $collection = false;
	protected $requiredOptions = array();
	
	public function __construct($name, array $options) {
		$this->name = $name;
		$this->options = $options;
		$this->validateOptions();
	}
	
	public function validateOptions() {
		foreach ($this->requiredOptions as $opt) {
			if (!array_key_exists($opt, $this->options))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The option "%s" is required on a "%s" relation.', array($opt, $this->getType())));
		}
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getType() {
		return Inflector::variablize(str_replace('ActiveRecordRelation', '', get_class($this)));
	}
	
	public function getClass() {
		return $this->options['class'];
	}
	
	public function getOption($name, $fallaback=null) {
		return (array_key_exists($name, $this->options) ? $this->options[$name] : $fallaback);
	}
	
	public function setOption($name, $value) {
		$this->options[$name] = $value;
	}
	
	public function isCollection() {
		return $this->collection;
	}
	
	abstract public function find(ActiveRecord $base);
	
	abstract public function save(ActiveRecord $base, $data);
	
	abstract public function delete(ActiveRecord $base);
}