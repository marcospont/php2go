<?php

class FeedRendererEntryAtom extends FeedRenderer
{
	public function __construct(FeedWriterEntry $container) {
		parent::__construct($container);
	}
	
	public function render() {
		// root
		$this->dom = new DOMDocument('1.0', $this->container->getEncoding());
		$this->dom->formatOutput = true;
		$entry = $this->dom->createElementNS(FeedWriter::NAMESPACE_ATOM_10, 'entry');
		$this->dom->appendChild($entry);
		$this->renderSource($this->dom, $entry);
		$this->renderTitle($this->dom, $entry);
		$this->renderDescription($this->dom, $entry);
		$this->renderDateCreated($this->dom, $entry);
		$this->renderDateModified($this->dom, $entry);
		$this->renderLink($this->dom, $entry);
		$this->renderId($this->dom, $entry);
		$this->renderAuthors($this->dom, $entry);
		$this->renderEnclosure($this->dom, $entry);
		$this->renderContent($this->dom, $entry);
		$this->renderCategories($this->dom, $entry);
		return $this->dom;
	}
	
	protected function renderSource(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getSource();
		if (!$value)
			return;
		$renderer = new FeedRendererAtomSource($value);
		$renderer->setType($this->getType());
		$renderer->render();
		$root->appendChild($dom->importNode($renderer->getDocumentElement(), true));		
	}
	
	protected function renderTitle(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getTitle()) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'Atom 1.0 entry must contain a title'));
			else
				return;
		}
		$title = $dom->createElement('title');
		$title->setAttribute('type', 'html');
		$title->appendChild($dom->createCDATASection($this->getContainer()->getTitle()));
		$root->appendChild($title);		
	}
	
	protected function renderDescription(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getDescription();
		if (!$value)
			return;
		$summary = $dom->createElement('summary');
		$summary->setAttribute('type', 'html');
		$summary->appendChild($dom->createCDATASection($value));
		$root->appendChild($summary);
	}
	
	protected function renderDateCreated(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getDateCreated();
		if (!$value)
			return;
		$published = $dom->createElement('published');
		$published->appendChild($dom->createTextNode(DateTimeFormatter::format($value, DATE_ISO8601)));
		$root->appendChild($published);		
	}
	
	protected function renderDateModified(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getDateModified();
		if (!$value) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'Atom 1.0 entry must contain a modified date'));
			else
				return;
		}
		$updated = $dom->createElement('updated');
		$updated->appendChild($dom->createTextNode(DateTimeFormatter::format($value, DATE_ISO8601)));
		$root->appendChild($updated);		
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
	
	protected function renderId(DOMDocument $dom, DOMElement $root) {
		if (!$this->getContainer()->getId() && !$this->getContainer()->getLink()) {
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'Atom 1.0 entry must contain an id or a link'));
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
	
	protected function renderEnclosure(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getEnclosure();
		if (!$value)
			return;
		$enclosure = $dom->createElement('link');
		$enclosure->setAttribute('rel', 'enclosure');
		if (isset($value['type']))
			$enclosure->setAttribute('type', $value['type']);
		if (isset($value['length']))
			$enclosure->setAttribute('length', $value['length']);
		$enclosure->setHref('href', $value['uri']);
		$root->appendChild($enclosure);
	}
	
	protected function renderContent(DOMDocument $dom, DOMElement $root) {
		$value = $this->getContainer()->getContent();
		if (!$value && !$this->getContainer()->getLink()) {		
			if (!$this->ignoreExceptions)
				throw new FeedWriterException(__(PHP2GO_LANG_DOMAIN, 'Atom 1.0 entry must contain content or link'));
			else
				return;
		}
		if (!$value)
			return;
		$content = $dom->createElement('content');
		$content->setAttribute('type', 'xhtml');
		$content->appendChild($dom->importNode($this->loadXhtml($value), true));
		$root->appendChild($content);
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
	
	protected function validateTagUri($id) {
        if (preg_match('/^tag:(?<name>.*),(?<date>\d{4}-?\d{0,2}-?\d{0,2}):(?<specific>.*)(.*:)*$/', $id, $matches)) {
            $dvalid = false;
            $nvalid = false;
            $date = $matches['date'];
            $d6 = strtotime($date);
            if ((strlen($date) == 4) && $date <= date('Y')) {
                $dvalid = true;
            } elseif ((strlen($date) == 7) && ($d6 < strtotime("now"))) {
                $dvalid = true;
            } elseif ((strlen($date) == 10) && ($d6 < strtotime("now"))) {
                $dvalid = true;
            }
            $validator = new ValidatorEmail();
            if ($validator->validate($matches['name'])) {
                $nvalid = true;
            } else {
                $nvalid = $validator->validate('info@' . $matches['name']);
            }
            return $dvalid && $nvalid;

        }
        return false;		
	}	
	
	protected function loadXhtml($content) {
        $xhtml = '';
        if (class_exists('tidy', false)) {
            $tidy = new tidy;
            $config = array(
                'output-xhtml' => true,
                'show-body-only' => true,
                'quote-nbsp' => false
            );
            $encoding = str_replace('-', '', $this->getEncoding());
            $tidy->parseString($content, $config, $encoding);
            $tidy->cleanRepair();
            $xhtml = (string) $tidy;
        } else {
            $xhtml = $content;
        }
        $xhtml = preg_replace(array(
            "/(<[\/]?)([a-zA-Z]+)/"
        ), '$1xhtml:$2', $xhtml);
        $dom = new DOMDocument('1.0', $this->getEncoding());
        $dom->loadXML('<xhtml:div xmlns:xhtml="http://www.w3.org/1999/xhtml">' . $xhtml . '</xhtml:div>');
        return $dom->documentElement;		
	}
}