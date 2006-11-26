if(!PHP2Go.included[PHP2Go.baseUrl+'form/datepickerfield.js']){PHP2Go.include(PHP2Go.baseUrl+'vendor/jscalendar/calendar_stripped.js');PHP2Go.include(PHP2Go.baseUrl+'vendor/jscalendar/calendar-setup_stripped.js');DatePickerField=function(id,options){this.ComponentField($(id),'DatePickerField');this.calendar=null;this.container=$(id+'_calendar');this.options=options;this.setup();};DatePickerField.extend(ComponentField,'ComponentField');DatePickerField.prototype.setup=function(){var self=this;this.fld.component=this;this.options.flat=this.container.id;var func=this.options.statusFunc;this.options.dateStatusFunc=function(date){if(self.fld.disabled)return true;return(window[func]?window[func](date):false);};if(this.options.multiple){var ds,min="99999999";if(this.options.multiple.length>0){this.options.multiple=this.options.multiple.map(function(item,idx){var ret=Date.fromString(item);var ds=ret.print("%Y%m%d");if(ds<min){min=ds;self.options.date=ret.print("%Y/%m/%d %H:%M:%S");}return ret;});}this.options.onMultiple=this.multiSelectHandler.bind(this);}else{this.options.onSelect=this.selectHandler.bind(this);}Event.addListener(this.fld.form,'reset',function(evt){var dt=null,vals=[];if(self.options.multiple){self.calendar.multiple={};for(var i=0;i<self.options.multiple.length;i++){dt=self.options.multiple[i];self.calendar.multiple[dt.print("%Y%m%d")]=dt;vals.push(dt.print(self.calendar.dateFormat));}self.calendar.setDate(self.options.date?new Date(self.options.date):new Date());self.fld.value=vals.join(self.options.dateSep);}else{self.calendar.setDate(self.options.date?new Date(self.options.date):new Date());self.fld.value=(self.options.date?(new Date(self.options.date)).print(self.calendar.dateFormat):"");}self.raiseEvent('change');});Event.onDOMReady(function(){self.calendar=Calendar.setup(self.options);});};DatePickerField.prototype.getValue=function(){if(this.options.multiple)return(this.fld.value!=""?this.fld.value.split(this.options.dateSep):null);return this.fld.value;};DatePickerField.prototype.setValue=function(val){if(this.options.multiple){this.calendar.multiple={};var ds,min="99999999",dates=$A(val),dateVals=[];for(var i=0;i<dates.length;i++){dates[i]=Date.fromString(dates[i]);dateVals.push(dates[i].print(this.calendar.dateFormat));ds=dates[i].print("%Y%m%d");if(ds<min){min=ds;this.calendar.date=dates[i];}this.calendar.multiple[ds]=dates[i];}this.fld.value=dateVals.join(this.options.dateSep);}else{this.calendar.date=Date.fromString(val);this.fld.value=this.calendar.date.print(this.calendar.dateFormat);}this.calendar.refresh();this.raiseEvent('change');};DatePickerField.prototype.clear=function(){this.fld.value='';if(this.options.multiple)this.calendar.multiple={};this.calendar.dateStr=null;this.calendar.refresh();};DatePickerField.prototype.enable=function(){this.setDisabled(false);};DatePickerField.prototype.disable=function(){this.setDisabled(true);};DatePickerField.prototype.setDisabled=function(b){this.fld.disabled=b;this.calendar.refresh();};DatePickerField.prototype.focus=$EF;DatePickerField.prototype.serialize=function(){if(this.fld.value.trim()=='')return null;if(this.options.multiple){var self=this;return this.fld.value.split(this.options.dateSep).map(function(item,idx){return self.name+'[]='+item.urlEncode();}).join('&');}else{return this.name+'='+this.fld.value;}};DatePickerField.prototype.selectHandler=function(cal,date){this.fld.value=date;this.raiseEvent('click');this.raiseEvent('change');};DatePickerField.prototype.multiSelectHandler=function(cal,date,selected){var dts=[];for(var ds in cal.multiple)dts.push(cal.multiple[ds].print(cal.dateFormat));this.fld.value=dts.join(this.options.dateSep);this.raiseEvent('click');this.raiseEvent('change');};PHP2Go.included[PHP2Go.baseUrl+'form/datepickerfield.js']=true;}