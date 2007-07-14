<!--
  The following DIV element is used to show the client-side errors and the server-side errors
  (see FormTemplate::setErrorDisplayOptions). The special "errorDisplay" variable will be replaced
  by FormTemplate class hiding or showing the DIV, to avoid using unnecessary page space.
  For more information, please consult the documentation in the example script.
-->
<div style="width:800px;">
  <div id="form_client_errors" {$errorStyle}{$errorDisplay}>{$error}</div>
</div>
<table width="800" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td>
      <div class="blue_style form_title">Form validated on server</div>
    </td>
  </tr>
</table>
<table width="800" cellpadding="10" cellspacing="0" border="0" style="border:1px solid #000;background-color:#e8eef7;">
  <tr>
    <td><table width="100%" cellpadding="2" cellspacing="0" border="0">
	  <tr>
	    <td width="17%">{$label_Edit1}<br />{$Edit1}</td>
	    <td>{$label_Edit2}<br />{$Edit2}</td>
	  </tr>
	  <tr>
	    <td colspan="2">{$label_Combo1}<br />{$Combo1}</td>
	  </tr>
	  <tr>
	    <td colspan="2">{$label_File1}<br />{$File1}</td>
	  </tr>
	  <tr>
	    <td height="35" valign="bottom">{$send}</td>
	  </tr>
	</table></td>
  </tr>
</table>