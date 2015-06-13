<?php

class FeedRendererEntryRss extends FeedRenderer
{
	public function __construct(FeedWriterEntry $container) {
		parent::__construct($container);
	}
	
	public function render() {
		// item
		$this->dom = new DOMDocument('1.0', $this->container->getEncoding());
		$this->dom->formatOutput = true;
		$this->dom->substituteEntities = true;
		$entry = $this->dom->createElement('item');
		$this->dom->appendChild($entry);
		// attributes
		$this->renderTitle($this->dom, $entry);
		$this->renderDescription($this->dom, $entry);
		$this->renderContent($this->dom, $entry);
		$this->renderDateModified($this->dom, $entry);
		$this->renderLink($this->dom, $entry);
		$this->renderId($this->dom, $entry);
		$this->renderAuthors($this->dom, $entry);
		$this->renderEnclosure($this->dom, $entry);
		$this->renderCommentLink($this->dom, $entry);
		$this->renderCategories($this->dom, $entry);
		return $this->dom;		
	}

	protected function renderTitle(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getTitle() && !$this->getContainer()->getDescription()) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS 2.0 entry must contain a title or a description'));
			else
				return;
		}
		if (!$this->getContainer()->getTitle())
			return;		
		$title = $dom->createElement('title');
		$root->appendChild($title);
		$text = $dom->createTextNode($this->getContainer()->getTitle());
		$title->appendChild($text);		
	}
	
	protected function renderDescription(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getDescription() && !$this->getContainer()->getTitle()) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS 2.0 entry must contain a description or a title'));
			else
				return;
		}
		if (!$this->getContainer()->getDescription())
			return;
		$description = $dom->createElement('description');
		$root->appendChild($description);
		$text = $dom->createTextNode($this->getContainer()->getDescription());
		$description->appendChild($text);		
	}
	
	protected function renderContent(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getContent();
		if (!$value)
			return;
		if (!$this->getRootElement()->hasAttribute('xmlns:content'))
			$this->getRootElement()->setAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
		$content = $dom->createElement('content:encoded');
		$content->appendChild($dom->createCDATASection($value));
		$root->appendChild($content);
	}
	
	protected function renderDateModified(DOMDocument $dom, DOMElement $root) {
		$dateModified = $this->getContainer()->getDateModified();
		if (!$dateModified) {
			$dateCreated = $this->getContainer()->getDateCreated();
			if ($dateCreated) {
				$dateModified = $dateCreated;
				$this->getContainer()->setDateModified($dateCreated);
			} else {
				return;
			}
		}
		$modified = $dom->createElement('pubDate');
		$modified->appendChild($dom->createTextNode(DateTimeFormatter::format($dateModified, DATE_RSS)));
		$root->appendChild($modified);		
	}
	
	protected function renderLink(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getLink();
		if (!$value)
			return;
		$link = $dom->createElement('link');
		$link->appendChild($dom->createTextNode($value));
		$root->appendChild($link);		
	}
	
	protected function renderId(DOMDocument $dom, DOMElement $root) {
        if (!$this->getContainer()->getId() && !$this->getContainer()->getLink())
            return;
		$guid = $dom->createElement('guid');		
		if (!$this->getContainer()->getId())
			$this->getContainer()->setId($this->getContainer()->getLink());
		$guid->appendChild($dom->createTextNode($this->getContainer()->getId()));
		$validator = new ValidatorUrl();
		if (!$validator->validate($this->getContainer()->getId()))
			$guid->setAttribute('isPermalink', 'false');
		$root->appendChild($guid);		
	}
	
	protected function renderAuthors(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getAuthors();
		if (!$value || empty($value))
			return;
		foreach ($value as $entry) {
			$author = $dom->createElement('author');
			$name = $entry['name'];
			if (isset($entry['email']))
				$name = $entry['email'] . ' (' . $name . ')';
			$author->appendChild($dom->createTextNode($name));
			$root->appendChild($author);			
		}
	}
	
	protected function renderEnclosure(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getEnclosure();
		if (!$value || empty($value))
			return;
		if (!isset($value['type']) || !isset($value['length']) || (int) $value['length'] <= 0) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS 2.0 enclosure must contain a type and a length in bytes'));
			else
				return;
		}
		$enclosure = $dom->createElement('enclosure');
		$enclosure->setAttribute('type', $value['type']);
		$enclosure->setAttribute('length', $value['length']);
		$enclosure->setAttribute('uri', $value['uri']);
		$root->appendChild($enclosure);
	}
	
	protected function renderCommentLink(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getLink();
		if (!$value)
			return;
		$comments = $dom->createElement('comments');
		$comments->appendChild($dom->createTextNode($value));
		$root->appendChild($comments);	
		
	}
	
	protected function renderCategories(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getCategories();
		if (!$value || empty($value))
			return;
		foreach ($value as $entry) {
			$category = $dom->createElement('category');
			if (isset($entry['scheme']))
				$category->setAttribute('domain', $entry['scheme']);
			$category->appendChild($dom->createTextNode($entry['term']));
			$root->appendChild($category);
		}		
	}
}