if(!PHP2Go.included[PHP2Go.baseUrl+'validator.js']){Validator=function(args){this.fld=$FF(args.form,args.field);try{this.fldLabel=(this.fld.label||this.fld.name);}catch(e){}this.msg=null;};Validator.isMask=function(v,mask){switch(mask){case'DIGIT':return Validator.isDigit(v);case'INTEGER':return Validator.isInteger(v);case'FLOAT':return Validator.isFloat(v);case'CURRENCY':return Validator.isCurrency(v);case'CPFCNPJ':return Validator.isCPFCNPJ(v);case'WORD':return Validator.isWord(v);case'EMAIL':return Validator.isEmail(v);case'URL':return Validator.isURL(v);case'DATE':return Validator.isDate(v);case'TIME':return Validator.isTime(v,false);case'TIME-AMPM':return Validator.isTime(v,true);default:var m=[];if(m=/FLOAT(\-([1-9][0-9]*)\:([1-9][0-9]*))?/.exec(mask))return(m[1]?Validator.isFloat(v,m[2],m[3]):Validator.isFloat(v));else if(m=/ZIP\-?([1-9])\:?([1-9])/.exec(mask))return Validator.isZIP(v,m[1],m[2]);else return true;}return true;};Validator.isDigit=function(val){if(val=="")return true;if(!(/^[0-9]+$/.test(val)))return false;var intval=parseInt(val,10);return(intval==0||intval);};Validator.isInteger=function(val){if(val=="")return true;if(!(/^(\+|\-)?\d+$/.test(val)))return false;var intval=parseInt(val,10);return(intval==0||intval);};Validator.isFloat=function(val,intPart,decPart){if(val=="")return true;var floatval=parseFloat(val,10);if(floatval==0||floatval){intPart=intPart||"";decPart=decPart||"";return val.match(new RegExp("^\-?\\d{1,"+intPart+"}(\\.\\d{1,"+decPart+"})?$"));}return false;};Validator.isCurrency=function(val){if(val=="")return true;var size=(val.charAt(0)=='-'?val.length-1:val.length);var mod=(size-3)%4;var groups=(size-mod-3)/4;var re=new RegExp("^\-?\\d{"+mod+"}(\\.\\d{3}){"+groups+"},\\d{2}$");return re.test(val);};Validator.isCPFCNPJ=function(val){if(val=="")return true;var len=val.length;var sum1,sum2,rest,d1,d2;if(val.length==14){sum1=(val.charAt(0)*5)+(val.charAt(1)*4)+(val.charAt(2)*3)+(val.charAt(3)*2)+(val.charAt(4)*9)+(val.charAt(5)*8)+(val.charAt(6)*7)+(val.charAt(7)*6)+(val.charAt(8)*5)+(val.charAt(9)*4)+(val.charAt(10)*3)+(val.charAt(11)*2);rest=sum1%11,d1=rest<2?0:11-rest;sum2=(val.charAt(0)*6)+(val.charAt(1)*5)+(val.charAt(2)*4)+(val.charAt(3)*3)+(val.charAt(4)*2)+(val.charAt(5)*9)+(val.charAt(6)*8)+(val.charAt(7)*7)+(val.charAt(8)*6)+(val.charAt(9)*5)+(val.charAt(10)*4)+(val.charAt(11)*3)+(val.charAt(12)*2);rest=sum2%11,d2=rest<2?0:11-rest;return((val.charAt(12)==d1)&&(val.charAt(13)==d2));}else if(val.length==11){sum1=(val.charAt(0)*10)+(val.charAt(1)*9)+(val.charAt(2)*8)+(val.charAt(3)*7)+(val.charAt(4)*6)+(val.charAt(5)*5)+(val.charAt(6)*4)+(val.charAt(7)*3)+(val.charAt(8)*2);rest=sum1%11,d1=rest<2?0:11-rest;sum2=(val.charAt(0)*11)+(val.charAt(1)*10)+(val.charAt(2)*9)+(val.charAt(3)*8)+(val.charAt(4)*7)+(val.charAt(5)*6)+(val.charAt(6)*5)+(val.charAt(7)*4)+(val.charAt(8)*3)+(val.charAt(9)*2);rest=sum2%11,d2=rest<2?0:11-rest;return((val.charAt(9)==d1)&&(val.charAt(10)==d2));}else{return false;}};Validator.isWord=function(val){return(val==""||(/^\w+((-\w+)|(\.\w+))*$/.test(val)));};Validator.isEmail=function(val){var rep=val.replace(/^[^0-9a-zA-Z_\[\]\.\-@]+$/,"");return(val==""||(val==rep&&(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(val))));};Validator.isURL=function(val){return(val==""||(/^(ht|f)tps?\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z0-9\-\._]{2,3}(:[a-zA-Z0-9]*)?\/?([a-zA-Z0-9\-\._\?\,\'\/\\\+&%\$#\=~])*$/i.test(val)));};Validator.isDate=function(val){if(val=="")return true;var d,m,y,leap,m31,bm;var loc=Locale.date;var re=loc.regexp;if(mt=re.exec(val)){d=parseInt(mt[loc.matches[0]],10);m=parseInt(mt[loc.matches[1]],10);y=parseInt(mt[loc.matches[2]],10);leap=(y%4==0)&&(y%100!=0||y%400==0);m31=0xAD5,bm=(1<<(m-1));if(y<1000||m<1||m>12||d<1||d>31||(d==31&&(bm&m31)==0)||(d==30&&m==2)||(d==29&&m==2&&!leap)){return false;}else{return true;}}return false;};Validator.isTime=function(val,ampm){if(val=="")return true;ampm=!!ampm;var mt=val.match(new RegExp("^(\\d{1,2})\:(\\d{1,2})(\:(\d{1,2}))?"+(ampm?"(?:a|p)":"")));if(mt){var h=parseInt(mt[1],10);var m=parseInt(mt[2],10);var s=(mt[4]?parseInt(mt[4],10):0);return(h>=0&&h<=23&&m>=0&&m<=59&&s>=0&&s<=59);}return false;};Validator.isZIP=function(val,left,right){if(val=="")return true;left=left||5;right=right||3;return val.match(new RegExp("^\\d{"+left+"}\-\\d{"+right+"}$"));};Validator.prototype.isMandatory=function(){return(this instanceof RequiredValidator||(this instanceof RuleValidator&&!this.msg&&(/^REQIF/.test(this.ruleType))));};Validator.prototype.getErrorMessage=function(){if(!this.msg)return Lang.invalidValue;return this.msg.assignAll(this.fldLabel);};Validator.prototype.validate=function(){return true;};RequiredValidator=function(args){this.Validator(args);this.msg=Lang.validator.requiredField;};RequiredValidator.extend(Validator,'Validator');RequiredValidator.prototype.validate=function(){return(!this.fld.isEmpty());};RequiredValidator.prototype.getErrorMessage=function(){return Lang.validator.requiredField.assignAll(this.fldLabel);};DataTypeValidator=function(args){this.Validator(args);this.mask=args.mask;this.msg=args.msg;};DataTypeValidator.extend(Validator,'Validator');DataTypeValidator.prototype.validate=function(){if(this.fld.isEmpty())return true;var mask=this.fld.getMask();if(mask)mask.onBlur(this.fld.fld);return Validator.isMask(this.fld.getValue(),this.mask);};DataTypeValidator.prototype.getErrorMessage=function(){if(this.msg)return this.msg;var maskType=/^[A-Z]+/.exec(this.mask)[0];return Lang.validator.invalidDataType.assignAll(this.fldLabel,Lang.masks[maskType]);};LengthValidator=function(args){this.Validator(args);this.rule=(args.rule||"").toUpperCase();this.limit=parseInt(args.limit,10);this.msg=args.msg;};LengthValidator.extend(Validator,'Validator');LengthValidator.prototype.validate=function(){var val=this.fld.getValue();return(!val||PHP2Go.compare(val.length,this.limit,(this.rule=='MAXLENGTH'?'LOET':'GOET'),'INTEGER'));};LengthValidator.prototype.getErrorMessage=function(){if(this.msg)return this.msg;var msg=(this.rule=='MAXLENGTH'?Lang.validator.maxLengthField:Lang.validator.minLengthField);return msg.assignAll(this.fldLabel,this.limit);};RuleValidator=function(args){this.Validator(args);this.ruleType=(args.ruleType||"").toUpperCase();this.dataType=args.dataType||'STRING';this.peerValue=args.peerValue||'';this.peer=(args.peerField?$FF(args.form,args.peerField):args.peerValue);this.peerType=(args.peerField?RuleValidator.PEER_FIELD:RuleValidator.PEER_VALUE);this.peerLabel=(args.peerField&&this.peer?this.peer.label:null);this.func=args.func||$EF;this.msg=args.msg;};RuleValidator.extend(Validator,'Validator');RuleValidator.PEER_FIELD=1;RuleValidator.PEER_VALUE=2;RuleValidator.prototype.getErrorMessage=function(){if(this.msg)return this.msg;var m=Lang.validator,lbl=this.fldLabel;var v=(this.peerType==RuleValidator.PEER_VALUE);switch(this.ruleType){case'REGEX':return m.invalidField.assignAll(lbl);case'JSFUNC':return m.invalidField.assignAll(lbl);case'EQ':return(v?m.eqValue.assignAll(lbl,this.peer):m.eqField.assignAll(lbl,this.peerLabel));case'NEQ':return(v?m.neqValue.assignAll(lbl,this.peer):m.neqField.assignAll(lbl,this.peerLabel));case'GT':return(v?m.gtValue.assignAll(lbl,this.peer):m.gtField.assignAll(lbl,this.peerLabel));case'GOET':return(v?m.goetValue.assignAll(lbl,this.peer):m.goetField.assignAll(lbl,this.peerLabel));case'LT':return(v?m.ltValue.assignAll(lbl,this.peer):m.ltField.assignAll(lbl,this.peerLabel));case'LOET':return(v?m.loetValue.assignAll(lbl,this.peer):m.loetField.assignAll(lbl,this.peerLabel));default:return m.requiredField.assignAll(this.fldLabel);}};RuleValidator.prototype.compare=function(op){var trg,src=(this.fld.getValue()||'').toString();if(this.peerType==RuleValidator.PEER_FIELD){trg=(this.peer.getValue()||'').toString();if(src.trim()==""&&trg.trim()=="")return true;}else{trg=this.peer;if(src.trim()=="")return true;}try{if(this.fld.getMask()==CurrencyMask)src=src.replace(".","").replace(",",".");if(this.peerType==RuleValidator.PEER_FIELD&&this.peer.getMask()==CurrencyMask)trg=trg.replace(".","").replace(",",".");}catch(e){}return PHP2Go.compare(src,trg,op||this.ruleType,this.dataType);};RuleValidator.prototype.validate=function(){var f=this.fld,p=this.peer;switch(this.ruleType){case'REGEX':return(f.isEmpty()||p.test(f.getValue()));case'JSFUNC':return this.func(f.fld||f.grp);case'EQ':case'NEQ':case'GT':case'GOET':case'LT':case'LOET':return this.compare();case'REQIF':return(!f.isEmpty()||p.isEmpty());case'REQIFEQ':case'REQIFNEQ':case'REQIFGT':case'REQIFGOET':case'REQIFLT':case'REQIFLOET':return(!f.isEmpty()||p.isEmpty()||!PHP2Go.compare(p.getValue(),this.peerValue,this.ruleType.substring(5),this.dataType));}};FormValidator=function(frm){this.frm=$(frm)||document.forms[frm];this.validators=[];this.messages=[];this.emptyLabels=[];this.errorDisplayOptions={header:Lang.validator.invalidFields,mode:FormValidator.MODE_ALERT,list:FormValidator.LIST_FLOW,showAll:true,target:null,nl:"\n",ls:"---------------------------------------------------------------------------------\n"};};FormValidator.MODE_ALERT=1;FormValidator.MODE_DHTML=2;FormValidator.LIST_FLOW=1;FormValidator.LIST_BULLET=2;FormValidator.prototype.setErrorDisplayOptions=function(mode,target,showAll,list,header){var opt=this.errorDisplayOptions;opt.showAll=!!showAll;if(mode==FormValidator.MODE_DHTML){var trg=$(target);if(trg){opt.mode=mode;opt.ls="";opt.nl="<br />";opt.target=trg;if(list==FormValidator.LIST_FLOW||list==FormValidator.LIST_BULLET)opt.list=list;}}else if(mode==FormValidator.MODE_ALERT){opt.mode=mode;opt.ls="----------------------------------------------------------------------------\n";opt.nl="\n";}if(header!=null)opt.header=header;};FormValidator.prototype.add=function(field,validator,args){if(this.frm&&typeof(validator)=='function'){args=args||{};args.form=this.frm;args.field=field;this.validators.push(new validator(args));}};FormValidator.prototype.setup=function(){var frm=this.frm;frm.validator=this;if(!frm.ajax){Event.addListener(frm,'submit',function(e){frm.validator.run(e);});}Event.addListener(frm,'reset',function(e){frm.validator.clearErrors();});};FormValidator.prototype.onBeforeValidate=function(self){};FormValidator.prototype.onAfterValidate=function(self){return true;};FormValidator.prototype.run=function(e){e=$EV(e);this.messages=[];this.emptyLabels=[];var res=true,valid=true;var ept,inv,items=this.validators;this.onBeforeValidate(this,this.frm);for(var i=0;i<items.length;i++){valid=items[i].validate();res=res&&valid;if(!valid){this.messages.push(items[i].getErrorMessage());if(!inv){inv=items[i].fld;(e&&e.preventDefault());}if(!this.errorDisplayOptions.showAll)break;if(items[i].isMandatory()){this.emptyLabels.push(items[i].fldLabel);(!ept)&&(ept=items[i].fld);}}}if(res&&!this.onAfterValidate(this)){res=false;(e&&e.preventDefault());}if(!res){(!this.emptyLabels.empty()||!this.messages.empty())&&(this.showErrors());(ept||inv)&&((ept||inv).focus());return false;}else{this.clearErrors();}return true;};FormValidator.prototype.buildErrors=function(){var buf="",m=Lang.validator,opt=this.errorDisplayOptions;var bullets=(opt.list==FormValidator.LIST_BULLET);var dhtml=(opt.mode==FormValidator.MODE_DHTML);if(!opt.showAll)return this.messages[0];if(this.emptyLabels.length>0){if(dhtml){if(opt.header!=""){buf+=opt.header;if(!bullets)buf+=opt.nl;}if(bullets)buf+="<ul>";this.emptyLabels.walk(function(item,idx){if(bullets)buf+="<li>"+m.requiredField.assignAll(item)+"</li>";else buf+=m.requiredField.assignAll(item)+opt.nl;});if(bullets)buf+="</ul>";buf+=opt.ls;}else{buf+=m.requiredFields+opt.nl+opt.ls;this.emptyLabels.walk(function(item,idx){buf+=item+opt.nl;});buf+=opt.ls+m.completeFields;}}else{if(dhtml){if(opt.header!=""){buf+=opt.header;if(!bullets)buf+=opt.nl;}if(bullets)buf+="<ul>";this.messages.walk(function(item,idx){if(bullets)buf+="<li>"+item+"</li>";else buf+=item+opt.nl;});if(bullets)buf+="</ul>";buf+=opt.ls;}else{if(opt.header!="")buf+=opt.header.stripTags()+opt.nl+opt.ls;this.messages.walk(function(item,idx){buf+=item+opt.nl;});buf+=opt.ls+m.fixFields;}}return buf;};FormValidator.prototype.showErrors=function(){var opt=this.errorDisplayOptions;if(opt.mode==FormValidator.MODE_ALERT){alert(this.buildErrors());}else{var trg=$(opt.target);if(trg){trg.update(this.buildErrors());trg.show();window.scrollTo(0,trg.getPosition().y);}}};FormValidator.prototype.clearErrors=function(){var opt=this.errorDisplayOptions;if(opt.mode==FormValidator.MODE_DHTML){var trg=$(opt.target);if(trg){trg.clear();trg.hide();}}};PHP2Go.included[PHP2Go.baseUrl+'validator.js']=true;}