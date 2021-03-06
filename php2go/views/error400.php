<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Bad Request</title>
		<style type="text/css">
			/*<![CDATA[*/
			body {font-family:"Verdana";font-weight:normal;color:black;background-color:white;}
			h1 { font-family:"Verdana";font-weight:normal;font-size:18pt;color:red;}
			h2 { font-family:"Verdana";font-weight:normal;font-size:11pt;color:maroon;}
			p {font-family:"Verdana";font-weight:normal;color:black;font-size:9pt;}
			.date {color: gray;font-size:8pt;border-top:1px solid #aaa;}
			/*]]>*/
		</style>
	</head>
	<body>
		<h1>Bad Request</h1>
		<h2><?=nl2br(htmlspecialchars($data['message'], ENT_COMPAT))?></h2>
		<p>The request could not be understood by the server due to malformed syntax. Please do not repeat the request without modifications.</p>
		<p>If you think this is a server error, please contact the site administration.</p>
		<div class="date"><?=date('Y-m-d H:i:s', $data['time'])?>
</div>
</body>
</html>