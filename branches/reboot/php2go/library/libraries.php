<?php

$path = dirname(__FILE__);
return array(
	'jquery' => array(
		'assetPath' => $path . '/jquery',
		'files' => array(
			'jquery.min.js'
		)
	),
	'jquery-ajaxqueue' => array(
		'assetPath' => $path . '/jquery',
		'files' => array(
			'jquery.ajaxqueue.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-autocomplete' => array(
		'assetPath' => $path . '/jquery/jquery-autocomplete',
		'publishAll' => true,
		'files' => array(
			'jquery.autocomplete.min.js',
			'jquery.autocomplete.css'
		),
		'dependencies' => array(
			'jquery',
			'jquery-ajaxqueue',
			'jquery-bgiframe'
		)
	),
	'jquery-bgiframe' => array(
		'assetPath' => $path . '/jquery',
		'files' => array(
			'jquery.bgiframe.min.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-blockui' => array(
		'assetPath' => $path . '/jquery',
		'files' => array(
			'jquery.blockui.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-gmap' => array(
		'assetPath' => $path . '/jquery',
		'files' => array(
			'jquery.gmap.min.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-jcrop' => array(
		'assetPath' => $path . '/jquery/jquery-jcrop',
		'publishAll' => true,
		'files' => array(
			'jquery.Jcrop.min.js',
			'jquery.Jcrop.css'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-maskedinput' => array(
		'assetPath' => $path . '/jquery',
		'files' => array(
			'jquery.maskedinput.min.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-passStrengthener' => array(
		'assetPath' => $path . '/jquery/jquery-passStrengthener',
		'files' => array(
			'jquery.passStrengthener.js',
			'jquery.passStrengthener.css'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-prettyPhoto' => array(
		'assetPath' => $path . '/jquery/jquery-prettyPhoto',
		'publishAll' => true,
		'files' => array(
			'jquery.prettyPhoto.js',
			'jquery.prettyPhoto.css'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-sprintf' => array(
		'assetPath' => $path . '/jquery',
		'files' => array(
			'jquery.sprintf.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-stars' => array(
		'assetPath' => $path . '/jquery/jquery-stars',
		'publishAll' => true,
		'files' => array(
			'jquery-stars.gif',
			'jquery-stars.min.css',
			'jquery-stars.min.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-tooltip' => array(
		'assetPath' => $path . '/jquery/jquery-tooltip',
		'files' => array(
			'jquery.tooltip.css',
			'jquery.tooltip.min.js'
		),
		'dependencies' => array(
			'jquery',
			'jquery-bgiframe'
		)
	),
	'jquery-ui' => array(
		'assetPath' => $path . '/jquery/jquery-ui',
		'files' => array(
			'jquery-ui.min.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'jquery-ui-datepicker' => array(
		'assetPath' => $path . '/jquery/jquery-ui',
		'files' => array(
			'jquery-ui-i18n-datepicker.js'
		),
		'dependencies' => array(
			'jquery',
			'jquery-ui'
		)
	),
	'jquery-uploader' => array(
		'assetPath' => $path . '/jquery/jquery-uploader',
		'publishAll' => true,
		'files' => array(
			'swfupload.js',
			'swfupload.queue.js',
			'swfupload.swfobject.js',
			'jquery-uploader.js',
			'jquery-uploader.css'
		),
		'dependencies' => array(
			'jquery',
			'jquery-sprintf',
			'php2go',
			'swfobject'
		)
	),
	'php2go' => array(
		'assetPath' => $path . '/php2go',
		'files' => array(
			'php2go.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'tinymce' => array(
		'assetPath' => $path . '/tinymce',
		'publishAll' => true,
		'files' => array(
			'tiny_mce.js',
			'jquery.tinymce.js'
		),
		'dependencies' => array(
			'jquery'
		)
	),
	'tinymce-gz' => array(
		'assetPath' => $path . '/tinymce',
		'publishAll' => true,
		'files' => array(
			'tiny_mce_gzip.js',
			'jquery.tinymce.js'
		)
	),
 	'swfobject' => array(
		'assetPath' => $path . '/swfobject',
		'files' => array(
			'swfobject.min.js'
		)
	)
);