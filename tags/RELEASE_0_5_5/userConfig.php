<?php
/**
 * PHP2Go Web Development Framework
 *
 * Copyright (c) 2002-2007 Marcos Pont
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 2.1 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @copyright 2002-2007 Marcos Pont
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version $Id$
 */

/**
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
$port = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '');
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
 * HTML page settings
 * BASE_URL - base URL for all links and images in the page, will be used to render a <base> tag
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
$P2G_USER_CFG['ERROR_LOG_FILE'] = '';
$P2G_USER_CFG['DB_ERROR_LOG_FILE'] = '';

/**
 * Database connection settings
 * CONNECTIONS: an array of key=>value pairs containing database connections and their parameters {
 *	DSN: Data Source Name. Optional way of defining all the connection parameters in a single string. Example: "driver://user:password@host/database"
 *	HOST: name/address of your database host
 *	USER: username of the database connection
 *	PASS: password of the database connection
 *	BASE: database/tablespace/service name of your database connection
 *	TYPE: database type, or driver name - you must choose one of the drivers implemented by ADODb (http://adodb.sourceforge.net)
 *	PERSISTENT:	define if the connection must be persistent
 *	FETCH_MODE: default fetch mode (0=default, 1=num, 2=assoc, 3=both)
 *	TRANSACTION_MODE: default transaction mode ('READ UNCOMMITTED', 'READ COMMITTED', 'REPEATABLE READ' or 'SERIALIZABLE')
 * }
 * DEFAULT_CONNECTION: the ID of the default connection (when no ID is specified)
 * CONNECTION_CLASS_PATH: path to the connection class (extending php2go.db.Db) that must be instantiated by Db::getInstance
 */
$P2G_USER_CFG['DATABASE'] = array(
	'CONNECTIONS' => array(
		'DEFAULT' => array(
			//'DSN' => '',
			'HOST' => 'localhost',
			'USER' => 'root',
			'PASS' => '',
			'BASE' => 'test',
			'TYPE' => 'mysql',
			'PERSISTENT' => FALSE/*,
			'FETCH_MODE' => 2,
			'TRANSACTION_MODE' => ''
			*/
		)
	),
	'DEFAULT_CONNECTION' => 'DEFAULT'/*,
	'CONNECTION_CLASS_PATH' => ''*/
);

/**
 * Session settings
 * NAME - name for the session cookie (defaults to PHP2GO_SESSID)
 * LIFETIME - lifetime, in seconds, for the session data (defaults to php.ini setting)
 * SAVE_PATH - customize path where session files must be saved (defaults to php.ini setting)
 * AUTO_START - auto start session during framework's initialization (defaults to TRUE)
 * COOKIES_ONLY - use only cookies (disable trans id and URL rewriting)
 */
$P2G_USER_CFG['SESSION'] = array(
	'NAME' => 'PHP2GO_SESSID',
	'LIFETIME' => NULL,
	'SAVE_PATH' => NULL,
	'AUTO_START' => TRUE,
	'COOKIES_ONLY' => TRUE
);

/**
 * Auth settings
 * EXPIRY_TIME - default expiry time of the user session, in seconds (0 means limited by PHP session lifetime)
 * IDLE_TIME - default max idle time (between 2 requests), in seconds (0 means limited by PHP session lifetime)
 * REGENID_ON_LOGIN - whether to regenerate session ID when user logs in (if omitted, defaults to FALSE)
 * DESTROY_ON_LOGOUT - whether to destroy all session data when user logs out (defaults to FALSE)
 * AUTHENTICATOR_PATH - path (using "dot" pattern) of the authenticator class
 *		- it must extend php2go.auth.Auth or one of its children
 *		- the default authenticator in the framework is php2go.auth.AuthDb
 *		- calls to Auth::getInstance will return a singleton of the authenticator class defined in this section, or the default authenticator (php2go.auth.AuthDb)
 * AUTHORIZER_PATH - path (using "dot" pattern) of the authorizer class
 *		- it must extend php2go.auth.Authorizer
 *		- calls to Authorizer::getInstance will return a singleton of the authorizer class defined in this section
 */
$P2G_USER_CFG['AUTH'] = array(
	'EXPIRY_TIME' => 0,
	'IDLE_TIME' => 0,
	'REGENID_ON_LOGIN' => TRUE,
	'DESTROY_ON_LOGOUT' => TRUE/*,
	'AUTHENTICATOR_PATH' => '',
	'AUTHORIZER_PATH' => ''*/
);

/**
 * User settings
 * SESSION_NAME - name of the key in the superglobal $_SESSION array that must store the user data
 * CONTAINER_PATH - path (using PHP2Go "dot" pattern) of the user class (it must extend php2go.auth.User)
 */
$P2G_USER_CFG['USER'] = array(
	'SESSION_NAME' => 'PHP2GO_USER'/*,
	'CONTAINER_PATH' => ''*/
);

/**
 * PHP2Go template global configuration settings
 *
$P2G_USER_CFG['TEMPLATES'] = array(
	// global cache settings
	'CACHE' => array(
		// cached templates folder
		'DIR' => 'cache/',
		// cache lifetime, in seconds
		'LIFETIME' => 600,
		// or renew cache when original file is newer
		'USEMTIME' => TRUE
	),
	// tag delimiter (TEMPLATE_DELIM_COMMENT | TEMPLATE_DELIM_BRACE)
		'TAG_DELIMITER' => 'TEMPLATE_DELIM_BRACE',
	// custom modifiers
	// 	* simple functions (string) : "myFunction"
	// 	* static methods from loaded classes : array("Class", "method")
	// 	* static methods from non-loaded classes : array("Class", "method", "path.to.the.Class")
	'MODIFIERS' => array(
		'myMod' => 'myModifierFunction'
	)
);

 */

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
	// Enable/disable access keys highlight inside labels
	'ACCESSKEY_HIGHLIGHT' => TRUE,
	// Display mode of the help tips of form fields
	'HELP_MODE' => 'FORM_HELP_POPUP|FORM_HELP_INLINE',
	// Display options of the help tips
	'HELP_OPTIONS' => array(),
	'ERRORS' => array(
		// CSS class to the form validation errors
		'STYLE' => 'css_class_name',
		// Header of the error summary
		'HEADER_TEXT' => 'Header text',
		// CSS class of the header
		'HEADER_STYLE' => 'css_class_name',
		// Display mode of the validation error summary
		'LIST_MODE' => 'FORM_ERROR_BULLET_LIST|FORM_ERROR_FLOW',
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
		// Form width (pixels)
		'WIDTH' => int,
		// Form align
		'ALIGN' => 'left|right|center',
		// Table cellpadding
		'TABLE_PADDING' => int,
		// Table cellspacing
		'TABLE_SPACING' => int,
		// Label width (float, between 0 and 1)
		'LABEL_WIDTH' => int <= 1,
		// Label align
		'LABEL_ALIGN' => 'left|right|center'
	)
);
*/

/**
 * PHP2Go reports global configuration settings
 *
$P2G_USER_CFG['REPORTS'] = array(
	// Enable/disable external templates for empty data sets => bool
	'EMPTYTEMPLATE' => FALSE,
	// Template block that must be used to generate empty cells
	'EMPTYBLOCK' => 'empty_cell',
	// Pagination settings
	'PAGINATION' => array(
		// Pagination style => 'REPORT_PAGING_DEFAULT|REPORT_PREVNEXT|REPORT_FIRSTPREVNEXTLAST'
		'STYLE' => 'REPORT_FIRSTPREVNEXTLAST'
		// Records per page => int
		'PAGESIZE' => 20,
		// Pages per screen (when style=REPORT_PAGING_DEFAULT) => int
		'VISIBLEPAGES' => 10,
		// Other parameters
		'PARAMS' => array(
			'useButtons' => true,
			'hideInvalid' => false
		)
	),
	// Style settings
	'STYLE' => array(
		// CSS class for links
		'LINK' => 'links',
		// CSS class for inputs (filters, order options)
		'FILTER' => 'filter',
		// CSS class for buttons
		'BUTTON' => 'buttons',
		// CSS class for report headers
		'HEADER' => 'col_header',
		// CSS class for report title
		'TITLE' => 'title',
		// Alternating style (comma separated list of class names)
		'ALTSTYLE' => 'col_odd,col_even',
		// Search keyword highlight colors (foreground,background)
		'HIGHLIGHT' => '#ff0000,#ffffff',
		// Help tooltip style properties (overlib format)
		'HELP' => 'BGCOLOR,"#000000",FGCOLOR,"#FFFFFF",WIDTH,100'
	),
	// Override default icons
	'ICONS' => array(
		'ORDERASC' => 'images/order_asc.gif',
		'ORDERDESC' => 'images/order_desc.gif',
		'HELP' => 'images/help.gif'
	),
	// Transformation functions used on masked search terms
	'MASKFUNCTIONS' => array(
		'DATE' => 'Date::fromEuroToSqlDate'
	)
);
*/

/**
 * Through this configuration entry, you'll be able to register include keys that
 * could be used inside import() calls, just as you see in "php2go.xxx.yyy".
 * For instance, if you have your classes saved at "/www/htdocs/app/classes/",
 * you could type $P2G_USER_CFG['INCLUDE_PATH']['app'] = "/www/htdocs/app/classes/";
 * and then import your classes using import("app.MyClassName");
 */
$P2G_USER_CFG['INCLUDE_PATH'] = array();

/**
 * If you will use this default configuration set, don't forget to keep this return statement
 * in the last line, so the $P2G_USER_CFG may be understood by the initialization process
 */
return $P2G_USER_CFG;

?>