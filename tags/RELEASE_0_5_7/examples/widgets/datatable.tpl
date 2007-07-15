<style type="text/css">
	.table th, .table td {
		font-family: Tahoma, Arial;
		font-size: 13px;
		padding: 4px;
	}
	.row_odd {
		background-color: #e8eef7;
	}
	.row_even {
		background-color: #fff;
	}
</style>
<!-- include block : header.tpl -->
<!-- widget path="DataTable"
	sortable=yes sortTypes="STRING,STRING,DATE,CURRENCY" selectable=yes multiple=yes
	rowClass="row_odd" alternateRowClass="row_even"
-->
<table cellpadding="3" cellspacing="0" border="0" width="800" border="0" class="table">
<!-- the table must contain a thead element -->
  <thead>
    <tr>
      <td>Code</td>
      <td>Short Desc</td>
      <td>Date Added</td>
      <td>Price</td>
    </tr>
  </thead>
<!-- the table must contain a tbody element -->
  <tbody>
<!-- loop var=$products item="product" -->
    <tr>
      <td>{$product.code}</td>
      <td>{$product.short_desc}</td>
      <td>{$product.date_added|date_sql_euro}</td>
      <td>{$product.price|decimal_currency}</td>
    </tr>
<!-- end loop -->
  </tbody>
</table>
<!-- end widget -->