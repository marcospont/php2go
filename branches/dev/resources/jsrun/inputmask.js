if(!PHP2Go.included[PHP2Go.baseUrl+'inputmask.js']){PHP2Go.include(PHP2Go.baseUrl+'form.js');InputMask=function(fld,mask){this.fld=fld;this.mask=mask;this.fs=FieldSelection;this.caret=0;this.selSize=0;this.lastValue=this.fld.value;this.ignore=false;this.valid=true;this.pressing=false;this.paste=false;this.ctrl=false;this.shift=false;if(this.mask){this.addListeners();if(maxLength=this.mask.getMaxLength())this.fld.maxLength=maxLength;}};InputMask.ignoreCodes='#33#34#35#36#37#38#39#40#45#127#4098#';InputMask.setup=function(fld,mask){fld=$(fld);if(fld){if(mask instanceof Mask){fld.inputMask=new InputMask(fld,mask);}else{fld.inputMask=new InputMask(fld,Mask.fromExpression(mask));}}};InputMask.prototype.addListeners=function(){var f=this.fld,self=this;Event.addListener(f,'keydown',this.keyDownHandler.bind(this));Event.addListener(f,'keypress',this.keyPressHandler.bind(this));Event.addListener(f,'keyup',this.keyUpHandler.bind(this));Event.addListener(f,'blur',this.blurHandler.bind(this));if(f.onpaste===null){Event.addListener(f,'paste',function(e){setTimeout(function(){if(!self.pressing)self.pasteHandler(e);},10);});}else{Event.addListener(f,'input',function(e){if(!self.pressing)self.pasteHandler(e);});}};InputMask.prototype.keyDownHandler=function(e){var k=$K(e);this.pressing=true;this.caret=this.fs.getCaret(this.fld);this.selSize=this.fs.getRange(this.fld).size;this.lastValue=this.fld.value;this.paste=((this.ctrl&&k==86)||(this.shift&&k==45));this.ignore=(!this.paste&&(k<32||(k>=112&&k<=123)||InputMask.ignoreCodes.indexOf('#'+k+'#')!=-1||(k==46&&!PHP2Go.browser.opera&&!PHP2Go.browser.khtml)));this.valid=this.mask.onKeyDown(this.fld,this.caret,k);(k==17)&&(this.ctrl=true);(k==16)&&(this.shift=true);};InputMask.prototype.keyPressHandler=function(e){var e=e||window.event;var k=$K(e);var c=String.fromCharCode(k);if(this.valid){this.valid=(this.ignore||e.ctrlKey);if(!this.valid)this.valid=this.mask.accept(c,this.caret,this.selSize,this.fld.maxLength);}if(!this.valid&&!this.paste)(e.preventDefault?e.preventDefault():e.returnValue=false);};InputMask.prototype.keyUpHandler=function(e){var k=$K(e),f=this.fld,c=this.caret;this.pressing=false;if(f.value!=this.lastValue||!this.ignore)this.update(e||window.event);(k==17)&&(this.ctrl=false);(k==16)&&(this.shift=false);var newCaret=(this.ignore||!this.valid?false:this.mask.getCaretPosition(this.lastValue,f.value,c,k));if(newCaret!==false){this.fs.setCaret(f,newCaret);}else if(c<f.value.length){if((k==46&&!PHP2Go.browser.opera&&!PHP2Go.browser.khtml)||(k==127&&PHP2Go.browser.khtml))this.fs.setCaret(f,c);else if(k==8)this.fs.setCaret(f,c-1);else if(PHP2Go.browser.opera&&c==this.lastValue.length&&this.valid)this.fs.setCaret(f,f.value.length);}return true;};InputMask.prototype.pasteHandler=function(e){this.update(e||window.event);};InputMask.prototype.blurHandler=function(e){this.update(e||window.event,true);};InputMask.prototype.update=function(e,isBlur){isBlur=!!isBlur;this.mask.apply(this.fld);(isBlur)&&(this.mask.onBlur(this.fld));if(this.fld.value!=this.lastValue&&this.fld.onchange){var evt={};if(!Object.isUndef(e)){for(var p in e)evt[p]=e[p];}evt.type='change';this.fld.onchange(evt);}};Mask=function(){this.fields=[];};Mask.fromExpression=function(expr,d){var mask=new Mask();mask.loadExpression(expr,d);return mask;};Mask.cache={};Mask.fromMaskName=function(name){if(Mask.cache[name])return Mask.cache[name];switch(name){case'DIGIT':Mask.cache[name]=DigitMask;break;case'INTEGER':Mask.cache[name]=IntegerMask;break;case'CURRENCY':Mask.cache[name]=CurrencyMask;break;case'CPFCNPJ':Mask.cache[name]=CPFCNPJMask;break;case'WORD':Mask.cache[name]=WordMask;break;case'EMAIL':Mask.cache[name]=EmailMask;break;case'URL':Mask.cache[name]=URLMask;break;case'DATE':Mask.cache[name]=DateMask;break;case'TIME':Mask.cache[name]=TimeMask(false);break;case'TIME-AMPM':Mask.cache[name]=TimeMask(true);break;default:var m=[];if(m=/FLOAT(\-([1-9][0-9]*)\:([1-9][0-9]*))?/.exec(name))Mask.cache[name]=(m[1]?FloatMask(m[2],m[3]):FloatMask());else if(m=/ZIP\-?([1-9])\:?([1-9])/.exec(name))Mask.cache[name]=ZIPMask(m[1],m[2]);else Mask.cache[name]=NullMask;}return Mask.cache[name];};Mask.prototype.loadExpression=function(expr){var ex=PHP2Go.raiseException;var map={'#':'0-9','A':'a-zA-Z','L':'a-z','P':'\.\,','U':'A-Z','W':'0-9A-Za-z_'};var c,lit,cc="",cm="",state=0,m;try{for(var i=0;i<expr.length;i++){c=expr.charAt(i);if(c=='?'){if(i==0)ex(Lang.inputMask.optionalCharFirst);if(state==1){this.addField(cc,0,1);cc="";state=0;}continue;}if(c=="\\"){if(state==1){this.addField(cc,1,1);cc="";state=0;}else if(state==2){this.addField(map[cm.charAt(0)],cm.length,cm.length);cm="";state=0;}if(i<(expr.length-1)){lit=expr.charAt(++i);if(cc!=""){this.addField(cc,1,1);cc="";state=0;}this.addLiteral(lit,(expr.charAt(i+1)=='?'));continue;}else{ex(Lang.inputMask.escapeCharLast);}}if(c=='['){if(state==1){this.addField(cc,1,1);cc="";state=0;}else if(state==2){this.addField(map[cm.charAt(0)],cm.length,cm.length);cm="";state=0;}while(i<expr.length&&(c=expr.charAt(++i))!=']'){if(c=='\\'){if(i<(expr.length-1)){cc+='\\'+expr.charAt(++i);continue;}else{ex(Lang.inputMask.escapeCharLast);}}if(c=='[')ex(Lang.inputMask.invalidCharClass);if(map[c])cc+=map[c];else cc+=c;}if(c==']'){state=1;continue;}ex(Lang.inputMask.invalidCharClass);}if(c=='*'||c=='+'){if(state==1){this.addField(cc,(c=='*'?0:1),-1);cc="";state=0;}else{this.addLiteral(c,(expr.charAt(i+1)=='?'));}continue;}if(c=='{'){if(state==1){buf="";while(i<expr.length&&(c=expr.charAt(++i))!='}'){if(c=='{')ex(Lang.inputMask.invalidLimits);buf+=c;}if(c=='}'&&(m=/^([0-9]+)(,([0-9]+)?)?$/.exec(buf))){var min=parseInt(m[1],10);var max=(m[3]?parseInt(m[3],10):(m[2]?-1:min));if(min>=0){this.addField(cc,min,max);cc="";state=0;continue;}}}ex(Lang.inputMask.invalidLimits);}if(map[c]){if(state==1){this.addField(cc,1,1);cc="";state=0;}else if(state==2){if(c!=cm.charAt(0)){this.addField(map[cm.charAt(0)],cm.length,cm.length);cm=c;}else{cm+=c;}}else{cm=c;state=2;}}else{if(state==1){this.addField(cc,1,1);cc="";state=0;}else if(state==2){this.addField(map[cm.charAt(0)],cm.length,cm.length);cm="";state=0;}this.addLiteral(c,(expr.charAt(i+1)=='?'));}}if(state==1)this.addField(cc,1,1);if(state==2)this.addField(map[cm.charAt(0)],cm.length,cm.length);}catch(e){alert(e.message);}};Mask.prototype.addField=function(chars,min,max){min=parseInt(min,10);max=parseInt(max,10);var idx=this.fields.length;this.fields.push({idx:idx,literal:false,value:"",filled:false,positive:new RegExp("["+chars+"]"),negative:new RegExp("[^"+chars+"]+","g"),min:min,max:max,optional:(min==0)});};Mask.prototype.addLiteral=function(c,opt){var idx=this.fields.length;this.fields.push({idx:idx,literal:true,value:String(c).charAt(0),filled:false,optional:!!opt});};Mask.prototype.getMaxLength=function(){var len=0;for(var i=0,f=this.fields;i<f.length;i++){if(f[i].literal)len++;else if(f[i].max!=-1)len+=f[i].max;else return false;}return len;};Mask.prototype.getMaxPosition=function(idx,max){var fld=this.fields[idx];if(fld.literal){return(!fld.optional||fld.filled?1:0);}else{var fldNext=this.fields[idx+1]||null;if(fld.max!=-1){if(fld.min<fld.max&&fldNext&&((fldNext.literal&&fldNext.filled)||(!fldNext.literal&&fldNext.value!="")))return fld.value.length+1;return fld.max;}else if(fldNext&&((fldNext.literal&&fldNext.filled)||(!fldNext.literal&&fldNext.value!=""))){return fld.value.length+1;}else{return parseInt(max,10);}}};Mask.prototype.getCaretPosition=function(oldVal,newVal,caret,key){return false;};Mask.prototype.isOnlyFilter=function(){var f=this.fields;return(f.length==1&&!f[0].literal&&f[0].max==-1);};Mask.prototype.isComplete=function(){var f=this.fields;for(var i=0;i<f.length;i++){if((f[i].literal&&!f[i].optional&&!f[i].filled)||(f[i].value.length<f[i].min))return false;}return true;};Mask.prototype.accept=function(chr,caretPos,selSize,maxLen){var fld,flds=this.fields;var minPos=maxPos=0;var partial=false;if(this.isOnlyFilter())return flds[0].positive.test(chr);for(var i=0;i<flds.length;i++){fld=flds[i];if(fld.literal){if(selSize>1)return true;if((partial&&caretPos<=maxPos)||caretPos==maxPos){if(chr==fld.value&&!fld.filled)return true;}maxPos+=this.getMaxPosition(i,maxLen);continue;}else{minPos=maxPos;maxPos+=this.getMaxPosition(i,maxLen);if(caretPos<maxPos){if(selSize>0||fld.max==-1||fld.value.length<fld.max){if(caretPos<minPos&&fld.value!="")return false;if(fld.positive.test(chr))return true;if(fld.min==fld.max)return false;}}partial=false;if(fld.max!=-1&&fld.min<fld.max){maxPos=minPos+fld.value.length;partial=true;}else if(fld.max==-1&&fld.value.length>=fld.min){partial=true;}continue;}}return false;};Mask.prototype.apply=function(fld){if(this.isOnlyFilter())this.filter(fld);else this.format(fld);};Mask.prototype.onKeyDown=function(fld,caret,key){return true;};Mask.prototype.onBeforeChange=function(val){return val;};Mask.prototype.onAfterChange=function(val){return val;};Mask.prototype.onBlur=function(fld){};Mask.prototype.filter=function(field){var newVal=field.value,f=this.fields;newVal=this.onBeforeChange(newVal);newVal=f[0].value=newVal.replace(f[0].negative,"");newVal=this.onAfterChange(field.maxLength!=-1?newVal.substring(0,field.maxLength):newVal);if(newVal!=field.value)field.value=newVal;};Mask.prototype.format=function(field){var maxPos=chrIdx=fldIdx=0;var chr=newVal="",shift=false;var pendingLiterals=[],val=field.value;var f,flds=this.fields;for(var i=0;i<flds.length;i++){flds[i].filled=false;if(!flds[i].literal)flds[i].value="";}val=this.onBeforeChange(val);for(var i=0;i<val.length;i++){pendingLiterals=[],chr=val.charAt(i);for(var j=0;j<flds.length;j++){f=flds[j];if(!f.literal){shift=true;maxPos+=this.getMaxPosition(j,field.maxLength);if(chrIdx<maxPos&&j>=fldIdx){if(f.max==-1||f.value.length<f.max){if(f.positive.test(chr)){for(var k=i+1,l=1;k<val.length;k++,l++){if(flds[j-l]&&flds[j-l].literal&&flds[j-l].optional&&!flds[j-l].filled&&val.charAt(k)==flds[j-l].value.charAt(0)){newVal+=flds[j-l].value;flds[j-l].filled=true;continue;}break;}for(k=0;k<pendingLiterals.length;k++){newVal+=flds[pendingLiterals[k]].value;flds[pendingLiterals[k]].filled=true;}f.value+=chr;newVal+=chr;chrIdx++;fldIdx=j;if(f.value.length>=f.min)shift=false;if(f.max!=-1&&f.value.length==f.max){f.filled=true;fldIdx=j+1;}break;}}else{shift=false;continue;}}if(f.value.length>=f.min){shift=false;continue;}else{break;}}else{maxPos+=this.getMaxPosition(j,field.maxLength);if(j>=fldIdx&&!f.filled){if(chrIdx<=maxPos&&chr==f.value.charAt(0)&&pendingLiterals.length==0){if(!shift){newVal+=f.value;f.filled=true;chrIdx++;fldIdx=j+1;}break;}else if(!f.optional){pendingLiterals.push(j);}}}}}newVal=this.onAfterChange(newVal.substring(0,field.maxLength));if(newVal!=field.value)field.value=newVal;};Mask.prototype.serialize=function(){return this.fields.serialize();};var DigitMask=Mask.fromExpression("[#]+");var IntegerMask=Mask.fromExpression("-?[#]+");var FloatMask=function(intPart,decPart){var mask=new Mask();mask.intSize=Math.max(parseInt(intPart,10),1);mask.decSize=Math.max(parseInt(decPart,10),1);mask.loadExpression((mask.intSize>0&&mask.decSize>0?"-?[#]{1,"+mask.intSize+"}P[#]{0,"+mask.decSize+"}":"-?[#]+P[#]*"));mask.onBeforeChange=function(val){return val.replace(',','.');};return mask;};var CurrencyMask=function(){var mask=new Mask();mask.timeout=null;mask.loadExpression("-?[#\.\,]+");mask.onKeyDown=function(f,c,k){if(k==110||k==194||k==188||k==190)return false;clearTimeout(this.timeout);this.timeout=this.applyFormat.bind(this).delay(40,f,k);return true;};mask.applyFormat=function(f,k){var before=f.value,caret=FieldSelection.getCaret(f);var tmp=before.replace(/[^0-9]+/g,''),neg=(before.charAt(0)=='-');var isNum=((k>=48&&k<=57)||(k>=96&&k<=105));if(tmp.length>2){var grp='',intp='',decp='',after='';decp=tmp.substring(tmp.length-2);tmp=tmp.substring(0,tmp.length-2);while(tmp!=""){grp=(tmp.length>3?tmp.substring(tmp.length-3):tmp);tmp=tmp.substring(0,tmp.length-grp.length);intp=grp+'.'+intp;}intp=intp.substring(0,intp.length-1);after=(neg?'-':'')+intp+','+decp;if(after!=f.value){f.value=after;var left=before.substring(0,caret).replace(/[\.,]+/g,"");var c,n=0,validChars=left.length+(isNum?1:(k==8?-1:0));for(var i=0;i<after.length;i++){c=after.charAt(i);if(c=='-'||c=='.'||c==',')continue;if(++n==validChars)FieldSelection.setCaret(f,i+1);}}}else if(tmp!=f.value){f.value=(neg?'-':'')+tmp;}};mask.onBlur=function(f){v=f.value;if(v!=""&&v.length<4){if(v.length==1&&v.charAt(0)=='-')f.value="";else if(v.length<=2||(v.length==3&&v.charAt(0)=='-'))f.value+=',00';}};return mask;}();var CPFCNPJMask=Mask.fromExpression("[#]{11,14}");var WordMask=Mask.fromExpression("[W\\.\\-]+");var EmailMask=Mask.fromExpression("[W\\.\\-\\[\\]@]+");var URLMask=Mask.fromExpression("[W\\.\\-\\[\\]@\\:=&;\\+\\/\\?%\\$\\#~]+");var DateMask=Mask.fromExpression(Locale.date.mask);DateMask.onBeforeChange=function(val){var f=Locale.date.type;if(f=='EURO'||f=='US')return val.replace(/[-\.]/,'/');return val.replace(/[\/\.]/,'-');};DateMask.onBlur=function(fld){var m=[],f=Locale.date.type;if(f=='EURO'||f=='US'){if(m=/^([0-9]{2}\/[0-9]{2}\/)([0-9]{2})$/.exec(fld.value))fld.value=m[1]+(parseInt(m[2])>50?'19'+m[2]:'20'+m[2]);}else{if(m=/^([0-9]{2})(\-[0-9]{2}\-[0-9]{2})$/.exec(fld.value))fld.value=(parseInt(m[1])>50?'19'+m[1]:'20'+m[1])+m[2];}};var TimeMask=function(ampm){var mask=new Mask();mask.loadExpression(!!ampm?"##:##[ap]":"##:##");mask.onBeforeChange=function(val){return val.replace("A","a").replace("P","p");};return mask;};var ZIPMask=function(left,right){left=Math.max(parseInt(left,10),1);right=Math.max(parseInt(right,10),1);var mask=new Mask();mask.loadExpression("[#]{"+left+"}-[#]{"+right+"}");return mask;};var NullMask=function(){var mask=new Mask();mask.accept=function(c,p,s,m){return true;};mask.apply=$EF;return mask;}();PHP2Go.included[PHP2Go.baseUrl+'inputmask.js']=true;}