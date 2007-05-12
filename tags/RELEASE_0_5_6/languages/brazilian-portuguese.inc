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

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "As configurações atuais do servidor não permitem a execução do PHP2Go.<BR>A versão do PHP instalada em seu servidor é a <I>%s</I>. O requerimento mínimo do PHP2Go é a versão <I>%s</I>.<BR>Instale uma versão igual ou superior para utilizá-lo normalmente.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "A extensão <I>%s</I> não é suportada pela instalação atual do PHP.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Erro na Classe %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Erro no script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Erro no Banco de Dados";
$P2G_LANGUAGE['ERR_INVALID_VALUE'] = "Valor Inválido!";
$P2G_LANGUAGE['ERR_DUPLICATED_VALUE'] = "Este valor já foi inserido!";
$P2G_LANGUAGE['ERR_COMM_FAILURE'] = "Falha na comunicação";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Não foi possível carregar os parâmetros de conexão com o banco de dados! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "Os parâmetros de conexão ao banco de dados são inválidos! Os parâmetros USER, BASE e TYPE são obrigatórios. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Não foi possível conectar ao banco de dados com as configurações de acesso atuais. Elas estão corretas?";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS_PATH'] = "Não foi possível importar e/ou instanciar a classe de conexão %s!";
$P2G_LANGUAGE['ERR_INVALID_CONNECTION_CLASS'] = "A classe de conexão %s não é uma subclasse de php2go.db.Db!";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Erro Fatal",
	2 => "Alerta",
	4 => "Erro de Sintaxe",
	8 => "Aviso",
	16 => "Erro Fatal do Núcleo",
	32 => "Alerta do Núcleo",
	64 => "Erro Fatal do Compilador",
	128 => "Alerta do Compilador",
	256 => "Erro de Usuário",
	512 => "Alerta de Usuário",
	1024 => "Aviso de Usuário"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "A variável 'ABSOLUTE_URI' não foi informada na tabela geral de configurações!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "A variável <I>%s</I> não foi informada na tabela geral de configurações!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "O arquivo de mensagens para o domínio %s, na linguagem %s, é inválido ou inexistente.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Não foi possível encontrar o valor <I>%s</I> no tabela geral de configurações. Ela foi corretamente configurada na instalação?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Não foi possível carregar o módulo <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Não foi possível carregar módulos a partir de um diretório válido em <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Não foi possível carregar o arquivo <I>%s</I> em <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Não foi possível converter a data para o formato %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "A classe %s não pode ser utilizada em ambiente Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "A função %s não está definida!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "O método %s não está definido na classe %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "A classe <I>%s</I> é abstrata e não pode ser instanciada diretamente!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "A data %s não está no formato %s exigido.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "A variável %s fornecida à função %s não é um tipo 'resource' válido.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "O autenticador %s não é uma subclasse de php2go.auth.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Não foi possível importar e/ou instanciar o autenticador %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "O container de usuário %s não é uma subclasse de php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "Não foi possível importar e/ou instanciar o container de usuário %s!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER'] = "O autorizador %s não é uma subclasse de php2go.auth.Authorizer!";
$P2G_LANGUAGE['ERR_INVALID_AUTHORIZER_PATH'] = "Não foi possível importar e/ou instanciar o autorizador %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Ocorreu um erro na interpretação do arquivo XML:<BR>Erro: %s<BR>Linha: %s, Coluna: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Ocorreu um erro de sintaxe na interpretação do código PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Não foi possível encontrar o arquivo ou diretório %s. Ele existe?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Não foi possível abrir o arquivo %s com o modo %s. Verifique as permissões do arquivo e o modo utilizado.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Não foi possível criar o arquivo ou diretório %s. Verifique as permissões do diretório.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Não foi possível abrir o arquivo ou diretório %s para leitura. Ele existe? O caminho está correto?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Não foi possível abrir o arquivo ou diretório %s para escrita. Você tem permissões para sobrescreve-lo?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Não foi possível excluir o arquivo %s.<BR>Você possui permissões de escrita neste diretório? Os arquivos possuem permissão para serem deletados?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Não foi possível abrir o arquivo %s para inclusão. Ele existe? O caminho está correto?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Não foi possível aplicar o modo %s ao arquivo ou diretório %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Não foi possível encontrar o diretório %s. Ele existe?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Não foi possível abrir o diretório %s para leitura. Verifique suas permissões.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Não foi possível abrir o arquivo do objeto serializado para leitura. O caminho está certo? O arquivo foi criado?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "A consulta de inserção na tabela '%s' retornou vazia para o conjunto de valores fornecidos.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "A consulta de alteração da tabela '%s' para a seleção %s retornou vazia para o conjunto de valores fornecidos.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "A consulta de publicação de variáveis globais %s não retornou resultados.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "A consulta fornecida à função Db::toGlobals() deve iniciar por 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Não foi possível inicializar o Template com um nome de arquivo vazio!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "O template não foi preparado, pois o seu conteúdo é vazio. Verifique o conteúdo fornecido ao construtor como parâmetro.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Não foi possível encontrar a variável %s no arquivo template <I>%s</I>. <BR>Para obter o funcionamento completo desta ferramenta, insira {%s} no código do template.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Não foi possível encontrar o bloco <I>%s</I> para criação. Ele foi declarado no template?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Não foi possível encontrar o bloco %s para reusá-lo como %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "O elemento %s já foi configurado com o método DocumentElement::parse(). Não é possível adicionar elementos com a função put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "O template %s não foi preparado. Execute a função %s::parse(); antes de realizar operações sobre o mesmo.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Este template já foi preparado anteriormente. Execute o método parse() somente uma vez.";
$P2G_LANGUAGE['ERR_TPLPARSE_DEFINED_BLOCK'] = "O bloco %s já foi definido anteriormente!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_BLOCKDEF'] = "A definição do bloco <I>%s</I> está desbalanceada!";
$P2G_LANGUAGE['ERR_TPLPARSE_INCOMPLETE_BLOCKDEF'] = "A especificação do bloco <I>%s</I> está incompleta na tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_BLOCKINSIDELOOP'] = "Não é possível definir blocos dinâmicos dentro de loops!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_TAG'] = "A definição da tag <I>%s</I> está desbalanceada!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNEXPECTED_TAG'] = "Tag <I>%s</I> não esperada!";
$P2G_LANGUAGE['ERR_TPLPARSE_UNBALANCED_PARENTHESIS'] = "Parênteses não balanceados na expressão <I>%s</I>!";
$P2G_LANGUAGE['ERR_TPLPARSE_TAG_SYNTAX'] = "A definição da tag <I>%s</I> é inválida : %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TOKEN'] = "Token inválido na expressão <I>%s</I> : %s";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTE'] = "Valor de atributo inválido ou não encontrado!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_ATTRIBUTENAME'] = "<I>%s</I> não é um nome válido de atributo!";
$P2G_LANGUAGE['ERR_TPLPARSE_REQUIRED_ATTRIBUTE'] = "A propriedade <I>%s</I> é obrigatória na definição da tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ATTRIBUTE'] = "A propriedade <I>%s</I> é inválida na definição da tag %s!";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_ASSIGN'] = "Caractere '=' após nome de atributo não encontrado!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_IS_EXPR'] = "Operação inválida para uma expressão do tipo 'is' : <I>%s</I>";
$P2G_LANGUAGE['ERR_TPLPARSE_MISSING_TAG_ARGS'] = "A tag <I>%s</I> necessita de argumentos!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_TAG_ARGS'] = "A tag <I>%s</I> não pode conter argumentos!";
$P2G_LANGUAGE['ERR_TPLPARSE_INVALID_MODIFIER'] = "O modificador <I>%s</I> é inválido ou inexistente.";
$P2G_LANGUAGE['ERR_WIDGET_MANDATORY_PROPERTY'] = "A propriedade <i>%s</i> é obrigatória na definição do widget <i>%s</i>!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "Não foi possível encontrar nenhum elemento no layout de documento fornecido.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "O bloco base de um template não pode ser replicado.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "O valor de %s deve ser maior do que zero na função %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "O valor de %s deve ser um array na função %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "O valor de '%s' está fora dos limites permitidos: %s e %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "O valor de <I>%s</I> não é um valor válido para URL!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> não é um valor válido para host de conexão!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Não foi possível encontrar um DNS válido para o domínio %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "A propriedade %s da variável de sessão %s não foi encontrada. Execute a função createProperty()";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "O cronômetro %s da variável de sessão %s não foi encontrado. Execute a função createTimeCounter()";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "O objeto fornecido como parâmetro não é uma instância válida da classe <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Parâmetros insuficientes para construção da calculadora!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Parâmetros insuficientes para construção da seleção de cor!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "O método %s é inválido para a submissão do formulário '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> não é um nome válido para função de validação!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "O campo <I>%s</I> já foi definido para o formulário <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "O botão <I>%s</I> já foi definido para o formulário <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "O ID de seção <I>%s</I> já foi definido para o formulário <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Não foi possível definir a visibilidade da seção <I>%s</I>, pois a função <I>%s</I> não foi definida!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "A seção <I>%s</I> é condicional. Deve ser declarado um bloco de nome <I>%s</I> no template contendo os campos da seção!";
$P2G_LANGUAGE['ERR_FORM_REQUIRED_SUMMARY'] = "Os seguintes campos do formulário são obrigatórios:";
$P2G_LANGUAGE['ERR_FORM_ERRORS_SUMMARY'] = "Os seguintes erros foram encontrados neste formulário:";
$P2G_LANGUAGE['ERR_FORM_COMPLETE_FIELDS'] = "Por favor, complete os campos e tente novamente.";
$P2G_LANGUAGE['ERR_FORM_FIX_FIELDS'] = "Por favor, corrija os erros e tente novamente.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "O campo %s é obrigatório.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID'] = "O campo %s possui um valor incorreto!";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_DATATYPE'] = "O campo %s possui um valor de %s inválido.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "A precisão numérica do campo %s é inválida.\nOs tamanhos máximos são %s para a parte inteira e %s para a parte decimal.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "O campo %s deve conter apenas caracteres alfanuméricos.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "O campo %s deve conter um dos seguintes valores: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "O campo %s contém um número inválido de cartão de crédito %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "O campo %s deve possuir ao menos %s caracteres.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "O campo %s deve possuir no máximo %s caracteres.";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "O campo %s deve ser igual ao campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "O campo %s deve ser diferente do campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "O campo %s deve ser maior do que o campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "O campo %s deve ser menor do que o campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "O campo %s deve ser maior ou igual ao campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "O campo %s deve ser menor ou igual ao campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "O campo %s deve ter um valor igual a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "O campo %s deve ter um valor diferente de %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "O campo %s deve ter um valor maior do que %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "O campo %s deve ter um valor menor do que %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "O campo %s deve ter um valor maior ou igual a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "O campo %s deve ter um valor menor ou igual a %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "A máscara <I>%s</I>, no campo <I>%s</I>, não é uma máscara válida!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "O tipo de campo <I>%s</I> é inválido!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "O código informado no campo %s não corresponde ao conteúdo da imagem!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "O tipo de campo <I>%s</I> não pode ser utilizado dentro de um <I>grid</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "O valor do atributo <I>CELLSIZES</I> no campo <I>%s</I> é inválido. É necessário fornecer N+1 tamanhos (N=tamanho do FIELDSET) e a soma dos tamanhos deve ser 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "A consulta definida no <I>DATASOURCE</I> do campo <I>%s</I> deve retornar N+2 colunas (N=tamanho do FIELDSET)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Os atributos do tratador de eventos <I>%s</I> são inválidos ou incompletos!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Os atributos da regra <I>%s</I> são inválidos ou incompletos!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "A especificação da tag DATASOURCE do campo <I>%s</I> é inválida.<BR>Os nodos filhos KEYFIELD e LOOKUPTABLE são obrigatórios!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "A especificação do campo <I>%s</I> está incompleta. Deve ser definido ao menos um filho do tipo &lt;OPTION&gt;!";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "O atributo VALUE da opção índice <I>%d</I> do campo <I>%s</I> não foi especificado!";
$P2G_LANGUAGE['ERR_MISSING_DBGROUPFIELD_DATASOURCE'] = "Não foi especificada uma tag DATASOURCE para o campo %s <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBGROUPFIELD_RESULTS'] = "A consulta especificada no DATASOURCE do campo %s <I>%s</I> não retornou resultados!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "O atributo VALUE da opção índice %d do campo COMBOFIELD <I>%s</I> não foi especificado!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "A especificação do campo EDITSELECTIONFIELD <I>%s</I> está incompleta. Os nodos filhos EDITFIELD e LOOKUPFIELD são obrigatórios!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "A especificação do campo LOOKUPSELECTIONFIELD <I>%s</I> está errada. É obrigatório fornecer dois nodos filhos do tipo LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "Não foi especificada uma tag DATASOURCE para o primeiro filho LOOKUPFIELD do campo LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Os itens \$fields e \$tables são obrigatórios na montagem de uma consulta SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "A estrutura do grid <I>%s</I> é inválida. Deve ser declarado um <I>DATASOURCE</I> e um conjunto de campos (<I>FIELDSET</I>) com pelo menos um elemento!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "A especificação do campo RANGEFIELD <I>%s</I> está errada. É obrigatório fornecer dois nodos filhos do tipo EDITFIELD, COMBOFIELD, LOOKUPFIELD ou DATEPICKERFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "A especificação do DATAFILTER <I>%s</I> é inválida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "A máscara informada para o DATAFILTER <I>%s</I> é inválida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Já existe um DATAFILTER com o ID <I>%s</I> informado.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "A especificação do campo EDITSEARCHFIELD <I>%s</I> está incompleta. É obrigatório fornecer ao menos um DATAFILTER, um DATASOURCE e um LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Não foi possível realizar a operação!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "A pesquisa não pôde ser executada porque todos os campos de pesquisa estão vazios.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "A pesquisa não pôde ser executada porque os filtros são vazios ou possuem menos do que %s caracteres.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "O número de colunas de agrupamento deve ser menor do que o número de colunas da consulta!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "A coluna de agrupamento <I>%s</I> não existe no resultado da consulta!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "A variável <I>%s</I> do bloco <I>%s</I> não foi declarada no template!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "O valor <I>%s</I> não é uma definição válida para os tamanhos das colunas!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "A soma dos tamanhos das colunas fornecido é função setColumnSizes deve ser igual a 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "O número de tamanhos de colunas fornecido (%s) é diferente do número de colunas do relatório (%s, %s agrupadas)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Não foi possível gerar o formulário de busca, pois a variável <I>%s</I> não foi encontrada.<BR>Declare {%s} no template do relatório para prosseguir.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Você deve informar Rótulo, Nome do Campo e Máscara obrigatoriamente na montagem dos parâmetros de busca!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "A máscara do <I>%s</I> do parâmetro <I>%d</I> de busca é inválida!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "A função setAlternateStyle deve ser chamada com pelo menos dois parâmetros (nomes de estilos CSS)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Uma coluna escondida (%s) não pode ser incluída nos cabeçalhos de agrupamento!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Uma coluna de cabeçalho de grupo (%s) não pode ser definida como escondida!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "O número de colunas escondidas deve ser menor do que o total de colunas da consulta!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "A coluna <I>%s</I> marcada como escondida não existe no resultado da consulta!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "O tipo <I>%s</I> não é um tipo de DataSet válido!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "A consulta fornecida no parâmetro \$rootSql do método Menu::loadFromDatabase() não retornou resultados ou não possui o mínimo de 2 colunas (CAPTION e LINK)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "A consulta fornecida no parâmetro \$childSql do método Menu::loadFromDatabase() é inválida.<BR>Forneça um statement com a variável de ligação '~variável~' declarada e as 2 colunas obrigatórias (CAPTION e LINK)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "O menu não foi criado. Construa os itens do menu utilizando Menu::loadFromDatabase() ou Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "O nodo raiz do arquivo XML não possui nodos filhos!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "A imagem <I>%s</I> é inválida. Os valores válidos são: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Não foi possível realizar a conexão. É preciso configurar a conexão com os métodos FtpClient::setServer() e FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Não foi possível realizar a autenticação. Um dos dados do Usuário (nome ou senha) não foi informado";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Limites da planilha excedidos: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "O tamanho máximo para uma nota de célula foi excedido<BR>Valor: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "O número máximo de 4 fontes para uma planilha foi excedido!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Não foi possível realizar o upload. O arquivo não foi transferido corretamente.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Não foi possível realizar o upload. O arquivo transferido é maior do que o tamanho máximo permitido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "Não foi possível realizar o upload. O nome de arquivo %s é inválido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "Não foi possível realizar o upload. A extensão %s não é permitida.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Não foi possível realizar o upload. O arquivo <I>%s</I> já existe e não foi sobrescrito.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Não foi possível mover o arquivo temporário de upload para a pasta destino.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Não foi possível abrir o socket na porta %s com o host %s!%s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "A conexão com o socket não está ativa!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Fim de arquivo atingido, não foi possível completar a operação!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "O tempo de espera do socket foi excedido. A operação não pode ser concluída!";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Não foi fornecido nenhum destinatário à mensagem!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Não foi possível assinar o conteúdo da mensagem.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Erro ao conectar ao servidor SMTP %s!<BR>[Erro %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Erro ao executar o comando %s no servidor SMTP!<BR>[Erro %d] - %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Não foi possível realizar a autenticação com o servidor SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Para encriptar ou desencriptar dados, você deve fornecer uma chave de criptografia através do método Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Não foi possível inicializar o cifrador <I>%s</I> utilizando o modo %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Não foi possível extrair uma chave pública para encriptar os dados fornecidos. Forneça um arquivo contendo um certificado correto ao método OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Não foi possível extrair uma chave pública para verificar a assinatura dos dados. Forneça um arquivo contendo um certificado correto ao método OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Não foi possível extrair uma chave privada para decriptar os dados. Forneça o nome do arquivo que contém a chave privada e a frase de passagem, se a chave criada exigir uma.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Não foi possível extrair uma chave privada para gerar a assinatura digital. Forneça o nome do arquivo que contém a chave privada e a frase de passagem, se a chave criada exigir uma.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "O caminho fornecido para o certificado é inválido!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Não foi possível ler o conteúdo do certificado fornecido!%s";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Erro ao conectar ao servidor POP3 %s!<BR>[Erro %d] - %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Erro ao executar o comando %s no servidor POP!<BR>%s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Não foi possível realizar a autenticação com o servidor POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Não foi possível executar o comando APOP : não existe um banner de autenticação armazenado!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Não foi possível estabelecer a conexão HTTP com o host <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "O host da conexão HTTP não foi setado. Execute o método HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Não foi possível instanciar a classe de validação <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "A classe de validação <I>%s</I> é inválida! O método %s::execute() não foi encontrado.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "A expressão avaliada não alcançou o resultado desejado.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "A função ou método <I>%s</I> não é um callback válido!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "O arquivo <I>%s</I> não contém dados válidos de configuração!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> não é uma especificação válida de cor.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Não foi possível alocar uma nova cor para esta imagem!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Nome ou alias do tratador de evento JSRS não fornecido!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "O tratador de evento JSRS %s é inválido!";
$P2G_LANGUAGE['ERR_AJAX_SUPPORT'] = "Seu navegador não tem suporte a AJAX!";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPNAME'] = "Erro de interpretação JSON: %s não é um nome válido de propriedade";
$P2G_LANGUAGE['ERR_JSON_MISSING_COLON'] = "Erro de interpretação JSON: separador : não encontrado na posição %d";
$P2G_LANGUAGE['ERR_JSON_INVALID_PROPVALUE'] = "Erro de interpretação JSON: %s não é um valor válido de propriedade";
$P2G_LANGUAGE['ERR_JSON_MISSING_COMMA'] = "Erro de interpretação JSON: separador , não encontrado na posição %d";
$P2G_LANGUAGE['ERR_JSON_ESCAPE_SEQUENCE'] = "Erro de interpretação JSON: seqüência de escape inválida na posição %d";
$P2G_LANGUAGE['ERR_JSON_UNTERMINATED_STRING'] = "Erro de interpretação JSON: string não terminada na posição %d";
$P2G_LANGUAGE['ERR_JSON_CYCLE'] = "O codificador JSON encontrou um ciclo em uma instância da classe %s!";
$P2G_LANGUAGE['ERR_JSON_ENCODE'] = "Ocorreu um erro ao converter o tipo %s para uma string JSON!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Submeter";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Ver Calendário";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Abrir a Calculadora";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "Calculadora";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "Resultado";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Domingo","Segunda-Feira","Terça-Feira","Quarta-Feira","Quinta-Feira","Sexta-Feira","Sábado");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Utiliza Compressão %s -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Tamanho Original: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Tamanho Compactado: %d";
$P2G_LANGUAGE['EMPTY_SEARCH'] = "Você precisa escolher um filtro e informar um termo de pesquisa!";
$P2G_LANGUAGE['EMPTY_RESULTS'] = "A pesquisa não retornou resultados!";
$P2G_LANGUAGE['FORM_INPUT_MASK_MESSAGES'] = array(
	'optionalCharFirst' => "O indicador de caractere opcional deve suceder um literal.",
	'escapeCharLast' => "O indicador de caractere especial (escape) deve anteceder um literal.",
	'nestedCharClass' => "Classe de caracteres inválida.",
	'invalidLimits' => "Limitadores de tamanho inválidos."
);
$P2G_LANGUAGE['FORM_MASKS'] = array(
	'CPFCNPJ' =>'CPF/CNPJ',
	'CURRENCY' => 'Moeda',
	'DATE' => 'Data',
	'TIME' => 'Hora',
	'EMAIL' => 'E-Mail',
	'FLOAT' => 'Número Decimal',
	'INTEGER' => 'Número Inteiro',
	'DIGIT' => 'Número',
	'IP' => 'Endereço IP',
	'WORD' => 'Texto alfanumérico',
	'URL' => 'URL/Endereço Internet',
	'ZIP' => 'CEP'
);
$P2G_LANGUAGE['EDITSEARCH_BTN_VALUE'] = "Buscando...";
$P2G_LANGUAGE['EDITOR_VARS'] = array(
	'formatBlock' => 'Formatação',
	'formatBlockOptions' => array(
		'h1' => 'Cabeçalho 1 &lt;H1&gt;',
		'h2' => 'Cabeçalho 2 &lt;H2&gt;',
		'h3' => 'Cabeçalho 3 &lt;H3&gt;',
		'h4' => 'Cabeçalho 4 &lt;H4&gt;',
		'h5' => 'Cabeçalho 5 &lt;H5&gt;',
		'h6' => 'Cabeçalho 6 &lt;H6&gt;',
		'address' => 'Endereço &lt;ADDR&gt;',
		'pre' => 'Formatado &lt;PRE&gt;',
		'removeformat' => 'Remover Formato'
	),
	'topButtons' => array(
		'bold' => 'Formatar Negrito',
		'italic' => 'Formatar Itálico',
		'underline' => 'Formatar Sublinhado',
		'strikethrough' => 'Formatar Tachado',
		'cut' => 'Cortar',
		'copy' => 'Copiar',
		'paste' => 'Colar',
		'pickforecolor' => 'Alterar a cor da letra',
		'pickbackcolor' => 'Alterar a cor do fundo',
		'addemoticon' => 'Inserir uma Caricatura',
		'selectall' => 'Selecionar Tudo',
		'removeformat' => 'Remover Formato'
	),
	'font' => 'Fonte',
	'fontSize' => 'Tam',
	'bottomButtons' => array(
		'justifyleft' => 'Alinhar o texto à Esquerda',
		'justifycenter' => 'Centralizar o texto',
		'justifyright' => 'Alinhar o texto à Direita',
		'justifyfull' => 'Justificar o texto',
		'insertorderedlist' => 'Inserir/Remover Lista Numerada',
		'insertunorderedlist' => 'Inserir/Remover Lista não Numerada',
		'indent' => 'Aumentar a Indentação',
		'outdent' => 'Diminuir a Indentação',
		'createlink' => 'Inserir um Link',
		'unlink' => 'Remover Link',
		'insertimage' => 'Inserir uma Imagem',
		'inserthorizontalrule' => 'Inserir Linha Horizontal'
	),
	'editMode' => 'Editar em Modo Avançado',
	'emoticon' => 'Escolha uma Caricatura',
	'validateMode' => 'Para utilizar as ferramentas do editor avançado, desmarque a caixa \'Edição Avançada\'',
	'createLink' => 'Digite o endereço do link (por exemplo: http://www.domínio.com.br):',
	'insertImage' => 'Digite o caminho da imagem:'
);
$P2G_LANGUAGE['COLOR_PICKER_VARS'] = array(
	'popupTitle' => "Clique sobre a cor que desejar",
	'colorTitle' => "Escolher esta Cor"
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtre a Lista ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Insira Valores ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Adicionar os Itens Selecionados',
	'addall' => 'Adicionar todos os Itens',
	'rem' => 'Remover os Itens Selecionados',
	'remall' => 'Remover todos os Itens'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Adicionar',
	'rem' => 'Remover os Itens Selecionados',
	'remall' => 'Remover Todos os Itens'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Caracteres";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Disponíveis:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Inseridos:";
$P2G_LANGUAGE['FORM_DATA_BIND_MESSAGES'] = array(
	'insertSuccess' => "Dados incluídos com sucesso!",
	'updateEmpty' => "Não existem registros para serem editados!",
	'updateSuccess' => "Dados atualizados com sucesso!",
	'deleteEmpty' => "Não existem registros para serem excluídos!",
	'deleteSuccess' => "Dados excluídos com sucesso!",
	'deleteConfirm' => "Deseja realmente excluir este registro?",
	'sortInvalid' => "Você precisa escolher um campo para ordenar!",
	'gotoEmpty' => "Você precisa digitar o registro para o qual deseja ir!",
	'gotoInvalid' => "O registro solicitado é inválido!"
);
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Ir para o primeiro registro',
	'navPrevTip' => 'Ir para o registro anterior',
	'navNextTip' => 'Ir para o próximo registro',
	'navLastTip' => 'Ir para o último registro',
	'actNew' => 'Novo',
	'actNewTip' => 'Criar um novo registro',
	'actEdit' => 'Editar',
	'actEditTip' => 'Editar o registro atual',
	'actDel' => 'Excluir',
	'actDelTip' => 'Excluir o registro atual',
	'actSave' => 'Salvar',
	'actSaveTip' => 'Salvar o registro atual',
	'actCancel' => 'Cancelar',
	'actCancelTip' => 'Cancelar a ação atual',
	'sortAsc' => 'Ordenação Ascendente',
	'sortDesc' => 'Ordenação Descendente',
	'sortChoose' => 'Escolha um valor para ordenar',
	'sortTit' => 'Ordenar',
	'sortFirst' => 'Campos de Ordenação',
	'gotoTip' => 'Digite o registro para o qual deseja ir',
	'goto' => 'Ir',
	'gotoBtnTip' => 'Clique para ir para o registro escolhido',
	'gotoTit' => 'Ir Para',
	'filter' => 'Filtrar',
	'filterTip' => 'Escolha o campo pelo qual deseja filtrar',
	'filterVTip' => 'Escolha o valor a ser utilizado no filtro',
	'filterBtnTip' => 'Clique aqui para filtrar',
	'filterTit' => 'Filtrar',
	'filterFirst' => 'Campos de Filtragem'
);
$P2G_LANGUAGE['REPORT_INVALID_PAGE'] = "Número de página inválido!";
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'A consulta ou os filtros de pesquisa utilizados não retornaram resultados',
	'backLink' => 'Voltar ao Relatório'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordernar por %s";
$P2G_LANGUAGE['REPORT_ORDER_OPTIONS_LABEL'] = "Ordernar por:";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Primeira',
	'firstTip' => 'Ir para a primeira página do relatório',
	'prevTit' => 'Anterior',
	'prevTip' => 'Ir para a página anterior',
	'prevScrTit' => 'Voltar %d',
	'prevScrTip' => 'Voltar %d Páginas no relatório',
	'pageTip' => 'Ir para a página %d de %d',
	'nextTit' => 'Próxima',
	'nextTip' => 'Ir para a próxima página',
	'nextScrTit' => 'Avançar %d',
	'nextScrTip' => 'Avançar %d páginas no relatório',
	'lastTit' => 'Última',
	'lastTip' => 'Ir para a última página do relatório',
	'rowCount' => '%d registro(s) encontrado(s)',
	'rowsPerPage' => '%d resultado(s) por página',
	'thisPage' => 'Página %d de %d',
	'rowInterval' => 'Exibindo %d-%d de %d resultados',
	'goTo' => 'Ir para a página'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTitle' => 'Buscar',
	'filtersTitle' => 'Escolha um Campo',
	'addBtn' => 'Adicionar',
	'mainOpOr' => 'Qualquer um dos filtros',
	'mainOpAnd' => 'Todos os Filtros',
	'clearBtn' => 'Limpar',
	'viewBtn' => 'Visualizar',
	'sendBtn' => 'Buscar',
	'emptyFilters' => 'Não há filtros adicionados',
	'closeFilters' => 'Fechar',
	'removeFilter' => 'Remover',
	'addedFilter' => 'Filtro Adicionado!',
	'resendConfirmation' => 'Deseja reenviar a consulta com o(s) filtro(s) atual(is)?'
);
$P2G_LANGUAGE['REPORT_NUMBER_OPERATORS'] = array(
	'=' => 'Igual a',
	'!=' => 'Diferente de',
	'>' => 'Maior que',
	'<' => 'Menor que',
	'>=' => 'Maior ou igual a',
	'<=' => 'Menor ou igual a'
);
$P2G_LANGUAGE['REPORT_STRING_OPERATORS'] = array(
	'=' => 'Igual a',
	'!=' => 'Diferente de',
	'LIKE' => 'Contendo',
	'NOT LIKE' => 'Não Contendo',
	'LIKEI' => 'Iniciando com',
	'LIKEF' => 'Terminando com'
);
$P2G_LANGUAGE['OPERATORS'] = array(
	'EQ' => 'igual a',
	'NEQ' => 'diferente de',
	'GT' => 'maior que',
	'LT' => 'menor que',
	'GOET' => 'maior ou igual a',
	'LOET' => 'menor ou igual a',
	'CONTAINING' => 'contendo',
	'STARTING' => 'iniciando com',
	'ENDING' => 'terminando com',
	'BETWEEN' => 'entre',
	'IN' => 'está na lista',
	'NOTIN' => 'não está na lista',
	'AND' => 'e',
	'OR' => 'ou'
);
$P2G_LANGUAGE['CHECKBOX_DESCRIPTIONS'] = array(
	'T' => '%s está selecionado',
	'F' => '%s não está selecionado'
);
$P2G_LANGUAGE['CHECKGROUP_SHORTCUTS'] = array(
	'all' => 'Todos',
	'none' => 'Nenhum',
	'invert' => 'Inverter'
);
return $P2G_LANGUAGE;
?>