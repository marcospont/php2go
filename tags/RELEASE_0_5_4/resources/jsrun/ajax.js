if(!PHP2Go.included[PHP2Go.baseUrl+'ajax.js']){PHP2Go.include(PHP2Go.baseUrl+'util/throbber.js');if(window.ActiveXObject&&!window.XMLHttpRequest){window.XMLHttpRequest=function(){var conn=null,ids=['MSXML2.XMLHTTP.5.0','MSXML2.XMLHTTP.4.0','MSXML2.XMLHTTP.3.0','MSXML2.XMLHTTP','Microsoft.XMLHTTP'];for(var i=0;i<ids.length;i++){try{conn=new ActiveXObject(ids[i]);break;}catch(e){};}return conn;};}Ajax=function(){this.conn=null;this.transId=PHP2Go.uid('ajax');this.listeners=[];};Ajax.getTransport=function(){var conn=null;try{conn=new XMLHttpRequest();}catch(e){}finally{if(!conn)PHP2Go.raiseException(Lang.ajaxSupport);return conn;}};Ajax.parseHeaders=function(str){var res={},pos,headers=str.split("\n");headers.walk(function(item,idx){if(item){pos=item.indexOf(':');if(pos!=-1)res[item.substring(0,pos).trim()]=item.substring(pos+1).trim();}});return res;};Ajax.transactionCount=0;Ajax.prototype.bind=function(name,func,scope){this.listeners[name]=this.listeners[name]||[];this.listeners[name].push([func,scope||this]);};Ajax.prototype.raise=function(name,args){args=args||[];var listeners=this.listeners[name]||[];listeners.walk(function(item,idx){item[0].apply(item[1],args);});};AjaxRequest=function(url,args){this.Ajax();this.uri=url;this.method='POST';this.async=true;this.headers={'Accept':'text/javascript, text/html, application/xml, text/xml, application/json, */*'};this.contentType='application/x-www-form-urlencoded';this.encoding='iso-8859-1';this.params={};this.form=null;this.formFields=[];this.body=null;this.throbber=null;this.readArguments(args||{});};AjaxRequest.extend(Ajax,'Ajax');AjaxRequest.prototype.readArguments=function(args){var events=['onLoading','onLoaded','onInteractive','onComplete','onSuccess','onFailure','onJSONResult','onXMLResult','onException'];if(args.method)this.method=args.method;if(typeof(args.async)!='undefined')this.async=!!args.async;if(args.params){for(var param in args.params)this.params[param]=args.params[param];}if(args.headers){for(var name in args.headers)this.headers[name]=args.headers[name];}if(args.contentType)this.contentType=args.contentType;if(args.encoding)this.encoding=args.encoding;if(args.body)this.body=args.body;if(args.form)this.form=args.form;if(args.formFields)this.formFields=args.formFields;if(args.throbber){if((args.throbber.constructor||$EF)==Throbber)this.throbber=args.throbber;else this.throbber=new Throbber({element:args.throbber});}for(var i=0;i<events.length;i++){if(args[events[i]])this.bind(events[i],args[events[i]],args.scope||null);}};AjaxRequest.prototype.addParam=function(param,val){this.params[param]=val;};AjaxRequest.prototype.send=function(){this.form=$(this.form);if(this.form){if(this.form.validator&&!this.form.validator.run())return;}try{var queryStr=this.buildQueryString();var uri,body;if(this.method.equalsIgnoreCase('get')){uri=this.uri+(this.uri.match(/\?/)?'&'+queryStr:'?'+queryStr);body=null;}else{uri=this.uri;body=(this.body||queryStr);}Ajax.transactionCount++;if(this.throbber)this.throbber.show();this.conn=Ajax.getTransport();this.conn.open(this.method,uri,this.async);this.conn.onreadystatechange=this.onStateChange.bind(this);this.headers['Content-Type']=this.contentType+'; charset='+this.encoding;this.headers['X-Requested-With']='XMLHttpRequest';if(this.method.equalsIgnoreCase('post')){this.headers['Content-Length']=body.length;if(this.conn.overrideMimeType)this.headers['Connection']='close';}for(name in this.headers)this.conn.setRequestHeader(name,this.headers[name]);this.conn.send(body);if(!this.async&&this.conn.overrideMimeType)this.onStateChange();}catch(e){this.raise('onException',[e]);}};AjaxRequest.prototype.abort=function(){if(this.conn&&this.conn.readyState>=1&&this.conn.readyState<4){this.conn.abort();this.release();}};AjaxRequest.prototype.onStateChange=function(){if(this.conn){switch(this.conn.readyState){case 0:break;case 1:this.raise('onLoading');break;case 2:this.raise('onLoaded');break;case 3:this.raise('onInteractive');break;case 4:if(this.throbber)this.throbber.hide();var resp=this.createResponse();this.raise('onComplete',[resp]);if(resp.success){if((resp.headers['Content-type']||'').match(/^text\/javascript/i)){try{eval(resp.responseText);this.raise('onSuccess',[resp]);}catch(e){this.raise('onException',[e]);}}else{this.raise('onSuccess',[resp]);if(resp.json)this.raise('onJSONResult',[resp]);if(resp.xmlRoot)this.raise('onXMLResult',[resp]);}}else{this.raise('onFailure',[resp]);}this.release();break;}}};AjaxRequest.prototype.buildQueryString=function(){var item,key,subKey,buf=[];if(this.form)buf.push(Form.serialize(this.form,this.formFields));for(key in this.params){item=this.params[key];if(item!=null){if(typeof(item)=='object'){if(item.length){for(var i=0;i<item.length;i++)buf.push(key.urlEncode()+"[]="+String(item[i]).urlEncode());}else{for(subKey in item)buf.push(key.urlEncode()+"["+subKey.urlEncode()+"]="+String(item[subKey]).urlEncode());}}else{buf.push(key.urlEncode()+"="+String(item).urlEncode());}}};return buf.join('&');};AjaxRequest.prototype.createResponse=function(){var resp=new AjaxResponse(this.transId);try{resp.status=this.conn.status;}catch(e){resp.status=13030;};switch(resp.status){case 12002:case 12029:case 12030:case 12031:case 12152:case 13030:resp.status=0;resp.statusText=Lang.commFailure;return resp;default:resp.statusText=this.conn.statusText;resp.headers=Ajax.parseHeaders(this.conn.getAllResponseHeaders());resp.responseText=this.conn.responseText;resp.responseXML=this.conn.responseXML;try{if(resp.headers['X-JSON'])resp.json=eval('('+resp.headers['X-JSON']+')');if((resp.headers['Content-Type']||'').match(/^application\/json/i))resp.json=eval('('+resp.responseText+')');}catch(e){}if(resp.responseXML)resp.xmlRoot=resp.responseXML.documentElement;resp.success=(resp.status>=200&&resp.status<300);return resp;}};AjaxRequest.prototype.release=function(){this.conn.onreadystatechange=$EF;delete this.conn;Ajax.transactionCount--;};AjaxResponse=function(transId){this.transId=transId;this.status=null;this.statusText=null;this.headers={};this.responseText=null;this.responseXML=null;this.json=null;this.xmlRoot=null;this.success=false;};AjaxUpdater=function(url,args){args=args||{};this.AjaxRequest(url,args);this.success=null;this.failure=null;if(args.container){if(args.container.success){this.success=$(args.container.success);if(args.container.failure)this.failure=$(args.container.failure);}else{this.success=$(args.container);this.failure=$(args.container);}}this.insert=args.insert||null;this.noScripts=!!args.noScripts;this.bind('onSuccess',this.update);this.bind('onFailure',this.update);};AjaxUpdater.extend(AjaxRequest,'AjaxRequest');AjaxUpdater.prototype.update=function(response){var resp=response.responseText;if(this.noScripts)resp=resp.stripScripts();if(response.success){if(this.success){if(this.insert)this.success.insertHTML(resp,this.insert,true);else this.success.update(resp,true);this.success.show();}}else{if(this.failure){if(this.insert)this.failure.insertHTML(resp,this.insert,true);else this.failure.update(resp,true);this.failure.show();}}};PHP2Go.included[PHP2Go.baseUrl+'ajax.js']=true;}