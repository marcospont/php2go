<?php

class CacheBackendFile extends CacheBackend
{
	protected $cacheDir;
	protected $fileNamePrefix = 'cache';
	protected $fileExtension = '';
	public $fileMask = 0600;
	public $fileLock = true;
	public $readControl = true;
	protected $readControlType = 'crc32';
	public $metaDataCount = 100;
	private $metaData = array();	
	
	public function getCacheDir() {
		if ($this->cacheDir === null)
			$this->cacheDir = System::getTempDir() . DS;
		return $this->cacheDir;
	}
	
	public function setCacheDir($cacheDir) {
		if (!is_dir($cacheDir))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid directory: "%s".', array($cacheDir)));
		if (!is_writeable($cacheDir))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Directory "%s" is not writeable.', array($cacheDir)));
		$this->cacheDir = rtrim($cacheDir, '/\\') . DS;
	}
	
	public function getFileNamePrefix() {
		return $this->fileNamePrefix;
	}
	
	public function setFileNamePrefix($prefix) {
		if (!preg_match('~^[a-zA-Z0-9_]+$~D', $prefix))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid file name prefix: "%s".', array($prefix)));
		$this->fileNamePrefix = $prefix;
	}
	
	public function getFileExtension() {
		return $this->fileExtension;
	}
	
	public function setFileExtension($extension) {
		if (!empty($extension) && !preg_match('~[a-z]+~', $extension))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid file extension: "%s".', array($extension)));
		$this->fileExtension = (!empty($extension) ? '.' . $extension : '');
	}
	
	public function getReadControlType() {
		return $this->readControlType;
	}
	
	public function setReadControlType($type) {
		if (!in_array($type, array('md5', 'crc32', 'strlen', 'adler32')))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid read control type: "%s".', array($type)));
		$this->readControlType = $type;
	}
	
	public function load($id) {
		$metaData = $this->getMetaData($id);
		if (!$metaData || time() > $metaData['expire'])
			return false;
		$data = $this->readFile($this->idToFile($id));
		if ($data) {
			if ($this->readControl) {
				$hash = $this->hash($this->readControlType, $data);
				if ($hash != $metaData['hash']) {
					$this->backend->delete($id);
					return false;
				}
			}
			return $data;
		}
		return false;
	}
	
	public function contains($id) {
		clearstatcache();
		$metaData = $this->getMetaData($id);
		if ($metaData && file_exists($this->idToFile($id)))
			return $metaData['time'];
		return false;
	}
	
	public function save($data, $id, $lifetime=false) {
		clearstatcache();
		$time = time();
		$lifetime = ($lifetime === false ? $this->lifetime : $lifetime);
		$path = $this->idToFile($id);
		$metaData = array(
			'hash' => ($this->readControl ? $this->hash($this->readControlType, $data) : ''),
			'time' => $time,
			'expire' => ($lifetime === null ? 9999999999 : $time + $lifetime)
		);
		if (!$this->setMetaData($id, $metaData))
			return false;
		return $this->writeFile($path, $data);
	}
	
	public function touch($id, $lifetime) {
		$time = time();
		$metaData = $this->getMetaData($id);
		if ($metaData)
			return false;		
		if ($time > $metaData['expire'])
			return false;
		$newMetaData = array(
			'time' => $time,
			'expire' => $metaData['expire'] + $lifetime			
		);
		return $this->setMetaData($id, $newMetaData);
	}
	
	public function delete($id) {
		$path = $this->idToFile($id);
		return ($this->deleteFile($path) && $this->deleteMetaData($id));
	}
	
	public function clean($mode=Cache::CLEANING_MODE_ALL, $param=null) {
		$result = true;
		$glob = @glob($this->getCacheDir() . $this->fileNamePrefix . '---' . ($mode == Cache::CLEANING_MODE_PATTERN ? $param : '') . '*');
		if ($glob !== false) {
			foreach ($glob as $filePath) {
				if (is_file($filePath)) {
					$fileName = basename($filePath);
					if ($this->isMetaDataFile($fileName))
						continue;
					$id = $this->fileToId($fileName);
					$metaData = $this->getMetaData($id);
					if ($metaData === false)
						continue;
					switch ($mode) {
						case Cache::CLEANING_MODE_ALL :
						case Cache::CLEANING_MODE_PATTERN :
							$result = $this->delete($id) && $result;
							break;
						case Cache::CLEANING_MODE_EXPIRED :
							if (time() > $metaData['expire'])
								$result = $this->delete($id) && $result;
							break;
					}
				}
			}
		}
		return $result;
	}
	
	public function getIds() {
		$result = array();
		$glob = @glob($this->getCacheDir() . $this->fileNamePrefix . '---*');
		if ($glob !== false) {
			foreach ($glob as $filePath) {
				if (is_file($filePath)) {
					$fileName = basename($filePath);
					if ($this->isMetaDataFile($fileName))
						continue;
					$id = $this->fileToId($fileName);
					$metaData = $this->getMetaData($id);
					if ($metaData === false || time() > $metaData['expire'])
						continue;
					$result[] = $id;
				}
			}
		}
		return $result;			
	}
	
	public function getUsage() {
		$cacheDir = $this->getCacheDir();
		$free = disk_free_space($cacheDir);
		$total = disk_total_space($cacheDir);
		if ($total == 0)
			throw new Exception(__(PHP2GO_LANG_DOMAIN, 'Backend usage could not be calculated.'));
		if ($free >= $total)
			return 100;
		return (round(100 * ($total - $free) / $total, 2));
	}
	
	public function getFeatures() {
		return array(
			'autoCleaning' => true,
			'list' => true,
			'usage' => true
		);
	}
	
	protected function getMetaData($id) {
		if (isset($this->metaData[$id]))
			return $this->metaData[$id];
		$metaData = $this->readMetaData($id);
		if (!$metaData)
			return false;
		if (count($this->metaData) >= $this->metaDataCount)
			$this->metaData = array_slice($this->metaData, 1);
		$this->metaData[$id] = $metaData;
		return $metaData;
	}
	
	protected function setMetaData($id, array $metaData) {
		$result = $this->writeMetaData($id, $metaData);
		if ($result) {
			if (count($this->metaData) >= $this->metaDataCount)
				$this->metaData = array_slice($this->metaData, 1);
			$this->metaData[$id] = $metaData;
		}
		return $result;		
	}
	
	protected function deleteMetaData($id) {
		if (isset($this->metaData[$id]))
			unset($this->metaData[$id]);
		$path = $this->idToMetaDataFile($id);
		return $this->deleteFile($path);
	}
	
	protected function readMetaData($id) {
		$metaDataFile = $this->idToMetaDataFile($id);
		$result = $this->readFile($metaDataFile);
		if ($result)
			return unserialize($result);
		return false;
	}
	
	protected function writeMetaData($id, array $metaData) {
		$metaDataFile = $this->idToMetaDataFile($id);
		return $this->writeFile($metaDataFile, serialize($metaData));		
	}
	
	protected function isMetaDataFile($fileName) {
		$test = $this->fileNamePrefix . '---metadata---';
		return (strpos($test, $fileName) === 0 && strlen($fileName) > strlen($test));
	}
	
	protected function idToMetaDataFile($id) {
		return $this->idToFile('metadata---' . $id);
	}
	
	protected function idToFile($id) {
		return $this->getCacheDir() . $this->fileNamePrefix . '---' . $id . $this->fileExtension;
	}
	
	protected function fileToId($fileName) {
        return preg_replace('~^' . $this->fileNamePrefix . '---(.*)' . $this->fileExtension . '$~', '$1', $fileName);		
	}
	
	protected function hash($type, $contents) {
		switch ($type) {
			case 'md5' :
				return md5($contents);
			case 'crc32' :
				return crc32($contents);
			case 'strlen' :
				return strlen($contents);
			case 'adler32' :
				return hash('adler32', $contents);
		}
	}
	
	protected function readFile($path) {
		if (is_file($path)) {
			$fp = @fopen($path, 'rb');
			if ($fp !== false) {
				if ($this->fileLock)
					@flock($fp, LOCK_SH);
				$contents = stream_get_contents($fp);
				if ($this->fileLock)
					@flock($fp, LOCK_UN);
				@fclose($fp);
				return $contents;
			} else {
				throw new IOException(__(PHP2GO_LANG_DOMAIN, 'File "%s" is not readable.', array($path)));
			}
		}
		return false;		
	}
	
	protected function writeFile($path, $contents) {
		$result = false;
		$fp = @fopen($path, 'ab+');
		if ($fp !== false) {
			if ($this->fileLock)
				@flock($fp, LOCK_EX);
			fseek($fp, 0);
			ftruncate($fp, 0);
			if (@fwrite($fp, $contents) !== false)
				$result = true;
			@fclose($fp);
			@chmod($fp, $this->fileMask);
		} else {
			throw new IOException(__(PHP2GO_LANG_DOMAIN, 'File "%s" is not writeable.', array($path)));
		}
		return $result;
	}
	
	protected function deleteFile($path) {
		if (is_file($path) && @unlink($path))
			return true;
		return false;
	}
}