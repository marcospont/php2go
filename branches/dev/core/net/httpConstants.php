<?php

// @const HTTP_DEFAULT_PORT "80"
// Porta padrão para a criação de conexões HTTP
define('HTTP_DEFAULT_PORT', 80);

// @const HTTP_DEFAULT_TIMEOUT "10"
// Timeout padrão para as conexões HTTP criadas na classe
define('HTTP_DEFAULT_TIMEOUT', 10);

// @const HTTP_CRLF "\r\n"
// Caractere(s) de final de linha utilizados
define('HTTP_CRLF', "\r\n");

// @const HTTP_STATUS_CONTINUE "100"
// Indica que o cliente deve continuar com o envio da requisição
define('HTTP_STATUS_CONTINUE', 100);

// @const HTTP_STATUS_SWITCHING_PROTOCOLS "101"
// Indica uma alteração no protocolo utilizado na conexão
define('HTTP_STATUS_SWITCHING_PROTOCOLS', 101);

// @const HTTP_STATUS_OK "200"
// A requisição HTTP foi processada com sucesso
define('HTTP_STATUS_OK', 200);

// @const HTTP_STATUS_CREATED "201"
// A requisição resultou na criação de um novo recurso, retornado na resposta
define('HTTP_STATUS_CREATED', 201);

// @const HTTP_STATUS_ACCEPTED "202"
// A requisição foi aceita para processamento
define('HTTP_STATUS_ACCEPTED', 202);

// @const HTTP_STATUS_NON_AUTHORITATIVE "203"
// A metainformação retornada não é o conjunto definitivo como disponibilizado no servidor de origem
define('HTTP_STATUS_NON_AUTHORITATIVE', 203);

// @const HTTP_STATUS_NO_CONTENT "204"
// O servidor não necessita retornar um entidade body na mensagem de resposta
define('HTTP_STATUS_NO_CONTENT', 204);

// @const HTTP_STATUS_RESET_CONTENT "205"
// O agente deve resetar o documento que fez com que a requisição fosse enviada
define('HTTP_STATUS_RESET_CONTENT', 205);

// @const HTTP_STATUS_PARTIAL_CONTENT "206"
// O sevidor processou uma requisição GET parcial para o recurso
define('HTTP_STATUS_PARTIAL_CONTENT', 206);

// @const HTTP_STATUS_MULTIPLE_CHOICES "300"
// O servidor retorna múltiplas escolhas de redirecionamento
define('HTTP_STATUS_MULTIPLE_CHOICES', 300);

// @const HTTP_STATUS_MOVED_PERMANENTLY "301"
// O recurso requisitado foi movido permanentemente para uma outra URI
define('HTTP_STATUS_MOVED_PERMANENTLY', 301);

// @const HTTP_STATUS_FOUND "302"
// O recurso requisitado reside temporariamente em uma outra URI
define('HTTP_STATUS_FOUND',302);

// @const HTTP_STATUS_SEE_OTHER "303"
// A resposta para a requisição aponta para uma outra URI, que deve ser acessada via GET
define('HTTP_STATUS_SEE_OTHER', 303);

// @const HTTP_STATUS_NOT_MODIFIED "304"
// Se foi enviado um comando GET condicional, este status é retornado se o recurso não foi atualizado
define('HTTP_STATUS_NOT_MODIFIED', 304);

// @const HTTP_STATUS_USE_PROXY "305"
// O servidor retorna a recomendação do uso de um servidor proxy
define('HTTP_STATUS_USE_PROXY', 305);

// @const HTTP_STATUS_TEMPORARY_REDIRECT "307"
// O recurso requisitado reside temporariamente em uma outra URI
define('HTTP_STATUS_TEMPORARY_REDIRECT', 307);

// @const HTTP_STATUS_BAD_REQUEST "400"
// A requisição não pode ser entendida pelo servidor
define('HTTP_STATUS_BAD_REQUEST', 400);

// @const HTTP_STATUS_UNAUTHORIZED "401"
// A requisição requer autenticação de usuário, na forma de um cabeçalho WWW-Authenticate
define('HTTP_STATUS_UNAUTHORIZED', 401);

// @const HTTP_STATUS_FORBIDDEN "403"
// O servidor aceitou a requisição, mas não está habilitado a processá-la
define('HTTP_STATUS_FORBIDDEN', 403);

// @const HTTP_STATUS_NOT_FOUND "404"
// O servidor não encontrou nenhum recurso relacionado com a URI da requisição
define('HTTP_STATUS_NOT_FOUND', 404);

// @const HTTP_STATUS_METHOD_NOT_ALLOWED "405"
// O método especificado na requisição não é permitido pelo recurso solicitado
define('HTTP_STATUS_METHOD_NOT_ALLOWED', 405);

// @const HTTP_STATUS_NOT_ACCEPTABLE "406"
// O recurso identificado na requisição não é capaz de gerar resposta a partir dos cabeçalhos enviados
define('HTTP_STATUS_NOT_ACCEPTABLE', 406);

// @const HTTP_STATUS_PROXY_AUTH_REQUIRED "407"
// O código é similar ao 401 (Unauthorized), mas indica que o cliente deve autenticar-se em um servidor proxy
define('HTTP_STATUS_PROXY_AUTH_REQUIRED', 407);

// @const HTTP_STATUS_REQUEST_TIMEOUT "408"
// O servidor não conseguiu responder à requisição em tempo hábil
define('HTTP_STATUS_REQUEST_TIMEOUT', 408);

// @const HTTP_STATUS_CONFLICT "409"
// A requisição não pôde ser completada devido a um conflito no recurso solicitado
define('HTTP_STATUS_CONFLICT', 409);

// @const HTTP_STATUS_GONE "410"
// O recurso requisitado não está mais disponível no servidor, e não existem endereços de redirecionamento
define('HTTP_STATUS_GONE', 410);

// @const HTTP_STATUS_LENGTH_REQUIRED "411"
// O servidor não pode aceitar a requisição sem a presença de um cabeçalho Content-Length
define('HTTP_STATUS_LENGTH_REQUIRED', 411);

// @const HTTP_STATUS_REQUEST_TOO_LARGE "413"
// A requisição possui um tamanho maior do que o máximo que o servidor é capaz de processar
define('HTTP_STATUS_REQUEST_TOO_LARGE', 413);

// @const HTTP_STATUS_URI_TOO_LONG "414"
// A requisição enviou um valor de URI maior do que o máximo que o servidor é capaz de interpretar
define('HTTP_STATUS_URI_TOO_LONG', 414);

// @const HTTP_STATUS_SERVER_ERROR "500"
// Indica que ocorreu um erro interno no servidor, que impede a resposta à requisição enviada
define('HTTP_STATUS_SERVER_ERROR', 500);

// @const HTTP_STATUS_NOT_IMPLEMENTED "501"
// O servidor não suporta a funcionalidade encontrada na requisição
define('HTTP_STATUS_NOT_IMPLEMENTED', 501);

// @const HTTP_STATUS_BAD_GATEWAY "502"
// O servidor, agindo como gateway ou proxy, recebeu uma resposta inválida de um servidor utilizado para responder à requisição
define('HTTP_STATUS_BAD_GATEWAY', 502);

// @const HTTP_STATUS_SERVICE_UNAVAILABLE "503"
// O servidor está temporariamente indisponível para responder à requisição
define('HTTP_STATUS_SERVICE_UNAVAILABLE', 503);

// @const HTTP_STATUS_GATEWAY_TIMEOUT "504"
// O servidor, agindo como gateway ou proxy, não recebeu uma resposta de outro servidor em tempo hábil
define('HTTP_STATUS_GATEWAY_TIMEOUT', 504);

// @const HTTP_STATUS_VERSION_NOT_SUPPORTED "505"
// O servidor não suporta ou não é capaz de interpretar a versão de protocolo HTTP utilizada na requisição
define('HTTP_STATUS_VERSION_NOT_SUPPORTED',	505);

?>
