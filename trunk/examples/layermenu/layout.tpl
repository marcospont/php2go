<!-- PHP2Go Examples : php2go.gui.LayerMenu -->
<style type="text/css">
	#header {
		font-family: Verdana;
		font-size: 16px;
		font-weight: bold;
		background-color: #DDE4EA;
		padding: 10px 0px 10px 10px;
	}
	#menuContainer {
		float: left;
		width: 100%;
		height: 20px;
		background-color: #666;
		padding-left: 15px;
		padding-top: 5px;
	}
	#contentFrame {
		width: 98%;
		height: 450px;
		overflow: auto;
		border: none;
	}
</style>
<div id="overall">
  <div id="header">
    Application Title
  </div>
  <div id="menuContainer">
{$menu}
  </div>
  <iframe id="contentFrame" frameborder="0"></iframe>
</div>