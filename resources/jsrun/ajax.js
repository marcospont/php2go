if(!PHP2Go.included[PHP2Go.baseUrl+'ajax.js']){PHP2Go.include(PHP2Go.baseUrl+'form.js');PHP2Go.include(PHP2Go.baseUrl+'util/throbber.js');if(window.ActiveXObject&&!window.XMLHttpRequest){window.XMLHttpRequest=function(){var conn=null,ids=['MSXML2.XMLHTTP.5.0','MSXML2.XMLHTTP.4.0','MSXML2.XMLHTTP.3.0','MSXML2.XMLHTTP','Microsoft.XMLHTTP'];for(var i=0;i<ids.length;i++){try{conn=new ActiveXObject(ids[i]);break;}catch(e){};}return conn;};}Ajax=function(){this.conn=null;this.transId=PHP2Go.uid('ajax');this.listeners=[];};Ajax.getTransport=function(){var conn=null;try{conn=new XMLHttpRequest();}catch(e){}finally{if(!conn)PHP2Go.raiseException(Lang.ajaxSupport);return conn;}};Ajax.bind=function(name,func,scope,unshift){unshift=!!unshift;Ajax.listeners[name]=Ajax.listeners[name]||[];Ajax.listeners[name][unshift?'unshift':'push']([func,scope||null]);};Ajax.parseHeaders=function(str){var res={},pos,headers=str.split("\n");headers.walk(function(item,idx){if(item){pos=item.indexOf(':');if(pos!=-1)res[item.substring(0,pos).trim()]=item.substring(pos+1).trim();}});return res;};Ajax.lastModified={};Ajax.listeners={};Ajax.transactionCount=0;Ajax.prototype.bind=function(name,func,scope,unshift){unshift=!!unshift;this.listeners[name]=this.listeners[name]||[];this.listeners[name][unshift?'unshift':'push']([func,scope||null]);};Ajax.prototype.raise=function(name,args){args=args||[];var listeners=(Ajax.listeners[name]||[]).concat(this.listeners[name]||[]);for(var i=0;i<listeners.length;i++)listeners[i][0].apply(listeners[i][1]||this,args);};AjaxRequest=function(uri,args){this.Ajax();this.uri=uri;this.method='POST';this.async=true;this.headers={'Accept':'text/javascript, text/html, application/xml, text/xml, application/json, */*'};this.contentType='application/x-www-form-urlencoded';this.encoding=null;this.ifModified=false;this.params={};this.form=null;this.formFields=[];this.formUpload=false;this.formValidate=true;this.body=null;this.throbber=null;this.secureUri='javascript:false';this.readArguments(args||{});};AjaxRequest.extend(Ajax,'Ajax');AjaxRequest.prototype.readArguments=function(args){for(var n in args){switch(n){case'onLoading':case'onLoaded':case'onInteractive':case'onComplete':case'onSuccess':case'onFailure':case'onJSONResult':case'onXMLResult':case'onException':this.bind(n,args[n],args.scope||null);break;case'headers':case'params':for(var pn in args[n])this[n][pn]=args[n][pn];break;case'async':case'ifModified':case'formValidate':case'formUpload':this[n]=!!args[n];break;case'throbber':if((args.throbber.constructor||$EF)!=Throbber)args.throbber=new Throbber({element:args.throbber});this.throbber=args.throbber;break;case'throbberCentralize':if(this.throbber)this.throbber.centralize=!!args.throbberCentralize;default:this[n]=args[n];break;}}};AjaxRequest.prototype.addParam=function(param,val){this.params[param]=val;};AjaxRequest.prototype.send=function(){this.form=$(this.form);if(this.form&&this.formValidate){if(this.form.validator&&!this.form.validator.run())return;}Ajax.transactionCount++;if(this.throbber)this.throbber.show();if(this.form){var enctype=(this.form.getAttribute('enctype')||'');if(this.formUpload||enctype.equalsIgnoreCase('multipart/form-data')){this.doFormUpload();return;}}var uri,body,queryStr=this.buildQueryString();if(this.method.equalsIgnoreCase('get')){uri=this.uri+(this.uri.match(/\?/)?'&'+queryStr:'?'+queryStr);body=null;}else{uri=this.uri;body=(this.body||queryStr);}try{this.conn=Ajax.getTransport();this.conn.open(this.method,uri,this.async);this.conn.onreadystatechange=this.onStateChange.bind(this);this.headers['X-Requested-With']='XMLHttpRequest';if(this.ifModified)this.headers['If-Modified-Since']=Ajax.lastModified[this.uri]||'Thu, 01 Jan 1970 00:00:00 GMT';if(this.method.equalsIgnoreCase('post')){this.headers['Content-Type']=this.contentType+(this.encoding?'; charset='+this.encoding:'');this.headers['Content-Length']=body.length;if(this.conn.overrideMimeType)this.headers['Connection']='close';}for(var name in this.headers)this.conn.setRequestHeader(name,this.headers[name]);this.conn.send(body);if(!this.async&&this.conn.overrideMimeType)this.onStateChange();}catch(e){this.raise('onException',[e]);}};AjaxRequest.prototype.doFormUpload=function(){try{var id=PHP2Go.uid('ajaxFrame');if(PHP2Go.browser.ie){var ifr=document.createElement("<iframe id=\"%1\" name=\"%2\" />".assignAll(id,id));ifr.src=this.secureUri;}else{var ifr=document.createElement('iframe');ifr.id=id;ifr.name=id;}ifr.style.position='absolute';ifr.style.top='-1000px';ifr.style.left='-1000px';document.body.appendChild(ifr);var frm=this.form;frm.target=id;frm.method='post';frm.enctype=frm.encoding='multipart/form-data';frm.action=this.uri;var hp=[];this.params['X-Requested-With']='XMLHttpRequest';if(this.headers['X-Handler-ID'])this.params['X-Handler-ID']=this.headers['X-Handler-ID'];for(var n in this.params){var input=$N('input');input.type='hidden';input.name=n;input.id=PHP2Go.uid('ajaxField'+this.transId+'-');input.value=this.params[n];frm.appendChild(input);hp.push(input);}var ajax=this;var uploadCallback=function(e){ajax.conn={status:200,statusText:'OK',readyState:4,responseText:'',responseXML:null,abort:$EF,headers:{}};var doc=(PHP2Go.browser.ie?ifr.contentWindow.document:(ifr.contentDocument||window.frames[id].document));if(doc&&doc.body){var container=(doc.body.getElementsByTagName('textarea')||[]);ajax.conn.responseText=(container?container[0].value:doc.body.innerHTML);ajax.conn.headers['Content-Type']='text/html';ajax.conn.headers['Content-Length']=ajax.conn.responseText.length;if(!ajax.conn.responseText.match(/^\s*</)){try{var json=eval('('+ajax.conn.responseText+')');ajax.conn.json=json;ajax.conn.headers['Content-Type']='application/json';}catch(e){}}}if(doc&&doc.XMLDocument){ajax.conn.responseXML=doc.XMLDocument;ajax.conn.headers['Content-Type']='text/xml';}Event.removeListener(ifr,'load',uploadCallback);ajax.onStateChange();(function(){document.body.removeChild(ifr);}).delay(100);};frm.submit();Event.addListener(ifr,'load',uploadCallback);hp.walk(function(item,idx){frm.removeChild(item);});}catch(e){this.raise('onException',[e]);}};AjaxRequest.prototype.abort=function(){if(this.conn&&this.conn.readyState>=1&&this.conn.readyState<4){this.conn.abort();this.release();return true;}return false;};AjaxRequest.prototype.onStateChange=function(){if(this.conn){switch(this.conn.readyState){case 0:break;case 1:this.raise('onLoading');break;case 2:this.raise('onLoaded');break;case 3:this.raise('onInteractive');break;case 4:if(this.throbber)this.throbber.hide();var resp=this.createResponse();this.raise('onComplete',[resp]);if(resp.success){if((resp.headers['Content-type']||'').match(/^(text|application)\/(x-)?(java|ecma)script(;.*)?$/i)){try{PHP2Go.eval(resp.responseText);this.raise('onSuccess',[resp]);}catch(e){this.raise('onException',[e]);}}else{this.raise('onSuccess',[resp]);if(resp.json)this.raise('onJSONResult',[resp]);if(resp.xmlRoot)this.raise('onXMLResult',[resp]);}}else{this.raise('onFailure',[resp]);}this.release();break;}}};AjaxRequest.prototype.buildQueryString=function(){var item,key,subKey,buf=[];if(this.form)buf.push(Form.serialize(this.form,this.formFields));for(key in this.params){item=this.params[key];if(item!=null){if(typeof(item)=='object'){if(item.length){for(var i=0;i<item.length;i++)buf.push(key.urlEncode()+"[]="+String(item[i]).urlEncode());}else{for(subKey in item)buf.push(key.urlEncode()+"["+subKey.urlEncode()+"]="+String(item[subKey]).urlEncode());}}else{buf.push(key.urlEncode()+"="+String(item).urlEncode());}}};return buf.join('&');};AjaxRequest.prototype.createResponse=function(){var resp=new AjaxResponse(this.transId);try{resp.status=this.conn.status;}catch(e){resp.status=13030;};switch(resp.status){case 12002:case 12029:case 12030:case 12031:case 12152:case 13030:resp.status=0;resp.statusText=Lang.commFailure;return resp;default:resp.statusText=this.conn.statusText;resp.headers=(this.conn.headers||Ajax.parseHeaders(this.conn.getAllResponseHeaders()));resp.responseText=this.conn.responseText;resp.responseXML=this.conn.responseXML;if(this.ifModified&&resp.headers['Last-Modified'])Ajax.lastModified[this.uri]=resp.headers['Last-Modified'];try{if(this.conn.json){resp.json=this.conn.json;}else if((resp.headers['Content-Type']||'').match(/^application\/json/i)){resp.json=eval('('+resp.responseText+')');}else if(resp.headers['X-JSON']){resp.json=eval('('+resp.headers['X-JSON']+')');}}catch(e){}if(resp.responseXML)resp.xmlRoot=resp.responseXML.documentElement;resp.success=((resp.status>=200&&resp.status<300)||resp.status==304);return resp;}};AjaxRequest.prototype.release=function(){if(this.conn){this.conn.onreadystatechange=$EF;delete this.conn;Ajax.transactionCount--;}};AjaxResponse=function(transId){this.transId=transId;this.status=null;this.statusText=null;this.headers={};this.responseText=null;this.responseXML=null;this.json=null;this.xmlRoot=null;this.success=false;};AjaxResponse.prototype.run=function(){var skip=0;var json=(this.json||{});var cmds=json.commands||[];for(var i=0;i<cmds.length;i++){var item=cmds[i];if(skip-->0)continue;switch(item.cmd){case'value':(item.frm)?($FF(item.frm,item.fld).setValue(item.arg)):($F(item.id).setValue(item.arg));break;case'combo':var combo=$F(item.id);(!item.arg.value)&&(item.arg.value=combo.getValue());(item.arg.clear)&&(combo.clearOptions());for(var v in item.arg.options)combo.addOption(v,item.arg.options[v]);combo.setValue(item.arg.value);break;case'enable':var fld=null,elm=null;if(item.frm&&(fld=$FF(item.frm,item.fld))){fld.enable();}else if(fld=$F(item.id)){fld.enable();}else if(elm=$(item.id)){elm.disabled=false;}break;case'disable':var fld=null,elm=null;if(item.frm&&(fld=$FF(item.frm,item.fld))){fld.disable();}else if(fld=$F(item.id)){fld.disable();}else if(elm=$(item.id)){elm.disabled=true;}break;case'focus':(item.frm)?($FF(item.frm,item.fld).focus()):($F(item.id).focus());break;case'clear':if(item.frm){var fld=$FF(item.frm,item.fld);(fld)&&(fld.clear());}else{var fld=$F(item.id)||$(item.id);(fld)&&(fld.clear());}break;case'reset':Form.reset($(item.id));break;case'hide':$(item.id).hide();break;case'show':$(item.id).show();break;case'prop':var elm=$(item.id);for(var p in item.arg)elm.setAttribute(p,item.arg[p]);break;case'style':var elm=$(item.id);for(var p in item.arg)elm.setStyle(p,item.arg[p]);break;case'create':item.arg.attrs=item.arg.attrs||{};(item.id)&&(item.arg.attrs.id=id);$N(item.arg.tag,$(item.arg.parent),item.arg.styles,item.arg.cont,item.arg.attrs);break;case'clear':$(item.id).clear();break;case'update':$(item.id).update(item.arg.code,item.arg.eval,item.arg.dom);break;case'insert':$(item.id).insert(item.arg.code,item.arg.pos,item.arg.eval);break;case'replace':$(item.id).replace(item.arg.code,item.arg.eval);break;case'remove':$(item.id).remove();break;case'addev':Event.addListener($(item.id),item.arg.type,item.arg.func,item.arg.capt);break;case'remev':Event.removeListener($(item.id),item.arg.type,item.arg.func,item.arg.capt);break;case'alert':alert(item.arg);break;case'confirm':if(!confirm(item.arg.msg)){skip=item.arg.skip;if(!skip)break;}break;case'redirect':window.location.href=item.arg;return;case'eval':PHP2Go.eval(item.arg);break;case'func':if(item.arg.delay>0){setTimeout(function(){item.id.apply(item.arg.scope,item.arg.params);},item.arg.delay);}else{item.id.apply(item.arg.scope,item.arg.params);}break;}}};AjaxUpdater=function(uri,args){args=args||{};this.AjaxRequest(uri,args);this.success=null;this.failure=null;if(args.container){if(args.container.success){this.success=$(args.container.success);if(args.container.failure)this.failure=$(args.container.failure);}else{this.success=$(args.container);this.failure=$(args.container);}}this.insert=args.insert||null;this.noScripts=!!args.noScripts;this.bind('onSuccess',this.update);this.bind('onFailure',this.update);if(args.onUpdate)this.bind('onUpdate',args.onUpdate,args.scope||null);};AjaxUpdater.extend(AjaxRequest,'AjaxRequest');AjaxUpdater.prototype.update=function(response){var resp=response.responseText;if(this.noScripts)resp=resp.stripScripts();if(response.success){if(this.success){if(this.insert)this.success.insert(resp,this.insert,true);else this.success.update(resp,true);if(this.success.getStyle('display')=='none')this.success.show();}}else{if(this.failure){if(this.insert)this.failure.insert(resp,this.insert,true);else this.failure.update(resp,true);if(this.failure.getStyle('display')=='none')this.failure.show();}}this.raise('onUpdate',[response]);};AjaxService=function(uri,args){this.AjaxRequest(uri,args);this.bind('onJSONResult',this.parseResponse,null,true);this.setHandler(args.handler);};AjaxService.extend(AjaxRequest,'AjaxRequest');AjaxService.prototype.getHandler=function(){return this.headers['X-Handler-ID']||'';};AjaxService.prototype.setHandler=function(id){this.headers['X-Handler-ID']=id||'';};AjaxService.prototype.parseResponse=function(response){try{response.run();}catch(e){this.raise('onException',[e]);}};AjaxPeriodicalUpdater=function(uri,args){this.uri=uri;this.frequency=args.frequency||2;this.updater=null;this.updaterArgs=args;this.onBeforeUpdate=args.onBeforeUpdate;this.timer=null;this.start();};AjaxPeriodicalUpdater.prototype.start=function(){this.updater=new AjaxUpdater(this.uri,this.updaterArgs);this.updater.bind('onComplete',this.onUpdate,this);this.onTimer();};AjaxPeriodicalUpdater.prototype.stop=function(){if(this.updater)this.updater.abort();clearTimeout(this.timer);};AjaxPeriodicalUpdater.prototype.onTimer=function(){if(this.onBeforeUpdate)this.onBeforeUpdate.apply(this);this.updater.send();};AjaxPeriodicalUpdater.prototype.onUpdate=function(){this.timer=this.onTimer.bind(this).delay(this.frequency*1000);};PHP2Go.included[PHP2Go.baseUrl+'ajax.js']=true;}