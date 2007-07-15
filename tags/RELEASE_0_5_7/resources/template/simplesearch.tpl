<form name="{$name}_search" method="post" action="{$searchUrl|if_empty:$p2g.server.REQUEST_URI}" style="display:inline">
<!-- assign mainOpCookie=$name|concat:"_mainop" -->
<!-- if $p2g.cookie.$mainOpCookie eq "OR" -->
	<!-- assign orChecked=" checked=\"checked\"" -->
	<!-- assign andChecked="" -->
<!-- else -->
	<!-- assign andChecked=" checked=\"checked\"" -->
	<!-- assign orChecked="" -->
<!-- end if -->
<table border="0" width="100%" cellpadding="3" cellspacing="0">
  <tr>
    <td align="left" valign="top" nowrap="nowrap">
	  <input type="hidden" id="{$name}_search_serialized_fields" name="search_fields" value="" />
	  <input type="hidden" id="{$name}_search_serialized_operators" name="search_operators" value="" />
	  <input type="hidden" id="{$name}_search_serialized_values" name="search_values" value="" />
	  <label for="{$name}_search_fields"{$labelStyle}>{$searchTitle}</label><br />
      <select id="{$name}_search_fields" name="{$name}_search_fields"{$inputStyle}><option value="-1">{$filtersTitle}</option>{$filterOptions}</select>&nbsp;
      <select id="{$name}_search_operators" name="{$name}_search_operators" style="width:120px"{$inputStyle}>{$operatorOptions}</select>&nbsp;
      <input type="text" id="{$name}_search_term" name="{$name}_search_term" size="20" maxlength="100"{$inputStyle} />&nbsp;
      <input type="button" id="{$name}_search_send" name="{$name}_search_send" value="{$sendBtn}"{$buttonStyle} />
    </td>
  </tr>
  <tr>
    <td align="left" nowrap="nowrap">
      <input type="radio" id="{$name}_search_mainop_and" name="search_main_op" value="AND"{$andChecked} /><label for="{$name}_search_mainop_and"{$labelStyle}>&nbsp;{$mainOpAnd}&nbsp;&nbsp;</label>
      <input type="radio" id="{$name}_search_mainop_or" name="search_main_op" value="OR"{$orChecked} /><label for="{$name}_search_mainop_or"{$labelStyle}>&nbsp;{$mainOpOr}&nbsp;&nbsp;</label>
      <button type="button" id="{$name}_search_add" name="{$name}_search_add"{$buttonStyle}>{$addBtn}</button>&nbsp;
      <button type="button" id="{$name}_search_view" name="{$name}_search_view"{$buttonStyle}>{$viewBtn}</button>&nbsp;
      <button type="button" id="{$name}_search_clear" name="{$name}_search_clear"{$buttonStyle}>{$clearBtn}</button>&nbsp;
    </td>
  </tr>
  <tr><td><div id="{$name}_filters" style="display:none"></div></td></tr>
</table>
<script type="text/javascript">
	search_{$name} = new ReportSimpleSearch('{$name}', [{$masks}], '{$searchUrl}');
<!-- if $onSearch is not empty -->
	search_{$name}.onSearch = function(args) {
{$onSearch}
	};
<!-- end if -->
</script>
</form>
