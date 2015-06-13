<?php

abstract class FeedRenderer extends Component
{
	protected $container = null;
	protected $type = null;
	protected $encoding = null;
	protected $dom = null;
	protected $rootElement = null;
	protected $ignoreExceptions = false;

	protected function __construct($container) {
		$this->container = $container;
		$this->encoding = Php2Go::app()->getCharset();
		$this->setType($container->getType());		
	}
	
	abstract public function render();
	
	public function getContainer() {
		return $this->container;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getEncoding() {
		return $this->encoding;
	}
	
	public function setEncoding($encoding) {
		$this->encoding = $encoding;
	}
	
	public function getDom() {
		return $this->dom;
	}
	
	public function getDocumentElement() {
		return $this->dom->documentElement;
	}
	
	public function getRootElement() {
		return $this->rootElement;
	}
	
	public function setRootElement(DOMElement $element) {
		$this->rootElement = $element;
	}
	
	public function getIgnoreExceptions() {
		return $this->ignoreExceptions;
	}
	
	public function setIgnoreExceptions($ignoreExceptions) {
		$this->ignoreExceptions = !!$ignoreExceptions;
	}		
}