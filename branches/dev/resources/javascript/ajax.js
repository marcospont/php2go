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

PHP2Go.include(PHP2Go.baseUrl + 'form.js');
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
 * Binds an AJAX global event listener. The event name is case-sensitive,
 * and the supported events are: onLoading, onLoaded, onIteractive, onComplete,
 * onSuccess, onJSONResult, onXMLResult, onFailure and onException. More than
 * one global listener can be bound to a single event
 * @param {String} name Event name
 * @param {Function} func Listener function
 * @param {Object} scope Listener scope
 * @param {Bool} unshift Add listener on the top of the stack
 * @type void
 */
Ajax.bind = function(name, func, scope, unshift) {
	unshift = !!unshift;
	Ajax.listeners[name] = Ajax.listeners[name] || [];
	Ajax.listeners[name][unshift ? 'unshift' : 'push']([func, scope || null]);
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
 * @ignore
 */
Ajax.lastModified = {};

/**
 * Global AJAX event listeners
 * @type Object
 */
Ajax.listeners = {};

/**
 * @ignore
 */
Ajax.transactionCount = 0;

/**
 * Binds a new event listener. The event name is case-sensitive, and
 * the supported events are: onLoading, onLoaded, onInteractive,
 * onComplete, onSuccess, onJSONResult, onXMLResult, onFailure and onException.
 * More than one listener can be bound to a single event name
 * @param {String} name Event name
 * @param {Function} func Listener function
 * @param {Object} scope Listener scope
 * @param {Bool} unshift Add the listener on the top of the stack
 * @type void
 */
Ajax.prototype.bind = function(name, func, scope, unshift) {
	unshift = !!unshift;
	this.listeners[name] = this.listeners[name] || [];
	this.listeners[name][unshift ? 'unshift' : 'push']([func, scope || null]);
};

/**
 * Raise an event from its name and an optional set of arguments
 * @param {String} name Event name
 * @param {Array} args Event arguments
 * @type void
 */
Ajax.prototype.raise = function(name, args) {
	args = args || [];
	var listeners = (Ajax.listeners[name] || []).concat(this.listeners[name] || []);
	for (var i=0; i<listeners.length; i++)
		listeners[i][0].apply(listeners[i][1] || this, args);
};

/**
 * This class is able to perform HTTP requests using Ajax. It's fully
 * customizable, once you're able to configure HTTP headers, parameters,
 * content type and body. Besides, the class is able to respond to different
 * response types: JSON, XML, Javascript or HTML
 * @constructor
 * @extends Ajax
 * @param {String} uri Request URI (with or without a query string)
 * @param {Object} args Arguments
 */
AjaxRequest = function(uri, args) {
	this.Ajax();
	/**
	 * Request URI
	 * @type String
	 */
	this.uri = uri;
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
	 * Character encoding
	 * @type String
	 */
	this.encoding = null;
	/**
	 * Whether an If-Modified-Since header should be sent
	 * @type Boolean
	 */
	this.ifModified = false;
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
	 * Indicates if the form associated with the request contains files to be uploaded
	 * @type Boolean
	 */
	this.formUpload = false;
	/**
	 * Indicates if the form associated with the request should be validated
	 * @type Boolean
	 */
	this.formValidate = true;
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
	/**
	 * Secure URI, when using IFRAME to upload files
	 * @type String
	 */
	this.secureUri = 'javascript:false';
	this.readArguments(args || {});
};
AjaxRequest.extend(Ajax, 'Ajax');

/**
 * Parses the arguments provided to the AjaxRequest constructor.
 *
 * Acceptable argument names: <ul><li>method</li><li>async</li><li>headers</li>
 * <li>contentType</li><li>encoding</li><li>ifModified</li><li>params</li><li>form</li>
 * <li>formFields</li><li>formUpload</li><li>formValidate</li><li>body</li><li>throbber</li>
 * <li>secureUri</li><li>scope</li><li>onInit</li><li>onLoading</li><li>onLoaded</li>
 * <li>onInteractive</li><li>onComplete</li><li>onSuccess</li><li>onFailure</li>
 * <li>onJSONResult</li><li>onXMLResult</li><li>onException</li></ul>
 *
 * @param {Object} args Arguments
 * @type void
 */
AjaxRequest.prototype.readArguments = function(args) {
	for (var n in args) {
		switch (n) {
			case 'onInit' : case 'onLoading' : 
			case 'onLoaded' : case 'onInteractive' : 
			case 'onComplete' : case 'onSuccess' : 
			case 'onFailure' : case 'onJSONResult' : 
			case 'onXMLResult' : case 'onException' :
				(Object.isFunc(args[n])) && (this.bind(n, args[n], args.scope || null));
				break;
			case 'headers' : case 'params' :
				for (var pn in args[n])
					this[n][pn] = args[n][pn];
				break;
			case 'async' : case 'ifModified' : case 'formValidate' : case 'formUpload' :
				this[n] = !!args[n];
				break;
			case 'throbber' :
				if (args.throbber && (args.throbber.constructor || $EF) != Throbber)
					args.throbber = new Throbber({element: args.throbber});
				this.throbber = args.throbber;
				break;
			case 'throbberCentralize' :
				if (this.throbber)
					this.throbber.centralize = !!args.throbberCentralize;
			default :
				this[n] = args[n];
				break;
		}
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
	if (this.form && this.formValidate) {
		if (this.form.validator && !this.form.validator.run())
			return;
	}
	Ajax.transactionCount++;
	if (this.throbber)
		this.throbber.show();
	// form upload
	if (this.form) {
		var enctype = (this.form.getAttribute('enctype') || '');
		if (this.formUpload || enctype.equalsIgnoreCase('multipart/form-data')) {
			this.doFormUpload();
			return;
		}
	}
	// build query string
	var uri, body, queryStr = this.buildQueryString();
	// get request
	if (this.method.equalsIgnoreCase('get')) {
		uri = this.uri + (this.uri.match(/\?/) ? '&' + queryStr : '?' + queryStr);
		body = null;
	// post request
	} else {
		uri = this.uri;
		body = (this.body || queryStr);
	}
	try {
		this.conn = Ajax.getTransport();
		this.conn.open(this.method, uri, this.async);
		this.conn.onreadystatechange = this.onStateChange.bind(this);
		// set request headers
		this.headers['X-Requested-With'] = 'XMLHttpRequest';
		if (this.ifModified)
			this.headers['If-Modified-Since'] = Ajax.lastModified[this.uri] || 'Thu, 01 Jan 1970 00:00:00 GMT';
		if (this.method.equalsIgnoreCase('post')) {
			this.headers['Content-Type'] = this.contentType + (this.encoding ? '; charset=' + this.encoding : '');
			this.headers['Content-Length'] = body.length;
			if (this.conn.overrideMimeType)
				this.headers['Connection'] = 'close';
		}
		for (var name in this.headers)
			this.conn.setRequestHeader(name, this.headers[name]);
		this.raise('onInit');
		this.conn.send(body);
		if (!this.async && this.conn.overrideMimeType)
			this.onStateChange();
	} catch (e) {
		this.raise('onException', [e]);
	}
};

/**
 * Method called when the form associated with the AJAX request
 * contains one or more files to upload. Performs the request
 * by submitting the form through an IFRAME element
 * @type void
 * @private
 */
AjaxRequest.prototype.doFormUpload = function() {
	try {
		// build iframe
		var id = PHP2Go.uid('ajaxFrame');
		if (PHP2Go.browser.ie) {
			var ifr = document.createElement("<iframe id=\"%1\" name=\"%2\" />".assignAll(id, id));
			ifr.src = this.secureUri;
		} else {
			var ifr = document.createElement('iframe');
			ifr.id = id;
			ifr.name = id;
		}
		ifr.style.position = 'absolute';
		ifr.style.top = '-1000px';
		ifr.style.left = '-1000px';
		document.body.appendChild(ifr);
		// configure form
		var frm = this.form;
		frm.target = id;
		frm.method = 'post';
		frm.enctype = frm.encoding = 'multipart/form-data';
		frm.action = this.uri;
		// add hidden params
		var hp = [];
		this.params['X-Requested-With'] = 'XMLHttpRequest';
		if (this.headers['X-Handler-ID'])
			this.params['X-Handler-ID'] = this.headers['X-Handler-ID'];
		for (var n in this.params) {
			var input = $N('input');
			input.type = 'hidden';
			input.name = n;
			input.id = PHP2Go.uid('ajaxField' + this.transId + '-');
			input.value = this.params[n];
			frm.appendChild(input);
			hp.push(input);
		}
		// upload callback
		var ajax = this;
		var uploadCallback = function(e) {
			ajax.conn = {
				status: 200,
				statusText: 'OK',
				readyState: 4,
				responseText: '',
				responseXML: null,
				abort: $EF,
				headers: {}
			};
			var doc;
			try { 
				doc = (PHP2Go.browser.ie ? ifr.contentWindow.document : (ifr.contentDocument || window.frames[id].document));	
			} catch(e) { 
				doc = null;
			}
			if (doc && doc.body) {
				var container = (doc.body.getElementsByTagName('textarea') || []);
				ajax.conn.responseText = (container.length > 0 ? container[0].value : doc.body.innerHTML);
				ajax.conn.headers['Content-Type'] = 'text/html';
				ajax.conn.headers['Content-Length'] = ajax.conn.responseText.length;
				if (!ajax.conn.responseText.match(/^\s*</)) {
					try {
						var json = eval('(' + ajax.conn.responseText + ')');
						ajax.conn.json = json;
						ajax.conn.headers['Content-Type'] = 'application/json';
					} catch (e) { }
				}
			}
			if (doc && doc.XMLDocument) {
				ajax.conn.responseXML = doc.XMLDocument;
				ajax.conn.headers['Content-Type'] = 'text/xml';
			}
			Event.removeListener(ifr, 'load', uploadCallback);
			ajax.onStateChange();
			(function() { document.body.removeChild(ifr); }).delay(100);
		};
		// add load listener and submit form
		this.raise('onInit');
		frm.submit();
		Event.addListener(ifr, 'load', uploadCallback);
		// remove hidden fields
		hp.walk(function(item, idx) {
			frm.removeChild(item);
		});
	} catch (e) {
		this.raise('onException', [e]);
	}
};

/**
 * This method can be used to abort a request in progress
 * @type Boolean
 */
AjaxRequest.prototype.abort = function() {
	if (this.conn && this.conn.readyState >= 1 && this.conn.readyState < 4) {
		this.conn.abort();
		this.release();
		return true;
	}
	return false;
};

/**
 * Internal method used to handle ready state changes.
 * When the request state changes to complete, a response
 * object is created and the proper events are fired: onComplete,
 * onJSONResult (if a json object is available), onXMLResult
 * (if responseXML is available), onSuccess (successful HTTP
 * response code) and onFailure (HTTP error)
 * @type void
 * @private 
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
					// script responses
					if ((resp.headers['Content-Type'] || '').match(/^(text|application)\/(x-)?(java|ecma)script(;.*)?$/i)) {
						try {
							PHP2Go.eval(resp.responseText);
							this.raise('onSuccess', [resp]);
						} catch(e) {
							this.raise('onException', [e]);
						}
					} else {
						this.raise('onSuccess', [resp]);
						// json handler
						if (resp.json)
							this.raise('onJSONResult', [resp]);
						// xml handler
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
 * @type String
 * @private 
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
 * @type AjaxResponse
 * @private 
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
			resp.headers = (this.conn.headers || Ajax.parseHeaders(this.conn.getAllResponseHeaders()));
			resp.responseText = this.conn.responseText;
			resp.responseXML = this.conn.responseXML;
			// catch Last-Modified header
			if (this.ifModified && resp.headers['Last-Modified'])
				Ajax.lastModified[this.uri] = resp.headers['Last-Modified'];
			// eval JSON response
			try {
				if (this.conn.json) {
					resp.json = this.conn.json;
				} else if ((resp.headers['Content-Type'] || '').match(/^application\/json/i)) {
					resp.json = eval('(' + resp.responseText + ')');
				} else if (resp.headers['X-JSON']) {
					resp.json = eval('(' + resp.headers['X-JSON'] + ')');
				}
			} catch(e) {}
			if (resp.responseXML)
				resp.xmlRoot = resp.responseXML.documentElement;
			resp.success = ((resp.status >= 200 && resp.status < 300) || resp.status == 304);
			return resp;
	}
};

/**
 * Release the connection object
 * @type void
 * @private 
 */
AjaxRequest.prototype.release = function() {
	if (this.conn) {
		this.conn.onreadystatechange = $EF;
		delete this.conn;
		Ajax.transactionCount--;
	}
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
 * Parses and executes command definitions
 * returned in the response in JSON format
 * @type void
 */
AjaxResponse.prototype.run = function() {
	var skip = 0;
	var json = (this.json || {});
	var cmds = json.commands || [];
	for (var i=0; i<cmds.length; i++) {
		var item = cmds[i];
		if (skip-- > 0)
			continue;
		switch (item.cmd) {
			case 'value' :
				(item.frm) ? ($FF(item.frm, item.fld).setValue(item.arg)) : ($F(item.id).setValue(item.arg));
				break;
			case 'combo' :
				var combo = $F(item.id);
				(!item.arg.value) && (item.arg.value = combo.getValue());
				(item.arg.clear) && (combo.clearOptions());
				for (var v in item.arg.options)
					combo.addOption(v, item.arg.options[v]);
				combo.setValue(item.arg.value);
				break;
			case 'enable' :
				var fld = null, elm = null;
				if (item.frm && (fld = $FF(item.frm, item.fld))) {
					fld.enable();
				} else if (fld = $F(item.id)) {
					fld.enable();
				} else if (elm = $(item.id)) {
					elm.disabled = false;
				}
				break;
			case 'disable' :
				var fld = null, elm = null;
				if (item.frm && (fld = $FF(item.frm, item.fld))) {
					fld.disable();
				} else if (fld = $F(item.id)) {
					fld.disable();
				} else if (elm = $(item.id)) {
					elm.disabled = true;
				}
				break;
			case 'focus' :
				(item.frm) ? ($FF(item.frm, item.fld).focus()) : ($F(item.id).focus());
				break;
			case 'clear' :
				if (item.frm) {
					var fld = $FF(item.frm, item.fld);
					(fld) && (fld.clear());
				} else {
					var fld = $F(item.id) || $(item.id);
					(fld) && (fld.clear());
				}
				break;
			case 'reset' :
				Form.reset($(item.id));
				break;
			case 'hide' :
				$(item.id).hide();
				break;
			case 'show' :
				$(item.id).show();
				break;
			case 'attr' :
				var elm = $(item.id);
				for (var p in item.arg)
					elm[p] = item.arg[p];
				break;
			case 'style' :
				var elm = $(item.id);
				for (var p in item.arg)
					elm.setStyle(p, item.arg[p]);
				break;
			case 'create' :
				item.arg.attrs = item.arg.attrs || {};
				(item.id) && (item.arg.attrs.id = id);
				$N(item.arg.tag, $(item.arg.parent), item.arg.styles, item.arg.cont, item.arg.attrs);
				break;
			case 'clear' :
				$(item.id).clear();
				break;
			case 'update' :
				$(item.id).update(item.arg.code, item.arg.eval, item.arg.dom);
				break;
			case 'insert' :
				$(item.id).insert(item.arg.code, item.arg.pos, item.arg.eval);
				break;
			case 'replace' :
				$(item.id).replace(item.arg.code, item.arg.eval);
				break;
			case 'remove' :
				$(item.id).remove();
				break;
			case 'addev' :
				Event.addListener($(item.id), item.arg.type, item.arg.func, item.arg.capt);
				break;
			case 'remev' :
				Event.removeListener($(item.id), item.arg.type, item.arg.func, item.arg.capt);
				break;
			case 'alert' :
				alert(item.arg);
				break;
			case 'confirm' :
				if (!confirm(item.arg.msg)) {
					skip = item.arg.skip;
					// skip all commands
					if (!skip)
						break;
				}
				break;
			case 'redirect' :
				window.location.href = item.arg;
				return;
			case 'eval' :
				PHP2Go.eval(item.arg);
				break;
			case 'func' :
				if (item.arg.delay > 0) {
					setTimeout(function() {
						item.id.apply(item.arg.scope, item.arg.params);
					}, item.arg.delay);
				} else {
					item.id.apply(item.arg.scope, item.arg.params);
				}
				break;
		}
	}
};

/**
 * Extends AjaxRequest in order to populate a given container
 * with the response returned by the server. Optionally, 2
 * containers (success and failure) can be provided. 3 more
 * settings can be provided through the args parameter: noScripts
 * (avoid &lt;script&gt; tags inside the response), insert
 * (indicates response's insert position in the target container)
 * and onUpdate (update event listener)
 * @constructor
 * @extends AjaxRequest
 * @param {String} uri Request URI
 * @param {Object} args Settings
 */
AjaxUpdater = function(uri, args) {
	args = args || {};
	this.AjaxRequest(uri, args);
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
	if (args.onUpdate)
		this.bind('onUpdate', args.onUpdate, args.scope || null);
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
				this.success.insert(resp, this.insert, true);
			else
				this.success.update(resp, true);
			if (this.success.getStyle('display') == 'none')
				this.success.show();
		}
	} else {
		if (this.failure) {
			if (this.insert)
				this.failure.insert(resp, this.insert, true);
			else
				this.failure.update(resp, true);
			if (this.failure.getStyle('display') == 'none')
				this.failure.show();
		}
	}
	this.raise('onUpdate', [response]);
};

/**
 * This class is able to interact with php2go.service.AjaxService
 * class in order to call PHP functions or methods. If the returned
 * response is a JSON string, the class will try to run commands
 * and statements specified inside it
 * @constructor
 * @extends AjaxRequest
 * @param {String} uri Request URI
 * @param {Object} args Settings
 */
AjaxService = function(uri, args) {
	this.AjaxRequest(uri, args);
	this.bind('onFailure', this.parseResponse, null, true);
	this.bind('onJSONResult', this.parseResponse, null, true);
	this.setHandler(args.handler);
};
AjaxService.extend(AjaxRequest, 'AjaxRequest');

/**
 * Returns the active handler ID
 * @type String
 */
AjaxService.prototype.getHandler = function() {
	return this.headers['X-Handler-ID'] || '';
};

/**
 * Changes the service handler ID
 * @param {String} id Handler ID
 * @type void
 */
AjaxService.prototype.setHandler = function(id) {
	this.headers['X-Handler-ID'] = id || '';
};

/**
 * Tries to parse and execute commands from
 * the returned JSON string
 * @param {AjaxResponse} response Response
 * @type void
 * @private
 */
AjaxService.prototype.parseResponse = function(response) {
	try {
		response.run();
	} catch(e) {
		this.raise('onException', [e]);
	}
};

/**
 * This class is able to perform a periodic update, needed by
 * any kind of polling. An instance of the AjaxUpdater class
 * is created and executed at regular intervals
 * @constructor
 * @param {String} uri Request URI
 * @param {Object} args Settings
 */
AjaxPeriodicalUpdater = function(uri, args) {
	/**
	 * Request URI
	 * @type String
	 */
	this.uri = uri;
	/**
	 * Number of seconds between each request
	 * @type Number
	 */
	this.frequency = args.frequency || 2;
	/**
	 * AjaxUpdater instance used to perform periodical requests
	 * @type AjaxRequest
	 */
	this.updater = null;
	/**
	 * Updater arguments
	 * @type Object
	 */
	this.updaterArgs = args;
	/**
	 * Called before each request is performed
	 * @type Function
	 */
	this.onBeforeUpdate = args.onBeforeUpdate;
	/**
	 * @ignore
	 */
	this.timer = null;
	this.start();
};

/**
 * Starts the periodical updater
 * @type void
 */
AjaxPeriodicalUpdater.prototype.start = function() {
	this.updater = new AjaxUpdater(this.uri, this.updaterArgs);
	this.updater.bind('onComplete', this.onUpdate, this);
	this.onTimer();
};

/**
 * Stops the periodical updater
 * @type void
 */
AjaxPeriodicalUpdater.prototype.stop = function() {
	if (this.updater)
		this.updater.abort();
	clearTimeout(this.timer);
};

/**
 * Executes an iteration by calling AjaxUpdater.send
 * @type void
 * @private
 */
AjaxPeriodicalUpdater.prototype.onTimer = function() {
	if (this.onBeforeUpdate)
		this.onBeforeUpdate.apply(this);
	this.updater.send();
};

/**
 * Called when updater response is returned
 * @type void
 * @private
 */
AjaxPeriodicalUpdater.prototype.onUpdate = function() {
	this.timer = this.onTimer.bind(this).delay(this.frequency * 1000);
};

PHP2Go.included[PHP2Go.baseUrl + 'ajax.js'] = true;

}