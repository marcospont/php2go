<?php

return array(
	'authenticator' => array(
		'adapter' => array(
			'type' => 'db',
			'tableName' => 'users',
			'usernameColumn' => 'name',
			'passwordColumn' => 'password',
			'extraCondition' => 'locked = 0'
		),
		'loginRoute' => 'sandbox/login',
		'successRoute' => 'sandbox/index',
		'logoutRoute' => 'sandbox/login'
	),
	'cache' => array(
		'backend' => array(
			'type' => 'eaccelerator'
		)
	),
	/*'pageCache' => array(
		'debugHeader' => true,
		'memorizeHeaders' => array('Content-Type'),
		'patterns' => array(
			'sandbox/index' => array(
				'params' => array('op')
			),
			'sandbox/xml' => array(
				'lifetime' => 10
			)
		)
	),*/
	'db' => array(
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
);