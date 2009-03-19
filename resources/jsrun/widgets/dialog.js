if(!PHP2Go.included[PHP2Go.baseUrl+'widgets/dialog.js']){PHP2Go.include(PHP2Go.baseUrl+'ajax.js');Dialog=function(opts){this.id=opts.id||PHP2Go.uid('dialog');this.el=null;this.contentEl=null;this.parent=$(opts.parent)||document.body;this.container=$(opts.container)||null;this.left=opts.left||null;this.top=opts.top||null;this.zIndex=opts.zIndex||9999;this.followScroll=(!this.container?!!opts.followScroll:false);this.contents=opts.contents;this.contentsClass=opts.contentsClass||null;this.loadUri=opts.loadUri||null;this.loadMethod=opts.loadMethod||'get';this.loadParams=opts.loadParams||null;this.trigger=$(opts.trigger)||null;if(this.trigger){this.trigger.setStyle('cursor','pointer');Event.addListener(this.trigger,'click',this.open.bind(this));}this.focusId=opts.focusId||null;this.buttons=[];this.buttonsClass=opts.buttonsClass||'';this.hoverClose=!!opts.hoverClose;this.onBeforeOpen=opts.onBeforeOpen||null;this.onOpen=opts.onOpen||null;this.onBeforeClose=opts.onBeforeClose||null;this.onClose=opts.onClose||null;this.visible=false;this.defaultButton=null;this.tabDelim={};this.tabForward=false;if(opts.buttons&&opts.buttons.length){for(var i=0;i<opts.buttons.length;i++)this.addButton(opts.buttons[i][0],opts.buttons[i][1]||null,!!opts.buttons[i][2]);}};Dialog.prototype.setup=function(){if(!this.el){Event.addListener(window,'unload',this.close.bind(this));this.el=$N('div',this.parent,{position:'absolute',display:'none',zIndex:this.zIndex },'',{id:this.id});var tdTag=(PHP2Go.browser.opera?'button':'span');var tdStyles=(PHP2Go.browser.opera?{width:'0px',height:'0px'}:null);var tdAttrs=Object.extend({tabIndex:0},(PHP2Go.browser.opera?{type:'button'}:{}));this.tabDelim.start=$N(tdTag,this.el,tdStyles,'',tdAttrs);var contentRoot=$N('div',this.el,(this.contentsClass?{}:{backgroundColor:'#fff',color:'#000',border:'1px solid #000',padding:'5px'}),'',{id:this.id+'_content',className:this.contentsClass });this.contentEl=$N('div',contentRoot);this.setupContents();this.tabDelim.end=$N(tdTag,this.el,tdStyles,'',tdAttrs);if(this.hoverClose){var self=this;Event.addListener(this.el,'mouseout',function(e){(!$EV(e).isRelated(self.el))&&(self.close());});}}};Dialog.prototype.setupContents=function(){if(!this.loadUri){this.setContents();}else{var req=new AjaxUpdater(this.loadUri,{method:this.loadMethod,params:this.loadParams,async:false,container:this.contentEl });req.send();}if(this.buttons.length>0){var self=this,parent=$N('div',this.contentEl.parentNode,{textAlign:'center',marginTop:'4px',marginBottom:'2px'});for(var i=0;i<this.buttons.length;i++){var btn=this.buttons[i].el=$N('button',parent,{marginLeft:'5px',marginRight:'5px'},this.buttons[i].text,{id:this.id+'_btn'+i,className:this.buttonsClass });btn.setAttribute('type','button');btn.index=i;Event.addListener(btn,'click',function(e){var idx=$EV(e).target.index;if(Object.isFunc(self.buttons[idx].fn))self.buttons[idx].fn.apply(self);});if(this.buttons[i].def)this.defaultButton=btn;}}else{Event.addListener(this.el,'click',this.close.bind(this));}};Dialog.prototype.addButton=function(text,fn,def){this.buttons.push({text:text,fn:fn||null,def:!!def });};Dialog.prototype.setButtonAction=function(idx,fn){if(this.buttons[idx])this.buttons[idx].fn=fn;};Dialog.prototype.enableButtons=function(){for(var i=0;i<this.buttons.length;i++)this.buttons[i].el.disabled=false;};Dialog.prototype.disableButtons=function(){for(var i=0;i<this.buttons.length;i++)this.buttons[i].el.disabled=true;};Dialog.prototype.setContainer=function(cont,place){this.container=$(cont);(!!place)&&(this.place());};Dialog.prototype.setContents=function(contents){this.contents=contents||this.contents;if(this.contents){if(Object.isString(this.contents)){this.contentEl.update(this.contents);}else if(this.contents.tagName){this.contents=$E(this.contents);this.contents.setParentNode(this.contentEl);}else if(this.contents.toString){this.contentEl.update(this.contents.toString());}}};Dialog.prototype.open=function(){if(this.onBeforeOpen&&this.onBeforeOpen.apply(this)==false)return;this.setup();this.show();this.visible=true;(this.onOpen)&&(this.onOpen.apply(this));};Dialog.prototype.close=function(){if(this.onBeforeClose&&this.onBeforeClose.apply(this)==false)return;this.hide();this.visible=false;if(this.onClose)this.onClose.apply(this);};Dialog.prototype.show=function(){if(this.followScroll&&!this.container)Event.addListener(window,'scroll',PHP2Go.method(this,'scrollHandler'),true);Event.addListener(window,'resize',PHP2Go.method(this,'resizeHandler'),true);this.el.show();this.place();var focusEl=$(this.focusId);if(focusEl&&focusEl.isChildOf(this.contentEl))focusEl.focus();else if(this.defaultButton)this.defaultButton.focus();else if(this.buttons.length>0)this.buttons[0].el.focus();else this.tabDelim.start.focus();};Dialog.prototype.place=function(){var elDim,parDim,offset;if(this.container){if(this.left!==null||this.top!==null){offset=(this.container==document.body?{x:0,y:0}:this.container.getPosition());this.el.moveTo(offset.x+(this.left||0),offset.y+(this.top||0));}else{elDim=this.el.getDimensions();contDim=this.container.getDimensions();offset=this.cont.getPosition();this.el.moveTo((((contDim.width-elDim.width)/2)+offset.x),(((contDim.height-elDim.height)/2)+offset.y));}}else{elDim=this.el.getDimensions();parDim=Window.size();offset=Window.scroll();this.el.moveTo((((parDim.width-elDim.width)/2)+offset.x),(((parDim.height-elDim.height)/2)+offset.y));}};Dialog.prototype.hide=function(){if(this.followScroll&&!this.container)Event.removeListener(window,'scroll',PHP2Go.method(this,'scrollHandler'),true);Event.removeListener(window,'resize',PHP2Go.method(this,'resizeHandler'),true);this.el.hide();};Dialog.prototype.resizeHandler=function(e){this.place();};Dialog.prototype.scrollHandler=function(e){this.place();};ModalDialog=function(opts){this.Dialog(Object.extend(opts,{followScroll:true }));this.overlay=null;this.overlayColor=opts.overlayColor||'#ccc';this.overlayClass=opts.overlayClass||'';this.opacity=opts.opacity||0.4;};ModalDialog.extend(Dialog,'Dialog');ModalDialog.prototype.setup=function(){ModalDialog.superclass.setup.apply(this);if(!this.overlay){var styles=Object.extend({position:'absolute',left:'0px',top:'0px',zIndex:this.zIndex-2,backgroundColor:this.overlayColor,opacity:this.opacity,display:'none'},(PHP2Go.browser.ie?{}:{width:'100%',height:'100%'}));this.overlay=$N('div',document.body,styles,'',{id:this.id+'_overlay',className:this.overlayClass });}};ModalDialog.prototype.show=function(){ModalDialog.superclass.show.apply(this);var tf=PHP2Go.method(this,'tabDelimFocusHandler');var tb=PHP2Go.method(this,'tabDelimBlurHandler');var k=PHP2Go.method(this,'keyHandler');Event.addListener(this.tabDelim.start,'focus',tf);Event.addListener(this.tabDelim.start,'blur',tb);Event.addListener(this.tabDelim.end,'focus',tf);Event.addListener(this.tabDelim.end,'blur',tb);Event.addListener(document,'keypress',k);this.overlay.show();};ModalDialog.prototype.place=function(){ModalDialog.superclass.place.apply(this);var sc=Window.scroll();this.overlay.moveTo(sc.x,sc.y);if(PHP2Go.browser.ie){var size=Window.size();this.overlay.resizeTo(size.width,size.height);}};ModalDialog.prototype.hide=function(){ModalDialog.superclass.hide.apply(this);var tf=PHP2Go.method(this,'tabDelimFocusHandler');var tb=PHP2Go.method(this,'tabDelimBlurHandler');var k=PHP2Go.method(this,'keyHandler');Event.removeListener(this.tabDelim.start,'focus',tf);Event.removeListener(this.tabDelim.start,'blur',tb);Event.removeListener(this.tabDelim.end,'focus',tf);Event.removeListener(this.tabDelim.end,'blur',tb);Event.removeListener(document,'keypress',k);this.overlay.hide();};ModalDialog.prototype.tabDelimFocusHandler=function(e){e=e||window.event;var trg=e.target||e.srcElement;if(trg==this.tabDelim.start){if(this.tabForward){this.tabForward=false;}else{this.tabForward=true;this.tabDelim.end.focus();}}else if(trg==this.tabDelim.end){if(this.tabForward){this.tabForward=false;}else{this.tabForward=true;this.tabDelim.start.focus();}}};ModalDialog.prototype.tabDelimBlurHandler=function(){var self=this;setTimeout(function(){self.tabForward=false;},100);};ModalDialog.prototype.keyHandler=function(e){var e=$EV(e);if(e.target.isChildOf(this.el))return;if(e.key()!=9){e.stop();}else{try{this.tabDelim.start.focus();}catch(e){}}};ImageDialog=function(opts){this.ModalDialog(Object.extend(opts,{contents:null,loadUri:null,container:null }));this.img=null;this.imgUri=(opts.img||(this.trigger||{}).src||null);};ImageDialog.extend(ModalDialog,'ModalDialog');ImageDialog.setup=function(cls){var imgs=document.getElementsByTagName('img');for(var i=0;i<imgs.length;i++){if(Element.hasClass(imgs[i],cls)){new ImageDialog({trigger:imgs[i]});}}};ImageDialog.prototype.setup=function(){ImageDialog.superclass.setup.apply(this);if(!this.img){this.img=$N('img',this.contentEl,{cursor:'pointer',display:'none'});this.img.onload=function(){this.img.style.display='';this.place();}.bind(this);this.img.src=this.imgUri;}};PHP2Go.included[PHP2Go.baseUrl+'widgets/dialog.js']=true;}