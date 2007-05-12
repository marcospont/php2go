
<fieldset id="{$id}" title="{$label}" style="border:none;padding:0;margin:0"{$style}>
<table id="{$id}_top" cellpadding="0" cellspacing="0" border="0"{$tableWidth}>
  <tr><td><label for="{$editId}"{$labelStyle}>{$editLabel}</label></td><td>&nbsp;</td></tr>
  <tr><td>{$edit}</td><td align="center" style="padding-left:5px">{$button0}</td></tr>
  <tr><td><label for="{$lookupId}"{$labelStyle}>{$lookupLabel}</label></td><td align="center">&nbsp;</td></tr>
  <tr><td valign="top">{$lookup}</td><td align="center" valign="top" rowspan="2" style="padding-left:5px">{$button1}<br><br>{$button2}</td></tr>
  <tr><td valign="top" align="center">&nbsp;<span{$labelStyle}>{$countLabel}</span>&nbsp;<label id="{$id}_cnt" style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;font-weight:bold;color:#000000"></label></td></tr>
</table><input type="hidden" id="{$addedName}" name="{$addedName}"><input type="hidden" id="{$removedName}" name="{$removedName}">
<script type="text/javascript">
{$id}_instance = new EditSelectionField('{$id}', '{$editId}', '{$lookupId}', '{$addedName}', '{$removedName}', '{$separator}');
<!-- loop var=$customListeners item="listener" key="event" -->
{$id}_instance.addEventListener('{$event|lower|substring:2}', {$listener});
<!-- end loop -->
</script>
</fieldset>
