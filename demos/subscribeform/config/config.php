<?php

/**
 * first, define if this is a production environment or not
 */
define('PRODUCTION', !ereg('localhost', $_SERVER['SERVER_NAME']));

/**
 * define the root path of your application
 */
define('ROOT_PATH', str_replace("\\", "/", dirname(dirname(__FILE__))) . '/');

$P2G_USER_CFG['LANGUAGE'] = "en-us";
$P2G_USER_CFG['LOCAL_DATE_FORMAT'] = "d/m/Y";

/**
 * Website definitions (META tags)
 */
$P2G_USER_CFG['TITLE'] = "My Subscribe Form";
$P2G_USER_CFG['DESCRIPTION'] = "This is the PHP2Go subscribe form example";
$P2G_USER_CFG['KEYWORDS'] = "";
$P2G_USER_CFG['CATEGORY'] = "";
$P2G_USER_CFG['DATE_CREATION'] = "14/06/2004";

/**
 * This line allows us to call import("loginapp.SomeClassName")
 * The class extension must be ".class.php"
 */
$P2G_USER_CFG['INCLUDE_PATH']['subscribe'] = ROOT_PATH . 'classes/';

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
	$P2G_USER_CFG['ERROR_LOG_FILE'] = ROOT_PATH . 'logs/php_error_log.txt';
	$P2G_USER_CFG['DB_ERROR_LOG_FILE'] = ROOT_PATH . 'logs/db_error_log.txt';	
	$P2G_USER_CFG['DATABASE_HOST'] = "localhost";
	$P2G_USER_CFG['DATABASE_USER'] = "php2go";
	$P2G_USER_CFG['DATABASE_PASS'] = "admin";
	$P2G_USER_CFG['DATABASE_BASE'] = "php2go";
	$P2G_USER_CFG['DATABASE_TYPE'] = "mysql";
	$P2G_USER_CFG['DATABASE_PCONNECTION'] = FALSE;		
} else {
	$P2G_USER_CFG['ABSOLUTE_URI'] = "http://localhost/php2go/";
	$P2G_USER_CFG['CAPTURE_ERRORS'] = TRUE;
	$P2G_USER_CFG['LOG_ERRORS'] = TRUE;
	$P2G_USER_CFG['SHOW_ERRORS'] = TRUE;
	$P2G_USER_CFG['DEBUG_TRACE'] = TRUE;
	$P2G_USER_CFG['ERROR_LOG_FILE'] = ROOT_PATH . 'logs/php_error_log.txt';	
	$P2G_USER_CFG['DB_ERROR_LOG_FILE'] = ROOT_PATH . 'logs/db_error_log.txt';
	$P2G_USER_CFG['DATABASE_HOST'] = "localhost";
	$P2G_USER_CFG['DATABASE_USER'] = "admin";
	$P2G_USER_CFG['DATABASE_PASS'] = "admin";
	$P2G_USER_CFG['DATABASE_BASE'] = "p2gapps";
	$P2G_USER_CFG['DATABASE_TYPE'] = "mysql";
	$P2G_USER_CFG['DATABASE_PCONNECTION'] = FALSE;	
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
require_once("../../php2go/p2gConfig.php");
session_save_path(ROOT_PATH . "session/");

?>