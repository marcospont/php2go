<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?=$data['type']?></title>
		<style type="text/css">
			/*<![CDATA[*/
			body {font-family:"Verdana";font-weight:normal;color:black;background-color:white;}
			h1 { font-family:"Verdana";font-weight:normal;font-size:18pt;color:red;}
			h3 {font-family:"Verdana";font-weight:bold;font-size:11pt;}
			p {font-family:"Verdana";font-weight:normal;color:black;font-size:9pt;}
			p.message {color: maroon;}
			p.date {color: gray;font-size:8pt;border-top:1px solid #aaa;}
			/*]]>*/
		</style>		
	</head>
	<body>
		<h1><?=$data['type']?></h1>
		<h3>Description</h3>
		<p class="message"><?=nl2br(htmlspecialchars($data['message'], ENT_COMPAT))?></p>
		<h3>Source File</h3>
		<p><?=htmlspecialchars($data['file'], ENT_COMPAT)."({$data['line']})"?></p>
		<h3>Stack Trace</h3>
		<pre><?=htmlspecialchars($data['trace'], ENT_COMPAT)?></pre>
		<p class="date"><?=date('Y-m-d H:i:s', $data['time'])?></p>
	</body>
</html>