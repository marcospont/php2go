<!-- PHP2Go Example : template include file -->
<table width="100%" cellpadding="2" cellspacing="0" border="0">
  <!-- START BLOCK : common_clients -->
  <tr>
    <td colspan="2"><strong>Common Clients (NAME like '%ma%')</strong></td>
  </tr>
  <!-- START BLOCK : common_client_loop -->
  <tr style="color: #FF0000; background-color: #FFFFFF">
    <td>{$NAME|lower}</td>
    <td>{$ADDRESS}</td>
  </tr>
  <!-- END BLOCK : common_client_loop -->
  <!-- END BLOCK : common_clients -->
  <!-- START BLOCK : common_clients_empty -->
  <tr>
    <td colspan="2"><strong>There are no clients with that name</strong></td>
  </tr>
  <!-- END BLOCK : common_clients_empty -->
  <tr>
    <td colspan="2"><i>{$date}</i></td>
  </tr>
</table>
<!-- PHP2Go Example : template include file -->