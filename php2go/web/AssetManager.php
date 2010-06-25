<?php

class AssetManager extends Component
{
	protected $basePath;
	protected $baseUrl;
	protected $exclude = array('CVS', '.git', '.svn');
	protected $published = array();

	public function __construct() {
		$this->setBasePath(Php2Go::app()->getRootPath() . DS . 'assets');
		$this->baseUrl = Php2Go::app()->getBaseUrl() . '/assets';
	}

	public function getBasePath() {
		return $this->basePath;
	}

	public function setBasePath($path) {
		if (($real = realpath($path)) !== false && is_dir($real) && is_writeable($real))
			$this->basePath = $real;
		else
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Asset base path "%s" needs to be a directory writeable by the Web server.', array($path)));
	}

	public function getBaseUrl() {
		return $this->baseUrl;
	}

	public function setBaseUrl($url) {
		$this->baseUrl = rtrim($url, '/');
	}

	public function setExclude($exclude) {
		$this->exclude = array_merge($this->exclude, $exclude);
	}

	public function getPublishedPath($path) {
		if (($src = realpath($path)) !== false) {
			if (is_file($src))
				return $this->basePath . DS . Util::hash(dirname($src)) . DS . basename($src);
			else
				return $this->basePath . DS . Util::hash($src);
		}
		return null;
	}

	public function getPublishedUrl($path) {
		if (isset($this->published[$path]))
			return $this->published[$path];
		if (($src = realpath($path)) !== false) {
			if (is_file($src))
				return $this->baseUrl . '/' . Util::hash(dirname($src)) . '/' . basename($path);
			else
				return $this->baseUrl . '/' . Util::hash($src);
		}
		return null;
	}

	public function publish($path, $level=-1, $force=false) {
		if (isset($this->published[$path]))
			return $this->published[$path];
		if (($src = realpath($path)) !== false) {
			if (is_file($src)) {
				$dir = Util::hash(dirname($src));
				$file = basename($src);
				$targetDir = $this->basePath . DS . $dir;
				$targetFile = $targetDir . DS . $file;
				if (@filemtime($targetFile) < @filemtime($src) || $force) {
					if (!is_dir($targetDir)) {
						mkdir($targetDir);
						@chmod($targetDir, 0666);
					}
					copy($src, $targetFile);
				}
				return ($this->published[$path] = $this->baseUrl . '/' . $dir . '/' . $file);
			} elseif (is_dir($src)) {
				$dir = Util::hash($src);
				$targetDir = $this->basePath . DS . $dir;
				if (!is_dir($targetDir) || $force)
					DirectoryUtil::copy($src, $targetDir, array('exclude' => $this->exclude, 'level' => -1));
				return ($this->published[$path] = $this->baseUrl . '/' . $dir);
			}
		} else {
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'The asset path "%s" does not exist.', array($path)));
		}
	}
}