<!-- PHP2Go Example : template used by crudcomponents example -->
<style type="text/css">
	.tasksContainer {
		border: 1px solid #000;
		padding: 8px;
		width: 740px;
	}
	.dataGrid th {
		background-color: #c3d9ff;
		color: #000;
	}
	.dataGrid th, .dataGrid td {
		padding: 4px;
	}
	#error_container {
		width: 770px;
		margin-top: 5px;
		padding: 5px;
		font-family: Verdana;
		font-size: 11px;
		color: #ff0000;
		border: 1px solid #000;
	}
</style>
<div style="width:770px;padding:5px;height:18px;margin-top:10px;background-color:#c3d9ff;border:1px solid #000;">
  <div style="width:500px;float:left;" class="reportTitle">{$section_project}</div>
  <div style="width:260px;float:right;text-align:right;padding-right:3px;"><a href="{$p2g.server.PHP_SELF}" class="reportLinks">Back</a></div>
</div>
<div id="error_container" {$errorDisplay}>{$error}</div>
<div style="width:770px;padding:10px 5px 10px 5px;border:1px solid #000;margin-top:5px;">
  <table width="98%" cellpadding="3" cellspacing="1" border="0" align="center">
	<tr>
	  <td colspan="2" valign="top" height="20">{$label_name}<br />{$name}</td>
	  <td rowspan="3">{$label_members}<br />{$members}</td>
	</tr>
	<tr>
	  <td width="18%" valign="top" height="20">{$label_start_date}<br />{$start_date}</td>
	  <td valign="top" height="20">{$label_end_date}<br />{$end_date}</td>
	</tr>
	<tr>
	  <td colspan="2" valign="top">{$label_id_manager}<br />{$id_manager}</td>
	</tr>
  </table>
</div>
<!-- start block : details -->
<div style="width:770px;padding:10px 5px 10px 5px;border:1px solid #000;margin-top:5px;">
  <table width="98%" cellpadding="3" cellspacing="1" border="0" align="center">
    <tr><td colspan="8" class="label_style">New Task</td></tr>
    <tr>
      <td>{$label_task_name}<br />{$task_name}</td>
      <td>{$label_task_description}<br />{$task_description}</td>
      <td>{$label_task_id_owner}<br />{$task_id_owner}</td>
      <td>{$label_task_status}<br />{$task_status}</td>
      <td>{$label_task_priority}<br />{$task_priority}</td>
      <td>{$label_task_start_date}<br />{$task_start_date}</td>
      <td>{$label_task_end_date}<br />{$task_end_date}</td>
      <td valign="bottom">{$add}</td>
    </tr>
    <tr><td colspan="8" class="label_style">Edit Tasks</td></tr>
	<tr><td colspan="8">{$tasks}</td></tr>
  </table>
</div>
<!-- end block : details -->
<div style="width:770px;padding:10px 5px 10px 5px;height:18px;margin-top:5px;background-color:#e8eef7;border:1px solid #000;" align="center">
  {$send}
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$back}
<!-- if $action eq "update" -->
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$delete}
<!-- end if -->
</div>