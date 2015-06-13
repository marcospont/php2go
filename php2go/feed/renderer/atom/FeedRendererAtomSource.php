<?php

class FeedRendererAtomSource extends FeedRendererAtomAbstract
{
	public function __construct(FeedWriterSource $container) {
		parent::__construct($container);
	}
	
	public function render() {
		if (!$this->container->getEncoding())
			$this->container->setEncoding(Php2Go::app()->getCharset());
		// root
		$this->dom = new DOMDocument('1.0', $this->container->getEncoding());
		$this->dom->formatOutput = true;
		$root = $this->dom->createElement('source');
		$this->setRootElement($root);
		$this->renderBaseUrl($this->dom, $root);
		$this->renderLanguage($this->dom, $root);
		$this->renderTitle($this->dom, $root);
		$this->renderDescription($this->dom, $root);
		$this->renderDateModified($this->dom, $root);
		$this->renderGenerator($this->dom, $root);
		$this->renderLink($this->dom, $root);
		$this->renderFeedLinks($this->dom, $root);
		$this->renderId($this->dom, $root);
		$this->renderAuthors($this->dom, $root);
		$this->renderCopyright($this->dom, $root);
		$this->renderCategories($this->dom, $root);		
	}
	
	protected function renderGenerator(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getGenerator();
		if (!$value || empty($value))
			return;
		$generator = $dom->createElement('generator');
		$generator->appendChild($dom->createTextNode($value['name']));
		if (array_key_exists('version', $value))
			$generator->setAttribute('version', $value['version']);
		if (array_key_exists('uri', $value))
			$generator->setAttribute('uri', $value['uri']);
		$root->appendChild($generator);			
	}
}