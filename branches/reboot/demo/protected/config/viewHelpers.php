<?php

return array(
	'doctype' => array(
		'type' => 'XHTML11'
	),
	'head' => array(
		'title' => 'PHP2Go Demo',
		'meta' => array(
			array('keywords', 'keyword1, keyword2')
		),
		'scriptFiles' => array(
			array('js/demo.js')
		),
		'styleSheets' => array(
			array('css/demo.css')
		),
		'alternateLinks' => array(
			array('sandbox/rss', 'application/rss+xml', 'RSS Feed')
		)
	),
	'jQuery' => array(
		'uiTheme' => 'ui-lightness'
	),
	'form' => array(
		'afterRequiredLabel' => '&nbsp;<span class="required">(*)</span>',
		'requiredClass' => 'required',
		'errorClass' => 'error'
	),
	'menu' => array(
		'class' => 'menu',
		'activeClass' => 'menuActive'
	)
);