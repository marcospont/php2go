<!-- start block : hidden_field -->{$field}<!-- end block : hidden_field -->
<!-- if $formAlign neq "left" --><div align="{$formAlign}" style="width:100%"><!-- end if -->
<table cellpadding="0" cellspacing="0" border="0"{$formWidth}>
  <tr><td>
    <div{$errorStyle} id="form_client_errors"{$errorDisplay}>{$errorTitle}{$errorMessages}</div>
    <!-- start block : loop_section -->
    <!-- if $compatMode eq false -->
    <fieldset{$fieldsetStyle}>
      <!-- if $sectionName is not empty --><legend{$sectionTitleStyle}>{$sectionName}</legend><!-- end if -->
    <!-- end if -->
      <table cellpadding="{$tablePadding}" cellspacing="{$tableSpacing}" width="100%"{$sectionTableStyle}>
        <!-- if ($compatMode eq true && $sectionName is not empty) -->
        <tr><td colspan="2">&nbsp;&nbsp;<span{$sectionTitleStyle}>{$sectionName}</span><br /><hr /></td></tr>
        <!-- end if -->
        <!-- start block : section_item -->
        <!-- if $itemType eq 'field' -->
        <tr>
          <td align="{$labelAlign}" width="{$labelWidth}">&nbsp;{$label}{$popupHelp}&nbsp;</td>
          <td align="left" width="{$fieldWidth}">{$field}{$button}{$inlineHelp}</td>
        </tr>
        <!-- else if $itemType eq 'button' -->
        <tr>
          <td>&nbsp;</td>
          <td align="left">{$button}</td>
        </tr>
        <!-- else if $itemType eq 'button_group' -->
        <tr>
          <td colspan="2"><table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
              <!-- start block : loop_button_group -->
              <td width="{$btnW}" align="center">{$button}</td>
              <!-- end block : loop_button_group -->
            </tr>
          </table></td>
        </tr>
        <!-- end if -->
        <!-- end block : section_item -->
      </table>
    <!-- if $compatMode eq false -->
    </fieldset>
    <!-- end if --><br />
    <!-- end block : loop_section -->
  </td></tr>
</table>
<!-- if $formAlign neq "left" -->
</div>
<!-- end if -->

