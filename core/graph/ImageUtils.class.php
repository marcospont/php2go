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

import('php2go.graph.Image');

/**
 * Collection of utility methods
 *
 * One of its features is to collect information about the
 * installed GD library.
 *
 * @package graph
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class ImageUtils extends PHP2Go
{
	/**
	 * Get installed GD version
	 *
	 * @return float
	 * @static
	 */
	function getGDVersion() {
		static $version;
		if (!isset($version)) {
			if (!extension_loaded('gd')) {
				$version = NULL;
			} elseif (function_exists('gd_info')) {
				$info = gd_info();
				preg_match("/\d/", $info['GD Version'], $match);
				$version = $match[0];
			} elseif (!preg_match("/phpinfo/", ini_get('disable_functions'))) {
				ob_start();
				phpinfo(8);
				$info = ob_get_clean();
				$info = stristr($info, 'gd version');
				preg_match("/\d/", $info, $match);
				$version = $match[0];
			} else {
				$version = NULL;
			}
		}
		return $version;
	}

	/**
	 * Check JPEG support
	 *
	 * @return bool
	 * @static
	 */
	function jpgSupported() {
		return function_exists('imagecreatefromjpeg');
	}

	/**
	 * Check TrueType fonts support
	 *
	 * @return bool
	 * @static
	 */
	function ttfSupported() {
		return function_exists('imagettftext');
	}

	/**
	 * Generates a temp name for a given image type
	 *
	 * @param int $imageType Image type
	 * @param int $length Name length
	 * @return string
	 * @static
	 */
	function getTempName($imageType=IMAGETYPE_PNG, $length=8) {
		switch ($imageType) {
			case IMAGETYPE_GD :
				$extension = '.gd';
				break;
			case IMAGETYPE_GD2 :
				$extension = '.gd2';
				break;
			case IMAGETYPE_GIF :
				$extension = '.gif';
				break;
			case IMAGETYPE_JPEG :
				$extension = '.jpg';
				break;
			case IMAGETYPE_PNG :
				$extension = '.png';
				break;
			case IMAGETYPE_WBMP :
				$extension = '.wbmp';
				break;
			case IMAGETYPE_XBM :
				$extension = '.xbm';
				break;
			default :
				$extension = '';
		}
		$filename = substr(md5(uniqid(rand(), TRUE)), 0, $length);
		return $filename . $extension;
	}

	/**
	 * Converts values of 3 RGB channels to its hexa representation
	 *
	 * @param int $red Red channel
	 * @param int $green Green channel
	 * @param int $blue Blue channel
	 * @return string
	 * @static
	 */
	function fromRGBToHex($red, $green, $blue) {
		return '#' . dechex($red) . dechex($green) . dechex($blue);
	}

	/**
	 * Convert a color in hexa to an array containing
	 * values of the 3 RGB channels
	 *
	 * @param string $hex Hexa color
	 * @return array
	 * @static
	 */
	function fromHexToRGB($hex) {
		if (preg_match("/#[0-9A-Fa-f]{6}/", $hex)) {
			$tmp = substr($hex, 1);
			$red = hexdec(substr($tmp, 0, 2));
			$green = hexdec(substr($tmp, 2, 2));
			$blue = hexdec(substr($tmp, 4, 2));
			return array($red, $green, $blue);
		}
		return NULL;
	}
}
?>