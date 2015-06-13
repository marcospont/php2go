<?php

class FeedWriterAbstract extends Component
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
	
	public function reset() {
		$this->data = array();
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
	
	public function getLastBuildDate() {	
		return (array_key_exists('lastBuildDate', $this->data) ? $this->data['lastBuildDate'] : null);
	}
	
	public function setLastBuildDate($lastBuildDate=null, $format=null) {
		if (Util::isEmpty($lastBuildDate))
			$lastBuildDate = time();
		if (is_string($lastBuildDate))
			$lastBuildDate = DateTimeParser::parse($lastBuildDate, $format);
		$this->data['lastBuildDate'] = $lastBuildDate;
	}
	
	public function getDescription() {
		return (array_key_exists('description', $this->data) ? $this->data['description'] : null);
	}
	
	public function setDescription($description) {
		$this->data['description'] = $description;
	}
	
	public function getGenerator() {
		return (array_key_exists('generator', $this->data) ? $this->data['generator'] : null);
	}
	
	public function setGenerator($name, $version=null, $uri=null) {
		$generator = array();
		if (is_array($name)) {
			if (!array_key_exists('name', $name))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid generator specification'));
			if (isset($name['version']))
				$version = $name['version'];
			if (isset($name['uri']))
				$uri = $name['uri'];
			$name = $name['name'];
		}
		if (Util::isEmpty($name))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid generator specification'));
		$generator['name'] = $name;
		if (isset($version))
			$generator['version'] = $version;
		if (isset($uri)) {
			if (!$this->getUrlValidator()->validate($uri))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid generator specification'));
			$generator['uri'] = $uri;
		}
		$this->data['generator'] = $generator;		
	}
	
	public function getId() {
		return (array_key_exists('id', $this->data) ? $this->data['id'] : null);
	}
	
	public function setId($id) {
		if ((Util::isEmpty($id) || !$this->getUrlValidator()->validate($id)) &&
			!preg_match("#^urn:[a-zA-Z0-9][a-zA-Z0-9\-]{1,31}:([a-zA-Z0-9\(\)\+\,\.\:\=\@\;\$\_\!\*\-]|%[0-9a-fA-F]{2})*#", $id) &&
			!$this->validateTagUri($id)) {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid id'));
		}
		$this->data['id'] = $id;		
	}
	
	public function getImage() {
		return (array_key_exists('image', $this->data) ? $this->data['image'] : null);
	}
	
	public function setImage(array $image) {
		if (!array_key_exists('uri', $image) || Util::isEmpty($image['uri']) || !$this->getUrlValidator()->validate($image['uri']))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid image specification'));
		$this->data['image'] = $image;		
	}
	
	public function getIcon() {
		return (array_key_exists('icon', $this->data) ? $this->data['icon'] : null);
	}
	
	public function setIcon($icon) {
		if (!array_key_exists('uri', $icon) || Util::isEmpty($icon['uri']) || !$this->getUrlValidator()->validate($icon['uri']))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid icon specification'));
		$this->data['icon'] = $icon;
	}
	
	public function getLanguage() {
		return (array_key_exists('language', $this->data) ? $this->data['language'] : null);
	}
	
	public function setLanguage($language) {
		$this->data['language'] = $language;
	}
	
	public function getLink() {
		return (array_key_exists('link', $this->data) ? $this->data['link'] : null);
	}
	
	public function setLink($link) {
		if (Util::isEmpty($link) || !$this->getUrlValidator()->validate($link))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid link URL'));
		$this->data['link'] = $link;
	}
	
	public function getFeedLinks() {
		return (array_key_exists('feedLinks', $this->data) ? $this->data['feedLinks'] : array());
	}
	
	public function setFeedLink($link, $type) {
		if (Util::isEmpty($link) || !$this->getUrlValidator()->validate($link))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid feed link specification'));
		if (!in_array($type, array('rss', 'atom', 'rdf')))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid feed link type'));
		if (!isset($this->data['feedLinks']))
			$this->data['feedLinks'] = array();
		$this->data['feedLinks'][$type] = $link;
	}
	
	public function getTitle() {
		return (array_key_exists('title', $this->data) ? $this->data['title'] : null);
	}
	
	public function setTitle($title) {
		$this->data['title'] = $title;
	}
	
	public function getBaseUrl() {
		return (array_key_exists('baseUrl', $this->data) ? $this->data['baseUrl'] : null);
	}
	
	public function setBaseUrl($baseUrl) {
		if (Util::isEmpty($baseUrl) || !$this->getUrlValidator()->validate($baseUrl))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid base URL'));
		$this->data['baseUrl'] = $baseUrl;
	}
	
	public function getHubs() {
		return (array_key_exists('hubs', $this->data) ? $this->data['hubs'] : array());
	}
	
	public function addHub($hub) {
		if (Util::isEmpty($hub) || !$this->getUrlValidator()->validate($hub))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid URL'));
		if (!isset($this->data['hubs']))
			$this->data['hubs'] = array();
		$this->data['hubs'][] = $hub;		
	}
	
	public function addHubs(array $hubs) {
		foreach ($hubs as $hub)
			$this->addHub($hub);
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

	protected function getUrlValidator() {
		if (self::$urlValidator == null)
			self::$urlValidator = new ValidatorUrl();
		return self::$urlValidator;
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
}