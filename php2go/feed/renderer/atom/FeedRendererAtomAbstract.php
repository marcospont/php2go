<?php

abstract class FeedRendererAtomAbstract extends FeedRenderer
{
	protected function renderBaseUrl(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getBaseUrl();
		if (!$value)
			return;
		$root->setAttribute('xml:base', $value);		
	}
	
	protected function renderLanguage(DOMDocument $dom, DOMElement $root) {
		if ($this->getContainer()->getLanguage())
			$root->setAttribute('xml:lang', $this->getContainer()->getLanguage());
	}
	
	protected function renderTitle(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getTitle()) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'Atom 1.0 feed must contain a title'));
			else
				return;
		}
		$title = $dom->createElement('title');
		$title->setAttribute('type', 'text');
		$title->appendChild($dom->createTextNode($this->getContainer()->getTitle()));
		$root->appendChild($title);
	}
	
	protected function renderDescription(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getDescription();
		if (!$value)
			return;
		$subtitle = $dom->createElement('subtitle');
		$subtitle->setAttribute('type', 'text');
		$subtitle->appendChild($dom->createTextNode($value));
		$root->appendChild($subtitle);		
	}
	
	protected function renderImage(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getImage();
		if (!$value)
			return;
		$image = $dom->createElement('logo');
		$image->appendChild($dom->createTextNode($value['uri']));
		$root->appendChild($image);
	}
	
	protected function renderIcon(DOMDocument $dom, DOMElement $root) {		
		$value = $this->getContainer()->getImage();
		if (!$value)
			return;
		$icon = $dom->createElement('icon');
		$icon->appendChild($dom->createTextNode($value['uri']));
		$root->appendChild($icon);		
	}
	
	protected function renderDateModified(DOMDocument $dom, DOMElement $root) {
		$dateModified = $this->getContainer()->getDateModified();
		if (!$dateModified) {
			$dateCreated = $this->getContainer()->getDateCreated();
			if ($dateCreated) {
				$dateModified = $dateCreated;
				$this->getContainer()->setDateModified($dateCreated);
			} elseif (!$this->ignoreExceptions) {
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'Atom 1.0 feed must contain a modified date'));
			} else {
				return;
			}
		}
		$modified = $dom->createElement('updated');
		$modified->appendChild($dom->createTextNode(DateTimeFormatter::format($dateModified, DATE_ISO8601)));
		$root->appendChild($modified);
	}
	
	protected function renderGenerator(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getGenerator())
			$this->getContainer()->setGenerator('Php2go Feed Writer', PHP2GO_VERSION, 'http://www.php2go.com.br');
		$value = $this->getContainer()->getGenerator();
		$generator = $dom->createElement('generator');
		$generator->appendChild($dom->createTextNode($value['name']));
		if (array_key_exists('version', $value))
			$generator->setAttribute('version', $value['version']);
		if (array_key_exists('uri', $value))
			$generator->setAttribute('uri', $value['uri']);
		$root->appendChild($generator);
	}
	
	protected function renderLink(DOMDocument $dom, DOMElement $root) {		
		$value = $this->getContainer()->getLink();
		if (!$value)
			return;
		$link = $dom->createElement('link');
		$link->setAttribute('rel', 'altenate');
		$link->setAttribute('type', 'text/html');
		$link->setAttribute('href', $value);
		$root->appendChild($link);		
	}
	
	protected function renderFeedLinks(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getFeedLinks();
		if (!$value || empty($value) || !isset($value['atom'])) {
			if (!$this->ignoreExtensions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'Atom 1.0 feed must contain a feed link of "atom" type'));
			else
				return;
		}
		foreach ($value as $type => $href) {
			$mime = 'application/' . strtolower($type) . '+xml';
			$link = $dom->createElement('link');
			$link->setAttribute('rel', 'self');
			$link->setAttribute('type', $mime);
			$link->setAttribute('href', $href);
			$root->appendChild($link);
		}
	}
	
	protected function renderId(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getId() && !$this->getContainer()->getLink()) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'Atom 1.0 feed must contain an id or a link'));
			else
				return;
		}
		if (!$this->getContainer()->getId())
			$this->getContainer()->setId($this->getContainer()->getLink());
		$id = $dom->createElement('id');
		$id->appendChild($dom->createTextNode($this->getContainer()->getId()));
		$root->appendChild($id);
	}
	
	protected function renderAuthors(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getAuthors();
		if (!$value || empty($value))
			return;
		foreach ($value as $entry) {
			$author = $dom->createElement('author');
			$name = $dom->createElement('name');
			$name->appendChild($dom->createTextNode($entry['name']));
			$author->appendChild($name);
			if (array_key_exists('email', $entry)) {
				$email = $dom->createElement('email');
				$email->appendChild($dom->createTextNode($entry['email']));
				$author->appendChild($email);
			}
			if (array_key_exists('uri', $entry)) {
				$uri = $dom->createElement('uri');
				$uri->appendChild($dom->createTextNode($entry['uri']));
				$author->appendChild($uri);
			}
			$root->appendChild($author);
		}
	}
	
	protected function renderCopyright(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getCopyright();
		if (!$value)
			return;
		$rights = $dom->createElement('rights');
		$rights->appendChild($dom->createTextNode($value));
		$root->appendChild($rights);		
	}
	
	protected function renderCategories(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getCategories();
		if (!$value || empty($value))
			return;
		foreach ($value as $entry) {
			$category = $dom->createElement('category');
			$category->setAttribute('term', $entry['term']);
			if (isset($entry['label']))
				$category->setAttribute('label', $entry['label']);
			else
				$category->setAttribute('label', $entry['term']);
			if (isset($entry['scheme']))
				$category->setAttribute('scheme', $entry['scheme']);
			$root->appendChild($category);
		}
	}
	
	protected function renderHubs(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getHubs();
		if (!$value || empty($value))
			return;
		foreach ($value as $entry) {
			$link = $dom->createElement('link');
			$link->setAttribute('rel', 'hub');
			$link->setAttribute('href', $entry);
			$root->appendChild($link);
		}
	}	
}