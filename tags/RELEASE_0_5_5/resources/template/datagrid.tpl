
<table id="{$id}" cellpadding="1" cellspacing="0" border="0"{$width}{$style}>
  <!-- START BLOCK : loop_line -->
  <tr id="{$id}_{$row_id}">
    <!-- START BLOCK : loop_header_cell -->
	<th {$width}align="center" valign="top"{$style}>{$col_name}</th>
    <!-- END BLOCK : loop_header_cell -->
    <!-- START BLOCK : loop_cell -->
    <td {$width}align="{$align}" valign="top"{$style}>{$col_data}</td>
	<!-- END BLOCK : loop_cell -->
  </tr>
  <!-- END BLOCK : loop_line -->
</table>
