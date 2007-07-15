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

import('php2go.text.StringUtils');

/**
 * Implementation of a MIME message part
 *
 * @package net
 * @uses StringUtils
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class MailPart extends PHP2Go
{
	/**
	 * Boundary ID
	 *
	 * @var string
	 */
	var $boundaryId;

	/**
	 * Content ID
	 *
	 * @var string
	 */
	var $contentId;

	/**
	 * Charset
	 *
	 * @var string
	 */
    var $charset;

    /**
     * Content type
     *
     * @var string
     */
	var $contentType;

	/**
	 * Content encoding
	 *
	 * @var string
	 */
    var $contentEncoding;

    /**
     * Content disposition
     *
     * @var string
     */
    var $contentDisposition;

    /**
     * File name
     *
     * @var string
     */
	var $fileName;

	/**
	 * Content
	 *
	 * @var string
	 */
	var $content;

	/**
	 * Line end chars to be used when building the part
	 *
	 * @var string
	 */
    var $lineEnd;

    /**
     * Class constructor
     *
     * Set default values for some properties of the class.
     *
     * @return MailPart
     */
	function MailPart() {
		parent::PHP2Go();
        $this->boundaryId;
		$this->contentId = md5(uniqid(time()));
		$this->charset = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->contentType = 'text/plain';
		$this->contentEncoding = '8bit';
		$this->lineEnd = "\n";
    }

	/**
	 * Get part's boundary ID
	 *
	 * @return string
	 */
	function getBoundaryId() {
		return $this->boundaryId;
	}

	/**
	 * Set part's boundary ID
	 *
	 * @param string $bid Boundary ID
	 */
	function setBoundaryId($bid) {
		$this->boundaryId = $bid;
	}

	/**
	 * Get part's content ID
	 *
	 * @return string
	 */
	function getContentId() {
		return $this->contentId;
	}

	/**
	 * Set part's content ID
	 *
	 * @param string $cid Content ID
	 */
	function setContentId($cid) {
		$this->contentId = $cid;
	}

	/**
	 * Get part's charset
	 *
	 * @return string
	 */
	function getCharset() {
		return $this->charset;
	}

	/**
	 * Set part's charset
	 *
	 * @param string $charset Charset
	 */
	function setCharset($charset) {
		$this->charset = $charset;
	}

	/**
	 * Get part's content type
	 *
	 * @return string
	 */
	function getContentType() {
		return $this->contentType;
	}

	/**
	 * Set part's content type
	 *
	 * @param string $contentType Content type
	 */
	function setContentType($contentType) {
		$this->contentType = $contentType;
	}

	/**
	 * Get part's content encoding
	 *
	 * @return string
	 */
	function getEncoding() {
		return $this->contentEncoding;
	}

	/**
	 * Set part's content encoding
	 *
	 * @param string $encoding Content encoding
	 */
	function setEncoding($encoding) {
		$this->contentEncoding = $encoding;
	}

	/**
	 * Get part's content disposition
	 *
	 * @return string
	 */
	function getDisposition() {
		return isset($this->contentDisposition) ? $this->contentDisposition : NULL;
	}

	/**
	 * Set part's content disposition
	 *
	 * @param string $disposition Content disposition
	 */
	function setDisposition($disposition) {
		$this->contentDisposition = $disposition;
	}

	/**
	 * Get the name of the file associated with the part
	 *
	 * @return string
	 */
	function getFileName() {
		return isset($this->fileName) ? $this->fileName : NULL;
	}

	/**
	 * Loads part contents from the given $fileName
	 *
	 * @param string $fileName File name
	 */
	function setFileName($fileName) {
		$contents = @file_get_contents($fileName);
		if ($contents !== FALSE) {
			$this->fileName = basename($fileName);
			$this->content = $contents;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	/**
	 * Get part's content
	 *
	 * @return string
	 */
	function getContent() {
		return isset($this->content) ? $this->content : NULL;
	}

	/**
	 * Set part's content
	 *
	 * @param string $content
	 */
	function setContent($content) {
		$this->content = $content;
	}

	/**
	 * Encode the part's content, using the encoding set in
	 * the {@link contentEncoding} property
	 *
	 * @uses StringUtils::encode()
	 */
	function encodeContent() {
		if ($this->contentEncoding == 'quoted-printable')
			$this->_encodeQuotedPrintable();
		else
			$this->content = StringUtils::encode($this->content, $this->contentEncoding);
	}

    /**
     * Builds and returns the source code of the part
     *
     * @return string
     */
	function buildSource() {
		if (!isset($this->boundaryId))
			return '';
        $source  = sprintf("--%s%s", $this->boundaryId, $this->lineEnd);
        $source .= sprintf("Content-Type: %s", $this->contentType);
		if (!isset($this->contentDisposition))
			$source .= sprintf("; charset=\"%s\"", $this->charset);
		if (isset($this->fileName))
			$source .= sprintf(";%s\tname=\"%s\"", $this->lineEnd, $this->fileName);
		$source .= $this->lineEnd;
        $source .= sprintf("Content-Transfer-Encoding: %s%s", $this->contentEncoding, $this->lineEnd);
        if (isset($this->contentDisposition)) {
			if ($this->contentDisposition == 'inline')
				$source .= sprintf("Content-ID: %s%s", '<' . $this->contentId . '>', $this->lineEnd);
            $source .= sprintf("Content-Disposition: %s", $this->contentDisposition);
            if (isset($this->fileName))
                $source .= sprintf(";%s\tfilename=\"%s\"", $this->lineEnd, $this->fileName);
		}
		$source .= $this->lineEnd . $this->lineEnd;
		$source .= $this->content;
		return $source;
    }

	/**
	 * Encode part's content using quoted-printable mode
	 *
	 * @author Brent R. Matzelle <bmatzelle@yahoo.com>
	 * @access private
	 */
	function _encodeQuotedPrintable() {
		$this->content = str_replace("\r\n", "\n", $this->content);
		$this->content = str_replace("\r", "\n", $this->content);
		$this->content = str_replace("\n", $this->lineEnd, $this->content);
		if (!StringUtils::endsWith($this->content, $this->lineEnd))
			$this->content .= $this->lineEnd;
        $this->content = preg_replace('/([\000-\010\013\014\016-\037\075\177-\377])/e', "'='.sprintf('%02X', ord('\\1'))", $this->content);
        $this->content = preg_replace("/([\011\040])" . $this->lineEnd . "/e", "'='.sprintf('%02X', ord('\\1')).'" . $this->lineEnd . "'", $this->content);
        $this->content = StringUtils::wrap($this->content, 74);
	}
}
?>