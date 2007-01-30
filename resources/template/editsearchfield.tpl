
<fieldset id="{$id}" title="{$label}" style="border:0;padding:0;margin:0"{$style}>
<input id="{$id}_lastfilter" name="{$id}_lastfilter" type="hidden" value=""/>
<input id="{$id}_lastsearch" name="{$id}_lastsearch" type="hidden" value=""/>
<table id="{$id}_top" cellpadding="0" cellspacing="0" border="0">
  <tr><td valign="top" nowrap>
    {$filters}&nbsp;{$search}&nbsp;
<!-- IF $btnImg neq '' -->
	<a id="{$id}_button" name="{$id}_button" href="javascript:void(0)"{$labelStyle}><img src="{$btnImg}" alt="" border="0"></a>
<!-- ELSE -->
	<button id="{$id}_button" name="{$id}_button" type="button"{$buttonStyle}{$tabIndex}{$disabled}>{$btnValue}</button>
<!-- END IF -->
  </td></tr>
  <tr><td height="5" style="font-size:5px">&nbsp;</td></tr>
  <tr><td valign="top">{$results}</td></tr>
</table>
<script type="text/javascript">{$id}_instance = new EditSearchField('{$id}', '{$resultsName}', [{$masks}], {$idx|if_empty:"null"}, '{$url|if_empty:$p2g.server.REQUEST_URI}', {$autoTrim|if_empty:"false"}, {$autoDispatch|if_empty:"false"}, {$debug|if_empty:"false"}, {$value});</script>
</fieldset>
