if(!PHP2Go.included[PHP2Go.baseUrl+'widgets/collapsiblepanel.js']){function CollapsiblePanel(attrs){this.Widget(attrs);this.header=null;this.tip=null;this.icon=null;this.content=null;}CollapsiblePanel.extend(Widget,'Widget');CollapsiblePanel.prototype.setup=function(){this.header=$(this.attributes['id']+'_header');this.tip=$(this.attributes['id']+'_tip');this.icon=$(this.attributes['id']+'_icon');this.content=$(this.attributes['id']+'_content');var self=this;var exp=new Image();exp.src=this.attributes['expandIcon'];var cps=new Image();cps.src=this.attributes['collapseIcon'];var toggle=function(){if(self.content.isVisible()){self.content.hide();self.icon.src=exp.src;(self.tip)&&(self.tip.update(self.attributes['collapsedTip']));}else{self.content.show();self.icon.src=cps.src;(self.tip)&&(self.tip.update(self.attributes['expandedTip']));}};Event.addListener(this.header,'click',toggle);};PHP2Go.included[PHP2Go.baseUrl+'widgets/collapsiblepanel.js']=true;}