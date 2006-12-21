<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2006 Marcos Pont
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
 * @copyright 2002-2006 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @package net
 * @version $Id$
 */

/**
 * Default HTTP port
 */
define('HTTP_DEFAULT_PORT', 80);

/**
 * Default HTTP connection timeout
 */
define('HTTP_DEFAULT_TIMEOUT', 10);

/**
 * HTTP line end characters
 */
define('HTTP_CRLF', "\r\n");

/**
 * Only a part of the request has been received by the server, but as long as it has not been rejected, the client should continue with the request
 */
define('HTTP_STATUS_CONTINUE', 100);

/**
 * The server switches protocol
 */
define('HTTP_STATUS_SWITCHING_PROTOCOLS', 101);

/**
 * The request is OK
 */
define('HTTP_STATUS_OK', 200);

/**
 * The request is complete, and a new resource is created
 */
define('HTTP_STATUS_CREATED', 201);

/**
 * The request is accepted for processing, but the processing is not complete
 */
define('HTTP_STATUS_ACCEPTED', 202);

/**
 * The returned metainformation in the entity-header is not the definitive set as available from the origin server
 */
define('HTTP_STATUS_NON_AUTHORITATIVE', 203);

/**
 * The server has fulfilled the request but does not need to return an entity-body
 */
define('HTTP_STATUS_NO_CONTENT', 204);

/**
 * The user agent should reset the document view which caused the request to be sent
 */
define('HTTP_STATUS_RESET_CONTENT', 205);

/**
 * The server has fulfilled the partial GET request for the resource
 */
define('HTTP_STATUS_PARTIAL_CONTENT', 206);

/**
 * The requested resource corresponds to any one of a set of representations, each with its own specific location
 */
define('HTTP_STATUS_MULTIPLE_CHOICES', 300);

/**
 * The requested resource has been assigned a new permanent URI
 */
define('HTTP_STATUS_MOVED_PERMANENTLY', 301);

/**
 * The requested resource resides temporarily under a different URI
 */
define('HTTP_STATUS_FOUND',302);

/**
 * The response to the request can be found under a different URI and should be retrieved using GET
 */
define('HTTP_STATUS_SEE_OTHER', 303);

/**
 * Indicates that the requested resource is not modified
 */
define('HTTP_STATUS_NOT_MODIFIED', 304);

/**
 * The requested resource must be accessed through the proxy given by the Location field
 */
define('HTTP_STATUS_USE_PROXY', 305);

/**
 * The requested resource resides temporarily under a different URI
 */
define('HTTP_STATUS_TEMPORARY_REDIRECT', 307);

/**
 * The request could not be understood by the server due to malformed syntax
 */
define('HTTP_STATUS_BAD_REQUEST', 400);

/**
 * The request requires user authentication
 */
define('HTTP_STATUS_UNAUTHORIZED', 401);

/**
 * The server understood the request, but is refusing to fulfill it
 */
define('HTTP_STATUS_FORBIDDEN', 403);

/**
 * The server has not found anything matching the Request-URI
 */
define('HTTP_STATUS_NOT_FOUND', 404);

/**
 * The method specified in the Request-Line is not allowed for the resource identified by the Request-URI
 */
define('HTTP_STATUS_METHOD_NOT_ALLOWED', 405);

/**
 * The resource is not acceptable according to the request headers
 */
define('HTTP_STATUS_NOT_ACCEPTABLE', 406);

/**
 * This code is similar to 401, but indicates that the client must first authenticate itself with the proxy
 */
define('HTTP_STATUS_PROXY_AUTH_REQUIRED', 407);

/**
 * The client did not produce a request within the time that the server was prepared to wait
 */
define('HTTP_STATUS_REQUEST_TIMEOUT', 408);

/**
 * The request could not be completed due to a conflict with the current state of the resource
 */
define('HTTP_STATUS_CONFLICT', 409);

/**
 * The requested resource is no longer available at the server and no forwarding address is known
 */
define('HTTP_STATUS_GONE', 410);

/**
 * The server refuses to accept the request without a defined Content-Length
 */
define('HTTP_STATUS_LENGTH_REQUIRED', 411);

/**
 * The server is refusing to process a request because the request entity is larger than the server is willing or able to process
 */
define('HTTP_STATUS_REQUEST_TOO_LARGE', 413);

/**
 * The server is refusing to service the request because the Request-URI is longer than the server is willing to interpret
 */
define('HTTP_STATUS_URI_TOO_LONG', 414);

/**
 * The server encountered an unexpected condition which prevented it from fulfilling the request
 */
define('HTTP_STATUS_SERVER_ERROR', 500);

/**
 * The server does not support the functionality required to fulfill the request
 */
define('HTTP_STATUS_NOT_IMPLEMENTED', 501);

/**
 * The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request
 */
define('HTTP_STATUS_BAD_GATEWAY', 502);

/**
 * The server is currently unable to handle the request due to a temporary overloading or maintenance of the server
 */
define('HTTP_STATUS_SERVICE_UNAVAILABLE', 503);

/**
 * The server, while acting as a gateway or proxy, did not receive a timely response from the upstream server
 */
define('HTTP_STATUS_GATEWAY_TIMEOUT', 504);

/**
 * The server does not support, or refuses to support, the HTTP protocol version that was used in the request message
 */
define('HTTP_STATUS_VERSION_NOT_SUPPORTED',	505);

?>
