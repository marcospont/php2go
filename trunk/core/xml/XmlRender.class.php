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
// $Header: /www/cvsroot/php2go/core/xml/XmlRender.class.php,v 1.17 2006/05/07 15:35:54 mpont Exp $
// $Date: 2006/05/07 15:35:54 $

//------------------------------------------------------------------
import('php2go.xml.XmlDocument');
import('php2go.xml.XmlNode');
import('php2go.file.FileManager');
import('php2go.net.HttpResponse');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		XmlRender
// @desc		Constrói um arquivo XML a partir da criação de seus nodos
// @package		php2go.xml
// @extends		PHP2Go
// @uses		XmlNode
// @uses		FileManager
// @uses		HttpResponse
// @author		Marcos Pont
// @version		$Revision: 1.17 $
// @note		Exemplo de uso:
//				<pre>
//
//				$Rend = new XmlRender('ROOT', array('attrib'=>'value'));
//				$Rend->setCharset('iso-8859-1');
//				$Rend->Document->setDoctype('SYSTEM', 'file.dtd');
//				$Root = $Rend->getRoot();
//				$Root->addChild(new XmlNode('CHILD', array('attrib'=>'value')));
//				$Rend->render('iso-8859-1');
//				$Rend->toFile('file.xml');
//
//				</pre>
//!-----------------------------------------------------------------
class XmlRender extends PHP2Go
{
	var $charset;				// @var charset string				Charset a ser utilizado na construção do arquivo XML	
	var $xmlContent = '';		// @var xmlContent string			"" Conteúdo XML gerado pela classe
	var $addOptions = array();	// @var addOptions array			"array()" Opções de inclusão de nodos a partir de objetos, arrays, data sets e result sets
	var $Document = NULL;		// @var Document XmlDocument object	"NULL" XmlDocument gerado pela classe

	//!-----------------------------------------------------------------
	// @function	XmlRender::XmlRender
	// @desc		Construtor da classe XmlRender. Cria o nodo raiz a
	//				partir dos parâmetros fornecidos
	// @access		public
	// @param		rootTag string		Tag do nodo raiz
	// @param		rootAttrs array		"array()" Atributos do nodo raiz	
	//!-----------------------------------------------------------------
	function XmlRender($rootTag, $rootAttrs = array()) {
		parent::PHP2Go();
		$this->charset = PHP2GO_DEFAULT_CHARSET;
		$this->Document = new XmlDocument();
		$this->Document->DocumentElement = new XmlNode($rootTag, $rootAttrs);
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::&getRoot
	// @desc		Busca o nodo raiz da árvore XML
	// @access		public
	// @return		XmlNode object
	//!-----------------------------------------------------------------
	function &getRoot() {
		return $this->Document->getRoot();
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::setCharset
	// @desc		Define o charset a ser utilizado na geração do arquivo XML
	// @access		public
	// @param		charset string		Charset a ser utilizado
	// @return		void
	//!-----------------------------------------------------------------
	function setCharset($charset) {
		$this->charset = $charset;
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::setAddOptions
	// @desc		Define as configurações a serem utilizadas pelo método addContent
	//				na inclusão de conteúdo no documento XML
	// @access		public
	// @param		option array		Vetor de opções
	// @param		overwrite bool		"FALSE" Sobrescrever as configurações existentes
	// @return		void
	// @note		Valores aceitos pelo array options :<br>
	//				defaultNodeName => Nome padrão para os nodos XML,<br>
	//				typeHints => Incluir o tipo do valor do nodo,<br>
	//				classAsNodeName => Definir o nome da classe como nome do nodo, em caso de inclusão de objetos,<br>
	//				createArrayNode => Define se um nodo representando um array deve ser criado, ou se devem ser criados apenas os nodos das entradas deste array,<br>
	//				arrayEntryAsRepeat => Cria N nodos de mesmo nome caso uma entrada de hash array possua um array numérico como valor,<br>
	//				attributeKey => Nome da entrada de um array que define os atributos do nodo,<br>
	//				cdataKey => Nome da entrada de um array que define a seção CDATA de um nodo
	//!-----------------------------------------------------------------
	function setAddOptions($options, $overwrite=FALSE) {
		if (TypeUtils::isHashArray($options)) {
			$this->addOptions = (!empty($this->addOptions) && !$overwrite ? array_merge($this->addOptions, $options) : $options);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::addContent
	// @desc		Método genérico de inclusão de conteúdo abaixo do nodo raiz da árvore XML
	// @access		public			
	// @param		value mixed			Conteúdo a ser incluído
	// @param		options array		"array()" Opções de inclusão
	// @note		Este método é ideal para a inclusão de objetos, arrays e valores escalares no documento
	// @see			XmlRender::addContentAt
	// @return		void	
	//!-----------------------------------------------------------------
	function addContent($value, $options=array()) {
		$this->addContentAt($this->Document->DocumentElement, $value, $options);
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::addContentAt
	// @desc		Método genérico de inclusão de conteúdo em um determinado nodo da árvore XML
	// @access		public
	// @param		value mixed		Conteúdo a ser incluído
	// @param		options array	"array()" Opções de inclusão
	// @note		Este método é ideal para a inclusão de objetos, arrays e valores escalares no documento
	// @see			XmlRender::addContent
	// @return		void
	//!-----------------------------------------------------------------
	function addContentAt(&$Node, $value, $options=array()) {
		$this->setAddOptions($options, FALSE);
		if (TypeUtils::isObject($value))			
			$this->_addObject($Node, $value, $options);
		elseif (TypeUtils::isArray($value))
			$this->_addArray($Node, $value, $options);
		else
			$this->_addValue($Node, $value);
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::addDataSet
	// @desc		Método de inclusão de um data set na árvore XML
	// @access		public
	// @param		DataSet DataSet object	Data set (de qualquer tipo) a ser incluído
	// @param		options array			"array()" Opções de inclusão
	// @return		void
	//!-----------------------------------------------------------------
	function addDataSet($DataSet, $options=array()) {
		if (TypeUtils::isInstanceOf($DataSet, 'DataSet') && $DataSet->getRecordCount() > 0) {
			$options['createArrayNode'] = TRUE;
			while (!$DataSet->eof()) {
				$this->_addArray($this->Document->DocumentElement, $DataSet->current(), $options);
				$DataSet->moveNext();
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::render
	// @desc		Constrói o conteúdo do arquivo XML
	// @access		public
	// @param		lineEnd string		"" Quebra de linha a ser utilizada
	// @param		indent string		"" String a ser usada para indentação
	// @return		void	
	//!-----------------------------------------------------------------
	function render($lineEnd ='', $indent='') {		
		$this->Document->addXmlDeclaration('1.0', $this->charset);
		$this->xmlContent = $this->Document->render($lineEnd, $indent);
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::getContent
	// @desc		Retorna o conteúdo gerado para o arquivo XML
	// @access		public
	// @return		string Conteúdo gerado para o arquivo XML
	//!-----------------------------------------------------------------
	function getContent() {
		return $this->xmlContent;
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::download
	// @desc		Imprime o conteúdo gerado para o arquivo XML
	// @access		public
	// @param		fileName string		Nome de arquivo, para geração de header HTTP do tipo attachment
	// @param		showHeaders bool	"TRUE" Indica se os headers HTTP devem ser enviados junto com o conteúdo
	// @param		mimeType string		"text/xml" Tipo mime associado ao conteúdo
	// @return		void	
	// @note		A classe tem como padrão a inclusão de headers HTTP para download do arquivo.
	//				Para imprimir na tela o conteúdo do arquivo, execute o método com o parâmetro
	//				showHeaders=FALSE
	//!-----------------------------------------------------------------
	function download($fileName, $showHeaders=TRUE, $mimeType='text/xml') {
		if ($showHeaders && !HttpResponse::headersSent()) {
			HttpResponse::download($fileName, strlen($this->getContent()), $mimeType, 'inline');
			print $this->getContent();
		} else {
			print htmlspecialchars($this->getContent());
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::toFile
	// @desc		Salva o conteúdo XML em um arquivo
	// @access		public
	// @param		fileName string		Nome do arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function toFile($fileName) {
		if ($this->getContent() != '') {			
			$Mgr =& new FileManager();
			$Mgr->throwErrors = FALSE;
			if ($Mgr->open($fileName, FILE_MANAGER_WRITE_BINARY)) {
				$Mgr->write($this->getContent());
				$Mgr->close();
				return TRUE;
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_WRITE_FILE', $fileName), E_USER_WARNING, __FILE__, __LINE__);
				return FALSE;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::_addObject
	// @desc		Método interno de inclusão de um objeto no documento XML
	// @access		private
	// @param		&Node XmlNode object	Nodo onde o objeto será incluído
	// @param		Object object			Objeto a ser incluído
	// @param		options array			"array()" Opções de inclusão
	// @return		void
	//!-----------------------------------------------------------------
	function _addObject(&$Node, $Object, $options=array()) {
		$options = (TypeUtils::isHashArray($options) ? array_merge($this->addOptions, $options) : $this->addOptions);
		$typeHints = TypeUtils::toBoolean($options['typeHints']);
		$defaultNodeName = (isset($options['defaultNodeName']) ? $options['defaultNodeName'] : XML_NODE_DEFAULT_NAME);
		$nodeName = (TypeUtils::toBoolean($options['classAsNodeName']) ? get_class($Object) : $defaultNodeName);
		$attributes = ($typeHints ? array('type' => 'object', 'class' => get_class($Object)) : array());
		$Child =& $Node->addChild(new XmlNode($nodeName, $attributes));
		$this->_addArray($Child, get_object_vars($Object), array('createArrayNode'=>FALSE));
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::_addArray
	// @desc		Método interno de inclusão de um array no documento XML
	// @access		private
	// @param		&Node XmlNode object	Nodo onde o array será incluído
	// @param		array array				Array a ser incluído
	// @param		options array			"array()" Opções de inclusão
	// @return		void
	//!-----------------------------------------------------------------
	function _addArray(&$Node, $array, $options=array()) {
		$options = (TypeUtils::isHashArray($options) ? array_merge($this->addOptions, $options) : $this->addOptions);
		$typeHints = TypeUtils::toBoolean($options['typeHints']);
		$defaultNodeName = (isset($options['defaultNodeName']) ? $options['defaultNodeName'] : XML_NODE_DEFAULT_NAME);
		if (!isset($options['createArrayNode']) || $options['createArrayNode'] === TRUE) {
			if ($typeHints) {
				$attributes = array(
					'type' => 'array',
					'hash' => (TypeUtils::isHashArray($array) ? 1 : 0)
				);
			} else {
				$attributes = array();
			}
			$Child =& $Node->addChild(new XmlNode($defaultNodeName, $attributes));
		} else {
			$Child =& $Node;
		}		
		if (TypeUtils::isHashArray($array)) {
			$arrayEntryAsRepeat = TypeUtils::toBoolean($options['arrayEntryAsRepeat']);
			foreach ($array as $key => $value) {
				if (TypeUtils::isObject($value)) {
					$opt = array('defaultNodeName' => $key, 'classAsNodeName' => FALSE);
					$this->_addObject($Child, $value, $opt);
				} elseif (isset($options['attributeKey']) && $key === $options['attributeKey']) {
					$Child->addAttributes($value);
				} elseif (isset($options['cdataKey']) && $key === $options['cdataKey']) {
					$Child->setData(TypeUtils::parseString($value));
				} elseif ($arrayEntryAsRepeat && TypeUtils::isArray($value) && !TypeUtils::isHashArray($value)) {
					for ($i=0,$s=sizeof($value); $i<$s; $i++) {
						$InnerChild =& $Child->addChild(new XmlNode($key, array()));
						if (TypeUtils::isArray($value[$i])) {
							$opt = array('createArrayNode' => FALSE);
							$this->_addArray($InnerChild, $value[$i], $opt);
						} else {
							$InnerChild->setData($value[$i]);
						}
					}
				} else {
					$attributes = ($typeHints ? array('type' => TypeUtils::getType($value)) : array());
					$InnerChild =& $Child->addChild(new XmlNode($key, $attributes));
					if (TypeUtils::isArray($value)) {
						$this->_addArray($InnerChild, $value, array(), FALSE);
					} else {
						$InnerChild->setData($value);
					}
				}
			}
		} else {
			for ($i=0, $size=sizeof($array); $i<$size; $i++) {
				if (TypeUtils::isObject($array[$i])) {
					$opt = array('classAsNodeName' => TRUE);
					$this->_addObject($Child, $array[$i], $opt);
				} else {
					$attributes = ($typeHints ? array('type' => TypeUtils::getType($array[$i])) : array());
					$InnerChild =& $Child->addChild(new XmlNode($defaultNodeName, $attributes));
					if (TypeUtils::isArray($array[$i])) {
						$this->_addArray($InnerChild, $array[$i]);
					} else {
						$InnerChild->setData($array[$i]);
					}
				}
			}
		}	
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlRender::_addValue
	// @desc		Método interno de inclusão de um valor escalar no documento XML
	// @access		private
	// @param		&Node XmlNode object	Nodo onde o valor será incluído
	// @param		value mixed				Valor escalar a ser incluído
	// @param		options array			"array()" Opções de inclusão
	// @return		void
	//!-----------------------------------------------------------------
	function _addValue(&$Node, $value, $options=array()) {
		$options = (TypeUtils::isHashArray($options) ? array_merge($this->addOptions, $options) : $this->addOptions);
		$typeHints = TypeUtils::toBoolean($options['typeHints']);
		$nodeName = (isset($this->addOptions['defaultNodeName']) ? $this->addOptions['defaultNodeName'] : XML_NODE_DEFAULT_NAME);
		$attributes = ($typeHints ? array('type' => TypeUtils::getType($value)) : array());
		$Node->addChild(new XmlNode($nodeName, $attributes, NULL, $value));
	}
}
?>