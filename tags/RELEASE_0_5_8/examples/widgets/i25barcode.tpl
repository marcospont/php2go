<style type="text/css">
	.table {
		margin-top: 10px;
		border: 1px solid #000;
	}
</style>
<!-- include block : header.tpl -->
<table width="800" cellpadding="12" cellspacing="0" border="0" class="table">
	<tr>
		<td align="center" valign="top">
<!-- include widget path="I25BarCode" code=$code1 barHeight=80 barWidth=4 --><br/>
{$code1}
		</td>
		<td align="center" valign="top">
<!-- include widget path="I25BarCode" code=$code1 barHeight=50 barWidth=3 --><br/>
{$code1}
		</td>
	</tr>
	<tr>
		<td align="center" valign="top">
<!-- include widget path="I25BarCode" code=$code2 barHeight=120 barWidth=5 --><br/>
{$code2}
		</td>
		<td align="center" valign="top">
<!-- include widget path="I25BarCode" code=$code2 barHeight=30 barWidth=2 --><br/>
{$code2}
		</td>
	</tr>
</table>