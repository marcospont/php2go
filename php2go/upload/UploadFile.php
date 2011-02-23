<?php

class UploadFile extends Component
{
	private $name;
	private $extension;
	private $tempPath;
	private $path;
	private $mimeType;
	private $size;
	private $errorCode;
	private $uploadKey;
	private $error;
	private $uploaded = false;
	private $valid = false;
	private $saved = false;

	public function __construct($name, $tempPath, $errorCode, $uploadKey=null) {
		$this->name = $name;
		$this->tempPath = $tempPath;
		$this->errorCode = $errorCode;
		$this->uploadKey = $uploadKey;
		$this->uploaded = is_uploaded_file($tempPath);
		switch ($this->errorCode) {
			case UPLOAD_ERR_OK :
				$this->error = (!$this->uploaded ? UploadManager::INVALID : null);
				$this->valid = $this->uploaded;
				break;
			case UPLOAD_ERR_INI_SIZE :
				$this->error = UploadManager::INI_SIZE;
				break;
			case UPLOAD_ERR_FORM_SIZE :
				$this->error = UploadManager::FORM_SIZE;
				break;
			case UPLOAD_ERR_PARTIAL :
				$this->error = UploadManager::PARTIAL;
				break;
			case UPLOAD_ERR_NO_FILE :
				$this->error = UploadManager::NO_FILE;
				break;
			case UPLOAD_ERR_NO_TMP_DIR :
				$this->error = UploadManager::NO_TMP_DIR;
				break;
			case UPLOAD_ERR_CANT_WRITE :
				$this->error = UploadManager::CANT_WRITE;
				break;
			case UPLOAD_ERR_EXTENSION :
				$this->error = UploadManager::EXTENSION;
				break;
			default :
				$this->error = UploadManager::UNKNOWN;
				break;
		}
	}

	public function getName() {
		return $this->name;
	}

	public function getExtension() {
		if (!$this->extension)
			$this->extension = FileUtil::getExtension($this->name);
		return $this->extension;
	}

	public function getContents($deleteTempFile=false) {
		if ($this->uploaded) {
			$contents = file_get_contents($this->tempPath);
			if ($deleteTempFile)
				unlink($this->tempPath);
			return $contents;
		}
		return null;
	}

	public function getPath() {
		return ($this->saved ? $this->path : $this->tempPath);
	}

	public function getTempPath() {
		return $this->tempPath;
	}

	public function getMimeType() {
		if (!$this->mimeType)
			$this->mimeType = FileUtil::getMimeType($this);
		return $this->mimeType;
	}

	public function getSize() {
		if (!$this->size)
			$this->size = FileUtil::getSize($this->tempPath);
		return $this->size;
	}

	public function getError() {
		return $this->error;
	}

	public function getUploadKey() {
		return $this->uploadKey;
	}

	public function getUploaded() {
		return $this->uploaded;
	}

	public function getSaved() {
		return $this->saved;
	}

	public function getValid() {
		return $this->valid;
	}

	public function saveTo($path, $mode=0666) {
		if ($this->uploaded && !$this->saved) {
			$dir = realpath($path);
			if (!is_dir($dir) || !is_writable($dir))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, '"%s" is not a writeable directory.', array($path)));
			if (!@move_uploaded_file($this->tempPath, $dir . DS . $this->name))
				throw new UploadFileException(__(PHP2GO_LANG_DOMAIN, 'File "%s" could not be saved.', array($this->name)));
			chmod($dir . DS . $this->name, $mode);
			$this->path = $dir;
			$this->saved = true;
		}
	}

	public function saveAs($path, $mode=0666) {
		if ($this->uploaded && !$this->saved) {
			$dir = realpath(dirname($path));
			$file = basename($path);
			if (!is_dir($dir) || !is_writable($dir))
				throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, '"%s" is not a writeable directory.', array($path)));
			if (!@move_uploaded_file($this->tempPath, $path))
				throw new UploadFileException(__(PHP2GO_LANG_DOMAIN, 'File "%s" could not be saved.', array($this->name)));
			chmod($path, $mode);
			$this->path = $dir;
			$this->name = $file;
			$this->saved = true;
		}
	}

	public function __toString() {
		return $this->name;
	}
}

class UploadFileException
{
}