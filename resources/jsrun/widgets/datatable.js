if(!PHP2Go.included[PHP2Go.baseUrl+'widgets/datatable.js']){function DataTable(attrs,func){this.Widget(attrs,func);this.root=null;this.table=null;this.thead=null;this.tbody=null;this.desc=null;this.sortIdx=null;this.sortTypes={};this.selected=[];this.anchor=null;}DataTable.extend(Widget,'Widget');DataTable.instances={};DataTable.prototype.setup=function(){var attrs=this.attributes;this.root=$(attrs.id);this.table=(this.root.getElementsByTagName('table')||[null])[0];if(this.table&&this.table.tHead&&this.table.tBodies.length>0){if(PHP2Go.browser.ie)Event.addListener(this.table,'selectstart',function(){var e=window.event;(e.ctrlKey||e.shiftKey)&&(e.returnValue=false);});this.thead=$(this.table.tHead);this.thead.addClass(attrs.headerClass);this.tbody=$(this.table.tBodies[0]);if(attrs.selectable)Event.addListener(this.table,'click',this.selectHandler.bind(this),true);if(this.thead.rows.length>0){var headers=this.thead.rows[0].cells;for(var i=0;i<headers.length;i++){headers[i].sortType=attrs.sortTypes[i]||'NONE';if(attrs.sortable&&headers[i].sortType!='NONE'){headers[i].style.cursor='pointer';Event.addListener(headers[i],'click',this.sortHandler.bind(this));$N('img',headers[i],{visibility:'hidden'},'',{className:'dataTableArrow',src:attrs.orderAscIcon});}}}if(this.tbody.rows.length>0){var highlight=function(e){e=(e||window.event);var row=Element.getParentByTagName(e.target||e.srcElement,'tr');if(row){row=$(row);if(e.type=='mouseover')row.addClass(attrs.highlightClass);else row.removeClass(attrs.highlightClass);}};for(var i=0;i<this.tbody.rows.length;i++){var row=$(this.tbody.rows[i]);if(attrs.rowClass){if(attrs.alternateRowClass&&((i+1)%2)==0)row.addClass(attrs.alternateRowClass);else row.addClass(attrs.rowClass);}if(attrs.highlightClass){Event.addListener(row,'mouseover',highlight);Event.addListener(row,'mouseout',highlight);}if(attrs.selectable)row.style.cursor='pointer';}}var toDate=function(v){var d=Date.fromString(v);return d.valueOf();};var toFloat=function(v){var f=v.replace(/[^0-9]+/g,'').replace(/([0-9]{2})$/,'.$1');return parseFloat(f,10);};var toUpper=function(v){return String(v).toUpperCase();};this.addSortType('NUMBER',Number);this.addSortType('DATE',toDate);this.addSortType('DATETIME',toDate);this.addSortType('CURRENCY',toFloat);this.addSortType('STRING');this.addSortType('ISTRING',toUpper);this.raiseEvent('init');}};DataTable.prototype.addSortType=function(type,valueFunc,compareFunc,rowFunc){if(!this.sortTypes[type]){this.sortTypes[type]={type:type,rowFunc:(typeof(rowFunc)=='function'?rowFunc:this._getRowValue),valueFunc:(typeof(valueFunc)=='function'?valueFunc:$IF),compareFunc:(typeof(compareFunc)=='function'?compareFunc:this._compare)};}};DataTable.prototype.getSelectedRows=function(){var sel=this.selected;var res=new Array(sel.length);for(var i=0;i<sel.length;i++)res.push(sel[i]);return res;};DataTable.prototype.getFirstRow=function(){if(this.tbody&&this.tbody.rows.length>0)return this.tbody.rows[0];return null;};DataTable.prototype.getPreviousRow=function(row){if(row&&row.previousSibling){var n=row.previousSibling;while(n){if(n.nodeType==1&&n.tagName.equalsIgnoreCase('tr')&&n.parentNode==this.tbody)return n;n=n.previousSibling;}}return null;};DataTable.prototype.getNextRow=function(row){if(row&&row.nextSibling){var n=row.nextSibling;while(n){if(n.nodeType==1&&n.tagName.equalsIgnoreCase('tr')&&n.parentNode==this.tbody)return n;n=n.nextSibling;}}return null;};DataTable.prototype.sortHandler=function(e){var ev=$EV(e);ev.stop();var cell=ev.findElement('td');if(cell){var idx=null;if(PHP2Go.browser.ie){var cells=cell.parentNode.childNodes;for(var i=0;i<cells.length;i++){if(cells[i]==cell){idx=i;break;}}}else{idx=cell.cellIndex;}this.sort(idx);}};DataTable.prototype.sort=function(idx,desc){if(this.tbody){var attrs=this.attributes,tbody=this.tbody;var type=attrs.sortTypes[idx]||'STRING';if(type=='NONE')return;if(desc==null){if(idx!=this.sortIdx)this.desc=attrs.descending;else this.desc=!this.desc;}else{this.desc=!!desc;}this.raiseEvent('beforesort',[idx,this.desc]);var cache=[],rows=this.tbody.rows;for(var i=0;i<rows.length;i++){cache.push({value:this.sortTypes[type].valueFunc(this.sortTypes[type].rowFunc(rows[i],idx)),row:rows[i]});}cache.sort(this.sortTypes[type].compareFunc);if(this.desc)cache.reverse();if(PHP2Go.browser.mozilla){var sib=tbody.nextSibling;var par=tbody.parentNode;par.removeChild(tbody);}for(var i=0;i<cache.length;i++){if(attrs.rowClass){if(attrs.alternateRowClass&&((i+1)%2)==0){cache[i].row.removeClass(attrs.rowClass);cache[i].row.addClass(attrs.alternateRowClass);}else{cache[i].row.removeClass(attrs.alternateRowClass);cache[i].row.addClass(attrs.rowClass);}}tbody.appendChild(cache[i].row);}if(PHP2Go.browser.mozilla)par.insertBefore(tbody,sib);for(var i=0;i<cache.length;i++){cache[i].value=null;cache[i].row=null;cache[i]=null;}var headers=this.thead.rows[0].cells;if(this.sortIdx!=null&&idx!=this.sortIdx)headers[this.sortIdx].lastChild.style.visibility='hidden';headers[idx].lastChild.src=(this.desc?attrs.orderDescIcon:attrs.orderAscIcon);headers[idx].lastChild.style.visibility='visible';this.raiseEvent('sort',[idx,this.desc]);this.sortIdx=idx;}};DataTable.prototype.selectHandler=function(e){var ev=$EV(e);var row=ev.findElement('tr');if(row&&row.parentNode==this.tbody){ev.stop();row=$(row);var before=this.getSelectedRows();var attrs=this.attributes;if(this.selected.length==0)this.anchor=row;if(!attrs.multiple||(!e.ctrlKey&&!e.shiftKey)){for(var i=0;i<this.selected.length;i++){if(this.selected[i].selected&&this.selected[i]!=row)this.selectRowUI(this.selected[i],false);}this.anchor=row;if(!row.selected)this.selectRowUI(row,true);this.selected=[row];}else if(attrs.multiple&&e.ctrlKey&&!e.shiftKey){this.selectRow(row.rowIndex-1,!row.selected,false);this.anchor=row;}else if(attrs.multiple&&e.ctrlKey&&e.shiftKey){var up=(row.rowIndex<this.anchor.rowIndex);var item=this.anchor;while(item!=null&&item!=row){(!item.selected)&&(this.selectRow(item.rowIndex-1,true,false));item=(up?this.getPreviousRow(item):this.getNextRow(item));}(!row.selected)&&(this.selectRow(row.rowIndex-1,true,false));}else if(attrs.multiple&&!e.ctrlKey&&e.shiftKey){for(var i=0;i<this.selected.length;i++)this.selectRowUI(this.selected[i],false);this.selected=[];var up=(row.rowIndex<this.anchor.rowIndex);var item=this.anchor;while(item!=null){this.selectRow(item.rowIndex-1,true,false);if(item==row)break;item=(up?this.getPreviousRow(item):this.getNextRow(item));}}var found,changed=(before.length!=this.selected.length);if(!changed){for(var i=0;i<before.length;i++){found=false;for(var j=0;j<this.selected.length;j++){if(before[i]==this.selected[j]){found=true;break;}}if(!found){changed=true;break;}}}(changed&&this.raiseEvent('changeselection'));}};DataTable.prototype.selectRow=function(idx,b,r){if(this.tbody){b=!!b,r=PHP2Go.ifUndef(r,true);var row=this.tbody.rows[idx];if(row){if(this.attributes.multiple){if(row.selected==b)return;this.selectRowUI(row,b);if(b){this.selected.push(row);}else{for(var i=0;i<this.selected.length;i++){if(this.selected[i]==row){this.selected.splice(i,1);break;}}}(r&&this.raiseEvent('changeselection'));}else{var old=this.selected[0];if(b){if(old==row)return;if(old)this.selectRowUI(old,false);this.selectRowUI(row,true);this.selected=[row];(r&&this.raiseEvent('changeselection'));}else{if(old==row){this.selectRowUI(old,false);this.selected=[];(r&&this.raiseEvent('changeselection'));}}}}}};DataTable.prototype.selectRowUI=function(row,b){var a=this.attributes,b=!!b;(b?row.addClass(a.selectedClass):row.removeClass(a.selectedClass));row.selected=b;};DataTable.prototype._getRowValue=function(row,idx){if(row&&row.cells){var cell=row.cells[idx]||null;if(cell)return Element.getInnerText(cell);}return null;};DataTable.prototype._compare=function(a,b){if(a.value<b.value)return-1;if(a.value>b.value)return 1;return 0;};}