<?php

final class DirectoryUtil
{
	public static function getFiles($dir, array $options=array()) {
		if (($dir = realpath($dir)) === false || !is_dir($dir))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid directory: %s.', array($dir)));
		$extensions = Util::consumeArray($options, 'extensions', array());
		if (is_string($extensions))
			$extensions = explode(',', $extensions);
		$exclude = Util::consumeArray($options, 'exclude', array());
		if (is_string($exclude))
			$exclude = explode(',', $exclude);
		$level = Util::consumeArray($options, 'level', -1);
		$files = self::getFilesRecursive($dir, '', $extensions, $exclude, $level);
		sort($files);
		return $files;
	}
	
	public static function getFilesRecursive($dir, $base, array $extensions, array $exclude, $level) {
		$files = array();
		$handle = opendir($dir);
		while (($file = readdir($handle)) !== false) {
			if ($file == '.' || $file == '..')
				continue;
			$path = $dir . DS . $file;
			$isFile = is_file($path);
			if (self::acceptPath($base, $file, $isFile, $extensions, $exclude)) {
				if ($isFile)
					$files[] = $path;
				elseif ($level)
					$files = array_merge($files, self::getFilesRecursive($path, $base . DS . $file, $extensions, $exclude, $level-1));
			}
		}
		closedir($handle);
		return $files;
	}
	
	public static function copy($src, $trg, array $options=array()) {
		if (($src = realpath($src)) === false || !is_dir($src))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid source directory: %s.', array($src)));
		if (!is_dir($trg) && !is_writeable(dirname($trg)))
			throw new InvalidArgumentException(__(PHP2GO_LANG_DOMAIN, 'Invalid target directory: %s.', array($trg)));
		$base = Util::consumeArray($options, 'base', '');
		$extensions = Util::consumeArray($options, 'extensions', array());
		if (is_string($extensions))
			$extensions = explode(',', $extensions);
		$exclude = Util::consumeArray($options, 'exclude', array());
		if (is_string($exclude))
			$exclude = explode(',', $exclude);
		$mode = Util::consumeArray($options, 'mode', 0777);
		$level = Util::consumeArray($options, 'level', -1);
		self::copyRecursive($src, $trg, $base, $extensions, $exclude, $mode, $level);
	}
	
	private static function copyRecursive($src, $trg, $base='', array $extensions, array $exclude, $mode=0777, $level) {
		@mkdir($trg);
		@chmod($trg, $mode);
		$dir = opendir($src);
		while (($file = readdir($dir)) !== false) {
			if ($file == '.' || $file == '..')
				continue;
			$path = $src . DS . $file;
			$isFile = is_file($path);
			if (self::acceptPath($base, $file, $isFile, $extensions, $exclude)) {
				if ($isFile)
					copy($path, $trg . DS . $file);
				elseif ($level)
					self::copyRecursive($path, $trg . DS . $file, $base . DS . $file, $extensions, $exclude, $mode, $level-1);	
			}				
		}		
	}
	
	private static function acceptPath($base, $file, $isFile, array $extensions=array(), array $exclude=array()) {
		foreach ($exclude as $item) {
			if ($file == $item || strpos($base . DS . $file, $item) === 0)
				return false;
		}
		if (!$isFile || empty($extensions))
			return true;
		if (($pos = strrpos($file, '.')) !== false) {
			$extension = (string)substr($file, $pos+1);
			return in_array($extension, $extensions);
		}
		return true;
	}
}