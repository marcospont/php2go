if(!PHP2Go.included[PHP2Go.baseUrl+'widgets/tabview.js']){PHP2Go.include(PHP2Go.baseUrl+'ajax.js');function TabView(attrs){this.Widget(attrs);this.root=null;this.navScroll=null;this.navContainer=null;this.contentContainer=null;this.activeTab=null;this.tabs=[];this.busy=false;}TabView.extend(Widget,'Widget');TabView.instances={};TabView.prototype.setup=function(){this.root=$(this.attributes.id);this.navScroll=this.root.getElementsByClassName('tabNavigationContainer')[0];this.navContainer=this.root.getElementsByClassName('tabNavigation')[0];this.contentContainer=this.root.getElementsByClassName('tabContainer')[0];var navItems=this.navContainer.getElementsByTagName('li');for(var i=0;i<this.attributes.tabs.length;i++){var panel=new TabPanel(this.attributes.tabs[i]);panel.labelEl=$E(navItems[i]);panel.parent=this;panel.setup();this.tabs.push(panel);Event.addListener(navItems[i].firstChild,'click',this._clickHandler.bind(this));}if(PHP2Go.browser.gecko&&(this.attributes.orientation=='left'||this.attributes.orientation=='right'))this.navScroll.style.height=this.navContainer.style.height=$E(this.contentContainer.getElementsByTagName('div')[0]).getDimensions().height;this._initArrows();this.setActiveIndex(this.attributes.activeIndex);TabView.instances[this.attributes.id]=this;this.raiseEvent('init');};TabView.prototype.addTab=function(tab,idx){if(tab instanceof TabPanel&&tab.hasAttributes('id','caption')){tab.parent=this;var before=this.getTabByIndex(idx);var nav=this.navContainer,cont=this.contentContainer;var li=$N('li');tab.labelEl=li;var a=$N('a',li);a.href='javascript:;';if(!tab.isEnabled())a.setAttribute('disabled',true);var span=$N('em',a);span.innerHTML=tab.attributes.caption||'';var div=$N('div');tab.contentEl=div;if(tab.attributes.id)div.id=tab.attributes.id;if(before){nav.insertBefore(li,before.labelEl);cont.insertBefore(div,before.contentEl);this.tabs.splice(idx-1,0,tab);}else{nav.appendChild(li);cont.appendChild(div);this.tabs.push(tab);}tab.setup();if(tab.isActive())this.setActiveTab(tab);else this._updateArrows();Event.addListener(a,'click',this._clickHandler.bind(this));}return tab;};TabView.prototype.removeTabByIndex=function(idx){this.removeTab(this.getTabByIndex(idx));};TabView.prototype.removeTab=function(tab){if(tab instanceof TabPanel&&tab.parent==this){if(!this.raiseEvent('beforeremove',[tab]))return;var idx=this.getTabIndex(tab);if(tab==this.activeTab){var found=false;for(var i=this.activeIndex+1;i<=this.tabs.length;i++){if(this.tabs[i-1].isEnabled()){this.setActiveTab(this.tabs[i-1]);found=true;break;}}if(!found){for(var i=this.activeIndex-1;i>0;i--){if(this.tabs[i-1].isEnabled()){this.setActiveTab(this.tabs[i-1]);break;}}}}Event.removeListener(tab.labelEl,'click',this._clickHandler.bind(this));this.navContainer.removeChild(tab.labelEl);this.contentContainer.removeChild(tab.contentEl);this.tabs.splice(idx-1,1);this.raiseEvent('afterremove',[tab]);this._updateArrows();}return tab;};TabView.prototype.getTabIndex=function(tab){for(var i=0;i<this.tabs.length;i++){if(this.tabs[i]==tab)return(i+1);}return-1;};TabView.prototype.getTabByIndex=function(idx){if(idx>=1&&idx<=this.tabs.length)return this.tabs[idx-1];return null;};TabView.prototype.getTabById=function(id){for(var i=0;i<this.tabs.length;i++){if(this.tabs[i].attributes.id==id)return this.tabs[i];}return null;};TabView.prototype.getActiveIndex=function(){return this.activeIndex;};TabView.prototype.setActiveIndex=function(idx){this.setActiveTab(this.getTabByIndex(idx));};TabView.prototype.getActiveTab=function(){return this.tabs[this.activeIndex-1];};TabView.prototype.setActiveTab=function(tab){if(tab instanceof TabPanel&&tab.isEnabled()&&tab!=this.activeTab){if(!this.raiseEvent('beforechange',[this.activeTab,tab]))return;if(this.activeTab){this._changeActiveState(this.activeTab,false);}if(tab.attributes.loadUri&&(!tab.attributes.loaded||!this.attributes.loadCache)){this._loadContents(tab);}else{this._changeActiveState(tab,true);this.raiseEvent('afterchange',[this.activeTab,tab]);this.activeIndex=this.getTabIndex(tab);this.activeTab=tab;this._updateArrows();}}return tab;};TabView.prototype._changeActiveState=function(tab,state){if(state){tab.labelEl.addClass('tabViewSelected');tab.contentEl.addClass('tabViewVisible');tab.labelEl.scrollIntoView(false);tab.raiseEvent('activate');}else{tab.labelEl.removeClass('tabViewSelected');tab.contentEl.removeClass('tabViewVisible');tab.raiseEvent('deactivate');}tab.attributes.active=state;};TabView.prototype._loadContents=function(tab){var self=this;var request=new AjaxUpdater(tab.attributes.loadUri,{method:tab.attributes.loadMethod,params:tab.attributes.loadParams,container:tab.contentEl,async:true });tab.attributes.loaded=false;tab.contentEl.addClass('tabViewLoading');self.busy=true;self._changeActiveState(tab,true);self.raiseEvent('beforeload',[tab,request]);request.bind('onUpdate',function(){tab.contentEl.removeClass('tabViewLoading');tab.attributes.loaded=true;self.raiseEvent('afterload',[tab]);self.raiseEvent('afterchange',[self.activeTab,tab]);self.activeIndex=self.getTabIndex(tab);self.activeTab=tab;self._updateArrows();self.busy=false;});request.send();};TabView.prototype._clickHandler=function(e){e=$EV(e);e.stop();if(!this.busy){var elm=this.root;var trg=e.target,tabs=this.tabs;for(var i=0;i<tabs.length;i++){if(trg.isChildOf(tabs[i].labelEl)){this.setActiveTab(tabs[i]);break;}}}};TabView.prototype._initArrows=function(){var ac=this.root.getElementsByClassName('tabScrollContainer')[0];var ar=this.arrows=ac.getElementsByClassName('tabScrollArrow');var self=this,ns=this.navScroll,dim=ns.getDimensions(),al=Event.addListener;var functions={left:function(){(ns.scrollLeft>0)&&(ns.scrollLeft-=5);self._updateArrows();(ns.scrollLeft>0)&&(self.timeout=functions.left.delay(20));},right:function(){((ns.scrollLeft+dim.width)<ns.scrollWidth)&&(ns.scrollLeft+=5);self._updateArrows();((ns.scrollLeft+dim.width)<ns.scrollWidth)&&(self.timeout=functions.right.delay(20));},top:function(){(ns.scrollTop>0)&&(ns.scrollTop-=5);self._updateArrows();(ns.scrollTop>0)&&(self.timeout=functions.top.delay(20));},bottom:function(){((ns.scrollTop+dim.height)<ns.scrollHeight)&&(ns.scrollTop+=5);self._updateArrows();((ns.scrollTop+dim.height)<ns.scrollHeight)&&(self.timeout=functions.bottom.delay(20));},clear:function(){clearTimeout(self.timeout);}};if(this.attributes.orientation=='top'||this.attributes.orientation=='bottom'){ar[0].style.height=ar[1].style.height=(ns.offsetHeight)+'px';ar[1].style.left=(dim.width-12)+'px';al(ar[0],'mousedown',functions.left);al(ar[0],'mouseup',functions.clear);al(ar[1],'mousedown',functions.right);al(ar[1],'mouseup',functions.clear);}else{ar[0].style.width=ar[1].style.width=(ns.offsetWidth)+'px';ar[1].style.top=(dim.height-12)+'px';al(ar[0],'mousedown',functions.top);al(ar[0],'mouseup',functions.clear);al(ar[1],'mousedown',functions.bottom);al(ar[1],'mouseup',functions.clear);}};TabView.prototype._updateArrows=function(){var ar=this.arrows,ns=this.navScroll;var dim=this.navScroll.getDimensions();if(this.attributes.orientation=='top'||this.attributes.orientation=='bottom'){ar[0].style.visibility=(ns.scrollLeft>0?'visible':'hidden');ar[1].style.visibility=(ns.scrollWidth>(dim.width+ns.scrollLeft)?'visible':'hidden');}else{ar[0].style.visibility=(ns.scrollTop>0?'visible':'hidden');ar[1].style.visibility=(ns.scrollHeight>(dim.height+ns.scrollTop)?'visible':'hidden');}};function TabPanel(attrs){this.Widget(Object.extend({id:null},attrs),null);this.labelEl=null;this.contentEl=null;this.parent=null;this.attributes.disabled=!!this.attributes.disabled;this.attributes.active=!!this.attributes.active;this.attributes.loaded=false;}TabPanel.extend(Widget,'Widget');TabPanel.prototype.setup=function(){if(!this.contentEl)this.contentEl=$(this.attributes.id);this.contentEl.tabPanel=this;};TabPanel.prototype.getIndex=function(){return this.parent.getTabIndex(this);};TabPanel.prototype.activate=function(){this.parent.setActiveTab(this);};TabPanel.prototype.isActive=function(){return(this.attributes.active&&this.parent.activeTab==this);};TabPanel.prototype.enable=function(){this.attributes.disabled=false;this.labelEl.firstChild.removeAttribute('disabled');};TabPanel.prototype.disable=function(){this.attributes.disabled=true;this.labelEl.firstChild.setAttribute('disabled',true);};TabPanel.prototype.isEnabled=function(){return(this.attributes.disabled==false);};TabPanel.prototype.loadContent=function(uri,method,params,activate){this.attributes.loaded=false;this.attributes.loadUri=uri;if(method)this.attributes.loadMethod=method;if(params)this.attributes.loadParams=params;this.attributes.loaded=false;activate=!!activate;if(this.parent.activeTab==this){this.parent._loadContents(this);}else if(activate){this.parent.setActiveTab(this);}};TabPanel.prototype.setContent=function(code,evalScripts){if(typeof(code)=='string')this.contentEl.update(code,evalScripts);};PHP2Go.included[PHP2Go.baseUrl+'widgets/tabview.js']=true;}