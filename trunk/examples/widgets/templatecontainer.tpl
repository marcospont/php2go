<!-- include block : header.tpl -->
<!-- widget path="TemplateContainer" tpl="container.tpl" -->
<!-- loop var=$clients name="clients" item="client" -->
<!-- if $p2g.loop.clients.iteration is odd -->
<tr class="row_odd">
<!-- else -->
<tr class="row_even">
<!-- end if -->
  <td>{$client.name}</td>
  <td>{$client.address}</td>
  <td>{$client.category}</td>
</tr>
<!-- end loop -->
<!-- end widget -->