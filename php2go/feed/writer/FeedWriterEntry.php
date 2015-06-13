<?php

Php2Go::import('php2go.feed.writer.FeedWriterSource');

class FeedWriterEntry extends Component
{
	private static $urlValidator = null;	
	protected $type = null;
	protected $data = array();
	
	public function __unset($property) {
		if (array_key_exists($property, $this->data))
			unset($this->data[$property]);
		else
			parent::__unset($property);
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getEncoding() {
		return (array_key_exists('encoding', $this->data) ? $this->data['encoding'] : null);
	}
	
	public function setEncoding($encoding) {
		$this->data['encoding'] = $encoding;
	}
	
	public function getAuthors() {
		return (array_key_exists('authors', $this->data) ? $this->data['authors'] : array());
	}

	public function addAuthor($name, $email=null, $uri=null) {
		$author = array();
		if (is_array($name)) {
			if (!array_key_exists('name', $name))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid author specification'));
			if (isset($name['email']))
				$email = $name['email'];
			if (isset($name['uri']))
				$uri = $name['uri'];
			$name = $name['name'];
		}
		if (Util::isEmpty($name))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid author specification'));
		$author['name'] = $name;
		if (isset($email)) {
			$validator = new ValidatorEmail();
			if (!$validator->validate($email))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid author specification'));
			$author['email'] = $email;
		}
		if (isset($uri)) {
			if (!$this->getUrlValidator()->validate($uri))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid author specification'));
			$author['uri'] = $uri;
		}
		if (!isset($this->data['authors']))
			$this->data['authors'] = array();
		$this->data['authors'][] = $author;
	}
	
	public function addAuthors(array $authors) {
		foreach ($authors as $author)
			$this->addAuthor($author);
	}
	
	public function getCopyright() {
		return (array_key_exists('copyright', $this->data) ? $this->data['copyright'] : null);
	}
	
	public function setCopyright($copyright) {
		$this->data['copyright'] = $copyright;		
	}
	
	public function getDateCreated() {
		return (array_key_exists('dateCreated', $this->data) ? $this->data['dateCreated'] : null);
	}
	
	public function setDateCreated($dateCreated=null, $format=null) {
		if (Util::isEmpty($dateCreated))
			$dateCreated = time();
		if (is_string($dateCreated))
			$dateCreated = DateTimeParser::parse($dateCreated, $format);
		$this->data['dateCreated'] = $dateCreated;
	}
	
	public function getDateModified() {
		return (array_key_exists('dateModified', $this->data) ? $this->data['dateModified'] : null);
	}
	
	public function setDateModified($dateModified=null, $format=null) {
		if (Util::isEmpty($dateModified))
			$dateModified = time();
		if (is_string($dateModified))
			$dateModified = DateTimeParser::parse($dateModified, $format);
		$this->data['dateModified'] = $dateModified;
	}
	
	public function getDescription() {
		return (array_key_exists('description', $this->data) ? $this->data['description'] : null);
	}
	
	public function setDescription($description) {
		$this->data['description'] = $description;
	}
	
	public function getContent() {
		return (array_key_exists('content', $this->data) ? $this->data['content'] : null);
	}
	
	public function setContent($content) {
		$this->data['content'] = $content;		
	}
	
	public function getId() {
		return (array_key_exists('id', $this->data) ? $this->data['id'] : null);
	}
	
	public function setId($id) {
		$this->data['id'] = $id;		
	}
	
	public function getLink() {
		return (array_key_exists('link', $this->data) ? $this->data['link'] : null);
	}
	
	public function setLink($link) {
		if (Util::isEmpty($link) || !$this->getUrlValidator()->validate($link))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid URL'));
		$this->data['link'] = $link;
	}
	
	public function getCommmentCount() {
		return (array_key_exists('commentCount', $this->data) ? $this->data['commentCount'] : null);
	}
	
	public function setCommentCount($commentCount) {
		$this->data['commentCount'] = (int) $commentCount;
	}
	
	public function getCommentLink() {
		return (array_key_exists('commentLink', $this->data) ? $this->data['commentLink'] : null);
	}
	
	public function setCommentLink($commentLink) {
		if (Util::isEmpty($commentLink) || !$this->getUrlValidator()->validate($commentLink))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid URL'));
		$this->data['commentLink'] = $commentLink;
	}
	
	public function getCommentFeedLinks() {
		return (array_key_exists('commentFeedLinks', $this->data) ? $this->data['commentFeedLinks'] : array());
	}
	
	public function addCommentFeedLink(array $commentFeedLink) {
		if (!isset($commentFeedLink['uri']) || Util::isEmpty($commentFeedLink['uri']) || !$this->getUrlValidator()->validate($commentFeedLink['uri']))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid comment feed link specification'));
		if (!isset($commentFeedLink['type']) || !in_array($commentFeedLink['type'], array('rss', 'atom', 'rdf')))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid comment feed link type'));
		if (!isset($this->data['commentFeedLinks']))
			$this->data['commentFeedLinks'] = array();			
		$this->data['commentFeedLinks'][] = $commentFeedLink;
	}
	
	public function addCommentFeedLinks(array $commentFeedLinks) {
		foreach ($commentFeedLinks as $commentFeedLink)
			$this->addCommentFeedLink($commentFeedLink);
	}
	
	public function getTitle() {
		return (array_key_exists('title', $this->data) ? $this->data['title'] : null);
	}
	
	public function setTitle($title) {
		$this->data['title'] = $title;
	}	
	
	public function getEnclosure() {
		return (array_key_exists('enclosure', $this->data) ? $this->data['enclosure'] : null);
	}
	
	public function setEnclosure(array $enclosure) {
		if (!isset($enclosure['uri']) || !$this->getUrlValidator()->validate($enclosure['uri']))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid URL'));
		$this->data['enclosure'] = $enclosure;
	}
	
	public function getCategories() {
		return (array_key_exists('categories', $this->data) ? $this->data['categories'] : array());
	}
	
	public function addCategory(array $category) {
		if (!isset($category['term']))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid category specification'));
		if (isset($category['scheme']) && !$this->getUrlValidator()->validate($category['scheme']))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid category specification'));
		if (!isset($this->data['categories']))
			$this->data['categories'] = array();
		$this->data['categories'][] = $category;
	}

	public function addCategories(array $categories) {
		foreach ($categories as $category)
			$this->addCategory($category);
	}	
	
	public function getSource() {
		return (array_key_exists('source', $this->data) ? $this->data['source'] : null);
	}
	
	public function setSource(FeedWriterSource $source) {
		$this->data['source'] = $source;
	}
	
	public function createSource() {
		$source = new FeedWriterSource();
		if ($this->getEncoding())
			$source->setEncoding($this->getEncoding());
		$source->setType($this->getType());
		return $source;
	}
	
	protected function getUrlValidator() {
		if (self::$urlValidator == null)
			self::$urlValidator = new ValidatorUrl();
		return self::$urlValidator;
	}
}