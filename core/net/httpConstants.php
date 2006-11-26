<?php

// @const HTTP_DEFAULT_PORT "80"
// Porta padr�o para a cria��o de conex�es HTTP
define('HTTP_DEFAULT_PORT', 80);

// @const HTTP_DEFAULT_TIMEOUT "10"
// Timeout padr�o para as conex�es HTTP criadas na classe
define('HTTP_DEFAULT_TIMEOUT', 10);

// @const HTTP_CRLF "\r\n"
// Caractere(s) de final de linha utilizados
define('HTTP_CRLF', "\r\n");

// @const HTTP_STATUS_CONTINUE "100"
// Indica que o cliente deve continuar com o envio da requisi��o
define('HTTP_STATUS_CONTINUE', 100);

// @const HTTP_STATUS_SWITCHING_PROTOCOLS "101"
// Indica uma altera��o no protocolo utilizado na conex�o
define('HTTP_STATUS_SWITCHING_PROTOCOLS', 101);

// @const HTTP_STATUS_OK "200"
// A requisi��o HTTP foi processada com sucesso
define('HTTP_STATUS_OK', 200);

// @const HTTP_STATUS_CREATED "201"
// A requisi��o resultou na cria��o de um novo recurso, retornado na resposta
define('HTTP_STATUS_CREATED', 201);

// @const HTTP_STATUS_ACCEPTED "202"
// A requisi��o foi aceita para processamento
define('HTTP_STATUS_ACCEPTED', 202);

// @const HTTP_STATUS_NON_AUTHORITATIVE "203"
// A metainforma��o retornada n�o � o conjunto definitivo como disponibilizado no servidor de origem
define('HTTP_STATUS_NON_AUTHORITATIVE', 203);

// @const HTTP_STATUS_NO_CONTENT "204"
// O servidor n�o necessita retornar um entidade body na mensagem de resposta
define('HTTP_STATUS_NO_CONTENT', 204);

// @const HTTP_STATUS_RESET_CONTENT "205"
// O agente deve resetar o documento que fez com que a requisi��o fosse enviada
define('HTTP_STATUS_RESET_CONTENT', 205);

// @const HTTP_STATUS_PARTIAL_CONTENT "206"
// O sevidor processou uma requisi��o GET parcial para o recurso
define('HTTP_STATUS_PARTIAL_CONTENT', 206);

// @const HTTP_STATUS_MULTIPLE_CHOICES "300"
// O servidor retorna m�ltiplas escolhas de redirecionamento
define('HTTP_STATUS_MULTIPLE_CHOICES', 300);

// @const HTTP_STATUS_MOVED_PERMANENTLY "301"
// O recurso requisitado foi movido permanentemente para uma outra URI
define('HTTP_STATUS_MOVED_PERMANENTLY', 301);

// @const HTTP_STATUS_FOUND "302"
// O recurso requisitado reside temporariamente em uma outra URI
define('HTTP_STATUS_FOUND',302);

// @const HTTP_STATUS_SEE_OTHER "303"
// A resposta para a requisi��o aponta para uma outra URI, que deve ser acessada via GET
define('HTTP_STATUS_SEE_OTHER', 303);

// @const HTTP_STATUS_NOT_MODIFIED "304"
// Se foi enviado um comando GET condicional, este status � retornado se o recurso n�o foi atualizado
define('HTTP_STATUS_NOT_MODIFIED', 304);

// @const HTTP_STATUS_USE_PROXY "305"
// O servidor retorna a recomenda��o do uso de um servidor proxy
define('HTTP_STATUS_USE_PROXY', 305);

// @const HTTP_STATUS_TEMPORARY_REDIRECT "307"
// O recurso requisitado reside temporariamente em uma outra URI
define('HTTP_STATUS_TEMPORARY_REDIRECT', 307);

// @const HTTP_STATUS_BAD_REQUEST "400"
// A requisi��o n�o pode ser entendida pelo servidor
define('HTTP_STATUS_BAD_REQUEST', 400);

// @const HTTP_STATUS_UNAUTHORIZED "401"
// A requisi��o requer autentica��o de usu�rio, na forma de um cabe�alho WWW-Authenticate
define('HTTP_STATUS_UNAUTHORIZED', 401);

// @const HTTP_STATUS_FORBIDDEN "403"
// O servidor aceitou a requisi��o, mas n�o est� habilitado a process�-la
define('HTTP_STATUS_FORBIDDEN', 403);

// @const HTTP_STATUS_NOT_FOUND "404"
// O servidor n�o encontrou nenhum recurso relacionado com a URI da requisi��o
define('HTTP_STATUS_NOT_FOUND', 404);

// @const HTTP_STATUS_METHOD_NOT_ALLOWED "405"
// O m�todo especificado na requisi��o n�o � permitido pelo recurso solicitado
define('HTTP_STATUS_METHOD_NOT_ALLOWED', 405);

// @const HTTP_STATUS_NOT_ACCEPTABLE "406"
// O recurso identificado na requisi��o n�o � capaz de gerar resposta a partir dos cabe�alhos enviados
define('HTTP_STATUS_NOT_ACCEPTABLE', 406);

// @const HTTP_STATUS_PROXY_AUTH_REQUIRED "407"
// O c�digo � similar ao 401 (Unauthorized), mas indica que o cliente deve autenticar-se em um servidor proxy
define('HTTP_STATUS_PROXY_AUTH_REQUIRED', 407);

// @const HTTP_STATUS_REQUEST_TIMEOUT "408"
// O servidor n�o conseguiu responder � requisi��o em tempo h�bil
define('HTTP_STATUS_REQUEST_TIMEOUT', 408);

// @const HTTP_STATUS_CONFLICT "409"
// A requisi��o n�o p�de ser completada devido a um conflito no recurso solicitado
define('HTTP_STATUS_CONFLICT', 409);

// @const HTTP_STATUS_GONE "410"
// O recurso requisitado n�o est� mais dispon�vel no servidor, e n�o existem endere�os de redirecionamento
define('HTTP_STATUS_GONE', 410);

// @const HTTP_STATUS_LENGTH_REQUIRED "411"
// O servidor n�o pode aceitar a requisi��o sem a presen�a de um cabe�alho Content-Length
define('HTTP_STATUS_LENGTH_REQUIRED', 411);

// @const HTTP_STATUS_REQUEST_TOO_LARGE "413"
// A requisi��o possui um tamanho maior do que o m�ximo que o servidor � capaz de processar
define('HTTP_STATUS_REQUEST_TOO_LARGE', 413);

// @const HTTP_STATUS_URI_TOO_LONG "414"
// A requisi��o enviou um valor de URI maior do que o m�ximo que o servidor � capaz de interpretar
define('HTTP_STATUS_URI_TOO_LONG', 414);

// @const HTTP_STATUS_SERVER_ERROR "500"
// Indica que ocorreu um erro interno no servidor, que impede a resposta � requisi��o enviada
define('HTTP_STATUS_SERVER_ERROR', 500);

// @const HTTP_STATUS_NOT_IMPLEMENTED "501"
// O servidor n�o suporta a funcionalidade encontrada na requisi��o
define('HTTP_STATUS_NOT_IMPLEMENTED', 501);

// @const HTTP_STATUS_BAD_GATEWAY "502"
// O servidor, agindo como gateway ou proxy, recebeu uma resposta inv�lida de um servidor utilizado para responder � requisi��o
define('HTTP_STATUS_BAD_GATEWAY', 502);

// @const HTTP_STATUS_SERVICE_UNAVAILABLE "503"
// O servidor est� temporariamente indispon�vel para responder � requisi��o
define('HTTP_STATUS_SERVICE_UNAVAILABLE', 503);

// @const HTTP_STATUS_GATEWAY_TIMEOUT "504"
// O servidor, agindo como gateway ou proxy, n�o recebeu uma resposta de outro servidor em tempo h�bil
define('HTTP_STATUS_GATEWAY_TIMEOUT', 504);

// @const HTTP_STATUS_VERSION_NOT_SUPPORTED "505"
// O servidor n�o suporta ou n�o � capaz de interpretar a vers�o de protocolo HTTP utilizada na requisi��o
define('HTTP_STATUS_VERSION_NOT_SUPPORTED',	505);

?>
