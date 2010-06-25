<?php

final class System
{
	public static function getMicrotime() {
		return microtime(true);
	}
	
	public static function getMemoryUsage($realUsage=false) {
		if (function_exists('memory_get_usage')) {
			$memory = memory_get_usage($realUsage);
		} else {
			$pid = getmypid();
			$output = array();
			if (IS_WINDOWS) {				
				exec('tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output);
				$memory = (isset($output[5]) ? (preg_replace('/[\D]/', '', $output[5]) * 1024) : 0);
			} else {
				exec('ps -eo%mem,rss,pid | grep ' . $pid, $output);
				$output = explode('  ', $output[0]);
				$memory = (isset($output[1]) ? ($output[1] * 1024) : 0);
			}
		}
		return Util::toByteString($memory);
	}
	
	public static function getTempDir() {
		foreach (array($_ENV, $_SERVER) as $src) {
			foreach (array('TMPDIR', 'TEMP', 'TMP', 'windir', 'SystemRoot') as $key) {
				if (isset($src[$key])) {
					if ($key == 'windir' || $key == 'SystemRoot')
						$dir = realpath($src[$key] . '\\temp');
					else
						$dir = realpath($src[$key]);
					if (is_readable($dir) && is_writeable($dir))
						return $dir;
				}
			}
		}
		$upload = ini_get('upload_tmp_dir');
		if ($upload) {
			$dir = realpath($upload);
			if (is_readable($dir) && is_writeable($dir))
				return $dir;
		}
		if (function_exists('sys_get_temp_dir')) {
			$dir = sys_get_temp_dir();
			if (is_readable($dir) && is_writeable($dir))
				return $dir;
		}
		$tempFile = tempname(md5(uniqid(rand(), true)), '');
		if ($tempFile) {
			$dir = realpath(dirname($tempFile));
			unlink($tempFile);
			if (is_readable($dir) && is_writeable($dir))
				return $dir;
		}
		if (is_readable('/tmp') && is_writeable('/tmp'))
			return '/tmp';
		if (is_readable('\\temp') && is_writeable('\\temp'))
			return '\\temp';
		throw new Exception('Could not determine system\'s temp directory.');
	}
	
	public static function which($program, $fallback=false) {
		if (!is_string($program) || empty($program))
			return $fallback;
		if (basename($program) != $program) {
			$pathElements = array(dirname($program));
			$program = basename($program);
		} else {
			if (!ini_get('safe_mode') || !($path = ini_get('safe_mode_exec_dir'))) {
				$path = getenv('PATH');
				if (!$path)
					$path = getenv('Path');
			}
			$pathElements = explode(PATH_SEPARATOR, $path);
		}		
		if (IS_WINDOWS) {
			$pathExtensions = getenv('PATHEXT');
			$extensions = ($pathExtensions ? explode(PATH_SEPARATOR, $pathExtensions) : array('.exe', '.bat', '.cmd', '.com'));
			foreach ($extensions as $extension) {
				foreach ($pathElements as $path) {
					$file = $path . DS . $program . $extension;
					if (@is_executable($file))
						return $file;
				}
			}
			$extension = FileUtil::getExtension($program);
			if (!empty($extension)) {
				$file = $path . DS . $program;
				if (@is_executable($file))
					return $file;
			}
		} else {
			foreach ($pathElements as $path) {
				$file = $path . DS . $program;
				if (@is_executable($file))
					return $file;
			}
		}
		return $fallback;
	}	
}
