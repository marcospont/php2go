<?php

Php2go::import('php2go.feed.renderer.entry.FeedRendererEntryRss');

class FeedRendererRss extends FeedRenderer
{
	const MAX_IMAGE_HEIGHT = 400;
	const MAX_IMAGE_WIDTH = 144;
	
	public function __construct(FeedWriter $container) {
		parent::__construct($container);
	}
	
	public function render() {
		if (!$this->container->getEncoding())
			$this->container->setEncoding(Php2Go::app()->getCharset());
		// root
		$this->dom = new DOMDocument('1.0', $this->container->getEncoding());
		$this->dom->formatOutput = true;
		$this->dom->substituteEntities = true;
		$rss = $this->dom->createElement('rss');
		$this->setRootElement($rss);
		$rss->setAttribute('version', '2.0');
		// channel
		$channel = $this->dom->createElement('channel');
		$rss->appendChild($channel);
		$this->dom->appendChild($rss);
		$this->renderBaseUrl($this->dom, $channel);		
		$this->renderLanguage($this->dom, $channel);
		$this->renderTitle($this->dom, $channel);
		$this->renderDescription($this->dom, $channel);
		$this->renderImage($this->dom, $channel);
		$this->renderDateModified($this->dom, $channel);
		$this->renderLastBuildDate($this->dom, $channel);
		$this->renderGenerator($this->dom, $channel);
		$this->renderLink($this->dom, $channel);
		$this->renderAuthors($this->dom, $channel);
		$this->renderCopyright($this->dom, $channel);
		$this->renderCategories($this->dom, $channel);
		// entries
		foreach ($this->container as $entry) {
			if ($entry instanceof FeedWriterEntry)
				$renderer = new FeedRendererEntryRss($entry);
			else
				continue;
			if ($this->container->getEncoding())
				$entry->setEncoding($this->container->getEncoding());
			if ($this->ignoreExceptions)
				$renderer->ignoreExceptions = true;
			$renderer->setType($this->getType());
			$renderer->setRootElement($this->dom->documentElement);
			$renderer->render();
			$channel->appendChild($this->dom->importNode($renderer->getDocumentElement(), true));
		}
		return $this->dom;
	}
	
	protected function renderBaseUrl(DOMDocument $dom, DOMElement $root) {
		$baseUrl = $this->getContainer()->getBaseUrl();
		if (!$baseUrl)
			return;
		$root->setAttribute('xml:base', $baseUrl);
	}
	
	protected function renderLanguage(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getLanguage();
		if (!$value)
			return;
		$language = $dom->createElement('language');
		$root->appendChild($language);
		$language->nodeValue = $value;
	}
	
	protected function renderTitle(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getTitle()) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS 2.0 feed must contain a title'));
			else
				return;
		}
		$title = $dom->createElement('title');
		$root->appendChild($title);
		$text = $dom->createTextNode($this->getContainer()->getTitle());
		$title->appendChild($text);
	}
	
	protected function renderDescription(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getDescription()) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS 2.0 feed must contain a description'));
			else
				return;
		}
		$description = $dom->createElement('description');
		$root->appendChild($description);
		$text = $dom->createTextNode($this->getContainer()->getDescription());
		$description->appendChild($text);
	}
	
	protected function renderImage(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getImage();
		if (!$value)
			return;
		if (!isset($value['title']) || Util::isEmpty($value['title'])) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS feed images must contain a title'));
			else
				return;
		}		
		if (!isset($value['link']))
			$value['link'] = $value['uri'];
		$validator = new ValidatorUrl();
		if (!$validator->validate($value['link'])) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS feed image link must be a non-empty valid URL'));
			else
				return;
		}
		$image = $dom->createElement('image');
		$root->appendChild($image);
		$url = $dom->createElement('url');
		$url->appendChild($dom->createTextNode($value['uri']));
		$title = $dom->createElement('title');
		$title->appendChild($dom->createTextNode($value['title']));
		$link = $dom->createElement('link');
		$link->appendChild($dom->createTextNode($value['link']));
		$image->appendChild($url);
		$image->appendChild($title);
		$image->appendChild($link);
		if (isset($value['height'])) {
			if (!is_int($value['height']) || $value['height'] > self::MAX_IMAGE_HEIGHT) {
				if (!$this->ignoreExceptions)
					throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS feed images height must be at most %d pixels', array(self::MAX_IMAGE_HEIGHT)));
				else
					return;
			}
			$height = $dom->createElement('height');
			$height->appendChild($dom->createTextNode($value['height']));
			$image->appendChild($height);
		}
		if (isset($value['width'])) {
			if (!is_int($value['width']) || $value['width'] > self::MAX_IMAGE_WIDTH) {
				if (!$this->ignoreExceptions)
					throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS feed images width must be at most %d pixels', array(self::MAX_IMAGE_WIDTH)));
				else
					return;
			}
			$width = $dom->createElement('width');
			$width->appendChild($dom->createTextNode($value['width']));
			$image->appendChild($width);
		}
		if (isset($value['description']) && !Util::isEmpty($value['description'])) {
			$description = $dom->createElement('description');
			$description->appendChild($dom->createTextNode($value['description']));
			$image->appendChild($description);
		}
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
	
	protected function renderLastBuildDate(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getLastBuildDate();
		if (!$value)
			return;
		$lastBuildDate = $dom->createElement('lastBuildDate');
		$lastBuildDate->appendChild($dom->createTextNode(DateTimeFormatter::format($value, DATE_RSS)));
		$root->appendChild($lastBuildDate);
	}
	
	protected function renderGenerator(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getGenerator())
			$this->getContainer()->setGenerator('Php2go Feed Writer', PHP2GO_VERSION, 'http://www.php2go.com.br');
		$value = $this->getContainer()->getGenerator();
		$name = $value['name'];
		if (array_key_exists('version', $value))
			$name .= ' ' . $value['version'];
		if (array_key_exists('uri', $value))
			$name .= ' (' . $value['uri'] . ')';
		$generator = $dom->createElement('generator');
		$generator->appendChild($dom->createTextNode($name));
		$root->appendChild($generator);		
	}
	
	protected function renderLink(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getLink();
		if (!$value) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'RSS 2.0 feed must contain a link'));
			else
				return;
		}
		$link = $dom->createElement('link');
		$link->appendChild($dom->createTextNode($value));
		$validator = new ValidatorUrl();
		if (!$validator->validate($value))
			$link->setAttribute('isPermalink', 'false');
		$root->appendChild($link);
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
	
	protected function renderCopyright(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getCopyright();
		if (!$value)
			return;
		$copyright = $dom->createElement('copyright');
		$copyright->appendChild($dom->createTextNode($value));
		$root->appendChild($copyright);		
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