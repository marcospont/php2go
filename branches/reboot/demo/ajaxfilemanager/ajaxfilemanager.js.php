<?php
$url = dirname($_SERVER['REQUEST_URI']) . '/ajaxfilemanager.php?editor=tinymce';
?>
function ajaxfilemanager(field_name, url, type, win) {
  var ajaxfilemanagerurl = "<?=$url?>";
  switch (type) {
    case "image":
      break;
    case "media":
      break;
    case "flash":
      break;
    case "file":
      break;
    default:
      return false;
  }
  tinyMCE.activeEditor.windowManager.open({
    url: ajaxfilemanagerurl,
    width: 782,
    height: 440,
    inline : "yes",
    close_previous : "no"
  },{
    window : win,
    input : field_name
  });
}