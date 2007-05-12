<!-- if $formReadOnly -->
	<!-- assign buttonCss="editorBtnDisabled" -->
	<!-- assign globalDisabled=" disabled" -->
<!-- else -->
	<!-- assign buttonCss="editorBtn" -->
	<!-- assign globalDisabled="" -->
<!-- end if -->
<div id="{$id}_main" style="background-color:#f0f0ee;border:1px solid #ccc;">
  <div id="{$id}_toolbar" class="editorToolbar" style="margin-left:2px;margin-top:3px">
    <table cellpadding="0" cellspacing="0" border="0">
      <tr id="{$id}_top">
        <td align="left" nowrap><select id="{$id}_formatblock" name="{$id}_formatblock"{$inputStyle}{$globalDisabled}>
          <option value="" selected>{$formatBlock}</option><!-- LOOP var=$formatBlockOptions key="value" item="caption" --><option value="{$value}">{$caption}</option><!-- END LOOP -->
        </select></td>
        <td><img src="{$iconPath}spacer.gif" width="1" height="1" border="0" alt=""/></td>
        <!-- loop var=$topButtons key="key" item="caption" -->
        <td class="editorBtnCell"><a id="{$id}_{$key}" name="{$id}_{$key}" href="javascript:;" title="{$caption}" class="{$buttonCss}"><img class="{$buttonCss}" src="{$iconPath}editor_{$key}.gif" alt="{$caption}"></a></td>
        <!-- end loop -->
      </tr>
      <tr id="{$id}_bottom">
        <td align="left" nowrap><select id="{$id}_fontname" name="{$id}_fontname"{$inputStyle} style="width:110px"{$globalDisabled}>
          <option value="" selected>{$font}</option><!-- LOOP var=$fontNames key="fontValue" item="fontName" --><option value="{$fontValue}">{$fontName}</option><!-- END LOOP -->
        </select><select id="{$id}_fontsize" name="{$id}_fontsize"{$inputStyle} style="width:70px"{$globalDisabled}>
          <option value="" selected>{$fontSize}</option><option value="1">1 (8 pt)</option><option value="2">2 (10 pt)</option><option value="3">3 (12 pt)</option><option value="4">4 (14 pt)</option><option value="5">5 (18 pt)</option><option value="6">6 (24 pt)</option><option value="7">7 (36 pt)</option>
        </select></td>
        <td><img src="{$iconPath}spacer.gif" width="1" height="1" border="0" alt=""/></td>
        <!-- loop var=$bottomButtons key="key" item="caption" -->
        <td class="editorBtnCell"><a id="{$id}_{$key}" name="{$id}_{$key}" href="javascript:;" title="{$caption}" class="{$buttonCss}"><img class="{$buttonCss}" src="{$iconPath}editor_{$key}.gif" alt="{$caption}"></a></td>
        <!-- end loop -->
      </tr>
    </table>
  </div>
  <!-- if $resizeMode neq "none" --><span id="{$id}_resizeBox" class="editorResizeBox"></span><!-- end if -->
  <div id="{$id}_container" style="margin-top:1px;">
    <div id="{$id}_window">
      {$hiddenField}
      <iframe id="{$id}_iframe" style="width:{$width}px;height:{$height}px;background-color:#fff;"></iframe>
      <textarea id="{$id}_textarea" style="width:{$width}px;height:{$height}px;display:none" rows="50" cols="8"></textarea>
    </div>
    <div id="{name}_footer" style="margin-left:2px;padding-bottom:2px;padding-right:2px;">
      <div style="float:left;">
        <input id="{$id}_switch" name="{$id}_switch" type="checkbox"{$globalDisabled}>&nbsp;<label for="{$id}_switch" id="label_{$id}_switch"{$labelStyle}>{$editMode}</label>
      </div>
      <!-- if $resizeMode neq "none" --><div id="{$id}_resize" class="editorResize" style="background-image:url({$iconPath}editor_resize.gif)"></div><!-- end if -->
      <br style="clear:both">
    </div>
  </div>
  <div id="{$id}_divemoticons" class="editorDivEmoticons">
    <table width="100%" cellpadding="1" cellspacing="1" style="border:none;background-color:#000000">
      <tr><td align="center" class="editorDivEmoticonsTitle">{$emoticon}</td></tr>
      <tr><td align="center" style="padding:2px;background-color:Window">
        <!-- loop name="emoticonLoop" var=$emoticons item="emoticon" --><img class="editorEmoticon" src="{$iconPath}emoticons/{$emoticon}.gif" alt=""><!-- if ($p2g.loop.emoticonLoop.rownum mod 7) eq 0 --><br><!-- else --><!-- end if --><!-- end loop -->
      </td></tr>
    </table>
  </div>
  <script type="text/javascript">
	{$id}_instance = new EditorField($('{$id}'), {$options});
	{$id}_instance.setup();
  </script>
</div>