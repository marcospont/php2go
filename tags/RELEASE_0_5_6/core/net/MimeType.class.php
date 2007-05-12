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

/**
 * Default MIME type to be used on unknown file extensions
 */
define("DEFAULT_MIME_TYPE", "application/octet-stream");

import('php2go.file.FileSystem');

/**
 * Resolve MIME types from file extensions or file names
 *
 * This class keeps a table mapping file extensions to MIME types. This
 * MIME type table follows the patterns accepted by most browsers and
 * operating systems.
 *
 * @package net
 * @uses FileSystem
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class MimeType extends PHP2Go
{
	/**
	 * Check if the given file extension is present in the internal MIME table
	 *
	 * @param string $fileExtension File extension
	 * @return bool
	 * @static
	 */
	function has($fileExtension) {
		$fileExtension = strtolower($fileExtension);
		$mimeTable = MimeType::getMimeTable();
		return isset($mimeTable[$fileExtension]);
	}

	/**
	 * Resolve the MIME type of a given file extension
	 *
	 * @param string $fileExtension File extension
	 * @return string
	 * @static
	 */
	function get($fileExtension) {
		$fileExtension = strtolower($fileExtension);
		$mimeTable = MimeType::getMimeTable();
		return $mimeTable[$fileExtension];
	}

	/**
	 * Resolve the MIME type of a file
	 *
	 * @param string $fileName File path
	 * @deprecated Use {@link getFromFileName} instead
	 * @return string
	 * @static
	 */
	function getFromFile($fileName) {
		$extension = FileSystem::getFileExtension($fileName);
		return (MimeType::has($extension) ? MimeType::get($extension) : DEFAULT_MIME_TYPE);
	}

	/**
	 * Get the MIME type from a file name or path
	 *
	 * @param string $fileName File name or path
	 * @return string
	 * @static
	 */
	function getFromFileName($fileName) {
		$extension = FileSystem::getFileExtension($fileName);
		return (MimeType::has($extension) ? MimeType::get($extension) : DEFAULT_MIME_TYPE);
	}

	/**
	 * Get the internal MIME table
	 *
	 * @return array
	 * @static
	 */
	function getMimeTable() {
		static $mimeTable;
		if (!isset($mimeTable)) {
			$mimeTable = array(
				'3dm'	=>	'x-world/x-3dmf',
				'3dmf'	=>	'x-world/x-3dmf',
				'a'		=>	'application/octet-stream',
				'abc'	=>	'text/vnd.abc',
				'acgi'	=>	'text/html',
				'afl'	=>	'video/animaflex',
				'ai'	=>	'application/postscript',
				'aif'	=>	'audio/x-aiff',
				'aifc'	=>	'audio/x-aiff',
				'aiff'	=>	'audio/x-aiff',
				'aim'	=>	'application/x-aim',
				'aip'	=>	'text/x-audiosoft-intra',
				'ani'	=>	'application/x-navi-animation',
				'aos'	=>	'application/x-nokia-9000-communicator-add-on-software',
				'aps'	=>	'application/mime',
				'arc'	=>	'application/octet-stream',
				'arj'	=>	'application/arj',
				'art'	=>	'application/x-jg',
				'asf'	=>	'application/x-ms-asf',
				'asm'	=>	'application/x-asm',
				'asp'	=>	'text/x-asp',
				'aspx'	=>	'text/x-asp',
				'asx'	=>	'application/x-mplayer2',
				'au'	=>	'audio/basic',
				'avi'	=>	'video/x-msvideo',
				'bcpio'	=>	'application/x-bcpio',
				'bin'	=>	'application/octet-stream',
				'bm'	=>	'image/bmp',
				'bmp'	=>	'image/bmp',
				'boo'	=>	'application/book',
				'book'	=>	'application/book',
				'boz'	=>	'application/x-bzip2',
				'bsh'	=>	'application/x-bsh',
				'bz'	=>	'application/x-bzip',
				'bz2'	=>	'application/x-bzip2',
				'c'		=>	'text/plain',
				'c++'	=>	'text/plain',
				'cc'	=>	'text/plain',
				'ccad'	=>	'application/clariscad',
				'cco'	=>	'application/x-cocoa',
				'cdf'	=>	'application/x-cdf',
				'cer'	=>	'application/pkix-cert',
				'cha'	=>	'application/x-chat',
				'chat'	=>	'application/x-chat',
				'class'	=>	'application/java',
				'com'	=>	'application/octet-stream',
				'conf'	=>	'text/plain',
				'cpio'	=>	'application/x-cpio',
				'cpp'	=>	'text/x-c',
				'cpt'	=>	'application/x-compactpro',
				'crl'	=>	'application/pkix-crl',
				'crt'	=>	'application/x-x509-ca-cert',
				'csh'	=>	'application/x-csh',
				'css'	=>	'text/css',
				'csv'	=>	'text/comma-separated-values',
				'cxx'	=>	'text/plain',
				'dcr'	=>	'application/x-director',
				'dcx'	=>	'image/x-dcx',
				'def'	=>	'text/plain',
				'der'	=>	'application/x-x509-ca-cert',
				'dif'	=>	'video/x-dv',
				'dir'	=>	'application/x-director',
				'dl'	=>	'video/dl',
				'doc'	=>	'application/msword',
				'dot'	=>	'application/msword',
				'dp'	=>	'application/commongroud',
				'drw'	=>	'application/drafting',
				'dtd'	=>	'application/xml-dtd',
				'dump'	=>	'application/octet-stream',
				'dv'	=>	'video/x-dv',
				'dvi'	=>	'application/x-dvi',
				'dwf'	=>	'model/vnd.dwf',
				'dwg'	=>	'application/acad',
				'dxf'	=>	'image/x-dwg',
				'dxr'	=>	'application/x-director',
				'el'	=>	'text/x-script.elisp',
				'elc'	=>	'application/x-elc',
				'env'	=>	'application/x-envoy',
				'eps'	=>	'application/postscript',
				'es'	=>	'application/x-esrehber',
				'etx'	=>	'text/x-setext',
				'evy'	=>	'application/x-envoy',
				'exe'	=>	'application/octet-stream',
				'f'		=>	'text/plain',
				'f77'	=>	'text/x-fortran',
				'f90'	=>	'text/plain',
				'fdf'	=>	'application/vnd.fdf',
				'fif'	=>	'image/fif',
				'fli'	=>	'video/fli',
				'flo'	=>	'image/florian',
				'for'	=>	'text/plain',
				'fpx'	=>	'image/vnd.fpx',
				'frl'	=>	'application/freeloader',
				'funk'	=>	'audio/make',
				'g'		=>	'text/plain',
				'g3'	=>	'image/g3fax',
				'gif'	=>	'image/gif',
				'gl'	=>	'video/gl',
				'gsd'	=>	'audio/x-gsm',
				'gsm'	=>	'audio/x-gsm',
				'gsp'	=>	'application/x-gsp',
				'gss'	=>	'application/x-gss',
				'gtar'	=>	'application/x-gtar',
				'gz'	=>	'application/x-gzip',
				'gzip'	=>	'application/x-gzip',
				'h'		=>	'text/plain',
				'hdf'	=>	'application/x-hdf',
				'help'	=>	'application/x-helpfile',
				'hh'	=>	'text/plain',
				'hlb'	=>	'text/x-script',
				'hlp'	=>	'application/x-helpfile',
				'hqx'	=>	'application/mac-binhex40',
				'hta'	=>	'application/hta',
				'htc'	=>	'text/x-component',
				'htm'	=>	'text/html',
				'html'	=>	'text/html',
				'htmls'	=>	'text/html',
				'htt'	=>	'text/webviewhtml',
				'htx'	=>	'text/html',
				'ico'	=>	'image/x-icon',
				'idc'	=>	'text/plain',
				'ief'	=>	'image/ief',
				'iefs'	=>	'image/ief',
				'iges'	=>	'model/iges',
				'igs'	=>	'model/iges',
				'ima'	=>	'application/ima',
				'imap'	=>	'application/x-httpd-imap',
				'img'	=>	'image/x-img',
				'inf'	=>	'application/inf',
				'ip'	=>	'application/x-ip2',
				'it'	=>	'audio/it',
				'iv'	=>	'application/x-inventor',
				'jam'	=>	'audio/x-jam',
				'jav'	=>	'text/plain',
				'java'	=>	'text/plain',
				'jfif'	=>	'image/jpeg',
				'jpe'	=>	'image/jpeg',
				'jpeg'	=>	'image/jpeg',
				'jpg'	=>	'image/jpeg',
				'jps'	=>	'image/x-jps',
				'js'	=>	'application/x-javascript',
				'kar'	=>	'audio/midi',
				'ksh'	=>	'application/x-ksh',
				'la'	=>	'audio/nspaudio',
				'lam'	=>	'application/x-liveaudio',
				'latex'	=>	'application/x-latex',
				'lha'	=>	'application/lha',
				'lhx'	=>	'application/octet-stream',
				'list'	=>	'image/x-list',
				'log'	=>	'text/plain',
				'lsp'	=>	'application/x-lisp',
				'lst'	=>	'image/x-lst',
				'ltx'	=>	'application/x-latex',
				'lzh'	=>	'application/x-lzh',
				'lzx'	=>	'application/x-lzx',
				'm'		=>	'text/plain',
				'm1v'	=>	'video/mpeg',
				'm2a'	=>	'audio/mpeg',
				'm2v'	=>	'video/mpeg',
				'm3u'	=>	'audio/x-mpegurl',
				'mar'	=>	'text/plain',
				'mcd'	=>	'application/mcad',
				'mcf'	=>	'text/mcf',
				'mcp'	=>	'application/netmc',
				'mht'	=>	'message/rfc822',
				'mhtml'	=>	'message/rfc822',
				'mid'	=>	'audio/midi',
				'midi'	=>	'audio/midi',
				'mime'	=>	'message/rfc822',
				'mjpg'	=>	'video/x-motion-jpeg',
				'mm'	=>	'application/base64',
				'mme'	=>	'application/base64',
				'mod'	=>	'audio/mod',
				'moov'	=>	'video/quicktime',
				'mov'	=>	'video/quicktime',
				'movie'	=>	'video/x-sgi-movie',
				'mp2'	=>	'audio/mpeg',
				'mp3'	=>	'audio/mpeg',
				'mpa'	=>	'audio/mpeg',
				'mpc'	=>	'application/x-project',
				'mpe'	=>	'video/mpeg',
				'mpeg'	=>	'video/mpeg',
				'mpg'	=>	'video/mpeg',
				'mpga'	=>	'audio/mpeg',
				'mpp'	=>	'application/vnd.ms-project',
				'mpt'	=>	'application/x-project',
				'mpv'	=>	'application/x-project',
				'mpx'	=>	'application/x-project',
				'mv'	=>	'video/x-sgi-movie',
				'my'	=>	'audio/make',
				'nc'	=>	'application/x-netcdf',
				'ncm'	=>	'application/vnd.nokia.configuration-message',
				'nif'	=>	'image/x-niff',
				'niff'	=> 	'image/x-niff',
				'nvd'	=>	'application/x-navidoc',
				'o'		=>	'application/octet-stream',
				'omc'	=>	'application/x-omc',
				'p'		=>	'text/x-pascal',
				'pas'	=>	'text/x-pascal',
				'pbm'	=>	'application/x-portable-bitmap',
				'pcl'	=>	'application/x-pcl',
				'pcx'	=>	'image/x-pcx',
				'pdb'	=>	'chemical/x-pdb',
				'pdf'	=>	'application/pdf',
				'pgm'	=>	'application/x-portable-graymap',
				'php'	=>	'text/x-php',
				'php3'	=>	'text/x-php',
				'php4'	=>	'text/x-php',
				'pic'	=>	'image/pict',
				'pict'	=>	'image/pict',
				'pl'	=>	'text/plain',
				'pm'	=>	'image/x-script.perl-module',
				'pm4'	=>	'application/x-pagemaker',
				'pm5'	=>	'application/x-pagemaker',
				'png'	=>	'image/png',
				'pnm'	=>	'application/x-portable-anymap',
				'pot'	=>	'application/vnd.ms-powerpoint',
				'pov'	=>	'model/x-pov',
				'ppa'	=>	'application/vnd.ms-powerpoint',
				'ppm'	=>	'application/x-portable-pixmap',
				'pps'	=>	'application/vnd.ms-powerpoint',
				'ppt'	=>	'application/vnd.ms-powerpoint',
				'ppz'	=>	'application/vnd.ms-powerpoint',
				'pre'	=>	'application/x-freelance',
				'ps'	=>	'application/x-postscript',
				'psd'	=>	'application/octet-stream',
				'pwz'	=>	'application/vnd.ms-powerpoint',
				'py'	=>	'text/x-script.python',
				'pyc'	=>	'application/x-bytecode.python',
				'pzm'	=>	'image/x-pzm',
				'qif'	=>	'image/x-quicktime',
				'qt'	=>	'video/quicktime',
				'qtc'	=>	'video/x-qtc',
				'qti'	=>	'video/x-quicktime',
				'qtif'	=>	'video/x-quicktime',
				'ra'	=>	'audio/x-pn-realaudio',
				'ram'	=>	'audio/x-pn-realaudio',
				'rf'	=>	'image/vnd.rn-realflash',
				'rgb'	=>	'image/x-rgb',
				'rm'	=>	'audio/x-pn-realaudio',
				'rmi'	=>	'audio/mid',
				'rmm'	=>	'audio/x-pn-realaudio',
				'rmp'	=>	'audio/x-pn-realaudio',
				'rng'	=>	'application/ringing-tones',
				'rnx'	=>	'application/vnd.rn-realplayer',
				'roff'	=>	'application/x-troff',
				'rt'	=>	'text/richtext',
				'rtf'	=>	'application/x-rtf',
				'rv'	=>	'video/vnd.rn-realvideo',
				's'		=>	'text/x-asm',
				'scm'	=>	'video/x-scm',
				'sdml'	=>	'text/plain',
				'sdp'	=>	'application/x-sdp',
				'sea'	=>	'application/x-sea',
				'sgm'	=>	'text/x-sgml',
				'sgml'	=>	'text/x-sgml',
				'sh'	=>	'application/x-sh',
				'shar'	=>	'application/x-bsh',
				'shtml'	=>	'text/html',
				'smi'	=>	'application/smil',
				'smil'	=>	'application/smil',
				'snd'	=>	'audio/basic',
				'spr'	=>	'application/x-sprite',
				'sprite'=>	'application/x-sprite',
				'ssi'	=>	'application/x-server-parsed-html',
				'ssm'	=>	'application/streamingmedia',
				'stl'	=>	'application/x-navistyle',
				'svf'	=>	'image/x-dwg',
				'svr'	=>	'application/x-world',
				't'		=>	'application/x-troff',
				'tar'	=>	'application/x-tar',
				'tcl'	=>	'text/x-script.tcl',
				'tex'	=>	'application/x-tex',
				'texi'	=>	'application/x-texinfo',
				'text'	=>	'text/plain',
				'tgz'	=>	'application/x-compressed',
				'tif'	=>	'image/tiff',
				'tiff'	=>	'image/tiff',
				'tpl'	=>	'text/plain',
				'tr'	=>	'application/x-troff',
				'tsp'	=>	'audio/tsplayer',
				'tsv'	=>	'text/tab-separated-values',
				'txt'	=>	'text/plain',
				'uri'	=>	'text/uri-list',
				'ustar'	=>	'application/x-ustar',
				'uu'	=>	'application/octet-stream',
				'vcd'	=>	'application/x-cdlink',
				'vda'	=>	'application/vda',
				'vdo'	=>	'video/vdo',
				'voc'	=>	'audio/x-voc',
				'vox'	=>	'auxio/voxware',
				'vrml'	=>	'application/x-vrml',
				'vsd'	=>	'application/x-visio',
				'vst'	=>	'application/x-visio',
				'vsw'	=>	'application/x-visio',
				'wav'	=>	'audio/x-wav',
				'wbmp'	=>	'image/vnd.wap.wbmp',
				'web'	=>	'application/vnd.xara',
				'wiz'	=>	'application/msword',
				'wmf'	=>	'windows/metafile',
				'wml'	=>	'text/vnd.wap.wml',
				'wmlc'	=>	'application/vnd.wap.wmlc',
				'wmls'	=>	'text/vnd.wap.wmlscript',
				'wmlsc'	=>	'application/vnd.wap.wmlscript',
				'word'	=>	'application/msword',
				'wp'	=>	'application/wordperfect',
				'wp5'	=>	'application/wordperfect',
				'wp6'	=>	'application/wordperfect',
				'wpd'	=>	'application/wordperfect',
				'wq1'	=>	'application/x-lotus',
				'wri'	=>	'application/mswrite',
				'wrl'	=>	'model/vrml',
				'wrz'	=>	'model/vrml',
				'wsc'	=>	'text/scriptlet',
				'xbm'	=>	'image/x-xbitmap',
				'xl'	=>	'application/excel',
				'xla'	=>	'application/x-msexcel',
				'xlb'	=>	'application/vnd.ms-excel',
				'xlc'	=>	'application/vnd.ms-excel',
				'xld'	=>	'application/x-msexcel',
				'xlk'	=>	'application/x-msexcel',
				'xll'	=>	'application/vnd.ms-excel',
				'xlm'	=>	'application/vnd.ms-excel',
				'xls'	=>	'application/vnd.ms-excel',
				'xlt'	=>	'application/x-msexcel',
				'xlv'	=>	'application/x-msexcel',
				'xlw'	=>	'application/vnd.ms-excel',
				'xml'	=>	'text/xml',
				'xpm'	=>	'image/x-xpixmap',
				'xsl'	=>	'text/xsl',
				'xlst'	=>	'text/xsl',
				'xyz'	=>	'chemical/x-pdb',
				'x-png'	=>	'image/png',
				'z'		=>	'application/x-compressed',
				'zip'	=>	'application/zip',
				'zsh'	=>	'text/x-script.zsh'
			);
		}
		return $mimeTable;
	}
}
?>