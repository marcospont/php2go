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
// $Header: /www/cvsroot/php2go/core/template/DocumentElement.class.php,v 1.21 2006/05/07 15:21:50 mpont Exp $
// $Date: 2006/05/07 15:21:50 $

//!-----------------------------------------------------------------
import('php2go.template.Template');
//!-----------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DocumentElement
// @desc		Este objeto gerencia um elemento declarado no layout
//				de um documento. Este elemento pode ser do tipo string,
//				ao qual pode ser atribu�do um valor, ou do tipo template,
//				o que torna todos os m�todos da classe Template acess�veis
//				para a inst�ncia da classe DocumentElement
// @package		php2go.template
// @extends		Template
// @uses		Db
// @uses		ADORecordSet
// @uses		FileSystem
// @author		Marcos Pont
// @version		$Revision: 1.21 $
//!-----------------------------------------------------------------
class DocumentElement extends Template
{
	var $contentBuffer = '';	// @var contentBuffer string	"" Buffer do conte�do inserido atrav�s da fun��o 'put'

	//!-----------------------------------------------------------------
	// @function	DocumentElement::DocumentElement
	// @desc		Construtor de um elemento de documento. Executa
	//				o construtor da classe pai (Template) inicialmente
	//				orientado a um valor de texto e n�o a um arquivo
	// @access		public
	//!-----------------------------------------------------------------
	function DocumentElement() {
		parent::Template('', T_BYVAR);
	}

	//!-----------------------------------------------------------------
	// @function	DocumentElement::&createFrom
	// @desc		Cria uma inst�ncia da classe DocumentElement a partir
	//				dos par�metros fornecidos
	// @param		src string	Arquivo ou conte�do string do elemento
	// @param		type int	"T_BYFILE" Indicativo do tipo do conte�do
	// @return		DocumentElement object
	// @access		public
	// @static
	//!-----------------------------------------------------------------
	function &factory($src, $type=T_BYFILE) {
		$Element = new DocumentElement();
		$Element->put($src, $type);
		$Element->parse();
		return $Element;
	}

	//!-----------------------------------------------------------------
	// @function	DocumentElement::getContentBuffer
	// @desc		Busca o conte�do atual do buffer
	// @access		public
	// @return		string Conte�do atual do buffer da classe
	//!-----------------------------------------------------------------
	function getContentBuffer() {
		return $this->contentBuffer;
	}

	//!-----------------------------------------------------------------
	// @function	DocumentElement::put
	// @desc		Adiciona um conte�do string ou de arquivo ao elemento.
	//				O conte�do extra�do da vari�vel ou do arquivo � inserido
	//				em um buffer. Ao fim de todas as inser��es de conte�do a
	//				partir da fun��o put(), deve-se executar o comando parse()
	// @access		public
	// @param		content mixed		Conte�do string ou arquivo a ser inclu�do no elemento
	// @param		contentType int		"T_BYVAR" Tipo do conte�do que est� sendo inserido:
	//									T_BYFILE para arquivos e T_BYVAR para vari�veis
	// @return		void
	//!-----------------------------------------------------------------
	function put($content, $contentType = T_BYVAR) {
		if (parent::isPrepared()) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_PUT_ON_PREPARED_TEMPLATE'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			if ($contentType == T_BYFILE) {
				// adiciona o conte�do do arquivo no buffer
				$this->contentBuffer .= FileSystem::getContents($content);
				// registra a data de �ltima modifica��o, se esta for a maior entre os componentes do buffer
				$mtime = FileSystem::lastModified($content, TRUE);
				if (!isset($this->tplMTime) || $mtime > $this->tplMTime)
					$this->tplMTime = $mtime;
			} elseif ($contentType == T_BYVAR) {
				$this->contentBuffer .= $content;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	DocumentElement::parse
	// @desc		Sobrescreve o m�todo parse da classe Template para que seja poss�vel
	//				reiniciar o parser com o conte�do final do buffer
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function parse() {
		$saveIncludeData = $this->Parser->tplIncludes;
		$this->Parser = new TemplateParser($this->contentBuffer, T_BYVAR);
		$this->Parser->tplIncludes = $saveIncludeData;
		parent::parse();
	}

	//!-----------------------------------------------------------------
	// @function	DocumentElement::assignFromQuery
	// @desc		Atribui a uma vari�vel declarada no template o
	//				valor da primeira c�lula do resultado de uma query
	// @access		public
	// @param		variable string		Vari�vel declarada no template
	// @param		sqlStmt mixed		Comando SQL (string ou statement preparado) para consulta dos dados
	// @param		connectionId string	"NULL" ID da conex�o a banco de dados a ser utilizada
	// @return		bool Retorna TRUE se a vari�vel foi declarada ou FALSE do contr�rio
	//!-----------------------------------------------------------------
	function assignFromQuery($variable, $sqlStmt, $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		if (parent::isVariableDefined($variable)) {
        	parent::assign($variable, $Db->getFirstCell($sqlStmt));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	DocumentElement::generateFromQuery
	// @desc		A partir de uma consulta SQL, cria v�rias inst�ncias
	//				de um bloco e atribui os valores encontrados �s
	//				colunas declaradas para o bloco
	// @param		blockName string		Nome do bloco. A escolha do bloco base gerar� um erro
	// @param		sqlCode string		Consulta SQL a ser realizada
	// @param		connectionId string	"NULL" ID da conex�o a banco de dados a ser utilizada
	// @return		void
	//!-----------------------------------------------------------------
	function generateFromQuery($blockName, $sqlCode, $connectionId=NULL) {
		if (!parent::isBlockDefined($blockName)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $blockName), E_USER_ERROR, __FILE__, __LINE__);
		} else if ($blockName == TP_ROOTBLOCK) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_REPLICATE_ROOT_BLOCK'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$Db =& Db::getInstance($connectionId);
			$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
			if ($Rs =& $Db->query($sqlCode)) {
				$saveCurrentBlock = parent::getCurrentBlockName();
				while (!$Rs->EOF) {
					parent::createAndAssign($blockName, $Rs->fields);
					$Rs->moveNext();
				}
				parent::setCurrentBlock($saveCurrentBlock);
			}
			$Db->setFetchMode($oldMode);
		}
	}

	//!-----------------------------------------------------------------
	// @function	DocumentElement::generateFromDataSet
	// @desc		A partir de um DataSet cujo conjunto de dados j� foi carregado,
	//				exibe as informa��es replicando um bloco do template e atribuindo
	//				os valores de cada registro retornado
	// @access		public
	// @param		DataSet DataSet object	Conjunto de dados previamente constru�do
	// @param		containerBlock string	Bloco que cont�m o bloco de repeti��o em seu interior
	// @param		emptyBlock string		Bloco a ser criado se o DataSet estiver vazio
	// @param		loopBlock string		Nome do bloco de repeti��o
	// @return		void
	// @note		Abaixo, segue uma poss�vel estrutura para um arquivo template:
	//				<pre>
	//				&lt;!-- START BLOCK : container --&gt;
	//				Insira neste bloco os cabe�alhos das colunas
	//				&lt;!-- START BLOCK : loop --&gt;
	//				Insira vari�veis para as colunas, baseadas nos nomes dos campos no DataSet
	//				Ex: {colunaA}, {colunaB}
	//				&lt;!-- END BLOCK : loop --&gt;
	//				&lt;!-- END BLOCK : container --&gt;
	//				&lt;!-- START BLOCK : empty --&gt;
	//				Insira uma mensagem informando que n�o existem dados a serem exibidos
	//				&lt;!-- END BLOCK : empty --&gt;
	//!-----------------------------------------------------------------
	function generateFromDataSet($DataSet, $containerBlock, $emptyBlock, $loopBlock) {
		if ($containerBlock == TP_ROOTBLOCK || $emptyBlock == TP_ROOTBLOCK || $loopBlock == TP_ROOTBLOCK) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_REPLICATE_ROOT_BLOCK'), E_USER_ERROR, __FILE__, __LINE__);
		}
		if (TypeUtils::isInstanceOf($DataSet, 'DataSet')) {
			$saveCurrentBlock = parent::getCurrentBlockName();
			if ($DataSet->getRecordCount() > 0) {
				if (!parent::isBlockDefined($containerBlock))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $containerBlock), E_USER_ERROR, __FILE__, __LINE__);
				parent::createBlock($containerBlock);
				if (!parent::isBlockDefined($loopBlock))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $loopBlock), E_USER_ERROR, __FILE__, __LINE__);
				while (!$DataSet->eof()) {
					parent::createAndAssign($loopBlock, $DataSet->current());
					$DataSet->moveNext();
				}
			} else {
				if (!parent::isBlockDefined($emptyBlock))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $emptyBlock), E_USER_ERROR, __FILE__, __LINE__);
				parent::createBlock($emptyBlock);
			}
			parent::setCurrentBlock($saveCurrentBlock);
		}
	}
}
?>