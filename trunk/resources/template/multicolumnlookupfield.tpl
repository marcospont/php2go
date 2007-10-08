
<select id="{$id}" name="{$name}" title="{$label}"{$attrs.DATASRC}{$attrs.DATAFLD}{$attrs.SCRIPT}><option value=""></option><!-- loop var=$options item="option" --><option value="{$option.0}"<!-- if $option.0 eq $value --> selected="selected"<!-- assign textValue=$option.1 --><!-- end if -->>{$option.1}</option><!-- end loop --></select>
<!-- if TypeUtils::isInstanceOf($options, 'ADORecordSet') --><!-- call function="$options->moveFirst" output=false --><!-- end if -->
<div id="{$id}_container" style="display:none"><input id="{$id}_text" name="{$name}_text" type="text" value="{$textValue}" readonly="readonly"{$attrs.WIDTH}{$attrs.STYLE}{$attrs.DISABLED} /><button id="{$id}_button" type="button"{$attrs.ACCESSKEY}{$attrs.TABINDEX}{$attrs.DISABLED}><img src="{$p2g.const.PHP2GO_ICON_PATH}arrow.gif" border="0" alt="" /></button><br />
<div id="{$id}_tableContainer" style="position:absolute;display:none;cursor:pointer;overflow-x:hidden;overflow-y:auto;border:1px solid ThreeDFace">
<table id="{$id}_table" cellspacing="0" cellpadding="4" border="0" <!-- if $attrs.TABLESTYLE is not empty --> class="{$attrs.TABLESTYLE}"<!-- end if--><!-- if $attrs.TABLEWIDTH is not empty --> style="width:{$attrs.TABLEWIDTH}px"<!-- end if -->>
  <tr class="{$rowStyle.normal}"><!-- loop var=$headers item="header" --><th nowrap="nowrap">{$header}</th><!-- end loop --></tr>
<!-- loop var=$options item="option" -->
  <tr class="<!-- if $option.0 eq $value -->{$attrs.ROWSTYLE.selected}<!-- else -->{$attrs.ROWSTYLE.normal}<!-- end if -->">
<!-- loop var=$option item="column" key="index" -->
    <!-- if $index gt 0 --><td nowrap="nowrap">{$column}</td><!-- end if -->
<!-- end loop --></tr>
<!-- end loop -->
</table>
</div></div><script type="text/javascript">new MultiColumnLookupField('{$id}',<!-- if $attrs.TABLEHEIGHT is empty -->null<!-- else -->{$attrs.TABLEHEIGHT}<!-- end if -->,{$attrs.ROWSTYLE|json});</script>