<?php

class FeedWriterDeleted
{
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
	
	public function getReference() {
		return (array_key_exists('reference', $this->data) ? $this->data['reference'] : null);
	}
	
	public function setReference($reference) {
		$this->data['reference'] = $reference;
	}
	
	public function getWhen() {
		return (array_key_exists('when', $this->data) ? $this->data['when'] : null);
	}
	
	public function setWhen($when=null, $format=null) {
		if (Util::isEmpty($when))
			$when = time();
		if (is_string($when))
			$when = DateTimeParser::parse($when, $format);
		$this->data['when'] = $when;
	}
	
	public function getBy() {
		return (array_key_exists('by', $this->data) ? $this->data['by'] : null);
	}
	
	public function setBy($name, $email=null, $uri=null) {
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
			$validator = new ValidatorUrl();
			if (!$validator->validate($uri))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid author specification'));
			$author['uri'] = $uri;
		}
		$this->data['by'] = $author;
	}

	public function getComment() {
		return (array_key_exists('comment', $this->data) ? $this->data['comment'] : null);
	}
	
	public function setComment($comment) {
		$this->data['comment'] = $comment;
	}
}