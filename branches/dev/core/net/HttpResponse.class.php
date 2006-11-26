<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/core/net/HttpResponse.class.php,v 1.23 2006/05/07 15:23:54 mpont Exp $
// $Date: 2006/05/07 15:23:54 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
import('php2go.net.Url');
import('php2go.net.MimeType');
import('php2go.net.httpConstants', 'php', FALSE);
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		HttpResponse
// @desc		Classe para manipulaчуo da resposta da requisiчуo HTTP
// @package		php2go.net
// @extends		PHP2Go
// @uses		Environment
// @uses		HttpRequest
// @uses		MimeType
// @author		Marcos Pont
// @version		$Revision: 1.23 $
// @static
//!-----------------------------------------------------------------
class HttpResponse extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	HttpResponse::headersSent
	// @desc		Verifica se os headers da resposta р requisiчуo HTTP jс foram enviados
	// @access		public
	// @return		bool
	// @static
	//!-----------------------------------------------------------------
	function headersSent() {
		return headers_sent();
	}

	//!-----------------------------------------------------------------
	// @function	HttpResponse::addHeader
	// @desc		Adiciona um header ao conteњdo retornado
	// @param		name string		Nome do header
	// @param		value string	"" Valor do header
	// @param		replace bool	"TRUE" Substituir o valor atual do header, se existente
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function addHeader($name, $value='', $replace=TRUE) {
		if (empty($value) && strlen($value) == 0)
			@header("$name", TypeUtils::toBoolean($replace));
		else
			@header("$name: $value", TypeUtils::toBoolean($replace));
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpResponse::addHeaders
	// @desc		Adiciona um conjunto de headers no response da requisiчуo
	// @param		headers array	Conjunto de headers
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function addHeaders($headers) {
		foreach((array)$headers as $name => $value)
			HttpResponse::addHeader($name, $value);
	}

	//!-----------------------------------------------------------------
	// @function	HttpResponse::addCookie
	// @desc		Adiciona um cookie р resposta HTTP do servidor
	// @access		public
	// @param		Cookie HttpCookie object	Cookie a ser adicionado
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function addCookie($Cookie) {
		if (TypeUtils::isInstanceOf($Cookie, 'HttpCookie')) {
			setcookie($Cookie->getName(), $Cookie->getValue(),
					$Cookie->getExpiryDate(), $Cookie->getPath(),
					$Cookie->getDomain(), ($Cookie->isSecure() ? 1 : 0));
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpResponse::redirect
	// @desc		Redireciona para outra URL utilizando header HTTP Location
	// @param		location Url object		URL de redirecionamento
	// @access		public	
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function redirect($Location) {
		if (TypeUtils::isInstanceOf($Location, 'Url')) {
			HttpResponse::setStatus(HTTP_STATUS_MOVED_PERMANENTLY);
			HttpResponse::addHeader('Location', $Location->getUrl());
			HttpResponse::addHeader('Connection', 'close');
		}
	}

	//!-----------------------------------------------------------------
	// @function	HttpResponse::setStatus
	// @desc		Define o cѓdigo de status para a resposta HTTP do servidor.
	//				Este mщtodo pode ser utilizado quando щ necessсrio enviar
	//				um determinado cѓdigo de status para o agente que requisitou
	//				um determinado recurso do sistema via HTTP
	// @param		code int			Cѓdigo de status
	// @param		httpVersion string	"1.0" Versуo do protocolo HTTP
	// @access		public	
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function setStatus($code, $httpVersion='1.0') {
		HttpResponse::addHeader("HTTP/$httpVersion $code");
	}

	//!-----------------------------------------------------------------
	// @function	HttpResponse::download
	// @desc		Envia os headers especэficos para download de um arquivo
	// @access		public
	// @param		fileName string		Nome do arquivo
	// @param		length int			"0" Tamanho do conteњdo
	// @param		mimeType string		"" Permite definir um mime-type diferente do padrуo que щ definido a partir do nome do arquivo
	// @param		contentDisp string	"" Permite definir um valor diferente do padrуo para o header Content-disposition
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function download($fileName, $length=0, $mimeType='', $contentDisp='') {
		if (empty($mimeType))
			$mimeType = MimeType::getFromFileName($fileName);
		if (empty($contentDisp))
			$contentDisp = 'attachment';
		// headers bсsicos
		$headers = array();
		$headers['Content-Type'] = $mimeType;
		$headers['Content-Disposition'] = "{$contentDisp}; filename=\"{$fileName}\"";
		if ($length) 
			$headers['Content-Length'] = $length;
		$headers['Last-Modified'] = gmdate("D, d M Y H:i:s") . ' GMT';
		$headers['Expires'] = '0';
		// headers dependentes do browser
		$Agent =& UserAgent::getInstance();
		if ($Agent->matchBrowser('ie')) {
			$headers['Pragma'] = 'public';
			$headers['Cache-Control'] = (HttpRequest::isSecure() ? 'max-age=0' : 'must-revalidate, post-check=0, pre-check=0');
		} else {
			$headers['Pragma'] = 'no-cache';
		}
		HttpResponse::addHeaders($headers);
    }
}
?>