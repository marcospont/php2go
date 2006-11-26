<!-- START BLOCK : hidden_field -->{$field}<!-- END BLOCK : hidden_field -->
<!-- IF $formAlign neq "left" --><div align="{$formAlign}" style="width:100%"><!-- END IF -->
<table cellpadding="0" cellspacing="0" border="0"{$formWidth}>
  <tr><td>
    <div{$errorStyle} id="form_client_errors"{$errorDisplay}>{$errorTitle}{$errorMessages}</div>
    <!-- START BLOCK : loop_section -->
    <!-- IF $compatMode eq false -->
    <fieldset{$fieldsetStyle}>
      <!-- IF $sectionName is not empty --><legend{$sectionTitleStyle}>{$sectionName}</legend><!-- END IF -->
    <!-- END IF -->
      <table cellpadding="{$tablePadding}" cellspacing="{$tableSpacing}" width="100%"{$sectionTableStyle}>
        <!-- IF ($compatMode eq true && $sectionName is not empty) -->
        <tr><td colspan="2">&nbsp;&nbsp;<span{$sectionTitleStyle}>{$sectionName}</span><br><hr noshade/></td></tr>
        <!-- END IF -->
        <!-- START BLOCK : section_item -->
        <!-- IF $itemType eq 'field' -->
        <tr>
          <td align="{$labelAlign}" width="{$labelWidth}">&nbsp;{$label}{$popupHelp}&nbsp;</td>
          <td align="left" width="{$fieldWidth}">{$field}{$button}{$inlineHelp}</td>
        </tr>
        <!-- ELSE IF $itemType eq 'button' -->
        <tr>
          <td>&nbsp;</td>
          <td align="left">{$button}</td>
        </tr>
        <!-- ELSE IF $itemType eq 'button_group' -->
        <tr>
          <td>&nbsp;</td>
          <td><table cellpadding="0" cellspacing="0"  border="0" width="100%">
            <tr>
              <!-- START BLOCK : loop_button_group -->
              <td width="{$btnW}" align="center">{$button}</td>
              <!-- END BLOCK : loop_button_group -->
            </tr>
          </table></td>
        </tr>
        <!-- END IF -->
        <!-- END BLOCK : section_item -->
      </table>
    <!-- IF $compatMode eq false -->
    </fieldset>
    <!-- END IF --><br>
    <!-- END BLOCK : loop_section -->
  </td></tr>
</table>
<!-- IF $formAlign neq "left" -->
</div>
<!-- END IF -->

