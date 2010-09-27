<?php

class ActiveRecordBehaviorUpload extends ActiveRecordBehavior
{
	private static $defaultOptions = array(
		'saveMode' => 0666,
		'savePath' => null,
		'generateFileName' => false
	);
	protected $attrs;
	protected $savePath;

	public function setAttrs($attrs) {
		if (is_string($attrs))
			$attrs = explode(',', $attrs);
		elseif (!is_array($attrs))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid attributes specification.'));
		foreach ($attrs as $name => $options) {
			if (is_int($name) && is_string($options))
				$this->attrs[$options] = self::$defaultOptions;
			elseif (is_string($name) && Util::isMap($options))
				$this->attrs[$name] = array_merge(self::$defaultOptions, $options);
			else
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid attributes specification.'));
		}
	}

	public function setSavePath($path) {
		$this->savePath = $path;
	}

	public function attach(Component $model) {
		parent::attach($model);
		if ($this->savePath === null) {
			foreach ($this->attrs as $name => &$options) {
				if ($options['savePath'] === null)
					throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Missing save path for "%s" attribute.', array($name)));
			}
		}
	}

	public function onLoad(Event $event) {
		foreach ($this->attrs as $name => &$options)
			$options['previousFileName'] = $this->owner->{$name};
	}

	public function onImport(Event $event) {
		$key = '%s[%s]';
		$mgr = new UploadManager();
		foreach (array_keys($this->attrs) as $name) {
			$file = $mgr->getFile(sprintf($key, $this->owner->getNamePrefix(), $name));
			if ($file && $file->uploaded)
				$this->owner->{$name} = $file;
		}
	}

	public function onBeforeInsert(Event $event) {
		foreach ($this->attrs as $name => $options) {
			$file = $this->owner->{$name};
			if ($file instanceof UploadFile && $file->uploaded) {
				if (!$this->saveFile($name, $file))
					return false;
			}
		}
	}

	public function onBeforeUpdate(Event $event) {
		foreach ($this->attrs as $name => $options) {
			$file = $this->owner->{$name};
			if ($file instanceof UploadFile && $file->uploaded) {
				if (!$this->saveFile($name, $file))
					return false;
			}
		}
	}

	public function onAfterDelete(Event $event) {
		foreach ($this->attrs as $name => $options) {
			$filePath = ($options['savePath'] !== null ? $options['savePath'] : $this->savePath);
			$fileName = $this->owner->{$name};
			if (is_file($filePath . DS . $fileName))
				@unlink($filePath . DS . $fileName);
		}
	}

	private function saveFile($attr, UploadFile $file) {
		$options = $this->attrs[$attr];
		$savePath = ($options['savePath'] !== null ? $options['savePath'] : $this->savePath);
		try {
			if ($options['generateFileName'])
				$file->saveAs($savePath . DS . $this->generateFileName($file), $options['saveMode']);
			else
				$file->saveTo($savePath, $options['saveMode']);
			if (isset($options['previousFileName']) && $file->getName() !== $options['previousFileName'] && is_file($savePath . DS . $options['previousFileName']))
				@unlink($savePath . DS . $options['previousFileName']);
			return true;
		} catch (UploadFileException $e) {
			$this->owner->addError($name, $e->getMessage());
			return false;
		}
	}

	private function generateFileName(UploadFile $file) {
		return sprintf("%s_%s_%s",
			time(), $this->owner->getTableName(), strtolower(
				preg_replace('/\s+/', '', Inflector::unaccent($file->getName()))
			)
		);
	}
}