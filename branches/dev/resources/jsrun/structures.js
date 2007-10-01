if(!PHP2Go.included[PHP2Go.baseUrl+'structures.js']){var $break={};var $continue={};var Collection={some:function(filter,scope){var res=false;this.walk(function(item,idx){if(res=!!filter.apply(scope||null,[item,idx]))throw $break;});return res;},every:function(filter,scope){var res=true;this.walk(function(item,idx){if(!filter.apply(scope||null,[item,idx])){res=false;throw $break;}});return res;},accept:function(filter,scope){var res=[];this.walk(function(item,idx){if(filter.apply(scope||null,[item,idx]))res.push(item);});return res;},reject:function(filter,scope){var res=[];this.walk(function(item,idx){if(!filter.apply(scope||null,[item,idx]))res.push(item);});return res;},valid:function(filter,scope){var res=[],v=null;this.walk(function(item,idx){v=filter.apply(scope||null,[item,idx]);if(v!=null)res.push(v);});return res;},grep:function(pattern){var str,res=[];var re=(Object.isString(pattern)?new RegExp(pattern):pattern);this.walk(function(item,idx){str=(item.toString?item.toString():String(item));if(str.match(pattern))res.push(item);});return res;},contains:function(obj){if(Object.isFunc(this.indexOf))return(this.indexOf(obj)!=-1);var found=false;this.walk(function(item,idx){if(item===obj){found=true;throw $break;}});return found;},extract:function(property){var res=[];this.walk(function(item,idx){if(!Object.isUndef(item[property]))res.push(item[property]);});return res;},inject:function(memo,iterator,scope){this.walk(function(item,idx){memo=iterator.apply(scope||null,[memo,item,idx]);});return memo;},invoke:function(method){var args=Array.prototype.slice.call(arguments,1);this.walk(function(item,idx){(item[method])&&(item[method].apply(item,args));});},map:function(iterator,scope){var res=[];this.walk(function(item,idx){res.push(iterator.apply(scope||null,[item,idx]));});return res;},walk:function(iterator){if(Object.isFunc(iterator)){var idx=0;try{this.each(function(item){iterator(item,idx++);});}catch(e){if(e!=$break)throw e;}}}};$C=function(o,assoc){assoc=!!assoc;if(o.walk&&o.each)return o;else if(!o)o=(assoc?{}:[]);if(assoc){o={data:o};o.each=Hash.each;}else{o.each=Array.prototype.each;}Object.extend(o,Collection,false);return o;};var Hash={each:function(iterator){for(key in this.data){var value=this.data[key];iterator({key:key,value:value});}},getKeys:function(){return this.extract('key');},getValues:function(){return this.extract('value');},containsKey:function(key){return(!!this.data[key]);},set:function(key,value){this.data[key]=value;},unset:function(key){delete this.data[key];},findKey:function(value){var key=null;this.walk(function(item){if(item.value===value){key=item.key;throw $break;}});return key;},findValue:function(key){var value=null;this.walk(function(item){if(item.key===key){value=item.value;throw $break;}});return value;},assign:function(target){this.each(function(item){target[item.key]=item.value;});},toQueryString:function(){return this.map(function(pair){return pair.key.urlEncode()+"="+String(pair.value).urlEncode();}).join('&');},serialize:function(){return'{'+this.map(function(pair){return pair.key+" : "+Object.serialize(pair.value);}).join(', ')+'}';},valueOf:function(iterable){var h=new Object();h.data=iterable||{};Object.extend(h,Hash);return h;}};Object.extend(Hash,Collection);$H=function(obj){return Hash.valueOf(obj);};Array.valueOf=function(iterable){if(!iterable)return[];if(iterable.toArray)return iterable.toArray();if(Object.isUndef(iterable.length))iterable=[iterable];var res=[];for(var i=0;i<iterable.length;i++)res.push(iterable[i]);return res;};if(!Array.prototype.push){Array.prototype.push=function(){var a=arguments;for(var i=0;i<a.length;i++)this[this.length]=a[i];return this.length;};}if(!Array.prototype.pop){Array.prototype.pop=function(){if(this.length>0)return this[this.length-1];};}if(!Array.prototype.shift){Array.prototype.shift=function(){var res=this[0];for(var i=0;i<this.length-1;i++)this[i]=this[i+1];this.length--;return res;};}if(!Array.prototype.unshift){Array.prototype.unshift=function(){var a=arguments;for(var i=a.length;i<this.length;i++)this[i]=this[i-1];for(var i=0;i<a.length;i++)this[i]=a[i];this.length+=len;};}if(PHP2Go.browser.opera){Array.prototype.concat=function(){var res=[];for(var i=0,len=this.length;i<len;i++)res.push(this[i]);for(var i=0,len=arguments.length;i<len;i++){if(Object.isArray(arguments[i])){for(var j=0,alen=arguments[i].length;j<alen;j++)res.push(arguments[i][j]);}else{res.push(arguments[i]);}}return res;};}if(!Array.prototype.indexOf){Array.prototype.indexOf=function(obj,idx){var len=this.length;var idx=(idx<0?idx+len:idx||0);for(;idx<len;idx++){if(this[idx]===obj)return idx;}return-1;};}if(!Array.prototype.lastIndexOf){Array.prototype.lastIndexOf=function(obj,idx){var len=this.length;var idx=(isNaN(idx)?len:(idx<0?idx+len:(idx>=len?len-1:idx)));for(;idx>-1;idx--)if(this[idx]===obj)return idx;return-1;};}if(!Array.prototype.forEach){Array.prototype.each=function(iterator){for(var i=0,len=this.length;i<len;i++)iterator(this[i]);};}else{Array.prototype.each=Array.prototype.forEach;}Array.prototype.first=function(){return this[0];};Array.prototype.last=function(){return this[this.length-1];};Array.prototype.include=function(item){if(this.indexOf(item)==-1)this.push(item);return this;};Array.prototype.remove=function(item){var i=0,len=this.length;while(i<len){if(this[i]===item){this.splice(i,1);len--;}else{i++;}}return this;};Array.prototype.empty=function(){return(this.length==0);};Array.prototype.clear=function(){this.length=0;return this;};Array.prototype.clone=function(){return[].concat(this);};Array.prototype.serialize=function(){return'['+this.map(Object.serialize).join(', ')+']';};Array.implement(Collection);if(Array.prototype.filter)Array.prototype.accept=Array.prototype.filter;$A=function(o){if(o&&o.constructor==Array)return o;return Array.valueOf(o);};PHP2Go.included[PHP2Go.baseUrl+'structures.js']=true;}