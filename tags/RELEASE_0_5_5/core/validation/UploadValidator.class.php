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

import('php2go.file.FileUpload');

/**
 * Validates and processes file uploads
 *
 * @package validation
 * @uses FileUpload
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class UploadValidator extends AbstractValidator
{
	/**
	 * Instance of the upload processor
	 *
	 * @var FileUpload
	 * @access private
	 */
	var $Uploader = NULL;

	/**
	 * Class constructor
	 *
	 * @param array $params Arguments
	 * @return UploadValidator
	 */
	function UploadValidator($params=NULL) {
		parent::AbstractValidator($params);
		$this->Uploader =& FileUpload::getInstance();
	}

	/**
	 * Validates and processes the upload of a file
	 *
	 * @param array $upload Contains the file ID and upload settings
	 * @return bool
	 */
	function execute($upload) {
		if (isset($upload['MAXFILESIZE']))
			$this->Uploader->setMaxFileSize($upload['MAXFILESIZE']);
		if (isset($upload['ALLOWEDTYPES']))
			call_user_func_array(array(&$this->Uploader, 'setAllowedTypes'), TypeUtils::toArray($upload['ALLOWEDTYPES']));
		if (isset($upload['OVERWRITE']))
			$this->Uploader->setOverwriteFiles($upload['OVERWRITE']);
		if (isset($upload['FIELDNAME'])) {
			$idx = $this->Uploader->addHandler($upload['FIELDNAME'], @$upload['SAVEPATH'], @$upload['SAVENAME'], @$upload['SAVEMODE'], @$upload['SAVEFUNCTION']);
			if ($this->Uploader->upload($idx)) {
				return TRUE;
			} else {
				$this->errorMessage = $this->Uploader->getErrorAt($idx);
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
}
?>