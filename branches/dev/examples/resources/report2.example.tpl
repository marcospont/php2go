<style type="text/css">
	#overall div table {
		font-family: Verdana; font-size: 11px; font-weight: normal; color: #000;
	}
	.links {
		font-family: Verdana; font-size: 11px; font-weight: bold; color: #000;
	}
	select, .filters {
		font-family: Verdana; font-size: 11px; font-weight: normal; color: #000;
	}
	.buttons {
		font-family: Verdana; font-size: 11px; font-weight: bold; color: #000;
	}
	.title {
		font-family: Verdana; font-size: 14px; font-weight: bold; color: #000;
	}
	.col_header {
		font-family: Verdana; font-size: 11px; font-weight: normal; color: #000;
	}
	.col_odd {
		font-family: Verdana; background-color: #dddde9; font-size: 11px; font-weight: normal; color: #000;
	}
	.col_even {
		font-family: Verdana; background-color: #aaaabc; font-size: 11px; font-weight: normal; color: #000;
	}
	.col_odd table, .col_even table {
		font-family: Verdana; font-size: 11px; font-weight: normal; color: #000;
	}
</style>
<center>
<div id="overall" style="width:625px">
	<div style="text-align:center;font-weight:bold;padding:10px;font-size:16px;">
		<table width="100%" cellpadding="4" cellspacing="0" border="0">
			<tr>
				<td>{$title}</td>
				<td align="right">
					<form id="reports" name="reports" action="{$p2g.server.PHP_SELF}" method="GET" style="display:inline">
						<div style="padding:4px">Choose a report type:</div>
						<select id="rep" name="rep" onChange="this.form.submit()">
<!-- LOOP var=$options item="caption" key="value" -->
							<option value="{$value}"<!-- IF $p2g.get.rep eq $value --> selected<!-- END IF -->>{$caption}</option>
<!-- END LOOP -->
						</select>
					</form>
				</td>
			</tr>
		</table>
	</div>
	<div style="background-color:#dfdfe9;border:1px solid #999;padding:4px;">{$simple_search}</div>
<!-- IF $report.total_rows gt 0 -->
	<div style="padding:4px;">
		<table width="100%" cellpadding="4" cellspacing="0" border="0">
			<tr>
				<td>{$row_count}</td>
				<td align="right">{$rows_per_page}</td>
			</tr>
			<tr>
				<td>{$go_to_page}</td>
				<td align="right">{$page_links}</td>
			</tr>
		</table>
	</div>
	<table width="625" cellpadding="4" cellspacing="1" align="center" style="border:1px solid #999">
<!-- START BLOCK : loop_line -->
		<tr>
<!-- START BLOCK : loop_group -->
			<td width="100%" colspan="{$group_span}" valign="top" class="{$report.style.title}"><b>{$group_display}</b></td>
<!-- END BLOCK : loop_group -->
<!-- START BLOCK : loop_cell -->
			<td width="{$col_wid}" valign="top" class="{$alt_style}">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td valign="top">
							{$name_alias}: <b>{$name|colorize:"#ff0000"}</b><br/>
							{$address_alias}: <b>{$address}</b><br/>
							{$active_alias}: <b>{$active|map:1:"Yes":"No"}</b><br/>
							<div align="right">{$client_id}</div>
						</td>
					</tr>
				</table>
			</td>
<!-- END BLOCK : loop_cell -->
<!-- START BLOCK : empty_cell -->
			<td width="{$col_wid}" valign="top" class="{$alt_style}">&nbsp;</td>
		</tr>
<!-- END BLOCK : empty_cell -->
<!-- END BLOCK : loop_line -->
	</table>
	<div style="padding:4px;">
		<table width="100%" cellpadding="4" cellspacing="0" border="0">
			<tr>
				<td>{$go_to_page}</td>
				<td align="right">{$page_links}</td>
			</tr>
			<tr>
				<td>{$this_page}</td>
				<td align="right">{$row_interval}</td>
			</tr>
		</table>
	</div>
<!-- ELSE -->
	<div style="padding:20px;font-size:14px;">No records found.</div>
<!-- END IF -->
</div>
</center>