<?php

return array(
	'id' => 'demo',
	'name' => 'PHP2Go Demo',
	'charset' => 'iso-8859-1',
	'locale' => 'pt_BR',
	'basePath' => 'protected',
	'timezone' => 'America/Sao_Paulo',
	'imports' => array(
		'application.classes.*',
		'application.models.*',
		'application.widgets.*'
	),
	'defaultLayout' => 'main',
	'defaultController' => 'sandbox',
	'modules' => array(),
	'components' => Config::fromFile('protected/config/components.php'),
	'viewHelpers' => Config::fromFile('protected/config/viewHelpers.php'),
	'libraries' => array(
		'ajaxfilemanager' => array(
			'files' => array(
				'ajaxfilemanager/ajaxfilemanager.js.php'
			)
		)
	),
	'session' => array(
		'name' => 'SANDBOXSESSID'
	),
	'csrfValidation' => true
);