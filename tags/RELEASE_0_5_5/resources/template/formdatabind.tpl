
<object id="{$csvDbName}" name="{$csvDbName}" classid="clsid:333C7BC4-460F-11D0-BC04-0080C7055A83" width="0" height="0">
  <param name="DataURL" value="{$databindSource}">
  <param name="UseHeader" value="True">
  <param name="TextQualifier" value="'">
  <param name="CaseSensitive" value="false">
</object>
<script for="{$csvDbName}" event="ondatasetcomplete">
	setTimeout(function() {
		new FormDataBind('{$csvDbName}', '{$formName}', '{$tableName}', '{$primaryKey}', {$readonlyForm|if_empty:"false"}, {$jsrsSubmit|if_empty:"true"}, '{$p2g.server.REQUEST_URI}');
	}, 100);
</script>
<table width="100%" cellpadding="6" cellspacing="0" border="0">
  <tr>
    <td align="left" width="48%" nowrap>
      <label for="{$formName}_filterSelect"{$labelStyle}>{$lang.filterTit}</label><br>
      <select id="{$formName}_filterSelect" name="actFilterSelect"{$inputStyle}{$lang.filterTip|status_bar}>{$filterOptions}</select>
      <input id="{$formName}_filterTerm" name="actFilterTerm" type="text" size="15" value=""{$inputStyle}{$lang.filterVTip|status_bar}>
      <input id="{$formName}_filterBtn" name="actFilterBtn" type="button" value="{$lang.filter}"{$buttonStyle}{$lang.filterBtnTip|status_bar}>
    <td>
    <td align="left" width="33%" nowrap>
      <label for="{$formName}_sortSelect"{$labelStyle}>{$lang.sortTit}</label><br>
      <select id="{$formName}_sortSelect" name="actSortSelect"{$inputStyle}{$lang.sortChoose|status_bar}>{$sortOptions}</select>
      <a href="#" name="actSortAsc"><img id="{$formName}_sortAsc" name="imgSortAsc" src="{$icons.sortasc}" alt="{$lang.sortAsc}" border="0"></a>
      <a href="#" name="actSortAsc"><img id="{$formName}_sortDesc" name="imgSortDesc" src="{$icons.sortdesc}" alt="{$lang.sortDesc}" border="0"></a>
    </td>
    <td align="left" nowrap>
      <label for="{$formName}_gotoField"{$labelStyle}>{$lang.gotoTit}</label><br>
      <input id="{$formName}_gotoField" name="actGotoField" type="text" size="5" maxlength="5" value=""{$inputStyle}{$lang.gotoTip|status_bar}>
      <input id="{$formName}_gotoBtn" name="actGotoBtn" type="button" value="{$lang.goto}"{$buttonStyle}{$lang.gotoBtnTip|status_bar}>
    </td>
  </tr>
</table>
<table width="100%" cellpadding="6" cellspacing="0" border="0">
  <tr>
    <td align="left">
      <input id="{$formName}_navFirst" name="navFirst" type="button" datasrc="#{$csvDbName}" datafld="navFirst" value="<<"{$buttonStyle}{$lang.navFirstTip|status_bar}{$extraFunctions.FIRST}>&nbsp;
      <input id="{$formName}_navPrevious" name="navPrevious" type="button" datasrc="#{$csvDbName}" datafld="navPrevious" value="<"{$buttonStyle}{$lang.navPrevTip|status_bar}{$extraFunctions.PREVIOUS}>&nbsp;
      <input id="{$formName}_navNext" name="navNext" type="button" datasrc="#{$csvDbName}" datafld="navNext" value=">"{$buttonStyle}{$lang.navPrevTip|status_bar}{$extraFunctions.NEXT}>&nbsp;
      <input id="{$formName}_navLast" name="navLast" type="button" datasrc="#{$csvDbName}" datafld="navLast" value=">>"{$buttonStyle}{$lang.navPrevTip|status_bar}{$extraFunctions.LAST}>&nbsp;
      <input id="{$formName}_actNew" name="actNew" type="button" datasrc="#{$csvDbName}" datafld="actNew" value="{$lang.actNew}"{$buttonStyle}{$lang.actNewTip|status_bar}{$globalDisabled}{$extraFunctions.NEW}>&nbsp;
      <input id="{$formName}_actEdit" name="actEdit" type="button" datasrc="#{$csvDbName}" datafld="actEdit" value="{$lang.actEdit}"{$buttonStyle}{$lang.actEditTip|status_bar}{$globalDisabled}{$extraFunctions.EDIT}>&nbsp;
      <input id="{$formName}_actDel" name="actDel" type="button" datasrc="#{$csvDbName}" datafld="actDel" value="{$lang.actDel}"{$buttonStyle}{$lang.actDelTip|status_bar}{$globalDisabled}{$extraFunctions.DELETE}>&nbsp;
      <input id="{$formName}_actSave" name="actSave" type="button" datasrc="#{$csvDbName}" datafld="actSave" value="{$lang.actSave}"{$buttonStyle}{$lang.actSaveTip|status_bar}{$globalDisabled}{$extraFunctions.SAVE}>&nbsp;
      <input id="{$formName}_actCancel" name="actCancel" type="button" datasrc="#{$csvDbName}" datafld="actCancel" value="{$lang.actCancel}"{$buttonStyle}{$lang.actCancelTip|status_bar}{$globalDisabled}{$extraFunctions.CANCEL}>
    </td>
    <td align="right"><div id="{$formName}_recCount" name="recCount"{$labelStyle}></div></td>
  </tr>
</table>
