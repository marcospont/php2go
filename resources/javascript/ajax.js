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

/**
 * @fileoverview
 * This file holds the AJAX libraries of the PHP2Go
 * Javascript Framework: base class, request class,
 * response class and an utility class called AjaxUpdater
 */

if (!PHP2Go.included[PHP2Go.baseUrl + 'ajax.js']) {

PHP2Go.include(PHP2Go.baseUrl + 'util/throbber.js');

if (window.ActiveXObject && !window.XMLHttpRequest) {
	/**
	 * @ignore
	 */
    window.XMLHttpRequest = function() {
		var conn = null, ids = ['MSXML2.XMLHTTP.5.0', 'MSXML2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP', 'Microsoft.XMLHTTP'];
		for (var i=0; i<ids.length; i++) {
			try {
				conn = new ActiveXObject(ids[i]);
				break;
			} catch(e) { };
		}
		return conn;
    };
}

/**
 * Base class of this AJAX library. Holds the connection object,
 * which can be an ActiveX object or an XMLHttpRequest instance
 * @constructor
 */
Ajax = function() {
	/**
	 * XMLHttp connection
	 * @type Object
	 */
	this.conn = null;
	/**
	 * Transaction ID
	 * @type String
	 */
	this.transId = PHP2Go.uid('ajax');
	/**
	 * Event listeners
	 * @type Array
	 */
	this.listeners = [];
};

/**
 * This static method is used by {@link Ajax} constructor to
 * create a new HTTP connection. An error message is displayed
 * if the browser doesn't support AJAX
 * @type Object
 */
Ajax.getTransport = function() {
	var conn = null;
	try {
		conn = new XMLHttpRequest();
	} catch(e) {
	} finally {
		if (!conn)
			PHP2Go.raiseException(Lang.ajaxSupport);
		return conn;
	}
};

/**
 * Utility method that transforms a string of
 * HTTP headers in an associative hash
 * @type Hash
 */
Ajax.parseHeaders = function(str) {
	var res = {}, pos, headers = str.split("\n");
	headers.walk(function(item, idx) {
		if (item) {
			pos = item.indexOf(':');
			if (pos != -1)
				res[item.substring(0, pos).trim()] = item.substring(pos+1).trim();
		}
	});
	return res;
};

/**
 * Holds the current active transaction count
 * @type Number
 */
Ajax.transactionCount = 0;

/**
 * Binds a new event listener. The event name is case-sensitive, and
 * the supported events are: onLoading, onLoaded, onInteractive,
 * onComplete, onSuccess, onJSONResult, onXMLResult, onFailure and onException.
 * More than one listener can be bound to a single event name
 * @param {String} name Event name
 * @param {Function} func Listener function
 * @param {Object} scope Listener scope. Defaults to 'this'
 * @type void
 */
Ajax.prototype.bind = function(name, func, scope) {
	this.listeners[name] = this.listeners[name] || [];
	this.listeners[name].push([func, scope || this]);
};

/**
 * Raise an event from its name and an optional set of arguments
 * @param {String} name Event name
 * @param {Array} args Event arguments
 * @type void
 */
Ajax.prototype.raise = function(name, args) {
	args = args || [];
	var listeners = this.listeners[name] || [];
	listeners.walk(function(item, idx) {
		item[0].apply(item[1], args);
	});
};

/**
 * This class is able to perform HTTP requests using Ajax. It's fully
 * customizable, once you're able to configure HTTP headers, parameters,
 * content type and body. Besides, the class is able to respond to different
 * response types: JSON, XML, Javascript or HTML
 * @constructor
 * @extends Ajax
 * @param {String} url Request URL (with or without a query string)
 * @param {Object} args Arguments
 */
AjaxRequest = function(url, args) {
	this.Ajax();
	/**
	 * Request URI
	 * @type String
	 */
	this.uri = url;
	/**
	 * Request method (GET or POST)
	 * @type String
	 */
	this.method = 'POST';
	/**
	 * Whether to use an asynchronous request
	 * @type Boolean
	 */
	this.async = true;
	/**
	 * Set of HTTP headers
	 * @type Hash
	 */
	this.headers = {'Accept' : 'text/javascript, text/html, application/xml, text/xml, application/json, */*'};
	/**
	 * Content type. Defaults to 'application/x-www-form-urlencoded'
	 * @type String
	 */
	this.contentType = 'application/x-www-form-urlencoded';
	/**
	 * Encoding. Defaults to 'iso-8859-1'
	 * @type String
	 */
	this.encoding = 'iso-8859-1';
	/**
	 * Hash of GET/POST parameters
	 * @type Hash
	 */
	this.params = {};
	/**
	 * Associated form
	 * @type Object
	 */
	this.form = null;
	/**
	 * Associated form fields
	 * @type Array
	 */
	this.formFields = [];
	/**
	 * Request body
	 * @type String
	 */
	this.body = null;
	/**
	 * Throbber to be used during the request
	 * @type Object
	 */
	this.throbber = null;
	this.readArguments(args || {});
};
AjaxRequest.extend(Ajax, 'Ajax');

/**
 * Parses the arguments provided to the AjaxRequest constructor.
 * Acceptable argument names: method, async, params, contentType,
 * encoding, body, form, formFields, scope and all supported event names
 * @param {Object} args Arguments
 * @type void
 */
AjaxRequest.prototype.readArguments = function(args) {
	var events = ['onLoading', 'onLoaded', 'onInteractive', 'onComplete', 'onSuccess', 'onFailure', 'onJSONResult', 'onXMLResult', 'onException'];
	if (args.method)
		this.method = args.method;
	if (typeof(args.async) != 'undefined')
		this.async = !!args.async;
	if (args.params) {
		for (var param in args.params)
			this.params[param] = args.params[param];
	}
	if (args.headers) {
		for (var name in args.headers)
			this.headers[name] = args.headers[name];
	}
	if (args.contentType)
		this.contentType = args.contentType;
	if (args.encoding)
		this.encoding = args.encoding;
	if (args.body)
		this.body = args.body;
	if (args.form)
		this.form = args.form;
	if (args.formFields)
		this.formFields = args.formFields;
	if (args.throbber) {
		if ((args.throbber.constructor || $EF) == Throbber)
			this.throbber = args.throbber;
		else
			this.throbber = new Throbber({element: args.throbber});
	}
	for (var i=0; i<events.length; i++) {
		if (args[events[i]])
			this.bind(events[i], args[events[i]], args.scope || null);
	}
};

/**
 * Adds a new request parameter
 * @param {String} param Param name
 * @param {Object} val Param value
 * @type void
 */
AjaxRequest.prototype.addParam = function(param, val) {
	this.params[param] = val;
};

/**
 * Method that effectively creates and performs the Ajax HTTP request.
 * If the class contains a form that requires validation, its validator
 * will be executed, aborting the request in case of failure. An exception
 * during the send process will raise an onException event
 * @type void
 */
AjaxRequest.prototype.send = function() {
	this.form = $(this.form);
	if (this.form) {
		if (this.form.validator && !this.form.validator.run())
			return;
	}
	try {
		// query string
		var queryStr = this.buildQueryString();
		// uri & body
		var uri, body;
		if (this.method.equalsIgnoreCase('get')) {
			uri = this.uri + (this.uri.match(/\?/) ? '&' + queryStr : '?' + queryStr);
			body = null;
		} else {
			uri = this.uri;
			body = (this.body || queryStr);
		}
		Ajax.transactionCount++;
		if (this.throbber)
			this.throbber.show();
		this.conn = Ajax.getTransport();
		this.conn.open(this.method, uri, this.async);
		this.conn.onreadystatechange = this.onStateChange.bind(this);
		// request headers
		this.headers['Content-Type'] = this.contentType + '; charset=' + this.encoding;
		this.headers['X-Requested-With'] = 'XMLHttpRequest';
		if (this.method.equalsIgnoreCase('post')) {
			this.headers['Content-Length'] = body.length;
			if (this.conn.overrideMimeType)
				this.headers['Connection'] = 'close';
		}
		for (name in this.headers)
			this.conn.setRequestHeader(name, this.headers[name]);
		this.conn.send(body);
		if (!this.async && this.conn.overrideMimeType)
			this.onStateChange();
	} catch (e) {
		this.raise('onException', [e]);
	}
};

/**
 * This method can be used to abort a request in progress
 * @type void
 */
AjaxRequest.prototype.abort = function() {
	if (this.conn && this.conn.readyState >= 1 && this.conn.readyState < 4) {
		this.conn.abort();
		this.release();
	}
};

/**
 * Internal method used to handle ready state changes.
 * When the request state changes to complete, a response
 * object is created and the proper events are fired: onComplete,
 * onJSONResult (if a json object is available), onXMLResult
 * (if responseXML is available), onSuccess (successful HTTP
 * response code) and onFailure (HTTP error)
 * @access private
 * @type void
 */
AjaxRequest.prototype.onStateChange = function() {
	if (this.conn) {
		switch (this.conn.readyState) {
			// uninitialized
			case 0 :
				break;
			// loading
			case 1 :
				this.raise('onLoading');
				break;
			// loaded
			case 2 :
				this.raise('onLoaded');
				break;
			// interactive
			case 3 :
				this.raise('onInteractive');
				break;
			// complete
			case 4 :
				if (this.throbber)
					this.throbber.hide();
				var resp = this.createResponse();
				this.raise('onComplete', [resp]);
				if (resp.success) {
					if ((resp.headers['Content-type'] || '').match(/^text\/javascript/i)) {
						try {
							eval(resp.responseText);
							this.raise('onSuccess', [resp]);
						} catch(e) {
							this.raise('onException', [e]);
						}
					} else {
						this.raise('onSuccess', [resp]);
						if (resp.json)
							this.raise('onJSONResult', [resp]);
						if (resp.xmlRoot)
							this.raise('onXMLResult', [resp]);
					}
				} else {
					this.raise('onFailure', [resp]);
				}
				this.release();
				break;
		}
	}
};

/**
 * Internal method used by {@link AjaxRequest#send} to
 * build the query string included in the HTTP request
 * @access private
 * @type String
 */
AjaxRequest.prototype.buildQueryString = function() {
	var item, key, subKey, buf = [];
	// form fields
	if (this.form)
		buf.push(Form.serialize(this.form, this.formFields));
	// request parameters
	for (key in this.params) {
		item = this.params[key];
		if (item != null) {
			if (typeof(item) == 'object') {
				if (item.length) {
					for (var i=0; i<item.length; i++)
						buf.push(key.urlEncode() + "[]=" + String(item[i]).urlEncode());
				} else {
					for (subKey in item)
						buf.push(key.urlEncode() + "[" + subKey.urlEncode() + "]=" + String(item[subKey]).urlEncode());
				}
			} else {
				buf.push(key.urlEncode() + "=" + String(item).urlEncode());
			}
		}
	};
	return buf.join('&');
};

/**
 * Internal method used by {@link AjaxRequest#onStateChange} to
 * create a new instance of the AjaxResponse class
 * @access private
 * @type AjaxResponse
 */
AjaxRequest.prototype.createResponse = function() {
	var resp = new AjaxResponse(this.transId);
	try {
		resp.status = this.conn.status;
	} catch(e) {
		resp.status = 13030;
	};
	switch (resp.status) {
		case 12002 :
		case 12029 :
		case 12030 :
		case 12031 :
		case 12152 :
		case 13030 :
			resp.status = 0;
			resp.statusText = Lang.commFailure;
			return resp;
		default :
			resp.statusText = this.conn.statusText;
			resp.headers = Ajax.parseHeaders(this.conn.getAllResponseHeaders());
			resp.responseText = this.conn.responseText;
			resp.responseXML = this.conn.responseXML;
			try {
				if (resp.headers['X-JSON'])
					resp.json = eval('(' + resp.headers['X-JSON'] + ')');
				if ((resp.headers['Content-Type'] || '').match(/^application\/json/i))
					resp.json = eval('(' + resp.responseText + ')');
			} catch(e) {}
			if (resp.responseXML)
				resp.xmlRoot = resp.responseXML.documentElement;
			resp.success = (resp.status >= 200 && resp.status < 300);
			return resp;
	}
};

/**
 * Release the connection object
 * @access private
 * @type void
 */
AjaxRequest.prototype.release = function() {
	this.conn.onreadystatechange = $EF;
	delete this.conn;
	Ajax.transactionCount--;
};

/**
 * This class is used to respond to an Ajax HTTP request.
 * It's created inside {@link AjaxRequest} and is populated
 * according with the information returned by the server when
 * the request is complete
 * @constructor
 * @param {String} transId Transaction ID
 */
AjaxResponse = function(transId) {
	/**
	 * Transaction ID
	 * @type String
	 */
	this.transId = transId;
	/**
	 * Response status
	 * @type Number
	 */
	this.status = null;
	/**
	 * Text response status
	 * @type String
	 */
	this.statusText = null;
	/**
	 * Response headers
	 * @type Object
	 */
	this.headers = {};
	/**
	 * Response text
	 * @type String
	 */
	this.responseText = null;
	/**
	 * Response XML
	 * @type Object
	 */
	this.responseXML = null;
	/**
	 * Response JSON object
	 * @type Object
	 */
	this.json = null;
	/**
	 * XML root, when responseXML is available
	 * @type Object
	 */
	this.xmlRoot = null;
	/**
	 * Indicates a successful response
	 * @type Boolean
	 */
	this.success = false;
};

/**
 * Extends AjaxRequest in order to populate a given container
 * with the response returned by the server. Optionally, 2
 * containers (success and failure) can be provided. 2 more
 * settings can be provided through the args parameter: noScripts
 * (avoid &lt;script&gt; tags inside the response) and insert
 * (indicates response's insert position in the target container)
 * @constructor
 * @extends AjaxRequest
 * @param {String} url Request URL
 * @param {Object} args Settings
 */
AjaxUpdater = function(url, args) {
	args = args || {};
	this.AjaxRequest(url, args);
	/**
	 * Success container
	 * @type Object
	 */
	this.success = null;
	/**
	 * Failure container
	 * @type Object
	 */
	this.failure = null;
	if (args.container) {
		if (args.container.success) {
			this.success = $(args.container.success);
			if (args.container.failure)
				this.failure = $(args.container.failure);
		} else {
			this.success = $(args.container);
			this.failure = $(args.container);
		}
	}
	/**
	 * Insert type. Null means that we must replace the container's HTML code
	 * @type String
	 */
	this.insert = args.insert || null;
	/**
	 * Whether to strip script tags from the response
	 * @type Boolean
	 */
	this.noScripts = !!args.noScripts;
	this.bind('onSuccess', this.update);
	this.bind('onFailure', this.update);
};
AjaxUpdater.extend(AjaxRequest, 'AjaxRequest');

/**
 * This method is called during the onSuccess and
 * onFailure events. It populates the proper container
 * with the returned response body
 * @param {AjaxResponse} response AJAX response
 * @type void
 */
AjaxUpdater.prototype.update = function(response) {
	var resp = response.responseText;
	if (this.noScripts)
		resp = resp.stripScripts();
	if (response.success) {
		if (this.success) {
			if (this.insert)
				this.success.insertHTML(resp, this.insert, true);
			else
				this.success.update(resp, true);
			this.success.show();
		}
	} else {
		if (this.failure) {
			if (this.insert)
				this.failure.insertHTML(resp, this.insert, true);
			else
				this.failure.update(resp, true);
			this.failure.show();
		}
	}
};

PHP2Go.included[PHP2Go.baseUrl + 'ajax.js'] = true;

}