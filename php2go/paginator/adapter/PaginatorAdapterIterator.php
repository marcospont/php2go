<?php

class PaginatorAdapterIterator extends PaginatorAdapter 
{
	private $iterator;
	
	public function __construct(Iterator $iterator, array $options=array()) {
		if (!$iterator instanceof Countable)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The iterator must implement the Countable interface.'));
		$this->iterator = $iterator;
		$this->count = count($iterator);
	}
	
	public function count() {
		return $this->count;
	}
	
	public function getItems($offset, $pageSize) {
		return new LimitIterator($this->iterator, $offset, $pageSize);
	}
}