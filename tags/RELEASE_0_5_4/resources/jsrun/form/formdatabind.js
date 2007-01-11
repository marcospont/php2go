if(!PHP2Go.included[PHP2Go.baseUrl+'form/formdatabind.js']){PHP2Go.include(PHP2Go.baseUrl+'inputmask.js');PHP2Go.include(PHP2Go.baseUrl+'jsrsclient.js');FormDataBind=function(db,frm,tbl,pk,readOnly,forcePost,uri){this.absoluteUri=uri||document.location.pathname;this.tableName=tbl;this.pkName=pk;this.form=$(frm);this.formName=frm;this.db=eval("document.all."+db);this.rs=null;this.readOnly=!!readOnly;this.forcePost=!!forcePost;this.action=null;this.cacheValues=null;this.cachePosition=1;this.setup();};FormDataBind.prototype.setup=function(){Event.addListener(document,'click',this.clickHandler.bind(this));this.rs=this.db.recordset;if(!this.readOnly){this.disableActions(false);this.disableNavigation(false);}this.fixTabIndexes();if(this.form.elements['lastposition'].value!=''){this.moveToRecord(this.form.elements['lastposition'].value);this.form.elements['lastposition'].value='';}else{this.showRecCount();}};FormDataBind.prototype.clickHandler=function(e){var elm=$EV(e).element();if(elm.name&&!elm.disabled){switch(elm.name){case'navFirst':case'navPrevious':case'navNext':case'navLast':this.navigate(elm);break;case'actNew':case'actEdit':case'actDel':case'actSave':case'actCancel':this.executeAction(elm);break;case'actFilterBtn':this.cancelAction();this.applyFilter();break;case'imgSortAsc':case'imgSortDesc':this.cancelAction();this.applySort(elm.name=='imgSortAsc');break;case'actGotoBtn':this.cancelAction();this.moveToRecord();break;}}};FormDataBind.prototype.navigate=function(elm){if(elm&&elm.disabled==false){if(elm.name=='navFirst'){this.rs.MoveFirst();}else if(elm.name=='navPrevious'){if(this.rs.BOF){this.rs.MoveFirst();}else if(this.rs.AbsolutePosition!=1){this.rs.MovePrevious();}}else if(elm.name=='navNext'){if(this.rs.EOF){this.rs.MoveLast();}else if(this.rs.AbsolutePosition!=this.rs.RecordCount){this.rs.MoveNext();}}else if(elm.name=='navLast'){this.rs.MoveLast();}setTimeout(this.showRecCount.bind(this),100);}};FormDataBind.prototype.executeAction=function(elem){if(elem.disabled==false){if(elem.name=='actNew'){this.addRecord();}else if(elem.name=='actEdit'){this.editRecord();}else if(elem.name=='actSave'){this.saveRecord();}else if(elem.name=='actDel'){if(this.rs.RecordCount>0){this.deleteRecord();}else{alert(Lang.dataBind.deleteEmpty);}}else if(elem.name=='actCancel'){this.cancelAction();}}};FormDataBind.prototype.addRecord=function(){this.currentAction='add';this.cachePosition=this.rs.AbsolutePosition;this.rs.AddNew();this.showRecCount();this.disableForm(false);this.disableActions(true);this.disableNavigation(true);};FormDataBind.prototype.editRecord=function(){if(this.rs.RecordCount>0){this.currentAction='edit';var cache=[];for(var i=0;i<this.rs.fields.count;i++)cache.push(this.rs.fields(i).value);this.cacheValues=cache.join('#');this.cachePosition=this.rs.AbsolutePosition;this.disableForm(false);this.disableActions(true);this.disableNavigation(true);}else{alert(Lang.dataBind.updateEmpty);}};FormDataBind.prototype.saveRecord=function(){if(!this.form.validator||this.form.validator.run()){if(this.forcePost){this.form.elements['lastposition'].value=this.rs.AbsolutePosition;this.form.submit();}else{var self=this;var saveValues=$C(this.form.elements).filter(function(item,idx){if(!item.name||!item.type)throw $continue;if(item.type=='radio'){if(item.checked)return item.name+'|'+item.value;}else if(item.type=='checkbox'){return item.name+'|'+(item.checked?'T':'F');}else{return item.name+'|'+item.value;}return null;}).join('#');var saveRecordReturn=function(result,context){if(!parseInt(result,10)){alert(result);self.cancelAction();}else{if(self.currentAction=='add'){var pkField=self.form.elements[self.pkName];if(pkField)pkField.value=result;alert(Lang.dataBind.insertSuccess);}else{alert(Lang.dataBind.updateSuccess);}self.currentAction=null;self.disableForm(true);self.disableActions(false);self.disableNavigation(false);}};jsrsExecute(this.absoluteUri,saveRecordReturn,"saveRecord",Array(saveValues,this.tableName,this.pkName));window.status='';}}};FormDataBind.prototype.deleteRecord=function(){function getPrimaryKeyValue(){for(var i=0;i<this.rs.fields.count;i++){if(this.rs.fields(i).name==this.pkName){return this.rs.fields(i).value;}}return'';}if(confirm(Lang.dataBind.deleteConfirm)){if(this.forcePost){this.form.elements['lastposition'].value=this.rs.AbsolutePosition;this.form.elements['removeid'].value=getPrimaryKeyValue();this.form.submit();}else{var self=this;var delRegReturn=function(result,context){if(!parseInt(result,10)){alert(result);self.cancelAction();}else{self.rs.Delete();self.showRecCount();alert(Lang.dataBind.deleteSuccess);}self.disableNavigation(false);};jsrsExecute(this.absoluteUri,delRegReturn,"deleteRecord",Array(this.tableName,this.pkName,getPrimaryKeyValue()));window.status='';}}};FormDataBind.prototype.cancelAction=function(){if(this.currentAction=='add'){this.rs.Delete();}else if(this.currentAction=='edit'){var cache=this.cacheValues.split('#');for(var i=0;i<this.rs.fields.count;i++)this.rs.fields(i).value=(cache[i]!=''?cache[i]:'');}this.currentAction=null;if(this.rs.AbsolutePosition>0)this.rs.AbsolutePosition=(isNaN(this.cachePosition)?1:Math.max(parseInt(this.cachePosition,10),1));this.showRecCount();this.disableForm(true);this.disableActions(false);this.disableNavigation(false);};FormDataBind.prototype.applyFilter=function(){var self=this;var filter=$FF(this.formName,'actFilterSelect');var term=$FF(this.formName,'actFilterTerm');if(!filter.isEmpty()&&!term.isEmpty()){this.db.Filter=filter.getValue()+" >= "+term.getValue();this.db.Reset();setTimeout(function(){self.showRecCount();if(self.rs.RecordCount==0)alert(Lang.search.emptyResults);},100);}else{this.db.Filter='';this.db.Reset();setTimeout(this.showRecCount.bind(this),100);alert(Lang.search.emptySearch);(filter.isEmpty()?filter.focus():term.focus());}};FormDataBind.prototype.applySort=function(ascending){var sort=$FF(this.formName,'actSortSelect');if(!sort.isEmpty()){this.db.SortColumn=sort.getValue();this.db.SortAscending=ascending;this.db.Reset();this.rs=this.db.recordset;}else{alert(Lang.dataBind.sortInvalid);sort.focus();}};FormDataBind.prototype.moveToRecord=function(recordNum){var gotoField=$FF(this.formName,'actGotoField');var target=parseInt(PHP2Go.ifUndef(recordNum,gotoField.getValue()),10);if(target){if(target<this.rs.RecordCount){this.rs.AbsolutePosition=target;setTimeout(this.showRecCount.bind(this),100);}else{if(recordNum!=null){this.rs.AbsolutePosition=this.rs.RecordCount;setTimeout(this.showRecCount.bind(this),100);}else{alert(Lang.dataBind.gotoInvalid);gotoField.clear();gotoField.focus();}}}else{alert(Lang.dataBind.gotoEmpty);if(recordNum==null)gotoField.focus();}};FormDataBind.prototype.disableForm=function(act){if(!this.readOnly){act=!!act;$C(this.form.elements).walk(function(item,idx){if(item.name&&item.name.substring(0,3)!='act')item.disabled=act;});}};FormDataBind.prototype.disableActions=function(act){if(!this.readOnly){act=!!act;var name,inputs=$C(this.form.getElementsByTagName('input'));inputs.walk(function(item,idx){switch(item.name){case'actNew':case'actEdit':case'actDel':case'actFilterTerm':case'actFilterBtn':case'actGotoField':case'actGotoBtn':item.disabled=act;break;case'actSave':case'actCancel':item.disabled=!act;break;}});}};FormDataBind.prototype.disableNavigation=function(act){if(!this.readOnly){var empty=(this.rs.RecordCount==0);var inputs=$C(this.form.getElementsByTagName('input'));inputs.walk(function(item,idx){if(item.name.substring(0,3)=='nav'){item.disabled=(empty?true:act);}});}};FormDataBind.prototype.showRecCount=function(current,count){this.rs=this.db.recordset;current=PHP2Go.ifUndef(current,this.rs.AbsolutePosition);count=PHP2Go.ifUndef(count,this.rs.RecordCount);var trg=$(this.formName+'_recCount');if(current>0)trg.update("Registro"+"&nbsp;&nbsp;"+current+"/"+count);else trg.update("Registro"+"&nbsp;&nbsp;0/0");};FormDataBind.prototype.fixTabIndexes=function(){var max=0,elms=this.form.elements;for(var i=0;i<elms.length;i++){if(elms[i].tabIndex&&elms[i].tabIndex>max)max=elms[i].tabIndex;}elms['actSave'].tabIndex=max+1;elms['actCancel'].tabIndex=max+2;};PHP2Go.included[PHP2Go.baseUrl+'form/formdatabind.js']=true;}