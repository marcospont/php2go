if(!PHP2Go.included[PHP2Go.baseUrl+'jsrsclient.js']){function jsrsBrowserSniff(){if(document.layers)return"NS";if(document.all){var agt=navigator.userAgent.toLowerCase();var is_opera=(agt.indexOf("opera")!=-1);var is_konq=(agt.indexOf("konqueror")!=-1);if(is_opera){return"OPR";}else{if(is_konq){return"KONQ";}else{return"IE";}}}if(document.getElementById)return"MOZ";return"OTHER";}var jsrsContextPoolSize=0;var jsrsContextMaxPool=10;var jsrsContextPool=new Array();var jsrsBrowser=jsrsBrowserSniff();var jsrsPOST=false;var containerName;function jsrsContextObj(contextID){this.id=contextID;this.busy=true;this.visible=false;this.url=null;this.func=null;this.params=null;this.callback=null;this.container=contextCreateContainer(contextID);this.GET=contextGET;this.POST=contextPOST;this.getPayload=contextGetPayload;this.setVisibility=contextSetVisibility;}function contextCreateContainer(containerName){var container;switch(jsrsBrowser){case'NS':container=new Layer(100);container.name=containerName;container.visibility='hidden';container.clip.width=100;container.clip.height=100;break;case'IE':document.body.insertAdjacentHTML("afterBegin",'<span id="SPAN'+containerName+'"></span>');var span=document.all("SPAN"+containerName);var html='<iframe name="'+containerName+'" src=""></iframe>';span.innerHTML=html;span.style.display='none';container=window.frames[containerName];break;case'MOZ':var span=document.createElement('SPAN');span.id="SPAN"+containerName;document.body.appendChild(span);var iframe=document.createElement('IFRAME');iframe.name=containerName;iframe.id=containerName;span.appendChild(iframe);container=iframe;break;case'OPR':var span=document.createElement('SPAN');span.id="SPAN"+containerName;document.body.appendChild(span);var iframe=document.createElement('IFRAME');iframe.name=containerName;iframe.id=containerName;span.appendChild(iframe);container=iframe;break;case'KONQ':var span=document.createElement('SPAN');span.id="SPAN"+containerName;document.body.appendChild(span);var iframe=document.createElement('IFRAME');iframe.name=containerName;iframe.id=containerName;span.appendChild(iframe);container=iframe;span.style.display=none;iframe.style.display=none;iframe.style.visibility=hidden;iframe.height=0;iframe.width=0;break;}return container;}function contextPOST(){var d=new Date();var unique=d.getTime()+''+Math.floor(1000*Math.random());var doc=(jsrsBrowser=="IE")?this.container.document:this.container.contentDocument;doc.open();doc.write('<html><body>');doc.write('<form name="jsrsForm" method="post" target="" ');doc.write(' action="'+this.url+'?U='+unique+'">');doc.write('<input type="hidden" name="C" value="'+this.id+'">');if(this.func!=null){doc.write('<input type="hidden" name="F" value="'+this.func+'">');if(this.params!=null){if(typeof(this.params)=="string"){doc.write('<input type="hidden" name="P0" '+'value="['+jsrsEscapeQQ(this.params)+']">');}else{for(var i=0;i<this.params.length;i++){doc.write('<input type="hidden" name="P'+i+'" '+'value="['+jsrsEscapeQQ(this.params[i])+']">');}}}}doc.write('<input type="hidden" name="D" value="'+(this.visible?'1':'0')+'">');doc.write('</form></body></html>');doc.close();doc.forms['jsrsForm'].submit();}function contextGET(){var URL=this.url;if(URL.indexOf('?')!=-1)URL+="&C="+this.id;else URL+="?C="+this.id;if(this.func!=null){URL+="&F="+escape(this.func);if(this.params!=null){if(typeof(this.params)=="string"){URL+="&P0=["+escape(this.params+'')+"]";}else{for(var i=0;i<this.params.length;i++){(this.params[i]==null)&&(this.params[i]='');URL+="&P"+i+"=["+escape(this.params[i]+'')+"]";}}}}URL+="&D="+(this.visible?'1':'0');var d=new Date();URL+="&U="+d.getTime();switch(jsrsBrowser){case'NS':this.container.src=URL;break;case'IE':this.container.document.location.replace(URL);break;case'MOZ':this.container.src='';this.container.src=URL;break;case'OPR':this.container.src='';this.container.src=URL;break;case'KONQ':this.container.src='';this.container.src=URL;break;}}function contextGetPayload(){switch(jsrsBrowser){case'NS':return this.container.document.forms['jsrs_Form'].elements['jsrs_Payload'].value;case'IE':return this.container.document.forms['jsrs_Form']['jsrs_Payload'].value;case'MOZ':return window.frames[this.container.name].document.forms['jsrs_Form']['jsrs_Payload'].value;case'OPR':var textElement=window.frames[this.container.name].document.getElementById("jsrs_Payload");case'KONQ':var textElement=window.frames[this.container.name].document.getElementById("jsrs_Payload");return textElement.value;}}function contextSetVisibility(vis){this.visible=(vis)?true:false;switch(jsrsBrowser){case'NS':this.container.visibility=(vis)?'show':'hidden';break;case'IE':document.all("SPAN"+this.id).style.display=(vis)?'':'none';break;case'MOZ':document.getElementById("SPAN"+this.id).style.visibility=(vis)?'':'hidden';case'OPR':document.getElementById("SPAN"+this.id).style.visibility=(vis)?'':'hidden';this.container.width=(vis)?250:0;this.container.height=(vis)?100:0;break;}}function jsrsGetContextID(){var contextObj;for(var i=1;i<=jsrsContextPoolSize;i++){contextObj=jsrsContextPool['jsrs'+i];if(!contextObj.busy){contextObj.busy=true;return contextObj.id;}}if(jsrsContextPoolSize<=jsrsContextMaxPool){var contextID="jsrs"+(jsrsContextPoolSize+1);jsrsContextPool[contextID]=new jsrsContextObj(contextID);jsrsContextPoolSize++;return contextID;}else{alert("jsrs Error:  context pool full");return null;}}function jsrsExecute(url,callback,func,params,visibility){var contextObj=jsrsContextPool[jsrsGetContextID()];contextObj.url=url;contextObj.func=func;contextObj.params=params;contextObj.callback=callback;contextObj.setVisibility((visibility==null?false:visibility));if(jsrsPOST&&((jsrsBrowser=='IE')||(jsrsBrowser=='MOZ'))){contextObj.POST();}else{contextObj.GET();}return contextObj.id;}function jsrsLoaded(contextID){var contextObj=jsrsContextPool[contextID];if(contextObj.callback!=null){contextObj.callback(jsrsUnescape(contextObj.getPayload()),contextObj);}contextObj.url=null;contextObj.func=null;contextObj.params=null;contextObj.callback=null;contextObj.busy=false;}function jsrsError(contextID,str){alert(unescape(str));var contextObj=jsrsContextPool[contextID];contextObj.busy=false;}function jsrsEscapeQQ(thing){(thing==null)&&(thing='');return thing.replace(/'"'/g,'\\"');}function jsrsUnescape(str){return str.replace(/\\\//g,"/");}function jsrsArrayFromString(s,delim){var d=(delim==null)?'~':delim;return s.split(d);}function jsrsDebugInfo(){var doc=window.open().document;doc.open;doc.write('Pool Size: '+jsrsContextPoolSize+'<br><font face="arial" size="2"><b>');for(var i in jsrsContextPool){var contextObj=jsrsContextPool[i];doc.write('<hr>'+contextObj.id+' : '+(contextObj.busy?'busy':'available')+'<br>');doc.write(contextObj.container.document.location.pathname+'<br>');doc.write(contextObj.container.document.location.search+'<br>');doc.write('<table border="1"><tr><td>'+contextObj.container.document.body.innerHTML+'</td></tr></table>');}doc.write('</table>');doc.close();return false;}PHP2Go.included[PHP2Go.baseUrl+'jsrsclient.js']=true;}