<!-- PHP2Go Example : template used by crudcomponents example -->
<div style="width:770px;padding:5px;height:18px;margin-top:10px;background-color:#c3d9ff;border:1px solid #000;">
  <div style="width:500px;float:left;">{$title}</div>
  <div style="width:260px;float:right;text-align:right;padding-right:3px;"><a href="{$p2g.server.PHP_SELF}?action=create" class="reportLinks">Insert Project</a></div>
</div>
<!-- if $message is not empty -->
<div class="error_style" style="width:770px;padding:5px;height:15px;margin-top:10px;text-align:center;">{$message}</div>
<!-- end if -->
<!-- if $report.total_rows gt 0 -->
<div style="width:770px;padding:5px;margin-top:5px;">
  <div style="width:380px;float:left;" class="reportInputs">{$go_to_page}</div>
  <div style="width:380px;float:right;text-align:right;" class="reportInputs">{$page_links}</div>
  <br style="clear:both;" />
</div>
<div style="width:770px;padding:5px;border:1px solid #999;margin-top:5px;">
  <table width="100%" cellpadding="0" cellspacing="1" border="0">
	<!-- start block : loop_line -->
	<tr>
	  <!-- start block : loop_header_cell -->
	  <td width="{$col_wid}" valign="top" class="{$report.style.header}">{$col_name}{$col_order}</td>
	  <!-- end block : loop_header_cell -->
	  <!-- start block : loop_cell -->
	  <td bgcolor="#f2f2f2" width="{$col_wid}" valign="top" class="{$alt_style}">{$col_data}</td>
	  <!-- end block : loop_cell -->
	</tr>
	<!-- end block : loop_line -->
  </table>
</div>
<div style="width:770px;padding:5px;margin-top:5px;">
  <div style="width:380px;float:left;" class="reportInputs">{$this_page}</div>
  <div style="width:380px;float:right;text-align:right;" class="reportInputs">{$rows_per_page}</div>
  <br style="clear:both;" />
</div>
<!-- else -->
<div style="width:770px;padding:80px 5px 80px 5px;border:1px solid #999;margin-top:5px;" align="center">
  <span class="reportTitle">There are no records in the <B>projects</B> table.</span>
</div>
<!-- end if -->