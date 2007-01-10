if(!PHP2Go.included[PHP2Go.baseUrl+'dom.js']){if(!window.Element){var Element={};}Element.extend=function(obj){var v,cache=function(v){return this[v]=this[v]||function(){var a=[this];for(var i=0;i<arguments.length;i++)a.push(arguments[i]);return v.apply(null,a);}};for(var p in Element){v=Element[p];if(typeof(v)=='function'&&p!='extend'&&!obj[p]){try{obj[p]=cache(v);}catch(e){}}}};Element.watch=function(elm,property,func){if(elm=$(elm)){var setter='_set_'+property;elm[setter]=func;if(elm.__defineSetter__){elm.__defineSetter__(property,function(val){elm[setter](val);});}else{elm.attachEvent('onpropertychange',function(){if(event.propertyName==property)event.srcElement[setter](event.srcElement[property]);});}}};Element.getElementsByTagName=function(elm,tag){if(elm=$(elm))return $C(elm.getElementsByTagName(tag||'*')).map($E);return[];};Element.getElementsByClassName=function(elm,clsName,tagName){if(elm=$(elm)){if(document.getElementsByXPath)return document.getElementsByXPath(".//*[contains(concat(' ', @class, ' '), ' "+clsName+" ')]",elm);var re=new RegExp("(^|\\s)"+clsName+"(\\s|$)");return $C(elm.getElementsByTagName(tagName||'*')).accept(function(item,idx){return(item.className&&re.test(item.className));}).map($E);}return[];};Element.getParentByTagName=function(elm,tag){if(elm=$(elm)){if(elm.nodeName.equalsIgnoreCase(tag))return elm;do{if(elm.nodeName.equalsIgnoreCase(tag))return $E(elm);}while((elm=elm.parentNode)!=null);return null;}};Element.isChildOf=function(elm,anc){elm=$(elm),anc=$(anc);if(elm&&anc){if(anc.contains&&!PHP2Go.browser.khtml){return anc.contains(elm);}else if(anc.compareDocumentPosition){return!!(anc.compareDocumentPosition(elm)&16);}else{var p=elm.parentNode;while(p){if(p==anc)return true;if(p.tagName.equalsIgnoreCase('html'))return false;p=p.parentNode||null;}return false;}}};Element.setParentNode=function(elm,par){elm=$(elm),par=$(par);if(elm&&par){if(elm.parentNode){if(elm.parentNode==par)return elm;elm.oldParent=elm.parentNode;elm=elm.parentNode.removeChild(elm);}elm=par.appendChild(elm);}return elm;};Element.insertAfter=function(elm,ref){if(elm=$(elm)){if(ref.nextSibling)ref.parentNode.insertBefore(elm,ref.nextSibling);else ref.parentNode.appendChild(elm);}return elm;};Element.getPosition=function(elm){var elm=$(elm),res={x:-1,y:-1};if(elm){if(document.getBoxObjectFor){var box=document.getBoxObjectFor(elm);var bl=parseInt(elm.getStyle('border-left-width'),10);var bt=parseInt(elm.getStyle('border-top-width'),10);res.x=box.x-(!isNaN(bl)?bl:0);res.y=box.y-(!isNaN(bt)?bt:0);}else{res.x=elm.offsetLeft||parseInt(elm.style.left.replace('px','')||'0',10);res.y=elm.offsetTop||parseInt(elm.style.top.replace('px','')||'0',10);var p=elm.offsetParent;while(p){res.x+=p.offsetLeft;res.y+=p.offsetTop;p=p.offsetParent;}if(PHP2Go.browser.opera||(PHP2Go.browser.khtml&&elm.getStyle('position')=='absolute')){res.x-=document.body.offsetLeft;res.y-=document.body.offsetTop;}}}return res;};Element.getDimensions=function(elm){var elm=$(elm),d={width:-1,height:-1};if(elm){if(elm.getStyle('display')!='none'){if(elm.offsetWidth&&elm.offsetHeight){d.width=elm.offsetWidth;d.height=elm.offsetHeight;}else if(elm.style&&elm.style.pixelWidth&&elm.style.pixelHeight){d.width=elm.style.pixelWidth;d.height=elm.style.pixelHeight;}}else{var s=elm.style;var old={visibility:s.visibility,position:s.position};s.visibility='hidden';s.position='absolute';s.display=(elm.tagName.equalsIgnoreCase('div')?'block':'');d.width=elm.clientWidth;d.height=elm.clientHeight;s.visibility=old.visibility;s.position=old.position;s.display='none';}}return d;};Element.isWithin=function(elm,p1,p2){if(elm=$(elm)){var p=Element.getPosition(elm),d=Element.getDimensions(elm);var ex1=p.x,ex2=(p.x+d.width),ey1=p.y,ey2=(p.y+d.height);return(ex1<p2.x&&ex2>p1.x&&ey1<p2.y&&ey2>p1.y);}return false;};Element.getStyle=function(elm,property){elm=$(elm),d=document,val=null;if(elm&&elm.style){var camel=property.camelize();val=elm.style[camel];if(!val){if(d.defaultView&&d.defaultView.getComputedStyle){var cs=d.defaultView.getComputedStyle(elm,null);(cs)&&(val=cs.getPropertyValue(property));}else if(elm.currentStyle){val=elm.currentStyle[camel];}}if(PHP2Go.browser.opera&&['left','top','right','bottom'].contains(property)&&Element.getStyle(elm,'position')=='static')val=null;(val=='auto')&&(val=null);}return val;};Element.setStyle=function(elm,prop,value){elm=$(elm),prop=prop.camelize();if(elm){switch(prop){case'opacity':Element.setOpacity(elm,value);break;default:elm.style[prop]=value;}}};Element.getOpacity=function(elm){if(elm=$(elm)){var op,re=new RegExp("alpha\(opacity=(.*)\)");if(op=elm.getStyle('opacity'))return parseFloat(op,10);if(op=re.exec(elm.getStyle('filter')||'')&&op[1])return(parseFloat(op[1],10)/100);return 1.0;}return null;};Element.setOpacity=function(elm,op){if(elm=$(elm)){op=(isNaN(op)||op>=1?null:(op<0.00001?0:op));var s=elm.style;s['opacity']=s['-moz-opacity']=s['-khtml-opacity']=op;if(PHP2Go.browser.ie){s['filter']=elm.getStyle('filter').replace(/alpha\([^\)]*\)/gi,'');s['filter']+=(op?'alpha(opacity='+Math.round(op*100)+')':'');}}};Element.classNames=function(elm){if(elm=$(elm)){if(!elm._classNames)elm._classNames=new CSSClasses(elm);return elm._classNames;}};Element.isVisible=function(elm){if(elm=$(elm))return(elm.style.display!='none');return false;};Element.show=function(){$C(arguments).map($).walk(function(item,idx){if(item){var newDisplay=(item.tagName&&item.tagName.match(/^div$/i)?'block':'');item.style.display=newDisplay;if(PHP2Go.browser.ie&&item.getStyle('position')=='absolute')WCH.attach(item);}});};Element.hide=function(){$C(arguments).map($).walk(function(item,idx){if(item){item.style.display='none';if(PHP2Go.browser.ie&&item.getStyle('position')=='absolute')WCH.detach(item);}});};Element.toggleDisplay=function(){$C(arguments).map($).walk(function(item,idx){if(item){if(item.getStyle('display')=='none')item.show();else item.hide();}});};Element.moveTo=function(elm,x,y){if(elm=$(elm)){elm.setStyle('left',x+'px');elm.setStyle('top',y+'px');if(PHP2Go.browser.ie&&elm.getStyle('position')=='absolute')WCH.update(elm);}};Element.resizeTo=function(elm,w,h){if(elm=$(elm)){elm.setStyle('width',w+'px');elm.setStyle('height',h+'px');if(PHP2Go.browser.ie&&elm.getStyle('position')=='absolute')WCH.update(elm);}};Element.clear=function(elm,useDom){elm=$(elm),useDom=!!useDom;if(elm){if(useDom){while(elm.firstChild)elm.removeChild(elm.firstChild);}else{elm.innerHTML='';}}};Element.isEmpty=function(elm){if(elm=$(elm)){return elm.innerHTML.match(/^\s*$/);}};Element.update=function(elm,code,evalScripts,useDom){elm=$(elm),code=String(code),evalScripts=!!evalScripts,useDom=!!useDom;if(elm){if(useDom){var div=document.createElement('div');div.innerHTML=code.stripScripts();while(elm.firstChild)elm.removeChild(elm.firstChild);while(div.firstChild)elm.appendChild(div.removeChild(div.firstChild));delete div;}else{elm.innerHTML=code.stripScripts();}(evalScripts)&&(code.evalScriptsDelayed());}};Element.insertHTML=function(elm,code,pos,evalScripts){elm=$(elm),evalScripts=!!evalScripts;var html=String(code).stripScripts();if(elm){if(elm.insertAdjacentHTML){var map={'before':'BeforeBegin','top':'AfterBegin','bottom':'BeforeEnd','after':'AfterEnd'};elm.insertAdjacentHTML(map[pos]||'BeforeEnd',html);}else{var fgm,rng=elm.ownerDocument.createRange();switch(pos){case'before':rng.setStartBefore(elm);fgm=rng.createContextualFragment(html);elm.parentNode.insertBefore(fgm,elm);break;case'top':rng.selectNodeContents(elm);rng.collapse(true);fgm=rng.createContextualFragment(html);elm.insertBefore(fgm,elm.firstChild);break;case'after':rng.setStartAfter(elm);fgm=rng.createContextualFragment(html);elm.parentNode.insertBefore(fgm,elm.nextSibling);break;default:rng.selectNodeContents(elm);rng.collapse(true);fgm=rng.createContextualFragment(html);elm.appendChild(fgm);break;}}(evalScripts)&&(code.evalScriptsDelayed());}};Element.replace=function(elm,code,evalScripts){elm=$(elm),evalScripts=!!evalScripts;var html=code.stripScripts();if(elm){if(elm.outerHTML){elm.outerHTML=html;}else{var rng=document.createRange();rng.selectNodeContents(elm);elm.parentNode.replaceChild(rng.createContextualFragment(html),elm);}(evalScripts)&&(code.evalScriptsDelayed());}};Element.remove=function(elm){elm=$(elm);if(elm&&elm.parentNode)return elm.parentNode.removeChild(elm);};if(!HTMLElement&&PHP2Go.browser.khtml){var HTMLElement={};HTMLElement.prototype=document.createElement('div').__proto__;}if(typeof(HTMLElement)!='undefined'){Element.extend(HTMLElement.prototype);PHP2Go.nativeElementExtension=true;}document.getElementsByClassName=function(clsName,tagName){return Element.getElementsByClassName(document,clsName,tagName);};if(document.evaluate){document.getElementsByXPath=function(expr,parent){var res=[],qry=document.evaluate(expr,$(parent)||document,null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null);for(var i=0,s=qry.snapshotLength;i<s;i++)res.push($E(qry.snapshotItem(i)));return res;};}CSSClasses=function(elm){this.elm=$(elm);this.each=function(iterator){var list=this.elm.className.trim().split(/\s+/);for(var i=0;i<list.length;i++)iterator(list[i]);};this.has=function(cls){var re=new RegExp("\s?"+cls+"\s?",'i');return re.test(this.elm.className);};this.set=function(clsNames){this.elm.className=clsNames;};this.add=function(){var a=arguments,c=this.elm.className;for(var i=0;i<a.length;i++)c=a[i]+' '+c.trim();this.set(c.trim());};this.remove=function(){var re,a=arguments,c=this.elm.className;for(var i=0;i<a.length;i++){re=new RegExp("^"+a[i]+"\\b\\s*|\\s*\\b"+a[i]+"\\b",'g');c=c.replace(re,'');}this.set(c.trim());};this.toggle=function(alt){(this.has(alt))?(this.remove(alt)):(this.add(alt));};this.toString=function(){return this.elm.className;};};Object.implement(CSSClasses.prototype,Collection);var WCH={attach:function(elm){elm=$(elm);if(PHP2Go.browser.ie5){var elms=[],list=[];var tags=['select','iframe','applet','object','embed'];var p=elm.getPosition(),d=elm.getDimensions();var p1={x:p.x,y:p.y};var p2={x:(p.x+d.width),y:(p.y+d.height)};for(var i=0;i<tags.length;i++){elms=document.getElementsByTagName(tags[i]);for(var j=0;j<elms.length;j++){if(Element.isWithin(elms[j],p1,p2)){elms[j].style.visibility="hidden";list.push(elms[j]);}}}elm.wchList=list;}else{var wch,pos=elm.getPosition(),dim=elm.getDimensions();if(!elm.wchIframe){wch=elm.wchIframe=$N('iframe',elm.parentNode,{position:'absolute'});if(PHP2Go.browser.ie6)wch.style.filter='progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0);';if(elm.getStyle('z-index')<=2)elm.setStyle('z-index',1000);}else{wch=elm.wchIframe;}wch.style.display='block';wch.style.width=dim.width;wch.style.height=dim.height;wch.style.top=pos.y;wch.style.left=pos.x;wch.style.zIndex=elm.getStyle('z-index')-1;}},detach:function(elm){if(elm.wchIframe)elm.wchIframe.style.display='none';if(elm.wchList){elm.wchList.walk(function(item,idx){item.style.visibility='visible';});elm.wchList=null;}},update:function(elm){if(elm.wchIframe||elm.wchList){if(elm.wchIframe){var pos=elm.getPosition();elm.wchIframe.style.left=pos.x;elm.wchIframe.style.top=pos.y;}else{this.detach(elm);this.attach(elm);}}else{this.attach(elm);}}};if(!window.Event){var Event={};if(!PHP2Go.nativeEventExtension)Event.prototype={};}Event.cache=[];Event.onDOMReady=function(fn){if(!this.queue){var b=PHP2Go.browser;var self=this,d=document;var run=function(){if(!arguments.callee.done){arguments.callee.done=true;if(self.timer){clearInterval(self.timer);self.timer=null;}self.queue.walk(function(item,idx){item();});self.queue=null;}};if(d.addEventListener)d.addEventListener('DOMContentLoaded',run,false);d.write("<scr"+"ipt id=defer_script defer src=javascript:void(0)><\/scr"+"ipt>");var defer=$('defer_script');if(defer){defer.onreadystatechange=function(){if(this.readyState=="complete")run();};defer.onreadystatechange();defer=null;}if(b.khtml||b.opera){this.timer=setInterval(function(){if(/loaded|complete/.test(document.readyState)){run();}},10);}this.addLoadListener(run);this.queue=[];}this.queue.push(fn);};Event.addLoadListener=function(fn){if(!document.body){Event.addListener(window,'load',fn);}else{var oldLoad=window.onload;if(typeof oldLoad!='function'){window.onload=fn;}else{window.onload=function(){oldLoad();fn();}}}};Event.addListener=function(elm,type,fn,capt){if(elm=$(elm)){type=type.replace('/^on/i','').toLowerCase();if(type=='keypress'&&PHP2Go.browser.khtml)type='keydown';capt=!!capt;if(elm.addEventListener)elm.addEventListener(type,fn,capt);else if(elm.attachEvent)elm.attachEvent('on'+type,fn);else elm['on'+type]=fn;Event.cache.push([elm,type,fn,capt]);}};Event.removeListener=function(elm,type,fn,capt){if(elm=$(elm)){type=type.replace('/^on/i','').toLowerCase();if(type=='keypress'&&PHP2Go.browser.khtml)type='keydown';capt=!!capt;if(elm.removeEventListener)elm.removeEventListener(type,fn,capt);else if(elm.detachEvent)elm.detachEvent('on'+type,fn);else elm['on'+type]=null;}};Event.flushCache=function(){Event.cache.walk(function(item,idx){Event.removeListener.apply(this,item);});delete Event.cache;try{window.onload=$EF;window.onunload=$EF;}catch(e){}};if(PHP2Go.browser.ie)Event.addListener(window,'unload',Event.flushCache);Event.prototype.element=function(){var elm=(this.target||this.srcElement);if(elm.nodeType){while(elm.nodeType!=1)elm=elm.parentNode;}return elm;};Event.prototype.findElement=function(tag){var elm=this.element();return(elm?Element.getParentByTagName(elm,tag):null);};Event.prototype.isRelated=function(elm){elm=$(elm);var related=this.relatedTarget;if(!related){if(this.type=='mouseout')related=this.toElement;else if(this.type=='mouseover')related=this.fromElement;}return(related&&(related==elm||$(related).isChildOf(elm)));};Event.prototype.key=function(){return this.keyCode||this.which;};Event.prototype.char=function(){return String.fromCharCode(this.keyCode||this.which).toLowerCase();};Event.prototype.position=function(){var b=document.body,e=document.documentElement;return{x:this.clientX+(b.scrollLeft?b.scrollLeft:e.scrollLeft),y:this.clientY+(b.scrollTop?b.scrollTop:e.scrollTop)};};if(!Event.prototype.preventDefault){Event.prototype.preventDefault=function(){this.returnValue=false;};}if(!Event.prototype.stopPropagation){Event.prototype.stopPropagation=function(){this.cancelBubble=true;};}Event.prototype.stop=function(){this.preventDefault();this.stopPropagation();};$EV=function(e){e=e||window.event;if(!e||PHP2Go.nativeEventExtension)return e;Object.implement(e,Event.prototype);return e;};$K=function(e){e=(e||window.event);return(e.keyCode||e.which);};PHP2Go.included[PHP2Go.baseUrl+'dom.js']=true;}