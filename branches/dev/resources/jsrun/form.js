if(!PHP2Go.included[PHP2Go.baseUrl+'form.js']){var Form={get:function(formName){return document.forms[formName];},getElements:function(form){form=$(form);if(form)return form.elements;return[];},getFields:function(form){var res=$H();form=$(form);if(form){$C(form.elements).walk(function(el,idx){if(el.auxiliary==true||((!el.type||!el.name)&&!el.component)||(el.type&&(/^submit|reset|button$/.test(el.type))))return;var key=(el.name||el.id);if(!res.containsKey(key))res.data[key]=Form.getField(form,key);});}return res;},getField:function(form,name){if(form=$(form)){var fld=form.elements[name];if(fld&&!fld.type&&fld.length)return Field.fromFormElement(fld[0]);return Field.fromFormElement(fld);}return null;},clear:function(form,prsv){if(form=$(form)){var flds=this.getFields(form);var prsv=$A(prsv);flds.walk(function(el,idx){if(el.key!='__form_signature'&&!prsv.contains(el.key))el.value.clear();});if(form.validator)form.validator.clearErrors();}},reset:function(form){if(form=$(form))form.reset();},enableAll:function(form,prsv){form=$(form),prsv=$A(prsv);var flds=this.getFields(form);flds.walk(function(el,idx){if(!prsv.contains(el.name))el.value.enable();});},enable:function(form){var list=$A(arguments).slice(1);list.walk(function(el,idx){if(f=Form.getField(form,el))f.enable();});},disableAll:function(form,prsv){form=$(form),prsv=$A(prsv);var flds=this.getFields(form);flds.walk(function(el,idx){if(!prsv.contains(el.name))el.value.disable();});},disable:function(form){var list=$A(arguments).slice(1);list.walk(function(el,idx){if(f=Form.getField(form,el))f.disable();});},serialize:function(form,fld){fld=fld||[];this.updateCheckboxes(form);return this.getFields(form).filter(function(el){if(fld.empty()||fld.contains(el.key))return el.value.serialize();}).join('&');},ajaxify:function(form,ajax){form=$(form);if(form&&Object.isFunc(ajax)){form.ajax=ajax;Event.addListener(form,'submit',function(e){var evt=$EV(e),frm=null;evt.stop();frm=Element.getParentByTagName(evt.element(),'form');if(frm){var ajax=frm.ajax();ajax.form=frm.id;ajax.formValidate=true;ajax.send();}},true);}},focusFirstField:function(form){var flds=this.getFields(form).getValues();flds.walk(function(el,idx){if(el.type!='hidden'&&el.focus())throw $break;});},updateCheckboxes:function(form){form=$(form);if(form){function updateHidden(chk,idx){var rel=$('V_'+chk.name);if(rel){rel.value=(chk.checked?'T':'F');rel.disabled=chk.disabled;}}$C(form.elements).accept(function(item){return(item.type=='checkbox');}).walk(updateHidden);}}};Event.addListener(window,'load',function(){var forms=$C(document.getElementsByTagName('form'));forms.walk(function(item,idx){Event.addListener(item,'submit',function(){Form.updateCheckboxes(item);});});});Field=function(fld){this.fld=fld;this.id=(fld.id||null);this.name=(fld.name||fld.id);this.label=(fld.title||null);this.type=(fld.type||null);};Field.fromFormElement=function(elm){elm=$(elm);if(elm){if(elm.component){return elm.component;}else if(elm.tagName.equalsIgnoreCase('select')){elm.component=new SelectField(elm);return elm.component;}else if(elm.type=='radio'||elm.type=='checkbox'){var grp=elm.form.elements[elm.name];if(grp.length||/\[\]$/.test(elm.name)){var comp=new GroupField(grp);return comp;}else{elm.component=new InputSelectorField(elm);return elm.component;}}else if(elm.name&&elm.type){elm.component=new InputField(elm);return elm.component;}}return null;};Field.prototype.getFormElement=function(){return this.fld;};Field.prototype.getMask=function(){if(this.fld&&this.fld.inputMask)return this.fld.inputMask.mask;return null;};Field.prototype.getValue=function(){return(this.fld.value||null);};Field.prototype.setValue=function(val){val=val||'';this.fld.value=val;if(this.fld.onchange)this.fld.onchange();};Field.prototype.clear=function(){if(!this.fld.readOnly)this.fld.value='';};Field.prototype.isEmpty=function(){return(this.getValue()==null);};Field.prototype.enable=function(){this.setDisabled(false);};Field.prototype.disable=function(){this.setDisabled(true);};Field.prototype.setDisabled=function(b){this.fld.disabled=b;};Field.prototype.focus=function(){if(this.beforeFocus()&&!this.fld.disabled){this.fld.focus();return true;}return false;};Field.prototype.beforeFocus=function(){var elm=(this instanceof GroupField?this.grp[0]:this.fld);while(elm=elm.parentNode){if(elm.tabPanel&&!elm.tabPanel.isActive()){if(!elm.tabPanel.isEnabled())return false;elm.tabPanel.activate();}}return true;};Field.prototype.serialize=function(){if(this.fld.disabled==true)return null;var nm=this.name.replace(/\[\]$/,'');var self=this,v=this.getValue();if(v!=null&&v!=''){if(v.constructor==Array){return v.map(function(el){return nm+'[]='+el.urlEncode();}).join('&');}else{return this.name+'='+v.urlEncode();}}return null;};InputField=function(fld){this.Field(fld);this.fld.component=this;};InputField.extend(Field,'Field');InputField.prototype.isEmpty=function(){return(this.fld.value.trim()=='');};InputField.prototype.getSelection=function(){return FieldSelection.get(this.fld);};InputSelectorField=function(fld){this.Field(fld);this.fld.component=this;};InputSelectorField.extend(Field,'Field');InputSelectorField.prototype.getValue=function(){if(this.fld.type=='checkbox'){var peer=$('V_'+this.fld.name);if(peer)return peer.value;}return(this.fld.checked?this.fld.value:null);};InputSelectorField.prototype.setChecked=function(b){this.fld.checked=!!b;};InputSelectorField.prototype.clear=function(){this.fld.checked=false;};InputSelectorField.prototype.isEmpty=function(){return(this.fld.checked==false);};SelectField=function(fld){this.Field(fld);this.fld.component=this;this.multiple=(this.type!='select-one');};SelectField.extend(Field,'Field');SelectField.prototype.getValue=function(){if(this.multiple){var res=$C(this.fld.options).filter(function(el){if(el.selected==true)return el.value;return null;});return(res.empty()?null:res);}else{var idx=this.fld.selectedIndex;if(idx>=0&&this.fld.options[idx].value!="")return this.fld.options[idx].value;return null;}};SelectField.prototype.setValue=function(val){var self=this,val=(this.multiple?$A(val):val);$C(this.fld.options).walk(function(el,idx){if(self.multiple){el.selected=(val.contains(el.value)?true:false);}else if(el.value==val){el.selected=true;self.fld.value=el.value;self.fld.selectedIndex=idx;throw $break;}});if(this.fld.onchange)this.fld.onchange();};SelectField.prototype.selectByText=function(text){$C(this.fld.options).walk(function(el,idx){(el.text==text)&&(el.selected=true);});};SelectField.prototype.clear=function(){if(!this.multiple)this.fld.value='';$C(this.fld.options).walk(function(el,idx){el.selected=false;});};SelectField.prototype.clearOptions=function(){this.fld.options.length=0;};SelectField.prototype.setFirstOption=function(value,text){var o=this.fld.options;o[0]=new Option(text,value);(o.length==0)&&(o.length=1);};SelectField.prototype.addOption=function(value,text,pos){var i,f=this.fld;pos=Math.abs(Object.ifUndef(pos,f.options.length));if(f.add){if(pos<f.options.length){try{f.add(new Option(text,value),f.options[pos]);}catch(e){f.add(new Option(text,value),pos);}}else{f.options[pos]=new Option(text,value);}}else{for(i=f.options.length;i>pos;i--)f.options[i]=f.options[i-1];f.options[pos]=new Option(text,value);}};SelectField.prototype.importOptions=function(str,lsep,csep,pos,val){lsep=(lsep||'|'),csep=(csep||'~'),val=(val||this.fld.value);pos=Math.abs(pos||0);if(pos<=this.fld.options.length){var self=this;self.fld.options.length=pos;str.split(lsep).walk(function(el,idx){var opt=el.split(csep);if(opt.length>=2){self.fld.options[pos]=new Option(opt[1],opt[0]);pos++;}if(val&&val==opt[0])self.fld.value=val;});}};SelectField.prototype.removeOption=function(idx){var i,f=this.fld,idx=Math.abs(idx);var r=Object.isFunc(f.remove);if(idx>=f.options.length)return;if(r){f.remove(idx);}else{for(i=idx;i<(f.options.length-1);i++){f.options[i].value=f.options[i+1].value;f.options[i].text=f.options[i+1].text;f.options[i].selected=f.options[i+1].selected;}f.options.length--;}};SelectField.prototype.removeSelectedOptions=function(func,idx){var i,c,j,k,f=this.fld;var r=Object.isFunc(f.remove);func=func||$EF,idx=Object.ifUndef(idx,0);for(i=idx;i<f.options.length;i++){if(f.options[i].selected==true){if(r){try{func(f.options[i]);}catch(e){if(e==$break)break;if(e==$continue)continue;}f.remove(i);i--;}else{c=0,j=i;while((j<f.options.length)&&(f.options[j].selected==true)){try{func(f.options[j]);}catch(e){if(e==$break)break;if(e==$continue)continue;}c++,j++;}if(f.options.length>(i+c)){for(k=i;k<(f.options.length-c);k++){f.options[k].value=f.options[k+c].value;f.options[k].text=f.options[k+c].text;f.options[k].selected=f.options[k+c].selected;}}f.options.length=f.options.length-c;}}}};GroupField=function(grp){this.grp=$A(grp);var self=this;this.grp.walk(function(item,idx){item.component=self;});this.name=this.grp[0].name;this.label=(this.grp[0].title||null);this.type=this.grp[0].type;this.multiple=(this.type=='checkbox');};GroupField.extend(Field,'Field');GroupField.prototype.getValue=function(){var v=this.grp.filter(function(el){if(el.checked)return el.value;return null;});if(v.empty())return null;return(this.multiple?v:v[0]);};GroupField.prototype.getFormElement=function(){return this.grp;};GroupField.prototype.setValue=function(val){var self=this,val=(this.multiple?$A(val):val);this.grp.walk(function(el,idx){if(self.multiple){el.checked=val.contains(el.value);if(el.onchange)el.onchange();}else if(el.value==val){el.checked=true;if(el.onchange)el.onchange();throw $break;}});};GroupField.prototype.clear=function(){this.setAll(false);};GroupField.prototype.invert=function(){var res=null;if(this.multiple){res=0;this.grp.walk(function(el,idx){el.checked=!el.checked;res+=(el.checked?1:0);});}return res;};GroupField.prototype.setAll=function(b){b=!!b;this.grp.walk(function(el,idx){el.checked=b;});};GroupField.prototype.isEmpty=function(){var empty=true;this.grp.walk(function(el,idx){if(el.checked){empty=false;throw $break;}});return empty;};GroupField.prototype.setDisabled=function(b){this.grp.walk(function(el,idx){el.disabled=b;});};GroupField.prototype.focus=function(){if(!this.beforeFocus())return false;var found=false;this.grp.walk(function(el,idx){if(!el.disabled){el.focus();found=true;throw $break;}});return found;};GroupField.prototype.serialize=function(){var self=this,nm=this.name.replace(/\[\]$/,'');var v=this.grp.filter(function(el){if(el.checked&&!el.disabled){if(self.multiple)return nm+'[]='+el.value.urlEncode();return nm+'='+el.value.urlEncode();}return null;});return(v.empty()?null:v.join('&'));};ComponentField=function(fld,clsName){this.Field(fld);this.componentClass=clsName;this.listeners={};};ComponentField.extend(Field,'Field');ComponentField.prototype.addEventListener=function(name,func){this.listeners[name]=this.listeners[name]||[];this.listeners[name].push(func.bind(this));};ComponentField.prototype.raiseEvent=function(name,args){var funcs=this.listeners[name]||[];for(var i=0;i<funcs.length;i++){funcs[i](args);}if(this.fld&&Object.isFunc(this.fld['on'+name]))this.fld['on'+name]();};CheckboxController=function(frm,name,opts){this.group=$FF(frm,name);this.all=(opts.all?$(opts.all):null);this.none=(opts.none?$(opts.none):null);this.toggle=(opts.toggle&&(opts.toggle.type||'')=='checkbox'?$(opts.toggle):null);this.invert=(opts.invert?$(opts.invert):null);this.enabler=(Object.isFunc(opts.enabler)?opts.enabler:null);if(this.group&&(this.all||this.none||this.toggle||this.invert||this.enabler))this.setupEvents();};CheckboxController.prototype.setupEvents=function(){var self=this;var inputs=self.group.grp;var boxChanged=function(e){e=(e||window.event);var box=(e.target||e.srcElement);var val=self.group.getValue();if(box.checked){(self.enabler)&&(self.enabler(true));if(val.length==inputs.length&&self.toggle)self.toggle.checked=true;}else{(self.toggle)&&(self.toggle.checked=false);if(val==null&&self.enabler)self.enabler(false);}};for(var i=0;i<inputs.length;i++)Event.addListener(inputs[i],'click',boxChanged);if(self.invert){Event.addListener(self.invert,'click',function(e){var res=self.group.invert();(self.toggle)&&(self.toggle.checked=(res==inputs.length));(self.enabler)&&(self.enabler(res>0));});}var setAll=function(b){self.group.setAll(b);(self.toggle)&&(self.toggle.checked=b);(self.enabler)&&(self.enabler(b));};(self.all)&&(Event.addListener(self.all,'click',function(e){setAll(true);}));(self.none)&&(Event.addListener(self.none,'click',function(e){setAll(false);}));if(self.toggle){self.toggle.auxiliary=true;Event.addListener(self.toggle,'click',function(e){setAll(self.toggle.checked);});}};var FieldSelection={prepare:function(elm){elm=$(elm);if(elm&&elm.createTextRange){var setRange=function(e){elm.range=document.selection.createRange().duplicate();};Event.addListener(elm,'click',setRange);Event.addListener(elm,'dblclick',setRange);Event.addListener(elm,'select',setRange);Event.addListener(elm,'keyup',setRange);Event.addListener(elm,'paste',setRange);setRange();}},get:function(elm){elm=$(elm);if(elm&&!elm.disabled){if(elm.createTextRange){if(!elm.range)this.prepare(elm);return elm.range.text;}else if(elm.setSelectionRange){return elm.value.substring(elm.selectionStart,elm.selectionEnd);}}return"";},getRange:function(elm){elm=$(elm);if(elm&&!elm.disabled){if(elm.setSelectionRange){return{start:elm.selectionStart,end:elm.selectionEnd,size:(elm.selectionEnd-elm.selectionStart)};}else if(elm.createTextRange){var range,start,end;if(!elm.range)this.prepare(elm);if(elm.range.parentElement()==elm){range=elm.range.duplicate();range.moveStart('textedit',-1);end=range.text.length;start=end-elm.range.text.length;return{start:start,end:end,size:(end-start)};}}}return{start:0,end:0,size:0};},setRange:function(elm,start,end){elm=$(elm);if(elm&&!elm.disabled){start=Math.max(start,0);end=Math.min(end,elm.value.length);if(elm.createTextRange){elm.focus();if(!elm.range){this.prepare(elm);}else{elm.range.moveStart('textedit',-1);elm.range.moveEnd('textedit',-1);}elm.range.moveEnd('character',end);elm.range.moveStart('character',start);elm.range.select();}else if(elm.setSelectionRange){elm.focus();elm.setSelectionRange(start,end);}}},getCaret:function(elm){elm=$(elm);if(elm&&!elm.disabled){if(elm.setSelectionRange){return elm.selectionStart;}else if(elm.createTextRange){var range,end;if(!elm.range)this.prepare(elm);if(elm.range.parentElement()==elm){range=elm.range.duplicate();range.moveStart('textedit',-1);if(range.text.length!=elm.range.text.length){end=range.text.length;return(end-elm.range.text.length);}else{return range.text.length;}}}}return 0;},setCaret:function(elm,pos){this.setRange(elm,pos,pos);},collapse:function(elm,toStart){elm=$(elm);if(elm&&!elm.disabled){toStart=Object.ifUndef(toStart,true);if(elm.createTextRange){var range=elm.createTextRange();range.collapse(toStart);range.select();}else if(elm.setSelectionRange){var pos=(toStart?0:elm.value.length);elm.setSelectionRange(pos,pos);elm.focus();}}}};$F=function(elm){return Field.fromFormElement(elm);};$FF=function(form,field){return Form.getField(form,field);};$V=function(form,field){var f=Form.getField(form,field);return(f?f.getValue():null);};PHP2Go.included[PHP2Go.baseUrl+'form.js']=true;}