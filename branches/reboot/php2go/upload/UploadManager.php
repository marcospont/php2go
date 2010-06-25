<?php

class UploadManager
{
	const REQUEST_SIZE = 'requestSizeExceeded';
	const INI_SIZE = 'iniSizeExceeded';
	const FORM_SIZE = 'formSizeExceeded';
	const PARTIAL = 'partial';
	const NO_FILE = 'noFile';
	const NO_TMP_DIR = 'noTmpDir';
	const CANT_WRITE = 'cantWrite';
	const EXTENSION = 'extension';
	const INVALID = 'invalid';
	const NOT_FOUND = 'notFound';
	const UNKNOWN = 'unknown';
	const TOO_FEW = 'tooFew';
	const TOO_MANY = 'tooMany';

	private static $files;
	private static $defaultValidatorClasses = array(
		'count' => 'ValidatorFileCount',
		'extension' => 'ValidatorFileExtension',
		'imageSize' => 'ValidatorImageSize',
		'mimeType' => 'ValidatorFileMimeType',
		'size' => 'ValidatorFileSize'
	);
	private $rules = array();
	private $validators = array();
	private $errors = array();
	private $messages = array();

	public function __construct() {
		$this->initMessages();
		$this->parseFiles();
	}

	public function getFile($key) {
		return (isset(self::$files[$key]) ? self::$files[$key] : null);
	}

	public function getFiles($keys=null, $fallback=true, $names=false) {
		if (is_string($keys))
			$keys = array($keys);
		if (is_array($keys)) {
			$files = array();
			foreach ($keys as $key) {
				if (isset(self::$files[$key])) {
					if ($names)
						$files[] = $key;
					else
						$files[$key] = self::$files[$key];
				}
			}
			return $files;
		}
		return ($fallback ? ($names ? array_keys(self::$files) : self::$files) : null);
	}

	public function addRules(array $rules=array()) {
		foreach ($rules as $key => $config) {
			if (is_string($key)) {
				foreach ($config as $k => $v) {
					if (is_string($k) && is_array($v))
						$this->addRule($k, $v, $key);
					elseif (is_int($k) && is_string($v))
						$this->addRule($v, array(), $key);
				}
			} elseif (is_int($key)) {
				foreach ($config as $k => $v) {
					if (is_string($k) && is_array($v))
						$this->addRule($k, $v);
					elseif (is_int($k) && is_string($v))
						$this->addRule($v, array());
				}
			}
		}
	}

	public function addRule($validator, array $options, $key=null) {
		if (is_string($key))
			$this->rules[$key][$validator] = $options;
		else
			$this->rules[0][$validator] = $options;
	}

	public function removeRule($validator, $key=null) {
		if (is_string($key))
			unset($this->rules[$key][$validator]);
		else
			unset($this->rules[0][$validator]);
	}

	public function clearRules() {
		$this->rules = array();
	}

	public function setMessage($key, $message) {
		if (array_key_exists($key, $this->messages))
			$this->messages[$key] = $message;
	}

	public function hasErrors() {
		return (!empty($this->errors));
	}

	public function getGlobalErrors() {
		return (isset($this->errors[0]) ? $this->errors[0] : null);
	}

	public function getErrors($keys=null) {
		if ($keys !== null) {
			$errors = array();
			// normalize keys
			$arrKeys = (is_string($keys) ? array($keys) : (is_array($keys) ? $keys : array()));
			foreach ($arrKeys as $key) {
				$errors[$key] = array_merge((isset($this->errors[0]) ? $this->errors[0] : array()), (isset($this->errors[$key]) ? $this->errors[$key] : array()));
			}
			if (!empty($errors)) {
				if (is_string($keys) && isset($errors[$key]))
					return $errors[$key];
				return $errors;
			}
			return null;
		} else {
			return (!empty($this->errors) ? $this->errors : null);
		}
	}

	public function validate($keys=null) {
		$this->errors = array();
		// normalize keys
		$keys = (is_string($keys) ? array($keys) : (is_array($keys) ? $keys : array()));
		// max_post_size exceeded
		if (empty(self::$files) && Php2Go::app()->getRequest()->getContentLength() > Util::fromByteString(ini_get('post_max_size'))) {
			$this->errors[0][] = $this->resolveMessage(self::REQUEST_SIZE);
			return false;
		}
		// set 'not found' error and run 'count' validator on requested keys
		foreach ($keys as $key) {
			if (!($file = $this->getFile($key))) {
				$this->errors[$key][] = $this->resolveMessage(self::NOT_FOUND);
				$validators = $this->getValidators($key);
				if (isset($validators['count']) && !$validators['count']->validate(array()))
					$this->errors[$key][] = $validators['count']->getError();
			}
		}
		// validate all files
		foreach ($this->getFiles($keys) as $key => $f) {
			$validators = $this->getValidators($key);
			if ($f instanceof UploadFileCollection) {
				if (isset($validators['count']) && !$validators['count']->validate($file))
					$this->errors[$key][] = $validators['count']->getError();
				foreach ($f as $file) {
					if (!$file->getValid())
						$this->errors[$key][] = $this->resolveMessage($file->getError(), array('value' => $file->getName()));
					foreach ($validators as $name => $validator) {
						if ($name != 'count' && !$validator->validate($file))
							$this->errors[$key][] = $validator->getError();
					}
				}
			} else {
				if (!$f->getValid())
					$this->errors[$key][] = $this->resolveMessage($f->getError(), array('value' => $f->getName()));
				if (isset($validators['count']) && !$validators['count']->validate(array($f)))
					$this->errors[$key][] = $validators['count']->getError();
				foreach ($validators as $name => $validator) {
					if ($name != 'count' && !$validator->validate($f))
						$this->errors[$key][] = $validator->getError();
				}
			}
		}
		return (empty($this->errors));
	}

	protected function resolveMessage($key, array $params=array()) {
		if ($key == self::INI_SIZE) {
			$params['max'] = ini_get('upload_max_filesize');
		} elseif ($key == self::FORM_SIZE) {
			$params['max'] = Util::buildByteString($_POST['MAX_FILE_SIZE']);
		}
		return Util::buildMessage($this->messages[$key], $params);
	}

	private function getValidators($key) {
		$validators = array();
		$config = (isset($this->rules[0]) ? $this->rules[0] : array());
		if (isset($this->rules[$key]))
			$config = array_merge($config, $this->rules[$key]);
		foreach ($config as $key => $params) {
			$validator = $this->createValidator($key, $params);
			$validators[$key] = $validator;
		}
		return $validators;
	}

	private function createValidator($validator, array $options) {
		if (!isset($this->validators[$validator])) {
			$config = array('options' => $options);
			if (isset(self::$defaultValidatorClasses[$validator])) {
				$config['class'] = self::$defaultValidatorClasses[$validator];
			} else {
				$config['class'] = $validator;
				$config['parent'] = 'Validator';
			}
			$this->validators[$validator] = Php2Go::newInstance($config);;
		} else {
			$this->validators[$validator]->loadOptions($options);
		}
		return $this->validators[$validator];
	}

	private function initMessages() {
		$this->messages = array(
			self::REQUEST_SIZE => __(PHP2GO_LANG_DOMAIN, 'The maximum request size was exceeded.'),
			self::INI_SIZE => __(PHP2GO_LANG_DOMAIN, 'File "{value}" exceeds the maximum allowed size: {max}.'),
			self::FORM_SIZE => __(PHP2GO_LANG_DOMAIN, 'File "{value}" exceeds the maximum allowed size: {max}.'),
			self::PARTIAL => __(PHP2GO_LANG_DOMAIN, 'File "{value}" was only partially uploaded.'),
			self::NO_FILE => __(PHP2GO_LANG_DOMAIN, 'File "{value}" was not uploaded.'),
			self::NO_TMP_DIR => __(PHP2GO_LANG_DOMAIN, 'No temporary folder is available to write "{value}".'),
			self::CANT_WRITE => __(PHP2GO_LANG_DOMAIN, 'File "{value}" could not be written.'),
			self::EXTENSION => __(PHP2GO_LANG_DOMAIN, 'A PHP extension returned an error while uploading "{value}".'),
			self::INVALID => __(PHP2GO_LANG_DOMAIN, 'File "{value}" was not properly uploaded.'),
			self::NOT_FOUND => __(PHP2GO_LANG_DOMAIN, 'File was not uploaded.'),
			self::UNKNOWN => __(PHP2GO_LANG_DOMAIN, 'Unknown error while uploading file "{value}".')
		);
	}

	private function parseFiles() {
		if (!self::$files) {
			if (isset($_FILES) && !empty($_FILES)) {
				foreach ($_FILES as $item => $data)
					$this->parseFilesRecursive($item, $data['name'], $data['tmp_name'], $data['type'], $data['size'], $data['error']);
			} else {
				self::$files = array();
			}
		}
	}

	private function parseFilesRecursive($key, $names, $tmpNames, $types, $sizes, $errors) {
		if (Util::isMap($names)) {
			foreach ($names as $item => $data)
				$this->parseFilesRecursive($key . '[' . $item . ']', $names[$item], $tmpNames[$item], $types[$item], $sizes[$item], $errors[$item]);
		} elseif (is_array($names)) {
			$files = array();
			foreach ($names as $item => $name) {
				if (!empty($name))
					$files[] = new UploadFile($name, $tmpNames[$item], $errors[$item]);
			}
			self::$files[$key] = new UploadFileCollection($files, $key);
		} else {
			if (!empty($names))
				self::$files[$key] = new UploadFile($names, $tmpNames, $errors, $key);
		}
	}
}