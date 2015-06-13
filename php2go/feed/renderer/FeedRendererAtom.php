<?php

Php2go::import('php2go.feed.renderer.atom.*');
Php2go::import('php2go.feed.renderer.entry.FeedRendererEntryAtom');

class FeedRendererAtom extends FeedRendererAtomAbstract
{
	public function __construct(FeedWriter $container) {
		parent::__construct($container);
	}	
	
	public function render() {
		if (!$this->container->getEncoding())
			$this->container->setEncoding(Php2Go::app()->getCharset());
		// root
		$this->dom = new DOMDocument('1.0', $this->container->getEncoding());
		$this->dom->formatOutput = true;
		$root = $this->dom->createElementNS(FeedWriter::NAMESPACE_ATOM_10, 'feed');
		$this->setRootElement($root);
		$this->dom->appendChild($root);
		$this->renderBaseUrl($this->dom, $root);
		$this->renderLanguage($this->dom, $root);
		$this->renderTitle($this->dom, $root);
		$this->renderDescription($this->dom, $root);
		$this->renderImage($this->dom, $root);
		$this->renderIcon($this->dom, $root);
		$this->renderDateModified($this->dom, $root);
		$this->renderGenerator($this->dom, $root);
		$this->renderLink($this->dom, $root);
		$this->renderFeedLinks($this->dom, $root);
		$this->renderId($this->dom, $root);
		$this->renderAuthors($this->dom, $root);
		$this->renderCopyright($this->dom, $root);
		$this->renderCategories($this->dom, $root);
		$this->renderHubs($this->dom, $root);
		// entries
		foreach ($this->container as $entry) {
			if ($entry instanceof FeedWriterEntry) {
				$renderer = new FeedRendererEntryAtom($entry);
			} else {
				if (!$this->dom->documentElement->hasAttribute('xmlns:at'))
					$this->dom->documentElement->setAttribute('xmlns:at', 'http://purl.org/atompub/tombstones/1.0');
				$renderer = new FeedRendererAtomDeleted($entry);
			}
			if ($this->container->getEncoding())
				$entry->setEncoding($this->container->getEncoding());
			if ($this->ignoreExceptions)
				$renderer->ignoreExceptions = true;
			$renderer->setType($this->getType());
			$renderer->setRootElement($this->dom->documentElement);
			$renderer->render();
			$root->appendChild($this->dom->importNode($renderer->getDocumentElement(), true));
		}
		return $this->dom;
	}
}