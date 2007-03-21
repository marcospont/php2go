<table id="{$id}" align="{$align}" style="width:{$width};height:{$itemHeight}">
  <tr>
    <td align="{$align}">
<!-- LOOP var=$items name="toolbar_items" item="item" -->
<!-- IF $p2g.loop.toolbar_items.rownum eq $activeIndex -->
	<!-- ASSIGN _itemClass = $activeClass -->
<!-- ELSE -->
	<!-- ASSIGN _itemClass = $itemClass -->
<!-- END IF -->
<!-- IF $mode eq $p2g.const.TOOLBAR_MODE_ICONS -->
      <a id="{$id}{$p2g.loop.toolbar_items.rownum}" href="{$item.link}"{$_itemClass} title="{$item.caption}" onMouseOver="{$id}Desc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="{$id}Desc.update('&nbsp;');"><img src="{$item.image}" border="0"/></a>{$separator}
<!-- ELSE IF $mode eq $p2g.const.TOOLBAR_MODE_BUTTONS -->
      <button id="{$id}{$p2g.loop.toolbar_items.rownum}"{$_itemClass} onClick="window.location.href='{$item.link}'" onMouseOver="{$id}Desc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="{$id}Desc.update('&nbsp;');">{$item.caption}</button>{$separator}
<!-- ELSE -->
      <a href="{$item.link}"{$_itemClass} onMouseOver="{$id}Desc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="{$id}Desc.update('&nbsp;');">{$item.caption}</a>{$separator}
<!-- END IF -->
<!-- IF $horizontal eq TRUE -->
	<!-- IF $p2g.loop.toolbar_items.last eq FALSE -->
    </td><td align="{$align}">
	<!-- END IF -->
<!-- ELSE -->
    <br>
<!-- END IF -->
<!-- END LOOP -->
    </td>
  </tr>
  <tr>
<!-- IF $horizontal eq TRUE -->
    <td id="{$id}_description" colspan="{$p2g.loop.toolbar_items.total}" align="{$descriptionAlign}"{$descriptionClass}>
<!-- ELSE -->
    <td id="{$id}_description" align="{$descriptionAlign}"{$descriptionClass}>
<!-- END IF -->
      &nbsp;
    </td>
  </tr>
</table>
<script type="text/javascript">
	var {$id}Desc = $('{$id}_description');
</script>