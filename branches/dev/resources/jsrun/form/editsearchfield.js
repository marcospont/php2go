if(!PHP2Go.included[PHP2Go.baseUrl+'form/editsearchfield.js']){PHP2Go.include(PHP2Go.baseUrl+'jsrsclient.js');PHP2Go.include(PHP2Go.baseUrl+'inputmask.js');EditSearchField=function(id,results,masks,loadIdx,reqUrl,autoTrim,autoDispatch,debug,initOption){this.ComponentField($(id),'EditSearchField');this.filters=$(id+"_filters");this.masks=masks;this.search=$(this.id+'_search');this.results=$(results);this.button=$(id+"_button");this.loadIdx=loadIdx||0;this.requestUrl=reqUrl||document.location.pathname;this.autoTrim=!!autoTrim;this.autoDispatch=!!autoDispatch;this.debug=!!debug;this.hideAlerts=false;this.setup();if(this.autoDispatch){this.hideAlerts=true;this.submit(initOption);this.hideAlerts=false;}};EditSearchField.extend(ComponentField,'ComponentField');EditSearchField.prototype.setup=function(){var self=this;this.fld.component=this;this.filters.auxiliary=true;this.search.auxiliary=true;this.results.auxiliary=true;Event.addListener(this.search,'keypress',function(e){var e=$EV(e),b=self.button;if(e.key()==13){self.submit();e.stop();}});InputMask.setup(this.search,Mask.fromMaskName(this.masks[this.filters.selectedIndex]));Event.addListener(this.filters,'change',function(e){var newMask=Mask.fromMaskName(self.masks[self.filters.selectedIndex]);self.search.inputMask.mask=newMask;self.search.value='';self.search.focus();});Event.addListener(this.button,'click',function(e){if(!self.button.disabled&&!self.button.searching)self.submit();});};EditSearchField.prototype.getValue=function(){var idx=this.results.selectedIndex;if(idx>=0&&this.results.options[idx].value!="")return this.results.options[idx].value;return null;};EditSearchField.prototype.setValue=function(val){var self=this,opt=$C(this.results.options);opt.walk(function(item,idx){if(item.value==val){self.results.value=val;item.selected=true;throw $break;}});};EditSearchField.prototype.clear=function(){this.search.value='';this.filters.options[0].selected=true;this.results.options.length=0;};EditSearchField.prototype.setDisabled=function(b){this.filters.disabled=b;this.search.disabled=b;this.button.disabled=b;this.results.disabled=b;};EditSearchField.prototype.focus=function(){if(this.beforeFocus()){if(this.results.options.length>this.loadIdx){if(!this.results.disabled){this.results.focus();return true;}}else{if(!this.filters.disabled){this.filters.focus();return true;}}}return false;};EditSearchField.prototype.submit=function(initOption){var ffilt=$F(this.filters);var initOption=PHP2Go.ifUndef(initOption,null);if(this.search.value!=''){if(this.autoTrim)this.search.value=this.search.value.trim();if(this.validate()){var self=this,btn=this.button;var btnValue=btn.innerHTML;var processResponse=function(response,context){var select=new SelectField(self.results);btn.searching=false;if(btn.tagName.equalsIgnoreCase('button')){btn.disabled=false;btn.innerHTML=btnValue;}if(response!=""){select.fld.options.length=self.loadIdx;select.importOptions(response,'|','~',self.loadIdx);if(initOption)select.setValue(initOption);select.focus();}else if(!self.hideAlerts){alert(Lang.search.emptyResults);}};if(btn.tagName.equalsIgnoreCase('button')){btn.disabled=true;btn.innerHTML=Lang.search.searchingBtnValue;}$(this.id+'_lastfilter').value=ffilt.getValue();$(this.id+'_lastsearch').value=this.search.value;btn.searching=true;jsrsExecute(this.requestUrl,processResponse,this.id.toLowerCase()+'PerformSearch',Array(ffilt.getValue(),this.search.value),this.debug);}else{if(!this.hideAlerts)alert(Lang.invalidValue);this.search.select();}}else{if(!this.hideAlerts)alert(Lang.search.emptySearch);this.filters.focus();}};EditSearchField.prototype.validate=function(){try{var mask=this.masks[this.filters.selectedIndex];return(mask=='STRING'?true:Validator.isMask(this.search.value,mask));}catch(e){return false;}};PHP2Go.included[PHP2Go.baseUrl+'form/editsearchfield.js']=true;}