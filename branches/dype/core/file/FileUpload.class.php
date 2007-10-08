<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

import('php2go.util.Callback');

/**
 * Maximum size for file uploads
 */
define('FILE_UPLOAD_MAX_SIZE', 2000000);

/**
 * Handles file uploads
 *
 * The FileUpload class can validate the integrity of a file
 * upload operation. If a valid path and file name is provided,
 * the class can automatically move the uploaded file from the
 * temporary folder to a user-defined target folder.
 *
 * This class is highly integrated with the {@link FileField} form
 * component. This means that this class is used to process the
 * uploaded file right after the field is validated.
 *
 * Example using a single handler:
 * <code>
 * $upload = new FileUpload();
 * $upload->setAllowedTypes('image/gif', 'image/jpeg');
 * $upload->setMaxFileSize('100K');
 * $upload->setOverwriteFiles(FALSE);
 * $upload->addHandler('pic', 'tmp/', '', 0777);
 * if (!$upload->upload()) {
 *   print $upload->getErrorAt(0);
 * } else {
 *   print "Upload successful!";
 * }
 * </code>
 *
 * @package file
 * @uses Callback
 * @uses TypeUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class FileUpload extends PHP2Go
{
	/**
	 * Registered upload handlers
	 *
	 * @var array
	 */
	var $uploadHandlers = array();

	/**
	 * Allowed mime-types
	 *
	 * @var array
	 */
	var $allowedFileTypes = array();

	/**
	 * Maximum file size
	 *
	 * @var int
	 */
	var $maxFileSize = FILE_UPLOAD_MAX_SIZE;

	/**
	 * Verbose signs that should be removed from file names
	 *
	 * @var string
	 */
	var $verboseSigns = "[[:space:]]|[\"\*\\\'\%\$\&\@\<\>]";

	/**
	 * Whether to overwrite existing files
	 *
	 * @var bool
	 */
	var $overwriteFiles = TRUE;

	/**
	 * User-defined callback to process the uploaded file
	 *
	 * @var object Callback
	 */
	var $SaveCallback = NULL;

	/**
	 * Class constructor
	 *
	 * @return FileUpload
	 */
	function FileUpload() {
		parent::PHP2Go();
	}

	/**
	 * Get the singleton of the FileUpload class
	 *
	 * @return FileUpload
	 * @static
	 */
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new FileUpload();
		return $instance;
	}

	/**
	 * Reset class defaults
	 *
	 * Returns to the default settings for max file size,
	 * allowed file types and overwrite files flag.
	 */
	function resetDefaults() {
		$this->maxFileSize = FILE_UPLOAD_MAX_SIZE;
		$this->allowedFileTypes = array();
		$this->overwriteFiles = TRUE;
	}

	/**
	 * Set allowed mime types
	 *
	 * @param string $type,... Expects a list of mime-types
	 */
	function setAllowedTypes() {
		$this->allowedFileTypes = func_get_args();
	}

	/**
	 * Set maximum file size
	 *
	 * Accepts integer or string values:
	 * <code>
	 * $upload = new FileUpload();
	 * $upload->setMaxFileSize(2000000);
	 * $upload->setMaxFileSize('2M');
	 * $upload->setMaxFileSize('500K');
	 * </code>
	 *
	 * @param int|string $maxSize Max file size
	 */
	function setMaxFileSize($maxSize) {
		$maxPHP = str_replace(array('g', 'G', 'm', 'M', 'k', 'K'), array('000000000', '000000000', '000000', '000000', '000', '000'), System::getIni('upload_max_filesize'));
		$maxUser = str_replace(array('g', 'G', 'm', 'M', 'k', 'K'), array('000000000', '000000000', '000000', '000000', '000', '000'), $maxSize);
		$this->maxFileSize = min(intval($maxPHP), abs(intval($maxUser)));
	}

	/**
	 * Enable/disable overwrite of existing files
	 *
	 * @param bool $setting Enable/disable
	 */
	function setOverwriteFiles($setting=TRUE) {
		$this->overwriteFiles = (bool)$setting;
	}

	/**
	 * Define the callback function that will process the
	 * upload operations, overriding the internal handler
	 *
	 * When a save callback is used, the uploaded file is validated
	 * but not moved from the temporary directory. The task of copying
	 * or moving the file is passed to the user-defined function.
	 *
	 * A save callback receives an array (the upload handler) and
	 * should return the same handler (with needed transformations):
	 * <code>
	 * function uploadHandler($handler) {
	 *   $name = $handler['save_name'];
	 *   /* apply some transformations in the file name {@*}
	 *   $name = trim(strtolower($name));
	 *   $name = preg_replace('/\s+/', '_', $name);
	 *   $name = date('dmY') . '_' . $name;
	 *   if (move_uploaded_file($handler['tmp_name'], $handler['save_path'] . $name)) {
	 *     $handler['save_name'] = $name;
	 *     chmod($handler['save_path'] . $handler['save_name'], $handler['save_mode']);
	 *   } else {
	 *     $handler['error'] = "It wasn't possible to move the uploaded file {$handler['save_name']}";
	 *   }
	 *   /* a save callback must always return back the modified upload handler {@*}
	 *   return $handler;
	 * }
	 *
	 * $upload = new FileUpload();
	 * $upload->setAllowedTypes('image/gif');
	 * $upload->setMaxFileSize('100K');
	 * $upload->addHandler('pic', 'tmp/', '', NULL, 'uploadHandler');
	 * if (!$upload->upload()) {
	 *   print $upload->getErrorAt(0);
	 * } else {
	 *   print "Upload successful!";
	 * }
	 * </code>
	 *
	 * @param mixed $callback Function name, class/method or object/method
	 */
	function setSaveCallback($callback) {
		$this->SaveCallback =& new Callback($callback);
	}

	/**
	 * Register a new upload handler
	 *
	 * @param string $fieldName Input field name
	 * @param string $savePath Save path
	 * @param string $saveName Save name
	 * @param int $mode Create mode
	 * @param mixed $callback Save callback
	 * @return int Handler index
	 */
	function addHandler($fieldName, $savePath='', $saveName='', $mode=0644, $callback=NULL) {
		$newHandler = array(
			'uploaded' => FALSE,
			'field' => $fieldName,
			'save_path' => (!empty($savePath) ? $savePath : getcwd()),
			'save_name' => $saveName,
			'save_mode' => $mode,
			'save_callback' => NULL,
			'error' => ''
		);
		if (!is_null($callback))
			$newHandler['save_callback'] = new Callback($callback);
		$this->uploadHandlers[] =& $newHandler;
		return sizeof($this->uploadHandlers) - 1;
	}

	/**
	 * Get the upload handler associated with the input field $field
	 *
	 * Returns an array containing:
	 * # uploaded (bool) : whether the file was uploaded by the user
	 * # field (string) : field name
	 * # save_path (string) : save path
	 * # save_name (string) : save name
	 * # save_mode (int) : save mode
	 * # save_callback (object) : save callback function
	 * # error (string) : upload error
	 *
	 * When this method is called after {@link upload()}, the handler
	 * data will already contain any errors detected in the upload
	 * process, as long as any transformations applied in the save
	 * path and save name due to security reasons.
	 *
	 * @param string $field Field name
	 * @return array|bool
	 */
	function getHandlerByName($field) {
		if (empty($this->uploadHandlers))
			return FALSE;
		for ($i=0; $i<sizeof($this->uploadHandlers); $i++) {
			if ($this->uploadHandlers[$i]['field'] == $field)
				return $this->uploadHandlers[$i];
		}
		return FALSE;
	}

	/**
	 * Get the upload error for a given handler index
	 *
	 * Returns FALSE when the handler doesn't exist or when
	 * the handler doesn't contain an associated error message.
	 *
	 * @param int $index Handler index
	 * @return string|bool
	 */
	function getErrorAt($index) {
		if (isset($this->uploadHandlers[$index]))
			return ($this->uploadHandlers[$index]['error'] != '' ? $this->uploadHandlers[$index]['error'] : FALSE);
		return FALSE;
	}

	/**
	 * Log all upload errors in a given file
	 *
	 * Each line written in the log file contains upload
	 * field name, upload mime-type, upload file size,
	 * save path, save name and error message.
	 *
	 * The log file is opened using "append" mode.
	 *
	 * @param string $logFile Log file path
	 * @param string $lineEnd Line end char(s)
	 */
	function logErrors($logFile, $lineEnd="\n") {
		$errors = '';
		for ($i=0; $i<sizeof($this->uploadHandlers); $i++) {
			if ($this->uploadHandlers[$i]['error'] != '')
				$errors .= $this->uploadHandlers[$i]['name'] . ';' . $this->uploadHandlers[$i]['type'] . ';' . $this->uploadHandlers[$i]['size'] . ';' . $this->uploadHandlers[$i]['save_path'] . ';' . $this->uploadHandlers[$i]['save_name'] . ';' . $this->uploadHandlers[$i]['error'] . $lineEnd;
		}
		if (!empty($errors)) {
			$fp = @fopen($logFile, 'a');
			if ($fp === FALSE)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $logFile), E_USER_ERROR, __FILE__, __LINE__);
			fputs($fp, $errors);
			fclose($fp);
		}
	}

	/**
	 * Process a single handler or all registered upload handlers
	 *
	 * @param int $i Handler index
	 * @return bool
	 */
	function upload($i=NULL) {
		if (TypeUtils::isInteger($i)) {
			return $this->_uploadFile($i);
		} else {
			$operationResult = TRUE;
			for ($i=0; $i<sizeof($this->uploadHandlers); $i++)
				$operationResult &= $this->_uploadFile($i);
			return (bool)$operationResult;
		}
	}

	/**
	 * Process an upload handler
	 *
	 * # Update handler data using information found in the $_FILES superglobal
	 * # Detect errors reported by PHP (UPLOAD_ERR_**)
	 * # Call {@link _checkFile()} to validate the file
	 * # Call {@link _moveFile()}, which in turn will move the file to the
	 *   requested target or call the associated save callback
	 *
	 * @param int $i Handler index
	 * @access private
	 * @return bool
	 */
	function _uploadFile($i) {
		if (!array_key_exists($i, $this->uploadHandlers)) {
			return FALSE;
		} else {
			$fileData =& $this->uploadHandlers[$i];
			if (isset($_FILES[$fileData['field']]) && !empty($_FILES[$fileData['field']]['name'])) {
				$file = $_FILES[$fileData['field']];
				$fileData['uploaded'] = TRUE;
				$fileData['name'] = $file['name'];
				$fileData['type'] = $file['type'];
				$fileData['tmp_name'] = $file['tmp_name'];
				$fileData['size'] = $file['size'];
				// errors reported by PHP
				if (isset($file['error']) && $file['error'] != UPLOAD_ERR_OK) {
					switch ($file['error']) {
						case UPLOAD_ERR_INI_SIZE :
						case UPLOAD_ERR_FORM_SIZE :
							$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_TOO_BIG');
							break;
						case UPLOAD_ERR_PARTIAL :
						case UPLOAD_ERR_NO_FILE :
						case UPLOAD_ERR_NO_TMP_DIR :
							$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_NOT_FOUND');
							break;
						default :
							$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_NOT_FOUND');
							break;
					}
					return FALSE;
				} else {
					// validates the file
					if ($this->_checkFile($i)) {
						// moves the file
						if (!$this->_moveFile($i)) {
							return FALSE;
						}
						return TRUE;
					} else {
						return FALSE;
					}
				}
			} else {
				$fileData['uploaded'] = FALSE;
				return TRUE;
			}
		}
	}

	/**
	 * Validates the uploaded file
	 *
	 * Validates:
	 * # mime-type, according to {@link allowedFileTypes}
	 * # file size, according to {@link maxFileSize}
	 * # file name and extension
	 *
	 * @param int $index Handler index
	 * @return bool
	 */
	function _checkFile($index) {
		$fileData =& $this->uploadHandlers[$index];
		if ($fileData['size'] <= 0) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_NOT_FOUND');
			return FALSE;
		}
		else if (!empty($this->allowedFileTypes) && !in_array($fileData['type'], $this->allowedFileTypes)) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_INVALID_TYPE', $fileData['type']);
			return FALSE;
		}
		else if ($fileData['size'] > $this->maxFileSize) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_TOO_BIG');
			return FALSE;
		}
		else if (!is_uploaded_file($fileData['tmp_name'])) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_NOT_FOUND');
			return FALSE;
		}
		else if (ereg("\.+.+\.+", $fileData['name'])){
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_INVALID_NAME', $fileData['name']);
			return FALSE;
		}
		else if (System::isGlobalsOn() && ( isset($_GET[$fileData['name']]) || isset($_POST[$fileData['name']]) || isset($_COOKIE[$fileData['name']]) )) {
			$fileData['error'] = PHP2Go::getLangVal('ERR_UPLOAD_INVALID_NAME', $fileData['name']);
			return FALSE;
		}
		else {
			return TRUE;
		}
	}

	/**
	 * Moves an uploaded file, or calls the associated save callback
	 *
	 * @param int $index Handler index
	 * @access private
	 * @return bool
	 */
	function _moveFile($index) {
		$fileData =& $this->uploadHandlers[$index];
		$fileData['save_name'] = empty($fileData['save_name']) ? $fileData['name'] : $fileData['save_name'];
		if (substr($fileData['save_path'], -1) != '/')
			$fileData['save_path'] .= '/';
		if (is_object($fileData['save_callback'])) {
			$ret = $fileData['save_callback']->invoke($fileData);
			if (!is_array($ret)) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_UPLOAD_CANT_MOVE');
				return FALSE;
			} else {
				$fileData = array_merge($fileData, $ret);
				return (empty($fileData['error']));
			}
		} elseif (is_object($this->SaveCallback)) {
			$ret = $this->SaveCallback->invoke($fileData);
			if (!is_array($ret)) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_UPLOAD_CANT_MOVE');
				return FALSE;
			} else {
				$fileData = array_merge($fileData, $ret);
				return (empty($fileData['error']));
			}
		} else {
			$fileData['name'] = eregi_replace($this->verboseSigns, '', $fileData['name']);
			if (!is_dir($fileData['save_path'])) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_CANT_FIND_FILE', $fileData['save_path']);
				return FALSE;
			} elseif (!$this->overwriteFiles && file_exists($fileData['save_path'] . $fileData['save_name'])) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_UPLOAD_FILE_EXISTS', $fileData['save_name']);
				return FALSE;
			} elseif (!@is_writable($fileData['save_path'])) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $fileData['save_path']);
				return FALSE;
			} elseif (!@move_uploaded_file($fileData['tmp_name'], $fileData['save_path'] . $fileData['save_name'])) {
				$fileData['error'] .= PHP2Go::getLangVal('ERR_UPLOAD_CANT_MOVE');
				return FALSE;
			} else {
				if (!is_null($fileData['save_mode']) && !@chmod($fileData['save_path'] . $fileData['save_name'], $fileData['save_mode'])) {
					$fileData['error'] .= PHP2Go::getLangVal('ERR_CANT_CHANGE_MODE', array($fileData['save_mode'], $fileData['save_path'] . $fileData['save_name']));
					return FALSE;
				}
				return TRUE;
			}
		}
	}
}
?>