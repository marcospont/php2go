<table width="100%" cellpadding="6" cellspacing="0" border="0" heigh="100">
<!-- IF $title is not empty -->
  <tr><td align="center">{$title}</td></tr>
<!-- END IF -->
  <tr>
    <td align="center" class="{$report.style.filter}">
      {$emptyMsg}
<!-- IF $report.search_sent -->
      <br><br><input type="button" name="btn_back" id="report_btn_back" class="{$report.style.button}" value="{$backLink}" title="{$backLink}" onClick="location.replace('{$report.base_uri}')">
<!-- END IF -->
    </td>
  </tr>
</table>