<?php

class ValidatorImageSize extends Validator
{
	protected $width;
	protected $height;
	protected $minWidth;
	protected $minHeight;
	protected $maxWidth;
	protected $maxHeight;
	
	public function __construct() {
		$this->defaultMessages = array(
			'notDetected' => __(PHP2GO_LANG_DOMAIN, 'The size of the image "{value}" could not be detected.'),
			'dimensionsInvalid' => __(PHP2GO_LANG_DOMAIN, '"{value}" must be {width}x{height} pixels.'),
			'widthInvalid' => __(PHP2GO_LANG_DOMAIN, '"{value}" must have width of {width} pixels.'),
			'heightInvalid' => __(PHP2GO_LANG_DOMAIN, '"{value}" must have height of {height} pixels.'),
			'dimensionsTooSmall' => __(PHP2GO_LANG_DOMAIN, '"{value}" must be at least {width}x{height} pixels.'),
			'widthTooSmall' => __(PHP2GO_LANG_DOMAIN, '"{value}" must have width of at least {width} pixels.'),
			'heightTooSmall' => __(PHP2GO_LANG_DOMAIN, '"{value}" must have height of at least {height} pixels.'),
			'dimensionsTooBig' => __(PHP2GO_LANG_DOMAIN, '"{value}" must be at most {width}x{height} pixels.'),
			'widthTooBig' => __(PHP2GO_LANG_DOMAIN, '"{value}" must have width of at most {width} pixels.'),
			'heightTooBig' => __(PHP2GO_LANG_DOMAIN, '"{value}" must have height of at most {height} pixels.')
		);		
	}
	
	protected function validateOptions() {
		if (
			($this->width === null && $this->height === null && $this->maxWidth === null && $this->maxHeight === null && $this->minWidth === null && $this->minHeight === null) ||
			($this->width !== null && ($this->minWidth !== null || $this->maxWidth !== null)) ||
			($this->height !== null && ($this->minHeight !== null || $this->maxHeight !== null))
		)
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid %s specification.', array(__CLASS__)));		
	}
	
	public function validate($value) {
		if ($value instanceof UploadFile) {
			$name = $value->getName();
			$path = $value->getPath();			
		} else {			
			$name = basename($value);
			$path = $value;
		}
		$size = @getimagesize($path);
		if (empty($size) || $size[0] === 0 || $size[1] === 1) {
			$this->setError($this->resolveMessage('notDetected', true), array('value' => $value));
			return false;
		}
		if ($this->width !== null && $this->height !== null && ($size[0] != $this->width || $size[1] != $this->height)) {
			$this->setError($this->resolveMessage('dimensionsInvalid', true), array('value' => $name, 'width' => $this->width, 'height' => $this->height));
			return false;
		}
		if ($this->width !== null && $size[0] != $this->width) {
			$this->setError($this->resolveMessage('widthInvalid', true), array('value' => $name, 'width' => $this->width));
			return false;
		}
		if ($this->height !== null && $size[1] != $this->height) {
			$this->setError($this->resolveMessage('heightInvalid', true), array('value' => $name, 'height' => $this->height));
			return false;
		}
		if ($this->minWidth !== null && $this->minHeight !== null && ($size[0] < $this->minWidth || $size[1] < $this->minHeight)) {
			$this->setError($this->resolveMessage('dimensionsTooSmall', true), array('value' => $name, 'width' => $this->minWidth, 'height' => $this->minHeight));
			return false;
		}
		if ($this->minWidth !== null && $size[0] < $this->minWidth) {
			$this->setError($this->resolveMessage('widthTooSmall', true), array('value' => $name, 'width' => $this->minWidth));
			return false;
		}
		if ($this->minHeight !== null && $size[1] < $this->minHeight) {
			$this->setError($this->resolveMessage('heightTooSmall', true), array('value' => $name, 'height' => $this->minHeight));
			return false;
		}
		if ($this->maxWidth !== null && $this->maxHeight !== null && ($size[0] > $this->maxWidth || $size[1] > $this->maxHeight)) {
			$this->setError($this->resolveMessage('dimensionsTooBig', true), array('value' => $name, 'width' => $this->maxWidth, 'height' => $this->maxHeight));
			return false;
		}
		if ($this->maxWidth !== null && $size[0] > $this->maxWidth) {
			$this->setError($this->resolveMessage('widthTooBig', true), array('value' => $name, 'width' => $this->maxWidth));
			return false;
		}
		if ($this->maxHeight !== null && $size[1] > $this->maxHeight) {
			$this->setError($this->resolveMessage('heightTooBig', true), array('value' => $name, 'height' => $this->maxHeight));
			return false;
		}
		return true;
	}
}