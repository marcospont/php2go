if(!PHP2Go.included[PHP2Go.baseUrl+'form/memofield.js']){MemoField=function(id,maxlength){this.ComponentField($(id),'MemoField');this.count=$(this.id+"_count");this.maxlength=maxlength;this.setup();};MemoField.extend(ComponentField,'ComponentField');MemoField.prototype.setup=function(){this.fld.component=this;this.count.auxiliary=true;Event.addListener(this.fld,'keydown',this.keyHandler.bind(this));Event.addListener(this.fld,'keyup',this.keyHandler.bind(this));};MemoField.prototype.setValue=function(val){val=val||'';this.fld.value=val.substring(0,this.maxlength);this.count.value=this.maxlength-this.fld.value.length;if(this.fld.onchange)this.fld.onchange();};MemoField.prototype.clear=function(){this.fld.value='';this.count.value=this.maxlength;};MemoField.prototype.isEmpty=function(){return(this.fld.value.trim()=='');};MemoField.prototype.keyHandler=function(e){var key=$K(e),ign='#33#34#35#36#37#38#39#40#45#4098#';if(ign.indexOf('#'+key+'#')==-1&&(key<112||key>123)){var len=this.fld.value.length;if(len>=this.maxlength){this.fld.value=this.fld.value.substring(0,this.maxlength);this.count.value=0;}else{this.count.value=this.maxlength-len;}}};PHP2Go.included[PHP2Go.baseUrl+'form/memofield.js']=true;}