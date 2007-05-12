<!-- PHP2Go Examples : php2go.gui.TreeMenu -->
<style type="text/css">
	#overall {
		width: 780px;
		border: 1px solid #bbb;
		background-color: #f2f2f2;
		height: 500px;
		overflow: hidden;
	}
	#header {
		background-color: #ccc;
		font-family: Verdana;
		font-size: 16px;
		font-weight: bold;
		padding-top: 10px;
		padding-bottom: 10px;
		padding-left: 10px;
		clear: both;
	}
	#menu {
		float: left;
		width: 180px;
		height: 100%;
	}
	#content {
		float: left;
		width: 598px;
		height: 100%;
		background-color: #fff;
		padding-top: 10px;
		text-align: center;
		font-family: Verdana;
		font-size: 14px;
	}
</style>
{$menu}
<div id="overall">
  <div id="header">
    Application Title
  </div>
  <div id="menu"></div>
  <div id="content">
<!-- if $p2g.get.op is not empty -->
    You've choosen option {$p2g.get.op|@urldecode}
<!-- end if -->
  </div>
</div>