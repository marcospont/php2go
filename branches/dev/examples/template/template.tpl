<style type="text/css">
	div {
		font-family: Verdana; 
		background-color: #c3d9ff;
	}
	td,th {
		font-family: Verdana;
		font-size: 11px;
		vertical-align: top;
	}
	table {
		border: 1px solid #000000;
		padding: 4px
	}
	span {
		font-size: 16px;
		font-weight: bold;
	}
</style>
<br />
<span>
	<!-- example of internacionalization -->
	{"SAMPLE:MESSAGES.HELLO_WORLD"|i18n}<br />
	{$simple_var}
</span>
<br /><br />

<!-- start block : modifiers_block -->

<div style="width:800px;">
<table width="100%" border="0">
  <tr>
	<!-- simple string modifiers: capitalize (StringUtils::capitalize) and truncate (StringUtils::truncate) -->
	<td width="50%">Capitalize string:<br /><b>{$simple_string|capitalize}</b></td>
	<td width="50%">Truncate string:<br /><b>{$big_string|truncate:40}</b></td>
  </tr>
  <tr>
	<!-- trim, normalize (StringUtils::normalize) and upper (strtoupper) -->
    <td>Trim, normalize and upper:<br /><b>{$raw_string|trim|normalize|upper}</b></td>
	<!-- escape (StringUtils::escape), nl2br and filter (StringUtils::filter) -->
	<td rowspan="2">Escape, convert new lines and filter:<br /><b>{$raw_string_multiline|escape:"htmlall"|nl2br|filter:"blank":" "}</b></td>
  </tr>
  <tr>
    <!-- implode a list of values using a given glue -->
    <td>Implode:<br /><b>{$array|implode:", "}</b></td>
  </tr>
  <tr>
	<!-- format timestamp, using Date::formatTime -->
	<td>Format current timestamp:<br /><b>{$timestamp|format_time:DATE_FORMAT_CUSTOM:"D d/m/Y H:i:s"}</b></td>
	<!-- convert date, in this case using Date::fromSqlToEuroDate -->
	<td>Convert date:<br /><b>{$sql_date|date_sql_euro}</b></td>
  </tr>
  <tr>
	<!-- build a mailto link, using HtmlUtils::mailtoAnchor -->
	<td>Mailto link:<br /><b>{$mail|mailto:"Send Mail":"Click here to send me a mail message!"}</b></td>
	<!-- parse links of a given string, using HtmlUtils::parseLinks -->
	<td>Parse links in text:<br /><b>{$text_links|parse_links}</b></td>
  </tr>
  <tr>
    <td>Number format:<br /><b>{$number|number_format:3:".":""}</b></td>
	<td>Currency format:<br /><b>{$number|decimal_currency}</b></td>
  </tr>
  <tr>
    <!-- file name modifiers, based on the absolute path: basename, size and last modified time -->
	<td>File properties:<br /><b>Base name: {$file|file_basename}<br />Size: {$file|file_size}<br />Last modified date: {$file|last_modified|format_time:DATE_FORMAT_RFC822}</b></td>
	<td>Image Tag:<br />{$img|image:"Hi there!":120:63:4:6}</td>
  </tr>
</table>
</div><br />
<!-- html table rendering, using HtmlUtils::table -->
{$data|table:true:"width=\"100%\"":"style=\"background-color:#b2b2b2\"":"style=\"background-color:#f2f2f2\"":"style=\"background-color:#000000;color:#ffffff\""|scroll_area:779:200}<br />

<!-- end block : modifiers_block -->

<table width="779" cellpadding="0" cellspacing="0" border="0">

	<!-- start block : example_block -->
	<tr>
		<td align="left" width="30%">{$foo|surround:"[ ":" ]"}</td>
		<td align="left" width="50%">{$bar}</td>
		<!-- start block : inner_block -->
		<td align="right" width="20%">{$baz}</td>
		<!-- end block : inner_block -->
	</tr>
	<!-- end block : example_block -->

</table>