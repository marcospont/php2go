<?php

import('php2go.text.StringUtils');

class CollapsiblePanel extends Widget
{
	function CollapsiblePanel($attrs) {
		parent::Widget($attrs);
		$this->isContainer = TRUE;
		$this->mandatoryAttributes[] = 'caption';
	}

	function getDefaultAttributes() {
		return array(
			'id' => PHP2Go::generateUniqueId(parent::getClassName()),
			'collapsed' => FALSE,
			'width' => '100%',
			'expandedCaption' => '',
			'class' => '',
			'expandedClass' => '',
			'captionClass' => '',
			'captionHeight' => '30px',
			'contentClass' => '',
			'collapseIcon' => PHP2GO_ICON_PATH . 'panel_collapse.gif',
			'expandIcon' => PHP2GO_ICON_PATH . 'panel_expand.gif'
		);
	}

	function loadAttributes($attrs) {
		$attrs['id'] = StringUtils::normalize($attrs['id']);
		if (empty($attrs['expandedCaption']))
			$attrs['expandedCaption'] = $attrs['caption'];
		if (empty($attrs['expandedClass']))
			$attrs['expandedClass'] = $attrs['class'];
		if (is_int($attrs['width']))
			$attrs['width'] .= 'px';
		if (is_int($attrs['captionWidth']))
			$attrs['captionWidth'] .= 'px';
		parent::loadAttributes($attrs);
	}

	function render() {
		$attrs =& $this->attributes;
		$code =<<<CODE
<script type="text/javascript">
	var {$attrs['id']}CollapseIcon = new Image();
	{$attrs['id']}CollapseIcon.src = "{$attrs['collapseIcon']}";
	var {$attrs['id']}ExpandIcon = new Image();
	{$attrs['id']}ExpandIcon.src = "{$attrs['expandIcon']}";
	function {$attrs['id']}Toggle(node) {
		var content = $('{$attrs['id']}_content');
		if (content.isVisible()) {
			content.hide();
			$('{$attrs['id']}_caption').update("{$attrs['expandedCaption']}");
			$('{$attrs['id']}_icon').src = {$attrs['id']}ExpandIcon.src;
		} else {
			$('{$attrs['id']}_caption').update("{$attrs['caption']}");
			$('{$attrs['id']}_icon').src = {$attrs['id']}CollapseIcon.src;
			content.show();
		}
	}
</script>
CODE;
		$code .= sprintf("<div id=\"%s\"%s style=\"width:%s;height:%s;\" onclick=\"%sToggle(this)\">", $attrs['id'], (!empty($attrs['class']) ? " class=\"{$attrs['class']}\"" : ""), $attrs['width'], $attrs['captionHeight'], $attrs['id']);
		$code .= sprintf("\n  <div id=\"%s_header\"%s style=\"cursor:pointer;vertical-align:middle;\">", $attrs['id'], (!empty($attrs['captionClass']) ? " class=\"{$attrs['captionClass']}\"" : ""));
		$code .= sprintf("\n    <div id=\"%s_caption\" style=\"float:left;width:95%%;\">%s</div>", $attrs['id'], $attrs['caption']);
		$code .= sprintf("\n    <div style=\"float:right;width:5%%;vertical-align:middle;\"><img id=\"%s_icon\" src=\"%s\" style=\"border:0\"></div>", $attrs['id'], ($attrs['collapsed'] ? $attrs['expandIcon'] : $attrs['collapseIcon']));
		$code .= "\n  </div>";
		$code .= "\n</div>";
		$code .= sprintf("\n<div id=\"%s_content\"%s style=\"display:%s;\">", $attrs['id'], (!empty($attrs['contentClass']) ? " class=\"{$attrs['contentClass']}\"" : ""), ($attrs['collapsed'] ? "none" : "block"));
		$code .= $this->content;
		$code .= "\n</div>";
		print $code;
	}
}

?>