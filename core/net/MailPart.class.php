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
// $Header: /www/cvsroot/php2go/core/net/MailPart.class.php,v 1.10 2006/03/15 04:43:24 mpont Exp $
// $Date: 2006/03/15 04:43:24 $

//------------------------------------------------------------------
import('php2go.file.FileManager');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		MailPart
// @desc		Implementa uma parte de uma mensagem MIME (conte�do de
//				texto, conte�do HTML, arquivo anexo, imagem embebida),
//				gerando seu cabe�alho e seu conte�do para que seja
//				poss�vel inclui-la no corpo da mensagem a ser enviada
// @package		php2go.net
// @uses		FileManager
// @uses		StringUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.10 $
//!-----------------------------------------------------------------
class MailPart extends PHP2Go
{
    var $boundaryId;				// @var id string					C�digo que identifica a parte na mensagem (usado nos boundaries)
	var $contentId;					// @var contentId string			C�digo que identifica uma parte embebida no corpo da mensagem (imagem, som, ...)
    var $charset;					// @var charset string				Charset do conte�do
	var $contentType;				// @var contentType string			Tipo MIME do conte�do
    var $contentEncoding;			// @var contentEncoding string		Tipo de codifica��o do conte�do
    var $contentDisposition;		// @var contentDisposition string	Disposi��o do conte�do no corpo da mensagem (attachment, inline)
	var $fileName;					// @var fileName string				Nome do arquivo envolvido
	var $content;					// @var content string				Conte�do ASCII ou bin�rio da parte
    var $lineEnd;					// @var lineEnd string				Final de linha a ser utilizado

    //!-----------------------------------------------------------------
	// @function	MailPart::MailPart
	// @desc		Construtor da classe, a partir de um c�digo �nico de
	//				parte na mensagem
	// @access		public
	//!-----------------------------------------------------------------
	function MailPart() {
		parent::PHP2Go();
        $this->boundaryId;
		$this->contentId = md5(uniqid(time()));
		$this->charset = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->contentType = 'text/plain';
		$this->contentEncoding = '8bit';
		$this->lineEnd = "\n";
    }

	//!-----------------------------------------------------------------
	// @function	MailPart::getBoundaryId
	// @desc		Retorna o c�digo da parte na mensagem
	// @access		public
	// @return		string C�digo da parte
	//!-----------------------------------------------------------------
	function getBoundaryId() {
		return $this->boundaryId;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::getContentId
	// @desc		Retorna o c�digo da parte em rela��o ao corpo da mensagem
	// @access		public
	// @return		string C�digo da parte na mensagem
	//!-----------------------------------------------------------------
	function getContentId() {
		return $this->contentId;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::getCharset
	// @desc		Busca o conjunto de caracteres utilizado no conte�do da parte
	// @access		public
	// @return		string Charset da parte
	//!-----------------------------------------------------------------
	function getCharset() {
		return $this->charset;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::getContentType
	// @desc		Retorna o tipo MIME deste elemento
	// @access		public
	// @return		string Tipo MIME da parte
	//!-----------------------------------------------------------------
	function getContentType() {
		return $this->contentType;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::getEncoding
	// @desc		Busca o tipo de codifica��o utilizado
	// @access		public
	// @return		string Tipo de codifica��o
	//!-----------------------------------------------------------------
	function getEncoding() {
		return $this->contentEncoding;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::getDisposition
	// @desc		Busca a disposi��o do conte�do
	// @access		public
	// @return		string Tipo de disposi��o do conte�do da parte
	//!-----------------------------------------------------------------
	function getDisposition() {
		return isset($this->contentDisposition) ? $this->contentDisposition : NULL;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::getFileName
	// @desc		Busca o nome do arquivo envolvido
	// @access		public
	// @return		string Nome do arquivo
	//!-----------------------------------------------------------------
	function getFileName() {
		return isset($this->fileName) ? $this->fileName : NULL;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::getContent
	// @desc		Retorna o conte�do da parte
	// @access		public
	// @return		string Conte�do ASCII ou bin�rio associado a esta parte
	//!-----------------------------------------------------------------
	function getContent() {
		return isset($this->content) ? $this->content : NULL;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::getLineEnd
	// @desc		Busca o(s) caractere(s) de final de linha utilizado(s) na gera��o dos cabe�alhos
	// @access		public
	// @return		string Caractere(s) de final de linha utilizados
	//!-----------------------------------------------------------------
	function getLineEnd() {
		return $this->lineEnd;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setBoundaryId
	// @desc		Seta o c�digo de boundary relacionado a esta parte
	// @access		public
	// @param		bid string	C�digo boundary
	// @return		void
	//!-----------------------------------------------------------------
	function setBoundaryId($bid) {
		$this->boundaryId = $bid;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setContentId
	// @desc		Atribui um id de conte�do � parte
	// @access		public
	// @param		cid string	Id de conte�do
	// @return		void
	//!-----------------------------------------------------------------
	function setContentId($cid) {
		$this->contentId = $cid;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setCharset
	// @desc		Atribui um valor ao charset deste elemento
	// @access		public
	// @param		charset string	Valor para o charset
	// @return		void
	//!-----------------------------------------------------------------
	function setCharset($charset) {
		$this->charset = $charset;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setContentType
	// @desc		Define o tipo MIME deste elemento
	// @access		public
	// @param		contentType string	Tipo MIME a ser utilizado
	// @return		void
	//!-----------------------------------------------------------------
	function setContentType($contentType) {
		$this->contentType = $contentType;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setEncoding
	// @desc		Atribui um valor para o tipo de codifica��o do conte�do da parte
	// @access		public
	// @param		encoding string	Novo m�todo de codifica��o
	// @return		void
	//!-----------------------------------------------------------------
	function setEncoding($encoding) {
		$this->contentEncoding = $encoding;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setDisposition
	// @desc		Seta a disposi��o do conte�do
	// @access		public
	// @param		disposition string	Valor para o tipo de disposi��o
	// @return		void
	//!-----------------------------------------------------------------
	function setDisposition($disposition) {
		$this->contentDisposition = $disposition;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setFileName
	// @desc		Seta o nome do arquivo associado � parte
	// @access		public
	// @param		fileName string	Caminho completo e nome do arquivo
	// @return		void
	// @note		Ap�s atribuir a vari�vel $fileName � propriedade fileName
	//				da classe, o m�todo tentar� abrir o arquivo para buscar
	//				seu conte�do no servidor
	//!-----------------------------------------------------------------
	function setFileName($fileName) {
		$this->fileName = basename($fileName);
		$_FileManager = new FileManager();
		if ($_FileManager->open($fileName, FILE_MANAGER_READ_BINARY)) {
			$this->content = $_FileManager->readFile();
			$_FileManager->close();
		}
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setContent
	// @desc		Seta o conte�do do elemento
	// @access		public
	// @param		content string	Valor para o conte�do da parte
	// @return		void
	//!-----------------------------------------------------------------
	function setContent($content) {
		$this->content = $content;
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::encodeContent
	// @desc		Solicita a codifica��o do conte�do armazenado na classe
	//				utilizando o padr�o de codifica��o escolhido (padr�o da classe
	//				� 8bit)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function encodeContent() {
		if ($this->contentEncoding == 'quoted-printable')
			$this->_encodeQuotedPrintable();
		else
			$this->content = StringUtils::encode($this->content, $this->contentEncoding);
	}

	//!-----------------------------------------------------------------
	// @function	MailPart::setLineEnd
	// @desc		Atribui um valor para a string de final de linha utilizada pela classe
	// @access		public
	// @param		lineEnd string	Caractere(s) de final de linha
	// @return		void
	//!-----------------------------------------------------------------
	function setLineEnd($lineEnd) {
		$this->lineEnd = $lineEnd;
	}

    //!-----------------------------------------------------------------
	// @function	MailPart::buildSource
	// @desc		Constr�i os cabe�alhos e os retorna montados juntamente
	//				com o conte�do da parte
	// @access		public
	// @return		string	C�digo da parte MIME
	//!-----------------------------------------------------------------
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

	//!-----------------------------------------------------------------
	// @function	MailPart::_encodeQuotedPrintable
	// @desc		Codifica o conte�do da parte para o modo de codifica��o QP (quoted-printable)
	// @access		private
	// @return		string String codificada
	// @author		Brent R. Matzelle <bmatzelle@yahoo.com>
	//!-----------------------------------------------------------------
	function _encodeQuotedPrintable() {
		$this->content = str_replace("\r\n", "\n", $this->content);
		$this->content = str_replace("\r", "\n", $this->content);
		$this->content = str_replace("\n", $this->lineEnd, $this->content);
		if (!StringUtils::endsWith($this->content, $this->lineEnd))
			$this->content .= $this->lineEnd;
		// substitui caracteres ASCII altos e caracteres de controle
        $this->content = preg_replace('/([\000-\010\013\014\016-\037\075\177-\377])/e', "'='.sprintf('%02X', ord('\\1'))", $this->content);
		// substitui espa�os e tabula��es quando for o �ltimo caractere de uma linha
        $this->content = preg_replace("/([\011\040])" . $this->lineEnd . "/e", "'='.sprintf('%02X', ord('\\1')).'" . $this->lineEnd . "'", $this->content);
		// tamanho m�ximo de linha: 76 depois do final (74 + espa�o + '=')
        $this->content = StringUtils::wrap($this->content, 74);
	}
}
?>