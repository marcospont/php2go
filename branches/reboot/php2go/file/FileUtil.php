<?

final class FileUtil
{
	private static $mimeTable = array();
	
	public static function getExtension($path) {
		if (($pos = strrpos($path, '.')) !== false)
			return (string)substr($path, $pos+1);
		return '';
	}
	
	public static function getSize($path) {
		if (!file_exists($path))
			return 0;
		$size = @filesize($path);
		if ($size !== false)
			return sprintf('%u', $size);
		return 0;
	}
	
	public static function getMimeType($path) {
		if ($path instanceof UploadFile) {
			$name = $path->getName();
			$path = $path->getPath();
		} else {			
			$name = basename($path);
			$path = realpath($path);
		}		
		if (!$path || !is_readable($path))
			return '';
		if (function_exists('finfo_open')) {
			$info = finfo_open(FILEINFO_MIME);
			$type = finfo_file($info, $path);
			finfo_close($info);
			if ($type !== false && $type !== '') {
				if (($pos = strpos($type, ';')) !== false)
					$type = substr($type, 0, $pos);
				return $type;
			}
		}
		if (function_exists('mime_content_type')) {
			$type = mime_content_type($path);
			if ($type !== false && $type !== '') {
				if (($pos = strpos($type, ';')) !== false)
					$type = substr($type, 0, $pos);
				return $type;					
			}
		}
		if ($cmdPath = System::which('file')) {			
			$matches = array();
			$output = @exec(escapeshellcmd('file') . ' -bi ' . escapeshellarg($path));
			if (preg_match('!([\w-]+/[\w\d_+-]+)!', $output, $matches))
				return $matches[1];			
		}
		if (!self::$mimeTable)
			self::$mimeTable = require(Php2Go::getPathAlias('php2go.file.mimeTypes') . '.php');
		$extension = strtolower(self::getExtension($name));
		if (isset(self::$mimeTable[$extension]))
			return self::$mimeTable[$extension];
		return 'application/octet-stream';
	}
}