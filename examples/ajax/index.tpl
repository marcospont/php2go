<!-- Hidden DIV used as AJAX throbber -->
<div id="throbber" style="position:absolute;display:none;background-color:#fff;border:1px solid gray;padding:4px;width:180px;text-align:center">
	<p class="sample_simple_text"><img src="{$p2g.const.PHP2GO_ICON_PATH}indicator.gif" border="0" align="top" alt="" />&nbsp;Loading...</p>
</div>

<!-- JS functions that execute AJAX actions -->
<script type="text/javascript">
	function addToCart(id, desc, price) {
		var args = {
			throbber: 'throbber',
			container: 'cart',
			params: { action: 'add_item', id: id, desc: desc, price: price }
		};
		var req = new AjaxUpdater(document.location.pathname, args);
		req.send();
	}
	function removeFromCart(id) {
		var args = {
			throbber: 'throbber',
			container: 'cart',
			params: { action: 'remove_item', id: id }
		};
		var req = new AjaxUpdater(document.location.pathname, args);
		req.send();
	}
	function updateTotals() {
		var args = {
			throbber: 'throbber',
			container: 'cart',
			form: 'frm_cart',
			params: { action: 'update_totals' }
		};
		var req = new AjaxUpdater(document.location.pathname, args);
		req.send();
	}
</script>

<!-- Page layout -->
<div class="sample_big_text">PHP2Go Ajax Example</div>
<div id="overall" style="width:779px;padding:3px;">
<div style="float:left;width:450px;">
  <div class="sample_title" style="padding:4px;">List of Products</div>
  <table class="sample_border_table" style="background-color:#e9e9e9">
    <!-- loop var=$products item="product" -->
    <tr>
      <td class="sample_list_item" colspan="2">
        <b>{$product.short_desc}</b><br />
        <span class="sample_medium_text">{$product.long_desc|truncate:50} - {$product.price|decimal_currency|colorize:"red"}</span>
      </td>
      <td class="sample_list_item" align="center">
        <a href="javascript:;" onclick="addToCart({$product.id_product}, '{$product.short_desc|escape:'javascript'}', '{$product.price}');"><img src="add.gif" border="0" alt="" /></a>
      </td>
    </tr>
    <!-- end loop -->
  </table>
</div>
<div style="float:right;width:320px;">
  <div class="sample_title" style="padding:4px;">Your Shopping Cart</div>
  <form id="frm_cart" name="frm_cart" action="" style="display:inline">
  <div class="sample_border_table" id="cart">
<!-- include block : cart.tpl -->
  </div>
  </form>
</div>
</div>