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
		color: #666;
	}
</style>
<br>
<div class="sample_title">PHP2Go Examples - php2go.template.Template</div><br>
<div class="sample_border_table" style="background-color:#e9e9e9;padding:12px;">
  <b>Simple variable access:</b>
  <p>
  $simpleVar : {$simpleVar}<br>
  $simpleVar with upper modifier : {$simpleVar|upper}
  </p>
  <b>Array access:</b>
  <p>
  $numeric.0 : {$numeric.0}<br>
  $numeric.1 : {$numeric.1}<br>
  $numeric with dynamic key : {$numeric.$numericKey}<br>
  $associative.PHP_SELF : {$associative.PHP_SELF}<br>
  $associative with dynamic key : {$associative.$associativeKey}<br>
  $associative with modifiers : {$associative.PHP_SELF|file_basename}<br>
  n-dimensions : {$ndimension.address.street}, {$ndimension.address.number} - {$ndimension.address.city}/{$ndimension.address.country}
  </p>
  <b>Object access:</b>
  <p>
  member ($object->bool) : {$object->bool|@intval}<br>
  numeric array member ($object->numeric.0) : {$object->numeric.0}<br>
  associative array member ($object->associative.foo) : {$object->associative.foo}<br>
  member of an object member ($object->object->string) : {$object->object->string}
  </p>
  <b>Internal variables:</b>
  <p>
  request access ($p2g.get.param) : {$p2g.get.param}<br>
  server and environment : {$p2g.server.SERVER_ADDR}, {$p2g.env.OS}<br>
  registry access : {$p2g.registry.global}<br>
  config access : {$p2g.conf.ABSOLUTE_URI}<br>
  session objects : {$p2g.sessionobject.sample.property}<br>
  user access : {$p2g.user->username}, {$p2g.user->properties.name}<br>
  current timestamp : {$p2g.time}<br>
  current microtime : {$p2g.microtime}<br>
  php version (constant) : {$p2g.const.PHP_VERSION}
  </p>
  <b>IF, ELSE IF, ELSE, END IF:</b>
  <p>
  <!-- IF $p2g.user->registered -->
  User is logged in!<br>
  <!-- END IF -->
  <!-- IF $ifVariable loet 5 -->
  Less or equal than 5
  <!-- ELSE IF $ifVariable lt 10 -->
  Less than 10
  <!-- ELSE IF $ifVariable eq 10 -->
  Ten
  <!-- ELSE -->
  Other numbers
  <!-- END IF -->
  </p>
  <b>LOOP, ELSE LOOP, END LOOP:</b>
  <p>
  <!-- LOOP var=$object->rs item="product" name="products" -->
  <!-- FUNCTION name="$object->sum" p1=$product -->
  <!-- IF $p2g.loop.products.first -->
  --- List of products ---<br>
  <!-- END IF -->
  #{$p2g.loop.products.rownum}: {$product.code} - {$product.price|decimal_currency}<br>
  <!-- IF $p2g.loop.products.last -->
  --- Total: {$object->total|decimal_currency} ---<br>
  <!-- END IF -->
  <!-- END LOOP -->
  </p>
  <b>ASSIGN command:</b>
  <!-- ASSIGN currentTime=$p2g.time -->
  <p>{$currentTime|format_time}</p>
  <b>CAPTURE Blocks:</b>
  <!-- CAPTURE -->
  this code was captured and printed out later
  <!-- END CAPTURE -->
  <p>{$p2g.capture.default}</p>
  <b>Calling functions and/or methods:</b>
  <p>
  simple:&nbsp;<!-- FUNCTION name="strlen" p1="PHP2Go Framework" --><br>
  static method:&nbsp;<!-- FUNCTION name="HttpRequest::uri" --><br>
  dynamic method:&nbsp;<!-- FUNCTION name="$p2g.user->getElapsedTime" --><br>
  <!-- FUNCTION name="ucfirst" p1="php2go framework" assign="output" -->
  assigning result to a variable: {$output}<br>
  <!-- START FUNCTION name="parseEntities" -->
  container func: this is <html> entities & special chars<br>
  <!-- END FUNCTION -->
  </p>
</div>