<!-- PHP2Go Example : page layout containing two slots : menu and main -->
<!-- the main table, containing the page body -->
{$menu}
<table width="770" cellpadding="4" cellspacing="0" style="border:1px solid #bbb; background-color: #fff">
  <tr>
    <td class="sample_simple_text" colspan="2" align="center" style="height:30px;background-color:#ccc">
      <b>[ PHP2Go Examples - php2go.gui.TreeMenu ]</b>
    </td>
  </tr>
  <tr>
    <td style="background-color:#f4f4f4;width:170px" valign="top">
      <span class="sample_simple_text"><b>[ Menu ]</b></span>
    </td>
    <td style="height:400px;width:600px" valign="top" class="sample_medium_text">
{$main}
    </td>
  </tr>
</table>
<!-- PHP2Go Example : layout_menu.example.tpl -->