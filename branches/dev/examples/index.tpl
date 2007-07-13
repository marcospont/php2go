<script type="text/javascript">
	function changeExample() {
		var example = $('examples').value;
		if (example) {
			var tabView = TabView.instances['example_view'];
			// change iframe's URL and activate first tab
			IFrame.setUrl($('example_iframe'), example + '/index.php');
			var request = new AjaxRequest(example + '/descriptor.php', {
				onJSONResult: function(response) {
					// activate first tab
					tabView.setActiveIndex(1);
					// remove other tabs (from last to second)
					for (var i=tabView.tabs.length; i>1; i--)
						tabView.removeTabByIndex(i);
					// add new tabs
					response.json.files.walk(function(item, idx) {
						var panel = new TabPanel({
							id: 'tab' + (idx+2),
							caption: item,
							loadUri: 'source.php?file=' + example + '/' + item,
							loadMethod: 'get'
						});
						tabView.addTab(panel);
					});
				}
			});
			request.send();
		}
	}
</script>
<div id="example_outer">
<div id="example_inner">
<!-- widget id="example_view" path="TabView" width="100%" contentHeight=520 -->
	<!-- widget id="example_panel" path="TabPanel" caption="Example" -->
	<iframe id="example_iframe" frameborder="0"></iframe>
	<!-- end widget -->
<!-- end widget -->
<!-- if $examples is not empty -->
<div id="example_loader">
  <form id="loader_form" name="loader" method="post" action="" style="display:inline;">
    Examples:&nbsp;
    <select id="examples" name="examples" style="width:250px;">
<!-- loop var=$examples item="example" -->
      <option value="{$example}">{$example}</option>
<!-- end loop -->
    </select>&nbsp;
    <button id="view" name="view" type="button" onclick="changeExample()">View</button>
  </form>
</div>
<!-- end if -->
</div>
</div>