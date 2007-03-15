
<select id="{$id}" name="{$name}" title="{$label}"{$attrs.DATASRC}{$attrs.DATAFLD}{$attrs.SCRIPT}><option value=""></option><!-- LOOP var=$options item="option" --><option value="{$option.0}"<!-- IF $option.0 eq $value --> selected<!-- ASSIGN textValue=$option.1 --><!-- END IF -->>{$option.1}</option><!-- END LOOP --></select>
<!-- IF TypeUtils::isInstanceOf($options, 'ADORecordSet') --><!-- FUNCTION name="$options->moveFirst" output=false --><!-- END IF -->
<div id="{$id}_container" style="display:none"><input id="{$id}_text" name="{$name}_text" type="text" value="{$textValue}" readonly{$attrs.WIDTH}{$attrs.STYLE}{$attrs.DISABLED}><button id="{$id}_button" type="button"{$attrs.ACCESSKEY}{$attrs.TABINDEX}{$attrs.DISABLED}><img src="{$p2g.const.PHP2GO_ICON_PATH}arrow.gif" border="0" alt=""></button><br>
<div id="{$id}_tableContainer" style="position:absolute;display:none;cursor:pointer;overflow:auto;border:1px solid ThreeDFace">
<table id="{$id}_table" cellspacing="0" cellpadding="4" border="0" <!-- IF $attrs.TABLESTYLE is not empty --> class="{$attrs.TABLESTYLE}"<!-- END IF --><!-- IF $attrs.TABLEWIDTH is not empty --> style="width:{$attrs.TABLEWIDTH}px"<!-- END IF -->>
  <tr class="{$rowStyle.normal}"><!-- LOOP var=$headers item="header" --><th nowrap>{$header}</th><!-- END LOOP --></tr>
<!-- LOOP var=$options item="option" -->
  <tr class="<!-- IF $option.0 eq $value -->{$attrs.ROWSTYLE.selected}<!-- ELSE -->{$attrs.ROWSTYLE.normal}<!-- END IF -->">
<!-- LOOP var=$option item="column" key="index" -->
    <!-- IF $index gt 0 --><td nowrap>{$column}</td><!-- END IF -->
<!-- END LOOP --></tr>
<!-- END LOOP -->
</table>
</div></div><script type="text/javascript">new MultiColumnLookupField('{$id}',<!-- IF $attrs.TABLEHEIGHT is empty -->null<!-- ELSE -->{$attrs.TABLEHEIGHT}<!-- END IF -->,{$attrs.ROWSTYLE|json});</script>