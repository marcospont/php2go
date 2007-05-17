<div><a href="index.php" class="label_style">« New Search</a></div><br />
<table cellpadding="0" cellspacing="0" border="0" width="550">
  <tr><td>
    <fieldset>
      <legend class="label_style">Search Results</legend>
      <div style="overflow:auto;padding-top:8px;padding-bottom:8px;" class="input_style">
          <strong>Query description:</strong><br />{$filter_description}<br />
          <strong>Query debug:</strong><br />{$filter}
      </div>
      <table cellpadding="2" cellspacing="2" style="border:1px solid #000000" width="100%" align="center">
<!-- loop name="results" var=$results item="result" -->
	<!-- if $p2g.loop.results.first -->
        <tr>
          <th class="blue_style">Code</th>
          <th class="blue_style">Description</th>
          <th class="blue_style">Price</th>
          <th class="blue_style">Amount</th>
        </tr>
	<!-- end if -->
        <tr>
          <td class="input_style">{$result.code}</td>
          <td class="input_style">{$result.short_desc}</td>
          <td class="input_style">{$result.price|decimal_currency}</td>
          <td class="input_style">{$result.amount}</td>
        </tr>
<!-- else loop -->
        <tr>
          <td colspan="3" class="error_style" style="padding:10px">The submitted search returned an empty result set!</td>
        </tr>
<!-- end loop -->
      </table>
    </fieldset><br />
  </td></tr>
</table>