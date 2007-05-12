<span class="sample_style"><b>PHP2Go Examples</b> : php2go.auth.AuthDb</span><br><br>
<!-- if !$p2g.user->registered -->
<fieldset style="width:350px" class="sample_border_table">
	<legend class="label_style">Login</legend>
	<!-- if $loginErrorMsg is not empty -->
	<p class="sample_style">{$loginErrorMsg}</p>
	<!-- end if -->
	<form method="post" action="{$p2g.server.PHP_SELF}" style="display:inline">
	<table border="0" cellpadding="3" cellspacing="0">
		<tr>
			<td><label for="username" class="label_style">Username:</label></td>
			<td><input type="text" id="username" name="username" value="{$p2g.post.username}" class="input_style" size="25"></td>
		</tr>
		<tr>
			<td><label for="password" class="label_style">Password:</label></td>
			<td><input type="password" id="password" name="password" class="input_style" size="25"></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="Login" class="button_style"></td>
		</tr>
	</table>
	</form>
</fieldset>
<!-- else -->
<fieldset style="width:600px;" class="sample_simple_text sample_border_table">
	<legend class="label_style">Secure Page</legend>
	<!-- if $successMsg is not empty -->
	<p><i>{$successMsg}</i></p>
	<!-- end if -->
	Logged user: <b>{$p2g.user->username}</b><br>
	User login date: <b><!-- call function="p2g.user->getLoginTime" p1="d/m/Y H:i:s" --></b><br>
	User is logged in since: <b><!-- call function="p2g.user->getElapsedTime" --></b> seconds ago<br>
	User's last idle time: <b><!-- call function="p2g.user->getLastIdleTime" --></b> seconds<br>
	User properties: {$p2g.user->properties|@exportVariable:true}
	<button type="button" class="button_style" name="reload" onClick="window.location.href='{$p2g.server.PHP_SELF}'">Reload Page</button>&nbsp;
	<button type="button" class="button_style" name="logout" onClick="window.location.href='{$p2g.server.PHP_SELF}?logout'">Logout</button><br>
</fieldset>
<!-- end if -->