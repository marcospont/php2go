<?php

/**
 * first, define if this is a production environment or not
 */
define('PRODUCTION', !ereg('localhost|w3', $_SERVER['SERVER_NAME']));

/**
 * define the root path of your application
 */
define('ROOT_PATH', str_replace("\\", "/", dirname(dirname(__FILE__))) . '/');

$P2G_USER_CFG['LANGUAGE'] = "en-us";
$P2G_USER_CFG['LOCAL_DATE_FORMAT'] = "d/m/Y";
$P2G_USER_CFG['LOCAL_TIME_ZONE'] = 'GMT';

/**
 * Website definitions (META tags)
 */
$P2G_USER_CFG['TITLE'] = "My Login Application";
$P2G_USER_CFG['DESCRIPTION'] = "This is the PHP2Go login application example";
$P2G_USER_CFG['KEYWORDS'] = "";
$P2G_USER_CFG['CATEGORY'] = "";
$P2G_USER_CFG['DATE_CREATION'] = "04/06/2004";

/**
 * This line allows us to call import("loginapp.SomeClassName")
 * The class extension must be ".class.php"
 */
$P2G_USER_CFG['INCLUDE_PATH']['loginapp'] = ROOT_PATH . 'classes/';

/**
 * Auth and user configuration 
 */
$P2G_USER_CFG['AUTH'] = array(
	'AUTHENTICATOR_PATH' => 'loginapp.MyAuth',
	'EXPIRY_TIME' => 0,
	'IDLE_TIME' => 0
);
$P2G_USER_CFG['USER'] = array(
	'CONTAINER_PATH' => 'loginapp.MyUser',
	'SESSION_NAME' => 'mysession'	
);

/**
 * Session configuration
 */
$P2G_USER_CFG['SESSION_PATH'] = ROOT_PATH . 'session/';
$P2G_USER_CFG['SESSION_LIFETIME'] = 1800;

$P2G_USER_CFG['USE_COMPRESSED_JS'] = false;

/**
 * Some configuration entries may depend on the environment.
 * Error handling, absolute paths and database settings are some examples
 */
if (PRODUCTION) {
	$P2G_USER_CFG['ABSOLUTE_URI'] = "http://www.php2go.com.br/php2go/";
	$P2G_USER_CFG['CAPTURE_ERRORS'] = TRUE;
	$P2G_USER_CFG['LOG_ERRORS'] = TRUE;
	$P2G_USER_CFG['SHOW_ERRORS'] = FALSE;
	$P2G_USER_CFG['DEBUG_TRACE'] = FALSE;
	$P2G_USER_CFG['ERROR_LOG_FILE'] = ROOT_PATH . 'logs/LoginApp_ErrorLog_%d%m%Y_%H.txt';
	$P2G_USER_CFG['DB_ERROR_LOG_FILE'] = ROOT_PATH . 'logs/LoginApp_DbErrorLog_%d%m%Y_%H.txt';
	$P2G_USER_CFG['DATABASE'] = array(
		'CONNECTIONS' => array(
			'DEFAULT' => array(
				'HOST' => 'localhost',
				'USER' => 'php2go',
				'PASS' => 'admin',
				'BASE' => 'php2go',
				'TYPE' => 'mysql',
				'PERSISTENT' => FALSE
			)
		),
		'DEFAULT_CONNECTION' => 'DEFAULT'
	);	
} else {
	$P2G_USER_CFG['ABSOLUTE_URI'] = "http://localhost/php2go/";
	$P2G_USER_CFG['CAPTURE_ERRORS'] = TRUE;
	$P2G_USER_CFG['LOG_ERRORS'] = TRUE;
	$P2G_USER_CFG['SHOW_ERRORS'] = TRUE;
	$P2G_USER_CFG['DEBUG_TRACE'] = TRUE;
	$P2G_USER_CFG['ERROR_LOG_FILE'] = ROOT_PATH . 'logs/LoginApp_ErrorLog_%d%m%Y_%H.txt';
	$P2G_USER_CFG['DB_ERROR_LOG_FILE'] = ROOT_PATH . 'logs/LoginApp_DbErrorLog_%d%m%Y_%H.txt';
	$P2G_USER_CFG['DATABASE'] = array(
		'CONNECTIONS' => array(
			'DEFAULT' => array(
				'HOST' => 'localhost',
				'USER' => 'admin',
				'PASS' => 'MSAPNS',				
				'BASE' => 'test',
				'TYPE' => 'mysql',
				'PERSISTENT' => FALSE
			)
		),
		'DEFAULT_CONNECTION' => 'DEFAULT'
	);
}

/**
 * Define another useful path constants
 */
define('TEMPLATE_PATH', ROOT_PATH . 'resources/template/');
define('XML_PATH', ROOT_PATH . 'resources/xml/');
define('CSS_PATH', 'resources/css/');

/**
 * Include the PHP2Go configuration file
 */
require_once('../../php2go/p2gConfig.php');

?>