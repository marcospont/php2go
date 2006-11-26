<!-- PHP2Go Example : template used in crud.example.php -->
<table width="625" cellpadding="4" cellspacing="0" border="0">
  <tr>
    <td class="label_style"><b>{$title}</b></td>
    <td align="right"><a href="crud.example.php?action=create" class="label_style">Insert People</a></td>
  </tr>
</table>
<!--*

The available control variables are:

$report.base_uri : report base URI
$report.search_sent : indicate if a search query (using ReportSimpleSearch) is present in the last request
$report.style : array of style mappings of this Report instance - same keys as $Report->styleSheet property
$report.total_rows : total rows of the report
$report.page_rows : rows returned in the current page
$report.page_size : rows per page
$report.current_page : current page
$report.total_pages : total number of pages
$report.at_first_page : indicate if we're on the first page
$report.at_last_page : indicate if we're on the last page

*-->
<!-- IF $report.total_rows gt 0 -->
<table width="625" cellpadding="4" cellspacing="4" border="0" >
  <tr>
    <td width="40%" align="left" class="sample_simple_text">{$row_count}</td>
    <td width="60%" align="right" class="sample_simple_text">{$rows_per_page}</td>
  </tr>
  <tr>
    <td align="left" class="sample_simple_text">{$go_to_page}</td>
    <td align="right" class="sample_simple_text">{$page_links}</td>
  </tr>
</table>
<table width="625" cellpadding="4" cellspacing="0" border="1" bordercolor="#999999" >
  <tr><td align="center">
	<table width="620" cellpadding="3" cellspacing="1" border="0">
	  <!-- START BLOCK : loop_line -->
	  <tr>
		<!-- START BLOCK : loop_header_cell -->
		<td width="{$col_wid}" valign="top" class="label_style">{$col_name}{$col_order}</td>
		<!-- END BLOCK : loop_header_cell -->
		<!-- START BLOCK : loop_cell -->
		<td bgcolor="#f2f2f2" width="{$col_wid}" valign="top" class="sample_simple_text">{$col_data}{$NAME}</td>
		<!-- END BLOCK : loop_cell -->
	  </tr>
	  <!-- END BLOCK : loop_line -->
	</table>
  </td></tr>
</table>
<table width="625" cellpadding="4" cellspacing="4" border="0" >
  <tr>
    <td width="40%" align="left" class="sample_simple_text">{$go_to_page}</td>
    <td width="60%" align="right" class="sample_simple_text">{$page_links}</td>
  </tr>
  <tr>
    <td align="left" class="sample_simple_text">{$this_page}</td>
    <td align="right" class="sample_simple_text">{$row_interval}</td>
  </tr>
</table>
<!-- ELSE -->
<table width="625" cellpadding="4" cellspacing="0" border="1" bordercolor="#999999">
  <tr>
    <td class="input_style" align="center" height="200">
    	There are no records in the <b>people</b> table.
    </td>
  </tr>
</table>
<!-- END IF -->