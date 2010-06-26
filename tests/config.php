<?php

return array(
	'id' => 'test',
	'name' => 'PHP2Go Test',
	'charset' => 'iso-8859-1',
	'locale' => 'pt_BR',
	'basePath' => '../demo/protected',
	'timezone' => 'America/Sao_Paulo',
	'imports' => array(
		'application.models.*'
	),
	'defaultLayout' => 'main',
	'defaultController' => 'site',
	'modules' => array(),
	'components' => array(
		'db' => array(
			'adapter' => 'ado',
			'dsn' => 'mysqli://root:@localhost/test'
		),
		'errorHandler' => array(
			'discardOutput' => !PHP2GO_DEBUG_MODE
		),
		'logger' => array(
			'appenders' => array(
				array(
					'type' => 'rollingFile',
					'filePath' => 'protected/logs/log.txt',
					'maxFileSize' => '1M',
					'maxFiles' => 5
				)
			),
			'filters' => array(
				array('priority', Logger::CRIT)
			)
		),
		'router' => array(
			'appendParams' => true,
			'showScriptFile' => false,
			'rules' => array(
				'login' => 'sandbox/login',
				'logout' => 'sandbox/logout'
			)
		)
	),
	'session' => array(
		'name' => 'test-application'
	)
);