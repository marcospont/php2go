<style type="text/css">
	.table {
		margin-top: 5px;
	}
	.toolbar {
		border: 1px solid #000;
		background-color: #e8eef7;
	}
	.toolbarItem {
		width: 80px;
	}
	.toolbarItem, .toolbarItemVert {		
		font-family: Verdana;
		font-size: 12px;
		font-weight: bold;
	}
	.toolbarDesc {
		background-color: #c3d9ff;	
	}
	.toolbarDesc, .toolbarDesc2 {
		font-family: Verdana;
		font-size: 12px;
		font-weight: bold;
		padding: 4px;
	}
	.toolbarVert {
		border: 1px solid #000;
		background-color: #e8eef7;
		width: 140px;
		padding: 10px;
	}
	.toolbarItemVert {
		height: 30px;
		padding-left: 5px;
	}
</style>
<!-- include block : header.tpl -->
<table width="100%" cellpadding="8" cellspacing="0" border="0" class="table">
<tr><td align="center">
<!-- include widget path="Toolbar"
	mode=TOOLBAR_MODE_ICONS horizontal=yes items=$items1
	width=500 class="toolbar" descriptionClass="toolbarDesc"
-->
</td></tr>
<tr><td align="center">
<!-- include widget path="Toolbar"
	mode=TOOLBAR_MODE_BUTTONS horizontal=yes items=$items2
	width=500 itemClass="toolbarItem" descriptionClass="toolbarDesc2"
-->
</td></tr>
<tr><td align="center">
<!-- include widget path="Toolbar"
	mode=TOOLBAR_MODE_LINKS horizontal=no items=$items2 align="left"
	class="toolbarVert" itemClass="toolbarItemVert" descriptionClass="toolbarDesc2"
-->
</td></tr>
</table>