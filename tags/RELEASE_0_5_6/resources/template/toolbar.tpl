<table id="{$id}" align="{$align}" style="width:{$width};height:{$itemHeight}">
  <tr>
    <td align="{$align}">
<!-- loop var=$items name="toolbar_items" item="item" -->
<!-- if $p2g.loop.toolbar_items.rownum eq $activeIndex -->
	<!-- assign _itemClass = $activeClass -->
<!-- else -->
	<!-- assign _itemClass = $itemClass -->
<!-- end if -->
<!-- if $mode eq $p2g.const.TOOLBAR_MODE_ICONS -->
      <a id="{$id}{$p2g.loop.toolbar_items.rownum}" href="{$item.link}"{$_itemClass} title="{$item.caption}" onMouseOver="{$id}Desc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="{$id}Desc.update('&nbsp;');"><img src="{$item.image}" border="0"/></a>{$separator}
<!-- else if $mode eq $p2g.const.TOOLBAR_MODE_BUTTONS -->
      <button id="{$id}{$p2g.loop.toolbar_items.rownum}"{$_itemClass} onClick="window.location.href='{$item.link}'" onMouseOver="{$id}Desc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="{$id}Desc.update('&nbsp;');">{$item.caption}</button>{$separator}
<!-- else -->
      <a href="{$item.link}"{$_itemClass} onMouseOver="{$id}Desc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="{$id}Desc.update('&nbsp;');">{$item.caption}</a>{$separator}
<!-- end if -->
<!-- if $horizontal eq TRUE -->
	<!-- if $p2g.loop.toolbar_items.last eq FALSE -->
    </td><td align="{$align}">
	<!-- end if -->
<!-- else -->
    <br>
<!-- end if -->
<!-- end loop -->
    </td>
  </tr>
  <tr>
<!-- if $horizontal eq TRUE -->
    <td id="{$id}_description" colspan="{$p2g.loop.toolbar_items.total}" align="{$descriptionAlign}"{$descriptionClass}>
<!-- else -->
    <td id="{$id}_description" align="{$descriptionAlign}"{$descriptionClass}>
<!-- end if -->
      &nbsp;
    </td>
  </tr>
</table>
<script type="text/javascript">
	var {$id}Desc = $('{$id}_description');
</script>