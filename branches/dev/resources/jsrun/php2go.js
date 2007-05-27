var PHP2Go={baseUrl:null,locale:null,included:{},loaded:false,uidCache:[0,{}],scriptRegExpAll:new RegExp('(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)','img'),scriptRegExpOne:new RegExp('(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)','im'),nativeElementExtension:!!window.HTMLElement,def:function(){var i,a=arguments;for(i=0;i<a.length;i++){if(typeof(a[i])!=''&&typeof(a[i])!='undefined')return false;}return true;},ifUndef:function(obj,def){return(typeof(obj)!='undefined'?obj:def);},include:function(lib,charset){if(!this.included[lib])document.write("<script type=\"text/javascript\""+(charset?" charset=\""+charset+"\"":"")+" src=\""+lib+"\"></script>");},load:function(){if(this.loaded)return;var mt,scripts=document.getElementsByTagName('script');for(var i=0;i<scripts.length;i++){if(scripts[i].src){this.included[scripts[i].src]=true;mt=scripts[i].src.match(/(.*)php2go\.js(\?locale=([^&]+)(&charset=(.*))?)?/);if(mt){this.baseUrl=mt[1];break;}}else{mt=[];}}if(!mt[3])PHP2Go.raiseException("PHP2Go Javascript Framework needs a locale parameter: php2go.js?locale=locale_code");this.locale=mt[3];this.include(this.baseUrl+'structures.js');this.include(this.baseUrl+'dom.js');this.include(this.baseUrl+'compat.js');this.include(this.baseUrl+'lang.php?locale='+mt[3],(mt[5]?mt[5]:null));this.loaded=true;},raiseException:function(msg,name){if(typeof(Error)=='function'){var e=new Error(msg);if(!e.message)e.message=expr;if(name)e.name=name;throw e;}else if(typeof(msg)=='string'){throw msg;}},uid:function(pfx){var c=PHP2Go.uidCache;if(pfx){c[1][pfx]=(c[1][pfx]||0)+1;return pfx+String(c[1][pfx]);}else{return String(++c[0]);}},browser:function(){var bw={},ua=navigator.userAgent.toLowerCase();bw.ie=/msie/i.test(ua)&&!/opera/i.test(ua);bw.ie7=bw.ie&&/msie 7/i.test(ua);bw.ie6=bw.ie&&/msie 6/i.test(ua);bw.ie5=bw.ie&&/msie 5\.0/i.test(ua);bw.opera=/opera/i.test(ua);bw.khtml=/konqueror|safari|webkit|khtml/i.test(ua);bw.safari=/safari|webkit/i.test(ua);bw.mozilla=!bw.ie&&!bw.opera&&!bw.khtml&&/mozilla/i.test(ua);bw.gecko=/gecko/i.test(ua);bw.windows=bw.linux=bw.mac=bw.unix=false;bw.os=(/windows/i.test(ua)?'windows':(/linux/i.test(ua)?'linux':(/mac/i.test(ua)?'mac':(/unix/i.test(ua)?'unix':'unknown'))));(bw.os!='unknown')&&(bw[bw.os]=true);return bw;}(),compare:function(a,b,op,type){type=type||'STRING';switch(type){case'INTEGER':a=parseInt(a,10),b=parseInt(b,10);break;case'FLOAT':a=parseFloat(a,10),b=parseFloat(b,10);break;case'CURRENCY':a=parseFloat(a,10),b=parseFloat(b,10);break;case'DATE':a=Date.toDays(a),b=Date.toDays(b);break;}switch(op){case'EQ':return(a==b);case'NEQ':return(a!=b);case'LT':return(a<b);case'LOET':return(a<=b);case'GT':return(a>b);case'GOET':return(a>=b);default:return false;}},eval:function(str){if(window.execScript)window.execScript(str);else if(this.browser.safari)window.setTimeout(str,0);else eval.call(window,str);}};try{PHP2Go.nativeEventExtension=(typeof(Event.prototype)!='undefined');}catch(e){PHP2Go.nativeEventExtension=false;}Object.extend=function(dst,src){for(p in src)dst[p]=src[p];return dst;};Object.serialize=function(obj){if(typeof(obj)=='undefined')return;if(typeof(obj)=='function')return;if(typeof(obj)=='boolean')return obj.toString();if(obj===null)return'null';if(obj.serialize)return obj.serialize();if(obj===window||obj===document)return;var buf=[];for(var p in obj){try{if(obj[p]=='')continue;if(obj[p]&&obj[p].ownerDocument===document)continue;var v=Object.serialize(obj[p]);if(typeof(v)!='undefined')buf.push(p+":"+v);}catch(e){}}return'{'+buf.join(', ')+'}';};if(!Function.prototype.apply){Function.prototype.apply=function(obj,args){var argStr=[],res=null;(!obj)&&(obj=window);(!args)&&(args=[]);for(var i=0;i<args.length;i++)argStr[i]='args['+i+']';obj.__f__=this;res=eval('obj.__f__('+argStr.join(',')+')');obj.__f__=null;return res;};}Function.prototype.bind=function(obj){var self=this;return function(){self.apply(obj,arguments);}};Function.prototype.extend=function(parent,propName){if(typeof(parent)=='function'){var f=function(){};f.prototype=parent.prototype;this.prototype=new f();this.prototype.constructor=this;this.superclass=parent.prototype;if(propName)this.prototype[propName]=parent.prototype.constructor;if(parent.prototype.constructor==Object.prototype.constructor)parent.prototype.constructor=parent;}};String.prototype.trim=function(){return this.replace(/^\s*/,"").replace(/\s*$/,"");};String.prototype.empty=function(){return/^\s*$/.test(this);};String.prototype.find=function(str){return(this.indexOf(str)!=-1);};String.prototype.wrap=function(l,r){return(l||'')+this+(r||l||'');};String.prototype.cut=function(p1,p2){p2=PHP2Go.ifUndef(p2,this.length);return this.substr(0,p1)+this.substr(p2);};String.prototype.insert=function(val,at){at=PHP2Go.ifUndef(at,0);return this.substr(0,at)+val+this.substr(at);};String.prototype.splice=function(offset,len,replace){return this.cut(offset,offset+len).insert(replace,offset);};String.prototype.repeat=function(n){var res='',n=(n||0);while(n--)res+=this;return res;};String.prototype.pad=function(pad,len,type){if(len<0||len<=this.length)return this;pad=(pad?pad.charAt(0):' ');type=type||'left';if(type=='left')return pad.repeat(len-this.length)+this;else if(type=='right')return this+pad.repeat(len-this.length);else return pad.repeat(Math.ceil((len-this.length)/2))+this+pad.repeat(Math.floor((len-this.length)/2));};String.prototype.equalsIgnoreCase=function(str){return this.toLowerCase()==String(str).toLowerCase();};String.prototype.urlEncode=function(){if(window.encodeURIComponent)return encodeURIComponent(this);return escape(this);};String.prototype.camelize=function(){var res,tmp=this.split('-');if(tmp.length==1)return tmp[0];res=(this.indexOf('-')==0?tmp[0].charAt(0).toUpperCase()+tmp[0].substring(1):tmp[0]);for(var i=1;i<tmp.length;i++)res+=tmp[i].charAt(0).toUpperCase()+tmp[i].substring(1);return res;};String.prototype.capitalize=function(){var wl=this.split(/\s+/g);return $C(wl).map(function(w,idx){return w.charAt(0).toUpperCase()+w.substr(1).toLowerCase();}).join(' ');};String.prototype.escapeHTML=function(){var d=document;var dv=d.createElement('div');dv.appendChild(d.createTextNode(this));return dv.innerHTML;};String.prototype.stripSpaces=function(){return this.replace(/\s+/g,' ');};String.prototype.stripTags=function(){return this.replace(/<\/?[^>]+>/gi,'');};String.prototype.stripScripts=function(){try{return this.replace(PHP2Go.scriptRegExpAll,'');}catch(e){var self=this,sp=this.indexOf("<script"),ep=0;while(sp!=-1){ep=self.substr(sp).indexOf("</script>");if(ep!=-1){self=(sp?self.substr(0,sp-1):'')+self.substr(sp).substr(ep+9);sp=self.indexOf("<script");}else{sp=-1;}}return self;}};String.prototype.evalScripts=function(){try{var ra=PHP2Go.scriptRegExpAll;var ro=PHP2Go.scriptRegExpOne;var matches=this.match(ra)||[];return $C(matches).map(function(item){var match=item.match(ro);if(match)PHP2Go.eval(match[1]);});}catch(e){var tmp,self=this,sp=this.indexOf("<script"),ep1=0,ep2=0;while(sp!=-1){tmp=self.substr(sp);ep1=tmp.indexOf(">");ep2=tmp.indexOf("</script>");if(ep1!=-1&&ep2!=-1){PHP2Go.eval(tmp.substr(ep1+1,ep2-ep1-1));self=(sp?self.substr(0,sp-1):'')+tmp.substr(ep2+9);sp=self.indexOf("<script");}else{sp=-1;}}return self;}};String.prototype.evalScriptsDelayed=function(t){var self=this,t=(t||10);if(PHP2Go.browser.ie5){window.timeoutArg=self;setTimeout("window.timeoutArg.evalScripts();window.timeoutArg=null;",t);}else{setTimeout(function(){self.evalScripts();},t);}};String.prototype.assignAll=function(){var a=$A(arguments),self=this;a.walk(function(item,idx){self=self.replace('%'+(idx+1),item);});return self;};String.prototype.serialize=function(){return"'"+this.replace('\\','\\\\').replace("'",'\\\'')+"'";};Date.fromString=function(str){var mt,d,m,y,dt=new Date();if(mt=str.match(new RegExp("^([0-9]{1,2})(\/|\.|\-)([0-9]{1,2})(\/|\.|\-)([0-9]{4})(?: ([0-9]{2})\:([0-9]{2})\:?([0-9]{2})?)?"))){dt.setDate(parseInt(mt[1],10));dt.setMonth(parseInt(mt[3],10)-1);dt.setYear(mt[5]);(mt[6])&&(dt.setHours(parseInt(mt[6],10)));(mt[7])&&(dt.setMinutes(parseInt(mt[7],10)));(mt[8])&&(dt.setSeconds(parseInt(mt[8],10)));return dt;}else if(mt=str.match(new RegExp("^([0-9]{4})(?:\/|\.|\-)([0-9]{1,2})(?:\/|\.|\-)([0-9]{1,2})(?: ([0-9]{2})\:([0-9]{2})\:?([0-9]{2})?)?"))){dt.setDate(parseInt(mt[5],10));dt.setMonth(parseInt(mt[3],10)-1);dt.setYear(mt[1]);(mt[6])&&(dt.setHours(parseInt(mt[6],10)));(mt[7])&&(dt.setMinutes(parseInt(mt[7],10)));(mt[8])&&(dt.setSeconds(parseInt(mt[8],10)));return dt;}else{return dt;}};Date.toDays=function(date){var d,m,y,c;if(mt=date.match(new RegExp("^([0-9]{1,2})(\/|\.|\-)([0-9]{1,2})(\/|\.|\-)([0-9]{4})"))){d=parseInt(mt[1],10),m=parseInt(mt[3],10),y=mt[5];}else if(mt=date.match(new RegExp("^([0-9]{4})(?:\/|\.|\-)([0-9]{1,2})(?:\/|\.|\-)([0-9]{1,2})"))){d=parseInt(m[5],10),m=parseInt(mt[3],10),y=mt[1];}else{return 0;}c=parseInt(y.substring(0,2));y=y.substring(2);if(m>2){m-=3;}else{m+=9;if(y){y--;}else{y=99;c--;}}return(Math.floor((146097*c)/4)+Math.floor((1461*y)/4)+Math.floor((153*m+2)/5)+d+1721119);};Math.truncate=function(num,prec){(isNaN(prec))&&(prec=0);return(Math.round(num*Math.pow(10,prec))/Math.pow(10,prec));};if(typeof(window.isFinite)!='function'){window.isFinite=function(number){return(!isNaN(number)&&(number<=Math.POSITIVE_INFINITY||number>=Math.NEGATIVE_INFINITY));}}Number.prototype.toPaddedString=function(len,base){return this.toString(base||10).pad('0',len);};Number.prototype.serialize=function(){return(isFinite(this)?this.toString():'null');};var Cookie={get:function(name){var re=new RegExp("(^|;)\\s*"+escape(name)+"=([^;]+)");var res=document.cookie.match(re);if(res&&res[2])return unescape(res[2]);return null;},getAll:function(){var res=$H(),ck=document.cookie.split(';');var rn=new RegExp("^\s*([^=]+)"),rv=new RegExp("=(.*$)");for(var i=0;i<ck.length;i++){try{res.set(unescape(ck[i].match(rn)[1]),unescape(ck[i].match(rv)[1]));}catch(e){}}return res;},set:function(name,value,exp,path,domain,sec){name=escape(name),value=escape(value);exp=(exp&&!isNaN(exp)?parseInt(exp,10):0);var ck=name+"="+value;if(exp){var date=new Date();date.setTime(date.getTime()+(1000*parseInt(exp,10)));ck+=';expires='+date.toGMTString();}if(path)ck+=';path='+path;if(domain)ck+=';domain='+domain;if(!!sec)ck+=';secure';document.cookie=ck;},remove:function(name,path,domain){this.set(name,"",-3600,path,domain);},buildLifeTime:function(d,h,m,w){return((!isNaN(d)?(d*24*60*60):0)+(!isNaN(h)?(h*60*60):0)+(!isNaN(m)?(m*60):0)+(!isNaN(w)?(w*7*24*60*60):0));}};var Logger={initialize:function(){this.expanded=false;this.container=$N('div',document.body,{position:'absolute',left:'0px',top:'0px',width:'100px',height:'20px',textAlign:'left',fontFamily:'Courier',fontSize:'10px',color:'white',zIndex:2000 });var self=this;this.top=$N('div',this.container,{height:'20px'},"<button type='button' style='width:50px;height:20px;background-color:ButtonFace;border:1px solid black;padding:0;margin:1px'>toggle</button><button type='button' style='width:40px;height:20px;background-color:ButtonFace;border:1px solid black;padding:0;margin:1px'>clear</button>");this.top.firstChild.onclick=function(){var c=self.container,o=self.output;var e=self.expanded=!self.expanded;c.style.width=(e?'100%':'100px');c.style.height=(e?'200px':'20px');o.toggleDisplay();if(e&&o.lastChild)o.lastChild.scrollIntoView(false);};this.top.firstChild.nextSibling.onclick=function(){self.output.clear();};this.output=$N('div',this.container,{width:'98%',height:'160px',display:'none',position:'absolute',overflow:'auto',backgroundColor:'white',margin:'5px',padding:'4px',border:'1px solid black'});},log:function(text,color){(!this.container)&&(this.initialize());(typeof(text)!='string')&&(text=Object.serialize(text));this.output.insertHTML("<pre style='padding:0;margin:0;color:"+(color||'white')+"'>"+String(text).escapeHTML()+"</pre>","bottom");},info:function(text){this.log(text,'blue');},debug:function(text){this.log(text,'green');},warn:function(text){this.log(text,'orange');},error:function(text){this.log(text,'red');},exception:function(e){var info='['+e.name+'] - '+e.message;if(e.stack){info+="\n"+e.stack.split("\n").filter(function(item,idx){var where=item.split('@');if(where[1]&&where[1]!=':0')return'at '+where[1];}).join("\n");}this.log(info,'red');}};var Window={open:function(url,wid,hei,x,y,type,tit,ret){wid=wid||screen.width,hei=hei||screen.availHeight;x=Math.abs(x),y=Math.abs(y);type=PHP2Go.ifUndef(type,255),ret=!!ret;var props=(type&1?'toolbar,':'')+(type&2?'location,':'')+(type&4?'directories,':'')+(type&8?'status,':'')+(type&16?'menubar,':'')+(type&32?'scrollbars,':'')+(type&64?'resizable,':'')+(type&128?'copyhistory,':'')+'width='+wid+',height='+hei+',left='+x+',top='+y;var wnd=window.open(url,tit,props);wnd.focus();if(ret)return wnd;},openCentered:function(url,wid,hei,type,tit,ret){wid=wid||800,hei=hei||600;var x=Math.floor((screen.availWidth-wid)/2);var y=Math.floor((screen.availHeight-hei)/2);return Window.open(url,wid,hei,x,y,type,tit,ret);},openFromEvent:function(e,url,wid,hei,type,tit,ret){e=$EV(e),wid=wid||800,hei=hei||600;var w=window,b=document.body,x,y;var el=$E(e.element());var ep=el.getPosition();var ed=el.getDimensions();var ws=Window.scroll();if(typeof(w.screenLeft)!='undefined'){x=(w.screenLeft+ep.x+ed.width)-ws.x;y=(w.screenTop+ep.y)-ws.y;}else{var sx=(w.screenX>=0?w.screenX:0);x=(sx+ep.x+ed.width)-ws.x;y=e.screenY-10;}return Window.open(url,wid,hei,x+2,y,type,tit,ret);},blank:function(wid,hei,x,y,type){return Window.open('about:blank',wid,hei,x,y,type,PHP2Go.uid('blank'),true);},write:function(wnd,html,close){close=PHP2Go.ifUndef(close,true);if(wnd.document){if(!wnd.writing){wnd.document.open();wnd.writing=true;}wnd.document.write(html);if(close){wnd.writing=false;wnd.document.close();}}},size:function(){var w=window,b=document.body,e=document.documentElement;return{width:(w.innerWidth||e.clientWidth||b.offsetWidth),height:(w.innerHeight||e.clientHeight||b.offsetHeight)};},position:function(){var w=window;return{x:(w.screenX||w.screenLeft),y:(w.screenY||w.screenTop)};},scroll:function(){var w=window,e=document.documentElement,b=document.body;return{x:(w.pageXOffset||(e&&e.scrollLeft?e.scrollLeft:(b&&b.scrollLeft?b.scrollLeft:0))),y:(w.pageYOffset||(e&&e.scrollTop?e.scrollTop:(b&&b.scrollTop?b.scrollTop:0)))};}};var IFrame={size:function(elm){try{if(elm=$(elm)){var b=(elm.contentDocument?elm.contentDocument.body:window.frames[elm.id].document.body);return{width:b.offsetWidth,height:b.offsetHeight};}}catch(e){}return{width:-1,height:-1};},scrollXTo:function(elm,x){elm=$(elm);var win=(elm?(elm.contentWindow||window.frames(elm.id)):null);if(window){var y=(win.scrollY||win.document.body.scrollTop);win.scrollTo(x,y);}},scrollYTo:function(elm,y){elm=$(elm);var win=(elm?(elm.contentWindow||window.frames(elm.id)):null);if(win){var x=(win.scrollX||win.document.body.scrollLeft);win.scrollTo(x,y);}},setUrl:function(elm,url){elm=$(elm);if(elm){if(elm.contentDocument)elm.setAttribute('src',url);else if(window.frames(elm.id)){window.frames(elm.id).location.replace(url);}}}};var Report={goToPage:function(frm,curr,total,handler){var pg,fld=frm.elements['page'];if(fld.value!=''){pg=parseInt(fld.value,10);if(pg>0&&pg<=total){if(typeof(handler)=='function')handler({from:curr,to:pg});if(frm.action.indexOf('?')==-1)frm.action=frm.action+'?page='+pg;else if(frm.action.indexOf('?')==frm.action.length-1)frm.action=frm.action+'page='+pg;else frm.action=frm.action+'&page='+pg;return true;}else{alert(Lang.report.invalidPage);fld.value='';fld.focus();}}return false;}};Widget=function(attrs,func){this.attributes={};this.listeners={};this.loadAttributes(attrs);if(typeof(func)=='function')func(this);};Widget.widgets=[];Widget.init=function(name,attrs,setupFunc){PHP2Go.include(PHP2Go.baseUrl+'widgets/'+name.toLowerCase()+'.js');this.widgets.push([name,attrs,setupFunc]);};Widget.prototype.hasAttributes=function(){for(var i=0;i<arguments.length;i++){if(typeof(this.attributes[arguments[i]])=='undefined')return false;}return true;};Widget.prototype.loadAttributes=function(attrs){for(var prop in attrs){this.attributes[prop]=attrs[prop];}};Widget.prototype.addEventListener=function(name,func){this.listeners[name]=this.listeners[name]||[];this.listeners[name].push(func.bind(this));};Widget.prototype.raiseEvent=function(name,args){var funcs=this.listeners[name]||[];for(var i=0;i<funcs.length;i++){if(funcs[i](args)===false)return false;}return true;};$=function(){var elm,d=document,a=arguments;if(a.length==1){elm=a[0];if(typeof(elm)=='string')elm=(d.getElementById?$E(d.getElementById(elm)):(d.all?$E(d.all[elm]):null));else elm=$E(elm);return elm;}var res=[];for(var i=0;i<a.length;i++){elm=a[i];if(typeof(elm)=='string')elm=(d.getElementById?$E(d.getElementById(elm)):(d.all?$E(d.all[elm]):null));else elm=$E(elm);res.push(elm);}return res;};$EF=function(){};$A=function(o){if(o&&o.constructor==Array)return o;return Array.valueOf(o);};$C=function(o,assoc){assoc=!!assoc;if(o.walk&&o.each)return o;else if(!o)o=(assoc?{}:[]);if(assoc){o={data:o};o.each=Hash.each;}else{o.each=Array.prototype.each;}Object.extend(o,Collection);return o;};$H=function(obj){return Hash.valueOf(obj);};$E=function(elm){if(!elm||!elm.tagName||elm.nodeType==3||elm._extended||elm==window||PHP2Go.nativeElementExtensions)return elm;Element.extend(elm);elm._extended=$EF;return elm;};$N=function(name,parent,style,html){var elm=$E(document.createElement(name.toLowerCase()));elm.setStyle(style);(parent)&&(parent.appendChild(elm));(html)&&(elm.innerHTML=html);return elm;};PHP2Go.load();