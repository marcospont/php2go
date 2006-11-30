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
// $Header: /www/cvsroot/php2go/core/xml/feed/Feed.class.php,v 1.6 2006/05/07 15:12:22 mpont Exp $
// $Date: 2006/05/07 15:12:22 $

//------------------------------------------------------------------
import('php2go.xml.feed.FeedChannel');
//------------------------------------------------------------------

// @const FEED_RSS "RSS"
// Constante para feeds do tipo RSS
define('FEED_RSS', 'RSS');
// @const FEED_ATOM "ATOM"
// Constante para feeds do tipo ATOM
define('FEED_ATOM', 'ATOM');

//!-----------------------------------------------------------------
// @class		Feed
// @desc		Esta classe funciona como base para um feed (conjunto de informaчѕes),
//				constituэdo por um canal (FeedChannel) e um ou mais itens (FeedItem).
//				Alщm disso, uma instтncia da classe Feed possui um cѓdigo de tipo
//				(FEED_RSS ou FEED_ATOM), a propriedade etag (hash do feed) e a data
//				da њltima modificaчуo das informaчѕes
// @package		php2go.xml.feed
// @extends		FeedNode
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class Feed extends FeedNode
{
	var $type;				// @var type int					Tipo de feed (FEED_RSS ou FEED_ATOM)
	var $version;			// @var version string				Versуo do formato (utilizado em feeds RSS)
	var $contentType;		// @var contentType string			Content-type do feed
	var $etag;				// @var etag string					Hash do conteњdo XML do feed
	var $lastModified;		// @var lastModified int			Timestamp da њltima modificaчуo do feed
	var $syndicationURL;	// @var syndicationURL string		URL de origem do feed
	var $Channel = NULL;	// @var Channel FeedChannel	object	Canal associado ao feed

	//!-----------------------------------------------------------------
	// @function	Feed::Feed
	// @desc		Construtor da classe
	// @param		type int		Tipo do feed
	// @param		version string	"NULL" Versуo
	// @access		public
	//!-----------------------------------------------------------------
	function Feed($type, $version=NULL) {
		parent::FeedNode();
		switch (strtoupper($type)) {
			case 'RDF' :
				$this->type = FEED_RSS;
				$this->version = '1.0';
				$this->contentType = 'application/xml';
				break;
			case 'RSS' :
				$this->type = FEED_RSS;
				$this->version = TypeUtils::ifNull($version, '2.0');
				$this->contentType = 'application/rss+xml';
				break;
			case 'ATOM' :
			case 'FEED' :
				$this->type = FEED_ATOM;
				$this->version = TypeUtils::ifNull($version, '0.3');
				$this->contentType = 'application/atom+xml';
				break;
			default :
				$this->type = FEED_RSS;
				$this->version = '2.0';
				$this->contentType = 'application/rss+xml';
				break;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Feed::isATOM
	// @desc		Verifica se o feed щ do tipo ATOM
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isATOM() {
		return ($this->type == FEED_ATOM);
	}

	//!-----------------------------------------------------------------
	// @function	Feed::isRSS
	// @desc		Verifica se o feed щ do tipo RSS
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isRSS() {
		return ($this->type == FEED_RSS);
	}

	//!-----------------------------------------------------------------
	// @function	Feed::getLastModified
	// @desc		Busca a data da њltima atualizaчуo do feed
	// @param		fmt string	"r" Formato de apresentaчуo da data/hora
	// @return		string Data formatada
	// @note		Se a data armazenada nуo for do tipo unix timestamp, o formato desejado nуo serс aplicado
	// @access		public
	//!-----------------------------------------------------------------
	function getLastModified($fmt='r') {
		return (TypeUtils::isInteger($this->lastModified) ? date($fmt, $this->lastModified) : $this->lastModified);
	}

	//!-----------------------------------------------------------------
	// @function	Feed::&getChannel
	// @desc		Busca o canal associado a este feed
	// @return		FeedChannel object
	// @access		public
	//!-----------------------------------------------------------------
	function &getChannel() {
		return $this->Channel;
	}

	//!-----------------------------------------------------------------
	// @function	Feed::getChannelElementNames
	// @desc		Retorna o conjunto de propriedades vсlidas para o canal,
	//				de acordo com o tipo e versуo do feed
	// @return		array Vetor de propriedades
	// @access		public
	//!-----------------------------------------------------------------
	function getChannelElementNames() {
		if ($this->isRSS()) {
			switch ($this->version) {
				// RSS 0.9 e 0.91
				case '0.9' :
				case '0.91' :
					return array(
						'title', 'description', 'link', 'image', 'textinput'
					);
				// RSS 0.92, 0.93 e 0.94
				case '0.92' :
				case '0.93' :
				case '0.94' :
					return array(
						'title', 'description', 'link', 'category', 'image', 'textinput',
						'cloud', 'language', 'copyright', 'docs', 'lastBuildDate',
						'managingEditor', 'pubDate', 'rating', 'skipDays', 'skipHours'
					);
				// RSS 1.0
				case '1.0' :
					return array(
						'title', 'description', 'link', 'image', 'textinput', 'language',
						'copyright', 'docs', 'lastBuildDate', 'managingEditor', 'pubDate',
						'rating', 'skipDays', 'skipHours'
					);
				// RSS 2.0
				default :
					return array(
						'title', 'description', 'link', 'category', 'image', 'textinput',
						'cloud', 'language', 'copyright', 'docs', 'lastBuildDate',
						'managingEditor', 'webMaster', 'pubDate', 'rating', 'skipDays',
						'skipHours', 'generator', 'ttl'
					);
			}
		} else {
			// ATOM 0.x
			return array(
				'title', 'tagline', 'link', 'author', 'contributor', 'id', 'generator',
				'copyright', 'info', 'created', 'issued', 'published', 'updated', 'modified'
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Feed::getItemElementNames
	// @desc		Retorna o conjunto de propriedades vсlidas para um item do canal,
	//				de acordo com o tipo e versуo do feed
	// @return		array Vetor de propriedades vсlidas para um item
	// @access		public
	//!-----------------------------------------------------------------
	function getItemElementNames() {
		if ($this->isRSS()) {
			switch ($this->version) {
				// RSS 0.9
				case '0.9' :
					return array(
						'title', 'link'
					);
				// RSS 0.91
				case '0.91 ':
					return array(
						'title', 'description', 'link'
					);
				// RSS 1.0
				case '1.0' :
					return array(
						'title', 'description', 'link', 'dc:date', 'dc:creator', 'dc:source', 'dc:format'
					);
				// RSS 0.92, 0.93 e 0.94
				case '0.92' :
				case '0.93' :
				case '0.94' :
					return array(
						'title', 'description', 'link', 'category', 'enclosure', 'source'
					);
				// RSS 2.0
				default :
					return array(
						'title', 'description', 'link', 'guid', 'author', 'pubDate', 'category', 'enclosure', 'source', 'comments'
					);
			}
		} else {
			// ATOM 0.x
			return array(
				'title', 'link', 'author', 'contributor', 'id', 'created',
				'issued', 'published', 'modified', 'updated', 'content', 'summary'
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Feed::setETag
	// @desc		Define o hash (ETag) do feed
	// @param		hash string	Valor do hash
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setETag($hash) {
		$this->etag = $hash;
	}

	//!-----------------------------------------------------------------
	// @function	Feed::setLastModified
	// @desc		Seta o timestamp da њltima modificaчуo do feed
	// @param		lastModified int	Timestamp
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setLastModified($lastModified) {
		$this->lastModified = parent::parseDate($lastModified);
	}

	//!-----------------------------------------------------------------
	// @function	Feed::setSyndicationURL
	// @desc		Define a URL de origem do feed
	// @param		url string		URL de origem
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setSyndicationURL($url) {
		$this->syndicationURL = $url;
	}

	//!-----------------------------------------------------------------
	// @function	Feed::setChannel
	// @desc		Define o canal associado ao feed
	// @param		Channel FeedChannel object
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setChannel($Channel) {
		if (TypeUtils::isInstanceOf($Channel, 'FeedChannel'))
			$this->Channel = $Channel;
	}

	//!-----------------------------------------------------------------
	// @function	Feed::renderRootProperties
	// @desc		Monta uma estrutura com nome e atributos do nodo raiz
	//				da сrvore XML do feed para fins de renderizaчуo
	// @return		array Vetor contendo nome e atributos do nodo raiz
	// @access		public
	//!-----------------------------------------------------------------
	function renderRootProperties() {
		if ($this->isRSS()) {
			if ($this->version == '1.0') {
				// RSS 1.0
				return array(
					'name' => 'rdf:RDF',
					'attrs' => array('xmlns' => 'http://purl.org/rss/1.0', 'xmlns:rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'xmlns:slash' => 'http://purl.org/rss/1.0/modules/slash/', 'xmlns:dc' => 'http://purl.org/dc/elements/1.1/')
				);
			} else {
				// RSS 0.9x e 2.0
				return array(
					'name' => 'rss',
					'attrs' => array('version' => $this->version)
				);
			}
		} else {
			// ATOM 0.x
			return array(
				'name' => 'feed',
				'attrs' => array('version' => $this->version, 'xmlns' => 'http://purl.org/atom/ns#')
			);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Feed::renderChannelElements
	// @desc		Este mщtodo monta um vetor com os nomes e valores das
	//				propriedades formatados para fins de geraчуo do arquivo
	//				XML do feed (renderizaчуo)
	// @return		array Vetor de propriedades com valores formatados para exibiчуo
	// @access		public
	//!-----------------------------------------------------------------
	function renderChannelElements() {
		if (TypeUtils::isInstanceOf($this->Channel, 'FeedChannel')) {
			$result = array();
			$elements = $this->getChannelElementNames();
			foreach ($elements as $element) {
				// busca o valor da propriedade
				$value = $this->Channel->getElement($element);
				if (!$value)
					continue;
				// elementos de data/timestamp
				if (in_array($element, array('lastBuildDate', 'pubDate', 'modified', 'updated'))) {
					$result[$element] = parent::buildDate($value, $this->type, $this->version);
				}
				// elementos onde os atributos sуo atributos de nodo, e nуo nodos filhos
				elseif ($element == 'cloud' || ($element == 'link' && $this->isATOM())) {
					$result[$element] = array('_attrs' => $this->_formatElementValue($value));
				}
				// outros elementos
				else {
					$result[$element] = $this->_formatElementValue($value);
				}
			}
			// atributos e elementos especiais
			if ($this->isRSS() && $this->version == '1.0') {
				$result['_attrs'] = array('rdf:about' => htmlspecialchars($this->syndicationURL));
				$result['dc:date'] = Date::formatTime(time(), DATE_FORMAT_ISO8601);
				if (isset($result['image']) && isset($result['image']['link']))
					$result['image']['_attrs'] = array('rdf:about' => htmlspecialchars($result['image']['link']));
				$items = array();
				foreach ($this->Channel->getChildren() as $item)
					$items[] = array('_attrs' => array('rdf:resource' => htmlspecialchars($item->getElement('link', ''))));
				$result['items'] = array(
					'rdf:Seq' => array(
						'rdf:li' => $items
					)
				);
			}
			return $result;
		}
		return array();
	}

	//!-----------------------------------------------------------------
	// @function	Feed::renderItems
	// @desc		Este mщtodo monta um vetor com todos os itens do canal,
	//				com suas propriedades e elementos formatados para fins de
	//				geraчуo do arquivo XML do feed (renderizaчуo)
	// @return		array Vetor com os dados dos itens do canal formatados
	// @access		public
	//!-----------------------------------------------------------------
	function renderItems() {
		if (TypeUtils::isInstanceOf($this->Channel, 'FeedChannel')) {
			$itemList = array();
			$itemElements = $this->getItemElementNames();
			$items = $this->Channel->getChildren();
			foreach ($items as $item) {
				$itemData = array();
				reset($itemElements);
				foreach ($itemElements as $element) {
					// busca o valor da propriedade
					$value = $item->getElement($element);
					if (!$value)
						continue;
					// elementos de data/timestamp
					if (in_array($element, array('pubDate', 'created', 'issued', 'published', 'modified', 'updated'))) {
						$itemData[$element] = parent::buildDate($value, $this->type, $this->version);
					}
					// enclosure: elemento com atributos e sem nodos filhos
					elseif ($element == 'enclosure') {
						$itemData[$element] = array('_attrs' => $this->_formatElementValue($value));
					}
					// link no formato ATOM: elemento com atributos, pode ser mњltiplo
					elseif ($element == 'link' && $this->isATOM()) {
						if (TypeUtils::isArray($value)) {
							// conjunto de links
							if (!TypeUtils::isHashArray($value) && !empty($value)) {
								foreach ($value as $key=>$link) {
									if (TypeUtils::isArray($link))
										$value[$key] = array('_attrs' => $link);
									else
										$value[$key] = array('_attrs' => array('href' => htmlspecialchars($link)));
								}
								$itemData[$element] = $value;
							}
							// link њnico com atributos
							else {
								$value = $this->_formatElementValue($value);
								$itemData[$element] = array('_attrs' => $value);
							}
						}
						// link simples formato string: converter em elemento com atributo href
						else {
							$value = array('href' => htmlspecialchars($value));
							$itemData[$element] = array('_attrs' => $value);
						}
					}
					// outros elementos
					else {
						$itemData[$element] = $value;
					}
				}
				// atributos e elementos especiais
				if ($this->isRSS() && $this->version == '1.0')
					$itemData['_attrs'] = array('rdf:about' => htmlspecialchars($item->getElement('link', '')));
				if ($this->isRSS() && !in_array($this->version, array('0.9', '0.91')))
					$itemData['source'] = array('_attrs' => array('url' => htmlspecialchars($this->syndicationURL)), '_cdata' => $this->Channel->getElement('title', ''));
				if ($this->isRSS() && $this->version == '2.0') {
					if (isset($itemData['guid']))
						$itemData['guid'] = array('_attrs' => array('isPermaLink' => 'true'), '_cdata' => $itemData['guid']);
				}
				$itemList[] = $itemData;
			}
			if ($this->isRSS())
				return array('item' => $itemList);
			else
				return array('entry' => $itemList);
		}
		return array();
	}

	//!-----------------------------------------------------------------
	// @function	Feed::_formatElementValue
	// @desc		Formata o valor de um elemento, sendo ele simples, composto,
	//				mњltiplo ou composto e mњltiplo
	// @param		value mixed		Valor do elemento
	// @return		mixed Valor(es) do elemento formatados para inclusуo no
	//				arquivo XML (usando a funчуo htmlspecialchars)
	// @access		private
	//!-----------------------------------------------------------------
	function _formatElementValue($value) {
		// elementos compostos ou mњltiplos (ex: image, textinput, author, contributor)
		if (TypeUtils::isArray($value)) {
			foreach ($value as $k=>$v) {
				// elementos mњltiplos e compostos (ex: contributor)
				if (TypeUtils::isArray($value[$k])) {
					foreach ($value[$k] as $_k => $_v)
						$value[$k][$_k] = htmlspecialchars($_v);
				} else {
					$value[$k] = htmlspecialchars($v);
				}
			}
		} else {
			$value = htmlspecialchars($value);
		}
		return $value;
	}
}
?>