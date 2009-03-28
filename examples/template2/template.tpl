<style type="text/css">
	b {
		font-family: Tahoma,Verdana,Helvetica;
		font-size: 14px;
		color: #333;
	}
	p {
		font-family: Courier;
		font-size: 14px;
		font-weight: normal;
		color: #000;
	}
</style>
<br />
<div class="sample_title">PHP2Go Examples - php2go.template.Template</div><br />
<div class="sample_border_table" style="width:800px;background-color:#c3d9ff;padding:12px;">
  <b>Simple variable access:</b>
  <p>
  $simpleVar : {$simpleVar}<br />
  $simpleVar with upper modifier : {$simpleVar|upper}
  </p>
  <b>Array access:</b>
  <p>
  $numeric.0 : {$numeric.0}<br />
  $numeric.1 : {$numeric.1}<br />
  $numeric with dynamic key : {$numeric.$numericKey}<br />
  $associative.PHP_SELF : {$associative.PHP_SELF}<br />
  $associative with dynamic key : {$associative.$associativeKey}<br />
  $associative with modifiers : {$associative.PHP_SELF|file_basename}<br />
  n-dimensions : {$ndimension.address.street}, {$ndimension.address.number} - {$ndimension.address.city}/{$ndimension.address.country}
  </p>
  <b>Object access:</b>
  <p>
  member ($object->bool) : {$object->bool|@intval}<br />
  numeric array member ($object->numeric.0) : {$object->numeric.0}<br />
  associative array member ($object->associative.foo) : {$object->associative.foo}<br />
  member of an object member ($object->object->string) : {$object->object->string}
  </p>
  <b>Internal variables:</b>
  <p>
  request access ($p2g.get.param) : {$p2g.get.param}<br />
  server and environment : {$p2g.server.SERVER_ADDR}, {$p2g.env.OS}<br />
  registry access : {$p2g.registry.global}<br />
  config access : {$p2g.conf.ABSOLUTE_URI}<br />
  session objects : {$p2g.sessionobject.sample.property}<br />
  user access : {$p2g.user->username}, {$p2g.user->properties.name}<br />
  current timestamp : {$p2g.time}<br />
  current microtime : {$p2g.microtime}<br />
  php version (constant) : {$p2g.const.PHP_VERSION}
  </p>
  <b>Condition tags: if, else if, else, end if:</b>
  <p>
  <!-- if $p2g.user->registered -->
  User is logged in!<br />
  <!-- end if -->
  <!-- if $ifVariable loet 5 -->
  Less or equal than 5
  <!-- else if $ifVariable lt 10 -->
  Less than 10
  <!-- else if $ifVariable eq 10 -->
  Ten
  <!-- else -->
  Other numbers
  <!-- end if -->
  </p>
  <b>Iteration tags: loop, else loop, end loop:</b>
  <p>
  <!-- loop var=$object->rs item="product" name="products" -->
  <!-- call function="$object->sum" p1=$product -->
  <!-- if $p2g.loop.products.first -->
  --- List of products ---<br />
  <!-- end if -->
  #{$p2g.loop.products.rownum}: {$product.code} - {$product.price|decimal_currency}<br />
  <!-- if $p2g.loop.products.last -->
  --- Total: {$object->total|decimal_currency} ---<br />
  <!-- end if -->
  <!-- end loop -->
  </p>
  <b>Assign command:</b>
  <!-- assign currentTime=$p2g.time -->
  <p>{$currentTime|format_time}</p>
  <b>Assign command with array variable:</b>
  <!-- assign myArray=[1, true, "string", $p2g.time] -->
  <p>{','|@join:$myArray}</p>  
  <b>Capture blocks:</b>
  <!-- capture -->
  this code was captured and printed out later
  <!-- end capture -->
  <p>{$p2g.capture.default}</p>
  <b>Calling functions and/or methods:</b>
  <p>
  procedural function:&nbsp;<!-- call function="strlen" p1="PHP2Go Framework" --><br />
  static class method:&nbsp;<!-- call function="HttpRequest::uri" --><br />
  object method:&nbsp;<!-- call function="$p2g.user->getElapsedTime" --><br />
  object method with hash parameter:&nbsp;<!-- call function="$object->buildProps" p1=[number:1,string:"string",bool:true,var:$p2g.time] --><br/>
  <!-- call function="ucfirst" p1="php2go framework" assign="output" -->
  assigning result to a variable: {$output}<br />
  <!-- function name="parseEntities" -->
  container func: this is <html> entities & special chars<br />
  <!-- end function -->
  </p>
</div>