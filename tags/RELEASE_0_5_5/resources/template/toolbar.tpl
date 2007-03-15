<div id="{id}"{class} align="{align}">
<table id="{$id}_items" align="{$align}" style="width:{$width};height:{$itemHeight}">
  <tr>
    <td align="{$align}">
<!-- LOOP var=$items name="toolbar_items" item="item" -->
<!-- IF $p2g.loop.toolbar_items.rownum eq $activeIndex -->
	<!-- ASSIGN _itemClass = $activeClass -->
<!-- ELSE -->
	<!-- ASSIGN _itemClass = $itemClass -->
<!-- END IF -->
<!-- IF $mode eq 1 -->
      <a id="{$id}{$p2g.loop.toolbar_items.rownum}" href="{$item.link}"{$_itemClass} title="{$item.caption}" onMouseOver="itemDesc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="itemDesc.update('&nbsp;');"><img src="{$item.image}" border="0"/></a>{$separator}
<!-- ELSE IF $mode eq 2 -->
      <button id="{$id}{$p2g.loop.toolbar_items.rownum}"{$_itemClass} onClick="window.location.href='{$item.link}'" onMouseOver="itemDesc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="itemDesc.update('&nbsp;');">{$item.caption}</button>{$separator}
<!-- ELSE -->
      <a href="{$item.link}"{$_itemClass} onMouseOver="itemDesc.update('{$item.description|if_empty:"&nbsp;"}');" onMouseOut="itemDesc.update('&nbsp;');">{$item.caption}</a>{$separator}
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
	var itemDesc = $('{$id}_description');
</script>
</div>