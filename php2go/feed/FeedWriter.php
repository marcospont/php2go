<?php

Php2go::import('php2go.feed.writer.FeedWriterAbstract');
Php2go::import('php2go.feed.writer.FeedWriterDeleted');
Php2go::import('php2go.feed.writer.FeedWriterEntry');
Php2go::import('php2go.feed.FeedRenderer');
Php2Go::import('php2go.feed.renderer.*');

class FeedWriter extends FeedWriterAbstract implements Iterator, Countable 
{
	const NAMESPACE_ATOM_10  = 'http://www.w3.org/2005/Atom';
	
	protected $entries = array();
	protected $entriesKey = 0;

	public function createEntry() {
		$entry = new FeedWriterEntry();
		if ($this->getEncoding())
			$entry->setEncoding($this->getEncoding());
		$entry->setType($this->getType());
		$this->entries[] = $entry;
		return $entry;
	}
	
	public function createDeleted() {
		$deleted = new FeedWriterDeleted();
		if ($this->getEncoding())
			$deleted->setEncoding($this->getEncoding());
		$deleted->setType($this->getType());
		$this->entries[] = $deleted;
		return $deleted;		
	}

	public function addEntry(FeedWriterEntry $entry) {
		$this->entries[] = $entry;
	}
	
	public function addDeleted(FeedWriterDeleted $deleted) {
		$this->entries[] = $deleted;
	}

	public function getEntry($index=0) {
		return (isset($this->entries[$index]) ? $this->entries[$index] : null);
	}
	
	public function removeEntry($index) {
		if (isset($this->entries[$index]))
			unset($this->entries[$index]);		
	}
	
	public function count() {
		return count($this->entries);
	}
	
	public function current() {
		return $this->entries[$this->key()];
	}
	
	public function key() {
		return $this->entriesKey;
	}
	
	public function next() {
		++$this->entriesKey;
	}
	
	public function rewind() {
		$this->entriesKey = 0;
	}
	
	public function valid() {
		return (0 <= $this->entriesKey && $this->entriesKey < $this->count());
	}
	
	public function render($type, $ignoreExceptions=false) {
		$this->setType(strtolower($type));
		$type = ucfirst($this->getType());
		if ($type !== 'Rss' && $type !== 'Atom')
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Feed type must be "rss" or "atom"'));
		$rendererClass = 'FeedRenderer' . $type;
		$renderer = new $rendererClass($this);
		if ($ignoreExceptions)
			$renderer->ignoreExceptions = true;
		return $renderer->render();		
	}	
}

class FeedWriterException extends Exception {
}