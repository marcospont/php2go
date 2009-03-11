
<table id="{$id}" cellpadding="1" cellspacing="0" border="0"{$width}{$style}>
<!-- start block : loop_line -->
  <tr id="{$id}_{$row_id}">
<!-- start block : loop_header_cell -->
	<th {$width}align="{$align}" valign="top"{$style}>{$col_name}</th>
<!-- end block : loop_header_cell -->
<!-- start block : loop_cell -->
    <td {$width}align="{$align}" valign="top">{$col_data}</td>
<!-- end block : loop_cell -->
  </tr>
<!-- end block : loop_line -->
<!-- if $total eq 0 && $message is not empty -->
  <tr id="{$id}_empty">
    <td align="center" valign="top" colspan="{$span}"{$labelStyle}>{$message}</td>
  </tr>
<!-- end if -->
</table>
