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
// $Header: /www/cvsroot/php2go/core/xml/feed/FeedCreator.class.php,v 1.5 2006/02/28 21:56:05 mpont Exp $
// $Date: 2006/02/28 21:56:05 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.net.HttpRequest');
import('php2go.net.HttpResponse');
import('php2go.xml.XmlRender');
import('php2go.xml.feed.Feed');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FeedCreator
// @desc		Dentro da package php2go.xml.feed, a classe FeedCreator oferece o
//				mecanismo de construção de um feed (informações sobre canal e itens).
//				Os dados do feed podem ser exportados no formato RSS ou no formato
//				ATOM (cada qual em diversas versões)
// @package		php2go.xml.feed
// @extends		PHP2Go
// @uses		Feed
// @uses		HttpResponse
// @uses		TypeUtils
// @uses		XmlRender
// @author		Marcos Pont
// @version		$Revision: 1.5 $
//!-----------------------------------------------------------------
class FeedCreator extends PHP2Go
{
	var $encoding;		// @var encoding string		Codificação a ser utilizada no conteúdo XML do feed
	var $css;			// @var css string			URL do stylesheet CSS
	var $xsl;			// @var xsl string			URL do stylsheet XSL
	var $Feed = NULL;	// @var Feed Feed object	"NULL" Armazena o feed que é criado na classe
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::FeedCreator
	// @desc		Construtor da classe
	// @access		public
	// @param		feedType string		Tipo de feed
	// @param		feedVersion string	"NULL" Versão do formato
	//!-----------------------------------------------------------------
	function FeedCreator($feedType, $feedVersion=NULL) {
		parent::PHP2Go();
		$this->encoding = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->Feed = new Feed($feedType, $feedVersion);
		$this->Feed->setSyndicationURL(HttpRequest::url());
		$this->Feed->setChannel(new FeedChannel());		
		$this->Feed->Channel->setElement('generator', 'PHP2Go Feed Creator ' . PHP2GO_VERSION);
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::setEncoding
	// @desc		Seta o tipo de codificação do feed
	// @access		public
	// @param		encoding string		Codificação
	// @return		void
	//!-----------------------------------------------------------------
	function setEncoding($encoding) {
		$this->encoding = $encoding;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::setCssStylesheet
	// @desc		Permite definir um arquivo CSS para o feed
	// @access		public
	// @param		url string	URL do arquivo de estilos
	// @return		void
	//!-----------------------------------------------------------------
	function setCssStylesheet($url) {
		$this->css = $url;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::setXslStylesheet
	// @desc		Permite definir um arquivo XSL para o feed
	// @access		public
	// @param		url string	URL do arquivo de transformações
	// @return		void
	//!-----------------------------------------------------------------
	function setXslStylesheet($url) {
		$this->xsl = $url;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::setChannelElement
	// @desc		Permite setar um atributo ou um conjunto de atributos do feed
	// @access		public
	// @param		name mixed	Nome do elemento ou array hash elemento=>valor
	// @param		value mixed	"" Valor do atributo
	// @return		void
	//!-----------------------------------------------------------------
	function setChannelElement($name, $value='') {
		if (TypeUtils::isHashArray($name)) {
			foreach ($name as $key => $value)
				$this->Feed->Channel->setElement($key, $value);
		} else {
			$this->Feed->Channel->setElement($name, $value);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::addChannelElement
	// @desc		Adiciona uma nova entrada de um determinado elemento,
	//				dentre aqueles que são múltiplos (ex: category, contributor)
	// @access		public
	// @param		name string		Nome do elemento
	// @param		value mixed		Conteúdo do elemento
	// @return		void
	//!-----------------------------------------------------------------
	function addChannelElement($name, $value) {
		$this->Feed->Channel->addElement($name, $value);
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::addItem
	// @desc		Adiciona um item ao feed
	// @access		public
	// @param		Item FeedItem object	Item a ser adicionado
	// @return		void
	//!-----------------------------------------------------------------
	function addItem($Item) {
		$this->Feed->Channel->addItem($Item);
	}

	//!-----------------------------------------------------------------
	// @function	FeedCreator::downloadFeed
	// @desc		Gera e envia o conteúdo do feed ao browser do cliente
	// @access		public
	// @param		fileName string		Nome do arquivo a ser enviado nos cabeçalhos de resposta
	// @return		void
	//!-----------------------------------------------------------------
	function downloadFeed($fileName) {
		HttpResponse::addHeader('Content-type', $this->Feed->contentType);
		// @todo : inserir header Etag?? como gerar??
		$Rend =& $this->_renderFeeed();
		$Rend->download($fileName, TRUE, $this->Feed->contentType);
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::saveFeed
	// @desc		Gera o conteúdo do feed e salva em um arquivo
	// @access		public
	// @param		fileName string		Caminho e nome do arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function saveFeed($fileName) {
		$Rend =& $this->_renderFeeed();		
		return $Rend->toFile($fileName);
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedCreator::&_renderFeed
	// @desc		Cria uma instância da classe de renderização de XML e
	//				adiciona na árvore os dados do canal e dos itens do feed
	// @access		private
	// @return		XmlRender object	Renderizador, contendo o documento XML já construído
	//!-----------------------------------------------------------------
	function &_renderFeeed() {
		// data de modificação
		$this->Feed->setLastModified(time());
		// cria o renderer
		$rootProperties = $this->Feed->renderRootProperties();
		$Rend = new XmlRender($rootProperties['name'], $rootProperties['attrs']);
		// define o target encoding
		$Rend->setCharset($this->encoding);
		// adiciona definições de estilo
		if (!empty($this->css))
			$Rend->Document->addStylesheet($this->css, FALSE, 'text/css');
		if (!empty($this->xsl))
			$Rend->Document->addStylesheet($this->xsl, FALSE, 'text/xsl');
		// adiciona o canal e os itens
		if ($this->Feed->isRSS())
			$Node =& $Rend->Document->DocumentElement->addChild(new XmlNode('channel', array()));
		else
			$Node =& $Rend->getRoot();
		$Rend->addContentAt($Node, $this->Feed->renderChannelElements(), array('createArrayNode' => FALSE, 'arrayEntryAsRepeat' => TRUE, 'attributeKey' => '_attrs', 'cdataKey' => '_cdata'));
		$Rend->addContentAt($Node, $this->Feed->renderItems(), array('createArrayNode' => FALSE, 'arrayEntryAsRepeat' => TRUE, 'attributeKey' => '_attrs', 'cdataKey' => '_cdata'));
		// gera o conteúdo XML
		$Rend->render();
		return $Rend;
	}	
}
?>