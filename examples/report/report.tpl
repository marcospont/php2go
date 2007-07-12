<style type="text/css">
	#overall div table {
		font-family: Verdana; font-size: 11px; font-weight: normal; color: #000; }
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
		text-align: left; font-family: Verdana; background-color: #dde9dd; font-size: 11px; font-weight: normal; color: #000;
	}
	.col_even {
		text-align: left; font-family: Verdana; background-color: #aabcaa; font-size: 11px; font-weight: normal; color: #000;
	}
</style>
<center>
<div id="overall" style="width:625px">
	<div style="text-align:center;font-weight:bold;padding:10px;font-size:16px;">
		<table width="100%" cellpadding="4" cellspacing="0" border="0">
			<tr>
				<td>{$title}</td>
				<td align="right">
					<form id="reports" name="reports" action="{$p2g.server.PHP_SELF}" method="get" style="display:inline">
						<div style="padding:4px">Choose a report type:</div>
						<select id="rep" name="rep" onchange="this.form.submit()">
<!-- loop var=$options item="caption" key="value" -->
							<option value="{$value}"<!-- IF $p2g.get.rep eq $value --> selected="selected"<!-- END IF -->>{$caption}</option>
<!-- end loop -->
						</select>
					</form>
				</td>
			</tr>
		</table>
	</div>
	<div style="background-color:#dfe9df;border:1px solid #999;padding:4px;">{$simple_search}</div>
<!-- if $report.total_rows gt 0 -->
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
	<table width="625" cellpadding="4" cellspacing="1" style="border:1px solid #999">
<!-- start block : loop_line -->
		<tr>
<!-- start block : loop_group -->
			<td width="100%" colspan="{$group_span}" valign="top" class="{$report.style.title}"><b>{$group_display}</b></td>
<!-- end block : loop_group -->
<!-- start block : loop_header_cell -->
			<th id="{$col_id}" width="{$col_wid}">{$col_help}&nbsp;{$col_name}{$col_order}</th>
<!-- end block : loop_header_cell -->
<!-- start block : loop_cell -->
			<td width="{$col_wid}" valign="top" class="{$alt_style}">{$col_data}</td>
<!-- end block : loop_cell -->
		</tr>
<!-- end block : loop_line -->
	</table>
	<div style="padding:4px;">
		<table width="100%" cellpadding="4" cellspacing="0" border="0">
			<tr>
				<td>{$this_page}</td>
				<td align="right">{$row_interval}</td>
			</tr>
		</table>
	</div>
<!-- else -->
	<div style="padding:20px;font-size:14px;">No records found.</div>
<!-- end if -->
</div>
</center>