
<fieldset id="{$id}" title="{$label}" style="border:0;padding:0;margin:0"{$style}>
<table id="{$id}_top" cellpadding="2" cellspacing="0" border="0"{$tableWidth}>
  <tr><td align="center"><label for="{$availableId}"{$labelStyle}>{$availableLabel}</label></td><td>&nbsp;</td><td align="center"><label for="{$selectedId}"{$labelStyle}>{$selectedLabel}</label></td></tr>
  <tr><td align="center" valign="top" rowspan="4">{$available}<div{$labelStyle}>{$availableCountLabel}&nbsp;<label id="{$id}_available" style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;font-weight:bold;color:#000000">{$availableCount}</label></div></td><td align="center" valign="top">{$button0}</td><td align="center" valign="top" rowspan="4">{$selected}<div{$labelStyle}>{$selectedCountLabel}&nbsp;<label id="{$id}_cnt" style="font-family:Verdana,Arial,Helvetica,sans-serif;font-size:11px;font-weight:bold;color:#000000"></label></div></td></tr>
  <tr><td align="center" valign="top">{$button1}</td></tr>
  <tr><td align="center" valign="top">{$button2}</td></tr>
  <tr><td align="center" valign="top">{$button3}</td></tr>
</table><input type="hidden" id="{$addedName}" name="{$addedName}" /><input type="hidden" id="{$removedName}" name="{$removedName}" />
<script type="text/javascript">
{$id}_instance = new LookupSelectionField('{$id}', '{$availableId}', '{$selectedId}', '{$addedName}', '{$removedName}', '{$separator}');
<!-- loop var=$customListeners item="actions" key="event" -->
<!-- loop var=$actions item="action" -->
{$id}_instance.addEventListener('{$event|lower|substring:2}', {$action});
<!-- end loop -->
<!-- end loop -->
</script>
</fieldset>
