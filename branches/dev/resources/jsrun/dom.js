if(!PHP2Go.included[PHP2Go.baseUrl+'dom.js']){if(!window.Element){var Element={};}Element.extend=function(obj){var v,cache=function(v){return this[v]=this[v]||function(){var a=[this];for(var i=0;i<arguments.length;i++)a.push(arguments[i]);return v.apply(null,a);}};for(var p in Element){v=Element[p];if(typeof(v)=='function'&&p!='extend'&&!obj[p]){try{obj[p]=cache(v);}catch(e){}}}};Element.watch=function(elm,property,func){if(elm=$(elm)){var setter='_set_'+property;elm[setter]=func;if(elm.__defineSetter__){elm.__defineSetter__(property,function(val){elm[setter](val);});}else{elm.attachEvent('onpropertychange',function(){if(event.propertyName==property)event.srcElement[setter](event.srcElement[property]);});}}};Element.getElementsByTagName=function(elm,tag){if(elm=$(elm))return $C(elm.getElementsByTagName(tag||'*')).map($E);return[];};Element.getElementsByClassName=function(elm,clsName,tagName){if(elm=$(elm)){if(document.getElementsByXPath)return document.getElementsByXPath(".//*[contains(concat(' ', @class, ' '), ' "+clsName+" ')]",elm);var re=new RegExp("(^|\\s)"+clsName+"(\\s|$)");var res=[],elms=elm.getElementsByTagName(tagName||'*');for(var i=0,s=elms.length;i<s;i++){if(elms[i].className&&re.test(elms[i].className))res.push($E(elms[i]));}return res;}return[];};Element.getParentByTagName=function(elm,tag){if(elm=$(elm)){if(elm.nodeName.equalsIgnoreCase(tag))return elm;do{if(elm.nodeName.equalsIgnoreCase(tag))return $E(elm);}while((elm=elm.parentNode)!=null);}return null;};Element.recursivelyCollect=function(elm,prop){var res=[];if(elm=$(elm)){while(elm=elm[prop]){if(elm.nodeType==1)res.push($E(elm));}}return res;};Element.recursivelySum=function(elm,prop){var res=0;if(elm=$(elm)){while(elm){if(elm.getComputedStyle('position')=='fixed')return 0;var val=elm[prop];if(val){res+=val-0;}if(elm==document.body)break;elm=elm.parentNode;}}return res;};Element.getParentNodes=function(elm){if(elm=$(elm))return elm.recursivelyCollect('parentNode');return[];};Element.getChildNodes=function(elm){var res=[];if(elm=$(elm)&&(elm=elm.firstChild)){while(elm&&elm.nodeType!=1)elm=elm.nextSibling;if(elm){elm=$E(elm);return[elm].concat(elm.getNextSiblings());}}return res;};Element.getPreviousSiblings=function(elm){if(elm=$(elm))return elm.recursivelyCollect('previousSibling');return[];};Element.getNextSiblings=function(elm){if(elm=$(elm))return elm.recursivelyCollect('nextSibling');return[];};Element.getSiblings=function(elm){if(elm=$(elm)){var rc=elm.recursivelyCollect;return rc('previousSibling').reverse.concat(rc('nextSibling'));}return[];};Element.isChildOf=function(elm,anc){elm=$(elm),anc=$(anc);if(elm&&anc){if(anc.contains&&!PHP2Go.browser.khtml){return anc.contains(elm);}else if(anc.compareDocumentPosition){return!!(anc.compareDocumentPosition(elm)&16);}else{var p=elm.parentNode;while(p){if(p==anc)return true;if(p.tagName.equalsIgnoreCase('html'))return false;p=p.parentNode||null;}}}return false;};Element.setParentNode=function(elm,par){elm=$(elm),par=$(par);if(elm&&par){if(elm.parentNode){if(elm.parentNode==par)return elm;elm.oldParent=elm.parentNode;elm=elm.parentNode.removeChild(elm);}elm=par.appendChild(elm);}return elm;};Element.hasAttribute=function(elm,attr){if(elm=$(elm)){if(elm.hasAttribute)return elm.hasAttribute(attr);var node=elm.getAttributeNode(attr);return(node&&node.specified);}return false;};Element.insertAfter=function(elm,ref){if(elm=$(elm)){if(ref.nextSibling)ref.parentNode.insertBefore(elm,ref.nextSibling);else ref.parentNode.appendChild(elm);}return elm;};Element.getPosition=function(elm){var elm=$(elm),p={x:0,y:0};if(elm){var b=PHP2Go.browser,db=document.body||document.documentElement;var nbt=2,tbt=0;if(elm.getBoundingClientRect){var bcr=elm.getBoundingClientRect();p.x=bcr.left-2;p.y=bcr.top-2;}else if(document.getBoxObjectFor){nbt=1;var box=document.getBoxObjectFor(elm);p.x=box.x-elm.recursivelySum('scrollLeft');p.y=box.y-elm.recursivelySum('scrollTop');}else if(elm.offsetParent){if(elm.parentNode!=db){p.x-=Element.recursivelySum((b.opera?db:elm),'scrollLeft');p.y-=Element.recursivelySum((b.opera?db:elm),'scrollTop');}var cur=elm,end=(b.safari&&elm.style.getPropertyValue('position')=='absolute'&&elm.parentNode==db?db:db.parentNode);do{var l=elm.offsetLeft;if(!b.opera||l>0)p.x+=(isNaN(l)?0:l);var t=elm.offsetTop;p.y+=(isNaN(t)?0:t);cur=cur.offsetParent;}while(cur!=end&&cur!=null);}else if(elm.x&&elm.y){p.x+=(isNaN(elm.x)?0:elm.x);p.y+=(isNan(elm.y)?0:elm.y);}var extents=['padding','border','margin'];if(nbt>tbt){for(var i=tbt;i<nbt;i++){p.x-=PHP2Go.toPixels(elm.getComputedStyle(extents[i]+'-left'));p.y-=PHP2Go.toPixels(elm.getComputedStyle(extents[i]+'-top'));}}else if(tbt>nbt){for(var i=tbt;i>nbt;--i){p.x-=PHP2Go.toPixels(elm.getComputedStyle(extents[i-1]+'-left'));p.y-=PHP2Go.toPixels(elm.getComputedStyle(extents[i-1]+'-top'));}}}return p;};Element.getDimensions=function(elm){var elm=$(elm),d={width:0,height:0};if(elm){if(elm.getComputedStyle('display')!='none'){if(elm.offsetWidth&&elm.offsetHeight){d.width=elm.offsetWidth;d.height=elm.offsetHeight;}else if(elm.style&&elm.style.pixelWidth&&elm.style.pixelHeight){d.width=elm.style.pixelWidth;d.height=elm.style.pixelHeight;}}else{elm.swapStyles({position:'absolute',visibility:'hidden',display:(elm.tagName.equalsIgnoreCase('div')?'block':'')},function(){d.width=elm.clientWidth;d.height=elm.clientHeight;});}}return d;};Element.isWithin=function(elm,p1,p2){if(elm=$(elm)){var p=Element.getPosition(elm),d=Element.getDimensions(elm);var ex1=p.x,ex2=(p.x+d.width),ey1=p.y,ey2=(p.y+d.height);return(ex1<p2.x&&ex2>p1.x&&ey1<p2.y&&ey2>p1.y);}return false;};if(PHP2Go.browser.ie){Element.getComputedStyle=function(elm,prop){elm=$(elm);if(elm&&elm.currentStyle)return elm.currentStyle[prop.camelize()];return null;};Element.getComputedStyles=function(elm){if(elm=$(elm))return elm.currentStyle;return null;};}else{Element.getComputedStyle=function(elm,prop){var d=document,cs,elm=$(elm);if(elm&&d.defaultView&&d.defaultView.getComputedStyle){if(cs=d.defaultView.getComputedStyle(elm,null))return cs[prop.camelize()];}return null;};Element.getComputedStyles=function(elm){var d=document,elm=$(elm);if(elm&&d.defaultView&&d.defaultView.getComputedStyle)return d.defaultView.getComputedStyle(elm,null);return null;};}Element.getStyle=function(elm,prop){var val=null,elm=$(elm);if(elm&&elm.style){var d=document,camel=prop.camelize();if(PHP2Go.browser.ie&&(prop=='float'||prop=='cssFloat'))camel='styleFloat';else if(prop=='float')camel='cssFloat';val=elm.style[camel];if(!val)val=elm.getComputedStyle(prop);if(PHP2Go.browser.opera&&['left','top','right','bottom'].contains(prop)&&elm.getComputedStyle('position')=='static')val=null;(val=='auto')&&(val=null);(prop=='opacity'&&val)&&(val=parseFloat(val,10));}return val;};Element.setStyle=function(elm,prop,value){if(elm=$(elm)){var props=prop;if(typeof(prop)=='string'){props={};props[prop]=value;}for(var prop in props){switch(prop){case'opacity':elm.setOpacity(props[prop]);break;case'width':case'height':elm.style[prop.camelize()]=props[prop];if(PHP2Go.browser.ie&&elm.getStyle('position')=='absolute'&&elm.getStyle('display')!='none')WCH.update(elm);break;default:elm.style[prop.camelize()]=props[prop];break;}}}return elm;};Element.swapStyles=function(elm,props,func){if(elm=$(elm)){for(var p in props){elm.style['old'+p]=elm.style[p];elm.style[p.camelize()]=props[p];}func();for(var p in props){elm.style[p.camelize()]=elm.style['old'+p];elm.style['old'+p]=null;}}return elm;};Element.getOpacity=function(elm){if(elm=$(elm)){var op=1;if(PHP2Go.browser.ie){var mt=[];if(mt=Element.getOpacity.re.exec(elm.getStyle('filter')||''))op=(parseFloat(mt[1],10)/100);}else{op=parseFloat(elm.style.opacity||elm.style.MozOpacity||elm.style.KhtmlOpacity||1,10);}return(op>=0.999999?1:op);}return null;};Element.getOpacity.re=/alpha\(opacity=(.*)\)/;Element.setOpacity=function(elm,op){if(elm=$(elm)){op=(isNaN(op)||op>=1?1:(op<0.00001?0:op));var s=elm.style,b=PHP2Go.browser;if(b.ie){s.zoom=1;s.filter=(elm.getStyle('filter')||'').replace(Element.setOpacity.re,'');s.filter+=(op?'alpha(opacity='+Math.round(op*100)+')':'');}else if(b.mozilla){s.opacity=s.MozOpacity=op;}else if(b.khtml){s.opacity=s.KhtmlOpacity=op;}else{s.opacity=op;}}return elm;};Element.setOpacity.re=/alpha\([^\)]*\)/gi;Element.classNames=function(elm){if(elm=$(elm)){if(!elm._classNames)elm._classNames=new CSSClasses(elm);return elm._classNames;}return null;};Element.addClass=function(elm,cl){if((elm=$(elm))&&cl){elm.removeClass(cl);elm.className=cl+' '+elm.className.trim();}};Element.removeClass=function(elm,cl){if((elm=$(elm))&&cl){var re=new RegExp("^"+cl+"\\b\\s*|\\s*\\b"+cl+"\\b",'g');elm.className=elm.className.replace(re,'');}};Element.isVisible=function(elm){if(elm=$(elm)){while(elm&&elm!=document){if(elm.style.display=='none'||elm.style.visibility=='hidden')return false;elm=elm.parentNode;}return true;}return false;};Element.show=function(){var item,arg=arguments;for(var i=0;i<arg.length;i++){item=$(arg[i]);item.style.display=(item.tagName&&item.tagName.equalsIgnoreCase('div')?'block':'');if(PHP2Go.browser.ie&&item.getComputedStyle('position')=='absolute')WCH.attach(item);}};Element.hide=function(){var item,arg=arguments;for(var i=0;i<arg.length;i++){item=$(arg[i]);item.style.display='none';if(PHP2Go.browser.ie&&item.getComputedStyle('position')=='absolute')WCH.detach(item);}};Element.toggleDisplay=function(){var item,arg=arguments;for(var i=0;i<arg.length;i++){item=$(arg[i]);if(item.getComputedStyle('display')=='none')item.show();else item.hide();}};Element.moveTo=function(elm,x,y){if(elm=$(elm)){elm.setStyle('left',x+'px');elm.setStyle('top',y+'px');if(PHP2Go.browser.ie&&elm.getComputedStyle('position')=='absolute')WCH.update(elm,{position:{x:x,y:y}});}return elm;};Element.resizeTo=function(elm,w,h){if(elm=$(elm)){elm.setStyle('width',w+'px');elm.setStyle('height',h+'px');if(PHP2Go.browser.ie&&elm.getComputedStyle('position')=='absolute')WCH.update(elm,{dimensions:{width:w,height:h}});}return elm;};Element.clear=function(elm,useDom){elm=$(elm),useDom=!!useDom;if(elm){if(useDom){while(elm.firstChild)elm.removeChild(elm.firstChild);}else{elm.innerHTML='';}}return elm;};Element.empty=function(elm){if(elm=$(elm))return elm.innerHTML.empty();return true;};Element.getInnerText=function(elm){if(elm=$(elm)){if(elm.innerText)return elm.innerText;var s='',cs=elm.childNodes;for(var i=0;i<cs.length;i++){switch(cs[i].nodeType){case 1:s+=Element.getInnerText(cs[i]);break;case 3:s+=cs[i].nodeValue;break;}}return s;}};Element.update=function(elm,code,evalScripts,useDom){elm=$(elm),code=String(code),evalScripts=!!evalScripts,useDom=!!useDom;if(elm){if(code.empty()){elm.clear(useDom);}else{var stripped=code.stripScripts();if(PHP2Go.browser.ie&&elm.tagName.match(/^(table|tbody|tr|td)$/i)){var depth,div=$N('div');switch(elm.tagName.toLowerCase()){case'table':div.innerHTML='<table>'+stripped+'</table>';depth=1;break;case'tbody':div.innerHTML='<table><tbody>'+stripped+'</tbody></table>';depth=2;break;case'tr':div.innerHTML='<table><tbody><tr>'+stripped+'</tr></tbody></table>';depth=3;break;case'td':div.innerHTML='<table><tbody><tr><td>'+stripped+'</td></tr></tbody></table>';depth=4;break;}while(elm.firstChild)elm.removeChild(elm.firstChild);for(var i=0;i<depth;i++)div=div.firstChild;for(var i=0;i<div.childNodes.length;i++)elm.appendChild(div.childNodes[i]);}else if(useDom){var div=$N('div',null,{},stripped);while(elm.firstChild)elm.removeChild(elm.firstChild);while(div.firstChild)elm.appendChild(div.removeChild(div.firstChild));delete div;}else{elm.innerHTML=stripped;}(evalScripts)&&(code.evalScriptsDelayed());}}return elm;};Element.insertHTML=function(elm,code,pos,evalScripts){elm=$(elm),evalScripts=!!evalScripts;var html=String(code).stripScripts();if(elm){if(elm.insertAdjacentHTML){var map={'before':'BeforeBegin','top':'AfterBegin','bottom':'BeforeEnd','after':'AfterEnd'};elm.insertAdjacentHTML(map[pos]||'BeforeEnd',html);}else{var fgm,rng=elm.ownerDocument.createRange();switch(pos){case'before':rng.setStartBefore(elm);fgm=rng.createContextualFragment(html);elm.parentNode.insertBefore(fgm,elm);break;case'top':rng.selectNodeContents(elm);rng.collapse(true);fgm=rng.createContextualFragment(html);elm.insertBefore(fgm,elm.firstChild);break;case'after':rng.setStartAfter(elm);fgm=rng.createContextualFragment(html);elm.parentNode.insertBefore(fgm,elm.nextSibling);break;default:rng.selectNodeContents(elm);rng.collapse(true);fgm=rng.createContextualFragment(html);elm.appendChild(fgm);break;}}(evalScripts)&&(code.evalScriptsDelayed());}return elm;};Element.replace=function(elm,code,evalScripts){elm=$(elm),evalScripts=!!evalScripts;var html=code.stripScripts();if(elm){if(elm.outerHTML){elm.outerHTML=html;}else{var rng=document.createRange();rng.selectNodeContents(elm);elm.parentNode.replaceChild(rng.createContextualFragment(html),elm);}(evalScripts)&&(code.evalScriptsDelayed());}};Element.remove=function(elm){elm=$(elm);if(elm&&elm.parentNode)return elm.parentNode.removeChild(elm);};if(!PHP2Go.nativeElementExtension&&document.createElement('div').__proto__){window.HTMLElement={};window.HTMLElement.prototype=document.createElement('div').__proto__;PHP2Go.nativeElementExtension=true;}if(PHP2Go.nativeElementExtension)Element.extend(HTMLElement.prototype);if(!document.getElementsByClassName){document.getElementsByClassName=function(clsName,tagName){return Element.getElementsByClassName(document,clsName,tagName);};}if(document.evaluate){document.getElementsByXPath=function(expr,parent){var res=[],qry=document.evaluate(expr,$(parent)||document,null,XPathResult.ORDERED_NODE_SNAPSHOT_TYPE,null);for(var i=0,s=qry.snapshotLength;i<s;i++)res.push($E(qry.snapshotItem(i)));return res;};}CSSClasses=function(elm){this.elm=$(elm);this.each=function(iterator){var list=this.elm.className.trim().split(/\s+/);for(var i=0;i<list.length;i++)iterator(list[i]);};this.has=function(cl){var re=new RegExp("\s?"+cl+"\s?",'i');return re.test(this.elm.className);};this.set=function(clsNames){this.elm.className=clsNames;};this.add=function(){var a=arguments,c=this.elm.className;for(var i=0;i<a.length;i++){(a[i])&&(c=a[i]+' '+c.trim());}this.set(c.trim());};this.remove=function(){var re,a=arguments,c=this.elm.className;for(var i=0;i<a.length;i++){if(a[i]){re=new RegExp("^"+a[i]+"\\b\\s*|\\s*\\b"+a[i]+"\\b",'g');c=c.replace(re,'');}}this.set(c.trim());};this.toggle=function(alt){(this.has(alt))?(this.remove(alt)):(this.add(alt));};this.toString=function(){return this.elm.className;};};Object.extend(CSSClasses.prototype,Collection);var WCH={attach:function(elm){elm=$(elm);if(PHP2Go.browser.ie5){var elms=[],list=[];var tags=['select','iframe','applet','object','embed'];var p=elm.getPosition(),d=elm.getDimensions();var p1={x:p.x,y:p.y};var p2={x:(p.x+d.width),y:(p.y+d.height)};for(var i=0;i<tags.length;i++){elms=document.getElementsByTagName(tags[i]);for(var j=0;j<elms.length;j++){if(Element.isWithin(elms[j],p1,p2)){elms[j].style.visibility="hidden";list.push(elms[j]);}}}elm.wchList=list;}else{var wch,pos=elm.getPosition(),dim=elm.getDimensions();if(!elm.wchIframe){wch=elm.wchIframe=$N('iframe',elm.parentNode,{position:'absolute'});if(PHP2Go.browser.ie6)wch.style.filter='progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0);';if(elm.getComputedStyle('z-index')<=2)elm.setStyle('z-index',1000);}else{wch=elm.wchIframe;}wch.style.display='block';wch.style.width=dim.width;wch.style.height=dim.height;wch.style.top=pos.y;wch.style.left=pos.x;wch.style.zIndex=elm.getComputedStyle('z-index')-1;}},detach:function(elm){if(elm.wchIframe)elm.wchIframe.style.display='none';if(elm.wchList){elm.wchList.walk(function(item,idx){item.style.visibility='visible';});elm.wchList=null;}},update:function(elm,opts){if(elm.wchIframe||elm.wchList){if(elm.wchIframe){opts=opts||{};var pos=opts.position||elm.getPosition();var dim=opts.dimensions||elm.getDimensions();elm.wchIframe.style.left=pos.x;elm.wchIframe.style.top=pos.y;elm.wchIframe.style.width=dim.width;elm.wchIframe.style.height=dim.height;}else{this.detach(elm);this.attach(elm);}}else{this.attach(elm);}}};if(!window.Event){var Event={};if(!PHP2Go.nativeEventExtension)Event.prototype={};}Event.cache=[];Event.onDOMReady=function(fn){if(!this.queue){var b=PHP2Go.browser;var self=this,d=document;var run=function(){if(!arguments.callee.done){arguments.callee.done=true;for(var i=0;i<self.queue.length;i++)self.queue[i]();self.queue=null;if(d.removeEventListener)d.removeEventListener('DOMContentLoaded',run,false);var defer=$('defer_script');if(defer)defer.remove();if(self.timer){clearInterval(self.timer);self.timer=null;}}};this.queue=[];if(d.addEventListener)d.addEventListener('DOMContentLoaded',run,false);d.write("<scr"+"ipt id=defer_script defer src=javascript:void(0)><\/scr"+"ipt>");var defer=$('defer_script');if(defer){defer.onreadystatechange=function(){if(this.readyState=="complete")run();};defer.onreadystatechange();defer=null;}if(b.khtml||b.opera){this.timer=setInterval(function(){if(/loaded|complete/.test(document.readyState)){run();}},10);}this.addLoadListener(run);}this.queue.push(fn);};Event.addLoadListener=function(fn){if(!document.body){Event.addListener(window,'load',fn);}else{var oldLoad=window.onload;if(typeof oldLoad!='function'){window.onload=fn;}else{window.onload=function(){oldLoad();fn();}}}};Event.addListener=function(elm,type,fn,capt){if(elm=$(elm)){type=type.replace('/^on/i','').toLowerCase();if(type=='keypress'&&PHP2Go.browser.khtml)type='keydown';capt=!!capt;if(elm.addEventListener)elm.addEventListener(type,fn,capt);else if(elm.attachEvent)elm.attachEvent('on'+type,fn);else elm['on'+type]=fn;Event.cache.push([elm,type,fn,capt]);}};Event.removeListener=function(elm,type,fn,capt){if(elm=$(elm)){type=type.replace('/^on/i','').toLowerCase();if(type=='keypress'&&PHP2Go.browser.khtml)type='keydown';capt=!!capt;if(elm.removeEventListener)elm.removeEventListener(type,fn,capt);else if(elm.detachEvent)elm.detachEvent('on'+type,fn);else elm['on'+type]=null;}};Event.flushCache=function(){Event.cache.walk(function(item,idx){Event.removeListener.apply(this,item);});delete Event.cache;try{window.onload=$EF;window.onunload=$EF;}catch(e){}};if(PHP2Go.browser.ie)Event.addListener(window,'unload',Event.flushCache);Event.prototype.element=function(){var elm=(this.target||this.srcElement);if(elm.nodeType){while(elm.nodeType!=1)elm=elm.parentNode;}return elm;};Event.prototype.findElement=function(tag){var elm=this.element();return(elm?Element.getParentByTagName(elm,tag):null);};Event.prototype.isRelated=function(elm){elm=$(elm);var related=this.relatedTarget;if(!related){if(this.type=='mouseout')related=this.toElement;else if(this.type=='mouseover')related=this.fromElement;}return(related&&(related==elm||$(related).isChildOf(elm)));};Event.prototype.key=function(){return this.keyCode||this.which;};Event.prototype.char=function(){return String.fromCharCode(this.keyCode||this.which).toLowerCase();};Event.prototype.position=function(){var e=document.documentElement||document.body;return{x:this.pageX||(this.clientX+e.scrollLeft),y:this.pageY||(this.clientY+e.scrollTop)};};if(!Event.prototype.preventDefault){Event.prototype.preventDefault=function(){this.returnValue=false;};}if(!Event.prototype.stopPropagation){Event.prototype.stopPropagation=function(){this.cancelBubble=true;};}Event.prototype.stop=function(){this.preventDefault();this.stopPropagation();};Event.addLoadListener(function(){for(var i=0;i<Widget.widgets.length;i++){var attrs=Widget.widgets[i];var widget=new window[attrs[0]](attrs[1],attrs[2]);widget.setup();}});$EV=function(e){e=e||window.event;if(!e||PHP2Go.nativeEventExtension)return e;return Object.extend(e,Event.prototype);};$K=function(e){e=(e||window.event);return(e.keyCode||e.which);};PHP2Go.included[PHP2Go.baseUrl+'dom.js']=true;}