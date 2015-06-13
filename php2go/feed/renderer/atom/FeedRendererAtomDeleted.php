<?php

class FeedRendererAtomDeleted extends FeedRenderer
{
	public function __construct(FeedWriterDeleted $container) {
		parent::__construct($container);
	}	
	
	public function render() {
		// root
		$this->dom = new DOMDocument('1.0', $this->container->getEncoding());
		$this->dom->formatOutput = true;
		$entry = $this->dom->createElement('at:deleted-entry');
		$this->dom->appendChild($entry);
		$entry->setAttribute('href', $this->container->getReference());
		$entry->setAttribute('when', DateTimeFormatter::format($this->container->getWhen(), DATE_ISO8601));
		$this->renderBy($this->dom, $entry);
		$this->renderComment($this->dom, $entry);
		return $this->dom;
	}
	
	protected function renderBy(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getBy();
		if (!$value || empty($value))
			return;
		$author = $dom->createElement('at:by');
		$name = $dom->createElement('name');
		$name->appendChild($dom->createTextNode($value['name']));
		$author->appendChild($name);
		if (array_key_exists('email', $value)) {
			$email = $dom->createElement('email');
			$email->appendChild($dom->createTextNode($value['email']));
			$author->appendChild($email);
		}
		if (array_key_exists('uri', $value)) {
			$uri = $dom->createElement('uri');
			$uri->appendChild($dom->createTextNode($value['uri']));
			$author->appendChild($uri);
		}
		$root->appendChild($author);		
	}
	
	protected function renderComment(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getComment();
		if (!$value)
			return;
		$comment = $dom->createElement('at:comment');
		$comment->setAttribute('type', 'html');
		$comment->appendChild($dom->createCDATASection($value));
		$root->appendChild($comment);		
	}	
}