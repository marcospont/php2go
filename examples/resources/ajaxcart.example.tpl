    <!-- IF sizeof($cart->items) gt 0 -->
    <!-- FUNCTION name="cart->sort" -->
    <table class="sample_list_item" width="100%">
      <tr>
        <th>&nbsp;</th>
        <th>Desc</th>
        <th>Unit</th>
        <th>Units</th>
      </tr>
      <!-- LOOP var=$cart->items item="item" key="id" -->
      <tr>
        <td><a href="javascript:;" onClick="removeFromCart({$id})"><img src="resources/icon_remove.gif" border="0" alt=""></a></td>
        <td>{$item.desc}</td>
        <td>{$item.price|decimal_currency}</td>
        <td>
          <input type="text" id="units_{$id}" name="units[{$id}]" size="4" maxlength="4" value="{$item.units}"/>
          <script type="text/javascript">InputMask.setup($('units_{$id}'), DigitMask);</script>
        </td>
      </tr>
      <!-- END LOOP -->
      <!-- FUNCTION name="cart->getTotal" assign="total" -->
      <tr>
        <td colspan="4" align="right">
          Total: <b>{$total|decimal_currency}</b><br>
          <a href="javascript:;" onClick="updateTotals()">Update Totals</a>
        </td>
      </tr>
    </table>
    <!-- ELSE -->
    <div class="sample_list_item">The cart is empty</div>
    <!-- END IF -->    