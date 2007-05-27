if(!PHP2Go.included[PHP2Go.baseUrl+'ajax.js']){PHP2Go.include(PHP2Go.baseUrl+'form.js');PHP2Go.include(PHP2Go.baseUrl+'util/throbber.js');if(window.ActiveXObject&&!window.XMLHttpRequest){window.XMLHttpRequest=function(){var conn=null,ids=['MSXML2.XMLHTTP.5.0','MSXML2.XMLHTTP.4.0','MSXML2.XMLHTTP.3.0','MSXML2.XMLHTTP','Microsoft.XMLHTTP'];for(var i=0;i<ids.length;i++){try{conn=new ActiveXObject(ids[i]);break;}catch(e){};}return conn;};}Ajax=function(){this.conn=null;this.transId=PHP2Go.uid('ajax');this.listeners=[];};Ajax.getTransport=function(){var conn=null;try{conn=new XMLHttpRequest();}catch(e){}finally{if(!conn)PHP2Go.raiseException(Lang.ajaxSupport);return conn;}};Ajax.parseHeaders=function(str){var res={},pos,headers=str.split("\n");headers.walk(function(item,idx){if(item){pos=item.indexOf(':');if(pos!=-1)res[item.substring(0,pos).trim()]=item.substring(pos+1).trim();}});return res;};Ajax.lastModified={};Ajax.transactionCount=0;Ajax.prototype.bind=function(name,func,scope){this.listeners[name]=this.listeners[name]||[];this.listeners[name].push([func,scope||this]);};Ajax.prototype.raise=function(name,args){args=args||[];var listeners=this.listeners[name]||[];listeners.walk(function(item,idx){item[0].apply(item[1],args);});};AjaxRequest=function(url,args){this.Ajax();this.uri=url;this.method='POST';this.async=true;this.headers={'Accept':'text/javascript, text/html, application/xml, text/xml, application/json, */*'};this.contentType='application/x-www-form-urlencoded';this.encoding=null;this.ifModified=false;this.params={};this.form=null;this.formFields=[];this.formValidate=true;this.body=null;this.throbber=null;this.readArguments(args||{});};AjaxRequest.extend(Ajax,'Ajax');AjaxRequest.prototype.readArguments=function(args){for(var n in args){switch(n){case'onLoading':case'onLoaded':case'onInteractive':case'onComplete':case'onSuccess':case'onFailure':case'onJSONResult':case'onXMLResult':case'onException':this.bind(n,args[n],args.scope||null);break;case'headers':case'params':for(var pn in args[n])this[n][pn]=args[n][pn];break;case'async':case'ifModified':case'formValidate':this[n]=!!args[n];break;case'throbber':if((args.throbber.constructor||$EF)!=Throbber)args.throbber=new Throbber({element:args.throbber});this.throbber=args.throbber;break;default:this[n]=args[n];break;}}};AjaxRequest.prototype.addParam=function(param,val){this.params[param]=val;};AjaxRequest.prototype.send=function(){try{this.form=$(this.form);if(this.form&&this.formValidate){if(this.form.validator&&!this.form.validator.run())return;}var uri,body,queryStr=this.buildQueryString();if(this.method.equalsIgnoreCase('get')){uri=this.uri+(this.uri.match(/\?/)?'&'+queryStr:'?'+queryStr);body=null;}else{uri=this.uri;body=(this.body||queryStr);}Ajax.transactionCount++;if(this.throbber)this.throbber.show();this.conn=Ajax.getTransport();this.conn.open(this.method,uri,this.async);this.conn.onreadystatechange=this.onStateChange.bind(this);this.headers['X-Requested-With']='XMLHttpRequest';if(this.ifModified)this.headers['If-Modified-Since']=Ajax.lastModified[this.uri]||'Thu, 01 Jan 1970 00:00:00 GMT';if(this.method.equalsIgnoreCase('post')){this.headers['Content-Type']=this.contentType+(this.encoding?'; charset='+this.encoding:'');this.headers['Content-Length']=body.length;if(this.conn.overrideMimeType)this.headers['Connection']='close';}for(var name in this.headers)this.conn.setRequestHeader(name,this.headers[name]);this.conn.send(body);if(!this.async&&this.conn.overrideMimeType)this.onStateChange();}catch(e){this.raise('onException',[e]);}};AjaxRequest.prototype.abort=function(){if(this.conn&&this.conn.readyState>=1&&this.conn.readyState<4){this.conn.abort();this.release();}};AjaxRequest.prototype.onStateChange=function(){if(this.conn){switch(this.conn.readyState){case 0:break;case 1:this.raise('onLoading');break;case 2:this.raise('onLoaded');break;case 3:this.raise('onInteractive');break;case 4:if(this.throbber)this.throbber.hide();var resp=this.createResponse();this.raise('onComplete',[resp]);if(resp.success){if((resp.headers['Content-type']||'').match(/^(text|application)\/(x-)?(java|ecma)script(;.*)?$/i)){try{PHP2Go.eval(resp.responseText);this.raise('onSuccess',[resp]);}catch(e){this.raise('onException',[e]);}}else{this.raise('onSuccess',[resp]);if(resp.json)this.raise('onJSONResult',[resp]);if(resp.xmlRoot)this.raise('onXMLResult',[resp]);}}else{this.raise('onFailure',[resp]);}this.release();break;}}};AjaxRequest.prototype.buildQueryString=function(){var item,key,subKey,buf=[];if(this.form)buf.push(Form.serialize(this.form,this.formFields));for(key in this.params){item=this.params[key];if(item!=null){if(typeof(item)=='object'){if(item.length){for(var i=0;i<item.length;i++)buf.push(key.urlEncode()+"[]="+String(item[i]).urlEncode());}else{for(subKey in item)buf.push(key.urlEncode()+"["+subKey.urlEncode()+"]="+String(item[subKey]).urlEncode());}}else{buf.push(key.urlEncode()+"="+String(item).urlEncode());}}};return buf.join('&');};AjaxRequest.prototype.createResponse=function(){var resp=new AjaxResponse(this.transId);try{resp.status=this.conn.status;}catch(e){resp.status=13030;};switch(resp.status){case 12002:case 12029:case 12030:case 12031:case 12152:case 13030:resp.status=0;resp.statusText=Lang.commFailure;return resp;default:resp.statusText=this.conn.statusText;resp.headers=Ajax.parseHeaders(this.conn.getAllResponseHeaders());resp.responseText=this.conn.responseText;resp.responseXML=this.conn.responseXML;if(this.ifModified){try{Ajax.lastModified[this.uri]=resp.headers['Last-Modified'];}catch(e){}}try{if(resp.headers['X-JSON'])resp.json=eval('('+resp.headers['X-JSON']+')');if((resp.headers['Content-Type']||'').match(/^application\/json/i))resp.json=eval('('+resp.responseText+')');}catch(e){}if(resp.responseXML)resp.xmlRoot=resp.responseXML.documentElement;resp.success=((resp.status>=200&&resp.status<300)||resp.status==304);return resp;}};AjaxRequest.prototype.release=function(){this.conn.onreadystatechange=$EF;delete this.conn;Ajax.transactionCount--;};AjaxResponse=function(transId){this.transId=transId;this.status=null;this.statusText=null;this.headers={};this.responseText=null;this.responseXML=null;this.json=null;this.xmlRoot=null;this.success=false;};AjaxResponse.prototype.run=function(){var skip=0;var json=(this.json||{});var cmds=json.commands||[];for(var i=0;i<cmds.length;i++){var item=cmds[i];if(skip-->0)continue;switch(item.cmd){case'value':(item.frm)?($FF(item.frm,item.fld).setValue(item.arg)):($F(item.id).setValue(item.arg));break;case'combo':var combo=$F(item.id);(!item.arg.value)&&(item.arg.value=combo.getValue());(item.arg.clear)&&(combo.clearOptions());for(var v in item.arg.options)combo.addOption(v,item.arg.options[v]);combo.setValue(item.arg.value);break;case'enable':(item.frm)?($FF(item.frm,item.fld).enable()):($F(item.id).enable());break;case'disable':(item.frm)?($FF(item.frm,item.fld).disable()):($F(item.id).disable());break;case'focus':(item.frm)?($FF(item.frm,item.fld).focus()):($F(item.id).focus());break;case'clear':(item.frm)?($FF(item.frm,item.fld).clear()):($F(item.id).clear());break;case'reset':Form.reset($(item.id));break;case'hide':$(item.id).hide();break;case'show':$(item.id).show();break;case'prop':var elm=$(item.id);for(var p in item.arg)elm.setAttribute(p,item.arg[p]);break;case'style':var elm=$(item.id);for(var p in item.arg)elm.setStyle(p,item.arg[p]);break;case'create':var elm=$N(item.arg.tag,$(item.arg.parent),item.arg.styles,item.arg.cont);if(item.id)elm.id=item.id;for(var p in item.arg.attrs)elm.setAttribute(p,item.arg.attrs[p]);break;case'clear':$(item.id).clear();break;case'update':$(item.id).update(item.arg.code,item.arg.eval,item.arg.dom);break;case'insert':$(item.id).insertHTML(item.arg.code,item.arg.pos,item.arg.eval);break;case'replace':$(item.id).replace(item.arg.code,item.arg.eval);break;case'remove':$(item.id).remove();break;case'addev':Event.addListener($(item.id),item.arg.type,item.arg.func,item.arg.capt);break;case'remev':Event.removeListener($(item.id),item.arg.type,item.arg.func,item.arg.capt);break;case'alert':alert(item.arg);break;case'confirm':if(!confirm(item.arg.msg)){skip=item.arg.skip;if(!skip)break;}break;case'redirect':window.location.href=item.arg;return;case'eval':PHP2Go.eval(item.arg);break;case'func':item.id.apply(item.arg.scope,item.arg.params);break;}}};AjaxUpdater=function(url,args){args=args||{};this.AjaxRequest(url,args);this.success=null;this.failure=null;if(args.container){if(args.container.success){this.success=$(args.container.success);if(args.container.failure)this.failure=$(args.container.failure);}else{this.success=$(args.container);this.failure=$(args.container);}}this.insert=args.insert||null;this.noScripts=!!args.noScripts;this.bind('onSuccess',this.update);this.bind('onFailure',this.update);if(args.onUpdate)this.bind('onUpdate',args.onUpdate,args.scope||null);};AjaxUpdater.extend(AjaxRequest,'AjaxRequest');AjaxUpdater.prototype.update=function(response){var resp=response.responseText;if(this.noScripts)resp=resp.stripScripts();if(response.success){if(this.success){if(this.insert)this.success.insertHTML(resp,this.insert,true);else this.success.update(resp,true);if(this.success.getStyle('display')=='none')this.success.show();}}else{if(this.failure){if(this.insert)this.failure.insertHTML(resp,this.insert,true);else this.failure.update(resp,true);if(this.failure.getStyle('display')=='none')this.failure.show();}}this.raise('onUpdate',[response]);};AjaxService=function(url,args){this.AjaxRequest(url,Object.extend(args||{},{headers:{'X-Handler-ID':args.handler||''}}));this.bind('onJSONResult',this.parseResponse);};AjaxService.extend(AjaxRequest,'AjaxRequest');AjaxService.prototype.parseResponse=function(response){try{response.run();}catch(e){this.raise('onException',[e]);}};PHP2Go.included[PHP2Go.baseUrl+'ajax.js']=true;}