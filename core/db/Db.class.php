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

require_once(PHP2GO_ROOT . "vendor/adodb/adodb.inc.php");
require_once(PHP2GO_ROOT . "vendor/adodb/adodb-active-record.inc.php");
import('php2go.datetime.Date');
import('php2go.util.Callback');

/**
 * Database connection class
 *
 * The Db class is a simple wrapper over the connection class provided
 * by the ADODb database abstract library. Overrides the most important
 * functions of the ADOConnection class, and adds new utility methods
 * to perform SQL/DML commands and manage database statements.
 *
 * @package db
 * @uses Callback
 * @uses Date
 * @author Marcos Pont <mpont@users.sourceforge.net>
 * @version $Revision$
 */
class Db extends PHP2Go
{
	/**
	 * Indicates if we're connected to the database
	 *
	 * @var bool
	 */
	var $connected;

	/**
	 * Rows affected by last SQL command
	 *
	 * @var int
	 */
	var $affectedRows;

	/**
	 * Holds the SQL executed command
	 *
	 * This is useful to gather information on
	 * error situations.
	 *
	 * @var array
	 */
	var $lastStatement = array();

	/**
	 * Tells if result sets cache is enabled
	 *
	 * @var bool
	 */
	var $makeCache;

	/**
	 * Holds lifetime for cached result sets
	 *
	 * @var int
	 */
	var $cacheSecs;

	/**
	 * Internal instance of the ADODb connection class
	 *
	 * @var ADOConnection
	 */
	var $AdoDb;

	/**
	 * Class constructor
	 *
	 * Shouldn't be called directly. Prefer calling always {@link getInstance},
	 * so that you'll open only one connection for each ID, and will have the
	 * ability to use a custom connection class.
	 *
	 * @param unknown_type $id
	 * @return Db
	 */
	function Db($id=NULL) {
		parent::PHP2Go();
		$connParameters = Conf::getConnectionParameters($id);
		if (!empty($connParameters['DSN'])) {
			// connect using a single DSN string
			$this->AdoDb =& ADONewConnection($connParameters['DSN']);
			if (!$this->AdoDb)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATABASE_CONNECTION_FAILED'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			// connect using separated parameters
			$this->AdoDb =& AdoNewConnection($connParameters['TYPE']);
			$connFunc = ($connParameters['PERSISTENT'] ? 'PConnect' : 'Connect');
			if (!$this->AdoDb || !$this->AdoDb->$connFunc(@$connParameters['HOST'], $connParameters['USER'], @$connParameters['PASS'], $connParameters['BASE']))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATABASE_CONNECTION_FAILED'), E_USER_ERROR, __FILE__, __LINE__);
		}
		// default fetch mode
		if (array_key_exists('FETCH_MODE', $connParameters))
			$this->AdoDb->SetFetchMode($connParameters['FETCH_MODE']);
		// defulat transaction mode
		if (array_key_exists('TRANSACTION_MODE', $connParameters))
			$this->AdoDb->SetTransactionMode($connParameters['TRANSACTION_MODE']);
		$this->AdoDb->raiseErrorFn = 'dbErrorHandler';
		$this->connected = ($this->AdoDb->_connectionID !== FALSE);
		$this->affectedRows = 0;
		$this->makeCache = FALSE;
		if ($this->connected)
			$this->onAfterConnect();
		parent::registerDestructor($this, '__destruct');
	}

	/**
	 * Class destructor
	 */
  	function __destruct() {
    	$this->close();
  	}

  	/**
  	 * Get the singleton of a database connection
  	 *
  	 * Always use this method whenever you need a database
  	 * connection. The $id parameter represents the connection
  	 * ID that you want to use. If this argument is missing,
  	 * the value of the DATABASE.DEFAULT_CONNECTION configuration
  	 * setting will be used.
  	 *
  	 * When there's a customized connection class set in the
  	 * global configuration (DATABASE.CONNECTION_CLASS_PATH),
  	 * getInstance will return an instance of this class instead
  	 * of returning an instance of the default connection class.
  	 *
  	 * Examples:
  	 * <code>
  	 * /* use the default connection ID {@*}
  	 * $db =& Db::getInstance();
  	 * /* use the SECONDARY_DB connection ID {@*}
  	 * $db =& Db::getInstance('SECONDARY_DB');
  	 * </code>
  	 *
  	 * @param string $id Connection ID
  	 * @return Db
  	 * @static
  	 */
  	function &getInstance($id=NULL) {
  		static $instances;
  		if (!isset($instances))
  			$instances = array();
  		$Conf =& Conf::getInstance();
  		if (!is_null($id)) {
  			$key = $id;
  		} else {
  			$default = $Conf->getConfig('DATABASE.DEFAULT_CONNECTION');
  			if (!empty($default)) {
  				$key = $default;
  			} else {
  				$connections = $Conf->getConfig('DATABASE.CONNECTIONS');
  				if (is_array($connections)) {
					reset($connections);
  					list($key, $value) = each($connections);
  				} else {
  					$key = 'DEFAULT';
  				}
  			}
  		}
  		if (!isset($instances[$key])) {
  			if ($connectionClassPath = $Conf->getConfig('DATABASE.CONNECTION_CLASS_PATH')) {
  				if ($connectionClass = classForPath($connectionClassPath)) {
  					$instances[$key] =& new $connectionClass($id);
  					if (!TypeUtils::isInstanceOf($instances[$key], 'Db'))
  						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_CONNECTION_CLASS', $connectionClass), E_USER_ERROR, __FILE__, __LINE__);
  				} else {
  					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_CONNECTION_CLASS_PATH', $connectionClassPath), E_USER_ERROR, __FILE__, __LINE__);
  				}
  			} else {
  				$instances[$key] =& new Db($id);
  			}
  		}
  		return $instances[$key];
  	}

	/**
	 * Enable cache for all result sets
	 *
	 * When enabling cache, the result sets will be retrieved
	 * from cache during a number of seconds provided by the
	 * $seconds argument.
	 *
	 * If you want to purge cached result sets, call:
	 * <code>
	 * $db->setCache(TRUE, 0);
	 * </code>
	 *
	 * @param bool $flag Enable/disable
	 * @param int $seconds Cache lifetime, in seconds
	 */
	function setCache($flag, $seconds=0) {
		$flag = (bool)$flag;
		if ($flag) {
			$this->makeCache = TRUE;
			$this->cacheSecs = abs(intval($seconds));
		} else {
			$this->makeCache = FALSE;
		}
	}

	/**
	 * Enable debug for all SQL commands
	 *
	 * When debug mode is on, all commands executed in the
	 * database are displayed in the screen.
	 *
	 * @param bool $setting Enable/disable
	 */
	function setDebug($setting=TRUE) {
		$this->AdoDb->debug = ($setting ? 1 : 0);
	}

	/**
	 * Set the error handler function for all database errors
	 *
	 * To disable error handling on database operations, you
	 * can set error handler to FALSE, or use the @ operator
	 * when executing database commands.
	 *
	 * @param string $errorHandler Function name
	 * @return string Old error handler
	 */
	function setErrorHandler($errorHandler) {
		$oldErrorHandler = $this->AdoDb->raiseErrorFn;
		$this->AdoDb->raiseErrorFn = $errorHandler;
		return $oldErrorHandler;
	}

	/**
	 * Set fetch mode for query operations
	 *
	 * @link http://phplens.com/lens/adodb/docs-adodb.htm#setfetchmode
	 * @param int $mode Fetch mode
	 * @return int Old setting
	 */
	function setFetchMode($mode) {
		return $this->AdoDb->SetFetchMode($mode);
	}

	/**
	 * Define the behaviour of routines that build DML commands
	 * when converting empty PHP values to SQL
	 *
	 * @link http://phplens.com/lens/adodb/docs-adodb.htm#force_type
	 * @param int $forceType Force type
	 * @return int Old force type
	 */
	function setForceType($forceType) {
		if (in_array($forceType, array(ADODB_FORCE_IGNORE, ADODB_FORCE_NULL, ADODB_FORCE_EMPTY, ADODB_FORCE_VALUE))) {
			global $ADODB_FORCE_TYPE;
			$old = $ADODB_FORCE_TYPE;
			$ADODB_FORCE_TYPE = $forceType;
			return $old;
		}
		return FALSE;
	}

	/**
	 * Get the number of rows affected by the last operation
	 *
	 * @return int
	 */
	function affectedRows() {
		return $this->affectedRows;
	}

  	/**
  	 * Get database connection handle
  	 *
  	 * @return resource
  	 */
  	function getConnectionId() {
  		return ($this->connected ? $this->AdoDb->_connectionID : NULL);
  	}

  	/**
  	 * Get database driver name
  	 *
  	 * @return string
  	 */
  	function getDatabaseType() {
  		return $this->AdoDb->databaseType;
  	}

  	/**
  	 * Return information about the database server
  	 *
  	 * The value returned is an array containing the 'description'
  	 * and 'version' keys.
  	 *
  	 * @return unknown
  	 */
  	function getServerInfo() {
  		return ($this->connected ? $this->AdoDb->ServerInfo() : NULL);
  	}

	/**
	 * Get the error message reported by the database
	 *
	 * Returns FALSE if there's no error message available.
	 *
	 * @return string|bool
	 */
	function getError() {
		$errorMsg = $this->AdoDb->ErrorMsg();
		if (!empty($errorMsg) && strlen($errorMsg) > 0)
			return $errorMsg;
		return FALSE;
	}

	/**
	 * Get the error code reported by the database
	 *
	 * @return int
	 */
	function getErrorCode() {
		return $this->AdoDb->ErrorNo();
	}

	/**
	 * Get all database names
	 *
	 * @return array
	 */
	function getDatabases() {
		return $this->AdoDb->MetaDatabases();
	}

	/**
	 * Get all tables and/or views of the database
	 *
	 * @param string|bool $tableType 'TABLE' (tables only), 'VIEW' (views only) or FALSE (all)
	 * @return array
	 */
	function getTables($tableType=FALSE) {
		$tables = $this->AdoDb->MetaTables($tableType);
		return $tables;
	}

	/**
	 * Get an array of objects representing the columns of a given table or view
	 *
	 * @param string $table Table or view name
	 * @return array
	 */
	function getColumns($table) {
		return $this->AdoDb->MetaColumns($table);
	}

	/**
	 * Get column names of a given table or view
	 *
	 * @param string $table Table or view name
	 * @param bool $assoc Whether to return a hash array
	 * @return array
	 */
	function getColumnNames($table, $assoc=TRUE) {
		return $this->AdoDb->MetaColumnNames($table, !$assoc);
	}

	/**
	 * Get primary key(s) of a given table
	 *
	 * @param string $table Table name
	 * @return array
	 */
	function getPrimaryKeys($table) {
		return $this->AdoDb->MetaPrimaryKeys($table);
	}

	/**
	 * Get indexes of a given table
	 *
	 * @param string $table Table name
	 * @return array
	 */
	function getIndexes($table) {
		return $this->AdoDb->MetaIndexes($table);
	}

	/**
	 * Generate the call for a stored procedure
	 *
	 * @param string $stmt Procedure name and arguments
	 * @param bool $prepare Whether to prepare the created SQL command
	 * @todo Add support for other drivers
	 * @return string|array
	 */
	function getProcedureSQL($stmt, $prepare=FALSE) {
		switch ($this->AdoDb->dataProvider) {
			// oci8, oci805, ocipo
			case 'oci8' :
				$stmt = "begin {$stmt}; end;";
				break;
			// db2
			case 'db2' :
			// mysqli
			case 'mysqli' :
				$stmt = "call {$stmt};";
				break;
			// sybase, sybase_ase
			case 'sybase' :
				$stmt = "exec {$stmt}";
				break;
			default :
				break;
		}
		if ($prepare)
			return $this->prepare($stmt, TRUE);
		else
			return $stmt;
	}

  	/**
  	 * Generate and return the next value of a given database sequence
  	 *
  	 * If the database driver doesn't support sequences, ADODb will use
  	 * regular tables instead. If the sequence doesn't exist, it is created.
  	 *
  	 * @link http://phplens.com/lens/adodb/docs-adodb.htm#genid
  	 * @param string $seqName Sequence name
  	 * @param int $startId Start ID, if sequence needs to be created
  	 * @return int
  	 */
  	function getNextId($seqName='p2gseq', $startId=1) {
  		return ($this->connected ? $this->AdoDb->GenID($seqName, $startId) : 0);
  	}

	/**
	 * Get database last insert ID
	 *
	 * Returns 0 if database doesn't support this feature.
	 *
	 * @return int
	 */
	function lastInsertId() {
		return ($this->AdoDb->hasInsertID ? $this->AdoDb->Insert_ID() : 0);
	}

	/**
	 * Get the first cell (0,0) of a result set
	 *
	 * Example:
	 * <code>
	 * /* this will print the name of the first user in the returned result set {@*}
	 * print $db->getFirstCell("select name from users");
	 * </code>
	 *
	 * @param string|array $stmt SQL query or prepared statement
	 * @param array $bindVars Bind variables
	 * @return mixed
	 */
	function getFirstCell($stmt, $bindVars=FALSE) {
		$this->lastStatement = array(
			'source' => 'getFirstCell',
			'statement' => $stmt,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if ($this->makeCache)
			return $this->AdoDb->CacheGetOne($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetOne($stmt, $bindVars);
	}

	/**
	 * Get the first row of a result set
	 *
	 * @param string|array $stmt SQL query or prepared statement
	 * @param array $bindVars Bind variables
	 * @return array
	 */
	function getFirstRow($stmt, $bindVars=FALSE) {
		$this->lastStatement = array(
			'source' => 'getFirstRow',
			'statement' => $stmt,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if ($this->makeCache)
			return $this->AdoDb->CacheGetRow($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetRow($stmt, $bindVars);
	}

	/**
	 * Get the first column of a result set
	 *
	 * @param string|array $stmt SQL query or prepared statement
	 * @param array $bindVars Bind variables
	 * @return array
	 */
	function getFirstCol($stmt, $bindVars=FALSE) {
		$this->lastStatement = array(
			'source' => 'getFirstCol',
			'statement' => $stmt,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if ($this->makeCache)
			return $this->AdoDb->CacheGetCol($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetCol($stmt, $bindVars);
	}

	/**
	 * Get a set of active records
	 *
	 * Run a query on table $table, using $clause as condition clause,
	 * and return the results as a list of objects that can be manipulated.
	 * These objects are based on the Active Record pattern.
	 *
	 * Options:
	 * # class : active records class name (defaults to ADODB_Active_Record)
	 * # primaryKeys : allows to manually set the table primary keys (by default, this is auto detected)
	 * # order : orderby clause
	 *
	 * @link http://phplens.com/lens/adodb/docs-active-record.htm
	 * @param string $table Table name
	 * @param string $clause Condition clause
	 * @param array $bindVars Bind variables
	 * @param array $options Extra options
	 * @return array
	 */
	function &getActiveRecords($table, $clause=NULL, $bindVars=FALSE, $options=array()) {
		$options = (array)$options;
		$className = (array_key_exists('class', $options) ? $options['class'] : 'ADODB_Active_Record');
		$clause = (empty($clause) ? "1=1" : $clause);
		$clause .= (array_key_exists('order', $options) ? " ORDER BY {$options['order']}" : '');
		$primaryKeys = (array_key_exists('primaryKeys', $options) ? $options['primaryKeys'] : FALSE);
		$this->lastStatement = array(
			'source' => 'getActiveRecords',
			'statement' => "SELECT * FROM {$table} WHERE {$clause}",
			'vars' => ($bindVars ? $bindVars : array())
		);
		$records =& $this->AdoDb->GetActiveRecordsClass($className, $table, $clause, $bindVars, $primaryKeys);
		return $records;
	}

	/**
	 * Get all results of an SQL query
	 *
	 * @param string|array $stmt SQL query or prepared statement
	 * @param array $bindVars Bind Variables
	 * @return array
	 */
	function getAll($stmt, $bindVars=FALSE) {
		$this->lastStatement = array(
			'source' => 'getAll',
			'statement' => $stmt,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if ($this->makeCache)
			return $this->AdoDb->CacheGetAll($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetAll($stmt, $bindVars);
	}

	/**
	 * Builds a query that counts records based on a normal SQL query
	 *
	 * The method will use nested SQL whenever possible. It will also try to
	 * replace the query fields by a count(*) statement. Queries that use "group by"
	 * or "distinct" keywords under databases that doesn't support nested SQL
	 * will remain unchanged.
	 *
	 * @param string|array $stmt SQL query or prepared statement
	 * @param array $bindVars Bind variables
	 * @param bool $optimize Set this to FALSE to disable query transformations (removal or sort clause)
	 * @return int Number of records
	 */
	function getCount($stmt, $bindVars=FALSE, $optimize=TRUE) {
		$rewriteSql = $sql = (is_array($stmt) ? $stmt[0] : $stmt);
		import('php2go.db.QueryBuilder');
		$query = QueryBuilder::createFromSql($sql);
		// remove sort clause in order to optimize record count
		if ($optimize)
			$query->clearOrder();
		// driver supports nested SQL, query uses "top", "limit", "distinct" or "group by" keywords: must encapsulate into a subquery
		if (!empty($this->AdoDb->_nestedSQL) || preg_match("/(\btop\b|\blimit\b)/i", $sql) || preg_match("/\b(distinct|distinctrow)\b/i", $query->fields) || $query->groupby) {
			// oci8
			if ($this->AdoDb->dataProvider == 'oci8') {
				if (preg_match('#/\\*+.*?\\*\\/#', $sql, $matches))
					$rewriteSql = "select {$matches[0]} count(*) from (" . $query->getQuery() . ") _p2g_alias_";
				else
					$rewriteSql = "select count(*) from (" . $query->getQuery() . ") _p2g_alias_";
			}
			// db2, interbase, firebird, mssql, pdo, postgres
			elseif (preg_match("/^(db2|ibase|mssql|pdo|postgres)$/", $this->AdoDb->dataProvider)) {
				$rewriteSql = "select count(*) from (" . $query->getQuery() . ") _p2g_alias_";
			}
			// mysql >= 4.1
			elseif (strncmp($this->AdoDb->databaseType, 'mysql', 5) == 0) {
				$info = $this->AdoDb->ServerInfo();
				$version = (float)$info['version'];
				if ($version >= 4.1)
					$rewriteSql = "select count(*) from (" . $query->getQuery() . ") _p2g_alias_";
			}
		}
		// other queries, when not using "top" and "limit" keywords: replace query fields by "count(*)"
		elseif (!preg_match("/(\btop\b|\blimit\b)/i", $sql)) {
			$query->setFields('count(*)');
			$rewriteSql = $query->getQuery();
		}
		// run the rewritten sql, if it's valid
		if (isset($rewriteSql) && $rewriteSql != $sql) {
			$oldMode = $this->AdoDb->SetFetchMode(ADODB_FETCH_NUM);
			if ($this->makeCache) {
				$rs =& $this->AdoDb->CacheExecute($this->cacheSecs, $rewriteSql, $bindVars);
				$count = ($rs ? $rs->fields[0] : FALSE);
			} else {
				$rs =& $this->AdoDb->Execute($rewriteSql, $bindVars);
				$count = ($rs ? $rs->fields[0] : FALSE);
			}
			$this->AdoDb->SetFetchMode($oldMode);
			if ($count !== FALSE) {
				$this->lastStatement = array(
					'source' => 'getCount',
					'statement' => $rewriteSql,
					'vars' => ($bindVars ? $bindVars : array())
				);
				return $count;
			}
		}
		// rewrite failed: use the original query
		$this->lastStatement = array(
			'source' => 'getCount',
			'statement' => $rewriteSql,
			'vars' => ($bindVars ? $bindVars : array())
		);
		$rs =& $this->AdoDb->Execute($rewriteSql, $bindVars);
		if ($rs) {
			$count = $rs->RecordCount();
			if ($count == -1) {
				while (!$rs->EOF)
					$rs->MoveNext();
				$count = $rs->_currentRow;
			}
			$rs->Close();
			if ($count > -1)
				return $count;
		}
		return 0;
	}

	/**
	 * Set transaction mode
	 *
	 * @link http://phplens.com/lens/adodb/docs-adodb.htm#SetTransactionMode
	 * @param string $mode Transaction mode
	 */
	function setTransactionMode($mode) {
		$this->AdoDb->SetTransactionMode($mode);
	}

	/**
	 * Start a new transaction
	 *
	 * ADODb supports nested transactions.
	 * Returns FALSE when the database driver doesn't support transactions.
	 *
	 * @return bool
	 */
	function startTransaction() {
		return $this->AdoDb->StartTrans();
	}

	/**
	 * Flags the active transaction as failed
	 *
	 * @return bool
	 */
	function failTransaction() {
		return $this->AdoDb->FailTrans();
	}

	/**
	 * Verify if the active transaction has failed
	 *
	 * @return bool
	 */
	function hasFailedTransaction() {
		return $this->AdoDb->HasFailedTrans();
	}

	/**
	 * Completes the active transaction
	 *
	 * The ADODb library verifies if the active transaction was
	 * flagged as failed. If yes, a rollback operation will be
	 * performed. Otherwise, the transaction will be committed.
	 *
	 * @param bool $forceRollback Whether to force a rollback
	 * @return bool Returns TRUE when a commit was executed
	 */
	function completeTransaction($forceRollback=FALSE) {
		$forceRollback = (bool)$forceRollback;
		return $this->AdoDb->CompleteTrans(!$forceRollback);
	}

	/**
	 * Finishes the active transaction
	 *
	 * @param bool $flag If TRUE, transaction will be committed. Otherwise, it will be rolled back
	 * @deprecated Prefer using {@link startTransaction}, {@link failTransaction} and {@link completeTransaction}
	 * @return bool
	 */
	function commit($flag=TRUE) {
		return $this->AdoDb->CommitTrans((bool)$flag);
	}

	/**
	 * Rollback the active transaction
	 *
	 * @deprecated Prefer using {@link startTransaction}, {@link failTransaction} and {@link completeTransaction}
	 * @return bool
	 */
	function rollback() {
		return $this->AdoDb->RollbackTrans();
	}

	/**
	 * Prepare an SQL statement for execution
	 *
	 * @param string $stmtCode SQL statement
	 * @param bool $cursor Set to TRUE if the statement will return a cursor (oci8 only)
	 * @return bool
	 */
	function prepare($stmtCode, $cursor=FALSE) {
		return $this->AdoDb->Prepare($stmtCode, (bool)$cursor);
	}

	/**
	 * Registers a bind variable in a given statement
	 *
	 * @param array $statement Prepared statement returned by {@link prepare}
	 * @param mixed &$value Variable value
	 * @param mixed $varName Variable name or position
	 * @param int $type Variable type
	 * @param int $maxLen Value maxlength
	 * @param bool $isOutput Whether this is an output variable (when supported by the driver)
	 * @return bool
	 */
	function bind($statement, &$value, $varName, $type=FALSE, $maxLen=4000, $isOutput=FALSE) {
		return $this->AdoDb->Parameter($statement, $value, $varName, $isOutput, $maxLen, $type);
	}
	
	/**
	 * Correctly quotes an identifier
	 * 
	 * Adds quotes to a database identifier according
	 * to the rules of the active driver.
	 *
	 * @todo Verify how database drivers quote identifiers
	 * @param string $alias Alias
	 * @return Quoted alias
	 */
	function quoteIdentifier($alias) {
		switch ($this->AdoDb->dataProvider) {
			case 'mysql' :
				return "`{$alias}`";
			default :
				return "\"{$alias}\"";
		}
	}

	/**
	 * Correctly quotes a string
	 *
	 * Adds and escapes quotes inside a string according to the
	 * rules of the active database driver.
	 *
	 * @param string $str Input string
	 * @param bool $magicQuotes If $str comes from the request, set this to get_magic_quotes_gpc()
	 * @return Quoted string
	 */
	function quoteString($str, $magicQuotes=FALSE) {
		return $this->AdoDb->qstr($str, $magicQuotes);
	}

	/**
	 * Prepare a given date string to be saved by the database
	 *
	 * Date strings written in EURO or US formats are converted.
	 * Time part can be preserved by setting $time=TRUE. If you
	 * don't want the method to add quotes in the date string,
	 * set $bind=TRUE.
	 *
	 * @param string $date Date string
	 * @param bool $time Whether to preserve/add time values
	 * @param bool $bind If FALSE, a quoted date string will be returned
	 * @return string
	 */
	function date($date=NULL, $time=FALSE, $bind=FALSE) {
		if (empty($date)) {
			return ($time ? $this->AdoDb->sysTimeStamp : $this->AdoDb->sysDate);
		} else {
			if (!TypeUtils::isInteger($date))
				$date = Date::toSqlDate($date, $time);
			if ($time)
				return ($bind ? $this->AdoDb->BindTimeStamp($date) : $this->AdoDb->DBTimeStamp($date));
			else
				return ($bind ? $this->AdoDb->BindDate($date) : $this->AdoDb->DBDate($date));
		}
	}

	/**
	 * Execute a command on the database
	 *
	 * The difference between {@link execute} and {@link query} is that
	 * the first should be used to perform operations that return a boolean
	 * result (success or failure). The second should only be used to
	 * run regular SQL queries that return record sets.
	 *
	 * @param mixed $statement SQL code or prepared statement
	 * @param array $bindVars Bind variables
	 * @param string $cursorName Cursor name (oci8 only)
	 * @see query
	 * @see limitQuery
	 * @return bool
	 */
	function &execute($statement, $bindVars=FALSE, $cursorName=NULL) {
		$this->lastStatement = array(
			'source' => 'execute',
			'statement' => $statement,
			'vars' => ($bindVars ? $bindVars : array())
		);
		if (!is_null($cursorName) && $this->AdoDb->dataProvider == 'oci8')
			$rs =& $this->AdoDb->ExecuteCursor($statement, $cursorName, $bindVars);
		elseif ($this->makeCache)
			$rs =& $this->AdoDb->CacheExecute($this->cacheSecs, $statement, $bindVars);
		else
			$rs =& $this->AdoDb->Execute($statement, $bindVars);
		if ($rs) {
			$this->affectedRows = ($rs->EOF ? 0 : $rs->RecordCount());
			return $rs;
		} else {
			$false = FALSE;
			$this->affectedRows = 0;
			return $false;
		}
	}

	/**
	 * Execute an SQL query on the database
	 *
	 * @param mixed $sqlCode SQL code or prepared statement
	 * @param bool $execute Whether to execute or just print the SQL code
	 * @param array $bindVars Bind variables
	 * @return ADORecordSet
	 */
	function &query($sqlCode, $execute=TRUE, $bindVars=FALSE) {
		if ($execute) {
			$this->lastStatement = array(
				'source' => 'query',
				'statement' => $sqlCode,
				'vars' => ($bindVars ? $bindVars : array())
			);
			if ($this->makeCache)
				$rs =& $this->AdoDb->CacheExecute($this->cacheSecs, $sqlCode, $bindVars);
			else
				$rs =& $this->AdoDb->Execute($sqlCode, $bindVars);
			if ($rs) {
				$this->affectedRows = $rs->RecordCount();
			} else {
				$this->affectedRows = 0;
				$rs =& $this->emptyRecordSet();
			}
			return $rs;
		} else {
			$true = TRUE;
			println((is_array($sqlCode) ? $sqlCode[0] : $sqlCode));
			return $true;
		}
	}

	/**
	 * Execute an SQL query on the database, rescricting the returned number of rows
	 *
	 * <code>
	 * /* get first 30 rows {@*}
	 * $db->limitQuery("select * from users", 30);
	 * /* get 20 rows, starting from the 20th row {@*}
	 * $db->limitQuery("select * from users", 20, 20);
	 * </code>
	 *
	 * @param mixed $sqlCode SQL code or prepared statement
	 * @param int $rows Subset size
	 * @param int $offset Starting offset (defaults to 0)
	 * @param bool $execute Whether to execute, or just print the SQL code
	 * @param array $bindVars Bind variables
	 * @return ADORecordSet
	 */
	function &limitQuery($sqlCode, $rows=-1, $offset=0, $execute=TRUE, $bindVars=FALSE) {
		if ($offset < 0) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$offset", "limitQuery")), E_USER_WARNING, __FILE__, __LINE__);
			$offset = 0;
		}
		if ($execute) {
			$this->lastStatement = array(
				'source' => 'limitQuery',
				'statement' => $sqlCode,
				'vars' => ($bindVars ? $bindVars : array())
			);
			if ($this->makeCache)
				$rs =& $this->AdoDb->CacheSelectLimit($this->cacheSecs, $sqlCode, $rows, $offset, $bindVars);
			else
				$rs =& $this->AdoDb->SelectLimit($sqlCode, $rows, $offset, $bindVars);
			if ($rs) {
				$this->affectedRows = $rs->RecordCount();
			} else {
				$this->affectedRows = 0;
				$rs =& $this->emptyRecordSet();
			}
			return $rs;
		} else {
			$true = TRUE;
			println((is_array($sqlCode) ? $sqlCode[0] : $sqlCode));
			return $true;
		}
	}

	/**
	 * Build and execute an INSERT on the database
	 *
	 * Options:
	 * # forceType (int) - read ADODb docs for more details
	 * # sequenceName (string) - sequence to be used to generate a new primary key value
	 *
	 * The primary key value will be determined by calling
	 * {@link lastInsertId} (when database driver supports
	 * auto-increment columns).
	 *
	 * Returns the PK value of the new record, or FALSE in case of errors.
	 *
	 * Examples:
	 * <code>
	 * $db->insert('person', $personData);
	 * $db->insert('user', $userData, array('sequenceName'=>'seq_user'));
	 * </code>
	 *
	 * @param string $table Table name
	 * @param array $arrData Hash array of fields
	 * @param array $options Extra options
	 * @return int|bool
	 */
	function insert($table, $arrData, $options=array()) {
		if (empty($table))
			return FALSE;
		if (TypeUtils::isHashArray($arrData)) {
			// set force type
			if (isset($options['forceType']))
				$this->setForceType($options['forceType']);
			// generate pk value using a sequence
			if (isset($options['sequenceName'])) {
				$pk = $this->AdoDb->MetaPrimaryKeys($table);
				if ($pk && sizeof($pk) == 1) {
					$insertId = $this->AdoDb->GenID($options['sequenceName']);
					$arrData[$pk[0]] = $insertId;
				}
			}
			$insertSQL = $this->AdoDb->GetInsertSQL($table, $arrData);
			if (!empty($insertSQL)) {
				$this->lastStatement = array(
					'source' => 'insert',
					'statement' => $insertSQL,
					'vars' => array()
				);
        		$result = $this->AdoDb->Execute($insertSQL);
				if ($result) {
					// update affectedRows property
					$this->affectedRows = $this->AdoDb->Affected_Rows();
					if (!isset($insertId))
						// return last inserted ID
						$insertId = $this->lastInsertId();
					return ($insertId ? $insertId : TRUE);
				} else {
					$this->affectedRows = 0;
					return FALSE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Build and execute an UPDATE command on the database
	 *
	 * Options:
	 * # forceType (int) (see ADODb docs for more details).
	 *
	 * Example:
	 * <code>
	 * $db->update('person', $_POST, 'id_person='.$_POST['id_person']);
	 * </code>
	 *
	 * @param string $table Table name
	 * @param array $arrData Hahs array of fields
	 * @param string $clause Condition clause
	 * @param bool $force Force update even when record is unchanged
	 * @param array $options Extra options
	 * @return bool
	 */
	function update($table, $arrData, $clause, $force=FALSE, $options=array()) {
		if (empty($table) || empty($clause))
			return FALSE;
		$rs =& $this->AdoDb->Execute(sprintf("SELECT * FROM %s WHERE %s", $table, $clause));
		if ($rs && TypeUtils::isHashArray($arrData)) {
			// set force type
			if (isset($options['forceType']))
				$this->setForceType($options['forceType']);
			$updateSQL = $this->AdoDb->GetUpdateSQL($rs, $arrData, $force);
			if (!empty($updateSQL)) {
				$this->lastStatement = array(
					'source' => 'update',
					'statement' => $updateSQL,
					'vars' => array()
				);
				$result = $this->AdoDb->Execute($updateSQL);
				$this->affectedRows = $this->AdoDb->Affected_Rows();
				return ($result ? TRUE : FALSE);
			}
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Update columns of type CLOB/BLOB
	 *
	 * Examples:
	 * <code>
	 * $db->updateLob('person', 'photo', '/upload/pic.gif', 'id_person='.$idPerson, 'BLOB', T_BY_FILE);
	 * $db->updateLob('person', 'photo', file_get_contents('/upload/pic.gif'), 'id_person='.$idPerson);
	 * </code>
	 *
	 * @param string $table Table name
	 * @param string $column LOB column name
	 * @param string $value LOB value
	 * @param string $clause Condition clause
	 * @param string $lobType LOB type ('BLOB' or 'CLOB')
	 * @param int $valueType Value type ({@link T_BY_VAR} or {@link T_BY_FILE})
	 * @return bool
	 */
	function updateLob($table, $column, $value, $clause, $lobType='BLOB', $valueType=T_BYVAR) {
		$lobType = strtoupper($lobType);
		if ($valueType == T_BYVAR)
			return $this->AdoDb->UpdateBlob($table, $column, $value, $clause, $lobType);
		return $this->AdoDb->UpdateBlobFile($table, $column, $value, $clause, $lobType);
	}

	/**
	 * Insert or update the database row represented by $arrFields
	 *
	 * If the record is already on the database, an UPDATE command
	 * is executed. Otherwise, an INSERT is performed.
	 *
	 * Return values:
	 * # returns 0 in case of error
	 * # returns 1 if an UPDATE was executed
	 * # returns 2 if an INSERT was executed
	 *
	 * @link http://phplens.com/lens/adodb/docs-adodb.htm#replace
	 * @param string $table Table name
	 * @param array $arrFields Hash array of fields
	 * @param array $keyFields Primary key field(s)
	 * @param bool $quoteVals Whether to quote string values
	 * @return int
	 */
	function replace($table, $arrFields, $keyFields, $quoteVals=FALSE) {
		return $this->AdoDb->Replace($table, $arrFields, $keyFields, $quoteVals);
	}

	/**
	 * Build and execute a DELETE command on the database
	 *
	 * @param string $table Table name
	 * @param string $clause Delete condition clause
	 * @param array $bindVars Bind variables
	 * @return bool Operation result
	 */
	function delete($table, $clause, $bindVars=FALSE) {
		if (empty($table) || empty($clause))
			return FALSE;
		$sqlCode = sprintf("DELETE FROM %s WHERE %s", $table, $clause);
		$this->lastStatement = array(
			'source' => 'delete',
			'statement' => $sqlCode,
			'vars' => ($bindVars ? $bindVars : array())
		);
		$result = $this->AdoDb->Execute($sqlCode, $bindVars);
		$this->affectedRows = $this->AdoDb->Affected_Rows();
		return ($result ? TRUE : FALSE);
	}

	/**
	 * Apply integrity tests on a given table/column
	 *
	 * Example:
	 * <code>
	 * /* setup a list of table/column pairs that refer person.id_person {@*}
	 * $ref = array('client'=>'id_client', 'user'=>'id_user');
	 * /* check integrity {@*}
	 * $bool = $db->checkIntegrity('person', 'id_person', 7, $ref);
	 * </code>
	 *
	 * @param string $table Table name
	 * @param string $column Column name
	 * @param mixed $value Column value
	 * @param array $reference Table/column pairs to be tested
	 * @return bool
	 */
	function checkIntegrity($table, $column, $value, $reference) {
		$ok = TRUE;
		if (is_array($reference)) {
			foreach($reference as $tb => $col) {
				$fields = "{$table}.{$column}";
				$tables = "{$table},{$tb}";
				$clause = "{$table}.{$column} = {$value} AND {$table}.{$column} = {$tb}.{$col}";
				$sqlCode = "SELECT {$fields} FROM {$tables} WHERE {$clause}";
				$this->lastStatement = array(
					'source' => 'checkIntegrity',
					'statement' => $sqlCode,
					'vars' => array()
				);
				$rs =& $this->query($sqlCode);
				if ($rs && $rs->recordCount() > 0) {
					$ok = FALSE;
					break;
				}
			}
		} else {
			$fields = "{$table}.{$column}";
			$tables = "{$table},{$reference}";
			$clause = "{$table}.{$column} = {$value} AND {$table}.{$column} = {$reference}.{$column}";
			$sqlCode = "SELECT {$fields} FROM {$tables} WHERE {$clause}";
			$this->lastStatement = array(
				'source' => 'checkIntegrity',
				'statement' => $sqlCode,
				'vars' => array()
			);
			$rs =& $this->query($sqlCode);
			$ok = ($rs && $rs->RecordCount() == 0);
		}
		return $ok;
     }

	/**
	 * Publish the first record returned by an SQL query in the registry
	 *
	 * This commands expects an SQL query or prepared statement in
	 * the $sqlCode argument. This command is executed (using $bindVars,
	 * if available), and the first returned row is registered in the
	 * registry singleton (global scope).
	 *
	 * @param string $sqlCode SQL query
	 * @param array $bindVars SQL bind vars
	 * @param bool $ignoreEmptyResults Ignore empty results (TRUE) or throw an error (FALSE)
	 * @uses Registry::set()
	 * @return bool
	 */
	function toGlobals($sqlCode, $bindVars=FALSE, $ignoreEmptyResults=FALSE) {
		if (!$this->isDbQuery($sqlCode) || $this->isDbDesign($sqlCode)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TOGLOBALS_WRONG_USAGE'), E_USER_WARNING, __FILE__, __LINE__);
			return FALSE;
		}
		$oldFetchMode = $this->AdoDb->fetchMode;
		$this->setFetchMode(ADODB_FETCH_ASSOC);
		$this->lastStatement = array(
			'source' => 'toGlobals',
			'statement' => $sqlCode,
			'vars' => $bindVars
		);
		$rs =& $this->AdoDb->Execute($sqlCode, $bindVars);
		$this->setFetchMode($oldFetchMode);
		if ($rs->RecordCount() > 0) {
			foreach ($rs->fields as $key => $value) {
				Registry::set($key, $value);
			}
			return TRUE;
		} else if (!$ignoreEmptyResults) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_TOGLOBALS_QUERY', $sqlCode), E_USER_NOTICE, __FILE__, __LINE__);
			return FALSE;
		}
		return FALSE;
	}

	/**
	 * Builds and returns an empty record set
	 *
	 * @return ADORecordSet_empty
	 */
	function &emptyRecordSet() {
		$Rs = new ADORecordSet_empty();
		return $Rs;
	}

	/**
	 * Check if a given command is a database design operation (DML or DDL)
	 *
	 * @param string $sql SQL command
	 * @return bool
	 */
	function isDbDesign($sql) {
		if (is_array($sql))
			$sql = $sql[0];
		$resWords = 'INSERT|UPDATE|DELETE|' . 'REPLACE|CREATE|DROP|' .
					'ALTER|GRANT|REVOKE|' . 'LOCK|UNLOCK';
		if (preg_match('/^\s*"?(' . $resWords . ')\s+/i', $sql)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Check if a given command is DQL (contains "SELECT")
	 *
	 * @param string $sql SQL command
	 * @return bool
	 */
	function isDbQuery($sql) {
		if (is_array($sql))
			$sql = $sql[0];
		$resWord = 'SELECT';
		if (preg_match('/^\s*"?(' . $resWord . ')\s+/i', $sql)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Close the database connection
	 *
	 * @return bool
	 */
	function close() {
		if (isset($this->AdoDb->_connectionID) && is_resource($this->AdoDb->_connectionID)) {
			$this->onBeforeClose();
			$this->connected = $this->AdoDb->Close();
		} else {
			$this->connected = FALSE;
		}
		return ($this->connected === FALSE);
	}

	/**
	 * Abstract method, must be implemented by custom connection class
	 *
	 * @abstract
	 */
	function onAfterConnect() {
	}

	/**
	 * Abstract method, must be implemented by custom connection class
	 *
	 * @abstract
	 */
	function onBeforeClose() {
	}
}
?>