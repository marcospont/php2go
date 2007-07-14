	<!-- PHP2Go Example : template used by formtemplate example -->
    <div style="width:800px;">
      <div id="form_client_errors" {errorStyle} {errorDisplay}>{error}</div>
    </div>
	<table width="800" cellpadding="0" cellspacing="0" border="0">
	  <tr>
	    <td class="blue_style form_title">{section_section}</td>
	  </tr>
	</table>
	<table width="800" cellpadding="8" cellspacing="2" border="0" style="border:1px solid #000;background-color:#e8eef7;">
	  <tr>
	    <td>
		  <!--
			this HTML div will be used to display the client-side and server-side error summary;
			it's recommended to include "style='display:none'" in the attribute string to avoid
			using unnecessary page space when the error summary is empty;
			in the FormTemplate instance configuration, call $form->setErrorStyle("error_css_class", FORM_ERROR_FLOW|FORM_ERROR_BULLET_LIST, "error_div_id");
		  -->
		  <table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr>
			  <td colspan="2" height="35" class="label_style"><big><big>PHP2Go Example : php2go.form.FormTemplate</big></big></td>
			</tr>
			<tr>
			  <td width="17%">{label_edit_field}&nbsp;{help_edit_field}<br/>{edit_field}</td>
			  <td>{label_passwd_field}&nbsp;{help_passwd_field}<br />{passwd_field}</td>
			</tr>
			<tr>
			  <td colspan="2">{label_range_field}<br />{range_field}</td>
			</tr>
			<tr>
			  <td colspan="2">
			  	{label_lookup_field}&nbsp;{help_lookup_field}<br />
			  	{lookup_field}&nbsp;{fill}
			  </td>
			</tr>
			<tr>
			  <td valign="top">{label_combo_field}<br />{combo_field}</td>
			  <td>{label_radio_field}<br />{radio_field}</td>
			</tr>
			<tr>
			  <td colspan="2">{check_field}</td>
			</tr>
			<!-- start block : condsection -->
			<tr style="background-color:#99ff99;">
			  <td>{label_remote_addr}<br />{remote_addr}</td>
			  <td>{label_php_text}<br />{php_text}</td>
			</tr>
			<tr style="background-color:#99ff99;">
			  <td colspan="2" align="right" class="input_style">This is a conditional section. Its visibility is defined by the developer.</td>
			</tr>
			<!-- end block : condsection -->
			<tr>
			  <td colspan="2">{label_memo_field}<br />{memo_field}</td>
			</tr>
			<tr>
			  <td colspan="2">{submit}&nbsp;&nbsp;{reset}&nbsp;&nbsp;{clear}</td>
			</tr>
		  </table>
		</td>
	  </tr>
	</table>
	<br />
	<!-- PHP2Go Example : formtemplate.example.tpl -->