<?
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2006 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/userConfig.php,v 1.31 2006/02/28 21:55:48 mpont Exp $
// $Date: 2006/02/28 21:55:48 $
// $Revision: 1.31 $

/**
 *
 * PHP2Go - Default Configuration File
 *
 * Define your own values and use this default file if you don't need to have multiple configuration sets.
 * Otherwise, you must define the configuration settings in one of the following ways:
 * 1) Write another file called userConfig.php and place it in the DOCUMENT_ROOT of your web application;
 * 2) Define the $P2G_USER_CFG array in your own configuration file, and then include/require p2gConfig.php.
 *
 *
 * Framework absolute URL - fill this entry with an absolute HTTP path to the framework root folder;
 * For more details on how to fill this entry, please consult INSTALL.txt.
 */
$port = ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '');
$P2G_USER_CFG['ABSOLUTE_URI'] = 'http://' . $_SERVER['SERVER_NAME'] . $port . '/php2go/';

/**
 * Language/locale settings
 *
 * LANGUAGE - an array of language settings {
 *   DEFAULT: language code that must be chosen if no one was detected using HTTP, session or request (default if not present: en-us)
 *   AUTO_DETECT: enables or disables HTTP language auto-detection (default if not present: false)
 *   AVAILABLE: using this entry you can define the subset of language codes that's supported by your application (default if not present: all languages supported by PHP2Go)
 *     -> languages supported by PHP2Go:
 *			en-us (English/United States),
 *			pt-br (Portuguese/Brazil),
 *			es (Spanish),
 *			it (Italian),
 *			cs (Czech),
 *			de-de (German/Germany),
 *			fr-fr (French/France)
 *   REQUEST_PARAM: name of the request param used to change the language dinamically (user defined)
 *   MESSAGES_PATH:
 *		Path to the user language domains. Inside it, you must create a folder for each supported
 *		language and inside these folders a file for each language domain. Besides, all these files
 *		must return the array of entries in the last line in order to be used  by the LanguageBase class
 * }
 * CHARSET - default charset to be used when needed
 *   -> you can provide a valid charset identifier;
 *   -> or you can set this value to "auto" to use charset auto detection based on the Accept-Charset HTTP header
 * COUNTRY - name of the local country (used in date print functions)
 * CITY - name of the local city (used in date print functions)
 */
$P2G_USER_CFG['LANGUAGE'] = array(
	'DEFAULT' => 'en-us',
	'AUTO_DETECT' => TRUE,
	//'AVAILABLE' => array('en-us', 'pt-br'),
	'REQUEST_PARAM' => 'lang',
	'MESSAGES_PATH' => PHP2GO_ROOT . 'examples/locale/'
);
$P2G_USER_CFG['CHARSET'] = 'auto';
$P2G_USER_CFG['COUNTRY'] = '';
$P2G_USER_CFG['CITY'] = '';

/**
 * Date and time zone settings
 * Possible date formats: d/m/Y or Y/m/d (case sensitive)
 * Possible time zone values: hour offset (-01:00, +03:00, -04:30), universal time (UT, GMT), north american time (EST, EDT, CST, ...) or military time (A..Z)
 */
$P2G_USER_CFG['LOCAL_DATE_FORMAT'] = "d/m/Y";
$P2G_USER_CFG['LOCAL_TIME_ZONE'] = 'GMT';

/**
 * BASE_URL - base URL for all links and images in the page, will be used to render a <base> tag
 * HTML header settings (META tags)
 * TITLE - base title for all HTML documents of the application
 * DESCRIPTION - DESCRIPTION meta tag
 * KEYWORDS - KEYWORDS meta tag
 * CATEGORY - CATEGORY meta tag
 * DATE_CREATION - DATE_CREATION meta tag
 */
$P2G_USER_CFG['BASE_URL'] = '';
$P2G_USER_CFG['TITLE'] = '';
$P2G_USER_CFG['DESCRIPTION'] = '';
$P2G_USER_CFG['KEYWORDS'] = '';
$P2G_USER_CFG['CATEGORY'] = '';
$P2G_USER_CFG['DATE_CREATION'] = '';

/**
 * Error handling settings
 * CAPTURE_ERRORS: choose if the framework must capture application and database errors and exceptions
 * LOG_ERRORS: choose if the framework must log the errors in the server file system
 * SHOW_ERRORS: choose if the framework must display the errors; use true in development environment, false in production environment
 * DEBUG_TRACE: enables or disables the stack trace when an error or exception is captured
 * ERROR_LOG_FILE: full path of your application error log file - all the values processed by strftime function are allowed here
 * DB_ERROR_LOG_FILE: full path of your database error log file - all the values processed by strftime function are allowed here
 */
$P2G_USER_CFG['CAPTURE_ERRORS'] = true;
$P2G_USER_CFG['LOG_ERRORS'] = true;
$P2G_USER_CFG['SHOW_ERRORS'] = true;
$P2G_USER_CFG['DEBUG_TRACE'] = true;
$P2G_USER_CFG['ERROR_LOG_FILE'] = '/usr/local/apache2/htdocs/php2go/tmp/error_%d%m%H.log';
$P2G_USER_CFG['DB_ERROR_LOG_FILE'] = '/usr/local/apache2/htdocs/php2go/tmp/db_error_%d%m%H.log';

/**
 * Database connection settings
 * CONNECTIONS: an array of key=>value pairs containing database connections and their parameters {
 *   DSN: Data Source Name. Optional way of defining all the connection parameters in a single string. Example: "driver://user:password@host/database"
 * 	 HOST: name/address of your database host
 * 	 USER: username of the database connection
 * 	 PASS: password of the database connection
 * 	 BASE: database/tablespace/service name of your database connection
 * 	 TYPE: database type, or driver name - you must choose one of the drivers implemented by ADODb (http://adodb.sourceforge.net)
 * 	 PCONNECTION:	define if the connection must be persistent
 * }
 * DEFAULT_CONNECTION: the ID of the default connection (when no ID is specified)
 * CONNECTION_CLASS_PATH: path to the connection class (extending php2go.db.Db) that must be instantiated by Db::getInstance
 */
$P2G_USER_CFG['DATABASE'] = array(
	'CONNECTIONS' => array(
		'DEFAULT' => array(
			//'DSN' => '',
			'HOST' => 'localhost',
			'USER' => 'php2go',
			'PASS' => 'admin',
			'BASE' => 'php2go',
			'TYPE' => 'mysql',
			'PERSISTENT' => FALSE
		)
	),
	'DEFAULT_CONNECTION' => 'DEFAULT'//,
	//'CONNECTION_CLASS_PATH' => ''
);

/**
 * Session settings
 * SESSION_AUTO_START - enable or disable session auto start during PHP2Go initialization process
 *		> If this key is missing, the default value is TRUE
 *		> PAY ATTENTION: disabling this feature will make user persistence in the session scope fail
 * SESSION_NAME - the name for the session cookie
 *		> if this entry is missing, the name of the cookie will be PHPSESSID (PHP default)
 * SESSION_PATH - the path where the session files must be saved; use a path that can't be accessed from outside the server
 *		> if this entry is missing, the path stored in the php.ini will be used
 * SESSION_LIFETIME - number of seconds that the session must be kept alive
 */
$P2G_USER_CFG['SESSION_AUTO_START'] = TRUE;
//$P2G_USER_CFG['SESSION_NAME'] = 'PHP2GO_SESSION';
$P2G_USER_CFG['SESSION_PATH'] = '';
$P2G_USER_CFG['SESSION_LIFETIME'] = 1800;

/**
 * User settings
 * SESSION_NAME - name of the key in the superglobal $_SESSION array that must store the user data
 * CONTAINER_PATH - path (using PHP2Go "dot" pattern) of the user class (it must extend php2go.auth.User)
 */
$P2G_USER_CFG['USER'] = array(
	'SESSION_NAME' => 'PHP2GO_USER'//,
	//'CONTAINER_PATH' => ''
);

/**
 * Auth settings
 * EXPIRY_TIME - default expiry time of the user session, in seconds (0 means limited by PHP session lifetime)
 * IDLE_TIME - default max idle time (between 2 requests), in seconds (0 means limited by PHP session lifetime)
 * AUTHENTICATOR_PATH - path (using PHP2Go "dot" pattern) of the default authenticator class
 *		- it must extend php2go.auth.Auth or one of its children
 *		- the default authenticator in the framework is php2go.auth.AuthDb
 *		- calls to Auth::getInstance will return a singleton of the authenticator class defined in this section, or the default authenticator (php2go.auth.AuthDb)
 * AUTHORIZER_PATH - path (using PHP2Go "dot" pattern) of the authorizer class
 *		- it must extend php2go.auth.Authorizer
 *		- calls to Authorizer::getInstance will return a singleton of the authorizer class defined in this section
 */
$P2G_USER_CFG['AUTH'] = array(
	'EXPIRY_TIME' => 0,
	'IDLE_TIME' => 0 //,
	//'AUTHENTICATOR_PATH' => '',
	//'AUTHORIZER_PATH' => ''
);

/**
 * PHP2Go forms global configuration settings
 *
$P2G_USER_CFG['FORMS'] = array(
	// Text to indicate that a field is mandatory
	'SECTION_REQUIRED_TEXT' => '*',
	// Color to the text described above
	'SECTION_REQUIRED_COLOR' => '#ff0000',
	// CSS class to the form inputs
	'INPUT_STYLE' => 'css_class_name',
	// CSS class to the form buttons
	'BUTTON_STYLE' => 'css_class_name',
	// CSS class to the input labels
	'LABEL_STYLE' => 'css_class_name',
	// Display mode of the help tips of form fields
	'HELP_MODE' => 'FORM_HELP_POPUP|FORM_HELP_INLINE',
	// Display options of the help tips
	'HELP_OPTIONS' => array(),
	'ERRORS' => array(
		// CSS class to the form validation errors
		'STYLE' => 'css_class_name',
		// Display mode of the validation error summary
		'LIST_MODE' => 'FORM_ERROR_BULLET_LIST|FORM_ERROR_FLOW',
		// Header of the error summary
		'HEADER_TEXT' => 'Header text',
		// CSS class of the header
		'HEADER_STYLE' => 'css_class_name',
		// Display mode of the form validation in the client side (JavaScript)
		'CLIENT_MODE' => 'FORM_CLIENT_ERROR_ALERT|FORM_CLIENT_ERROR_DHTML',
		// ID of the DIV container used to display errors (only if mode=FORM_CLIENT_ERROR_DHTML)
		'CLIENT_CONTAINER' => 'div_id',
		// Template placeholder (variable) used to display the server validation errors
		'TEMPLATE_PLACEHOLDER' => 'placeholder_name'
	),
	// Presentation settings for instances of the FormBasic class
	'BASIC' => array(
		// CSS class to the FIELDSET elements
		'FIELDSET_STYLE' => 'css_class_name',
		// CSS class to the section titles
		'SECTION_TITLE_STYLE' => 'css_class_name',
		// Form align
		'ALIGN' => 'left|right|center',
		// Form width (pixels)
		'WIDTH' => int,
		// Label align
		'LABEL_ALIGN' => 'left|right|center',
		// Label width (float, between 0 and 1)
		'LABEL_WIDTH' => int <= 1,
		// Table cellpadding
		'TABLE_PADDING' => int,
		// Table cellspacing
		'TABLE_SPACING' => int
	)
);
*/

/**
 * If you will use this default configuration set, don't forget to keep this return statement
 * in the last line, so the $P2G_USER_CFG may be understood by the initialization process
 */
return $P2G_USER_CFG;

?>