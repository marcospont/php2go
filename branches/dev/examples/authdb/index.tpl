<style type="text/css">
	form {
		display: inline;
	}
	fieldset {
		border: 1px solid #888;
		background-color: #fff;
		padding: 10px;
		width: 400px;
	}
</style>
<span class="sample_style"><b>PHP2Go Examples</b> : php2go.auth.AuthDb</span><br />
<!-- if !$p2g.user->registered -->
<fieldset>
	<legend class="label_style">Login</legend>
	<!-- if $loginErrorMsg is not empty -->
	<div align="center" class="error_header">{$loginErrorMsg}</div>
	<!-- end if -->
	<form method="post" action="{$p2g.server.PHP_SELF}">
	<table width="100%" border="0" cellpadding="3" cellspacing="0">
		<tr>
			<td>&nbsp;</td>
			<td class="label_style"><i>Username: admin, Password: admin</i></td>
		</tr>
		<tr>
			<td width="20%"><label for="username" class="label_style">Username:</label></td>
			<td><input type="text" id="username" name="username" value="{$p2g.post.username}" class="input_style" size="25" /></td>
		</tr>
		<tr>
			<td><label for="password" class="label_style">Password:</label></td>
			<td><input type="password" id="password" name="password" class="input_style" size="25" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Login" class="button_style" /></td>
		</tr>
	</table>
	</form>
	<script type="text/javascript">
		$('username').focus();
	</script>
</fieldset>
<!-- else -->
<fieldset style="width:600px;">
	<legend class="label_style">Secure Page</legend>
	<!-- if $successMsg is not empty -->
	<p><i>{$successMsg}</i></p>
	<!-- end if -->
	Logged user: <b>{$p2g.user->username}</b><br />
	User login date: <b><!-- call function="p2g.user->getLoginTime" p1="d/m/Y H:i:s" --></b><br />
	User is logged in since: <b><!-- call function="p2g.user->getElapsedTime" --></b> seconds ago<br />
	User's last idle time: <b><!-- call function="p2g.user->getLastIdleTime" --></b> seconds<br />
	User properties: {$p2g.user->properties|@exportVariable:true}
	<button type="button" class="button_style" name="reload" onclick="window.location.href='{$p2g.server.PHP_SELF}'">Reload Page</button>&nbsp;
	<button type="button" class="button_style" name="logout" onclick="window.location.href='{$p2g.server.PHP_SELF}?logout'">Logout</button><br />
</fieldset>
<!-- end if -->