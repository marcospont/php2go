if(!PHP2Go.included[PHP2Go.baseUrl+'form/multicolumnlookupfield.js']){MultiColumnLookupField=function(id,height,style){this.ComponentField($(id),'MultiColumnLookupField');this.container=$(id+'_container');this.text=$(id+'_text');this.btn=$(id+'_button');this.tblContainer=$(id+'_tableContainer');this.tbl=this.tblContainer.getElementsByTagName('table')[0];this.height=height||150;this.style={normal:(style||{}).normal||'mclookupNormal',selected:(style||{}).selected||'mclookupSelected',hover:(style||{}).hover||'mclookupHover'};this.size=this.fld.options.length;this.selectedIndex=(this.isEmpty()?-1:this.fld.selectedIndex);this.setup();};MultiColumnLookupField.extend(ComponentField,'ComponentField');MultiColumnLookupField.prototype.setup=function(){var self=this;var f=this.fld,t=this.text;var b=this.btn,c=this.container;f.component=b.component=t.component=this;f.hide();t.auxiliary=true;t.style.border='none';t.style.paddingLeft='5px';b.style.width='18px';b.style.height='19px';b.style.verticalAlign='top';b.firstChild.style.verticalAlign='middle';c.style.border='1px solid ThreeDShadow';c.style.display='block';Event.addLoadListener(function(){c.style.width=(t.offsetWidth+b.offsetWidth+(PHP2Go.browser.ie?2:0))+'px';});Event.addListener(self.btn,'focus',function(e){self.raiseEvent('focus');});Event.addListener(self.btn,'keydown',function(e){self.keyHandler(e);});Event.addListener(self.btn,'click',function(e){self.raiseEvent('click');self.toggleDisplay();});Event.addListener(self.text,'keydown',function(e){self.keyHandler(e);});Event.addListener(self.text,'click',function(e){self.raiseEvent('click');self.toggleDisplay();});Event.addListener(self.tblContaier,'keydown',function(e){self.keyHandler(e);});$C(self.tbl.getElementsByTagName('tr')).walk(function(item,idx){item.index=idx;Event.addListener(item,'mouseover',function(e){self.cellHoverHandler($EV(e));});Event.addListener(item,'mouseout',function(e){self.cellHoverHandler($EV(e));});Event.addListener(item,'click',function(e){self.cellClickHandler($EV(e));});});if(this.selectedIndex>=1){this.text.value=this.fld.options[this.selectedIndex].text;this.tbl.rows[this.selectedIndex].className=this.style.selected;}};MultiColumnLookupField.prototype.getValue=function(){var idx=this.fld.selectedIndex;if(idx>=0&&this.fld.options[idx].value!="")return this.fld.options[idx].value;return null;};MultiColumnLookupField.prototype.setValue=function(val){var self=this;$C(this.fld.options).walk(function(item,idx){if(item.value==val){self.setValueByIndex(idx);throw $break;}});};MultiColumnLookupField.prototype.setValueByIndex=function(idx){var rows=this.tbl.rows;if(this.selectedIndex>=1)rows[this.selectedIndex].className=this.style.normal;this.fld.options[idx].selected=true;this.selectedIndex=this.fld.selectedIndex=idx;this.text.value=this.fld.options[idx].text;rows[idx].className=this.style.selected;this.raiseEvent('change');};MultiColumnLookupField.prototype.clear=function(){$C(this.fld.options).walk(function(item,idx){item.selected=false;});this.fld.selectedIndex=-1;this.selectedIndex=null;this.text.value='';};MultiColumnLookupField.prototype.clearOptions=function(){var fst=this.tbl.firstChild.firstChild;this.tbl.update("<tr class='"+this.style.normal+"'>"+fst.innerHTML+"</tr>");this.txt.value='';this.fld.options.length=1;this.fld.selectedIndex=-1;this.selectedIndex=null;};MultiColumnLookupField.prototype.importOptions=function(str,lsep,csep,pos){lsep=(lsep||'|'),csep=(csep||'~');pos=Math.abs(pos||0)+1;if(pos<=this.fld.options.length){var self=this;var unselect=false;str.split(lsep).walk(function(el,idx){var opt=el.split(csep);if(opt.length>=2){self.fld.options[pos]=new Option(opt[1],opt[0]);opt.shift();if(pos==self.selectedIndex)unselect=true;if(pos<self.tbl.rows.length){var row=self.tbl.rows[pos];opt.walk(function(item,idx){row.cells[idx].innerHTML=item;});}else{var row=self.tbl.insertRow(pos);row.className=self.style.normal;row.index=pos;opt.walk(function(item,idx){var cell=row.insertCell(idx);cell.noWrap=true;cell.innerHTML=item;});Event.addListener(row,'mouseover',function(e){self.cellHoverHandler($EV(e));});Event.addListener(row,'mouseout',function(e){self.cellHoverHandler($EV(e));});Event.addListener(row,'click',function(e){self.cellClickHandler($EV(e));});}pos++;}});if(pos<this.tbl.rows.length){for(i=(this.tbl.rows.length-1);i>=pos;i--){if(pos==this.selectedIndex)unselect=false;this.tbl.deleteRow(i);}}if(unselect){this.text.value='';this.selectedIndex=null;}}};MultiColumnLookupField.prototype.setDisabled=function(b){b&&this.hide();this.fld.disabled=b;this.text.disabled=b;this.btn.disabled=b;this.btn.setOpacity(b?0.6:1);};MultiColumnLookupField.prototype.focus=function(){if(this.beforeFocus()&&!this.btn.disabled){this.btn.focus();this.raiseEvent('focus');return true;}return false;};MultiColumnLookupField.prototype.toggleDisplay=function(){if(!this.tblContainer.isVisible()){var b=document.body,c=this.container;var t=this.tbl,tc=this.tblContainer;var ss=(PHP2Go.browser.ie?18:16);tc.style.left=c.getPosition().x;tc.style.height=this.height+'px';tc.style.zIndex=2;tc.show();if(t.offsetWidth<c.offsetWidth){tc.style.width=c.offsetWidth;t.style.width=c.offsetWidth-ss;}if(t.offsetHeight<this.height)tc.style.height=t.offsetHeight+(tc.offsetWidth<t.offsetWidth?ss:0);var p=tc.getPosition();if((p.x+tc.offsetWidth)>(b.clientWidth+b.scrollLeft))tc.style.left=(b.scrollLeft+b.clientWidth-tc.offsetWidth);if((p.y+tc.offsetHeight)>(b.clientHeight+b.scrollTop))tc.style.top=(p.y-tc.offsetHeight-c.offsetHeight);if(this.selectedIndex>=1)this.tbl.rows[this.selectedIndex].scrollIntoView(false);Event.addListener(document,'mousedown',this.mouseDownHandler.bind(this),true);}else{this.tblContainer.hide();}};MultiColumnLookupField.prototype.hide=function(){if(this.tblContainer.isVisible()){this.raiseEvent('blur');this.tblContainer.hide();if(this.fld.selectedIndex<1){if(this.selectedIndex>=1){this.tbl.rows[this.selectedIndex].className=this.style.normal;this.selectedIndex=this.fld.selectedIndex;}}Event.removeListener(document,'mousedown',this.mouseDownHandler.bind(this),true);}};MultiColumnLookupField.prototype.keyHandler=function(e){var k=$K(e);switch(k){case 34:this.navigate(5);break;case 33:this.navigate(-5);break;case 40:this.navigate(1);break;case 38:this.navigate(-1);break;case 35:this.navigate(this.selectedIndex>-1?this.size-this.selectedIndex-1:this.size-1);break;case 36:this.navigate(this.selectedIndex>-1?-this.selectedIndex+1:2);break;case 9:case 27:this.hide();break;case 13:if(this.selectedIndex>=1){this.setValueByIndex(this.selectedIndex);this.toggleDisplay();$EV(e).stop();}break;default:if(!e.ctrlKey&&!e.altKey&&k!=32&&(k<112||k>123))$EV(e).stop();break;}};MultiColumnLookupField.prototype.mouseDownHandler=function(e){var e=$EV(e),t=$E(e.element());if(!t.isChildOf(this.container)){this.hide();}};MultiColumnLookupField.prototype.cellHoverHandler=function(e){var elm=e.element();if(elm&&elm.parentNode.index){var p=elm.parentNode;if(e.type=='mouseover'){(this.selectedIndex==-1||p.index!=(this.selectedIndex))&&(p.className=this.style.hover);}else{p.className=(this.selectedIndex!=-1&&p.index==(this.selectedIndex)?this.style.selected:this.style.normal);}}};MultiColumnLookupField.prototype.cellClickHandler=function(e){var elm=e.element();if(elm&&elm.parentNode.index){this.setValueByIndex(elm.parentNode.index);this.hide();}};MultiColumnLookupField.prototype.navigate=function(fw){if(!this.tblContainer.isVisible())this.toggleDisplay();var rows=this.tbl.rows;if(this.selectedIndex==-1){this.selectedIndex=0;}else if(this.selectedIndex>=1){rows[this.selectedIndex].className=this.style.normal;}this.selectedIndex=(fw>0 ?(this.selectedIndex+fw<=(this.size-1)?this.selectedIndex+fw:1):(this.selectedIndex+fw>=1?this.selectedIndex+fw:this.size-1));rows[this.selectedIndex].className=this.style.selected;rows[this.selectedIndex].scrollIntoView(false);};PHP2Go.included[PHP2Go.baseUrl+'form/multicolumnlookupfield.js']=true;}