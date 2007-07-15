<style type="text/css">
	/* navigationClass applies to the <ul> element that surrounds the tab captions */
	.navigation a {
		width: auto;
		text-align: center;
		font-family: Arial;
		font-size: 12px;
		font-weight: bold;
		color: #000;		
	}
	.navigation li a {
		background-color: #eee;
	}
	/* tabViewSelected is an internal CSS class name that is used to highlight the selected tab */
	.navigation li.tabViewSelected a {
		background-color: #e8eef7;
	}
	/* containerClass applies to the element that contains the div node of each panel */
	.panel {
		background-color: #e8eef7;
	}
	.panel div {
		font-family: Verdana;
		font-size: 12px;
		padding: 10px;		
	}
</style>
<script type="text/javascript">
	function disableTab() {
		var inst = TabView.instances['ex2'];
		// panel indexes are 1-based
		inst.getTabByIndex(2).disable();
	}
	function enableTab() {
		var inst = TabView.instances['ex2'];
		// panel indexes are 1-based
		inst.getTabByIndex(2).enable();
	}
	function addTab() {
		var inst = TabView.instances['ex4'];
		var idx = inst.tabs.length+1;
		var panel = new TabPanel({
			caption: 'Tab D' + idx
		});
		inst.addTab(panel);
	}
	function removeTab() {
		var inst = TabView.instances['ex4'];
		if (inst.tabs.length > 2) {
			inst.removeTabByIndex(inst.tabs.length);
		}
	}
</script>
<!-- include block : header.tpl -->
<table cellpadding="8" cellspacing="0" border="0">
<tr>
<td>
<!-- widget id="ex1" path="TabView" orientation=TABVIEW_ORIENTATION_TOP width=400 contentHeight=200 navigationClass="navigation" containerClass="panel" -->
	<!-- widget path="TabPanel" caption="Tab A1" -->
	By default, the TabView widget uses top orientation.
	<!-- end widget -->
	<!-- widget path="TabPanel" caption="Tab A2" -->
	This example customizes the component's default layout by defining CSS styles.
	<!-- end widget -->	
	<!-- widget path="TabPanel" caption="Tab A3" loadUri="index.php?panel=A3" -->
	<!-- end widget -->		
<!-- end widget -->
</td>
<td>
<!-- widget id="ex2" path="TabView" orientation=TABVIEW_ORIENTATION_BOTTOM width=400 contentHeight=200 navigationClass="navigation" containerClass="panel" -->
	<!-- widget path="TabPanel" caption="Tab B1" -->
	TabView using bottom aligment.<br/><br/>
	Click <a href="javascript:disableTab();">here</a> to disable 'Tab B2'<br/><br/>
	Click <a href="javascript:enableTab();">here</a> to enable 'Tab B2'
	<!-- end widget -->
	<!-- widget path="TabPanel" caption="Tab B2" -->
	<img src="http://www.php2go.com.br/resources/images/p2g_transp_logo_header.gif"/>
	<!-- end widget -->	
	<!-- widget path="TabPanel" caption="Tab B3" loadUri="index.php?panel=B3" -->
	<!-- end widget -->		
<!-- end widget -->
</td>
</tr>
<tr>
<td>
<!-- widget id="ex3" path="TabView" orientation=TABVIEW_ORIENTATION_LEFT width=300 navigationClass="navigation" containerClass="panel" -->
	<!-- widget path="TabPanel" caption="Tab C1" -->
	TabView using left alignment.
	<!-- end widget -->
	<!-- widget path="TabPanel" caption="Tab C2" -->
	Second panel
	<!-- end widget -->
	<!-- widget path="TabPanel" caption="Tab C3" -->
	Third panel
	<!-- end widget -->
<!-- end widget -->
</td>
<td>
<!-- widget id="ex4" path="TabView" orientation=TABVIEW_ORIENTATION_RIGHT width=300 navigationClass="navigation" containerClass="panel" -->
	<!-- widget path="TabPanel" caption="Tab D1" -->
	TabView using right alignment.
	<!-- end widget -->
	<!-- widget path="TabPanel" caption="Tab D2" -->
	Click <a href="javascript:addTab();">here</a> to add a new tab on this component.<br/><br/>
	Click <a href="javascript:removeTab();">here</a> to remove the last tab (first and second will be preserved).
	<!-- end widget -->	
<!-- end widget -->
</td>
</tr>
</table>