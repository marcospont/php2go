<div id="header" style="width:800px">
	<table width="100%" cellpadding="4" cellspacing="0" border="0">
		<tr>
			<td class="sample_big_text">
				PHP2Go Widgets<br/>
<!-- assign widget=$p2g.get.widget -->
<!-- if $widget and $options.$widget -->
				{$options.$widget|colorize:"red"}
<!-- end if -->
			</td>
			<td align="right">
				<form id="widgets" name="widgets" action="{$p2g.server.PHP_SELF}" method="get" style="display:inline">
					<div style="padding:4px" class="sample_medium_text">Choose a widget:</div>
					<select id="widget" name="widget" onchange="this.form.submit()" class="input_style" style="width:250px;">
						<option value=""></option>
<!-- loop var=$options item="caption" key="value" -->
						<option value="{$value}"<!-- IF $widget eq $value --> selected="selected"<!-- END IF -->>{$caption}</option>
<!-- end loop -->
					</select>
				</form>
			</td>
		</tr>
	</table>
</div>